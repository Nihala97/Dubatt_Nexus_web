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

        .main-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }

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

        /* ── Readonly notice ── */
        .readonly-notice {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 16px;
            display: none;
        }

        /* ── Status badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 11px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
        }

        .badge-draft {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-submitted {
            background: #d1fae5;
            color: #065f46;
        }

        /* ── Autosave dot ── */
        .as-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .as-dot.saving {
            background: #d97706;
            animation: pulse .8s infinite;
        }

        .as-dot.saved {
            background: var(--green);
        }

        .as-dot.error {
            background: #dc2626;
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

        /* ── Modal ── */
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

    <!-- Page Header -->
    <div class="form-page-header">
        <div>
            <h2 id="pageTitle">Loading…</h2>
            <p id="pageSubtitle">Record input lot details, output materials and power consumption for a BBSU cycle</p>
            <div id="statusBadge" style="margin-top:6px"></div>
        </div>
        <div style="display:flex;gap:10px;" id="headerActions">
            <a href="{{ route('admin.mes.bbsu.index') }}" class="btn btn-outline btn-sm">
                <svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div id="readonlyNotice" class="readonly-notice">🔒 This batch has been submitted and is locked from editing.</div>
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
                        <input type="text" id="doc_no" class="no-icon" style="padding-left:38px;"
                            oninput="triggerAutosave()">
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
                        <input type="datetime-local" id="start_time" required onchange="triggerAutosave()">
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
                        <input type="datetime-local" id="end_time" required onchange="triggerAutosave()">
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
                        <input type="date" id="date" required onchange="triggerAutosave()">
                    </div>
                    <div class="error-msg" id="err_date"></div>
                </div>

                <div class="field">
                    <label for="category">Category <span class="req">*</span></label>
                    <div class="input-wrap select-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <select id="category" required onchange="triggerAutosave()">
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
                                <th>QTY</th>
                                <th>Acid %</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="add-row-wrap">
                    <button class="btn-add" id="btnAddRow" onclick="addInputRow()">
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
                                <th>QTY</th>
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
                        <input type="number" id="power_initial" step="0.01" placeholder="0.00"
                            oninput="calcConsumption();triggerAutosave()">
                    </div>
                    <div class="error-msg" id="err_power_initial"></div>
                </div>

                <div class="field">
                    <label for="power_final">Final Reading <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        <input type="number" id="power_final" step="0.01" placeholder="0.00"
                            oninput="calcConsumption();triggerAutosave()">
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
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <span id="autosaveStatus" style="font-size:12px;color:var(--text-muted);display:none">
                <span class="as-dot" id="asDot"></span>
                <span id="asText">Saving…</span>
            </span>
            <button type="button" class="btn btn-primary btn-sm" id="btnSave" onclick="saveForm()">
                <svg viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span id="btnSaveLabel">Create Record</span>
            </button>
            <button type="button" class="btn btn-outline btn-sm" id="btnSubmit" onclick="submitRecord()"
                style="display:none">
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

@endsection

@push('scripts')
    <script>
        // ─── State ────────────────────────────────────────────────────────
        let rowCount = 0;
        let activeRowIndex = null;
        let lotOptions = [];
        let isSubmitted = false;
        let autosaveTimer;

        const PATH_PARTS = window.location.pathname.split('/').filter(Boolean);
        const isCreate = PATH_PARTS[PATH_PARTS.length - 1] === 'create';
        const recordId = isCreate ? null : PATH_PARTS[PATH_PARTS.length - 2];

        const outputMaterials = [
            'METALLIC', 'PASTE', 'FINES', 'PP CHIPS', 'ABS CHIPS', 'SEPARATOR', 'BATTERY PLATE', 'TERMINALS', 'ACID'
        ];

        // ─── Helpers ──────────────────────────────────────────────────────
        function showAlert(msg, type = 'error') {
            const el = document.getElementById('formAlert');
            el.className = `form-alert ${type}`;
            el.textContent = msg;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (type === 'success') setTimeout(() => { el.className = 'form-alert'; el.textContent = ''; }, 4000);
        }

        function clearAlert() {
            const el = document.getElementById('formAlert');
            el.className = 'form-alert';
            el.textContent = '';
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

        function setDot(state, text) {
            document.getElementById('asDot').className = `as-dot ${state}`;
            const txt = document.getElementById('asText');
            if (txt) txt.textContent = text;
        }

        function setReadonly(ro) {
            document.querySelectorAll('input, select, textarea').forEach(el => {
                if (ro) el.setAttribute('disabled', true);
                else el.removeAttribute('disabled');
            });
            ['btnSave', 'btnSubmit', 'btnAddRow'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = ro ? 'none' : '';
            });
            document.querySelectorAll('.delete-btn').forEach(b => b.style.display = ro ? 'none' : '');
            if (ro) document.getElementById('readonlyNotice').style.display = 'block';
        }

        // ─── Autosave ─────────────────────────────────────────────────────
        function setupAutosave() {
            ['doc_no', 'start_time', 'end_time', 'date', 'category', 'power_initial', 'power_final'].forEach(id => {
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
        function calculateYield() {
            let totalInput = 0;

            // ✅ Sum input quantities
            document.querySelectorAll('.input-qty').forEach(el => {
                totalInput += parseFloat(el.value) || 0;
            });

            let totalOutput = 0;

            // ✅ Loop all output rows
            document.querySelectorAll('.output-row').forEach(row => {
                let qtyInput = row.querySelector('.output-qty');
                let yieldInput = row.querySelector('.output-yield');

                let qty = parseFloat(qtyInput.value) || 0;

                totalOutput += qty;

                let yieldVal = totalInput > 0 ? (qty / totalInput) * 100 : 0;

                // ✅ Set yield %
                yieldInput.value = yieldVal.toFixed(2);
            });

            // ✅ TOTAL YIELD %
            let totalYield = totalInput > 0 ? (totalOutput / totalInput) * 100 : 0;
            document.getElementById('total_yield').value = totalYield.toFixed(2);
        }

        // trigger on change
        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('input-qty') || e.target.id === 'output') {
                calculateYield();
            }
        });

        // ─── Init ─────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', async () => {
            document.getElementById('date').value = new Date().toISOString().slice(0, 10);

            await loadLots();
            buildOutputTable();

            if (isCreate) {
                const res = await apiFetch('/bbsu-batches/generate-batch-no');
                if (res?.ok) {
                    const d = await res.json();
                    document.getElementById('doc_no').value = d.batch_no ?? '';
                } else {
                    document.getElementById('doc_no').value = 'BBSU-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 9000) + 1000);
                }
                document.getElementById('pageTitle').textContent = 'Create BBSU Batch';
                document.getElementById('pageSubtitle').textContent = 'Record new BBSU batch log';
                document.getElementById('breadcrumbTitle').textContent = 'Create Batch';
                document.getElementById('btnSaveLabel').textContent = 'Create Batch';
                addInputRow();
            } else {
                await loadRecord();
                setupAutosave();
            }
        });

        // ─── Load Lots from API ───────────────────────────────────────────
        async function loadLots() {
            const res = await apiFetch('/bbsu-batches/acid-test-lot-numbers');
            if (!res || !res.ok) return;
            const json = await res.json();
            lotOptions = Array.isArray(json) ? json : (json.data?.data ?? json.data ?? []);
        }

        function buildLotOptions() {
            const blank = '<option value="">Select lot...</option>';
            if (!lotOptions.length) return blank;
            return blank + lotOptions.map(l => `<option value="${l.lot_number}">${l.lot_number}</option>`).join('');
        }

        // ─── Input Rows ───────────────────────────────────────────────────
        function addInputRow() {
            rowCount++;
            const tbody = document.getElementById('inputRowsBody');
            const tr = document.createElement('tr');
            tr.id = `row-${rowCount}`;
            tr.dataset.rowIndex = rowCount;
            tr.innerHTML = `
                <td class="sr-cell">${rowCount}</td>
                <td class="select-cell">
                    <select class="row-select" id="lot_no_${rowCount}" onchange="onLotChange(${rowCount});triggerAutosave()">
                        ${buildLotOptions()}
                    </select>
                </td>
                <td>
                    <button type="button" class="qty-btn" id="qty_btn_${rowCount}" onclick="openQtyModal(${rowCount})">
                        <span id="qty_display_${rowCount}" style="color:var(--text-muted);font-size:13px;">Enter qty...</span>
                        <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <input type="hidden" id="qty_val_${rowCount}" value="">
                </td>
                <td>
                    <input type="text" class="row-input" id="acid_${rowCount}" placeholder="0.00" oninput="recalcTotals();triggerAutosave()">
                </td>
                <td>
                    ${rowCount > 1
                    ? `<button class="delete-btn" onclick="removeRow(${rowCount})" title="Remove">
                               <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                           </button>`
                    : '<span></span>'}
                </td>
            `;
            tbody.appendChild(tr);
            tr.style.opacity = '0';
            tr.style.transform = 'translateY(-6px)';
            requestAnimationFrame(() => {
                tr.style.transition = 'opacity 0.25s,transform 0.25s';
                tr.style.opacity = '1';
                tr.style.transform = 'translateY(0)';
            });
        }

        function removeRow(idx) {
            const tr = document.getElementById(`row-${idx}`);
            if (tr) {
                tr.style.transition = 'opacity 0.2s';
                tr.style.opacity = '0';
                setTimeout(() => { tr.remove(); renumberRows(); recalcTotals(); triggerAutosave(); }, 200);
            }
        }

        function renumberRows() {
            document.querySelectorAll('#inputRowsBody tr').forEach((tr, i) => {
                const srCell = tr.querySelector('.sr-cell');
                if (srCell) srCell.textContent = i + 1;
            });
        }

        function onLotChange(idx) {
            const lotNo = document.getElementById(`lot_no_${idx}`)?.value;
            const lot = lotOptions.find(l => l.lot_no === lotNo);
            if (lot) {
                document.getElementById(`acid_${idx}`).value = parseFloat(lot.acid_pct || 0).toFixed(2);
                recalcTotals();
            }
        }

        function recalcTotals() {
            let totalQty = 0, totalAcid = 0, acidCount = 0;
            document.querySelectorAll('#inputRowsBody tr').forEach(tr => {
                const idx = tr.dataset.rowIndex;
                const qty = parseFloat(document.getElementById(`qty_val_${idx}`)?.value) || 0;
                const acid = parseFloat(document.getElementById(`acid_${idx}`)?.value) || 0;
                totalQty += qty;
                if (acid > 0) { totalAcid += acid; acidCount++; }
            });
            document.getElementById('totalQty').value = totalQty.toFixed(2);
            document.getElementById('totalAcid').value = acidCount ? (totalAcid / acidCount).toFixed(2) : '0.00';
            calcOutputTotal();
        }

        // ─── QTY Modal ────────────────────────────────────────────────────
        async function openQtyModal(rowIdx) {
            activeRowIndex = rowIdx;
            const lotNo = document.getElementById(`lot_no_${rowIdx}`)?.value;
            if (!lotNo) { alert('Please select a Lot No first.'); return; }

            document.getElementById('qtyModalBody').innerHTML = `
                <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">Loading lot data...</td></tr>
            `;
            document.getElementById('qtyModal').classList.add('open');

            const res = await apiFetch(`/bbsu-batches/acid-summary/${lotNo}`);
            if (!res?.ok) {
                document.getElementById('qtyModalBody').innerHTML = `
                    <tr><td colspan="6" style="text-align:center;padding:24px;color:#dc2626;">Failed to load data for lot <strong>${lotNo}</strong>.</td></tr>
                `;
                return;
            }

            const json = await res.json();
            const rows = json.data ?? [];

            if (!rows.length) {
                document.getElementById('qtyModalBody').innerHTML = `
                    <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">No data found for lot <strong>${lotNo}</strong>.</td></tr>
                `;
                return;
            }

            const existing = document.getElementById(`qty_val_${rowIdx}`)?.value;
            document.getElementById('qtyModalBody').innerHTML = rows.map((l, i) => `
                <tr>
                    <td><strong>${l.lot_no}</strong></td>
                    <td>${l.material_description ?? '—'}</td>
                    <td>${parseFloat(l.avg_acid_pct || 0).toFixed(2)}%</td>
                    <td>${l.unit ?? 'KG'}</td>
                    <td><span class="avail-badge">${parseFloat(l.net_weight || 0).toFixed(3)} ${l.unit ?? 'KG'}</span></td>
                    <td>
                        <input type="number" class="assign-input" id="assign_${i}"
                            placeholder="0.00" step="0.01" min="0" max="${l.net_weight ?? ''}"
                            data-lot="${l.lot_no}" data-ulab="${l.ulab_type}"
                            value="${rows.length === 1 && existing ? existing : ''}">
                    </td>
                </tr>
            `).join('');
        }

        function closeQtyModal() {
            document.getElementById('qtyModal').classList.remove('open');
            activeRowIndex = null;
        }

        function confirmQtyAssign() {
            if (activeRowIndex === null) return;
            let assigned = 0, acidPct = 0;
            document.querySelectorAll('#qtyModalBody .assign-input').forEach(inp => {
                const qty = parseFloat(inp.value) || 0;
                if (qty > 0) {
                    assigned += qty;
                    const row = inp.closest('tr');
                    const acidCell = row.querySelectorAll('td')[2];
                    const cellText = acidCell?.textContent?.replace('%', '').trim();
                    acidPct = parseFloat(cellText) || 0;
                }
            });

            document.getElementById(`qty_val_${activeRowIndex}`).value = assigned;
            const display = document.getElementById(`qty_display_${activeRowIndex}`);
            if (display) {
                display.textContent = assigned ? assigned.toFixed(2) + ' KG' : 'Enter qty...';
                display.style.color = assigned ? 'var(--text)' : 'var(--text-muted)';
            }
            const acidField = document.getElementById(`acid_${activeRowIndex}`);
            if (acidField) acidField.value = acidPct.toFixed(2);

            recalcTotals();
            triggerAutosave();
            closeQtyModal();
        }

        document.getElementById('qtyModal').addEventListener('click', function (e) {
            if (e.target === this) closeQtyModal();
        });

        // ─── Output Table ─────────────────────────────────────────────────
        function buildOutputTable() {
            const tbody = document.getElementById('outputTableBody');
            tbody.innerHTML = outputMaterials.map(mat => `
                <tr>
                    <td class="mat-name">${mat}</td>
                    <td><input type="number" class="out-input" placeholder="0.00" step="0.01" oninput="calcOutputTotal();triggerAutosave()"></td>
                    <td><input type="number" class="out-input" placeholder="0.00" step="0.01" oninput="triggerAutosave()"></td>
                </tr>
            `).join('') + `
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td><input type="text" class="out-input" id="outputTotalQty" readonly placeholder="0.00" style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
                    <td><input type="text" class="out-input" id="outputTotalYield" readonly placeholder="0.00" style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
                </tr>
            `;
        }

        function calcOutputTotal() {
            let total = 0;
            document.querySelectorAll('#outputTableBody tr:not(.total-row)').forEach(tr => {
                const qtyInput = tr.querySelectorAll('input')[0];
                total += parseFloat(qtyInput?.value) || 0;
            });
            document.getElementById('outputTotalQty').value = total.toFixed(2);
            const inputTotal = parseFloat(document.getElementById('totalQty')?.value) || 0;
            document.getElementById('outputTotalYield').value = inputTotal
                ? ((total / inputTotal) * 100).toFixed(1) + '%'
                : '0.0%';
        }

        // ─── Power Consumption ────────────────────────────────────────────
        function calcConsumption() {
            const initial = parseFloat(document.getElementById('power_initial').value) || 0;
            const final_ = parseFloat(document.getElementById('power_final').value) || 0;
            document.getElementById('power_consumption').value = final_ >= initial ? (final_ - initial).toFixed(2) : '';
        }

        // ─── Build Payload ────────────────────────────────────────────────
        function buildPayload() {
            const batchNo = document.getElementById('doc_no').value?.trim();
            const date = document.getElementById('date').value;
            const category = document.getElementById('category').value;
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            const inputDetails = [];
            document.querySelectorAll('#inputRowsBody tr').forEach((tr, i) => {
                const idx = tr.dataset.rowIndex;
                const lotNo = document.getElementById(`lot_no_${idx}`)?.value?.trim();
                const qty = parseFloat(document.getElementById(`qty_val_${idx}`)?.value) || 0;
                const acid = parseFloat(document.getElementById(`acid_${idx}`)?.value) || 0;
                inputDetails.push({ lot_no: lotNo, quantity: qty, acid_percentage: acid });
            });

            const OUTPUT_KEYS = ['metallic', 'paste', 'fines', 'pp_chips', 'abs_chips', 'separator', 'battery_plates', 'terminals', 'acid'];
            const outputMaterial = {};
            document.querySelectorAll('#outputTableBody tr:not(.total-row)').forEach((tr, i) => {
                const inputs = tr.querySelectorAll('input[type="number"]');
                const key = OUTPUT_KEYS[i];
                outputMaterial[`${key}_qty`] = parseFloat(inputs[0]?.value) || 0;
                outputMaterial[`${key}_yield`] = parseFloat(inputs[1]?.value) || 0;
            });

            const initialPower = parseFloat(document.getElementById('power_initial').value) || 0;
            const finalPower = parseFloat(document.getElementById('power_final').value) || 0;
            const totalPower = parseFloat(document.getElementById('power_consumption').value) || 0;

            return {
                batch_no: batchNo,
                doc_date: date,
                category: category,
                start_time: startTime,
                end_time: endTime,
                input_details: inputDetails,
                output_material: outputMaterial,
                power_consumption: { initial_power: initialPower, final_power: finalPower, total_power_consumption: totalPower },
            };
        }

        // ─── Save ─────────────────────────────────────────────────────────
        async function saveForm(silent = false) {
            const payload = buildPayload();
            const btn = document.getElementById('btnSave');
            if (!silent) btn.disabled = true;

            const method = isCreate ? 'POST' : 'PUT';
            const endpoint = isCreate ? '/bbsu-batches' : `/bbsu-batches/${recordId}`;

            const res = await apiFetch(endpoint, { method, body: JSON.stringify(payload) });
            if (!silent) btn.disabled = false;
            if (!res) return;

            const data = await res.json();

            if (res.ok && data.status === 'ok') {
                if (!silent) {
                    if (isCreate) {
                        window.location.href = `{{ url('/admin/mes/bbsu') }}/${data.data.id}/edit`;
                    } else {
                        showAlert('Record saved successfully.', 'success');
                    }
                } else {
                    setDot('saved', 'Autosaved at ' + new Date().toLocaleTimeString());
                    setTimeout(() => document.getElementById('autosaveStatus').style.display = 'none', 4000);
                }
            } else if (res.status === 422) {
                showFieldErrors(data.errors ?? {});
                if (!silent) showAlert(data.message ?? 'Please fix the errors below.');
            } else {
                if (!silent) showAlert(data.message ?? 'Something went wrong.');
            }
        }

        // ─── Submit & Lock ────────────────────────────────────────────────
        async function submitRecord() {
            if (!confirm('Submit this batch? It will be locked from further edits.')) return;
            await saveForm(true);
            const res = await apiFetch(`/bbsu-batches/${recordId}/submit`, { method: 'POST', body: '{}' });
            if (res?.ok) {
                showAlert('Batch submitted and locked.', 'success');
                // setTimeout(() => window.location.href = '{{ route("admin.mes.bbsu.index") }}', 1400);
            } else {
                const d = await res?.json();
                showAlert(d?.message ?? 'Submit failed.');
            }
        }
        // async function submitRecord() {
        //       if (!confirm('Submit this record? It will be locked from further edits.')) return;
        //       await saveForm(true);
        //       const res = await apiFetch(`/bbsu-batches/${recordId}/status`, {
        //         method: 'PATCH', body: JSON.stringify({ status: 1 }),
        //       });
        //       if (res?.ok) {
        //         showAlert('Submitted successfully.', 'success');
        //         // setTimeout(() => window.location.href = '{{ route("admin.mes.bbsu.index") }}', 1500);
        //       } else {
        //         const d = await res?.json();
        //         showAlert(d?.message ?? 'Submit failed.');
        //       }
        //     }

        // ─── Load Record (edit mode) ──────────────────────────────────────
        async function loadRecord() {
            const res = await apiFetch(`/bbsu-batches/${recordId}`);
            if (!res?.ok) { showAlert('Failed to load record.'); return; }

            const { data } = await res.json();
            isSubmitted = data.status === 1;

            // Primary Details
            document.getElementById('doc_no').value = data.batch_no ?? '';
            document.getElementById('date').value = data.doc_date?.slice(0, 10) ?? '';
            document.getElementById('category').value = data.category ?? '';
            document.getElementById('start_time').value = formatForDatetimeLocal(data.start_time);
            document.getElementById('end_time').value = formatForDatetimeLocal(data.end_time);

            // Input Lots
            document.getElementById('inputRowsBody').innerHTML = '';
            rowCount = 0;
            if (data.input_details?.length) {
                data.input_details.forEach(detail => {
                    addInputRow();
                    const idx = rowCount;
                    document.getElementById(`lot_no_${idx}`).value = detail.lot_no ?? '';
                    document.getElementById(`acid_${idx}`).value = detail.acid_percentage ?? '';
                    const qty = parseFloat(detail.quantity) || 0;
                    document.getElementById(`qty_val_${idx}`).value = qty;
                    const display = document.getElementById(`qty_display_${idx}`);
                    if (display) {
                        display.textContent = qty ? qty.toFixed(2) + ' KG' : 'Enter qty...';
                        display.style.color = qty ? 'var(--text)' : 'var(--text-muted)';
                    }
                });
                recalcTotals();
            } else {
                addInputRow();
            }

            // Output Materials
            const om = data.output_material;
            if (om) {
                const OUTPUT_KEYS = ['metallic', 'paste', 'fines', 'pp_chips', 'abs_chips', 'separator', 'battery_plates', 'terminals', 'acid'];
                document.querySelectorAll('#outputTableBody tr:not(.total-row)').forEach((tr, i) => {
                    const inputs = tr.querySelectorAll('input[type="number"]');
                    const key = OUTPUT_KEYS[i];
                    if (inputs[0]) inputs[0].value = parseFloat(om[`${key}_qty`]) || '';
                    if (inputs[1]) inputs[1].value = parseFloat(om[`${key}_yield`]) || '';
                });
                calcOutputTotal();
            }

            // Power Consumption
            const pc = data.power_consumption;
            if (pc) {
                document.getElementById('power_initial').value = parseFloat(pc.initial_power) || '';
                document.getElementById('power_final').value = parseFloat(pc.final_power) || '';
                document.getElementById('power_consumption').value = parseFloat(pc.total_power_consumption) || '';
            }

            // Page UI
            document.getElementById('pageTitle').textContent = 'Edit BBSU Batch';
            document.getElementById('pageSubtitle').textContent = `Batch: ${data.batch_no}`;
            document.getElementById('breadcrumbTitle').textContent = 'Edit Record';
            document.getElementById('btnSaveLabel').textContent = 'Save Draft';

            const badge = document.getElementById('statusBadge');
            if (isSubmitted) {
                badge.innerHTML = '<span class="badge badge-submitted">● Submitted</span>';
                setReadonly(true);
                document.getElementById('btnSubmit').style.display = 'none';
            } else {
                badge.innerHTML = '<span class="badge badge-draft">● Draft</span>';
                document.getElementById('btnSubmit').style.display = 'inline-flex';
            }
        }

        function formatForDatetimeLocal(isoString) {
            if (!isoString) return '';
            const d = new Date(isoString);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            const hh = String(d.getHours()).padStart(2, '0');
            const min = String(d.getMinutes()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
        }
    </script>
@endpush