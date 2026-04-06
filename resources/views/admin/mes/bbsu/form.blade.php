@extends('admin.layouts.app')

@section('title', isset($bbsu_id) ? 'Edit BBSU Log' : 'Create BBSU Log')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <a href="{{ route('admin.mes.bbsu.index') }}" style="color:var(--text-muted);text-decoration:none;">Battery Breaking
        &amp; Separation Unit</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong id="breadcrumbTitle">{{ isset($bbsu_id) ? 'Edit Record' : 'Create Record' }}</strong>
@endsection

@push('styles')
    <style>
        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 9px;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn svg {
            width: 15px;
            height: 15px;
            stroke: currentColor;
            flex-shrink: 0;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .btn-primary {
            background: var(--green);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--green-dark);
            box-shadow: 0 4px 14px rgba(26, 122, 58, 0.28);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: var(--white);
            color: var(--text-mid);
            border: 1.5px solid var(--border);
        }

        .btn-outline:hover {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-xlight);
        }

        /* ── NEW: Submit button style ── */
        .btn-submit {
            background: #1d4ed8;
            color: #fff;
        }

        .btn-submit:hover {
            background: #1e40af;
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.28);
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 8px 15px;
            font-size: 13px;
        }

        .btn-add {
            background: var(--green);
            color: #fff;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-add:hover {
            background: var(--green-dark);
            transform: translateY(-1px);
        }

        .btn-add svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* ── Status pill ── */
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .pill-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .pill-submitted {
            background: #d1fae5;
            color: #065f46;
        }

        .pill-new {
            background: #e0e7ff;
            color: #3730a3;
        }

        /* ── Locked banner ── */
        .locked-banner {
            display: none;
            background: #fef3c7;
            border: 1.5px solid #fde68a;
            border-radius: 10px;
            padding: 12px 18px;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            font-size: 13px;
            color: #92400e;
            font-weight: 600;
        }

        .locked-banner.show {
            display: flex;
        }

        .locked-banner svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            flex-shrink: 0;
        }

        /* ── Page header ── */
        .form-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .form-page-header h2 {
            font-size: clamp(18px, 2.5vw, 23px);
            font-weight: 800;
            color: var(--text);
            margin-bottom: 3px;
            letter-spacing: -0.3px;
        }

        .form-page-header p {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* ── Cards ── */
        .form-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }

        .form-section-head {
            padding: 13px 22px;
            background: var(--green-light);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-head svg {
            width: 15px;
            height: 15px;
            stroke: var(--green);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }

        .form-section-head span {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--green);
        }

        .form-section-body {
            padding: 26px 22px 30px;
        }

        /* ── Grids ── */
        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px 26px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 26px;
        }

        /* ── Fields ── */
        .field {
            display: flex;
            flex-direction: column;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-mid);
            margin-bottom: 7px;
        }

        .field label .req {
            color: var(--error, #dc2626);
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .ico {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 10px 13px 10px 38px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            background: var(--green-xlight);
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            color: var(--text);
            outline: none;
            appearance: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .no-icon {
            padding-left: 13px !important;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(26, 122, 58, 0.08);
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--text-muted);
        }

        input[readonly] {
            background: #f0f4f2;
            color: var(--text-muted);
            cursor: default;
        }

        .select-wrap::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid var(--text-muted);
            pointer-events: none;
        }

        .error-msg {
            margin-top: 5px;
            font-size: 11.5px;
            color: var(--error, #dc2626);
        }

        /* ── Two-column layout ── */
        .main-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }

        /* ── Input Rows Table ── */
        .input-rows-table {
            width: 100%;
            border-collapse: collapse;
        }

        .input-rows-table thead th {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--green);
            background: var(--green-light);
            padding: 10px 12px;
            border-bottom: 2px solid var(--border);
            text-align: left;
        }

        .input-rows-table thead th:first-child {
            border-radius: 8px 0 0 0;
            width: 54px;
            text-align: center;
        }

        .input-rows-table thead th:last-child {
            border-radius: 0 8px 0 0;
        }

        .input-rows-table tbody tr td {
            padding: 7px 8px;
            border-bottom: 1px solid #edf2ef;
            vertical-align: middle;
        }

        .input-rows-table tbody tr:last-child td {
            border-bottom: none;
        }

        .input-rows-table tbody tr:hover td {
            background: #f7fbf8;
        }

        .sr-cell {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--green);
            width: 44px;
        }

        .row-input {
            width: 100%;
            padding: 8px 11px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            background: var(--green-xlight);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .row-input:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, 0.08);
        }

        .qty-btn {
            width: 100%;
            padding: 8px 11px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            background: var(--green-xlight);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            color: var(--text);
            outline: none;
            cursor: pointer;
            text-align: left;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .qty-btn:hover {
            border-color: var(--green);
            background: var(--white);
        }

        .qty-btn.filled {
            border-color: var(--green);
            background: #d1fae5;
        }

        .qty-btn svg {
            width: 12px;
            height: 12px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .row-select {
            width: 100%;
            padding: 8px 30px 8px 11px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            background: var(--green-xlight);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            color: var(--text);
            outline: none;
            appearance: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .row-select:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, 0.08);
        }

        .select-cell {
            position: relative;
        }

        .select-cell::after {
            content: '';
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid var(--text-muted);
            pointer-events: none;
        }

        .delete-btn {
            width: 28px;
            height: 28px;
            background: #fee2e2;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin: auto;
        }

        .delete-btn:hover {
            background: #fca5a5;
        }

        .delete-btn svg {
            width: 13px;
            height: 13px;
            stroke: #dc2626;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .totals-row td {
            background: var(--green-light);
            font-weight: 700;
            font-size: 13px;
            color: var(--green);
            padding: 9px 12px;
        }

        .add-row-wrap {
            padding: 14px 0 0;
            display: flex;
            justify-content: flex-end;
        }

        /* ── Output Materials Table ── */
        .output-table {
            width: 100%;
            border-collapse: collapse;
        }

        .output-table thead th {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--green);
            background: var(--green-light);
            padding: 10px 12px;
            border-bottom: 2px solid var(--border);
            text-align: left;
        }

        .output-table thead th:first-child {
            width: 40%;
            border-radius: 8px 0 0 0;
        }

        .output-table thead th:last-child {
            border-radius: 0 8px 0 0;
        }

        .output-table tbody tr td {
            padding: 7px 10px;
            border-bottom: 1px solid #edf2ef;
            font-size: 13px;
            color: var(--text-mid);
            vertical-align: middle;
        }

        .output-table tbody tr:hover td {
            background: #f7fbf8;
        }

        .output-table tbody tr:last-child td {
            border-bottom: none;
        }

        .output-table tbody tr.total-row td {
            background: var(--green-light);
            font-weight: 700;
            color: var(--green);
            font-size: 13px;
        }

        .mat-name {
            font-weight: 600;
            color: var(--text);
            font-size: 13px;
        }

        .out-input {
            width: 100%;
            padding: 7px 10px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            background: var(--green-xlight);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .out-input:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, 0.08);
        }

        /* ── Power Consumption ── */
        .power-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px 22px;
        }

        /* ── Sticky footer ── */
        .form-actions {
            position: sticky;
            bottom: 0;
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 15px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            z-index: 10;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
        }

        /* ── Alert ── */
        .form-alert {
            display: none;
            padding: 11px 16px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .form-alert.error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            display: block;
        }

        .form-alert.success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            display: block;
        }

        /* ── QTY Popup Modal ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-box {
            background: var(--white);
            border-radius: 14px;
            width: 100%;
            max-width: 820px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
            animation: modalIn 0.22s ease-out;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(8px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-head {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--green-light);
        }

        .modal-head h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--green);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-head h3 svg {
            width: 16px;
            height: 16px;
            stroke: var(--green);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .modal-close:hover {
            background: #d1e8da;
        }

        .modal-close svg {
            width: 16px;
            height: 16px;
            stroke: var(--green);
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
        }

        .modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 14px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: var(--white);
        }

        .popup-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .popup-table thead th {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--green);
            background: var(--green-light);
            padding: 11px 14px;
            border-bottom: 2px solid var(--border);
            text-align: left;
            white-space: nowrap;
        }

        .popup-table tbody td {
            padding: 10px 14px;
            border-bottom: 1px solid #edf2ef;
            font-size: 13px;
            color: var(--text);
            vertical-align: middle;
        }

        .popup-table tbody tr:last-child td {
            border-bottom: none;
        }

        .popup-table tbody tr:hover td {
            background: #f7fbf8;
        }

        .assign-input {
            width: 100%;
            padding: 8px 10px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            background: var(--green-xlight);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .assign-input:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, 0.08);
        }

        .avail-badge {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .avail-badge.zero {
            background: #fee2e2;
            color: #991b1b;
        }

        .view-btn {
            width: 28px;
            height: 28px;
            background: #e0e7ff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin: auto;
        }

        .view-btn:hover {
            background: #a5b4fc;
        }

        .view-btn svg {
            width: 13px;
            height: 13px;
            stroke: #3730a3;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* ── Lot Detail Modal ── */
        .detail-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .detail-modal-overlay.open {
            display: flex;
        }

        .detail-modal-box {
            background: var(--white);
            border-radius: 14px;
            width: 100%;
            max-width: 560px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
            animation: modalIn 0.22s ease-out;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--green);
            background: var(--green-light);
            padding: 10px 14px;
            border-bottom: 2px solid var(--border);
            text-align: left;
        }

        .detail-table td {
            padding: 9px 14px;
            border-bottom: 1px solid #edf2ef;
            font-size: 13px;
            color: var(--text);
            vertical-align: middle;
        }

        .detail-table tr:last-child td {
            border-bottom: none;
        }

        .detail-table tr:hover td {
            background: #f7fbf8;
        }

        .as-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }

        .as-dot.saving {
            background: #f59e0b;
        }

        .as-dot.saved {
            background: #10b981;
        }

        .as-dot.error {
            background: #ef4444;
        }

        /* ── Responsive ── */
        @media(max-width:900px) {
            .main-cols {
                grid-template-columns: 1fr;
            }

            .form-grid-3 {
                grid-template-columns: 1fr 1fr;
            }

            .power-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media(max-width:560px) {

            .form-grid-2,
            .form-grid-3,
            .power-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .form-actions .btn {
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')

    {{-- Locked banner (shown when submitted) --}}
    <div class="locked-banner" id="lockedBanner">
        <svg viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </svg>
        This batch has been submitted and is locked for editing.
    </div>

    <!-- Page Header -->
    <div class="form-page-header">
        <div>
            <h2 id="pageHeading">Battery Breaking &amp; Separation Unit Log</h2>
            <p>Record input lot details, output materials and power consumption for a BBSU cycle</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <span class="status-pill pill-new" id="statusPill">New Record</span>
            <a href="{{ route('admin.mes.bbsu.index') }}" class="btn btn-outline btn-sm">
                <svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div id="formAlert" class="form-alert"></div>

    <!-- ═══════════════════════════════════════
                         SECTION 1 — Primary Details
                    ════════════════════════════════════════ -->
    <div class="form-card">
        <div class="form-section-head">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <span>Primary Details</span>
        </div>
        <div class="form-section-body">
            <div class="form-grid-3">

                <div class="field">
                    <label for="doc_no">Doc No <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        <input type="text" id="doc_no" style="padding-left:38px;" placeholder="Auto-generated" readonly>
                    </div>
                    <div class="error-msg" id="err_doc_no"></div>
                </div>

                <div class="field">
                    <label for="start_time">Start Time <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <input type="datetime-local" id="start_time" required>
                    </div>
                    <div class="error-msg" id="err_start_time"></div>
                </div>

                <div class="field">
                    <label for="end_time">End Time <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <input type="datetime-local" id="end_time" required>
                    </div>
                    <div class="error-msg" id="err_end_time"></div>
                </div>

                <div class="field">
                    <label for="date">Date <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <input type="date" id="date" required>
                    </div>
                    <div class="error-msg" id="err_date"></div>
                </div>

                <div class="field">
                    <label for="category">Category <span class="req">*</span></label>
                    <div class="input-wrap select-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <select id="category" required>
                            <option value="">Select category...</option>
                            <option value="BBSU">BBSU</option>
                            <option value="MANUAL_CUTTING">Manual Cutting</option>
                        </select>
                    </div>
                    <div class="error-msg" id="err_category"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
                         MAIN TWO-COLUMN AREA
                    ════════════════════════════════════════ -->
    <div class="main-cols">

        <!-- LEFT: Input Lots -->
        <div class="form-card" style="margin-bottom:0;">
            <div class="form-section-head">
                <svg viewBox="0 0 24 24">
                    <polygon points="12 2 2 7 12 12 22 7 12 2" />
                    <polyline points="2 17 12 22 22 17" />
                    <polyline points="2 12 12 17 22 12" />
                </svg>
                <span>Input Lots</span>
            </div>
            <div class="form-section-body" style="padding-bottom:20px;">
                <div style="overflow-x:auto;">
                    <table class="input-rows-table" id="inputRowsTable">
                        <thead>
                            <tr>
                                <th style="text-align:center;">SR</th>
                                <th>Lot No</th>
                                <th>QTY (KG)</th>
                                <th>Acid %</th>
                                <th style="width:36px;text-align:center;">View</th>
                                <th style="width:36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="inputRowsBody"></tbody>
                        <tfoot>
                            <tr class="totals-row">
                                <td colspan="2" style="text-align:right;padding-right:14px;">TOTAL</td>
                                <td><input type="text" id="totalQty" readonly class="out-input" placeholder="0.00"
                                        style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
                                <td><input type="text" id="totalAcid" readonly class="out-input" placeholder="0.00"
                                        style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="add-row-wrap" id="addRowWrap">
                    <button class="btn-add" onclick="addInputRow()">
                        <svg viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Add New
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT: Output Materials -->
        <div class="form-card" style="margin-bottom:0;">
            <div class="form-section-head">
                <svg viewBox="0 0 24 24">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
                <span>Output Materials</span>
            </div>
            <div class="form-section-body" style="padding-bottom:20px;">
                <div style="overflow-x:auto;">
                    <table class="output-table">
                        <thead>
                            <tr>
                                <th>O/P Material</th>
                                <th>QTY (KG)</th>
                                <th>Yield %</th>
                            </tr>
                        </thead>
                        <tbody id="outputTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- /main-cols -->

    <div style="height:20px;"></div>

    <!-- ═══════════════════════════════════════
                         SECTION: BBSU Power Consumption
                    ════════════════════════════════════════ -->
    <div class="form-card">
        <div class="form-section-head">
            <svg viewBox="0 0 24 24">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
            </svg>
            <span>BBSU Power Consumption</span>
        </div>
        <div class="form-section-body">
            <div class="power-grid">

                <div class="field">
                    <label for="power_initial">Initial Reading <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        <input type="number" id="power_initial" step="0.01" placeholder="0.00" oninput="calcConsumption()">
                    </div>
                    <div class="error-msg" id="err_power_initial"></div>
                </div>

                <div class="field">
                    <label for="power_final">Final Reading <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        <input type="number" id="power_final" step="0.01" placeholder="0.00" oninput="calcConsumption()">
                    </div>
                    <div class="error-msg" id="err_power_final"></div>
                </div>

                <div class="field">
                    <label for="power_consumption">Consumption (kWh)</label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                        <input type="number" id="power_consumption" readonly placeholder="Auto-calculated"
                            style="background:#f0f4f2;color:var(--green);font-weight:700;">
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
                         STICKY FOOTER ACTIONS
                    ════════════════════════════════════════ -->
    <div class="form-actions">
        <a href="{{ route('admin.mes.bbsu.index') }}" class="btn btn-outline btn-sm">Cancel</a>
        <div style="display:flex;gap:10px;align-items:center;">
            {{-- Save Draft button — shown ONLY on CREATE page --}}
            <button type="button" class="btn btn-primary btn-sm" id="btnSave" style="display:none;" onclick="saveForm()">
                <svg viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                <span id="btnSaveLabel">Save</span>
            </button>

            {{-- Submit button — shown ONLY on EDIT page (draft status) --}}
            <button type="button" class="btn btn-submit btn-sm" id="btnSubmit" style="display:none;"
                onclick="submitBatch()">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                Submit &amp; Lock
            </button>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
                         QTY POPUP MODAL
                    ════════════════════════════════════════ -->
    <div class="modal-overlay" id="qtyModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3>
                    <svg viewBox="0 0 24 24">
                        <polygon points="12 2 2 7 12 12 22 7 12 2" />
                        <polyline points="2 17 12 22 22 17" />
                        <polyline points="2 12 12 17 22 12" />
                    </svg>
                    Assign Quantity from Lot
                </h3>
                <button class="modal-close" onclick="closeQtyModal()">
                    <svg viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">Enter the quantity to assign from the
                    available lot inventory.</p>
                <div style="overflow-x:auto;">
                    <table class="popup-table">
                        <thead>
                            <tr>
                                <th>Lot No</th>
                                <th>Material Description</th>
                                <th>Acid %</th>
                                <th>Unit</th>
                                <th>Available Qty</th>
                                <th>Assign Qty</th>
                            </tr>
                        </thead>
                        <tbody id="qtyModalBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeQtyModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" onclick="confirmQtyAssign()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Confirm Assignment
                </button>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
                         LOT DETAIL MODAL
                    ════════════════════════════════════════ -->
    <div class="detail-modal-overlay" id="lotDetailModal">
        <div class="detail-modal-box">
            <div class="modal-head">
                <h3>
                    <svg viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    Lot Assignment Details
                </h3>
                <button class="modal-close" onclick="closeLotDetailModal()">
                    <svg viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="lotDetailContent"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeLotDetailModal()">Close</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            // ─── State ────────────────────────────────────────────────────────
            let rowCount = 0;
            // ── ID injected directly from Blade — no URL parsing needed ──
            const BLADE_ITEM_ID = {{ isset($item_id) ? (int) $item_id : 'null' }};
            const isCreate = BLADE_ITEM_ID === null;
            const recordId = BLADE_ITEM_ID;

            let activeRowIndex = null;
            let lotOptions = [];
            let isLocked = false;
            let currentBatchId = BLADE_ITEM_ID;  // always correct — comes from PHP, not URL
            let autosaveTimer = null;

            // 9 fixed BBSU output material codes — order = display order.
            // Codes match material_code in the materials table.
            // Labels & stock codes are fetched from DB at init via loadOutputMaterials().
            const OUTPUT_KEYS = [
                { code: '1007', key: 'metallic' },
                { code: '1008', key: 'paste' },
                { code: '1019', key: 'fines' },
                { code: '1005', key: 'pp_chips' },
                { code: '1023', key: 'abs_chips' },
                { code: '1006', key: 'separator' },
                { code: '1055', key: 'battery_plates' },
                { code: '1057', key: 'terminals' },
                { code: '1267', key: 'acid' },
            ];
            // Populated by loadOutputMaterials() — keyed by material_code
            let outputMaterialInfo = {};

            // ─── Init ─────────────────────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', async () => {
                document.getElementById('date').value = new Date().toISOString().slice(0, 10);

                await loadOutputMaterials();   // fetch names/stock codes from DB first
                buildOutputTable();
                await loadLots();

                if (isCreate) {
                    await generateDocNo();
                    addInputRow();
                    updatePageUI('new', null);
                } else {
                    await loadRecord();
                }
            });

            // ─── Load output material info from materials table via API ────────
            async function loadOutputMaterials() {
                try {
                    const codes = OUTPUT_KEYS.map(m => m.code).join(',');
                    const res = await apiFetch(`/bbsu-batches/output-material-info?codes=${codes}`);
                    if (!res?.ok) return;
                    const json = await res.json();
                    // json.data = [ { material_code, material_name, stock_code, ... }, ... ]
                    (json.data ?? []).forEach(m => {
                        outputMaterialInfo[m.material_code] = m;
                    });
                } catch { /* silently fall back to codes */ }
            }

            function getOutputLabel(code) {
                const info = outputMaterialInfo[code];
                if (!info) return code;
                return info.secondary_name ?? info.material_name ?? code;
            }

            function getOutputStockCode(code) {
                return outputMaterialInfo[code]?.stock_code ?? '';
            }

            // ─── Generate Doc No ─────────────────────────────────────────────
            async function generateDocNo() {
                try {
                    const res = await apiFetch('/bbsu-batches/generate-batch-no');
                    if (res?.ok) {
                        const d = await res.json();
                        document.getElementById('doc_no').value = d.batch_no || fallbackBatchNo();
                    } else {
                        document.getElementById('doc_no').value = fallbackBatchNo();
                    }
                } catch {
                    document.getElementById('doc_no').value = fallbackBatchNo();
                }
            }
            function fallbackBatchNo() {
                return 'BBSU-' + new Date().getFullYear() + '-' + String(Date.now()).slice(-4);
            }

            // ─── Load Lots from API ───────────────────────────────────────────
            async function loadLots() {
                try {
                    // Collect current lot numbers to include them even if available_qty is 0
                    const currentLots = [];
                    document.querySelectorAll('.row-select').forEach(sel => {
                        if (sel.value) currentLots.push(sel.value);
                    });
                    const includeParam = currentLots.length ? `?include=${currentLots.join(',')}` : '';

                    const res = await apiFetch(`/bbsu-batches/acid-test-lot-numbers${includeParam}`);
                    if (!res?.ok) return;
                    const json = await res.json();
                    lotOptions = Array.isArray(json)
                        ? json
                        : (json.data?.data ?? json.data ?? []);
                } catch { lotOptions = []; }
            }

            function buildLotDropdown() {
                const blank = '<option value="">Select lot...</option>';
                if (!lotOptions.length) return blank;
                return blank + lotOptions.map(l =>
                    `<option value="${l.lot_number}">${l.lot_number}</option>`
                ).join('');
            }

            // ─── Input Rows ───────────────────────────────────────────────────
            function addInputRow(data = null) {
                if (isLocked) return;
                rowCount++;
                const idx = rowCount;
                const lot = data?.lot_no || '';
                const qty = data?.quantity || '';
                const acid = data?.acid_percentage || '';
                const breakdown = data?.material_breakdown ? JSON.stringify(data.material_breakdown) : '{}';

                const tbody = document.getElementById('inputRowsBody');
                const tr = document.createElement('tr');
                tr.id = `row-${idx}`;
                tr.dataset.rowIndex = idx;

                tr.innerHTML = `
                            <td class="sr-cell">${tbody.rows.length + 1}</td>
                            <td class="select-cell">
                                <select class="row-select" id="lot_no_${idx}" onchange="onLotChange(${idx})">
                                    ${buildLotDropdown()}
                                </select>
                            </td>
                            <td>
                                <button type="button" class="qty-btn ${qty ? 'filled' : ''}" id="qty_btn_${idx}" onclick="openQtyModal(${idx})">
                                    <span id="qty_display_${idx}" style="color:${qty ? 'var(--text)' : 'var(--text-muted)'};font-size:13px;">
                                        ${qty ? parseFloat(qty).toFixed(2) + ' KG' : 'Click to assign...'}
                                    </span>
                                    <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                                <input type="hidden" id="qty_val_${idx}" value="${qty}">
                                <input type="hidden" id="qty_breakdown_${idx}" value='${breakdown}'>
                                <input type="hidden" id="qty_material_names_${idx}" value='{}'>
                            </td>
                            <td>
                                <input type="text" class="row-input" id="acid_${idx}"
                                       placeholder="0.000" value="${acid}"
                                       oninput="recalcTotals(); triggerAutosave();">
                            </td>
                            <td style="text-align:center;">
                                <button type="button" class="view-btn" onclick="openLotDetailModal(${idx})" title="View assigned materials">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="delete-btn" onclick="removeRow(${idx})" title="Remove">
                                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                            </td>
                        `;
                tbody.appendChild(tr);

                // Set selected lot if data provided
                if (lot) {
                    const sel = document.getElementById(`lot_no_${idx}`);
                    if (sel) sel.value = lot;
                }

                // Animate in
                tr.style.opacity = '0';
                tr.style.transform = 'translateY(-6px)';
                requestAnimationFrame(() => {
                    tr.style.transition = 'opacity 0.25s,transform 0.25s';
                    tr.style.opacity = '1';
                    tr.style.transform = 'translateY(0)';
                });

                recalcTotals();
            }

            function removeRow(idx) {
                const rows = document.querySelectorAll('#inputRowsBody tr');
                if (rows.length <= 1) return;
                const tr = document.getElementById(`row-${idx}`);
                if (!tr) return;
                tr.style.transition = 'opacity 0.2s';
                tr.style.opacity = '0';
                setTimeout(() => { tr.remove(); renumberRows(); recalcTotals(); triggerAutosave(); }, 200);
            }

            function renumberRows() {
                document.querySelectorAll('#inputRowsBody tr').forEach((tr, i) => {
                    const c = tr.querySelector('.sr-cell');
                    if (c) c.textContent = i + 1;
                });
            }

            function onLotChange(idx) {
                // acid % is filled after qty assignment from modal, not on lot select
                recalcTotals();
                triggerAutosave();
            }

            function recalcTotals() {
                let totalQty = 0;
                // Weighted average acid% = Sum(qty × acid%) / Sum(qty)
                let weightedAcidNum = 0, weightedAcidDen = 0;
                document.querySelectorAll('#inputRowsBody tr').forEach(tr => {
                    const i = tr.dataset.rowIndex;
                    const qty = parseFloat(document.getElementById(`qty_val_${i}`)?.value) || 0;
                    const acid = parseFloat(document.getElementById(`acid_${i}`)?.value) || 0;
                    totalQty += qty;
                    if (qty > 0 && acid > 0) {
                        weightedAcidNum += qty * acid;
                        weightedAcidDen += qty;
                    }
                });
                document.getElementById('totalQty').value = totalQty.toFixed(2);
                // Show weighted avg acid% — formula: Sum(Qty*acid%) / Sum(qty)
                document.getElementById('totalAcid').value = weightedAcidDen > 0
                    ? (weightedAcidNum / weightedAcidDen).toFixed(3)
                    : '0.000';
                calcOutputTotal();
            }

            // ─── QTY Modal ────────────────────────────────────────────────────
            async function openQtyModal(rowIdx) {
                if (isLocked) return;
                activeRowIndex = rowIdx;

                const lotNo = document.getElementById(`lot_no_${rowIdx}`)?.value;
                if (!lotNo) { showAlert('Please select a Lot No first.', 'error'); return; }

                // Show loading
                document.getElementById('qtyModalBody').innerHTML = `
                            <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">Loading lot data...</td></tr>`;
                document.getElementById('qtyModal').classList.add('open');

                try {
                    // ── FIX: Call acidSummaryByLot which returns material_description from stockCondition ──
                    const res = await apiFetch(`/bbsu-batches/acid-summary/${encodeURIComponent(lotNo)}`);

                    if (!res?.ok) {
                        document.getElementById('qtyModalBody').innerHTML = `
                                    <tr><td colspan="6" style="text-align:center;padding:24px;color:#dc2626;">
                                        Failed to load data for lot <strong>${lotNo}</strong>.
                                    </td></tr>`;
                        return;
                    }

                    const json = await res.json();
                    const rows = json.data ?? [];

                    if (!rows.length) {
                        document.getElementById('qtyModalBody').innerHTML = `
                                    <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">
                                        No stock data found for lot <strong>${lotNo}</strong>.
                                    </td></tr>`;
                        return;
                    }

                    const existingQty = parseFloat(document.getElementById(`qty_val_${rowIdx}`)?.value) || 0;

                    // ── Render rows — store acid_pct as data attribute for auto-fill ──
                    document.getElementById('qtyModalBody').innerHTML = rows.map((l, i) => {
                        const available = parseFloat(l.available_qty || 0);
                        const isZero = available <= 0;
                        const preVal = (rows.length === 1 && existingQty > 0) ? existingQty.toFixed(3) : '';
                        const description = (l.material_description ?? '—').replace(/'/g, '&#39;');
                        return `<tr>
                                    <td><strong>${l.lot_no ?? lotNo}</strong></td>
                                    <td>${l.material_description ?? '—'}</td>
                                    <td>${parseFloat(l.avg_acid_pct || 0).toFixed(2)}%</td>
                                    <td>${l.unit ?? 'KG'}</td>
                                    <td><span class="avail-badge ${isZero ? 'zero' : ''}">${available.toFixed(3)} ${l.unit ?? 'KG'}</span></td>
                                    <td>
                                        <input type="number" class="assign-input" id="assign_${i}"
                                            placeholder="0.00" step="0.001" min="0"
                                            max="${available}"
                                            data-acid="${parseFloat(l.avg_acid_pct || 0).toFixed(3)}"
                                            data-available="${available}"
                                            data-ulab="${l.ulab_type}"
                                            data-description="${description}"
                                            value="${preVal}"
                                            ${isZero ? 'disabled' : ''}
                                            oninput="capAssign(this)">
                                    </td>
                                </tr>`;
                    }).join('');

                } catch (e) {
                    document.getElementById('qtyModalBody').innerHTML = `
                                <tr><td colspan="6" style="text-align:center;padding:24px;color:#dc2626;">
                                    Network error loading lot data.
                                </td></tr>`;
                }
            }

            function capAssign(input) {
                const max = parseFloat(input.dataset.available) || 0;
                if (parseFloat(input.value) > max) input.value = max;
            }

            // ── FIX: confirmQtyAssign — weighted avg acid% from data-acid attributes ──
            function confirmQtyAssign() {
                if (activeRowIndex === null) return;

                let totalAssigned = 0;
                let weightedAcidNum = 0;
                let weightedAcidDen = 0;
                let breakdownObj = {};
                let materialNamesObj = {}; // { ulab_type: { description, acid_pct, qty } }

                document.querySelectorAll('#qtyModalBody .assign-input').forEach(inp => {
                    const qty = parseFloat(inp.value) || 0;
                    const acid = parseFloat(inp.dataset.acid) || 0;
                    const ulab = inp.dataset.ulab;
                    const description = inp.dataset.description || ulab || '';
                    if (qty > 0) {
                        totalAssigned += qty;
                        weightedAcidNum += qty * acid;
                        weightedAcidDen += qty;
                        if (ulab) {
                            breakdownObj[ulab] = qty;
                            materialNamesObj[ulab] = {
                                description: description,
                                acid_pct: acid,
                                qty: qty,
                            };
                        }
                    }
                });

                // Update hidden qty value
                const qtyVal = document.getElementById(`qty_val_${activeRowIndex}`);
                const qtyDisplay = document.getElementById(`qty_display_${activeRowIndex}`);
                const qtyBtn = document.getElementById(`qty_btn_${activeRowIndex}`);

                if (qtyVal) qtyVal.value = totalAssigned.toFixed(4);
                if (qtyDisplay) {
                    qtyDisplay.textContent = totalAssigned > 0 ? totalAssigned.toFixed(3) + ' KG' : 'Click to assign...';
                    qtyDisplay.style.color = totalAssigned > 0 ? 'var(--text)' : 'var(--text-muted)';
                }
                if (qtyBtn) qtyBtn.classList.toggle('filled', totalAssigned > 0);

                const breakdownEl = document.getElementById(`qty_breakdown_${activeRowIndex}`);
                if (breakdownEl) breakdownEl.value = JSON.stringify(breakdownObj);

                // Store material names/details for view modal
                const materialNamesEl = document.getElementById(`qty_material_names_${activeRowIndex}`);
                if (materialNamesEl) materialNamesEl.value = JSON.stringify(materialNamesObj);

                // ── FIX: Auto-fill Acid % using weighted average from data-acid ──
                const acidField = document.getElementById(`acid_${activeRowIndex}`);
                if (acidField) {
                    const computedAcid = weightedAcidDen > 0
                        ? (weightedAcidNum / weightedAcidDen).toFixed(3)
                        : '0.000';
                    acidField.value = computedAcid;
                }

                recalcTotals();
                closeQtyModal();
                triggerAutosave();
            }

            function closeQtyModal() {
                document.getElementById('qtyModal').classList.remove('open');
                activeRowIndex = null;
            }

            document.getElementById('qtyModal').addEventListener('click', function (e) {
                if (e.target === this) closeQtyModal();
            });

            // ─── Lot Detail Modal (View button) ───────────────────────────────
            function openLotDetailModal(rowIdx) {
                const lotNo = document.getElementById(`lot_no_${rowIdx}`)?.value;
                const qty = parseFloat(document.getElementById(`qty_val_${rowIdx}`)?.value) || 0;
                const acid = document.getElementById(`acid_${rowIdx}`)?.value || '0.000';

                let materialNamesRaw = '{}';
                const mnEl = document.getElementById(`qty_material_names_${rowIdx}`);
                if (mnEl && mnEl.value) materialNamesRaw = mnEl.value;

                let breakdownRaw = '{}';
                const bdEl = document.getElementById(`qty_breakdown_${rowIdx}`);
                if (bdEl && bdEl.value) breakdownRaw = bdEl.value;

                let materialNames = {};
                let breakdown = {};
                try { materialNames = JSON.parse(materialNamesRaw); } catch (e) { }
                try { breakdown = JSON.parse(breakdownRaw); } catch (e) { }

                const container = document.getElementById('lotDetailContent');

                if (!lotNo) {
                    container.innerHTML = `<p style="color:var(--text-muted);font-size:13px;text-align:center;padding:16px 0;">No lot selected for this row.</p>`;
                    document.getElementById('lotDetailModal').classList.add('open');
                    return;
                }

                if (qty <= 0) {
                    container.innerHTML = `
                            <p style="color:var(--text-muted);font-size:13px;text-align:center;padding:16px 0;">
                                No quantity has been assigned yet for lot <strong>${lotNo}</strong>.
                            </p>`;
                    document.getElementById('lotDetailModal').classList.add('open');
                    return;
                }

                // Build detail rows
                const ulabKeys = Object.keys(breakdown);
                let rows = '';

                if (ulabKeys.length > 0) {
                    ulabKeys.forEach(ulab => {
                        const assignedQty = parseFloat(breakdown[ulab] || 0);
                        if (assignedQty <= 0) return;
                        const info = materialNames[ulab] || {};
                        const desc = info.description || ulab;
                        const acidPct = parseFloat(info.acid_pct || 0);
                        const showAcid = acidPct > 0;
                        rows += `<tr>
                                <td><strong>${lotNo}</strong></td>
                                <td>${desc}</td>
                                <td>${showAcid ? acidPct.toFixed(2) + '%' : '<span style="color:var(--text-muted);">—</span>'}</td>
                                <td><strong style="color:var(--green);">${assignedQty.toFixed(3)} KG</strong></td>
                            </tr>`;
                    });
                } else {
                    // Fallback: no breakdown stored yet — show aggregate row
                    rows = `<tr>
                            <td><strong>${lotNo}</strong></td>
                            <td><span style="color:var(--text-muted);">—</span></td>
                            <td>${parseFloat(acid) > 0 ? parseFloat(acid).toFixed(3) + '%' : '<span style="color:var(--text-muted);">—</span>'}</td>
                            <td><strong style="color:var(--green);">${qty.toFixed(3)} KG</strong></td>
                        </tr>`;
                }

                container.innerHTML = `
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>Lot No</th>
                                    <th>Material Name</th>
                                    <th>Acid %</th>
                                    <th>Assigned Qty</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                            <tfoot>
                                <tr style="background:var(--green-light);">
                                    <td colspan="3" style="font-weight:700;color:var(--green);font-size:13px;padding:9px 14px;text-align:right;">TOTAL</td>
                                    <td style="font-weight:700;color:var(--green);font-size:13px;padding:9px 14px;">${qty.toFixed(3)} KG</td>
                                </tr>
                            </tfoot>
                        </table>`;

                document.getElementById('lotDetailModal').classList.add('open');
            }

            function closeLotDetailModal() {
                document.getElementById('lotDetailModal').classList.remove('open');
            }

            document.getElementById('lotDetailModal').addEventListener('click', function (e) {
                if (e.target === this) closeLotDetailModal();
            });

            // ─── Load material names for view modal (edit mode) ───────────────
            async function loadMaterialNamesForRow(rowIdx, lotNo) {
                try {
                    const res = await apiFetch(`/bbsu-batches/acid-summary/${encodeURIComponent(lotNo)}`);
                    if (!res?.ok) return;
                    const json = await res.json();
                    const rows = json.data ?? [];

                    // Build a lookup: ulab_type -> description + acid_pct
                    const lookup = {};
                    rows.forEach(r => {
                        lookup[r.ulab_type] = {
                            description: r.material_description || r.ulab_type,
                            acid_pct: parseFloat(r.avg_acid_pct || 0),
                        };
                    });

                    // Read existing breakdown for qty
                    const bdEl = document.getElementById(`qty_breakdown_${rowIdx}`);
                    let breakdown = {};
                    try { breakdown = JSON.parse(bdEl?.value || '{}'); } catch (e) { }

                    // Build materialNames
                    const materialNames = {};
                    Object.entries(breakdown).forEach(([ulab, qty]) => {
                        const info = lookup[ulab] || { description: ulab, acid_pct: 0 };
                        materialNames[ulab] = {
                            description: info.description,
                            acid_pct: info.acid_pct,
                            qty: parseFloat(qty),
                        };
                    });

                    const mnEl = document.getElementById(`qty_material_names_${rowIdx}`);
                    if (mnEl) mnEl.value = JSON.stringify(materialNames);
                } catch (e) { /* silent — view modal will show aggregate fallback */ }
            }

            // ─── Output Table ─────────────────────────────────────────────────
            function buildOutputTable() {
                const tbody = document.getElementById('outputTableBody');
                tbody.innerHTML = OUTPUT_KEYS.map(mat => `
                            <tr>
                                <td class="mat-name">${getOutputLabel(mat.code)}</td>
                                <td><input type="number" class="out-input" id="out_qty_${mat.key}"
                                           placeholder="0.00" step="0.0001" oninput="calcOutputTotal(); triggerAutosave();"></td>
                                <td><input type="number" class="out-input" id="out_yield_${mat.key}"
                                           placeholder="0.00" readonly
                                           style="background:#eef6f1;color:var(--green);font-weight:600;cursor:default;"></td>
                            </tr>
                        `).join('') + `
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="text" class="out-input" id="outputTotalQty" readonly placeholder="0.00"
                                           style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
                                <td><input type="text" class="out-input" id="outputTotalYield" readonly placeholder="0.00%"
                                           style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
                            </tr>
                        `;
            }

            function calcOutputTotal() {
                let total = 0;
                OUTPUT_KEYS.forEach(mat => {
                    total += parseFloat(document.getElementById(`out_qty_${mat.key}`)?.value) || 0;
                });
                document.getElementById('outputTotalQty').value = total.toFixed(2);

                // ── FIX: compute inputTotal directly from hidden qty_val fields ──
                // Do NOT rely on the totalQty display field which may be stale.
                let inputTotal = 0;
                document.querySelectorAll('#inputRowsBody tr').forEach(tr => {
                    const i = tr.dataset.rowIndex;
                    inputTotal += parseFloat(document.getElementById(`qty_val_${i}`)?.value) || 0;
                });
                // Also sync the totalQty display field
                const totalQtyEl = document.getElementById('totalQty');
                if (totalQtyEl && inputTotal > 0) totalQtyEl.value = inputTotal.toFixed(2);

                // Per-row yield % = (row_qty / total_input) * 100 — auto-calc, readonly
                OUTPUT_KEYS.forEach(mat => {
                    const rowQty = parseFloat(document.getElementById(`out_qty_${mat.key}`)?.value) || 0;
                    const yieldEl = document.getElementById(`out_yield_${mat.key}`);
                    if (yieldEl) {
                        yieldEl.value = inputTotal > 0
                            ? ((rowQty / inputTotal) * 100).toFixed(2)
                            : '';
                    }
                });

                // Total yield % = total_output / total_input * 100
                document.getElementById('outputTotalYield').value = inputTotal
                    ? ((total / inputTotal) * 100).toFixed(2) + '%'
                    : '0.00%';
            }

            // ─── Power Consumption ────────────────────────────────────────────
            function calcConsumption() {
                const initial = parseFloat(document.getElementById('power_initial').value) || 0;
                const final_ = parseFloat(document.getElementById('power_final').value) || 0;
                document.getElementById('power_consumption').value =
                    final_ >= initial ? (final_ - initial).toFixed(2) : '';
                triggerAutosave();
            }

            // ─── Collect Payload ─────────────────────────────────────────────
            function buildPayload() {
                // Input details
                const input_details = [];
                document.querySelectorAll('#inputRowsBody tr').forEach(tr => {
                    const idx = tr.dataset.rowIndex;
                    const lot = document.getElementById(`lot_no_${idx}`)?.value;
                    if (!lot) return;
                    input_details.push({
                        lot_no: lot,
                        quantity: parseFloat(document.getElementById(`qty_val_${idx}`)?.value) || 0,
                        acid_percentage: parseFloat(document.getElementById(`acid_${idx}`)?.value) || 0,
                        material_breakdown: (document.getElementById(`qty_breakdown_${idx}`)?.value) ? JSON.parse(document.getElementById(`qty_breakdown_${idx}`).value) : null,
                    });
                });

                // Output material — keyed by material_code, e.g. { "1007": { "qty": 120.5 }, ... }
                const output_material = {};
                OUTPUT_KEYS.forEach(mat => {
                    output_material[mat.code] = {
                        qty: parseFloat(document.getElementById(`out_qty_${mat.key}`)?.value) || 0,
                    };
                });

                // Power
                const initial = parseFloat(document.getElementById('power_initial').value) || 0;
                const final_ = parseFloat(document.getElementById('power_final').value) || 0;

                return {
                    batch_no: document.getElementById('doc_no').value,
                    doc_date: document.getElementById('date').value,
                    category: document.getElementById('category').value,
                    start_time: document.getElementById('start_time').value,
                    end_time: document.getElementById('end_time').value,
                    input_details,
                    output_material,
                    power_consumption: {
                        initial_power: initial,
                        final_power: final_,
                        total_power_consumption: final_ >= initial ? parseFloat((final_ - initial).toFixed(2)) : 0,
                    },
                };
            }

            // ─── Validate Mandatory Fields ────────────────────────────────────
            function validateMandatory(forSubmit = false) {
                const errors = [];
                clearFieldErrors();

                const batchNo = document.getElementById('doc_no').value?.trim();
                const date = document.getElementById('date').value;
                const category = document.getElementById('category').value;
                const start = document.getElementById('start_time').value;
                const end = document.getElementById('end_time').value;

                if (!batchNo) errors.push('Doc No is required.');
                if (!date) { errors.push('Date is required.'); document.getElementById('err_date').textContent = 'Required'; }
                if (!category) { errors.push('Category is required.'); document.getElementById('err_category').textContent = 'Required'; }
                if (!start) { errors.push('Start Time is required.'); document.getElementById('err_start_time').textContent = 'Required'; }
                if (!end) { errors.push('End Time is required.'); document.getElementById('err_end_time').textContent = 'Required'; }

                // Input lots
                let hasValidLot = false;
                document.querySelectorAll('#inputRowsBody tr').forEach((tr, i) => {
                    const idx = tr.dataset.rowIndex;
                    const lot = document.getElementById(`lot_no_${idx}`)?.value;
                    const qty = parseFloat(document.getElementById(`qty_val_${idx}`)?.value) || 0;
                    if (lot && qty > 0) hasValidLot = true;
                    if (lot && qty <= 0) errors.push(`Input row ${i + 1}: Quantity must be assigned.`);
                });
                // Replaced output material validations: All output fields are entirely optional

                if (forSubmit) {
                    // Extra checks for submit
                    const init = parseFloat(document.getElementById('power_initial').value);
                    const final = parseFloat(document.getElementById('power_final').value);
                    if (!init || init <= 0) { errors.push('Initial power reading is required.'); document.getElementById('err_power_initial').textContent = 'Required'; }
                    if (!final || final <= 0) { errors.push('Final power reading is required.'); document.getElementById('err_power_final').textContent = 'Required'; }
                }

                return errors;
            }

            // ─── Save Draft ───────────────────────────────────────────────────
            async function saveForm(silent = false) {
                if (!silent) {
                    clearAlert();
                    clearFieldErrors();
                }

                const btn = document.getElementById('btnSave');
                if (!silent) { btn.disabled = true; document.getElementById('btnSaveLabel').textContent = 'Saving...'; }

                const method = isCreate ? 'POST' : 'PUT';
                const endpoint = isCreate ? '/bbsu-batches' : `/bbsu-batches/${currentBatchId}`;

                try {
                    const res = await apiFetch(endpoint, { method, body: JSON.stringify(buildPayload()) });
                    const data = await res.json();

                    if (res.ok) {
                        // After create → redirect to INDEX LIST (not edit page)
                        if (isCreate) {
                            window.location.href = `{{ route('admin.mes.bbsu.index') }}`;
                            return;
                        }
                        currentBatchId = data.data?.id || currentBatchId;
                        if (!silent) {
                            showAlert('Record saved as draft successfully.', 'success');
                            updatePageUI('draft', data.data);
                        } else {
                            setDot('saved');
                            document.getElementById('asText').textContent = 'Saved ' + new Date().toLocaleTimeString();
                        }
                    } else if (res.status === 422) {
                        showFieldErrors(data.errors ?? {});
                        if (!silent) showAlert(data.message ?? 'Please fix the errors below.', 'error');
                    } else {
                        if (!silent) showAlert(data.message ?? 'Something went wrong.', 'error');
                    }
                } catch (e) {
                    if (!silent) showAlert('Network error: ' + e.message, 'error');
                    else setDot('error');
                }

                if (!silent) { btn.disabled = false; document.getElementById('btnSaveLabel').textContent = 'Save Draft'; }
            }

            // ─── Submit Batch ─────────────────────────────────────────────────
            async function submitBatch() {
                // Guard — must have a saved batch ID
                if (!currentBatchId) {
                    showAlert('No batch ID found. Please save the draft first.', 'error');
                    return;
                }

                // Validate mandatory fields before submitting
                const errors = validateMandatory(true);
                if (errors.length) {
                    showAlert('Please fix the following before submitting:\n• ' + errors.join('\n• '), 'error');
                    return;
                }

                if (!confirm('Submit and lock this batch? This cannot be undone.')) return;

                const btn = document.getElementById('btnSubmit');
                btn.disabled = true;
                btn.innerHTML = `<svg style="animation:spin .8s linear infinite;width:14px;height:14px;stroke:currentColor;fill:none;" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.22-8.56"/></svg> Submitting...`;

                try {
                    // ONLY call PATCH /status — no data re-save, no unique validation triggered
                    const url = `/bbsu-batches/${currentBatchId}/status`;
                    console.log('[BBSU] Submitting batch ID:', currentBatchId, 'URL:', url);

                    const res = await apiFetch(url, {
                        method: 'PATCH',
                        body: JSON.stringify({ status: 1 }),
                    });

                    if (!res) {
                        showAlert('No response from server. Check your connection.', 'error');
                        btn.disabled = false;
                        btn.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Submit &amp; Lock`;
                        return;
                    }

                    const d = await res.json();
                    console.log('[BBSU] Status update response:', res.status, d);

                    if (res.ok && d.status === 'ok') {
                        showAlert('Batch submitted successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route('admin.mes.bbsu.index') }}';
                        }, 1200);
                    } else {
                        showAlert(d.message || 'Failed to submit batch.', 'error');
                        btn.disabled = false;
                        btn.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Submit &amp; Lock`;
                    }
                } catch (e) {
                    showAlert('Network error: ' + e.message, 'error');
                    btn.disabled = false;
                    btn.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Submit &amp; Lock`;
                }
            }

            // ─── Autosave (edit/draft only) — silent background save ─────────
            function triggerAutosave() {
                if (isCreate || isLocked || !currentBatchId) return;
                clearTimeout(autosaveTimer);
                // autosaveTimer = setTimeout(() => saveForm(true), 2500);
            }

            function setDot(state) {
                // No-op if autosave indicator not present — kept for silent save path
            }

            // ─── Load Existing Record (edit mode) ────────────────────────────
            async function loadRecord() {
                try {
                    const res = await apiFetch(`/bbsu-batches/${recordId}`);
                    if (!res?.ok) { showAlert('Failed to load record.', 'error'); return; }
                    const { data } = await res.json();

                    // Primary fields
                    document.getElementById('doc_no').value = data.batch_no ?? '';
                    document.getElementById('date').value = data.doc_date?.slice(0, 10) ?? '';
                    document.getElementById('category').value = data.category ?? '';
                    document.getElementById('start_time').value = formatForDatetimeLocal(data.start_time);
                    document.getElementById('end_time').value = formatForDatetimeLocal(data.end_time);

                    // Input lots
                    document.getElementById('inputRowsBody').innerHTML = '';
                    rowCount = 0;
                    if (data.input_details?.length) {
                        data.input_details.forEach(detail => addInputRow(detail));
                        // Populate material names for view modal from API (for edit mode)
                        data.input_details.forEach((detail, i) => {
                            const rowIdx = i + 1; // rowCount increments from 1 in addInputRow
                            if (detail.lot_no && detail.material_breakdown) {
                                loadMaterialNamesForRow(rowIdx, detail.lot_no);
                            }
                        });
                    } else {
                        addInputRow();
                    }
                    recalcTotals();

                    // Output materials — new structure: array of { material_code, qty, yield_pct }
                    if (data.output_materials?.length) {
                        // Build a lookup: code -> row
                        const omByCode = {};
                        data.output_materials.forEach(row => { omByCode[row.material_code] = row; });
                        OUTPUT_KEYS.forEach(mat => {
                            const row = omByCode[mat.code];
                            const qEl = document.getElementById(`out_qty_${mat.key}`);
                            const yEl = document.getElementById(`out_yield_${mat.key}`);
                            if (qEl) qEl.value = row ? (parseFloat(row.qty) || '') : '';
                            if (yEl) yEl.value = row ? (parseFloat(row.yield_pct) || '') : '';
                        });
                        calcOutputTotal();
                    }

                    // Power consumption
                    if (data.power_consumption) {
                        document.getElementById('power_initial').value = parseFloat(data.power_consumption.initial_power) || '';
                        document.getElementById('power_final').value = parseFloat(data.power_consumption.final_power) || '';
                        document.getElementById('power_consumption').value = parseFloat(data.power_consumption.total_power_consumption) || '';
                    }

                    currentBatchId = data.id;
                    updatePageUI(data.status, data);

                } catch (e) {
                    showAlert('Error loading record: ' + e.message, 'error');
                }
            }

            // ─── Update Page UI based on status ──────────────────────────────
            function updatePageUI(status, data) {
                // Normalise: DB stores 0=draft, 1=submitted; create state = 'new'
                const isDraft = (status === 'draft' || status === 0 || status === '0');
                const isSubmitted = (status === 'submitted' || status === 1 || status === '1');
                const isNew = (status === 'new' || status === 'create');

                isLocked = isSubmitted;

                // Status pill
                const pill = document.getElementById('statusPill');
                if (isNew) { pill.textContent = 'New Record'; pill.className = 'status-pill pill-new'; }
                else if (isDraft) { pill.textContent = 'Draft'; pill.className = 'status-pill pill-draft'; }
                else { pill.textContent = 'Submitted'; pill.className = 'status-pill pill-submitted'; }

                // Locked banner
                if (isLocked) document.getElementById('lockedBanner').classList.add('show');

                // ── Button visibility rules ──────────────────────────────────
                // CREATE page  → Show "Save Draft" only
                // EDIT page    → Show "Submit & Lock" and "Save Draft"
                // SUBMITTED    → No buttons
                document.getElementById('btnSave').style.display = (isNew || isDraft) ? '' : 'none';
                document.getElementById('btnSubmit').style.display = isDraft ? '' : 'none';

                // Add row button / lock inputs when submitted
                if (isLocked) {
                    const aw = document.getElementById('addRowWrap');
                    if (aw) aw.style.display = 'none';
                    // Hide delete buttons when locked
                    document.querySelectorAll('.delete-btn').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('input, select, button.qty-btn, button.btn-add, button.delete-btn').forEach(el => {
                        el.setAttribute('disabled', true);
                    });
                }

                // Breadcrumb + heading
                document.getElementById('breadcrumbTitle').textContent = isNew ? 'Create Record' : (isLocked ? 'View Record' : 'Edit Record');
                document.getElementById('pageHeading').textContent = isLocked ? 'BBSU Batch (Submitted)' : 'Battery Breaking & Separation Unit Log';
            }

            // ─── Helpers ──────────────────────────────────────────────────────
            function showAlert(msg, type = 'error') {
                const el = document.getElementById('formAlert');
                el.className = `form-alert ${type}`;
                el.textContent = msg;
                el.style.display = 'block';
                if (type === 'success') setTimeout(() => el.style.display = 'none', 3500);
                el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            function clearAlert() {
                const el = document.getElementById('formAlert');
                el.className = 'form-alert';
                el.textContent = '';
                el.style.display = 'none';
            }

            function clearFieldErrors() {
                document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');
            }

            function showFieldErrors(errors) {
                Object.entries(errors).forEach(([field, messages]) => {
                    const errEl = document.getElementById('err_' + field);
                    if (errEl) errEl.textContent = Array.isArray(messages) ? messages[0] : messages;
                });
            }

            function formatForDatetimeLocal(isoString) {
                if (!isoString) return '';
                const d = new Date(isoString);
                const pad = n => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            }

            // ── Spin keyframe ──
            const styleEl = document.createElement('style');
            styleEl.textContent = '@keyframes spin{to{transform:rotate(360deg)}}';
            document.head.appendChild(styleEl);

            // ── Expose to window for inline onclick ──
            window.addInputRow = addInputRow;
            window.removeRow = removeRow;
            window.openQtyModal = openQtyModal;
            window.closeQtyModal = closeQtyModal;
            window.confirmQtyAssign = confirmQtyAssign;
            window.capAssign = capAssign;
            window.onLotChange = onLotChange;
            window.calcConsumption = calcConsumption;
            window.saveForm = saveForm;
            window.submitBatch = submitBatch;
            window.calcOutputTotal = calcOutputTotal;
            window.recalcTotals = recalcTotals;
            window.triggerAutosave = triggerAutosave;
            window.openLotDetailModal = openLotDetailModal;
            window.closeLotDetailModal = closeLotDetailModal;

        })();
    </script>
@endpush