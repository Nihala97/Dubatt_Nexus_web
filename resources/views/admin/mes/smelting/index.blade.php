@extends('admin.layouts.app')

@section('title', 'Smelting Batches')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 8px;color:var(--border)">/</span>
    <strong>Smelting Batches</strong>
@endsection

@push('styles')
<style>
    .page-header{display:flex;align-items:center;gap:16px;margin-bottom:28px}
    .page-header-icon{width:52px;height:52px;background:var(--green-light);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .page-header-icon svg{width:26px;height:26px;stroke:var(--green);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .page-header-text h1{font-size:22px;font-weight:700;color:var(--text);margin:0 0 2px}
    .page-header-text p{font-size:13px;color:var(--text-muted);margin:0}
    .page-header-actions{margin-left:auto;display:flex;gap:10px}
    .btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all .15s;font-family:inherit}
    .btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .btn-outline{background:var(--white);color:var(--text);border:1.5px solid var(--border)}
    .btn-outline:hover{border-color:var(--green);color:var(--green)}
    .btn-primary{background:var(--green);color:#fff}
    .btn-primary:hover{background:#15803d;transform:translateY(-1px);box-shadow:0 4px 14px rgba(26,122,58,.28)}
    .stat-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:24px}
    .stat-card{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-sm)}
    .stat-card-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .stat-card-icon svg{width:19px;height:19px;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .stat-card-icon.green{background:var(--green-light)}.stat-card-icon.green svg{stroke:var(--green)}
    .stat-card-icon.indigo{background:#ede9fe}.stat-card-icon.indigo svg{stroke:#7c3aed}
    .stat-card-icon.emerald{background:#d1fae5}.stat-card-icon.emerald svg{stroke:#059669}
    .stat-card-icon.amber{background:#fef3c7}.stat-card-icon.amber svg{stroke:#d97706}
    .stat-val{font-size:24px;font-weight:700;color:var(--text);line-height:1}
    .stat-lbl{font-size:12px;color:var(--text-muted);margin-top:3px}
    .filter-bar{background:var(--white);border:1px solid var(--border);border-radius:12px;margin-bottom:20px;box-shadow:var(--shadow-sm);overflow:hidden}
    .filter-bar-header{display:flex;align-items:center;gap:10px;padding:14px 18px;cursor:pointer;user-select:none}
    .filter-bar-header svg{width:16px;height:16px;stroke:var(--text-muted);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .filter-bar-header span{font-size:13px;font-weight:600;color:var(--text)}
    .filter-count{background:var(--green);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px}
    .filter-chevron{margin-left:auto;width:16px;height:16px;stroke:var(--text-muted);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;transition:transform .2s}
    .filter-chevron.open{transform:rotate(180deg)}
    .filter-body{display:none;padding:0 18px 18px;border-top:1px solid var(--border)}
    .filter-body.open{display:block}
    .filter-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-top:14px}
    .filter-group label{display:block;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
    .filter-group input,.filter-group select{width:100%;padding:8px 11px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:var(--bg);outline:none;transition:border .15s;box-sizing:border-box;font-family:inherit}
    .filter-group input:focus,.filter-group select:focus{border-color:var(--green)}
    .filter-actions{display:flex;gap:10px;margin-top:14px}
    .tab-bar{display:flex;align-items:center;gap:6px;margin-bottom:16px;flex-wrap:wrap}
    .tab{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1.5px solid transparent;background:none;font-family:inherit;color:var(--text-muted);transition:all .15s}
    .tab.active{background:var(--green);color:#fff;border-color:var(--green)}
    .tab:not(.active):hover{border-color:var(--border);color:var(--text)}
    .tab-count{font-size:11px;background:rgba(255,255,255,.25);padding:1px 6px;border-radius:20px}
    .tab:not(.active) .tab-count{background:var(--border);color:var(--text-muted)}
    .search-row{display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap}
    .search-wrap{position:relative;flex:1;max-width:380px}
    .search-wrap svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;stroke:var(--text-muted);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .search-wrap input{width:100%;padding:8px 11px 8px 33px;border:1.5px solid var(--border);border-radius:9px;font-size:13px;color:var(--text);background:var(--white);outline:none;transition:border .15s;box-sizing:border-box;font-family:inherit}
    .search-wrap input:focus{border-color:var(--green)}
    .result-count{font-size:13px;color:var(--text-muted);margin-left:auto}
    .table-wrap{background:var(--white);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow-sm);overflow:hidden}
    .data-table{width:100%;border-collapse:collapse}
    .data-table thead th{padding:11px 16px;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;background:var(--bg);border-bottom:1px solid var(--border);white-space:nowrap;text-align:left}
    .data-table tbody tr{border-bottom:1px solid var(--border);transition:background .12s}
    .data-table tbody tr:last-child{border-bottom:none}
    .data-table tbody tr:hover{background:#f8fdf9}
    .data-table tbody td{padding:13px 16px;font-size:13px;color:var(--text);vertical-align:middle}
    .batch-no{font-size:13px;font-weight:700;color:var(--text)}
    .batch-sub{font-size:11px;color:var(--text-muted);margin-top:2px}
    .status-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700}
    .status-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor}
    .status-badge.draft{background:#e0e7ff;color:#3730a3}
    .status-badge.submitted{background:#d1fae5;color:#065f46}
    .rotary-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700}
    .rotary-1{background:#fef3c7;color:#92400e}
    .rotary-2{background:#ede9fe;color:#5b21b6}
    .action-btn{width:30px;height:30px;border-radius:7px;border:1.5px solid var(--border);background:var(--white);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);transition:all .15s;text-decoration:none}
    .action-btn:hover{border-color:var(--green);color:var(--green)}
    .action-btn.danger:hover{border-color:#ef4444;color:#ef4444}
    .action-btn svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .actions-cell{display:flex;gap:6px;align-items:center}
    .empty-state{text-align:center;padding:60px 20px}
    .empty-state svg{width:48px;height:48px;stroke:var(--border);fill:none;stroke-width:1.5;display:block;margin:0 auto 12px}
    .empty-state h3{font-size:15px;font-weight:700;color:var(--text);margin:0 0 6px}
    .empty-state p{font-size:13px;color:var(--text-muted);margin:0 0 18px}
    .table-footer{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border);font-size:12px;color:var(--text-muted);flex-wrap:wrap;gap:8px}
    .pagination{display:flex;gap:4px}
    .page-btn{width:30px;height:30px;border-radius:7px;border:1.5px solid var(--border);background:var(--white);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;cursor:pointer;color:var(--text);text-decoration:none;transition:all .15s}
    .page-btn:hover,.page-btn.active{background:var(--green);border-color:var(--green);color:#fff}
    .page-btn.disabled{opacity:.4;pointer-events:none}
    .page-btn svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
</style>
@endpush

@section('content')

@php
    /*
     * ── Status filter logic ──────────────────────────────────────────────────
     * request('status') can be:
     *   null / ''   → no filter (show all)
     *   '0'         → Draft   (status = 0)
     *   '1'         → Submitted (status >= 1)
     *
     * PHP treats '0' as falsy, so we MUST use strict !== '' check.
     */
    $reqStatus  = request('status', '');   // always a string or ''
    $reqRotary  = request('rotary_no', '');
    $reqFrom    = request('date_from', '');
    $reqTo      = request('date_to', '');
    $reqSearch  = request('search', '');

    $q = \App\Models\SmeltingBatch::with(['createdBy'])
            ->where('is_active', true);

    // Status: only filter when a non-empty string is provided
    if ($reqStatus !== '') {
        $q->where('status', (int) $reqStatus);
    }
    if ($reqRotary !== '') {
        $q->where('rotary_no', $reqRotary);
    }
    if ($reqFrom !== '') {
        $q->whereDate('date', '>=', $reqFrom);
    }
    if ($reqTo !== '') {
        $q->whereDate('date', '<=', $reqTo);
    }
    if ($reqSearch !== '') {
        $q->where(function ($sq) use ($reqSearch) {
            $sq->where('batch_no',  'like', '%' . $reqSearch . '%')
               ->orWhere('charge_no', 'like', '%' . $reqSearch . '%');
        });
    }

    $list_items = $q->orderByDesc('date')->orderByDesc('created_at')
                    ->paginate(20)->withQueryString();

    // Stat counts — always unfiltered
    $base         = \App\Models\SmeltingBatch::where('is_active', true);
    $totalAll     = (clone $base)->count();
    $draftCnt     = (clone $base)->where('status', 0)->count();
    $submittedCnt = (clone $base)->where('status', '>=', 1)->count();
    $thisMonthCnt = (clone $base)->whereMonth('date', now()->month)
                                 ->whereYear('date', now()->year)->count();

    // Count non-empty active filters (status=0 must count as active!)
    $activeFilters = 0;
    if ($reqStatus  !== '') $activeFilters++;
    if ($reqRotary  !== '') $activeFilters++;
    if ($reqFrom    !== '') $activeFilters++;
    if ($reqTo      !== '') $activeFilters++;
    if ($reqSearch  !== '') $activeFilters++;
@endphp

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-icon">
        <svg viewBox="0 0 24 24">
            <ellipse cx="12" cy="5" rx="9" ry="3"/>
            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
        </svg>
    </div>
    <div class="page-header-text">
        <h1>Smelting Batches</h1>
        <p>Manage rotary furnace smelting batch records</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Dashboard
        </a>
        <a href="{{ route('admin.mes.smelting.create') }}" class="btn btn-primary" data-permission="smelting,can_create">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Batch
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-card-icon green">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div><div class="stat-val">{{ $totalAll }}</div><div class="stat-lbl">Total Batches</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon indigo">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div><div class="stat-val">{{ $draftCnt }}</div><div class="stat-lbl">Draft</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon emerald">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div><div class="stat-val">{{ $submittedCnt }}</div><div class="stat-lbl">Submitted</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon amber">
            <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div><div class="stat-val">{{ $thisMonthCnt }}</div><div class="stat-lbl">This Month</div></div>
    </div>
</div>

{{--
    ═══════════════════════════════════════════════════════════
    SINGLE FORM — every filter lives here, ONE status source.
    The <select id="statusSelect"> is the ONLY element with
    name="status". Tab buttons just change its value and submit.
    ═══════════════════════════════════════════════════════════
--}}
<form method="GET" action="{{ route('admin.mes.smelting.index') }}" id="filterForm">

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div class="filter-bar-header" onclick="toggleFilter()">
            <svg viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            <span>Filter Records</span>
            @if($activeFilters)
                <span class="filter-count">{{ $activeFilters }} active</span>
            @else
                <span style="font-size:12px;color:var(--text-muted)">0 filters</span>
            @endif
            <svg class="filter-chevron {{ $activeFilters ? 'open' : '' }}" id="filterChevron" viewBox="0 0 24 24">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>
        <div class="filter-body {{ $activeFilters ? 'open' : '' }}" id="filterBody">
            <div class="filter-grid">

                {{-- ★ THE ONLY name="status" element in the entire form ★ --}}
                <div class="filter-group">
                    <label>Status</label>
                    <select id="statusSelect" name="status" onchange="this.form.submit()">
                        <option value=""  {{ $reqStatus === ''  ? 'selected' : '' }}>All Status</option>
                        <option value="0" {{ $reqStatus === '0' ? 'selected' : '' }}>Draft</option>
                        <option value="1" {{ $reqStatus === '1' ? 'selected' : '' }}>Submitted</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Rotary</label>
                    <select name="rotary_no" onchange="this.form.submit()">
                        <option value=""  {{ $reqRotary === ''  ? 'selected' : '' }}>All Rotary</option>
                        <option value="1" {{ $reqRotary === '1' ? 'selected' : '' }}>Rotary 1</option>
                        <option value="2" {{ $reqRotary === '2' ? 'selected' : '' }}>Rotary 2</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Date From</label>
                    <input type="date" name="date_from" value="{{ $reqFrom }}" onchange="this.form.submit()">
                </div>

                <div class="filter-group">
                    <label>Date To</label>
                    <input type="date" name="date_to" value="{{ $reqTo }}" onchange="this.form.submit()">
                </div>

            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('admin.mes.smelting.index') }}" class="btn btn-outline">Clear</a>
            </div>
        </div>
    </div>

    {{-- Tab Bar — buttons mutate #statusSelect then submit, no hidden inputs --}}
    <div class="tab-bar">
        <button type="button" class="tab {{ $reqStatus === '' ? 'active' : '' }}"
                onclick="setTabStatus('')">
            All <span class="tab-count">{{ $totalAll }}</span>
        </button>
        <button type="button" class="tab {{ $reqStatus === '0' ? 'active' : '' }}"
                onclick="setTabStatus('0')">
            Draft <span class="tab-count">{{ $draftCnt }}</span>
        </button>
        <button type="button" class="tab {{ $reqStatus === '1' ? 'active' : '' }}"
                onclick="setTabStatus('1')">
            Submitted <span class="tab-count">{{ $submittedCnt }}</span>
        </button>
    </div>

    {{-- Search row --}}
    <div class="search-row">
        <div class="search-wrap">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" id="searchInput"
                   value="{{ $reqSearch }}"
                   placeholder="Search batch no or charge no…"
                   oninput="debounceSearch()">
        </div>
        <div class="result-count">{{ $list_items->total() }} result(s)</div>
    </div>

</form>

{{-- Data Table --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Batch No</th>
                <th>Charge No</th>
                <th>Date</th>
                <th>Rotary</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Output Material</th>
                <th>Output Qty</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list_items as $batch)
            <tr>
                <td>
                    <div class="batch-no">{{ $batch->batch_no }}</div>
                    <div class="batch-sub">#{{ $batch->id }}</div>
                </td>
                <td>{{ $batch->charge_no ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($batch->date)->format('d M Y') }}</td>
                <td>
                    <span class="rotary-badge rotary-{{ $batch->rotary_no }}">
                        Rotary {{ $batch->rotary_no }}
                    </span>
                </td>
                <td>{{ $batch->start_time ? \Carbon\Carbon::parse($batch->start_time)->format('H:i') : '—' }}</td>
                <td>{{ $batch->end_time   ? \Carbon\Carbon::parse($batch->end_time)->format('H:i')   : '—' }}</td>
                <td>{{ $batch->output_material ?? $batch->outputMaterial_name ?? '—' }}</td>
                <td>
                    @if($batch->output_qty)
                        {{ number_format($batch->output_qty, 3) }}
                        <span style="font-size:11px;color:var(--text-muted)">KG</span>
                    @else
                        —
                    @endif
                </td>
                <td>
                    @php $statusStr = $batch->status >= 1 ? 'submitted' : 'draft'; @endphp
                    <span class="status-badge {{ $statusStr }}">{{ ucfirst($statusStr) }}</span>
                </td>
                <td>{{ optional($batch->createdBy)->name ?? $batch->created_by ?? '—' }}</td>
                <td>
                    <div class="actions-cell">
                        <a href="{{ route('admin.mes.smelting.edit', $batch->id) }}"
                           class="action-btn" data-permission="smelting,can_edit"
                           title="{{ $batch->status >= 1 ? 'View' : 'Edit' }}">
                            @if($batch->status >= 1)
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            @else
                                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            @endif
                        </a>
                        @if($batch->status == 0)
                        <form method="POST" action="{{ route('admin.mes.smelting.destroy', $batch->id) }}"
                              onsubmit="return confirm('Delete batch {{ $batch->batch_no }}?')"
                              style="display:contents">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger"
                                    data-permission="smelting,can_delete" title="Delete">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11">
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <h3>No smelting batches found</h3>
                        <p>{{ ($reqSearch !== '' || $activeFilters) ? 'Try adjusting your filters.' : 'Create your first batch to get started.' }}</p>
                        @if($reqSearch === '' && !$activeFilters)
                            <a href="{{ route('admin.mes.smelting.create') }}" class="btn btn-primary" data-permission="smelting,can_create">
                                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                New Batch
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

{{-- Flash Messages --}}
@if(session('success'))
<div id="flashMsg" style="position:fixed;bottom:24px;right:24px;background:#166534;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:9999;display:flex;align-items:center;gap:8px">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
<script>setTimeout(()=>{const e=document.getElementById('flashMsg');if(e)e.remove()},3500)</script>
@endif
@if(session('error'))
<div id="flashMsg" style="position:fixed;bottom:24px;right:24px;background:#991b1b;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:9999;display:flex;align-items:center;gap:8px">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ session('error') }}
</div>
<script>setTimeout(()=>{const e=document.getElementById('flashMsg');if(e)e.remove()},3500)</script>
@endif

@endsection

@push('scripts')
<script>
    function toggleFilter() {
        document.getElementById('filterBody').classList.toggle('open');
        document.getElementById('filterChevron').classList.toggle('open');
    }

    // Tab click → change the ONE status select → submit form
    function setTabStatus(val) {
        document.getElementById('statusSelect').value = val;
        document.getElementById('filterForm').submit();
    }

    // Debounced search → submit form
    let _st;
    function debounceSearch() {
        clearTimeout(_st);
        _st = setTimeout(() => document.getElementById('filterForm').submit(), 500);
    }
</script>
@endpush