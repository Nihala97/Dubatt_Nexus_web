<?php
// ── app/Http/Controllers/Api/MaterialController.php ──────────────

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $materials = Material::query()
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('material_name', 'like', "%{$request->search}%")
                  ->orWhere('material_code', 'like', "%{$request->search}%")
                  ->orWhere('stock_code', 'like', "%{$request->search}%");
            }))
            ->when($request->category,  fn($q) => $q->where('category', $request->category))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('material_name')
            ->paginate($request->per_page ?? 50);

        return response()->json(['status' => 'ok', 'data' => $materials]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'material_code'  => 'required|string|max:50|unique:materials,material_code',
            'material_name'  => 'required|string|max:255',
            'secondary_name' => 'nullable|string|max:255',
            'stock_code'     => 'nullable|string|max:50',
            'category'       => 'nullable|string|max:100',
            'section'        => 'nullable|string|max:100',
            'unit'           => 'nullable|string|max:50',
            'is_active'      => 'boolean',
        ]);

        $material = Material::create(array_merge(
            $request->only(['material_code', 'material_name', 'secondary_name', 'stock_code', 'category', 'section', 'unit', 'is_active']),
            ['created_by' => auth()->id(), 'updated_by' => auth()->id()]
        ));

        return response()->json(['status' => 'ok', 'message' => 'Material created.', 'data' => $material], 201);
    }

    public function show($id)
    {
        return response()->json(['status' => 'ok', 'data' => Material::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $request->validate([
            'material_code'  => ['sometimes', 'required', 'string', 'max:50', Rule::unique('materials')->ignore($material->id)],
            'material_name'  => 'sometimes|required|string|max:255',
            'secondary_name' => 'nullable|string|max:255',
            'stock_code'     => 'nullable|string|max:50',
            'category'       => 'nullable|string|max:100',
            'section'        => 'nullable|string|max:100',
            'unit'           => 'nullable|string|max:50',
            'is_active'      => 'boolean',
        ]);

        $material->update(array_merge(
            $request->only(['material_code', 'material_name', 'secondary_name', 'stock_code', 'category', 'section', 'unit', 'is_active']),
            ['updated_by' => auth()->id()]
        ));

        return response()->json(['status' => 'ok', 'message' => 'Material updated.', 'data' => $material]);
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);

        if ($material->receivings()->count() > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete — material has receiving records.',
            ], 422);
        }

        $material->delete();
        return response()->json(['status' => 'ok', 'message' => 'Material deleted.']);
    }

    public function getStock($id)
    {
        $material = Material::findOrFail($id);
        return response()->json([
            'status' => 'ok',
            'data' => [
                'material_id' => $material->id,
                'material_name' => $material->material_name,
                'unit' => $material->unit,
                'available_qty' => $material->available_qty,
            ]
        ]);
    }
}
