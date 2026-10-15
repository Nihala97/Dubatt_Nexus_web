@extends('admin.layouts.app')

@section('title', 'Acid Testing')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong>Acid Testing</strong>
@endsection

@push('styles')
    <style>
        /* ── Page header ── */
        .page-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
        }

        .page-header-icon {
            width: 52px;
            height: 52px;
            background: var(--green-light);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .page-header-icon svg {
            width: 26px;
            height: 26px;
            stroke: var(--green);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .page-header-text h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 2px;
        }

        .page-header-text p {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        .page-header-actions {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .15s;
            font-family: inherit;
        }

        .btn svg {
            width: 15px;
            height: 15px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .btn-outline {
            background: var(--white);
            color: var(--text);
            border: 1.5px solid var(--border);
        }

        .btn-outline:hover {
            border-color: var(--green);
            color: var(--green);
        }

        .btn-primary {
            background: var(--green);
            color: #fff;
        }

        .btn-primary:hover {
            background: #15803d;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(26, 122, 58, .28);
        }

        /* ── Stat cards ── */
        .stat-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: var(--shadow-sm);
        }

        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-card-icon svg {
            width: 19px;
            height: 19px;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .stat-card-icon.green {
            background: var(--green-light);
        }

        .stat-card-icon.green svg {
            stroke: var(--green);
        }

        .stat-card-icon.indigo {
            background: #ede9fe;
        }

        .stat-card-icon.indigo svg {
            stroke: #7c3aed;
        }

        .stat-card-icon.emerald {
            background: #d1fae5;
        }

        .stat-card-icon.emerald svg {
            stroke: #059669;
        }

        .stat-card-icon.amber {
            background: #fef3c7;
        }

        .stat-card-icon.amber svg {
            stroke: #d97706;
        }

        .stat-val {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .stat-lbl {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* ── Filter bar ── */
        .filter-bar {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            overflow: visible;
            /* lets the dropdown panels show outside the bar */
        }

        .filter-bar-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            cursor: pointer;
            user-select: none;
        }

        .filter-bar-header svg {
            width: 16px;
            height: 16px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .filter-bar-header span {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .filter-count {
            background: var(--green);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .filter-bar-header .filter-count {
            color: #fff;
        }

        .filter-toggle-icon {
            margin-left: auto;
            width: 16px;
            height: 16px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform .2s;
        }

        .filter-toggle-icon.open {
            transform: rotate(180deg);
        }

        .filter-body {
            display: none;
            padding: 0 18px 18px;
            border-top: 1px solid var(--border);
        }

        .filter-body.open {
            display: block;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .filter-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 5px;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 8px 11px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 13px;
            color: var(--text);
            background: var(--bg);
            outline: none;
            transition: border .15s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: var(--green);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }

        /* ── Multi-select checkbox dropdowns ── */
        .msel {
            position: relative;
        }

        .msel-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            padding: 8px 11px;
            min-height: 36px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: var(--bg);
            font-family: inherit;
            font-size: 13px;
            color: var(--text);
            cursor: pointer;
            text-align: left;
            box-sizing: border-box;
        }

        .msel.open .msel-trigger,
        .msel-trigger:hover {
            border-color: var(--green);
            background: var(--white);
        }

        .msel-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 600;
        }

        .msel-text.placeholder {
            color: var(--text-muted);
            font-weight: 400;
        }

        .msel-trigger svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform .18s;
        }

        .msel.open .msel-trigger svg {
            transform: rotate(180deg);
            stroke: var(--green);
        }

        .msel-panel {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            min-width: 100%;
            width: 280px;
            max-width: 90vw;
            background: #fff;
            border: 1.5px solid var(--green);
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .16);
            z-index: 60;
            overflow: hidden;
        }

        .msel.open .msel-panel {
            display: block;
        }

        .filter-group .msel-search {
            width: calc(100% - 20px);
            margin: 8px 10px;
            padding: 7px 10px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            background: var(--bg);
            font-size: 12.5px;
        }

        .msel-list {
            max-height: 230px;
            overflow-y: auto;
            padding: 4px 0;
        }

        .filter-group label.msel-item {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0;
            text-transform: none;
            color: var(--text);
            cursor: pointer;
        }

        .filter-group label.msel-item:hover {
            background: #f0f9f4;
        }

        .filter-group .msel-item input[type=checkbox] {
            width: 15px;
            height: 15px;
            padding: 0;
            margin: 0;
            accent-color: var(--green);
            flex-shrink: 0;
            cursor: pointer;
        }

        .msel-foot {
            border-top: 1px solid var(--border);
            padding: 6px 10px;
            text-align: right;
            background: var(--bg);
        }

        .msel-foot button {
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .msel-foot button:hover {
            color: #ef4444;
        }

        /* ── Tab bar ── */
        .tab-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid transparent;
            text-decoration: none;
            color: var(--text-muted);
            transition: all .15s;
        }

        .tab.active {
            background: var(--green);
            color: #fff;
            border-color: var(--green);
        }

        .tab:not(.active):hover {
            border-color: var(--border);
            color: var(--text);
        }

        .tab-count {
            font-size: 11px;
            background: rgba(255, 255, 255, .25);
            padding: 1px 6px;
            border-radius: 20px;
        }

        .tab:not(.active) .tab-count {
            background: var(--border);
            color: var(--text-muted);
        }

        /* ── Search row ── */
        .search-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            max-width: 380px;
        }

        .search-wrap svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .search-wrap input {
            width: 100%;
            padding: 8px 11px 8px 33px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 13px;
            color: var(--text);
            background: var(--white);
            outline: none;
            transition: border .15s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .search-wrap input:focus {
            border-color: var(--green);
        }

        .result-count {
            font-size: 13px;
            color: var(--text-muted);
            margin-left: auto;
        }

        /* ── Table ── */
        .table-wrap {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead th {
            padding: 11px 16px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .6px;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            text-align: left;
        }

        .data-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }

        .data-table tbody tr:last-child {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background: #f8fdf9;
        }

        .data-table tbody td {
            padding: 13px 16px;
            font-size: 13px;
            color: var(--text);
            vertical-align: middle;
        }

        .lot-no {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .lot-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ── Status badges ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-badge.draft {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-badge.submitted {
            background: #d1fae5;
            color: #065f46;
        }

        /* ── Action buttons ── */
        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: 1.5px solid var(--border);
            background: var(--white);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all .15s;
            text-decoration: none;
        }

        .action-btn:hover {
            border-color: var(--green);
            color: var(--green);
        }

        .action-btn.danger:hover {
            border-color: #ef4444;
            color: #ef4444;
        }

        .action-btn.print-btn:hover {
            border-color: #7c3aed;
            color: #7c3aed;
        }

        .action-btn.download-btn:hover {
            border-color: #0284c7;
            color: #0284c7;
        }

        .action-btn svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .actions-cell {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* ── Attachment count badge (display only) ── */
        .attach-count {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            cursor: default;
        }

        .attach-count svg {
            width: 12px;
            height: 12px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }

        .attach-count.has {
            background: #d1fae5;
            color: #065f46;
        }

        .attach-count.none {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            stroke: var(--border);
            fill: none;
            stroke-width: 1.5;
            display: block;
            margin: 0 auto 12px;
        }

        .empty-state h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0 0 18px;
        }

        /* ── Pagination footer ── */
        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 8px;
        }

        .pagination {
            display: flex;
            gap: 4px;
        }

        .page-btn {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: 1.5px solid var(--border);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            color: var(--text);
            text-decoration: none;
            transition: all .15s;
        }

        .page-btn:hover,
        .page-btn.active {
            background: var(--green);
            border-color: var(--green);
            color: #fff;
        }

        .page-btn.disabled {
            opacity: .4;
            pointer-events: none;
        }

        .page-btn svg {
            width: 13px;
            height: 13px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        @media(max-width:900px) {
            .stat-row {
                grid-template-columns: 1fr 1fr;
            }

            .page-header {
                flex-wrap: wrap;
            }

            .page-header-actions {
                width: 100%;
            }
        }

        @media(max-width:560px) {
            .stat-row {
                grid-template-columns: 1fr;
            }

            .search-row {
                flex-direction: column;
                align-items: stretch;
            }

            .search-wrap {
                max-width: 100%;
            }

            .result-count {
                margin-left: 0;
            }
        }
    </style>
@endpush

@section('content')

    @php
        // Supplier list for the filter dropdown
        $suppliers = \App\Models\Supplier::orderBy('supplier_name')->get(['id', 'supplier_name']);

        // Multi-select filters (also accept old single values like ?status=1)
        $statusFilter = array_values(array_filter((array) request('status', []), fn($v) => $v !== '' && $v !== null));
        $supplierFilter = array_values(array_filter((array) request('supplier_id', []), fn($v) => $v !== '' && $v !== null));

        $q = \App\Models\AcidTesting::with(['supplier', 'createdBy', 'details' => fn($q) => $q->where('is_active', 1), 'attachments'])
            ->where('is_active', true);

        if (count($statusFilter)) {
            $q->where(function ($sq) use ($statusFilter) {
                if (in_array('0', $statusFilter, true)) {
                    $sq->orWhere('status', 0);
                }
                if (in_array('1', $statusFilter, true)) {
                    $sq->orWhere('status', '>=', 1);
                }
            });
        }
        if (count($supplierFilter)) {
            $q->whereIn('supplier_id', $supplierFilter);
        }
        if (request('date_from'))
            $q->whereDate('test_date', '>=', request('date_from'));
        if (request('date_to'))
            $q->whereDate('test_date', '<=', request('date_to'));
        if (request('search'))
            $q->where(function ($sq) {
                $sq->where('lot_number', 'like', '%' . request('search') . '%')
                    ->orWhere('vehicle_number', 'like', '%' . request('search') . '%')
                    ->orWhereHas('supplier', fn($s) => $s->where('supplier_name', 'like', '%' . request('search') . '%'));
            });

        $list_items = $q->orderByDesc('test_date')->orderByDesc('created_at')->paginate(20)->withQueryString();

        $atBase = \App\Models\AcidTesting::where('is_active', true);
        $totalAll = (clone $atBase)->count();
        $draftCnt = (clone $atBase)->where('status', 0)->count();
        $submittedCnt = (clone $atBase)->where('status', '>=', 1)->count();
        $thisMonthCnt = (clone $atBase)->whereMonth('test_date', now()->month)->whereYear('test_date', now()->year)->count();

        $activeFilters = (count($statusFilter) ? 1 : 0)
            + (count($supplierFilter) ? 1 : 0)
            + (request('date_from') ? 1 : 0)
            + (request('date_to') ? 1 : 0)
            + (request('search') ? 1 : 0);

        $activeTab = count($statusFilter) === 1 ? (string) $statusFilter[0] : 'all';
        $keep = request()->except(['status', 'page']);
    @endphp

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div class="page-header-icon">
            <svg viewBox="0 0 24 24">
                <path
                    d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2v-4M9 21H5a2 2 0 0 1-2-2v-4m0 0h18" />
            </svg>
        </div>
        <div class="page-header-text">
            <h1>Acid Testing</h1>
            <p>Manage acid testing records, drafts and submissions</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                <svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                Back to Dashboard
            </a>
            <a href="{{ route('admin.mes.acidTesting.create') }}" class="btn btn-primary"
                data-permission="acid_testing,can_create">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                New Record
            </a>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-card-icon green">
                <svg viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
            </div>
            <div>
                <div class="stat-val">{{ $totalAll }}</div>
                <div class="stat-lbl">Total Records</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon indigo">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
            </div>
            <div>
                <div class="stat-val">{{ $draftCnt }}</div>
                <div class="stat-lbl">Pending Drafts</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon emerald">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </div>
            <div>
                <div class="stat-val">{{ $submittedCnt }}</div>
                <div class="stat-lbl">Submitted</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon amber">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
            </div>
            <div>
                <div class="stat-val">{{ $thisMonthCnt }}</div>
                <div class="stat-lbl">This Month</div>
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('admin.mes.acidTesting.index') }}" id="filterForm">

        <div class="filter-bar">
            <div class="filter-bar-header" onclick="toggleFilter()">
                <svg viewBox="0 0 24 24">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                </svg>
                <span>Filter Records</span>
                @if($activeFilters)
                    <span class="filter-count">{{ $activeFilters }} active</span>
                @else
                    <span style="font-size:12px;color:var(--text-muted)">0 filters</span>
                @endif
                <svg class="filter-toggle-icon {{ $activeFilters ? 'open' : '' }}" id="filterChevron" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </div>
            <div class="filter-body {{ $activeFilters ? 'open' : '' }}" id="filterBody">
                <div class="filter-grid">

                    {{-- STATUS (multi-select) --}}
                    <div class="filter-group">
                        <label>Status</label>
                        <div class="msel" id="msel_status" data-placeholder="All Status">
                            <button type="button" class="msel-trigger" onclick="mselToggle('msel_status')">
                                <span class="msel-text placeholder">All Status</span>
                                <svg viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </button>
                            <div class="msel-panel">
                                <div class="msel-list">
                                    <label class="msel-item">
                                        <input type="checkbox" name="status[]" value="0" data-label="Draft" {{ in_array('0', $statusFilter, true) ? 'checked' : '' }} onchange="mselUpdate('msel_status')">
                                        <span>Draft</span>
                                    </label>
                                    <label class="msel-item">
                                        <input type="checkbox" name="status[]" value="1" data-label="Submitted" {{ in_array('1', $statusFilter, true) ? 'checked' : '' }}
                                            onchange="mselUpdate('msel_status')">
                                        <span>Submitted</span>
                                    </label>
                                </div>
                                <div class="msel-foot">
                                    <button type="button" onclick="mselClear('msel_status')">Clear selection</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SUPPLIER (multi-select with search) --}}
                    <div class="filter-group">
                        <label>Supplier</label>
                        <div class="msel" id="msel_supplier" data-placeholder="All Suppliers">
                            <button type="button" class="msel-trigger" onclick="mselToggle('msel_supplier')">
                                <span class="msel-text placeholder">All Suppliers</span>
                                <svg viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </button>
                            <div class="msel-panel">
                                <input type="text" class="msel-search" placeholder="Search suppliers…" autocomplete="off"
                                    oninput="mselFilter(this)">
                                <div class="msel-list">
                                    @foreach($suppliers as $s)
                                        <label class="msel-item">
                                            <input type="checkbox" name="supplier_id[]" value="{{ $s->id }}"
                                                data-label="{{ $s->supplier_name }}" {{ in_array((string) $s->id, $supplierFilter, true) ? 'checked' : '' }}
                                                onchange="mselUpdate('msel_supplier')">
                                            <span>{{ $s->supplier_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="msel-foot">
                                    <button type="button" onclick="mselClear('msel_supplier')">Clear selection</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="filter-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.mes.acidTesting.index') }}" class="btn btn-outline">Clear</a>
                </div>
            </div>
        </div>

        {{-- ── Tabs (keep all other filters) ── --}}
        <div class="tab-bar">
            <a href="{{ route('admin.mes.acidTesting.index', $keep) }}"
                class="tab {{ $activeTab === 'all' ? 'active' : '' }}">
                All <span class="tab-count">{{ $totalAll }}</span>
            </a>
            <a href="{{ route('admin.mes.acidTesting.index', array_merge($keep, ['status' => ['0']])) }}"
                class="tab {{ $activeTab === '0' ? 'active' : '' }}">
                Draft <span class="tab-count">{{ $draftCnt }}</span>
            </a>
            <a href="{{ route('admin.mes.acidTesting.index', array_merge($keep, ['status' => ['1']])) }}"
                class="tab {{ $activeTab === '1' ? 'active' : '' }}">
                Submitted <span class="tab-count">{{ $submittedCnt }}</span>
            </a>
        </div>

        {{-- ── Search row ── --}}
        <div class="search-row">
            <div class="search-wrap">
                <svg viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search lot no, vehicle, supplier…" oninput="debounceSearch(this)">
            </div>
            <div class="result-count">{{ $list_items->total() }} result(s)</div>
        </div>
    </form>

    {{-- ── Data Table ── --}}
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Test Date</th>
                    <th>Lot No</th>
                    <th>Vehicle No</th>
                    <th>Supplier</th>
                    <th>In-House Wt (KG)</th>
                    <th>Avg P&amp;F Wt (KG)</th>
                    <th>Pallets</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($list_items as $test)
                    @php
                        $isSubmitted = (int) $test->status >= 1;
                        $statusLabel = $isSubmitted ? 'submitted' : 'draft';
                        $palletCount = $test->details->count();

                        $attCount = $test->attachments ? $test->attachments->count() : 0;
                        // legacy fallback: old records that only have attachment_path on the header
                        if ($attCount === 0 && !empty($test->attachment_path)) {
                            $attCount = 1;
                        }
                    @endphp
                    <tr id="row-{{ $test->id }}">
                        <td>
                            <div class="lot-no">
                                {{ $test->test_date ? \Carbon\Carbon::parse($test->test_date)->format('d M Y') : '—' }}
                            </div>
                            <div class="lot-sub">#{{ $test->id }}</div>
                        </td>
                        <td>
                            <strong style="color:var(--text)">{{ $test->lot_number ?? '—' }}</strong>
                        </td>
                        <td>{{ $test->vehicle_number ?? '—' }}</td>
                        <td>
                            <div style="font-weight:600;color:var(--text)">{{ $test->supplier->supplier_name ?? '—' }}</div>
                        </td>
                        <td>
                            @if($test->received_qty)
                                <strong>{{ number_format($test->received_qty, 2) }}</strong>
                                <span style="font-size:11px;color:var(--text-muted)">KG</span>
                            @else —
                            @endif
                        </td>
                        <td>
                            @if($test->avg_pallet_and_foreign_weight)
                                {{ number_format($test->avg_pallet_and_foreign_weight, 3) }}
                                <span style="font-size:11px;color:var(--text-muted)">KG</span>
                            @else —
                            @endif
                        </td>
                        <td>
                            @if($palletCount > 0)
                                <span
                                    style="background:#ede9fe;color:#5b21b6;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700;">
                                    {{ $palletCount }} pallet{{ $palletCount > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span style="color:var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $statusLabel }}">
                                {{ $isSubmitted ? 'Submitted' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ optional($test->createdBy)->name ?? $test->created_by ?? '—' }}</td>
                        <td>
                            <div class="actions-cell">

                                {{-- VIEW (always shown) --}}
                                <a href="{{ route('admin.mes.acidTesting.edit', $test->id) }}" class="action-btn"
                                    title="View Details">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </a>

                                {{-- EDIT (draft only) --}}
                                @if(!$isSubmitted)
                                    <a href="{{ route('admin.mes.acidTesting.edit', $test->id) }}" class="action-btn"
                                        title="Edit Record" data-permission="acid_testing,can_edit">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>
                                @endif

                                {{-- PRINT + DOWNLOAD (submitted only) --}}
                                @if($isSubmitted)
                                    <a href="{{ route('admin.mes.acidTesting.print', $test->id) }}" class="action-btn print-btn"
                                        title="Print Report" target="_blank">
                                        <svg viewBox="0 0 24 24">
                                            <polyline points="6 9 6 2 18 2 18 9" />
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                            <rect x="6" y="14" width="12" height="8" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.mes.acidTesting.download', $test->id) }}"
                                        class="action-btn download-btn" title="Download Report + Attachments">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="7 10 12 15 17 10" />
                                            <line x1="12" y1="15" x2="12" y2="3" />
                                        </svg>
                                    </a>
                                @endif

                                {{-- ATTACHMENT COUNT (display only) --}}
                                <span class="attach-count {{ $attCount > 0 ? 'has' : 'none' }}"
                                    title="{{ $attCount }} attachment{{ $attCount === 1 ? '' : 's' }}">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                    </svg>
                                    {{ $attCount }} {{ $attCount === 1 ? 'attachment' : 'attachments' }}
                                </span>

                                {{-- DELETE (draft only) --}}
                                @if(!$isSubmitted)
                                    <button id="del-{{ $test->id }}" class="action-btn danger"
                                        data-permission="acid_testing,can_delete"
                                        onclick="deleteBatch({{ $test->id }}, '{{ $test->lot_number }}', '/acid-testings/{{ $test->id }}')"
                                        title="Delete">
                                        <svg viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            <path d="M10 11v6M14 11v6" />
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                        </svg>
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2v-4M9 21H5a2 2 0 0 1-2-2v-4m0 0h18" />
                                </svg>
                                <h3>No acid test records found</h3>
                                <p>{{ request('search') || $activeFilters ? 'Try adjusting your filters.' : 'Create your first acid test record to get started.' }}
                                </p>
                                @if(!request('search') && !$activeFilters)
                                    <a href="{{ route('admin.mes.acidTesting.create') }}" class="btn btn-primary"
                                        data-permission="acid_testing,can_create">
                                        <svg viewBox="0 0 24 24">
                                            <line x1="12" y1="5" x2="12" y2="19" />
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                        </svg>
                                        New Record
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($list_items->total() > 0)
            <div class="table-footer">
                <span>Showing {{ $list_items->firstItem() }}–{{ $list_items->lastItem() }} of {{ $list_items->total() }}
                    results</span>
                @if($list_items->hasPages())
                    <div class="pagination">
                        @if($list_items->onFirstPage())
                            <span class="page-btn disabled"><svg viewBox="0 0 24 24">
                                    <polyline points="15 18 9 12 15 6" />
                                </svg></span>
                        @else
                            <a href="{{ $list_items->previousPageUrl() }}" class="page-btn"><svg viewBox="0 0 24 24">
                                    <polyline points="15 18 9 12 15 6" />
                                </svg></a>
                        @endif
                        @foreach($list_items->getUrlRange(1, $list_items->lastPage()) as $page => $url)
                            <a href="{{ $url }}"
                                class="page-btn {{ $page == $list_items->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($list_items->hasMorePages())
                            <a href="{{ $list_items->nextPageUrl() }}" class="page-btn"><svg viewBox="0 0 24 24">
                                    <polyline points="9 18 15 12 9 6" />
                                </svg></a>
                        @else
                            <span class="page-btn disabled"><svg viewBox="0 0 24 24">
                                    <polyline points="9 18 15 12 9 6" />
                                </svg></span>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div id="flashMsg"
            style="position:fixed;bottom:24px;right:24px;background:#166534;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:9999;display:flex;align-items:center;gap:8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" style="width:16px;height:16px">
                <polyline points="20 6 9 17 4 12" />
            </svg>
            {{ session('success') }}
        </div>
        <script>setTimeout(() => { const el = document.getElementById('flashMsg'); if (el) el.remove(); }, 3500);</script>
    @endif
    @if(session('error'))
        <div id="flashMsg"
            style="position:fixed;bottom:24px;right:24px;background:#991b1b;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:9999;display:flex;align-items:center;gap:8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" style="width:16px;height:16px">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            {{ session('error') }}
        </div>
        <script>setTimeout(() => { const el = document.getElementById('flashMsg'); if (el) el.remove(); }, 3500);</script>
    @endif

@endsection

@push('scripts')
    <script>
        function toggleFilter() {
            document.getElementById('filterBody').classList.toggle('open');
            document.getElementById('filterChevron').classList.toggle('open');
        }

        let searchTimer;
        function debounceSearch(input) {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
        }

        /* ── Inject keyframe styles once (used by toasts) ── */
        if (!document.getElementById('_utilStyles')) {
            const s = document.createElement('style');
            s.id = '_utilStyles';
            s.textContent = `
                    @keyframes _toastIn  { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
                    @keyframes _toastOut { from{opacity:1;transform:translateY(0)}   to{opacity:0;transform:translateY(10px)} }
                    @keyframes _fadeIn   { from{opacity:0} to{opacity:1} }
                    @keyframes _fadeOut  { from{opacity:1} to{opacity:0} }
                    @keyframes _slideUp  { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
                `;
            document.head.appendChild(s);
        }

        /* ── Multi-select checkbox dropdowns ── */
        function mselToggle(id) {
            document.querySelectorAll('.msel.open').forEach(el => { if (el.id !== id) el.classList.remove('open'); });
            document.getElementById(id).classList.toggle('open');
        }

        function mselUpdate(id) {
            const wrap = document.getElementById(id);
            const text = wrap.querySelector('.msel-text');
            const checked = [...wrap.querySelectorAll('input[type=checkbox]:checked')];
            if (!checked.length) {
                text.textContent = wrap.dataset.placeholder;
                text.classList.add('placeholder');
            } else {
                text.classList.remove('placeholder');
                text.textContent = checked.length <= 2
                    ? checked.map(c => c.dataset.label).join(', ')
                    : checked.length + ' selected';
            }
        }

        function mselClear(id) {
            document.getElementById(id).querySelectorAll('input[type=checkbox]').forEach(c => c.checked = false);
            mselUpdate(id);
        }

        function mselFilter(input) {
            const q = input.value.toLowerCase().trim();
            input.closest('.msel').querySelectorAll('.msel-item').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        document.addEventListener('click', e => {
            if (!e.target.closest('.msel')) {
                document.querySelectorAll('.msel.open').forEach(el => el.classList.remove('open'));
            }
        });

        // show the current selection text on page load
        document.querySelectorAll('.msel').forEach(el => mselUpdate(el.id));

        function showToast(msg, bg) {
            const t = document.createElement('div');
            t.style.cssText = `position:fixed;bottom:24px;right:24px;background:${bg};color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:99999;display:flex;align-items:center;gap:8px;animation:_toastIn .2s ease;`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => { t.style.animation = '_toastOut .2s ease'; setTimeout(() => t.remove(), 200); }, 3000);
        }
    </script>
@endpush