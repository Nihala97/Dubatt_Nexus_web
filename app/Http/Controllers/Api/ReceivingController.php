<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceivingRequest;
use App\Http\Requests\UpdateReceivingRequest;
use App\Models\Receiving;
use App\Models\Material;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class ReceivingController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * GET /api/receivings
     * List all receivings with filters and pagination
     */
    public function index(Request $request)
    {
        $receivings = Receiving::with(['supplier', 'material', 'createdBy', 'updatedBy'])
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->material_id, fn($q) => $q->where('material_id', $request->material_id))
            ->when($request->status !== null, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('receipt_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('receipt_date', '<=', $request->date_to))
            ->when($request->lot_no, fn($q) => $q->where('lot_no', 'like', "%{$request->lot_no}%"))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('lot_no', 'like', "%{$request->search}%")
                    ->orWhere('vehicle_number', 'like', "%{$request->search}%")
                    ->orWhere('driver_name', 'like', "%{$request->search}%");
            }))
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'status' => 'ok',
            'data' => $receivings,
        ]);
    }

    /**
     * Compute next available numeric lot number for ULAB materials.
     */
    public function generateNextUlabLot(): string
    {
        $driver = DB::connection()->getDriverName();

        $query = Receiving::withTrashed()
            ->join('materials', 'receivings.material_id', '=', 'materials.id')
            ->whereRaw('UPPER(TRIM(materials.category)) = ?', ['ULAB']);

        if ($driver === 'mysql') {
            $highestLotRow = $query
                ->whereRaw("receivings.lot_no REGEXP '^[0-9]+$'")
                ->orderByRaw("CAST(receivings.lot_no AS UNSIGNED) DESC")
                ->select('receivings.lot_no')
                ->first();
        } elseif ($driver === 'sqlite') {
            $highestLotRow = $query
                ->whereRaw("receivings.lot_no GLOB '[0-9]*'")
                ->orderByRaw("CAST(receivings.lot_no AS INTEGER) DESC")
                ->select('receivings.lot_no')
                ->first();
        } else {
            $lots = $query->pluck('receivings.lot_no');
            $numericMax = $lots->filter(fn($l) => preg_match('/^\d+$/', trim($l)))
                ->map(fn($l) => (int)trim($l))
                ->max();
            $candidate = ($numericMax ?: 0) + 1;
            while (Receiving::withTrashed()->where('lot_no', (string) $candidate)->exists()) {
                $candidate++;
            }
            return (string) $candidate;
        }

        $highest = $highestLotRow && preg_match('/^\d+$/', trim($highestLotRow->lot_no))
            ? (int) trim($highestLotRow->lot_no)
            : 0;

        $candidate = $highest > 0 ? $highest + 1 : 1;

        while (Receiving::withTrashed()->where('lot_no', (string) $candidate)->exists()) {
            $candidate++;
        }

        return (string) $candidate;
    }

    /**
     * GET /api/receivings/next-lot
     * Preview the next auto-generated lot number for ULAB materials
     */
    public function getNextLot(Request $request)
    {
        $nextLot = $this->generateNextUlabLot();

        return response()->json([
            'status' => 'ok',
            'next_lot' => $nextLot,
        ]);
    }

    /**
     * POST /api/receivings
     * Create new receiving record
     */
    public function store(StoreReceivingRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['status'] = 0; // 0 = Pending

        $material = Material::findOrFail($data['material_id']);
        $isUlab = strtoupper(trim($material->category ?? '')) === 'ULAB';

        $driver = DB::connection()->getDriverName();
        $lockAcquired = false;

        try {
            if ($isUlab && $driver === 'mysql') {
                DB::select("SELECT GET_LOCK('receiving_ulab_lot_lock', 15)");
                $lockAcquired = true;
            }

            $receiving = DB::transaction(function () use (&$data, $isUlab) {
                if ($isUlab) {
                    $data['lot_no'] = $this->generateNextUlabLot();
                }

                return Receiving::create($data);
            });

            return response()->json([
                'status' => 'ok',
                'message' => 'Receiving record created successfully.',
                'data' => $receiving->load(['supplier', 'material']),
            ], 201);
        } finally {
            if ($lockAcquired && $driver === 'mysql') {
                DB::select("SELECT RELEASE_LOCK('receiving_ulab_lot_lock')");
            }
        }
    }

    /**
     * GET /api/receivings/{id}
     * Get single receiving by ID
     */
    public function show($id)
    {
        $receiving = Receiving::with(['supplier', 'material', 'createdBy', 'updatedBy'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'ok',
            'data' => $receiving,
        ]);
    }

    /**
     * GET /api/receivings/lot/{lotNo}
     * Get receiving by Lot Number (for ERP traceability)
     */
    public function getByLot($lotNo)
    {
        $receiving = Receiving::with(['supplier', 'material'])
            ->where('lot_no', $lotNo)
            ->first();

        if (!$receiving) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lot number not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $receiving,
        ]);
    }

    /**
     * PUT /api/receivings/{id}
     * Update receiving record (only if still pending)
     */
    public function update(UpdateReceivingRequest $request, $id)
    {
        $receiving = Receiving::with('material')->findOrFail($id);

        // Lock record if it has moved to acid testing
        if ($receiving->status >= 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot edit — this lot has already been processed downstream.',
            ], 422);
        }

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // Keep saved lot number on edit; do not allow editing lot_no for ULAB materials
        $isUlab = $receiving->material && strtoupper(trim($receiving->material->category ?? '')) === 'ULAB';
        if ($isUlab) {
            unset($data['lot_no']);
        }

        $receiving->update($data);

        return response()->json([
            'status' => 'ok',
            'message' => 'Receiving record updated successfully.',
            'data' => $receiving->fresh(['supplier', 'material']),
        ]);
    }

    /**
     * PATCH /api/receivings/{id}/status
     * Update status only
     * Status: 0=Pending, 1=Approved, 2=In Progress, 3=Completed, 4=Cancelled
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4',
        ]);

        $receiving = Receiving::findOrFail($id);

        // Prevent cancelling if already in downstream process
        if ($request->status == 4 && $receiving->status >= 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot cancel — lot is already in downstream processing.',
            ], 422);
        }

        $oldStatus = $receiving->status;

        $receiving->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        if ($request->status == 1 && $oldStatus != 1) {
            $this->inventoryService->stockIn(
                $receiving->material_id,
                $receiving->received_qty,
                'Receiving',
                $receiving->id,
                $receiving->lot_no,
                auth()->id()
            );
        } elseif ($request->status != 1 && $oldStatus == 1) {
            $this->inventoryService->revertTransaction('Receiving', $receiving->id, auth()->id());
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Status updated successfully.',
            'data' => ['status' => $receiving->status],
        ]);
    }

    /**
     * DELETE /api/receivings/{id}
     * Soft delete receiving record
     */
    public function destroy($id)
    {
        $receiving = Receiving::findOrFail($id);

        if ($receiving->status >= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete — this lot is already in downstream processing.',
            ], 422);
        }

        $receiving->update(['is_active' => false, 'updated_by' => auth()->id()]);
        $receiving->delete();

        return response()->json([
            'status' => 'ok',
            'message' => 'Receiving record deleted successfully.',
        ]);
    }
    public function getApprovedLots()
    {
        $lots = Receiving::where('status', 1)
            ->where('is_active', 1)
            ->select('id', 'lot_no')
            ->get();

        if ($lots->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No lots found.'
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $lots
        ]);
    }
    
    public function getLots()
    {
        $lots = Receiving::where('is_active', 1)
            ->select('id', 'lot_no')
            ->get();

        if ($lots->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No lots found.'
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $lots
        ]);
    }
}
