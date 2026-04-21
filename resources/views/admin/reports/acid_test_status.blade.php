{{--
resources/views/admin/reports/acid_test_status.blade.php
Acid Test Status Report — data from acid_test_header only
Columns: Date · Lot No · Supplier · Material · Category · Unit · Qty · Test Status
Sort: date · supplier · material
Filter: date range, supplier, material, category, test status, lot no
--}}
@extends('admin.layouts.app')

@section('title', 'Acid Test Status Report')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <span style="color:var(--text-muted);">Reports</span>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <strong>Acid Test Status</strong>
@endsection

@push('styles')
    <style>
        /* ── Design tokens ───────────────────────────────────────────── */
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

        /* ── Page header ─────────────────────────────────────────────── */
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

        /* ── Buttons ─────────────────────────────────────────────────── */
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

        /* ── Card ────────────────────────────────────────────────────── */
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

        /* ── Filter grid ─────────────────────────────────────────────── */
        .fg {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 14px 20px
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px
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

        /* ── Summary chips ───────────────────────────────────────────── */
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
            min-width: 140px
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

        .chip.chip-notdone .chip-val {
            color: #b45309
        }

        .chip.chip-progress .chip-val {
            color: #1d4ed8
        }

        .chip.chip-done .chip-val {
            color: #065f46
        }

        /* ── Data table ──────────────────────────────────────────────── */
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
            user-select: none
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
            vertical-align: middle
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
            border-top: 2px solid var(--bdr)
        }

        .num {
            text-align: right;
            font-variant-numeric: tabular-nums
        }

        /* ── Test status badges ──────────────────────────────────────── */
        .ts-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap
        }

        .ts-badge .ts-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0
        }

        .ts-notdone {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a
        }

        .ts-notdone .ts-dot {
            background: #f59e0b
        }

        .ts-progress {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe
        }

        .ts-progress .ts-dot {
            background: #3b82f6
        }

        .ts-done {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7
        }

        .ts-done .ts-dot {
            background: #10b981
        }

        /* ── Pagination ──────────────────────────────────────────────── */
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

        /* ── Empty / loading states ──────────────────────────────────── */
        .state-row td {
            text-align: center;
            padding: 40px 20px;
            color: var(--txtmu);
            font-size: 13px
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
        }
    </style>
@endpush

@section('content')

    {{-- Page header --}}
    <div class="ph">
        <div>
            <h2>Acid Test Status Report</h2>
            <p>Lot-level view of all acid test records with their current test progress</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn btn-excel btn-sm" id="btnExcel" onclick="exportExcel()">
                <svg viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- ── FILTERS ──────────────────────────────────────────────────── --}}
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
                    <label>Date From</label>
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
                    <label>Date To</label>
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
                    <label>Category</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                        <select id="f_category" onchange="onCategoryChange()">
                            <option value="">All Categories</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Material</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path
                                d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                        </svg>
                        <select id="f_material_id" onchange="onFilterChange()">
                            <option value="">All Materials</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Test Status</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                        <select id="f_test_status" onchange="onFilterChange()">
                            <option value="">All Statuses</option>
                            <option value="0">Test Not Done</option>
                            <option value="1">In Progress</option>
                            <option value="2">Testing Done</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Lot No</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="f_lot_no" placeholder="Search lot…" oninput="onFilterChange()">
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

    {{-- ── SUMMARY CHIPS ─────────────────────────────────────────────── --}}
    <div class="summary-row" id="summaryRow" style="display:none!important">
        <div class="chip">
            <span class="chip-label">Total Lots</span>
            <span class="chip-val" id="smTotal">—</span>
        </div>
        <div class="chip chip-notdone">
            <span class="chip-label">Test Not Done</span>
            <span class="chip-val" id="smNotDone">—</span>
        </div>
        <div class="chip chip-progress">
            <span class="chip-label">In Progress</span>
            <span class="chip-val" id="smProgress">—</span>
        </div>
        <div class="chip chip-done">
            <span class="chip-label">Testing Done</span>
            <span class="chip-val" id="smDone">—</span>
        </div>
    </div>

    {{-- ── REPORT TABLE ──────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="3" y1="15" x2="21" y2="15" />
                    <line x1="9" y1="3" x2="9" y2="21" />
                </svg>
                <span>Acid Test Status</span>
            </div>
            <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>

        <div class="tbl-wrap" style="border-radius:0;border:none">
            <table class="dt" id="reportTable">
                <thead>
                    <tr>
                        <th class="sortable" data-col="receipt_date" onclick="sortBy('receipt_date')">
                            Date
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th>Lot No</th>
                        <th class="sortable" data-col="supplier_name" onclick="sortBy('supplier_name')">
                            Supplier
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th class="sortable" data-col="material_name" onclick="sortBy('material_name')">
                            Material
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th class="num">Qty (KG)</th>
                        <th style="text-align:center">Test Status</th>
                    </tr>
                </thead>
                <tbody id="reportBody">
                    <tr class="state-row">
                        <td colspan="8"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
                <tfoot id="reportFoot" style="display:none">
                    <tr>
                        <td colspan="6" style="text-align:right;font-size:10.5px;letter-spacing:.5px;color:var(--txtmu)">
                            PAGE TOTAL</td>
                        <td class="num" id="footQty"></td>
                        <td></td>
                    </tr>
                </tfoot>
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
        // ── State ───────────────────────────────────────────────────────────────
        let currentPage = 1;
        let currentSort = 'receipt_date';
        let currentDir = 'desc';
        let filterTimer = null;

        // All materials from server (used for category→material filtering)
        let allMaterials = [];

        // ════════════════════════════════════════════════════════════════════════
        // INIT
        // ════════════════════════════════════════════════════════════════════════
        async function init() {
            await loadFilters();
            await loadReport();
        }
        init();

        // ════════════════════════════════════════════════════════════════════════
        // LOAD FILTER DROPDOWNS
        // ════════════════════════════════════════════════════════════════════════
        async function loadFilters() {
            const res = await apiFetch('/reports/acid-test-status/filters');
            if (!res?.ok) return;
            const { data } = await res.json();

            // Suppliers
            const supSel = document.getElementById('f_supplier_id');
            (data.suppliers ?? []).forEach(s => {
                const o = document.createElement('option');
                o.value = s.id; o.textContent = s.supplier_name;
                supSel.appendChild(o);
            });

            // Categories
            const catSel = document.getElementById('f_category');
            (data.categories ?? []).forEach(c => {
                const o = document.createElement('option');
                o.value = c; o.textContent = c;
                catSel.appendChild(o);
            });

            // Materials (store all for dynamic filtering by category)
            allMaterials = data.materials ?? [];
            populateMaterialDropdown('');
        }

        // Populate material dropdown, optionally filtered by category
        function populateMaterialDropdown(category) {
            const matSel = document.getElementById('f_material_id');
            const prevVal = matSel.value;

            matSel.innerHTML = '<option value="">All Materials</option>';
            const list = category
                ? allMaterials.filter(m => m.category === category)
                : allMaterials;

            list.forEach(m => {
                const o = document.createElement('option');
                o.value = m.id; o.textContent = m.name;
                matSel.appendChild(o);
            });

            // Restore previous selection if still valid
            if (prevVal && list.some(m => String(m.id) === String(prevVal))) {
                matSel.value = prevVal;
            }
        }

        // When category changes: re-populate materials then reload
        function onCategoryChange() {
            const cat = document.getElementById('f_category').value;
            populateMaterialDropdown(cat);
            // Reset material selection if it's no longer in the filtered list
            const matSel = document.getElementById('f_material_id');
            if (cat && matSel.value) {
                const valid = allMaterials
                    .filter(m => m.category === cat)
                    .some(m => String(m.id) === matSel.value);
                if (!valid) matSel.value = '';
            }
            onFilterChange();
        }

        // ════════════════════════════════════════════════════════════════════════
        // LOAD REPORT DATA
        // ════════════════════════════════════════════════════════════════════════
        async function loadReport(page = 1) {
            currentPage = page;
            setLoading(true);

            const params = new URLSearchParams({
                date_from: document.getElementById('f_date_from').value,
                date_to: document.getElementById('f_date_to').value,
                supplier_id: document.getElementById('f_supplier_id').value,
                category: document.getElementById('f_category').value,
                material_id: document.getElementById('f_material_id').value,
                test_status: document.getElementById('f_test_status').value,
                lot_no: document.getElementById('f_lot_no').value,
                sort_by: currentSort,
                sort_dir: currentDir,
                per_page: document.getElementById('f_per_page').value,
                page,
            });
            [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });

            const res = await apiFetch(`/reports/acid-test-status?${params.toString()}`);
            setLoading(false);

            if (!res?.ok) { renderError(); return; }

            const json = await res.json();
            const rows = json.data ?? [];

            renderTable(rows);
            renderSummary(rows, json.meta);
            renderPagination(json.meta);
            updateSortHeaders();
        }

        // ════════════════════════════════════════════════════════════════════════
        // RENDER TABLE
        // ════════════════════════════════════════════════════════════════════════
        const BADGE = {
            0: `<span class="ts-badge ts-notdone"><span class="ts-dot"></span>Test Not Done</span>`,
            1: `<span class="ts-badge ts-progress"><span class="ts-dot"></span>In Progress</span>`,
            2: `<span class="ts-badge ts-done"><span class="ts-dot"></span>Testing Done</span>`,
        };

        function renderTable(rows) {
            const tbody = document.getElementById('reportBody');
            const tfoot = document.getElementById('reportFoot');

            if (!rows.length) {
                tbody.innerHTML = `<tr class="state-row"><td colspan="8">No records found for the selected filters.</td></tr>`;
                tfoot.style.display = 'none';
                return;
            }

            let pageQty = 0;
            tbody.innerHTML = rows.map(r => {
                pageQty += Number(r.received_qty) || 0;
                return `<tr>
                    <td>${escHtml(r.receipt_date)}</td>
                    <td style="font-weight:600">${escHtml(r.lot_no)}</td>
                    <td>${escHtml(r.supplier_name)}</td>
                    <td>${escHtml(r.material_name)}</td>
                    <td>${escHtml(r.category)}</td>
                    <td>${escHtml(r.unit)}</td>
                    <td class="num">${fmtNum(r.received_qty)}</td>
                    <td style="text-align:center">${BADGE[r.test_status_key] ?? escHtml(r.test_status)}</td>
                </tr>`;
            }).join('');

            document.getElementById('footQty').textContent = fmtNum(pageQty);
            tfoot.style.display = '';
        }

        // ════════════════════════════════════════════════════════════════════════
        // RENDER SUMMARY CHIPS
        // ════════════════════════════════════════════════════════════════════════
        function renderSummary(rows, meta) {
            document.getElementById('summaryRow').style.display = 'flex';

            const notDone = rows.filter(r => r.test_status_key === 0).length;
            const progress = rows.filter(r => r.test_status_key === 1).length;
            const done = rows.filter(r => r.test_status_key === 2).length;

            document.getElementById('smTotal').textContent = (meta?.total ?? rows.length).toLocaleString();
            document.getElementById('smNotDone').textContent = notDone.toLocaleString();
            document.getElementById('smProgress').textContent = progress.toLocaleString();
            document.getElementById('smDone').textContent = done.toLocaleString();

            document.getElementById('tableCaption').textContent = meta
                ? `Showing ${rows.length} of ${meta.total.toLocaleString()} records`
                : `${rows.length} records`;
        }

        // ════════════════════════════════════════════════════════════════════════
        // RENDER PAGINATION
        // ════════════════════════════════════════════════════════════════════════
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

        // ════════════════════════════════════════════════════════════════════════
        // SORT
        // ════════════════════════════════════════════════════════════════════════
        function sortBy(col) {
            currentDir = (currentSort === col && currentDir === 'desc') ? 'asc' : 'desc';
            currentSort = col;
            loadReport(1);
        }

        function updateSortHeaders() {
            document.querySelectorAll('#reportTable thead th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
                if (th.dataset.col === currentSort) {
                    th.classList.add(currentDir === 'asc' ? 'sort-asc' : 'sort-desc');
                }
            });
        }

        // ════════════════════════════════════════════════════════════════════════
        // FILTERS
        // ════════════════════════════════════════════════════════════════════════
        function onFilterChange() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(() => loadReport(1), 350);
        }

        function resetFilters() {
            ['f_date_from', 'f_date_to', 'f_lot_no'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            ['f_supplier_id', 'f_category', 'f_material_id', 'f_test_status', 'f_per_page'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = id === 'f_per_page' ? '50' : '';
            });
            // Restore full material list
            populateMaterialDropdown('');
            currentSort = 'receipt_date';
            currentDir = 'desc';
            loadReport(1);
        }

        // ════════════════════════════════════════════════════════════════════════
        // EXPORT TO EXCEL
        // ════════════════════════════════════════════════════════════════════════
        async function exportExcel() {
            const btn = document.getElementById('btnExcel');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner" style="border-top-color:#fff"></span> Exporting…`;

            const exportRows = await fetchAllForExport();

            btn.disabled = false;
            btn.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Export Excel`;

            if (!exportRows.length) return;

            const wsData = [
                ['Date', 'Lot No', 'Supplier', 'Material', 'Category', 'Unit', 'Qty (KG)', 'Test Status'],
                ...exportRows.map(r => [
                    r.receipt_date,
                    r.lot_no,
                    r.supplier_name,
                    r.material_name,
                    r.category,
                    r.unit,
                    r.received_qty,
                    r.test_status,
                ]),
            ];

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            ws['!cols'] = [{ wch: 12 }, { wch: 14 }, { wch: 26 }, { wch: 26 }, { wch: 20 }, { wch: 8 }, { wch: 12 }, { wch: 16 }];
            XLSX.utils.book_append_sheet(wb, ws, 'Acid Test Status');
            XLSX.writeFile(wb, `Acid_Test_Status_Report_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.xlsx`);
        }

        async function fetchAllForExport() {
            const all = [];
            let page = 1, lastPage = 1;
            do {
                const params = new URLSearchParams({
                    date_from: document.getElementById('f_date_from').value,
                    date_to: document.getElementById('f_date_to').value,
                    supplier_id: document.getElementById('f_supplier_id').value,
                    category: document.getElementById('f_category').value,
                    material_id: document.getElementById('f_material_id').value,
                    test_status: document.getElementById('f_test_status').value,
                    lot_no: document.getElementById('f_lot_no').value,
                    sort_by: currentSort,
                    sort_dir: currentDir,
                    per_page: 500,
                    page,
                });
                [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
                const res = await apiFetch(`/reports/acid-test-status?${params.toString()}`);
                if (!res?.ok) break;
                const json = await res.json();
                all.push(...(json.data ?? []));
                lastPage = json.meta?.last_page ?? 1;
                page++;
            } while (page <= lastPage);
            return all;
        }

        // ════════════════════════════════════════════════════════════════════════
        // UTILITIES
        // ════════════════════════════════════════════════════════════════════════
        function setLoading(on) {
            if (!on) return;
            document.getElementById('reportBody').innerHTML =
                `<tr class="state-row"><td colspan="8"><span class="spinner"></span>Loading…</td></tr>`;
            document.getElementById('reportFoot').style.display = 'none';
            document.getElementById('pagBar').style.display = 'none';
        }

        function renderError() {
            document.getElementById('reportBody').innerHTML =
                `<tr class="state-row"><td colspan="8" style="color:var(--err)">Failed to load data. Please try again.</td></tr>`;
        }

        function fmtNum(n) {
            if (n === null || n === undefined || n === '') return '—';
            return parseFloat(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function escHtml(str) {
            if (str === null || str === undefined) return '—';
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>

    {{-- SheetJS (Apache-2.0) — client-side Excel export only --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush