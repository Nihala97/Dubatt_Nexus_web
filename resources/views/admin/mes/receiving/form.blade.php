@extends('admin.layouts.app')

@section('title', isset($item_id) ? 'Edit Receiving' : 'Create Receiving')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <a href="{{ route('admin.mes.receiving.index') }}" style="color:var(--text-muted);text-decoration:none;">Receiving</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong id="breadcrumbTitle">Loading...</strong>
@endsection

@push('styles')
<style>
    .form-page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .form-page-header h2 { font-size:clamp(18px,2.5vw,24px); font-weight:700; color:var(--text); margin-bottom:3px; }
    .form-page-header p { font-size:13px; color:var(--text-muted); }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:10px 18px; border-radius:9px;
           font-family:'Outfit',sans-serif; font-size:13.5px; font-weight:600; cursor:pointer;
           text-decoration:none; border:none; transition:all 0.2s; white-space:nowrap; }
    .btn svg { width:15px; height:15px; stroke:currentColor; flex-shrink:0; }
    .btn-primary { background:var(--green); color:#fff; }
    .btn-primary:hover { background:var(--green-dark); box-shadow:0 4px 12px rgba(26,122,58,0.25); transform:translateY(-1px); }
    .btn-outline { background:var(--white); color:var(--text-mid); border:1.5px solid var(--border); }
    .btn-outline:hover { border-color:var(--green); color:var(--green); background:var(--green-xlight); }
    .btn-sm { padding:8px 16px; font-size:13px; }
    .btn:disabled { opacity:0.6; cursor:not-allowed; transform:none !important; }

    .form-card { background:var(--white); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:var(--shadow-sm); margin-bottom:20px; }
    .form-section-head { padding:14px 24px; background:var(--green-light); border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
    .form-section-head svg { width:16px; height:16px; stroke:var(--green); flex-shrink:0; }
    .form-section-head span { font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--green); }
    .form-section-body { padding:28px 24px 32px; }

    .form-grid   { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px,1fr)); gap:20px 28px; }
    .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px 28px; }
    .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px 28px; }
    .field { display:flex; flex-direction:column; }
    .field.full { grid-column:1/-1; }

    .field label { font-size:11px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase; color:var(--text-mid); margin-bottom:8px; }
    .field label .req { color:var(--error); }
    .optional-tag { display:inline-block; background:var(--green-light); color:var(--text-muted); font-size:10px; font-weight:600; padding:2px 8px; border-radius:10px; text-transform:uppercase; letter-spacing:1px; margin-left:6px; vertical-align:middle; }

    .input-wrap { position:relative; }
    .input-wrap .ico { position:absolute; left:13px; top:50%; transform:translateY(-50%); width:15px; height:15px; stroke:var(--text-muted); pointer-events:none; }

    input[type="text"], input[type="number"], input[type="date"], select, textarea {
        width:100%; padding:11px 14px 11px 40px; border:1.5px solid var(--border); border-radius:9px;
        background:var(--green-xlight); font-family:'Outfit',sans-serif; font-size:14px; color:var(--text);
        outline:none; appearance:none; transition:border-color 0.2s,box-shadow 0.2s,background 0.2s;
    }
    textarea { padding-left:14px; resize:vertical; min-height:80px; }
    input.no-icon, select.no-icon, textarea.no-icon { padding-left:14px; }
    input:focus, select:focus, textarea:focus { border-color:var(--green); background:var(--white); box-shadow:0 0 0 4px rgba(26,122,58,0.08); }
    input.is-invalid, select.is-invalid, textarea.is-invalid { border-color:var(--error); }
    input::placeholder, textarea::placeholder { color:var(--text-muted); }
    .select-wrap::after { content:''; position:absolute; right:13px; top:50%; transform:translateY(-50%); border-left:5px solid transparent; border-right:5px solid transparent; border-top:5px solid var(--text-muted); pointer-events:none; }
    .error-msg { margin-top:5px; font-size:12px; color:var(--error); }

    .form-actions { position:sticky; bottom:0; background:var(--white); border-top:1px solid var(--border);
                    padding:16px 24px; display:flex; align-items:center; justify-content:space-between;
                    flex-wrap:wrap; gap:12px; z-index:10; box-shadow:0 -4px 16px rgba(0,0,0,0.06); }

    .status-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap; margin-top:10px; }
    .status-badge.draft     { background:#e0e7ff; color:#3730a3; }
    .status-badge.submitted { background:#d1fae5; color:#065f46; }

    /* Alert box */
    .form-alert { display:none; padding:12px 16px; border-radius:9px; font-size:13px; font-weight:500; margin-bottom:16px; }
    .form-alert.error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; display:block; }
    .form-alert.success { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; display:block; }

    @media (max-width:768px) {
        .form-grid-2, .form-grid-3 { grid-template-columns:1fr 1fr; }
        .form-section-body { padding:20px 16px 24px; }
    }
    @media (max-width:520px) {
        .form-grid, .form-grid-2, .form-grid-3 { grid-template-columns:1fr; }
        .field.full { grid-column:auto; }
        .form-actions { flex-direction:column; align-items:stretch; }
        .form-actions .btn { justify-content:center; }
    }
</style>
@endpush

@section('content')

{{-- Page title/header rendered by JS after data loads --}}
<div class="form-page-header" id="pageHeader">
    <div>
        <h2 id="pageTitle">Loading...</h2>
        <p id="pageSubtitle"></p>
        <div id="statusBadge"></div>
    </div>
    <div style="display:flex;gap:10px;" id="headerActions">
        <a href="{{ route('admin.mes.receiving.index') }}" class="btn btn-outline btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Back to List
        </a>
    </div>
</div>

<div id="formAlert" class="form-alert"></div>

<div id="formWrapper">
    {{-- Skeleton shown while loading --}}
    <div id="loadingSkeleton" style="text-align:center;padding:60px;color:var(--text-muted);">Loading form...</div>
    
    {{-- Actual form, hidden until data is ready --}}
    <div id="formContainer" style="display:none;">
        
        <div class="form-card">
            <div class="form-section-head">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Primary Details</span>
            </div>
            <div class="form-section-body">
                <div class="form-grid-3">

                    <div class="field">
                        <label for="receipt_date">Receipt Date <span class="req">*</span></label>
                        <div class="input-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <input type="date" id="receipt_date" name="receipt_date" required>
                        </div>
                        <div class="error-msg" id="err_receipt_date"></div>
                    </div>

                    <div class="field">
                        <label for="lot_no">Lot Number <span class="req">*</span></label>
                        <div class="input-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 11V7a5 5 0 0 1 10 0v4"/><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/></svg>
                            <input type="text" id="lot_no" name="lot_no" placeholder="Unique lot identifier" required>
                        </div>
                        <div class="error-msg" id="err_lot_no"></div>
                    </div>

                    <div class="field">
                        <label for="vehicle_number">Vehicle Number <span class="req">*</span></label>
                        <div class="input-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            <input type="text" id="vehicle_number" name="vehicle_number" placeholder="Truck/Vehicle License" required>
                        </div>
                        <div class="error-msg" id="err_vehicle_number"></div>
                    </div>

                    <div class="field full">
                        <label for="supplier_id">Supplier <span class="req">*</span></label>
                        <div class="input-wrap select-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <select id="supplier_id" name="supplier_id" required>
                                <option value="">Select a supplier...</option>
                            </select>
                        </div>
                        <div class="error-msg" id="err_supplier_id"></div>
                    </div>

                    <div class="field full">
                        <label for="material_id">Material <span class="req">*</span></label>
                        <div class="input-wrap select-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                            <select id="material_id" name="material_id" required>
                                <option value="">Select material...</option>
                            </select>
                        </div>
                        <div class="error-msg" id="err_material_id"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-section-head">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span>Quantities</span>
            </div>
            <div class="form-section-body">
                <div class="form-grid-3">

                    <div class="field">
                        <label for="invoice_qty">Invoice Quantity <span class="req">*</span></label>
                        <div class="input-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <input type="number" step="0.01" id="invoice_qty" name="invoice_qty" placeholder="0.00" required>
                        </div>
                        <div class="error-msg" id="err_invoice_qty"></div>
                    </div>

                    <div class="field">
                        <label for="received_qty">Received Quantity <span class="req">*</span></label>
                        <div class="input-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/></svg>
                            <input type="number" step="0.01" id="received_qty" name="received_qty" placeholder="0.00" required>
                        </div>
                        <div class="error-msg" id="err_received_qty"></div>
                    </div>

                    <div class="field">
                        <label for="unit">Unit of Measurement <span class="req">*</span></label>
                        <div class="input-wrap select-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            <select id="unit" name="unit" required>
                                <option value="MT">Metric Tons (MT)</option>
                                <option value="KG">Kilograms (KG)</option>
                            </select>
                        </div>
                        <div class="error-msg" id="err_unit"></div>
                    </div>

                    <div class="field full">
                        <label for="remarks">Remarks <span class="optional-tag">Optional</span></label>
                        <textarea id="remarks" name="remarks" class="no-icon" placeholder="Enter any additional notes..."></textarea>
                        <div class="error-msg" id="err_remarks"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="form-actions" id="formActions">
            <a href="{{ route('admin.mes.receiving.index') }}" class="btn btn-outline btn-sm">Cancel</a>
            <div style="display:flex;gap:10px;align-items:center;">
                <span id="autosaveStatus" style="font-size:12px;color:var(--text-muted);display:none;"></span>
                <button type="button" class="btn btn-primary btn-sm" id="btnSave" onclick="saveForm()">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <span id="btnSaveLabel">Create Record</span>
                </button>
            </div>
        </div>

    </div>{{-- /formContainer --}}
</div>

@endsection

@push('scripts')
<script>
// <script type="module">

// // ── Import DataService for offline support ────────────────────────
// import { DataService } from '/pwa/data-service.js';
// const ds = new DataService('receiving');
// let _localId     = null; // tracks IndexedDB local record id

// ── Determine if this is create or edit ───────────────────────────
const PATH_PARTS = window.location.pathname.split('/').filter(Boolean);
// URL pattern: /admin/mes/receiving/{id}/edit  OR  /admin/mes/receiving/create
const isCreate   = PATH_PARTS[PATH_PARTS.length - 1] === 'create';
const recordId   = isCreate ? null : PATH_PARTS[PATH_PARTS.length - 2];

let isSubmitted  = false;
let autosaveTimer;


// ── Helpers ───────────────────────────────────────────────────────
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

function clearFieldErrors() {
    document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showFieldErrors(errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const errEl = document.getElementById('err_' + field);
        const input = document.getElementById(field);
        if (errEl) errEl.textContent = Array.isArray(messages) ? messages[0] : messages;
        if (input) input.classList.add('is-invalid');
    });
}

function getFormData() {
    return {
        receipt_date:   document.getElementById('receipt_date').value,
        lot_no:         document.getElementById('lot_no').value,
        vehicle_number: document.getElementById('vehicle_number').value,
        supplier_id:    document.getElementById('supplier_id').value,
        material_id:    document.getElementById('material_id').value,
        invoice_qty:    document.getElementById('invoice_qty').value,
        received_qty:   document.getElementById('received_qty').value,
        unit:           document.getElementById('unit').value,
        remarks:        document.getElementById('remarks').value,
    };
}

function setReadonly(readonly) {
    const fields = ['receipt_date','lot_no','vehicle_number','supplier_id','material_id','invoice_qty','received_qty','unit','remarks'];
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (readonly) { el.setAttribute('disabled', true); el.setAttribute('readonly', true); }
        else          { el.removeAttribute('disabled'); el.removeAttribute('readonly'); }
    });
    document.getElementById('formActions').style.display = readonly ? 'none' : 'flex';
}

// ── Load dropdowns ────────────────────────────────────────────────
async function loadDropdowns() {
    const [sRes, mRes] = await Promise.all([
        apiFetch('/suppliers?per_page=200'),
        apiFetch('/materials?per_page=200'),
    ]);

    if (sRes?.ok) {
        const data = await sRes.json();
        const sel  = document.getElementById('supplier_id');
        (data.data.data || []).forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = `${s.supplier_name} (${s.supplier_code})`;
            sel.appendChild(opt);
        });
    }

    if (mRes?.ok) {
        const data = await mRes.json();
        const sel  = document.getElementById('material_id');
        (data.data.data || []).forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = `${m.material_name} (${m.material_code})`;
            sel.appendChild(opt);
        });
    }
}

// ── Load existing record for edit ────────────────────────────────
async function loadRecord() {
    const res = await apiFetch(`/receivings/${recordId}`);
    if (!res?.ok) { showAlert('Failed to load record.'); return; }

    const { data } = await res.json();
    isSubmitted = data.status >= 1;

    // Fill form fields
    document.getElementById('receipt_date').value   = data.receipt_date?.split('T')[0] ?? '';
    document.getElementById('lot_no').value         = data.lot_no ?? '';
    document.getElementById('vehicle_number').value = data.vehicle_number ?? '';
    document.getElementById('supplier_id').value    = data.supplier_id ?? '';
    document.getElementById('material_id').value    = data.material_id ?? '';
    document.getElementById('invoice_qty').value    = data.invoice_qty ?? '';
    document.getElementById('received_qty').value   = data.received_qty ?? '';
    document.getElementById('unit').value           = data.unit ?? 'MT';
    document.getElementById('remarks').value        = data.remarks ?? '';

    // Update page header
    document.getElementById('pageTitle').textContent    = 'Edit Receiving';
    document.getElementById('pageSubtitle').textContent = 'Update receiving details';
    document.getElementById('breadcrumbTitle').textContent = 'Edit Receiving';
    document.getElementById('btnSaveLabel').textContent = 'Save Draft';

    if (isSubmitted) {
        document.getElementById('statusBadge').innerHTML =
            '<div class="status-badge submitted">Status: Submitted</div>';
        setReadonly(true);
    } else {
        document.getElementById('statusBadge').innerHTML =
            '<div class="status-badge draft">Status: Draft</div>';

        // Add Submit button to header
        const actionsDiv = document.getElementById('headerActions');
        const submitBtn  = document.createElement('button');
        submitBtn.className   = 'btn btn-outline btn-sm';
        submitBtn.innerHTML   = `<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Submit Record`;
        submitBtn.onclick     = submitRecord;
        actionsDiv.prepend(submitBtn);

        // Setup autosave
        setupAutosave();
    }
}

// // ── Save — now uses DataService (offline-aware) ───────────────────
// async function saveForm(silent = false) {
//     clearAlert();
//     clearFieldErrors();

//     const btn = document.getElementById('btnSave');
//     btn.disabled = true;

//     const result = await ds.save(
//         getFormData(),
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
//                     window.location.href = `/admin/mes/receiving/${result.server_id}/edit`;
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

// ── Save (create or update) ───────────────────────────────────────
async function saveForm(silent = false) {
    clearAlert();
    clearFieldErrors();

    const btn = document.getElementById('btnSave');
    btn.disabled = true;

    const method   = isCreate ? 'POST' : 'PUT';
    const endpoint = isCreate ? '/receivings' : `/receivings/${recordId}`;

    const res = await apiFetch(endpoint, {
        method,
        body: JSON.stringify(getFormData()),
    });

    btn.disabled = false;

    if (!res) return;

    const data = await res.json();

    if (res.ok && data.status === 'ok') {
        if (!silent) {
            if (isCreate) {
                // Redirect to edit page of newly created record
                window.location.href = `{{ url('/admin/mes/receiving') }}/${data.data.id}/edit`;
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

// ── Submit record ─────────────────────────────────────────────────
async function submitRecord() {
    if (!confirm('Submit this record? It will be locked from further edits.')) return;

    // Save first, then submit
    await saveForm(true);

    const res = await apiFetch(`/receivings/${recordId}/status`, {
        method: 'PATCH',
        body: JSON.stringify({ status: 1 }),
    });

    if (res?.ok) {
        showAlert('Record submitted successfully.', 'success');
        setTimeout(() => window.location.href = '{{ route('admin.mes.receiving.index') }}', 1500);
    } else {
        const d = await res.json();
        showAlert(d.message ?? 'Submit failed.');
    }
}

// ── Autosave ──────────────────────────────────────────────────────
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

// ── Init ──────────────────────────────────────────────────────────
async function init() {
    await loadDropdowns();

    if (isCreate) {
        // Set defaults
        document.getElementById('receipt_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('pageTitle').textContent       = 'Create Receiving';
        document.getElementById('pageSubtitle').textContent    = 'Record new raw material receiving log';
        document.getElementById('breadcrumbTitle').textContent = 'Create Receiving';
        document.getElementById('btnSaveLabel').textContent    = 'Create Record';
    } else {
        await loadRecord();
    }

    // Show form, hide skeleton
    document.getElementById('loadingSkeleton').style.display = 'none';
    document.getElementById('formContainer').style.display   = 'block';
}

init();
</script>
@endpush