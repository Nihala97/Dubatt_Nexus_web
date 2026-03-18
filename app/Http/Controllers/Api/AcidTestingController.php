<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Api/AcidTestingController.php
// ─────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcidTesting;
use App\Models\AcidTestPercentageDetail;
use App\Models\AcidStockCondition;
use App\Models\Receiving;
use App\Models\Material;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class AcidTestingController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // ── GET /api/acid-testings ────────────────────────────────────
    public function index(Request $request)
    {
        $tests = AcidTesting::with(['supplier', 'createdBy', 'updatedBy', 'details'])
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->status !== null && $request->status !== '', fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('test_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('test_date', '<=', $request->date_to))
            ->when($request->lot_number, fn($q) => $q->where('lot_number', 'like', "%{$request->lot_number}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json(['status' => 'ok', 'data' => $tests]);
    }

    // ── GET /api/acid-testings/stock-conditions ───────────────────
    // Returns all active conditions for client-side stock code resolution
    public function stockConditions()
    {
        $conditions = AcidStockCondition::where('is_active', true)
            ->orderByRaw('COALESCE(min_pct, -999) ASC')
            ->get(['stock_code', 'description', 'min_pct', 'max_pct']);

        return response()->json(['status' => 'ok', 'data' => $conditions]);
    }

    // ── GET /api/acid-testings/available-lots ─────────────────────
    // Returns lots that are: receiving status=1 (submitted/approved)
    // AND not already in acid_test_header (any status)
    public function availableLots(Request $request)
    {
        // Lots already used in acid testing (draft OR submitted)
        $usedLots = AcidTesting::withTrashed()->pluck('lot_number')->toArray();

        $lots = Receiving::with('supplier')
            ->where('status', 1)                       // submitted/approved receivings only
            ->whereNotIn('lot_no', $usedLots)          // not already in acid testing
            ->when($request->search, fn($q) => $q->where('lot_no', 'like', "%{$request->search}%"))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => [
                'lot_no' => $r->lot_no,
                'supplier_name' => $r->supplier->supplier_name ?? '—',
                'vehicle_number' => $r->vehicle_no ?? $r->vehicle_number ?? '—',
                'invoice_qty' => $r->invoice_qty,
                'received_qty' => $r->received_qty,
                'supplier_id' => $r->supplier_id,
                'receipt_date' => $r->receipt_date ?? $r->created_at?->format('Y-m-d'),
            ]);

        return response()->json(['status' => 'ok', 'data' => $lots]);
    }

    // ── GET /api/acid-testings/lot-check/{lotNo} ─────────────────
    // Validates a lot before use — returns prefill data or error
    public function lotCheck($lotNo)
    {
        $receiving = Receiving::with('supplier')
            ->where('lot_no', $lotNo)
            ->first();

        if (!$receiving) {
            return response()->json(['status' => 'error', 'message' => 'Lot number not found in receiving records.'], 404);
        }

        if ((int) $receiving->status !== 1) {
            return response()->json(['status' => 'error', 'message' => 'This lot has not been submitted/approved in receiving.'], 422);
        }

        $existing = AcidTesting::where('lot_number', $lotNo)->first();
        if ($existing) {
            $statusLabel = (int) $existing->status === 0 ? 'draft' : 'submitted';
            return response()->json([
                'status' => 'error',
                'message' => "This lot is already in acid testing (status: {$statusLabel}).",
            ], 422);
        }

        return response()->json([
            'status' => 'ok',
            'data' => [
                'lot_no' => $receiving->lot_no,
                'supplier_id' => $receiving->supplier_id,
                'supplier_name' => $receiving->supplier->supplier_name ?? '—',
                'vehicle_number' => $receiving->vehicle_no ?? $receiving->vehicle_number ?? '',
                'invoice_qty' => $receiving->invoice_qty,
                'received_qty' => $receiving->received_qty,
            ],
        ]);
    }

    // ── GET /api/acid-testings/{id} ───────────────────────────────
    public function show($id)
    {
        $test = AcidTesting::with([
            'supplier',
            'createdBy',
            'updatedBy',
            'details' => function ($query) {
                $query->where('is_active', 1);
            }
        ])
            ->where('is_active', 1)
            ->findOrFail($id);

        return response()->json(['status' => 'ok', 'data' => $test]);
    }

    // ── POST /api/acid-testings ───────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'test_date' => 'required|date',
            'lot_number' => 'required|string|exists:receivings,lot_no',
            'supplier_id' => 'required|integer',
            'vehicle_number' => 'nullable|string|max:50',
            'avg_pallet_weight' => 'required|numeric|min:0',
            'foreign_material_weight' => 'nullable|numeric|min:0',
            'invoice_qty' => 'required|numeric|min:0',
            'received_qty' => 'required|numeric|min:0',
            'avg_pallet_and_foreign_weight' => 'required|numeric|min:0',
            'details' => 'required|array|min:1',
            'details.*.pallet_no' => 'required',
            'details.*.ulab_type' => 'required|string|max:100',
            'details.*.gross_weight' => 'required|numeric|min:0',
            'details.*.net_weight' => 'required|numeric',
            'details.*.initial_weight' => 'nullable|numeric|min:0',
            'details.*.drained_weight' => 'nullable|numeric|min:0',
            'details.*.stock_code' => 'nullable|string|max:20',
        ]);

        // Check lot not already taken
        if (AcidTesting::where('lot_number', $request->lot_number)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'This lot already has an acid test record.'], 422);
        }

        $header = AcidTesting::create([
            'test_date' => $request->test_date,
            'lot_number' => $request->lot_number,
            'supplier_id' => $request->supplier_id,
            'vehicle_number' => $request->vehicle_number,
            'avg_pallet_weight' => $request->avg_pallet_weight,
            'foreign_material_weight' => $request->foreign_material_weight ?? 0,
            'invoice_qty' => $request->invoice_qty,
            'received_qty' => $request->received_qty,
            'avg_pallet_and_foreign_weight' => $request->avg_pallet_and_foreign_weight,
            'status' => 0,
            'is_active' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->syncDetails($header, $request->details);

        // Mark receiving as in-testing
        Receiving::where('lot_no', $request->lot_number)
            ->update(['status' => 2, 'updated_by' => auth()->id()]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Acid test saved successfully.',
            'data' => $header->load('details', 'supplier'),
        ], 201);
    }

    // ── PUT /api/acid-testings/{id} ───────────────────────────────
    public function update(Request $request, $id)
    {
        $header = AcidTesting::findOrFail($id);

        if ((int) $header->status >= 1) {
            return response()->json(['status' => 'error', 'message' => 'Cannot edit — record is already submitted.'], 422);
        }

        $request->validate([
            'test_date' => 'sometimes|required|date',
            'avg_pallet_weight' => 'sometimes|required|numeric|min:0',
            'foreign_material_weight' => 'nullable|numeric|min:0',
            'vehicle_number' => 'nullable|string|max:50',
            'avg_pallet_and_foreign_weight' => 'required|numeric|min:0',
            'details' => 'sometimes|required|array|min:1',
            'details.*.pallet_no' => 'required',
            'details.*.ulab_type' => 'required|string|max:100',
            'details.*.gross_weight' => 'required|numeric|min:0',
            'details.*.net_weight' => 'required|numeric',
            'details.*.initial_weight' => 'nullable|numeric|min:0',
            'details.*.drained_weight' => 'nullable|numeric|min:0',
            'details.*.stock_code' => 'nullable|string|max:20',
        ]);

        $header->update(array_merge(
            $request->only([
                'test_date',
                'avg_pallet_weight',
                'foreign_material_weight',
                'avg_pallet_and_foreign_weight',
                'vehicle_number',
            ]),
            ['updated_by' => auth()->id()]
        ));

        if ($request->has('details')) {
            $this->syncDetails($header, $request->details);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Acid test updated successfully.',
            'data' => $header->fresh(['details', 'supplier']),
        ]);
    }

    // ── PATCH /api/acid-testings/{id}/status ─────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer|in:0,1,2,3,4']);

        $header = AcidTesting::with('details')->findOrFail($id);
        $oldStatus = $header->status;

        $header->update(['status' => $request->status, 'updated_by' => auth()->id()]);

        if ($request->status == 1 && $oldStatus != 1) {
            // Processing Acid Testing Stock
            // 1. OUT: Deduct incoming material
            $receiving = Receiving::where('lot_no', $header->lot_number)->first();
            if ($receiving && $receiving->material_id) {
                // Determine the total input qty to deduce
                $this->inventoryService->stockOut(
                    $receiving->material_id,
                    $header->received_qty,
                    'AcidTesting',
                    $header->id,
                    $header->lot_number,
                    auth()->id()
                );
            }

            // 2. IN: Add resulting test output materials to stock based on stock code
            foreach ($header->details as $detail) {
                if ($detail->stock_code) {
                    $material = Material::where('stock_code', $detail->stock_code)
                        ->orWhere('material_code', $detail->stock_code)
                        ->first();
                    if ($material) {
                        $this->inventoryService->stockIn(
                            $material->id,
                            $detail->net_weight, // using net_weight as output qty
                            'AcidTesting',
                            $header->id,
                            $header->lot_number,
                            auth()->id()
                        );
                    }
                }
            }
        } elseif ($request->status != 1 && $oldStatus == 1) {
            $this->inventoryService->revertTransaction('AcidTesting', $header->id, auth()->id());
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Status updated.',
            'data' => ['status' => $request->status],
        ]);
    }

    // ── DELETE /api/acid-testings/{id} ───────────────────────────
    public function destroy($id)
    {
        $header = AcidTesting::findOrFail($id);

        if ((int) $header->status >= 1) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete a submitted record.'], 422);
        }

        Receiving::where('lot_no', $header->lot_number)
            ->update(['status' => 1, 'updated_by' => auth()->id()]);

        $header->details()->delete();
        $header->delete();

        return response()->json(['status' => 'ok', 'message' => 'Deleted successfully.']);
    }

    // ── Private: sync detail rows ─────────────────────────────────
    private function syncDetails(AcidTesting $header, array $details): void
    {
        // AcidTestPercentageDetail::where('acid_test_id', $header->id)->delete();
        AcidTestPercentageDetail::where('acid_test_id', $header->id)->update(['is_active' => 0]);
        foreach ($details as $row) {
            $initial = (float) ($row['initial_weight'] ?? 0);
            $drained = (float) ($row['drained_weight'] ?? 0);
            $gross = (float) ($row['gross_weight'] ?? 0);

            $weightDiff = max(0, $initial - $drained);

            $avgAcidPct = ($initial > 0)
                ? round(($drained / $initial) * 100, 2)
                : 0;

            AcidTestPercentageDetail::create([
                'acid_test_id' => $header->id,
                'pallet_no' => $row['pallet_no'],
                'gross_weight' => $gross,
                'net_weight' => (float) ($row['net_weight'] ?? 0),
                'ulab_type' => $row['ulab_type'],
                'stock_code' => $row['stock_code'] ?? null,
                'initial_weight' => $initial,
                'drained_weight' => $drained,
                'weight_difference' => $weightDiff,
                'avg_acid_pct' => $avgAcidPct,
                'remarks' => $row['remarks'] ?? null,
                'status' => 0,
                'is_active' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
    }
}