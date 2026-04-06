<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Api/AcidTestingController.php
// ─────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcidTesting;
use App\Models\AcidTestPercentageDetail;
use App\Models\AcidStockCondition;
use App\Models\Company;
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

    // ─────────────────────────────────────────────────────────────
    // HELPER: Resolve matching AcidStockCondition from acid_stock_conditions
    //
    // Range rules (from DB data):
    //   min_pct > 0  AND  max_pct > 0  → bounded range   e.g. 18% – 24%
    //   min_pct > 0  AND  max_pct = 0  → open upper end  e.g. > 24%
    //   min_pct = 0  AND  max_pct > 0  → bounded from 0  e.g. 0% – 1%
    //   min_pct = 0  AND  max_pct = 0  → non-acid / dry  → excluded at query level
    //
    // Boundaries are INCLUSIVE on both ends:
    //   18.00 <= acidPct <= 24.00  matches 1000001
    //   Open-ended (max=0, min>0):  acidPct >= min  always matches upper
    //
    // ORDER: CAST(min_pct AS DECIMAL) ASC guarantees numeric sort even when
    //   the column is stored as varchar/string in older MySQL schemas.
    //   Tightest lower bound is checked first; first full match wins.
    // ─────────────────────────────────────────────────────────────
    private function resolveCondition(float $acidPct): ?AcidStockCondition
    {
        // Exclude non-acid rows (min=0, max=0) at query level — no PHP skip needed.
        // CAST ensures numeric ordering regardless of column type.
        $conditions = AcidStockCondition::where('is_active', true)
            ->where(function ($q) {
                $q->where('min_pct', '>', 0)
                    ->orWhere('max_pct', '>', 0);
            })
            ->orderByRaw('CAST(min_pct AS DECIMAL(10,4)) ASC')
            ->get();

        foreach ($conditions as $condition) {
            $min = (float) $condition->min_pct;
            $max = (float) $condition->max_pct;

            // Lower bound — always inclusive
            if ($acidPct < $min) {
                continue;
            }

            // Upper bound:
            //   max_pct = 0 with min_pct > 0 → open-ended (no ceiling)
            //   otherwise                    → inclusive upper bound
            if ($max > 0 && $acidPct > $max) {
                continue;
            }

            return $condition;
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────
    // HELPER: Compute Net Average Acid % for the entire batch
    //
    //   Net Avg Acid % = (Σ weight_diff / Σ initial_weight) × 100
    //   where weight_diff per pallet = initial_weight − drained_weight
    //
    // Only pallets with initial_weight > 0 contribute (acid-present rows).
    // ─────────────────────────────────────────────────────────────
    private function computeNetAvgAcidPct(array $details): float
    {
        $totalWeightDiff = 0.0;
        $totalInitial = 0.0;

        foreach ($details as $row) {
            $initial = (float) ($row['initial_weight'] ?? 0);
            $drained = (float) ($row['drained_weight'] ?? 0);

            if ($initial > 0) {
                $totalWeightDiff += max(0.0, $initial - $drained);
                $totalInitial += $initial;
            }
        }

        return $totalInitial > 0
            ? round(($totalWeightDiff / $totalInitial) * 100, 4)
            : 0.0;
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/acid-testings
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $tests = AcidTesting::with(['supplier', 'createdBy', 'updatedBy', 'details'])
            ->when(
                $request->supplier_id,
                fn($q) => $q->where('supplier_id', $request->supplier_id)
            )
            ->when(
                $request->status !== null && $request->status !== '',
                fn($q) => $q->where('status', $request->status)
            )
            ->when(
                $request->date_from,
                fn($q) => $q->whereDate('test_date', '>=', $request->date_from)
            )
            ->when(
                $request->date_to,
                fn($q) => $q->whereDate('test_date', '<=', $request->date_to)
            )
            ->when(
                $request->lot_number,
                fn($q) => $q->where('lot_number', 'like', "%{$request->lot_number}%")
            )
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json(['status' => 'ok', 'data' => $tests]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/acid-testings/stock-conditions
    // ─────────────────────────────────────────────────────────────
    public function stockConditions()
    {
        $conditions = AcidStockCondition::where('is_active', true)
            ->orderBy('min_pct', 'asc')
            ->get(['id', 'stock_code', 'description', 'min_pct', 'max_pct']);

        return response()->json(['status' => 'ok', 'data' => $conditions]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/acid-testings/available-lots
    // ─────────────────────────────────────────────────────────────
    public function availableLots(Request $request)
    {
        $usedLots = AcidTesting::withTrashed()->pluck('lot_number')->toArray();

        $lots = Receiving::with('supplier')
            ->where('status', 1)
            ->whereNotIn('lot_no', $usedLots)
            ->when(
                $request->search,
                fn($q) => $q->where('lot_no', 'like', "%{$request->search}%")
            )
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

    // ─────────────────────────────────────────────────────────────
    // GET /api/acid-testings/lot-check/{lotNo}
    // ─────────────────────────────────────────────────────────────
    public function lotCheck($lotNo)
    {
        $receiving = Receiving::with('supplier')
            ->where('lot_no', $lotNo)
            ->first();

        if (!$receiving) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lot number not found in receiving records.',
            ], 404);
        }

        if ((int) $receiving->status !== 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'This lot has not been submitted/approved in receiving.',
            ], 422);
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

    // ─────────────────────────────────────────────────────────────
    // GET /api/acid-testings/{id}
    // ─────────────────────────────────────────────────────────────
    public function show($id)
    {
        $test = AcidTesting::with([
            'supplier',
            'createdBy',
            'updatedBy',
            'details' => fn($q) => $q->where('is_active', 1),
        ])
            ->where('is_active', 1)
            ->findOrFail($id);

        return response()->json(['status' => 'ok', 'data' => $test]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/acid-testings
    // ─────────────────────────────────────────────────────────────
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
            // stock_code and remarks are server-resolved — not accepted from client
        ]);

        if (AcidTesting::where('lot_number', $request->lot_number)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This lot already has an acid test record.',
            ], 422);
        }

        // Compute net avg acid% across all pallet rows
        $netAvgAcidPct = $this->computeNetAvgAcidPct($request->details);

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
            'net_avg_acid_pct' => $netAvgAcidPct,
            'status' => 0,
            'is_active' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->syncDetails($header, $request->details, $netAvgAcidPct);

        Receiving::where('lot_no', $request->lot_number)
            ->update(['status' => 2, 'updated_by' => auth()->id()]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Acid test saved successfully.',
            'data' => $header->load('details', 'supplier'),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────
    // PUT /api/acid-testings/{id}
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $header = AcidTesting::findOrFail($id);

        if ((int) $header->status >= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot edit — record is already submitted.',
            ], 422);
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
            // stock_code and remarks are server-resolved — not accepted from client
        ]);

        $netAvgAcidPct = $request->has('details')
            ? $this->computeNetAvgAcidPct($request->details)
            : (float) $header->net_avg_acid_pct;

        $header->update(array_merge(
            $request->only([
                'test_date',
                'avg_pallet_weight',
                'foreign_material_weight',
                'avg_pallet_and_foreign_weight',
                'vehicle_number',
            ]),
            [
                'net_avg_acid_pct' => $netAvgAcidPct,
                'updated_by' => auth()->id(),
            ]
        ));

        if ($request->has('details')) {
            $this->syncDetails($header, $request->details, $netAvgAcidPct);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Acid test updated successfully.',
            'data' => $header->fresh(['details', 'supplier']),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // PATCH /api/acid-testings/{id}/status
    // ─────────────────────────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer|in:0,1,2,3,4']);

        $header = AcidTesting::with('details')->findOrFail($id);
        $oldStatus = $header->status;

        $header->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        if ($request->status == 1 && $oldStatus != 1) {
            // Stock OUT: deduct the incoming raw material
            $receiving = Receiving::where('lot_no', $header->lot_number)->first();
            if ($receiving && $receiving->material_id) {
                $this->inventoryService->stockOut(
                    $receiving->material_id,
                    $header->received_qty,
                    'AcidTesting',
                    $header->id,
                    $header->lot_number,
                    auth()->id()
                );
            }

            // Stock IN: add output materials grouped by stock_code
            // All rows share the same batch stock_code, so we aggregate
            // net_weight per stock_code to avoid duplicate stock-in entries.
            $grouped = $header->details
                ->whereNotNull('stock_code')
                ->groupBy('stock_code');

            foreach ($grouped as $stockCode => $rows) {
                $material = Material::where('stock_code', $stockCode)
                    ->orWhere('material_code', $stockCode)
                    ->first();

                if ($material) {
                    $totalNetWeight = $rows->sum('net_weight');
                    $this->inventoryService->stockIn(
                        $material->id,
                        $totalNetWeight,
                        'AcidTesting',
                        $header->id,
                        $header->lot_number,
                        auth()->id()
                    );
                }
            }
        } elseif ($request->status != 1 && $oldStatus == 1) {
            $this->inventoryService->revertTransaction(
                'AcidTesting',
                $header->id,
                auth()->id()
            );
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Status updated.',
            'data' => ['status' => $request->status],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE /api/acid-testings/{id}
    // ─────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $header = AcidTesting::findOrFail($id);

        if ((int) $header->status >= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete a submitted record.',
            ], 422);
        }

        Receiving::where('lot_no', $header->lot_number)
            ->update(['status' => 1, 'updated_by' => auth()->id()]);

        $header->details()->delete();
        $header->delete();

        return response()->json(['status' => 'ok', 'message' => 'Deleted successfully.']);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/acid-testings/{id}/print
    // ─────────────────────────────────────────────────────────────
    public function printView($id)
    {
        $test = AcidTesting::where('is_active', 1)
            ->with([
                'details' => fn($q) => $q->where('is_active', 1),
                'createdBy',
                'supplier',
            ])
            ->findOrFail($id);

        if ((int) $test->status < 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only submitted records can be printed.',
            ], 422);
        }

        $company = Company::first();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'acidTesting' => $test,
                'company' => $company,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE: Sync / replace detail rows
    //
    // ulab_type = '5'  (acid / wet battery):
    //   • Per-pallet acid% = (weight_diff / initial_weight) × 100
    //     stored as avg_acid_pct for display/reference only.
    //   • stock_code / ulab_type / remarks are resolved from the
    //     NET AVERAGE acid% of the WHOLE BATCH (netAvgAcidPct),
    //     NOT from each pallet's own acid%.
    //     Rule: Net Avg Acid% = (Σ weight_diff / Σ initial_weight) × 100
    //     All acid-present pallets in the batch share the same
    //     stock_code because they belong to the same pallet load.
    //
    // ulab_type ∈ {1000024, 1000025, 1000026, 1000028}  (non-acid / dry types):
    //   • No acid% calculation — ulab_type stored as-is
    //   • stock_code column → null (no acid categorisation applies)
    //   • remarks    column → description fetched by that stock_code
    //   • initial_weight / drained_weight are null
    // ─────────────────────────────────────────────────────────────
    private function syncDetails(
        AcidTesting $header,
        array $details,
        float $netAvgAcidPct   // batch-level net avg acid% — used for ALL acid row categorisation
    ): void {
        // Soft-delete existing active rows
        AcidTestPercentageDetail::where('acid_test_id', $header->id)
            ->update(['is_active' => 0]);

        // ── Pre-load non-acid descriptions in one query ───────────
        $nonAcidStockCodes = collect($details)
            ->pluck('ulab_type')
            ->map(fn($v) => (string) $v)
            ->filter(fn($v) => $v !== '5')
            ->unique()
            ->values()
            ->toArray();

        $nonAcidDescriptions = [];
        if (!empty($nonAcidStockCodes)) {
            AcidStockCondition::where('is_active', true)
                ->whereIn('stock_code', $nonAcidStockCodes)
                ->get(['stock_code', 'description'])
                ->each(function ($c) use (&$nonAcidDescriptions) {
                    $nonAcidDescriptions[$c->stock_code] = $c->description;
                });
        }

        // ── Resolve stock condition once for ALL acid rows using batch net avg acid% ──
        // All acid-present pallets share the same stock_code because the
        // categorisation is based on the full batch average, not per-pallet.
        $batchAcidCondition = $netAvgAcidPct > 0
            ? $this->resolveCondition($netAvgAcidPct)
            : null;

        foreach ($details as $row) {
            $frontUlabType = (string) ($row['ulab_type'] ?? '');
            $gross = (float) ($row['gross_weight'] ?? 0);
            $isAcid = ($frontUlabType === '5');

            // initial / drained only exist for acid rows
            $initial = $isAcid ? (float) ($row['initial_weight'] ?? 0) : 0.0;
            $drained = $isAcid ? (float) ($row['drained_weight'] ?? 0) : 0.0;

            $weightDiff = $initial > 0 ? max(0.0, $initial - $drained) : 0.0;

            // Per-pallet acid% = (weight_difference / initial_weight) × 100
            // Stored for display/reference — NOT used for stock_code resolution.
            $perPalletAcidPct = $initial > 0
                ? round(($weightDiff / $initial) * 100, 2)
                : 0.0;

            if ($isAcid) {
                // ── Categorise using the BATCH net avg acid% ─────────
                // All acid rows in this batch get the same stock_code,
                // derived from (Σ weight_diff / Σ initial_weight) × 100.
                $dbUlabType = $batchAcidCondition?->stock_code;  // overwrites '5'
                $dbStockCode = $batchAcidCondition?->stock_code;
                $dbRemarks = $batchAcidCondition?->description;
            } else {
                // ── Non-acid: store as-is, look up description only ──
                $dbUlabType = $frontUlabType;
                $dbStockCode = null;
                $dbRemarks = $nonAcidDescriptions[$frontUlabType] ?? null;
            }

            AcidTestPercentageDetail::create([
                'acid_test_id' => $header->id,
                'pallet_no' => $row['pallet_no'],
                'gross_weight' => $gross,
                'net_weight' => (float) ($row['net_weight'] ?? 0),
                'ulab_type' => $dbUlabType,
                'stock_code' => $dbStockCode,
                'initial_weight' => $initial ?: null,
                'drained_weight' => $drained ?: null,
                'weight_difference' => $weightDiff ?: null,
                'avg_acid_pct' => $perPalletAcidPct ?: null,  // per-pallet, display only
                'remarks' => $dbRemarks,
                'status' => 0,
                'is_active' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
    }
}