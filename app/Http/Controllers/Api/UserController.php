<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use App\Models\UserModulePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * GET /api/users
     * Admin only — list all users with filters
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->with(['createdBy:id,name'])
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when(
                $request->is_active !== null && $request->is_active !== '',
                fn($q) => $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN))
            )
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('username', 'like', "%{$request->search}%");
            }))
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return response()->json(['status' => 'ok', 'data' => $users]);
    }

    /**
     * POST /api/users
     * Admin only — create a new user
     */
    public function store(Request $request)
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

        return response()->json([
            'status' => 'ok',
            'message' => 'User created successfully.',
            'data' => $user,
        ], 201);
    }

    /**
     * GET /api/users/{id}
     * Admin only — get user with permissions
     */
    public function show(string $id)
    {
        $user = User::with([
            'modulePermissions.module',
            'createdBy:id,name',
            'updatedBy:id,name',
        ])->findOrFail($id);

        return response()->json(['status' => 'ok', 'data' => $user]);
    }

    /**
     * PUT /api/users/{id}
     * Admin only — update user details
     */
    public function update(Request $request, string $id)
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

        return response()->json([
            'status' => 'ok',
            'message' => 'User updated successfully.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * DELETE /api/users/{id}
     * Admin only — soft delete user
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status' => 'ok',
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * PATCH /api/users/{id}/toggle-status
     * Admin only — enable or disable a user
     */
    public function toggleStatus(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot disable your own account.',
            ], 422);
        }

        $user->update([
            'is_active' => !$user->is_active,
            'updated_by' => auth()->id(),
        ]);

        // If disabled, revoke all active tokens
        if (!$user->is_active) {
            $user->tokens()->delete();
        }

        return response()->json([
            'status' => 'ok',
            'message' => $user->is_active ? 'User enabled.' : 'User disabled and logged out.',
            'data' => ['is_active' => $user->is_active],
        ]);
    }

    /**
     * PUT /api/users/{id}/permissions
     * Admin only — set module permissions for a normal user
     */
    public function updatePermissions(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Only normal users have configurable permissions
        if (!$user->isNormal()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permissions are only configurable for normal users. Admin and Management have full access.',
            ], 422);
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.module_id' => 'required|integer|exists:modules,id',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ]);

        // Sync permissions — delete old, insert new
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
            'message' => 'Permissions updated successfully.',
            'data' => UserModulePermission::with('module')
                ->where('user_id', $user->id)
                ->get(),
        ]);
    }

    /**
     * GET /api/users/{id}/permissions
     * Get current permissions for a user
     */
    public function getPermissions(string $id)
    {
        $user = User::with(['modulePermissions.module'])->findOrFail($id);

        if (!$user->isNormal()) {
            return response()->json([
                'status' => 'ok',
                'data' => [
                    'full_access' => true,
                    'role' => $user->role,
                    'permissions' => [],
                ],
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $user->modulePermissions->map(fn($p) => [
                'module_id' => $p->module_id,
                'can_view' => (bool) $p->can_view,
                'can_create' => (bool) $p->can_create,
                'can_edit' => (bool) $p->can_edit,
                'can_delete' => (bool) $p->can_delete,
                'module' => $p->module ? [
                    'id' => $p->module->id,
                    'name' => $p->module->name,
                    'slug' => $p->module->slug,
                    'group' => $p->module->group,
                ] : null,
            ]),
        ]);
    }

    /**
     * PUT /api/users/{id}/change-password
     * Admin can reset any password; normal users change their own
     */
    public function changePassword(Request $request, string $id)
    {
        $authUser = auth()->user();
        $targetUser = User::findOrFail($id);

        // Non-admins can only change their own password
        if (!$authUser->isAdmin() && $authUser->id !== $targetUser->id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
        }

        $rules = ['new_password' => 'required|string|min:8|confirmed'];

        // If changing own password, require current password
        if ($authUser->id === $targetUser->id) {
            $rules['current_password'] = 'required|string';
        }

        $request->validate($rules);

        if (isset($rules['current_password']) && !Hash::check($request->current_password, $targetUser->password)) {
            return response()->json(['status' => 'error', 'message' => 'Current password is incorrect.'], 422);
        }

        $targetUser->update([
            'password' => Hash::make($request->new_password),
            'updated_by' => $authUser->id,
        ]);

        // Force re-login after password change
        $targetUser->tokens()->delete();

        return response()->json([
            'status' => 'ok',
            'message' => 'Password changed successfully. Please log in again.',
        ]);
    }
}