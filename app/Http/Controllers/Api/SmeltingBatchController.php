<?php
// ═══════════════════════════════════════════════════════════════════
// app/Http/Controllers/Api/SmeltingBatchController.php
// ═══════════════════════════════════════════════════════════════════

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmeltingBatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SmeltingBatchController extends Controller
{
    // ── GET /api/smelting-batches ─────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $query = SmeltingBatch::with([
            'rawMaterials.material:id,material_name,material_code',
            'fluxChemicals.chemical:id,material_name,material_code',
            'processDetails',
            'temperatureRecords',
            'outputBlocks.material:id,material_name,material_code',
            'createdBy:id,name',
        ])->where('is_active', true);

        // Optional filters
        if ($request->filled('rotary_no')) {
            $query->where('rotary_no', $request->rotary_no);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('batch_no', 'like', '%' . $request->search . '%');
        }

        $batches = $query->orderByDesc('created_at')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'ok',
            'data'   => $batches,
        ]);
    }

    // ── GET /api/smelting-batches/{id} ────────────────────────────
    public function show($id): JsonResponse
    {
        $batch = SmeltingBatch::with([
            'rawMaterials.material:id,material_name,material_code',
            'fluxChemicals.chemical:id,material_name,material_code',
            'processDetails',
            'temperatureRecords',
            'outputBlocks.material:id,material_name,material_code',
            'createdBy:id,name',
            'updatedBy:id,name',
        ])->findOrFail($id);

        return response()->json([
            'status' => 'ok',
            'data'   => $batch,
        ]);
    }

    // ── POST /api/smelting-batches ────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Batch header
            'batch_no'                 => 'required|string|unique:smelting_batches,batch_no',
            'rotary_no'                => 'required|integer|in:1,2',
            'date'                     => 'required|date',
            'start_time'               => 'nullable|date',
            'end_time'                 => 'nullable|date',
            'lpg_consumption'          => 'nullable|numeric|min:0',
            'o2_consumption'           => 'nullable|numeric|min:0',
            'id_fan_initial'           => 'nullable|numeric|min:0',
            'id_fan_final'             => 'nullable|numeric|min:0',
            'id_fan_consumption'       => 'nullable|numeric|min:0',
            'rotary_power_initial'     => 'nullable|numeric|min:0',
            'rotary_power_final'       => 'nullable|numeric|min:0',
            'rotary_power_consumption' => 'nullable|numeric|min:0',
            'output_material'          => 'nullable|string',
            'output_qty'               => 'nullable|numeric|min:0',

            // Raw materials
            'raw_materials'                          => 'nullable|array',
            'raw_materials.*.raw_material_id'        => 'required|integer',
            'raw_materials.*.bbsu_batch_id'          => 'nullable|integer',
            'raw_materials.*.bbsu_batch_no'          => 'nullable|string',
            'raw_materials.*.raw_material_qty'       => 'required|numeric|min:0',
            'raw_materials.*.raw_material_yield_pct' => 'nullable|numeric|min:0',
            'raw_materials.*.expected_output_qty'    => 'nullable|numeric|min:0',

            // Flux chemicals
            'flux_chemicals'              => 'nullable|array',
            'flux_chemicals.*.chemical_id'=> 'required|integer',
            'flux_chemicals.*.qty'        => 'required|numeric|min:0',

            // Process details
            'process_details'                => 'nullable|array',
            'process_details.*.process_name' => 'required|string',
            'process_details.*.start_time'   => 'nullable|date',
            'process_details.*.end_time'     => 'nullable|date',
            'process_details.*.total_time'   => 'nullable|numeric|min:0',
            'process_details.*.firing_mode'  => 'nullable|string',

            // Temperature records
            'temperature_records'                              => 'nullable|array',
            'temperature_records.*.record_time'                => 'nullable|date',
            'temperature_records.*.inside_temp_before_charging'=> 'nullable|numeric',
            'temperature_records.*.process_gas_chamber_temp'   => 'nullable|numeric',
            'temperature_records.*.shell_temp'                 => 'nullable|string',
            'temperature_records.*.bag_house_temp'             => 'nullable|string',

            // Output blocks
            'output_blocks'                  => 'nullable|array',
            'output_blocks.*.material_id'    => 'required|integer',
            'output_blocks.*.block_sl_no'    => 'required|integer',
            'output_blocks.*.block_weight'   => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::id();

            // 1. Create batch header
            $batch = SmeltingBatch::create([
                ...$this->batchFields($validated),
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // 2. Raw materials
            foreach ($validated['raw_materials'] ?? [] as $item) {
                $batch->rawMaterials()->create([
                    ...$item,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // 3. Flux chemicals
            foreach ($validated['flux_chemicals'] ?? [] as $item) {
                $batch->fluxChemicals()->create([
                    ...$item,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // 4. Process details
            foreach ($validated['process_details'] ?? [] as $item) {
                $batch->processDetails()->create([
                    ...$item,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // 5. Temperature records
            foreach ($validated['temperature_records'] ?? [] as $item) {
                $batch->temperatureRecords()->create([
                    ...$item,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // 6. Output blocks
            foreach ($validated['output_blocks'] ?? [] as $item) {
                $batch->outputBlocks()->create([
                    ...$item,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'ok',
                'message' => 'Smelting batch created successfully.',
                'data'    => $batch->load([
                    'rawMaterials.material:id,material_name,material_code', 'fluxChemicals.chemical:id,material_name,material_code', 'processDetails',
                    'temperatureRecords', 'outputBlocks.material:id,material_name,material_code',
                ]),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create smelting batch.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ── PUT /api/smelting-batches/{id} ────────────────────────────
    public function update(Request $request, $id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);

        $validated = $request->validate([
            'batch_no'                 => 'sometimes|string|unique:smelting_batches,batch_no,' . $id,
            'rotary_no'                => 'sometimes|integer|in:1,2',
            'date'                     => 'sometimes|date',
            'start_time'               => 'nullable|date',
            'end_time'                 => 'nullable|date',
            'lpg_consumption'          => 'nullable|numeric|min:0',
            'o2_consumption'           => 'nullable|numeric|min:0',
            'id_fan_initial'           => 'nullable|numeric|min:0',
            'id_fan_final'             => 'nullable|numeric|min:0',
            'id_fan_consumption'       => 'nullable|numeric|min:0',
            'rotary_power_initial'     => 'nullable|numeric|min:0',
            'rotary_power_final'       => 'nullable|numeric|min:0',
            'rotary_power_consumption' => 'nullable|numeric|min:0',
            'output_material'          => 'nullable|string',
            'output_qty'               => 'nullable|numeric|min:0',

            'raw_materials'                          => 'nullable|array',
            'raw_materials.*.raw_material_id'        => 'required|integer',
            'raw_materials.*.bbsu_batch_id'          => 'nullable|integer',
            'raw_materials.*.bbsu_batch_no'          => 'nullable|string',
            'raw_materials.*.raw_material_qty'       => 'required|numeric|min:0',
            'raw_materials.*.raw_material_yield_pct' => 'nullable|numeric|min:0',
            'raw_materials.*.expected_output_qty'    => 'nullable|numeric|min:0',

            'flux_chemicals'               => 'nullable|array',
            'flux_chemicals.*.chemical_id' => 'required|integer',
            'flux_chemicals.*.qty'         => 'required|numeric|min:0',

            'process_details'                => 'nullable|array',
            'process_details.*.process_name' => 'required|string',
            'process_details.*.start_time'   => 'nullable|date',
            'process_details.*.end_time'     => 'nullable|date',
            'process_details.*.total_time'   => 'nullable|numeric|min:0',
            'process_details.*.firing_mode'  => 'nullable|string',

            'temperature_records'                               => 'nullable|array',
            'temperature_records.*.record_time'                 => 'nullable|date',
            'temperature_records.*.inside_temp_before_charging' => 'nullable|numeric',
            'temperature_records.*.process_gas_chamber_temp'    => 'nullable|numeric',
            'temperature_records.*.shell_temp'                  => 'nullable|string',
            'temperature_records.*.bag_house_temp'              => 'nullable|string',

            'output_blocks'                => 'nullable|array',
            'output_blocks.*.material_id'  => 'required|integer',
            'output_blocks.*.block_sl_no'  => 'required|integer',
            'output_blocks.*.block_weight' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::id();

            // Update header
            $batch->update([
                ...$this->batchFields($validated),
                'updated_by' => $userId,
            ]);

            // Sync children — delete all and re-insert
            if (isset($validated['raw_materials'])) {
                $batch->rawMaterials()->delete();
                foreach ($validated['raw_materials'] as $item) {
                    $batch->rawMaterials()->create([...$item, 'created_by' => $userId, 'updated_by' => $userId]);
                }
            }

            if (isset($validated['flux_chemicals'])) {
                $batch->fluxChemicals()->delete();
                foreach ($validated['flux_chemicals'] as $item) {
                    $batch->fluxChemicals()->create([...$item, 'created_by' => $userId, 'updated_by' => $userId]);
                }
            }

            if (isset($validated['process_details'])) {
                $batch->processDetails()->delete();
                foreach ($validated['process_details'] as $item) {
                    $batch->processDetails()->create([...$item, 'created_by' => $userId, 'updated_by' => $userId]);
                }
            }

            if (isset($validated['temperature_records'])) {
                $batch->temperatureRecords()->delete();
                foreach ($validated['temperature_records'] as $item) {
                    $batch->temperatureRecords()->create([...$item, 'created_by' => $userId, 'updated_by' => $userId]);
                }
            }

            if (isset($validated['output_blocks'])) {
                $batch->outputBlocks()->delete();
                foreach ($validated['output_blocks'] as $item) {
                    $batch->outputBlocks()->create([...$item, 'created_by' => $userId, 'updated_by' => $userId]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'ok',
                'message' => 'Smelting batch updated successfully.',
                'data'    => $batch->load([
                    'rawMaterials.material:id,material_name,material_code', 'fluxChemicals.chemical:id,material_name,material_code', 'processDetails',
                    'temperatureRecords', 'outputBlocks.material:id,material_name,material_code',
                ]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update smelting batch.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ── DELETE /api/smelting-batches/{id} ─────────────────────────
    public function destroy($id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);
        $batch->update(['is_active' => false, 'updated_by' => Auth::id()]);
        $batch->delete(); // soft delete

        return response()->json([
            'status'  => 'ok',
            'message' => 'Smelting batch deleted successfully.',
        ]);
    }

    /**
     * PATCH /api/smelting-batches/{id}/status
     * Update status only
     * Status: 0=Pending, 1=Approved, 2=In Progress, 3=Completed, 4=Cancelled
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4',
        ]);

        $receiving = SmeltingBatch::findOrFail($id);

        // Prevent cancelling if already in downstream process
        if ($request->status == 4 && $receiving->status >= 2) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot cancel — lot is already in downstream processing.',
            ], 422);
        }

        $receiving->update([
            'status'     => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Status updated successfully.',
            'data'    => ['status' => $receiving->status],
        ]);
    }

    // ── PATCH /api/smelting-batches/{id}/status ───────────────────
    // public function updateStatus(Request $request, $id): JsonResponse
    // {
    //     $request->validate([
    //         'status' => 'required|integer',
    //     ]);

    //     $batch = SmeltingBatch::findOrFail($id);
    //     $batch->update([
    //         'status'     => $request->status,
    //         'updated_by' => Auth::id(),
    //     ]);

    //     return response()->json([
    //         'status'  => 'ok',
    //         'message' => 'Status updated successfully.',
    //         'data'    => $batch,
    //     ]);
    // }
    // ── GET /api/smelting-batches/generate-batch-no ───────────────────
    public function generateBatchNo(): JsonResponse
    {
        $prefix = 'SMB-' . date('Y') . '-';

        do {
            $number   = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $batchNo  = $prefix . $number;
            $exists   = SmeltingBatch::where('batch_no', $batchNo)->exists();
        } while ($exists);

        return response()->json([
            'status' => 'ok',
            'data'   => [
                'batch_no' => $batchNo,
            ],
        ]);
    }
    // ── Private: extract only batch header fields ─────────────────
    private function batchFields(array $data): array
    {
        return array_filter([
            'batch_no'                 => $data['batch_no']                 ?? null,
            'rotary_no'                => $data['rotary_no']                ?? null,
            'date'                     => $data['date']                     ?? null,
            'start_time'               => $data['start_time']               ?? null,
            'end_time'                 => $data['end_time']                 ?? null,
            'lpg_consumption'          => $data['lpg_consumption']          ?? null,
            'o2_consumption'           => $data['o2_consumption']           ?? null,
            'id_fan_initial'           => $data['id_fan_initial']           ?? null,
            'id_fan_final'             => $data['id_fan_final']             ?? null,
            'id_fan_consumption'       => $data['id_fan_consumption']       ?? null,
            'rotary_power_initial'     => $data['rotary_power_initial']     ?? null,
            'rotary_power_final'       => $data['rotary_power_final']       ?? null,
            'rotary_power_consumption' => $data['rotary_power_consumption'] ?? null,
            'output_material'          => $data['output_material']          ?? null,
            'output_qty'               => $data['output_qty']               ?? null,
        ], fn($v) => $v !== null);
    }
}