<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/AdminWebController.php
// Only serves Blade views. All data comes from API via JS.
// ─────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AdminWebController extends Controller
{
    // GET /admin/users
    public function users()
    {
        return view('admin.settings.users.index');
    }

    // GET /admin/roles
    public function roles()
    {
        return view('admin.settings.roles.index');
    }

    // GET /admin/profiles
    public function profiles()
    {
        return view('admin.settings.profiles.index');
    }

    // GET /admin/modules
    public function modules()
    {
        return view('admin.settings.modules.index');
    }
}