<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('username', $request->login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been disabled. Please contact an administrator.',
            ], 403);
        }

        $user->update(['last_login_at' => now()]);
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;
        $tokenId = $user->tokens()->latest('id')->first()?->id;

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $tokenId ? 'tkn_' . $tokenId : null,
            'logged_at' => now(),
        ]);

        // Load permissions for management + normal users (admin has full_access, no need)
        if (!$user->isAdmin()) {
            $user->load(['modulePermissions.module' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')]);
        }

        return response()->json([
            'status' => 'ok',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $this->userResource($user),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $tokenId = $user->currentAccessToken()?->id;

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $tokenId ? 'tkn_' . $tokenId : null,
            'logged_at' => now(),
        ]);

        $user->currentAccessToken()->delete();

        return response()->json(['status' => 'ok', 'message' => 'Logged out successfully.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        // Load permissions for management + normal users (not admin — they have full_access)
        if (!$user->isAdmin()) {
            $user->load([
                'modulePermissions.module' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $this->userResource($user),
        ]);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'ok',
            'data' => ['token' => $token, 'token_type' => 'Bearer'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // SINGLE SOURCE OF TRUTH for user payload sent to the browser.
    //
    // PERMISSION MODEL:
    //   admin      → full_access: true  (sees everything, no checks)
    //   management → full_access: false (permission-based, same as normal)
    //   normal     → full_access: false (permission-based)
    //
    // ALWAYS returns both full_access and permissions fields so the
    // JS can() function always has what it needs from localStorage.
    // ─────────────────────────────────────────────────────────────
    public function userResource(User $user): array
    {
        // ONLY admin gets full access — management is permission-based
        $isFullAccess = $user->isAdmin();

        $permissions = [];
        // Load permissions for management AND normal users
        if (!$isFullAccess && $user->relationLoaded('modulePermissions')) {
            $permissions = $user->modulePermissions
                ->filter(fn($p) => $p->module !== null)
                ->map(fn($p) => [
                    'module' => $p->module->slug,
                    'module_name' => $p->module->name,
                    'can_view' => (bool) $p->can_view,
                    'can_create' => (bool) $p->can_create,
                    'can_edit' => (bool) $p->can_edit,
                    'can_delete' => (bool) $p->can_delete,
                ])
                ->values()
                ->toArray();
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'department' => $user->department,
            'phone' => $user->phone,
            'is_active' => (bool) $user->is_active,
            'last_login_at' => $user->last_login_at,
            'full_access' => $isFullAccess,   // true ONLY for admin
            'permissions' => $permissions,    // populated for management + normal
        ];
    }
}