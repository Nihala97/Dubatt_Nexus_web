@extends('admin.layouts.app')
@section('title', 'Refining Batches')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 8px;color:var(--border)">/</span>
    <strong>Refining Batches</strong>
@endsection

@push('styles')
    <style>
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

        .btn-sm {
            padding: 7px 13px;
            font-size: 12.5px;
        }

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

        .filter-bar {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
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

        .filter-toggle-icon {
            margin-left: auto;
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

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
        }

        .badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .badge-draft {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-draft::before {
            background: #6366f1;
        }

        .badge-submitted {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-submitted::before {
            background: #10b981;
        }

        .act-btn {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
        }

        .act-btn svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .act-btn.edit {
            background: #f0fdf4;
        }

        .act-btn.edit svg {
            stroke: var(--green);
        }

        .act-btn.edit:hover {
            background: #dcfce7;
        }

        .act-btn.del {
            background: #fff1f2;
        }

        .act-btn.del svg {
            stroke: #ef4444;
        }

        .act-btn.del:hover {
            background: #ffe4e6;
        }

        .empty-state {
            text-align: center;
            padding: 56px 20px;
            color: var(--text-muted);
        }

        .empty-state svg {
            display: block;
            margin: 0 auto 14px;
        }

        .pagination-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            font-size: 13px;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 8px;
        }

        .pg-btns {
            display: flex;
            gap: 6px;
        }

        .pg-btn {
            padding: 6px 12px;
            border-radius: 7px;
            border: 1.5px solid var(--border);
            background: var(--white);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            font-family: inherit;
            color: var(--text);
        }

        .pg-btn:hover {
            border-color: var(--green);
            color: var(--green);
        }

        .pg-btn.active {
            background: var(--green);
            color: #fff;
            border-color: var(--green);
        }

        .pg-btn:disabled {
            opacity: .4;
            cursor: default;
        }
    </style>
@endpush

@section('content')

    @php
        use App\Models\RefiningBatch;
        use Illuminate\Support\Facades\DB;

        $status = request('status');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $search = request('search');
        $perPage = 20;
        $page = (int) request('page', 1);
        $activeFilters = collect([$status, $dateFrom, $dateTo, $search])->filter()->count();

        $query = RefiningBatch::with('material')
            ->where('is_active', true);
        if ($status)
            $query->where('status', $status);
        if ($dateFrom)
            $query->whereDate('date', '>=', $dateFrom);
        if ($dateTo)
            $query->whereDate('date', '<=', $dateTo);
        if ($search)
            $query->where('batch_no', 'like', "%{$search}%");

        $total = $query->count();
        $batches = $query->orderByDesc('created_at')->paginate($perPage);

        $stats = [
            'total' => RefiningBatch::where('is_active', true)->count(),
            'draft' => RefiningBatch::where('is_active', true)->where('status', 0)->count(),
            'submitted' => RefiningBatch::where('is_active', true)->where('status', '>=', 1)->count(),
            'this_month' => RefiningBatch::where('is_active', true)->whereMonth('date', now()->month)->count(),
        ];
    @endphp

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-icon">
            <svg viewBox="0 0 24 24">
                <path d="M2 20h20M6 20V8l6-6 6 6v12M10 20v-5h4v5" />
            </svg>
        </div>
        <div class="page-header-text">
            <h1>Refining Batches</h1>
            <p>Refining log sheet — finished goods &amp; dross tracking</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.mes.refining.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                New Batch
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-card-icon green"><svg viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                </svg></div>
            <div>
                <div class="stat-val">{{ $stats['total'] }}</div>
                <div class="stat-lbl">Total Batches</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon indigo"><svg viewBox="0 0 24 24">
                    <path d="M12 2l2 7h7l-6 4 2 7-5-3-5 3 2-7-6-4h7z" />
                </svg></div>
            <div>
                <div class="stat-val">{{ $stats['draft'] }}</div>
                <div class="stat-lbl">Draft</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon emerald"><svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12" />
                </svg></div>
            <div>
                <div class="stat-val">{{ $stats['submitted'] }}</div>
                <div class="stat-lbl">Submitted</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon amber"><svg viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg></div>
            <div>
                <div class="stat-val">{{ $stats['this_month'] }}</div>
                <div class="stat-lbl">This Month</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div class="filter-bar-header" onclick="toggleFilters()">
            <svg viewBox="0 0 24 24">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            <span>Filters</span>
            @if($activeFilters > 0) <span class="filter-count">{{ $activeFilters }}</span> @endif
            <svg class="filter-toggle-icon {{ $activeFilters ? 'open' : '' }}" id="filterChevron" viewBox="0 0 24 24">
                <polyline points="6 9 12 15 18 9" />
            </svg>
        </div>
        <div class="filter-body {{ $activeFilters ? 'open' : '' }}" id="filterBody">
            <form method="GET" action="{{ route('admin.mes.refining.index') }}">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All</option>
                            <option value="0" {{ $status == '0' ? 'selected' : '' }}>Draft</option>
                            <option value="1" {{ $status == '1' ? 'selected' : '' }}>Submitted</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="filter-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="filter-group">
                        <label>Search Batch No</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="RFN-2026-…">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                    <a href="{{ route('admin.mes.refining.index') }}" class="btn btn-outline btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab Bar --}}
    <div class="tab-bar">
        <a href="{{ route('admin.mes.refining.index') }}" class="tab {{ !$status ? 'active' : '' }}">
            All <span class="tab-count">{{ $stats['total'] }}</span>
        </a>
        <a href="{{ route('admin.mes.refining.index', ['status' => '0']) }}"
            class="tab {{ $status == '0' ? 'active' : '' }}">
            Draft <span class="tab-count">{{ $stats['draft'] }}</span>
        </a>
        <a href="{{ route('admin.mes.refining.index', ['status' => '1']) }}"
            class="tab {{ $status == '1' ? 'active' : '' }}">
            Submitted <span class="tab-count">{{ $stats['submitted'] }}</span>
        </a>
    </div>

    {{-- Search Row --}}
    <div class="search-row">
        <div class="search-wrap">
            <svg viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" id="liveSearch" placeholder="Quick search batch no…" value="{{ $search }}"
                onkeyup="liveSearchFn(this.value)">
        </div>
        <div class="result-count" id="resultCount">{{ $batches->total() }} records</div>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="data-table" id="mainTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Batch No</th>
                    <th>Pot No</th>
                    <th>Material</th>
                    <th>Date</th>
                    <th>LPG Consump.</th>
                    <th>Elec. Consump.</th>
                    <th>Status</th>
                    <th style="text-align:center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $b)
                    <tr data-search="{{ strtolower($b->batch_no) }}">
                        <td style="color:var(--text-muted);font-size:12px">{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{ route('admin.mes.refining.edit', $b->id) }}"
                                style="font-weight:700;color:var(--green);text-decoration:none">
                                {{ $b->batch_no }}
                            </a>
                        </td>
                        <td>{{ $b->pot_no ?? '—' }}</td>
                        <td>{{ $b->material?->name ?? '—' }}</td>
                        <td>{{ $b->date?->format('d M Y') ?? '—' }}</td>
                        <td>{{ $b->lpg_consumption ? number_format($b->lpg_consumption, 3) . ' m³' : '—' }}</td>
                        <td>{{ $b->electricity_consumption ? number_format($b->electricity_consumption, 3) . ' kWh' : '—' }}
                        </td>
                        <td>
                            @php
                                $statusStr = $b->status >= 1 ? 'submitted' : 'draft';
                            @endphp
                            <span class="badge badge-{{ $statusStr }}">{{ ucfirst($statusStr) }}</span>
                        </td>
                        <td style="text-align:center">
                            <div style="display:inline-flex;gap:6px">
                                <a href="{{ route('admin.mes.refining.edit', $b->id) }}" class="act-btn edit" title="Edit">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </a>
                                @if($b->status == 0)
                                    <button class="act-btn del" title="Delete"
                                        onclick="deleteBatch({{ $b->id }}, '{{ $b->batch_no }}')">
                                        <svg viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14H6L5 6" />
                                            <path d="M10 11v6M14 11v6" />
                                            <path d="M9 6V4h6v2" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#c8dfd1" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 20h20M6 20V8l6-6 6 6v12" />
                                </svg>
                                <p style="font-weight:600;color:var(--text)">No refining batches found</p>
                                <p style="font-size:12.5px;margin-top:4px">Adjust filters or create a new batch</p>
                                <a href="{{ route('admin.mes.refining.create') }}" class="btn btn-primary btn-sm"
                                    style="margin-top:14px;display:inline-flex">
                                    <svg viewBox="0 0 24 24">
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    New Batch
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($batches->hasPages())
            <div class="pagination-row">
                <span>Showing {{ $batches->firstItem() }}–{{ $batches->lastItem() }} of {{ $batches->total() }}</span>
                <div class="pg-btns">
                    @if($batches->onFirstPage())
                        <button class="pg-btn" disabled>← Prev</button>
                    @else
                        <a href="{{ $batches->previousPageUrl() }}" class="pg-btn">← Prev</a>
                    @endif

                    @foreach($batches->getUrlRange(max(1, $batches->currentPage() - 2), min($batches->lastPage(), $batches->currentPage() + 2)) as $pg => $url)
                        <a href="{{ $url }}" class="pg-btn {{ $pg == $batches->currentPage() ? 'active' : '' }}">{{ $pg }}</a>
                    @endforeach

                    @if($batches->hasMorePages())
                        <a href="{{ $batches->nextPageUrl() }}" class="pg-btn">Next →</a>
                    @else
                        <button class="pg-btn" disabled>Next →</button>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        function toggleFilters() {
            document.getElementById('filterBody').classList.toggle('open');
            document.getElementById('filterChevron').classList.toggle('open');
        }
        function liveSearchFn(val) {
            const q = val.toLowerCase();
            let count = 0;
            document.querySelectorAll('#mainTable tbody tr[data-search]').forEach(tr => {
                const match = tr.dataset.search.includes(q);
                tr.style.display = match ? '' : 'none';
                if (match) count++;
            });
            document.getElementById('resultCount').textContent = count + ' records';
        }
        async function deleteBatch(id, batchNo) {
            if (!confirm(`Delete batch ${batchNo}? This cannot be undone.`)) return;
            const res = await apiFetch(`/refining/${id}`, { method: 'DELETE' });
            if (res?.ok) window.location.reload();
            else alert('Delete failed.');
        }
    </script>
@endpush