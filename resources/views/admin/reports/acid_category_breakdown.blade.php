@extends('admin.layouts.app')

@section('title', 'Acid Test Category Breakdown')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <span style="color:var(--text-muted);">Reports</span>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <strong>Acid Test Category Breakdown</strong>
@endsection

@push('styles')
    <style>
        :root {
            --g: #1a7a3a;
            --gd: #145f2d;
            --gl: #e8f5ed;
            --gxl: #f2faf5;
            --white: #fff;
            --bg: #f4f7f5;
            --bdr: #dde8e2;
            --txt: #1e2d26;
            --txtm: #3d5449;
            --txtmu: #6b8a78;
            --err: #dc2626;
            --sh: 0 1px 6px rgba(26, 122, 58, .07), 0 4px 18px rgba(26, 122, 58, .05);
            --r: 12px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--txt)
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        .ph {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px
        }

        .ph h2 {
            font-size: clamp(17px, 2.3vw, 22px);
            font-weight: 800;
            color: var(--txt);
            letter-spacing: -.3px
        }

        .ph p {
            font-size: 12.5px;
            color: var(--txtmu);
            margin-top: 2px
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 17px;
            border-radius: 9px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all .2s;
            white-space: nowrap
        }

        .btn svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .btn-outline {
            background: var(--white);
            color: var(--txtm);
            border: 1.5px solid var(--bdr)
        }

        .btn-outline:hover {
            border-color: var(--g);
            color: var(--g);
            background: var(--gxl)
        }

        .btn-sm {
            padding: 7px 13px;
            font-size: 12.5px
        }

        .btn-excel {
            background: #217346;
            color: #fff
        }

        .btn-excel:hover {
            background: #185c38;
            box-shadow: 0 4px 14px rgba(33, 115, 70, .3);
            transform: translateY(-1px)
        }

        .btn:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none
        }

        .card {
            background: var(--white);
            border: 1px solid var(--bdr);
            border-radius: var(--r);
            box-shadow: var(--sh);
            margin-bottom: 18px;
            overflow: hidden
        }

        .card-head {
            padding: 11px 20px;
            background: var(--gl);
            border-bottom: 1px solid var(--bdr);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px
        }

        .card-head-left {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .card-head-left svg {
            width: 14px;
            height: 14px;
            stroke: var(--g);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .card-head span {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--g)
        }

        .card-body {
            padding: 20px
        }

        .fg {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 14px 20px
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px
        }

        .field-wide {
            grid-column: span 2
        }

        .field label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: var(--txtm)
        }

        .iw {
            position: relative
        }

        .iw svg.ico {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 13px;
            height: 13px;
            stroke: var(--txtmu);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
            z-index: 1
        }

        input[type=text],
        input[type=date],
        select {
            width: 100%;
            padding: 9px 12px 9px 34px;
            border: 1.5px solid var(--bdr);
            border-radius: 8px;
            background: var(--gxl);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            color: var(--txt);
            outline: none;
            appearance: none;
            transition: border-color .18s, box-shadow .18s, background .18s
        }

        input:focus,
        select:focus {
            border-color: var(--g);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .09)
        }

        select {
            padding-right: 30px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b8a78' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center
        }

        .summary-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px
        }

        .chip {
            background: var(--gl);
            border: 1px solid var(--bdr);
            border-radius: 9px;
            padding: 8px 16px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 170px
        }

        .chip-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--txtmu)
        }

        .chip-val {
            font-size: 20px;
            font-weight: 800;
            color: var(--g);
            letter-spacing: -.5px;
            line-height: 1
        }

        .chip.chip-blue .chip-val {
            color: #1d4ed8
        }

        .chip.chip-done .chip-val {
            color: #065f46
        }

        .tbl-wrap {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--bdr)
        }

        .dt {
            width: 100%;
            border-collapse: collapse
        }

        .dt thead th {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 9px 12px;
            border-bottom: 2px solid var(--bdr);
            white-space: nowrap;
            user-select: none;
            text-align: left;
            vertical-align: bottom
        }

        .dt thead th.num {
            text-align: right
        }

        .dt thead th.cat-th {
            white-space: normal;
            min-width: 125px;
            line-height: 1.3
        }

        .dt thead th.sortable {
            cursor: pointer;
            transition: background .15s
        }

        .dt thead th.sortable:hover {
            background: #daf0e3
        }

        .dt thead th .sort-ico {
            display: inline-flex;
            flex-direction: column;
            gap: 1px;
            margin-left: 5px;
            vertical-align: middle;
            opacity: .35
        }

        .dt thead th.sort-asc .sort-ico,
        .dt thead th.sort-desc .sort-ico {
            opacity: 1
        }

        .dt thead th .sort-ico svg {
            width: 8px;
            height: 8px;
            stroke: var(--g);
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .dt thead th.sort-asc .ico-desc {
            opacity: .2
        }

        .dt thead th.sort-desc .ico-asc {
            opacity: .2
        }

        .dt tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #edf2ef;
            font-size: 12.5px;
            vertical-align: middle;
            white-space: nowrap
        }

        .dt tbody tr:last-child td {
            border-bottom: none
        }

        .dt tbody tr:hover td {
            background: #f7fbf8
        }

        .dt tfoot td {
            background: var(--gl);
            font-weight: 700;
            font-size: 12.5px;
            color: var(--g);
            padding: 8px 12px;
            border-top: 2px solid var(--bdr);
            white-space: nowrap
        }

        .num {
            text-align: right;
            font-variant-numeric: tabular-nums
        }

        .pag {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            flex-wrap: wrap;
            gap: 8px;
            border-top: 1px solid var(--bdr)
        }

        .pag-info {
            font-size: 12px;
            color: var(--txtmu)
        }

        .pag-btns {
            display: flex;
            gap: 5px
        }

        .pag-btn {
            padding: 5px 11px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1.5px solid var(--bdr);
            background: var(--white);
            color: var(--txtm);
            cursor: pointer;
            transition: all .15s;
            font-family: 'Outfit', sans-serif
        }

        .pag-btn:hover:not(:disabled) {
            border-color: var(--g);
            color: var(--g);
            background: var(--gxl)
        }

        .pag-btn.active {
            background: var(--g);
            color: #fff;
            border-color: var(--g)
        }

        .pag-btn:disabled {
            opacity: .4;
            cursor: default
        }

        .state-row td {
            text-align: center;
            padding: 40px 20px;
            color: var(--txtmu);
            font-size: 13px;
            white-space: normal
        }

        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2.5px solid var(--bdr);
            border-top-color: var(--g);
            border-radius: 50%;
            animation: spin .7s linear infinite;
            vertical-align: middle;
            margin-right: 6px
        }

        @media(max-width:700px) {
            .fg {
                grid-template-columns: 1fr 1fr
            }

            .summary-row {
                gap: 8px
            }
        }

        @media(max-width:480px) {
            .fg {
                grid-template-columns: 1fr
            }

            .field-wide {
                grid-column: auto
            }
        }
    </style>
@endpush

@section('content')

    {{-- ── Page header ──────────────────────────────────────────────────────── --}}
    <div class="ph">
        <div>
            <h2>Acid Test Category Breakdown</h2>
            <p>Net weight of each submitted lot split by ULAB category</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn btn-excel btn-sm" id="btnExcel" onclick="exportExcel()">
                <svg viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- ── Filters ───────────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                </svg>
                <span>Filters</span>
            </div>
            <button class="btn btn-outline btn-sm" onclick="resetFilters()">
                <svg viewBox="0 0 24 24">
                    <polyline points="1 4 1 10 7 10" />
                    <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                </svg>
                Reset
            </button>
        </div>
        <div class="card-body">
            <div class="fg">

                <div class="field">
                    <label>Arrival Date From</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <input type="date" id="f_date_from" onchange="onFilterChange()">
                    </div>
                </div>

                <div class="field">
                    <label>Arrival Date To</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <input type="date" id="f_date_to" onchange="onFilterChange()">
                    </div>
                </div>

                <div class="field">
                    <label>Supplier</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                        <select id="f_supplier_id" onchange="onFilterChange()">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Lot No (optional)</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="f_lot_no" placeholder="Search lot…" oninput="onFilterChange()">
                    </div>
                </div>

                <div class="field field-wide">
                    <label>Acid %</label>
                    <div style="display:flex;gap:8px">
                        <select id="f_acid_op" style="flex:0 0 56%;padding-left:12px" onchange="onFilterChange()">
                            <option value="">All</option>
                            <option value="lte">≤ Less than or equal</option>
                            <option value="gte">≥ Greater than or equal</option>
                            <option value="lt">&lt; Less than</option>
                            <option value="gt">&gt; Greater than</option>
                            <option value="eq">= Equals</option>
                        </select>
                        <input type="text" id="f_acid_value" inputmode="decimal" placeholder="e.g. 18"
                            style="flex:1;padding-left:12px" oninput="onFilterChange()">
                    </div>
                </div>

                <div class="field">
                    <label>Rows per page</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg>
                        <select id="f_per_page" onchange="onFilterChange()">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Summary chips (all filtered lots) ─────────────────────────────────── --}}
    <div class="summary-row">
        <div class="chip">
            <span class="chip-label">Total Lots</span>
            <span class="chip-val" id="smLots">—</span>
        </div>
        <div class="chip chip-blue">
            <span class="chip-label">In-House Weighbridge Wt (KG)</span>
            <span class="chip-val" id="smInHouse">—</span>
        </div>
        <div class="chip chip-done">
            <span class="chip-label">Total Net Weight (KG)</span>
            <span class="chip-val" id="smNet">—</span>
        </div>
    </div>

    {{-- ── Report table ──────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="3" y1="15" x2="21" y2="15" />
                    <line x1="9" y1="3" x2="9" y2="21" />
                </svg>
                <span>Category-wise Breakdown (KG)</span>
            </div>
            <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>

        <div class="tbl-wrap" style="border-radius:0;border:none">
            <table class="dt" id="reportTable">
                <thead id="reportHead"></thead>
                <tbody id="reportBody">
                    <tr class="state-row">
                        <td colspan="16"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
                <tfoot id="reportFoot" style="display:none"></tfoot>
            </table>
        </div>

        <div class="pag" id="pagBar" style="display:none">
            <span class="pag-info" id="pagInfo"></span>
            <div class="pag-btns" id="pagBtns"></div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── State ────────────────────────────────────────────────────────────────────
        let currentPage = 1;
        let currentSort = 'lot_number';
        let currentDir = 'asc';
        let filterTimer = null;
        let COLSPAN = 16;

        const SORT_ICO = `<span class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg><svg class="ico-desc" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>`;

        // ════════════════════════════════════════════════════════════════════════════
        // INIT
        // ════════════════════════════════════════════════════════════════════════════
        async function init() {
            setDefaultDates();
            renderHead([]);
            await loadFilters();
            await loadReport();
        }
        init();

        function ymd(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }
        function setDefaultDates() {
            const now = new Date();
            document.getElementById('f_date_from').value = ymd(new Date(now.getFullYear(), now.getMonth(), 1));
            document.getElementById('f_date_to').value = ymd(now);
        }

        // ════════════════════════════════════════════════════════════════════════════
        // FILTER DROPDOWNS
        // ════════════════════════════════════════════════════════════════════════════
        async function loadFilters() {
            const res = await apiFetch('/reports/acid-category-breakdown/filters');
            if (!res?.ok) return;
            const { data } = await res.json();
            const sel = document.getElementById('f_supplier_id');
            (data.suppliers ?? []).forEach(s => {
                const o = document.createElement('option');
                o.value = s.id; o.textContent = s.supplier_name;
                sel.appendChild(o);
            });
        }

        // ════════════════════════════════════════════════════════════════════════════
        // LOAD REPORT
        // ════════════════════════════════════════════════════════════════════════════
        function buildParams(extra = {}) {
            const params = new URLSearchParams({
                date_from: document.getElementById('f_date_from').value,
                date_to: document.getElementById('f_date_to').value,
                supplier_id: document.getElementById('f_supplier_id').value,
                lot_no: document.getElementById('f_lot_no').value,
                acid_op: document.getElementById('f_acid_op').value,
                acid_value: document.getElementById('f_acid_value').value.trim(),
                sort_by: currentSort,
                sort_dir: currentDir,
                per_page: document.getElementById('f_per_page').value,
                page: currentPage,
            });
            Object.entries(extra).forEach(([k, v]) => params.set(k, v));
            if (!params.get('acid_value')) params.delete('acid_op');
            [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
            return params;
        }

        async function loadReport(page = 1) {
            currentPage = page;
            setLoading();

            const res = await apiFetch(`/reports/acid-category-breakdown?${buildParams().toString()}`);
            if (!res?.ok) { renderError(); return; }

            const json = await res.json();
            const columns = json.columns ?? [];
            const rows = json.data ?? [];

            renderHead(columns);
            renderTable(rows, columns, json.meta?.totals);
            renderSummary(rows, json.meta);
            renderPagination(json.meta);
            updateSortHeaders();
        }

        // ════════════════════════════════════════════════════════════════════════════
        // RENDER
        // ════════════════════════════════════════════════════════════════════════════
        function renderHead(columns) {
            const s = (col, label, cls = '') =>
                `<th class="sortable ${cls}" data-col="${col}" onclick="sortBy('${col}')">${label}${SORT_ICO}</th>`;
            document.getElementById('reportHead').innerHTML = '<tr>' + [
                s('lot_number', 'Lot No'),
                '<th>Supplier</th>',
                s('arrival_date', 'Date Of Arrival'),
                s('received_qty', 'In-House Weighbridge Wt', 'num'),
                '<th class="num">Total Net Weight (KG)</th>',
                s('net_avg_acid_pct', 'Acid (%)', 'num'),
                ...columns.map(c => `<th class="num cat-th">${escHtml(c.label)}</th>`),
            ].join('') + '</tr>';
            COLSPAN = 6 + columns.length;
        }

        function renderTable(rows, columns, totals) {
            const tbody = document.getElementById('reportBody');
            const tfoot = document.getElementById('reportFoot');

            if (!rows.length) {
                tbody.innerHTML = `<tr class="state-row"><td colspan="${COLSPAN}">No records found.</td></tr>`;
                tfoot.style.display = 'none';
                return;
            }

            tbody.innerHTML = rows.map(r => {
                const cats = columns.map(c => `<td class="num">${fmtW(r.cats?.[c.code])}</td>`).join('');
                return `<tr>
                            <td style="font-weight:700">${escHtml(r.lot_number)}</td>
                            <td>${escHtml(r.supplier_name)}</td>
                            <td>${escHtml(r.arrival_date)}</td>
                            <td class="num">${fmtW(r.received_qty)}</td>
                            <td class="num" style="font-weight:700">${fmtW(r.total_net)}</td>
                            <td class="num">${r.acid_pct ? Number(r.acid_pct).toFixed(2) + '%' : ''}</td>
                            ${cats}
                        </tr>`;
            }).join('');

            tfoot.innerHTML = `<tr>
                        <td colspan="3" style="text-align:right;font-size:10.5px;letter-spacing:.5px;color:var(--txtmu)">TOTAL (ALL FILTERED LOTS)</td>
                        <td class="num">${fmtW(totals?.received_qty)}</td>
                        <td class="num">${fmtW(totals?.total_net)}</td>
                        <td></td>
                        ${columns.map(c => `<td class="num">${fmtW(totals?.cats?.[c.code])}</td>`).join('')}
                    </tr>`;
            tfoot.style.display = '';
        }

        function renderSummary(rows, meta) {
            const t = meta?.totals ?? {};
            document.getElementById('smLots').textContent = (meta?.total ?? rows.length).toLocaleString();
            document.getElementById('smInHouse').textContent = fmtNum(t.received_qty);
            document.getElementById('smNet').textContent = fmtNum(t.total_net);
            document.getElementById('tableCaption').textContent = meta
                ? `Showing ${rows.length} of ${meta.total.toLocaleString()} lots`
                : `${rows.length} lots`;
        }

        // ════════════════════════════════════════════════════════════════════════════
        // PAGINATION
        // ════════════════════════════════════════════════════════════════════════════
        function renderPagination(meta) {
            const bar = document.getElementById('pagBar');
            const info = document.getElementById('pagInfo');
            const btns = document.getElementById('pagBtns');
            if (!meta || meta.last_page <= 1) { bar.style.display = 'none'; return; }
            bar.style.display = 'flex';
            const from = (meta.current_page - 1) * meta.per_page + 1;
            const to = Math.min(meta.current_page * meta.per_page, meta.total);
            info.textContent = `${from}–${to} of ${meta.total.toLocaleString()}`;
            const pages = paginationRange(meta.current_page, meta.last_page);
            btns.innerHTML = [
                `<button class="pag-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="loadReport(${meta.current_page - 1})">‹</button>`,
                ...pages.map(p => p === '…'
                    ? `<button class="pag-btn" disabled>…</button>`
                    : `<button class="pag-btn${p === meta.current_page ? ' active' : ''}" onclick="loadReport(${p})">${p}</button>`),
                `<button class="pag-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="loadReport(${meta.current_page + 1})">›</button>`,
            ].join('');
        }
        function paginationRange(cur, last) {
            const delta = 2, range = [];
            for (let i = Math.max(2, cur - delta); i <= Math.min(last - 1, cur + delta); i++) range.push(i);
            if (range[0] > 2) range.unshift('…');
            if (range[range.length - 1] < last - 1) range.push('…');
            range.unshift(1);
            if (last !== 1) range.push(last);
            return range;
        }

        // ════════════════════════════════════════════════════════════════════════════
        // SORT
        // ════════════════════════════════════════════════════════════════════════════
        function sortBy(col) {
            currentDir = (currentSort === col) ? (currentDir === 'asc' ? 'desc' : 'asc') : 'asc';
            currentSort = col;
            loadReport(1);
        }
        function updateSortHeaders() {
            document.querySelectorAll('#reportTable thead th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
                if (th.dataset.col === currentSort)
                    th.classList.add(currentDir === 'asc' ? 'sort-asc' : 'sort-desc');
            });
        }

        // ════════════════════════════════════════════════════════════════════════════
        // FILTERS
        // ════════════════════════════════════════════════════════════════════════════
        function onFilterChange() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(() => loadReport(1), 350);
        }
        function resetFilters() {
            setDefaultDates();
            ['f_supplier_id', 'f_lot_no', 'f_acid_value'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('f_acid_op').value = 'lte';
            document.getElementById('f_per_page').value = '50';
            currentSort = 'lot_number'; currentDir = 'asc';
            loadReport(1);
        }

        // ════════════════════════════════════════════════════════════════════════════
        // EXPORT
        // ════════════════════════════════════════════════════════════════════════════
        async function exportExcel() {
            const btn = document.getElementById('btnExcel');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner" style="border-top-color:#fff"></span> Exporting…`;

            const { rows, columns, totals } = await fetchAllForExport();

            btn.disabled = false;
            btn.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Export Excel`;

            if (!rows.length) return;

            const head = ['Lot No', 'Supplier', 'Date Of Arrival', 'In-House Weighbridge Wt (KG)', 'Total Net Weight (KG)', 'Acid (%)',
                ...columns.map(c => c.label)];
            const body = rows.map(r => [
                r.lot_number, r.supplier_name, r.arrival_date, r.received_qty, r.total_net, r.acid_pct || '',
                ...columns.map(c => r.cats?.[c.code] || ''),
            ]);
            const total = ['TOTAL', '', '', totals?.received_qty ?? '', totals?.total_net ?? '', '',
                ...columns.map(c => totals?.cats?.[c.code] || '')];

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet([head, ...body, total]);
            ws['!cols'] = [{ wch: 12 }, { wch: 32 }, { wch: 14 }, { wch: 20 }, { wch: 18 }, { wch: 10 }, ...columns.map(() => ({ wch: 22 }))];
            XLSX.utils.book_append_sheet(wb, ws, 'Category Breakdown');
            XLSX.writeFile(wb, `Acid_Category_Breakdown_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.xlsx`);
        }

        async function fetchAllForExport() {
            const all = []; let page = 1, lastPage = 1, columns = [], totals = null;
            do {
                const res = await apiFetch(`/reports/acid-category-breakdown?${buildParams({ per_page: 500, page }).toString()}`);
                if (!res?.ok) break;
                const json = await res.json();
                if (page === 1) { columns = json.columns ?? []; totals = json.meta?.totals ?? null; }
                all.push(...(json.data ?? []));
                lastPage = json.meta?.last_page ?? 1; page++;
            } while (page <= lastPage);
            return { rows: all, columns, totals };
        }

        // ════════════════════════════════════════════════════════════════════════════
        // UTILITIES
        // ════════════════════════════════════════════════════════════════════════════
        function setLoading() {
            document.getElementById('reportBody').innerHTML =
                `<tr class="state-row"><td colspan="${COLSPAN}"><span class="spinner"></span>Loading…</td></tr>`;
            document.getElementById('reportFoot').style.display = 'none';
            document.getElementById('pagBar').style.display = 'none';
        }
        function renderError() {
            document.getElementById('reportBody').innerHTML =
                `<tr class="state-row"><td colspan="${COLSPAN}" style="color:var(--err)">Failed to load data. Please try again.</td></tr>`;
        }
        // table cells: blank when zero (like the Excel sheet)
        function fmtW(n) {
            const v = Number(n);
            if (!n || !isFinite(v) || v === 0) return '';
            return v.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }
        // chips: show 0 as 0
        function fmtNum(n) {
            const v = Number(n);
            if (!isFinite(v)) return '0';
            return v.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }
        function escHtml(str) {
            if (str === null || str === undefined || str === '') return '—';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>

    {{-- SheetJS (Apache-2.0) — client-side Excel export only --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush