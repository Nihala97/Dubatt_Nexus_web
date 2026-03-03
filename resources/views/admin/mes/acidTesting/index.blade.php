@extends('admin.layouts.app')

@section('title', 'Receiving')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong>Acid Testing</strong>
@endsection

@push('styles')
<style>
    :root {
        --gr-primary: #1e7b51; --gr-primary-light: #e8f5ed; --gr-bg: #f8f9fc;
        --gr-border: #eef0f6; --gr-text-dark: #2d3748; --gr-text-muted: #718096;
        --gr-white: #ffffff; --radius-lg: 12px; --radius-md: 8px;
        --shadow-sm: 0 2px 6px rgba(0,0,0,0.04); --shadow-md: 0 4px 12px rgba(0,0,0,0.06);
    }
    body { background: var(--gr-bg); }
    .page-wrapper { display: flex; flex-direction: column; gap: 20px; }
    .top-action-bar { background: var(--gr-white); border-radius: var(--radius-lg); padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: var(--shadow-sm); flex-wrap: wrap; gap: 16px; }
    .top-action-left { display: flex; align-items: center; gap: 16px; }
    .page-icon-box { width: 48px; height: 48px; background: var(--gr-primary-light); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: var(--gr-primary); }
    .page-icon-box svg { width: 24px; height: 24px; stroke: currentColor; }
    .page-info h2 { font-size: 18px; font-weight: 700; color: var(--gr-text-dark); margin-bottom: 2px; }
    .page-info p  { font-size: 13px; color: var(--gr-text-muted); }
    .top-action-right { display: flex; align-items: center; gap: 12px; }
    .btn-white { background: var(--gr-white); border: 1px solid #dcdfe6; color: var(--gr-text-dark); padding: 9px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; cursor: pointer; transition: all 0.2s; }
    .btn-white:hover { background: #f7f8fa; border-color: #c0c4cc; }
    .btn-primary { background: var(--gr-primary); border: 1px solid var(--gr-primary); color: #fff; padding: 9px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; cursor: pointer; transition: all 0.2s; }
    .btn-primary:hover { background: #166140; border-color: #166140; }
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .summary-card { background: var(--gr-white); padding: 20px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 16px; }
    .sc-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f0f4f8; color: var(--gr-text-muted); }
    .sc-icon svg { width: 20px; height: 20px; stroke: currentColor; }
    .sc-icon.primary { background: var(--gr-primary-light); color: var(--gr-primary); }
    .sc-icon.success { background: #e6f6f0; color: #059669; }
    .sc-icon.warning { background: #fff8e6; color: #d97706; }
    .sc-info h4 { font-size: 20px; font-weight: 700; color: var(--gr-text-dark); margin-bottom: 2px; }
    .sc-info p  { font-size: 12px; color: var(--gr-text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .shimmer { background: linear-gradient(90deg,#e8f5ed 25%,#d0e8d8 50%,#e8f5ed 75%); background-size: 200% 100%; animation: shimmer 1.2s infinite; border-radius: 4px; color: transparent !important; min-width: 36px; display: inline-block; }
    @keyframes shimmer { to { background-position: -200% 0; } }
    .filter-card { background: var(--gr-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
    .filter-header { padding: 14px 24px; border-bottom: 1px solid #edf2f7; display: flex; align-items: center; justify-content: space-between; cursor: pointer; user-select: none; }
    .fh-left { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: var(--gr-text-dark); }
    .fh-left svg { width: 16px; height: 16px; stroke: currentColor; }
    .fh-badge { background: var(--gr-primary-light); color: var(--gr-primary); font-size: 11px; padding: 2px 8px; border-radius: 12px; font-weight: 600; }
    .fh-right svg { width: 16px; height: 16px; stroke: var(--gr-text-muted); transition: transform 0.3s; }
    .filter-card.open .fh-right svg { transform: rotate(180deg); }
    .filter-body { padding: 20px 24px; display: none; }
    .filter-card.open .filter-body { display: block; }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 16px; align-items: flex-end; }
    .f-group label { display: block; font-size: 12px; color: var(--gr-text-muted); margin-bottom: 6px; font-weight: 500; }
    .f-group input, .f-group select { width: 100%; border: 1px solid #e2e8f0; border-radius: var(--radius-md); font-family: inherit; padding: 9px 12px; font-size: 13px; color: var(--gr-text-dark); background: #fff; outline: none; transition: border 0.2s; }
    .f-group input:focus, .f-group select:focus { border-color: var(--gr-primary); }
    .list-card { background: var(--gr-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
    .list-tabs { display: flex; align-items: center; padding: 12px 24px; gap: 8px; overflow-x: auto; border-bottom: 1px solid #edf2f7; }
    .list-tab { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; color: var(--gr-text-muted); text-decoration: none; display: flex; align-items: center; gap: 6px; background: transparent; transition: all 0.2s; cursor: pointer; border: none; font-family: inherit; }
    .list-tab:hover { background: #f7fafc; color: var(--gr-text-dark); }
    .list-tab.active { background: var(--gr-primary); color: #fff; }
    .list-tab .t-badge { background: rgba(255,255,255,0.2); padding: 1px 6px; border-radius: 10px; font-size: 11px; }
    .list-tab:not(.active) .t-badge { background: #edf2f7; color: var(--gr-text-muted); }
    .list-toolbar { padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .search-input { position: relative; max-width: 320px; width: 100%; }
    .search-input svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: var(--gr-text-muted); pointer-events: none; }
    .search-input input { width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #e2e8f0; border-radius: 20px; font-size: 13px; background: #f8fafc; outline: none; transition: all 0.2s; font-family: inherit; }
    .search-input input:focus { border-color: var(--gr-primary); background: #fff; box-shadow: 0 0 0 3px var(--gr-primary-light); }
    .list-meta { font-size: 12px; color: var(--gr-text-muted); }
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 800px; }
    thead { background: #f0f7f4; }
    th { padding: 14px 24px; text-align: left; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; color: var(--gr-primary); text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    td { padding: 16px 24px; font-size: 13px; color: var(--gr-text-dark); border-bottom: 1px solid #edf2f7; vertical-align: middle; }
    tr:hover td { background: #f8fafc; }
    tr:last-child td { border-bottom: none; }
    .checkbox-wrap { display: flex; align-items: center; justify-content: center; }
    .checkbox-wrap input { width: 16px; height: 16px; accent-color: var(--gr-primary); cursor: pointer; }
    .status-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status-badge.draft     { background: #e2e8f0; color: #4a5568; }
    .status-badge.submitted { background: #d1fae5; color: #065f46; }
    .status-badge.completed { background: #d1fae5; color: #065f46; }
    .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
    .action-dropdown { position: relative; display: inline-block; }
    .btn-action { width: 32px; height: 32px; background: #f1f5f9; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--gr-text-muted); transition: all 0.2s; }
    .btn-action:hover { background: #e2e8f0; color: var(--gr-text-dark); }
    .btn-action svg { width: 16px; height: 16px; fill: currentColor; stroke: none; }
    .dropdown-content { display: none; position: absolute; right: 0; top: calc(100% + 4px); background: #fff; min-width: 160px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: var(--radius-md); border: 1px solid #e2e8f0; z-index: 100; padding: 4px 0; animation: fadeUp 0.15s ease-out; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:translateY(0); } }
    .action-dropdown.show .dropdown-content { display: block; }
    .dm-item { display: flex; align-items: center; gap: 8px; width: 100%; padding: 8px 16px; border: none; background: none; text-align: left; font-size: 13px; font-family: inherit; color: var(--gr-text-dark); cursor: pointer; transition: background 0.2s; text-decoration: none; }
    .dm-item:hover { background: #f8fafc; color: var(--gr-primary); }
    .dm-item svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }
    .dm-item.danger { color: #e53e3e; }
    .dm-item.danger:hover { background: #fff5f5; color: #c53030; }
    .dm-divider { height: 1px; background: #e2e8f0; margin: 4px 0; }
    .pagination-wrapper { padding: 16px 24px; border-top: 1px solid #edf2f7; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .paginator { display: flex; gap: 4px; }
    .paginator button { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-md); font-size: 13px; font-weight: 500; border: none; background: transparent; cursor: pointer; color: var(--gr-text-dark); font-family: inherit; transition: all 0.2s; }
    .paginator button:hover:not(:disabled) { background: #f1f5f9; }
    .paginator button.active { background: var(--gr-primary-light); color: var(--gr-primary); }
    .paginator button:disabled { color: #cbd5e0; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div class="page-wrapper">

    <div class="top-action-bar">
        <div class="top-action-left">
            <div class="page-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                    <path d="M16.5 9.4 7.55 4.24M3.29 7 12 12l8.71-5M12 22V12"/>
                </svg>
            </div>
            <div class="page-info">
                <h2>Acid Testing</h2>
                <p>Manage Acid Testing, drafts and submissions</p>
            </div>
        </div>
        <div class="top-action-right">
            <a href="{{ route('admin.dashboard') }}" class="btn-white">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Dashboard
            </a>
            <a href="{{ route('admin.mes.acidTesting.create') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="sc-icon primary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="sc-info"><h4 id="scTotal" class="shimmer">—</h4><p>Total Records</p></div>
        </div>
        <div class="summary-card">
            <div class="sc-icon warning">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            <div class="sc-info"><h4 id="scPending" class="shimmer">—</h4><p>Pending Drafts</p></div>
        </div>
        <div class="summary-card">
            <div class="sc-icon success">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="sc-info"><h4 id="scSubmitted" class="shimmer">—</h4><p>Submitted Orders</p></div>
        </div>
        <div class="summary-card">
            <div class="sc-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="sc-info"><h4>{{ date('M') }}</h4><p>This Month</p></div>
        </div>
    </div>

    <div class="filter-card" id="filterCard">
        <div class="filter-header" onclick="document.getElementById('filterCard').classList.toggle('open')">
            <div class="fh-left">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filter Records
                <span class="fh-badge" id="filterBadge">0 filters</span>
            </div>
            <div class="fh-right"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></div>
        </div>
        <div class="filter-body">
            <div class="filter-grid">
                <div class="f-group">
                    <label>Status</label>
                    <select id="fStatus">
                        <option value="">All Statuses</option>
                        <option value="0">Draft</option>
                        <option value="1">Submitted</option>
                    </select>
                </div>
                <div class="f-group">
                    <label>Supplier</label>
                    <select id="fSupplier"><option value="">All Suppliers</option></select>
                </div>
                <div class="f-group"><label>Date From</label><input type="date" id="fDateFrom"></div>
                <div class="f-group"><label>Date To</label><input type="date" id="fDateTo"></div>
                <div class="f-group" style="display:flex;gap:10px;">
                    <button onclick="applyFilters()" class="btn-primary" style="flex:1;justify-content:center;padding:10px 16px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Apply Filters
                    </button>
                    <button onclick="clearFilters()" class="btn-white" style="padding:10px 16px;">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <div class="list-card">
        <div class="list-tabs">
            <button class="list-tab active" onclick="switchTab(this, '')">All <span class="t-badge" id="tabAll">—</span></button>
            <button class="list-tab" onclick="switchTab(this, '0')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Draft <span class="t-badge" id="tabDraft">—</span>
            </button>
            <button class="list-tab" onclick="switchTab(this, '1')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Submitted <span class="t-badge" id="tabSubmitted">—</span>
            </button>
        </div>

        <div class="list-toolbar">
            <div class="search-input">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" placeholder="Search orders by number, supplier, material...">
            </div>
            <div class="list-meta" id="listMeta">Loading...</div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;padding-right:0;"><div class="checkbox-wrap"><input type="checkbox"></div></th>
                        <th>Doc No</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Catgory</th>
                        <th>Status</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gr-text-muted);">Loading...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            <div class="list-meta" id="paginationMeta"></div>
            <div class="paginator" id="paginator"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const EDIT_BASE  = '{{ url('/admin/mes/receiving') }}/';
    const CREATE_URL = '{{ route('admin.mes.receiving.create') }}';

    let state = { page: 1, perPage: 20, search: '', status: '', supplierId: '', dateFrom: '', dateTo: '' };

    const STATUS_MAP = {
        0: { label: 'Draft',     cls: 'draft' },
        1: { label: 'Submitted', cls: 'submitted' },
        2: { label: 'Submitted', cls: 'submitted' },
        3: { label: 'Completed', cls: 'completed' },
        4: { label: 'Cancelled', cls: 'cancelled' },
    };

    async function loadSuppliers() {
        const res = await apiFetch('/suppliers?per_page=200');
        if (!res?.ok) return;
        const data = await res.json();
        const sel  = document.getElementById('fSupplier');
        (data.data.data || []).forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id; opt.textContent = s.supplier_name;
            sel.appendChild(opt);
        });
    }

    async function loadReceivings() {
        document.getElementById('tableBody').innerHTML =
            '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gr-text-muted);">Loading...</td></tr>';

        let url = `/receivings?page=${state.page}&per_page=${state.perPage}`;
        if (state.search)       url += `&search=${encodeURIComponent(state.search)}`;
        if (state.status !== '') url += `&status=${state.status}`;
        if (state.supplierId)   url += `&supplier_id=${state.supplierId}`;
        if (state.dateFrom)     url += `&date_from=${state.dateFrom}`;
        if (state.dateTo)       url += `&date_to=${state.dateTo}`;

        const res = await apiFetch(url);
        if (!res?.ok) {
            document.getElementById('tableBody').innerHTML =
                '<tr><td colspan="8" style="text-align:center;padding:40px;color:red;">Failed to load data.</td></tr>';
            return;
        }

        const { data: pagination } = await res.json();
        renderTable(pagination.data || []);
        renderPagination(pagination);
        updateSummaryCards(pagination.total);
    }

    function renderTable(items) {
        const tbody = document.getElementById('tableBody');
        if (!items.length) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:40px 20px;">
                <div style="color:var(--gr-text-muted);margin-bottom:12px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <h3 style="font-size:16px;font-weight:600;color:var(--gr-text-dark);margin-bottom:4px;">No records found</h3>
                <p style="font-size:13px;color:var(--gr-text-muted);">Try adjusting your search or filters to find what you're looking for.</p>
            </td></tr>`;
            return;
        }

        tbody.innerHTML = items.map(item => {
            const s = STATUS_MAP[item.status] ?? { label: 'Unknown', cls: 'draft' };
            const isDraft = item.status === 0;
            const date = item.receipt_date
                ? new Date(item.receipt_date).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' })
                : '—';
            return `<tr>
                <td style="padding-right:0;"><div class="checkbox-wrap"><input type="checkbox"></div></td>
                <td><strong style="color:var(--gr-text-dark);">${item.lot_no ?? '—'}</strong></td>
                <td><div style="display:flex;flex-direction:column;gap:2px;"><span style="font-weight:500;">${date}</span></div></td>
                <td><div style="display:flex;flex-direction:column;gap:2px;">
                    <span style="font-weight:600;color:var(--gr-text-dark);">${item.supplier?.supplier_name ?? '—'}</span>
                    <span style="font-size:11px;color:var(--gr-text-muted);">${item.material?.material_name ?? '—'}</span>
                </div></td>
                <td>${item.vehicle_number ?? '—'}</td>
                <td><strong>${parseFloat(item.received_qty ?? 0).toFixed(2)}</strong> <span style="font-size:11px;color:var(--gr-text-muted);">${item.unit ?? ''}</span></td>
                <td><span class="status-badge ${s.cls}">${s.label}</span></td>
                <td>
                    <div class="action-dropdown" id="dropdown-${item.id}">
                        <button class="btn-action" onclick="toggleActionMenu(${item.id}, event)">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="17" r="1.5"/></svg>
                        </button>
                        <div class="dropdown-content">
                            <a href="${EDIT_BASE}${item.id}/edit" class="dm-item">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View Details
                            </a>
                            ${isDraft ? `
                            <a href="${EDIT_BASE}${item.id}/edit" class="dm-item">
                                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit Record
                            </a>
                            <div class="dm-divider"></div>
                            <button class="dm-item danger" onclick="deleteReceiving(${item.id}, event)">
                                <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                Delete
                            </button>` : ''}
                        </div>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function renderPagination(p) {
        document.getElementById('listMeta').textContent = `${p.total} result(s)`;
        document.getElementById('paginationMeta').textContent =
            p.from ? `Showing ${p.from} to ${p.to} of ${p.total} results` : '0 results';
        let html = `<button ${p.current_page===1?'disabled':''} onclick="goPage(${p.current_page-1})">&laquo;</button>`;
        for (let i = Math.max(1, p.current_page-2); i <= Math.min(p.last_page, p.current_page+2); i++) {
            html += `<button class="${i===p.current_page?'active':''}" onclick="goPage(${i})">${i}</button>`;
        }
        html += `<button ${p.current_page===p.last_page?'disabled':''} onclick="goPage(${p.current_page+1})">&raquo;</button>`;
        document.getElementById('paginator').innerHTML = html;
    }

    async function updateSummaryCards(total) {
        const set = (id, val) => { const el = document.getElementById(id); el.textContent = val; el.classList.remove('shimmer'); };
        set('scTotal', total); set('tabAll', total);
        const [dr, sr] = await Promise.all([apiFetch('/receivings?status=0&per_page=1'), apiFetch('/receivings?status=1&per_page=1')]);
        if (dr?.ok) { const d = await dr.json(); const n = d.data.total; set('scPending', n); set('tabDraft', n); }
        if (sr?.ok) { const d = await sr.json(); const n = d.data.total; set('scSubmitted', n); set('tabSubmitted', n); }
    }

    function switchTab(el, status) {
        document.querySelectorAll('.list-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active'); state.status = status; state.page = 1; loadReceivings();
    }

    function applyFilters() {
        state.status     = document.getElementById('fStatus').value;
        state.supplierId = document.getElementById('fSupplier').value;
        state.dateFrom   = document.getElementById('fDateFrom').value;
        state.dateTo     = document.getElementById('fDateTo').value;
        state.page = 1;
        const count = [state.status, state.supplierId, state.dateFrom, state.dateTo].filter(Boolean).length;
        document.getElementById('filterBadge').textContent = `${count} filter${count !== 1 ? 's' : ''}`;
        loadReceivings();
    }

    function clearFilters() {
        state.status = ''; state.supplierId = ''; state.dateFrom = ''; state.dateTo = '';
        ['fStatus','fSupplier','fDateFrom','fDateTo'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('filterBadge').textContent = '0 filters';
        state.page = 1; loadReceivings();
    }

    function goPage(page) { state.page = page; loadReceivings(); }

    async function deleteReceiving(id, e) {
        e.stopPropagation();
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.remove('show'));
        if (!confirm('Are you sure you want to delete this record?')) return;
        const res = await apiFetch(`/receivings/${id}`, { method: 'DELETE' });
        if (res?.ok) { loadReceivings(); }
        else { const d = await res.json(); alert(d.message ?? 'Delete failed.'); }
    }

    function toggleActionMenu(id, e) {
        e.stopPropagation();
        const menu = document.getElementById('dropdown-' + id);
        const isOpen = menu.classList.contains('show');
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.remove('show'));
        if (!isOpen) menu.classList.add('show');
    }

    document.addEventListener('click', () => document.querySelectorAll('.action-dropdown').forEach(d => d.classList.remove('show')));

    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => { state.search = this.value; state.page = 1; loadReceivings(); }, 600);
    });

    loadSuppliers();
    loadReceivings();
</script>
@endpush