{{-- resources/views/admin/settings/modules/index.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Modules')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span><span style="color:var(--text-muted)">Settings</span>
    <span style="margin:0 6px;color:var(--border)">/</span><strong>Modules</strong>
@endsection

@push('styles')
    <style>
        :root {
            --g: #1a7a3a;
            --gd: #145f2d;
            --gl: #e8f5ed;
            --gxl: #f2faf5;
            --bdr: #dde8e2;
            --txt: #1e2d26;
            --txtm: #3d5449;
            --txtmu: #6b8a78;
            --err: #dc2626;
            --sh: 0 1px 6px rgba(26, 122, 58, .07), 0 4px 18px rgba(26, 122, 58, .05);
            --r: 12px
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
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
            transform: translateY(-1px)
        }

        .btn-outline {
            background: #fff;
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
            color: #dc2626;
            border: 1.5px solid #fca5a5
        }

        .btn-danger:hover {
            background: #fca5a5
        }

        .btn-sm {
            padding: 6px 13px;
            font-size: 12.5px
        }

        .btn-xs {
            padding: 4px 10px;
            font-size: 11.5px;
            border-radius: 7px
        }

        .card {
            background: #fff;
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

        .filter-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 14px 20px;
            border-bottom: 1px solid var(--bdr);
            background: var(--gxl)
        }

        .filter-bar input,
        .filter-bar select {
            padding: 7px 12px;
            border: 1.5px solid var(--bdr);
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            color: var(--txt);
            background: #fff;
            outline: none;
            transition: border-color .18s
        }

        .filter-bar input:focus,
        .filter-bar select:focus {
            border-color: var(--g)
        }

        .filter-bar input {
            min-width: 200px
        }

        .tbl-wrap {
            overflow-x: auto
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
            padding: 9px 14px;
            border-bottom: 2px solid var(--bdr);
            white-space: nowrap;
            text-align: left;
            vertical-align: middle
        }

        .dt tbody td {
            padding: 10px 14px;
            border-bottom: 1px solid #edf2ef;
            font-size: 12.5px;
            vertical-align: middle;
            text-align: left
        }

        .dt tbody tr:last-child td {
            border-bottom: none
        }

        .dt tbody tr:hover td {
            background: #f7fbf8
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b
        }

        .act-btns {
            display: flex;
            gap: 5px;
            align-items: center
        }

        .tbl-state {
            text-align: center;
            padding: 48px 20px;
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
            max-width: 520px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .22);
            transform: translateY(12px);
            transition: transform .2s;
            max-height: 90vh
        }

        .modal-overlay.open .modal-box {
            transform: translateY(0)
        }

        .modal-head {
            padding: 15px 22px;
            border-bottom: 1px solid var(--bdr);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--gl);
            border-radius: 14px 14px 0 0
        }

        .modal-title {
            font-size: 14px;
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
            font-size: 15px;
            color: var(--txtm);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s
        }

        .modal-close:hover {
            background: #fca5a5;
            color: #dc2626
        }

        .modal-body {
            padding: 20px 22px;
            overflow-y: auto;
            flex: 1
        }

        .modal-footer {
            padding: 13px 22px;
            border-top: 1px solid var(--bdr);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            background: #fafcfb;
            border-radius: 0 0 14px 14px
        }

        .fg2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 20px
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 0
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
            left: 10px;
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

        .fi {
            width: 100%;
            padding: 8px 12px 8px 32px;
            border: 1.5px solid var(--bdr);
            border-radius: 8px;
            background: var(--gxl);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            color: var(--txt);
            outline: none;
            transition: border-color .18s, background .18s
        }

        .fi:focus {
            border-color: var(--g);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .08)
        }

        .form-alert {
            display: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
            margin-bottom: 14px
        }

        .form-alert.error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            display: block
        }

        .info-banner {
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 12px;
            margin-bottom: 14px;
            line-height: 1.5
        }

        @media(max-width:600px) {
            .fg2 {
                grid-template-columns: 1fr
            }
        }
    </style>
@endpush

@section('content')
    <div class="ph">
        <div>
            <h2>Modules</h2>
            <p>System modules that can be permission-controlled per user or profile</p>
        </div>
        <button class="btn btn-primary btn-sm" id="btnAddModule" onclick="openModal()">
            <svg viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Add Module
        </button>
    </div>

    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>All Modules</span>
            </div>
            <span id="caption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>
        <div class="filter-bar">
            <input type="text" id="fSearch" placeholder="Search modules…" oninput="onFilter()">
            <select id="fGroup" onchange="onFilter()">
                <option value="">All Groups</option>
            </select>
        </div>
        <div class="tbl-wrap">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Module Name</th>
                        <th>Slug</th>
                        <th>Group</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th style="width:130px">Actions</th>
                    </tr>
                </thead>
                <tbody id="moduleTbody">
                    <tr>
                        <td colspan="6" class="tbl-state"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODULE FORM MODAL --}}
    <div class="modal-overlay" id="moduleModal" onclick="if(event.target===this)closeModal()">
        <div class="modal-box">
            <div class="modal-head">
                <span class="modal-title" id="modalTitle">Add Module</span>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="info-banner">
                    ⚠️ The <strong>Slug</strong> is used in code to check permissions (e.g.
                    <code>middleware('module:receiving')</code>).
                    Set it carefully — changing it later requires code changes too.
                </div>
                <div id="formAlert" class="form-alert"></div>
                <div class="fg2" style="margin-bottom:14px">
                    <div class="field">
                        <label>Module Name <span style="color:var(--err)">*</span></label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" />
                                <rect x="14" y="3" width="7" height="7" />
                                <rect x="14" y="14" width="7" height="7" />
                                <rect x="3" y="14" width="7" height="7" />
                            </svg>
                            <input class="fi" type="text" id="m_name" placeholder="e.g. Receiving" oninput="autoSlug()">
                        </div>
                    </div>
                    <div class="field">
                        <label>Slug <span style="color:var(--err)">*</span></label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                            </svg>
                            <input class="fi" type="text" id="m_slug" placeholder="e.g. receiving">
                        </div>
                    </div>
                    <div class="field">
                        <label>Group</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                            </svg>
                            <input class="fi" type="text" id="m_group" placeholder="e.g. Masters, MES, Reports">
                        </div>
                    </div>
                    <div class="field">
                        <label>Sort Order</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <line x1="8" y1="6" x2="21" y2="6" />
                                <line x1="8" y1="12" x2="21" y2="12" />
                                <line x1="8" y1="18" x2="21" y2="18" />
                                <line x1="3" y1="6" x2="3.01" y2="6" />
                                <line x1="3" y1="12" x2="3.01" y2="12" />
                                <line x1="3" y1="18" x2="3.01" y2="18" />
                            </svg>
                            <input class="fi" type="number" id="m_sort" placeholder="0" min="0" style="padding-left:32px">
                        </div>
                    </div>
                </div>
                <label
                    style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--txtm);font-weight:600;cursor:pointer">
                    <input type="checkbox" id="m_active" checked style="width:15px;height:15px;accent-color:var(--g)">
                    Active
                </label>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" id="saveBtn" onclick="saveModule()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg> Save Module
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let editId = null, filterTimer = null, allModules = [];

        (function init() {
            if (!can('settings_modules', 'can_create')) document.getElementById('btnAddModule').style.display = 'none';
            loadModules();
        })();

        async function loadModules() {
            document.getElementById('moduleTbody').innerHTML = '<tr><td colspan="6" class="tbl-state"><span class="spinner"></span>Loading…</td></tr>';
            const s = document.getElementById('fSearch').value.trim();
            const g = document.getElementById('fGroup').value;
            const p = new URLSearchParams({ per_page: 500 });
            if (s) p.set('search', s);
            if (g) p.set('group', g);
            const res = await apiFetch('/admin/modules?' + p);
            if (!res?.ok) { document.getElementById('moduleTbody').innerHTML = '<tr><td colspan="6" class="tbl-state" style="color:var(--err)">Failed to load.</td></tr>'; return; }
            const json = await res.json();
            allModules = Array.isArray(json.data) ? json.data : [];
            document.getElementById('caption').textContent = allModules.length + ' modules';

            // Populate group filter
            const groups = [...new Set(allModules.map(m => m.group).filter(Boolean))].sort();
            const gSel = document.getElementById('fGroup');
            const curG = gSel.value;
            gSel.innerHTML = '<option value="">All Groups</option>' + groups.map(g => '<option value="' + esc(g) + '"' + (g === curG ? ' selected' : '') + '>' + esc(g) + '</option>').join('');

            renderModules(allModules);
        }

        function renderModules(rows) {
            const canEdit = can('settings_modules', 'can_edit'), canDel = can('settings_modules', 'can_delete');
            document.getElementById('moduleTbody').innerHTML = rows.length ? rows.map(m => {
                const eb = canEdit ? '<button class="btn btn-outline btn-xs" onclick="openModal(' + m.id + ')">Edit</button>' : '';
                const db = canDel ? '<button class="btn btn-danger btn-xs" onclick="delModule(' + m.id + ',\'' + esc(m.name) + '\')">Delete</button>' : '';
                return '<tr>'
                    + '<td style="font-weight:700">' + esc(m.name) + '</td>'
                    + '<td><code style="font-size:11.5px;background:var(--gl);padding:2px 7px;border-radius:5px;color:var(--g)">' + esc(m.slug) + '</code></td>'
                    + '<td><span style="background:#f1f5f9;color:#475569;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700">' + esc(m.group || '—') + '</span></td>'
                    + '<td style="color:var(--txtmu);text-align:center">' + (m.sort_order ?? 0) + '</td>'
                    + '<td>' + (m.is_active ? '<span class="badge badge-active">● Active</span>' : '<span class="badge badge-inactive">● Inactive</span>') + '</td>'
                    + '<td><div class="act-btns">' + eb + db + '</div></td>'
                    + '</tr>';
            }).join('') : '<tr><td colspan="6" class="tbl-state">No modules found.</td></tr>';
        }

        function autoSlug() {
            if (editId) return; // don't auto-change slug when editing
            const name = document.getElementById('m_name').value;
            document.getElementById('m_slug').value = name.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
        }

        function openModal(id = null) {
            editId = id;
            document.getElementById('formAlert').className = 'form-alert';
            document.getElementById('modalTitle').textContent = id ? 'Edit Module' : 'Add Module';
            if (id) {
                const mod = allModules.find(m => m.id === id);
                document.getElementById('m_name').value = mod?.name ?? '';
                document.getElementById('m_slug').value = mod?.slug ?? '';
                document.getElementById('m_group').value = mod?.group ?? '';
                document.getElementById('m_sort').value = mod?.sort_order ?? 0;
                document.getElementById('m_active').checked = mod?.is_active ?? true;
            } else {
                document.getElementById('m_name').value = '';
                document.getElementById('m_slug').value = '';
                document.getElementById('m_group').value = '';
                document.getElementById('m_sort').value = 0;
                document.getElementById('m_active').checked = true;
            }
            document.getElementById('moduleModal').classList.add('open');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('m_name').focus(), 100);
        }

        function closeModal() {
            document.getElementById('moduleModal').classList.remove('open');
            document.body.style.overflow = '';
            editId = null;
        }

        async function saveModule() {
            const btn = document.getElementById('saveBtn');
            btn.disabled = true; btn.textContent = 'Saving…';
            const payload = {
                name: document.getElementById('m_name').value.trim(),
                slug: document.getElementById('m_slug').value.trim(),
                group: document.getElementById('m_group').value.trim(),
                sort_order: parseInt(document.getElementById('m_sort').value) || 0,
                is_active: document.getElementById('m_active').checked,
            };
            const res = await apiFetch(editId ? '/admin/modules/' + editId : '/admin/modules', {
                method: editId ? 'PUT' : 'POST', body: JSON.stringify(payload)
            });
            const data = await res.json();
            btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2"><polyline points="20 6 9 17 4 12"/></svg> Save Module';
            if (res.ok && data.status === 'ok') { closeModal(); loadModules(); showToast(editId ? 'Module updated.' : 'Module created.'); }
            else { const e = document.getElementById('formAlert'); e.className = 'form-alert error'; e.textContent = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message ?? 'Something went wrong.'); }
        }

        async function delModule(id, name) {
            if (!await showConfirm('Delete module "' + name + '"? This will also remove all related permissions.')) return;
            const res = await apiFetch('/admin/modules/' + id, { method: 'DELETE' });
            const data = await res.json();
            if (res.ok) { loadModules(); showToast('Module deleted.'); }
            else alert(data.message ?? 'Cannot delete.');
        }

        function onFilter() { clearTimeout(filterTimer); filterTimer = setTimeout(loadModules, 350); }
        function esc(s) { return s == null ? '' : String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
    </script>
@endpush