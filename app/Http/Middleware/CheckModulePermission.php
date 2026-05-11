<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Middleware/CheckModulePermission.php
//
// FIX: Receivers and other normal users were getting 403 on
// GET /api/suppliers and GET /api/materials because the entire
// route group was wrapped in middleware('module:suppliers') which
// blocked ALL access — even reading the dropdown lists needed for
// Receiving / Smelting / BBSU / Refining create forms.
//
// RULE: Modules in LOOKUP_MODULES allow any authenticated user to
// perform GET (read-only) requests without a permission row.
// Write operations (POST/PUT/DELETE) still need the real can_*
// permission. This is safe — suppliers and materials are reference
// data that every module needs to read for dropdowns.
// ─────────────────────────────────────────────────────────────────
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModulePermission
{
    /**
     * Modules used as lookup/dropdown data by other modules.
     * Any authenticated user may GET these without explicit can_view.
     */
    private const LOOKUP_MODULES = ['suppliers_master', 'materials_master'];

    public function handle(Request $request, Closure $next, string $moduleSlug, string $action = 'can_view')
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        // ONLY admin has full bypass — management checks permissions like normal users
        if ($user->isAdmin()) {
            return $next($request);
        }

        // ── LOOKUP BYPASS ─────────────────────────────────────────
        // Allow any authenticated normal user to GET supplier/material
        // data so form dropdowns work regardless of their permissions.
        // Only bypasses read (GET + can_view); writes still need perm.
        if (
            $action === 'can_view'
            && $request->isMethod('GET')
            && in_array($moduleSlug, self::LOOKUP_MODULES, true)
        ) {
            return $next($request);
        }

        // ── Standard permission check ─────────────────────────────
        if (!$user->canAccessModule($moduleSlug, $action)) {
            return response()->json([
                'status' => 'error',
                'message' => "You do not have {$action} permission for '{$moduleSlug}'.",
            ], 403);
        }

        return $next($request);
    }
}