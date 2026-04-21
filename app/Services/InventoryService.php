<?php

namespace App\Services;

use App\Models\Material;
use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    // ── Stock IN ────────────────────────────────────────────────────
    // Call ONLY from a submit() method, never from store()/update().
    public function stockIn(
        int $materialId,
        float $qty,
        string $processType,
        int $processId,
        ?string $docNo = null,
        ?int $userId = null
    ): StockLedger {
        return $this->recordTransaction($materialId, $qty, 0, $processType, $processId, $docNo, $userId);
    }

    // ── Stock OUT ───────────────────────────────────────────────────
    // Call ONLY from a submit() method, never from store()/update().
    public function stockOut(
        int $materialId,
        float $qty,
        string $processType,
        int $processId,
        ?string $docNo = null,
        ?int $userId = null
    ): StockLedger {
        return $this->recordTransaction($materialId, 0, $qty, $processType, $processId, $docNo, $userId);
    }

    // ── Revert ALL entries for a process ────────────────────────────
    // Creates OPPOSITE reversal rows — never deletes existing ledger rows.
    public function revertTransaction(string $processType, int $processId, ?int $userId = null): void
    {
        DB::beginTransaction();
        try {
            $ledgers = StockLedger::where('process_type', $processType)
                ->where('process_id', $processId)
                ->where('is_active', true)
                ->get();

            foreach ($ledgers as $ledger) {
                // Flip IN↔OUT to create the reversal
                $this->recordTransaction(
                    $ledger->material_id,
                    (float) $ledger->out_qty,   // was OUT → bring back IN
                    (float) $ledger->in_qty,    // was IN  → take back OUT
                    $processType . '_REVERT',
                    $processId,
                    $ledger->doc_no,
                    $userId
                );
                // Mark original row inactive so it won't be double-reverted
                $ledger->update(['is_active' => false]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ── Revert a SINGLE dross ledger entry by its ID ─────────────────
    // Used by syncDrossStock() to revert one material's dross entry
    // when its qty changes or its row is deleted.
    // Marks the original ledger row is_active=false to prevent
    // double-revert on subsequent saves.
    public function revertDrossEntry(int $ledgerId, ?int $userId = null): void
    {
        DB::beginTransaction();
        try {
            $ledger = StockLedger::where('id', $ledgerId)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$ledger) {
                // Already reverted — nothing to do
                DB::commit();
                return;
            }

            // Post the reversal
            $this->recordTransaction(
                $ledger->material_id,
                (float) $ledger->out_qty,   // flip IN↔OUT
                (float) $ledger->in_qty,
                $ledger->process_type . '_REVERT',
                $ledger->process_id,
                $ledger->doc_no,
                $userId
            );

            // Mark original inactive so it can't be reverted twice
            $ledger->update(['is_active' => false]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ── Get current available qty for a material ────────────────────
    public function getAvailableQty(int $materialId): float
    {
        $material = Material::find($materialId);
        return $material ? (float) $material->available_qty : 0.0;
    }

    // ── Get full ledger history for a material ──────────────────────
    public function getLedgerForMaterial(int $materialId, ?string $processType = null): \Illuminate\Support\Collection
    {
        return StockLedger::where('material_id', $materialId)
            ->where('is_active', true)
            ->when($processType, fn($q) => $q->where('process_type', $processType))
            ->orderByDesc('created_at')
            ->get(['id', 'process_type', 'process_id', 'doc_no', 'in_qty', 'out_qty', 'balance_qty', 'created_at']);
    }

    // ── Core transaction recorder (private) ─────────────────────────
    private function recordTransaction(
        int $materialId,
        float $inQty,
        float $outQty,
        string $processType,
        int $processId,
        ?string $docNo,
        ?int $userId
    ): StockLedger {
        if ($inQty == 0 && $outQty == 0) {
            throw new Exception("Cannot record a zero-quantity transaction.");
        }

        DB::beginTransaction();
        try {
            // Lock the material row to prevent race conditions
            $material = Material::where('id', $materialId)->lockForUpdate()->firstOrFail();
            $currentBalance = (float) $material->available_qty;
            $newBalance = round($currentBalance + $inQty - $outQty, 3);

            // Write ledger row
            $ledger = StockLedger::create([
                'material_id' => $materialId,
                'process_type' => $processType,
                'process_id' => $processId,
                'doc_no' => $docNo,
                'in_qty' => $inQty,
                'out_qty' => $outQty,
                'balance_qty' => $newBalance,
                'is_active' => true,
                'status' => 1,            // 1 = posted
                'created_by' => $userId ?? auth()->id(),
                'updated_by' => $userId ?? auth()->id(),
            ]);

            // Update the material's running balance
            $material->update(['available_qty' => $newBalance]);

            DB::commit();
            return $ledger;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}