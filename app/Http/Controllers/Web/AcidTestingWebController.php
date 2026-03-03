<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/ReceivingWebController.php
// Only serves Blade views. All data comes from the API via JS.
// ─────────────────────────────────────────────────────────────────

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AcidTestingWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.acidTesting.index');
    }

    public function create()
    {
        return view('admin.mes.acidTesting.form');
    }

    public function edit($id)
    {
        // Pass id to view just in case, but form loads data via JS apiFetch
        return view('admin.mes.acidTesting.form', ['item_id' => $id]);
    }
}