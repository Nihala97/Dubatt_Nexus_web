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
    // ─────────────────────────────────────────────────────────────
    private function resolveCondition(float $acidPct): ?AcidStockCondition
    {
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

            if ($acidPct < $min) {
                continue;
            }

            if ($max > 0 && $acidPct > $max) {
                continue;
            }

            return $condition;
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────
    // HELPER: Compute Net Average Acid % for the entire batch
    //   Net Avg Acid % = (Σ weight_diff / Σ initial_weight) × 100
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
        $receiving = Receiving::with('supplier')->where('lot_no', $lotNo)->first();

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
        ]);

        if (AcidTesting::where('lot_number', $request->lot_number)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This lot already has an acid test record.',
            ], 422);
        }

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
    //
    // STOCK LEDGER LOGIC ON SUBMIT (status → 1):
    //
    //   OUT: The InHouse Weigh Bridge qty (received_qty on the header)
    //        is deducted from the incoming raw material that was
    //        received via the Receiving record.
    //        → stockOut(rawMaterial, header->received_qty)
    //
    //   IN:  After acid testing, pallets are split into output
    //        materials by their resolved stock_code (e.g. ULAB 18–24%,
    //        ULAB >24%, dry type codes, etc.).
    //        The Net Weight (net_weight) of each pallet detail row is
    //        the actual output weight for that material.
    //        We aggregate net_weight per stock_code across all active
    //        detail rows, then post one stockIn per distinct stock_code.
    //
    //        NOTE: non-acid rows have stock_code = null — they are
    //        matched by ulab_type instead (stored in the ulab_type col).
    //        We look up the Material by stock_code first, then by
    //        material_code / ulab_type as fallback.
    //
    // ─────────────────────────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer|in:0,1,2,3,4']);

        $header = AcidTesting::with('details')->findOrFail($id);
        $oldStatus = (int) $header->status;
        $newStatus = (int) $request->status;

        $header->update([
            'status' => $newStatus,
            'updated_by' => auth()->id(),
        ]);

        // ── Submitting (draft → submitted) ────────────────────────
        if ($newStatus === 1 && $oldStatus !== 1) {

            // ── STOCK OUT: deduct InHouse Weigh Bridge qty ────────
            // received_qty on the header = InHouse Weigh Bridge (KG)
            // This is the raw-material lot that came in via Receiving.
            $receiving = Receiving::where('lot_no', $header->lot_number)->first();
            if ($receiving && $receiving->material_id) {
                $this->inventoryService->stockOut(
                    (int) $receiving->material_id,
                    (float) $header->received_qty,   // InHouse Weigh Bridge (KG)
                    'AcidTesting',
                    $header->id,
                    $header->lot_number,
                    auth()->id()
                );
            }

            // ── STOCK IN: add output materials by Net Wt per stock_code ──
            //
            // Each detail row's net_weight is the actual usable output
            // weight for that pallet after acid testing / sorting.
            //
            // For acid rows   : stock_code is the resolved condition code
            //                   (e.g. 1000001 for 18–24%)
            // For non-acid rows: stock_code is null; use ulab_type as the
            //                   material identifier (stored in ulab_type col)
            //
            // We group by the effective material key and sum net_weight,
            // then post one stockIn per group.
            //
            $activeDetails = $header->details->where('is_active', 1);

            // Build a map: materialKey → total net_weight
            // materialKey = stock_code if set, else ulab_type
            $grouped = [];
            foreach ($activeDetails as $row) {
                $key = $row->stock_code ?? $row->ulab_type;
                if (!$key)
                    continue;                     // skip rows with no identifier
                $netWt = (float) $row->net_weight;
                if ($netWt <= 0)
                    continue;               // skip zero-weight rows
                $grouped[$key] = ($grouped[$key] ?? 0.0) + $netWt;
            }

            foreach ($grouped as $materialKey => $totalNetWeight) {
                // Look up Material: try stock_code first, then material_code
                $material = Material::where('stock_code', $materialKey)->first()
                    ?? Material::where('material_code', $materialKey)->first();

                if (!$material) {
                    // Log and continue — don't abort the whole submit
                    \Log::warning("AcidTesting #{$header->id}: No material found for key '{$materialKey}'. Stock IN skipped for this group.");
                    continue;
                }

                $this->inventoryService->stockIn(
                    $material->id,
                    $totalNetWeight,          // sum of net_weight for this stock_code
                    'AcidTesting',
                    $header->id,
                    $header->lot_number,
                    auth()->id()
                );
            }

            // ── Un-submitting (submitted → draft/other) ───────────────
        } elseif ($newStatus !== 1 && $oldStatus === 1) {
            $this->inventoryService->revertTransaction(
                'AcidTesting',
                $header->id,
                auth()->id()
            );
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Status updated.',
            'data' => ['status' => $newStatus],
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
    //   stock_code / ulab_type / remarks resolved from batch net avg acid%
    //   (NOT per-pallet acid%). All acid pallets in the same batch share
    //   the same stock_code because categorisation is batch-level.
    //
    // ulab_type ∈ non-acid codes:
    //   stock_code → null
    //   ulab_type  → stored as-is (used as material key at stock-in time)
    //   remarks    → description fetched from AcidStockCondition
    // ─────────────────────────────────────────────────────────────
    private function syncDetails(
        AcidTesting $header,
        array $details,
        float $netAvgAcidPct
    ): void {
        // Soft-delete existing active rows
        AcidTestPercentageDetail::where('acid_test_id', $header->id)
            ->update(['is_active' => 0]);

        // Pre-load non-acid descriptions in one query
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

        // Resolve stock condition once for ALL acid rows using batch net avg acid%
        $batchAcidCondition = $netAvgAcidPct > 0
            ? $this->resolveCondition($netAvgAcidPct)
            : null;

        foreach ($details as $row) {
            $frontUlabType = (string) ($row['ulab_type'] ?? '');
            $gross = (float) ($row['gross_weight'] ?? 0);
            $isAcid = ($frontUlabType === '5');
            $isTraction = ($frontUlabType === '1000025');

            $initial = ($isAcid || $isTraction) ? (float) ($row['initial_weight'] ?? 0) : 0.0;
            $drained = ($isAcid || $isTraction) ? (float) ($row['drained_weight'] ?? 0) : 0.0;
            $weightDiff = $initial > 0 ? max(0.0, $initial - $drained) : 0.0;

            // Per-pallet acid% — display/reference only, NOT used for stock_code resolution
            $perPalletAcidPct = $initial > 0
                ? round(($weightDiff / $initial) * 100, 2)
                : 0.0;

            if ($isAcid) {
                $dbUlabType = $batchAcidCondition?->stock_code;
                $dbStockCode = $batchAcidCondition?->stock_code;
                $dbRemarks = $batchAcidCondition?->description;
            } else {
                $dbUlabType = $frontUlabType;
                $dbStockCode = null;   // non-acid: no acid-condition stock_code
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
                'avg_acid_pct' => $perPalletAcidPct ?: null,
                'remarks' => $dbRemarks,
                'status' => 0,
                'is_active' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
    }
}