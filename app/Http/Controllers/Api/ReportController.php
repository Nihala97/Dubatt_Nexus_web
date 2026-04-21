<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcidTesting;
use App\Models\Receiving;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ═════════════════════════════════════════════════════════════
    // MATERIAL INWARD REPORT
    // ═════════════════════════════════════════════════════════════

    public function materialInward(Request $request)
    {
        $allowedSorts = [
            'receipt_date',
            'lot_no',
            'supplier_name',
            'material_name',
            'received_qty',
            'invoice_qty',
        ];

        $sortBy = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'receipt_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $perPage = min((int) ($request->per_page ?? 50), 500);

        $query = Receiving::with(['supplier', 'material'])
            ->when($request->date_from, fn($q) => $q->whereDate('receipt_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('receipt_date', '<=', $request->date_to))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->material_id, fn($q) => $q->where('material_id', $request->material_id))
            ->when($request->lot_no, fn($q) => $q->where('lot_no', 'like', "%{$request->lot_no}%"));

        $nativeSort = in_array($sortBy, ['receipt_date', 'lot_no', 'received_qty', 'invoice_qty']);
        $query->orderBy($nativeSort ? $sortBy : 'receipt_date', $nativeSort ? $sortDir : 'desc');

        $paginated = $query->paginate($perPage);

        $rows = collect($paginated->items())->map(fn($r) => [
            'id' => $r->id,
            'receipt_date' => $r->receipt_date
                ? Carbon::parse($r->receipt_date)->format('d/m/Y')
                : ($r->created_at?->format('d/m/Y') ?? '—'),
            'receipt_date_raw' => $r->receipt_date ?? $r->created_at?->toDateString(),
            'lot_no' => $r->lot_no ?? '—',
            'supplier_name' => $r->supplier->supplier_name ?? '—',
            'material_name' => $r->material->material_name ?? $r->material->name ?? '—',
            'received_qty' => (float) ($r->received_qty ?? 0),
            'invoice_qty' => (float) ($r->invoice_qty ?? 0),
            'unit' => $r->material->unit ?? $r->material->uom ?? 'KG',
            'category' => $r->material->category ?? $r->material->material_category ?? '—',
            'status' => (int) ($r->status ?? 0),
        ]);

        if (!$nativeSort) {
            $rows = $sortDir === 'asc'
                ? $rows->sortBy($sortBy)->values()
                : $rows->sortByDesc($sortBy)->values();
        }

        return response()->json([
            'status' => 'ok',
            'data' => $rows,
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────────────────────
    public function materialInwardDashboard(Request $request)
    {
        try {
            $now = Carbon::now();
            $thisMonth = $now->month;
            $thisYear = $now->year;

            // ── Helper: resolve effective date for a Receiving row ────
            // receipt_date may be null — fall back to created_at date
            // We use DB raw to handle this at SQL level for month queries

            // ── Current month base ─────────────────────────────────────
            // Use COALESCE so rows with null receipt_date fall back to created_at
            $currentMonthRows = Receiving::with(['supplier', 'material'])
                ->whereRaw(
                    'MONTH(COALESCE(receipt_date, created_at)) = ? AND YEAR(COALESCE(receipt_date, created_at)) = ?',
                    [$thisMonth, $thisYear]
                )
                ->get();

            // ── Filtered base for Last Day (uses filter params) ────────
            $filteredRows = Receiving::with(['supplier', 'material'])
                ->when($request->date_from, fn($q) => $q->whereDate(DB::raw('COALESCE(receipt_date, DATE(created_at))'), '>=', $request->date_from))
                ->when($request->date_to, fn($q) => $q->whereDate(DB::raw('COALESCE(receipt_date, DATE(created_at))'), '<=', $request->date_to))
                ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
                ->when($request->material_id, fn($q) => $q->where('material_id', $request->material_id))
                ->orderByRaw('COALESCE(receipt_date, DATE(created_at)) DESC')
                ->get();

            // ── Known categories ───────────────────────────────────────
            $knownCategories = ['ULAB', 'ULAB PLATES / TERMINALS', 'DROSS', 'CHEMICAL / METALS', 'RML'];

            // Helper to resolve category from a receiving row
            $resolveCategory = function ($r) use ($knownCategories) {
                $cat = $r->material->category
                    ?? $r->material->material_category
                    ?? null;
                if (!$cat || !in_array($cat, $knownCategories)) {
                    $cat = 'Others';
                }
                return $cat;
            };

            // ══════════════════════════════════════════════════════════
            // 1. CATEGORY SCORECARD (current month)
            // ══════════════════════════════════════════════════════════
            $byCategory = $currentMonthRows
                ->groupBy(fn($r) => $resolveCategory($r))
                ->map(fn($group, $cat) => [
                    'category' => $cat,
                    'unit' => $group->first()->material->unit
                        ?? $group->first()->material->uom
                        ?? 'KG',
                    'total_qty' => round($group->sum('received_qty'), 3),
                    'record_count' => $group->count(),
                ])
                ->sortByDesc('total_qty')
                ->values();

            // ══════════════════════════════════════════════════════════
            // 2. MATERIAL SCORECARD (current month)
            // ══════════════════════════════════════════════════════════
            $byMaterial = $currentMonthRows
                ->groupBy('material_id')
                ->map(fn($group) => [
                    'material_name' => $group->first()->material->material_name
                        ?? $group->first()->material->name
                        ?? 'Unknown',
                    'category' => $resolveCategory($group->first()),
                    'unit' => $group->first()->material->unit
                        ?? $group->first()->material->uom
                        ?? 'KG',
                    'total_qty' => round($group->sum('received_qty'), 3),
                    'record_count' => $group->count(),
                ])
                ->sortByDesc('total_qty')
                ->values();

            // ══════════════════════════════════════════════════════════
            // 3. SUPPLIER WISE ACCUMULATION (current month)
            //    Grouped by supplier + material combination
            // ══════════════════════════════════════════════════════════
            $supplierAccumulation = $currentMonthRows
                ->groupBy(fn($r) => ($r->supplier_id ?? 0) . '_' . ($r->material_id ?? 0))
                ->map(fn($group) => [
                    'supplier_name' => $group->first()->supplier->supplier_name
                        ?? $group->first()->supplier->name
                        ?? 'Unknown',
                    'material_name' => $group->first()->material->material_name
                        ?? $group->first()->material->name
                        ?? '—',
                    'unit' => $group->first()->material->unit
                        ?? $group->first()->material->uom
                        ?? 'KG',
                    'total_qty' => round($group->sum('received_qty'), 3),
                ])
                ->sortByDesc('total_qty')
                ->values();

            // ══════════════════════════════════════════════════════════
            // 4. LAST DAY INWARDS (from filtered base)
            // ══════════════════════════════════════════════════════════
            $lastDay = collect();
            $lastDayDate = '';

            if ($filteredRows->isNotEmpty()) {
                // Find the most recent effective date
                $lastDate = $filteredRows->map(
                    fn($r) =>
                    $r->receipt_date
                    ? Carbon::parse($r->receipt_date)->toDateString()
                    : $r->created_at?->toDateString()
                )->filter()->max();

                if ($lastDate) {
                    $lastDayDate = Carbon::parse($lastDate)->format('d M Y');

                    $lastDay = $filteredRows
                        ->filter(fn($r) => (
                            ($r->receipt_date
                                ? Carbon::parse($r->receipt_date)->toDateString()
                                : $r->created_at?->toDateString()
                            ) === $lastDate
                        ))
                        ->sortByDesc('received_qty')
                        ->values()
                        ->map(fn($r) => [
                            'supplier_name' => $r->supplier->supplier_name ?? '—',
                            'material_name' => $r->material->material_name
                                ?? $r->material->name
                                ?? '—',
                            'received_qty' => (float) ($r->received_qty ?? 0),
                            'unit' => $r->material->unit
                                ?? $r->material->uom
                                ?? 'KG',
                            'section' => $resolveCategory($r),
                        ]);
                }
            }

            // ── Period label ───────────────────────────────────────────
            $from = $request->date_from
                ? Carbon::parse($request->date_from)->format('d M Y') : null;
            $to = $request->date_to
                ? Carbon::parse($request->date_to)->format('d M Y') : null;

            $periodLabel = match (true) {
                (bool) $from && (bool) $to => "{$from} → {$to}",
                (bool) $from => "From {$from}",
                (bool) $to => "Up to {$to}",
                default => 'All time (filtered)',
            };

            $monthLabel = $now->format('F Y');

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'by_category' => $byCategory,
                    'by_material' => $byMaterial,
                    'supplier_accumulation' => $supplierAccumulation,
                    'last_day' => $lastDay,
                    'last_day_date' => $lastDayDate,
                    'period_label' => $periodLabel,
                    'month_label' => $monthLabel,
                    // debug info (remove in production)
                    '_debug' => [
                        'current_month_row_count' => $currentMonthRows->count(),
                        'filtered_row_count' => $filteredRows->count(),
                        'month' => $thisMonth,
                        'year' => $thisYear,
                    ],
                ],
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard error', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => [
                    'by_category' => [],
                    'by_material' => [],
                    'supplier_accumulation' => [],
                    'last_day' => [],
                    'last_day_date' => '',
                    'period_label' => 'Error: ' . $e->getMessage(),
                    'month_label' => now()->format('F Y'),
                    '_debug' => ['error' => $e->getMessage()],
                ],
            ], 200);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/reports/material-inward/filters
    // ─────────────────────────────────────────────────────────────
    public function materialInwardFilters()
    {
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('supplier_name')
            ->get(['id', 'supplier_name']);

        $materials = Material::where('is_active', true)
            ->orderBy('material_name')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->material_name ?? $m->name ?? '—',
                'unit' => $m->unit ?? $m->uom ?? 'KG',
                'category' => $m->category ?? $m->material_category ?? '—',
            ]);

        return response()->json([
            'status' => 'ok',
            'data' => compact('suppliers', 'materials'),
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    // ACID TEST STATUS REPORT
    // ═════════════════════════════════════════════════════════════

    public function acidTestStatus(Request $request)
    {
        $allowedSorts = ['receipt_date', 'supplier_name', 'material_name'];
        $sortBy = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'receipt_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $perPage = min((int) ($request->per_page ?? 50), 500);

        $query = Receiving::with(['supplier', 'material'])
            ->where('status', 1)
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('receipt_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('receipt_date', '<=', $request->date_to))
            ->when($request->filled('supplier_id'), fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->filled('material_id'), fn($q) => $q->where('material_id', $request->material_id))
            ->when($request->filled('lot_no'), fn($q) => $q->where('lot_no', 'like', "%{$request->lot_no}%"));

        $nativeSort = $sortBy === 'receipt_date';
        $query->orderBy('receipt_date', $nativeSort ? $sortDir : 'desc');

        $paginated = $query->paginate($perPage);

        $lotNos = collect($paginated->items())->pluck('lot_no')->filter()->unique()->values()->toArray();
        $acidTests = AcidTesting::whereIn('lot_number', $lotNos)
            ->get(['id', 'lot_number', 'status'])
            ->keyBy('lot_number');

        $rows = collect($paginated->items())->map(function ($r) use ($acidTests) {
            $at = $acidTests->get($r->lot_no);

            if (!$at) {
                $testStatusKey = 0;
                $testStatusLabel = 'Test Not Done';
            } elseif ((int) $at->status < 1) {
                $testStatusKey = 1;
                $testStatusLabel = 'In Progress';
            } else {
                $testStatusKey = 2;
                $testStatusLabel = 'Testing Done';
            }

            return [
                'id' => $r->id,
                'lot_no' => $r->lot_no ?? '—',
                'receipt_date' => $r->receipt_date
                    ? Carbon::parse($r->receipt_date)->format('d/m/Y')
                    : ($r->created_at?->format('d/m/Y') ?? '—'),
                'receipt_date_raw' => $r->receipt_date ?? $r->created_at?->toDateString(),
                'supplier_name' => $r->supplier->supplier_name ?? '—',
                'material_name' => $r->material->material_name ?? $r->material->name ?? '—',
                'category' => $r->material->category ?? $r->material->material_category ?? '—',
                'unit' => $r->material->unit ?? $r->material->uom ?? 'KG',
                'received_qty' => (float) ($r->received_qty ?? 0),
                'test_status_key' => $testStatusKey,
                'test_status' => $testStatusLabel,
                'acid_test_id' => $at?->id,
            ];
        });

        if ($request->filled('test_status')) {
            $rows = $rows->filter(fn($r) => $r['test_status_key'] == (int) $request->test_status)->values();
        }
        if ($request->filled('category')) {
            $rows = $rows->filter(fn($r) => $r['category'] === $request->category)->values();
        }

        if (!$nativeSort) {
            $rows = $sortDir === 'asc'
                ? $rows->sortBy($sortBy)->values()
                : $rows->sortByDesc($sortBy)->values();
        }

        return response()->json([
            'status' => 'ok',
            'data' => $rows->values(),
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    public function acidTestStatusFilters()
    {
        $suppliers = Supplier::where('is_active', true)
            ->whereIn('id', Receiving::where('status', 1)->pluck('supplier_id')->unique())
            ->orderBy('supplier_name')
            ->get(['id', 'supplier_name']);

        $materials = Material::where('is_active', true)
            ->whereIn('id', Receiving::where('status', 1)->pluck('material_id')->unique())
            ->orderBy('material_name')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->material_name ?? $m->name ?? '—',
                'unit' => $m->unit ?? $m->uom ?? 'KG',
                'category' => $m->category ?? $m->material_category ?? '—',
            ]);

        $categories = $materials
            ->pluck('category')
            ->filter(fn($c) => $c && $c !== '—')
            ->unique()->sort()->values();

        return response()->json([
            'status' => 'ok',
            'data' => compact('suppliers', 'materials', 'categories'),
        ]);
    }
}