<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Api/AdminController.php
// ─────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Profile;
use App\Models\ProfileModulePermission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserModulePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // ── Helper: check if roles/profiles tables exist (migration safety) ──
    private function hasRolesTable(): bool
    {
        return Schema::hasTable('roles');
    }
    private function hasProfilesTable(): bool
    {
        return Schema::hasTable('profiles');
    }

    // ═════════════════════════════════════════════════════════════
    // USERS
    // ═════════════════════════════════════════════════════════════

    // GET /api/admin/users
    public function userIndex(Request $request)
    {
        $query = User::query()
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('username', 'like', "%{$request->search}%");
            }))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when(
                $request->is_active !== null && $request->is_active !== '',
                fn($q) => $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN))
            )
            ->orderBy('name');

        // Only eager-load if the tables exist
        if ($this->hasRolesTable())
            $query->with('roles');
        if ($this->hasProfilesTable())
            $query->with('profiles');

        $paginated = $query->paginate($request->per_page ?? 20);

        // Map rows to clean arrays — blade JS reads json.data (the paginator)
        // then json.data.data (the rows array)
        $paginated->getCollection()->transform(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'username' => $u->username,
            'role' => $u->role,
            'department' => $u->department,
            'phone' => $u->phone,
            'is_active' => (bool) $u->is_active,
            'last_login_at' => $u->last_login_at,
            'roles' => $u->roles ?? collect(),
            'profiles' => $u->profiles ?? collect(),
        ]);

        return response()->json(['status' => 'ok', 'data' => $paginated]);
    }

    // GET /api/admin/users/{id}
    public function userShow(string $id)
    {
        $query = User::query();
        if ($this->hasRolesTable())
            $query->with('roles');
        if ($this->hasProfilesTable())
            $query->with('profiles');
        $query->with('modulePermissions.module');

        $user = $query->findOrFail($id);

        return response()->json([
            'status' => 'ok',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'department' => $user->department,
                'phone' => $user->phone,
                'is_active' => (bool) $user->is_active,
                'last_login_at' => $user->last_login_at,
                'roles' => $user->roles ?? collect(),
                'profiles' => $user->profiles ?? collect(),
                'modulePermissions' => $user->modulePermissions,
            ]
        ]);
    }

    // POST /api/admin/users
    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,management,normal',
            'department' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
            'phone' => $request->phone,
            'is_active' => $request->input('is_active', true),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Sync roles/profiles only if tables exist
        if ($this->hasRolesTable() && $request->filled('role_ids')) {
            $user->roles()->sync($request->role_ids);
        }
        if ($this->hasProfilesTable() && $request->filled('profile_ids')) {
            $user->profiles()->sync($request->profile_ids);
            $this->applyProfilePermissionsToUser($user, $request->profile_ids);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'User created successfully.',
            'data' => ['id' => $user->id, 'name' => $user->name],
        ], 201);
    }

    // PUT /api/admin/users/{id}
    public function userUpdate(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['sometimes', 'required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'role' => 'sometimes|required|in:admin,management,normal',
            'department' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'email', 'username', 'role', 'department', 'phone', 'is_active']);
        $data['updated_by'] = auth()->id();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        if ($this->hasRolesTable() && $request->has('role_ids')) {
            $user->roles()->sync($request->role_ids ?? []);
        }
        if ($this->hasProfilesTable() && $request->has('profile_ids')) {
            $user->profiles()->sync($request->profile_ids ?? []);
        }

        if (isset($data['is_active']) && !$data['is_active']) {
            $user->tokens()->delete();
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'User updated successfully.',
            'data' => ['id' => $user->id, 'name' => $user->name],
        ]);
    }

    // DELETE /api/admin/users/{id}
    public function userDestroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete your own account.'], 422);
        }
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['status' => 'ok', 'message' => 'User deleted.']);
    }

    // PATCH /api/admin/users/{id}/toggle-status
    public function userToggleStatus(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Cannot disable your own account.'], 422);
        }
        $user->update(['is_active' => !$user->is_active, 'updated_by' => auth()->id()]);
        if (!$user->is_active)
            $user->tokens()->delete();

        return response()->json([
            'status' => 'ok',
            'message' => $user->is_active ? 'User enabled.' : 'User disabled.',
            'data' => ['is_active' => $user->is_active],
        ]);
    }

    // GET /api/admin/users/{id}/permissions
    public function userPermissions(string $id)
    {
        $user = User::with(['modulePermissions.module'])->findOrFail($id);

        if (!$user->isNormal()) {
            return response()->json([
                'status' => 'ok',
                'data' => ['full_access' => true, 'role' => $user->role, 'permissions' => []],
            ]);
        }

        return response()->json(['status' => 'ok', 'data' => $user->modulePermissions]);
    }

    // PUT /api/admin/users/{id}/permissions
    public function userUpdatePermissions(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.module_id' => 'required|integer|exists:modules,id',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ]);

        UserModulePermission::where('user_id', $user->id)->delete();

        $permissions = collect($request->permissions)->map(fn($p) => [
            'user_id' => $user->id,
            'module_id' => $p['module_id'],
            'can_view' => $p['can_view'] ?? true,
            'can_create' => $p['can_create'] ?? false,
            'can_edit' => $p['can_edit'] ?? false,
            'can_delete' => $p['can_delete'] ?? false,
            'granted_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        UserModulePermission::insert($permissions);

        return response()->json([
            'status' => 'ok',
            'message' => 'Permissions updated.',
            'data' => UserModulePermission::with('module')->where('user_id', $user->id)->get(),
        ]);
    }

    // POST /api/admin/users/{id}/apply-profile
    public function applyProfileToUser(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $profileIds = $request->validate([
            'profile_ids' => 'required|array',
            'profile_ids.*' => 'integer|exists:profiles,id',
        ])['profile_ids'];

        $this->applyProfilePermissionsToUser($user, $profileIds);

        return response()->json([
            'status' => 'ok',
            'message' => 'Profile permissions applied.',
            'data' => UserModulePermission::with('module')->where('user_id', $user->id)->get(),
        ]);
    }

    private function applyProfilePermissionsToUser(User $user, array $profileIds): void
    {
        if (!$this->hasProfilesTable())
            return;

        $profilePerms = ProfileModulePermission::whereIn('profile_id', $profileIds)->get();
        foreach ($profilePerms as $pp) {
            UserModulePermission::updateOrInsert(
                ['user_id' => $user->id, 'module_id' => $pp->module_id],
                [
                    'can_view' => $pp->can_view,
                    'can_create' => $pp->can_create,
                    'can_edit' => $pp->can_edit,
                    'can_delete' => $pp->can_delete,
                    'granted_by' => auth()->id(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    // ═════════════════════════════════════════════════════════════
    // ROLES
    // ═════════════════════════════════════════════════════════════

    public function roleIndex(Request $request)
    {
        if (!$this->hasRolesTable()) {
            return response()->json(['status' => 'ok', 'data' => ['data' => [], 'total' => 0]]);
        }

        $roles = Role::withCount('users')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate($request->per_page ?? 50);

        return response()->json(['status' => 'ok', 'data' => $roles]);
    }

    public function roleStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '_'),
            'description' => $request->description,
            'is_active' => $request->input('is_active', true),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Role created.', 'data' => $role], 201);
    }

    public function roleUpdate(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $role->update(array_merge(
            $request->only(['name', 'description', 'is_active']),
            ['slug' => Str::slug($request->name ?? $role->name, '_'), 'updated_by' => auth()->id()]
        ));

        return response()->json(['status' => 'ok', 'message' => 'Role updated.', 'data' => $role->fresh()]);
    }

    public function roleDestroy(string $id)
    {
        $role = Role::findOrFail($id);
        if ($role->users()->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete a role that has users assigned.'], 422);
        }
        $role->delete();
        return response()->json(['status' => 'ok', 'message' => 'Role deleted.']);
    }

    // ═════════════════════════════════════════════════════════════
    // PROFILES
    // ═════════════════════════════════════════════════════════════

    public function profileIndex(Request $request)
    {
        if (!$this->hasProfilesTable()) {
            return response()->json(['status' => 'ok', 'data' => ['data' => [], 'total' => 0]]);
        }

        $profiles = Profile::withCount('users')
            ->with(['modulePermissions.module'])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate($request->per_page ?? 50);

        return response()->json(['status' => 'ok', 'data' => $profiles]);
    }

    public function profileShow(string $id)
    {
        $profile = Profile::with(['modulePermissions.module'])->findOrFail($id);
        return response()->json(['status' => 'ok', 'data' => $profile]);
    }

    public function profileStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:profiles,name',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $profile = Profile::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '_'),
            'description' => $request->description,
            'is_active' => $request->input('is_active', true),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Profile created.', 'data' => $profile], 201);
    }

    public function profileUpdate(Request $request, string $id)
    {
        $profile = Profile::findOrFail($id);

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('profiles')->ignore($profile->id)],
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $profile->update(array_merge(
            $request->only(['name', 'description', 'is_active']),
            ['slug' => Str::slug($request->name ?? $profile->name, '_'), 'updated_by' => auth()->id()]
        ));

        return response()->json(['status' => 'ok', 'message' => 'Profile updated.', 'data' => $profile->fresh()]);
    }

    public function profileDestroy(string $id)
    {
        $profile = Profile::findOrFail($id);
        if ($profile->users()->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete a profile that has users assigned.'], 422);
        }
        $profile->modulePermissions()->delete();
        $profile->delete();
        return response()->json(['status' => 'ok', 'message' => 'Profile deleted.']);
    }

    public function profileUpdatePermissions(Request $request, string $id)
    {
        $profile = Profile::findOrFail($id);

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.module_id' => 'required|integer|exists:modules,id',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ]);

        ProfileModulePermission::where('profile_id', $profile->id)->delete();

        $perms = collect($request->permissions)->map(fn($p) => [
            'profile_id' => $profile->id,
            'module_id' => $p['module_id'],
            'can_view' => $p['can_view'] ?? true,
            'can_create' => $p['can_create'] ?? false,
            'can_edit' => $p['can_edit'] ?? false,
            'can_delete' => $p['can_delete'] ?? false,
            'granted_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        ProfileModulePermission::insert($perms);

        return response()->json([
            'status' => 'ok',
            'message' => 'Profile permissions updated.',
            'data' => ProfileModulePermission::with('module')->where('profile_id', $profile->id)->get(),
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    // MODULES
    // ═════════════════════════════════════════════════════════════

    public function moduleIndex(Request $request)
    {
        if (!Schema::hasTable('modules')) {
            return response()->json(['status' => 'ok', 'data' => []]);
        }

        $modules = Module::orderBy('sort_order')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->group, fn($q) => $q->where('group', $request->group))
            ->get();

        return response()->json(['status' => 'ok', 'data' => $modules]);
    }

    public function moduleStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:modules,name',
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $module = Module::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '_'),
            'group' => $request->group,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->input('is_active', true),
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Module created.', 'data' => $module], 201);
    }

    public function moduleUpdate(Request $request, string $id)
    {
        $module = Module::findOrFail($id);

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('modules')->ignore($module->id)],
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $module->update(array_merge(
            $request->only(['name', 'group', 'description', 'sort_order', 'is_active']),
            ['slug' => Str::slug($request->name ?? $module->name, '_')]
        ));

        return response()->json(['status' => 'ok', 'message' => 'Module updated.', 'data' => $module->fresh()]);
    }

    public function moduleDestroy(string $id)
    {
        $module = Module::findOrFail($id);
        $module->profilePermissions()->delete();
        $module->userPermissions()->delete();
        $module->delete();
        return response()->json(['status' => 'ok', 'message' => 'Module deleted.']);
    }
}