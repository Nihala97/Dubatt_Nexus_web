{{--
resources/views/admin/settings/roles/index.blade.php
Roles Management — list, create, edit, delete
--}}
@extends('admin.layouts.app')
@section('title', 'Roles')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <span style="color:var(--text-muted)">Settings</span>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <strong>Roles</strong>
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
            --r: 12px
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

        .filter-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 14px 20px;
            border-bottom: 1px solid var(--bdr);
            background: var(--gxl)
        }

        .filter-bar input {
            padding: 7px 12px;
            border: 1.5px solid var(--bdr);
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            color: var(--txt);
            background: #fff;
            outline: none;
            min-width: 220px;
            transition: border-color .18s
        }

        .filter-bar input:focus {
            border-color: var(--g)
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
            text-align: left
        }

        .dt tbody td {
            padding: 10px 14px;
            border-bottom: 1px solid #edf2ef;
            font-size: 12.5px;
            vertical-align: middle
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
            gap: 5px
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
            max-width: 500px;
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
            overflow-y: auto
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

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 14px
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
            transition: border-color .18s
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
    </style>
@endpush

@section('content')
    <div class="ph">
        <div>
            <h2>Roles</h2>
            <p>Define system roles and assign them to users</p>
        </div>
        <button class="btn btn-primary btn-sm" onclick="openModal()">
            <svg viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Add Role
        </button>
    </div>

    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                <span>All Roles</span>
            </div>
            <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>
        <div class="filter-bar">
            <input type="text" id="fSearch" placeholder="Search roles…" oninput="onFilter()">
        </div>
        <div class="tbl-wrap">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th style="width:120px">Actions</th>
                    </tr>
                </thead>
                <tbody id="roleTbody">
                    <tr>
                        <td colspan="6" class="tbl-state"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Role Form Modal --}}
    <div class="modal-overlay" id="roleModal" onclick="if(event.target===this)closeModal()">
        <div class="modal-box">
            <div class="modal-head">
                <span class="modal-title" id="modalTitle">Add Role</span>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <div id="formAlert" class="form-alert"></div>
                <div class="field">
                    <label>Role Name <span style="color:var(--err)">*</span></label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        <input class="fi" type="text" id="r_name" placeholder="e.g. Supervisor">
                    </div>
                </div>
                <div class="field">
                    <label>Description</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg>
                        <input class="fi" type="text" id="r_desc" placeholder="Short description">
                    </div>
                </div>
                <label
                    style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--txtm);font-weight:600;cursor:pointer">
                    <input type="checkbox" id="r_active" checked style="width:15px;height:15px;accent-color:var(--g)">
                    Active
                </label>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" onclick="saveRole()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg> Save
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let editId = null, filterTimer = null;

        async function loadRoles() {
            document.getElementById('roleTbody').innerHTML = `<tr><td colspan="6" class="tbl-state"><span class="spinner"></span>Loading…</td></tr>`;
            const search = document.getElementById('fSearch').value;
            const params = new URLSearchParams({ per_page: 100 });
            if (search) params.set('search', search);
            const res = await apiFetch(`/admin/roles?${params}`);
            if (!res?.ok) { document.getElementById('roleTbody').innerHTML = `<tr><td colspan="6" class="tbl-state" style="color:var(--err)">Failed to load.</td></tr>`; return; }
            const json = await res.json();
            const rows = json.data?.data ?? [];
            document.getElementById('tableCaption').textContent = `${json.data?.total ?? rows.length} roles`;
            document.getElementById('roleTbody').innerHTML = rows.length ? rows.map(r => `
          <tr>
            <td style="font-weight:700">${esc(r.name)}</td>
            <td><span style="font-family:monospace;font-size:12px;background:var(--gl);padding:2px 7px;border-radius:5px;color:var(--g)">${esc(r.slug)}</span></td>
            <td style="color:var(--txtmu)">${esc(r.description ?? '—')}</td>
            <td><span style="font-weight:700;color:var(--g)">${r.users_count ?? 0}</span></td>
            <td>${r.is_active ? '<span class="badge badge-active">● Active</span>' : '<span class="badge badge-inactive">● Inactive</span>'}</td>
            <td><div class="act-btns">
              <button class="btn btn-outline btn-xs" onclick="openModal(${r.id})">Edit</button>
              <button class="btn btn-danger btn-xs" onclick="deleteRole(${r.id},'${esc(r.name)}')">Delete</button>
            </div></td>
          </tr>
        `).join('') : `<tr><td colspan="6" class="tbl-state">No roles found.</td></tr>`;
        }

        function openModal(id = null) {
            editId = id;
            document.getElementById('r_name').value = '';
            document.getElementById('r_desc').value = '';
            document.getElementById('r_active').checked = true;
            document.getElementById('formAlert').className = 'form-alert';
            document.getElementById('modalTitle').textContent = id ? 'Edit Role' : 'Add Role';
            if (id) {
                const row = document.querySelector(`#roleTbody tr td`);
                // re-fetch for safety
                apiFetch(`/admin/roles?per_page=100`).then(r => r.json()).then(d => {
                    const role = (d.data?.data ?? []).find(r => r.id === id);
                    if (role) {
                        document.getElementById('r_name').value = role.name;
                        document.getElementById('r_desc').value = role.description ?? '';
                        document.getElementById('r_active').checked = !!role.is_active;
                    }
                });
            }
            document.getElementById('roleModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('roleModal').classList.remove('open');
            document.body.style.overflow = '';
            editId = null;
        }

        async function saveRole() {
            const payload = {
                name: document.getElementById('r_name').value,
                description: document.getElementById('r_desc').value,
                is_active: document.getElementById('r_active').checked,
            };
            const method = editId ? 'PUT' : 'POST';
            const endpoint = editId ? `/admin/roles/${editId}` : '/admin/roles';
            const res = await apiFetch(endpoint, { method, body: JSON.stringify(payload) });
            const data = await res.json();
            if (res.ok && data.status === 'ok') { closeModal(); loadRoles(); }
            else {
                const el = document.getElementById('formAlert');
                el.className = 'form-alert error';
                el.textContent = data.message ?? 'Something went wrong.';
            }
        }

        async function deleteRole(id, name) {
            if (!confirm(`Delete role "${name}"?`)) return;
            const res = await apiFetch(`/admin/roles/${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (res.ok) loadRoles();
            else alert(data.message ?? 'Cannot delete.');
        }

        function onFilter() { clearTimeout(filterTimer); filterTimer = setTimeout(loadRoles, 350); }

        function esc(s) {
            if (!s) return '';
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        loadRoles();
    </script>
@endpush