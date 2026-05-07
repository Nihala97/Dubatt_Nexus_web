<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RefiningBatch;
use App\Models\RefiningRawMaterial;
use App\Models\RefiningChemical;
use App\Models\RefiningProcessDetail;
use App\Models\RefiningFinishedGoodsBlock;
use App\Models\RefiningFinishedGoodsSummary;
use App\Models\RefiningDrossBlock;
use App\Models\RefiningDrossSummary;
use App\Models\Material;
use App\Models\StockLedger;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefiningBatchController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // ══════════════════════════════════════════════════════════════════
    // INDEX  GET /api/refining-batches
    // ══════════════════════════════════════════════════════════════════
    public function index(Request $request): JsonResponse
    {
        $query = RefiningBatch::with(['material', 'rawMaterials', 'chemicals'])
            ->where('is_active', true);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('date_from'))
            $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('search'))
            $query->where('batch_no', 'like', '%' . $request->search . '%');

        $batches = $query->orderByDesc('created_at')->paginate($request->get('per_page', 20));

        $stats = [
            'total' => RefiningBatch::where('is_active', true)->count(),
            'draft' => RefiningBatch::where('is_active', true)->where('status', 0)->count(),
            'submitted' => RefiningBatch::where('is_active', true)->where('status', '>=', 1)->count(),
            'this_month' => RefiningBatch::where('is_active', true)->whereMonth('date', now()->month)->count(),
        ];

        return response()->json(['status' => 'ok', 'data' => $batches, 'stats' => $stats]);
    }

    // ══════════════════════════════════════════════════════════════════
    // GENERATE BATCH NO  GET /api/refining-batches/generate-batch-no
    // ══════════════════════════════════════════════════════════════════
    public function generateBatchNo(): JsonResponse
    {
        $year = now()->format('Y');
        $prefix = 'RFN-' . $year . '-';
        $last = RefiningBatch::where('batch_no', 'like', $prefix . '%')
            ->orderByDesc('batch_no')->value('batch_no');
        $next = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        return response()->json([
            'status' => 'ok',
            'batch_no' => $prefix . str_pad($next, 4, '0', STR_PAD_LEFT),
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE  POST /api/refining-batches
    // After saving children, immediately sync dross → stock (draft-safe).
    // ══════════════════════════════════════════════════════════════════
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'batch_no' => 'required|string|unique:refining_batches,batch_no',
            'date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            $lpgKg = $this->calcDiff($request->lpg_final, $request->lpg_initial);
            $lpg2Kg = $this->calcDiff($request->lpg2_final, $request->lpg2_initial);

            $batch = RefiningBatch::create([
                'batch_no' => $request->batch_no,
                'pot_no' => $request->pot_no,
                'material_id' => $request->material_id ?? null,
                'date' => $request->date,
                'lpg_initial' => $request->lpg_initial,
                'lpg_final' => $request->lpg_final,
                'lpg_consumption' => $lpgKg,
                'lpg_consumption_ltr' => $lpgKg !== null ? round($lpgKg * 1.98, 3) : null,
                'lpg2_initial' => $request->lpg2_initial,
                'lpg2_final' => $request->lpg2_final,
                'lpg2_consumption' => $lpg2Kg,
                'lpg2_consumption_ltr' => $lpg2Kg !== null ? round($lpg2Kg * 1.98, 3) : null,
                'electricity_initial' => $request->electricity_initial,
                'electricity_final' => $request->electricity_final,
                'electricity_consumption' => $this->calcDiff($request->electricity_final, $request->electricity_initial),
                'oxygen_flow_nm3' => $request->oxygen_flow_nm3,
                'oxygen_flow_kg' => $request->oxygen_flow_kg,
                'oxygen_flow_time' => $request->oxygen_flow_time,
                'oxygen_consumption' => $request->oxygen_consumption,
                'total_process_time' => $request->total_process_time,
                'remarks' => $request->remarks ?? null,
                'status' => 0,
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->saveChildren($batch, $request, $userId);

            DB::commit();

            // ── Post dross → stock immediately (draft-safe, idempotent) ──
            $this->syncDrossStock($batch->fresh(['drossSummary']), $userId);

            return response()->json([
                'status' => 'ok',
                'message' => 'Refining batch created.',
                'data' => $batch->load($this->eagerRelations()),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Refining store failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // SHOW  GET /api/refining/{id}
    // ══════════════════════════════════════════════════════════════════
    public function show($id): JsonResponse
    {
        $batch = RefiningBatch::with($this->eagerRelations())->findOrFail($id);
        return response()->json(['status' => 'ok', 'data' => $batch]);
    }

    // ══════════════════════════════════════════════════════════════════
    // UPDATE  PUT /api/refining-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function update(Request $request, $id): JsonResponse
    {
        $batch = RefiningBatch::findOrFail($id);
        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Batch already submitted.'], 422);
        }

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            $lpgKg = $this->calcDiff(
                $request->lpg_final ?? $batch->lpg_final,
                $request->lpg_initial ?? $batch->lpg_initial
            );
            $lpg2Kg = $this->calcDiff(
                $request->lpg2_final ?? $batch->lpg2_final,
                $request->lpg2_initial ?? $batch->lpg2_initial
            );

            $batch->update([
                'pot_no' => $request->pot_no ?? $batch->pot_no,
                'material_id' => $request->material_id ?? $batch->material_id,
                'date' => $request->date ?? $batch->date,
                'lpg_initial' => $request->lpg_initial ?? $batch->lpg_initial,
                'lpg_final' => $request->lpg_final ?? $batch->lpg_final,
                'lpg_consumption' => $lpgKg,
                'lpg_consumption_ltr' => $lpgKg !== null ? round($lpgKg * 1.98, 3) : null,
                'lpg2_initial' => $request->lpg2_initial ?? $batch->lpg2_initial,
                'lpg2_final' => $request->lpg2_final ?? $batch->lpg2_final,
                'lpg2_consumption' => $lpg2Kg,
                'lpg2_consumption_ltr' => $lpg2Kg !== null ? round($lpg2Kg * 1.98, 3) : null,
                'electricity_initial' => $request->electricity_initial ?? $batch->electricity_initial,
                'electricity_final' => $request->electricity_final ?? $batch->electricity_final,
                'electricity_consumption' => $this->calcDiff(
                    $request->electricity_final ?? $batch->electricity_final,
                    $request->electricity_initial ?? $batch->electricity_initial
                ),
                'oxygen_flow_nm3' => $request->oxygen_flow_nm3 ?? $batch->oxygen_flow_nm3,
                'oxygen_flow_kg' => $request->oxygen_flow_kg ?? $batch->oxygen_flow_kg,
                'oxygen_flow_time' => $request->oxygen_flow_time ?? $batch->oxygen_flow_time,
                'oxygen_consumption' => $request->oxygen_consumption ?? $batch->oxygen_consumption,
                'total_process_time' => $request->total_process_time ?? $batch->total_process_time,
                'remarks' => $request->remarks ?? $batch->remarks,
                'updated_by' => $userId,
            ]);

            $this->saveChildren($batch, $request, $userId, delete: true);

            DB::commit();

            // ── Re-sync dross stock (handles qty changes, deletions) ──
            $this->syncDrossStock($batch->fresh(['drossSummary']), $userId);

            return response()->json([
                'status' => 'ok',
                'message' => 'Batch updated.',
                'data' => $batch->fresh($this->eagerRelations()),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Refining update failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // AUTOSAVE  POST /api/refining-batches/{id}/autosave
    // ══════════════════════════════════════════════════════════════════
    public function autosave(Request $request, $id): JsonResponse
    {
        $batch = RefiningBatch::findOrFail($id);
        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 400);
        }

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            $updates = ['updated_by' => $userId];
            $fields = [
                'pot_no',
                'material_id',
                'date',
                'lpg_initial',
                'lpg_final',
                'lpg2_initial',
                'lpg2_final',
                'electricity_initial',
                'electricity_final',
                'oxygen_flow_nm3',
                'oxygen_flow_kg',
                'oxygen_flow_time',
                'oxygen_consumption',
                'total_process_time',
                'remarks',
            ];
            foreach ($fields as $f) {
                if ($request->filled($f))
                    $updates[$f] = $request->input($f);
            }

            $lpgKg = $this->calcDiff(
                $request->lpg_final ?? $batch->lpg_final,
                $request->lpg_initial ?? $batch->lpg_initial
            );
            $lpg2Kg = $this->calcDiff(
                $request->lpg2_final ?? $batch->lpg2_final,
                $request->lpg2_initial ?? $batch->lpg2_initial
            );

            $updates['lpg_consumption'] = $lpgKg;
            $updates['lpg_consumption_ltr'] = $lpgKg !== null ? round($lpgKg * 1.98, 3) : null;
            $updates['lpg2_consumption'] = $lpg2Kg;
            $updates['lpg2_consumption_ltr'] = $lpg2Kg !== null ? round($lpg2Kg * 1.98, 3) : null;
            $updates['electricity_consumption'] = $this->calcDiff(
                $request->electricity_final ?? $batch->electricity_final,
                $request->electricity_initial ?? $batch->electricity_initial
            );

            $batch->update($updates);
            $this->saveChildren($batch, $request, $userId, delete: true);

            DB::commit();

            // ── Re-sync dross stock ───────────────────────────────────
            $this->syncDrossStock($batch->fresh(['drossSummary']), $userId);

            return response()->json(['status' => 'ok', 'saved_at' => now()->format('H:i:s')]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // SUBMIT  POST /api/refining-batches/{id}/submit
    //
    // Dross is already on stock (posted during draft save).
    // Submit only needs to post: Raw OUT, Chemical OUT, FG IN.
    // All three use ledgerExists() to prevent double-posting.
    // ══════════════════════════════════════════════════════════════════
    public function submit($id): JsonResponse
    {
        $batch = RefiningBatch::with([
            'rawMaterials',
            'chemicals',
            'finishedGoodsSummary',
            'drossSummary',
        ])->findOrFail($id);

        if ($batch->status === 'submitted' || $batch->status == 1) {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 422);
        }

        $batch->update(['status' => 1, 'updated_by' => auth()->id()]);

        $this->processRefiningInventory($batch);

        return response()->json(['status' => 'ok', 'message' => 'Batch submitted and locked.']);
    }

    // ══════════════════════════════════════════════════════════════════
    // DESTROY  DELETE /api/refining-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function destroy($id): JsonResponse
    {
        $batch = RefiningBatch::with(['drossSummary'])->findOrFail($id);
        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete submitted batch.'], 422);
        }

        // Revert all dross stock that was posted during draft saves
        $this->revertDrossStock($batch, auth()->id());

        $batch->update(['is_active' => false, 'updated_by' => auth()->id()]);
        return response()->json(['status' => 'ok', 'message' => 'Batch deleted.']);
    }

    // ══════════════════════════════════════════════════════════════════
    // SMELTING LOTS  GET /api/refining-batches/smelting-lots/{materialId}
    // ══════════════════════════════════════════════════════════════════
    public function getSmeltingLots(Request $request, int $materialId): JsonResponse
    {
        $material = Material::find($materialId);

        if (!$material) {
            return response()->json(['status' => 'ok', 'data' => [], 'message' => 'Material not found.']);
        }

        $availableQty = (float) $material->available_qty;

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
            'data' => [
                [
                    'smelting_batch_id' => $material->id,
                    'batch_no' => 'STOCK-' . $material->id,
                    'material_id' => $material->id,
                    'material_name' => $material->material_name ?? $material->name ?? 'Unknown',
                    'secondary_name' => $material->secondary_name ?? $material->material_name ?? 'Unknown',
                    'material_unit' => $material->unit ?? 'KG',
                    'output_qty' => $availableQty,
                    'already_used_qty' => 0,
                    'available_qty' => $availableQty,
                ],
            ],
        ]);
    }

    public function getAllSmeltingLots(Request $request): JsonResponse
    {
        $materials = Material::where('available_qty', '>', 0)->orderBy('material_name')->get();

        if ($materials->isEmpty()) {
            return response()->json(['status' => 'ok', 'data' => [], 'message' => 'No materials with available stock found.']);
        }

        $smeltingLots = $materials->map(fn($material) => [
            'smelting_batch_id' => $material->id,
            'batch_no' => 'STOCK-' . $material->id,
            'material_id' => $material->id,
            'material_name' => $material->material_name ?? $material->name ?? 'Unknown',
            'secondary_name' => $material->secondary_name ?? $material->material_name ?? 'Unknown',
            'material_unit' => $material->unit ?? 'KG',
            'output_qty' => (float) $material->available_qty,
            'already_used_qty' => 0,
            'available_qty' => (float) $material->available_qty,
        ]);

        return response()->json([
            'status' => 'ok',
            'data' => $smeltingLots,
            'total_count' => $smeltingLots->count(),
            'total_available_qty' => $materials->sum('available_qty'),
            'message' => 'Smelting lots retrieved successfully.',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // PROCESS NAMES  GET /api/refining-batches/process-names
    // ══════════════════════════════════════════════════════════════════
    public function getProcessNames(): JsonResponse
    {
        $defaults = [
            'BURNER Start',
            'Loading & Melting',
            'OD Drossing',
            'OD Drossing Pot Levelling',
            'Temp. Raising',
            'De-Cu',
            'De Ni',
            'De-Sn',
            'De-Sb(By Oxygen)',
            'De-Sb',
            'De Sb Caustic',
            'Caustic Cleaning/palta',
            'De-Ni/S ',
            'Pot Holding',
            'Pot Transfering',
            'Casting Preparation',
            'Casting',
            'De-Se/Te/Zn',
            'Break Down'
        ];

        $fromDb = DB::table('refining_process_details')->distinct()->pluck('refining_process')->toArray();
        $merged = array_values(array_unique(array_merge($defaults, $fromDb)));
        sort($merged);

        return response()->json(['status' => 'ok', 'data' => $merged]);
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE: Inventory processing on SUBMIT
    //
    // Dross is already on stock — do NOT re-post it.
    // Use ledgerExists() to guard against double-posting everything else.
    // ══════════════════════════════════════════════════════════════════
    private function processRefiningInventory($batch): void
    {
        $userId = auth()->id();
        $batchId = $batch->id;
        $docNo = $batch->batch_no;

        // 1. OUT: Raw Materials
        foreach ($batch->rawMaterials as $rm) {
            if (!$rm->raw_material_id)
                continue;
            if ($this->ledgerExists('Refining_RawMat', $batchId, $rm->raw_material_id))
                continue;
            $this->inventoryService->stockOut(
                $rm->raw_material_id,
                (float) $rm->qty,
                'Refining_RawMat',
                $batchId,
                $docNo,
                $userId
            );
        }

        // 2. OUT: Chemicals
        foreach ($batch->chemicals as $chem) {
            if (!$chem->chemical_id)
                continue;
            if ($this->ledgerExists('Refining_Chemical', $batchId, $chem->chemical_id))
                continue;
            $this->inventoryService->stockOut(
                $chem->chemical_id,
                (float) $chem->qty,
                'Refining_Chemical',
                $batchId,
                $docNo,
                $userId
            );
        }

        // 3. IN: Finished Goods
        foreach ($batch->finishedGoodsSummary as $fg) {
            if (!$fg->material_id)
                continue;
            if ($this->ledgerExists('Refining_FG', $batchId, $fg->material_id))
                continue;
            $this->inventoryService->stockIn(
                $fg->material_id,
                (float) $fg->total_qty,
                'Refining_FG',
                $batchId,
                $docNo,
                $userId
            );
        }

        // NOTE: Dross is NOT posted here — it was already posted during draft save
        // via syncDrossStock(), which uses process_type='Refining_Dross'.
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE: Sync dross → stock (idempotent, safe to call on every save)
    //
    // Rules:
    //  • First save:         post Refining_Dross stockIn
    //  • Re-save same qty:   skip (no duplicate entry)
    //  • Re-save diff qty:   revert old entry → post new entry
    //  • Dross row deleted:  revert orphaned entry
    // ══════════════════════════════════════════════════════════════════
    private function syncDrossStock(RefiningBatch $batch, int $userId): void
    {
        try {
            $batchId = $batch->id;
            $docNo = $batch->batch_no;

            // Build map of current dross rows: material_id → total_qty
            $currentDross = collect($batch->drossSummary ?? [])
                ->keyBy('material_id')
                ->map(fn($row) => (float) $row->total_qty);

            // Build map of already-posted dross: material_id → ledger row
            $postedDross = StockLedger::where('process_type', 'Refining_Dross')
                ->where('process_id', $batchId)
                ->where('is_active', true)
                ->get()
                ->keyBy('material_id');

            // 1. Handle new / changed dross rows
            foreach ($currentDross as $materialId => $newQty) {
                if ($newQty <= 0)
                    continue;

                $existing = $postedDross->get($materialId);

                if (!$existing) {
                    // Not yet posted → post it
                    $this->inventoryService->stockIn(
                        (int) $materialId,
                        $newQty,
                        'Refining_Dross',
                        $batchId,
                        $docNo,
                        $userId
                    );
                } elseif (abs((float) $existing->in_qty - $newQty) > 0.0001) {
                    // Qty changed → revert old, post new
                    $this->inventoryService->revertDrossEntry($existing->id, $userId);
                    $this->inventoryService->stockIn(
                        (int) $materialId,
                        $newQty,
                        'Refining_Dross',
                        $batchId,
                        $docNo,
                        $userId
                    );
                }
                // else: same qty → skip
            }

            // 2. Revert orphaned dross entries (rows deleted by user)
            foreach ($postedDross as $materialId => $ledger) {
                if (!$currentDross->has($materialId)) {
                    $this->inventoryService->revertDrossEntry($ledger->id, $userId);
                }
            }

        } catch (\Throwable $e) {
            Log::error('syncDrossStock failed', ['batch_id' => $batch->id, 'error' => $e->getMessage()]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE: Revert ALL dross stock for a batch (used on delete)
    // ══════════════════════════════════════════════════════════════════
    private function revertDrossStock(RefiningBatch $batch, int $userId): void
    {
        try {
            $posted = StockLedger::where('process_type', 'Refining_Dross')
                ->where('process_id', $batch->id)
                ->where('is_active', true)
                ->get();

            foreach ($posted as $ledger) {
                $this->inventoryService->revertDrossEntry($ledger->id, $userId);
            }
        } catch (\Throwable $e) {
            Log::error('revertDrossStock failed', ['batch_id' => $batch->id, 'error' => $e->getMessage()]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE: Check if a ledger entry already exists for this
    // process_type + process_id + material_id combination
    // ══════════════════════════════════════════════════════════════════
    private function ledgerExists(string $processType, int $processId, int $materialId): bool
    {
        return StockLedger::where('process_type', $processType)
            ->where('process_id', $processId)
            ->where('material_id', $materialId)
            ->where('is_active', true)
            ->exists();
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════
    private function eagerRelations(): array
    {
        return [
            'material',
            'rawMaterials',
            'chemicals',
            'processDetails',
            'finishedGoodsBlocks',
            'finishedGoodsSummary',
            'drossBlocks',
            'drossSummary',
        ];
    }

    private function calcDiff($final, $initial): ?float
    {
        if (is_numeric($final) && is_numeric($initial)) {
            $diff = (float) $final - (float) $initial;
            return $diff >= 0 ? round($diff, 3) : null;
        }
        return null;
    }

    private function saveChildren(RefiningBatch $batch, Request $request, int $userId, bool $delete = false): void
    {
        // ── Raw Materials ─────────────────────────────────────────────
        if ($request->has('raw_materials')) {
            if ($delete)
                RefiningRawMaterial::where('refining_batch_id', $batch->id)->delete();
            foreach ($request->raw_materials ?? [] as $row) {
                if (empty($row['raw_material_id']))
                    continue;
                RefiningRawMaterial::create([
                    'refining_batch_id' => $batch->id,
                    'raw_material_id' => $row['raw_material_id'],
                    'qty' => $row['qty'] ?? 0,
                    'smelting_batch_id' => $row['smelting_batch_id'] ?? null,
                    'smelting_batch_no' => $row['smelting_batch_no'] ?? null,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Chemicals ─────────────────────────────────────────────────
        if ($request->has('chemicals')) {
            if ($delete)
                RefiningChemical::where('refining_batch_id', $batch->id)->delete();
            foreach ($request->chemicals ?? [] as $row) {
                if (empty($row['chemical_id']))
                    continue;
                RefiningChemical::create([
                    'refining_batch_id' => $batch->id,
                    'chemical_id' => $row['chemical_id'],
                    'qty' => $row['qty'] ?? 0,
                    'smelting_batch_id' => $row['smelting_batch_id'] ?? null,
                    'smelting_batch_no' => $row['smelting_batch_no'] ?? null,
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
                RefiningProcessDetail::where('refining_batch_id', $batch->id)->delete();
            foreach ($request->process_details ?? [] as $row) {
                if (empty($row['refining_process']))
                    continue;
                $totalTime = 0;
                if (!empty($row['start_time']) && !empty($row['end_time'])) {
                    try {
                        $start = \Carbon\Carbon::parse($row['start_time']);
                        $end = \Carbon\Carbon::parse($row['end_time']);
                        $diff = $end->diffInMinutes($start, false);
                        $totalTime = $diff < 0 ? round($diff + 1440, 2) : round($diff, 2);
                    } catch (\Exception $e) {
                        $totalTime = 0;
                    }
                }
                RefiningProcessDetail::create([
                    'refining_batch_id' => $batch->id,
                    'refining_process' => $row['refining_process'],
                    'start_time' => $row['start_time'] ?? null,
                    'end_time' => $row['end_time'] ?? null,
                    'total_time' => $totalTime,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Finished Goods Blocks ─────────────────────────────────────
        if ($request->has('finished_goods_blocks')) {
            if ($delete)
                RefiningFinishedGoodsBlock::where('refining_batch_id', $batch->id)->delete();
            foreach ($request->finished_goods_blocks ?? [] as $row) {
                if (empty($row['material_id']) || empty($row['block_weight']))
                    continue;
                RefiningFinishedGoodsBlock::create([
                    'refining_batch_id' => $batch->id,
                    'material_id' => $row['material_id'],
                    'block_sl_no' => $row['block_sl_no'] ?? 0,
                    'block_weight' => $row['block_weight'],
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Finished Goods Summary ────────────────────────────────────
        if ($request->has('finished_goods_summary')) {
            if ($delete)
                RefiningFinishedGoodsSummary::where('refining_batch_id', $batch->id)->delete();
            foreach ($request->finished_goods_summary ?? [] as $row) {
                if (empty($row['material_id']))
                    continue;
                RefiningFinishedGoodsSummary::create([
                    'refining_batch_id' => $batch->id,
                    'material_id' => $row['material_id'],
                    'total_qty' => $row['total_qty'] ?? 0,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Dross Blocks ──────────────────────────────────────────────
        if ($request->has('dross_blocks')) {
            if ($delete)
                RefiningDrossBlock::where('refining_batch_id', $batch->id)->delete();
            foreach ($request->dross_blocks ?? [] as $row) {
                if (empty($row['material_id']) || empty($row['block_weight']))
                    continue;
                RefiningDrossBlock::create([
                    'refining_batch_id' => $batch->id,
                    'material_id' => $row['material_id'],
                    'block_sl_no' => $row['block_sl_no'] ?? 0,
                    'block_weight' => $row['block_weight'],
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Dross Summary ─────────────────────────────────────────────
        if ($request->has('dross_summary')) {
            if ($delete)
                RefiningDrossSummary::where('refining_batch_id', $batch->id)->delete();
            foreach ($request->dross_summary ?? [] as $row) {
                if (empty($row['material_id']))
                    continue;
                RefiningDrossSummary::create([
                    'refining_batch_id' => $batch->id,
                    'material_id' => $row['material_id'],
                    'total_qty' => $row['total_qty'] ?? 0,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }
    }
}