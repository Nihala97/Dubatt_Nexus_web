<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcidTesting;
use App\Models\AcidTestPercentageDetail;
use App\Models\Receiving;
use Illuminate\Http\Request;

class AcidTestingController extends Controller
{
    /**
     * GET /api/acid-testings
     * List all acid tests with filters and pagination
     */
    public function index(Request $request)
    {
        $tests = AcidTesting::with(['supplier', 'createdBy', 'updatedBy', 'details'])
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->status !== null, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('test_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('test_date', '<=', $request->date_to))
            ->when($request->lot_number, fn($q) => $q->where('lot_number', 'like', "%{$request->lot_number}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json(['status' => 'ok', 'data' => $tests]);
    }

    /**
     * GET /api/acid-testings/prefill/{lotNo}
     * Auto-fill form fields from Receiving data when lot number is selected
     */
    public function prefill($lotNo)
    {
        $receiving = Receiving::with('supplier')
            ->where('lot_no', $lotNo)
            ->first();

        if (!$receiving) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Lot number not found in receiving records.',
            ], 404);
        }

        // Check if acid test already exists for this lot
        $exists = AcidTesting::where('lot_number', $lotNo)->exists();
        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Acid test already exists for this lot number.',
            ], 422);
        }

        // Check receiving is approved before acid testing
        if ($receiving->status < 1) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Receiving lot is not yet approved.',
            ], 422);
        }

        return response()->json([
            'status' => 'ok',
            'data'   => [
                // Auto-filled from receiving (read-only in form)
                'supplier_id'      => $receiving->supplier_id,
                'supplier_name'    => $receiving->supplier->supplier_name ?? null,
                'vehicle_number'   => $receiving->vehicle_number,
                'lot_number'       => $receiving->lot_no,
                'received_qty'     => $receiving->received_qty,
                'invoice_qty'      => $receiving->invoice_qty,
                // Fields user must enter manually
                // test_date, avg_pallet_weight, foreign_material_weight
            ],
        ]);
    }

    /**
     * POST /api/acid-testings
     * Create acid test header + pallet details in one call
     */
    public function store(Request $request)
    {
        $request->validate([
            // Header fields
            'test_date'                => 'required|date',
            'lot_number'               => 'required|string|exists:receivings,lot_no|unique:acid_test_header,lot_number',
            'supplier_id'              => 'required|integer|exists:suppliers,id',
            'vehicle_number'           => 'nullable|string|max:50',
            'avg_pallet_weight'        => 'required|numeric|min:0',
            'foreign_material_weight'  => 'nullable|numeric|min:0',
            'invoice_qty'              => 'required|numeric|min:0',
            'received_qty'             => 'required|numeric|min:0',
            'avg_pallet_and_foreign_weight' => 'required|numeric|min:0',

            // Pallet details (at least 1 required)
            'details'                          => 'required|array|min:1',
            'details.*.pallet_no'              => 'required|integer|min:1',
            'details.*.gross_weight'           => 'required|numeric|min:0',
            'details.*.net_weight'             => 'required|numeric|min:0',
            'details.*.ulab_type'              => 'required|string|max:50',
            'details.*.initial_weight'         => 'required|numeric|min:0',
            'details.*.drained_weight'         => 'required|numeric|min:0',
            'details.*.remarks'                => 'nullable|string|max:500',
        ]);

        // Calculate avg_pallet_and_foreign_weight for header
        $avgPalletAndForeign = ($request->avg_pallet_weight ?? 0) + ($request->foreign_material_weight ?? 0);

        // Create header
        $header = AcidTesting::create([
            'test_date'                      => $request->test_date,
            'lot_number'                     => $request->lot_number,
            'supplier_id'                    => $request->supplier_id,
            'vehicle_number'                 => $request->vehicle_number,
            'avg_pallet_weight'              => $request->avg_pallet_weight,
            'foreign_material_weight'        => $request->foreign_material_weight ?? 0,
            'invoice_qty'                    => $request->invoice_qty,
            'received_qty'                   => $request->received_qty,
            'avg_pallet_and_foreign_weight'  => $request->avg_pallet_and_foreign_weight,
            'status'                         => 0,
            'is_active'                      => true,
            'created_by'                     => auth()->id(),
            'updated_by'                     => auth()->id(),
        ]);

        // Create pallet details with auto-calculated fields
        $details = [];
        foreach ($request->details as $row) {
            $weightDifference = $row['initial_weight'] - $row['drained_weight'];
            $avgAcidPct       = $row['gross_weight'] > 0
                ? round(($weightDifference / $row['gross_weight']) * 100, 2)
                : 0;

            $details[] = AcidTestPercentageDetail::create([
                'acid_test_id'                   => $header->id,
                'pallet_no'                      => $row['pallet_no'],
                'gross_weight'                   => $row['gross_weight'],
                'net_weight'                     => $row['net_weight'],
                'ulab_type'                      => $row['ulab_type'],
                'initial_weight'                 => $row['initial_weight'],
                'drained_weight'                 => $row['drained_weight'],
                'weight_difference'              => $weightDifference,
                'avg_acid_pct'                   => $avgAcidPct,
                'remarks'                        => $row['remarks'] ?? null,
                'status'                         => 0,
                'is_active'                      => true,
                'created_by'                     => auth()->id(),
                'updated_by'                     => auth()->id(),
            ]);
        }

        // Update receiving status to In Progress (2)
        Receiving::where('lot_no', $request->lot_number)
            ->update(['status' => 2, 'updated_by' => auth()->id()]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Acid test saved successfully.',
            'data'    => $header->load('details', 'supplier'),
        ], 201);
    }

    /**
     * GET /api/acid-testings/{id}
     * Get single acid test with all pallet details
     */
    public function show($id)
    {
        $test = AcidTesting::with(['supplier', 'details', 'createdBy', 'updatedBy'])
            ->findOrFail($id);

        return response()->json(['status' => 'ok', 'data' => $test]);
    }

    /**
     * GET /api/acid-testings/lot/{lotNo}
     * Get acid test by lot number
     */
    public function getByLot($lotNo)
    {
        $test = AcidTesting::with(['supplier', 'details'])
            ->where('lot_number', $lotNo)
            ->first();

        if (!$test) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No acid test found for this lot number.',
            ], 404);
        }

        return response()->json(['status' => 'ok', 'data' => $test]);
    }

    /**
     * PUT /api/acid-testings/{id}
     * Update header + replace pallet details
     */
    public function update(Request $request, $id)
    {
        $header = AcidTesting::findOrFail($id);

        if ($header->status >= 2) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot edit — acid test is already completed.',
            ], 422);
        }

        $request->validate([
            'test_date'               => 'sometimes|required|date',
            'avg_pallet_weight'       => 'sometimes|required|numeric|min:0',
            'foreign_material_weight' => 'nullable|numeric|min:0',
            'vehicle_number'          => 'nullable|string|max:50',
            'details.*.avg_pallet_and_foreign_weight' => 'required|numeric|min:0',

            'details'                          => 'sometimes|required|array|min:1',
            'details.*.pallet_no'              => 'required|integer|min:1',
            'details.*.gross_weight'           => 'required|numeric|min:0',
            'details.*.net_weight'             => 'required|numeric|min:0',
            'details.*.ulab_type'              => 'required|string|max:50',
            'details.*.initial_weight'         => 'required|numeric|min:0',
            'details.*.drained_weight'         => 'required|numeric|min:0',
            'details.*.remarks'                => 'nullable|string|max:500',
        ]);

        $header->update(array_merge(
            $request->only(['test_date', 'avg_pallet_weight', 'foreign_material_weight', 'avg_pallet_and_foreign_weight', 'vehicle_number']),
            ['updated_by' => auth()->id()]
        ));

        // Replace pallet details if provided
        if ($request->has('details')) {
            AcidTestPercentageDetail::where('acid_test_id', $header->id)->delete();

            foreach ($request->details as $row) {
                $weightDifference = $row['initial_weight'] - $row['drained_weight'];
                $avgAcidPct       = $row['gross_weight'] > 0
                    ? round(($weightDifference / $row['gross_weight']) * 100, 2)
                    : 0;

                AcidTestPercentageDetail::create([
                    'acid_test_id'                  => $header->id,
                    'pallet_no'                     => $row['pallet_no'],
                    'gross_weight'                  => $row['gross_weight'],
                    'net_weight'                    => $row['net_weight'],
                    'ulab_type'                     => $row['ulab_type'],
                    'initial_weight'                => $row['initial_weight'],
                    'drained_weight'                => $row['drained_weight'],
                    'weight_difference'             => $weightDifference,
                    'avg_acid_pct'                  => $avgAcidPct,
                    'remarks'                       => $row['remarks'] ?? null,
                    'status'                        => 0,
                    'is_active'                     => true,
                    'created_by'                    => auth()->id(),
                    'updated_by'                    => auth()->id(),
                ]);
            }
        }

        return response()->json([
            'status'  => 'ok',
            'message' => 'Acid test updated successfully.',
            'data'    => $header->fresh(['details', 'supplier']),
        ]);
    }

    /**
     * PATCH /api/acid-testings/{id}/status
     * Update status
     * 0=Pending, 1=Approved, 2=In Progress, 3=Completed, 4=Cancelled
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4',
        ]);

        $header = AcidTesting::findOrFail($id);

        $header->update([
            'status'     => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Status updated successfully.',
            'data'    => ['status' => $header->status, 'status_label' => $header->status_label],
        ]);
    }

    /**
     * DELETE /api/acid-testings/{id}
     * Soft delete acid test and its details
     */
    public function destroy($id)
    {
        $header = AcidTesting::findOrFail($id);

        if ($header->status >= 2) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete — acid test is already in progress or completed.',
            ], 422);
        }

        // Revert receiving status back to Approved (1)
        Receiving::where('lot_no', $header->lot_number)
            ->update(['status' => 1, 'updated_by' => auth()->id()]);

        $header->details()->delete();
        $header->delete();

        return response()->json([
            'status'  => 'ok',
            'message' => 'Acid test deleted successfully.',
        ]);
    }
}
