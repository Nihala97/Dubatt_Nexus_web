<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/ReceivingWebController.php
// Only serves Blade views. All data comes from the API via JS.
// ─────────────────────────────────────────────────────────────────

// namespace App\Http\Controllers\Web;

// use App\Http\Controllers\Controller;

// class AcidTestingWebController extends Controller
// {
//     public function index()
//     {
//         return view('admin.mes.acidTesting.index');
//     }

//     public function create()
//     {
//         return view('admin.mes.acidTesting.form');
//     }

//     public function edit($id)
//     {
//         // Pass id to view just in case, but form loads data via JS apiFetch
//         return view('admin.mes.acidTesting.form', ['item_id' => $id]);
//     }
// }

// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/AcidTestingWebController.php
// Only serves Blade views. All data comes from the API via JS.
// ─────────────────────────────────────────────────────────────────

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AcidTesting;
use App\Models\Company;

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
        // Pass id to view — form loads all data via JS apiFetch
        return view('admin.mes.acidTesting.form', ['item_id' => $id]);
    }

     // Named printView (not print) to avoid PHP reserved-word conflicts
     public function printView($id)
     {
         $test = AcidTesting::where('is_active', 1)
         ->with([
             'supplier' => function ($query) {
                 $query->where('is_active', 1);
             },
             'details' => function ($query) {
                 $query->where('is_active', 1);
             },
             'createdBy' // usually no filter needed for users
         ])
     ->findOrFail($id);
 
         if ((int) $test->status < 1) {
             return redirect()
                 ->to(url('/admin/mes/acidTesting/' . $id . '/edit'))
                 ->with('error', 'Only submitted records can be printed.');
         }
 
         $company = Company::first();
 
         return view('admin.mes.acidTesting.print', compact('test', 'company'));
     }

    // Actual deletion is handled via DELETE /api/acid-testings/{id} from JS
    public function destroy($id)
    {
        return redirect()->route('admin.mes.acidTesting.index');
    }
}