{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/reports/bbsu_dashboard.blade.php --}}
{{-- ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'BBSU Dashboard & Report')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <span style="color:var(--text-muted);">Reports</span>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <strong>BBSU Dashboard</strong>
@endsection

@push('styles')
    <style>
        :root {
            --g: #1a4f7a;
            --gd: #133d60;
            --gl: #e8f0f8;
            --gxl: #f2f7fc;
            --white: #fff;
            --bg: #f4f6f9;
            --bdr: #d8e3ef;
            --txt: #1e2d3a;
            --txtm: #2d4557;
            --txtmu: #6b849a;
            --err: #dc2626;
            --sh: 0 1px 6px rgba(26, 79, 122, .07), 0 4px 18px rgba(26, 79, 122, .05);
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

        @keyframes skel-pulse {
            0% {
                background-position: 200% 0
            }

            100% {
                background-position: -200% 0
            }
        }

        /* PAGE HEADER */
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

        /* BUTTONS */
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

        /* TABS */
        .tab-bar {
            display: flex;
            gap: 0;
            border-bottom: 2px solid var(--bdr);
            margin-bottom: 18px
        }

        .tab-btn {
            padding: 10px 22px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--txtmu);
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: .2s;
            font-family: 'Outfit', sans-serif
        }

        .tab-btn.active {
            color: var(--g);
            border-bottom-color: var(--g)
        }

        .tab-btn:hover:not(.active) {
            color: var(--txt)
        }

        .tab-pane {
            display: none
        }

        .tab-pane.active {
            display: block
        }

        /* CARD */
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

        /* FILTERS */
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
            box-shadow: 0 0 0 3px rgba(26, 79, 122, .09)
        }

        select {
            padding-right: 30px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b849a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center
        }

        /* SECTION TITLE */
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

        /* SCORECARD CHIPS */
        .sc-grid {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px
        }

        .sc-chip {
            flex: 1;
            min-width: 150px;
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

        .sc-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12)
        }

        .sc-chip.c-navy {
            background: linear-gradient(135deg, #0d1f3c 0%, #1560bd 100%);
            border-color: #0a1828
        }

        .sc-chip.c-blue {
            background: linear-gradient(135deg, #1560bd 0%, #3b9ddd 100%);
            border-color: #0e4a91
        }

        .sc-chip.c-teal {
            background: linear-gradient(135deg, #0e6655 0%, #1abc9c 100%);
            border-color: #0a4f41
        }

        .sc-chip.c-amber {
            background: linear-gradient(135deg, #7d4f00 0%, #d4a017 100%);
            border-color: #5a3900
        }

        .sc-chip.c-red {
            background: linear-gradient(135deg, #7b1010 0%, #c0392b 100%);
            border-color: #5a0c0c
        }

        .sc-chip-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .7)
        }

        .sc-chip-val {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            letter-spacing: -1px
        }

        .sc-chip-sub {
            font-size: 10px;
            color: rgba(255, 255, 255, .6)
        }

        .sc-chip-ico {
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

        .cat-pills-row {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 8px
        }

        .cat-pill {
            font-size: 9px;
            padding: 2px 7px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .18);
            color: #fff;
            font-weight: 600
        }

        /* MATERIAL CARDS */
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
            box-shadow: 0 2px 12px rgba(26, 79, 122, .1)
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

        /* CHART CARDS */
        .chart-card {
            background: var(--white);
            border: 1px solid var(--bdr);
            border-radius: var(--r);
            padding: 16px 20px;
            box-shadow: var(--sh);
            margin-bottom: 14px
        }

        .chart-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 8px
        }

        .chart-card-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--txt);
            text-transform: uppercase;
            letter-spacing: .8px
        }

        .ctrl-row {
            display: flex;
            gap: 5px;
            flex-wrap: wrap
        }

        .ctrl-btn {
            padding: 4px 11px;
            font-size: 11.5px;
            border-radius: 7px;
            border: 1.5px solid var(--bdr);
            background: var(--gxl);
            color: var(--txtm);
            cursor: pointer;
            font-weight: 600;
            transition: .15s;
            font-family: 'Outfit', sans-serif
        }

        .ctrl-btn.active,
        .ctrl-btn:hover {
            background: var(--g);
            color: #fff;
            border-color: var(--g)
        }

        /* Canvas heights */
        #prodChart {
            display: block;
            width: 100% !important;
            height: 260px !important
        }

        #hoursChart {
            display: block;
            width: 100% !important;
            height: 220px !important
        }

        #pwrChart {
            display: block;
            width: 100% !important;
            height: 220px !important
        }

        @media(max-width:640px) {

            #prodChart,
            #hoursChart,
            #pwrChart {
                height: 180px !important
            }
        }

        /* LAST DAY TABLE */
        .last-day-tbl {
            width: 100%;
            border-collapse: collapse
        }

        .last-day-tbl thead th {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 9px 14px;
            border-bottom: 2px solid var(--bdr);
            white-space: nowrap;
            text-align: left
        }

        .last-day-tbl thead th.num {
            text-align: right
        }

        .last-day-tbl tbody td {
            padding: 9px 14px;
            border-bottom: 1px solid #eaeef3;
            font-size: 12.5px;
            vertical-align: middle
        }

        .last-day-tbl tbody tr:last-child td {
            border-bottom: none
        }

        .last-day-tbl tbody tr:hover td {
            background: #f5f8fc
        }

        .acid-high {
            color: #dc2626;
            font-weight: 800
        }

        .acid-mid {
            color: #d97706;
            font-weight: 800
        }

        .acid-ok {
            color: #15803d;
            font-weight: 800
        }

        /* REPORT TABLE */
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
            background: #d5e4f0
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
            border-bottom: 1px solid #eaeef3;
            font-size: 12.5px;
            vertical-align: middle
        }

        .dt tbody tr:last-child td {
            border-bottom: none
        }

        .dt tbody tr:hover td {
            background: #f5f8fc
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

        /* BADGES */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700
        }

        .st-completed {
            background: #d1fae5;
            color: #065f46
        }

        .st-progress {
            background: #dbeafe;
            color: #1e40af
        }

        .st-pending {
            background: #fef3c7;
            color: #92400e
        }

        .st-cancelled {
            background: #fee2e2;
            color: #991b1b
        }

        .mat-pill {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            background: var(--gl);
            color: var(--g);
            margin: 2px 2px 0 0;
            white-space: nowrap
        }

        /* PAGINATION */
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

        /* STATES */
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

        .skel {
            background: linear-gradient(90deg, var(--bdr) 25%, #e5edf4 50%, var(--bdr) 75%);
            background-size: 200% 100%;
            animation: skel-pulse 1.2s ease infinite;
            border-radius: 4px
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

        /* DRILLDOWN MODAL */
        .bbsu-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 20, 40, .55);
            z-index: 9999;
            align-items: center;
            justify-content: center
        }

        .bbsu-modal-overlay.open {
            display: flex
        }

        .bbsu-modal {
            background: #fff;
            border-radius: 14px;
            width: 92%;
            max-width: 820px;
            max-height: 85vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .28)
        }

        .bbsu-modal-head {
            background: var(--g);
            color: #fff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .bbsu-modal-head h3 {
            margin: 0;
            font-size: .95rem;
            font-weight: 700
        }

        .bbsu-modal-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            line-height: 1
        }

        .bbsu-modal-body {
            padding: 18px;
            overflow-y: auto;
            flex: 1
        }

        /* ── MONTH / YEAR PICKER ── */
        .month-picker-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap
        }

        .month-picker-row label {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--txtmu);
            text-transform: uppercase;
            letter-spacing: .6px;
            white-space: nowrap
        }

        .month-select {
            padding: 6px 26px 6px 10px !important;
            font-size: 12.5px !important;
            border-radius: 8px;
            border: 1.5px solid var(--bdr);
            background: var(--white);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: var(--g);
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b849a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 7px center;
            transition: border-color .18s
        }

        .month-select:focus {
            outline: none;
            border-color: var(--g);
            box-shadow: 0 0 0 3px rgba(26, 79, 122, .09)
        }

        .month-select.ms-month {
            min-width: 120px
        }

        .month-select.ms-year {
            min-width: 80px
        }

        .picker-divider {
            font-size: 13px;
            color: var(--bdr);
            font-weight: 700;
            line-height: 1
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
    </style>
@endpush

@section('content')

    <div class="ph">
        <div>
            <h2>BBSU Dashboard &amp; Report</h2>
            <p>Battery Breaking &amp; Separation Unit — scorecards, production charts &amp; batch records</p>
        </div>
    </div>

    <div class="tab-bar">
        <button class="tab-btn active" onclick="switchTab('dashboard',this)">📊 Dashboard</button>
        <button class="tab-btn" onclick="switchTab('report',this)">📋 Report</button>
    </div>

    {{-- ═══════════════════════════════ DASHBOARD TAB ═══════════════════════════════ --}}
    <div id="tab-dashboard" class="tab-pane active">

        <div class="card" id="dashboardCard">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                    </svg>
                    <span>BBSU Dashboard</span>
                </div>
                {{-- Month / Year picker — dashboard tab only --}}
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <div class="month-picker-row" id="monthPickerWrap">
                        <label>Period</label>
                        <select class="month-select ms-month" id="dashMonthPicker" onchange="onMonthPickerChange()">
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <!-- <span class="picker-divider">/</span> -->
                        <select class="month-select ms-year" id="dashYearPicker" onchange="onMonthPickerChange()"></select>
                    </div>
                    <div style="display:none">
                        <span id="dashMonthBadge" class="dash-month-badge" style="display:none">—</span>
                    </div>
                </div>
            </div>
            <div class="card-body">

                {{-- SCORECARDS --}}
                <div class="dash-section-title">Production Scorecards <span class="dash-month-badge"
                        id="dashMonthBadge2">Current Month</span></div>
                <div class="sc-grid" id="scGrid">
                    <div class="sc-chip c-navy"><span class="skel" style="width:70%;height:10px;display:block"></span><span
                            class="skel" style="width:50%;height:26px;display:block;margin-top:6px"></span></div>
                    <div class="sc-chip c-blue"><span class="skel" style="width:70%;height:10px;display:block"></span><span
                            class="skel" style="width:50%;height:26px;display:block;margin-top:6px"></span></div>
                    <div class="sc-chip c-teal"><span class="skel" style="width:70%;height:10px;display:block"></span><span
                            class="skel" style="width:50%;height:26px;display:block;margin-top:6px"></span></div>
                    <div class="sc-chip c-amber"><span class="skel" style="width:70%;height:10px;display:block"></span><span
                            class="skel" style="width:50%;height:26px;display:block;margin-top:6px"></span></div>
                    <div class="sc-chip c-red"><span class="skel" style="width:70%;height:10px;display:block"></span><span
                            class="skel" style="width:50%;height:26px;display:block;margin-top:6px"></span></div>
                </div>

                {{-- OUTPUT MATERIALS --}}
                <div class="dash-section-title">Output Materials — Current Month <span class="dash-month-badge"
                        id="dashMonthBadge3">Current Month</span></div>
                <div class="mat-strip" id="matStrip">
                    <div class="mat-card"><span class="skel" style="width:80%;height:10px;display:block"></span><span
                            class="skel" style="width:50%;height:20px;display:block;margin-top:6px"></span></div>
                    <div class="mat-card"><span class="skel" style="width:80%;height:10px;display:block"></span><span
                            class="skel" style="width:50%;height:20px;display:block;margin-top:6px"></span></div>
                    <div class="mat-card"><span class="skel" style="width:80%;height:10px;display:block"></span><span
                            class="skel" style="width:50%;height:20px;display:block;margin-top:6px"></span></div>
                </div>

                {{-- CHARTS --}}
                <div class="dash-section-title">Production &amp; Hours Charts</div>

                <div class="chart-card">
                    <div class="chart-card-head">
                        <span class="chart-card-title">Production Comparison</span>
                        <div class="ctrl-row">
                            <button class="ctrl-btn active" id="btn-weekly" onclick="setMode('weekly')">Weekly</button>
                            <button class="ctrl-btn" id="btn-daily" onclick="setMode('daily')">Daily</button>
                            <button class="ctrl-btn active" id="btn-m1" onclick="setMonths(1)">1M</button>
                            <button class="ctrl-btn" id="btn-m2" onclick="setMonths(2)">2M</button>
                            <button class="ctrl-btn" id="btn-m3" onclick="setMonths(3)">3M</button>
                        </div>
                    </div>
                    <canvas id="prodChart"></canvas>
                </div>

                <div class="chart-card">
                    <div class="chart-card-head">
                        <span class="chart-card-title">Daily Avg Hours / MT</span>
                        <span style="font-size:10px;color:var(--txtmu)">Total Hrs ÷ (Total Input KG / 1000)</span>
                    </div>
                    <canvas id="hoursChart"></canvas>
                    <div id="hoursEmpty" class="dash-empty" style="display:none">No hours data for selected month</div>
                </div>

                <div class="chart-card">
                    <div class="chart-card-head">
                        <span class="chart-card-title">PWR Consumption / MT</span>
                        <span style="font-size:10px;color:var(--txtmu)">Total Power ÷ (Total Input KG / 1000)</span>
                    </div>
                    <canvas id="pwrChart"></canvas>
                    <div id="pwrEmpty" class="dash-empty" style="display:none">No power data for selected month</div>
                </div>

                {{-- LAST DAY BATCHES --}}
                <div class="dash-section-title">Last Day Batches</div>
                <div class="card" style="margin-bottom:0">
                    <div class="card-head" style="padding:10px 20px">
                        <div class="card-head-left">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span id="lastDayTitle">Last Day Batches</span>
                        </div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="last-day-tbl">
                            <thead>
                                <tr>
                                    <th>Batch ID</th>
                                    <th>Category</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th class="num">Total Weight (KG)</th>
                                    <th class="num">Acid %</th>
                                    <th class="num">Duration (Hrs)</th>
                                    <th style="text-align:center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="lastDayList">
                                <tr>
                                    <td colspan="8" style="text-align:center;padding:24px;color:var(--txtmu)"><span
                                            class="spinner"></span>Loading…</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="padding:10px 20px;border-top:1px solid var(--bdr);text-align:center">
                        <button onclick="switchTab('report', document.querySelectorAll('.tab-btn')[1])"
                            style="background:none;border:none;color:var(--g);font-size:12px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;letter-spacing:.3px;text-transform:uppercase">
                            VIEW FULL BATCH HISTORY →
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>{{-- /dashboard --}}

    {{-- ═══════════════════════════════ REPORT TAB ═══════════════════════════════ --}}
    <div id="tab-report" class="tab-pane">

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
                    <div class="field"><label>Category</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path
                                    d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                            </svg><select id="f_category" onchange="onFilterChange()">
                                <option value="">All Categories</option>
                            </select></div>
                    </div>
                    <div class="field"><label>Batch No</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg><input type="text" id="f_batch_no" placeholder="Search batch…" oninput="onFilterChange()">
                        </div>
                    </div>
                    <div class="field"><label>Status</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg><select id="f_status" onchange="onFilterChange()">
                                <option value="">All</option>
                                <option value="0">Pending</option>
                                <option value="1">In Progress</option>
                                <option value="2">Completed</option>
                                <option value="3">Cancelled</option>
                            </select></div>
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

        <div class="card">
            <div class="card-head">
                <div class="card-head-left"><svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <line x1="3" y1="9" x2="21" y2="9" />
                        <line x1="3" y1="15" x2="21" y2="15" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg><span>Batch Records</span></div>
                <div style="display:flex;align-items:center;gap:10px">
                    <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
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
            <div class="tbl-wrap" style="border-radius:0;border:none">
                <table class="dt" id="reportTable">
                    <thead>
                        <tr>
                            <th class="sortable" data-col="doc_date" onclick="sortBy('doc_date')"># Date<span
                                    class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                        <polyline points="18 15 12 9 6 15" />
                                    </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                        <polyline points="6 9 12 15 18 9" />
                                    </svg></span></th>
                            <th class="sortable" data-col="batch_no" onclick="sortBy('batch_no')">Batch No<span
                                    class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                        <polyline points="18 15 12 9 6 15" />
                                    </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                        <polyline points="6 9 12 15 18 9" />
                                    </svg></span></th>
                            <th class="sortable" data-col="category" onclick="sortBy('category')">Category<span
                                    class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                        <polyline points="18 15 12 9 6 15" />
                                    </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                        <polyline points="6 9 12 15 18 9" />
                                    </svg></span></th>
                            <th>Start</th>
                            <th>End</th>
                            <th style="text-align:center">Status</th>
                            <th class="sortable num" data-col="total_input_qty" onclick="sortBy('total_input_qty')">Input
                                (KG)<span class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                        <polyline points="18 15 12 9 6 15" />
                                    </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                        <polyline points="6 9 12 15 18 9" />
                                    </svg></span></th>
                            <th class="num">Acid %</th>
                            <th class="num">Init Pwr</th>
                            <th class="num">Final Pwr</th>
                            <th class="sortable num" data-col="total_power_hrs" onclick="sortBy('total_power_hrs')">Pwr
                                Hrs<span class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24">
                                        <polyline points="18 15 12 9 6 15" />
                                    </svg><svg class="ico-desc" viewBox="0 0 24 24">
                                        <polyline points="6 9 12 15 18 9" />
                                    </svg></span></th>
                            <th>Output Materials</th>
                        </tr>
                    </thead>
                    <tbody id="reportBody">
                        <tr class="state-row">
                            <td colspan="12"><span class="spinner"></span>Loading…</td>
                        </tr>
                    </tbody>
                    <tfoot id="reportFoot" style="display:none">
                        <tr>
                            <td colspan="6"
                                style="text-align:right;font-size:10.5px;letter-spacing:.5px;color:var(--txtmu)">PAGE TOTAL
                            </td>
                            <td class="num" id="footInput"></td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="pag" id="pagBar" style="display:none">
                <span class="pag-info" id="pagInfo"></span>
                <div class="pag-btns" id="pagBtns"></div>
            </div>
        </div>

    </div>{{-- /report --}}

    {{-- DRILLDOWN MODAL --}}
    <div class="bbsu-modal-overlay" id="drillModal">
        <div class="bbsu-modal">
            <div class="bbsu-modal-head">
                <h3 id="drillTitle">Day Detail</h3>
                <button class="bbsu-modal-close" onclick="closeDrill()">✕</button>
            </div>
            <div class="bbsu-modal-body" id="drillBody">
                <div style="text-align:center;padding:30px"><span class="spinner"></span></div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        const API_BBSU = {
            filters: '/reports/bbsu/filters',
            dashboard: '/reports/bbsu/dashboard',
            chart: '/reports/bbsu/chart',
            report: '/reports/bbsu/report',
            drilldown: '/reports/bbsu/drilldown',
        };

        let currentPage = 1, currentSort = 'doc_date', currentDir = 'desc';
        let filterTimer = null, chartMode = 'weekly', chartMonths = 1;
        let prodChart = null, hoursChart = null, pwrChart = null;

        // ── driven by the two separate dropdowns ──
        let dashMonth = new Date().getMonth() + 1;
        let dashYear = new Date().getFullYear();

        const STATUS_MAP = { 0: 'Pending', 1: 'In Progress', 2: 'Completed', 3: 'Cancelled' };
        const STATUS_CLS = { 0: 'st-pending', 1: 'st-progress', 2: 'st-completed', 3: 'st-cancelled' };
        const COLORS = ['#1560bd', '#00b4aa', '#e8a020', '#dc3545', '#6f42c1', '#28a745', '#fd7e14'];

        // ── INIT ──────────────────────────────────────────────────────────
        (async function init() {
            // Build year dropdown: current year down to current-4
            const yearSel = document.getElementById('dashYearPicker');
            for (let y = dashYear; y >= dashYear - 4; y--) {
                const o = document.createElement('option');
                o.value = y; o.textContent = y;
                if (y === dashYear) o.selected = true;
                yearSel.appendChild(o);
            }
            // Pre-select current month
            document.getElementById('dashMonthPicker').value = dashMonth;

            await loadFilters();
            await loadReport();
            await loadDashboard();
            await loadChart();
        })();

        // ── TABS ──────────────────────────────────────────────────────────
        function switchTab(tab, btn) {
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            btn.classList.add('active');
        }

        // ── MONTH / YEAR PICKER — fires on either dropdown change ─────────
        function onMonthPickerChange() {
            dashMonth = parseInt(document.getElementById('dashMonthPicker').value);
            dashYear = parseInt(document.getElementById('dashYearPicker').value);
            loadDashboard();
            loadChart();
        }

        // ── FILTERS (categories only — no available_months needed) ────────
        async function loadFilters() {
            const res = await apiFetch(API_BBSU.filters);
            if (!res?.ok) return;
            const { data } = await res.json();
            const catSel = document.getElementById('f_category');
            (data.categories ?? []).forEach(c => {
                const o = document.createElement('option'); o.value = c; o.textContent = c;
                catSel.appendChild(o);
            });
        }

        // ── DASHBOARD ─────────────────────────────────────────────────────
        async function loadDashboard() {
            document.getElementById('dashMonthBadge').style.display = 'none';
            let res;
            try { res = await apiFetch(`${API_BBSU.dashboard}?month=${dashMonth}&year=${dashYear}`); }
            catch (e) { console.error('[BBSU Dash]', e); return; }
            if (!res?.ok) return;
            let json;
            try { json = await res.json(); } catch (e) { return; }
            if (json.status === 'error') { console.error('[BBSU Dash error]', json.message); return; }

            const d = json.data ?? {};
            const ml = d.month_label ?? '';

            renderScorecards(d);
            renderMatStrip(d.output_materials ?? []);
            renderLastDay(d.last_day_batches ?? [], d.last_day_date ?? '');

            const badge = document.getElementById('dashMonthBadge');
            badge.textContent = ml; badge.style.display = '';
            ['dashMonthBadge2', 'dashMonthBadge3'].forEach(id => {
                const el = document.getElementById(id); if (el) el.textContent = ml;
            });
        }

        function renderScorecards(d) {
            const grid = document.getElementById('scGrid');
            const chips = [
                { label: 'Prev Month Total', val: fmtNum(d.last_month_total), sub: (d.last_month_label || '') + ' · KG', cls: 'c-navy', icon: `<rect x="1" y="6" width="18" height="12" rx="2"/><line x1="23" y1="10" x2="23" y2="14"/>`, cats: d.last_month_by_category ?? [] },
                { label: 'Selected Month Total', val: fmtNum(d.current_month_total), sub: (d.month_label || '') + ' · KG', cls: 'c-blue', icon: `<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>`, cats: d.current_month_by_category ?? [] },
                { label: 'Year Total', val: fmtNum(d.year_total), sub: 'KG · Year ' + (d.selected_year ?? new Date().getFullYear()), cls: 'c-teal', icon: `<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/>`, cats: [] },
                { label: 'Avg HR / MT', val: d.avg_hr_per_mt, sub: 'Hrs per MT · batch duration (start→end)', cls: 'c-amber', icon: `<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>`, cats: [] },
                { label: 'Avg Acid %', val: d.avg_acid_pct + '%', sub: 'Σ(Input KG × Acid%) ÷ Σ(Input KG)', cls: 'c-red', icon: `<path d="M10 2v7.31l-3.72 6.17A4 4 0 0 0 10 22h4a4 4 0 0 0 3.72-6.52L14 9.31V2"/>`, cats: [] },
            ];
            grid.innerHTML = chips.map(c => `
                <div class="sc-chip ${c.cls}">
                    <svg class="sc-chip-ico" viewBox="0 0 24 24">${c.icon}</svg>
                    <span class="sc-chip-label">${escHtml(c.label)}</span>
                    <span class="sc-chip-val">${c.val}</span>
                    <span class="sc-chip-sub">${escHtml(c.sub)}</span>
                    ${c.cats.length ? `<div class="cat-pills-row">${c.cats.map(x => `<span class="cat-pill">${escHtml(x.category || '—')}: ${fmtNum(x.total_qty)} KG</span>`).join('')}</div>` : ''}
                </div>`).join('');
        }

        function renderMatStrip(materials) {
            const wrap = document.getElementById('matStrip');
            if (!materials.length) { wrap.innerHTML = `<div class="dash-empty" style="width:100%">No output material data for selected month</div>`; return; }
            wrap.innerHTML = materials.map(m => `
                <div class="mat-card">
                    <div class="mat-card-name" title="${escHtml(m.material_name || m.material_code)}">${escHtml(m.material_name || m.material_code)}</div>
                    <div class="mat-card-val">${fmtNum(m.total_qty)}</div>
                    <div class="mat-card-unit">KG</div>
                </div>`).join('');
        }

        function renderLastDay(batches, dateStr) {
            const tbody = document.getElementById('lastDayList');
            const title = document.getElementById('lastDayTitle');
            if (dateStr && title) title.textContent = `Last Day Batches (${dateStr})`;

            if (!batches.length) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:24px;color:var(--txtmu)">No batch data for last day of selected month</td></tr>`;
                return;
            }

            tbody.innerHTML = batches.map(b => {
                const statusNum = b.status ?? 2;
                const statusMap = { 0: 'Pending', 1: 'Processing', 2: 'Completed', 3: 'Cancelled' };
                const statusCls = { 0: 'st-pending', 1: 'st-progress', 2: 'st-completed', 3: 'st-cancelled' };
                const statusLabel = statusMap[statusNum] ?? 'Completed';
                const statusClass = statusCls[statusNum] ?? 'st-completed';

                const acidVal = parseFloat(b.avg_acid ?? 0);
                const acidCls = acidVal > 10 ? 'acid-high' : acidVal > 7 ? 'acid-mid' : 'acid-ok';

                return `<tr>
                    <td style="font-weight:700">${escHtml(b.batch_no)}</td>
                    <td>${escHtml(b.category || '—')}</td>
                    <td>${escHtml(b.start_time || '—')}</td>
                    <td>${escHtml(b.end_time || '—')}</td>
                    <td class="num" style="font-weight:700">${fmtNum(b.total_input)}</td>
                    <td class="num ${acidCls}">${fmtNum(b.avg_acid)}%</td>
                    <td class="num">${fmtNum(b.total_hrs)}</td>
                    <td style="text-align:center"><span class="badge-status ${statusClass}">● ${statusLabel}</span></td>
                </tr>`;
            }).join('');
        }

        // ── CHARTS ────────────────────────────────────────────────────────
        async function loadChart() {
            let res;
            try { res = await apiFetch(`${API_BBSU.chart}?mode=${chartMode}&months=${chartMonths}&month=${dashMonth}&year=${dashYear}`); }
            catch (e) { return; }
            if (!res?.ok) return;
            const json = await res.json();
            renderProdChart(json);
            renderHoursChart(json.avg_hours_per_day ?? []);
            renderPwrChart(json.pwr_per_day ?? []);
        }

        function renderProdChart(json) {
            const allLabels = [...new Set((json.datasets || []).flatMap(d => Object.keys(d.data || {})))];
            const datasets = (json.datasets || []).map((ds, i) => ({
                label: ds.label,
                data: allLabels.map(l => ds.data[l] || 0),
                borderColor: COLORS[i % COLORS.length],
                backgroundColor: COLORS[i % COLORS.length] + '22',
                borderWidth: 2.5,
                pointRadius: allLabels.length > 20 ? 2 : 5,
                pointHoverRadius: 7,
                tension: .35, fill: false,
            }));
            const ctx = document.getElementById('prodChart').getContext('2d');
            if (prodChart) prodChart.destroy();
            prodChart = new Chart(ctx, {
                type: 'line',
                data: { labels: allLabels, datasets },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 }, padding: 14 } } },
                    onClick: (e, elements) => {
                        if (elements.length && chartMode === 'daily') {
                            const day = allLabels[elements[0].index];
                            openDrill(`${dashYear}-${String(dashMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`);
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#e5edf4' }, ticks: { font: { size: 11 }, maxTicksLimit: 6, callback: v => v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 0, maxTicksLimit: 10 } },
                    }
                }
            });
        }

        function renderHoursChart(data) {
            const ctx = document.getElementById('hoursChart').getContext('2d');
            const emptyEl = document.getElementById('hoursEmpty');
            if (hoursChart) hoursChart.destroy();

            const hasData = data.some(d => parseFloat(d.avg_hrs) > 0);
            if (!data.length || !hasData) {
                ctx.canvas.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'block';
                return;
            }
            ctx.canvas.style.display = '';
            if (emptyEl) emptyEl.style.display = 'none';

            hoursChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => 'D' + d.day),
                    datasets: [{
                        label: 'Avg Hrs / MT',
                        data: data.map(d => parseFloat(d.avg_hrs) || 0),
                        borderColor: '#1560bd',
                        backgroundColor: 'rgba(21,96,189,.10)',
                        borderWidth: 2,
                        pointRadius: data.length > 20 ? 2 : 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#1560bd',
                        tension: 0.35, fill: true, spanGaps: true,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: items => 'Day ' + items[0].label.replace('D', ''),
                                label: ctx => `${ctx.parsed.y.toFixed(4)} Hrs/MT`,
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#e5edf4' }, ticks: { font: { size: 10 }, callback: v => v.toFixed(1), maxTicksLimit: 6 }, title: { display: true, text: 'Hrs / MT', font: { size: 10 }, color: 'var(--txtmu)' } },
                        x: { grid: { display: false }, ticks: { font: { size: 9 }, maxTicksLimit: 16, maxRotation: 0 } },
                    }
                }
            });
        }

        function renderPwrChart(data) {
            const canvas = document.getElementById('pwrChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const emptyEl = document.getElementById('pwrEmpty');
            if (pwrChart) pwrChart.destroy();

            const hasData = data.some(d => parseFloat(d.pwr_per_mt) > 0);
            if (!data.length || !hasData) {
                canvas.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'block';
                return;
            }
            canvas.style.display = '';
            if (emptyEl) emptyEl.style.display = 'none';

            pwrChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => 'D' + d.day),
                    datasets: [{
                        label: 'PWR / MT',
                        data: data.map(d => parseFloat(d.pwr_per_mt) || 0),
                        borderColor: '#e8a020',
                        backgroundColor: 'rgba(232,160,32,.10)',
                        borderWidth: 2,
                        pointRadius: data.length > 20 ? 2 : 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#e8a020',
                        tension: 0.35, fill: true, spanGaps: true,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: items => 'Day ' + items[0].label.replace('D', ''),
                                label: ctx => `${ctx.parsed.y.toFixed(4)} PWR/MT`,
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#e5edf4' }, ticks: { font: { size: 10 }, callback: v => v.toFixed(0), maxTicksLimit: 6 }, title: { display: true, text: 'PWR / MT', font: { size: 10 }, color: 'var(--txtmu)' } },
                        x: { grid: { display: false }, ticks: { font: { size: 9 }, maxTicksLimit: 16, maxRotation: 0 } },
                    }
                }
            });
        }

        function setMode(mode) {
            chartMode = mode;
            document.getElementById('btn-weekly').classList.toggle('active', mode === 'weekly');
            document.getElementById('btn-daily').classList.toggle('active', mode === 'daily');
            loadChart();
        }
        function setMonths(n) {
            chartMonths = n;
            [1, 2, 3].forEach(m => document.getElementById('btn-m' + m).classList.toggle('active', m === n));
            loadChart();
        }

        // ── REPORT TABLE ──────────────────────────────────────────────────
        async function loadReport(page = 1) {
            currentPage = page; setLoading(true);
            const params = new URLSearchParams({
                date_from: document.getElementById('f_date_from').value,
                date_to: document.getElementById('f_date_to').value,
                category: document.getElementById('f_category').value,
                batch_no: document.getElementById('f_batch_no').value,
                status: document.getElementById('f_status').value,
                sort_by: currentSort, sort_dir: currentDir,
                per_page: document.getElementById('f_per_page').value,
                page: page,
            });
            [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
            const res = await apiFetch(`${API_BBSU.report}?${params.toString()}`);
            setLoading(false);
            if (!res?.ok) { renderError(); return; }
            const json = await res.json();
            renderTable(json.data ?? [], json.meta);
            renderPagination(json.meta);
            updateSortHeaders();
        }

        function renderTable(rows, meta) {
            const tbody = document.getElementById('reportBody'), tfoot = document.getElementById('reportFoot');
            if (!rows.length) { tbody.innerHTML = `<tr class="state-row"><td colspan="12">No records found.</td></tr>`; tfoot.style.display = 'none'; document.getElementById('tableCaption').textContent = ''; return; }
            let totalInput = 0;
            const offset = ((meta?.current_page || 1) - 1) * (meta?.per_page || 50);
            tbody.innerHTML = rows.map((r, i) => {
                totalInput += parseFloat(r.total_input_qty || 0);
                const badge = `<span class="badge-status ${STATUS_CLS[r.status] || 'st-pending'}">● ${escHtml(r.status_label)}</span>`;
                const mats = (r.output_materials || []).map(m => `<span class="mat-pill">${escHtml(m.material_name || m.material_code)}: ${fmtNum(m.qty)}</span>`).join('') || '—';
                return `<tr>
                    <td>${offset + i + 1} &nbsp;<small style="color:var(--txtmu)">${escHtml(r.doc_date)}</small></td>
                    <td style="font-weight:600">${escHtml(r.batch_no)}</td>
                    <td>${escHtml(r.category)}</td>
                    <td>${escHtml(r.start_time)}</td>
                    <td>${escHtml(r.end_time)}</td>
                    <td style="text-align:center">${badge}</td>
                    <td class="num">${fmtNum(r.total_input_qty)}</td>
                    <td class="num">${fmtNum(r.avg_acid_pct)}%</td>
                    <td class="num">${fmtNum(r.initial_power)}</td>
                    <td class="num">${fmtNum(r.final_power)}</td>
                    <td class="num">${fmtNum(r.total_power_hrs)}</td>
                    <td>${mats}</td>
                </tr>`;
            }).join('');
            document.getElementById('footInput').textContent = fmtNum(totalInput);
            tfoot.style.display = '';
            document.getElementById('tableCaption').textContent = meta ? `Showing ${rows.length} of ${meta.total.toLocaleString()} records` : `${rows.length} records`;
        }

        function renderPagination(meta) {
            const bar = document.getElementById('pagBar'), info = document.getElementById('pagInfo'), btns = document.getElementById('pagBtns');
            if (!meta || meta.last_page <= 1) { bar.style.display = 'none'; return; }
            bar.style.display = 'flex';
            info.textContent = `${(meta.current_page - 1) * meta.per_page + 1}–${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total.toLocaleString()}`;
            const pages = paginationRange(meta.current_page, meta.last_page);
            btns.innerHTML = [
                `<button class="pag-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="loadReport(${meta.current_page - 1})">‹</button>`,
                ...pages.map(p => p === '…' ? `<button class="pag-btn" disabled>…</button>` : `<button class="pag-btn${p === meta.current_page ? ' active' : ''}" onclick="loadReport(${p})">${p}</button>`),
                `<button class="pag-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="loadReport(${meta.current_page + 1})">›</button>`,
            ].join('');
        }
        function paginationRange(cur, last) { const delta = 2, range = []; for (let i = Math.max(2, cur - delta); i <= Math.min(last - 1, cur + delta); i++)range.push(i); if (range[0] > 2) range.unshift('…'); if (range[range.length - 1] < last - 1) range.push('…'); range.unshift(1); if (last !== 1) range.push(last); return range; }
        function sortBy(col) { currentDir = currentSort === col ? (currentDir === 'asc' ? 'desc' : 'asc') : 'desc'; currentSort = col; loadReport(1); }
        function updateSortHeaders() { document.querySelectorAll('#reportTable thead th').forEach(th => { th.classList.remove('sort-asc', 'sort-desc'); if (th.dataset.col === currentSort) th.classList.add(currentDir === 'asc' ? 'sort-asc' : 'sort-desc'); }); }
        function onFilterChange() { clearTimeout(filterTimer); filterTimer = setTimeout(() => loadReport(1), 350); }
        function resetFilters() { ['f_date_from', 'f_date_to', 'f_batch_no'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });['f_category', 'f_status'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; }); document.getElementById('f_per_page').value = '50'; currentSort = 'doc_date'; currentDir = 'desc'; loadReport(1); }
        function setLoading(on) { if (on) { document.getElementById('reportBody').innerHTML = `<tr class="state-row"><td colspan="12"><span class="spinner"></span>Loading…</td></tr>`; document.getElementById('reportFoot').style.display = 'none'; document.getElementById('pagBar').style.display = 'none'; } }
        function renderError() { document.getElementById('reportBody').innerHTML = `<tr class="state-row"><td colspan="12" style="color:var(--err)">Failed to load data.</td></tr>`; }

        // ── EXPORT EXCEL ──────────────────────────────────────────────────
        async function exportExcel() {
            const btn = document.getElementById('btnExcel');
            btn.disabled = true; btn.innerHTML = `<span class="spinner" style="border-top-color:#fff"></span> Exporting…`;
            const rows = await fetchAllForExport();
            if (!rows.length) { btn.disabled = false; btn.innerHTML = 'Export Excel'; return; }
            const allMatNames = [...new Set(rows.flatMap(r => (r.output_materials || []).map(m => m.material_name || m.material_code)))].sort();
            const wsData = [
                ['Batch No', 'Doc Date', 'Category', 'Start', 'End', 'Status', 'Input (KG)', 'Weighted Avg Acid %', 'Init Pwr', 'Final Pwr', 'Pwr Hrs', ...allMatNames.map(n => n + ' (KG)')],
                ...rows.map(r => { const matMap = Object.fromEntries((r.output_materials || []).map(m => [m.material_name || m.material_code, m.qty])); return [r.batch_no, r.doc_date, r.category, r.start_time, r.end_time, r.status_label, r.total_input_qty, r.avg_acid_pct, r.initial_power, r.final_power, r.total_power_hrs, ...allMatNames.map(n => matMap[n] ?? 0)]; }),
            ];
            const wb = XLSX.utils.book_new(), ws = XLSX.utils.aoa_to_sheet(wsData);
            ws['!cols'] = wsData[0].map(h => ({ wch: Math.max(14, String(h).length + 2) }));
            XLSX.utils.book_append_sheet(wb, ws, 'BBSU Report');
            XLSX.writeFile(wb, `BBSU_Report_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.xlsx`);
            btn.disabled = false;
            btn.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Export Excel`;
        }
        async function fetchAllForExport() { const all = []; let page = 1, lastPage = 1; do { const params = new URLSearchParams({ date_from: document.getElementById('f_date_from').value, date_to: document.getElementById('f_date_to').value, category: document.getElementById('f_category').value, batch_no: document.getElementById('f_batch_no').value, status: document.getElementById('f_status').value, sort_by: currentSort, sort_dir: currentDir, per_page: 500, page: page });[...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); }); const res = await apiFetch(`${API_BBSU.report}?${params.toString()}`); if (!res?.ok) break; const json = await res.json(); all.push(...(json.data ?? [])); lastPage = json.meta?.last_page ?? 1; page++; } while (page <= lastPage); return all; }

        // ── DRILLDOWN ─────────────────────────────────────────────────────
        async function openDrill(date) {
            const modal = document.getElementById('drillModal'), body = document.getElementById('drillBody');
            document.getElementById('drillTitle').textContent = 'Loading…';
            modal.classList.add('open');
            body.innerHTML = `<div style="text-align:center;padding:30px"><span class="spinner"></span></div>`;
            try {
                const res = await apiFetch(`${API_BBSU.drilldown}?date=${date}`);
                const json = await res.json();
                document.getElementById('drillTitle').textContent = `Batches — ${json.date || date}`;
                if (!json.batches?.length) { body.innerHTML = `<div class="dash-empty">No batches for this date.</div>`; return; }
                body.innerHTML = `<p style="font-size:11px;color:var(--txtmu);margin-bottom:12px">${json.batches.length} batch(es) on ${json.date || date}</p>
                    <div style="overflow-x:auto"><table class="dt">
                    <thead><tr><th>Batch No</th><th>Category</th><th>Start</th><th>End</th><th class="num">Input (KG)</th><th class="num">Acid %</th><th class="num">Pwr Hrs</th><th>Output Materials</th></tr></thead>
                    <tbody>${json.batches.map(b => `<tr>
                        <td style="font-weight:600">${escHtml(b.batch_no)}</td><td>${escHtml(b.category)}</td>
                        <td>${escHtml(b.start_time)}</td><td>${escHtml(b.end_time)}</td>
                        <td class="num">${fmtNum(b.total_input)}</td><td class="num">${fmtNum(b.avg_acid)}%</td>
                        <td class="num">${fmtNum(b.total_hrs)}</td>
                        <td>${(b.output_materials || []).map(m => `<span class="mat-pill">${escHtml(m.material_name || m.material_code)}: ${fmtNum(m.qty)} (${fmtNum(m.yield_pct)}%)</span>`).join('') || '—'}</td>
                    </tr>`).join('')}</tbody></table></div>`;
            } catch (e) { body.innerHTML = `<div style="color:var(--err);padding:20px">Error: ${escHtml(e.message)}</div>`; }
        }
        function closeDrill() { document.getElementById('drillModal').classList.remove('open'); }
        document.getElementById('drillModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeDrill(); });

        // ── UTILS ─────────────────────────────────────────────────────────
        function fmtNum(n) { if (n === null || n === undefined || n === '') return '—'; return parseFloat(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        function escHtml(str) { if (str === null || str === undefined) return '—'; return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    </script>
@endpush
