<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBbsuBatchRequest;
use App\Http\Requests\UpdateBbsuBatchRequest;
use App\Http\Resources\BbsuBatchResource;
use App\Models\BbsuBatch;
use App\Models\BbsuInputDetail;
use App\Models\BbsuOutputMaterial;
use App\Models\BbsuPowerConsumption;
use App\Models\AcidTestPercentageDetail;
use App\Models\AcidTesting;
use App\Models\Material;
use App\Models\StockLedger;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class BbsuBatchController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Calculate total input qty for a batch from its inputDetails.
     */
    private function totalInputQty(BbsuBatch $batch): float
    {
        return (float) $batch->inputDetails->sum('quantity');
    }

    /**
     * Upsert the 9 output material rows for a batch.
     *
     * $outputRows is an array keyed by material_code:
     *   [ '1007' => ['qty' => 120.5], '1008' => ['qty' => 80.0], ... ]
     *
     * Yield % = qty / totalInputQty * 100 (auto-calculated here).
     */
    private function syncOutputMaterials(BbsuBatch $batch, array $outputRows, int $userId): void
    {
        $totalInput = $this->totalInputQty($batch);

        // MATERIAL_KEYS = [ '1007' => 'metallic', '1008' => 'paste', ... ]
        // These codes match material_code in the materials table exactly.
        foreach (array_keys(BbsuOutputMaterial::MATERIAL_KEYS) as $code) {
            $qty = (float) ($outputRows[$code]['qty'] ?? 0);
            $yieldPct = $totalInput > 0 ? round(($qty / $totalInput) * 100, 4) : 0;

            BbsuOutputMaterial::updateOrCreate(
                [
                    'bbsu_batch_id' => $batch->id,
                    'material_code' => $code,
                ],
                [
                    'qty' => $qty,
                    'yield_pct' => $yieldPct,
                    'status' => 0,
                    'is_active' => true,
                    'updated_by' => $userId,
                    'created_by' => $userId,
                ]
            );
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * GET /api/bbsu-batches
     */
    public function index(Request $request)
    {
        $query = BbsuBatch::with(['inputDetails', 'outputMaterials', 'powerConsumption'])
            ->where('is_active', true);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('category'))
            $query->where('category', $request->category);
        if ($request->filled('doc_date'))
            $query->whereDate('doc_date', $request->doc_date);
        if ($request->filled('batch_no'))
            $query->where('batch_no', 'like', '%' . $request->batch_no . '%');

        $batches = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json(['status' => 'ok', 'data' => $batches]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  STORE
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * POST /api/bbsu-batches
     *
     * Payload output_material format (from blade):
     *   output_material: {
     *     "1007": { "qty": 120.5 },
     *     "1008": { "qty": 80.0 },
     *     ...
     *   }
     */
    public function store(StoreBbsuBatchRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $userId = auth()->id();

            // 1. Create header
            $batch = BbsuBatch::create([
                'batch_no' => $request->batch_no,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'doc_date' => $request->doc_date,
                'category' => $request->category,
                'status' => 0,
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // 2. Create input detail rows
            foreach ($request->input_details as $detail) {
                BbsuInputDetail::create([
                    'bbsu_batch_id' => $batch->id,
                    'lot_no' => $detail['lot_no'],
                    'quantity' => $detail['quantity'],
                    'acid_percentage' => $detail['acid_percentage'],
                    'material_breakdown' => $detail['material_breakdown'] ?? null,
                    'status' => 0,
                    'is_active' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // Reload inputDetails so totalInputQty is accurate
            $batch->load('inputDetails');

            // 3. Create 9 output material rows
            $this->syncOutputMaterials($batch, $request->output_material ?? [], $userId);

            // 4. Create power consumption
            $pc = $request->power_consumption;
            BbsuPowerConsumption::create([
                'bbsu_batch_id' => $batch->id,
                'initial_power' => $pc['initial_power'],
                'final_power' => $pc['final_power'],
                'total_power_consumption' => $pc['total_power_consumption'],
                'status' => 0,
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            DB::commit();

            $batch->load(['inputDetails', 'outputMaterials', 'powerConsumption']);

            return response()->json([
                'message' => 'BBSU batch created successfully.',
                'data' => $batch,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('BBSU batch store failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => 'Failed to create BBSU batch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  SHOW
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * GET /api/bbsu-batches/{id}
     */
    public function show($id)
    {
        $batch = BbsuBatch::with([
            'inputDetails',
            'outputMaterials',
            'powerConsumption',
            'createdBy:id,name',
            'updatedBy:id,name',
        ])->findOrFail($id);

        return response()->json([
            'status' => 'ok',
            'data' => $batch,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  UPDATE
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * PUT /api/bbsu-batches/{bbsu_batch}
     */
    public function update(UpdateBbsuBatchRequest $request, BbsuBatch $bbsu_batch): JsonResponse
    {
        try {
            DB::beginTransaction();

            $userId = auth()->id();

            // 1. Update header
            $bbsu_batch->update([
                'batch_no' => $request->batch_no,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'doc_date' => $request->doc_date,
                'category' => $request->category,
                'updated_by' => $userId,
            ]);

            BbsuInputDetail::where('bbsu_batch_id', $bbsu_batch->id)->delete();

            foreach ($request->input_details as $detail) {
                BbsuInputDetail::create([
                    'bbsu_batch_id' => $bbsu_batch->id,
                    'lot_no' => $detail['lot_no'],
                    'quantity' => $detail['quantity'],
                    'acid_percentage' => $detail['acid_percentage'],
                    'material_breakdown' => $detail['material_breakdown'] ?? null,
                    'status' => 0,
                    'is_active' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // Reload so totalInputQty reflects latest input rows
            $bbsu_batch->load('inputDetails');

            // 3. Sync 9 output material rows (upsert, yield auto-calculated)
            $this->syncOutputMaterials($bbsu_batch, $request->output_material ?? [], $userId);

            // 4. Update power consumption
            $pc = $request->power_consumption;
            $bbsu_batch->powerConsumption()->updateOrCreate(
                ['bbsu_batch_id' => $bbsu_batch->id],
                array_merge($pc, ['updated_by' => $userId])
            );

            DB::commit();

            $bbsu_batch->load(['inputDetails', 'outputMaterials', 'powerConsumption']);

            return response()->json([
                'message' => 'BBSU batch updated successfully.',
                'data' => $bbsu_batch,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('BBSU batch update failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to update BBSU batch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  DESTROY
    // ══════════════════════════════════════════════════════════════════════════

    public function destroy(BbsuBatch $bbsu_batch): JsonResponse
    {
        $userId = auth()->id();
        $bbsu_batch->update(['is_active' => false, 'updated_by' => $userId]);

        return response()->json(['message' => 'BBSU batch deleted successfully.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  STATUS / SUBMIT
    // ══════════════════════════════════════════════════════════════════════════

    public function submit($id): JsonResponse
    {
        $batch = BbsuBatch::with(['inputDetails', 'outputMaterials'])->findOrFail($id);

        if ($batch->status === 1) {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 422);
        }

        $batch->update(['status' => 1, 'updated_by' => auth()->id()]);
        $this->processBbsuInventory($batch);

        return response()->json(['status' => 'ok', 'message' => 'Batch submitted and locked.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer|in:0,1,2,3,4']);

        $header = BbsuBatch::with(['inputDetails', 'outputMaterials'])->findOrFail($id);
        $oldStatus = $header->status;
        $header->update(['status' => $request->status, 'updated_by' => auth()->id()]);

        if ($request->status == 1 && $oldStatus != 1) {
            $this->processBbsuInventory($header);
        } elseif ($request->status != 1 && $oldStatus == 1) {
            $this->inventoryService->revertTransaction('BBSU', $header->id, auth()->id());
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Status updated.',
            'data' => ['status' => $header->status],
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  INVENTORY PROCESSING  (updated to use new output structure)
    // ══════════════════════════════════════════════════════════════════════════

    private function processBbsuInventory($batch)
    {
        // 1. OUT stock for each input lot
        foreach ($batch->inputDetails as $detail) {
            $receiving = \App\Models\Receiving::where('lot_no', $detail->lot_no)->first();
            if ($receiving && $receiving->material_id) {
                $this->inventoryService->stockOut(
                    $receiving->material_id,
                    $detail->quantity,
                    'BBSU',
                    $batch->id,
                    $batch->batch_no,
                    auth()->id()
                );
            }
        }

        // 2. IN stock for each output material row.
        // material_code on the bbsu_output_materials row IS the material_code
        // in the materials table — look it up directly, no firstOrCreate needed.
        foreach ($batch->outputMaterials as $row) {
            $qty = (float) $row->qty;
            if ($qty <= 0)
                continue;

            $material = Material::where('material_code', $row->material_code)->first();

            if (!$material) {
                Log::warning('BBSU inventory: material not found for code ' . $row->material_code);
                continue;
            }

            $this->inventoryService->stockIn(
                $material->id,
                $qty,
                'BBSU',
                $batch->id,
                $batch->batch_no,
                auth()->id()
            );
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ACID SUMMARY ROUTES
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * GET /api/bbsu-batches/acid-summary/{lotNo}
     *
     * Returns per-material-type breakdown for the QTY popup modal.
     * Each row's avg_acid_pct is the CORRECT acid% for that specific material
     * within the lot (calculated as drained/initial × 100 for the group).
     */
    /**
     * GET /api/bbsu-batches/acid-summary/{lotNo}
     * Computes the available quantity for each material type in a lot.
     */
    public function acidSummaryByLot($lotNo): JsonResponse
    {
        $acidTest = AcidTesting::where('lot_number', $lotNo)->first();

        if (!$acidTest) {
            return response()->json([
                'message' => 'No acid test found for this lot number.',
                'data' => [],
            ], 404);
        }

        // Qty already used in ALL active BBSU batches for this lot (draft + submitted)
        $usedQty = DB::table('bbsu_input_details')
            ->join('bbsu_batches', 'bbsu_batches.id', '=', 'bbsu_input_details.bbsu_batch_id')
            ->where('bbsu_batches.is_active', true)
            ->where('bbsu_input_details.is_active', true)
            ->where('bbsu_input_details.lot_no', $lotNo)
            ->sum('bbsu_input_details.quantity');

        $data = AcidTestPercentageDetail::where('acid_test_id', $acidTest->id)
            ->where('is_active', true)
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No details found for this lot number.',
                'data' => [],
            ]);
        }

        $receiving = \App\Models\Receiving::where('lot_no', $acidTest->lot_number)->first();
        $lotNoDisplay = $receiving->lot_no ?? $lotNo;
        $unit = $receiving->unit ?? 'KG';

        $totalNet = (float) $data->sum('net_weight');
        $totalAvailable = max(0, $totalNet - (float) $usedQty);

        // ── Parse exactly which material was consumed from previous JSON breakdowns ──
        $usedByUlab = [];
        $rawBreakdowns = DB::table('bbsu_input_details')
            ->join('bbsu_batches', 'bbsu_batches.id', '=', 'bbsu_input_details.bbsu_batch_id')
            ->where('bbsu_batches.is_active', true)
            ->where('bbsu_input_details.is_active', true)
            ->where('bbsu_input_details.lot_no', $lotNo)
            ->pluck('bbsu_input_details.material_breakdown');

        foreach ($rawBreakdowns as $json) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                foreach ($decoded as $u => $q) {
                    $usedByUlab[$u] = isset($usedByUlab[$u]) ? $usedByUlab[$u] + $q : $q;
                }
            }
        }

        $result = collect();
        $netAvgPct = (float)($acidTest->net_avg_acid_pct ?? 0);

        // ── 1. Group ALL Acid Pallets together as ONE row ──
        $acidRows = $data->filter(fn($r) => (float)$r->avg_acid_pct > 0);
        if ($acidRows->isNotEmpty()) {
            // Resolve the common category for the whole batch
            $condition = $this->resolveConditionByPct($netAvgPct);
            $stockCode = $condition?->stock_code ?? '5';
            $description = $condition?->description ?? 'ULAB - ACID PRESENT';

            $rowNet = (float) $acidRows->sum('net_weight'); 
            $thisMaterialUsed = (float) ($usedByUlab[$stockCode] ?? 0);
            $rowAvail = max(0, $rowNet - $thisMaterialUsed);

            $result->push([
                'ulab_type'            => $stockCode,
                'material_description' => $description,
                'lot_no'               => $lotNoDisplay,
                'unit'                 => $unit,
                'avg_acid_pct'         => round($netAvgPct, 3),
                'net_weight'           => round($rowNet, 3),
                'available_qty'        => round($rowAvail, 3),
                'used_qty'             => round($thisMaterialUsed, 3),
            ]);
        }

        // ── 2. Group Non-Acid (Dry) Pallets by their respective ulab_type ──
        $dryGroups = $data->filter(fn($r) => !((float)$r->avg_acid_pct > 0))->groupBy('ulab_type');
        foreach ($dryGroups as $ulabType => $rows) {
            $rowNet = (float) $rows->sum('net_weight');
            $thisMaterialUsed = (float) ($usedByUlab[$ulabType] ?? 0);
            $rowAvail = max(0, $rowNet - $thisMaterialUsed);

            $description = $rows->first()?->remarks ?? $ulabType;

            $result->push([
                'ulab_type'            => $ulabType,
                'material_description' => $description,
                'lot_no'               => $lotNoDisplay,
                'unit'                 => $unit,
                'avg_acid_pct'         => 0.0,
                'net_weight'           => round($rowNet, 3),
                'available_qty'        => round($rowAvail, 3),
                'used_qty'             => round($thisMaterialUsed, 3),
            ]);
        }

        return response()->json([
            'message'       => 'Acid summary for lot ' . $lotNo . ' retrieved successfully.',
            'total_net'     => round($totalNet, 3),
            'used_qty'      => round((float) $usedQty, 3),
            'available_qty' => round($totalAvailable, 3),
            'data'          => $result,
        ]);
    }

    /**
     * Helper to resolve matching AcidStockCondition for a PCT value.
     */
    private function resolveConditionByPct(float $acidPct): ?\App\Models\AcidStockCondition
    {
        $conditions = \App\Models\AcidStockCondition::where('is_active', true)
            ->where(function ($q) {
                $q->where('min_pct', '>', 0)
                  ->orWhere('max_pct', '>', 0);
            })
            ->orderByRaw('CAST(min_pct AS DECIMAL(10,4)) ASC')
            ->get();

        foreach ($conditions as $condition) {
            $min = (float) $condition->min_pct;
            $max = (float) $condition->max_pct;
            if ($acidPct < $min) continue;
            if ($max > 0 && $acidPct > $max) continue;
            return $condition;
        }
        return null;
    }

    /**
     * GET /api/bbsu-batches/acid-test-lot-numbers
     *
     * Returns submitted lots with their remaining available qty and
     * the WEIGHTED AVERAGE acid% across all material types in the lot.
     * Formula: Sum(qty × acid%) / Sum(qty)
     */
    public function acidTestLotNumbers(Request $request): JsonResponse
    {
        $includeLots = array_filter(explode(',', $request->get('include', '')));

        $lots = AcidTesting::where('status', 1)
            ->select('id', 'lot_number', 'net_avg_acid_pct')
            ->orderBy('lot_number')
            ->get()
            ->map(function ($at) use ($includeLots) {

                $usedQty = DB::table('bbsu_input_details')
                    ->join('bbsu_batches', 'bbsu_batches.id', '=', 'bbsu_input_details.bbsu_batch_id')
                    ->where('bbsu_batches.is_active', true)
                    ->where('bbsu_input_details.is_active', true)
                    ->where('bbsu_input_details.lot_no', $at->lot_number)
                    ->sum('bbsu_input_details.quantity');

                $details = AcidTestPercentageDetail::where('acid_test_id', $at->id)
                    ->where('is_active', true)
                    ->get();

                $netWeight = (float) $details->sum('net_weight');

                // ── EXACT Net Avg Acid % from Acid Testing Header ──
                $avgAcid = (float) $at->net_avg_acid_pct;

                $availableQty = max(0, $netWeight - (float) $usedQty);

                return [
                    'lot_number' => $at->lot_number,
                    'lot_no' => $at->lot_number,
                    'net_weight' => round($netWeight, 3),
                    'used_qty' => round((float) $usedQty, 3),
                    'available_qty' => round($availableQty, 3),
                    'acid_pct' => $avgAcid,   // weighted average
                ];
            })
            ->filter(fn($l) => $l['available_qty'] > 0 || in_array($l['lot_number'], $includeLots))
            ->values();

        return response()->json(['status' => 'ok', 'data' => $lots]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  OUTPUT MATERIAL INFO  (for blade dropdown / table labels)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * GET /api/bbsu-batches/output-material-info?codes=1007,1008,...
     *
     * Returns material_name, secondary_name, stock_code, category from the
     * materials table for the 9 fixed BBSU output material codes.
     * The blade uses this to populate the output table labels at runtime
     * instead of having names hardcoded in JS.
     */
    public function outputMaterialInfo(Request $request): JsonResponse
    {
        $codes = array_filter(
            array_map('trim', explode(',', $request->get('codes', '')))
        );

        // If no codes supplied fall back to all 9 known codes
        if (empty($codes)) {
            $codes = array_keys(BbsuOutputMaterial::MATERIAL_KEYS);
        }

        $materials = Material::whereIn('material_code', $codes)
            ->select('id', 'material_code', 'material_name', 'stock_code', 'category', 'secondary_name')
            ->get()
            ->keyBy('material_code');

        // Return in the same order as MATERIAL_KEYS
        $result = collect(array_keys(BbsuOutputMaterial::MATERIAL_KEYS))
            ->map(fn($code) => $materials->get($code))
            ->filter()
            ->values();

        return response()->json(['status' => 'ok', 'data' => $result]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  GENERATE BATCH NO
    // ══════════════════════════════════════════════════════════════════════════

    public function generateBatchNo(): JsonResponse
    {
        $year = now()->format('Y');
        $last = BbsuBatch::whereYear('created_at', $year)->max('batch_no');
        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
        $batchNo = 'BBSU-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        return response()->json(['status' => 'ok', 'batch_no' => $batchNo]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  LEGACY
    // ══════════════════════════════════════════════════════════════════════════

    public function lotNumbers(): JsonResponse
    {
        $lots = AcidTesting::where('status', 1)
            ->select('id', 'lot_number')
            ->orderBy('lot_number')
            ->get();

        return response()->json(['status' => 'ok', 'data' => $lots]);
    }
}