<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class MaterialWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.material.index');
    }

    public function create()
    {
        return view('admin.mes.material.form');
    }

    public function edit($id)
    {
        return view('admin.mes.material.form', ['item_id' => $id]);
    }
}
