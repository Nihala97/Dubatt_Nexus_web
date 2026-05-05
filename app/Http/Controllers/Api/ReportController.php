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
            ->when($request->lot_no, fn($q) => $q->where('lot_no', 'like', "%{$request->lot_no}%"))
            ->when($request->filled('category'), fn($q) => $q->whereHas(
                'material',
                fn($mq) =>
                $mq->where('category', $request->category)
            ));

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

        // Pull distinct, non-null categories directly from materials table
        $categories = Material::where('is_active', true)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter()
            ->values();

        return response()->json([
            'status' => 'ok',
            'data' => compact('suppliers', 'materials', 'categories'),
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

    public function bbsuFilters(Request $request)
    {
        // Existing categories
        $categories = DB::table('bbsu_batches')
            ->where('is_active', 1)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // NEW: build available months list from actual data
        // Returns [{value:'2026-04', label:'April 2026'}, ...]
        $months = DB::table('bbsu_batches')
            ->where('is_active', 1)
            ->whereNotNull('doc_date')
            ->selectRaw("DATE_FORMAT(doc_date, '%Y-%m') as ym,
                          MAX(doc_date) as sample_date")
            ->groupBy(DB::raw("DATE_FORMAT(doc_date, '%Y-%m')"))
            ->orderByDesc('ym')
            ->get()
            ->map(fn($r) => [
                'value' => $r->ym,   // '2026-04'
                'label' => Carbon::parse($r->sample_date)->format('F Y'), // 'April 2026'
            ]);

        return response()->json([
            'status' => 'ok',
            'data' => [
                'categories' => $categories,
                'available_months' => $months,
            ],
        ]);
    }

    public function bbsuDashboard(Request $request)
    {
        try {
            $now = Carbon::now();

            // ── NEW: read selected month/year from request ────────────
            // Frontend sends ?month=4&year=2026
            // Defaults to current month/year
            $selectedYear = (int) ($request->input('year', $now->year));
            $selectedMonth = (int) ($request->input('month', $now->month));

            $selectedDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
            $prevMonthDate = $selectedDate->copy()->subMonth();
            $prevMonth = $prevMonthDate->month;
            $prevMonthYear = $prevMonthDate->year;

            // ── Base input query helper ───────────────────────────────
            $inputBase = fn() => DB::table('bbsu_input_details as bi')
                ->join('bbsu_batches as bb', 'bb.id', '=', 'bi.bbsu_batch_id')
                ->where('bb.is_active', 1)
                ->where('bi.is_active', 1);

            // ── 1. PRODUCTION SCORECARDS ──────────────────────────────
            // "Last Month" = month before selected month
            $lastMonthTotal = $inputBase()
                ->whereMonth('bb.doc_date', $prevMonth)
                ->whereYear('bb.doc_date', $prevMonthYear)
                ->sum('bi.quantity');

            $lastMonthByCategory = $inputBase()
                ->whereMonth('bb.doc_date', $prevMonth)
                ->whereYear('bb.doc_date', $prevMonthYear)
                ->select('bb.category', DB::raw('SUM(bi.quantity) as total_qty'), DB::raw('COUNT(DISTINCT bb.id) as batch_count'))
                ->groupBy('bb.category')->orderByDesc('total_qty')->get();

            $currentMonthTotal = $inputBase()
                ->whereMonth('bb.doc_date', $selectedMonth)
                ->whereYear('bb.doc_date', $selectedYear)
                ->sum('bi.quantity');

            $currentMonthByCategory = $inputBase()
                ->whereMonth('bb.doc_date', $selectedMonth)
                ->whereYear('bb.doc_date', $selectedYear)
                ->select('bb.category', DB::raw('SUM(bi.quantity) as total_qty'), DB::raw('COUNT(DISTINCT bb.id) as batch_count'))
                ->groupBy('bb.category')->orderByDesc('total_qty')->get();

            $yearTotal = $inputBase()
                ->whereYear('bb.doc_date', $selectedYear)
                ->sum('bi.quantity');

            // ── 2. AVG HR / MT — start_time → end_time diff ──────────
            $batchTimeRow = DB::table('bbsu_batches as bb')
                ->join('bbsu_input_details as bi', fn($j) =>
                    $j->on('bi.bbsu_batch_id', '=', 'bb.id')->where('bi.is_active', 1))
                ->whereMonth('bb.doc_date', $selectedMonth)
                ->whereYear('bb.doc_date', $selectedYear)
                ->where('bb.is_active', 1)
                ->whereNotNull('bb.start_time')
                ->whereNotNull('bb.end_time')
                ->selectRaw('SUM(TIMESTAMPDIFF(SECOND, bb.start_time, bb.end_time)) as total_seconds,
                              SUM(bi.quantity) as total_input_kg')
                ->first();

            $totalHours = ($batchTimeRow->total_seconds ?? 0) / 3600;
            $totalInputMT = ($batchTimeRow->total_input_kg ?? 0) / 1000;
            $avgHrPerMT = ($totalInputMT > 0 && $totalHours > 0)
                ? round($totalHours / $totalInputMT, 4) : 0;

            // ── 3. WEIGHTED AVG ACID % ────────────────────────────────
            $batchData = DB::table('bbsu_batches as bb')
                ->leftJoin('bbsu_input_details as bi', fn($j) =>
                    $j->on('bi.bbsu_batch_id', '=', 'bb.id')->where('bi.is_active', 1))
                ->where('bb.is_active', 1)
                ->where('bb.category', 'BBSU')
                ->whereMonth('bb.doc_date', $selectedMonth)
                ->whereYear('bb.doc_date', $selectedYear)
                ->select('bb.id', DB::raw('SUM(bi.quantity) as total_qty'), DB::raw('AVG(bi.acid_percentage) as avg_acid_pct'))
                ->groupBy('bb.id')->get();

            $acidNumerator = $batchData->sum(fn($r) => $r->total_qty * $r->avg_acid_pct);
            $totalQty = $batchData->sum('total_qty');
            $avgAcidPct = ($totalQty > 0) ? round($acidNumerator / $totalQty, 4) : 0;

            // ── 4. OUTPUT MATERIAL TOTALS ─────────────────────────────
            $outputMaterials = DB::table('bbsu_output_materials as bo')
                ->join('bbsu_batches as bb', 'bb.id', '=', 'bo.bbsu_batch_id')
                ->leftJoin('materials as m', 'm.material_code', '=', 'bo.material_code')
                ->whereMonth('bb.doc_date', $selectedMonth)
                ->whereYear('bb.doc_date', $selectedYear)
                ->where('bb.is_active', 1)->where('bo.is_active', 1)
                ->select(
                    'bo.material_code',
                    DB::raw("COALESCE(NULLIF(TRIM(m.secondary_name),''),NULLIF(TRIM(m.material_name),''),bo.material_code) as material_display_name"),
                    DB::raw('SUM(bo.qty) as total_qty')
                )
                ->groupBy('bo.material_code', 'm.secondary_name', 'm.material_name')
                ->orderBy('bo.material_code')->get()
                ->map(fn($r) => [
                    'material_code' => $r->material_code,
                    'material_name' => $r->material_display_name,
                    'total_qty' => round((float) $r->total_qty, 3),
                ]);

            // ── 5. LAST DAY BATCHES (within selected month) ───────────
            $lastDocDate = DB::table('bbsu_batches')
                ->where('is_active', 1)
                ->whereMonth('doc_date', $selectedMonth)
                ->whereYear('doc_date', $selectedYear)
                ->max('doc_date');

            $lastDayBatches = collect();
            $lastDayDate = '';

            if ($lastDocDate) {
                $lastDayDate = Carbon::parse($lastDocDate)->format('d M Y');
                $lastDayBatches = DB::table('bbsu_batches as bb')
                    ->leftJoin('bbsu_input_details as bi', fn($j) =>
                        $j->on('bi.bbsu_batch_id', '=', 'bb.id')->where('bi.is_active', 1))
                    ->where('bb.doc_date', $lastDocDate)->where('bb.is_active', 1)
                    ->select(
                        'bb.batch_no',
                        'bb.category',
                        'bb.start_time',
                        'bb.end_time',
                        DB::raw('SUM(bi.quantity) as total_input'),
                        DB::raw('SUM(bi.quantity * bi.acid_percentage) / NULLIF(SUM(bi.quantity),0) as avg_acid'),
                        DB::raw('ROUND(TIMESTAMPDIFF(SECOND, bb.start_time, bb.end_time) / 3600, 3) as total_hrs')
                    )
                    ->groupBy('bb.id', 'bb.batch_no', 'bb.category', 'bb.start_time', 'bb.end_time')
                    ->orderBy('bb.start_time')->get();
            }

            $monthLabel = $selectedDate->format('F Y');
            $lastMonthLabel = $prevMonthDate->format('F Y');

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'selected_month' => $selectedMonth,
                    'selected_year' => $selectedYear,
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
                ],
            ]);

        } catch (\Throwable $e) {
            \Log::error('BBSU Dashboard error', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
                ]
            ], 200);
        }
    }

    public function bbsuChart(Request $request)
    {
        $mode = $request->input('mode', 'weekly');
        $months = min((int) $request->input('months', 1), 3);
        $now = Carbon::now();

        // ── NEW: read selected month/year ─────────────────────────────
        $selectedYear = (int) ($request->input('year', $now->year));
        $selectedMonth = (int) ($request->input('month', $now->month));
        $selectedDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        $datasets = [];

        // Production comparison: uses selected month as base, goes back N months
        for ($i = $months - 1; $i >= 0; $i--) {
            $target = $selectedDate->copy()->subMonths($i);
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
                        ->where('bb.is_active', 1)->where('bi.is_active', 1)->sum('bi.quantity');
                    $buckets["Week $wk"] = round($qty, 2);
                    $cur = $wEnd->copy()->addDay();
                    $wk++;
                }
                $datasets[] = ['label' => $label, 'data' => $buckets];
            } else {
                $buckets = [];
                $cur = $monthStart->copy();
                while ($cur->lte($monthEnd)) {
                    $qty = DB::table('bbsu_input_details as bi')
                        ->join('bbsu_batches as bb', 'bb.id', '=', 'bi.bbsu_batch_id')
                        ->where('bb.doc_date', $cur->toDateString())
                        ->where('bb.is_active', 1)->where('bi.is_active', 1)->sum('bi.quantity');
                    $buckets[$cur->format('d')] = round($qty, 2);
                    $cur->addDay();
                }
                $datasets[] = ['label' => $label, 'data' => $buckets];
            }
        }

        $daysInMonth = $selectedDate->copy()->daysInMonth;

        // Avg hours/MT for selected month
        $rawHours = DB::table('bbsu_batches as bb')
            ->join('bbsu_input_details as bi', fn($j) =>
                $j->on('bi.bbsu_batch_id', '=', 'bb.id')->where('bi.is_active', 1))
            ->whereMonth('bb.doc_date', $selectedMonth)->whereYear('bb.doc_date', $selectedYear)
            ->where('bb.is_active', 1)->whereNotNull('bb.start_time')->whereNotNull('bb.end_time')
            ->selectRaw('DAY(bb.doc_date) as day,
                ROUND(SUM(TIMESTAMPDIFF(SECOND,bb.start_time,bb.end_time))/3600
                / NULLIF(SUM(bi.quantity)/1000,0),4) as avg_hrs')
            ->groupBy(DB::raw('DAY(bb.doc_date)'))->orderBy('day')->get()->keyBy('day');

        $avgHoursPerDay = collect(range(1, $daysInMonth))->map(fn($d) => [
            'day' => $d,
            'avg_hrs' => $rawHours->get($d)?->avg_hrs ?? 0,
        ]);

        // PWR/MT for selected month
        $rawPwr = DB::table('bbsu_batches as bb')
            ->join('bbsu_power_consumption as bp', fn($j) =>
                $j->on('bp.bbsu_batch_id', '=', 'bb.id')->where('bp.is_active', 1))
            ->join('bbsu_input_details as bi', fn($j) =>
                $j->on('bi.bbsu_batch_id', '=', 'bb.id')->where('bi.is_active', 1))
            ->whereMonth('bb.doc_date', $selectedMonth)->whereYear('bb.doc_date', $selectedYear)
            ->where('bb.is_active', 1)
            ->selectRaw('DAY(bb.doc_date) as day,
                ROUND(SUM(bp.total_power_consumption)/NULLIF(SUM(bi.quantity)/1000,0),4) as pwr_per_mt')
            ->groupBy(DB::raw('DAY(bb.doc_date)'))->orderBy('day')->get()->keyBy('day');

        $pwrPerDay = collect(range(1, $daysInMonth))->map(fn($d) => [
            'day' => $d,
            'pwr_per_mt' => $rawPwr->get($d)?->pwr_per_mt ?? 0,
        ]);

        return response()->json([
            'status' => 'ok',
            'mode' => $mode,
            'datasets' => $datasets,
            'avg_hours_per_day' => $avgHoursPerDay,
            'pwr_per_day' => $pwrPerDay,
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
                DB::raw('SUM(bi.quantity)                as total_input_qty'),
                DB::raw('AVG(bi.acid_percentage)         as avg_acid_pct'),
                DB::raw('MAX(bp.initial_power)           as initial_power'),
                DB::raw('MAX(bp.final_power)             as final_power'),
                DB::raw('MAX(bp.total_power_consumption) as total_power_hrs')
            )
            ->groupBy('bb.id', 'bb.batch_no', 'bb.doc_date', 'bb.category', 'bb.start_time', 'bb.end_time', 'bb.status');

        // Filters — unchanged
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

        // Sorting — unchanged
        $nativeSorts = ['doc_date', 'batch_no', 'category', 'start_time', 'end_time'];
        if (in_array($sortBy, $nativeSorts)) {
            $query->orderBy('bb.' . $sortBy, $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $paginated = $query->paginate($perPage);
        $batchIds = collect($paginated->items())->pluck('id')->toArray();

        // ── Output materials — NOW with secondary_name from materials table ──
        $outputMatsRaw = DB::table('bbsu_output_materials as bo')
            ->leftJoin('materials as m', 'm.material_code', '=', 'bo.material_code')
            ->whereIn('bo.bbsu_batch_id', $batchIds)
            ->where('bo.is_active', 1)
            ->select(
                'bo.bbsu_batch_id',
                'bo.material_code',
                // Priority: secondary_name → material_name → material_code
                DB::raw("COALESCE(
                    NULLIF(TRIM(m.secondary_name), ''),
                    NULLIF(TRIM(m.material_name), ''),
                    bo.material_code
                ) as material_name"),
                'bo.qty',
                'bo.yield_pct'
            )
            ->get()
            ->groupBy('bbsu_batch_id');

        $statusMap = [0 => 'Pending', 1 => 'In Progress', 2 => 'Completed', 3 => 'Cancelled'];

        $rows = collect($paginated->items())->map(function ($r) use ($outputMatsRaw, $statusMap) {
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
                // ↓ Now includes material_name (secondary_name from materials table)
                'output_materials' => ($outputMatsRaw[$r->id] ?? collect())->map(fn($m) => [
                    'material_code' => $m->material_code,
                    'material_name' => $m->material_name,   // ← secondary_name or fallback
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

    // ════════════════════════════════════════════════════════════
    // FILTERS  GET /api/reports/smelting/filters
    // ════════════════════════════════════════════════════════════
    public function smeltingReportFilters(): \Illuminate\Http\JsonResponse
    {
        $rotaries = \App\Models\SmeltingBatch::where('is_active', 1)
            ->distinct()->orderBy('rotary_no')->pluck('rotary_no');

        $months = \App\Models\SmeltingBatch::where('is_active', 1)
            ->whereNotNull('date')
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym, MAX(date) as sample_date")
            ->groupBy(\DB::raw("DATE_FORMAT(date, '%Y-%m')"))
            ->orderByDesc('ym')
            ->get()
            ->map(fn($r) => [
                'value' => $r->ym,
                'label' => \Carbon\Carbon::parse($r->sample_date)->format('F Y'),
                'month' => (int) \Carbon\Carbon::parse($r->sample_date)->format('m'),
                'year' => (int) \Carbon\Carbon::parse($r->sample_date)->format('Y'),
            ]);

        return response()->json([
            'status' => 'ok',
            'data' => [
                'rotaries' => $rotaries,
                'available_months' => $months,
            ],
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // DASHBOARD  GET /api/reports/smelting/dashboard
    //            ?month=4&year=2026
    // ════════════════════════════════════════════════════════════
    public function smeltingDashboard(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $now = \Carbon\Carbon::now();

            $selYear = (int) ($request->input('year', $now->year));
            $selMonth = (int) ($request->input('month', $now->month));

            $selDate = \Carbon\Carbon::createFromDate($selYear, $selMonth, 1)->startOfMonth();
            $curStart = $selDate->copy()->toDateString();
            $curEnd = $selDate->copy()->endOfMonth()->toDateString();

            $prevDate = $selDate->copy()->subMonth();
            $prevStart = $prevDate->copy()->startOfMonth()->toDateString();
            $prevEnd = $prevDate->copy()->endOfMonth()->toDateString();

            $yearStart = \Carbon\Carbon::createFromDate($selYear, 1, 1)->toDateString();
            $yearEnd = $curEnd;

            $outBetween = fn($f, $t) => round(
                \App\Models\SmeltingBatch::where('is_active', 1)->whereBetween('date', [$f, $t])->sum('output_qty'),
                3
            );

            $currentMonthTotal = $outBetween($curStart, $curEnd);
            $previousMonthTotal = $outBetween($prevStart, $prevEnd);
            $yearTotal = $outBetween($yearStart, $yearEnd);

            // Day-wise comparison (3 months)
            $dayWise = [];
            for ($i = 0; $i <= 2; $i++) {
                $m = \Carbon\Carbon::createFromDate($selYear, $selMonth, 1)->subMonths($i);
                $from = $m->copy()->startOfMonth()->toDateString();
                $to = $m->copy()->endOfMonth()->toDateString();

                $rows = \App\Models\SmeltingBatch::where('is_active', 1)
                    ->whereBetween('date', [$from, $to])
                    ->select(\DB::raw('DAY(date) as day'), \DB::raw('SUM(output_qty) as total_qty'))
                    ->groupBy(\DB::raw('DAY(date)'))
                    ->orderBy(\DB::raw('DAY(date)'))
                    ->get()->keyBy('day');

                $daysIn = $m->copy()->endOfMonth()->day;
                $daily = [];
                for ($d = 1; $d <= $daysIn; $d++) {
                    $daily[] = ['day' => $d, 'label' => 'D' . $d, 'qty' => round($rows->get($d)?->total_qty ?? 0, 3)];
                }
                $dayWise[] = [
                    'key' => 'month_' . $i,
                    'label' => $m->format('M Y'),
                    'month' => $m->month,
                    'year' => $m->year,
                    'data' => $daily,
                    'total' => round(collect($daily)->sum('qty'), 3),
                ];
            }

            // Yield data
            $yieldData = $this->smeltingYieldComparison($curStart, $curEnd);
            $yieldMonths = [];
            for ($i = 0; $i <= 5; $i++) {
                $m = \Carbon\Carbon::createFromDate($selYear, $selMonth, 1)->subMonths($i);
                $yieldMonths[] = [
                    'label' => $m->format('F Y'),
                    'from' => $m->copy()->startOfMonth()->toDateString(),
                    'to' => $m->copy()->endOfMonth()->toDateString(),
                ];
            }

            $avgHrsCur = $this->smeltingAvgHrs($curStart, $curEnd);
            $avgHrsPrev = $this->smeltingAvgHrs($prevStart, $prevEnd);
            $avgLpgCur = $this->smeltingAvgConsPerUnit('lpg', $curStart, $curEnd);
            $avgLpgPrev = $this->smeltingAvgConsPerUnit('lpg', $prevStart, $prevEnd);
            $avgO2Cur = $this->smeltingAvgConsPerUnit('o2', $curStart, $curEnd);
            $avgO2Prev = $this->smeltingAvgConsPerUnit('o2', $prevStart, $prevEnd);

            $trendBase = \Carbon\Carbon::createFromDate($selYear, $selMonth, 1);
            $lpgTrend = $this->smeltingMonthlyTrend('lpg', $trendBase, 6);
            $o2Trend = $this->smeltingMonthlyTrend('o2', $trendBase, 6);

            $tempData = $this->smeltingTempGraph($curStart, $curEnd);

            $rotaryBreakdown = \App\Models\SmeltingBatch::where('is_active', 1)
                ->whereBetween('date', [$curStart, $curEnd])
                ->select('rotary_no', \DB::raw('SUM(output_qty) as total_qty'), \DB::raw('COUNT(*) as batch_count'))
                ->groupBy('rotary_no')
                ->get();

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'selected_month' => $selMonth,
                    'selected_year' => $selYear,
                    'current_month_total' => $currentMonthTotal,
                    'previous_month_total' => $previousMonthTotal,
                    'year_total' => $yearTotal,
                    'day_wise' => $dayWise,
                    'yield_data' => $yieldData,
                    'yield_months' => $yieldMonths,
                    'avg_hrs_cur' => $avgHrsCur,
                    'avg_hrs_prev' => $avgHrsPrev,
                    'avg_lpg_cur' => $avgLpgCur,
                    'avg_lpg_prev' => $avgLpgPrev,
                    'avg_o2_cur' => $avgO2Cur,
                    'avg_o2_prev' => $avgO2Prev,
                    'lpg_trend' => $lpgTrend,
                    'o2_trend' => $o2Trend,
                    'temp_data' => $tempData,
                    'rotary_breakdown' => $rotaryBreakdown,
                    'month_label' => $selDate->format('F Y'),
                    'prev_label' => $prevDate->format('F Y'),
                    'year_label' => (string) $selYear,
                ],
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Smelting Dashboard error', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════
    // YIELD DRILLDOWN  GET /api/reports/smelting/yield
    // ════════════════════════════════════════════════════════════
    public function smeltingYieldDrilldown(Request $request): \Illuminate\Http\JsonResponse
    {
        $from = $request->from ?? \Carbon\Carbon::now()->startOfMonth()->toDateString();
        $to = $request->to ?? \Carbon\Carbon::now()->endOfMonth()->toDateString();
        return response()->json(['status' => 'ok', 'data' => $this->smeltingYieldComparison($from, $to)]);
    }

    // ════════════════════════════════════════════════════════════
    // REPORT  GET /api/reports/smelting/report
    // ════════════════════════════════════════════════════════════
    public function smeltingReport(Request $request): \Illuminate\Http\JsonResponse
    {
        $sortBy = in_array(
            $request->sort_by,
            ['date', 'batch_no', 'charge_no', 'rotary_no', 'output_qty', 'lpg_consumption', 'o2_consumption']
        ) ? $request->sort_by : 'date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $perPage = min((int) ($request->per_page ?? 50), 500);

        $query = \App\Models\SmeltingBatch::with([
            'rawMaterials',
            'fluxChemicals',
            'processDetails',
            'temperatureRecords',
            'outputBlocks',
        ])
            ->where('is_active', 1)
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->when($request->filled('rotary_no'), fn($q) => $q->where('rotary_no', $request->rotary_no))
            ->when($request->filled('batch_no'), fn($q) => $q->where('batch_no', 'like', "%{$request->batch_no}%"))
            ->when($request->filled('charge_no'), fn($q) => $q->where('charge_no', 'like', "%{$request->charge_no}%"))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderBy($sortBy, $sortDir);

        $paginated = $query->paginate($perPage);
        $rows = collect($paginated->items())->map(fn($b) => $this->smeltingMapRow($b));

        return response()->json([
            'status' => 'ok',
            'data' => $rows->values(),
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'summary' => [
                    'total_output_qty' => round($rows->sum('output_qty'), 3),
                    'total_raw_qty' => round($rows->sum('total_raw_qty'), 3),
                    'avg_yield_pct' => round($rows->avg('avg_yield_pct'), 2),
                    'total_lpg' => round($rows->sum('lpg_consumption'), 3),
                    'total_o2' => round($rows->sum('o2_consumption'), 3),
                    'total_id_fan' => round($rows->sum('id_fan_consumption'), 3),
                    'total_rotary_power' => round($rows->sum('rotary_power_consumption'), 3),
                ],
            ],
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ════════════════════════════════════════════════════════════

    private function smeltingBatchHours($b): float
    {
        if (!$b->start_time || !$b->end_time)
            return 0;
        $mins = \Carbon\Carbon::parse($b->start_time)->diffInMinutes(\Carbon\Carbon::parse($b->end_time), false);
        if ($mins < 0)
            $mins += 1440;
        return round($mins / 60, 4);
    }

    private function smeltingAvgHrs(string $from, string $to): float
    {
        $batches = \App\Models\SmeltingBatch::with('processDetails')
            ->where('is_active', 1)
            ->whereBetween('date', [$from, $to])
            ->where('output_qty', '>', 0)
            ->get();

        if ($batches->isEmpty())
            return 0;

        $totalProcessHrs = $batches->sum(function ($b) {
            return $b->processDetails->sum('total_time') / 60;
        });

        $totalOutputMT = $batches->sum('output_qty') / 1000;
        return $totalOutputMT > 0 ? round($totalProcessHrs / $totalOutputMT, 4) : 0;
    }

    private function smeltingAvgConsPerUnit(string $type, string $from, string $to): float
    {
        $col = $type === 'lpg' ? 'lpg_consumption' : 'o2_consumption';
        $rows = \App\Models\SmeltingBatch::where('is_active', 1)
            ->whereBetween('date', [$from, $to])
            ->whereNotNull($col)
            ->where('output_qty', '>', 0)
            ->get([$col, 'output_qty']);

        if ($rows->isEmpty())
            return 0;

        $totalOutputMT = $rows->sum('output_qty') / 1000;
        if ($totalOutputMT <= 0)
            return 0;

        if ($type === 'lpg') {
            return round(($rows->sum($col) * 4.2) / $totalOutputMT, 4);
        } else {
            return round(($rows->sum($col) * 1.429) / $totalOutputMT, 4);
        }
    }

    private function smeltingMonthlyTrend(string $type, \Carbon\Carbon $baseDate, int $months): array
    {
        $col = $type === 'lpg' ? 'lpg_consumption' : 'o2_consumption';
        $result = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $m = \Carbon\Carbon::createFromDate($baseDate->year, $baseDate->month, 1)->subMonths($i);
            $from = $m->copy()->startOfMonth()->toDateString();
            $to = $m->copy()->endOfMonth()->toDateString();

            $result[] = [
                'month' => $m->format('M Y'),
                'month_num' => $m->month,
                'year' => $m->year,
                'avg_per_mt' => $this->smeltingAvgConsPerUnit($type, $from, $to),
                'total' => round(\App\Models\SmeltingBatch::where('is_active', 1)->whereBetween('date', [$from, $to])->sum($col), 3),
                'output_qty' => round(\App\Models\SmeltingBatch::where('is_active', 1)->whereBetween('date', [$from, $to])->sum('output_qty'), 3),
            ];
        }
        return $result;
    }

    private function smeltingYieldComparison(string $from, string $to): array
    {
        $batches = \App\Models\SmeltingBatch::with(['rawMaterials'])
            ->where('is_active', 1)
            ->whereBetween('date', [$from, $to])
            ->get();

        $daily = $batches->groupBy(fn($b) => \Carbon\Carbon::parse($b->date)->format('d M'))
            ->map(fn($grp, $day) => [
                'day' => $day,
                'date' => \Carbon\Carbon::parse($grp->first()->date)->toDateString(),
                'expected_qty' => round($grp->sum(fn($b) => $b->rawMaterials->sum('expected_output_qty')), 3),
                'actual_qty' => round($grp->sum('output_qty'), 3),
            ])->values();

        $totalExp = round($daily->sum('expected_qty'), 3);
        $totalAct = round($daily->sum('actual_qty'), 3);

        return [
            'daily' => $daily,
            'total_expected' => $totalExp,
            'total_actual' => $totalAct,
            'diff_pct' => $totalExp > 0 ? round((($totalAct - $totalExp) / $totalExp) * 100, 2) : 0,
            'period' => \Carbon\Carbon::parse($from)->format('d M Y') . ' – ' . \Carbon\Carbon::parse($to)->format('d M Y'),
        ];
    }

    private function smeltingTempGraph(string $from, string $to): array
    {
        $records = \App\Models\SmeltingTemperatureRecord::join(
            'smelting_batches',
            'smelting_batches.id',
            '=',
            'smelting_temperature_records.smelting_batch_id'
        )
            ->where('smelting_batches.is_active', 1)
            ->whereBetween('smelting_batches.date', [$from, $to])
            ->where('smelting_temperature_records.is_active', 1)
            ->select(
                'smelting_batches.date',
                'smelting_temperature_records.inside_temp_before_charging',
                'smelting_temperature_records.process_gas_chamber_temp',
                'smelting_temperature_records.shell_temp',
                'smelting_temperature_records.bag_house_temp'
            )
            ->orderBy('smelting_batches.date')
            ->get();

        return $records->groupBy(fn($r) => \Carbon\Carbon::parse($r->date)->format('d M'))
            ->map(fn($grp, $day) => [
                'day' => $day,
                'inside_temp' => round($grp->avg('inside_temp_before_charging'), 2),
                'pgc_temp' => round($grp->avg('process_gas_chamber_temp'), 2),
                'shell_temp' => round($grp->whereNotNull('shell_temp')
                    ->filter(fn($r) => is_numeric($r->shell_temp))
                    ->avg(fn($r) => (float) $r->shell_temp), 2),
                'bag_house_temp' => round($grp->whereNotNull('bag_house_temp')
                    ->filter(fn($r) => is_numeric($r->bag_house_temp))
                    ->avg(fn($r) => (float) $r->bag_house_temp), 2),
            ])->values()->toArray();
    }

    private function smeltingMapRow(\App\Models\SmeltingBatch $b): array
    {
        $rawMats = $b->rawMaterials;
        $fluxChems = $b->fluxChemicals;
        $procDets = $b->processDetails;
        $tempRecs = $b->temperatureRecords;

        return [
            'id' => $b->id,
            'date' => $b->date?->format('d/m/Y') ?? '—',
            'date_raw' => $b->date?->toDateString() ?? '',
            'batch_no' => $b->batch_no,
            'charge_no' => $b->charge_no ?? '—',
            'rotary_no' => $b->rotary_no,
            'start_time' => $b->start_time?->format('H:i') ?? '—',
            'end_time' => $b->end_time?->format('H:i') ?? '—',
            'duration_hours' => round($this->smeltingBatchHours($b), 3),
            'total_raw_qty' => round($rawMats->sum('raw_material_qty'), 3),
            'expected_output_qty' => round($rawMats->sum('expected_output_qty'), 3),
            'avg_yield_pct' => round($rawMats->avg('raw_material_yield_pct') ?? 0, 2),
            'total_flux_qty' => round($fluxChems->sum('qty'), 3),
            'output_qty' => (float) ($b->output_qty ?? 0),
            'output_material' => $b->output_material ?? '—',
            'lpg_consumption' => (float) ($b->lpg_consumption ?? 0),
            'o2_consumption' => (float) ($b->o2_consumption ?? 0),
            'id_fan_initial' => (float) ($b->id_fan_initial ?? 0),
            'id_fan_final' => (float) ($b->id_fan_final ?? 0),
            'id_fan_consumption' => (float) ($b->id_fan_consumption ?? 0),
            'rotary_power_initial' => (float) ($b->rotary_power_initial ?? 0),
            'rotary_power_final' => (float) ($b->rotary_power_final ?? 0),
            'rotary_power_consumption' => (float) ($b->rotary_power_consumption ?? 0),
            'total_process_mins' => round($procDets->sum('total_time'), 2),
            'avg_inside_temp' => round($tempRecs->avg('inside_temp_before_charging') ?? 0, 2),
            'avg_pgc_temp' => round($tempRecs->avg('process_gas_chamber_temp') ?? 0, 2),
            'remarks' => $b->remarks ?? '—',
            'status' => $b->status,
            'raw_materials' => $rawMats->map(fn($r) => [
                'material' => optional($r->material)->material_name ?? optional($r->material)->name ?? '—',
                'bbsu_no' => $r->bbsu_batch_no ?? '—',
                'qty' => $r->raw_material_qty,
                'yield_pct' => $r->raw_material_yield_pct,
                'expected' => $r->expected_output_qty,
            ])->values(),
            'process_details' => $procDets->map(fn($p) => [
                'process' => $p->process_name,
                'start' => $p->start_time?->format('H:i') ?? '—',
                'end' => $p->end_time?->format('H:i') ?? '—',
                'total_time' => $p->total_time,
                'firing_mode' => $p->firing_mode ?? '—',
            ])->values(),
        ];
    }
    public function refiningDashboard(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $now = \Carbon\Carbon::now();

            // ── Selected month/year ───────────────────────────────
            $selYear = (int) ($request->input('year', $now->year));
            $selMonth = (int) ($request->input('month', $now->month));
            $selDate = \Carbon\Carbon::createFromDate($selYear, $selMonth, 1)->startOfMonth();

            $curStart = $selDate->copy()->toDateString();
            $curEnd = $selDate->copy()->endOfMonth()->toDateString();

            $prevDate = $selDate->copy()->subMonth();
            $prevStart = $prevDate->copy()->startOfMonth()->toDateString();
            $prevEnd = $prevDate->copy()->endOfMonth()->toDateString();

            $yearStart = \Carbon\Carbon::createFromDate($selYear, 1, 1)->toDateString();
            $yearEnd = $curEnd;

            // ── Helper: total FG output ───────────────────────────
            $fgBetween = fn($from, $to) => round(
                \App\Models\RefiningFinishedGoodsSummary::join(
                    'refining_batches',
                    'refining_batches.id',
                    '=',
                    'refining_finished_goods_summary.refining_batch_id'
                )
                    ->where('refining_batches.is_active', 1)
                    ->whereBetween('refining_batches.date', [$from, $to])
                    ->where('refining_finished_goods_summary.is_active', 1)
                    ->sum('refining_finished_goods_summary.total_qty'),
                3
            );

            // ── 1. SCORECARDS ─────────────────────────────────────
            $currentMonthTotal = $fgBetween($curStart, $curEnd);
            $lastMonthTotal = $fgBetween($prevStart, $prevEnd);
            $yearTotal = $fgBetween($yearStart, $yearEnd);

            // ── 2. CATEGORY OUTPUT (for existing category doughnut)
            $categoryOutput = \App\Models\RefiningFinishedGoodsSummary::join(
                'refining_batches',
                'refining_batches.id',
                '=',
                'refining_finished_goods_summary.refining_batch_id'
            )
                ->join('materials', 'materials.id', '=', 'refining_finished_goods_summary.material_id')
                ->where('refining_batches.is_active', 1)
                ->whereBetween('refining_batches.date', [$curStart, $curEnd])
                ->where('refining_finished_goods_summary.is_active', 1)
                ->select(
                    \DB::raw('COALESCE(materials.category, "Uncategorised") as category'),
                    \DB::raw('COALESCE(materials.material_name, materials.secondary_name, "Unknown") as material_name'),
                    \DB::raw('SUM(refining_finished_goods_summary.total_qty) as total_qty')
                )
                ->groupBy('materials.id', 'materials.category', 'materials.material_name', 'materials.secondary_name')
                ->orderByDesc('total_qty')
                ->get();

            $byCategory = $categoryOutput
                ->groupBy('category')
                ->map(fn($g, $cat) => ['category' => $cat, 'total_qty' => round($g->sum('total_qty'), 3)])
                ->sortByDesc('total_qty')
                ->values();

            // ── 3. MATERIAL-WISE FG DOUGHNUT (new) ───────────────
            // Shows each individual material's total_qty in selected month
            $materialDoughnut = \App\Models\RefiningFinishedGoodsSummary::join(
                'refining_batches',
                'refining_batches.id',
                '=',
                'refining_finished_goods_summary.refining_batch_id'
            )
                ->join('materials', 'materials.id', '=', 'refining_finished_goods_summary.material_id')
                ->where('refining_batches.is_active', 1)
                ->whereBetween('refining_batches.date', [$curStart, $curEnd])
                ->where('refining_finished_goods_summary.is_active', 1)
                ->select(
                    'materials.id as material_id',
                    \DB::raw('COALESCE(materials.secondary_name, materials.material_name, "Unknown") as material_name'),
                    \DB::raw('COALESCE(materials.category, "Uncategorised") as category'),
                    \DB::raw('SUM(refining_finished_goods_summary.total_qty) as total_qty')
                )
                ->groupBy('materials.id', 'materials.secondary_name', 'materials.material_name', 'materials.category')
                ->orderByDesc('total_qty')
                ->get()
                ->map(fn($r) => [
                    'material_id' => $r->material_id,
                    'material_name' => $r->material_name,
                    'category' => $r->category,
                    'total_qty' => round((float) $r->total_qty, 3),
                ]);

            // ── 4. DROSS-WISE DOUGHNUT (new) ──────────────────────
            // Shows each dross material's total_qty in selected month
            $drossDoughnut = \App\Models\RefiningDrossSummary::join(
                'refining_batches',
                'refining_batches.id',
                '=',
                'refining_dross_summary.refining_batch_id'
            )
                ->join('materials', 'materials.id', '=', 'refining_dross_summary.material_id')
                ->where('refining_batches.is_active', 1)
                ->whereBetween('refining_batches.date', [$curStart, $curEnd])
                ->select(
                    'materials.id as material_id',
                    \DB::raw('COALESCE(materials.secondary_name, materials.material_name, "Unknown") as material_name'),
                    \DB::raw('COALESCE(materials.category, "Uncategorised") as category'),
                    \DB::raw('SUM(refining_dross_summary.total_qty) as total_qty')
                )
                ->groupBy('materials.id', 'materials.secondary_name', 'materials.material_name', 'materials.category')
                ->orderByDesc('total_qty')
                ->get()
                ->map(fn($r) => [
                    'material_id' => $r->material_id,
                    'material_name' => $r->material_name,
                    'category' => $r->category,
                    'total_qty' => round((float) $r->total_qty, 3),
                ]);

            // ── 5. POT PRODUCTION ─────────────────────────────────
            $potBatches = \App\Models\RefiningBatch::with([
                'finishedGoodsSummary.material',
                'rawMaterials.material'
            ])
                ->where('is_active', 1)
                ->whereBetween('date', [$curStart, $curEnd])
                ->whereNotNull('pot_no')
                ->get();

            $potProduction = $potBatches->groupBy('pot_no')
                ->map(fn($batches, $pot) => [
                    'pot_no' => $pot,
                    'batch_count' => $batches->count(),
                    'total_fg_qty' => round($batches->flatMap->finishedGoodsSummary->sum('total_qty'), 3),
                    'total_raw_qty' => round($batches->flatMap->rawMaterials->sum('qty'), 3),
                    'materials' => $batches->flatMap->finishedGoodsSummary
                        ->groupBy('material_id')
                        ->map(fn($g) => [
                            'material_name' => $g->first()->material->material_name
                                ?? $g->first()->material->name ?? '—',
                            'category' => $g->first()->material->category ?? '—',
                            'total_qty' => round($g->sum('total_qty'), 3),
                        ])->sortByDesc('total_qty')->values(),
                ])
                ->sortByDesc('total_fg_qty')
                ->values()
                ->take(4);

            // ── 6. METRICS ────────────────────────────────────────
            $curBatches = \App\Models\RefiningBatch::where('is_active', 1)
                ->whereBetween('date', [$curStart, $curEnd])->get();
            $totalFgQty = $curBatches->flatMap->finishedGoodsSummary->sum('total_qty');
            $totalProcMins = $curBatches->sum('total_process_time');

            // FIX: Avg HR/MT = (total_process_time in min / 60) / (total_fg_qty / 1000)
            $totalOutputMT = $totalFgQty / 1000;
            $avgHrPerUnit = $totalOutputMT > 0
                ? round(($totalProcMins / 60) / $totalOutputMT, 4)
                : 0;

            // Category-wise avg hr
            $avgHrByCategory = $curBatches->flatMap(
                fn($b) => $b->finishedGoodsSummary->map(fn($fg) => [
                    'category' => $fg->material->category ?? '—',
                    'fg_qty' => $fg->total_qty,
                    'proc_hrs' => $b->total_process_time ? $b->total_process_time / 60 : 0,
                ])
            )->groupBy('category')->map(fn($g, $cat) => [
                    'category' => $cat,
                    'avg_hr_unit' => ($g->sum('fg_qty') / 1000) > 0
                        ? round($g->sum('proc_hrs') / ($g->sum('fg_qty') / 1000), 4) : 0,
                    'total_qty' => round($g->sum('fg_qty'), 3),
                ])->sortByDesc('total_qty')->values();

            // FIX: LPG/MT = total LPG consumption (LTR) / (total output / 1000)
            // lpg_consumption_ltr is already in litres; if not available, use lpg_consumption * 4.2
            $totalLpgLtr = $curBatches->sum('lpg_consumption_ltr')
                ?: ($curBatches->sum('lpg_consumption') * 4.2);
            $avgLpgPerUnit = $totalOutputMT > 0
                ? round($totalLpgLtr / $totalOutputMT, 4)
                : 0;

            // ── 7. DAILY FG OUTPUT ────────────────────────────────
            $dailyOutput = \App\Models\RefiningFinishedGoodsSummary::join(
                'refining_batches',
                'refining_batches.id',
                '=',
                'refining_finished_goods_summary.refining_batch_id'
            )
                ->where('refining_batches.is_active', 1)
                ->whereBetween('refining_batches.date', [$curStart, $curEnd])
                ->select(
                    \DB::raw('DAY(refining_batches.date) as day'),
                    \DB::raw('SUM(refining_finished_goods_summary.total_qty) as total_qty')
                )
                ->groupBy(\DB::raw('DAY(refining_batches.date)'))
                ->orderBy(\DB::raw('DAY(refining_batches.date)'))
                ->get()->keyBy('day');

            $daysIn = $selDate->copy()->endOfMonth()->day;
            $dailyArr = [];
            for ($d = 1; $d <= $daysIn; $d++) {
                $dailyArr[] = ['day' => $d, 'label' => 'D' . $d, 'qty' => round($dailyOutput->get($d)?->total_qty ?? 0, 3)];
            }

            // ── 8. DROSS TOTAL ────────────────────────────────────
            $drossTotal = round(
                \App\Models\RefiningDrossSummary::join(
                    'refining_batches',
                    'refining_batches.id',
                    '=',
                    'refining_dross_summary.refining_batch_id'
                )
                    ->where('refining_batches.is_active', 1)
                    ->whereBetween('refining_batches.date', [$curStart, $curEnd])
                    ->sum('refining_dross_summary.total_qty'),
                3
            );

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'selected_month' => $selMonth,
                    'selected_year' => $selYear,
                    'current_month_total' => $currentMonthTotal,
                    'last_month_total' => $lastMonthTotal,
                    'year_total' => $yearTotal,
                    'by_category' => $byCategory,
                    'category_output' => $categoryOutput,
                    'material_doughnut' => $materialDoughnut,   // NEW
                    'dross_doughnut' => $drossDoughnut,      // NEW
                    'pot_production' => $potProduction,
                    'avg_hr_per_unit' => $avgHrPerUnit,        // FIXED formula
                    'avg_hr_by_category' => $avgHrByCategory,    // FIXED formula
                    'avg_lpg_per_unit' => $avgLpgPerUnit,      // FIXED formula
                    // O2/MT removed from scorecards — kept for reference only
                    'avg_o2_per_unit' => $totalOutputMT > 0
                        ? round($curBatches->sum('oxygen_consumption') / $totalOutputMT, 4) : 0,
                    'daily_output' => $dailyArr,
                    'dross_total' => $drossTotal,
                    'month_label' => $selDate->format('F Y'),
                    'prev_label' => $prevDate->format('F Y'),
                    'year_label' => (string) $selYear,
                ],
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Refining Dashboard error', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════
    // REFINING REPORT
    // GET /api/reports/refining/report
    // ════════════════════════════════════════════════════════════
    public function refiningReport(Request $request): \Illuminate\Http\JsonResponse
    {
        $allowedSorts = [
            'date',
            'batch_no',
            'pot_no',
            'lpg_consumption',
            'lpg2_consumption',
            'electricity_consumption',
            'oxygen_consumption',
            'total_process_time'
        ];
        $sortBy = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $perPage = min((int) ($request->per_page ?? 50), 500);

        $query = \App\Models\RefiningBatch::with([
            'material',
            'rawMaterials',
            'chemicals',
            'processDetails',
            'finishedGoodsSummary.material',
            'drossSummary.material',
        ])
            ->where('is_active', 1)
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->when($request->filled('pot_no'), fn($q) => $q->where('pot_no', $request->pot_no))
            ->when($request->filled('batch_no'), fn($q) => $q->where('batch_no', 'like', "%{$request->batch_no}%"))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('material_id'), fn($q) => $q->where('material_id', $request->material_id))
            ->orderBy($sortBy, $sortDir);

        $paginated = $query->paginate($perPage);
        $rows = collect($paginated->items())->map(fn($b) => $this->refiningMapRow($b));

        return response()->json([
            'status' => 'ok',
            'data' => $rows->values(),
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'summary' => [
                    'total_fg_qty' => round($rows->sum('total_fg_qty'), 3),
                    'total_dross_qty' => round($rows->sum('total_dross_qty'), 3),
                    'total_raw_qty' => round($rows->sum('total_raw_qty'), 3),
                    'total_lpg' => round($rows->sum('lpg_consumption'), 3),
                    'total_lpg2' => round($rows->sum('lpg2_consumption'), 3),
                    'total_electricity' => round($rows->sum('electricity_consumption'), 3),
                    'total_oxygen' => round($rows->sum('oxygen_consumption'), 3),
                ],
            ],
        ]);
    }

    // ────────────────────────────────────────────────────────────
    // REFINING REPORT FILTERS  GET /api/reports/refining/filters
    // ────────────────────────────────────────────────────────────
    public function refiningReportFilters(): \Illuminate\Http\JsonResponse
    {
        $pots = \App\Models\RefiningBatch::where('is_active', 1)
            ->whereNotNull('pot_no')->distinct()->orderBy('pot_no')->pluck('pot_no');

        $materials = \App\Models\Material::where('is_active', 1)
            ->orderBy('material_name')
            ->get(['id', 'material_name', 'secondary_name'])
            ->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->material_name ?? $m->secondary_name ?? '—'
            ]);

        // Available months from actual refining batch dates
        $months = \App\Models\RefiningBatch::where('is_active', 1)
            ->whereNotNull('date')
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym, MAX(date) as sample_date")
            ->groupBy(\DB::raw("DATE_FORMAT(date, '%Y-%m')"))
            ->orderByDesc('ym')
            ->get()
            ->map(fn($r) => [
                'value' => $r->ym,
                'label' => \Carbon\Carbon::parse($r->sample_date)->format('F Y'),
                'month' => (int) \Carbon\Carbon::parse($r->sample_date)->format('m'),
                'year' => (int) \Carbon\Carbon::parse($r->sample_date)->format('Y'),
            ]);

        return response()->json([
            'status' => 'ok',
            'data' => compact('pots', 'materials', 'months'),
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // PRIVATE HELPERS — prefixed refiningXxx
    // ════════════════════════════════════════════════════════════
    private function refiningMapRow(\App\Models\RefiningBatch $b): array
    {
        $rawMats = $b->rawMaterials;
        $chems = $b->chemicals;
        $procDets = $b->processDetails;
        $fgSumm = $b->finishedGoodsSummary;
        $drossSumm = $b->drossSummary;

        return [
            'id' => $b->id,
            'date' => $b->date?->format('d/m/Y') ?? '—',
            'date_raw' => $b->date?->toDateString() ?? '',
            'batch_no' => $b->batch_no,
            'pot_no' => $b->pot_no ?? '—',
            'material_name' => $b->material->material_name ?? $b->material->name ?? '—',
            // Input
            'total_raw_qty' => round($rawMats->sum('qty'), 3),
            'total_chemical_qty' => round($chems->sum('qty'), 3),
            // FG Output
            'total_fg_qty' => round($fgSumm->sum('total_qty'), 3),
            'fg_details' => $fgSumm->map(fn($fg) => [
                'material' => $fg->material->material_name ?? $fg->material->name ?? '—',
                'category' => $fg->material->category ?? '—',
                'qty' => round($fg->total_qty, 3),
            ])->values(),
            // Dross
            'total_dross_qty' => round($drossSumm->sum('total_qty'), 3),
            'dross_details' => $drossSumm->map(fn($dr) => [
                'material' => $dr->material->material_name ?? $dr->material->name ?? '—',
                'qty' => round($dr->total_qty, 3),
            ])->values(),
            // LPG 1
            'lpg_initial' => (float) ($b->lpg_initial ?? 0),
            'lpg_final' => (float) ($b->lpg_final ?? 0),
            'lpg_consumption' => (float) ($b->lpg_consumption ?? 0),
            'lpg_consumption_ltr' => (float) ($b->lpg_consumption_ltr ?? 0),
            // LPG 2
            'lpg2_initial' => (float) ($b->lpg2_initial ?? 0),
            'lpg2_final' => (float) ($b->lpg2_final ?? 0),
            'lpg2_consumption' => (float) ($b->lpg2_consumption ?? 0),
            'lpg2_consumption_ltr' => (float) ($b->lpg2_consumption_ltr ?? 0),
            // Electricity
            'electricity_initial' => (float) ($b->electricity_initial ?? 0),
            'electricity_final' => (float) ($b->electricity_final ?? 0),
            'electricity_consumption' => (float) ($b->electricity_consumption ?? 0),
            // Oxygen
            'oxygen_flow_nm3' => (float) ($b->oxygen_flow_nm3 ?? 0),
            'oxygen_flow_kg' => (float) ($b->oxygen_flow_kg ?? 0),
            'oxygen_flow_time' => (float) ($b->oxygen_flow_time ?? 0),
            'oxygen_consumption' => (float) ($b->oxygen_consumption ?? 0),
            // Process
            'total_process_time' => (float) ($b->total_process_time ?? 0),
            'process_details' => $procDets->map(fn($p) => [
                'process' => $p->refining_process,
                'start' => $p->start_time ? \Carbon\Carbon::parse($p->start_time)->format('H:i') : '—',
                'end' => $p->end_time ? \Carbon\Carbon::parse($p->end_time)->format('H:i') : '—',
                'total_time' => $p->total_time,
            ])->values(),
            'remarks' => $b->remarks ?? '—',
            'status' => $b->status,
        ];
    }
    public function userActivityReport(Request $request)
    {
        $allowedSorts = ['logged_at', 'user_id', 'action', 'name', 'username', 'department'];
        $sortBy = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'logged_at';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $perPage = min((int) ($request->per_page ?? 50), 500);

        $query = \DB::table('user_activity_logs as al')
            ->join('users as u', 'u.id', '=', 'al.user_id')
            ->select(
                'al.id',
                'al.user_id',
                'al.action',
                'al.ip_address',
                'al.session_id',
                'al.logged_at',
                'u.name',
                'u.username',
                'u.email',
                'u.role',
                'u.department',
                'u.is_active'
            );

        // ── Filters ──────────────────────────────────────────────────
        if ($request->filled('user_id'))
            $query->where('al.user_id', $request->user_id);

        if ($request->filled('action'))
            $query->where('al.action', $request->action);

        if ($request->filled('role'))
            $query->where('u.role', $request->role);

        if ($request->filled('department'))
            $query->where('u.department', $request->department);

        if ($request->filled('date_from'))
            $query->whereDate('al.logged_at', '>=', $request->date_from);

        if ($request->filled('date_to'))
            $query->whereDate('al.logged_at', '<=', $request->date_to);

        if ($request->filled('search'))
            $query->where(function ($q) use ($request) {
                $q->where('u.name', 'like', '%' . $request->search . '%')
                    ->orWhere('u.username', 'like', '%' . $request->search . '%')
                    ->orWhere('u.email', 'like', '%' . $request->search . '%');
            });

        // ── Sorting ───────────────────────────────────────────────────
        $nativeUserCols = ['name', 'username', 'department', 'role'];
        if (in_array($sortBy, $nativeUserCols)) {
            $query->orderBy('u.' . $sortBy, $sortDir);
        } elseif ($sortBy === 'logged_at' || $sortBy === 'action') {
            $query->orderBy('al.' . $sortBy, $sortDir);
        } else {
            $query->orderBy('al.logged_at', 'desc');
        }

        $paginated = $query->paginate($perPage);

        // ── Pair logins with next logout for session duration ─────────
        // Build paired sessions per user
        $rows = collect($paginated->items())->map(function ($r) {
            return [
                'id' => $r->id,
                'user_id' => $r->user_id,
                'name' => $r->name,
                'username' => $r->username,
                'email' => $r->email,
                'role' => $r->role,
                'department' => $r->department ?? '—',
                'is_active' => $r->is_active,
                'action' => $r->action,
                'ip_address' => $r->ip_address ?? '—',
                'logged_at' => $r->logged_at
                    ? \Carbon\Carbon::parse($r->logged_at)->format('d/m/Y H:i:s')
                    : '—',
                'logged_at_raw' => $r->logged_at,
                'session_id' => $r->session_id
                    ? substr($r->session_id, 0, 8) . '…'
                    : '—',
            ];
        });

        // ── Summary stats for the filtered period ────────────────────
        $statsQuery = \DB::table('user_activity_logs as al')
            ->join('users as u', 'u.id', '=', 'al.user_id');

        if ($request->filled('user_id'))
            $statsQuery->where('al.user_id', $request->user_id);
        if ($request->filled('role'))
            $statsQuery->where('u.role', $request->role);
        if ($request->filled('department'))
            $statsQuery->where('u.department', $request->department);
        if ($request->filled('date_from'))
            $statsQuery->whereDate('al.logged_at', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $statsQuery->whereDate('al.logged_at', '<=', $request->date_to);

        $totalLogins = (clone $statsQuery)->where('al.action', 'login')->count();
        $totalLogouts = (clone $statsQuery)->where('al.action', 'logout')->count();
        $uniqueUsers = (clone $statsQuery)->where('al.action', 'login')->distinct('al.user_id')->count('al.user_id');

        // ── Dropdown data for filters ─────────────────────────────────
        $users = \DB::table('users')->select('id', 'name', 'username')->where('is_active', 1)->orderBy('name')->get();
        $departments = \DB::table('users')->whereNotNull('department')->distinct()->pluck('department');

        return response()->json([
            'status' => 'ok',
            'data' => $rows,
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
            'summary' => [
                'total_logins' => $totalLogins,
                'total_logouts' => $totalLogouts,
                'unique_users' => $uniqueUsers,
            ],
            'filters' => [
                'users' => $users,
                'departments' => $departments,
            ],
        ]);
    }

}