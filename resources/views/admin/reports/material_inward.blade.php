{{-- resources/views/admin/reports/material_inward.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Material Inward Report')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <span style="color:var(--text-muted);">Reports</span>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <strong>Material Inward</strong>
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
            --r: 12px
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
            min-width: 130px
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

        .chip-sub {
            font-size: 10.5px;
            color: var(--txtmu)
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

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700
        }

        .st-submitted {
            background: #d1fae5;
            color: #065f46
        }

        .st-draft {
            background: #e0e7ff;
            color: #3730a3
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
        }

        @media(max-width:480px) {
            .fg {
                grid-template-columns: 1fr
            }
        }

        /* Dashboard */
        .dash-section-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--txtmu);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 10px
        }

        .dash-section-title::before,
        .dash-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--bdr)
        }

        .dash-month-badge {
            font-size: 9.5px;
            padding: 2px 8px;
            background: var(--gl);
            border: 1px solid var(--bdr);
            border-radius: 20px;
            color: var(--g);
            font-weight: 700;
            white-space: nowrap
        }

        .cat-chips {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px
        }

        .cat-chip {
            flex: 1;
            min-width: 130px;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            position: relative;
            overflow: hidden;
            border: 1.5px solid transparent;
            transition: transform .18s, box-shadow .18s
        }

        .cat-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12)
        }

        .cat-chip.c-ulab {
            background: linear-gradient(135deg, #166534 0%, #15803d 100%);
            border-color: #14532d
        }

        .cat-chip.c-uplat {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            border-color: #1e3a8a
        }

        .cat-chip.c-dross {
            background: linear-gradient(135deg, #92400e 0%, #b45309 100%);
            border-color: #78350f
        }

        .cat-chip.c-chem {
            background: linear-gradient(135deg, #7c2d12 0%, #c2410c 100%);
            border-color: #7c2d12
        }

        .cat-chip.c-rml {
            background: linear-gradient(135deg, #6b21a8 0%, #9333ea 100%);
            border-color: #581c87
        }

        .cat-chip.c-oth {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            border-color: #1f2937
        }

        .cat-chip-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .7)
        }

        .cat-chip-val {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            letter-spacing: -1px
        }

        .cat-chip-sub {
            font-size: 10px;
            color: rgba(255, 255, 255, .6)
        }

        .cat-chip-ico {
            position: absolute;
            right: 12px;
            top: 12px;
            width: 28px;
            height: 28px;
            stroke: rgba(255, 255, 255, .2);
            fill: none;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .mat-strip {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px
        }

        .mat-card {
            flex: 1;
            min-width: 110px;
            background: var(--gxl);
            border: 1px solid var(--bdr);
            border-radius: 9px;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            gap: 3px;
            transition: border-color .15s, box-shadow .15s
        }

        .mat-card:hover {
            border-color: var(--g);
            box-shadow: 0 2px 12px rgba(26, 122, 58, .1)
        }

        .mat-card-name {
            font-size: 10px;
            font-weight: 700;
            color: var(--txtm);
            letter-spacing: .3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .mat-card-cat {
            font-size: 9px;
            color: var(--txtmu);
            letter-spacing: .4px;
            text-transform: uppercase
        }

        .mat-card-val {
            font-size: 18px;
            font-weight: 800;
            color: var(--g);
            letter-spacing: -.5px;
            line-height: 1.1
        }

        .mat-card-unit {
            font-size: 9.5px;
            color: var(--txtmu)
        }

        .dash-bottom-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 14px
        }

        @media(max-width:800px) {
            .dash-bottom-grid {
                grid-template-columns: 1fr
            }
        }

        .sup-tbl {
            width: 100%;
            border-collapse: collapse
        }

        .sup-tbl thead th {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 7px 11px;
            border-bottom: 2px solid var(--bdr);
            white-space: nowrap
        }

        .sup-tbl tbody td {
            padding: 6px 11px;
            border-bottom: 1px solid #edf2ef;
            font-size: 12px
        }

        .sup-tbl tbody tr:last-child td {
            border-bottom: none
        }

        .sup-tbl tbody tr:hover td {
            background: #f7fbf8
        }

        .sup-rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 19px;
            height: 19px;
            border-radius: 50%;
            background: var(--gl);
            font-size: 9px;
            font-weight: 800;
            color: var(--g)
        }

        .sup-bar-wrap {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .sup-bar {
            flex: 1;
            height: 4px;
            background: var(--bdr);
            border-radius: 2px;
            overflow: hidden
        }

        .sup-bar-fill {
            height: 100%;
            background: var(--g);
            border-radius: 2px;
            transition: width .4s ease
        }

        .last-day-list {
            display: flex;
            flex-direction: column
        }

        .last-day-item {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            padding: 7px 12px;
            border-bottom: 1px solid #edf2ef;
            gap: 8px;
            font-size: 12px
        }

        .last-day-item:last-child {
            border-bottom: none
        }

        .last-day-item:hover {
            background: #f7fbf8
        }

        .last-day-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--g);
            flex-shrink: 0
        }

        .last-day-info {
            display: flex;
            flex-direction: column;
            gap: 1px;
            overflow: hidden
        }

        .last-day-sup {
            font-weight: 600;
            color: var(--txtm);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .last-day-mat {
            font-size: 10.5px;
            color: var(--txtmu)
        }

        .last-day-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 1px;
            white-space: nowrap
        }

        .last-day-qty {
            font-weight: 700;
            color: var(--g);
            font-size: 13px
        }

        .last-day-section {
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 10px;
            background: var(--gl);
            color: var(--g);
            letter-spacing: .4px;
            text-transform: uppercase
        }

        .skel {
            background: linear-gradient(90deg, var(--bdr) 25%, #e8f0eb 50%, var(--bdr) 75%);
            background-size: 200% 100%;
            animation: skel-pulse 1.2s ease infinite;
            border-radius: 4px
        }

        @keyframes skel-pulse {
            0% {
                background-position: 200% 0
            }

            100% {
                background-position: -200% 0
            }
        }

        .dash-empty {
            text-align: center;
            padding: 28px 16px;
            color: var(--txtmu);
            font-size: 12.5px
        }

        .dash-empty svg {
            width: 32px;
            height: 32px;
            stroke: var(--bdr);
            fill: none;
            stroke-width: 1.5;
            display: block;
            margin: 0 auto 8px
        }
    </style>
@endpush

@section('content')
    <div class="ph">
        <div>
            <h2>Material Inward Report</h2>
            <p>Receiving records — filtered, sorted &amp; exportable</p>
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

    <div class="card">
        <div class="card-head">
            <div class="card-head-left"><svg viewBox="0 0 24 24">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                </svg><span>Filters</span></div>
            <button class="btn btn-outline btn-sm" onclick="resetFilters()"><svg viewBox="0 0 24 24">
                    <polyline points="1 4 1 10 7 10" />
                    <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                </svg>Reset</button>
        </div>
        <div class="card-body">
            <div class="fg">
                <div class="field"><label>Date From</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg><input type="date" id="f_date_from" onchange="onFilterChange()"></div>
                </div>
                <div class="field"><label>Date To</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg><input type="date" id="f_date_to" onchange="onFilterChange()"></div>
                </div>
                <div class="field"><label>Supplier</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg><select id="f_supplier_id" onchange="onFilterChange()">
                            <option value="">All Suppliers</option>
                        </select></div>
                </div>
                <div class="field"><label>Material</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <path
                                d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                        </svg><select id="f_material_id" onchange="onFilterChange()">
                            <option value="">All Materials</option>
                        </select></div>
                </div>
                <div class="field"><label>Lot No</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg><input type="text" id="f_lot_no" placeholder="Search lot…" oninput="onFilterChange()"></div>
                </div>
                <div class="field"><label>Rows per page</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg><select id="f_per_page" onchange="onFilterChange()">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                        </select></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="dashboardCard">
        <div class="card-head">
            <div class="card-head-left"><svg viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg><span>Material Inward Dashboard</span></div>
            <span id="dashPeriodLabel" style="font-size:11px;color:var(--txtmu);font-weight:500">Loading…</span>
        </div>
        <div class="card-body">
            <div class="dash-section-title">Score Card — Purchased Qty by Category <span class="dash-month-badge"
                    id="dashMonthBadge">Current Month</span></div>
            <div class="cat-chips" id="catChips">
                <div class="cat-chip c-ulab"><span class="skel" style="width:60%;height:10px;display:block"></span><span
                        class="skel" style="width:50%;height:24px;display:block;margin-top:6px"></span></div>
                <div class="cat-chip c-uplat"><span class="skel" style="width:60%;height:10px;display:block"></span><span
                        class="skel" style="width:50%;height:24px;display:block;margin-top:6px"></span></div>
                <div class="cat-chip c-chem"><span class="skel" style="width:60%;height:10px;display:block"></span><span
                        class="skel" style="width:50%;height:24px;display:block;margin-top:6px"></span></div>
                <div class="cat-chip c-rml"><span class="skel" style="width:60%;height:10px;display:block"></span><span
                        class="skel" style="width:50%;height:24px;display:block;margin-top:6px"></span></div>
            </div>
            <div class="dash-section-title">Score Card — Material Wise <span class="dash-month-badge"
                    id="dashMonthBadge2">Current Month</span></div>
            <div class="mat-strip" id="matStrip">
                <div class="mat-card"><span class="skel" style="width:80%;height:10px;display:block"></span><span
                        class="skel" style="width:50%;height:20px;display:block;margin-top:6px"></span></div>
                <div class="mat-card"><span class="skel" style="width:80%;height:10px;display:block"></span><span
                        class="skel" style="width:50%;height:20px;display:block;margin-top:6px"></span></div>
                <div class="mat-card"><span class="skel" style="width:80%;height:10px;display:block"></span><span
                        class="skel" style="width:50%;height:20px;display:block;margin-top:6px"></span></div>
                <div class="mat-card"><span class="skel" style="width:80%;height:10px;display:block"></span><span
                        class="skel" style="width:50%;height:20px;display:block;margin-top:6px"></span></div>
            </div>
            <div class="dash-bottom-grid">
                <div class="card" style="margin-bottom:0;box-shadow:none">
                    <div class="card-head" style="padding:8px 14px">
                        <div class="card-head-left"><svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg><span>Supplier Wise Qty Accumulation</span></div>
                        <span class="dash-month-badge" id="supMonthBadge">Current Month</span>
                    </div>
                    <div style="overflow-x:auto;max-height:320px;overflow-y:auto">
                        <table class="sup-tbl">
                            <thead>
                                <tr>
                                    <th style="width:28px">#</th>
                                    <th>Supplier</th>
                                    <th>Material</th>
                                    <th style="text-align:right">Qty</th>
                                    <th style="width:70px">Share</th>
                                </tr>
                            </thead>
                            <tbody id="supAccTbody">
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:20px;color:var(--txtmu)"><span
                                            class="spinner"></span> Loading…</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card" style="margin-bottom:0;box-shadow:none">
                    <div class="card-head" style="padding:8px 14px">
                        <div class="card-head-left"><svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg><span id="lastDayTitle">Last Day Inwards</span></div>
                    </div>
                    <div class="last-day-list" id="lastDayList" style="max-height:320px;overflow-y:auto">
                        <div style="text-align:center;padding:20px;color:var(--txtmu)"><span class="spinner"></span>
                            Loading…</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="summary-row" id="summaryRow" style="display:none!important">
        <div class="chip"><span class="chip-label">Total Records</span><span class="chip-val" id="smTotal">—</span></div>
        <div class="chip"><span class="chip-label">Total Received</span><span class="chip-val" id="smReceived">—</span><span
                class="chip-sub">KG</span></div>
        <div class="chip"><span class="chip-label">Total Invoice</span><span class="chip-val" id="smInvoice">—</span><span
                class="chip-sub">PCS</span></div>
    </div>

    <div class="card">
        <div class="card-head">
            <div class="card-head-left"><svg viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="3" y1="15" x2="21" y2="15" />
                    <line x1="9" y1="3" x2="9" y2="21" />
                </svg><span>Inward Records</span></div>
            <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>
        <div class="tbl-wrap" style="border-radius:0;border:none">
            <table class="dt" id="reportTable">
                <thead>
                    <tr>
                        <th class="sortable" data-col="receipt_date" onclick="sortBy('receipt_date')"># Date <span
                                class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg></span></th>
                        <th class="sortable" data-col="lot_no" onclick="sortBy('lot_no')">Lot No <span class="sort-ico"><svg
                                    class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg></span></th>
                        <th class="sortable" data-col="supplier_name" onclick="sortBy('supplier_name')">Supplier <span
                                class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg></span></th>
                        <th class="sortable" data-col="material_name" onclick="sortBy('material_name')">Material <span
                                class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg></span></th>
                        <th class="sortable num" data-col="received_qty" onclick="sortBy('received_qty')">Received Qty <span
                                class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg></span></th>
                        <th class="sortable num" data-col="invoice_qty" onclick="sortBy('invoice_qty')">Invoice Qty <span
                                class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg></span></th>
                        <th>Unit</th>
                        <th>Category</th>
                        <th style="text-align:center">Status</th>
                    </tr>
                </thead>
                <tbody id="reportBody">
                    <tr class="state-row">
                        <td colspan="9"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
                <tfoot id="reportFoot" style="display:none">
                    <tr>
                        <td colspan="4" style="text-align:right;font-size:10.5px;letter-spacing:.5px;color:var(--txtmu)">
                            PAGE TOTAL</td>
                        <td class="num" id="footReceived"></td>
                        <td class="num" id="footInvoice"></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="pag" id="pagBar" style="display:none"><span class="pag-info" id="pagInfo"></span>
            <div class="pag-btns" id="pagBtns"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentPage = 1, currentSort = 'receipt_date', currentDir = 'desc', filterTimer = null, allRows = [];

        async function init() { await loadFilters(); await loadReport(); }
        init();

        async function loadFilters() {
            const res = await apiFetch('/reports/material-inward/filters');
            if (!res?.ok) return;
            const { data } = await res.json();
            const supSel = document.getElementById('f_supplier_id');
            (data.suppliers ?? []).forEach(s => { const o = document.createElement('option'); o.value = s.id; o.textContent = s.supplier_name; supSel.appendChild(o); });
            const matSel = document.getElementById('f_material_id');
            (data.materials ?? []).forEach(m => { const o = document.createElement('option'); o.value = m.id; o.textContent = m.name; matSel.appendChild(o); });
        }

        async function loadReport(page = 1) {
            currentPage = page; setLoading(true);
            const params = new URLSearchParams({ date_from: document.getElementById('f_date_from').value, date_to: document.getElementById('f_date_to').value, supplier_id: document.getElementById('f_supplier_id').value, material_id: document.getElementById('f_material_id').value, lot_no: document.getElementById('f_lot_no').value, sort_by: currentSort, sort_dir: currentDir, per_page: document.getElementById('f_per_page').value, page: page });
            [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
            const res = await apiFetch(`/reports/material-inward?${params.toString()}`);
            setLoading(false);
            if (!res?.ok) { renderError(); return; }
            const json = await res.json();
            allRows = json.data ?? [];
            renderTable(allRows); renderSummary(allRows, json.meta); renderPagination(json.meta); updateSortHeaders();
        }

        function renderTable(rows) {
            const tbody = document.getElementById('reportBody'), tfoot = document.getElementById('reportFoot');
            if (!rows.length) { tbody.innerHTML = `<tr class="state-row"><td colspan="9">No records found.</td></tr>`; tfoot.style.display = 'none'; return; }
            let pr = 0, pi = 0;
            tbody.innerHTML = rows.map(r => {
                pr += r.received_qty; pi += r.invoice_qty;
                const badge = r.status >= 1 ? `<span class="badge-status st-submitted">● Submitted</span>` : `<span class="badge-status st-draft">Draft</span>`;
                return `<tr><td>${escHtml(r.receipt_date)}</td><td style="font-weight:600">${escHtml(r.lot_no)}</td><td>${escHtml(r.supplier_name)}</td><td>${escHtml(r.material_name)}</td><td class="num">${fmtNum(r.received_qty)}</td><td class="num">${fmtNum(r.invoice_qty)}</td><td>${escHtml(r.unit)}</td><td>${escHtml(r.category)}</td><td style="text-align:center">${badge}</td></tr>`;
            }).join('');
            document.getElementById('footReceived').textContent = fmtNum(pr); document.getElementById('footInvoice').textContent = fmtNum(pi); tfoot.style.display = '';
        }

        function renderSummary(rows, meta) {
            const sr = document.getElementById('summaryRow'); sr.style.display = 'flex';
            document.getElementById('smTotal').textContent = (meta?.total ?? rows.length).toLocaleString();
            document.getElementById('smReceived').textContent = fmtNum(rows.reduce((s, r) => s + r.received_qty, 0));
            document.getElementById('smInvoice').textContent = fmtNum(rows.reduce((s, r) => s + r.invoice_qty, 0));
            document.getElementById('tableCaption').textContent = meta ? `Showing ${rows.length} of ${meta.total.toLocaleString()} records` : `${rows.length} records`;
        }

        function renderPagination(meta) {
            const bar = document.getElementById('pagBar'), info = document.getElementById('pagInfo'), btns = document.getElementById('pagBtns');
            if (!meta || meta.last_page <= 1) { bar.style.display = 'none'; return; } bar.style.display = 'flex';
            info.textContent = `${(meta.current_page - 1) * meta.per_page + 1}–${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total.toLocaleString()}`;
            const pages = paginationRange(meta.current_page, meta.last_page);
            btns.innerHTML = [`<button class="pag-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="loadReport(${meta.current_page - 1})">‹</button>`, ...pages.map(p => p === '…' ? `<button class="pag-btn" disabled>…</button>` : `<button class="pag-btn${p === meta.current_page ? ' active' : ''}" onclick="loadReport(${p})">${p}</button>`), `<button class="pag-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="loadReport(${meta.current_page + 1})">›</button>`].join('');
        }

        function paginationRange(cur, last) { const delta = 2, range = []; for (let i = Math.max(2, cur - delta); i <= Math.min(last - 1, cur + delta); i++)range.push(i); if (range[0] > 2) range.unshift('…'); if (range[range.length - 1] < last - 1) range.push('…'); range.unshift(1); if (last !== 1) range.push(last); return range; }

        function sortBy(col) { currentDir = currentSort === col ? (currentDir === 'asc' ? 'desc' : 'asc') : 'desc'; currentSort = col; loadReport(1); }
        function updateSortHeaders() { document.querySelectorAll('#reportTable thead th').forEach(th => { th.classList.remove('sort-asc', 'sort-desc'); if (th.dataset.col === currentSort) th.classList.add(currentDir === 'asc' ? 'sort-asc' : 'sort-desc'); }); }

        function onFilterChange() { clearTimeout(filterTimer); filterTimer = setTimeout(() => loadReport(1), 350); }
        function resetFilters() { ['f_date_from', 'f_date_to', 'f_lot_no'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });['f_supplier_id', 'f_material_id'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; }); document.getElementById('f_per_page').value = '50'; currentSort = 'receipt_date'; currentDir = 'desc'; loadReport(1); }

        async function exportExcel() {
            const btn = document.getElementById('btnExcel'); btn.disabled = true; btn.innerHTML = `<span class="spinner" style="border-top-color:#fff"></span> Exporting…`;
            const exportRows = await fetchAllForExport();
            if (!exportRows.length) { btn.disabled = false; btn.innerHTML = `Export Excel`; return; }
            const wsData = [['Date', 'Lot No', 'Supplier', 'Material', 'Received Qty', 'Invoice Qty', 'Unit', 'Category', 'Status'], ...exportRows.map(r => [r.receipt_date, r.lot_no, r.supplier_name, r.material_name, r.received_qty, r.invoice_qty, r.unit, r.category, r.status >= 1 ? 'Submitted' : 'Draft'])];
            const wb = XLSX.utils.book_new(), ws = XLSX.utils.aoa_to_sheet(wsData);
            ws['!cols'] = [{ wch: 12 }, { wch: 14 }, { wch: 26 }, { wch: 28 }, { wch: 14 }, { wch: 12 }, { wch: 8 }, { wch: 20 }, { wch: 10 }];
            XLSX.utils.book_append_sheet(wb, ws, 'Material Inward');
            XLSX.writeFile(wb, `Material_Inward_Report_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.xlsx`);
            btn.disabled = false; btn.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Export Excel`;
        }

        async function fetchAllForExport() {
            const all = []; let page = 1, lastPage = 1;
            do { const params = new URLSearchParams({ date_from: document.getElementById('f_date_from').value, date_to: document.getElementById('f_date_to').value, supplier_id: document.getElementById('f_supplier_id').value, material_id: document.getElementById('f_material_id').value, lot_no: document.getElementById('f_lot_no').value, sort_by: currentSort, sort_dir: currentDir, per_page: 500, page: page });[...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); }); const res = await apiFetch(`/reports/material-inward?${params.toString()}`); if (!res?.ok) break; const json = await res.json(); all.push(...(json.data ?? [])); lastPage = json.meta?.last_page ?? 1; page++; } while (page <= lastPage);
            return all;
        }

        function setLoading(on) { if (on) { document.getElementById('reportBody').innerHTML = `<tr class="state-row"><td colspan="9"><span class="spinner"></span>Loading…</td></tr>`; document.getElementById('reportFoot').style.display = 'none'; document.getElementById('pagBar').style.display = 'none'; } }
        function renderError() { document.getElementById('reportBody').innerHTML = `<tr class="state-row"><td colspan="9" style="color:var(--err)">Failed to load data. Please try again.</td></tr>`; }
        function fmtNum(n) { if (n === null || n === undefined || n === '') return '—'; return parseFloat(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        function escHtml(str) { if (str === null || str === undefined) return '—'; return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

        /* ═══════════════ DASHBOARD ═══════════════ */
        const CAT_META = { 'ULAB': { cls: 'c-ulab', icon: 'battery' }, 'ULAB PLATES / TERMINALS': { cls: 'c-uplat', icon: 'cpu' }, 'DROSS': { cls: 'c-dross', icon: 'flame' }, 'CHEMICAL / METALS': { cls: 'c-chem', icon: 'zap' }, 'RML': { cls: 'c-rml', icon: 'layers' }, 'Others': { cls: 'c-oth', icon: 'box' } };
        const DASH_ICONS = { battery: `<rect x="2" y="7" width="16" height="10" rx="2"/><line x1="22" y1="11" x2="22" y2="13"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/>`, cpu: `<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/>`, flame: `<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>`, zap: `<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>`, layers: `<polyline points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>`, box: `<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>` };

        async function loadDashboard() {
            const params = new URLSearchParams({ date_from: document.getElementById('f_date_from').value, date_to: document.getElementById('f_date_to').value, supplier_id: document.getElementById('f_supplier_id').value, material_id: document.getElementById('f_material_id').value });
            [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
            let res;
            try { res = await apiFetch(`/reports/material-inward/dashboard?${params.toString()}`); } catch (e) { console.error('[Dash]', e); return; }
            if (!res?.ok) { console.error('[Dash] HTTP', res?.status); return; }
            let json;
            try { json = await res.json(); } catch (e) { console.error('[Dash] JSON', e); return; }
            console.log('[Dashboard]', json);
            if (json.status === 'error') { console.error('[Dash] error:', json.message); showDashError(json.message); return; }
            const d = json.data ?? {};
            console.log('[Dash] debug:', d._debug);
            renderCatChips(d.by_category ?? []);
            renderMatStrip(d.by_material ?? []);
            renderSupplierAccumulation(d.supplier_accumulation ?? [], d.month_label ?? '');
            renderLastDay(d.last_day ?? [], d.last_day_date ?? '');
            renderDashPeriod(d.period_label ?? '', d.month_label ?? '');
        }

        function showDashError(msg) {
            ['catChips', 'matStrip'].forEach(id => { const el = document.getElementById(id); if (el) el.innerHTML = `<div class="dash-empty" style="color:var(--err);width:100%">⚠ ${escHtml(msg)}</div>`; });
            const tb = document.getElementById('supAccTbody'); if (tb) tb.innerHTML = `<tr><td colspan="5" style="color:var(--err);padding:12px">⚠ ${escHtml(msg)}</td></tr>`;
            const ld = document.getElementById('lastDayList'); if (ld) ld.innerHTML = `<div style="color:var(--err);padding:12px">⚠ ${escHtml(msg)}</div>`;
        }

        function renderCatChips(cats) {
            const wrap = document.getElementById('catChips');
            if (!cats.length) { wrap.innerHTML = `<div class="dash-empty" style="width:100%"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>No data for current month</div>`; return; }
            const cc = ['c-ulab', 'c-uplat', 'c-dross', 'c-chem', 'c-rml', 'c-oth'];
            wrap.innerHTML = cats.map((c, idx) => { const meta = CAT_META[c.category] ?? { cls: cc[idx % cc.length], icon: 'box' }; const icon = DASH_ICONS[meta.icon] ?? DASH_ICONS['box']; return `<div class="cat-chip ${meta.cls}"><svg class="cat-chip-ico" viewBox="0 0 24 24">${icon}</svg><span class="cat-chip-label">${escHtml(c.category)}</span><span class="cat-chip-val">${fmtNum(c.total_qty)}</span><span class="cat-chip-sub">${escHtml(c.unit ?? 'KG')} · ${c.record_count} records</span></div>`; }).join('');
        }

        function renderMatStrip(materials) {
            const wrap = document.getElementById('matStrip');
            if (!materials.length) { wrap.innerHTML = `<div class="dash-empty" style="width:100%">No material data for current month</div>`; return; }
            wrap.innerHTML = materials.map(m => `<div class="mat-card"><div class="mat-card-name" title="${escHtml(m.material_name)}">${escHtml(m.material_name)}</div><div class="mat-card-cat">${escHtml(m.category)}</div><div class="mat-card-val">${fmtNum(m.total_qty)}</div><div class="mat-card-unit">${escHtml(m.unit)} · ${m.record_count} lots</div></div>`).join('');
        }

        function renderSupplierAccumulation(rows, monthLabel) {
            const tbody = document.getElementById('supAccTbody'), badge = document.getElementById('supMonthBadge');
            if (badge && monthLabel) badge.textContent = monthLabel;
            if (!rows.length) { tbody.innerHTML = `<tr><td colspan="5"><div class="dash-empty">No supplier data for current month</div></td></tr>`; return; }
            const maxQty = Math.max(...rows.map(r => r.total_qty), 1);
            tbody.innerHTML = rows.map((r, i) => { const pct = Math.round((r.total_qty / maxQty) * 100); return `<tr><td><span class="sup-rank">${i + 1}</span></td><td style="font-weight:600;color:var(--txtm)">${escHtml(r.supplier_name)}</td><td style="color:var(--txtmu);font-size:11px">${escHtml(r.material_name)}</td><td style="text-align:right;font-weight:700;color:var(--g)">${fmtNum(r.total_qty)}</td><td><div class="sup-bar-wrap"><div class="sup-bar"><div class="sup-bar-fill" style="width:${pct}%"></div></div><span style="font-size:9px;color:var(--txtmu);min-width:24px;text-align:right">${pct}%</span></div></td></tr>`; }).join('');
        }

        function renderLastDay(items, dateStr) {
            const list = document.getElementById('lastDayList'), title = document.getElementById('lastDayTitle');
            if (dateStr && title) title.textContent = `Last Day Inwards (${dateStr})`;
            if (!items.length) { list.innerHTML = `<div class="dash-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>No inwards on last transaction date</div>`; return; }
            list.innerHTML = items.map(r => `<div class="last-day-item"><div class="last-day-dot"></div><div class="last-day-info"><span class="last-day-sup">${escHtml(r.supplier_name)}</span><span class="last-day-mat">${escHtml(r.material_name)}</span></div><div class="last-day-right"><span class="last-day-qty">${fmtNum(r.received_qty)} <span style="font-size:9.5px;font-weight:400;color:var(--txtmu)">${escHtml(r.unit ?? 'KG')}</span></span><span class="last-day-section">${escHtml(r.section ?? '—')}</span></div></div>`).join('');
        }

        function renderDashPeriod(label, monthLabel) {
            const el = document.getElementById('dashPeriodLabel'); if (el) el.textContent = label || monthLabel || '';
            ['dashMonthBadge', 'dashMonthBadge2'].forEach(id => { const b = document.getElementById(id); if (b && monthLabel) b.textContent = monthLabel; });
        }

        const _origOnFilterChange = onFilterChange;
        onFilterChange = function () { _origOnFilterChange(); clearTimeout(window._dashTimer); window._dashTimer = setTimeout(loadDashboard, 400); };
        const _origResetFilters = resetFilters;
        resetFilters = function () { _origResetFilters(); setTimeout(loadDashboard, 400); };

        loadDashboard();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush