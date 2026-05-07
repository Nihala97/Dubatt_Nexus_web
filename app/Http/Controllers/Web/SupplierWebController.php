<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class SupplierWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.Supplier.index');
    }

    public function create()
    {
        return view('admin.mes.Supplier.form');
    }

    public function edit($id)
    {
        return view('admin.mes.Supplier.form', ['item_id' => $id]);
    }
    
}