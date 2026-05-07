<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class MaterialWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.Material.index');
    }

    public function create()
    {
        return view('admin.mes.Material.form');
    }

    public function edit($id)
    {
        return view('admin.mes.Material.form', ['item_id' => $id]);
    }
}