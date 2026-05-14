<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/ReceivingWebController.php
// Only serves Blade views. All data comes from the API via JS.
// ─────────────────────────────────────────────────────────────────

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ReceivingWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.receiving.index');
    }

    // public function create()
    // {
    //     return view('admin.mes.receiving.form');
    // }

    // public function edit($id)
    // {
    //     // Pass id to view just in case, but form loads data via JS apiFetch
    //     return view('admin.mes.receiving.form', ['item_id' => $id]);
    // }

    public function create()
    {
        $suppliers = \App\Models\Supplier::orderBy('supplier_name')
            ->get(['id', 'supplier_name', 'supplier_code']);

        $materials = \App\Models\Material::orderBy('secondary_name')
            ->get(['id', 'secondary_name', 'material_code']);

        return view('admin.mes.receiving.form', compact('suppliers', 'materials')); // <-- was missing
    }

    public function edit($id)
    {
        $suppliers = \App\Models\Supplier::orderBy('supplier_name')
            ->get(['id', 'supplier_name', 'supplier_code']);

        $materials = \App\Models\Material::orderBy('secondary_name')
            ->get(['id', 'secondary_name', 'material_code']);

        return view('admin.mes.receiving.form', compact('suppliers', 'materials', 'id') + ['item_id' => $id]);
    }
    public function destroy($id)
    {
        return redirect()->route('admin.mes.receiving.index');
    }
}
