{{-- resources/views/admin/reports/refining_dashboard.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Refining Dashboard & Report')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <span style="color:var(--text-muted)">Reports</span>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <strong>Refining</strong>
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
            --amber: #d97706;
            --blue: #2563eb;
            --red: #dc2626;
            --purple: #7c3aed;
            --teal: #0d9488;
            --orange: #ea580c;
            --indigo: #4338ca
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

        @keyframes skel {
            0% {
                background-position: 200% 0
            }

            100% {
                background-position: -200% 0
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* ── page header ── */
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

        /* ── buttons ── */
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

        /* ── tab bar ── */
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

        /* ── card ── */
        .card {
            background: var(--white);
            border: 1px solid var(--bdr);
            border-radius: var(--r);
            box-shadow: var(--sh);
            margin-bottom: 18px;
            overflow: hidden;
            animation: fadeUp .3s ease both
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

        /* ── scorecard ── */
        .sc-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 20px
        }

        @media(max-width:700px) {
            .sc-row {
                grid-template-columns: 1fr
            }
        }

        .sc {
            border-radius: 12px;
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
            transition: transform .18s, box-shadow .18s;
            animation: fadeUp .35s ease both
        }

        .sc:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 32px rgba(0, 0, 0, .14)
        }

        .sc.green {
            background: linear-gradient(135deg, #14532d, #16a34a)
        }

        .sc.amber {
            background: linear-gradient(135deg, #78350f, #d97706)
        }

        .sc.blue {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6)
        }

        .sc-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .72);
            margin-bottom: 10px
        }

        .sc-val {
            font-size: 32px;
            font-weight: 900;
            color: #fff;
            line-height: 1;
            letter-spacing: -1.5px
        }

        .sc-unit {
            font-size: 13px;
            color: rgba(255, 255, 255, .6);
            font-weight: 500;
            margin-left: 5px
        }

        .sc-sub {
            font-size: 10px;
            color: rgba(255, 255, 255, .5);
            margin-top: 6px
        }

        .sc-ico {
            position: absolute;
            right: 16px;
            top: 16px;
            width: 36px;
            height: 36px;
            stroke: rgba(255, 255, 255, .15);
            fill: none;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        /* ── two / three col ── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        .three-col {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px
        }

        @media(max-width:960px) {

            .two-col,
            .three-col {
                grid-template-columns: 1fr
            }
        }

        /* ── doughnut section ── */
        .donut-row {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap
        }

        .donut-wrap {
            position: relative;
            width: 180px;
            height: 180px;
            flex-shrink: 0
        }

        .donut-center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none
        }

        .donut-total {
            font-size: 20px;
            font-weight: 800;
            color: var(--g);
            line-height: 1;
            letter-spacing: -1px
        }

        .donut-label {
            font-size: 9px;
            font-weight: 700;
            color: var(--txtmu);
            text-transform: uppercase;
            letter-spacing: .6px
        }

        .cat-legend {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            min-width: 140px;
            max-height: 240px;
            overflow-y: auto
        }

        .cat-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 8px;
            background: var(--gxl);
            border: 1px solid var(--bdr);
            transition: background .12s
        }

        .cat-legend-item:hover {
            background: var(--gl)
        }

        .cat-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0
        }

        .cat-name {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--txtm);
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .cat-qty {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--g)
        }

        .cat-unit {
            font-size: 9px;
            color: var(--txtmu);
            margin-left: 2px
        }

        /* ── pot cards ── */
        .pot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px
        }

        .pot-card {
            border: 1.5px solid var(--bdr);
            border-radius: 10px;
            overflow: hidden;
            transition: border-color .15s, box-shadow .15s
        }

        .pot-card:hover {
            border-color: var(--g);
            box-shadow: 0 4px 18px rgba(26, 122, 58, .1)
        }

        .pot-card-head {
            padding: 10px 14px;
            background: var(--gl);
            border-bottom: 1px solid var(--bdr);
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .pot-no {
            font-size: 11px;
            font-weight: 800;
            color: var(--g);
            letter-spacing: .5px;
            text-transform: uppercase
        }

        .pot-batches {
            font-size: 10px;
            color: var(--txtmu);
            font-weight: 600
        }

        .pot-card-body {
            padding: 12px 14px
        }

        .pot-total {
            font-size: 20px;
            font-weight: 800;
            color: var(--g);
            letter-spacing: -1px;
            line-height: 1
        }

        .pot-total-label {
            font-size: 9px;
            color: var(--txtmu);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 6px
        }

        .pot-mat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px solid #f0f6f2;
            font-size: 11.5px
        }

        .pot-mat:last-child {
            border-bottom: none
        }

        .pot-mat-name {
            color: var(--txtm);
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 110px
        }

        .pot-mat-qty {
            color: var(--g);
            font-weight: 700;
            flex-shrink: 0
        }

        /* ── metric tiles ── */
        .metric-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
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
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            margin: 4px 0
        }

        .metric-unit {
            font-size: 9.5px;
            font-weight: 700;
            color: var(--txtmu);
            text-transform: uppercase;
            letter-spacing: .5px
        }

        .metric-label {
            font-size: 10.5px;
            color: var(--txtm);
            font-weight: 600;
            margin-bottom: 2px
        }

        /* ── avg hr table ── */
        .cat-hr-table {
            width: 100%;
            border-collapse: collapse
        }

        .cat-hr-table th {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 8px 12px;
            border-bottom: 2px solid var(--bdr);
            text-align: left
        }

        .cat-hr-table td {
            padding: 7px 12px;
            border-bottom: 1px solid #edf2ef;
            font-size: 12.5px
        }

        .cat-hr-table tr:last-child td {
            border-bottom: none
        }

        .cat-hr-table tr:hover td {
            background: #f7fbf8
        }

        .bar-cell {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .bar-track {
            flex: 1;
            height: 5px;
            background: var(--bdr);
            border-radius: 3px;
            overflow: hidden
        }

        .bar-fill {
            height: 100%;
            background: var(--g);
            border-radius: 3px;
            transition: width .5s ease
        }

        /* ── chart ── */
        .chart-wrap {
            position: relative
        }

        .chart-wrap canvas {
            display: block
        }

        /* ── filter fields ── */
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

        /* ── report table ── */
        .tbl-wrap {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--bdr)
        }

        .dt {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px
        }

        .dt thead th {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 9px 11px;
            border-bottom: 2px solid var(--bdr);
            white-space: nowrap;
            cursor: pointer;
            user-select: none
        }

        .dt thead th:hover {
            background: #d9f0e2
        }

        .dt thead th.sort-asc::after {
            content: '↑';
            margin-left: 3px
        }

        .dt thead th.sort-desc::after {
            content: '↓';
            margin-left: 3px
        }

        .dt tbody td {
            padding: 7px 11px;
            border-bottom: 1px solid #edf2ef;
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
            font-size: 12px;
            color: var(--g);
            padding: 7px 11px;
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
            padding: 2px 8px;
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

        /* ── summary badges ── */
        .sbadges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            padding: 12px 20px 0
        }

        .sbadge {
            background: var(--gl);
            border: 1px solid var(--bdr);
            border-radius: 8px;
            padding: 6px 12px;
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 100px
        }

        .sbadge-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: var(--txtmu)
        }

        .sbadge-val {
            font-size: 14px;
            font-weight: 800;
            color: var(--g);
            letter-spacing: -.5px
        }

        /* ── drill modal ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .48);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s
        }

        .modal-overlay.open {
            opacity: 1;
            pointer-events: all
        }

        .modal-box {
            background: var(--white);
            border-radius: 14px;
            width: 100%;
            max-width: 860px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .22);
            transform: translateY(10px);
            transition: transform .2s
        }

        .modal-overlay.open .modal-box {
            transform: translateY(0)
        }

        .modal-head {
            padding: 14px 20px;
            background: var(--gl);
            border-bottom: 1px solid var(--bdr);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 14px 14px 0 0
        }

        .modal-title {
            font-size: 13px;
            font-weight: 800;
            color: var(--g)
        }

        .modal-close {
            width: 28px;
            height: 28px;
            background: var(--bdr);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: var(--txtm);
            transition: all .15s
        }

        .modal-close:hover {
            background: #fca5a5;
            color: #dc2626
        }

        .modal-body {
            padding: 16px 20px;
            overflow-y: auto;
            flex: 1
        }

        /* ── pagination ── */
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
            width: 16px;
            height: 16px;
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
            animation: skel 1.2s ease infinite;
            border-radius: 4px;
            display: block
        }

        .state-row td {
            text-align: center;
            padding: 36px;
            color: var(--txtmu)
        }

        .section-divider {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--txtmu);
            margin: 16px 0 10px;
            display: flex;
            align-items: center;
            gap: 10px
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--bdr)
        }
    </style>
@endpush

@section('content')

    {{-- Drill-down Modal --}}
    <div class="modal-overlay" id="drillModal" onclick="closeDrill(event)">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-title" id="drillTitle">Batch Details</div>
                <div style="display:flex;align-items:center;gap:8px">
                    <button class="btn btn-excel btn-sm" onclick="exportDetailExcel()"
                        style="padding:4px 10px;font-size:11px">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        Download Excel
                    </button>
                    <button class="modal-close" onclick="closeDrill()">✕</button>
                </div>
            </div>
            <div class="modal-body" id="drillBody"></div>
        </div>
    </div>

    <div class="ph">
        <div>
            <h2>⚗️ Refinery Section — Dashboard &amp; Report</h2>
            <p id="dashMonthLabel" style="color:var(--txtmu)">Loading…</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
            {{-- Month + Year pickers — visible in both tabs --}}
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
        </div>
    </div>

    {{-- Tab bar --}}
    <div class="tab-bar">
        <button class="tab-btn active" id="tabDash" onclick="switchTab('dashboard')">📊 Dashboard</button>
        <button class="tab-btn" id="tabReport" onclick="switchTab('report')">📋 Report</button>
    </div>

    {{-- ════════════════ DASHBOARD ════════════════ --}}
    <div id="panelDashboard">

        {{-- Scorecards — 2 cards (removed O2/MT) --}}
        <div class="sc-row" id="scRow">
            <div class="sc green"><span class="skel" style="width:60%;height:10px;margin-bottom:10px"></span><span
                    class="skel" style="width:80%;height:28px"></span></div>
            <div class="sc amber"><span class="skel" style="width:60%;height:10px;margin-bottom:10px"></span><span
                    class="skel" style="width:80%;height:28px"></span></div>
        </div>

        {{-- Metrics row --}}
        <div class="metric-row" id="metricRow">
            <div class="metric">
                <div class="metric-label">Avg HR / MT</div>
                <div class="metric-val skel" style="width:60px;height:22px;margin:6px auto"></div>
            </div>
            <div class="metric">
                <div class="metric-label">Avg LPG / MT</div>
                <div class="metric-val skel" style="width:60px;height:22px;margin:6px auto"></div>
            </div>
            <div class="metric">
                <div class="metric-label">Avg O₂ / MT</div>
                <div class="metric-val skel" style="width:60px;height:22px;margin:6px auto"></div>
            </div>
            <div class="metric">
                <div class="metric-label">Dross Output</div>
                <div class="metric-val skel" style="width:60px;height:22px;margin:6px auto"></div>
            </div>
        </div>

        {{-- Row 1: Category doughnut + Daily chart --}}
        <div class="two-col">
            {{-- Doughnut — category output --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 2a10 10 0 0 1 10 10" />
                        </svg>
                        <span>Category Output</span>
                    </div>
                    <span id="donutMonthBadge" style="font-size:10px;color:var(--txtmu);font-weight:600"></span>
                </div>
                <div class="card-body">
                    <div class="donut-row">
                        <div class="donut-wrap">
                            <canvas id="chartDonut" width="180" height="180"></canvas>
                            <div class="donut-center">
                                <div class="donut-total" id="donutTotal">—</div>
                                <div class="donut-label">Total KG</div>
                            </div>
                        </div>
                        <div class="cat-legend" id="catLegend">
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daily output line chart --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                        </svg>
                        <span>Daily FG Output</span>
                    </div>
                </div>
                <div class="card-body" style="padding:14px 20px 18px">
                    <div class="chart-wrap" style="height:220px"><canvas id="chartDaily"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Row 2: Material-wise doughnut + Dross-wise doughnut (NEW) --}}
        <div class="two-col">
            {{-- Material-wise FG Doughnut --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 2a10 10 0 0 1 10 10" />
                        </svg>
                        <span>FG Output — Material Wise</span>
                    </div>
                    <span id="matDonutBadge" style="font-size:10px;color:var(--txtmu);font-weight:600"></span>
                </div>
                <div class="card-body">
                    <div class="donut-row">
                        <div class="donut-wrap">
                            <canvas id="chartMatDonut" width="180" height="180"></canvas>
                            <div class="donut-center">
                                <div class="donut-total" id="matDonutTotal">—</div>
                                <div class="donut-label">Total KG</div>
                            </div>
                        </div>
                        <div class="cat-legend" id="matLegend">
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dross-wise Doughnut --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 2a10 10 0 0 1 10 10" />
                        </svg>
                        <span>Dross Output — Material Wise</span>
                    </div>
                    <span id="drossDonutBadge" style="font-size:10px;color:var(--txtmu);font-weight:600"></span>
                </div>
                <div class="card-body">
                    <div class="donut-row">
                        <div class="donut-wrap">
                            <canvas id="chartDrossDonut" width="180" height="180"></canvas>
                            <div class="donut-center">
                                <div class="donut-total" id="drossDonutTotal">—</div>
                                <div class="donut-label">Dross KG</div>
                            </div>
                        </div>
                        <div class="cat-legend" id="drossLegend">
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                            <span class="skel" style="height:32px;border-radius:8px"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pot production --}}
        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 6h18M3 12h18M3 18h18" />
                    </svg>
                    <span>Current Pot Production — Material Details</span>
                </div>
                <span id="potMonthBadge" style="font-size:10px;color:var(--txtmu);font-weight:600"></span>
            </div>
            <div class="card-body">
                <div class="pot-grid" id="potGrid">
                    <span class="skel" style="height:160px;border-radius:10px"></span>
                    <span class="skel" style="height:160px;border-radius:10px"></span>
                    <span class="skel" style="height:160px;border-radius:10px"></span>
                    <span class="skel" style="height:160px;border-radius:10px"></span>
                </div>
            </div>
        </div>

        {{-- Avg HR per unit — category wise --}}
        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span>Avg Production HR / KG — Category Wise</span>
                </div>
            </div>
            <div class="card-body" style="padding:0">
                <table class="cat-hr-table" id="catHrTable">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="num">Total Output (KG)</th>
                            <th class="num">Avg HR / KG</th>
                            <th style="width:200px">Relative</th>
                        </tr>
                    </thead>
                    <tbody id="catHrBody">
                        <tr>
                            <td colspan="4" style="text-align:center;padding:24px;color:var(--txtmu)"><span
                                    class="spinner"></span>Loading…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /panelDashboard --}}

    {{-- ════════════════ REPORT ════════════════ --}}
    <div id="panelReport" style="display:none">

        {{-- Filters --}}
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
                    <div class="field"><label>Date From</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <input type="date" id="rf_from" onchange="loadReport()">
                        </div>
                    </div>
                    <div class="field"><label>Date To</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <input type="date" id="rf_to" onchange="loadReport()">
                        </div>
                    </div>
                    <div class="field"><label>Batch No</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input type="text" id="rf_batch" placeholder="Search…" oninput="debounceReport()">
                        </div>
                    </div>
                    <div class="field"><label>Pot No</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M3 6h18M3 12h18M3 18h18" />
                            </svg>
                            <select id="rf_pot" onchange="loadReport()">
                                <option value="">All Pots</option>
                            </select>
                        </div>
                    </div>
                    <div class="field"><label>Status</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <select id="rf_status" onchange="loadReport()">
                                <option value="">All</option>
                                <option value="0">Draft</option>
                                <option value="1">Submitted</option>
                            </select>
                        </div>
                    </div>
                    <div class="field"><label>Rows / Page</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <line x1="8" y1="6" x2="21" y2="6" />
                                <line x1="8" y1="12" x2="21" y2="12" />
                                <line x1="8" y1="18" x2="21" y2="18" />
                            </svg>
                            <select id="rf_pp" onchange="loadReport()">
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <line x1="3" y1="9" x2="21" y2="9" />
                        <line x1="3" y1="15" x2="21" y2="15" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                    <span>Refining Batch Records</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <span id="reportCaption" style="font-size:11px;color:var(--txtmu)"></span>
                    <button class="btn btn-excel btn-sm" onclick="exportExcel()">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        Export
                    </button>
                </div>
            </div>

            <div class="sbadges" id="reportSummary"></div>

            <div class="tbl-wrap" style="border-radius:0;border:none">
                <table class="dt" id="reportTable">
                    <thead>
                        <tr>
                            <th onclick="sortReport('date')">Date</th>
                            <th onclick="sortReport('batch_no')">Batch No</th>
                            <th onclick="sortReport('pot_no')">Pot</th>
                            <th>Material</th>
                            <th class="num" onclick="sortReport('total_fg_qty')">FG Output (KG)</th>
                            <th class="num">Dross (KG)</th>
                            <th class="num">Raw Input (KG)</th>
                            <th class="num" onclick="sortReport('lpg_consumption')">LPG (m³)</th>
                            <th class="num">LPG (Ltr)</th>
                            <th class="num">LPG2 (m³)</th>
                            <th class="num">LPG2 (Ltr)</th>
                            <th class="num" onclick="sortReport('electricity_consumption')">Electricity</th>
                            <th class="num">O₂ Flow (NM³)</th>
                            <th class="num" onclick="sortReport('oxygen_consumption')">O₂ Cons (KG)</th>
                            <th class="num" onclick="sortReport('total_process_time')">Process (min)</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th style="text-align:center">Details</th>
                        </tr>
                    </thead>
                    <tbody id="reportBody">
                        <tr class="state-row">
                            <td colspan="18"><span class="spinner"></span>Loading…</td>
                        </tr>
                    </tbody>
                    <tfoot id="reportFoot" style="display:none">
                        <tr>
                            <td colspan="4" style="text-align:right;font-size:10px;letter-spacing:.5px;color:var(--txtmu)">
                                PAGE TOTAL</td>
                            <td class="num" id="ftFg"></td>
                            <td class="num" id="ftDross"></td>
                            <td class="num" id="ftRaw"></td>
                            <td class="num" id="ftLpg"></td>
                            <td class="num" id="ftLpgL"></td>
                            <td class="num" id="ftLpg2"></td>
                            <td class="num" id="ftLpg2L"></td>
                            <td class="num" id="ftElec"></td>
                            <td class="num" id="ftO2Nm"></td>
                            <td class="num" id="ftO2"></td>
                            <td class="num" id="ftProc"></td>
                            <td colspan="3"></td>
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

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // ════════════════════════════════════════════════════════════
        // PALETTE
        // ════════════════════════════════════════════════════════════
        const COLORS = ['#15803d', '#d97706', '#2563eb', '#7c3aed', '#0d9488', '#ea580c', '#dc2626', '#4338ca',
            '#0891b2', '#65a30d', '#c2410c', '#6d28d9', '#047857', '#b45309', '#1d4ed8', '#be185d'];

        // ════════════════════════════════════════════════════════════
        // STATE
        // ════════════════════════════════════════════════════════════
        let dashData = null;
        let charts = {};
        let rPage = 1, rSort = 'date', rDir = 'desc', rTimer = null;
        let allRows = [];
        let dashMonth, dashYear;
        let currentDrillRow = null;

        // ════════════════════════════════════════════════════════════
        // INIT — build year dropdown, set current month/year, load data
        // ════════════════════════════════════════════════════════════
        (async function init() {
            const now = new Date();
            dashMonth = now.getMonth() + 1;   // 1-12
            dashYear = now.getFullYear();

            // Build year dropdown: current year down to current-4
            const yearSel = document.getElementById('dashYearPicker');
            for (let y = dashYear; y >= dashYear - 4; y--) {
                const o = document.createElement('option');
                o.value = y; o.textContent = y;
                if (y === dashYear) o.selected = true;
                yearSel.appendChild(o);
            }

            // Pre-select current month in month dropdown
            document.getElementById('dashMonthPicker').value = dashMonth;

            await loadFilters();   // loads pot list for report tab
            await loadDashboard();
        })();

        // ════════════════════════════════════════════════════════════
        // MONTH / YEAR PICKER — fires on either dropdown change
        // ════════════════════════════════════════════════════════════
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
            if (tab === 'report' && !allRows.length) loadReport();
        }

        // ════════════════════════════════════════════════════════════
        // DASHBOARD
        // ════════════════════════════════════════════════════════════
        async function loadDashboard() {
            showDashSkeleton();
            const res = await apiFetch(`/reports/refining/dashboard?month=${dashMonth}&year=${dashYear}`);
            if (!res?.ok) return;
            const json = await res.json();
            if (json.status !== 'ok') return;
            dashData = json.data;

            document.getElementById('dashMonthLabel').textContent =
                `${dashData.month_label} · Refinery Section Analytics`;

            renderScorecards();
            renderMetrics();
            renderDonut();
            renderMaterialDonut();
            renderDrossDonut();
            renderDailyChart();
            renderPotProduction();
            renderAvgHrTable();
        }

        function showDashSkeleton() {
            document.getElementById('scRow').innerHTML = `
                                                        <div class="sc green"><span class="skel" style="width:60%;height:10px;margin-bottom:10px"></span><span class="skel" style="width:80%;height:28px"></span></div>
                                                        <div class="sc amber"><span class="skel" style="width:60%;height:10px;margin-bottom:10px"></span><span class="skel" style="width:80%;height:28px"></span></div>`;
            document.getElementById('catHrBody').innerHTML =
                '<tr><td colspan="4" style="text-align:center;padding:24px;color:var(--txtmu)"><span class="spinner"></span>Loading…</td></tr>';
        }

        // ── Scorecards (2 cards — removed O2/MT) ─────────────────
        function renderScorecards() {
            const d = dashData;
            const pct = d.last_month_total > 0
                ? (((d.current_month_total - d.last_month_total) / d.last_month_total) * 100).toFixed(1)
                : null;

            document.getElementById('scRow').innerHTML = `
                                                    <div class="sc green" style="animation-delay:.05s">
                                                        <svg class="sc-ico" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                                                        <div class="sc-label">Total Production — ${esc(d.month_label)}</div>
                                                        <div class="sc-val">${fmt(d.current_month_total, 1)}<span class="sc-unit">KG</span></div>
                                                        ${pct !== null ? `<div class="sc-sub">${pct >= 0 ? '▲' : '▼'} ${Math.abs(pct)}% vs last month</div>` : ''}
                                                    </div>
                                                    <div class="sc amber" style="animation-delay:.1s">
                                                        <svg class="sc-ico" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                                        <div class="sc-label">Total Production — ${esc(d.prev_label)}</div>
                                                        <div class="sc-val">${fmt(d.last_month_total, 1)}<span class="sc-unit">KG</span></div>
                                                    </div>`;
        }

        // ── Metrics (HR/MT, LPG/MT, O2/MT, Dross) ────────────────
        function renderMetrics() {
            const d = dashData;
            document.getElementById('metricRow').innerHTML = `
                                                    <div class="metric">
                                                        <div class="metric-label">Avg HR / MT</div>
                                                        <div class="metric-val" style="color:var(--amber)">${fmt(d.avg_hr_per_unit, 4)}</div>
                                                        <div class="metric-unit">Hrs / MT</div>
                                                    </div>
                                                    <div class="metric">
                                                        <div class="metric-label">Avg LPG / MT</div>
                                                        <div class="metric-val" style="color:var(--blue)">${fmt(d.avg_lpg_per_unit, 4)}</div>
                                                        <div class="metric-unit">Ltr / MT</div>
                                                    </div>
                                                    <div class="metric">
                                                        <div class="metric-label">Avg O₂ / MT</div>
                                                        <div class="metric-val" style="color:var(--teal)">${fmt(d.avg_o2_per_unit, 4)}</div>
                                                        <div class="metric-unit">KG / MT</div>
                                                    </div>
                                                    <div class="metric">
                                                        <div class="metric-label">Dross Output</div>
                                                        <div class="metric-val" style="color:var(--orange)">${fmt(d.dross_total, 3)}</div>
                                                        <div class="metric-unit">KG this month</div>
                                                    </div>`;
        }

        // ── Category doughnut ─────────────────────────────────────
        function renderDonut() {
            const cats = dashData.by_category ?? [];
            const total = cats.reduce((s, c) => s + c.total_qty, 0);
            document.getElementById('donutTotal').textContent = fmt(total, 0);
            document.getElementById('donutMonthBadge').textContent = dashData.month_label;

            if (!cats.length) {
                document.getElementById('catLegend').innerHTML = '<div style="color:var(--txtmu);font-size:12px;padding:8px">No data</div>';
                return;
            }
            destroyChart('chartDonut');
            const ctx = document.getElementById('chartDonut').getContext('2d');
            charts['chartDonut'] = new Chart(ctx, donutConfig(
                cats.map(c => c.category),
                cats.map(c => c.total_qty),
                'KG'
            ));

            document.getElementById('catLegend').innerHTML = cats.map((c, i) => `
                                                    <div class="cat-legend-item">
                                                        <div class="cat-dot" style="background:${COLORS[i % COLORS.length]}"></div>
                                                        <div class="cat-name">${esc(c.category)}</div>
                                                        <div><span class="cat-qty">${fmt(c.total_qty, 1)}</span><span class="cat-unit">KG</span></div>
                                                    </div>`).join('');
        }

        // ── Material-wise FG doughnut (NEW) ───────────────────────
        function renderMaterialDonut() {
            const mats = dashData.material_doughnut ?? [];
            const total = mats.reduce((s, m) => s + m.total_qty, 0);
            document.getElementById('matDonutTotal').textContent = fmt(total, 0);
            document.getElementById('matDonutBadge').textContent = dashData.month_label;

            const legend = document.getElementById('matLegend');
            if (!mats.length) {
                legend.innerHTML = '<div style="color:var(--txtmu);font-size:12px;padding:8px">No data</div>';
                return;
            }
            destroyChart('chartMatDonut');
            const ctx = document.getElementById('chartMatDonut').getContext('2d');
            charts['chartMatDonut'] = new Chart(ctx, donutConfig(
                mats.map(m => m.material_name),
                mats.map(m => m.total_qty),
                'KG'
            ));

            legend.innerHTML = mats.map((m, i) => `
                                                    <div class="cat-legend-item">
                                                        <div class="cat-dot" style="background:${COLORS[i % COLORS.length]}"></div>
                                                        <div class="cat-name" title="${esc(m.material_name)}">${esc(m.material_name)}</div>
                                                        <div>
                                                            <span class="cat-qty">${fmt(m.total_qty, 1)}</span><span class="cat-unit">KG</span>
                                                            ${m.category ? `<span style="font-size:9px;color:var(--txtmu);margin-left:4px">(${esc(m.category)})</span>` : ''}
                                                        </div>
                                                    </div>`).join('');
        }

        // ── Dross-wise doughnut (NEW) ─────────────────────────────
        function renderDrossDonut() {
            const items = dashData.dross_doughnut ?? [];
            const total = items.reduce((s, d) => s + d.total_qty, 0);
            document.getElementById('drossDonutTotal').textContent = fmt(total, 0);
            document.getElementById('drossDonutBadge').textContent = dashData.month_label;

            const legend = document.getElementById('drossLegend');
            if (!items.length) {
                legend.innerHTML = '<div style="color:var(--txtmu);font-size:12px;padding:8px">No dross data</div>';
                return;
            }
            destroyChart('chartDrossDonut');
            const ctx = document.getElementById('chartDrossDonut').getContext('2d');

            // Use orange-toned palette for dross
            const drossColors = ['#ea580c', '#d97706', '#dc2626', '#c2410c', '#b45309', '#92400e',
                '#7c3aed', '#be185d', '#0d9488', '#4338ca'];
            charts['chartDrossDonut'] = new Chart(ctx, donutConfig(
                items.map(d => d.material_name),
                items.map(d => d.total_qty),
                'KG',
                drossColors
            ));

            legend.innerHTML = items.map((d, i) => `
                                                    <div class="cat-legend-item">
                                                        <div class="cat-dot" style="background:${drossColors[i % drossColors.length]}"></div>
                                                        <div class="cat-name" title="${esc(d.material_name)}">${esc(d.material_name)}</div>
                                                        <div>
                                                            <span class="cat-qty" style="color:var(--orange)">${fmt(d.total_qty, 1)}</span><span class="cat-unit">KG</span>
                                                        </div>
                                                    </div>`).join('');
        }

        // ── Shared doughnut chart config factory ──────────────────
        function donutConfig(labels, data, unit = 'KG', colors = COLORS) {
            return {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: colors.slice(0, labels.length),
                        borderColor: '#fff',
                        borderWidth: 3,
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#fff', borderColor: '#dde8e2', borderWidth: 1,
                            titleColor: '#1e2d26', bodyColor: '#3d5449',
                            titleFont: { family: 'Outfit', size: 12, weight: '700' },
                            bodyFont: { family: 'Outfit', size: 11 }, padding: 10, cornerRadius: 8,
                            callbacks: { label: ctx => ` ${fmt(ctx.parsed, 3)} ${unit}` }
                        }
                    }
                }
            };
        }

        // ── Daily line chart ──────────────────────────────────────
        function renderDailyChart() {
            const data = dashData.daily_output ?? [];
            if (!data.length) return;
            destroyChart('chartDaily');
            const ctx = document.getElementById('chartDaily').getContext('2d');
            charts['chartDaily'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => 'D' + d.day),
                    datasets: [{
                        label: 'FG Output (KG)',
                        data: data.map(d => d.qty),
                        borderColor: '#15803d',
                        backgroundColor: 'rgba(21,128,61,.12)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#15803d',
                        tension: 0.35,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#fff', borderColor: '#dde8e2', borderWidth: 1,
                            titleColor: '#1e2d26', bodyColor: '#3d5449',
                            titleFont: { family: 'Outfit', size: 11, weight: '700' },
                            bodyFont: { family: 'Outfit', size: 11 }, padding: 8, cornerRadius: 8,
                        }
                    },
                    scales: {
                        x: { grid: { color: '#edf2ef' }, ticks: { font: { family: 'Outfit', size: 10 }, color: '#6b8a78' } },
                        y: {
                            grid: { color: '#edf2ef' }, ticks: { font: { family: 'Outfit', size: 10 }, color: '#6b8a78' },
                            title: { display: true, text: 'KG', font: { family: 'Outfit', size: 10 }, color: '#6b8a78' }
                        }
                    }
                }
            });
        }

        // ── Pot production ────────────────────────────────────────
        function renderPotProduction() {
            const pots = dashData.pot_production ?? [];
            document.getElementById('potMonthBadge').textContent = dashData.month_label;
            const grid = document.getElementById('potGrid');

            if (!pots.length) {
                grid.innerHTML = '<div style="color:var(--txtmu);font-size:13px;padding:12px;grid-column:1/-1">No pot data for selected period.</div>';
                return;
            }

            grid.innerHTML = pots.map(p => `
                                                    <div class="pot-card">
                                                        <div class="pot-card-head">
                                                            <span class="pot-no">🫙 ${esc(p.pot_no)}</span>
                                                            <span class="pot-batches">${p.batch_count} batch${p.batch_count !== 1 ? 'es' : ''}</span>
                                                        </div>
                                                        <div class="pot-card-body">
                                                            <div class="pot-total-label">Total FG Output</div>
                                                            <div class="pot-total">${fmt(p.total_fg_qty, 1)} <span style="font-size:11px;color:var(--txtmu);font-weight:500">KG</span></div>
                                                            <div style="margin-top:10px">
                                                                ${(p.materials ?? []).slice(0, 5).map(m => `
                                                                    <div class="pot-mat">
                                                                        <span class="pot-mat-name" title="${esc(m.material_name)}">${esc(m.material_name)}</span>
                                                                        <span class="pot-mat-qty">${fmt(m.total_qty, 1)} KG</span>
                                                                    </div>`).join('')}
                                                            </div>
                                                        </div>
                                                    </div>`).join('');
        }

        // ── Avg HR / MT table (formula fixed in controller) ───────
        function renderAvgHrTable() {
            const rows = dashData.avg_hr_by_category ?? [];
            const tbody = document.getElementById('catHrBody');
            if (!rows.length) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:var(--txtmu)">No data available.</td></tr>';
                return;
            }
            const maxHr = Math.max(...rows.map(r => r.avg_hr_unit), 0.001);
            tbody.innerHTML = rows.map(r => `
                                                    <tr>
                                                        <td style="font-weight:600">${esc(r.category)}</td>
                                                        <td class="num" style="font-weight:700;color:var(--g)">${fmt(r.total_qty, 3)}</td>
                                                        <td class="num">${fmt(r.avg_hr_unit, 4)} <span style="font-size:10px;color:var(--txtmu)">Hrs/MT</span></td>
                                                        <td>
                                                            <div class="bar-cell">
                                                                <div class="bar-track">
                                                                    <div class="bar-fill" style="width:${Math.round((r.avg_hr_unit / maxHr) * 100)}%"></div>
                                                                </div>
                                                                <span style="font-size:10px;color:var(--txtmu);min-width:32px;text-align:right">${Math.round((r.avg_hr_unit / maxHr) * 100)}%</span>
                                                            </div>
                                                        </td>
                                                    </tr>`).join('');
        }

        // ════════════════════════════════════════════════════════════
        // ════════════════════════════════════════════════════════════
        // REPORT FILTERS (pot dropdown only — month picker is independent)
        // ════════════════════════════════════════════════════════════
        async function loadFilters() {
            const res = await apiFetch('/reports/refining/filters');
            if (!res?.ok) return;
            const json = await res.json();
            const data = json.data ?? {};
            const potSel = document.getElementById('rf_pot');
            (data.pots ?? []).forEach(p => {
                const o = document.createElement('option');
                o.value = p; o.textContent = 'Pot ' + p;
                potSel.appendChild(o);
            });
        }


        function buildParams(page) {
            const p = new URLSearchParams({
                date_from: document.getElementById('rf_from').value,
                date_to: document.getElementById('rf_to').value,
                batch_no: document.getElementById('rf_batch').value,
                pot_no: document.getElementById('rf_pot').value,
                status: document.getElementById('rf_status').value,
                sort_by: rSort, sort_dir: rDir,
                per_page: document.getElementById('rf_pp').value,
                page: page ?? rPage,
            });
            [...p.keys()].forEach(k => { if (!p.get(k)) p.delete(k); });
            return p.toString();
        }

        function resetFilters() {
            ['rf_from', 'rf_to', 'rf_batch'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
            document.getElementById('rf_pot').value = '';
            document.getElementById('rf_status').value = '';
            document.getElementById('rf_pp').value = '50';
            rSort = 'date'; rDir = 'desc'; rPage = 1; loadReport();
        }

        function debounceReport() { clearTimeout(rTimer); rTimer = setTimeout(() => loadReport(), 350); }

        async function loadReport(page = 1) {
            rPage = page;
            document.getElementById('reportBody').innerHTML = `<tr class="state-row"><td colspan="18"><span class="spinner"></span>Loading…</td></tr>`;
            document.getElementById('reportFoot').style.display = 'none';
            document.getElementById('pagBar').style.display = 'none';

            const res = await apiFetch(`/reports/refining/report?${buildParams(page)}`);
            if (!res?.ok) {
                document.getElementById('reportBody').innerHTML = `<tr class="state-row"><td colspan="18" style="color:var(--err)">Failed to load.</td></tr>`;
                return;
            }
            const json = await res.json();
            allRows = json.data ?? [];
            renderReportTable(allRows, json.meta);
            renderSummaryBadges(json.meta?.summary);
            renderPagination(json.meta);
            updateSortHeaders();
        }

        function renderReportTable(rows, meta) {
            const tbody = document.getElementById('reportBody'), tfoot = document.getElementById('reportFoot');
            if (!rows.length) { tbody.innerHTML = `<tr class="state-row"><td colspan="18">No records found.</td></tr>`; return; }

            let ftFg = 0, ftDr = 0, ftRaw = 0, ftLpg = 0, ftLpgL = 0, ftLpg2 = 0, ftLpg2L = 0, ftElec = 0, ftO2Nm = 0, ftO2 = 0, ftProc = 0;

            tbody.innerHTML = rows.map(r => {
                ftFg += r.total_fg_qty; ftDr += r.total_dross_qty; ftRaw += r.total_raw_qty;
                ftLpg += r.lpg_consumption; ftLpgL += r.lpg_consumption_ltr;
                ftLpg2 += r.lpg2_consumption; ftLpg2L += r.lpg2_consumption_ltr;
                ftElec += r.electricity_consumption; ftO2Nm += r.oxygen_flow_nm3; ftO2 += r.oxygen_consumption;
                ftProc += r.total_process_time;
                const st = r.status >= 1
                    ? '<span class="badge-st st-1">Submitted</span>'
                    : '<span class="badge-st st-0">Draft</span>';
                return `<tr>
                                                        <td style="white-space:nowrap">${r.date}</td>
                                                        <td style="font-weight:600">${esc(r.batch_no)}</td>
                                                        <td>${esc(r.pot_no)}</td>
                                                        <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis">${esc(r.material_name)}</td>
                                                        <td class="num" style="font-weight:700;color:var(--g)">${fmt(r.total_fg_qty, 3)}</td>
                                                        <td class="num" style="color:var(--orange)">${fmt(r.total_dross_qty, 3)}</td>
                                                        <td class="num">${fmt(r.total_raw_qty, 3)}</td>
                                                        <td class="num">${fmt(r.lpg_consumption, 3)}</td>
                                                        <td class="num">${fmt(r.lpg_consumption_ltr, 3)}</td>
                                                        <td class="num">${fmt(r.lpg2_consumption, 3)}</td>
                                                        <td class="num">${fmt(r.lpg2_consumption_ltr, 3)}</td>
                                                        <td class="num">${fmt(r.electricity_consumption, 3)}</td>
                                                        <td class="num">${fmt(r.oxygen_flow_nm3, 3)}</td>
                                                        <td class="num">${fmt(r.oxygen_consumption, 3)}</td>
                                                        <td class="num">${fmt(r.total_process_time, 0)}</td>
                                                        <td style="max-width:100px;overflow:hidden;text-overflow:ellipsis">${esc(r.remarks)}</td>
                                                        <td>${st}</td>
                                                        <td style="text-align:center">
                                                            <button onclick="openDrill(${r.id})"
                                                                style="padding:4px 10px;border-radius:6px;border:1.5px solid var(--bdr);background:var(--white);font-size:11px;font-weight:700;cursor:pointer;color:var(--g);font-family:'Outfit',sans-serif;transition:all .15s"
                                                                onmouseover="this.style.borderColor='var(--g)'" onmouseout="this.style.borderColor='var(--bdr)'">
                                                                View
                                                            </button>
                                                        </td>
                                                    </tr>`;
            }).join('');

            [['ftFg', ftFg], ['ftDross', ftDr], ['ftRaw', ftRaw], ['ftLpg', ftLpg], ['ftLpgL', ftLpgL],
            ['ftLpg2', ftLpg2], ['ftLpg2L', ftLpg2L], ['ftElec', ftElec], ['ftO2Nm', ftO2Nm],
            ['ftO2', ftO2], ['ftProc', ftProc]].forEach(([id, v]) => {
                const el = document.getElementById(id); if (el) el.textContent = fmt(v, 3);
            });
            tfoot.style.display = '';
            document.getElementById('reportCaption').textContent = meta
                ? `Showing ${rows.length} of ${meta.total.toLocaleString()} records` : `${rows.length} records`;
        }

        function renderSummaryBadges(s) {
            if (!s) return;
            document.getElementById('reportSummary').innerHTML = [
                ['FG Output', fmt(s.total_fg_qty, 3) + ' KG', '#15803d'],
                ['Dross', fmt(s.total_dross_qty, 3) + ' KG', '#ea580c'],
                ['Raw Input', fmt(s.total_raw_qty, 3) + ' KG', '#2563eb'],
                ['LPG Total', fmt(s.total_lpg, 3), '#d97706'],
                ['LPG2 Total', fmt(s.total_lpg2, 3), '#d97706'],
                ['Electricity', fmt(s.total_electricity, 3), '#7c3aed'],
                ['O₂ Cons', fmt(s.total_oxygen, 3) + ' KG', '#0d9488'],
            ].map(([l, v, c]) => `<div class="sbadge"><span class="sbadge-label">${l}</span><span class="sbadge-val" style="color:${c}">${v}</span></div>`).join('');
        }

        function renderPagination(meta) {
            const bar = document.getElementById('pagBar'), info = document.getElementById('pagInfo'), btns = document.getElementById('pagBtns');
            if (!meta || meta.last_page <= 1) { bar.style.display = 'none'; return; }
            bar.style.display = 'flex';
            info.textContent = `${(meta.current_page - 1) * meta.per_page + 1}–${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total.toLocaleString()}`;
            const pages = pagRange(meta.current_page, meta.last_page);
            btns.innerHTML = [
                `<button class="pag-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="loadReport(${meta.current_page - 1})">‹</button>`,
                ...pages.map(p => p === '…'
                    ? `<button class="pag-btn" disabled>…</button>`
                    : `<button class="pag-btn${p === meta.current_page ? ' active' : ''}" onclick="loadReport(${p})">${p}</button>`),
                `<button class="pag-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="loadReport(${meta.current_page + 1})">›</button>`
            ].join('');
        }

        function pagRange(cur, last) { const d = 2, r = []; for (let i = Math.max(2, cur - d); i <= Math.min(last - 1, cur + d); i++)r.push(i); if (r[0] > 2) r.unshift('…'); if (r[r.length - 1] < last - 1) r.push('…'); r.unshift(1); if (last !== 1) r.push(last); return r; }

        function sortReport(col) { rDir = rSort === col ? (rDir === 'asc' ? 'desc' : 'asc') : 'desc'; rSort = col; loadReport(1); }
        function updateSortHeaders() {
            document.querySelectorAll('#reportTable thead th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
                const col = th.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
                if (col === rSort) th.classList.add(rDir === 'asc' ? 'sort-asc' : 'sort-desc');
            });
        }

        // ════════════════════════════════════════════════════════════
        // DRILL-DOWN MODAL
        // ════════════════════════════════════════════════════════════
        function openDrill(batchId) {
            const row = allRows.find(r => r.id === batchId);
            if (!row) return;
            currentDrillRow = row;
            document.getElementById('drillTitle').textContent = `Batch ${row.batch_no} — ${row.date}`;

            const fgHtml = (row.fg_details ?? []).map(f => `
                                <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #edf2ef;font-size:12.5px">
                                    <span style="color:var(--txtm)">${esc(f.material)} <span style="font-size:10px;color:var(--txtmu)">(${esc(f.category)})</span></span>
                                    <strong style="color:var(--g)">${fmt(f.qty, 3)} KG</strong>
                                </div>`).join('') || '<div style="color:var(--txtmu);font-size:12px;padding:8px 0">No FG data</div>';

            const drossHtml = (row.dross_details ?? []).map(d => `
                                <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #edf2ef;font-size:12.5px">
                                    <span style="color:var(--txtm)">${esc(d.material)}</span>
                                    <strong style="color:var(--orange)">${fmt(d.qty, 3)} KG</strong>
                                </div>`).join('') || '<div style="color:var(--txtmu);font-size:12px;padding:8px 0">No dross data</div>';

            const rawMatRows = row.raw_materials ?? [];
            const rawHtml = rawMatRows.length ? `
                                <div style="overflow-x:auto;border-radius:8px;border:1px solid var(--bdr)">
                                    <table style="width:100%;border-collapse:collapse;font-size:12px">
                                        <thead>
                                            <tr style="background:var(--gl)">
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:left;border-bottom:2px solid var(--bdr)">Material</th>
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:left;border-bottom:2px solid var(--bdr)">Smelting Batch</th>
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:right;border-bottom:2px solid var(--bdr)">Qty (KG)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${rawMatRows.map(r => `<tr>
                                                <td style="padding:6px 10px;border-bottom:1px solid #edf2ef">${esc(r.material)}</td>
                                                <td style="padding:6px 10px;border-bottom:1px solid #edf2ef;color:var(--txtmu)">${esc(r.smelting_batch)}</td>
                                                <td style="padding:6px 10px;border-bottom:1px solid #edf2ef;text-align:right;font-weight:700;color:var(--blue)">${fmt(r.qty, 3)}</td>
                                            </tr>`).join('')}
                                        </tbody>
                                        <tfoot>
                                            <tr style="background:var(--gl)">
                                                <td colspan="2" style="padding:7px 10px;font-size:10px;color:var(--txtmu);text-align:right;font-weight:700;border-top:2px solid var(--bdr)">TOTAL</td>
                                                <td style="padding:7px 10px;text-align:right;font-weight:800;color:var(--g);border-top:2px solid var(--bdr)">${fmt(rawMatRows.reduce((s, r) => s + parseFloat(r.qty || 0), 0), 3)}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>` : '<div style="color:var(--txtmu);font-size:12px;padding:8px 0">No raw material data</div>';

            const chemRows = row.chemicals ?? [];
            const chemHtml = chemRows.length ? `
                                <div style="overflow-x:auto;border-radius:8px;border:1px solid var(--bdr)">
                                    <table style="width:100%;border-collapse:collapse;font-size:12px">
                                        <thead>
                                            <tr style="background:var(--gl)">
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:left;border-bottom:2px solid var(--bdr)">Chemical / Metal</th>
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:left;border-bottom:2px solid var(--bdr)">Smelting Batch</th>
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:right;border-bottom:2px solid var(--bdr)">Qty (KG)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${chemRows.map(c => `<tr>
                                                <td style="padding:6px 10px;border-bottom:1px solid #edf2ef">${esc(c.chemical)}</td>
                                                <td style="padding:6px 10px;border-bottom:1px solid #edf2ef;color:var(--txtmu)">${esc(c.smelting_batch)}</td>
                                                <td style="padding:6px 10px;border-bottom:1px solid #edf2ef;text-align:right;font-weight:700;color:var(--purple)">${fmt(c.qty, 3)}</td>
                                            </tr>`).join('')}
                                        </tbody>
                                        <tfoot>
                                            <tr style="background:var(--gl)">
                                                <td colspan="2" style="padding:7px 10px;font-size:10px;color:var(--txtmu);text-align:right;font-weight:700;border-top:2px solid var(--bdr)">TOTAL</td>
                                                <td style="padding:7px 10px;text-align:right;font-weight:800;color:var(--g);border-top:2px solid var(--bdr)">${fmt(chemRows.reduce((s, c) => s + parseFloat(c.qty || 0), 0), 3)}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>` : '<div style="color:var(--txtmu);font-size:12px;padding:8px 0">No chemical / metal data</div>';

            const procHtml = (row.process_details ?? []).map(p => `
                                <tr style="font-size:12px">
                                    <td style="padding:5px 10px;border-bottom:1px solid #edf2ef;font-weight:600">${esc(p.process)}</td>
                                    <td style="padding:5px 10px;border-bottom:1px solid #edf2ef;color:var(--txtm)">${p.start}</td>
                                    <td style="padding:5px 10px;border-bottom:1px solid #edf2ef;color:var(--txtm)">${p.end}</td>
                                    <td style="padding:5px 10px;border-bottom:1px solid #edf2ef;text-align:right;font-variant-numeric:tabular-nums">${fmt(p.total_time, 1)} min</td>
                                </tr>`).join('') || '<tr><td colspan="4" style="padding:12px;text-align:center;color:var(--txtmu)">No process data</td></tr>';

            document.getElementById('drillBody').innerHTML = `
                                <div class="two-col" style="gap:20px;margin-bottom:16px">
                                    <div>
                                        <div class="section-divider">📦 Finished Goods Output</div>
                                        ${fgHtml}
                                        <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px">
                                            <strong>Total</strong>
                                            <strong style="color:var(--g)">${fmt(row.total_fg_qty, 3)} KG</strong>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="section-divider">🪨 Dross Output</div>
                                        ${drossHtml}
                                        <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px">
                                            <strong>Total</strong>
                                            <strong style="color:var(--orange)">${fmt(row.total_dross_qty, 3)} KG</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-divider">🧱 Lead Raw Materials</div>
                                <div style="margin-bottom:16px">${rawHtml}</div>

                                <div class="section-divider">⚗️ Chemicals &amp; Metals</div>
                                <div style="margin-bottom:16px">${chemHtml}</div>

                                <div class="section-divider">⚡ Consumption Summary</div>
                                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-bottom:16px">
                                    ${[
                    ['LPG (m³)', row.lpg_consumption, '#d97706'],
                    ['LPG (Ltr)', row.lpg_consumption_ltr, '#d97706'],
                    ['LPG2 (m³)', row.lpg2_consumption, '#b45309'],
                    ['LPG2 (Ltr)', row.lpg2_consumption_ltr, '#b45309'],
                    ['Electricity', row.electricity_consumption, '#7c3aed'],
                    ['O₂ Flow (NM³)', row.oxygen_flow_nm3, '#0d9488'],
                    ['O₂ Cons (KG)', row.oxygen_consumption, '#0d9488'],
                    ['Process (min)', row.total_process_time, '#2563eb'],
                ].map(([l, v, c]) => `
                                        <div style="background:var(--gxl);border:1px solid var(--bdr);border-radius:8px;padding:10px 12px;text-align:center">
                                            <div style="font-size:9px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--txtmu);margin-bottom:4px">${l}</div>
                                            <div style="font-size:16px;font-weight:800;color:${c};letter-spacing:-.5px">${fmt(v, 3)}</div>
                                        </div>`).join('')}
                                </div>

                                <div class="section-divider">🔄 Process Details</div>
                                <div style="overflow-x:auto;border-radius:8px;border:1px solid var(--bdr);margin-bottom:16px">
                                    <table style="width:100%;border-collapse:collapse">
                                        <thead>
                                            <tr style="background:var(--gl)">
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:left;border-bottom:2px solid var(--bdr)">Process</th>
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:left;border-bottom:2px solid var(--bdr)">Start</th>
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:left;border-bottom:2px solid var(--bdr)">End</th>
                                                <th style="padding:8px 10px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:right;border-bottom:2px solid var(--bdr)">Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>${procHtml}</tbody>
                                    </table>
                                </div>
                                ${row.remarks && row.remarks !== '—'
                    ? `<div style="margin-top:12px;padding:10px 14px;background:var(--gxl);border-radius:8px;border:1px solid var(--bdr);font-size:12.5px;color:var(--txtm)"><strong style="color:var(--g)">Remarks:</strong> ${esc(row.remarks)}</div>`
                    : ''}`;

            document.getElementById('drillModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeDrill(e) {
            if (e && e.target !== document.getElementById('drillModal')) return;
            document.getElementById('drillModal').classList.remove('open');
            document.body.style.overflow = '';
        }
        function exportDetailExcel() {
            const r = currentDrillRow;
            if (!r) return;
            const wb = XLSX.utils.book_new();

            // Sheet 1: Summary
            const sumData = [
                ['Field', 'Value'],
                ['Batch No', r.batch_no], ['Date', r.date], ['Pot No', r.pot_no],
                ['Material', r.material_name],
                ['FG Output (KG)', r.total_fg_qty], ['Dross (KG)', r.total_dross_qty],
                ['Raw Input (KG)', r.total_raw_qty],
                ['LPG (m³)', r.lpg_consumption], ['LPG (Ltr)', r.lpg_consumption_ltr],
                ['LPG2 (m³)', r.lpg2_consumption], ['LPG2 (Ltr)', r.lpg2_consumption_ltr],
                ['Electricity', r.electricity_consumption],
                ['O₂ Flow (NM³)', r.oxygen_flow_nm3], ['O₂ Cons (KG)', r.oxygen_consumption],
                ['Process Time (min)', r.total_process_time],
                ['Remarks', r.remarks], ['Status', r.status >= 1 ? 'Submitted' : 'Draft'],
            ];
            const ws1 = XLSX.utils.aoa_to_sheet(sumData);
            ws1['!cols'] = [{ wch: 26 }, { wch: 20 }];
            XLSX.utils.book_append_sheet(wb, ws1, 'Summary');

            // Sheet 2: FG Output
            if (r.fg_details?.length) {
                const fgData = [['Material', 'Category', 'Qty (KG)'],
                ...r.fg_details.map(f => [f.material, f.category, f.qty])];
                const ws2 = XLSX.utils.aoa_to_sheet(fgData);
                ws2['!cols'] = [{ wch: 26 }, { wch: 18 }, { wch: 12 }];
                XLSX.utils.book_append_sheet(wb, ws2, 'FG Output');
            }

            // Sheet 3: Dross
            if (r.dross_details?.length) {
                const drData = [['Material', 'Qty (KG)'],
                ...r.dross_details.map(d => [d.material, d.qty])];
                const ws3 = XLSX.utils.aoa_to_sheet(drData);
                ws3['!cols'] = [{ wch: 26 }, { wch: 12 }];
                XLSX.utils.book_append_sheet(wb, ws3, 'Dross Output');
            }

            // Sheet 4: Raw Materials
            if (r.raw_materials?.length) {
                const rmData = [['Material', 'Smelting Batch', 'Qty (KG)'],
                ...r.raw_materials.map(m => [m.material, m.smelting_batch, m.qty])];
                const ws4 = XLSX.utils.aoa_to_sheet(rmData);
                ws4['!cols'] = [{ wch: 26 }, { wch: 18 }, { wch: 12 }];
                XLSX.utils.book_append_sheet(wb, ws4, 'Raw Materials');
            }

            // Sheet 5: Chemicals & Metals
            if (r.chemicals?.length) {
                const chData = [['Chemical / Metal', 'Smelting Batch', 'Qty (KG)'],
                ...r.chemicals.map(c => [c.chemical, c.smelting_batch, c.qty])];
                const ws5 = XLSX.utils.aoa_to_sheet(chData);
                ws5['!cols'] = [{ wch: 26 }, { wch: 18 }, { wch: 12 }];
                XLSX.utils.book_append_sheet(wb, ws5, 'Chemicals & Metals');
            }

            // Sheet 6: Process Details
            if (r.process_details?.length) {
                const pdData = [['Process', 'Start', 'End', 'Time (min)'],
                ...r.process_details.map(p => [p.process, p.start, p.end, p.total_time])];
                const ws6 = XLSX.utils.aoa_to_sheet(pdData);
                ws6['!cols'] = [{ wch: 22 }, { wch: 10 }, { wch: 10 }, { wch: 14 }];
                XLSX.utils.book_append_sheet(wb, ws6, 'Process Details');
            }

            XLSX.writeFile(wb, `Refining_${r.batch_no}_${r.date_raw || r.date}.xlsx`);
        }

        // ════════════════════════════════════════════════════════════
        // EXCEL EXPORT
        // ════════════════════════════════════════════════════════════
        async function exportExcel() {
            let all = [], page = 1, lastPage = 1;
            do {
                const res = await apiFetch(`/reports/refining/report?${buildParams(page)}&per_page=500`);
                if (!res?.ok) break;
                const json = await res.json();
                all.push(...(json.data ?? [])); lastPage = json.meta?.last_page ?? 1; page++;
            } while (page <= lastPage);

            if (!all.length) return;

            const headers = ['Date', 'Batch No', 'Pot No', 'Material',
                'FG Output (KG)', 'Dross (KG)', 'Raw Input (KG)',
                'LPG (m³)', 'LPG (Ltr)', 'LPG2 (m³)', 'LPG2 (Ltr)',
                'Electricity', 'O₂ Flow (NM³)', 'O₂ Cons (KG)',
                'Process Time (min)', 'Remarks', 'Status'];

            const wsData = [headers, ...all.map(r => [
                r.date, r.batch_no, r.pot_no, r.material_name,
                r.total_fg_qty, r.total_dross_qty, r.total_raw_qty,
                r.lpg_consumption, r.lpg_consumption_ltr,
                r.lpg2_consumption, r.lpg2_consumption_ltr,
                r.electricity_consumption, r.oxygen_flow_nm3, r.oxygen_consumption,
                r.total_process_time, r.remarks, r.status >= 1 ? 'Submitted' : 'Draft'
            ])];

            const fgHeaders = ['Batch No', 'Date', 'Pot', 'Material (FG)', 'Category', 'Qty (KG)'];
            const fgData = [fgHeaders];
            all.forEach(r => (r.fg_details ?? []).forEach(f => {
                fgData.push([r.batch_no, r.date, r.pot_no, f.material, f.category, f.qty]);
            }));

            const procHeaders = ['Batch No', 'Date', 'Process', 'Start', 'End', 'Total Time (min)'];
            const procData = [procHeaders];
            all.forEach(r => (r.process_details ?? []).forEach(p => {
                procData.push([r.batch_no, r.date, p.process, p.start, p.end, p.total_time]);
            }));

            const wb = XLSX.utils.book_new();
            const ws1 = XLSX.utils.aoa_to_sheet(wsData);
            const ws2 = XLSX.utils.aoa_to_sheet(fgData);
            const ws3 = XLSX.utils.aoa_to_sheet(procData);
            ws1['!cols'] = headers.map(() => ({ wch: 16 }));
            ws2['!cols'] = fgHeaders.map(() => ({ wch: 18 }));
            ws3['!cols'] = procHeaders.map(() => ({ wch: 16 }));
            XLSX.utils.book_append_sheet(wb, ws1, 'Refining Report');
            XLSX.utils.book_append_sheet(wb, ws2, 'FG Details');
            XLSX.utils.book_append_sheet(wb, ws3, 'Process Details');
            XLSX.writeFile(wb, `Refining_Report_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.xlsx`);
        }

        // ════════════════════════════════════════════════════════════
        // UTILS
        // ════════════════════════════════════════════════════════════
        function destroyChart(id) { if (charts[id]) { charts[id].destroy(); delete charts[id]; } }
        function fmt(n, d = 3) { if (n === null || n === undefined || n === '') return '—'; const v = parseFloat(n); if (isNaN(v)) return '—'; return v.toLocaleString(undefined, { minimumFractionDigits: d, maximumFractionDigits: d }); }
        function esc(s) { if (!s || s === '—') return '—'; return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    </script>
@endpush