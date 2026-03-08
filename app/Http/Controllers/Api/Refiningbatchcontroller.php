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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefiningBatchController extends Controller
{
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
            'draft' => RefiningBatch::where('is_active', true)->where('status', 'draft')->count(),
            'submitted' => RefiningBatch::where('is_active', true)->where('status', 'submitted')->count(),
            'this_month' => RefiningBatch::where('is_active', true)->whereMonth('date', now()->month)->count(),
        ];

        return response()->json(['status' => 'ok', 'data' => $batches, 'stats' => $stats]);
    }

    // ══════════════════════════════════════════════════════════════════
    // GENERATE BATCH NO  GET /api/refining-batches/generate-batch-no
    // Format: RFN-2026-0001
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

            $batch = RefiningBatch::create([
                'batch_no' => $request->batch_no,
                'pot_no' => $request->pot_no,
                'material_id' => $request->material_id ?? null,
                'date' => $request->date,
                'lpg_initial' => $request->lpg_initial,
                'lpg_final' => $request->lpg_final,
                'lpg_consumption' => $this->calcDiff($request->lpg_final, $request->lpg_initial),
                'electricity_initial' => $request->electricity_initial,
                'electricity_final' => $request->electricity_final,
                'electricity_consumption' => $this->calcDiff($request->electricity_final, $request->electricity_initial),
                'oxygen_flow_nm3' => $request->oxygen_flow_nm3,
                'oxygen_flow_kg' => $request->oxygen_flow_kg,
                'oxygen_flow_time' => $request->oxygen_flow_time,
                'oxygen_consumption' => $request->oxygen_consumption,
                'total_process_time' => $request->total_process_time,
                'status' => 'draft',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->saveChildren($batch, $request, $userId);
            DB::commit();

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
    // SHOW  GET /api/refining-batches/{id}
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

            $batch->update([
                'pot_no' => $request->pot_no ?? $batch->pot_no,
                'material_id' => $request->material_id ?? $batch->material_id,
                'date' => $request->date ?? $batch->date,
                'lpg_initial' => $request->lpg_initial ?? $batch->lpg_initial,
                'lpg_final' => $request->lpg_final ?? $batch->lpg_final,
                'lpg_consumption' => $this->calcDiff(
                    $request->lpg_final ?? $batch->lpg_final,
                    $request->lpg_initial ?? $batch->lpg_initial
                ),
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
                'updated_by' => $userId,
            ]);

            $this->saveChildren($batch, $request, $userId, delete: true);
            DB::commit();

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
                'electricity_initial',
                'electricity_final',
                'oxygen_flow_nm3',
                'oxygen_flow_kg',
                'oxygen_flow_time',
                'oxygen_consumption',
                'total_process_time',
            ];
            foreach ($fields as $f) {
                if ($request->filled($f))
                    $updates[$f] = $request->input($f);
            }

            $updates['lpg_consumption'] = $this->calcDiff(
                $request->lpg_final ?? $batch->lpg_final,
                $request->lpg_initial ?? $batch->lpg_initial
            );
            $updates['electricity_consumption'] = $this->calcDiff(
                $request->electricity_final ?? $batch->electricity_final,
                $request->electricity_initial ?? $batch->electricity_initial
            );

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
    // SUBMIT  POST /api/refining-batches/{id}/submit
    // ══════════════════════════════════════════════════════════════════
    public function submit($id): JsonResponse
    {
        $batch = RefiningBatch::findOrFail($id);
        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 422);
        }
        $batch->update(['status' => 'submitted', 'updated_by' => auth()->id()]);
        return response()->json(['status' => 'ok', 'message' => 'Batch submitted and locked.']);
    }

    // ══════════════════════════════════════════════════════════════════
    // DESTROY  DELETE /api/refining-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function destroy($id): JsonResponse
    {
        $batch = RefiningBatch::findOrFail($id);
        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete submitted batch.'], 422);
        }
        $batch->update(['is_active' => false, 'updated_by' => auth()->id()]);
        return response()->json(['status' => 'ok', 'message' => 'Batch deleted.']);
    }

    // ══════════════════════════════════════════════════════════════════
    // SMELTING LOTS FOR MATERIAL  GET /api/refining-batches/smelting-lots/{materialId}
    //
    // Returns submitted smelting batches whose output_material matches.
    // Calculates available_qty = output_qty - already assigned to refining.
    // Optional ?exclude_refining_id=X to exclude current draft.
    // ══════════════════════════════════════════════════════════════════
    public function getSmeltingLots(Request $request, int $materialId): JsonResponse
    {
        $excludeId = $request->get('exclude_refining_id');

        $smeltingBatches = DB::table('smelting_batches as sb')
            ->join('materials as mat', 'mat.id', '=', 'sb.output_material')
            ->where('sb.output_material', $materialId)
            ->where('sb.status', 'submitted')
            ->where('sb.is_active', true)
            ->select(
                'sb.id',
                'sb.batch_no',
                'sb.output_material',
                'sb.output_qty',
                'mat.name as material_name',
                'mat.unit as material_unit'
            )
            ->orderByDesc('sb.date')
            ->get();

        if ($smeltingBatches->isEmpty()) {
            return response()->json(['status' => 'ok', 'data' => [], 'message' => 'No smelting batches found.']);
        }

        $smeltingIds = $smeltingBatches->pluck('id')->toArray();

        // How much already consumed across all refining batches (raw_materials)
        $usedRaw = DB::table('refining_raw_materials as rrm')
            ->join('refining_batches as rb', 'rb.id', '=', 'rrm.refining_batch_id')
            ->whereIn('rrm.smelting_batch_id', $smeltingIds)
            ->where('rb.is_active', true)
            ->where('rrm.is_active', true)
            ->when($excludeId, fn($q) => $q->where('rrm.refining_batch_id', '!=', $excludeId))
            ->groupBy('rrm.smelting_batch_id')
            ->select('rrm.smelting_batch_id', DB::raw('SUM(rrm.qty) as used_qty'))
            ->pluck('used_qty', 'smelting_batch_id');

        // How much consumed across all refining batches (chemicals)
        $usedChem = DB::table('refining_chemicals as rc')
            ->join('refining_batches as rb', 'rb.id', '=', 'rc.refining_batch_id')
            ->whereIn('rc.smelting_batch_id', $smeltingIds)
            ->where('rb.is_active', true)
            ->where('rc.is_active', true)
            ->when($excludeId, fn($q) => $q->where('rc.refining_batch_id', '!=', $excludeId))
            ->groupBy('rc.smelting_batch_id')
            ->select('rc.smelting_batch_id', DB::raw('SUM(rc.qty) as used_qty'))
            ->pluck('used_qty', 'smelting_batch_id');

        $result = $smeltingBatches->map(function ($b) use ($usedRaw, $usedChem) {
            $outputQty = (float) $b->output_qty;
            $usedQty = (float) ($usedRaw[$b->id] ?? 0) + (float) ($usedChem[$b->id] ?? 0);
            $availableQty = max(0, $outputQty - $usedQty);
            return [
                'smelting_batch_id' => $b->id,
                'batch_no' => $b->batch_no,
                'material_id' => $b->output_material,
                'material_name' => $b->material_name,
                'material_unit' => $b->material_unit ?? 'KG',
                'output_qty' => round($outputQty, 3),
                'already_used_qty' => round($usedQty, 3),
                'available_qty' => round($availableQty, 3),
            ];
        })->values();

        return response()->json(['status' => 'ok', 'data' => $result]);
    }

    // ══════════════════════════════════════════════════════════════════
    // PROCESS NAMES  GET /api/refining-batches/process-names
    // Returns distinct process names from refining_process_details
    // (used for the process dropdown in the form)
    // ══════════════════════════════════════════════════════════════════
    public function getProcessNames(): JsonResponse
    {
        // Seed defaults + any user-added ones from DB
        $defaults = [
            'MELTING',
            'DROSSING',
            'CUPELLATION',
            'REFINING-1',
            'REFINING-2',
            'REFINING-3',
            'COOLING',
            'CASTING',
            'TAPPING',
        ];

        $fromDb = DB::table('refining_process_details')
            ->distinct()
            ->pluck('refining_process')
            ->toArray();

        $merged = array_values(array_unique(array_merge($defaults, $fromDb)));
        sort($merged);

        return response()->json(['status' => 'ok', 'data' => $merged]);
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
                    'status' => 'active',
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
                    'status' => 'active',
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
                    'status' => 'active',
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
                    'status' => 'active',
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
                    'status' => 'active',
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
                    'status' => 'active',
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
                    'status' => 'active',
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }
    }
}