@extends('admin.layouts.app')

@section('title', 'Materials')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong>Materials</strong>
@endsection

@push('styles')
<style>
    .page-header { display:flex; align-items:center; gap:16px; margin-bottom:28px; }
    .page-header-icon { width:52px; height:52px; background:var(--green-light); border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .page-header-icon svg { width:26px; height:26px; stroke:var(--green); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .page-header-text h1 { font-size:22px; font-weight:700; color:var(--text); margin:0 0 2px; }
    .page-header-text p  { font-size:13px; color:var(--text-muted); margin:0; }
    .page-header-actions { margin-left:auto; display:flex; gap:10px; }
    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:all .15s; font-family:inherit; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .btn-outline { background:var(--white); color:var(--text); border:1.5px solid var(--border); }
    .btn-outline:hover { border-color:var(--green); color:var(--green); }
    .btn-primary { background:var(--green); color:#fff; }
    .btn-primary:hover { background:#15803d; transform:translateY(-1px); box-shadow:0 4px 14px rgba(26,122,58,.28); }
    .stat-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:24px; }
    .stat-card { background:var(--white); border:1px solid var(--border); border-radius:12px; padding:18px 20px; display:flex; align-items:center; gap:14px; box-shadow:var(--shadow-sm); }
    .stat-card-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-card-icon svg { width:19px; height:19px; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .stat-card-icon.green   { background:var(--green-light); } .stat-card-icon.green   svg { stroke:var(--green); }
    .stat-card-icon.indigo  { background:#ede9fe; }            .stat-card-icon.indigo  svg { stroke:#7c3aed; }
    .stat-card-icon.emerald { background:#d1fae5; }            .stat-card-icon.emerald svg { stroke:#059669; }
    .stat-card-icon.amber   { background:#fef3c7; }            .stat-card-icon.amber   svg { stroke:#d97706; }
    .stat-card-icon.sky     { background:#e0f2fe; }            .stat-card-icon.sky     svg { stroke:#0369a1; }
    .stat-val { font-size:24px; font-weight:700; color:var(--text); line-height:1; }
    .stat-lbl { font-size:12px; color:var(--text-muted); margin-top:3px; }
    .filter-bar { background:var(--white); border:1px solid var(--border); border-radius:12px; margin-bottom:20px; box-shadow:var(--shadow-sm); overflow:hidden; }
    .filter-bar-header { display:flex; align-items:center; gap:10px; padding:14px 18px; cursor:pointer; user-select:none; }
    .filter-bar-header svg { width:16px; height:16px; stroke:var(--text-muted); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .filter-bar-header span { font-size:13px; font-weight:600; color:var(--text); }
    .filter-count { background:var(--green); color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; }
    .filter-toggle-icon { margin-left:auto; width:16px; height:16px; stroke:var(--text-muted); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; transition:transform .2s; }
    .filter-toggle-icon.open { transform:rotate(180deg); }
    .filter-body { display:none; padding:0 18px 18px; border-top:1px solid var(--border); }
    .filter-body.open { display:block; }
    .filter-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:12px; margin-top:14px; }
    .filter-group label { display:block; font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
    .filter-group input, .filter-group select { width:100%; padding:8px 11px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; color:var(--text); background:var(--bg); outline:none; transition:border .15s; box-sizing:border-box; font-family:inherit; }
    .filter-group input:focus, .filter-group select:focus { border-color:var(--green); }
    .filter-actions { display:flex; gap:10px; margin-top:14px; }
    .search-row { display:flex; align-items:center; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
    .search-wrap { position:relative; flex:1; max-width:380px; }
    .search-wrap svg { position:absolute; left:10px; top:50%; transform:translateY(-50%); width:15px; height:15px; stroke:var(--text-muted); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .search-wrap input { width:100%; padding:8px 11px 8px 33px; border:1.5px solid var(--border); border-radius:9px; font-size:13px; color:var(--text); background:var(--white); outline:none; transition:border .15s; box-sizing:border-box; font-family:inherit; }
    .search-wrap input:focus { border-color:var(--green); }
    .result-count { font-size:13px; color:var(--text-muted); margin-left:auto; }
    .table-wrap { background:var(--white); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden; }
    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead th { padding:11px 16px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.6px; background:var(--bg); border-bottom:1px solid var(--border); white-space:nowrap; text-align:left; }
    .data-table tbody tr { border-bottom:1px solid var(--border); transition:background .12s; }
    .data-table tbody tr:last-child { border-bottom:none; }
    .data-table tbody tr:hover { background:#f8fdf9; }
    .data-table tbody td { padding:13px 16px; font-size:13px; color:var(--text); vertical-align:middle; }
    .item-name  { font-size:13px; font-weight:700; color:var(--text); }
    .item-sub   { font-size:11px; color:var(--text-muted); margin-top:2px; }
    .status-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .status-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .status-badge.active   { background:#d1fae5; color:#065f46; }
    .status-badge.inactive { background:#fee2e2; color:#991b1b; }
    .cat-badge { display:inline-flex; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; background:#ede9fe; color:#5b21b6; }
    .unit-badge { display:inline-flex; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; background:#fef3c7; color:#92400e; }
    .action-btn { width:30px; height:30px; border-radius:7px; border:1.5px solid var(--border); background:var(--white); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-muted); transition:all .15s; text-decoration:none; }
    .action-btn:hover        { border-color:var(--green); color:var(--green); }
    .action-btn.danger:hover { border-color:#ef4444; color:#ef4444; }
    .action-btn svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .actions-cell { display:flex; gap:6px; align-items:center; }
    .empty-state { text-align:center; padding:60px 20px; }
    .empty-state svg { width:48px; height:48px; stroke:var(--border); fill:none; stroke-width:1.5; display:block; margin:0 auto 12px; }
    .empty-state h3 { font-size:15px; font-weight:700; color:var(--text); margin:0 0 6px; }
    .empty-state p  { font-size:13px; color:var(--text-muted); margin:0 0 18px; }
    .table-footer { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-top:1px solid var(--border); font-size:12px; color:var(--text-muted); flex-wrap:wrap; gap:8px; }
    .pagination { display:flex; gap:4px; }
    .page-btn { width:30px; height:30px; border-radius:7px; border:1.5px solid var(--border); background:var(--white); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; cursor:pointer; color:var(--text); text-decoration:none; transition:all .15s; }
    .page-btn:hover, .page-btn.active { background:var(--green); border-color:var(--green); color:#fff; }
    .page-btn.disabled { opacity:.4; pointer-events:none; }
    .page-btn svg { width:13px; height:13px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    @media(max-width:900px) { .stat-row { grid-template-columns:1fr 1fr; } .page-header { flex-wrap:wrap; } .page-header-actions { width:100%; } }
    @media(max-width:560px) { .stat-row { grid-template-columns:1fr; } .search-row { flex-direction:column; align-items:stretch; } .search-wrap { max-width:100%; } .result-count { margin-left:0; } }
</style>
@endpush

@section('content')

@php
    $q = \App\Models\Material::query();

    if (request('status') === 'active')   $q->where('is_active', true);
    if (request('status') === 'inactive') $q->where('is_active', false);
    if (request('category')) $q->where('category', request('category'));
    if (request('unit'))     $q->where('unit', request('unit'));
    if (request('search'))
        $q->where(function($sq) {
            $sq->where('material_name', 'like', '%'.request('search').'%')
               ->orWhere('material_code', 'like', '%'.request('search').'%')
               ->orWhere('stock_code', 'like', '%'.request('search').'%')
               ->orWhere('secondary_name', 'like', '%'.request('search').'%');
        });

    $list_items   = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
    
    $totalAll     = \App\Models\Material::count();
    $activeCnt    = \App\Models\Material::where('is_active', true)->count();
    $inactiveCnt  = \App\Models\Material::where('is_active', false)->count();
    $thisMonthCnt = \App\Models\Material::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    $categories   = \App\Models\Material::where('is_active', true)->distinct()->pluck('category')->filter()->sort()->values();
    $units        = \App\Models\Material::where('is_active', true)->distinct()->pluck('unit')->filter()->sort()->values();
@endphp

<div class="page-header">
    <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
    </div>
    <div class="page-header-text">
        <h1>Materials</h1>
        <p>Manage material master records</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
        <a href="{{ route('admin.mes.material.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Material
        </a>
    </div>
</div>

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-card-icon green">
            <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <div><div class="stat-val">{{ $totalAll }}</div><div class="stat-lbl">Total Materials</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon emerald">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div><div class="stat-val">{{ $activeCnt }}</div><div class="stat-lbl">Active</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon amber">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div><div class="stat-val">{{ $inactiveCnt }}</div><div class="stat-lbl">Inactive</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon indigo">
            <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div><div class="stat-val">{{ $thisMonthCnt }}</div><div class="stat-lbl">Added This Month</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon sky">
            <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
        </div>
        <div><div class="stat-val">{{ $categories->count() }}</div><div class="stat-lbl">Categories</div></div>
    </div>
</div>

<form method="GET" action="{{ route('admin.mes.material.index') }}" id="filterForm">
    @php $activeFilters = count(array_filter(request()->only(['status','category','unit','search']))); @endphp
    <div class="filter-bar">
        <div class="filter-bar-header" onclick="toggleFilter()">
            <svg viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            <span>Filter Records</span>
            @if($activeFilters)
                <span class="filter-count">{{ $activeFilters }} active</span>
            @else
                <span style="font-size:12px;color:var(--text-muted)">0 filters</span>
            @endif
            <svg class="filter-toggle-icon {{ $activeFilters ? 'open' : '' }}" id="filterChevron" viewBox="0 0 24 24">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>
        <div class="filter-body {{ $activeFilters ? 'open' : '' }}" id="filterBody">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Unit</label>
                    <select name="unit">
                        <option value="">All Units</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit }}" {{ request('unit') === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('admin.mes.material.index') }}" class="btn btn-outline">Clear</a>
            </div>
        </div>
    </div>

    <div class="search-row">
        <div class="search-wrap">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search materials…"
                   oninput="debounceSearch(this)">
        </div>
        <div class="result-count">{{ $list_items->total() }} result(s)</div>
    </div>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Material Code</th>
                <th>Material Name</th>
                <th>Stock Code</th>
                <th>Category</th>
                <th>Section</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list_items as $item)
                @php
                    $isSubmitted = $item->is_active;
                    $statusLabel = $isSubmitted ? 'active' : 'inactive';
                @endphp
                <tr id="row-{{ $item->id }}">
                    <td>
                        <div class="item-name">{{ $item->material_code }}</div>
                    </td>
                    <td>
                        <div class="item-name">{{ $item->material_name }}</div>
                        @if($item->secondary_name)
                            <div class="item-sub">{{ $item->secondary_name }}</div>
                        @endif
                    </td>
                    <td>{{ $item->stock_code ?: '—' }}</td>
                    <td>
                        @if($item->category)
                            <span class="cat-badge">{{ $item->category }}</span>
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>{{ $item->section ?: '—' }}</td>
                    <td>
                        @if($item->unit)
                            <span class="unit-badge">{{ $item->unit }}</span>
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge {{ $statusLabel }}">
                            {{ $isSubmitted ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ optional($item->createdBy)->name ?? '—' }}</td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('admin.mes.material.edit', $item->id) }}"
                               class="action-btn" title="View / Edit">
                                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button 
                                id="del-{{ $item->id }}"
                                class="action-btn danger" 
                                onclick="deleteBatch({{ $item->id }}, '{{ $item->material_name }}', '/material/{{ $item->id }}')"
                                title="Delete">
                                
                                <svg viewBox="0 0 24 24">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            <h3>No materials found</h3>
                            <p>{{ request('search') || $activeFilters ? 'Try adjusting your filters.' : 'Add your first material to get started.' }}</p>
                            @if(!request('search') && !$activeFilters)
                                <a href="{{ route('admin.mes.material.create') }}" class="btn btn-primary">
                                    <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    New Material
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
            <span>Showing {{ $list_items->firstItem() }}–{{ $list_items->lastItem() }} of {{ $list_items->total() }} results</span>
            @if($list_items->hasPages())
                <div class="pagination">
                    @if($list_items->onFirstPage())
                        <span class="page-btn disabled"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></span>
                    @else
                        <a href="{{ $list_items->previousPageUrl() }}" class="page-btn"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></a>
                    @endif
                    @foreach($list_items->getUrlRange(1, $list_items->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page == $list_items->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($list_items->hasMorePages())
                        <a href="{{ $list_items->nextPageUrl() }}" class="page-btn"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></a>
                    @else
                        <span class="page-btn disabled"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>

@if(session('success'))
    <div id="flashMsg" style="position:fixed;bottom:24px;right:24px;background:#166534;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:9999;display:flex;align-items:center;gap:8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    <script>setTimeout(() => { const el = document.getElementById('flashMsg'); if(el) el.remove(); }, 3500);</script>
@endif
@if(session('error'))
    <div id="flashMsg" style="position:fixed;bottom:24px;right:24px;background:#991b1b;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:9999;display:flex;align-items:center;gap:8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    <script>setTimeout(() => { const el = document.getElementById('flashMsg'); if(el) el.remove(); }, 3500);</script>
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
</script>
@endpush