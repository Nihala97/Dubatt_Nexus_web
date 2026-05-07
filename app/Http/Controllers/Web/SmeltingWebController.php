<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class SmeltingWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.smelting.index');
    }

    public function create()
    {
        return view('admin.mes.smelting.form');
    }

    public function edit(int $id)
    {
        return view('admin.mes.smelting.form');
    }
    public function destroy($id)
    {
        $res = \Illuminate\Support\Facades\Http::withToken(session('auth_token'))
            ->delete(url('/api/smelting-batches/' . $id));

        if ($res->successful()) {
            return redirect()->route('admin.mes.smelting.index')
                ->with('success', 'Batch deleted successfully.');
        }

        return back()->with('error', 'Failed to delete batch.');
    }
}