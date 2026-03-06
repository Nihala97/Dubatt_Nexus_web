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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class BbsuBatchController extends Controller
{
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
                'batch_no'   => $request->batch_no,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'doc_date'   => $request->doc_date,
                'category'   => $request->category,
                'status'     => 0,
                'is_active'  => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // 2. Create input detail rows (dynamic / multiple)
            foreach ($request->input_details as $detail) {
                BbsuInputDetail::create([
                    'bbsu_batch_id'   => $batch->id,
                    'lot_no'          => $detail['lot_no'],
                    'quantity'        => $detail['quantity'],
                    'acid_percentage' => $detail['acid_percentage'],
                    'status'          => 0,
                    'is_active'       => true,
                    'created_by'      => $userId,
                    'updated_by'      => $userId,
                ]);
            }

            // 3. Create output material (single row)
            $om = $request->output_material;
            BbsuOutputMaterial::create([
                'bbsu_batch_id'         => $batch->id,
                'metallic_qty'          => $om['metallic_qty'],
                'metallic_yield'        => $om['metallic_yield'],
                'paste_qty'             => $om['paste_qty'],
                'paste_yield'           => $om['paste_yield'],
                'fines_qty'             => $om['fines_qty'],
                'fines_yield'           => $om['fines_yield'],
                'pp_chips_qty'          => $om['pp_chips_qty'],
                'pp_chips_yield'        => $om['pp_chips_yield'],
                'abs_chips_qty'         => $om['abs_chips_qty'],
                'abs_chips_yield'       => $om['abs_chips_yield'],
                'separator_qty'         => $om['separator_qty'],
                'separator_yield'       => $om['separator_yield'],
                'battery_plates_qty'    => $om['battery_plates_qty'],
                'battery_plates_yield'  => $om['battery_plates_yield'],
                'terminals_qty'         => $om['terminals_qty'],
                'terminals_yield'       => $om['terminals_yield'],
                'acid_qty'              => $om['acid_qty'],
                'acid_yield'            => $om['acid_yield'],
                'status'                => 0,
                'is_active'             => true,
                'created_by'            => $userId,
                'updated_by'            => $userId,
            ]);

            // 4. Create power consumption (single row)
            $pc = $request->power_consumption;
            BbsuPowerConsumption::create([
                'bbsu_batch_id'           => $batch->id,
                'initial_power'           => $pc['initial_power'],
                'final_power'             => $pc['final_power'],
                'total_power_consumption' => $pc['total_power_consumption'],
                'status'                  => 0,
                'is_active'               => true,
                'created_by'              => $userId,
                'updated_by'              => $userId,
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
                'error'   => $e->getMessage(),
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
            'data'   => $batch,
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
                'batch_no'   => $request->batch_no,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'doc_date'   => $request->doc_date,
                'category'   => $request->category,
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
                            'lot_no'          => $detail['lot_no'],
                            'quantity'        => $detail['quantity'],
                            'acid_percentage' => $detail['acid_percentage'],
                            'updated_by'      => $userId,
                        ]);
                } else {
                    // Create new row
                    BbsuInputDetail::create([
                        'bbsu_batch_id'   => $bbsu_batch->id,
                        'lot_no'          => $detail['lot_no'],
                        'quantity'        => $detail['quantity'],
                        'acid_percentage' => $detail['acid_percentage'],
                        'status'          => 0,
                        'is_active'       => true,
                        'created_by'      => $userId,
                        'updated_by'      => $userId,
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
                'data'    => new BbsuBatchResource($bbsu_batch),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('BBSU batch update failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to update BBSU batch.',
                'error'   => $e->getMessage(),
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
            'is_active'  => false,
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
    public function updateStatus(Request $request, BbsuBatch $bbsu_batch): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        $bbsu_batch->update([
            'status'     => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Status updated successfully.',
            'status'  => $bbsu_batch->status,
        ]);
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
                'data'    => []
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
                'data'    => []
            ]);
        }
    
        // Separate ulab_type = 5 and other types
        $typeFive = $data->where('ulab_type', 5);
        $others   = $data->where('ulab_type', '!=', 5);
    
        $result = collect();
    
        // ── Group ulab_type = 5 into one row ─────────────────────────
        if ($typeFive->count() > 0) {
            $first = $typeFive->first();
    
            $result->push([
                'ulab_type'            => 5,
                'material_description' => $first->stockCondition->description ?? 'ACID',
                'lot_no'               => $first->acidTest->receiving->lot_no ?? null,
                'unit'                 => $first->acidTest->receiving->unit   ?? null,
                'avg_acid_pct'         => $typeFive->sum('avg_acid_pct'),
                'net_weight'           => $typeFive->sum('gross_weight'), //mk changed
            ]);
        }
    
        // ── Add other ulab_types as normal rows ───────────────────────
        foreach ($others as $row) {
            $result->push([
                'ulab_type'            => $row->ulab_type,
                'material_description' => $row->stockCondition->description   ?? null,
                'lot_no'               => $row->acidTest->receiving->lot_no   ?? null,
                'unit'                 => $row->acidTest->receiving->unit      ?? null,
                'avg_acid_pct'         => $row->avg_acid_pct,
                'net_weight'           => $row->gross_weight,//mk changed
            ]);
        }
    
        return response()->json([
            'message' => 'Acid summary for lot ' . $lotNo . ' retrieved successfully.',
            'data'    => $result
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
            'data'   => $lots
        ]);
    }
}
