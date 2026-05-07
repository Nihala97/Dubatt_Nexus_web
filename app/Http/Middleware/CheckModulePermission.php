<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModulePermission
{
    public function handle(Request $request, Closure $next, string $moduleSlug, string $action = 'can_view')
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        // Admin and Management always pass — no permission check needed
        if ($user->isAdmin() || $user->isManagement()) {
            return $next($request);
        }

        // Normal users: check their module permission
        if (!$user->canAccessModule($moduleSlug, $action)) {
            return response()->json([
                'status' => 'error',
                'message' => "You don't have {$action} permission for this module.",
            ], 403);
        }

        return $next($request);
    }
}