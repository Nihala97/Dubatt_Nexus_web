<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/ReceivingWebController.php
// Only serves Blade views. All data comes from the API via JS.
// ─────────────────────────────────────────────────────────────────

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class BbsuWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.bbsu.index');
    }

    public function create()
    {
        return view('admin.mes.bbsu.form');
    }

    public function edit($id)
    {
        // Pass id to view just in case, but form loads data via JS apiFetch
        return view('admin.mes.bbsu.form', ['item_id' => $id]);
    }
}