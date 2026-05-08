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
    /**
     * POST /api/auth/login
     */
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

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Revoke old tokens (single session)
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        // ✅ Log the login — get token ID after creation
        $tokenId = $user->tokens()->latest('id')->first()?->id;

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $tokenId ? 'tkn_' . $tokenId : null,
            'logged_at' => now(),
        ]);

        return response()->json([
            'status' => 'ok',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $this->userResource($user),
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $tokenId = $user->currentAccessToken()?->id;

        // ✅ Log the logout — before revoking so token context is still available
        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $tokenId ? 'tkn_' . $tokenId : null,
            'logged_at' => now(),
        ]);

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 'ok',
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user()->load([
            'modulePermissions.module' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
        ]);

        return response()->json([
            'status' => 'ok',
            'data' => $this->userResource($user),
        ]);
    }

    /**
     * POST /api/auth/refresh
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'ok',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    // ── Private Helpers ───────────────────────────────────────────

    private function userResource(User $user): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'department' => $user->department,
            'phone' => $user->phone,
            'is_active' => $user->is_active,
            'last_login_at' => $user->last_login_at,
        ];

        if ($user->isNormal() && $user->relationLoaded('modulePermissions')) {
            $data['permissions'] = $user->modulePermissions->map(fn($p) => [
                'module' => $p->module?->slug,
                'module_name' => $p->module?->name,
                'can_view' => $p->can_view,
                'can_create' => $p->can_create,
                'can_edit' => $p->can_edit,
                'can_delete' => $p->can_delete,
            ]);
        }

        if ($user->isAdmin() || $user->isManagement()) {
            $data['full_access'] = true;
        }

        return $data;
    }
}