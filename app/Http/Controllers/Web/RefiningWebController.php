<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RefiningWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.refining.index');
    }

    public function create()
    {
        return view('admin.mes.refining.form');
    }

    public function edit($id)
    {
        return view('admin.mes.refining.form');
    }

    public function destroy($id)
    {
        // Soft-delete via API from index blade
        return redirect()->route('admin.mes.refining.index');
    }
}