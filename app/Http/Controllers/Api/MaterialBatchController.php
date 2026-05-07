<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialBatchController extends Controller
{
    // GET /api/materials
    public function index(Request $request)
    {
        $q = Material::where('is_active', true);

        if ($request->filled('status'))
            $q->where('status', $request->status);

        if ($request->filled('category'))
            $q->where('category', $request->category);

        if ($request->filled('unit'))
            $q->where('unit', $request->unit);

        if ($request->filled('search'))
            $q->where(function ($sq) use ($request) {
                $sq->where('material_name', 'like', '%' . $request->search . '%')
                    ->orWhere('material_code', 'like', '%' . $request->search . '%')
                    ->orWhere('stock_code', 'like', '%' . $request->search . '%')
                    ->orWhere('secondary_name', 'like', '%' . $request->search . '%');
            });

        $data = $q->with('createdBy')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json(['status' => 'ok', 'data' => $data]);
    }

    // GET /api/materials/{id}
    public function show($id)
    {
        $material = Material::with('createdBy', 'updatedBy')
            ->where('is_active', true)
            ->findOrFail($id);
        return response()->json(['status' => 'ok', 'data' => $material]);
    }

    // POST /api/materials  (also handles HTML form submission)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_code' => 'required|string|max:100|unique:materials,material_code',
            'material_name' => 'required|string|max:255',
            'secondary_name' => 'nullable|string|max:255',
            'stock_code' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:1,0',
        ]);

        $material = Material::create(array_merge($validated, [
            'status' => $validated['status'] ?? 1,
            'is_active' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]));

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'data' => $material], 201);
        }

        return redirect()->route('admin.mes.material.edit', $material->id)
            ->with('success', 'Material created successfully.');
    }

    // PUT /api/materials/{id}  (also handles HTML form submission)
    public function update(Request $request, $id)
    {
        $material = Material::where('is_active', true)->findOrFail($id);

        $validated = $request->validate([
            'material_code' => 'required|string|max:100|unique:materials,material_code,' . $id,
            'material_name' => 'required|string|max:255',
            'secondary_name' => 'nullable|string|max:255',
            'stock_code' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:1,0',
        ]);

        $material->update(array_merge($validated, [
            'updated_by' => Auth::id(),
        ]));

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'data' => $material]);
        }

        return redirect()->route('admin.mes.material.edit', $material->id)
            ->with('success', 'Material updated successfully.');
    }

    // DELETE /api/materials/{id}  (also handles HTML form submission)
    public function destroy(Request $request, $id)
    {
        $material = Material::where('is_active', true)->findOrFail($id);
        $material->update(['is_active' => false, 'updated_by' => Auth::id()]);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'message' => 'Material deleted.']);
        }

        return redirect()->route('admin.mes.material.index')
            ->with('success', 'Material deleted successfully.');
    }
}