<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Battery Acid Test</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --green:#1a7a3a; --green-dark:#145f2d; --green-light:#e8f5ed; --green-xlight:#f2faf5;
    --white:#ffffff; --bg:#f4f7f5; --border:#dde8e2; --text:#1e2d26; --text-mid:#3d5449;
    --text-muted:#6b8a78; --error:#dc2626; --shadow-sm:0 2px 8px rgba(0,0,0,0.06);
    --shadow-md:0 4px 16px rgba(0,0,0,0.09); --radius:12px; --radius-sm:8px;
  }
  *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:'Outfit',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

  .page-shell { max-width:1280px; margin:0 auto; padding:28px 24px 100px; }

  .breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-muted); margin-bottom:20px; }
  .breadcrumb a { color:var(--text-muted); text-decoration:none; }
  .breadcrumb a:hover { color:var(--green); }
  .breadcrumb .sep { color:var(--border); }

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
  .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px 28px; }
  .form-grid-4 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:18px 28px; }
  .full { grid-column:1/-1; }

  .field { display:flex; flex-direction:column; }
  .field label { font-size:11px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase; color:var(--text-mid); margin-bottom:7px; }
  .field label .req { color:var(--error); }
  .optional-tag { display:inline-block; background:var(--green-light); color:var(--text-muted); font-size:10px; font-weight:600; padding:2px 7px; border-radius:10px; text-transform:uppercase; letter-spacing:0.8px; margin-left:6px; }
  .autofill-tag { display:inline-block; background:#dbeafe; color:#1d4ed8; font-size:10px; font-weight:600; padding:2px 7px; border-radius:10px; text-transform:uppercase; letter-spacing:0.8px; margin-left:6px; }

  .input-wrap { position:relative; }
  .input-wrap .ico { position:absolute; left:12px; top:50%; transform:translateY(-50%); width:14px; height:14px; stroke:var(--text-muted); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; pointer-events:none; }
  input[type="text"], input[type="number"], input[type="date"], input[type="time"], select, textarea {
    width:100%; padding:10px 13px 10px 38px; border:1.5px solid var(--border); border-radius:9px;
    background:var(--green-xlight); font-family:'Outfit',sans-serif; font-size:13.5px; color:var(--text);
    outline:none; appearance:none; transition:border-color 0.2s,box-shadow 0.2s,background 0.2s;
  }
  .no-icon { padding-left:13px !important; }
  input:focus, select:focus, textarea:focus { border-color:var(--green); background:var(--white); box-shadow:0 0 0 4px rgba(26,122,58,0.08); }
  input::placeholder, textarea::placeholder { color:var(--text-muted); }
  input[readonly], input.autofilled { background:#eef6f1; color:var(--text-mid); cursor:default; border-color:#c8dfd1; }
  input.autofilled:focus { box-shadow:none; border-color:#c8dfd1; }
  .select-wrap::after { content:''; position:absolute; right:12px; top:50%; transform:translateY(-50%); border-left:5px solid transparent; border-right:5px solid transparent; border-top:5px solid var(--text-muted); pointer-events:none; }
  .error-msg { margin-top:5px; font-size:11.5px; color:var(--error); }

  /* ── Pallet Rows Table ── */
  .pallet-table-wrap { overflow-x:auto; }
  .pallet-table { width:100%; border-collapse:collapse; min-width:700px; }
  .pallet-table thead th {
    font-size:10.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase;
    color:var(--green); background:var(--green-light); padding:10px 12px;
    border-bottom:2px solid var(--border); text-align:left; white-space:nowrap;
  }
  .pallet-table thead th:first-child { text-align:center; width:52px; }
  .pallet-table tbody td { padding:7px 8px; border-bottom:1px solid #edf2ef; vertical-align:top; }
  .pallet-table tbody tr:last-child td { border-bottom:none; }
  .pallet-table tbody tr:hover td { background:#f7fbf8; }
  .pallet-table tfoot td { background:var(--green-light); font-weight:700; font-size:13px; color:var(--green); padding:9px 12px; }

  .sr-cell { text-align:center; font-size:13px; font-weight:700; color:var(--green); padding-top:10px !important; }
  .row-input { width:100%; padding:8px 11px; border:1.5px solid var(--border); border-radius:7px;
               background:var(--green-xlight); font-family:'Outfit',sans-serif; font-size:13px; color:var(--text);
               outline:none; transition:border-color 0.2s,background 0.2s; }
  .row-input:focus { border-color:var(--green); background:var(--white); box-shadow:0 0 0 3px rgba(26,122,58,0.08); }
  .row-select { width:100%; padding:8px 28px 8px 11px; border:1.5px solid var(--border); border-radius:7px;
                background:var(--green-xlight); font-family:'Outfit',sans-serif; font-size:13px; color:var(--text);
                outline:none; appearance:none; transition:border-color 0.2s,background 0.2s; }
  .row-select:focus { border-color:var(--green); background:var(--white); box-shadow:0 0 0 3px rgba(26,122,58,0.08); }
  .select-cell-wrap { position:relative; }
  .select-cell-wrap::after { content:''; position:absolute; right:10px; top:50%; transform:translateY(-50%); border-left:4px solid transparent; border-right:4px solid transparent; border-top:4px solid var(--text-muted); pointer-events:none; }

  /* ── Acid Fields (shown when remarks = 5) ── */
  .acid-fields { margin-top:8px; display:none; grid-template-columns:repeat(4,1fr); gap:6px; }
  .acid-fields.visible { display:grid; }
  .acid-field-label { font-size:9.5px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; color:var(--text-muted); margin-bottom:4px; white-space:nowrap; }

  .delete-btn { width:28px; height:28px; background:#fee2e2; border:none; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s; margin:auto; }
  .delete-btn:hover { background:#fca5a5; }
  .delete-btn svg { width:13px; height:13px; stroke:#dc2626; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }

  .add-row-wrap { padding:14px 0 0; display:flex; justify-content:flex-end; }

  .form-actions { position:sticky; bottom:0; background:var(--white); border-top:1px solid var(--border);
                  padding:15px 22px; display:flex; align-items:center; justify-content:space-between;
                  flex-wrap:wrap; gap:12px; z-index:10; box-shadow:0 -4px 16px rgba(0,0,0,0.06); }

  .form-alert { display:none; padding:11px 16px; border-radius:9px; font-size:13px; font-weight:500; margin-bottom:16px; }
  .form-alert.error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; display:block; }
  .form-alert.success { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; display:block; }

  /* Loading shimmer */
  .shimmer { background:linear-gradient(90deg,#e8f5ed 25%,#d0e8d8 50%,#e8f5ed 75%); background-size:200% 100%; animation:shimmer 1.2s infinite; border-radius:7px; }
  @keyframes shimmer { to { background-position:-200% 0; } }

  @media(max-width:768px) {
    .form-grid-2, .form-grid-3, .form-grid-4 { grid-template-columns:1fr 1fr; }
  }
  @media(max-width:520px) {
    .form-grid-2, .form-grid-3, .form-grid-4 { grid-template-columns:1fr; }
    .form-actions { flex-direction:column; align-items:stretch; }
    .form-actions .btn { justify-content:center; }
  }
</style>
</head>
<body>
<div class="page-shell">

  <!-- Breadcrumb -->
  <div class="breadcrumb">
    <a href="#">Dashboard</a>
    <span class="sep">/</span>
    <a href="#">Acid Testing</a>
    <span class="sep">/</span>
    <strong style="color:var(--text);">Create Record</strong>
  </div>

  <!-- Page Header -->
  <div class="page-header">
    <div>
      <h2>Battery Acid Test</h2>
      <p>Record pallet weights, net weights and acid content readings for incoming battery lots</p>
    </div>
    <div style="display:flex;gap:10px;">
      <a href="#" class="btn btn-outline btn-sm">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Back to List
      </a>
    </div>
  </div>

  <div id="formAlert" class="form-alert"></div>

  <!-- ═══ SECTION 1 — Primary Details ═══ -->
  <div class="form-card">
    <div class="form-section-head">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span>Primary Details</span>
    </div>
    <div class="form-section-body">
      <div class="form-grid-2">

        <!-- DATE -->
        <div class="field">
          <label for="date">Date <span class="req">*</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input type="date" id="date" required>
          </div>
        </div>

        <!-- LOT NO -->
        <div class="field">
          <label for="lot_no">Lot No <span class="req">*</span></label>
          <div class="input-wrap select-wrap">
            <svg class="ico" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            <select id="lot_no" required onchange="onLotChange()">
              <option value="">Loading lots...</option>
            </select>
          </div>
          <div class="error-msg" id="err_lot_no"></div>
        </div>

        <!-- VEHICLE -->
        <div class="field">
          <label for="vehicle">Vehicle <span class="autofill-tag">Auto-filled</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <input type="text" id="vehicle" class="autofilled" readonly placeholder="Select a lot first...">
          </div>
        </div>

        <!-- SUPPLIER NAME -->
        <div class="field">
          <label for="supplier_name">Supplier Name <span class="autofill-tag">Auto-filled</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <input type="text" id="supplier_name" class="autofilled" readonly placeholder="Select a lot first...">
          </div>
        </div>

        <!-- AVG PALLET WEIGHT -->
        <div class="field">
          <label for="avg_pallet_weight">Avg Pallet Weight (KG) <span class="req">*</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <input type="number" id="avg_pallet_weight" step="0.01" placeholder="0.00" oninput="calcAvgPalletForeign()">
          </div>
        </div>

        <!-- IN HOUSE WEIGH BRIDGE WEIGHT -->
        <div class="field">
          <label for="inhouse_weight">In House Weigh Bridge Weight <span class="autofill-tag">Auto-filled</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            <input type="number" id="inhouse_weight" class="autofilled" readonly placeholder="Select a lot first...">
          </div>
        </div>

        <!-- FOREIGN MATERIAL WEIGHT -->
        <div class="field">
          <label for="foreign_material_weight">Foreign Material Weight (KG) <span class="req">*</span></label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <input type="number" id="foreign_material_weight" step="0.01" placeholder="0.00" oninput="calcAvgPalletForeign()">
          </div>
        </div>

        <!-- AVERAGE PALLET & FOREIGN WEIGHT -->
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

  <!-- ═══ SECTION 2 — Pallet Records ═══ -->
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
              <td><input type="text" id="totalNet" readonly class="row-input" placeholder="0.00" style="font-weight:700;color:var(--green);background:var(--green-light);"></td>
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

  <!-- ═══ STICKY FOOTER ═══ -->
  <div class="form-actions">
    <a href="{{ route('admin.mes.acidTesting.index') }}" class="btn btn-outline btn-sm">Cancel</a>
    <div style="display:flex;gap:10px;align-items:center;">
      <button type="button" class="btn btn-primary btn-sm" onclick="saveRecord()">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Create Record
      </button>
    </div>
  </div>

</div><!-- /page-shell -->

<script>
// ── Mock API Data ────────────────────────────────────────────────
const mockPrefillData = {
  'LOT-2026-001': { vehicle:'TRK-4821', supplier_name:'Al Madina Recycling LLC', inhouse_weight: 24500 },
  'LOT-2026-002': { vehicle:'TRK-3390', supplier_name:'Gulf Battery Traders',    inhouse_weight: 18750 },
  'LOT-2026-003': { vehicle:'TRK-7712', supplier_name:'Emirates Scrap Co.',      inhouse_weight: 31200 },
  'LOT-2026-004': { vehicle:'TRK-0055', supplier_name:'Sharjah Metal Works',     inhouse_weight: 22000 },
};

const REMARKS_OPTIONS = [1,2,3,4,5];
let rowCount = 0;

// ── Init ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('date').value = new Date().toISOString().slice(0,10);
  loadLots();
  addPalletRow();
});

// ── Load lots (mock API) ─────────────────────────────────────────
function loadLots() {
  const sel = document.getElementById('lot_no');
  sel.innerHTML = '<option value="">Select a lot...</option>';
  Object.keys(mockPrefillData).forEach(lot => {
    const opt = document.createElement('option');
    opt.value = lot;
    opt.textContent = lot;
    sel.appendChild(opt);
  });
}

// ── Lot change → autofill ────────────────────────────────────────
function onLotChange() {
  const lotNo = document.getElementById('lot_no').value;
  const fields = ['vehicle','supplier_name','inhouse_weight'];

  if (!lotNo) {
    fields.forEach(f => {
      const el = document.getElementById(f);
      el.value = '';
      el.placeholder = 'Select a lot first...';
    });
    return;
  }

  // Simulate API: GET acid-testings/prefill/{lotNo}
  const data = mockPrefillData[lotNo];
  if (data) {
    // Animate fill
    fields.forEach(f => {
      const el = document.getElementById(f);
      el.style.transition = 'background 0.3s';
      el.value = data[f] ?? '';
      el.style.background = '#d1fae5';
      setTimeout(() => { el.style.background = ''; }, 800);
    });
  }
}

// ── Avg Pallet & Foreign Weight (auto-calc) ──────────────────────
function calcAvgPalletForeign() {
  const avg = parseFloat(document.getElementById('avg_pallet_weight').value) || 0;
  const foreign = parseFloat(document.getElementById('foreign_material_weight').value) || 0;
  const result = avg + foreign;
  document.getElementById('avg_pallet_foreign_weight').value = result > 0 ? result.toFixed(2) : '';
}

// ── Add Pallet Row ───────────────────────────────────────────────
function addPalletRow() {
  rowCount++;
  const tbody = document.getElementById('palletBody');
  const tr = document.createElement('tr');
  tr.id = `prow-${rowCount}`;
  tr.dataset.rowIndex = rowCount;

  tr.innerHTML = `
    <td class="sr-cell">${rowCount}</td>
    <td><input type="text" class="row-input" id="pallet_no_${rowCount}" placeholder="P-001"></td>
    <td><input type="number" class="row-input" id="gross_${rowCount}" placeholder="0.00" step="0.01" oninput="calcNetWeight(${rowCount});recalcTotals()"></td>
    <td><input type="number" class="row-input" id="net_${rowCount}" placeholder="0.00" step="0.01" oninput="recalcTotals()" readonly style="background:#eef6f1;color:var(--green);font-weight:600;"></td>
    <td>
      <div class="select-cell-wrap">
        <select class="row-select" id="remarks_${rowCount}" onchange="onRemarksChange(${rowCount})">
          <option value="">Select...</option>
          ${REMARKS_OPTIONS.map(n=>`<option value="${n}">${n}</option>`).join('')}
        </select>
      </div>
      <div class="acid-fields" id="acid_fields_${rowCount}">
        <div>
          <div class="acid-field-label">Initial Weight</div>
          <input type="number" class="row-input" id="acid_initial_${rowCount}" placeholder="0.00" step="0.01">
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

  // Animate in
  tr.style.opacity = '0';
  tr.style.transform = 'translateY(-6px)';
  requestAnimationFrame(() => {
    tr.style.transition = 'opacity 0.25s,transform 0.25s';
    tr.style.opacity = '1';
    tr.style.transform = 'translateY(0)';
  });
}

// ── Remarks change → show/hide acid fields ───────────────────────
function onRemarksChange(idx) {
  const val = document.getElementById(`remarks_${idx}`).value;
  const acidFields = document.getElementById(`acid_fields_${idx}`);
  if (val === '5') {
    acidFields.classList.add('visible');
  } else {
    acidFields.classList.remove('visible');
    // clear acid fields
    ['acid_initial','acid_drained','acid_diff','acid_content'].forEach(f => {
      const el = document.getElementById(`${f}_${idx}`);
      if(el) el.value = '';
    });
  }
}

// ── Weight Difference (Initial - Drained) ────────────────────────
function calcWeightDiff(idx) {
  const initial  = parseFloat(document.getElementById(`acid_initial_${idx}`)?.value) || 0;
  const drained  = parseFloat(document.getElementById(`acid_drained_${idx}`)?.value) || 0;
  const diffEl   = document.getElementById(`acid_diff_${idx}`);
  if(diffEl) diffEl.value = (initial - drained).toFixed(2);
}

// ── Net weight = Gross - Avg Pallet & Foreign ────────────────────
function calcNetWeight(idx) {
  const gross = parseFloat(document.getElementById(`gross_${idx}`)?.value) || 0;
  const avgPF = parseFloat(document.getElementById('avg_pallet_foreign_weight')?.value) || 0;
  const net   = gross - avgPF;
  const netEl = document.getElementById(`net_${idx}`);
  if(netEl) netEl.value = net > 0 ? net.toFixed(2) : '0.00';
}

// ── Recalc totals ────────────────────────────────────────────────
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

// ── Remove row ───────────────────────────────────────────────────
function removeRow(idx) {
  const tr = document.getElementById(`prow-${idx}`);
  if(tr) {
    tr.style.transition = 'opacity 0.2s';
    tr.style.opacity = '0';
    setTimeout(() => { tr.remove(); renumberRows(); recalcTotals(); }, 200);
  }
}

function renumberRows() {
  document.querySelectorAll('#palletBody tr').forEach((tr, i) => {
    const srCell = tr.querySelector('.sr-cell');
    if(srCell) srCell.textContent = i + 1;
  });
}

// ── Save ─────────────────────────────────────────────────────────
function saveRecord() {
  const btn = document.querySelector('.form-actions .btn-primary');
  btn.disabled = true;
  const spinStyle = document.createElement('style');
  spinStyle.textContent = '@keyframes spin{to{transform:rotate(360deg)}}';
  document.head.appendChild(spinStyle);
  btn.innerHTML = `<svg viewBox="0 0 24 24" style="animation:spin 0.8s linear infinite;width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Saving...`;

  setTimeout(() => {
    btn.disabled = false;
    btn.innerHTML = `<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Create Record`;
    const alert = document.getElementById('formAlert');
    alert.className = 'form-alert success';
    alert.textContent = '✓ Acid test record created successfully!';
    setTimeout(() => { alert.style.display = 'none'; }, 3500);
  }, 1400);
}
</script>
</body>
</html>