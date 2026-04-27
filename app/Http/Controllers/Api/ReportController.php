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

    public function bbsuFilters()
    {
        $categories = DB::table('bbsu_batches')
            ->where('is_active', 1)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json([
            'status' => 'ok',
            'data' => ['categories' => $categories],
        ]);
    }
    public function bbsuDashboard(Request $request)
    {
        try {
            $now = Carbon::now();
            $thisYear = $now->year;
            $thisMonth = $now->month;
            $lastMonthDate = $now->copy()->subMonth();
            $lastMonth = $lastMonthDate->month;
            $lastMonthYear = $lastMonthDate->year;

            // ── Helper: base input query ───────────────────────────────
            $inputBase = fn() => DB::table('bbsu_input_details as bi')
                ->join('bbsu_batches as bb', 'bb.id', '=', 'bi.bbsu_batch_id')
                ->where('bb.is_active', 1)
                ->where('bi.is_active', 1);

            // ── 1. PRODUCTION SCORECARDS ───────────────────────────────

            // Last month total
            $lastMonthTotal = $inputBase()
                ->whereMonth('bb.doc_date', $lastMonth)
                ->whereYear('bb.doc_date', $lastMonthYear)
                ->sum('bi.quantity');

            // Last month by category
            $lastMonthByCategory = $inputBase()
                ->whereMonth('bb.doc_date', $lastMonth)
                ->whereYear('bb.doc_date', $lastMonthYear)
                ->select('bb.category', DB::raw('SUM(bi.quantity) as total_qty'), DB::raw('COUNT(DISTINCT bb.id) as batch_count'))
                ->groupBy('bb.category')
                ->orderByDesc('total_qty')
                ->get();

            // Current month total
            $currentMonthTotal = $inputBase()
                ->whereMonth('bb.doc_date', $thisMonth)
                ->whereYear('bb.doc_date', $thisYear)
                ->sum('bi.quantity');

            // Current month by category
            $currentMonthByCategory = $inputBase()
                ->whereMonth('bb.doc_date', $thisMonth)
                ->whereYear('bb.doc_date', $thisYear)
                ->select('bb.category', DB::raw('SUM(bi.quantity) as total_qty'), DB::raw('COUNT(DISTINCT bb.id) as batch_count'))
                ->groupBy('bb.category')
                ->orderByDesc('total_qty')
                ->get();

            // This year total
            $yearTotal = $inputBase()
                ->whereYear('bb.doc_date', $thisYear)
                ->sum('bi.quantity');

            // ── 2. AVG HR / MT & AVG ACID (current month) ─────────────

            $powerRow = DB::table('bbsu_power_consumption as bp')
                ->join('bbsu_batches as bb', 'bb.id', '=', 'bp.bbsu_batch_id')
                ->whereMonth('bb.doc_date', $thisMonth)
                ->whereYear('bb.doc_date', $thisYear)
                ->where('bb.is_active', 1)
                ->where('bp.is_active', 1)
                ->selectRaw('SUM(bp.total_power_consumption) as total_power')
                ->first();

            $avgHrPerMT = ($currentMonthTotal > 0 && $powerRow?->total_power)
                ? round($powerRow->total_power / $currentMonthTotal, 4)
                : 0;

            $avgAcidPct = round(
                (float) $inputBase()
                    ->whereMonth('bb.doc_date', $thisMonth)
                    ->whereYear('bb.doc_date', $thisYear)
                    ->avg('bi.acid_percentage'),
                2
            );

            // ── 3. OUTPUT MATERIAL TOTALS (current month) ─────────────
            //    Shows totals per material_code (Paste, Metallic, Fines, PP/ABS Chips…)

            $outputMaterials = DB::table('bbsu_output_materials as bo')
                ->join('bbsu_batches as bb', 'bb.id', '=', 'bo.bbsu_batch_id')
                ->whereMonth('bb.doc_date', $thisMonth)
                ->whereYear('bb.doc_date', $thisYear)
                ->where('bb.is_active', 1)
                ->where('bo.is_active', 1)
                ->select('bo.material_code', DB::raw('SUM(bo.qty) as total_qty'))
                ->groupBy('bo.material_code')
                ->orderBy('bo.material_code')
                ->get();

            // ── 4. LAST DAY BATCHES (most recent doc_date in batches) ─
            $lastDocDate = DB::table('bbsu_batches')
                ->where('is_active', 1)
                ->max('doc_date');

            $lastDayBatches = collect();
            $lastDayDate = '';

            if ($lastDocDate) {
                $lastDayDate = Carbon::parse($lastDocDate)->format('d M Y');
                $lastDayBatches = DB::table('bbsu_batches as bb')
                    ->leftJoin('bbsu_input_details as bi', function ($j) {
                        $j->on('bi.bbsu_batch_id', '=', 'bb.id')->where('bi.is_active', 1);
                    })
                    ->leftJoin('bbsu_power_consumption as bp', function ($j) {
                        $j->on('bp.bbsu_batch_id', '=', 'bb.id')->where('bp.is_active', 1);
                    })
                    ->where('bb.doc_date', $lastDocDate)
                    ->where('bb.is_active', 1)
                    ->select(
                        'bb.batch_no',
                        'bb.category',
                        'bb.start_time',
                        'bb.end_time',
                        DB::raw('SUM(bi.quantity)                  as total_input'),
                        DB::raw('AVG(bi.acid_percentage)           as avg_acid'),
                        DB::raw('MAX(bp.total_power_consumption)   as total_hrs')
                    )
                    ->groupBy('bb.id', 'bb.batch_no', 'bb.category', 'bb.start_time', 'bb.end_time')
                    ->orderBy('bb.start_time')
                    ->get();
            }

            // ── 5. Labels ─────────────────────────────────────────────
            $monthLabel = $now->format('F Y');
            $lastMonthLabel = $lastMonthDate->format('F Y');

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'last_month_total' => round($lastMonthTotal, 2),
                    'last_month_label' => $lastMonthLabel,
                    'last_month_by_category' => $lastMonthByCategory,
                    'current_month_total' => round($currentMonthTotal, 2),
                    'current_month_by_category' => $currentMonthByCategory,
                    'year_total' => round($yearTotal, 2),
                    'avg_hr_per_mt' => $avgHrPerMT,
                    'avg_acid_pct' => $avgAcidPct,
                    'output_materials' => $outputMaterials,
                    'last_day_batches' => $lastDayBatches,
                    'last_day_date' => $lastDayDate,
                    'month_label' => $monthLabel,
                    '_debug' => [
                        'current_month_input_mt' => $currentMonthTotal,
                        'total_power_hrs' => $powerRow?->total_power ?? 0,
                        'month' => $thisMonth,
                        'year' => $thisYear,
                    ],
                ],
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('BBSU Dashboard error', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => [
                    'last_month_total' => 0,
                    'last_month_by_category' => [],
                    'current_month_total' => 0,
                    'current_month_by_category' => [],
                    'year_total' => 0,
                    'avg_hr_per_mt' => 0,
                    'avg_acid_pct' => 0,
                    'output_materials' => [],
                    'last_day_batches' => [],
                    'last_day_date' => '',
                    'month_label' => now()->format('F Y'),
                    '_debug' => ['error' => $e->getMessage()],
                ],
            ], 200);
        }
    }
    public function bbsuChart(Request $request)
    {
        $mode = $request->input('mode', 'weekly');  // 'weekly' | 'daily'
        $months = min((int) $request->input('months', 1), 3);
        $now = Carbon::now();
        $datasets = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $target = $now->copy()->subMonths($i);
            $monthStart = $target->copy()->startOfMonth();
            $monthEnd = $target->copy()->endOfMonth();
            $label = $target->format('M Y');

            if ($mode === 'weekly') {
                $buckets = [];
                $cur = $monthStart->copy();
                $wk = 1;
                while ($cur->lte($monthEnd)) {
                    $wEnd = $cur->copy()->endOfWeek()->min($monthEnd);
                    $qty = DB::table('bbsu_input_details as bi')
                        ->join('bbsu_batches as bb', 'bb.id', '=', 'bi.bbsu_batch_id')
                        ->whereBetween('bb.doc_date', [$cur->toDateString(), $wEnd->toDateString()])
                        ->where('bb.is_active', 1)->where('bi.is_active', 1)
                        ->sum('bi.quantity');
                    $buckets["Week $wk"] = round($qty, 2);
                    $cur = $wEnd->copy()->addDay();
                    $wk++;
                }
                $datasets[] = ['label' => $label, 'data' => $buckets];
            } else {
                // Daily
                $buckets = [];
                $cur = $monthStart->copy();
                while ($cur->lte($monthEnd)) {
                    $qty = DB::table('bbsu_input_details as bi')
                        ->join('bbsu_batches as bb', 'bb.id', '=', 'bi.bbsu_batch_id')
                        ->where('bb.doc_date', $cur->toDateString())
                        ->where('bb.is_active', 1)->where('bi.is_active', 1)
                        ->sum('bi.quantity');
                    $buckets[$cur->format('d')] = round($qty, 2);
                    $cur->addDay();
                }
                $datasets[] = ['label' => $label, 'data' => $buckets];
            }
        }

        // Avg hours per day (current month only, for the bar chart)
        $avgHoursPerDay = DB::table('bbsu_batches as bb')
            ->join('bbsu_power_consumption as bp', 'bp.bbsu_batch_id', '=', 'bb.id')
            ->whereMonth('bb.doc_date', $now->month)
            ->whereYear('bb.doc_date', $now->year)
            ->where('bb.is_active', 1)->where('bp.is_active', 1)
            ->selectRaw('DAY(bb.doc_date) as day, AVG(bp.total_power_consumption) as avg_hrs')
            ->groupBy(DB::raw('DAY(bb.doc_date)'))
            ->orderBy('day')
            ->get();

        return response()->json([
            'status' => 'ok',
            'mode' => $mode,
            'datasets' => $datasets,
            'avg_hours_per_day' => $avgHoursPerDay,
        ]);
    }
    public function bbsuReport(Request $request)
    {
        $allowedSorts = ['doc_date', 'batch_no', 'category', 'start_time', 'end_time', 'total_input_qty', 'avg_acid_pct', 'total_power_hrs'];
        $sortBy = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'doc_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $perPage = min((int) ($request->per_page ?? 50), 500);

        $query = DB::table('bbsu_batches as bb')
            ->leftJoin('bbsu_input_details as bi', function ($j) {
                $j->on('bi.bbsu_batch_id', '=', 'bb.id')->where('bi.is_active', 1);
            })
            ->leftJoin('bbsu_power_consumption as bp', function ($j) {
                $j->on('bp.bbsu_batch_id', '=', 'bb.id')->where('bp.is_active', 1);
            })
            ->where('bb.is_active', 1)
            ->select(
                'bb.id',
                'bb.batch_no',
                'bb.doc_date',
                'bb.category',
                'bb.start_time',
                'bb.end_time',
                'bb.status',
                DB::raw('SUM(bi.quantity)                  as total_input_qty'),
                DB::raw('AVG(bi.acid_percentage)           as avg_acid_pct'),
                DB::raw('MAX(bp.initial_power)             as initial_power'),
                DB::raw('MAX(bp.final_power)               as final_power'),
                DB::raw('MAX(bp.total_power_consumption)   as total_power_hrs')
            )
            ->groupBy('bb.id', 'bb.batch_no', 'bb.doc_date', 'bb.category', 'bb.start_time', 'bb.end_time', 'bb.status');

        // Filters
        if ($request->filled('date_from'))
            $query->whereDate('bb.doc_date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $query->whereDate('bb.doc_date', '<=', $request->date_to);
        if ($request->filled('category'))
            $query->where('bb.category', $request->category);
        if ($request->filled('batch_no'))
            $query->where('bb.batch_no', 'like', '%' . $request->batch_no . '%');
        if ($request->filled('status'))
            $query->where('bb.status', $request->status);

        // Sorting — only native SQL columns can be ordered directly
        $nativeSorts = ['doc_date', 'batch_no', 'category', 'start_time', 'end_time'];
        if (in_array($sortBy, $nativeSorts)) {
            $query->orderBy('bb.' . $sortBy, $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);  // aggregate alias — works in MySQL
        }

        $paginated = $query->paginate($perPage);

        // Attach output materials per batch row
        $batchIds = collect($paginated->items())->pluck('id')->toArray();
        $outputMats = DB::table('bbsu_output_materials')
            ->whereIn('bbsu_batch_id', $batchIds)
            ->where('is_active', 1)
            ->get()
            ->groupBy('bbsu_batch_id');

        $statusMap = [0 => 'Pending', 1 => 'In Progress', 2 => 'Completed', 3 => 'Cancelled'];

        $rows = collect($paginated->items())->map(function ($r) use ($outputMats, $statusMap) {
            return [
                'id' => $r->id,
                'batch_no' => $r->batch_no,
                'doc_date' => $r->doc_date
                    ? Carbon::parse($r->doc_date)->format('d/m/Y') : '—',
                'doc_date_raw' => $r->doc_date,
                'category' => $r->category ?? '—',
                'start_time' => $r->start_time
                    ? Carbon::parse($r->start_time)->format('H:i') : '—',
                'end_time' => $r->end_time
                    ? Carbon::parse($r->end_time)->format('H:i') : '—',
                'status' => (int) ($r->status ?? 0),
                'status_label' => $statusMap[$r->status] ?? '—',
                'total_input_qty' => round((float) ($r->total_input_qty ?? 0), 3),
                'avg_acid_pct' => round((float) ($r->avg_acid_pct ?? 0), 2),
                'initial_power' => round((float) ($r->initial_power ?? 0), 3),
                'final_power' => round((float) ($r->final_power ?? 0), 3),
                'total_power_hrs' => round((float) ($r->total_power_hrs ?? 0), 3),
                'output_materials' => ($outputMats[$r->id] ?? collect())->map(fn($m) => [
                    'material_code' => $m->material_code,
                    'qty' => round((float) $m->qty, 3),
                    'yield_pct' => round((float) $m->yield_pct, 2),
                ])->values(),
            ];
        });

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
    public function bbsuDrilldown(Request $request)
    {
        $date = $request->input('date');
        if (!$date) {
            return response()->json(['status' => 'error', 'message' => 'Date required'], 422);
        }

        $batches = DB::table('bbsu_batches as bb')
            ->leftJoin('bbsu_input_details as bi', 'bi.bbsu_batch_id', '=', 'bb.id')
            ->leftJoin('bbsu_power_consumption as bp', 'bp.bbsu_batch_id', '=', 'bb.id')
            ->where('bb.doc_date', $date)
            ->where('bb.is_active', 1)
            ->select(
                'bb.id',
                'bb.batch_no',
                'bb.category',
                'bb.start_time',
                'bb.end_time',
                DB::raw('SUM(bi.quantity)                as total_input'),
                DB::raw('AVG(bi.acid_percentage)         as avg_acid'),
                DB::raw('MAX(bp.total_power_consumption) as total_hrs')
            )
            ->groupBy('bb.id', 'bb.batch_no', 'bb.category', 'bb.start_time', 'bb.end_time')
            ->orderBy('bb.start_time')
            ->get();

        $batchIds = $batches->pluck('id')->toArray();
        $outputMats = DB::table('bbsu_output_materials')
            ->whereIn('bbsu_batch_id', $batchIds)
            ->where('is_active', 1)
            ->select('bbsu_batch_id', 'material_code', 'qty', 'yield_pct')
            ->get()
            ->groupBy('bbsu_batch_id');

        foreach ($batches as $batch) {
            $batch->start_time = $batch->start_time
                ? Carbon::parse($batch->start_time)->format('H:i') : '—';
            $batch->end_time = $batch->end_time
                ? Carbon::parse($batch->end_time)->format('H:i') : '—';
            $batch->total_input = round((float) ($batch->total_input ?? 0), 3);
            $batch->avg_acid = round((float) ($batch->avg_acid ?? 0), 2);
            $batch->total_hrs = round((float) ($batch->total_hrs ?? 0), 3);
            $batch->output_materials = ($outputMats[$batch->id] ?? collect())->values();
        }

        return response()->json([
            'status' => 'ok',
            'date' => Carbon::parse($date)->format('d M Y'),
            'batches' => $batches,
        ]);
    }


}