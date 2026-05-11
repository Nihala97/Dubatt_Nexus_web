{{-- resources/views/admin/reports/smelting_dashboard.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Smelting Dashboard & Report')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <span style="color:var(--text-muted)">Reports</span>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <strong>Smelting</strong>
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
            --amber: #f59e0b;
            --blue: #2563eb;
            --red: #ef4444;
            --purple: #7c3aed;
            --teal: #0d9488;
            --orange: #ea580c
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

        /* layout */
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
            letter-spacing: -.3px
        }

        .ph p {
            font-size: 12.5px;
            color: var(--txtmu);
            margin-top: 2px
        }

        /* ── month/year picker ── */
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
            padding: 6px 26px 6px 10px;
            font-size: 12.5px;
            border-radius: 8px;
            border: 1.5px solid var(--bdr);
            background: var(--white);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: var(--g);
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b8a78' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 7px center;
            transition: border-color .18s
        }

        .month-select:focus {
            outline: none;
            border-color: var(--g);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .09)
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

        /* buttons */
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
            transform: translateY(-1px)
        }

        .btn-primary {
            background: var(--g);
            color: #fff
        }

        .btn-primary:hover {
            background: var(--gd);
            transform: translateY(-1px)
        }

        .btn-view {
            padding: 4px 10px;
            font-size: 11px;
            border-radius: 6px;
            border: 1.5px solid var(--bdr);
            background: var(--gxl);
            color: var(--g);
            cursor: pointer;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            transition: .15s
        }

        .btn-view:hover {
            background: var(--gl);
            border-color: var(--g)
        }

        /* tabs */
        .tab-bar {
            display: flex;
            gap: 0;
            border-bottom: 2px solid var(--bdr);
            margin-bottom: 20px
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
            transition: all .15s;
            font-family: 'Outfit', sans-serif
        }

        .tab-btn.active {
            color: var(--g);
            border-bottom-color: var(--g)
        }

        .tab-btn:hover:not(.active) {
            color: var(--txtm)
        }

        /* card */
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

        /* scorecards */
        .sc-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 20px
        }

        .sc {
            border-radius: 12px;
            padding: 18px 20px;
            position: relative;
            overflow: hidden;
            transition: transform .18s, box-shadow .18s
        }

        .sc:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0, 0, 0, .12)
        }

        .sc.green {
            background: linear-gradient(135deg, #166534, #15803d);
            border: 1.5px solid #14532d
        }

        .sc.amber {
            background: linear-gradient(135deg, #92400e, #b45309);
            border: 1.5px solid #78350f
        }

        .sc.blue {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            border: 1.5px solid #1e3a8a
        }

        .sc.teal {
            background: linear-gradient(135deg, #0f766e, #0d9488);
            border: 1.5px solid #115e59
        }

        .sc-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .75);
            margin-bottom: 8px
        }

        .sc-val {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            letter-spacing: -1px
        }

        .sc-unit {
            font-size: 12px;
            color: rgba(255, 255, 255, .65);
            font-weight: 500;
            margin-left: 4px
        }

        .sc-sub {
            font-size: 10px;
            color: rgba(255, 255, 255, .55);
            margin-top: 5px
        }

        .sc-ico {
            position: absolute;
            right: 14px;
            top: 14px;
            width: 30px;
            height: 30px;
            stroke: rgba(255, 255, 255, .2);
            fill: none;
            stroke-width: 1.5
        }

        /* metrics */
        .metric-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 20px
        }

        .metric {
            background: var(--white);
            border: 1px solid var(--bdr);
            border-radius: 10px;
            padding: 14px 16px;
            text-align: center;
            transition: box-shadow .15s
        }

        .metric:hover {
            box-shadow: 0 4px 18px rgba(26, 122, 58, .1)
        }

        .metric-val {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            margin: 6px 0 4px
        }

        .metric-unit {
            font-size: 10px;
            font-weight: 700;
            color: var(--txtmu);
            text-transform: uppercase;
            letter-spacing: .5px
        }

        .metric-label {
            font-size: 10.5px;
            color: var(--txtm);
            font-weight: 600;
            margin-bottom: 4px
        }

        .metric-delta {
            font-size: 10px;
            margin-top: 4px;
            font-weight: 600
        }

        .delta-up {
            color: #16a34a
        }

        .delta-down {
            color: #dc2626
        }

        .delta-flat {
            color: var(--txtmu)
        }

        /* chart */
        .chart-wrap {
            width: 100%;
            position: relative
        }

        .chart-wrap canvas {
            position: absolute;
            inset: 0
        }

        /* filters */
        .fg {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px 16px
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 4px
        }

        .field label {
            font-size: 10px;
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
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
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
            padding: 8px 11px 8px 30px;
            border: 1.5px solid var(--bdr);
            border-radius: 7px;
            background: var(--gxl);
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            color: var(--txt);
            outline: none;
            appearance: none;
            transition: border-color .18s, background .18s
        }

        input:focus,
        select:focus {
            border-color: var(--g);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .08)
        }

        select {
            padding-right: 26px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b8a78' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center
        }

        /* table */
        .tbl-wrap {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--bdr)
        }

        .dt {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px
        }

        .dt thead th {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 9px 12px;
            border-bottom: 2px solid var(--bdr);
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
            transition: background .12s
        }

        .dt thead th:hover {
            background: #d9f0e2
        }

        .dt thead th.sort-asc::after {
            content: '↑';
            margin-left: 4px;
            color: var(--g)
        }

        .dt thead th.sort-desc::after {
            content: '↓';
            margin-left: 4px;
            color: var(--g)
        }

        .dt tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #edf2ef;
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
            font-size: 12px;
            color: var(--g);
            padding: 8px 12px;
            border-top: 2px solid var(--bdr)
        }

        .num {
            text-align: right;
            font-variant-numeric: tabular-nums
        }

        .badge-st {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700
        }

        .st-0 {
            background: #e0e7ff;
            color: #3730a3
        }

        .st-1 {
            background: #d1fae5;
            color: #065f46
        }

        /* pagination */
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
            gap: 4px
        }

        .pag-btn {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: 1.5px solid var(--bdr);
            background: var(--white);
            color: var(--txtm);
            cursor: pointer;
            transition: all .12s;
            font-family: 'Outfit', sans-serif
        }

        .pag-btn:hover:not(:disabled) {
            border-color: var(--g);
            color: var(--g)
        }

        .pag-btn.active {
            background: var(--g);
            color: #fff;
            border-color: var(--g)
        }

        .pag-btn:disabled {
            opacity: .35;
            cursor: default
        }

        /* misc */
        .spinner {
            display: inline-block;
            width: 17px;
            height: 17px;
            border: 2.5px solid var(--bdr);
            border-top-color: var(--g);
            border-radius: 50%;
            animation: spin .7s linear infinite;
            vertical-align: middle;
            margin-right: 5px
        }

        .skel {
            background: linear-gradient(90deg, var(--bdr) 25%, #e8f0eb 50%, var(--bdr) 75%);
            background-size: 200% 100%;
            animation: skel-pulse 1.2s ease infinite;
            border-radius: 4px
        }

        .state-row td {
            text-align: center;
            padding: 36px;
            color: var(--txtmu)
        }

        .summary-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 12px
        }

        .sbadge {
            background: var(--gl);
            border: 1px solid var(--bdr);
            border-radius: 8px;
            padding: 6px 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 110px
        }

        .sbadge-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: var(--txtmu)
        }

        .sbadge-val {
            font-size: 15px;
            font-weight: 800;
            color: var(--g);
            letter-spacing: -.5px
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        @media(max-width:900px) {
            .two-col {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:600px) {
            .sc-row {
                grid-template-columns: 1fr
            }
        }

        /* DETAIL MODAL */
        .detail-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 9999;
            align-items: center;
            justify-content: center
        }

        .detail-overlay.open {
            display: flex
        }

        .detail-modal {
            background: #fff;
            border-radius: 14px;
            width: 94%;
            max-width: 1000px;
            max-height: 88vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .28)
        }

        .detail-modal-head {
            background: var(--g);
            color: #fff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .detail-modal-head h3 {
            margin: 0;
            font-size: .95rem;
            font-weight: 700
        }

        .detail-modal-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            line-height: 1
        }

        .detail-modal-body {
            padding: 18px;
            overflow-y: auto;
            flex: 1
        }

        .detail-section {
            margin-bottom: 18px
        }

        .detail-section h4 {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--g);
            margin: 0 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--bdr)
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
            margin-bottom: 14px
        }

        .detail-kv {
            background: var(--gxl);
            border: 1px solid var(--bdr);
            border-radius: 8px;
            padding: 8px 12px
        }

        .detail-kv-label {
            font-size: 9.5px;
            color: var(--txtmu);
            font-weight: 600;
            letter-spacing: .4px;
            text-transform: uppercase
        }

        .detail-kv-val {
            font-size: 14px;
            font-weight: 700;
            color: var(--txt);
            margin-top: 2px
        }
    </style>
@endpush

@section('content')

    <div class="ph">
        <div>
            <h2>🔥 Smelter Section — Dashboard &amp; Report</h2>
            <p id="dashMonthLabel">Loading…</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
            <div class="month-picker-row" id="monthPickerWrap">
                <label>Period</label>
                <select class="month-select ms-month" id="dashMonthPicker" onchange="onMonthChange()">
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
                <span class="picker-divider">/</span>
                <select class="month-select ms-year" id="dashYearPicker" onchange="onMonthChange()"></select>
            </div>
            <button class="btn btn-excel btn-sm" id="btnExcel" onclick="exportExcel()" style="display:none">
                <svg viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    <div class="tab-bar">
        <button class="tab-btn active" id="tabDash" onclick="switchTab('dashboard')">📊 Dashboard</button>
        <button class="tab-btn" id="tabReport" onclick="switchTab('report')">📋 Report</button>
    </div>

    {{-- ═══════════ DASHBOARD TAB ═══════════ --}}
    <div id="panelDashboard">

        <div class="sc-row" id="scRow">
            <div class="sc green" style="opacity:.5">
                <div class="sc-label">Current Month Production</div>
                <div class="skel" style="width:120px;height:28px;display:block;margin-top:4px"></div>
            </div>
            <div class="sc amber" style="opacity:.5">
                <div class="sc-label">Previous Month</div>
                <div class="skel" style="width:120px;height:28px;display:block;margin-top:4px"></div>
            </div>
            <div class="sc blue" style="opacity:.5">
                <div class="sc-label">This Year Total</div>
                <div class="skel" style="width:120px;height:28px;display:block;margin-top:4px"></div>
            </div>
            <div class="sc teal" style="opacity:.5">
                <div class="sc-label">Expected Yield</div>
                <div class="skel" style="width:120px;height:28px;display:block;margin-top:4px"></div>
            </div>
        </div>

        <div class="metric-row" id="metricRow">
            <div class="metric">
                <div class="metric-label">Avg HR / MT</div>
                <div class="metric-val" id="mHr" style="color:var(--amber)">—</div>
                <div class="metric-unit">Hrs/MT</div>
                <div class="metric-delta" id="mHrDelta"></div>
            </div>
            <div class="metric">
                <div class="metric-label">Avg LPG / MT</div>
                <div class="metric-val" id="mLpg" style="color:var(--blue)">—</div>
                <div class="metric-unit">LTR/MT</div>
                <div class="metric-delta" id="mLpgDelta"></div>
            </div>
            <div class="metric">
                <div class="metric-label">Avg O₂ / MT</div>
                <div class="metric-val" id="mO2" style="color:var(--teal)">—</div>
                <div class="metric-unit">KG/MT</div>
                <div class="metric-delta" id="mO2Delta"></div>
            </div>
            <div class="metric">
                <div class="metric-label">Yield vs Expected</div>
                <div class="metric-val" id="mYield" style="color:var(--purple)">—</div>
                <div class="metric-unit">% diff</div>
                <div class="metric-delta" id="mYieldSub"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    <span>Day Wise Production Comparison (KG)</span>
                </div>
            </div>
            <div class="card-body" style="padding:16px 20px 20px">
                <div class="chart-wrap" style="height:300px"><canvas id="chartDayWise"></canvas></div>
            </div>
        </div>

        <div class="two-col">
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                            <polyline points="17 6 23 6 23 12" />
                        </svg>
                        <span>Expected Yield vs Actual Production</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px">
                        <select id="yieldMonthSel" onchange="loadYield()"
                            style="font-size:11px;padding:4px 22px 4px 8px;min-width:110px"></select>
                    </div>
                </div>
                <div class="card-body" style="padding:14px 20px 20px">
                    <div style="display:flex;gap:12px;margin-bottom:12px;flex-wrap:wrap">
                        <div class="sbadge"><span class="sbadge-label">Expected</span><span class="sbadge-val"
                                id="yieldExp">—</span></div>
                        <div class="sbadge"><span class="sbadge-label">Actual</span><span class="sbadge-val"
                                id="yieldAct">—</span></div>
                        <div class="sbadge"><span class="sbadge-label">Difference</span><span class="sbadge-val"
                                id="yieldDiff">—</span></div>
                    </div>
                    <div class="chart-wrap" style="height:240px"><canvas id="chartYield"></canvas></div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        <span>LPG &amp; O₂ Consumption / MT (6 Months)</span>
                    </div>
                </div>
                <div class="card-body" style="padding:14px 20px 20px">
                    <div class="chart-wrap" style="height:240px"><canvas id="chartConsumption"></canvas></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" />
                    </svg>
                    <span>Average Temperature Record — Selected Month</span>
                </div>
            </div>
            <div class="card-body" style="padding:14px 20px 20px">
                <div class="chart-wrap" style="height:260px"><canvas id="chartTemp"></canvas></div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                    </svg>
                    <span>Rotary Wise Breakdown — Selected Month</span>
                </div>
            </div>
            <div class="card-body">
                <div class="tbl-wrap">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Rotary</th>
                                <th class="num">Output (KG)</th>
                                <th class="num">Batches</th>
                                <th class="num">Avg / Batch (KG)</th>
                            </tr>
                        </thead>
                        <tbody id="rotaryTbody">
                            <tr class="state-row">
                                <td colspan="4"><span class="spinner"></span>Loading…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /panelDashboard --}}

    {{-- ═══════════ REPORT TAB ═══════════ --}}
    <div id="panelReport" style="display:none">

        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                    </svg>
                    <span>Filters</span>
                </div>
                <button class="btn btn-outline btn-sm" onclick="resetReportFilters()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="1 4 1 10 7 10" />
                        <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                    </svg>Reset
                </button>
            </div>
            <div class="card-body">
                <div class="fg">
                    <div class="field"><label>Date From</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg><input type="date" id="rf_from" onchange="loadReport()"></div>
                    </div>
                    <div class="field"><label>Date To</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg><input type="date" id="rf_to" onchange="loadReport()"></div>
                    </div>
                    <div class="field"><label>Batch No</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg><input type="text" id="rf_batch" placeholder="Search…" oninput="debounceReport()"></div>
                    </div>
                    <div class="field"><label>Charge No</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg><input type="text" id="rf_charge" placeholder="Search…" oninput="debounceReport()"></div>
                    </div>
                    <div class="field"><label>Rotary</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                            </svg><select id="rf_rotary" onchange="loadReport()">
                                <option value="">All Rotaries</option>
                            </select></div>
                    </div>
                    <div class="field"><label>Status</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg><select id="rf_status" onchange="loadReport()">
                                <option value="">All</option>
                                <option value="0">Draft</option>
                                <option value="1">Submitted</option>
                            </select></div>
                    </div>
                    <div class="field"><label>Rows / Page</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <line x1="8" y1="6" x2="21" y2="6" />
                                <line x1="8" y1="12" x2="21" y2="12" />
                                <line x1="8" y1="18" x2="21" y2="18" />
                            </svg><select id="rf_pp" onchange="loadReport()">
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                            </select></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <line x1="3" y1="9" x2="21" y2="9" />
                        <line x1="3" y1="15" x2="21" y2="15" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                    <span>Smelting Batch Records</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <span id="reportCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
                    <button class="btn btn-excel btn-sm" onclick="exportExcel()">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>Export
                    </button>
                </div>
            </div>
            <div style="padding:14px 20px 0;display:flex;gap:8px;flex-wrap:wrap" id="reportSummary"></div>
            <div class="tbl-wrap" style="border-radius:0;border:none">
                <table class="dt" id="reportTable">
                    <thead>
                        <tr>
                            <th onclick="sortReport('date')">Date</th>
                            <th onclick="sortReport('batch_no')">Batch No</th>
                            <th onclick="sortReport('charge_no')">Charge No</th>
                            <th onclick="sortReport('rotary_no')">Rotary</th>
                            <th>Start</th>
                            <th>End</th>
                            <th onclick="sortReport('output_qty')" class="num">Output (KG)</th>
                            <th class="num">Expected (KG)</th>
                            <th class="num">Yield %</th>
                            <th class="num">Raw Input (KG)</th>
                            <th class="num">Flux (KG)</th>
                            <th onclick="sortReport('lpg_consumption')" class="num">LPG (m³)</th>
                            <th onclick="sortReport('o2_consumption')" class="num">O₂ (m³)</th>
                            <th class="num">ID Fan</th>
                            <th class="num">Rotary Pwr</th>
                            <th class="num">Proc (min)</th>
                            <th class="num">Inside °C</th>
                            <th class="num">PGC °C</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th style="text-align:center">Detail</th>
                        </tr>
                    </thead>
                    <tbody id="reportBody">
                        <tr class="state-row">
                            <td colspan="21"><span class="spinner"></span>Loading…</td>
                        </tr>
                    </tbody>
                    <tfoot id="reportFoot" style="display:none">
                        <tr>
                            <td colspan="6" style="text-align:right;font-size:10px;letter-spacing:.5px;color:var(--txtmu)">
                                PAGE TOTAL</td>
                            <td class="num" id="ftOutput"></td>
                            <td class="num" id="ftExpected"></td>
                            <td></td>
                            <td class="num" id="ftRaw"></td>
                            <td class="num" id="ftFlux"></td>
                            <td class="num" id="ftLpg"></td>
                            <td class="num" id="ftO2"></td>
                            <td class="num" id="ftIdFan"></td>
                            <td class="num" id="ftRotPwr"></td>
                            <td colspan="6"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="pag" id="pagBar" style="display:none">
                <span class="pag-info" id="pagInfo"></span>
                <div class="pag-btns" id="pagBtns"></div>
            </div>
        </div>

    </div>{{-- /panelReport --}}

    {{-- DETAIL MODAL --}}
    <div class="detail-overlay" id="detailOverlay" onclick="if(event.target===this)closeDetail()">
        <div class="detail-modal">
            <div class="detail-modal-head">
                <h3 id="detailTitle">Batch Detail</h3>
                <div style="display:flex;gap:8px;align-items:center">
                    <button class="btn btn-excel btn-sm" id="detailExcelBtn" onclick="exportDetailExcel()"
                        style="padding:4px 10px;font-size:11px">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        Download Excel
                    </button>
                    <button class="detail-modal-close" onclick="closeDetail()">✕</button>
                </div>
            </div>
            <div class="detail-modal-body" id="detailBody">
                <div style="text-align:center;padding:30px"><span class="spinner"></span></div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // ════════════════════════════════════════════════════════════
        // STATE
        // ════════════════════════════════════════════════════════════
        let dashData = null;
        let reportPage = 1, reportSort = 'date', reportDir = 'desc';
        let reportTimer = null, reportAllRows = [];
        let charts = {}, yieldMonths = [];

        // Selected month/year — driven by two independent dropdowns
        let dashMonth = new Date().getMonth() + 1;
        let dashYear = new Date().getFullYear();

        // Current detail row for export
        let currentDetailRow = null;

        const C = {
            green: '#15803d', amber: '#b45309', blue: '#2563eb',
            teal: '#0d9488', red: '#dc2626', purple: '#7c3aed', orange: '#ea580c'
        };

        // ════════════════════════════════════════════════════════════
        // INIT — build year dropdown, pre-select current month, then load
        // ════════════════════════════════════════════════════════════
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
            await loadDashboard();
        })();

        // ════════════════════════════════════════════════════════════
        // FILTERS (rotary dropdown only — month picker is independent)
        // ════════════════════════════════════════════════════════════
        async function loadFilters() {
            const res = await apiFetch('/reports/smelting/filters');
            if (!res?.ok) return;
            const { data } = await res.json();

            // Rotary dropdown
            const sel = document.getElementById('rf_rotary');
            (data.rotaries ?? []).forEach(r => {
                const o = document.createElement('option');
                o.value = r; o.textContent = 'Rotary ' + r;
                sel.appendChild(o);
            });
        }

        // ── Month / Year picker — fires on either dropdown change ─────────
        function onMonthChange() {
            dashMonth = parseInt(document.getElementById('dashMonthPicker').value);
            dashYear = parseInt(document.getElementById('dashYearPicker').value);
            loadDashboard();
        }

        // ════════════════════════════════════════════════════════════
        // TAB SWITCH
        // ════════════════════════════════════════════════════════════
        function switchTab(tab) {
            document.getElementById('panelDashboard').style.display = tab === 'dashboard' ? '' : 'none';
            document.getElementById('panelReport').style.display = tab === 'report' ? '' : 'none';
            document.getElementById('tabDash').classList.toggle('active', tab === 'dashboard');
            document.getElementById('tabReport').classList.toggle('active', tab === 'report');
            // Hide picker on report tab, show export; reverse on dashboard tab
            document.getElementById('monthPickerWrap').style.display = tab === 'dashboard' ? '' : 'none';
            document.getElementById('btnExcel').style.display = tab === 'report' ? '' : 'none';
            if (tab === 'report' && !reportAllRows.length) loadReport();
        }

        // ════════════════════════════════════════════════════════════
        // DASHBOARD
        // ════════════════════════════════════════════════════════════
        async function loadDashboard() {
            const res = await apiFetch(`/reports/smelting/dashboard?month=${dashMonth}&year=${dashYear}`);
            if (!res?.ok) return;
            const json = await res.json();
            if (json.status !== 'ok') return;
            dashData = json.data;

            document.getElementById('dashMonthLabel').textContent =
                `${dashData.month_label}  ·  Smelter Section Analytics`;

            renderScorecards();
            renderMetrics();
            renderDayWise();
            renderConsumptionTrend();
            renderTempGraph();
            renderRotaryBreakdown();

            // Populate yield month selector
            yieldMonths = dashData.yield_months ?? [];
            const sel = document.getElementById('yieldMonthSel');
            sel.innerHTML = yieldMonths.map((m, i) => `<option value="${i}">${m.label}</option>`).join('');
            renderYieldChart(dashData.yield_data);
            updateYieldBadges(dashData.yield_data);
        }

        function renderScorecards() {
            const d = dashData;
            const batches = d.rotary_breakdown?.reduce((s, r) => s + Number(r.batch_count), 0) ?? 0;
            document.getElementById('scRow').innerHTML = `
                                                            <div class="sc green">
                                                                <svg class="sc-ico" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                                                                <div class="sc-label">Total Production — ${d.month_label}</div>
                                                                <div class="sc-val">${fmt(d.current_month_total, 0)}<span class="sc-unit">KG</span></div>
                                                                <div class="sc-sub">${batches} batches · ${fmt(d.current_month_total / 1000, 2)} MT</div>
                                                            </div>
                                                            <div class="sc amber">
                                                                <svg class="sc-ico" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                                                <div class="sc-label">Total Production — ${d.prev_label}</div>
                                                                <div class="sc-val">${fmt(d.previous_month_total, 0)}<span class="sc-unit">KG</span></div>
                                                                <div class="sc-sub">${deltaStr(d.current_month_total, d.previous_month_total)} vs prev month</div>
                                                            </div>
                                                            <div class="sc blue">
                                                                <svg class="sc-ico" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                                <div class="sc-label">Total Production — ${d.year_label}</div>
                                                                <div class="sc-val">${fmt(d.year_total, 0)}<span class="sc-unit">KG</span></div>
                                                                <div class="sc-sub">${fmt(d.year_total / 1000, 2)} MT year to date</div>
                                                            </div>
                                                            <div class="sc teal">
                                                                <svg class="sc-ico" viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                                                                <div class="sc-label">Expected Yield</div>
                                                                <div class="sc-val">${fmt(dashData.yield_data?.total_expected ?? 0, 0)}<span class="sc-unit">KG</span></div>
                                                                <div class="sc-sub">Actual: ${fmt(dashData.yield_data?.total_actual ?? 0, 0)} KG</div>
                                                            </div>`;
        }

        function renderMetrics() {
            const d = dashData;
            setMetric('mHr', d.avg_hrs_cur, d.avg_hrs_prev, 'Hrs/MT');
            setMetric('mLpg', d.avg_lpg_cur, d.avg_lpg_prev, 'LTR/MT', true);
            setMetric('mO2', d.avg_o2_cur, d.avg_o2_prev, 'KG/MT', true);
            const yp = d.yield_data?.diff_pct ?? 0;
            document.getElementById('mYield').textContent = (yp >= 0 ? '+' : '') + fmt(yp, 2) + '%';
            document.getElementById('mYieldSub').innerHTML = `<span class="${yp >= 0 ? 'delta-up' : 'delta-down'}">${yp >= 0 ? 'Above' : 'Below'} expected</span>`;
        }

        function setMetric(id, cur, prev, unit, lowerBetter = false) {
            document.getElementById(id).textContent = fmt(cur, 4);
            const deltaEl = document.getElementById(id + 'Delta');
            if (prev && prev > 0) {
                const pct = ((cur - prev) / prev * 100).toFixed(1);
                const better = lowerBetter ? cur < prev : cur > prev;
                deltaEl.innerHTML = `<span class="${better ? 'delta-up' : 'delta-down'}">${pct > 0 ? '+' : ''}${pct}% vs prev</span>`;
            }
        }

        function deltaStr(cur, prev) {
            if (!prev) return '';
            const p = ((cur - prev) / prev * 100).toFixed(1);
            return (p >= 0 ? '+' : '') + p + '%';
        }

        function renderDayWise() {
            const series = dashData.day_wise ?? [];
            if (!series.length) return;
            const maxDays = Math.max(...series.map(s => s.data.length));
            const labels = Array.from({ length: maxDays }, (_, i) => 'D' + (i + 1));
            const colors = [C.green, C.amber, C.blue];
            destroyChart('chartDayWise');
            const ctx = document.getElementById('chartDayWise').getContext('2d');
            charts['chartDayWise'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: series.map((s, i) => ({
                        label: s.label,
                        data: s.data.map(d => d.qty),
                        borderColor: colors[i],
                        backgroundColor: colors[i] + '22',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: false,
                        spanGaps: true,
                    }))
                },
                options: lineOpts('Production (KG)')
            });
        }

        function renderYieldChart(data) {
            if (!data?.daily?.length) return;
            const labels = data.daily.map(d => d.day);
            destroyChart('chartYield');
            const ctx = document.getElementById('chartYield').getContext('2d');
            charts['chartYield'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'Expected', data: data.daily.map(d => d.expected_qty), borderColor: C.blue, backgroundColor: C.blue + '22', borderWidth: 2, pointRadius: 3, tension: .35, fill: false, spanGaps: true },
                        { label: 'Actual', data: data.daily.map(d => d.actual_qty), borderColor: C.green, backgroundColor: C.green + '22', borderWidth: 2, pointRadius: 3, tension: .35, fill: false, spanGaps: true },
                    ]
                },
                options: lineOpts('KG')
            });
        }

        function renderConsumptionTrend() {
            const lpg = dashData.lpg_trend ?? [];
            const o2 = dashData.o2_trend ?? [];
            if (!lpg.length) return;
            const labels = lpg.map(r => r.month);
            destroyChart('chartConsumption');
            const ctx = document.getElementById('chartConsumption').getContext('2d');
            charts['chartConsumption'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'LPG (LTR/MT)', data: lpg.map(r => r.avg_per_mt), borderColor: C.amber, backgroundColor: C.amber + '22', borderWidth: 2, pointRadius: 4, tension: .35, fill: false, spanGaps: true },
                        { label: 'O₂ (KG/MT)', data: o2.map(r => r.avg_per_mt), borderColor: C.teal, backgroundColor: C.teal + '22', borderWidth: 2, pointRadius: 4, tension: .35, fill: false, spanGaps: true },
                    ]
                },
                options: lineOpts('Per MT')
            });
        }

        function renderTempGraph() {
            const data = dashData.temp_data ?? [];
            if (!data.length) return;
            const labels = data.map(d => d.day);
            const series = [
                { key: 'inside_temp', label: 'Inside Temp Before Charging', color: C.red },
                { key: 'pgc_temp', label: 'Process Gas Chamber', color: C.orange },
                { key: 'shell_temp', label: 'Shell', color: C.amber },
                { key: 'bag_house_temp', label: 'Bag House', color: C.purple },
            ];
            destroyChart('chartTemp');
            const ctx = document.getElementById('chartTemp').getContext('2d');
            charts['chartTemp'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: series.map(s => ({
                        label: s.label, data: data.map(d => d[s.key] || null),
                        borderColor: s.color, backgroundColor: s.color + '22',
                        borderWidth: 2, tension: .35, pointRadius: 3, fill: false, spanGaps: true,
                    }))
                },
                options: lineOpts('Temperature (°C)')
            });
        }

        function renderRotaryBreakdown() {
            const rows = dashData.rotary_breakdown ?? [];
            const tbody = document.getElementById('rotaryTbody');
            if (!rows.length) { tbody.innerHTML = `<tr class="state-row"><td colspan="4">No data for selected month.</td></tr>`; return; }
            tbody.innerHTML = rows.map(r => `<tr>
                                                            <td style="font-weight:700">Rotary ${r.rotary_no}</td>
                                                            <td class="num" style="font-weight:700;color:var(--g)">${fmt(r.total_qty, 0)} KG</td>
                                                            <td class="num">${r.batch_count}</td>
                                                            <td class="num">${r.batch_count > 0 ? fmt(r.total_qty / r.batch_count, 0) + ' KG' : '—'}</td>
                                                        </tr>`).join('');
        }

        async function loadYield() {
            const idx = document.getElementById('yieldMonthSel').value;
            const m = yieldMonths[idx];
            if (!m) return;
            const res = await apiFetch(`/reports/smelting/yield?from=${m.from}&to=${m.to}`);
            if (!res?.ok) return;
            const json = await res.json();
            renderYieldChart(json.data);
            updateYieldBadges(json.data);
        }

        function updateYieldBadges(data) {
            if (!data) return;
            document.getElementById('yieldExp').textContent = fmt(data.total_expected, 0) + ' KG';
            document.getElementById('yieldAct').textContent = fmt(data.total_actual, 0) + ' KG';
            const diff = data.diff_pct ?? 0;
            document.getElementById('yieldDiff').textContent = (diff >= 0 ? '+' : '') + fmt(diff, 2) + '%';
            document.getElementById('yieldDiff').style.color = diff >= 0 ? C.green : C.red;
        }

        // ════════════════════════════════════════════════════════════
        // REPORT
        // ════════════════════════════════════════════════════════════
        function resetReportFilters() {
            ['rf_from', 'rf_to', 'rf_batch', 'rf_charge'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
            document.getElementById('rf_rotary').value = '';
            document.getElementById('rf_status').value = '';
            document.getElementById('rf_pp').value = '50';
            reportSort = 'date'; reportDir = 'desc'; reportPage = 1; loadReport();
        }

        function debounceReport() { clearTimeout(reportTimer); reportTimer = setTimeout(() => loadReport(), 350); }

        function buildReportParams(page) {
            const p = new URLSearchParams({
                date_from: document.getElementById('rf_from').value,
                date_to: document.getElementById('rf_to').value,
                batch_no: document.getElementById('rf_batch').value,
                charge_no: document.getElementById('rf_charge').value,
                rotary_no: document.getElementById('rf_rotary').value,
                status: document.getElementById('rf_status').value,
                sort_by: reportSort, sort_dir: reportDir,
                per_page: document.getElementById('rf_pp').value,
                page: page ?? reportPage,
            });
            [...p.keys()].forEach(k => { if (!p.get(k)) p.delete(k); }); return p.toString();
        }

        async function loadReport(page = 1) {
            reportPage = page;
            document.getElementById('reportBody').innerHTML = `<tr class="state-row"><td colspan="21"><span class="spinner"></span>Loading…</td></tr>`;
            document.getElementById('reportFoot').style.display = 'none';
            document.getElementById('pagBar').style.display = 'none';

            const res = await apiFetch(`/reports/smelting/report?${buildReportParams(page)}`);
            if (!res?.ok) { document.getElementById('reportBody').innerHTML = `<tr class="state-row"><td colspan="21" style="color:var(--err)">Failed to load.</td></tr>`; return; }
            const json = await res.json();
            reportAllRows = json.data ?? [];
            renderReportTable(reportAllRows, json.meta);
            renderReportSummary(json.meta?.summary);
            renderPagination(json.meta);
            updateSortHeaders();
        }

        function renderReportTable(rows, meta) {
            const tbody = document.getElementById('reportBody'), tfoot = document.getElementById('reportFoot');
            if (!rows.length) { tbody.innerHTML = `<tr class="state-row"><td colspan="21">No records found.</td></tr>`; return; }

            let fo = 0, fe = 0, fr = 0, ff = 0, flpg = 0, fo2 = 0, fif = 0, frp = 0;
            tbody.innerHTML = rows.map((r, idx) => {
                fo += r.output_qty; fe += r.expected_output_qty; fr += r.total_raw_qty; ff += r.total_flux_qty;
                flpg += r.lpg_consumption; fo2 += r.o2_consumption; fif += r.id_fan_consumption; frp += r.rotary_power_consumption;
                const st = r.status >= 1 ? '<span class="badge-st st-1">Submitted</span>' : '<span class="badge-st st-0">Draft</span>';
                return `<tr>
                                                                <td style="white-space:nowrap">${r.date}</td>
                                                                <td style="font-weight:600">${esc(r.batch_no)}</td>
                                                                <td>${esc(r.charge_no)}</td>
                                                                <td style="text-align:center">R${r.rotary_no}</td>
                                                                <td>${r.start_time}</td><td>${r.end_time}</td>
                                                                <td class="num" style="font-weight:700;color:var(--g)">${fmt(r.output_qty, 0)}</td>
                                                                <td class="num">${fmt(r.expected_output_qty, 0)}</td>
                                                                <td class="num">${fmt(r.avg_yield_pct, 2)}%</td>
                                                                <td class="num">${fmt(r.total_raw_qty, 0)}</td>
                                                                <td class="num">${fmt(r.total_flux_qty, 0)}</td>
                                                                <td class="num">${fmt(r.lpg_consumption, 3)}</td>
                                                                <td class="num">${fmt(r.o2_consumption, 3)}</td>
                                                                <td class="num">${fmt(r.id_fan_consumption, 3)}</td>
                                                                <td class="num">${fmt(r.rotary_power_consumption, 3)}</td>
                                                                <td class="num">${fmt(r.total_process_mins, 0)}</td>
                                                                <td class="num">${fmt(r.avg_inside_temp, 1)}</td>
                                                                <td class="num">${fmt(r.avg_pgc_temp, 1)}</td>
                                                                <td style="max-width:100px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(r.remarks)}</td>
                                                                <td>${st}</td>
                                                                <td style="text-align:center">
                                                                    <button class="btn-view" onclick="openDetail(${idx})">View</button>
                                                                </td>
                                                            </tr>`;
            }).join('');

            ['ftOutput', 'ftExpected', 'ftRaw', 'ftFlux', 'ftLpg', 'ftO2', 'ftIdFan', 'ftRotPwr'].forEach((id, i) => {
                document.getElementById(id).textContent = fmt([fo, fe, fr, ff, flpg, fo2, fif, frp][i], 0);
            });
            tfoot.style.display = '';
            document.getElementById('reportCaption').textContent = meta
                ? `Showing ${rows.length} of ${meta.total.toLocaleString()} records` : `${rows.length} records`;
        }

        function renderReportSummary(s) {
            if (!s) return;
            const wrap = document.getElementById('reportSummary');
            const items = [
                ['Output Qty', s.total_output_qty + ' KG', C.green],
                ['Raw Input', s.total_raw_qty + ' KG', C.blue],
                ['Avg Yield', s.avg_yield_pct + '%', C.teal],
                ['Total LPG', s.total_lpg + ' m³', C.amber],
                ['Total O₂', s.total_o2 + ' m³', C.orange],
                ['ID Fan', s.total_id_fan, C.purple],
                ['Rotary Pwr', s.total_rotary_power, C.red],
            ];
            wrap.innerHTML = items.map(([l, v, c]) =>
                `<div class="sbadge"><span class="sbadge-label">${l}</span><span class="sbadge-val" style="color:${c}">${v}</span></div>`
            ).join('');
        }

        // ════════════════════════════════════════════════════════════
        // DETAIL VIEW MODAL
        // ════════════════════════════════════════════════════════════
        function openDetail(rowIdx) {
            const r = reportAllRows[rowIdx];
            if (!r) return;
            currentDetailRow = r;

            document.getElementById('detailTitle').textContent = `Batch: ${r.batch_no}  ·  ${r.date}`;
            document.getElementById('detailOverlay').classList.add('open');

            const body = document.getElementById('detailBody');

            const kvs = [
                ['Batch No', r.batch_no],
                ['Charge No', r.charge_no],
                ['Date', r.date],
                ['Rotary', 'R' + r.rotary_no],
                ['Start Time', r.start_time],
                ['End Time', r.end_time],
                ['Duration', fmt(r.duration_hours, 3) + ' Hrs'],
                ['Output Qty', fmt(r.output_qty, 0) + ' KG'],
                ['Expected Output', fmt(r.expected_output_qty, 0) + ' KG'],
                ['Avg Yield %', fmt(r.avg_yield_pct, 2) + '%'],
                ['LPG Consumption', fmt(r.lpg_consumption, 3) + ' m³'],
                ['O₂ Consumption', fmt(r.o2_consumption, 3) + ' m³'],
                ['ID Fan Cons.', fmt(r.id_fan_consumption, 3)],
                ['Rotary Pwr Cons.', fmt(r.rotary_power_consumption, 3)],
                ['Process Time', fmt(r.total_process_mins, 0) + ' min'],
                ['Avg Inside Temp', fmt(r.avg_inside_temp, 1) + '°C'],
                ['Avg PGC Temp', fmt(r.avg_pgc_temp, 1) + '°C'],
                ['Status', r.status >= 1 ? 'Submitted' : 'Draft'],
                ['Remarks', r.remarks || '—'],
            ];

            const rawRows = r.raw_materials ?? [];
            const rawHtml = rawRows.length ? `
                                                            <div class="detail-section">
                                                                <h4>Raw Materials</h4>
                                                                <div class="tbl-wrap">
                                                                    <table class="dt">
                                                                        <thead><tr><th>Material</th><th>BBSU Batch</th><th class="num">Qty (KG)</th><th class="num">Yield %</th><th class="num">Expected (KG)</th></tr></thead>
                                                                        <tbody>${rawRows.map(rm => `<tr>
                                                                            <td>${esc(rm.material)}</td>
                                                                            <td>${esc(rm.bbsu_no)}</td>
                                                                            <td class="num">${fmt(rm.qty, 0)}</td>
                                                                            <td class="num">${fmt(rm.yield_pct, 2)}%</td>
                                                                            <td class="num">${fmt(rm.expected, 0)}</td>
                                                                        </tr>`).join('')}</tbody>
                                                                        <tfoot><tr>
                                                                            <td colspan="2" style="text-align:right;color:var(--txtmu);font-size:10px">TOTAL</td>
                                                                            <td class="num">${fmt(rawRows.reduce((s, rm) => s + parseFloat(rm.qty || 0), 0), 0)}</td>
                                                                            <td></td>
                                                                            <td class="num">${fmt(rawRows.reduce((s, rm) => s + parseFloat(rm.expected || 0), 0), 0)}</td>
                                                                        </tr></tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>` : '';

            const procRows = r.process_details ?? [];
            const procHtml = procRows.length ? `
                                                            <div class="detail-section">
                                                                <h4>Process Details</h4>
                                                                <div class="tbl-wrap">
                                                                    <table class="dt">
                                                                        <thead><tr><th>Process</th><th>Start</th><th>End</th><th class="num">Time (min)</th><th>Firing Mode</th></tr></thead>
                                                                        <tbody>${procRows.map(p => `<tr>
                                                                            <td>${esc(p.process)}</td>
                                                                            <td>${esc(p.start)}</td>
                                                                            <td>${esc(p.end)}</td>
                                                                            <td class="num">${fmt(p.total_time, 0)}</td>
                                                                            <td>${esc(p.firing_mode)}</td>
                                                                        </tr>`).join('')}</tbody>
                                                                        <tfoot><tr>
                                                                            <td colspan="3" style="text-align:right;color:var(--txtmu);font-size:10px">TOTAL</td>
                                                                            <td class="num">${fmt(procRows.reduce((s, p) => s + parseFloat(p.total_time || 0), 0), 0)}</td>
                                                                            <td></td>
                                                                        </tr></tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>` : '';
            const fluxRows = r.flux_chemicals ?? [];
            const fluxHtml = fluxRows.length ? `
                                                            <div class="detail-section">
                                                                <h4>Flux Chemicals</h4>
                                                                <div class="tbl-wrap">
                                                                    <table class="dt">
                                                                        <thead><tr><th>Chemical</th><th class="num">Qty (KG)</th></tr></thead>
                                                                        <tbody>${fluxRows.map(f => `<tr>
                                                                            <td>${esc(f.chemical)}</td>
                                                                            <td class="num">${fmt(f.qty, 3)}</td>
                                                                        </tr>`).join('')}</tbody>
                                                                        <tfoot><tr>
                                                                            <td style="text-align:right;color:var(--txtmu);font-size:10px">TOTAL</td>
                                                                            <td class="num">${fmt(fluxRows.reduce((s, f) => s + parseFloat(f.qty || 0), 0), 3)}</td>
                                                                        </tr></tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>` : '';

            body.innerHTML = `
                                                            <div class="detail-section">
                                                                <h4>Batch Summary</h4>
                                                                <div class="detail-grid">${kvs.map(([l, v]) => `
                                                                    <div class="detail-kv">
                                                                        <div class="detail-kv-label">${l}</div>
                                                                        <div class="detail-kv-val">${v}</div>
                                                                    </div>`).join('')}
                                                                </div>
                                                            </div>
                                                            ${rawHtml}
                                                            ${fluxHtml}
                                                            ${procHtml}`;
        }

        function closeDetail() {
            document.getElementById('detailOverlay').classList.remove('open');
            currentDetailRow = null;
        }

        // ════════════════════════════════════════════════════════════
        // DETAIL EXCEL EXPORT
        // ════════════════════════════════════════════════════════════
        function exportDetailExcel() {
            const r = currentDetailRow;
            if (!r) return;
            const wb = XLSX.utils.book_new();
            const sumData = [
                ['Field', 'Value'],
                ['Batch No', r.batch_no], ['Charge No', r.charge_no], ['Date', r.date],
                ['Rotary', 'R' + r.rotary_no], ['Start Time', r.start_time], ['End Time', r.end_time],
                ['Duration (Hrs)', r.duration_hours],
                ['Output Qty (KG)', r.output_qty], ['Expected Output (KG)', r.expected_output_qty],
                ['Avg Yield %', r.avg_yield_pct],
                ['LPG Consumption (m³)', r.lpg_consumption], ['O2 Consumption (m³)', r.o2_consumption],
                ['ID Fan Consumption', r.id_fan_consumption], ['Rotary Power Consumption', r.rotary_power_consumption],
                ['Total Process Time (min)', r.total_process_mins],
                ['Avg Inside Temp (°C)', r.avg_inside_temp], ['Avg PGC Temp (°C)', r.avg_pgc_temp],
                ['Remarks', r.remarks], ['Status', r.status >= 1 ? 'Submitted' : 'Draft'],
            ];
            const ws1 = XLSX.utils.aoa_to_sheet(sumData);
            ws1['!cols'] = [{ wch: 28 }, { wch: 20 }];
            XLSX.utils.book_append_sheet(wb, ws1, 'Summary');
            if (r.raw_materials?.length) {
                const rmData = [['Material', 'BBSU Batch No', 'Qty (KG)', 'Yield %', 'Expected Output (KG)'], ...r.raw_materials.map(rm => [rm.material, rm.bbsu_no, rm.qty, rm.yield_pct, rm.expected])];
                const ws2 = XLSX.utils.aoa_to_sheet(rmData); ws2['!cols'] = [{ wch: 24 }, { wch: 18 }, { wch: 12 }, { wch: 10 }, { wch: 18 }];
                XLSX.utils.book_append_sheet(wb, ws2, 'Raw Materials');
            }
            if (r.process_details?.length) {
                const pdData = [['Process', 'Start', 'End', 'Time (min)', 'Firing Mode'], ...r.process_details.map(p => [p.process, p.start, p.end, p.total_time, p.firing_mode])];
                const ws3 = XLSX.utils.aoa_to_sheet(pdData); ws3['!cols'] = [{ wch: 20 }, { wch: 10 }, { wch: 10 }, { wch: 12 }, { wch: 14 }];
                XLSX.utils.book_append_sheet(wb, ws3, 'Process Details');
            }
            if (r.flux_chemicals?.length) {
                const fcData = [['Chemical', 'Qty (KG)'], ...r.flux_chemicals.map(f => [f.chemical, f.qty])];
                const ws4 = XLSX.utils.aoa_to_sheet(fcData);
                ws4['!cols'] = [{ wch: 28 }, { wch: 12 }];
                XLSX.utils.book_append_sheet(wb, ws4, 'Flux Chemicals');
            }
            XLSX.writeFile(wb, `Smelting_${r.batch_no}_${r.date_raw || r.date}.xlsx`);
        }

        // ════════════════════════════════════════════════════════════
        // FULL REPORT EXCEL EXPORT
        // ════════════════════════════════════════════════════════════
        async function exportExcel() {
            const btn = document.querySelector('#panelReport .btn-excel');
            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner" style="border-top-color:#fff"></span> Exporting…'; }

            let all = [], page = 1, lastPage = 1;
            do {
                const res = await apiFetch(`/reports/smelting/report?${buildReportParams(page)}&per_page=500`);
                if (!res?.ok) break;
                const json = await res.json();
                all.push(...(json.data ?? [])); lastPage = json.meta?.last_page ?? 1; page++;
            } while (page <= lastPage);

            if (!all.length) { if (btn) { btn.disabled = false; btn.innerHTML = 'Export'; } return; }

            const wb = XLSX.utils.book_new();
            const headers = ['Date', 'Batch No', 'Charge No', 'Rotary', 'Start', 'End', 'Duration (Hrs)',
                'Output (KG)', 'Expected (KG)', 'Yield %', 'Raw Input (KG)', 'Flux (KG)',
                'LPG (m³)', 'O₂ (m³)', 'ID Fan', 'Rotary Pwr', 'Process (min)',
                'Avg Inside °C', 'Avg PGC °C', 'Remarks', 'Status'];
            const wsData = [headers, ...all.map(r => [
                r.date, r.batch_no, r.charge_no, 'R' + r.rotary_no, r.start_time, r.end_time, r.duration_hours,
                r.output_qty, r.expected_output_qty, r.avg_yield_pct, r.total_raw_qty, r.total_flux_qty,
                r.lpg_consumption, r.o2_consumption, r.id_fan_consumption, r.rotary_power_consumption,
                r.total_process_mins, r.avg_inside_temp, r.avg_pgc_temp, r.remarks, r.status >= 1 ? 'Submitted' : 'Draft'
            ])];
            const ws = XLSX.utils.aoa_to_sheet(wsData); ws['!cols'] = headers.map(() => ({ wch: 14 }));
            XLSX.utils.book_append_sheet(wb, ws, 'Smelting Report');

            const rmHeaders = ['Batch No', 'Date', 'Material', 'BBSU Batch No', 'Qty (KG)', 'Yield %', 'Expected (KG)'];
            const rmData = [rmHeaders, ...all.flatMap(r => (r.raw_materials ?? []).map(rm => [r.batch_no, r.date, rm.material, rm.bbsu_no, rm.qty, rm.yield_pct, rm.expected]))];
            const wsRm = XLSX.utils.aoa_to_sheet(rmData); wsRm['!cols'] = [{ wch: 16 }, { wch: 12 }, { wch: 24 }, { wch: 18 }, { wch: 12 }, { wch: 10 }, { wch: 16 }];
            XLSX.utils.book_append_sheet(wb, wsRm, 'Raw Materials Detail');

            const pdHeaders = ['Batch No', 'Date', 'Process', 'Start', 'End', 'Time (min)', 'Firing Mode'];
            const pdData = [pdHeaders, ...all.flatMap(r => (r.process_details ?? []).map(p => [r.batch_no, r.date, p.process, p.start, p.end, p.total_time, p.firing_mode]))];
            const wsPd = XLSX.utils.aoa_to_sheet(pdData); wsPd['!cols'] = [{ wch: 16 }, { wch: 12 }, { wch: 20 }, { wch: 10 }, { wch: 10 }, { wch: 12 }, { wch: 14 }];
            XLSX.utils.book_append_sheet(wb, wsPd, 'Process Details');

            XLSX.writeFile(wb, `Smelting_Report_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.xlsx`);
            if (btn) { btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Export'; }
        }

        // ════════════════════════════════════════════════════════════
        // CHART HELPERS
        // ════════════════════════════════════════════════════════════
        function lineOpts(yLabel) {
            return {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: true, position: 'top', labels: { font: { family: 'Outfit', size: 11 }, boxWidth: 12, padding: 14 } },
                    tooltip: {
                        backgroundColor: '#fff', borderColor: '#dde8e2', borderWidth: 1,
                        titleColor: '#1e2d26', bodyColor: '#3d5449',
                        titleFont: { family: 'Outfit', size: 12, weight: '700' },
                        bodyFont: { family: 'Outfit', size: 11 }, padding: 10, cornerRadius: 8
                    }
                },
                scales: {
                    x: { grid: { color: '#edf2ef' }, ticks: { font: { family: 'Outfit', size: 10 }, color: '#6b8a78', maxTicksLimit: 16, maxRotation: 0 } },
                    y: {
                        grid: { color: '#edf2ef' }, beginAtZero: true,
                        ticks: { font: { family: 'Outfit', size: 10 }, color: '#6b8a78', maxTicksLimit: 6 },
                        title: { display: !!yLabel, text: yLabel, font: { family: 'Outfit', size: 10 }, color: '#6b8a78' }
                    }
                }
            };
        }

        function destroyChart(id) { if (charts[id]) { charts[id].destroy(); delete charts[id]; } }

        // ════════════════════════════════════════════════════════════
        // PAGINATION + SORT
        // ════════════════════════════════════════════════════════════
        function renderPagination(meta) {
            const bar = document.getElementById('pagBar'), info = document.getElementById('pagInfo'), btns = document.getElementById('pagBtns');
            if (!meta || meta.last_page <= 1) { bar.style.display = 'none'; return; }
            bar.style.display = 'flex';
            info.textContent = `${(meta.current_page - 1) * meta.per_page + 1}–${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total.toLocaleString()}`;
            const pages = paginationRange(meta.current_page, meta.last_page);
            btns.innerHTML = [
                `<button class="pag-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="loadReport(${meta.current_page - 1})">‹</button>`,
                ...pages.map(p => p === '…' ? `<button class="pag-btn" disabled>…</button>` : `<button class="pag-btn${p === meta.current_page ? ' active' : ''}" onclick="loadReport(${p})">${p}</button>`),
                `<button class="pag-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="loadReport(${meta.current_page + 1})">›</button>`
            ].join('');
        }

        function paginationRange(cur, last) { const d = 2, r = []; for (let i = Math.max(2, cur - d); i <= Math.min(last - 1, cur + d); i++)r.push(i); if (r[0] > 2) r.unshift('…'); if (r[r.length - 1] < last - 1) r.push('…'); r.unshift(1); if (last !== 1) r.push(last); return r; }

        function sortReport(col) { reportDir = reportSort === col ? (reportDir === 'asc' ? 'desc' : 'asc') : 'desc'; reportSort = col; loadReport(1); }
        function updateSortHeaders() {
            document.querySelectorAll('#reportTable thead th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
                const col = th.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
                if (col === reportSort) th.classList.add(reportDir === 'asc' ? 'sort-asc' : 'sort-desc');
            });
        }

        // ════════════════════════════════════════════════════════════
        // UTILS
        // ════════════════════════════════════════════════════════════
        function fmt(n, d = 3) { if (n === null || n === undefined || n === '') return '—'; return parseFloat(n).toLocaleString(undefined, { minimumFractionDigits: d, maximumFractionDigits: d }); }
        function esc(s) { if (!s || s === '—') return '—'; return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    </script>
@endpush