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

    /**
     * GET /api/bbsu-batches
     * List all BBSU batches with optional filters.
     */
    public function index(Request $request)
    {
        $query = BbsuBatch::with(['inputDetails', 'outputMaterial', 'powerConsumption'])
            ->where('is_active', true);

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('doc_date')) {
            $query->whereDate('doc_date', $request->doc_date);
        }

        if ($request->filled('batch_no')) {
            $query->where('batch_no', 'like', '%' . $request->batch_no . '%');
        }

        $batches = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        //return BbsuBatchResource::collection($batches);
        return response()->json(['status' => 'ok', 'data' => $batches]);
    }

    /**
     * POST /api/bbsu-batches
     * Create a new BBSU batch with all child records in one transaction.
     */
    public function store(StoreBbsuBatchRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $userId = auth()->id();

            // 1. Create header (batch)
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

            // 2. Create input detail rows (dynamic / multiple)
            foreach ($request->input_details as $detail) {
                BbsuInputDetail::create([
                    'bbsu_batch_id' => $batch->id,
                    'lot_no' => $detail['lot_no'],
                    'quantity' => $detail['quantity'],
                    'acid_percentage' => $detail['acid_percentage'],
                    'status' => 0,
                    'is_active' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            $totalInput = collect($request->input_details)->sum('quantity');
            $om = $request->output_material;
            $metallic_yield = $totalInput > 0 ? ($om['metallic_qty'] / $totalInput) * 100 : 0;
            $paste_yield = $totalInput > 0 ? ($om['paste_qty'] / $totalInput) * 100 : 0;
            $fines_yield = $totalInput > 0 ? ($om['fines_qty'] / $totalInput) * 100 : 0;
            $pp_chips_yield = $totalInput > 0 ? ($om['pp_chips_qty'] / $totalInput) * 100 : 0;
            $abs_chips_yield = $totalInput > 0 ? ($om['abs_chips_qty'] / $totalInput) * 100 : 0;
            $separator_yield = $totalInput > 0 ? ($om['separator_qty'] / $totalInput) * 100 : 0;
            $battery_plates_yield = $totalInput > 0 ? ($om['battery_plates_qty'] / $totalInput) * 100 : 0;
            $terminals_yield = $totalInput > 0 ? ($om['terminals_qty'] / $totalInput) * 100 : 0;
            $acid_yield = $totalInput > 0 ? ($om['acid_qty'] / $totalInput) * 100 : 0;

            // 3. Create output material (single row)

            BbsuOutputMaterial::create([
                'bbsu_batch_id' => $batch->id,
                'metallic_qty' => $om['metallic_qty'],
                'paste_qty' => $om['paste_qty'],
                'fines_qty' => $om['fines_qty'],
                'pp_chips_qty' => $om['pp_chips_qty'],
                'abs_chips_qty' => $om['abs_chips_qty'],
                'separator_qty' => $om['separator_qty'],
                'battery_plates_qty' => $om['battery_plates_qty'],
                'terminals_qty' => $om['terminals_qty'],
                'acid_qty' => $om['acid_qty'],
                'status' => 0,
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
                'metallic_yield' => $metallic_yield,
                'paste_yield' => $paste_yield,
                'fines_yield' => $fines_yield,
                'pp_chips_yield' => $pp_chips_yield,
                'abs_chips_yield' => $abs_chips_yield,
                'separator_yield' => $separator_yield,
                'battery_plates_yield' => $battery_plates_yield,
                'terminals_yield' => $terminals_yield,
                'acid_yield' => $acid_yield,
            ]);

            // 4. Create power consumption (single row)
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


            $totalOutput =
                ($om['metallic_qty'] ?? 0) +
                ($om['paste_qty'] ?? 0) +
                ($om['fines_qty'] ?? 0) +
                ($om['pp_chips_qty'] ?? 0) +
                ($om['abs_chips_qty'] ?? 0) +
                ($om['separator_qty'] ?? 0) +
                ($om['battery_plates_qty'] ?? 0) +
                ($om['terminals_qty'] ?? 0) +
                ($om['acid_qty'] ?? 0);

            $yieldPercent = $totalInput > 0 ? ($totalOutput / $totalInput) * 100 : 0;

            $batch->update([
                'yield_percentage' => $yieldPercent
            ]);

            DB::commit();

            $batch->load(['inputDetails', 'outputMaterial', 'powerConsumption']);

            return response()->json([
                'message' => 'BBSU batch created successfully.',
                'data' => $batch->load('inputDetails', 'outputMaterial', 'powerConsumption')
                // 'data'    => $batch->load('pc', 'om',),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('BBSU batch store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to create BBSU batch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/bbsu-batches/{bbsu_batch}
     * Show a single BBSU batch with all related data.
     */
    public function show($id)
    {
        $batch = BbsuBatch::with([
            'inputDetails',
            'outputMaterial',
            'powerConsumption',
            'createdBy:id,name',
            'updatedBy:id,name',
        ])->findOrFail($id);

        return response()->json([
            'status' => 'ok',
            'data' => $batch,
        ]);
    }

    /**
     * PUT /api/bbsu-batches/{bbsu_batch}
     * Update an existing BBSU batch and all child records.
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

            // 2. Sync input details
            //    - Soft-delete rows not in the request (by id)
            //    - Update existing rows (id present)
            //    - Create new rows (no id)
            $existingIds = collect($request->input_details)
                ->pluck('id')
                ->filter()
                ->toArray();

            // Delete rows removed by user
            $bbsu_batch->inputDetails()
                ->whereNotIn('id', $existingIds)
                ->update(['is_active' => false, 'updated_by' => $userId]);

            foreach ($request->input_details as $detail) {
                if (!empty($detail['id'])) {
                    // Update existing row
                    BbsuInputDetail::where('id', $detail['id'])
                        ->update([
                            'lot_no' => $detail['lot_no'],
                            'quantity' => $detail['quantity'],
                            'acid_percentage' => $detail['acid_percentage'],
                            'updated_by' => $userId,
                        ]);
                } else {
                    // Create new row
                    BbsuInputDetail::create([
                        'bbsu_batch_id' => $bbsu_batch->id,
                        'lot_no' => $detail['lot_no'],
                        'quantity' => $detail['quantity'],
                        'acid_percentage' => $detail['acid_percentage'],
                        'status' => 0,
                        'is_active' => true,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }

            // 3. Update output material (upsert single row)
            $om = $request->output_material;
            $bbsu_batch->outputMaterial()->updateOrCreate(
                ['bbsu_batch_id' => $bbsu_batch->id],
                array_merge($om, ['updated_by' => $userId])
            );

            // 4. Update power consumption (upsert single row)
            $pc = $request->power_consumption;
            $bbsu_batch->powerConsumption()->updateOrCreate(
                ['bbsu_batch_id' => $bbsu_batch->id],
                array_merge($pc, ['updated_by' => $userId])
            );

            DB::commit();

            $bbsu_batch->load(['inputDetails', 'outputMaterial', 'powerConsumption']);

            return response()->json([
                'message' => 'BBSU batch updated successfully.',
                'data' => new BbsuBatchResource($bbsu_batch),
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

    /**
     * DELETE /api/bbsu-batches/{bbsu_batch}
     * Soft-delete a BBSU batch (set is_active = false).
     */
    public function destroy(BbsuBatch $bbsu_batch): JsonResponse
    {
        $userId = auth()->id();

        $bbsu_batch->update([
            'is_active' => false,
            'updated_by' => $userId,
        ]);

        return response()->json([
            'message' => 'BBSU batch deleted successfully.',
        ]);
    }

    /**
     * PATCH /api/bbsu-batches/{bbsu_batch}/status
     * Update the status of a BBSU batch (draft → submitted → completed).
     */
    // public function updateStatus(Request $request, BbsuBatch $bbsu_batch): JsonResponse
    // {
    //     $request->validate([
    //         'status' => 'required|in:0,1,2',
    //     ]);

    //     $bbsu_batch->update([
    //         'status'     => $request->status,
    //         'updated_by' => auth()->id(),
    //     ]);

    //     return response()->json([
    //         'message' => 'Status updated successfully.',
    //         'status'  => $bbsu_batch->status,
    //     ]);
    // }
    public function submit($id): JsonResponse
    {
        $batch = BbsuBatch::with(['inputDetails', 'outputMaterial'])->findOrFail($id);
        if ($batch->status === 1) {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 422);
        }
        $batch->update(['status' => 1, 'updated_by' => auth()->id()]);

        // Process Inventory
        $this->processBbsuInventory($batch);

        return response()->json(['status' => 'ok', 'message' => 'Batch submitted and locked.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer|in:0,1,2,3,4']);

        $header = BbsuBatch::with(['inputDetails', 'outputMaterial'])->findOrFail($id);
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

    private function processBbsuInventory($batch)
    {
        // 1. OUT stock for input lots
        foreach ($batch->inputDetails as $detail) {
            // we find the receiving or acid test that this lot belongs to, to know the material
            // but the lot usually corresponds to the original material received
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

        // 2. IN stock for fixed outputs
        $outputs = [
            'Metallic Lead' => $batch->outputMaterial->metallic_qty,
            'Paste' => $batch->outputMaterial->paste_qty,
            'Fines' => $batch->outputMaterial->fines_qty,
            'PP Chips' => $batch->outputMaterial->pp_chips_qty,
            'ABS Chips' => $batch->outputMaterial->abs_chips_qty,
            'Separator' => $batch->outputMaterial->separator_qty,
            'Battery Plates' => $batch->outputMaterial->battery_plates_qty,
            'Terminals' => $batch->outputMaterial->terminals_qty,
            'Acid' => $batch->outputMaterial->acid_qty,
        ];

        foreach ($outputs as $name => $qty) {
            $qty = (float) $qty;
            if ($qty > 0) {
                $material = Material::firstOrCreate(
                    ['material_name' => $name],
                    ['material_code' => strtoupper(str_replace(' ', '_', $name)) . '-OUT', 'status' => 1, 'is_active' => 1]
                );
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
    }

    // public function acidSummary(): JsonResponse
    // {
    //     // Load all active details with related acidTest and stockCondition
    //     $data = AcidTestPercentageDetail::with([
    //         'acidTest.receiving',
    //         'stockCondition'
    //     ])
    //     ->where('is_active', true)
    //     ->get();

    //     // Separate ulab_type = 5 and other types
    //     $typeFive = $data->where('ulab_type', 5);
    //     $others   = $data->where('ulab_type', '!=', 5);

    //     $result = collect();

    //     // ── Group ulab_type = 5 into one row ─────────────────────────────
    //     if ($typeFive->count() > 0) {
    //         $first = $typeFive->first();

    //         $result->push([
    //             'ulab_type'            => 5,
    //             'material_description' => $first->stockCondition->description ?? null,
    //             'lot_no'               => $first->acidTest->receiving->lot_no ?? null,
    //             'unit'                 => $first->acidTest->receiving->unit ?? null,
    //             'total_avg_acid_pct'   => $typeFive->sum('avg_acid_pct'),
    //             'total_net_weight'     => $typeFive->sum('net_weight'),
    //         ]);
    //     }

    //     // ── Add other ulab_types as normal rows ──────────────────────────
    //     foreach ($others as $row) {
    //         $result->push([
    //             'ulab_type'            => $row->ulab_type,
    //             'material_description' => $row->stockCondition->description ?? null,
    //             'lot_no'               => $row->acidTest->receiving->lot_no ?? null,
    //             'unit'                 => $row->acidTest->receiving->unit ?? null,
    //             'avg_acid_pct'         => $row->avg_acid_pct,
    //             'net_weight'           => $row->net_weight,
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'BBSU Acid summary report generated successfully.',
    //         'data'    => $result
    //     ]);
    // }

    public function acidSummaryByLot($lotNo): JsonResponse
    {
        // Find the acid test header for this lot number
        $acidTest = AcidTesting::where('lot_number', $lotNo)->first();

        if (!$acidTest) {
            return response()->json([
                'message' => 'No acid test found for this lot number.',
                'data' => []
            ], 404);
        }

        // Load all active details for this specific acid test
        $data = AcidTestPercentageDetail::with([
            'acidTest.receiving',
            'stockCondition'
        ])
            ->where('acid_test_id', $acidTest->id)
            ->where('is_active', true)
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No details found for this lot number.',
                'data' => []
            ]);
        }

        // Separate ulab_type = 5 and other types
        $typeFive = $data->where('ulab_type', 5);
        $others = $data->where('ulab_type', '!=', 5);

        $result = collect();

        // ── Group ulab_type = 5 into one row ─────────────────────────
        if ($typeFive->count() > 0) {
            $first = $typeFive->first();

            $result->push([
                'ulab_type' => 5,
                'material_description' => $first->stockCondition->description ?? 'ACID',
                'lot_no' => $first->acidTest->receiving->lot_no ?? null,
                'unit' => $first->acidTest->receiving->unit ?? null,
                'avg_acid_pct' => $typeFive->sum('avg_acid_pct'),
                'net_weight' => $typeFive->sum('net_weight'), //mk changed
            ]);
        }

        // ── Add other ulab_types as normal rows ───────────────────────
        foreach ($others as $row) {
            $result->push([
                'ulab_type' => $row->ulab_type,
                'material_description' => $row->stockCondition->description ?? null,
                'lot_no' => $row->acidTest->receiving->lot_no ?? null,
                'unit' => $row->acidTest->receiving->unit ?? null,
                'avg_acid_pct' => $row->avg_acid_pct,
                'net_weight' => $row->net_weight,//mk changed
            ]);
        }

        return response()->json([
            'message' => 'Acid summary for lot ' . $lotNo . ' retrieved successfully.',
            'data' => $result
        ]);
    }

    public function lotNumbers()
    {
        $lots = AcidTesting::where('status', 1)
            ->select('id', 'lot_number')
            ->orderBy('lot_number')
            ->get();

        return response()->json([
            'status' => 'ok',
            'data' => $lots
        ]);
    }
}
