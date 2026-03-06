@extends('admin.layouts.app')

@section('title', 'Acid Testing — Create Record')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <a href="{{ route('admin.mes.acidTesting.index') }}" style="color:var(--text-muted);text-decoration:none;">Acid Testing</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong id="breadcrumbTitle">Loading...</strong>
@endsection

@push('styles')
<style>
  :root {
    --green:#1a7a3a; --green-dark:#145f2d; --green-light:#e8f5ed; --green-xlight:#f2faf5;
    --white:#ffffff; --bg:#f4f7f5; --border:#dde8e2; --text:#1e2d26; --text-mid:#3d5449;
    --text-muted:#6b8a78; --error:#dc2626; --shadow-sm:0 2px 8px rgba(0,0,0,0.06);
    --radius:12px;
  }
  .form-page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
  .form-page-header h2 { font-size:clamp(18px,2.5vw,24px); font-weight:700; color:var(--text); margin-bottom:3px; }
  .form-page-header p { font-size:13px; color:var(--text-muted); }
  .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
  .page-header h2 { font-size:clamp(18px,2.5vw,23px); font-weight:800; color:var(--text); margin-bottom:3px; letter-spacing:-0.3px; }
  .page-header p { font-size:13px; color:var(--text-muted); }

  .btn { display:inline-flex; align-items:center; gap:7px; padding:10px 18px; border-radius:9px;
         font-family:'Outfit',sans-serif; font-size:13.5px; font-weight:600; cursor:pointer;
         text-decoration:none; border:none; transition:all 0.2s; white-space:nowrap; }
  .btn svg { width:15px; height:15px; stroke:currentColor; flex-shrink:0; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
  .btn-primary { background:var(--green); color:#fff; }
  .btn-primary:hover { background:var(--green-dark); box-shadow:0 4px 14px rgba(26,122,58,0.28); transform:translateY(-1px); }
  .btn-outline { background:var(--white); color:var(--text-mid); border:1.5px solid var(--border); }
  .btn-outline:hover { border-color:var(--green); color:var(--green); background:var(--green-xlight); }
  .btn-sm { padding:8px 15px; font-size:13px; }
  .btn-add { background:var(--green); color:#fff; padding:9px 16px; border-radius:8px; font-size:13px; font-weight:700; border:none; cursor:pointer; font-family:'Outfit',sans-serif; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s; white-space:nowrap; }
  .btn-add:hover { background:var(--green-dark); transform:translateY(-1px); }
  .btn-add svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }

  .form-card { background:var(--white); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow-sm); margin-bottom:20px; }
  .form-section-head { padding:13px 22px; background:var(--green-light); border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
  .form-section-head svg { width:15px; height:15px; stroke:var(--green); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; flex-shrink:0; }
  .form-section-head span { font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--green); }
  .form-section-body { padding:26px 22px 30px; }

  .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:18px 28px; }

  .field { display:flex; flex-direction:column; }
  .field label { font-size:11px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase; color:var(--text-mid); margin-bottom:7px; }
  .field label .req { color:var(--error); }
  .autofill-tag { display:inline-block; background:#dbeafe; color:#1d4ed8; font-size:10px; font-weight:600; padding:2px 7px; border-radius:10px; text-transform:uppercase; letter-spacing:0.8px; margin-left:6px; }

  .input-wrap { position:relative; }
  .input-wrap .ico { position:absolute; left:12px; top:50%; transform:translateY(-50%); width:14px; height:14px; stroke:var(--text-muted); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; pointer-events:none; }
  input[type="text"], input[type="number"], input[type="date"], select {
    width:100%; padding:10px 13px 10px 38px; border:1.5px solid var(--border); border-radius:9px;
    background:var(--green-xlight); font-family:'Outfit',sans-serif; font-size:13.5px; color:var(--text);
    outline:none; appearance:none; transition:border-color 0.2s,box-shadow 0.2s,background 0.2s;
  }
  input:focus, select:focus { border-color:var(--green); background:var(--white); box-shadow:0 0 0 4px rgba(26,122,58,0.08); }
  input::placeholder { color:var(--text-muted); }
  input[readonly], input.autofilled { background:#eef6f1; color:var(--text-mid); cursor:default; border-color:#c8dfd1; }
  input.autofilled:focus { box-shadow:none; border-color:#c8dfd1; }
  .select-wrap::after { content:''; position:absolute; right:12px; top:50%; transform:translateY(-50%); border-left:5px solid transparent; border-right:5px solid transparent; border-top:5px solid var(--text-muted); pointer-events:none; }
  .error-msg { margin-top:5px; font-size:11.5px; color:var(--error); }

  /* Lot loading spinner */
  .lot-loading { display:none; position:absolute; right:32px; top:50%; transform:translateY(-50%); }
  .lot-loading.active { display:block; }
  .lot-loading svg { width:14px; height:14px; stroke:var(--green); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; animation:spin 0.8s linear infinite; }
  @keyframes spin { to { transform:rotate(360deg); } }

  /* Prefill flash */
  @keyframes flashFill { 0% { background:#d1fae5; } 100% { background:#eef6f1; } }
  .autofilled.flash { animation:flashFill 0.7s ease forwards; }

  /* Pallet table */
  .pallet-table-wrap { overflow-x:auto; }
  .pallet-table { width:100%; border-collapse:collapse; min-width:700px; }
  .pallet-table thead th { font-size:10.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--green); background:var(--green-light); padding:10px 12px; border-bottom:2px solid var(--border); text-align:left; white-space:nowrap; }
  .pallet-table thead th:first-child { text-align:center; width:52px; }
  .pallet-table tbody td { padding:7px 8px; border-bottom:1px solid #edf2ef; vertical-align:top; }
  .pallet-table tbody tr:last-child td { border-bottom:none; }
  .pallet-table tbody tr:hover td { background:#f7fbf8; }
  .pallet-table tfoot td { background:var(--green-light); font-weight:700; font-size:13px; color:var(--green); padding:9px 12px; }

  .sr-cell { text-align:center; font-size:13px; font-weight:700; color:var(--green); padding-top:10px !important; }
  .row-input { width:100%; padding:8px 11px; border:1.5px solid var(--border); border-radius:7px; background:var(--green-xlight); font-family:'Outfit',sans-serif; font-size:13px; color:var(--text); outline:none; transition:border-color 0.2s,background 0.2s; }
  .row-input:focus { border-color:var(--green); background:var(--white); box-shadow:0 0 0 3px rgba(26,122,58,0.08); }
  .row-select { width:100%; padding:8px 28px 8px 11px; border:1.5px solid var(--border); border-radius:7px; background:var(--green-xlight); font-family:'Outfit',sans-serif; font-size:13px; color:var(--text); outline:none; appearance:none; transition:border-color 0.2s,background 0.2s; }
  .row-select:focus { border-color:var(--green); background:var(--white); box-shadow:0 0 0 3px rgba(26,122,58,0.08); }
  .select-cell-wrap { position:relative; }
  .select-cell-wrap::after { content:''; position:absolute; right:10px; top:50%; transform:translateY(-50%); border-left:4px solid transparent; border-right:4px solid transparent; border-top:4px solid var(--text-muted); pointer-events:none; }

  /* Acid sub-fields */
  .acid-fields { margin-top:8px; display:none; grid-template-columns:repeat(4,1fr); gap:6px; }
  .acid-fields.visible { display:grid; }
  .acid-field-label { font-size:9.5px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; color:var(--text-muted); margin-bottom:4px; white-space:nowrap; }

  .delete-btn { width:28px; height:28px; background:#fee2e2; border:none; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s; margin:auto; }
  .delete-btn:hover { background:#fca5a5; }
  .delete-btn svg { width:13px; height:13px; stroke:#dc2626; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }

  .add-row-wrap { padding:14px 0 0; display:flex; justify-content:flex-end; }

  .form-actions { position:sticky; bottom:0; background:var(--white); border-top:1px solid var(--border); padding:15px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; z-index:10; box-shadow:0 -4px 16px rgba(0,0,0,0.06); }

  .form-alert { display:none; padding:11px 16px; border-radius:9px; font-size:13px; font-weight:500; margin-bottom:16px; }
  .form-alert.error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; display:block; }
  .form-alert.success { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; display:block; }

  .status-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap; margin-top:10px; }
  .status-badge.draft     { background:#e0e7ff; color:#3730a3; }
  .status-badge.submitted { background:#d1fae5; color:#065f46; }
  @media(max-width:768px) { .form-grid-2 { grid-template-columns:1fr 1fr; } }
  @media(max-width:520px) {
    .form-grid-2 { grid-template-columns:1fr; }
    .form-actions { flex-direction:column; align-items:stretch; }
    .form-actions .btn { justify-content:center; }
  }
</style>
@endpush

@section('content')

  <!-- Page Header -->
  <div class="form-page-header" id="pageHeader">
    <div>
        <h2 id="pageTitle">Loading...</h2>
        <p id="pageSubtitle"></p>
        <div id="statusBadge"></div>
    </div>
    <div style="display:flex;gap:10px;" id="headerActions">
        <a href="{{ route('admin.mes.acidTesting.index') }}" class="btn btn-outline btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Back to List
        </a>
    </div>
</div>

  <div id="formAlert" class="form-alert"></div>

  {{-- ═══ SECTION 1 — Primary Details ═══ --}}
  <div class="form-card">
    <div class="form-section-head">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span>Primary Details</span>
    </div>
    <div class="form-section-body">
      <div class="form-grid-2">

        {{-- DATE --}}
        <div class="field">
          <label for="date">Date <span class="req">*</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input type="date" id="date" required>
          </div>
        </div>

        {{-- LOT NO --}}
        <div class="field">
          <label for="lot_no">Lot No <span class="req">*</span></label>
          <div class="input-wrap select-wrap">
            <svg class="ico" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            <select id="lot_no" required onchange="onLotChange()">
              <option value="">Loading lots...</option>
            </select>
            <span class="lot-loading" id="lotPrefillSpinner">
              <svg viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            </span>
          </div>
          <div class="error-msg" id="err_lot_no"></div>
        </div>

        {{-- VEHICLE --}}
        <div class="field">
          <label for="vehicle">Vehicle <span class="autofill-tag">Auto-filled</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <input type="text" id="vehicle" class="autofilled" readonly placeholder="Select a lot first...">
          </div>
        </div>

        {{-- SUPPLIER NAME --}}
        <div class="field">
          <label for="supplier_name">Supplier Name <span class="autofill-tag">Auto-filled</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <input type="text" id="supplier_name" class="autofilled" readonly placeholder="Select a lot first...">
            
          </div>
        </div>

        {{-- AVG PALLET WEIGHT --}}
        <div class="field">
          <label for="avg_pallet_weight">Avg Pallet Weight (KG) <span class="req">*</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <input type="number" id="avg_pallet_weight" step="0.01" placeholder="0.00" oninput="calcAvgPalletForeign()">
          </div>
        </div>

        {{-- IN HOUSE WEIGH BRIDGE WEIGHT --}}
        <div class="field">
          <label for="inhouse_weight">In House Weigh Bridge Weight <span class="autofill-tag">Auto-filled</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            <input type="number" id="inhouse_weight" class="autofilled" readonly placeholder="Select a lot first...">
          </div>
        </div>

        {{-- FOREIGN MATERIAL WEIGHT --}}
        <div class="field">
          <label for="foreign_material_weight">Foreign Material Weight (KG) <span class="req">*</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <input type="number" id="foreign_material_weight" step="0.01" placeholder="0.00" oninput="calcAvgPalletForeign()">
          </div>
        </div>

        {{-- AVG PALLET & FOREIGN WEIGHT (auto-calculated) --}}
        <div class="field">
          <label for="avg_pallet_foreign_weight">Average Pallet &amp; Foreign Weight (KG)</label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <input type="number" id="avg_pallet_foreign_weight" readonly placeholder="Auto-calculated" style="background:#eef6f1;color:var(--green);font-weight:700;">
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- ═══ SECTION 2 — Pallet Records ═══ --}}
  <div class="form-card">
    <div class="form-section-head">
      <svg viewBox="0 0 24 24"><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="M16.5 9.4 7.55 4.24"/></svg>
      <span>Pallet Records</span>
    </div>
    <div class="form-section-body" style="padding-bottom:20px;">
      <div class="pallet-table-wrap">
        <table class="pallet-table" id="palletTable">
          <thead>
            <tr>
              <th style="text-align:center;">SR</th>
              <th>Pallet No</th>
              <th>Gross Weight (KG)</th>
              <th>Net Weight (KG)</th>
              <th>Remarks</th>
              <th style="width:36px;"></th>
            </tr>
          </thead>
          <tbody id="palletBody"></tbody>
          <tfoot>
            <tr>
              <td colspan="2" style="text-align:right;padding-right:14px;">TOTAL (KG)</td>
              <td><input type="text" id="totalGross" readonly class="row-input" placeholder="0.00" style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
              <td><input type="text" id="totalNet"   readonly class="row-input" placeholder="0.00" style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
              <td></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="add-row-wrap">
        <button class="btn-add" onclick="addPalletRow()">
          <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add New
        </button>
      </div>
    </div>
  </div>
  {{-- ═══ Hidden fields ═══ --}}
  <input type="hidden" id="supplier" name="supplier">
  <input type="hidden" id="invoice_qty" name="invoice_qty">
  {{-- ═══ STICKY FOOTER ═══ --}}
  <div class="form-actions" id="formActions">
    <a href="{{ route('admin.mes.acidTesting.index') }}" class="btn btn-outline btn-sm">Cancel</a>
    <div style="display:flex;gap:10px;align-items:center;">
      <span id="autosaveStatus" style="font-size:12px;color:var(--text-muted);display:none;"></span>
      <button type="button" class="btn btn-primary btn-sm" id="btnSave" onclick="saveForm()">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span id="btnSaveLabel">Create Record</span>
      </button>
    </div>
  </div>

@endsection

@push('scripts')
<script>
// ── Import DataService for offline support ────────────────────────
// import { DataService } from '/pwa/data-service.js';
// const ds = new DataService('acid_testing');
// let _localId     = null; // tracks IndexedDB local record id

const PATH_PARTS = window.location.pathname.split('/').filter(Boolean);
// URL pattern: /admin/mes/receiving/{id}/edit  OR  /admin/mes/receiving/create
const isCreate   = PATH_PARTS[PATH_PARTS.length - 1] === 'create';
const recordId   = isCreate ? null : PATH_PARTS[PATH_PARTS.length - 2];
let isSubmitted  = false;
let autosaveTimer;
console.log("isCreate", isCreate);
console.log("recordId", recordId);
const REMARKS_OPTIONS = [
    { value: 1000024, text: 'USED GEL BATTERY/ABS'   },
    { value: 1000025, text: 'USED TRACTION BATTERY'   },
    { value: 1000026, text: 'ULAB - MC BATTERY (DRY)' },
    { value: 1000028, text: 'ULAB - INDUSTRIAL'       },
    { value: 5,       text: 'Acid'                    },
];

let currentLotData = null;
let rowCount = 0;

// ── Init ─────────────────────────────────────────────────────────────────────
// document.addEventListener('DOMContentLoaded', () => {
//     document.getElementById('date').value = new Date().toISOString().slice(0, 10);
//     loadLots();
//     addPalletRow();
// });
// ── Init ──────────────────────────────────────────────────────────
async function init() {
    // await loadDropdowns();
    document.getElementById('date').value = new Date().toISOString().slice(0, 10);
    loadLots();
    addPalletRow();
    if (isCreate) {
      
        // Set defaults
        // document.getElementById('receipt_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('pageTitle').textContent       = 'Create Acid Testing';
        document.getElementById('pageSubtitle').textContent    = 'Record new raw material Acid Testing log';
        document.getElementById('breadcrumbTitle').textContent = 'Create Acid Testing';
        document.getElementById('btnSaveLabel').textContent    = 'Create Record';
    } else {
        await loadRecord();
    }

    // Show form, hide skeleton
    // document.getElementById('loadingSkeleton').style.display = 'none';
    // document.getElementById('formContainer').style.display   = 'block';
}

init();

// ── Load existing record for edit ────────────────────────────────
async function loadRecord() {
    const res = await apiFetch(`/acid-testings/${recordId}`);
    if (!res?.ok) {
        showAlert('Failed to load record.');
        return;
    }

    const { data } = await res.json();
    isSubmitted = data.status >= 1;
    console.log("data", data);

    // Fill form fields safely
    document.getElementById('date').value                      = data.test_date?.split('T')[0] ?? '';
    document.getElementById('lot_no').value                    = data.lot_number ?? '';
    document.getElementById('vehicle').value                   = data.vehicle_number ?? '';
    document.getElementById('supplier_name').value            = data.supplier?.supplier_name ?? '';
    document.getElementById('supplier').value              = data.supplier_id ?? data.supplier?.id ?? '';
    document.getElementById('avg_pallet_weight').value        = data.avg_pallet_weight ?? '';
    document.getElementById('inhouse_weight').value           = data.received_qty ?? '';
    document.getElementById('foreign_material_weight').value  = data.foreign_material_weight ?? '';
    document.getElementById('avg_pallet_foreign_weight').value= data.avg_pallet_and_foreign_weight ?? '';

    // Clear existing pallet rows
    document.getElementById('palletBody').innerHTML = '';

    // Load pallet rows if present
    if (Array.isArray(data.details)) {
        data.details.forEach(row => {
            // Create a new row (should assign a proper data-row-index inside)
            addPalletRow();

            // Get the last added row index dynamically
            const rows = document.querySelectorAll('#palletBody tr');
            const lastRow = rows[rows.length - 1];
            const idx = lastRow.dataset.rowIndex;

            // Fill row fields
            document.getElementById(`pallet_no_${idx}`).value      = row.pallet_no ?? '';
            document.getElementById(`gross_${idx}`).value          = row.gross_weight ?? '';
            document.getElementById(`net_${idx}`).value            = row.net_weight ?? '';
            document.getElementById(`acid_initial_${idx}`).value   = row.initial_weight ?? '';
            document.getElementById(`acid_drained_${idx}`).value   = row.drained_weight ?? '';

            // Set REMARKS dropdown value by matching text
            const remarkOption = REMARKS_OPTIONS.find(r => r.text === row.ulab_type);
            if (remarkOption) {
                document.getElementById(`remarks_${idx}`).value = remarkOption.value;
            } else {
                // fallback if not found
                document.getElementById(`remarks_${idx}`).value = '';
            }
        });

        // Recalculate averages
        calcAvgPalletForeign();
    }

    // Update page header
    document.getElementById('pageTitle').textContent    = 'Edit Acid Testing';
    document.getElementById('pageSubtitle').textContent = 'Update acid testing details';
    document.getElementById('breadcrumbTitle').textContent = 'Edit Acid Testing';
    document.getElementById('btnSaveLabel').textContent = 'Save Draft';

    // Status badge and read-only mode
    const statusBadge = document.getElementById('statusBadge');
    if (isSubmitted) {
        statusBadge.innerHTML = '<div class="status-badge submitted">Status: Submitted</div>';
        setReadonly(true);
    } else {
        statusBadge.innerHTML = '<div class="status-badge draft">Status: Draft</div>';

        // Add Submit button dynamically if not submitted
        const actionsDiv = document.getElementById('headerActions');
        const submitBtn  = document.createElement('button');
        submitBtn.className = 'btn btn-outline btn-sm';
        submitBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Submit Record`;
        submitBtn.onclick   = submitRecord;
        actionsDiv.prepend(submitBtn);

        // Setup autosave
        setupAutosave();
    }
}
// ── GET /api/receivings — populate lot dropdown ───────────────────────────────
async function loadLots() {
    const sel = document.getElementById('lot_no');
    sel.innerHTML = '<option value="">Loading lots…</option>';
    sel.disabled  = true;

    const res = await apiFetch('/receivings/approved-lots');

    if (!res || !res.ok) {
        sel.innerHTML = '<option value="">No lots found</option>';
        sel.disabled  = false;
        return;
    }

    const json = await res.json();
    // Handles: plain array, { data: [] }, or paginated { data: { data: [] } }
    const lots = Array.isArray(json)
        ? json
        : (json.data?.data ?? json.data ?? []);

    sel.innerHTML = lots.length
        ? '<option value="">Select a lot…</option>'
        : '<option value="">No lots available</option>';

    lots.forEach(item => {
        const opt = document.createElement('option');
        opt.value       = item.lot_no;
        opt.textContent = item.lot_no;
        sel.appendChild(opt);
    });

    sel.disabled = false;
}

// ── GET /api/receivings/lot/{lotNo} — autofill fields ────────────────────────
async function onLotChange() {
    const lotNo     = document.getElementById('lot_no').value;
    const autoFields = ['vehicle', 'supplier_name', 'inhouse_weight', 'supplier', 'invoice_qty'];

    currentLotData = null;
    autoFields.forEach(f => {
        const el       = document.getElementById(f);
        el.value       = '';
        el.placeholder = lotNo ? 'Loading…' : 'Select a lot first…';
    });

    if (!lotNo) return;

    const spinner = document.getElementById('lotPrefillSpinner');
    spinner.classList.add('active');
    document.getElementById('lot_no').disabled = true;

    const res = await apiFetch(`/receivings/lot/${encodeURIComponent(lotNo)}`);

    if (!res || !res.ok) {
        autoFields.forEach(f => {
            document.getElementById(f).placeholder = 'Failed to load — try again';
        });
        spinner.classList.remove('active');
        document.getElementById('lot_no').disabled = false;
        return;
    }

    const json = await res.json();
    const data = json.data ?? json;
    currentLotData = data;
    console.log("data",data);
    // ⚠️ Adjust keys below to match your actual API response field names
    const fieldMap = {
        vehicle       : data.vehicle_no                  ?? data.vehicle_number  ?? '',
        supplier_name : data.supplier?.supplier_name     ?? data.supplier_name   ?? '',
        inhouse_weight: data.received_qty ?? data.received_qty  ?? '',
        supplier: data.supplier_id ?? data.supplier_id   ?? '',
        invoice_qty: data.invoice_qty ?? data.invoice_qty   ?? '',
    };

    autoFields.forEach(f => {
        const el = document.getElementById(f);
        el.value       = fieldMap[f];
        el.placeholder = '';
        el.classList.remove('flash');
        void el.offsetWidth; // force reflow to restart animation
        el.classList.add('flash');
    });

    calcAvgPalletForeign();

    spinner.classList.remove('active');
    document.getElementById('lot_no').disabled = false;
    console.log("supplier",document.getElementById("supplier").value);
    console.log("invoice_qty",document.getElementById("invoice_qty").value);
}

// ── Calculations ─────────────────────────────────────────────────────────────
function calcAvgPalletForeign() {
    const avg     = parseFloat(document.getElementById('avg_pallet_weight').value)       || 0;
    const foreign = parseFloat(document.getElementById('foreign_material_weight').value) || 0;
    const result  = avg + foreign;
    document.getElementById('avg_pallet_foreign_weight').value = result > 0 ? result.toFixed(2) : '';
    // Re-derive all net weights when this changes
    document.querySelectorAll('#palletBody tr').forEach(tr => calcNetWeight(tr.dataset.rowIndex));
}

function calcNetWeight(idx) {
    const gross = parseFloat(document.getElementById(`gross_${idx}`)?.value)              || 0;
    const avgPF = parseFloat(document.getElementById('avg_pallet_foreign_weight')?.value) || 0;
    const net   = gross - avgPF;
    const netEl = document.getElementById(`net_${idx}`);
    if (netEl) netEl.value = net > 0 ? net.toFixed(2) : '0.00';
}

function calcWeightDiff(idx) {
    const initial = parseFloat(document.getElementById(`acid_initial_${idx}`)?.value) || 0;
    const drained = parseFloat(document.getElementById(`acid_drained_${idx}`)?.value) || 0;
    const diffEl  = document.getElementById(`acid_diff_${idx}`);
    if (diffEl) diffEl.value = (initial - drained).toFixed(2);
}

function recalcTotals() {
    let gross = 0, net = 0;
    document.querySelectorAll('#palletBody tr').forEach(tr => {
        const idx = tr.dataset.rowIndex;
        gross += parseFloat(document.getElementById(`gross_${idx}`)?.value) || 0;
        net   += parseFloat(document.getElementById(`net_${idx}`)?.value)   || 0;
    });
    document.getElementById('totalGross').value = gross.toFixed(2);
    document.getElementById('totalNet').value   = net.toFixed(2);
}

// ── Pallet rows ───────────────────────────────────────────────────────────────
function addPalletRow() {
    rowCount++;
    const tbody = document.getElementById('palletBody');
    const tr    = document.createElement('tr');
    tr.id               = `prow-${rowCount}`;
    tr.dataset.rowIndex = rowCount;

    tr.innerHTML = `
        <td class="sr-cell">${rowCount}</td>
        <td><input type="text" class="row-input" id="pallet_no_${rowCount}" placeholder="P-001"></td>
        <td><input type="number" class="row-input" id="gross_${rowCount}" placeholder="0.00" step="0.01"
             oninput="calcNetWeight(${rowCount});recalcTotals()"></td>
        <td><input type="number" class="row-input" id="net_${rowCount}" placeholder="0.00"
             readonly style="background:#eef6f1;color:var(--green);font-weight:600;"></td>
        <td>
            <div class="select-cell-wrap">
                <select class="row-select" id="remarks_${rowCount}" onchange="onRemarksChange(${rowCount})">
                    <option value="">Select…</option>
                    ${REMARKS_OPTIONS.map(n => `<option value="${n.value}">${n.text}</option>`).join('')}
                </select>
            </div>
            <div class="acid-fields" id="acid_fields_${rowCount}">
                <div>
                    <div class="acid-field-label">Initial Weight</div>
                    <input type="number" class="row-input" id="acid_initial_${rowCount}" placeholder="0.00" step="0.01" oninput="calcWeightDiff(${rowCount})">
                </div>
                <div>
                    <div class="acid-field-label">Drained Weight</div>
                    <input type="number" class="row-input" id="acid_drained_${rowCount}" placeholder="0.00" step="0.01" oninput="calcWeightDiff(${rowCount})">
                </div>
                <div>
                    <div class="acid-field-label">Weight Difference</div>
                    <input type="number" class="row-input" id="acid_diff_${rowCount}" placeholder="0.00" readonly style="background:#eef6f1;color:var(--green);font-weight:600;">
                </div>
                <div>
                    <div class="acid-field-label">Acid Content %</div>
                    <input type="number" class="row-input" id="acid_content_${rowCount}" placeholder="0.00" step="0.01">
                </div>
            </div>
        </td>
        <td>${rowCount > 1 ? `
            <button class="delete-btn" onclick="removeRow(${rowCount})" title="Remove">
                <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </button>` : ''}</td>
    `;

    tbody.appendChild(tr);
    tr.style.opacity   = '0';
    tr.style.transform = 'translateY(-6px)';
    requestAnimationFrame(() => {
        tr.style.transition = 'opacity 0.25s,transform 0.25s';
        tr.style.opacity    = '1';
        tr.style.transform  = 'translateY(0)';
    });
}

function onRemarksChange(idx) {
    const val        = document.getElementById(`remarks_${idx}`).value;
    const acidFields = document.getElementById(`acid_fields_${idx}`);
    if (String(val) === '5') {
        acidFields.classList.add('visible');
    } else {
        acidFields.classList.remove('visible');
        ['acid_initial','acid_drained','acid_diff','acid_content'].forEach(f => {
            const el = document.getElementById(`${f}_${idx}`);
            if (el) el.value = '';
        });
    }
}

function removeRow(idx) {
    const tr = document.getElementById(`prow-${idx}`);
    if (tr) {
        tr.style.transition = 'opacity 0.2s';
        tr.style.opacity    = '0';
        setTimeout(() => { tr.remove(); renumberRows(); recalcTotals(); }, 200);
    }
}

function renumberRows() {
    document.querySelectorAll('#palletBody tr').forEach((tr, i) => {
        const srCell = tr.querySelector('.sr-cell');
        if (srCell) srCell.textContent = i + 1;
    });
}

// // ── Save ─────────────────────────────────────────────────────────────────────
// function saveRecord() {
//     const btn = document.querySelector('.form-actions .btn-primary');
//     btn.disabled  = true;
//     btn.innerHTML = `<svg viewBox="0 0 24 24" style="animation:spin 0.8s linear infinite;width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Saving…`;

//     // TODO: wire up to apiFetch('/acid-testings', { method: 'POST', body: JSON.stringify(payload) })
//     setTimeout(() => {
//         btn.disabled  = false;
//         btn.innerHTML = `<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Create Record`;
//         showAlert('✓ Acid test record created successfully!', 'success');
//     }, 1400);
// }

// ── Helpers ───────────────────────────────────────────────────────────────────
// function showAlert(msg, type = 'success') {
//     const el      = document.getElementById('formAlert');
//     el.className  = `form-alert ${type}`;
//     el.textContent = msg;
//     el.style.display = 'block';
//     setTimeout(() => { el.style.display = 'none'; }, 4000);
// }
function showAlert(msg, type = 'error') {
    const el = document.getElementById('formAlert');
    el.className = `form-alert ${type}`;
    el.textContent = msg;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function clearAlert() {
    const el = document.getElementById('formAlert');
    el.className = 'form-alert';
    el.textContent = '';
}
// ── Build payload matching POST /api/acid-testings validation ─────────────────
function buildPayload() {
    const details = [];
    let valid = true;
    const errors = [];
    const rows = document.querySelectorAll('#palletBody tr');
    console.log("Rows found:", rows.length);
    document.querySelectorAll('#palletBody tr').forEach((tr, i) => {
        const idx        = tr.dataset.rowIndex;
        const palletNo   = document.getElementById(`pallet_no_${idx}`)?.value?.trim();
        const gross      = parseFloat(document.getElementById(`gross_${idx}`)?.value)         || 0;
        const net        = parseFloat(document.getElementById(`net_${idx}`)?.value)           || 0;
        const remarks    = document.getElementById(`remarks_${idx}`)?.value;
        const initial    = parseFloat(document.getElementById(`acid_initial_${idx}`)?.value)  || 0;
        const drained    = parseFloat(document.getElementById(`acid_drained_${idx}`)?.value)  || 0;

        // Row-level validation
        if (!palletNo)  { errors.push(`Row ${i + 1}: Pallet No is required.`);   valid = false; }
        if (gross <= 0) { errors.push(`Row ${i + 1}: Gross weight must be > 0.`); valid = false; }
        if (!remarks)   { errors.push(`Row ${i + 1}: Remarks / ULAB type is required.`); valid = false; }

        // Find the text label for the selected remarks option
        const remarksLabel = REMARKS_OPTIONS.find(r => String(r.value) === String(remarks))?.text ?? remarks;

        details.push({
            pallet_no      : parseInt(palletNo) || (i + 1),
            gross_weight   : gross,
            net_weight     : net,
            ulab_type      : remarks,   // controller stores text e.g. "ULAB - INDUSTRIAL"
            initial_weight : initial,
            drained_weight : drained,
            remarks        : remarks,
        });
    });

    if (!valid) {
        showAlert(errors.join('\n'), 'error');
        return null;
    }


    return {
        test_date                    : document.getElementById('date').value,
        lot_number                   : document.getElementById('lot_no').value,
        supplier_id                  : document.getElementById('supplier').value,
        vehicle_number               : document.getElementById('vehicle').value,
        avg_pallet_weight            : parseFloat(document.getElementById('avg_pallet_weight').value)       || 0,
        foreign_material_weight      : parseFloat(document.getElementById('foreign_material_weight').value) || 0,
        invoice_qty                  : parseFloat(document.getElementById('invoice_qty').value)       || 0,
        received_qty                 : parseFloat(document.getElementById('inhouse_weight').value)       || 0,
        avg_pallet_and_foreign_weight: parseFloat(document.getElementById('avg_pallet_foreign_weight').value) || 0,
        details,
    };
}


// ── Save  →  POST /api/acid-testings ─────────────────────────────────────────
// ── Save (create or update) ───────────────────────────────────────
async function saveForm(silent = false) {
    // clearAlert();
    // clearFieldErrors();
    const payload = buildPayload();
    const btn = document.getElementById('btnSave');
    btn.disabled = true;

    const method   = isCreate ? 'POST' : 'PUT';
    const endpoint = isCreate ? '/acid-testings' : `/acid-testings/${recordId}`;

    const res = await apiFetch(endpoint, {
        method,
        body: JSON.stringify(payload),
    });

    btn.disabled = false;

    if (!res) return;

    const data = await res.json();

    if (res.ok && data.status === 'ok') {
        if (!silent) {
            if (isCreate) {
                // Redirect to edit page of newly created record
                window.location.href = `{{ url('/admin/mes/acidTesting') }}/${data.data.id}/edit`;
            } else {
                showAlert('Record saved successfully.', 'success');
            }
        } else {
            // Autosave — just update status text
            const status = document.getElementById('autosaveStatus');
            status.style.display = 'inline';
            status.textContent = 'Autosaved at ' + new Date().toLocaleTimeString();
            setTimeout(() => status.style.display = 'none', 5000);
        }
    } else if (res.status === 422) {
        showFieldErrors(data.errors ?? {});
        if (!silent) showAlert(data.message ?? 'Please fix the errors below.');
    } else {
        if (!silent) showAlert(data.message ?? 'Something went wrong.');
    }
}
// ── Save — now uses DataService (offline-aware) ───────────────────
// async function saveForm(silent = false) {
//     clearAlert();
//     clearFieldErrors();

//     const btn = document.getElementById('btnSave');
//     btn.disabled = true;

//     const result = await ds.save(
//         buildPayload(),
//         _localId,                    // local IndexedDB id (null on first save)
//         isCreate ? null : recordId   // server id (null when creating)
//     );

//     btn.disabled = false;

//     if (result.success) {
//         // Remember local_id for subsequent saves in this session
//         _localId = result.local_id;

//         if (result.synced) {
//             // ── Online: saved to server ───────────────────────────
//             if (!silent) {
//                 if (isCreate) {
//                     window.location.href = `/admin/mes/acidTesting/${result.server_id}/edit`;
//                 } else {
//                     showAlert('Record saved successfully.', 'success');
//                 }
//             } else {
//                 const status = document.getElementById('autosaveStatus');
//                 status.style.display = 'inline';
//                 status.textContent = 'Autosaved at ' + new Date().toLocaleTimeString();
//                 setTimeout(() => status.style.display = 'none', 5000);
//             }

//         } else {
//             // ── Offline: saved to IndexedDB ───────────────────────
//             if (!silent) {
//                 showAlert(
//                     '📱 Saved offline — will sync automatically when reconnected.',
//                     'success'
//                 );
//             } else {
//                 const status = document.getElementById('autosaveStatus');
//                 status.style.display = 'inline';
//                 status.textContent = '📱 Saved offline';
//                 setTimeout(() => status.style.display = 'none', 5000);
//             }
//         }

//     } else if (result.validation_error) {
//         // ── Server returned 422 validation errors ─────────────────
//         showFieldErrors(result.errors ?? {});
//         if (!silent) showAlert(result.message ?? 'Please fix the errors below.');

//     } else {
//         if (!silent) showAlert('Something went wrong. Please try again.');
//     }
// }

// // Expose saveForm globally so the onclick in the blade HTML can call it
// window.saveForm = saveForm;
// window.onLotChange = onLotChange;

function showFieldErrors(errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const errEl = document.getElementById('err_' + field);
        const input = document.getElementById(field);
        if (errEl) errEl.textContent = Array.isArray(messages) ? messages[0] : messages;
        if (input) input.classList.add('is-invalid');
    });
}
function setReadonly(readonly) {
    const fields = ['date','lot_no','vehicle','supplier_name','avg_pallet_weight','inhouse_weight','inhouse_weight','foreign_material_weight','avg_pallet_foreign_weight'];
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (readonly) { el.setAttribute('disabled', true); el.setAttribute('readonly', true); }
        else          { el.removeAttribute('disabled'); el.removeAttribute('readonly'); }
    });
    document.getElementById('formActions').style.display = readonly ? 'none' : 'flex';
}
async function submitRecord() {
    if (!confirm('Submit this record? It will be locked from further edits.')) return;

    // Save first, then submit
    await saveForm(true);

    const res = await apiFetch(`/acid-testings/${recordId}/status`, {
        method: 'PATCH',
        body: JSON.stringify({ status: 1 }),
    });

    if (res?.ok) {
        showAlert('Record submitted successfully.', 'success');
        setTimeout(() => window.location.href = '{{ route('admin.mes.acidTesting.index') }}', 1500);
    } else {
        const d = await res.json();
        showAlert(d.message ?? 'Submit failed.');
    }
}
function setupAutosave() {
    const fields = ['receipt_date','lot_no','vehicle_number','supplier_id','material_id','invoice_qty','received_qty','unit','remarks'];
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', scheduleAutosave);
        if (['text','number'].includes(el.type) || el.tagName === 'TEXTAREA') {
            el.addEventListener('keyup', scheduleAutosave);
        }
    });
}

function scheduleAutosave() {
    const status = document.getElementById('autosaveStatus');
    status.style.display = 'inline';
    status.style.color   = 'var(--text-muted)';
    status.textContent   = 'Saving...';
    clearTimeout(autosaveTimer);
    autosaveTimer = setTimeout(() => saveForm(true), 3000);
}
// async function saveRecord() {
//     const payload = buildPayload();
//     if (!payload) return; // validation failed, error already shown

//     const btn = document.querySelector('.form-actions .btn-primary');
//     btn.disabled  = true;
//     btn.innerHTML = `
//         <svg viewBox="0 0 24 24" style="animation:spin 0.8s linear infinite;width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round">
//             <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
//         </svg> Saving…`;

//     const res = await apiFetch('/acid-testings', {
//         method : 'POST',
//         body   : JSON.stringify(payload),
//     });

//     // apiFetch returns null on 401 (auto-redirects to login)
//     if (!res) return;

//     const json = await res.json();

//     if (res.ok && json.status === 'ok') {
//         showAlert('✓ ' + (json.message ?? 'Acid test saved successfully!'), 'success');
//         // Redirect to index after short delay
//         setTimeout(() => {
//             window.location.href = '{{ route("admin.mes.acidTesting.index") }}';
//         }, 1200);
//         return;
//     }

//     // Handle Laravel validation errors (422)
//     if (res.status === 422 && json.errors) {
//         const messages = Object.values(json.errors).flat().join('\n');
//         showAlert(messages, 'error');
//     } else {
//         showAlert(json.message ?? 'Failed to save. Please try again.', 'error');
//     }

//     btn.disabled  = false;
//     btn.innerHTML = `
//         <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round">
//             <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/>
//             <polyline points="17 21 17 13 7 13 7 21"/>
//             <polyline points="7 3 7 8 15 8"/>
//         </svg> Create Record`;
// }
</script>
@endpush