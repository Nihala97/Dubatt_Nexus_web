@extends('admin.layouts.app')
@section('title', 'Smelting Batches Log')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <a href="{{ route('admin.mes.smelting.index') }}" style="color:var(--text-muted);text-decoration:none">Smelting</a>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <strong id="breadcrumbTitle">Loading…</strong>
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
            --warn: #d97706;
            --shadow: 0 2px 10px rgba(0, 0, 0, .07);
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

        .sdd { display:block; width:100%; position:relative; }
        .sdd-trigger {
            width:100%; display:flex; align-items:center; justify-content:space-between;
            padding:7px 10px 7px 32px;
            border:1.5px solid var(--bdr); border-radius:6px;
            background:var(--gxl);
            font-family:'Outfit',sans-serif; font-size:12.5px; color:var(--txt);
            cursor:pointer; user-select:none; gap:6px; min-height:36px;
            transition:border-color .18s, background .18s;
        }
        .sdd-trigger:hover,
        .sdd.open > .sdd-trigger {
            border-color:var(--g); background:var(--white);
        }
        .sdd-trigger-text {
            flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-align:left;
        }
        .sdd-trigger-text.placeholder { color:var(--txtmu); }
        .sdd-trigger-chevron {
            width:12px; height:12px; stroke:var(--txtmu); fill:none;
            stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;
            flex-shrink:0; transition:transform .18s;
        }
        .sdd.open > .sdd-trigger .sdd-trigger-chevron { transform:rotate(180deg); stroke:var(--g); }
        .sdd-clear {
            display:none; width:12px; height:12px;
            stroke:var(--txtmu); fill:none; stroke-width:2.5;
            stroke-linecap:round; stroke-linejoin:round;
            flex-shrink:0; cursor:pointer; transition:stroke .15s;
        }
        .sdd-clear:hover { stroke:#dc2626; }
        .sdd.open > .sdd-trigger .sdd-clear,
        .sdd-trigger:hover .sdd-clear { display:block; }
        
        /* Portal — fixed, appears above everything */
        .sdd-portal {
            position:fixed; z-index:9999;
            background:#fff; border:1.5px solid var(--g);
            border-radius:10px; box-shadow:0 6px 24px rgba(0,0,0,.16);
            min-width:220px; overflow:hidden;
            display:none; animation:sddIn .12s ease;
        }
        .sdd-portal.visible { display:block; }
        @keyframes sddIn {
            from { opacity:0; transform:translateY(-4px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .sdd-search-wrap {
            padding:8px 10px; border-bottom:1px solid var(--bdr); position:relative;
        }
        .sdd-search-ico {
            position:absolute; left:18px; top:50%; transform:translateY(-50%);
            width:13px; height:13px; stroke:var(--txtmu); fill:none;
            stroke-width:2; stroke-linecap:round; stroke-linejoin:round; pointer-events:none;
        }
        .sdd-search {
            width:100%; padding:7px 10px 7px 32px;
            border:1.5px solid var(--bdr); border-radius:7px;
            background:var(--gxl); font-family:'Outfit',sans-serif;
            font-size:12.5px; color:var(--txt); outline:none;
            transition:border-color .18s; box-sizing:border-box;
        }
        .sdd-search:focus { border-color:var(--g); background:#fff; }
        .sdd-search::placeholder { color:var(--txtmu); }
        .sdd-list { max-height:220px; overflow-y:auto; padding:4px 0; }
        .sdd-item {
            padding:8px 14px; font-size:12.5px; cursor:pointer;
            transition:background .1s; display:flex; align-items:center;
            gap:9px; color:var(--txt);
        }
        .sdd-item:hover { background:#f0f9f4; color:var(--g); }
        .sdd-item.selected { background:#e8f5ed; color:var(--g); font-weight:600; }
        .sdd-item-check {
            width:14px; height:14px; flex-shrink:0;
            stroke:transparent; fill:none; stroke-width:2.5;
            stroke-linecap:round; stroke-linejoin:round;
        }
        .sdd-item.selected .sdd-item-check { stroke:var(--g); }
        .sdd-empty { padding:18px 14px; font-size:12px; color:var(--txtmu); text-align:center; }

        /* ── Page header ── */
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

        /* ── Buttons ── */
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

        .btn-primary {
            background: var(--g);
            color: #fff
        }

        .btn-primary:hover {
            background: var(--gd);
            box-shadow: 0 4px 14px rgba(26, 122, 58, .28);
            transform: translateY(-1px)
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

        .btn-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1.5px solid #fca5a5
        }

        .btn-danger:hover {
            background: #fca5a5
        }

        .btn-sm {
            padding: 7px 13px;
            font-size: 12.5px
        }

        .btn-add {
            background: var(--g);
            color: #fff;
            padding: 8px 14px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all .2s
        }

        .btn-add:hover {
            background: var(--gd);
            transform: translateY(-1px)
        }

        .btn-add svg,
        .btn-add-sm svg {
            width: 13px;
            height: 13px;
            stroke: #fff;
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        /* ── Card ── */
        .card {
            background: var(--white);
            border: 1px solid var(--bdr);
            border-radius: var(--r);
            box-shadow: var(--shadow);
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

        .card-head svg {
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
            padding: 22px 20px
        }

        /* ── Two-column layout ── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px
        }

        .three-col {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px
        }

        /* ── Form fields ── */
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

        .field label .req {
            color: var(--err)
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
            pointer-events: none
        }

        input[type=text],
        input[type=number],
        input[type=date],
        input[type=time],
        input[type=datetime-local],
        select,
        textarea {
            width: 100%;
            padding: 9px 12px 9px 36px;
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
        select:focus,
        textarea:focus {
            border-color: var(--g);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .09)
        }

        input[readonly],
        input.ro {
            background: #eef6f1;
            color: var(--txtm);
            cursor: default;
            border-color: #c8dfd1
        }

        input[readonly]:focus,
        input.ro:focus {
            box-shadow: none;
            border-color: #c8dfd1
        }

        input::placeholder {
            color: var(--txtmu);
            font-size: 12px
        }

        .sw::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid var(--txtmu);
            pointer-events: none
        }

        /* ── Status badge ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 11px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700
        }

        .badge-draft {
            background: #e0e7ff;
            color: #3730a3
        }

        .badge-submitted {
            background: #d1fae5;
            color: #065f46
        }

        /* ── Consumption mini card ── */
        .cons-card {
            background: var(--gxl);
            border: 1.5px solid var(--bdr);
            border-radius: 9px;
            padding: 14px 16px
        }

        .cons-title {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--g);
            margin-bottom: 12px
        }

        .cons-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px
        }

        .cons-total {
            grid-column: 1/-1;
            background: var(--gl);
            border-radius: 6px;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px
        }

        .cons-total-label {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--g)
        }

        .cons-total-val {
            font-size: 14px;
            font-weight: 800;
            color: var(--g)
        }

        /* ── Process table ── */
        .proc-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 560px
        }

        .proc-table thead th {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 9px 10px;
            border-bottom: 2px solid var(--bdr);
            text-align: left;
            white-space: nowrap
        }

        .proc-table tbody td {
            padding: 6px 6px;
            border-bottom: 1px solid #edf2ef;
            vertical-align: middle
        }

        .proc-table tbody tr:hover td {
            background: #f7fbf8
        }

        /* ── Temp table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse
        }

        .data-table thead th {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 9px 10px;
            border-bottom: 2px solid var(--bdr);
            text-align: left
        }

        .data-table tbody td {
            padding: 6px 6px;
            border-bottom: 1px solid #edf2ef;
            vertical-align: top
        }

        .data-table tfoot td {
            background: var(--gl);
            font-weight: 700;
            font-size: 12.5px;
            color: var(--g);
            padding: 8px 10px
        }

        /* Row inputs */
        .ri {
            width: 100%;
            padding: 7px 10px;
            border: 1.5px solid var(--bdr);
            border-radius: 6px;
            background: var(--gxl);
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            color: var(--txt);
            outline: none;
            transition: border-color .18s, background .18s
        }

        .ri:focus {
            border-color: var(--g);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .08)
        }

        .rs {
            width: 100%;
            padding: 7px 26px 7px 10px;
            border: 1.5px solid var(--bdr);
            border-radius: 6px;
            background: var(--gxl);
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            color: var(--txt);
            outline: none;
            appearance: none;
            transition: border-color .18s, background .18s
        }

        .rs:focus {
            border-color: var(--g);
            background: var(--white)
        }

        .sc::after {
            content: '';
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid var(--txtmu);
            pointer-events: none
        }

        /* Process button cells */
        .proc-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap
        }

        .proc-start {
            background: #16a34a;
            color: #fff
        }

        .proc-start:hover {
            background: #15803d
        }

        .proc-end {
            background: #dc2626;
            color: #fff
        }

        .proc-end:hover {
            background: #b91c1c
        }

        /* Delete btn */
        .del-btn {
            width: 26px;
            height: 26px;
            background: #fee2e2;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .18s;
            margin: auto
        }

        .del-btn:hover {
            background: #fca5a5
        }

        .del-btn svg {
            width: 12px;
            height: 12px;
            stroke: #dc2626;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        /* Alert */
        .form-alert {
            display: none;
            padding: 11px 15px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 500;
            margin-bottom: 16px
        }

        .form-alert.error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            display: block
        }

        .form-alert.success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            display: block
        }

        /* Sticky footer */
        .form-actions {
            position: sticky;
            bottom: 0;
            background: var(--white);
            border-top: 1px solid var(--bdr);
            padding: 13px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            z-index: 20;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, .06)
        }

        /* Autosave dot */
        .as-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px
        }

        .as-dot.saving {
            background: var(--warn);
            animation: pulse .8s infinite
        }

        .as-dot.saved {
            background: var(--g)
        }

        .as-dot.error {
            background: var(--err)
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        /* Right panel mini inputs (no left icon needed) */
        .ni {
            padding: 9px 12px
        }

        /* Readonly notice */
        .readonly-notice {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 16px;
            display: none
        }

        /* Scrollable table wrap */
        .tbl-wrap {
            overflow-x: auto
        }

        /* ── BBSU Lot Selection Modal ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s
        }

        .modal-overlay.open {
            opacity: 1;
            pointer-events: all
        }

        .modal-box {
            background: #fff;
            border-radius: 14px;
            width: 100%;
            max-width: 760px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .22);
            transform: translateY(12px);
            transition: transform .2s
        }

        .modal-overlay.open .modal-box {
            transform: translateY(0)
        }

        .modal-head {
            padding: 16px 22px;
            border-bottom: 1px solid var(--bdr);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--gl);
            border-radius: 14px 14px 0 0
        }

        .modal-head-left {
            display: flex;
            align-items: center;
            gap: 9px
        }

        .modal-head-left svg {
            width: 16px;
            height: 16px;
            stroke: var(--g);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .modal-title {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--g)
        }

        .modal-subtitle {
            font-size: 11px;
            color: var(--txtmu);
            margin-top: 1px
        }

        .modal-close {
            width: 30px;
            height: 30px;
            background: var(--bdr);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--txtm);
            transition: all .15s
        }

        .modal-close:hover {
            background: #fca5a5;
            color: #dc2626
        }

        .modal-body {
            padding: 18px 22px;
            overflow-y: auto;
            flex: 1
        }

        .modal-footer {
            padding: 14px 22px;
            border-top: 1px solid var(--bdr);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #fafcfb;
            border-radius: 0 0 14px 14px
        }

        /* Lot table inside modal */
        .lot-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 560px
        }

        .lot-table thead th {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--g);
            background: var(--gl);
            padding: 9px 12px;
            border-bottom: 2px solid var(--bdr);
            text-align: left
        }

        .lot-table tbody tr {
            cursor: pointer;
            transition: background .12s
        }

        .lot-table tbody tr:hover td {
            background: #f2faf5
        }

        .lot-table tbody tr.selected td {
            background: #d1fae5
        }

        .lot-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #edf2ef;
            font-size: 13px;
            vertical-align: middle
        }

        .lot-table .avail-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700
        }

        .lot-table .avail-good {
            background: #d1fae5;
            color: #065f46
        }

        .lot-table .avail-low {
            background: #fef9c3;
            color: #854d0e
        }

        .lot-table .avail-zero {
            background: #fee2e2;
            color: #991b1b
        }

        .assign-input {
            padding: 7px 10px;
            border: 1.5px solid var(--bdr);
            border-radius: 7px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            color: var(--txt);
            width: 110px;
            outline: none;
            transition: border-color .18s
        }

        .assign-input:focus {
            border-color: var(--g);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .09)
        }

        .lot-loading {
            text-align: center;
            padding: 32px;
            color: var(--txtmu);
            font-size: 13px
        }

        .lot-empty {
            text-align: center;
            padding: 32px;
            color: var(--txtmu);
            font-size: 13px
        }

        .lot-bbsu-tag {
            display: inline-block;
            padding: 2px 8px;
            background: var(--gl);
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            color: var(--g);
            font-variant-numeric: tabular-nums
        }

        /* Two panel layout for raw materials + flux */
        .panel-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px
        }

        /* Responsive */
        @media(max-width:900px) {

            .two-col,
            .three-col,
            .panel-row {
                grid-template-columns: 1fr
            }

            .cons-grid {
                grid-template-columns: 1fr 1fr
            }
        }

        @media(max-width:560px) {
            .form-actions {
                flex-direction: column;
                align-items: stretch
            }

            .form-actions .btn {
                justify-content: center
            }

            .cons-grid {
                grid-template-columns: 1fr
            }
        }
    </style>
@endpush

@section('content')

    <div class="sdd-portal" id="sddPortal">
        <div class="sdd-search-wrap">
            <svg class="sdd-search-ico" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input class="sdd-search" id="sddPortalSearch" placeholder="Search…"
                autocomplete="off"
                oninput="sddPortalFilter(this.value)"
                onkeydown="sddPortalKeydown(event)">
        </div>
        <div class="sdd-list" id="sddPortalList"></div>
    </div>
    {{-- ── Page header ── --}}
    <div class="ph">
        <div>
            <h2 id="pageTitle">Loading…</h2>
            <p id="pageSubtitle"></p>
            <div id="statusBadge" style="margin-top:6px"></div>
        </div>
        <div style="display:flex;gap:8px" id="headerActions">
            <a href="{{ route('admin.mes.smelting.index') }}" class="btn btn-outline btn-sm">
                <svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg> Back
            </a>
        </div>
    </div>

    <div id="readonlyNotice" class="readonly-notice">
        🔒 This batch has been submitted and is locked from editing.
    </div>
    <div id="formAlert" class="form-alert"></div>

    {{-- ── BBSU Lot Selection Modal ── --}}
    <div class="modal-overlay" id="bbsuLotModal" onclick="closeBbsuModal(event)">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-head-left">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                    </svg>
                    <div>
                        <div class="modal-title" id="bbsuModalTitle">Available Stock</div>
                        <div class="modal-subtitle" id="bbsuModalSubtitle">Select the quantity to assign from available
                            stock</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeBbsuModal()" title="Close">✕</button>
            </div>
            <div class="modal-body">
                <div id="bbsuLotTableWrap">
                    <div class="lot-loading" id="bbsuLotLoading">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--g)" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            style="animation:spin 1s linear infinite;display:inline-block">
                            <line x1="12" y1="2" x2="12" y2="6" />
                            <line x1="12" y1="18" x2="12" y2="22" />
                            <line x1="4.93" y1="4.93" x2="7.76" y2="7.76" />
                            <line x1="16.24" y1="16.24" x2="19.07" y2="19.07" />
                            <line x1="2" y1="12" x2="6" y2="12" />
                            <line x1="18" y1="12" x2="22" y2="12" />
                            <line x1="4.93" y1="19.07" x2="7.76" y2="16.24" />
                            <line x1="16.24" y1="7.76" x2="19.07" y2="4.93" />
                        </svg>
                        <p style="margin-top:8px">Loading available stock…</p>
                    </div>
                    <div class="lot-empty" id="bbsuLotEmpty" style="display:none">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c8dfd1" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 10px">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        No available stock found for this material.
                    </div>
                    <div class="tbl-wrap" id="bbsuLotTableScroll" style="display:none">
                        <table class="lot-table">
                            <thead>
                                <tr>
                                    <th>DOC NO</th>
                                    <th>MATERIAL DESCRIPTION</th>
                                    <th>UNIT</th>
                                    <th>AVAILABLE QTY</th>
                                    <th>ASSIGN QTY</th>
                                </tr>
                            </thead>
                            <tbody id="bbsuLotTbody"></tbody>
                            <tfoot id="bbsuLotTfoot" style="display:none">
                                <tr style="background:var(--gl)">
                                    <td colspan="3"
                                        style="text-align:right;padding:8px 12px;font-size:11px;font-weight:700;color:var(--g);letter-spacing:.7px">
                                        TOTAL ASSIGN QTY</td>
                                    <td colspan="2" style="padding:8px 12px">
                                        <span id="bbsuTotalAssign"
                                            style="font-size:14px;font-weight:800;color:var(--g)">0.000</span>
                                        <span style="font-size:11px;color:var(--txtmu);margin-left:4px">KG</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeBbsuModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" id="bbsuConfirmBtn" onclick="confirmBbsuSelection()" disabled>
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Confirm Selection
                </button>
            </div>
        </div>
    </div>
    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }
    </style>

    {{-- ── Output QTY Window Modal ── --}}
    <div class="modal-overlay" id="outputQtyModal" onclick="closeOutputModal(event)">
        <div class="modal-box" style="max-width:420px">
            <div class="modal-head">
                <div class="modal-head-left">
                    <svg viewBox="0 0 24 24">
                        <path d="M5 8l6 6 6-6" />
                    </svg>
                    <div>
                        <div class="modal-title">Output QTY Window</div>
                        <div class="modal-subtitle" id="outputModalSubtitle">Enter block weights — total auto-calculates
                        </div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeOutputModal()" title="Close">✕</button>
            </div>
            <div class="modal-body" style="padding:0">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                        <tr style="background:var(--gl)">
                            <th
                                style="padding:9px 14px;font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:right;width:80px">
                                SL NO</th>
                            <th
                                style="padding:9px 14px;font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:right">
                                QTY (KG)</th>
                        </tr>
                    </thead>
                    <tbody id="outputBlockTbody">
                        {{-- rows added by JS --}}
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--gl);border-top:2px solid var(--bdr)">
                            <td
                                style="padding:10px 14px;font-size:11px;font-weight:800;letter-spacing:.8px;color:var(--g);text-align:right">
                                TOTAL</td>
                            <td style="padding:10px 14px;text-align:right">
                                <span id="outputBlockTotal"
                                    style="font-size:15px;font-weight:800;color:var(--g)">0.000</span>
                                <span style="font-size:11px;color:var(--txtmu);margin-left:3px">KG</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer" style="justify-content:space-between">
                <button class="btn btn-outline btn-sm" onclick="addOutputRow()" style="gap:5px">
                    <svg viewBox="0 0 24 24"
                        style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    ADD
                </button>
                <div style="display:flex;gap:8px">
                    <button class="btn btn-outline btn-sm" onclick="closeOutputModal()">Cancel</button>
                    <button class="btn btn-primary btn-sm" onclick="confirmOutputQty()">
                        <svg viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Flux BBSU Lot Selection Modal ── --}}
    <div class="modal-overlay" id="fluxLotModal" onclick="closeFluxModal(event)">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-head-left">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18" />
                    </svg>
                    <div>
                        <div class="modal-title" id="fluxModalTitle">Available Stock</div>
                        <div class="modal-subtitle" id="fluxModalSubtitle">Select the quantity to assign from available
                            stock</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeFluxModal()" title="Close">✕</button>
            </div>
            <div class="modal-body">
                <div id="fluxLotTableWrap">
                    <div class="lot-loading" id="fluxLotLoading">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--g)" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            style="animation:spin 1s linear infinite;display:inline-block">
                            <line x1="12" y1="2" x2="12" y2="6" />
                            <line x1="12" y1="18" x2="12" y2="22" />
                            <line x1="4.93" y1="4.93" x2="7.76" y2="7.76" />
                            <line x1="16.24" y1="16.24" x2="19.07" y2="19.07" />
                            <line x1="2" y1="12" x2="6" y2="12" />
                            <line x1="18" y1="12" x2="22" y2="12" />
                            <line x1="4.93" y1="19.07" x2="7.76" y2="16.24" />
                            <line x1="16.24" y1="7.76" x2="19.07" y2="4.93" />
                        </svg>
                        <p style="margin-top:8px">Loading available stock…</p>
                    </div>
                    <div class="lot-empty" id="fluxLotEmpty" style="display:none">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c8dfd1" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 10px">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        No available stock found for this material.
                    </div>
                    <div class="tbl-wrap" id="fluxLotTableScroll" style="display:none">
                        <table class="lot-table">
                            <thead>
                                <tr>
                                    <th>DOC NO</th>
                                    <th>MATERIAL DESCRIPTION</th>
                                    <th>UNIT</th>
                                    <th>AVAILABLE QTY</th>
                                    <th>ASSIGN QTY</th>
                                </tr>
                            </thead>
                            <tbody id="fluxLotTbody"></tbody>
                            <tfoot id="fluxLotTfoot" style="display:none">
                                <tr style="background:var(--gl)">
                                    <td colspan="3"
                                        style="text-align:right;padding:8px 12px;font-size:11px;font-weight:700;color:var(--g);letter-spacing:.7px">
                                        TOTAL ASSIGN QTY</td>
                                    <td colspan="2" style="padding:8px 12px">
                                        <span id="fluxTotalAssign"
                                            style="font-size:14px;font-weight:800;color:var(--g)">0.000</span>
                                        <span style="font-size:11px;color:var(--txtmu);margin-left:4px">KG</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeFluxModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" id="fluxConfirmBtn" onclick="confirmFluxSelection()" disabled>
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    OK
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
    SECTION 1 — Primary Details
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
                <span>Primary Details</span>
            </div>
        </div>
        <div class="card-body">
            <div class="three-col">

                <div class="field">
                    <label>Batch No <span class="req">*</span></label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="5" y="2" width="14" height="20" rx="2" />
                            <line x1="9" y1="9" x2="15" y2="9" />
                            <line x1="9" y1="13" x2="15" y2="13" />
                        </svg>
                        <input type="text" id="batch_no" readonly class="ro" placeholder="Auto-generated…">
                    </div>
                </div>

                <div class="field">
                    <label>Date <span class="req">*</span></label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <input type="date" id="date" required>
                    </div>
                </div>

                <div class="field">
                    <label>Rotary No <span class="req">*</span></label>
                    <div class="iw sw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                        </svg>
                        <select id="rotary_no" required>
                            <option value="">Select…</option>
                            <option value="1">Rotary 1</option>
                            <option value="2">Rotary 2</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Start Time</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <input type="time" id="start_time">
                    </div>
                </div>

                <div class="field">
                    <label>End Time</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <input type="time" id="end_time">
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
    SECTION 2 — Raw Materials + Flux/Chemicals (side by side)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="panel-row">

        {{-- Raw Materials --}}
        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                    </svg>
                    <span>Raw Materials</span>
                </div>
                <button class="btn-add btn-sm" onclick="addRawRow()" id="btnAddRaw">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg> Add
                </button>
            </div>
            <div class="card-body" style="padding:0">
                <div class="tbl-wrap">
                    <table class="data-table" id="rawTable">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th>Raw Material</th>
                                <th>QTY (KG)</th>
                                <th>Yield %</th>
                                <th>Expected</th>
                                <th style="width:32px"></th>
                            </tr>
                        </thead>
                        <tbody id="rawBody"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align:right;padding-right:10px">TOTAL</td>
                                <td><input type="text" id="rawTotalQty" readonly class="ri ro"
                                        style="font-weight:700;color:var(--g)"></td>
                                <td></td>
                                <td><input type="text" id="rawTotalExpected" readonly class="ri ro"
                                        style="font-weight:700;color:var(--g)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Flux / Chemicals --}}
        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18" />
                    </svg>
                    <span>Flux / Chemicals</span>
                </div>
                <button class="btn-add btn-sm" onclick="addFluxRow()" id="btnAddFlux">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg> Add
                </button>
            </div>
            <div class="card-body" style="padding:0">
                <div class="tbl-wrap">
                    <table class="data-table" id="fluxTable">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th>Flux / Chemical</th>
                                <th>QTY (KG)</th>
                                <th style="width:32px"></th>
                            </tr>
                        </thead>
                        <tbody id="fluxBody"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align:right;padding-right:10px">TOTAL</td>
                                <td><input type="text" id="fluxTotalQty" readonly class="ri ro"
                                        style="font-weight:700;color:var(--g)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
    SECTION 3 — Process Details + Right Consumption Panel
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="two-col" style="align-items:start">

        {{-- Process Details --}}
        <div class="card">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    <span>Process Details</span>
                </div>
            </div>
            <div class="card-body" style="padding:0">
                <div class="tbl-wrap">
                    <table class="proc-table" id="procTable">
                        <thead>
                            <tr>
                                <th>Process</th>
                                <th>Start</th>
                                <th style="width:32px"></th>
                                <th>End</th>
                                <th style="width:32px"></th>
                                <th>Total Time</th>
                                <th>Firing Mode</th>
                            </tr>
                        </thead>
                        <tbody id="procBody">
                            {{-- Rows pre-generated by JS from PROCESS_NAMES constant --}}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"
                                    style="text-align:right;padding-right:10px;font-size:11px;font-weight:700;color:var(--g)">
                                    TOTAL BATCH TIME</td>
                                <td colspan="2">
                                    <input type="text" id="totalBatchTime" readonly class="ri ro"
                                        style="font-weight:700;color:var(--g)" placeholder="0 min">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right panel — Consumption + Output --}}
        <div>

            {{-- LPG + O2 --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 0 1 10 10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                        <span>Consumption</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="cons-grid" style="grid-template-columns:1fr 1fr;gap:12px">
                        <div class="field">
                            <label>LPG (m³)</label>
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <path d="M12 2v20M2 12h20" />
                                </svg>
                                <input type="number" id="lpg_consumption" step="0.001" placeholder="0.000"
                                    oninput="triggerAutosave()">
                            </div>
                        </div>
                        <div class="field">
                            <label>Liquid O₂ (m³)</label>
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <path d="M12 2v20M2 12h20" />
                                </svg>
                                <input type="number" id="o2_consumption" step="0.001" placeholder="0.000"
                                    oninput="triggerAutosave()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ID Fan --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M12 2a4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4m0 10a4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4z" />
                        </svg>
                        <span>ID Fan Consumption</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="cons-grid">
                        <div class="field"><label>Initial</label>
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="id_fan_initial" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('id_fan');triggerAutosave()">
                            </div>
                        </div>
                        <div class="field"><label>Final</label>
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="id_fan_final" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('id_fan');triggerAutosave()">
                            </div>
                        </div>
                    </div>
                    <div class="cons-total">
                        <span class="cons-total-label">CONSUMPTION</span>
                        <span class="cons-total-val" id="id_fan_consumption_display">—</span>
                    </div>
                    <input type="hidden" id="id_fan_consumption">
                </div>
            </div>

            {{-- Rotary Power --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                        </svg>
                        <span>Rotary Power Consumption</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="cons-grid">
                        <div class="field"><label>Initial</label>
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="rotary_power_initial" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('rotary_power');triggerAutosave()">
                            </div>
                        </div>
                        <div class="field"><label>Final</label>
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="rotary_power_final" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('rotary_power');triggerAutosave()">
                            </div>
                        </div>
                    </div>
                    <div class="cons-total">
                        <span class="cons-total-label">CONSUMPTION</span>
                        <span class="cons-total-val" id="rotary_power_consumption_display">—</span>
                    </div>
                    <input type="hidden" id="rotary_power_consumption">
                </div>
            </div>

            {{-- Output Window --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-left">
                        <svg viewBox="0 0 24 24">
                            <path d="M5 8l6 6 6-6" />
                        </svg>
                        <span>Output Window</span>
                    </div>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="field">
                            <label>Material</label>
                            <div class="sdd" id="sdd_output_material">
                                <div class="sdd-trigger" onclick="toggleSdd('output_material')">
                                    <svg style="position:absolute;left:11px;top:50%;transform:translateY(-50%);width:13px;height:13px;stroke:var(--txtmu);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;pointer-events:none" viewBox="0 0 24 24"><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0"/></svg>
                                    <span class="sdd-trigger-text placeholder" id="sdd_output_material_label" data-placeholder="Select material…">Select material…</span>
                                    <svg class="sdd-clear" onclick="clearSdd('output_material',event)" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    <svg class="sdd-trigger-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                                <input type="hidden" id="output_material">
                            </div>
                        </div>
                        <div class="field">
                            <label>Quantity (KG)</label>
                            <div class="iw">
                                <svg class="ico" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <input type="number" id="output_qty" step="0.001"
                                    placeholder="Click to enter block weights…" onclick="openOutputModal()" readonly
                                    style="cursor:pointer;background:var(--gxl)"
                                    title="Click to enter output block quantities">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
    SECTION 4 — Temperature Records
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" />
                </svg>
                <span>Temperature Record</span>
            </div>
            <button class="btn-add" onclick="addTempRow()" id="btnAddTemp">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg> Add Row
            </button>
        </div>
        <div class="card-body" style="padding:0">
            <div class="tbl-wrap">
                <table class="data-table" id="tempTable">
                    <thead>
                        <tr>
                            <th style="width:36px">#</th>
                            <th>Time</th>
                            <th>Inside Temp Before Charging (°C)</th>
                            <th>Process Gas Chamber</th>
                            <th>Shell</th>
                            <th>Bag House</th>
                            <th style="width:32px"></th>
                        </tr>
                    </thead>
                    <tbody id="tempBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
    AUTOSAVE STATUS + STICKY FOOTER
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="form-actions" id="formActions">
        <a href="{{ route('admin.mes.smelting.index') }}" class="btn btn-outline btn-sm">Cancel</a>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <span id="autosaveStatus" style="font-size:12px;color:var(--txtmu);display:none">
                <span class="as-dot" id="asDot"></span>
                <span id="asText">Saving…</span>
            </span>
            <button type="button" class="btn btn-primary btn-sm" id="btnSave" onclick="saveForm()">
                <svg viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                <span id="btnSaveLabel">Create Batch</span>
            </button>
            <button type="button" class="btn btn-outline btn-sm" id="btnSubmit" onclick="submitBatch()"
                style="display:none">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                Submit &amp; Lock
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Constants ───────────────────────────────────────────────────────
        const PATH = window.location.pathname.split('/').filter(Boolean);
        const isCreate = PATH[PATH.length - 1] === 'create';
        const recordId = isCreate ? null : PATH[PATH.length - 2];

        const PROCESS_NAMES = [
            'CHARGING', 'ROCKING', 'SMELTING 1', 'SMELTING 2',
            'SMELTING 3', 'SMELTING 4', 'TAPPING', 'SLAG PROCESS', 'SLAG TAPPING'
        ];
        const FIRING_OPTIONS = ['', 'Low', 'Medium', 'High'];

        let isSubmitted = false;
        let rawRowCount = 0, fluxRowCount = 0, tempRowCount = 0;
        let autosaveTimer;
        let itemsList = []; // for dropdowns

        // ── Init ────────────────────────────────────────────────────────────
        async function init() {
            document.getElementById('date').value = new Date().toISOString().slice(0, 10);
            await loadItems();
            buildProcessTable();
            buildOutputDropdown();

            if (isCreate) {
                const res = await apiFetch('/smelting-batches/generate-batch-no');
                if (res?.ok) {
                    const d = await res.json();
                    document.getElementById('batch_no').value = d.batch_no;
                }
                document.getElementById('pageTitle').textContent = 'Create Smelting Batch';
                document.getElementById('pageSubtitle').textContent = 'Record new smelting batch log';
                document.getElementById('breadcrumbTitle').textContent = 'Create Batch';
                document.getElementById('btnSaveLabel').textContent = 'Create Batch';
                addRawRow();
                addFluxRow();
                addTempRow();
            } else {
                await loadRecord();
            }
        }
        init();

        // ── Load items for dropdowns ─────────────────────────────────────────
        async function loadItems() {
            try {
                // Query the materials table directly (SELECT * FROM materials)
                const res = await apiFetch('/materials?per_page=500');
                if (res?.ok) {
                    const d = await res.json();
                    itemsList = d.data?.data ?? d.data ?? [];
                }
            } catch (e) { console.warn('Materials load failed', e); }
        }

        function buildOutputDropdown() {
            const sel = document.getElementById('output_material');
            sel.innerHTML = '<option value="">Select material…</option>';
            itemsList.forEach(item => {
                const o = document.createElement('option');
                o.value = item.id;
                o.textContent = item.name ?? item.item_name ?? item.material_name;
                sel.appendChild(o);
            });
        }

        function getItemOptions(selectedId) {
            if (!itemsList.length) return '<option value="">Select material…</option>';
            return '<option value="">Select material…</option>' +
                itemsList.map(i =>
                    `<option value="${i.id}" ${String(i.id) === String(selectedId) ? 'selected' : ''}>${i.name ?? i.item_name ?? i.material_name}</option>`
                ).join('');
        }

        // ── Process table (fixed rows from PROCESS_NAMES) ───────────────────
        function buildProcessTable() {
            const tbody = document.getElementById('procBody');
            tbody.innerHTML = '';
            PROCESS_NAMES.forEach((name, idx) => {
                const tr = document.createElement('tr');
                tr.id = `prow-${idx}`;
                tr.dataset.process = name;
                const firingOpts = FIRING_OPTIONS.map(f =>
                    `<option value="${f}">${f || 'Select…'}</option>`).join('');
                tr.innerHTML = `
          <td style="font-size:12px;font-weight:600;padding-left:10px;white-space:nowrap">${name}</td>
          <td><button class="proc-btn proc-start" onclick="setProcessTime(${idx},'start')">START</button></td>
          <td style="padding:4px 4px"><input type="time" class="ri" id="proc_start_${idx}" oninput="calcProcTime(${idx});triggerAutosave()" style="min-width:90px"></td>
          <td><button class="proc-btn proc-end" onclick="setProcessTime(${idx},'end')">END</button></td>
          <td style="padding:4px 4px"><input type="time" class="ri" id="proc_end_${idx}" oninput="calcProcTime(${idx});triggerAutosave()" style="min-width:90px"></td>
          <td><input type="text" class="ri ro" id="proc_total_${idx}" readonly placeholder="0 min" style="min-width:70px;font-weight:700;color:var(--g);background:var(--gxl)"></td>
          <td style="position:relative" class="sc">
            <select class="rs" id="proc_firing_${idx}" onchange="triggerAutosave()" style="min-width:100px">${firingOpts}</select>
          </td>`;
                tbody.appendChild(tr);
            });
        }

        function setProcessTime(idx, which) {
            const now = new Date();
            const t = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            document.getElementById(`proc_${which}_${idx}`).value = t;
            calcProcTime(idx);
            triggerAutosave();
        }

        function calcProcTime(idx) {
            const s = document.getElementById(`proc_start_${idx}`)?.value;
            const e = document.getElementById(`proc_end_${idx}`)?.value;
            const el = document.getElementById(`proc_total_${idx}`);
            if (s && e) {
                const [sh, sm] = s.split(':').map(Number);
                const [eh, em] = e.split(':').map(Number);
                let mins = (eh * 60 + em) - (sh * 60 + sm);
                if (mins < 0) mins += 1440;
                el.value = mins + ' min';
                el.dataset.mins = mins;
            } else {
                el.value = '';
                el.dataset.mins = 0;
            }
            calcTotalBatchTime();
        }

        function calcTotalBatchTime() {
            let total = 0;
            PROCESS_NAMES.forEach((_, idx) => {
                total += parseInt(document.getElementById(`proc_total_${idx}`)?.dataset.mins ?? 0);
            });
            const h = Math.floor(total / 60);
            const m = total % 60;
            document.getElementById('totalBatchTime').value = h > 0 ? `${h}h ${m}min` : `${m} min`;
        }

        // ── Raw Material rows ───────────────────────────────────────────────
        function addRawRow(data = {}) {
            rawRowCount++;
            const i = rawRowCount;
            const tbody = document.getElementById('rawBody');
            const tr = document.createElement('tr');
            tr.id = `rrow-${i}`;
            tr.dataset.rowIndex = i;
            // Store bbsu data on the row element itself
            tr.dataset.bbsuBatchId = data.bbsu_batch_id || '';
            tr.dataset.bbsuBatchNo = data.bbsu_batch_no || '';
            tr.dataset.bbsuAvailableQty = data.bbsu_available_qty || '';
            tr.innerHTML = `
        <td style="text-align:center;font-size:12px;font-weight:700;color:var(--g);padding:8px 4px">${i}</td>
        <td style="position:relative" class="sc">
          <select class="rs" id="rm_id_${i}" name="raw_materials[${i}][raw_material_id]"
            onchange="onRawMaterialChange(${i});triggerAutosave()"
            style="min-width:130px">${getItemOptions(data.raw_material_id)}</select>
        </td>
        <td>
          <input type="number" class="ri" id="rm_qty_${i}" name="raw_materials[${i}][raw_material_qty]"
            value="${data.raw_material_qty ?? ''}" step="0.001" placeholder="0.000"
            oninput="calcRawExpected(${i});recalcRawTotals();triggerAutosave()"
            onclick="onRawQtyClick(${i})"
            onfocus="onRawQtyFocus(${i})"
            style="min-width:90px;cursor:pointer"
            title="Click to assign from BBSU batch">
          <input type="hidden" id="rm_bbsu_id_${i}" name="raw_materials[${i}][bbsu_batch_id]" value="${data.bbsu_batch_id ?? ''}">
          <input type="hidden" id="rm_bbsu_no_${i}" name="raw_materials[${i}][bbsu_batch_no]" value="${data.bbsu_batch_no ?? ''}">
        </td>
        <td><input type="number" class="ri" id="rm_yield_${i}" name="raw_materials[${i}][raw_material_yield_pct]"
          value="${data.raw_material_yield_pct ?? ''}" step="0.01" placeholder="0.00"
          oninput="calcRawExpected(${i});recalcRawTotals();triggerAutosave()"></td>
        <td><input type="number" class="ri ro" id="rm_exp_${i}"
          value="${data.expected_output_qty ?? ''}" readonly placeholder="0.000"
          style="background:#eef6f1;color:var(--g);font-weight:600"></td>
        <td><button class="del-btn" onclick="removeRow('rrow-${i}',recalcRawTotals)" title="Remove">
          <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        </button></td>`;
            animateIn(tr);
            tbody.appendChild(tr);
        }

        // Called when material dropdown changes — enable/reset BBSU button
        function onRawMaterialChange(i) {
            clearBbsuOnRow(i);
        }

        // When user focuses the QTY field — if no BBSU selected yet AND material chosen, open modal
        function onRawQtyClick(i) {
            const materialId = document.getElementById(`rm_id_${i}`)?.value;
            if (materialId) openBbsuModal(i);
        }

        function onRawQtyFocus(i) {
            const materialId = document.getElementById(`rm_id_${i}`)?.value;
            const bbsuId = document.getElementById(`rm_bbsu_id_${i}`)?.value;
            if (materialId && !bbsuId) {
                openBbsuModal(i);
            }
        }

        // Clear BBSU selection on a row
        function clearBbsuOnRow(i) {
            const idEl = document.getElementById(`rm_bbsu_id_${i}`);
            const noEl = document.getElementById(`rm_bbsu_no_${i}`);
            if (idEl) idEl.value = '';
            if (noEl) noEl.value = '';
            const qtyEl = document.getElementById(`rm_qty_${i}`);
            if (qtyEl) { qtyEl.title = 'Click to assign from BBSU batch'; qtyEl.style.borderColor = ''; }
            const tr = document.getElementById(`rrow-${i}`);
            if (tr) { tr.dataset.bbsuBatchId = ''; tr.dataset.bbsuBatchNo = ''; tr.dataset.bbsuSelections = ''; }
            triggerAutosave();
        }

        function calcRawExpected(i) {
            const qty = parseFloat(document.getElementById(`rm_qty_${i}`)?.value) || 0;
            const yield_ = parseFloat(document.getElementById(`rm_yield_${i}`)?.value) || 0;
            const exp = document.getElementById(`rm_exp_${i}`);
            if (exp) exp.value = yield_ > 0 ? (qty * yield_ / 100).toFixed(3) : '';
        }

        function recalcRawTotals() {
            let qty = 0, exp = 0;
            document.querySelectorAll('#rawBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                qty += parseFloat(document.getElementById(`rm_qty_${i}`)?.value) || 0;
                exp += parseFloat(document.getElementById(`rm_exp_${i}`)?.value) || 0;
            });
            document.getElementById('rawTotalQty').value = qty ? qty.toFixed(3) : '';
            document.getElementById('rawTotalExpected').value = exp ? exp.toFixed(3) : '';
        }

        // ── Flux / Chemical rows ────────────────────────────────────────────
        function addFluxRow(data = {}) {
            fluxRowCount++;
            const i = fluxRowCount;
            const tbody = document.getElementById('fluxBody');
            const tr = document.createElement('tr');
            tr.id = `frow-${i}`;
            tr.dataset.rowIndex = i;
            tr.dataset.fluxBbsuSelections = data.flux_bbsu_selections ? JSON.stringify(data.flux_bbsu_selections) : '';
            tr.innerHTML = `
        <td style="text-align:center;font-size:12px;font-weight:700;color:var(--g);padding:8px 4px">${i}</td>
        <td style="position:relative" class="sc">
          <select class="rs" id="fl_id_${i}" onchange="onFluxMaterialChange(${i});triggerAutosave()"
            style="min-width:130px">${getItemOptions(data.chemical_id)}</select>
        </td>
        <td>
          <input type="number" class="ri" id="fl_qty_${i}"
            value="${data.qty ?? ''}" step="0.001" placeholder="0.000"
            oninput="recalcFluxTotals();triggerAutosave()"
            onclick="onFluxQtyClick(${i})"
            onfocus="onFluxQtyFocus(${i})"
            style="min-width:90px;cursor:pointer"
            title="Click to assign from BBSU batch">
          <input type="hidden" id="fl_bbsu_id_${i}" value="${data.fl_bbsu_batch_id ?? ''}">
          <input type="hidden" id="fl_bbsu_no_${i}" value="${data.fl_bbsu_batch_no ?? ''}">
        </td>
        <td><button class="del-btn" onclick="removeRow('frow-${i}',recalcFluxTotals)" title="Remove">
          <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        </button></td>`;
            animateIn(tr);
            tbody.appendChild(tr);
        }

        function recalcFluxTotals() {
            let qty = 0;
            document.querySelectorAll('#fluxBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                qty += parseFloat(document.getElementById(`fl_qty_${i}`)?.value) || 0;
            });
            document.getElementById('fluxTotalQty').value = qty ? qty.toFixed(3) : '';
        }

        // ── Flux BBSU helpers ───────────────────────────────────────────
        function onFluxMaterialChange(i) {
            clearFluxBbsuOnRow(i);
        }

        function onFluxQtyClick(i) {
            const materialId = document.getElementById(`fl_id_${i}`)?.value;
            if (materialId) openFluxModal(i);
        }

        function onFluxQtyFocus(i) {
            const materialId = document.getElementById(`fl_id_${i}`)?.value;
            const bbsuId = document.getElementById(`fl_bbsu_id_${i}`)?.value;
            if (materialId && !bbsuId) openFluxModal(i);
        }

        function clearFluxBbsuOnRow(i) {
            const idEl = document.getElementById(`fl_bbsu_id_${i}`);
            const noEl = document.getElementById(`fl_bbsu_no_${i}`);
            if (idEl) idEl.value = '';
            if (noEl) noEl.value = '';
            const qtyEl = document.getElementById(`fl_qty_${i}`);
            if (qtyEl) { qtyEl.title = 'Click to assign from BBSU batch'; qtyEl.style.borderColor = ''; }
            const tr = document.getElementById(`frow-${i}`);
            if (tr) tr.dataset.fluxBbsuSelections = '';
            triggerAutosave();
        }

        // ── Temperature rows ────────────────────────────────────────────────
        function addTempRow(data = {}) {
            tempRowCount++;
            const i = tempRowCount;
            const tbody = document.getElementById('tempBody');
            const tr = document.createElement('tr');
            tr.id = `trow-${i}`;
            tr.dataset.rowIndex = i;
            tr.innerHTML = `
        <td style="text-align:center;font-size:12px;font-weight:700;color:var(--g);padding:8px 4px">${i}</td>
        <td><input type="time" class="ri" id="temp_time_${i}" value="${data.record_time ?? ''}" oninput="triggerAutosave()"></td>
        <td><input type="number" class="ri" id="temp_inside_${i}" value="${data.inside_temp_before_charging ?? ''}" step="0.01" placeholder="°C" oninput="triggerAutosave()"></td>
        <td><input type="text" class="ri" id="temp_pgc_${i}" value="${data.process_gas_chamber_temp ?? ''}" placeholder="VARCHAR" oninput="triggerAutosave()"></td>
        <td><input type="text" class="ri" id="temp_shell_${i}" value="${data.shell_temp ?? ''}" placeholder="VARCHAR" oninput="triggerAutosave()"></td>
        <td><input type="text" class="ri" id="temp_bag_${i}" value="${data.bag_house_temp ?? ''}" placeholder="VARCHAR" oninput="triggerAutosave()"></td>
        <td><button class="del-btn" onclick="removeRow('trow-${i}',null)" title="Remove">
          <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        </button></td>`;
            animateIn(tr);
            tbody.appendChild(tr);
        }

        // ── Consumption calc ─────────────────────────────────────────────────
        function calcConsumption(prefix) {
            const init = parseFloat(document.getElementById(`${prefix}_initial`)?.value) || 0;
            const fin = parseFloat(document.getElementById(`${prefix}_final`)?.value) || 0;
            const diff = fin >= init ? (fin - init) : null;
            const disp = document.getElementById(`${prefix}_consumption_display`);
            const hid = document.getElementById(`${prefix}_consumption`);
            if (disp) disp.textContent = diff !== null ? diff.toFixed(3) : '—';
            if (hid) hid.value = diff !== null ? diff : '';
        }

        // ── Load record for edit ─────────────────────────────────────────────
        async function loadRecord() {
            const res = await apiFetch(`/smelting-batches/${recordId}`);
            if (!res?.ok) { showAlert('Failed to load record.'); return; }
            const { data } = await res.json();

            isSubmitted = data.status === 'submitted';

            // Fill header
            document.getElementById('batch_no').value = data.batch_no ?? '';
            document.getElementById('date').value = data.date?.slice(0, 10) ?? '';
            document.getElementById('rotary_no').value = data.rotary_no ?? '';
            if (data.start_time) document.getElementById('start_time').value = data.start_time.slice(11, 16);
            if (data.end_time) document.getElementById('end_time').value = data.end_time.slice(11, 16);

            document.getElementById('lpg_consumption').value = data.lpg_consumption ?? '';
            document.getElementById('o2_consumption').value = data.o2_consumption ?? '';
            document.getElementById('id_fan_initial').value = data.id_fan_initial ?? '';
            document.getElementById('id_fan_final').value = data.id_fan_final ?? '';
            document.getElementById('rotary_power_initial').value = data.rotary_power_initial ?? '';
            document.getElementById('rotary_power_final').value = data.rotary_power_final ?? '';
            document.getElementById('output_material').value = data.output_material ?? '';
            loadOutputBlocks(data.output_blocks);   // fills output_qty from block weights

            calcConsumption('id_fan');
            calcConsumption('rotary_power');

            // Raw materials
            (data.raw_materials ?? []).forEach(r => addRawRow(r));
            if (!data.raw_materials?.length) addRawRow();
            recalcRawTotals();

            // Flux chemicals
            (data.flux_chemicals ?? []).forEach(f => addFluxRow(f));
            if (!data.flux_chemicals?.length) addFluxRow();
            recalcFluxTotals();

            // Process details
            (data.process_details ?? []).forEach(pd => {
                const idx = PROCESS_NAMES.indexOf(pd.process_name);
                if (idx >= 0) {
                    if (pd.start_time) document.getElementById(`proc_start_${idx}`).value = pd.start_time.slice(11, 16);
                    if (pd.end_time) document.getElementById(`proc_end_${idx}`).value = pd.end_time.slice(11, 16);
                    if (pd.firing_mode) document.getElementById(`proc_firing_${idx}`).value = pd.firing_mode;
                    calcProcTime(idx);
                }
            });

            // Temperature records
            (data.temperature_records ?? []).forEach(t => addTempRow(t));
            if (!data.temperature_records?.length) addTempRow();

            // Page title
            document.getElementById('pageTitle').textContent = 'Edit Smelting Batch';
            document.getElementById('pageSubtitle').textContent = `Batch: ${data.batch_no}`;
            document.getElementById('breadcrumbTitle').textContent = 'Edit Batch';
            document.getElementById('btnSaveLabel').textContent = 'Save Draft';

            const badge = document.getElementById('statusBadge');
            if (isSubmitted) {
                badge.innerHTML = '<span class="badge badge-submitted">● Submitted</span>';
                setReadonly(true);
            } else {
                badge.innerHTML = '<span class="badge badge-draft">● Draft</span>';
                document.getElementById('btnSubmit').style.display = 'inline-flex';
                setupAutosave();
            }
        }

        // ── Build payload ────────────────────────────────────────────────────
        function buildPayload() {
            const raw_materials = [];
            document.querySelectorAll('#rawBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const id = document.getElementById(`rm_id_${i}`)?.value;
                if (!id) return;
                // For multi-bbsu: bbsu_selections holds each batch+qty pair;
                // bbsu_batch_id/no hold first (or only) value for backward compat
                const bbsuSelections = tr.dataset.bbsuSelections
                    ? JSON.parse(tr.dataset.bbsuSelections) : null;
                const bbsuIdRaw = document.getElementById(`rm_bbsu_id_${i}`)?.value || null;
                const bbsuNoRaw = document.getElementById(`rm_bbsu_no_${i}`)?.value || null;
                // first id/no only (backward compat with single-batch DB column)
                const firstBbsuId = bbsuIdRaw ? bbsuIdRaw.split(',')[0] : null;
                const firstBbsuNo = bbsuNoRaw ? bbsuNoRaw.split(',')[0] : null;
                raw_materials.push({
                    raw_material_id: id,
                    bbsu_batch_id: firstBbsuId,
                    bbsu_batch_no: firstBbsuNo,
                    bbsu_selections: bbsuSelections, // full multi-batch detail
                    raw_material_qty: document.getElementById(`rm_qty_${i}`)?.value || 0,
                    raw_material_yield_pct: document.getElementById(`rm_yield_${i}`)?.value || 0,
                    expected_output_qty: document.getElementById(`rm_exp_${i}`)?.value || 0,
                });
            });

            const flux_chemicals = [];
            document.querySelectorAll('#fluxBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const id = document.getElementById(`fl_id_${i}`)?.value;
                if (!id) return;
                const fluxSelections = tr.dataset.fluxBbsuSelections
                    ? JSON.parse(tr.dataset.fluxBbsuSelections) : null;
                const fluxBbsuIdRaw = document.getElementById(`fl_bbsu_id_${i}`)?.value || null;
                const fluxBbsuNoRaw = document.getElementById(`fl_bbsu_no_${i}`)?.value || null;
                flux_chemicals.push({
                    chemical_id: id,
                    bbsu_batch_id: fluxBbsuIdRaw ? fluxBbsuIdRaw.split(',')[0] : null,
                    bbsu_batch_no: fluxBbsuNoRaw ? fluxBbsuNoRaw.split(',')[0] : null,
                    bbsu_selections: fluxSelections,
                    qty: document.getElementById(`fl_qty_${i}`)?.value || 0,
                });
            });

            const process_details = PROCESS_NAMES.map((name, idx) => ({
                process_name: name,
                start_time: document.getElementById(`proc_start_${idx}`)?.value
                    ? document.getElementById('date').value + 'T' + document.getElementById(`proc_start_${idx}`).value + ':00'
                    : null,
                end_time: document.getElementById(`proc_end_${idx}`)?.value
                    ? document.getElementById('date').value + 'T' + document.getElementById(`proc_end_${idx}`).value + ':00'
                    : null,
                total_time: parseInt(document.getElementById(`proc_total_${idx}`)?.dataset.mins ?? 0),
                firing_mode: document.getElementById(`proc_firing_${idx}`)?.value || null,
            })).filter(p => p.start_time || p.end_time);

            const temperature_records = [];
            document.querySelectorAll('#tempBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                temperature_records.push({
                    record_time: document.getElementById(`temp_time_${i}`)?.value
                        ? document.getElementById('date').value + 'T' + document.getElementById(`temp_time_${i}`).value + ':00'
                        : null,
                    inside_temp_before_charging: document.getElementById(`temp_inside_${i}`)?.value || null,
                    process_gas_chamber_temp: document.getElementById(`temp_pgc_${i}`)?.value || null,
                    shell_temp: document.getElementById(`temp_shell_${i}`)?.value || null,
                    bag_house_temp: document.getElementById(`temp_bag_${i}`)?.value || null,
                });
            });

            const dateVal = document.getElementById('date').value;
            const stVal = document.getElementById('start_time').value;
            const etVal = document.getElementById('end_time').value;

            return {
                batch_no: document.getElementById('batch_no').value,
                rotary_no: document.getElementById('rotary_no').value,
                date: dateVal,
                start_time: stVal || null,
                end_time: etVal || null,
                lpg_consumption: document.getElementById('lpg_consumption').value || null,
                o2_consumption: document.getElementById('o2_consumption').value || null,
                id_fan_initial: document.getElementById('id_fan_initial').value || null,
                id_fan_final: document.getElementById('id_fan_final').value || null,
                id_fan_consumption: document.getElementById('id_fan_consumption').value || null,
                rotary_power_initial: document.getElementById('rotary_power_initial').value || null,
                rotary_power_final: document.getElementById('rotary_power_final').value || null,
                rotary_power_consumption: document.getElementById('rotary_power_consumption').value || null,
                output_material: document.getElementById('output_material').value || null,
                output_qty: document.getElementById('output_qty').value || null,
                raw_materials,
                flux_chemicals,
                process_details,
                temperature_records,
                output_blocks: outputBlockRows.map((r, idx) => ({
                    material_id: document.getElementById('output_material').value || null,
                    block_sl_no: idx + 1,
                    block_weight: parseFloat(r.qty) || 0,
                })).filter(r => r.block_weight > 0),
            };
        }

        // ── Save form ────────────────────────────────────────────────────────
        async function saveForm(silent = false) {
            const payload = buildPayload();
            const btn = document.getElementById('btnSave');
            if (!silent) btn.disabled = true;

            const method = isCreate ? 'POST' : 'PUT';
            const endpoint = isCreate ? '/smelting-batches' : `/smelting-batches/${recordId}`;

            const res = await apiFetch(endpoint, { method, body: JSON.stringify(payload) });
            if (!silent) btn.disabled = false;
            if (!res) return;

            const data = await res.json();

            if (res.ok && data.status === 'ok') {
                if (!silent) {
                    if (isCreate) {
                        window.location.href = `{{ url('/admin/mes/smelting') }}/${data.data.id}/edit`;
                    } else {
                        showAlert('Saved successfully.', 'success');
                    }
                } else {
                    setDot('saved', 'Autosaved at ' + new Date().toLocaleTimeString());
                    setTimeout(() => document.getElementById('autosaveStatus').style.display = 'none', 4000);
                }
            } else if (res.status === 422) {
                if (!silent) showAlert(data.message ?? 'Validation error.');
            } else {
                if (!silent) showAlert(data.message ?? 'Something went wrong.');
            }
        }

        // ── Submit ───────────────────────────────────────────────────────────
        async function submitBatch() {
            if (!confirm('Submit this batch? It will be locked from further edits.')) return;
            await saveForm(true);
            const res = await apiFetch(`/smelting-batches/${recordId}/submit`, { method: 'POST', body: '{}' });
            if (res?.ok) {
                showAlert('Batch submitted and locked.', 'success');
                setTimeout(() => window.location.href = '{{ route("admin.mes.smelting.index") }}', 1400);
            } else {
                const d = await res?.json();
                showAlert(d?.message ?? 'Submit failed.');
            }
        }

        // ── Autosave ─────────────────────────────────────────────────────────
        function setupAutosave() {
            const watch = ['date', 'rotary_no', 'start_time', 'end_time', 'lpg_consumption', 'o2_consumption',
                'id_fan_initial', 'id_fan_final', 'rotary_power_initial', 'rotary_power_final',
                'output_material', 'output_qty'];
            watch.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', triggerAutosave);
            });
        }
        function triggerAutosave() {
            if (isCreate || isSubmitted) return;
            setDot('saving', 'Saving…');
            document.getElementById('autosaveStatus').style.display = 'inline';
            clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(() => saveForm(true), 2200);
        }
        function setDot(state, text) {
            const dot = document.getElementById('asDot');
            const txt = document.getElementById('asText');
            dot.className = `as-dot ${state}`;
            if (txt) txt.textContent = text;
        }

        // ── Helpers ──────────────────────────────────────────────────────────
        function removeRow(id, recalcFn) {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.transition = 'opacity .18s';
            el.style.opacity = '0';
            setTimeout(() => { el.remove(); if (recalcFn) recalcFn(); triggerAutosave(); }, 190);
        }
        function animateIn(el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-5px)';
            requestAnimationFrame(() => {
                el.style.transition = 'opacity .22s,transform .22s';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        }
        function showAlert(msg, type = 'error') {
            const el = document.getElementById('formAlert');
            el.className = `form-alert ${type}`;
            el.textContent = msg;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (type === 'success') setTimeout(() => { el.className = 'form-alert'; el.textContent = ''; }, 4000);
        }
        function setReadonly(ro) {
            document.querySelectorAll('input,select,textarea').forEach(el => {
                if (ro) { el.setAttribute('disabled', true); }
                else { el.removeAttribute('disabled'); }
            });
            document.getElementById('btnSave').style.display = ro ? 'none' : '';
            document.getElementById('btnSubmit').style.display = ro ? 'none' : '';
            document.getElementById('btnAddRaw').style.display = ro ? 'none' : '';
            document.getElementById('btnAddFlux').style.display = ro ? 'none' : '';
            document.getElementById('btnAddTemp').style.display = ro ? 'none' : '';
            if (ro) document.getElementById('readonlyNotice').style.display = 'block';
            document.querySelectorAll('.del-btn').forEach(b => b.style.display = ro ? 'none' : '');
            document.querySelectorAll('.proc-btn').forEach(b => b.style.display = ro ? 'none' : '');
        }

        // ════════════════════════════════════════════════════════════════
        // OUTPUT QTY WINDOW MODAL
        // User enters block SL NO + weight rows; total → output_qty field
        // ════════════════════════════════════════════════════════════════
        let outputBlockRows = [];   // [{qty}] persisted across modal opens
        const OUTPUT_MAX_ROWS = 11; // screenshot shows 11 fixed rows

        function onOutputMaterialChange() {
            // When material changes, clear blocks
            outputBlockRows = [];
            document.getElementById('output_qty').value = '';
        }

        function openOutputModal() {
            const matId = document.getElementById('output_material').value;
            if (!matId) {
                showAlert('Please select a material first.', 'error');
                return;
            }
            const matName = document.getElementById('output_material').selectedOptions[0]?.text ?? '';
            document.getElementById('outputModalSubtitle').textContent = matName
                ? `Material: ${matName} — enter block weights`
                : 'Enter block weights — total auto-calculates';

            // Render rows
            renderOutputRows();
            document.getElementById('outputQtyModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeOutputModal(e) {
            if (e && e.target !== document.getElementById('outputQtyModal')) return;
            document.getElementById('outputQtyModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        function renderOutputRows() {
            const tbody = document.getElementById('outputBlockTbody');
            tbody.innerHTML = '';
            // Always show at least OUTPUT_MAX_ROWS rows
            const count = Math.max(OUTPUT_MAX_ROWS, outputBlockRows.length);
            for (let i = 0; i < count; i++) {
                const qty = outputBlockRows[i]?.qty ?? '';
                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid var(--bdr)';
                tr.innerHTML = `
          <td style="padding:5px 14px;font-size:12.5px;font-weight:700;color:var(--g);text-align:right;
                     background:var(--gxl);width:80px;border-right:1px solid var(--bdr)">${i + 1}</td>
          <td style="padding:4px 10px">
            <input type="number" step="0.001" min="0" placeholder="0.000"
              value="${qty}"
              style="width:100%;padding:7px 10px;border:1.5px solid var(--bdr);border-radius:6px;
                     font-family:'Outfit',sans-serif;font-size:13px;text-align:right;
                     background:var(--white);outline:none;transition:border-color .15s"
              oninput="onOutputQtyInput(this, ${i})"
              onfocus="this.style.borderColor='var(--g)'"
              onblur="this.style.borderColor='var(--bdr)'">
          </td>`;
                tbody.appendChild(tr);
            }
            recalcOutputTotal();
        }

        function addOutputRow() {
            // Save current values first
            syncOutputRows();
            outputBlockRows.push({ qty: '' });
            renderOutputRows();
            // Focus the new row's input
            const inputs = document.querySelectorAll('#outputBlockTbody input');
            if (inputs.length) inputs[inputs.length - 1].focus();
        }

        function syncOutputRows() {
            document.querySelectorAll('#outputBlockTbody input').forEach((inp, idx) => {
                if (!outputBlockRows[idx]) outputBlockRows[idx] = {};
                outputBlockRows[idx].qty = inp.value;
            });
        }

        function onOutputQtyInput(inp, rowIdx) {
            if (!outputBlockRows[rowIdx]) outputBlockRows[rowIdx] = {};
            outputBlockRows[rowIdx].qty = inp.value;
            recalcOutputTotal();
        }

        function recalcOutputTotal() {
            let total = 0;
            document.querySelectorAll('#outputBlockTbody input').forEach(inp => {
                const v = parseFloat(inp.value);
                if (!isNaN(v) && v > 0) total += v;
            });
            document.getElementById('outputBlockTotal').textContent = total > 0 ? total.toFixed(3) : '0.000';
        }

        function confirmOutputQty() {
            syncOutputRows();
            const total = outputBlockRows.reduce((s, r) => {
                const v = parseFloat(r.qty);
                return s + (isNaN(v) || v <= 0 ? 0 : v);
            }, 0);

            document.getElementById('output_qty').value = total > 0 ? total.toFixed(3) : '';
            triggerAutosave();

            document.getElementById('outputQtyModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        // ── Populate output_blocks from saved data (edit mode) ──────────
        function loadOutputBlocks(blocks) {
            if (!blocks?.length) return;
            outputBlockRows = blocks.map(b => ({ qty: b.block_weight ?? 0 }));
            const total = outputBlockRows.reduce((s, r) => s + (parseFloat(r.qty) || 0), 0);
            if (total > 0) document.getElementById('output_qty').value = total.toFixed(3);
        }

        // ════════════════════════════════════════════════════════════════
        // BBSU LOT SELECTION MODAL
        // Supports multi-row selection: user assigns qty per BBSU batch,
        // sum of all assign qtys populates the raw material QTY field.
        // Multiple selected batches are stored as comma-separated in
        // hidden fields (bbsu_batch_id / bbsu_batch_no).
        // ════════════════════════════════════════════════════════════════
        let bbsuActiveRowIndex = null;   // which raw-material row triggered modal

        function openBbsuModal(rowIndex) {
            const materialId = document.getElementById(`rm_id_${rowIndex}`)?.value;
            if (!materialId) return;

            bbsuActiveRowIndex = rowIndex;

            const materialName = document.getElementById(`rm_id_${rowIndex}`)?.selectedOptions[0]?.text ?? 'Material';
            document.getElementById('bbsuModalTitle').textContent = 'Available Stock — ' + materialName;
            document.getElementById('bbsuModalSubtitle').textContent = 'Current available stock for this material. Enter the quantity to assign.';
            document.getElementById('bbsuConfirmBtn').disabled = true;

            document.getElementById('bbsuLotModal').classList.add('open');
            document.body.style.overflow = 'hidden';

            loadBbsuLots(materialId, rowIndex);
        }

        function closeBbsuModal(e) {
            if (e && e.target !== document.getElementById('bbsuLotModal')) return;
            document.getElementById('bbsuLotModal').classList.remove('open');
            document.body.style.overflow = '';
            bbsuActiveRowIndex = null;
        }

        async function loadBbsuLots(materialId, rowIndex) {
            const loading = document.getElementById('bbsuLotLoading');
            const empty = document.getElementById('bbsuLotEmpty');
            const scroll = document.getElementById('bbsuLotTableScroll');
            const tbody = document.getElementById('bbsuLotTbody');
            const tfoot = document.getElementById('bbsuLotTfoot');

            loading.style.display = 'block';
            empty.style.display = 'none';
            scroll.style.display = 'none';
            tfoot.style.display = 'none';
            tbody.innerHTML = '';
            document.getElementById('bbsuTotalAssign').textContent = '0.000';
            document.getElementById('bbsuConfirmBtn').disabled = true;

            const excl = recordId ? `?exclude_smelting_id=${recordId}` : '';
            const res = await apiFetch(`/smelting-batches/bbsu-lots/${materialId}${excl}`, { method: 'GET' });
            loading.style.display = 'none';

            // apiFetch returns a Response object — parse JSON
            if (!res || !res.ok) { empty.style.display = 'block'; return; }
            const json = await res.json();
            const lots = json.data ?? [];

            if (!lots.length) { empty.style.display = 'block'; return; }

            scroll.style.display = 'block';
            tfoot.style.display = '';

            lots.forEach(lot => {
                const availClass = lot.available_qty <= 0 ? 'avail-zero'
                    : lot.available_qty < 50 ? 'avail-low'
                        : 'avail-good';
                const tr = document.createElement('tr');
                tr.dataset.bbsuId = lot.bbsu_batch_id;
                tr.dataset.bbsuNo = lot.batch_no;
                tr.dataset.availableQty = lot.available_qty;

                tr.innerHTML = `
          <td><span class="lot-bbsu-tag">${lot.batch_no}</span></td>
          <td style="font-size:12.5px;font-weight:600">${lot.material_name}</td>
          <td style="font-weight:600;color:var(--txtm)">${lot.material_unit ?? 'KG'}</td>
          <td><span class="avail-pill ${availClass}">${Number(lot.available_qty).toFixed(3)}</span></td>
          <td>
            <input type="number" class="assign-input" id="bbs_assign_${lot.bbsu_batch_id}"
              placeholder="0.000" step="0.001" min="0.001" max="${lot.available_qty}"
              ${lot.available_qty <= 0 ? 'disabled title="No available quantity"' : ''}
              oninput="onAssignQtyInput(${lot.bbsu_batch_id}, ${lot.available_qty})"
              onclick="event.stopPropagation()">
          </td>`;

                // clicking the row focuses the assign input
                tr.addEventListener('click', (e) => {
                    if (e.target.tagName === 'INPUT') return;
                    const inp = document.getElementById(`bbs_assign_${lot.bbsu_batch_id}`);
                    if (inp && !inp.disabled) inp.focus();
                });

                tbody.appendChild(tr);
            });
        }

        // Called every time any assign-qty input changes
        function onAssignQtyInput(bbsuId, maxQty) {
            const input = document.getElementById(`bbs_assign_${bbsuId}`);
            if (!input) return;

            let val = parseFloat(input.value);
            if (isNaN(val) || val < 0) { val = 0; input.value = ''; }
            if (val > maxQty) {
                val = parseFloat(maxQty.toFixed(3));
                input.value = val.toFixed(3);
                input.style.borderColor = '#d97706'; // orange = capped
            } else {
                input.style.borderColor = val > 0 ? 'var(--g)' : '';
            }

            // Highlight row if qty > 0
            const tr = input.closest('tr');
            if (tr) tr.classList.toggle('selected', val > 0);

            recalcBbsuModalTotal();
        }

        // Sum all filled assign-qty inputs and update total + confirm button
        function recalcBbsuModalTotal() {
            let total = 0;
            document.querySelectorAll('#bbsuLotTbody .assign-input').forEach(inp => {
                const v = parseFloat(inp.value);
                if (!isNaN(v) && v > 0) total += v;
            });
            document.getElementById('bbsuTotalAssign').textContent = total > 0 ? total.toFixed(3) : '0.000';
            document.getElementById('bbsuConfirmBtn').disabled = total <= 0;
        }

        // OK button — collect all rows that have assign qty > 0
        function confirmBbsuSelection() {
            if (!bbsuActiveRowIndex) return;

            const i = bbsuActiveRowIndex;
            const selections = [];

            document.querySelectorAll('#bbsuLotTbody tr').forEach(tr => {
                const bbsuId = tr.dataset.bbsuId;
                const bbsuNo = tr.dataset.bbsuNo;
                const inp = document.getElementById(`bbs_assign_${bbsuId}`);
                const qty = parseFloat(inp?.value);
                if (!isNaN(qty) && qty > 0) {
                    selections.push({ bbsuId, bbsuNo, qty });
                }
            });

            if (!selections.length) return;

            const totalQty = selections.reduce((s, r) => s + r.qty, 0);
            // Store first batch id/no in hidden fields (for single-batch compat);
            // store all as JSON in a data attribute on the row for multi-batch awareness
            const batchIds = selections.map(r => r.bbsuId).join(',');
            const batchNos = selections.map(r => r.bbsuNo).join(',');

            document.getElementById(`rm_bbsu_id_${i}`).value = batchIds;
            document.getElementById(`rm_bbsu_no_${i}`).value = batchNos;

            // Also store full detail on the row element for buildPayload
            const tr = document.getElementById(`rrow-${i}`);
            if (tr) tr.dataset.bbsuSelections = JSON.stringify(selections);

            // Set QTY field to total assigned qty
            const qtyInput = document.getElementById(`rm_qty_${i}`);
            if (qtyInput) {
                qtyInput.value = totalQty.toFixed(3);
                calcRawExpected(i);
                recalcRawTotals();
            }

            // Show selected batches as tooltip on the QTY input
            const qtyEl = document.getElementById(`rm_qty_${i}`);
            if (qtyEl) {
                qtyEl.title = 'BBSU: ' + selections.map(r => `${r.bbsuNo} (${r.qty} KG)`).join(', ');
                qtyEl.style.borderColor = 'var(--g)';
            }

            triggerAutosave();
            document.getElementById('bbsuLotModal').classList.remove('open');
            document.body.style.overflow = '';
            bbsuActiveRowIndex = null;
        }

        // ════════════════════════════════════════════════════════════════
        // FLUX BBSU LOT SELECTION MODAL
        // Same pattern as raw-material BBSU modal — reuses same API endpoint
        // getBbsuLots, filtering by the selected flux/chemical item id.
        // ════════════════════════════════════════════════════════════════
        let fluxActiveRowIndex = null;

        function openFluxModal(rowIndex) {
            const materialId = document.getElementById(`fl_id_${rowIndex}`)?.value;
            if (!materialId) return;

            fluxActiveRowIndex = rowIndex;

            const materialName = document.getElementById(`fl_id_${rowIndex}`)?.selectedOptions[0]?.text ?? 'Chemical';
            document.getElementById('fluxModalTitle').textContent = 'Available Stock — ' + materialName;
            document.getElementById('fluxModalSubtitle').textContent = 'Current available stock for this material. Enter the quantity to assign.';
            document.getElementById('fluxConfirmBtn').disabled = true;

            document.getElementById('fluxLotModal').classList.add('open');
            document.body.style.overflow = 'hidden';

            loadFluxLots(materialId, rowIndex);
        }

        function closeFluxModal(e) {
            if (e && e.target !== document.getElementById('fluxLotModal')) return;
            document.getElementById('fluxLotModal').classList.remove('open');
            document.body.style.overflow = '';
            fluxActiveRowIndex = null;
        }

        async function loadFluxLots(materialId, rowIndex) {
            const loading = document.getElementById('fluxLotLoading');
            const empty = document.getElementById('fluxLotEmpty');
            const scroll = document.getElementById('fluxLotTableScroll');
            const tbody = document.getElementById('fluxLotTbody');
            const tfoot = document.getElementById('fluxLotTfoot');

            loading.style.display = 'block';
            empty.style.display = 'none';
            scroll.style.display = 'none';
            tfoot.style.display = 'none';
            tbody.innerHTML = '';
            document.getElementById('fluxTotalAssign').textContent = '0.000';
            document.getElementById('fluxConfirmBtn').disabled = true;

            const excl = recordId ? `?exclude_smelting_id=${recordId}` : '';
            const res = await apiFetch(`/smelting-batches/bbsu-lots/${materialId}${excl}`, { method: 'GET' });
            loading.style.display = 'none';

            if (!res || !res.ok) { empty.style.display = 'block'; return; }
            const json = await res.json();
            const lots = json.data ?? [];

            if (!lots.length) { empty.style.display = 'block'; return; }

            scroll.style.display = 'block';
            tfoot.style.display = '';

            lots.forEach(lot => {
                const availClass = lot.available_qty <= 0 ? 'avail-zero'
                    : lot.available_qty < 50 ? 'avail-low'
                        : 'avail-good';
                const tr = document.createElement('tr');
                tr.dataset.bbsuId = lot.bbsu_batch_id;
                tr.dataset.bbsuNo = lot.batch_no;
                tr.dataset.availableQty = lot.available_qty;

                tr.innerHTML = `
          <td><span class="lot-bbsu-tag">${lot.batch_no}</span></td>
          <td style="font-size:12.5px;font-weight:600">${lot.material_name}</td>
          <td style="font-weight:600;color:var(--txtm)">${lot.material_unit ?? 'KG'}</td>
          <td><span class="avail-pill ${availClass}">${Number(lot.available_qty).toFixed(3)}</span></td>
          <td>
            <input type="number" class="assign-input" id="flux_assign_${lot.bbsu_batch_id}"
              placeholder="0.000" step="0.001" min="0.001" max="${lot.available_qty}"
              ${lot.available_qty <= 0 ? 'disabled title="No available quantity"' : ''}
              oninput="onFluxAssignInput(${lot.bbsu_batch_id}, ${lot.available_qty})"
              onclick="event.stopPropagation()">
          </td>`;

                tr.addEventListener('click', (e) => {
                    if (e.target.tagName === 'INPUT') return;
                    const inp = document.getElementById(`flux_assign_${lot.bbsu_batch_id}`);
                    if (inp && !inp.disabled) inp.focus();
                });

                tbody.appendChild(tr);
            });
        }

        function onFluxAssignInput(bbsuId, maxQty) {
            const input = document.getElementById(`flux_assign_${bbsuId}`);
            if (!input) return;

            let val = parseFloat(input.value);
            if (isNaN(val) || val < 0) { val = 0; input.value = ''; }
            if (val > maxQty) {
                val = parseFloat(maxQty.toFixed(3));
                input.value = val.toFixed(3);
                input.style.borderColor = '#d97706';
            } else {
                input.style.borderColor = val > 0 ? 'var(--g)' : '';
            }

            const tr = input.closest('tr');
            if (tr) tr.classList.toggle('selected', val > 0);

            recalcFluxModalTotal();
        }

        function recalcFluxModalTotal() {
            let total = 0;
            document.querySelectorAll('#fluxLotTbody .assign-input').forEach(inp => {
                const v = parseFloat(inp.value);
                if (!isNaN(v) && v > 0) total += v;
            });
            document.getElementById('fluxTotalAssign').textContent = total > 0 ? total.toFixed(3) : '0.000';
            document.getElementById('fluxConfirmBtn').disabled = total <= 0;
        }

        function confirmFluxSelection() {
            if (!fluxActiveRowIndex) return;

            const i = fluxActiveRowIndex;
            const selections = [];

            document.querySelectorAll('#fluxLotTbody tr').forEach(tr => {
                const bbsuId = tr.dataset.bbsuId;
                const bbsuNo = tr.dataset.bbsuNo;
                const inp = document.getElementById(`flux_assign_${bbsuId}`);
                const qty = parseFloat(inp?.value);
                if (!isNaN(qty) && qty > 0) selections.push({ bbsuId, bbsuNo, qty });
            });

            if (!selections.length) return;

            const totalQty = selections.reduce((s, r) => s + r.qty, 0);
            const batchIds = selections.map(r => r.bbsuId).join(',');
            const batchNos = selections.map(r => r.bbsuNo).join(',');

            document.getElementById(`fl_bbsu_id_${i}`).value = batchIds;
            document.getElementById(`fl_bbsu_no_${i}`).value = batchNos;

            const tr = document.getElementById(`frow-${i}`);
            if (tr) tr.dataset.fluxBbsuSelections = JSON.stringify(selections);

            // Set QTY field to total assigned qty
            const qtyInput = document.getElementById(`fl_qty_${i}`);
            if (qtyInput) {
                qtyInput.value = totalQty.toFixed(3);
                recalcFluxTotals();
            }

            // Show selected batches as tooltip on the QTY input
            const fluxQtyEl = document.getElementById(`fl_qty_${i}`);
            if (fluxQtyEl) {
                fluxQtyEl.title = 'BBSU: ' + selections.map(r => `${r.bbsuNo} (${r.qty} KG)`).join(', ');
                fluxQtyEl.style.borderColor = 'var(--g)';
            }

            triggerAutosave();
            document.getElementById('fluxLotModal').classList.remove('open');
            document.body.style.overflow = '';
            fluxActiveRowIndex = null;
        }
    </script>
@endpush