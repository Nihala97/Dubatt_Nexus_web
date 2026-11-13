@extends('admin.layouts.app')

@section('title', 'Stock Ledger Report')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <span style="color:var(--text-muted);">Reports</span>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <strong>Stock Ledger</strong>
@endsection

@push('styles')
    <style>
        /* ── tokens ──────────────────────────────────────────────────────────────── */
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

        /* ── page header ─────────────────────────────────────────────────────────── */
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

        /* ── buttons ─────────────────────────────────────────────────────────────── */
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

        /* ── card ────────────────────────────────────────────────────────────────── */
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

        /* ── filter grid ─────────────────────────────────────────────────────────── */
        .fg {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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

        /* ── summary chips ───────────────────────────────────────────────────────── */
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

        .chip.chip-out .chip-val {
            color: #b45309
        }

        .chip.chip-net .chip-val {
            color: #1d4ed8
        }

        .chip.chip-bal .chip-val {
            color: #065f46
        }

        /* ── table ───────────────────────────────────────────────────────────────── */
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
            text-align: left
        }

        .dt thead th.num {
            text-align: right
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

        .sub {
            font-size: 11px;
            color: var(--txtmu);
            margin-top: 2px
        }

        .qty-in {
            color: #059669;
            font-weight: 700
        }

        .qty-out {
            color: #dc2626;
            font-weight: 700
        }

        .qty-bal {
            font-weight: 800;
            color: var(--txt)
        }

        .unit {
            font-size: 11px;
            color: var(--txtmu);
            margin-left: 3px
        }

        /* ── pagination ──────────────────────────────────────────────────────────── */
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

        /* ── loading/empty ───────────────────────────────────────────────────────── */
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

        /* ── searchable material dropdown ─────────────────────────────────────── */
        .sdd {
            position: relative;
            width: 100%;
        }

        .sdd-trigger {
            min-height: 38px;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 34px 9px 34px;
            border: 1.5px solid var(--bdr);
            border-radius: 8px;
            background: var(--gxl);
            color: var(--txt);
            cursor: pointer;
            position: relative;
            transition: border-color .18s, box-shadow .18s, background .18s;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            text-align: left;
        }

        .sdd-trigger:hover,
        .sdd.open .sdd-trigger {
            border-color: var(--g);
            background: var(--white);
        }

        .sdd.open .sdd-trigger {
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .09);
        }

        .sdd-trigger-text {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sdd-trigger-text.placeholder {
            color: var(--txtmu);
        }

        .sdd-ico {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 13px;
            height: 13px;
            stroke: var(--txtmu);
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
        }

        .sdd-trigger-chevron {
            position: absolute;
            right: 10px;
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
            transition: transform .18s;
        }

        .sdd.open .sdd-trigger-chevron {
            transform: translateY(-50%) rotate(180deg);
        }

        .sdd-clear {
            display: none;
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            width: 13px;
            height: 13px;
            stroke: var(--txtmu);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            cursor: pointer;
        }

        .sdd.has-value .sdd-clear {
            display: block;
        }

        .sdd.has-value .sdd-trigger-chevron {
            display: none;
        }

        .sdd-portal {
            display: none;
            position: fixed;
            z-index: 99999;
            min-width: 260px;
            max-height: 320px;
            overflow: hidden;
            border: 1px solid var(--bdr);
            border-radius: 10px;
            background: var(--white);
            box-shadow: 0 12px 32px rgba(20, 50, 32, .18);
        }

        .sdd-portal.visible {
            display: block;
        }

        .sdd-search-wrap {
            position: relative;
            padding: 9px;
            border-bottom: 1px solid var(--bdr);
            background: var(--white);
        }

        .sdd-search-ico {
            position: absolute;
            left: 19px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            stroke: var(--txtmu);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
        }

        .sdd-search {
            width: 100%;
            padding: 8px 10px 8px 30px !important;
            border: 1px solid var(--bdr) !important;
            border-radius: 7px !important;
            background: var(--gxl) !important;
            font: 13px 'Outfit', sans-serif !important;
            color: var(--txt) !important;
            outline: none;
        }

        .sdd-search:focus {
            border-color: var(--g) !important;
            box-shadow: 0 0 0 2px rgba(26, 122, 58, .08) !important;
        }

        .sdd-list {
            max-height: 250px;
            overflow-y: auto;
            padding: 4px;
        }

        .sdd-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border-radius: 6px;
            color: var(--txt);
            font-size: 12.5px;
            line-height: 1.4;
            cursor: pointer;
            overflow-wrap: anywhere;
        }

        .sdd-item:hover,
        .sdd-item.selected {
            background: var(--gl);
            color: var(--gd);
        }

        .sdd-item-check {
            flex: 0 0 14px;
            width: 14px;
            height: 14px;
            stroke: var(--g);
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            visibility: hidden;
        }

        .sdd-item.selected .sdd-item-check {
            visibility: visible;
        }

        .sdd-empty {
            padding: 18px 10px;
            text-align: center;
            color: var(--txtmu);
            font-size: 12.5px;
        }
    </style>
@endpush

@section('content')


    {{-- Shared Searchable Dropdown Portal --}}
    <div class="sdd-portal" id="sddPortal">
        <div class="sdd-search-wrap">
            <svg class="sdd-search-ico" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>

            <input class="sdd-search" id="sddPortalSearch" placeholder="Search..." autocomplete="off"
                oninput="sddPortalFilter(this.value)" onkeydown="sddPortalKeydown(event)">
        </div>

        <div class="sdd-list" id="sddPortalList"></div>
    </div>

    {{-- ── Page header ──────────────────────────────────────────────────────── --}}
    <div class="ph">
        <div>
            <h2>Stock Ledger Report</h2>
            <p>Material-wise stock movement with in qty, out qty and current balance</p>
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
                    <label>Material</label>

                    <div class="sdd" id="sdd_f_material_id">
                        <div class="sdd-trigger" onclick="toggleSdd('f_material_id')">

                            <svg class="sdd-ico" viewBox="0 0 24 24">
                                <path
                                    d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                            </svg>

                            <span class="sdd-trigger-text placeholder" id="sdd_f_material_id_label"
                                data-placeholder="All Materials">
                                All Materials
                            </span>

                            <svg class="sdd-clear" onclick="clearSddFilter('f_material_id',event)" viewBox="0 0 24 24">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>

                            <svg class="sdd-trigger-chevron" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>

                        <input type="hidden" id="f_material_id" onchange="onFilterChange()">
                    </div>
                </div>

                <div class="field">
                    <label>Transaction Type</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <polyline points="17 1 21 5 17 9" />
                            <path d="M3 11V9a4 4 0 0 1 4-4h14" />
                            <polyline points="7 23 3 19 7 15" />
                            <path d="M21 13v2a4 4 0 0 1-4 4H3" />
                        </svg>
                        <select id="f_process_type" onchange="onFilterChange()">
                            <option value="">All Types</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Document No</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="f_doc_no" placeholder="Search document…" oninput="onFilterChange()">
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

    {{-- ── Summary chips ─────────────────────────────────────────────────────── --}}
    <div class="summary-row" id="summaryRow">
        <div class="chip">
            <span class="chip-label">Total In Qty</span>
            <span class="chip-val" id="smIn">—</span>
        </div>
        <div class="chip chip-out">
            <span class="chip-label">Total Out Qty</span>
            <span class="chip-val" id="smOut">—</span>
        </div>
        <div class="chip chip-net">
            <span class="chip-label">Net Movement (In − Out)</span>
            <span class="chip-val" id="smNet">—</span>
        </div>
        <div class="chip chip-bal">
            <span class="chip-label" id="smBalLbl">Closing Balance (select a material)</span>
            <span class="chip-val" id="smBal">—</span>
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
                <span>Stock Ledger</span>
            </div>
            <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>

        <div class="tbl-wrap" style="border-radius:0;border:none">
            <table class="dt" id="reportTable">
                <thead>
                    <tr>
                        <th class="sortable" data-col="created_at" onclick="sortBy('created_at')">
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
                        <th class="sortable" data-col="doc_no" onclick="sortBy('doc_no')">
                            Document No
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th>Material</th>
                        <th class="sortable num" data-col="in_qty" onclick="sortBy('in_qty')">
                            In Qty
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th class="sortable num" data-col="out_qty" onclick="sortBy('out_qty')">
                            Out Qty
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th class="sortable num" data-col="balance_qty" onclick="sortBy('balance_qty')">
                            Current Balance
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody id="reportBody">
                    <tr class="state-row">
                        <td colspan="6"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
                <tfoot id="reportFoot" style="display:none">
                    <tr>
                        <td colspan="3" style="text-align:right;font-size:10.5px;letter-spacing:.5px;color:var(--txtmu)">
                            PAGE TOTAL</td>
                        <td class="num" id="footIn"></td>
                        <td class="num" id="footOut"></td>
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
        // ── State ────────────────────────────────────────────────────────────────────
        let currentPage = 1;
        let currentSort = 'created_at';
        let currentDir = 'desc';
        let filterTimer = null;


        // ════════════════════════════════════════════════════════════════════════════
        // SEARCHABLE DROPDOWN (Material)
        // ════════════════════════════════════════════════════════════════════════════
        const sddRegistry = {};
        let sddActiveField = null;

        function sddRegister(fieldId, items, selectedValue = '') {
            sddRegistry[fieldId] = {
                items: Array.isArray(items) ? items : [],
                selected: null
            };

            if (selectedValue !== null && selectedValue !== '') {
                sddSelect(fieldId, String(selectedValue), false);
            } else {
                sddUpdateTrigger(fieldId);
            }
        }

        function sddUpdateTrigger(fieldId) {
            const reg = sddRegistry[fieldId];
            const label = document.getElementById(`sdd_${fieldId}_label`);
            const hidden = document.getElementById(fieldId);
            const wrapper = document.getElementById(`sdd_${fieldId}`);

            if (!reg || !label) return;

            if (reg.selected) {
                label.textContent = reg.selected.label;
                label.classList.remove('placeholder');
            } else {
                label.textContent = label.dataset.placeholder || 'Select…';
                label.classList.add('placeholder');
            }

            if (hidden) hidden.value = reg.selected ? reg.selected.value : '';
            if (wrapper) wrapper.classList.toggle('has-value', !!reg.selected);
        }

        function sddSelect(fieldId, value, triggerChange = true) {
            const reg = sddRegistry[fieldId];
            if (!reg) return;

            const item = value === '' || value === null
                ? null
                : reg.items.find(i => String(i.value) === String(value));

            reg.selected = item || null;
            sddUpdateTrigger(fieldId);

            const hidden = document.getElementById(fieldId);
            if (hidden && triggerChange) {
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }

            sddClosePortal();
        }

        function clearSddFilter(fieldId, event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            sddSelect(fieldId, '', true);
        }

        function toggleSdd(fieldId) {
            if (sddActiveField === fieldId) {
                sddClosePortal();
                return;
            }
            sddOpenPortal(fieldId);
        }

        function sddOpenPortal(fieldId) {
            const trigger = document.querySelector(`#sdd_${fieldId} .sdd-trigger`);
            const portal = document.getElementById('sddPortal');
            if (!trigger || !portal || !sddRegistry[fieldId]) return;

            sddActiveField = fieldId;
            document.querySelectorAll('.sdd.open').forEach(el => el.classList.remove('open'));
            document.getElementById(`sdd_${fieldId}`)?.classList.add('open');

            const rect = trigger.getBoundingClientRect();
            const portalWidth = Math.max(rect.width, 260);
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            portal.style.width = `${Math.min(portalWidth, viewportWidth - 16)}px`;

            let left = rect.left;
            if (left + portalWidth > viewportWidth - 8) {
                left = Math.max(8, viewportWidth - portalWidth - 8);
            }
            portal.style.left = `${left}px`;

            const estimatedHeight = Math.min(320, 58 + sddRegistry[fieldId].items.length * 38);
            const spaceBelow = viewportHeight - rect.bottom;
            if (spaceBelow >= Math.min(estimatedHeight, 200) || spaceBelow >= rect.top) {
                portal.style.top = `${rect.bottom + 4}px`;
                portal.style.bottom = 'auto';
            } else {
                portal.style.top = 'auto';
                portal.style.bottom = `${viewportHeight - rect.top + 4}px`;
            }

            const search = document.getElementById('sddPortalSearch');
            if (search) search.value = '';

            sddPortalRender('');
            portal.classList.add('visible');

            if (search) setTimeout(() => search.focus(), 30);
        }

        function sddClosePortal() {
            document.getElementById('sddPortal')?.classList.remove('visible');
            document.querySelectorAll('.sdd.open').forEach(el => el.classList.remove('open'));
            sddActiveField = null;
        }

        function sddPortalRender(query = '') {
            const list = document.getElementById('sddPortalList');
            const reg = sddRegistry[sddActiveField];
            if (!list || !reg) return;

            const q = String(query).toLocaleLowerCase().trim();
            const filtered = reg.items.filter(item =>
                String(item.label).toLocaleLowerCase().includes(q)
            );
            const selectedValue = reg.selected ? String(reg.selected.value) : '';

            list.replaceChildren();

            if (!filtered.length) {
                const empty = document.createElement('div');
                empty.className = 'sdd-empty';
                empty.textContent = 'No results found';
                list.appendChild(empty);
                return;
            }

            filtered.forEach(item => {
                const row = document.createElement('div');
                row.className = 'sdd-item' +
                    (String(item.value) === selectedValue ? ' selected' : '');
                row.setAttribute('role', 'option');
                row.setAttribute('aria-selected', String(String(item.value) === selectedValue));

                const check = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                check.setAttribute('class', 'sdd-item-check');
                check.setAttribute('viewBox', '0 0 24 24');

                const polyline = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
                polyline.setAttribute('points', '20 6 9 17 4 12');
                check.appendChild(polyline);

                const text = document.createElement('span');
                text.textContent = item.label;

                row.append(check, text);
                row.addEventListener('click', () => sddSelect(sddActiveField, String(item.value)));
                list.appendChild(row);
            });
        }

        function sddPortalFilter(query) {
            sddPortalRender(query);
        }

        function sddPortalKeydown(event) {
            if (event.key === 'Escape') {
                sddClosePortal();
                return;
            }

            if (event.key === 'Enter') {
                const firstItem = document.querySelector('#sddPortalList .sdd-item');
                if (firstItem) firstItem.click();
            }
        }

        document.addEventListener('click', event => {
            if (!event.target.closest('.sdd') && !event.target.closest('#sddPortal')) {
                sddClosePortal();
            }
        });

        window.addEventListener('resize', () => {
            if (sddActiveField) sddOpenPortal(sddActiveField);
        });

        document.addEventListener('scroll', () => {
            if (!sddActiveField) return;

            const trigger = document.querySelector(`#sdd_${sddActiveField} .sdd-trigger`);
            const portal = document.getElementById('sddPortal');
            if (!trigger || !portal) return;

            const rect = trigger.getBoundingClientRect();
            const portalRect = portal.getBoundingClientRect();

            let left = rect.left;
            if (left + portalRect.width > window.innerWidth - 8) {
                left = Math.max(8, window.innerWidth - portalRect.width - 8);
            }
            portal.style.left = `${left}px`;

            if (portal.style.bottom !== 'auto' && portal.style.bottom !== '') {
                portal.style.bottom = `${window.innerHeight - rect.top + 4}px`;
            } else {
                portal.style.top = `${rect.bottom + 4}px`;
            }
        }, true);

        // ════════════════════════════════════════════════════════════════════════════
        // INIT
        // ════════════════════════════════════════════════════════════════════════════
        async function init() {
            setDefaultDates();
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
            const res = await apiFetch('/reports/stock-ledger/filters');
            if (!res?.ok) return;
            const { data } = await res.json();

            // Register materials in the searchable dropdown.
            const materialItems = [
                { value: '', label: 'All Materials' },
                ...(data.materials ?? []).map(m => ({
                    value: String(m.id),
                    label: m.description
                        ? `${m.label} — ${m.description}`
                        : m.label
                }))
            ];
            sddRegister('f_material_id', materialItems);

            const pSel = document.getElementById('f_process_type');
            (data.processTypes ?? []).forEach(p => {
                const o = document.createElement('option');
                o.value = p.value; o.textContent = p.label;
                pSel.appendChild(o);
            });
        }

        // ════════════════════════════════════════════════════════════════════════════
        // LOAD REPORT
        // ════════════════════════════════════════════════════════════════════════════
        function buildParams(extra = {}) {
            const params = new URLSearchParams({
                date_from: document.getElementById('f_date_from').value,
                date_to: document.getElementById('f_date_to').value,
                material_id: document.getElementById('f_material_id').value,
                process_type: document.getElementById('f_process_type').value,
                doc_no: document.getElementById('f_doc_no').value,
                sort_by: currentSort,
                sort_dir: currentDir,
                per_page: document.getElementById('f_per_page').value,
                page: currentPage,
            });
            Object.entries(extra).forEach(([k, v]) => params.set(k, v));
            [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
            return params;
        }

        async function loadReport(page = 1) {
            currentPage = page;
            setLoading(true);

            const res = await apiFetch(`/reports/stock-ledger?${buildParams().toString()}`);
            setLoading(false);
            if (!res?.ok) { renderError(); return; }

            const json = await res.json();
            const rows = json.data ?? [];

            renderTable(rows);
            renderSummary(rows, json.meta);
            renderPagination(json.meta);
            updateSortHeaders();
        }

        // ════════════════════════════════════════════════════════════════════════════
        // RENDER TABLE
        // ════════════════════════════════════════════════════════════════════════════
        function renderTable(rows) {
            const tbody = document.getElementById('reportBody');
            const tfoot = document.getElementById('reportFoot');

            if (!rows.length) {
                tbody.innerHTML = `<tr class="state-row"><td colspan="6">No stock movements found.</td></tr>`;
                tfoot.style.display = 'none';
                return;
            }

            let pageIn = 0, pageOut = 0;
            tbody.innerHTML = rows.map(r => {
                pageIn += Number(r.in_qty) || 0;
                pageOut += Number(r.out_qty) || 0;
                const unit = `<span class="unit">${escHtml(r.unit)}</span>`;
                const code = r.material_code ? escHtml(r.material_code) + ' · ' : '';
                const desc = r.material_desc ? `<div class="sub">${escHtml(r.material_desc)}</div>` : '';

                return `<tr>
                                            <td style="font-weight:600">${escHtml(r.date)}</td>
                                            <td><div style="font-weight:700">${escHtml(r.doc_no)}</div><div class="sub">${escHtml(r.process_type)}</div></td>
                                            <td><div style="font-weight:600">${code}${escHtml(r.material_name)}</div>${desc}</td>
                                            <td class="num">${r.in_qty ? `<span class="qty-in">${fmt(r.in_qty)}</span>${unit}` : '—'}</td>
                                            <td class="num">${r.out_qty ? `<span class="qty-out">${fmt(r.out_qty)}</span>${unit}` : '—'}</td>
                                            <td class="num"><span class="qty-bal">${fmt(r.balance_qty)}</span>${unit}</td>
                                        </tr>`;
            }).join('');

            document.getElementById('footIn').textContent = pageIn ? fmt(pageIn) : '—';
            document.getElementById('footOut').textContent = pageOut ? fmt(pageOut) : '—';
            tfoot.style.display = '';
        }

        // ════════════════════════════════════════════════════════════════════════════
        // SUMMARY  (totals cover the whole filtered result, not just this page)
        // ════════════════════════════════════════════════════════════════════════════
        function renderSummary(rows, meta) {
            const s = meta?.summary ?? {};
            document.getElementById('smIn').textContent = fmt(s.total_in ?? 0);
            document.getElementById('smOut').textContent = fmt(s.total_out ?? 0);
            document.getElementById('smNet').textContent = fmt(s.net ?? 0);

            const hasBal = s.closing_balance !== null && s.closing_balance !== undefined;
            document.getElementById('smBal').textContent = hasBal ? fmt(s.closing_balance) : '—';
            document.getElementById('smBalLbl').textContent = hasBal ? 'Closing Balance' : 'Closing Balance (select a material)';

            document.getElementById('tableCaption').textContent = meta
                ? `Showing ${rows.length} of ${meta.total.toLocaleString()} records`
                : `${rows.length} records`;
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
            currentDir = (currentSort === col && currentDir === 'desc') ? 'asc' : 'desc';
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
            document.getElementById('f_doc_no').value = '';
            sddSelect('f_material_id', '', false);
            document.getElementById('f_process_type').value = '';
            document.getElementById('f_per_page').value = '50';
            currentSort = 'created_at'; currentDir = 'desc';
            loadReport(1);
        }

        // ════════════════════════════════════════════════════════════════════════════
        // EXPORT
        // ════════════════════════════════════════════════════════════════════════════
        async function exportExcel() {
            const btn = document.getElementById('btnExcel');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner" style="border-top-color:#fff"></span> Exporting…`;

            const all = await fetchAllForExport();

            btn.disabled = false;
            btn.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Export Excel`;

            if (!all.length) return;

            const wsData = [
                ['Date', 'Document No', 'Transaction Type', 'Material Code', 'Material', 'Description', 'In Qty', 'Out Qty', 'Current Balance'],
                ...all.map(r => [
                    r.date, r.doc_no, r.process_type, r.material_code, r.material_name, r.material_desc,
                    r.in_qty, r.out_qty, r.balance_qty,
                ]),
            ];

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            ws['!cols'] = [{ wch: 12 }, { wch: 22 }, { wch: 18 }, { wch: 14 }, { wch: 28 }, { wch: 28 }, { wch: 14 }, { wch: 14 }, { wch: 16 }];
            XLSX.utils.book_append_sheet(wb, ws, 'Stock Ledger');
            XLSX.writeFile(wb, `Stock_Ledger_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.xlsx`);
        }

        async function fetchAllForExport() {
            const all = []; let page = 1, lastPage = 1;
            do {
                const res = await apiFetch(`/reports/stock-ledger?${buildParams({ per_page: 500, page }).toString()}`);
                if (!res?.ok) break;
                const json = await res.json();
                all.push(...(json.data ?? []));
                lastPage = json.meta?.last_page ?? 1; page++;
            } while (page <= lastPage);
            return all;
        }

        // ════════════════════════════════════════════════════════════════════════════
        // UTILITIES
        // ════════════════════════════════════════════════════════════════════════════
        function setLoading(on) {
            if (!on) return;
            document.getElementById('reportBody').innerHTML =
                `<tr class="state-row"><td colspan="6"><span class="spinner"></span>Loading…</td></tr>`;
            document.getElementById('reportFoot').style.display = 'none';
            document.getElementById('pagBar').style.display = 'none';
        }
        function renderError() {
            document.getElementById('reportBody').innerHTML =
                `<tr class="state-row"><td colspan="6" style="color:var(--err)">Failed to load data. Please try again.</td></tr>`;
        }
        function fmt(n, d = 3) {
            if (n === null || n === undefined || n === '') return '—';
            return parseFloat(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: d });
        }
        function escHtml(str) {
            if (str === null || str === undefined || str === '') return '—';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>

    {{-- SheetJS (Apache-2.0) — client-side Excel export only --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush