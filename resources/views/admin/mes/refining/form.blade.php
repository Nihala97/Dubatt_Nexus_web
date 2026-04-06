@extends('admin.layouts.app')
@section('title', 'Refining Log Sheet')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <a href="{{ route('admin.mes.refining.index') }}" style="color:var(--text-muted);text-decoration:none">Refining</a>
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

        .btn-add svg {
            width: 13px;
            height: 13px;
            stroke: #fff;
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round
        }

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

        .four-col {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 14px
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

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

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

        .tbl-wrap {
            overflow-x: auto
        }

        /* Modal shared styles */
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
            /* display: inline-flex; mk changed */ 
            align-items: center;
            gap: 5px;
            padding: 3px 10px;    
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700
        }

        .avail-good {
            background: #d1fae5;
            color: #065f46
        }

        .avail-low {
            background: #fef9c3;
            color: #854d0e
        }

        .avail-zero {
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

        .smt-tag {
            display: inline-block;
            padding: 2px 8px;
            background: var(--gl);
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            color: var(--g);
            font-variant-numeric: tabular-nums
        }

        /* Output panel */
        .output-panel {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px
        }

        @media(max-width:900px) {

            .two-col,
            .three-col,
            .four-col,
            .output-panel {
                grid-template-columns: 1fr
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
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        /* ── Searchable Dropdown (ERPNext portal style) ───────────────── */
        .sdd {
            display: block;
            width: 100%
        }

        .sdd-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            border: 1.5px solid var(--bdr);
            border-radius: 6px;
            background: var(--gxl);
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            color: var(--txt);
            cursor: pointer;
            user-select: none;
            gap: 6px;
            transition: border-color .18s, background .18s;
            min-width: 130px
        }

        .sdd-trigger:hover,
        .sdd.open>.sdd-trigger {
            border-color: var(--g);
            background: var(--white)
        }

        .sdd-trigger-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left
        }

        .sdd-trigger-text.placeholder {
            color: var(--txtmu)
        }

        .sdd-trigger-chevron {
            width: 12px;
            height: 12px;
            stroke: var(--txtmu);
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
            transition: transform .18s
        }

        .sdd.open>.sdd-trigger .sdd-trigger-chevron {
            transform: rotate(180deg);
            stroke: var(--g)
        }

        /* Portal panel — appended to body, positioned by JS */
        .sdd-portal {
            position: fixed;
            z-index: 9999;
            background: #fff;
            border: 1.5px solid var(--g);
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .16);
            min-width: 220px;
            width: 260px;
            overflow: hidden;
            display: none;
            animation: sddIn .12s ease
        }

        .sdd-portal.visible {
            display: block
        }

        @keyframes sddIn {
            from {
                opacity: 0;
                transform: translateY(-4px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .sdd-search-wrap {
            padding: 8px 10px;
            border-bottom: 1px solid var(--bdr)
        }

        .sdd-search {
            width: 100%;
            padding: 7px 10px 7px 32px;
            border: 1.5px solid var(--bdr);
            border-radius: 7px;
            background: var(--gxl);
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            color: var(--txt);
            outline: none;
            transition: border-color .18s;
            box-sizing: border-box
        }

        .sdd-search:focus {
            border-color: var(--g);
            background: #fff
        }

        .sdd-search::placeholder {
            color: var(--txtmu)
        }

        .sdd-search-wrap {
            position: relative
        }

        .sdd-search-ico {
            position: absolute;
            left: 18px;
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

        .sdd-list {
            max-height: 220px;
            overflow-y: auto;
            padding: 4px 0
        }

        .sdd-item {
            padding: 8px 14px;
            font-size: 13px;
            cursor: pointer;
            transition: background .1s;
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--txt);
            white-space: nowrap
        }

        .sdd-item:hover {
            background: #f0f9f4;
            color: var(--g)
        }

        .sdd-item.selected {
            background: #e8f5ed;
            color: var(--g);
            font-weight: 600
        }

        .sdd-item-check {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            stroke: transparent;
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .sdd-item.selected .sdd-item-check {
            stroke: var(--g)
        }

        .sdd-empty {
            padding: 18px 14px;
            font-size: 12.5px;
            color: var(--txtmu);
            text-align: center
        }

        .sdd-clear {
            display: none;
            width: 13px;
            height: 13px;
            stroke: var(--txtmu);
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
            cursor: pointer;
            transition: stroke .15s
        }

        .sdd-clear:hover {
            stroke: var(--err)
        }

        .sdd.open>.sdd-trigger .sdd-clear,
        .sdd-trigger:hover .sdd-clear {
            display: block
        }
    </style>
@endpush

@section('content')

    {{-- Page header --}}
    <div class="ph">
        <div>
            <h2 id="pageTitle">Loading…</h2>
            <p id="pageSubtitle"></p>
            <div id="statusBadge" style="margin-top:6px"></div>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.mes.refining.index') }}" class="btn btn-outline btn-sm">
                <svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg> Back
            </a>
        </div>
    </div>

    <div id="readonlyNotice" class="readonly-notice">🔒 This batch has been submitted and is locked from editing.</div>
    <div id="formAlert" class="form-alert"></div>

    {{-- Shared SDD portal panel (appended once, reused by all dropdowns) --}}
    <div class="sdd-portal" id="sddPortal">
        <div class="sdd-search-wrap">
            <svg class="sdd-search-ico" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input class="sdd-search" id="sddPortalSearch" placeholder="Search…" autocomplete="off"
                oninput="sddPortalFilter(this.value)" onkeydown="sddPortalKeydown(event)">
        </div>
        <div class="sdd-list" id="sddPortalList"></div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
    SMELTING LOT SELECTION MODAL (for Raw Materials + Chemicals)
    ══════════════════════════════════════════════════════════════ --}}
    <div class="modal-overlay" id="smtLotModal" onclick="closeSmtModal(event)">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-head-left">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                    </svg>
                    <div>
                        <div class="modal-title" id="smtModalTitle">Select Smelting Batch</div>
                        <div class="modal-subtitle" id="smtModalSubtitle">Enter the quantity to assign from each smelting
                            batch</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeSmtModal()" title="Close">✕</button>
            </div>
            <div class="modal-body">
                <div id="smtLotLoading" class="lot-loading">
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
                    <p style="margin-top:8px">Loading smelting batches…</p>
                </div>
                <div class="lot-empty" id="smtLotEmpty" style="display:none">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c8dfd1" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 10px">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    No submitted smelting batches found for this material with available quantity.
                </div>
                <div class="tbl-wrap" id="smtLotTableScroll" style="display:none">
                    <table class="lot-table">
                        <thead>
                            <tr>
                                <th>BATCH NO</th>
                                <th>MATERIAL</th>
                                <th>UNIT</th>
                                <th>AVAILABLE QTY</th>
                                <th>ASSIGN QTY</th>
                            </tr>
                        </thead>
                        <tbody id="smtLotTbody"></tbody>
                        <tfoot id="smtLotTfoot" style="display:none">
                            <tr style="background:var(--gl)">
                                <td colspan="3"
                                    style="text-align:right;padding:8px 12px;font-size:11px;font-weight:700;color:var(--g);letter-spacing:.7px">
                                    TOTAL ASSIGN QTY</td>
                                <td colspan="2" style="padding:8px 12px">
                                    <span id="smtTotalAssign"
                                        style="font-size:14px;font-weight:800;color:var(--g)">0.000</span>
                                    <span style="font-size:11px;color:var(--txtmu);margin-left:4px">KG</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeSmtModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" id="smtConfirmBtn" onclick="confirmSmtSelection()" disabled>
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg> OK
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
    OUTPUT QTY WINDOW MODAL (shared for finished goods + dross)
    ══════════════════════════════════════════════════════════════ --}}
    <div class="modal-overlay" id="outputQtyModal" onclick="closeOutputModal(event)">
        <div class="modal-box" style="max-width:420px">
            <div class="modal-head">
                <div class="modal-head-left">
                    <svg viewBox="0 0 24 24">
                        <path d="M5 8l6 6 6-6" />
                    </svg>
                    <div>
                        <div class="modal-title" id="outputModalTitle">Output QTY Window</div>
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
                                style="padding:9px 14px;font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:right;width:80px;border-right:1px solid var(--bdr)">
                                SL NO</th>
                            <th
                                style="padding:9px 14px;font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g);text-align:right">
                                QTY (KG)</th>
                        </tr>
                    </thead>
                    <tbody id="outputBlockTbody"></tbody>
                    <tfoot>
                        <tr style="background:var(--gl);border-top:2px solid var(--bdr)">
                            <td
                                style="padding:10px 14px;font-size:11px;font-weight:800;letter-spacing:.8px;color:var(--g);text-align:right;border-right:1px solid var(--bdr)">
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
                    </svg>ADD
                </button>
                <div style="display:flex;gap:8px">
                    <button class="btn btn-outline btn-sm" onclick="closeOutputModal()">Cancel</button>
                    <button class="btn btn-primary btn-sm" onclick="confirmOutputQty()">
                        <svg viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
    SECTION 1 — HEADER: Batch No, Pot No, Material, Date
    ════════════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <path d="M2 20h20M6 20V8l6-6 6 6v12" />
                </svg>
                <span>Refining Log Sheet</span>
            </div>
        </div>
        <div class="card-body">
            <div class="four-col">
                <div class="field">
                    <label>Batch No <span class="req">*</span></label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M9 9h6M9 13h6M9 17h4" />
                        </svg>
                        <input type="text" id="batch_no" placeholder="Auto-generated" readonly
                            style="background:#eef6f1;cursor:default">
                    </div>
                </div>
                <div class="field">
                    <label>Pot No</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                        </svg>
                        <input type="text" id="pot_no" placeholder="e.g. POT-A1" oninput="triggerAutosave()">
                    </div>
                </div>
                <div class="field">
                    <label>Material</label>
                    <div class="sdd" id="sdd_material_id">
                        <div class="sdd-trigger" onclick="toggleSdd('material_id')">
                            <span class="sdd-trigger-text placeholder" id="sdd_material_id_label"
                                data-placeholder="Select material…">Select material…</span>
                            <svg class="sdd-clear" onclick="clearSdd('material_id',event)" viewBox="0 0 24 24">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                            <svg class="sdd-trigger-chevron" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>
                        <input type="hidden" id="material_id" onchange="triggerAutosave()">
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
                        <input type="date" id="date" onchange="triggerAutosave()">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
    SECTION 2 — INPUT: Lead Raw Material + Chemicals & Metals
    ════════════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                <span>Input</span>
            </div>
            <span id="inputAutosaveStatus"
                style="font-size:11.5px;color:var(--txtmu);display:none;align-items:center;gap:5px">
                <span class="as-dot" id="inputAsDot"></span>
                <span id="inputAsText" style="font-size:11px"></span>
            </span>
        </div>
        <div class="card-body" style="padding:14px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

                {{-- Lead Raw Material --}}
                <div style="border:1px solid var(--bdr);border-radius:9px;overflow:hidden">
                    <div
                        style="padding:10px 16px;background:var(--gl);border-bottom:1px solid var(--bdr);display:flex;align-items:center;justify-content:space-between">
                        <span
                            style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--g)">Lead
                            Raw Material</span>
                        <button class="btn-add" onclick="addRawRow()" id="btnAddRaw">
                            <svg viewBox="0 0 24 24">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg> Add
                        </button>
                    </div>
                    <div class="tbl-wrap">
                        <table class="data-table" id="rawTable">
                            <thead>
                                <tr>
                                    <th style="width:36px">#</th>
                                    <th>Raw Material</th>
                                    <th>QTY (KG)</th>
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
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Chemicals and Metals --}}
                <div style="border:1px solid var(--bdr);border-radius:9px;overflow:hidden">
                    <div
                        style="padding:10px 16px;background:var(--gl);border-bottom:1px solid var(--bdr);display:flex;align-items:center;justify-content:space-between">
                        <span
                            style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--g)">Chemicals
                            and Metals</span>
                        <button class="btn-add" onclick="addChemRow()" id="btnAddChem">
                            <svg viewBox="0 0 24 24">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg> Add
                        </button>
                    </div>
                    <div class="tbl-wrap">
                        <table class="data-table" id="chemTable">
                            <thead>
                                <tr>
                                    <th style="width:36px">#</th>
                                    <th>Chemical / Metal</th>
                                    <th>QTY (KG)</th>
                                    <th style="width:32px"></th>
                                </tr>
                            </thead>
                            <tbody id="chemBody"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="text-align:right;padding-right:10px">TOTAL</td>
                                    <td><input type="text" id="chemTotalQty" readonly class="ri ro"
                                            style="font-weight:700;color:var(--g)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
    SECTION 3 — Consumption: LPG, Electricity, Liquid Oxygen
    Layout: single card, table with col-headers + aligned rows
    ════════════════════════════════════════════════════════════ --}}
    <div class="card" style="margin-bottom:18px">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                <span>Consumption</span>
            </div>
        </div>
        <div class="card-body" style="padding:0">

            {{-- Table: label col + 3 value cols --}}
            <table style="width:100%;border-collapse:collapse">
                <colgroup>
                    <col style="width:180px">
                    <col>
                    <col>
                    <col>
                    <col style="width:38%">
                </colgroup>

                {{-- Column headers --}}
                <thead>
                    <tr>
                        <th
                            style="padding:10px 16px;background:var(--gl);border-bottom:1px solid var(--bdr);border-right:1px solid var(--bdr);font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--txtmu)">
                        </th>
                        <th
                            style="padding:10px 16px;background:var(--gl);border-bottom:1px solid var(--bdr);border-right:1px solid var(--bdr);text-align:left">
                            <div style="display:flex;align-items:center;gap:7px">
                                <svg style="width:13px;height:13px;stroke:var(--g);fill:none;stroke-width:2"
                                    viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
                                    <path d="M12 6v6l4 2" />
                                </svg>
                                <span
                                    style="font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g)">LPG</span>
                            </div>
                        </th>
                        <!-- Add after the LPG <th> block, before the Electricity <th> -->
                        <th style="padding:10px 16px;background:var(--gl);border-bottom:1px solid var(--bdr);border-right:1px solid var(--bdr);text-align:left">
                            <div style="display:flex;align-items:center;gap:7px">
                                <svg style="width:13px;height:13px;stroke:var(--g);fill:none;stroke-width:2" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
                                    <path d="M12 6v6l4 2" />
                                </svg>
                                <span style="font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g)">LPG 2</span>
                            </div>
                        </th>
                        <th
                            style="padding:10px 16px;background:var(--gl);border-bottom:1px solid var(--bdr);border-right:1px solid var(--bdr);text-align:left">
                            <div style="display:flex;align-items:center;gap:7px">
                                <svg style="width:13px;height:13px;stroke:var(--g);fill:none;stroke-width:2"
                                    viewBox="0 0 24 24">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                </svg>
                                <span
                                    style="font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g)">Electricity</span>
                            </div>
                        </th>
                        <th
                            style="padding:10px 16px;background:var(--gl);border-bottom:1px solid var(--bdr);text-align:left">
                            <div style="display:flex;align-items:center;gap:7px">
                                <svg style="width:13px;height:13px;stroke:var(--g);fill:none;stroke-width:2"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M8 12h8M12 8v8" />
                                </svg>
                                <span
                                    style="font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--g)">Liquid
                                    Oxygen</span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody>

                    {{-- Row 1: Initial / Flow NM³ --}}
                    <tr>
                        <td
                            style="padding:10px 16px;border-right:1px solid var(--bdr);border-bottom:1px solid var(--bdr);background:var(--gxl)">
                            <span
                                style="font-size:10.5px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--txtm)">Initial</span>
                        </td>
                        <td style="padding:8px 12px;border-right:1px solid var(--bdr);border-bottom:1px solid var(--bdr)">
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="lpg_initial" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('lpg');triggerAutosave()">
                            </div>
                        </td>
                        <td style="padding:8px 12px;border-right:1px solid var(--bdr);border-bottom:1px solid var(--bdr)">
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="lpg2_initial" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('lpg2');triggerAutosave()">
                            </div>
                        </td>
                        <td style="padding:8px 12px;border-right:1px solid var(--bdr);border-bottom:1px solid var(--bdr)">
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="electricity_initial" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('electricity');triggerAutosave()">
                            </div>
                        </td>
                        <td style="padding:8px 12px;border-bottom:1px solid var(--bdr)">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                <div>
                                    <div
                                        style="font-size:9.5px;font-weight:700;color:var(--txtmu);letter-spacing:.5px;margin-bottom:4px;display:flex;align-items:center;gap:5px">
                                        FLOW (NM³) <span
                                            style="font-size:9px;padding:1px 5px;border-radius:3px;background:#e0f2fe;color:#0369a1;font-weight:700">MANUAL</span>
                                    </div>
                                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                            <path d="M12 2v20M2 12h20" />
                                        </svg>
                                        <input type="number" id="oxygen_flow_nm3" step="0.001" placeholder="0.000"
                                            oninput="calcOxygen();triggerAutosave()">
                                    </div>
                                </div>
                                <div>
                                    <div
                                        style="font-size:9.5px;font-weight:700;color:var(--txtmu);letter-spacing:.5px;margin-bottom:4px;display:flex;align-items:center;gap:5px">
                                        FLOW (KG) <span
                                            style="font-size:9px;padding:1px 5px;border-radius:3px;background:#dcfce7;color:#166534;font-weight:700">AUTO</span>
                                        <span style="font-size:9px;color:var(--txtmu);font-weight:400">= NM³ × 1.429</span>
                                    </div>
                                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                            <path d="M12 2v20M2 12h20" />
                                        </svg>
                                        <input type="number" id="oxygen_flow_kg" step="0.001" placeholder="Auto" readonly
                                            class="ro" style="background:#eef6f1;cursor:default">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- Row 2: Final / Flow Time --}}
                    <tr>
                        <td
                            style="padding:10px 16px;border-right:1px solid var(--bdr);border-bottom:1px solid var(--bdr);background:var(--gxl)">
                            <span
                                style="font-size:10.5px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--txtm)">Final</span>
                        </td>
                        <td style="padding:8px 12px;border-right:1px solid var(--bdr);border-bottom:1px solid var(--bdr)">
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="lpg_final" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('lpg');triggerAutosave()">
                            </div>
                        </td>
                        <td style="padding:8px 12px;border-right:1px solid var(--bdr);border-bottom:1px solid var(--bdr)">
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="lpg2_final" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('lpg2');triggerAutosave()">
                            </div>
                        </td>
                        <td style="padding:8px 12px;border-right:1px solid var(--bdr);border-bottom:1px solid var(--bdr)">
                            <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                    <line x1="22" y1="12" x2="2" y2="12" />
                                </svg>
                                <input type="number" id="electricity_final" step="0.001" placeholder="0.000"
                                    oninput="calcConsumption('electricity');triggerAutosave()">
                            </div>
                        </td>
                        <td style="padding:8px 12px;border-bottom:1px solid var(--bdr)">
                            <div style="max-width:50%">
                                <div
                                    style="font-size:9.5px;font-weight:700;color:var(--txtmu);letter-spacing:.5px;margin-bottom:4px;display:flex;align-items:center;gap:5px">
                                    FLOW TIME (HR) <span
                                        style="font-size:9px;padding:1px 5px;border-radius:3px;background:#e0f2fe;color:#0369a1;font-weight:700">MANUAL</span>
                                </div>
                                <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    <input type="number" id="oxygen_flow_time" step="0.001" placeholder="0.000"
                                        oninput="calcOxygen();triggerAutosave()">
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- Row 3: Consumption total row --}}
                    <tr style="background:var(--gl)">
                        <td style="padding:10px 16px;border-right:1px solid var(--bdr)">
                            <span
                                style="font-size:10.5px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;color:var(--g)">Consumption</span>
                        </td>
                        <td style="padding:10px 12px;border-right:1px solid var(--bdr)">
                            <div style="display:flex;align-items:center;justify-content:space-between">
                                <span style="font-size:10px;font-weight:700;color:var(--g);letter-spacing:.5px">TOTAL</span>
                                <span style="font-size:15px;font-weight:700;color:var(--g)"
                                    id="lpg_consumption_display">—</span>
                            </div>
                            <input type="hidden" id="lpg_consumption">
                        </td>
                        <td style="padding:10px 12px;border-right:1px solid var(--bdr)">
                            <div style="display:flex;align-items:center;justify-content:space-between">
                                <span style="font-size:10px;font-weight:700;color:var(--g);letter-spacing:.5px">TOTAL</span>
                                <span style="font-size:15px;font-weight:700;color:var(--g)"
                                    id="lpg2_consumption_display">—</span>
                            </div>
                            <input type="hidden" id="lpg2_consumption">
                        </td>
                        <td style="padding:10px 12px;border-right:1px solid var(--bdr)">
                            <div style="display:flex;align-items:center;justify-content:space-between">
                                <span style="font-size:10px;font-weight:700;color:var(--g);letter-spacing:.5px">TOTAL</span>
                                <span style="font-size:15px;font-weight:700;color:var(--g)"
                                    id="electricity_consumption_display">—</span>
                            </div>
                            <input type="hidden" id="electricity_consumption">
                        </td>
                        <td style="padding:10px 12px">
                            <div style="display:flex;align-items:center;gap:16px">
                                <div style="flex:1">
                                    <div
                                        style="font-size:9.5px;font-weight:700;color:var(--txtmu);letter-spacing:.5px;margin-bottom:4px;display:flex;align-items:center;gap:5px">
                                        CONSUMPTION (KG) <span
                                            style="font-size:9px;padding:1px 5px;border-radius:3px;background:#dcfce7;color:#166534;font-weight:700">AUTO</span>
                                        <span style="font-size:9px;color:var(--txtmu);font-weight:400">= Time ×
                                            Flow(KG)</span>
                                    </div>
                                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                            <line x1="22" y1="12" x2="2" y2="12" />
                                        </svg>
                                        <input type="number" id="oxygen_consumption" step="0.001" placeholder="Auto"
                                            readonly class="ro" style="background:#eef6f1;cursor:default">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>

        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
    SECTION 4 — Process Details
    ════════════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                <span>Process Details</span>
            </div>
            <button class="btn-add" onclick="addProcessRow()" id="btnAddProcess">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg> Add Row
            </button>
        </div>
        <div class="card-body" style="padding:0">
            <div class="tbl-wrap">
                <table class="data-table" id="procTable">
                    <thead>
                        <tr>
                            <th>PROCESS</th>
                            <th>START TIME</th>
                            <th style="width:60px"></th>
                            <th>END TIME</th>
                            <th style="width:60px"></th>
                            <th>TOTAL TIME</th>
                            <th style="width:32px"></th>
                        </tr>
                    </thead>
                    <tbody id="procBody"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align:right;padding-right:12px">TOTAL PROCESS TIME</td>
                            <td><input type="text" class="ri ro" id="totalProcessTime" readonly
                                    style="font-weight:700;color:var(--g)"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
    SECTION 5 — Output: Finished Goods + Drosses
    ════════════════════════════════════════════════════════════ --}}
    <div class="output-panel">

        {{-- Finished Goods --}}
        <div class="card" style="margin-bottom:0">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    <span>Finished Goods</span>
                </div>
                <button class="btn-add" onclick="addFGRow()" id="btnAddFG">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg> Add
                </button>
            </div>
            <div class="card-body" style="padding:0">
                <div class="tbl-wrap">
                    <table class="data-table" id="fgTable">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th>Material</th>
                                <th>QTY (KG)</th>
                                <th style="width:32px"></th>
                            </tr>
                        </thead>
                        <tbody id="fgBody"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align:right;padding-right:10px">TOTAL</td>
                                <td><input type="text" id="fgTotalQty" readonly class="ri ro"
                                        style="font-weight:700;color:var(--g)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Drosses --}}
        <div class="card" style="margin-bottom:0">
            <div class="card-head">
                <div class="card-head-left">
                    <svg viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    <span>Drosses</span>
                </div>
                <button class="btn-add" onclick="addDrossRow()" id="btnAddDross">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg> Add
                </button>
            </div>
            <div class="card-body" style="padding:0">
                <div class="tbl-wrap">
                    <table class="data-table" id="drossTable">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th>Material</th>
                                <th>QTY (KG)</th>
                                <th style="width:32px"></th>
                            </tr>
                        </thead>
                        <tbody id="drossBody"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align:right;padding-right:10px">TOTAL</td>
                                <td><input type="text" id="drossTotalQty" readonly class="ri ro"
                                        style="font-weight:700;color:var(--g)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div style="margin-bottom:18px"></div>

    {{-- Sticky footer --}}
    <div class="form-actions" id="formActions">
        <a href="{{ route('admin.mes.refining.index') }}" class="btn btn-outline btn-sm">Cancel</a>
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
        // ════════════════════════════════════════════════════════════════
        // CONSTANTS + STATE
        // ════════════════════════════════════════════════════════════════
        const PATH = window.location.pathname.split('/').filter(Boolean);
        const isCreate = PATH[PATH.length - 1] === 'create';
        const recordId = isCreate ? null : PATH[PATH.length - 2];

        let isSubmitted = false;
        let rawRowCount = 0, chemRowCount = 0, procRowCount = 0, fgRowCount = 0, drossRowCount = 0;
        let autosaveTimer;
        let itemsList = [];  // all materials from DB
        let processNames = [];  // from API
        // Output block state (per output type: 'fg' or 'dross')
        // Each entry: { rowIndex, blocks: [{qty}] }
        let outputModal = { type: null, rowIndex: null, blocks: [] };
        const OUTPUT_MAX_ROWS = 11;

        // ── Smelting lot modal state ──────────────────────────────────
        let smtModal = { type: null, rowIndex: null }; // type: 'raw'|'chem'

        // ════════════════════════════════════════════════════════════════
        // INIT
        // ════════════════════════════════════════════════════════════════
        async function init() {
            document.getElementById('date').value = new Date().toISOString().slice(0, 10);
            await loadItems();
            await loadProcessNames();

            if (isCreate) {
                const res = await apiFetch('/refining/generate-batch-no');
                if (res?.ok) {
                    const d = await res.json();
                    document.getElementById('batch_no').value = d.batch_no;
                }
                document.getElementById('pageTitle').textContent = 'Create Refining Batch';
                document.getElementById('pageSubtitle').textContent = 'New refining log sheet';
                document.getElementById('breadcrumbTitle').textContent = 'Create Batch';
                document.getElementById('btnSaveLabel').textContent = 'Create Batch';
                addRawRow(); addChemRow(); addProcessRow(); addFGRow(); addDrossRow();
            } else {
                await loadRecord();
            }
            setupAutosave();
        }
        init();

        // ════════════════════════════════════════════════════════════════
        // DATA LOADERS
        // ════════════════════════════════════════════════════════════════
        async function loadItems() {
            try {
                const res = await apiFetch('/materials?per_page=500');
                if (res?.ok) {
                    const d = await res.json();
                    itemsList = d.data?.data ?? d.data ?? [];
                    buildMaterialDropdown('material_id');
                }
            } catch (e) { console.warn('Materials load failed', e); }
        }

        async function loadProcessNames() {
            try {
                const res = await apiFetch('/refining/process-names');
                if (res?.ok) {
                    const d = await res.json();
                    processNames = d.data ?? [];
                }
            } catch (e) { console.warn('Process names load failed', e); }
        }

        // ════════════════════════════════════════════════════════════════
        // SEARCHABLE DROPDOWN ENGINE — Portal / fixed positioning
        // Works inside overflow:hidden tables. One shared panel for all.
        // ════════════════════════════════════════════════════════════════
        const sddRegistry = {};   // fieldId -> { items, selected }
        let sddActiveField = null; // currently open fieldId

        function sddRegister(fieldId, items, selectedValue = null) {
            sddRegistry[fieldId] = { items, selected: null };
            if (selectedValue) sddSelect(fieldId, String(selectedValue), false);
            else sddUpdateTrigger(fieldId);
        }

        function sddUpdateTrigger(fieldId) {
            const reg = sddRegistry[fieldId];
            const label = document.getElementById(`sdd_${fieldId}_label`);
            const hidden = document.getElementById(fieldId);
            if (!label) return;
            if (reg?.selected) {
                label.textContent = reg.selected.label;
                label.classList.remove('placeholder');
            } else {
                label.textContent = label.dataset.placeholder || 'Select…';
                label.classList.add('placeholder');
            }
            if (hidden) hidden.value = reg?.selected?.value ?? '';
        }

        function sddSelect(fieldId, value, triggerChange = true) {
            if (!sddRegistry[fieldId]) return;
            const item = value
                ? sddRegistry[fieldId].items.find(i => String(i.value) === String(value))
                : null;
            sddRegistry[fieldId].selected = item || null;
            sddUpdateTrigger(fieldId);

            const hidden = document.getElementById(fieldId);
            if (hidden && triggerChange) hidden.dispatchEvent(new Event('change'));

            sddClosePortal();
        }

        function clearSdd(fieldId, e) {
            if (e) { e.stopPropagation(); e.preventDefault(); }
            sddSelect(fieldId, '', false);
            const hidden = document.getElementById(fieldId);
            if (hidden) hidden.dispatchEvent(new Event('change'));
        }

        function toggleSdd(fieldId) {
            if (sddActiveField === fieldId) { sddClosePortal(); return; }
            sddOpenPortal(fieldId);
        }

        function sddOpenPortal(fieldId) {
            const trigger = document.querySelector(`#sdd_${fieldId} .sdd-trigger`);
            if (!trigger || !sddRegistry[fieldId]) return;

            sddActiveField = fieldId;

            // Mark trigger open
            document.querySelectorAll('.sdd.open').forEach(el => el.classList.remove('open'));
            document.getElementById(`sdd_${fieldId}`)?.classList.add('open');

            // Position portal under trigger using fixed coords
            const portal = document.getElementById('sddPortal');
            const rect = trigger.getBoundingClientRect();
            const viewW = window.innerWidth;
            const viewH = window.innerHeight;

            portal.style.top = '';
            portal.style.bottom = '';
            portal.style.left = '';
            portal.style.right = '';
            portal.style.width = Math.max(rect.width, 240) + 'px';

            // position:fixed uses viewport coords — NO scrollX/Y offset needed
            // Horizontal: align with trigger left, clamp so it doesn't overflow right edge
            let left = rect.left;
            const portalW = Math.max(rect.width, 240);
            if (left + portalW > viewW - 8) left = Math.max(8, viewW - portalW - 8);
            portal.style.left = left + 'px';

            // Vertical: below trigger if enough space, otherwise above
            const spaceBelow = viewH - rect.bottom;
            const spaceAbove = rect.top;
            if (spaceBelow >= 200 || spaceBelow >= spaceAbove) {
                portal.style.top = (rect.bottom + 4) + 'px';
            } else {
                portal.style.bottom = (viewH - rect.top + 4) + 'px';
            }

            // Populate list
            sddPortalRender('');
            portal.classList.add('visible');

            // Focus search
            const search = document.getElementById('sddPortalSearch');
            if (search) { search.value = ''; setTimeout(() => search.focus(), 40); }
        }

        function sddClosePortal() {
            document.getElementById('sddPortal')?.classList.remove('visible');
            document.querySelectorAll('.sdd.open').forEach(el => el.classList.remove('open'));
            sddActiveField = null;
        }

        function sddPortalRender(query) {
            if (!sddActiveField || !sddRegistry[sddActiveField]) return;
            const q = query.toLowerCase().trim();
            const items = sddRegistry[sddActiveField].items;
            const filtered = q ? items.filter(i => i.label.toLowerCase().includes(q)) : items;
            const current = sddRegistry[sddActiveField].selected?.value ?? '';
            const list = document.getElementById('sddPortalList');
            if (!list) return;

            if (!filtered.length) {
                list.innerHTML = '<div class="sdd-empty">No results found</div>';
                return;
            }
            list.innerHTML = filtered.map(item => {
                const sel = String(item.value) === String(current);
                return `<div class="sdd-item${sel ? ' selected' : ''}" onclick="sddSelect('${sddActiveField}','${item.value}')">
                  <svg class="sdd-item-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                  <span>${item.label}</span>
                </div>`;
            }).join('');
        }

        function sddPortalFilter(query) { sddPortalRender(query); }

        function sddPortalKeydown(e) {
            if (e.key === 'Escape') { sddClosePortal(); }
        }

        // Close on outside click / scroll
        document.addEventListener('click', e => {
            if (!e.target.closest('.sdd') && !e.target.closest('#sddPortal')) {
                sddClosePortal();
            }
        });
        document.addEventListener('scroll', () => {
            if (sddActiveField) {
                const trigger = document.querySelector(`#sdd_${sddActiveField} .sdd-trigger`);
                if (trigger) {
                    const rect = trigger.getBoundingClientRect();
                    const portal = document.getElementById('sddPortal');
                    // position:fixed — viewport coords only, no scrollY
                    portal.style.top = (rect.bottom + 4) + 'px';
                    portal.style.left = rect.left + 'px';
                }
            }
        }, true);

        // ── Material / Process helpers (now using sdd) ─────────────────
        function buildMaterialDropdown(fieldId, selectedId = null) {
            const items = itemsList.map(i => ({ value: String(i.id), label: i.name ?? i.secondary_name ?? '' }));
            sddRegister(fieldId, items, selectedId ? String(selectedId) : null);
        }

        function initMaterialSdd(fieldId, selectedId = null) {
            const items = itemsList.map(i => ({ value: String(i.id), label: i.name ?? i.secondary_name ?? '' }));
            sddRegister(fieldId, items, selectedId ? String(selectedId) : null);
        }

        function initProcessSdd(fieldId, selectedVal = '') {
            const items = processNames.map(p => ({ value: p, label: p }));
            sddRegister(fieldId, items, selectedVal || null);
        }

        // Keep these for payload building — they now read hidden inputs
        function getMaterialOptions() { return ''; } // unused — kept for compat
        function getProcessOptions() { return ''; } // unused — kept for compat

        // ════════════════════════════════════════════════════════════════
        // LOAD RECORD (edit mode)
        // ════════════════════════════════════════════════════════════════
        async function loadRecord() {
            const res = await apiFetch(`/refining/${recordId}`);
            if (!res?.ok) { showAlert('Failed to load record.'); return; }
            const { data } = await res.json();

            isSubmitted = data.status === 'submitted';

            document.getElementById('batch_no').value = data.batch_no ?? '';
            document.getElementById('pot_no').value = data.pot_no ?? '';
            document.getElementById('date').value = data.date?.slice(0, 10) ?? '';
            buildMaterialDropdown('material_id', data.material_id);

            document.getElementById('lpg_initial').value = data.lpg_initial ?? '';
            document.getElementById('lpg_final').value = data.lpg_final ?? '';
            document.getElementById('lpg2_initial').value = data.lpg2_initial ?? '';
            document.getElementById('lpg2_final').value = data.lpg2_final ?? '';
// then recalc:
 
            document.getElementById('electricity_initial').value = data.electricity_initial ?? '';
            document.getElementById('electricity_final').value = data.electricity_final ?? '';
            document.getElementById('oxygen_flow_nm3').value = data.oxygen_flow_nm3 ?? '';
            document.getElementById('oxygen_flow_kg').value = data.oxygen_flow_kg ?? '';
            document.getElementById('oxygen_flow_time').value = data.oxygen_flow_time ?? '';
            document.getElementById('oxygen_consumption').value = data.oxygen_consumption ?? '';

            calcConsumption('lpg');
            calcConsumption('lpg2');
            calcConsumption('electricity');
            calcOxygen();

            (data.raw_materials ?? []).forEach(r => addRawRow(r));
            if (!data.raw_materials?.length) addRawRow();
            recalcRawTotals();

            (data.chemicals ?? []).forEach(c => addChemRow(c));
            if (!data.chemicals?.length) addChemRow();
            recalcChemTotals();

            (data.process_details ?? []).forEach(p => addProcessRow(p));
            if (!data.process_details?.length) addProcessRow();

            (data.finished_goods_summary ?? []).forEach(fg => addFGRow(fg));
            if (!data.finished_goods_summary?.length) addFGRow();
            recalcFGTotals();

            (data.dross_summary ?? []).forEach(dr => addDrossRow(dr));
            if (!data.dross_summary?.length) addDrossRow();
            recalcDrossTotals();

            document.getElementById('pageTitle').textContent = 'Edit Refining Batch';
            document.getElementById('pageSubtitle').textContent = `Batch: ${data.batch_no}`;
            document.getElementById('breadcrumbTitle').textContent = 'Edit Batch';
            document.getElementById('btnSaveLabel').textContent = 'Save Draft';

            const badge = document.getElementById('statusBadge');
            if (isSubmitted) {
                badge.innerHTML = '<span class="badge badge-submitted">● Submitted</span>';
                setReadonly(true);
                document.getElementById('btnSubmit').style.display = 'none';
            } else {
                badge.innerHTML = '<span class="badge badge-draft">● Draft</span>';
                document.getElementById('btnSubmit').style.display = '';
            }
            calcTotalProcessTime();
        }

        // ════════════════════════════════════════════════════════════════
        // RAW MATERIALS TABLE
        // ════════════════════════════════════════════════════════════════
        function addRawRow(data = {}) {
            rawRowCount++;
            const i = rawRowCount;
            const tbody = document.getElementById('rawBody');
            const tr = document.createElement('tr');
            tr.id = `rrow-${i}`;
            tr.dataset.rowIndex = i;
            tr.dataset.smtSelections = data.smelting_selections ? JSON.stringify(data.smelting_selections) : '';
            tr.innerHTML = `
                <td style="text-align:center;font-size:12px;font-weight:700;color:var(--g);padding:8px 4px">${i}</td>
                <td style="position:relative;min-width:160px">
                  <div class="sdd" id="sdd_rm_id_${i}">
                    <div class="sdd-trigger" onclick="toggleSdd('rm_id_'+${i})">
                      <span class="sdd-trigger-text placeholder" id="sdd_rm_id_${i}_label" data-placeholder="Select material…">Select material…</span>
                      <svg class="sdd-clear" onclick="clearSdd('rm_id_'+${i},event)" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      <svg class="sdd-trigger-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <input type="hidden" id="rm_id_${i}" onchange="onRawMaterialChange(${i});triggerAutosave()">
                  </div>
                </td>
                <td>
                  <input type="number" class="ri" id="rm_qty_${i}"
                    value="${data.qty ?? ''}" step="0.001" placeholder="0.000"
                    onclick="onRawQtyClick(${i})" onfocus="onRawQtyFocus(${i})"
                    oninput="recalcRawTotals();triggerAutosave()"
                    style="min-width:90px;cursor:pointer" title="Click to assign from smelting batch">
                  <input type="hidden" id="rm_smt_id_${i}" value="${data.smelting_batch_id ?? ''}">
                  <input type="hidden" id="rm_smt_no_${i}" value="${data.smelting_batch_no ?? ''}">
                </td>
                <td><button class="del-btn" onclick="removeRow('rrow-${i}',recalcRawTotals)" title="Remove">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button></td>`;
            animateIn(tr);
            tbody.appendChild(tr);
            // init searchable dropdown after DOM insertion
            initMaterialSdd(`rm_id_${i}`, data.raw_material_id ?? null);
        }

        function onRawMaterialChange(i) { clearSmtOnRow('raw', i); }
        function onRawQtyClick(i) {
            const matId = document.getElementById(`rm_id_${i}`)?.value;
            if (matId) openSmtModal('raw', i);
        }
        function onRawQtyFocus(i) {
            const matId = document.getElementById(`rm_id_${i}`)?.value;
            const smtId = document.getElementById(`rm_smt_id_${i}`)?.value;
            if (matId && !smtId) openSmtModal('raw', i);
        }
        function recalcRawTotals() {
            let total = 0;
            document.querySelectorAll('#rawBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const v = parseFloat(document.getElementById(`rm_qty_${i}`)?.value);
                if (!isNaN(v)) total += v;
            });
            document.getElementById('rawTotalQty').value = total > 0 ? total.toFixed(3) : '';
        }

        // ════════════════════════════════════════════════════════════════
        // CHEMICALS TABLE
        // ════════════════════════════════════════════════════════════════
        function addChemRow(data = {}) {
            chemRowCount++;
            const i = chemRowCount;
            const tbody = document.getElementById('chemBody');
            const tr = document.createElement('tr');
            tr.id = `crow-${i}`;
            tr.dataset.rowIndex = i;
            tr.dataset.smtSelections = data.smelting_selections ? JSON.stringify(data.smelting_selections) : '';
            tr.innerHTML = `
                <td style="text-align:center;font-size:12px;font-weight:700;color:var(--g);padding:8px 4px">${i}</td>
                <td style="position:relative;min-width:160px">
                  <div class="sdd" id="sdd_ch_id_${i}">
                    <div class="sdd-trigger" onclick="toggleSdd('ch_id_'+${i})">
                      <span class="sdd-trigger-text placeholder" id="sdd_ch_id_${i}_label" data-placeholder="Select material…">Select material…</span>
                      <svg class="sdd-clear" onclick="clearSdd('ch_id_'+${i},event)" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      <svg class="sdd-trigger-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <input type="hidden" id="ch_id_${i}" onchange="onChemMaterialChange(${i});triggerAutosave()">
                  </div>
                </td>
                <td>
                  <input type="number" class="ri" id="ch_qty_${i}"
                    value="${data.qty ?? ''}" step="0.001" placeholder="0.000"
                    onclick="onChemQtyClick(${i})" onfocus="onChemQtyFocus(${i})"
                    oninput="recalcChemTotals();triggerAutosave()"
                    style="min-width:90px;cursor:pointer" title="Click to assign from smelting batch">
                  <input type="hidden" id="ch_smt_id_${i}" value="${data.smelting_batch_id ?? ''}">
                  <input type="hidden" id="ch_smt_no_${i}" value="${data.smelting_batch_no ?? ''}">
                </td>
                <td><button class="del-btn" onclick="removeRow('crow-${i}',recalcChemTotals)" title="Remove">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button></td>`;
            animateIn(tr);
            tbody.appendChild(tr);
            initMaterialSdd(`ch_id_${i}`, data.chemical_id ?? null);
        }

        function onChemMaterialChange(i) { clearSmtOnRow('chem', i); }
        function onChemQtyClick(i) {
            const matId = document.getElementById(`ch_id_${i}`)?.value;
            if (matId) openSmtModal('chem', i);
        }
        function onChemQtyFocus(i) {
            const matId = document.getElementById(`ch_id_${i}`)?.value;
            const smtId = document.getElementById(`ch_smt_id_${i}`)?.value;
            if (matId && !smtId) openSmtModal('chem', i);
        }
        function recalcChemTotals() {
            let total = 0;
            document.querySelectorAll('#chemBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const v = parseFloat(document.getElementById(`ch_qty_${i}`)?.value);
                if (!isNaN(v)) total += v;
            });
            document.getElementById('chemTotalQty').value = total > 0 ? total.toFixed(3) : '';
        }

        // ════════════════════════════════════════════════════════════════
        // SMELTING LOT MODAL (shared for raw + chem)
        // ════════════════════════════════════════════════════════════════
        function openSmtModal(type, rowIndex) {
            const prefix = type === 'raw' ? 'rm' : 'ch';
            const matId = document.getElementById(`${prefix}_id_${rowIndex}`)?.value;
            if (!matId) return;

            smtModal = { type, rowIndex };
            const matName = sddRegistry[`${prefix}_id_${rowIndex}`]?.selected?.label ?? '';
            document.getElementById('smtModalTitle').textContent = `Select Smelting Batch — ${matName}`;
            document.getElementById('smtModalSubtitle').textContent = 'Enter quantity to assign from each smelting batch. Total fills the QTY field.';
            document.getElementById('smtConfirmBtn').disabled = true;

            document.getElementById('smtLotModal').classList.add('open');
            document.body.style.overflow = 'hidden';
            loadSmtLots(matId);
        }

        function closeSmtModal(e) {
            if (e && e.target !== document.getElementById('smtLotModal')) return;
            document.getElementById('smtLotModal').classList.remove('open');
            document.body.style.overflow = '';
            smtModal = { type: null, rowIndex: null };
        }

        async function loadSmtLots(materialId) {
            const loading = document.getElementById('smtLotLoading');
            const empty = document.getElementById('smtLotEmpty');
            const scroll = document.getElementById('smtLotTableScroll');
            const tbody = document.getElementById('smtLotTbody');
            const tfoot = document.getElementById('smtLotTfoot');

            loading.style.display = 'block';
            empty.style.display = 'none';
            scroll.style.display = 'none';
            tfoot.style.display = 'none';
            tbody.innerHTML = '';
            document.getElementById('smtTotalAssign').textContent = '0.000';
            document.getElementById('smtConfirmBtn').disabled = true;

            const excl = recordId ? `?exclude_refining_id=${recordId}` : '';
            const res = await apiFetch(`/refining/smelting-lots/${materialId}${excl}`, { method: 'GET' });
            loading.style.display = 'none';

            if (!res || !res.ok) { empty.style.display = 'block'; return; }
            const json = await res.json();
            const lots = json.data ?? [];

            if (!lots.length) { empty.style.display = 'block'; return; }

            scroll.style.display = 'block';
            tfoot.style.display = '';

            lots.forEach(lot => {
                const pillClass = lot.available_qty <= 0 ? 'avail-zero'
                    : lot.available_qty < 50 ? 'avail-low' : 'avail-good';
                const tr = document.createElement('tr');
                tr.dataset.smtId = lot.smelting_batch_id;
                tr.dataset.smtNo = lot.batch_no;
                tr.dataset.availableQty = lot.available_qty;
                tr.innerHTML = `
                  <td><span class="smt-tag">${lot.batch_no}</span></td>
                  <td style="font-size:12.5px;font-weight:600">${lot.secondary_name}</td>
                  <td style="font-weight:600;color:var(--txtm)">${lot.material_unit ?? 'KG'}</td>
                  <td><span class="lot-table avail-pill ${pillClass}">${Number(lot.available_qty).toFixed(3)}</span></td>
                  <td>
                    <input type="number" class="assign-input" id="smt_assign_${lot.smelting_batch_id}"
                      placeholder="0.000" step="0.001" min="0.001" max="${lot.available_qty}"
                      ${lot.available_qty <= 0 ? 'disabled title="No available quantity"' : ''}
                      oninput="onSmtAssignInput(${lot.smelting_batch_id}, ${lot.available_qty})"
                      onclick="event.stopPropagation()">
                  </td>`;
                tr.addEventListener('click', e => {
                    if (e.target.tagName === 'INPUT') return;
                    const inp = document.getElementById(`smt_assign_${lot.smelting_batch_id}`);
                    if (inp && !inp.disabled) inp.focus();
                });
                tbody.appendChild(tr);
            });
        }

        function onSmtAssignInput(smtId, maxQty) {
            const input = document.getElementById(`smt_assign_${smtId}`);
            if (!input) return;
            let val = parseFloat(input.value);
            if (isNaN(val) || val < 0) { val = 0; input.value = ''; }
            if (val > maxQty) { val = parseFloat(maxQty.toFixed(3)); input.value = val.toFixed(3); input.style.borderColor = '#d97706'; }
            else { input.style.borderColor = val > 0 ? 'var(--g)' : ''; }
            input.closest('tr')?.classList.toggle('selected', val > 0);
            recalcSmtTotal();
        }

        function recalcSmtTotal() {
            let total = 0;
            document.querySelectorAll('#smtLotTbody .assign-input').forEach(inp => {
                const v = parseFloat(inp.value); if (!isNaN(v) && v > 0) total += v;
            });
            document.getElementById('smtTotalAssign').textContent = total > 0 ? total.toFixed(3) : '0.000';
            document.getElementById('smtConfirmBtn').disabled = total <= 0;
        }

        function confirmSmtSelection() {
            const { type, rowIndex } = smtModal;
            if (!type || !rowIndex) return;
            const prefix = type === 'raw' ? 'rm' : 'ch';
            const qtyFieldId = type === 'raw' ? `rm_qty_${rowIndex}` : `ch_qty_${rowIndex}`;

            const selections = [];
            document.querySelectorAll('#smtLotTbody tr').forEach(tr => {
                const smtId = tr.dataset.smtId;
                const smtNo = tr.dataset.smtNo;
                const inp = document.getElementById(`smt_assign_${smtId}`);
                const qty = parseFloat(inp?.value);
                if (!isNaN(qty) && qty > 0) selections.push({ smtId, smtNo, qty });
            });
            if (!selections.length) return;

            const totalQty = selections.reduce((s, r) => s + r.qty, 0);
            const ids = selections.map(r => r.smtId).join(',');
            const nos = selections.map(r => r.smtNo).join(',');

            document.getElementById(`${prefix}_smt_id_${rowIndex}`).value = ids;
            document.getElementById(`${prefix}_smt_no_${rowIndex}`).value = nos;

            const tr = document.getElementById(type === 'raw' ? `rrow-${rowIndex}` : `crow-${rowIndex}`);
            if (tr) tr.dataset.smtSelections = JSON.stringify(selections);

            const qtyInput = document.getElementById(qtyFieldId);
            if (qtyInput) {
                qtyInput.value = totalQty.toFixed(3);
                qtyInput.title = 'SMT: ' + selections.map(r => `${r.smtNo} (${r.qty} KG)`).join(', ');
                qtyInput.style.borderColor = 'var(--g)';
                if (type === 'raw') recalcRawTotals();
                else recalcChemTotals();
            }

            triggerAutosave();
            document.getElementById('smtLotModal').classList.remove('open');
            document.body.style.overflow = '';
            smtModal = { type: null, rowIndex: null };
        }

        function clearSmtOnRow(type, i) {
            const prefix = type === 'raw' ? 'rm' : 'ch';
            const idEl = document.getElementById(`${prefix}_smt_id_${i}`);
            const noEl = document.getElementById(`${prefix}_smt_no_${i}`);
            if (idEl) idEl.value = '';
            if (noEl) noEl.value = '';
            const qtyEl = document.getElementById(type === 'raw' ? `rm_qty_${i}` : `ch_qty_${i}`);
            if (qtyEl) { qtyEl.title = 'Click to assign from smelting batch'; qtyEl.style.borderColor = ''; }
            const tr = document.getElementById(type === 'raw' ? `rrow-${i}` : `crow-${i}`);
            if (tr) tr.dataset.smtSelections = '';
            triggerAutosave();
        }

        // ════════════════════════════════════════════════════════════════
        // PROCESS TABLE
        // ════════════════════════════════════════════════════════════════
        function addProcessRow(data = {}) {
            procRowCount++;
            const i = procRowCount;
            const tbody = document.getElementById('procBody');
            const tr = document.createElement('tr');
            tr.id = `prow-${i}`;
            tr.dataset.rowIndex = i;
            tr.innerHTML = `
                <td style="position:relative;min-width:160px">
                  <div class="sdd" id="sdd_proc_name_${i}">
                    <div class="sdd-trigger" onclick="toggleSdd('proc_name_'+${i})">
                      <span class="sdd-trigger-text placeholder" id="sdd_proc_name_${i}_label" data-placeholder="Select process…">Select process…</span>
                      <svg class="sdd-clear" onclick="clearSdd('proc_name_'+${i},event)" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      <svg class="sdd-trigger-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <input type="hidden" id="proc_name_${i}" onchange="triggerAutosave()">
                  </div>
                </td>
                <td><button class="proc-btn proc-start" onclick="setProcTime(${i},'start')">START</button></td>
                <td style="padding:4px 4px"><input type="time" class="ri" id="proc_start_${i}"
                  value="${data.start_time ? data.start_time.slice(11, 16) : ''}"
                  oninput="calcProcTime(${i});triggerAutosave()" style="min-width:90px"></td>
                <td><button class="proc-btn proc-end" onclick="setProcTime(${i},'end')">END</button></td>
                <td style="padding:4px 4px"><input type="time" class="ri" id="proc_end_${i}"
                  value="${data.end_time ? data.end_time.slice(11, 16) : ''}"
                  oninput="calcProcTime(${i});triggerAutosave()" style="min-width:90px"></td>
                <td><input type="text" class="ri ro" id="proc_total_${i}" readonly placeholder="0 min"
                  style="min-width:70px;font-weight:700;color:var(--g);background:var(--gxl)"></td>
                <td><button class="del-btn" onclick="removeRow('prow-${i}',calcTotalProcessTime)" title="Remove">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button></td>`;
            animateIn(tr);
            tbody.appendChild(tr);
            initProcessSdd(`proc_name_${i}`, data.refining_process ?? '');
            if (data.start_time && data.end_time) calcProcTime(i);
        }

        function setProcTime(i, which) {
            const now = new Date();
            const t = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            document.getElementById(`proc_${which}_${i}`).value = t;
            calcProcTime(i); triggerAutosave();
        }

        function calcProcTime(i) {
            const s = document.getElementById(`proc_start_${i}`)?.value;
            const e = document.getElementById(`proc_end_${i}`)?.value;
            const el = document.getElementById(`proc_total_${i}`);

            if (s && e) {
                const [sh, sm] = s.split(':').map(Number);
                const [eh, em] = e.split(':').map(Number);

                let mins = (eh * 60 + em) - (sh * 60 + sm);
                if (mins < 0) mins += 1440; // handle midnight crossing

                const hours = Math.floor(mins / 60);
                const remainingMins = mins % 60;

                let display = '';
                if (hours > 0) display += hours + ' hr ';
                if (remainingMins > 0) display += remainingMins + ' min';
                if (display === '') display = '0 min';

                el.value = display.trim();
                el.dataset.mins = mins;   // keep minutes for backend
                el.dataset.hours = (mins / 60).toFixed(2); // optional decimal hours
            } else {
                el.value = '';
                el.dataset.mins = 0;
                el.dataset.hours = 0;
            }

            calcTotalProcessTime();
        }

        function calcTotalProcessTime() {
            let totalMins = 0;

            document.querySelectorAll('#procBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                totalMins += parseInt(
                    document.getElementById(`proc_total_${i}`)?.dataset.mins ?? 0
                );
            });

            const el = document.getElementById('totalProcessTime');

            if (el && totalMins > 0) {
                const hours = Math.floor(totalMins / 60);
                const mins = totalMins % 60;

                let display = '';
                if (hours > 0) display += hours + ' hr ';
                if (mins > 0) display += mins + ' min';

                el.value = display.trim();
            } else if (el) {
                el.value = '';
            }
        }

        // ════════════════════════════════════════════════════════════════
        // FINISHED GOODS TABLE
        // ════════════════════════════════════════════════════════════════
        function addFGRow(data = {}) {
            fgRowCount++;
            const i = fgRowCount;
            const tbody = document.getElementById('fgBody');
            const tr = document.createElement('tr');
            tr.id = `fgrow-${i}`;
            tr.dataset.rowIndex = i;
            tr.dataset.outputBlocks = data.output_blocks ? JSON.stringify(data.output_blocks) : '';
            tr.innerHTML = `
                <td style="text-align:center;font-size:12px;font-weight:700;color:var(--g);padding:8px 4px">${i}</td>
                <td style="position:relative;min-width:160px">
                  <div class="sdd" id="sdd_fg_id_${i}">
                    <div class="sdd-trigger" onclick="toggleSdd('fg_id_'+${i})">
                      <span class="sdd-trigger-text placeholder" id="sdd_fg_id_${i}_label" data-placeholder="Select material…">Select material…</span>
                      <svg class="sdd-clear" onclick="clearSdd('fg_id_'+${i},event)" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      <svg class="sdd-trigger-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <input type="hidden" id="fg_id_${i}" onchange="onFGMaterialChange(${i});triggerAutosave()">
                  </div>
                </td>
                <td>
                  <input type="number" class="ri" id="fg_qty_${i}"
                    value="${data.total_qty ?? ''}" step="0.001" placeholder="Click to enter blocks…"
                    onclick="openOutputModal('fg', ${i})" readonly
                    style="min-width:90px;cursor:pointer;background:var(--gxl)" title="Click to enter block weights">
                </td>
                <td><button class="del-btn" onclick="removeRow('fgrow-${i}',recalcFGTotals)" title="Remove">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button></td>`;
            animateIn(tr);
            tbody.appendChild(tr);
            initMaterialSdd(`fg_id_${i}`, data.material_id ?? null);
        }

        function onFGMaterialChange(i) {
            document.getElementById(`fg_qty_${i}`).value = '';
            const tr = document.getElementById(`fgrow-${i}`);
            if (tr) tr.dataset.outputBlocks = '';
            recalcFGTotals(); triggerAutosave();
        }

        function recalcFGTotals() {
            let total = 0;
            document.querySelectorAll('#fgBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const v = parseFloat(document.getElementById(`fg_qty_${i}`)?.value);
                if (!isNaN(v)) total += v;
            });
            document.getElementById('fgTotalQty').value = total > 0 ? total.toFixed(3) : '';
        }

        // ════════════════════════════════════════════════════════════════
        // DROSS TABLE
        // ════════════════════════════════════════════════════════════════
        function addDrossRow(data = {}) {
            drossRowCount++;
            const i = drossRowCount;
            const tbody = document.getElementById('drossBody');
            const tr = document.createElement('tr');
            tr.id = `drow-${i}`;
            tr.dataset.rowIndex = i;
            tr.dataset.outputBlocks = data.output_blocks ? JSON.stringify(data.output_blocks) : '';
            tr.innerHTML = `
                <td style="text-align:center;font-size:12px;font-weight:700;color:var(--g);padding:8px 4px">${i}</td>
                <td style="position:relative;min-width:160px">
                  <div class="sdd" id="sdd_dr_id_${i}">
                    <div class="sdd-trigger" onclick="toggleSdd('dr_id_'+${i})">
                      <span class="sdd-trigger-text placeholder" id="sdd_dr_id_${i}_label" data-placeholder="Select material…">Select material…</span>
                      <svg class="sdd-clear" onclick="clearSdd('dr_id_'+${i},event)" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      <svg class="sdd-trigger-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <input type="hidden" id="dr_id_${i}" onchange="onDrossMaterialChange(${i});triggerAutosave()">
                  </div>
                </td>
                <td>
                  <input type="number" class="ri" id="dr_qty_${i}"
                    value="${data.total_qty ?? ''}" step="0.001" placeholder="Click to enter blocks…"
                    onclick="openOutputModal('dross', ${i})" readonly
                    style="min-width:90px;cursor:pointer;background:var(--gxl)" title="Click to enter block weights">
                </td>
                <td><button class="del-btn" onclick="removeRow('drow-${i}',recalcDrossTotals)" title="Remove">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button></td>`;
            animateIn(tr);
            tbody.appendChild(tr);
            initMaterialSdd(`dr_id_${i}`, data.material_id ?? null);
        }

        function onDrossMaterialChange(i) {
            document.getElementById(`dr_qty_${i}`).value = '';
            const tr = document.getElementById(`drow-${i}`);
            if (tr) tr.dataset.outputBlocks = '';
            recalcDrossTotals(); triggerAutosave();
        }

        function recalcDrossTotals() {
            let total = 0;
            document.querySelectorAll('#drossBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const v = parseFloat(document.getElementById(`dr_qty_${i}`)?.value);
                if (!isNaN(v)) total += v;
            });
            document.getElementById('drossTotalQty').value = total > 0 ? total.toFixed(3) : '';
        }

        // ════════════════════════════════════════════════════════════════
        // OUTPUT QTY WINDOW MODAL (shared for FG + Dross)
        // ════════════════════════════════════════════════════════════════
        function openOutputModal(type, rowIndex) {
            const matId = type === 'fg'
                ? document.getElementById(`fg_id_${rowIndex}`)?.value
                : document.getElementById(`dr_id_${rowIndex}`)?.value;

            if (!matId) { showAlert('Please select a material first.', 'error'); return; }

            const fgKey = type === 'fg' ? `fg_id_${rowIndex}` : `dr_id_${rowIndex}`;
            const matName = sddRegistry[fgKey]?.selected?.label ?? '';

            outputModal.type = type;
            outputModal.rowIndex = rowIndex;

            // Load existing blocks from tr dataset
            const tr = document.getElementById(type === 'fg' ? `fgrow-${rowIndex}` : `drow-${rowIndex}`);
            outputModal.blocks = tr?.dataset.outputBlocks
                ? JSON.parse(tr.dataset.outputBlocks)
                : [];

            document.getElementById('outputModalTitle').textContent = type === 'fg' ? 'Finished Goods QTY Window' : 'Dross QTY Window';
            document.getElementById('outputModalSubtitle').textContent = matName ? `Material: ${matName}` : 'Enter block weights';

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
            const count = Math.max(OUTPUT_MAX_ROWS, outputModal.blocks.length);
            for (let i = 0; i < count; i++) {
                const qty = outputModal.blocks[i]?.qty ?? '';
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
                      oninput="onOutputQtyInput(this,${i})"
                      onfocus="this.style.borderColor='var(--g)'"
                      onblur="this.style.borderColor='var(--bdr)'">
                  </td>`;
                tbody.appendChild(tr);
            }
            recalcOutputTotal();
        }

        function addOutputRow() {
            syncOutputBlocks();
            outputModal.blocks.push({ qty: '' });
            renderOutputRows();
            const inputs = document.querySelectorAll('#outputBlockTbody input');
            if (inputs.length) inputs[inputs.length - 1].focus();
        }

        function syncOutputBlocks() {
            document.querySelectorAll('#outputBlockTbody input').forEach((inp, idx) => {
                if (!outputModal.blocks[idx]) outputModal.blocks[idx] = {};
                outputModal.blocks[idx].qty = inp.value;
            });
        }

        function onOutputQtyInput(inp, rowIdx) {
            if (!outputModal.blocks[rowIdx]) outputModal.blocks[rowIdx] = {};
            outputModal.blocks[rowIdx].qty = inp.value;
            recalcOutputTotal();
        }

        function recalcOutputTotal() {
            let total = 0;
            document.querySelectorAll('#outputBlockTbody input').forEach(inp => {
                const v = parseFloat(inp.value); if (!isNaN(v) && v > 0) total += v;
            });
            document.getElementById('outputBlockTotal').textContent = total > 0 ? total.toFixed(3) : '0.000';
        }

        function confirmOutputQty() {
            syncOutputBlocks();
            const { type, rowIndex } = outputModal;
            const total = outputModal.blocks.reduce((s, r) => {
                const v = parseFloat(r.qty); return s + (isNaN(v) || v <= 0 ? 0 : v);
            }, 0);

            const qtyEl = document.getElementById(type === 'fg' ? `fg_qty_${rowIndex}` : `dr_qty_${rowIndex}`);
            if (qtyEl) qtyEl.value = total > 0 ? total.toFixed(3) : '';

            // Persist blocks to row dataset
            const tr = document.getElementById(type === 'fg' ? `fgrow-${rowIndex}` : `drow-${rowIndex}`);
            if (tr) tr.dataset.outputBlocks = JSON.stringify(outputModal.blocks);

            if (type === 'fg') recalcFGTotals(); else recalcDrossTotals();
            triggerAutosave();
            document.getElementById('outputQtyModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        // ════════════════════════════════════════════════════════════════
        // CONSUMPTION CALCULATORS
        // ════════════════════════════════════════════════════════════════
        function calcConsumption(type) {
            const initial = parseFloat(document.getElementById(`${type}_initial`)?.value);
            const final_ = parseFloat(document.getElementById(`${type}_final`)?.value);
            const display = document.getElementById(`${type}_consumption_display`);
            const hidden = document.getElementById(`${type}_consumption`);
            if (!isNaN(initial) && !isNaN(final_) && final_ >= initial) {
                const diff = (final_ - initial).toFixed(3);
                if (display) display.textContent = diff + (type === 'lpg' ? ' m³' : ' kWh');
                if (hidden) hidden.value = diff;
            } else {
                if (display) display.textContent = '—';
                if (hidden) hidden.value = '';
            }
        }

        // Liquid Oxygen auto-calculations
        // Flow KG  = Flow NM³ × 1.429
        // Consumption KG = Flow Time (hr) × Flow KG
        function calcOxygen() {
            const nm3 = parseFloat(document.getElementById('oxygen_flow_nm3')?.value);
            const time = parseFloat(document.getElementById('oxygen_flow_time')?.value);

            const kgEl = document.getElementById('oxygen_flow_kg');
            const consEl = document.getElementById('oxygen_consumption');

            // Auto: Flow KG = NM3 × 1.429
            let flowKg = NaN;
            if (!isNaN(nm3) && nm3 >= 0) {
                flowKg = nm3 * 1.429;
                if (kgEl) kgEl.value = flowKg.toFixed(3);
            } else {
                if (kgEl) kgEl.value = '';
            }

            // Auto: Consumption = Flow Time × Flow KG
            if (!isNaN(time) && time >= 0 && !isNaN(flowKg)) {
                const cons = time * flowKg;
                if (consEl) consEl.value = cons.toFixed(3);
            } else {
                if (consEl) consEl.value = '';
            }
        }

        // ════════════════════════════════════════════════════════════════
        // BUILD PAYLOAD
        // ════════════════════════════════════════════════════════════════
        function buildPayload() {
            // Raw materials
            const raw_materials = [];
            document.querySelectorAll('#rawBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const id = document.getElementById(`rm_id_${i}`)?.value;
                if (!id) return;
                const smtSels = tr.dataset.smtSelections ? JSON.parse(tr.dataset.smtSelections) : null;
                raw_materials.push({
                    raw_material_id: id,
                    qty: document.getElementById(`rm_qty_${i}`)?.value || 0,
                    smelting_batch_id: (document.getElementById(`rm_smt_id_${i}`)?.value || '').split(',')[0] || null,
                    smelting_batch_no: (document.getElementById(`rm_smt_no_${i}`)?.value || '').split(',')[0] || null,
                    smelting_selections: smtSels,
                });
            });

            // Chemicals
            const chemicals = [];
            document.querySelectorAll('#chemBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const id = document.getElementById(`ch_id_${i}`)?.value;
                if (!id) return;
                const smtSels = tr.dataset.smtSelections ? JSON.parse(tr.dataset.smtSelections) : null;
                chemicals.push({
                    chemical_id: id,
                    qty: document.getElementById(`ch_qty_${i}`)?.value || 0,
                    smelting_batch_id: (document.getElementById(`ch_smt_id_${i}`)?.value || '').split(',')[0] || null,
                    smelting_batch_no: (document.getElementById(`ch_smt_no_${i}`)?.value || '').split(',')[0] || null,
                    smelting_selections: smtSels,
                });
            });

            // Process details
            const process_details = [];
            document.querySelectorAll('#procBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const name = document.getElementById(`proc_name_${i}`)?.value;
                if (!name) return;
                const dateVal = document.getElementById('date').value;
                const st = document.getElementById(`proc_start_${i}`)?.value;
                const et = document.getElementById(`proc_end_${i}`)?.value;
                process_details.push({
                    refining_process: name,
                    start_time: st ? dateVal + 'T' + st + ':00' : null,
                    end_time: et ? dateVal + 'T' + et + ':00' : null,
                });
            });

            // Finished goods blocks + summary
            const finished_goods_blocks = [];
            const finished_goods_summary = [];
            document.querySelectorAll('#fgBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const matId = document.getElementById(`fg_id_${i}`)?.value;
                if (!matId) return;
                const totalQty = parseFloat(document.getElementById(`fg_qty_${i}`)?.value) || 0;
                const blocks = tr.dataset.outputBlocks ? JSON.parse(tr.dataset.outputBlocks) : [];
                blocks.forEach((b, idx) => {
                    const w = parseFloat(b.qty);
                    if (!isNaN(w) && w > 0) {
                        finished_goods_blocks.push({ material_id: matId, block_sl_no: idx + 1, block_weight: w });
                    }
                });
                if (totalQty > 0) finished_goods_summary.push({ material_id: matId, total_qty: totalQty });
            });

            // Dross blocks + summary
            const dross_blocks = [];
            const dross_summary = [];
            document.querySelectorAll('#drossBody tr').forEach(tr => {
                const i = tr.dataset.rowIndex;
                const matId = document.getElementById(`dr_id_${i}`)?.value;
                if (!matId) return;
                const totalQty = parseFloat(document.getElementById(`dr_qty_${i}`)?.value) || 0;
                const blocks = tr.dataset.outputBlocks ? JSON.parse(tr.dataset.outputBlocks) : [];
                blocks.forEach((b, idx) => {
                    const w = parseFloat(b.qty);
                    if (!isNaN(w) && w > 0) {
                        dross_blocks.push({ material_id: matId, block_sl_no: idx + 1, block_weight: w });
                    }
                });
                if (totalQty > 0) dross_summary.push({ material_id: matId, total_qty: totalQty });
            });

            return {
                batch_no: document.getElementById('batch_no').value,
                pot_no: document.getElementById('pot_no').value || null,
                material_id: document.getElementById('material_id').value || null,
                date: document.getElementById('date').value,
                lpg_initial: document.getElementById('lpg_initial').value || null,
                lpg_final: document.getElementById('lpg_final').value || null,
                lpg_consumption: document.getElementById('lpg_consumption').value || null,
                lpg2_initial: document.getElementById('lpg2_initial').value || null,
                lpg2_final: document.getElementById('lpg2_final').value || null,
                lpg2_consumption: document.getElementById('lpg2_consumption').value || null,
                electricity_initial: document.getElementById('electricity_initial').value || null,
                electricity_final: document.getElementById('electricity_final').value || null,
                electricity_consumption: document.getElementById('electricity_consumption').value || null,
                oxygen_flow_nm3: document.getElementById('oxygen_flow_nm3').value || null,
                oxygen_flow_kg: document.getElementById('oxygen_flow_kg').value || null,
                oxygen_flow_time: document.getElementById('oxygen_flow_time').value || null,
                oxygen_consumption: document.getElementById('oxygen_consumption').value || null,
                total_process_time: document.getElementById('totalProcessTime').dataset?.totalMins || null,
                raw_materials,
                chemicals,
                process_details,
                finished_goods_blocks,
                finished_goods_summary,
                dross_blocks,
                dross_summary,
            };
        }

        // ════════════════════════════════════════════════════════════════
        // SAVE / SUBMIT / AUTOSAVE
        // ════════════════════════════════════════════════════════════════
        async function saveForm(silent = false) {
            const payload = buildPayload();
            const btn = document.getElementById('btnSave');
            if (!silent) btn.disabled = true;

            const method = isCreate ? 'POST' : 'PUT';
            const endpoint = isCreate ? '/refining' : `/refining/${recordId}`;
            const res = await apiFetch(endpoint, { method, body: JSON.stringify(payload) });
            if (!silent) btn.disabled = false;
            if (!res) return;

            const data = await res.json();
            if (res.ok && data.status === 'ok') {
                if (!silent) {
                    if (isCreate) window.location.href = `{{ url('/admin/mes/refining') }}/${data.data.id}/edit`;
                    else showAlert('Saved successfully.', 'success');
                } else {
                    setDot('saved', 'Autosaved at ' + new Date().toLocaleTimeString());
                    setTimeout(() => document.getElementById('autosaveStatus').style.display = 'none', 4000);
                }
            } else {
                if (!silent) showAlert(data.message ?? 'Something went wrong.');
            }
        }

        async function submitBatch() {
            if (!confirm('Submit this batch? It will be locked from further edits.')) return;
            await saveForm(true);
            const res = await apiFetch(`/refining/${recordId}/submit`, { method: 'POST', body: '{}' });
            if (res?.ok) {
                showAlert('Batch submitted and locked.', 'success');
                setTimeout(() => window.location.href = '{{ route("admin.mes.refining.index") }}', 1400);
            } else {
                const d = await res?.json();
                showAlert(d?.message ?? 'Submit failed.');
            }
        }

        function setupAutosave() {
            const watchFields = ['pot_no', 'material_id', 'date',
                'lpg_initial', 'lpg_final', 'lpg2_initial', 'lpg2_final', 'electricity_initial', 'electricity_final',
                'oxygen_flow_nm3', 'oxygen_flow_kg', 'oxygen_flow_time', 'oxygen_consumption'];
            watchFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', triggerAutosave);
            });
        }

        function triggerAutosave() {
            if (isCreate || isSubmitted) return;
            setDot('saving', 'Saving…');
            document.getElementById('autosaveStatus').style.display = 'inline';
            // Also show on input card header dot
            const inputStatus = document.getElementById('inputAutosaveStatus');
            if (inputStatus) {
                inputStatus.style.display = 'inline-flex';
                document.getElementById('inputAsDot').className = 'as-dot saving';
                document.getElementById('inputAsText').textContent = 'Saving…';
            }
            clearTimeout(autosaveTimer);
            // autosaveTimer = setTimeout(() => saveForm(true), 2200);
        }

        function setDot(state, text) {
            document.getElementById('asDot').className = `as-dot ${state}`;
            const txt = document.getElementById('asText');
            if (txt) txt.textContent = text;
            // Sync input card dot
            const inputDot = document.getElementById('inputAsDot');
            const inputText = document.getElementById('inputAsText');
            const inputStatus = document.getElementById('inputAutosaveStatus');
            if (inputDot) inputDot.className = `as-dot ${state}`;
            if (inputText) inputText.textContent = text;
            if (inputStatus) {
                if (state === 'saved') {
                    setTimeout(() => { inputStatus.style.display = 'none'; }, 4000);
                }
            }
        }

        // ════════════════════════════════════════════════════════════════
        // UTILITY
        // ════════════════════════════════════════════════════════════════
        function removeRow(id, recalcFn) {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.transition = 'opacity .18s'; el.style.opacity = '0';
            setTimeout(() => { el.remove(); if (recalcFn) recalcFn(); triggerAutosave(); }, 190);
        }
        function animateIn(el) {
            el.style.opacity = '0'; el.style.transform = 'translateY(-5px)';
            requestAnimationFrame(() => {
                el.style.transition = 'opacity .22s,transform .22s';
                el.style.opacity = '1'; el.style.transform = 'translateY(0)';
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
                if (ro) el.setAttribute('disabled', true);
                else el.removeAttribute('disabled');
            });
            document.querySelectorAll('.sdd-trigger,.sdd-search').forEach(el => {
                if (ro) { el.style.pointerEvents = 'none'; el.style.opacity = '.6'; }
                else { el.style.pointerEvents = ''; el.style.opacity = ''; }
            });
            ['btnSave', 'btnSubmit', 'btnAddRaw', 'btnAddChem', 'btnAddProcess', 'btnAddFG', 'btnAddDross'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = ro ? 'none' : '';
            });
            if (ro) document.getElementById('readonlyNotice').style.display = 'block';
            document.querySelectorAll('.del-btn,.proc-btn').forEach(b => b.style.display = ro ? 'none' : '');
        }
    </script>
@endpush
