<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmeltingBatch;
use App\Models\SmeltingRawMaterial;
use App\Models\SmeltingFluxChemical;
use App\Models\SmeltingProcessDetail;
use App\Models\SmeltingTemperatureRecord;
use App\Models\SmeltingOutputBlock;
use App\Models\Material;
use App\Models\StockLedger;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmeltingBatchController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // ══════════════════════════════════════════════════════════════════
    // INDEX  GET /api/smelting-batches
    // ══════════════════════════════════════════════════════════════════
    public function index(Request $request): JsonResponse
    {
        $query = SmeltingBatch::with([
            'rawMaterials',
            'fluxChemicals',
            'processDetails',
            'temperatureRecords',
            'outputBlocks',
        ])->where('is_active', true);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('rotary_no'))
            $query->where('rotary_no', $request->rotary_no);
        if ($request->filled('date_from'))
            $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('search'))
            $query->where('batch_no', 'like', '%' . $request->search . '%');

        $batches = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        // Stats for index page cards
        $stats = [
            'total' => SmeltingBatch::where('is_active', true)->count(),
            'draft' => SmeltingBatch::where('is_active', true)->where('status', 0)->count(),
            'submitted' => SmeltingBatch::where('is_active', true)->where('status', '>=', 1)->count(),
            'this_month' => SmeltingBatch::where('is_active', true)->whereMonth('date', now()->month)->count(),
        ];

        return response()->json(['status' => 'ok', 'data' => $batches, 'stats' => $stats]);
    }

    // ══════════════════════════════════════════════════════════════════
    // GENERATE BATCH NO  GET /api/smelting-batches/generate-batch-no
    // Returns next auto number: SMT-2026-0001
    // ══════════════════════════════════════════════════════════════════
    public function generateBatchNo(): JsonResponse
    {
        $year = now()->format('Y');
        $prefix = 'SMT-' . $year . '-';

        $last = SmeltingBatch::where('batch_no', 'like', $prefix . '%')
            ->orderByDesc('batch_no')
            ->value('batch_no');

        $next = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        $batchNo = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

        return response()->json(['status' => 'ok', 'batch_no' => $batchNo]);
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE  POST /api/smelting-batches
    // ══════════════════════════════════════════════════════════════════
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'batch_no' => 'required|string|unique:smelting_batches,batch_no',
            'charge_no' => 'required|string|max:50',
            'rotary_no' => 'required|integer|in:1,2',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            $batch = SmeltingBatch::create([
                'batch_no' => $request->batch_no,
                'charge_no' => $request->charge_no,
                'rotary_no' => $request->rotary_no,
                'date' => $request->date,
                'start_time' => $request->date . ' ' . ($request->start_time ?? '00:00') . ':00',
                'end_time' => $request->end_time ? $request->date . ' ' . $request->end_time . ':00' : null,
                'lpg_consumption' => $request->lpg_consumption,
                'o2_consumption' => $request->o2_consumption,
                'id_fan_initial' => $request->id_fan_initial,
                'id_fan_final' => $request->id_fan_final,
                'id_fan_consumption' => $this->calcDiff($request->id_fan_final, $request->id_fan_initial),
                'rotary_power_initial' => $request->rotary_power_initial,
                'rotary_power_final' => $request->rotary_power_final,
                'rotary_power_consumption' => $this->calcDiff($request->rotary_power_final, $request->rotary_power_initial),
                'output_material' => $request->output_material,
                'output_qty' => $request->output_qty,
                'remarks' => $request->remarks ?? null,
                'status' => 0,
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->saveChildren($batch, $request, $userId);

            DB::commit();

            return response()->json([
                'status' => 'ok',
                'message' => 'Smelting batch created.',
                'data' => $batch->load(['rawMaterials', 'fluxChemicals', 'processDetails', 'temperatureRecords', 'outputBlocks']),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Smelting store failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // SHOW  GET /api/smelting-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function show($id): JsonResponse
    {
        $batch = SmeltingBatch::with([
            'rawMaterials',
            'fluxChemicals',
            'processDetails',
            'temperatureRecords',
            'outputBlocks',
        ])->findOrFail($id);

        return response()->json(['status' => 'ok', 'data' => $batch]);
    }

    // ══════════════════════════════════════════════════════════════════
    // UPDATE  PUT /api/smelting-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function update(Request $request, $id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);

        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Batch already submitted.'], 422);
        }

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            $batch->update([
                'rotary_no' => $request->rotary_no ?? $batch->rotary_no,
                'charge_no' => $request->charge_no ?? $batch->charge_no,
                'date' => $request->date ?? $batch->date,
                'start_time' => $request->filled('start_time')
                    ? ($request->date ?? \Carbon\Carbon::parse($batch->date)->format('Y-m-d')) . ' ' . $request->start_time . ':00'
                    : $batch->start_time,
                'end_time' => $request->filled('end_time')
                    ? ($request->date ?? \Carbon\Carbon::parse($batch->date)->format('Y-m-d')) . ' ' . $request->end_time . ':00'
                    : $batch->end_time,
                'lpg_consumption' => $request->lpg_consumption ?? $batch->lpg_consumption,
                'o2_consumption' => $request->o2_consumption ?? $batch->o2_consumption,
                'id_fan_initial' => $request->id_fan_initial ?? $batch->id_fan_initial,
                'id_fan_final' => $request->id_fan_final ?? $batch->id_fan_final,
                'id_fan_consumption' => $this->calcDiff($request->id_fan_final ?? $batch->id_fan_final, $request->id_fan_initial ?? $batch->id_fan_initial),
                'rotary_power_initial' => $request->rotary_power_initial ?? $batch->rotary_power_initial,
                'rotary_power_final' => $request->rotary_power_final ?? $batch->rotary_power_final,
                'rotary_power_consumption' => $this->calcDiff($request->rotary_power_final ?? $batch->rotary_power_final, $request->rotary_power_initial ?? $batch->rotary_power_initial),
                'output_material' => $request->output_material ?? $batch->output_material,
                'output_qty' => $request->output_qty ?? $batch->output_qty,
                'remarks' => $request->remarks ?? $batch->remarks,
                'updated_by' => $userId,
            ]);

            $this->saveChildren($batch, $request, $userId, delete: true);

            DB::commit();

            return response()->json([
                'status' => 'ok',
                'message' => 'Batch updated.',
                'data' => $batch->fresh(['rawMaterials', 'fluxChemicals', 'processDetails', 'temperatureRecords', 'outputBlocks']),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Smelting update failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // AUTOSAVE  POST /api/smelting-batches/{id}/autosave
    // ══════════════════════════════════════════════════════════════════
    public function autosave(Request $request, $id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);

        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 400);
        }

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            // Update header fields that are present
            $headerFields = [
                'charge_no',
                'rotary_no',
                'date',
                'start_time',
                'end_time',
                'lpg_consumption',
                'o2_consumption',
                'id_fan_initial',
                'id_fan_final',
                'rotary_power_initial',
                'rotary_power_final',
                'output_material',
                'output_qty',
                'remarks',
            ];

            $updates = ['updated_by' => $userId];
            foreach ($headerFields as $f) {
                if ($request->filled($f))
                    $updates[$f] = $request->input($f);
            }

            // Recalculate consumptions if relevant fields provided
            $idFanFinal = $request->id_fan_final ?? $batch->id_fan_final;
            $idFanInitial = $request->id_fan_initial ?? $batch->id_fan_initial;
            $rpFinal = $request->rotary_power_final ?? $batch->rotary_power_final;
            $rpInitial = $request->rotary_power_initial ?? $batch->rotary_power_initial;

            $updates['id_fan_consumption'] = $this->calcDiff($idFanFinal, $idFanInitial);
            $updates['rotary_power_consumption'] = $this->calcDiff($rpFinal, $rpInitial);

            $batch->update($updates);
            $this->saveChildren($batch, $request, $userId, delete: true);

            DB::commit();

            return response()->json(['status' => 'ok', 'saved_at' => now()->format('H:i:s')]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // SUBMIT  POST /api/smelting-batches/{id}/submit
    // ══════════════════════════════════════════════════════════════════
    public function submit($id): JsonResponse
    {
        $batch = SmeltingBatch::with(['rawMaterials', 'fluxChemicals'])->findOrFail($id);

        if ($batch->status == 1) {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 422);
        }

        $batch->update(['status' => 1, 'updated_by' => auth()->id()]);

        $this->processSmeltingInventory($batch);

        return response()->json(['status' => 'ok', 'message' => 'Batch submitted and locked.']);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4',
        ]);

        $batch = SmeltingBatch::with(['rawMaterials', 'fluxChemicals'])->findOrFail($id);
        $oldStatus = $batch->status;

        // Prevent cancelling if already in downstream process
        if ($request->status == 4 && $batch->status >= 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot cancel — lot is already in downstream processing.',
            ], 422);
        }

        $batch->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        if ($request->status == 1 && $oldStatus != 1) {
            $this->processSmeltingInventory($batch);
        } elseif ($request->status != 1 && $oldStatus == 1) {
            $this->inventoryService->revertTransaction('Smelting', $batch->id, auth()->id());
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Status updated successfully.',
            'data' => ['status' => $batch->status],
        ]);
    }

    private function processSmeltingInventory($batch)
    {
        // 1. OUT: Raw Materials
        foreach ($batch->rawMaterials as $rm) {
            if ($rm->raw_material_id) {
                $this->inventoryService->stockOut(
                    $rm->raw_material_id,
                    $rm->raw_material_qty,
                    'Smelting',
                    $batch->id,
                    $batch->batch_no,
                    auth()->id()
                );
            }
        }

        // 2. OUT: Flux Chemicals
        foreach ($batch->fluxChemicals as $chem) {
            if ($chem->chemical_id) {
                $this->inventoryService->stockOut(
                    $chem->chemical_id,
                    $chem->qty,
                    'Smelting',
                    $batch->id,
                    $batch->batch_no,
                    auth()->id()
                );
            }
        }

        // 3. IN: Output production
        if ($batch->output_qty > 0 && $batch->output_material) {
            // output_material could be ID or String. If ID, search it, else create
            $material = null;
            if (is_numeric($batch->output_material)) {
                $material = Material::find($batch->output_material);
            }
            if (!$material) {
                $material = Material::firstOrCreate(
                    ['material_name' => $batch->output_material],
                    ['material_code' => strtoupper(str_replace(' ', '_', $batch->output_material)) . '-SMT-OUT', 'status' => 1, 'is_active' => 1]
                );
            }

            $this->inventoryService->stockIn(
                $material->id,
                $batch->output_qty,
                'Smelting',
                $batch->id,
                $batch->batch_no,
                auth()->id()
            );
        }
    }
    // ══════════════════════════════════════════════════════════════════
    // DESTROY  DELETE /api/smelting-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function destroy($id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);

        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete submitted batch.'], 422);
        }

        $batch->update(['is_active' => false, 'updated_by' => auth()->id()]);

        return response()->json(['status' => 'ok', 'message' => 'Batch deleted.']);
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════

    private function calcDiff($final, $initial): ?float
    {
        if (is_numeric($final) && is_numeric($initial)) {
            $diff = (float) $final - (float) $initial;
            return $diff >= 0 ? round($diff, 3) : null;
        }
        return null;
    }

    private function saveChildren(SmeltingBatch $batch, Request $request, int $userId, bool $delete = false): void
    {
        // ── Raw Materials ─────────────────────────────────────────────
        if ($request->has('raw_materials')) {
            if ($delete)
                SmeltingRawMaterial::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->raw_materials ?? [] as $row) {
                if (empty($row['raw_material_id']))
                    continue;
                $qty = (float) ($row['raw_material_qty'] ?? 0);
                $yieldPct = (float) ($row['raw_material_yield_pct'] ?? 0);
                SmeltingRawMaterial::create([
                    'smelting_batch_id' => $batch->id,
                    'raw_material_id' => $row['raw_material_id'],
                    'bbsu_batch_id' => $row['bbsu_batch_id'] ?? null,
                    'bbsu_batch_no' => $row['bbsu_batch_no'] ?? null,
                    'raw_material_qty' => $qty,
                    'raw_material_yield_pct' => $yieldPct,
                    'expected_output_qty' => $yieldPct > 0 ? round($qty * $yieldPct / 100, 3) : 0,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Flux Chemicals ────────────────────────────────────────────
        if ($request->has('flux_chemicals')) {
            if ($delete)
                SmeltingFluxChemical::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->flux_chemicals ?? [] as $row) {
                if (empty($row['chemical_id']))
                    continue;
                SmeltingFluxChemical::create([
                    'smelting_batch_id' => $batch->id,
                    'chemical_id' => $row['chemical_id'],
                    'qty' => $row['qty'] ?? 0,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Process Details ───────────────────────────────────────────
        if ($request->has('process_details')) {
            if ($delete)
                SmeltingProcessDetail::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->process_details ?? [] as $row) {
                if (empty($row['process_name']))
                    continue;
                $totalTime = 0;
                if (!empty($row['start_time']) && !empty($row['end_time'])) {
                    try {
                        $start = \Carbon\Carbon::parse($row['start_time']);
                        $end = \Carbon\Carbon::parse($row['end_time']);
                        $totalTime = max(0, round($end->diffInMinutes($start, false) * -1, 2));
                        if ($totalTime <= 0)
                            $totalTime = round($end->diffInMinutes($start), 2);
                    } catch (\Exception $e) {
                        $totalTime = 0;
                    }
                }
                SmeltingProcessDetail::create([
                    'smelting_batch_id' => $batch->id,
                    'process_name' => $row['process_name'],
                    'start_time' => $row['start_time'] ?? null,
                    'end_time' => $row['end_time'] ?? null,
                    'total_time' => $totalTime,
                    'firing_mode' => $row['firing_mode'] ?? null,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Temperature Records ───────────────────────────────────────
        if ($request->has('temperature_records')) {
            if ($delete)
                SmeltingTemperatureRecord::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->temperature_records ?? [] as $row) {
                SmeltingTemperatureRecord::create([
                    'smelting_batch_id' => $batch->id,
                    'record_time' => $row['record_time'] ?? null,
                    'inside_temp_before_charging' => $row['inside_temp_before_charging'] ?? null,
                    'process_gas_chamber_temp' => $row['process_gas_chamber_temp'] ?? null,
                    'shell_temp' => $row['shell_temp'] ?? null,
                    'bag_house_temp' => $row['bag_house_temp'] ?? null,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Output Blocks ─────────────────────────────────────────────
        if ($request->has('output_blocks')) {
            if ($delete)
                SmeltingOutputBlock::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->output_blocks ?? [] as $row) {
                if (empty($row['material_id']))
                    continue;
                SmeltingOutputBlock::create([
                    'smelting_batch_id' => $batch->id,
                    'material_id' => $row['material_id'],
                    'block_sl_no' => $row['block_sl_no'] ?? 0,
                    'block_weight' => $row['block_weight'] ?? 0,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // MATERIALS LIST  GET /api/materials
    // Returns SELECT * FROM materials (items table) for dropdowns
    // ══════════════════════════════════════════════════════════════════
    public function getMaterials(Request $request): JsonResponse
    {
        $query = DB::table('materials');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = (int) $request->get('per_page', 500);
        $materials = $query->select('id', 'name', 'unit')->orderBy('name')->paginate($perPage);

        return response()->json(['status' => 'ok', 'data' => $materials]);
    }

    // ══════════════════════════════════════════════════════════════════
    // BBSU LOTS FOR MATERIAL  GET /api/smelting-batches/bbsu-lots/{materialId}
    //
    // UPDATED: Now reads from stock_ledgers + materials.available_qty
    // instead of scanning bbsu_batches directly.
    // The route URL and method name are unchanged so existing clients
    // continue to work without any frontend change.
    // Response shape is the same — popup shows available_qty and lets
    // user type an assign qty, then press OK.
    // ══════════════════════════════════════════════════════════════════
    public function getBbsuLots(Request $request, int $materialId): JsonResponse
    {
        $material = \App\Models\Material::find($materialId);

        if (!$material) {
            return response()->json([
                'status' => 'ok',
                'data' => [],
                'message' => 'Material not found.',
            ]);
        }

        $availableQty = (float) $material->available_qty;

        // Recent IN ledger rows — shows the user where current stock came from
        $ledger = StockLedger::where('material_id', $materialId)
            ->where('is_active', true)
            ->where('in_qty', '>', 0)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['process_type', 'doc_no', 'in_qty', 'balance_qty', 'created_at'])
            ->map(fn($r) => [
                'source' => $r->process_type,
                'doc_no' => $r->doc_no,
                'in_qty' => (float) $r->in_qty,
                'created_at' => optional($r->created_at)->format('d-m-Y H:i'),
            ]);

        if ($availableQty <= 0) {
            return response()->json([
                'status' => 'ok',
                'data' => [],
                'available_qty' => 0,
                'ledger' => $ledger,
                'message' => 'No available stock for this material.',
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'available_qty' => $availableQty,
            'material' => [
                'id' => $material->id,
                'name' => $material->material_name ?? $material->name ?? 'Unknown',
                'unit' => $material->unit ?? 'KG',
            ],
            'ledger' => $ledger,
            // Keep legacy 'data' key so any existing frontend code still works
            'data' => [
                [
                    'bbsu_batch_id' => $material->id,
                    'batch_no' => 'STOCK-' . $material->id,
                    'material_id' => $material->id,
                    'material_name' => $material->material_name ?? $material->name ?? 'Unknown',
                    'material_unit' => $material->unit ?? 'KG',
                    'output_qty' => $availableQty,
                    'already_used_qty' => 0,
                    'available_qty' => $availableQty,
                ],
            ],
        ]);
    }


    public function getAllBbsuLots(Request $request): JsonResponse
    {
        // Get all materials with available_qty > 0
        $materials = \App\Models\Material::where('available_qty', '>', 0)
            ->orderBy('material_name')
            ->get();

        if ($materials->isEmpty()) {
            return response()->json([
                'status' => 'ok',
                'data' => [],
                'message' => 'No BBSU lots available.',
            ]);
        }

        $bbsuLots = $materials->map(function ($material) {
            $availableQty = (float) $material->available_qty;

            // Get recent ledger entries for each material (optional - remove if not needed)
            // $ledger = StockLedger::where('material_id', $material->id)
            //     ->where('is_active', true)
            //     ->where('in_qty', '>', 0)
            //     ->orderByDesc('created_at')
            //     ->limit(5)
            //     ->get(['process_type', 'doc_no', 'in_qty', 'balance_qty', 'created_at'])
            //     ->map(fn($r) => [
            //         'source' => $r->process_type,
            //         'doc_no' => $r->doc_no,
            //         'in_qty' => (float) $r->in_qty,
            //         'created_at' => optional($r->created_at)->format('d-m-Y H:i'),
            //     ]);

            return [
                'bbsu_batch_id' => $material->id,
                'batch_no' => 'STOCK-' . $material->id,
                'material_id' => $material->id,
                'material_name' => $material->material_name ?? $material->name ?? 'Unknown',
                'material_unit' => $material->unit ?? 'KG',
                'output_qty' => $availableQty,
                'already_used_qty' => 0,
                'available_qty' => $availableQty,
                // 'ledger' => $ledger, // Optional: remove if not needed for performance
            ];
        });

        return response()->json([
            'status' => 'ok',
            'data' => $bbsuLots,
            'total' => $bbsuLots->count(),
            'message' => 'BBSU lots retrieved successfully.',
        ]);
    }



}