{{--
resources/views/admin/settings/profiles/index.blade.php
Profiles Management — list, create, edit, set default module permissions per profile
--}}
@extends('admin.layouts.app')
@section('title', 'Profiles')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <span style="color:var(--text-muted)">Settings</span>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <strong>Profiles</strong>
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

        .badge-active {
            background: #d1fae5;
            color: #065f46
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b
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

        /* Modals */
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

        /* Permission grid */
        .perm-module {
            background: var(--gxl);
            border: 1px solid var(--bdr);
            border-radius: 9px;
            padding: 10px 14px;
            margin-bottom: 8px
        }

        .perm-module-name {
            font-size: 11px;
            font-weight: 700;
            color: var(--g);
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 8px
        }

        .perm-checks {
            display: flex;
            gap: 16px;
            flex-wrap: wrap
        }

        .perm-check {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            color: var(--txtm);
            cursor: pointer
        }

        .perm-check input[type=checkbox] {
            width: 15px;
            height: 15px;
            accent-color: var(--g);
            cursor: pointer
        }

        .perm-group-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--txtmu);
            margin: 14px 0 6px;
            border-bottom: 1px solid var(--bdr);
            padding-bottom: 5px
        }
    </style>
@endpush

@section('content')
    <div class="ph">
        <div>
            <h2>Profiles</h2>
            <p>Job profiles with default module permissions — assign to users to auto-apply access</p>
        </div>
        <button class="btn btn-primary btn-sm" onclick="openProfileModal()">
            <svg viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Add Profile
        </button>
    </div>

    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span>All Profiles</span>
            </div>
            <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>
        <div class="filter-bar">
            <input type="text" id="fSearch" placeholder="Search profiles…" oninput="onFilter()">
        </div>
        <div class="tbl-wrap">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Profile Name</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th style="width:160px">Actions</th>
                    </tr>
                </thead>
                <tbody id="profileTbody">
                    <tr>
                        <td colspan="5" class="tbl-state"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Profile Form Modal --}}
    <div class="modal-overlay" id="profileModal" onclick="if(event.target===this)closeProfileModal()">
        <div class="modal-box" style="max-width:500px">
            <div class="modal-head">
                <span class="modal-title" id="profileModalTitle">Add Profile</span>
                <button class="modal-close" onclick="closeProfileModal()">✕</button>
            </div>
            <div class="modal-body">
                <div id="profileFormAlert" class="form-alert"></div>
                <div class="field">
                    <label>Profile Name <span style="color:var(--err)">*</span></label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input class="fi" type="text" id="p_name" placeholder="e.g. Acid Tester">
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
                        <input class="fi" type="text" id="p_desc" placeholder="Short description">
                    </div>
                </div>
                <label
                    style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--txtm);font-weight:600;cursor:pointer">
                    <input type="checkbox" id="p_active" checked style="width:15px;height:15px;accent-color:var(--g)">
                    Active
                </label>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeProfileModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" onclick="saveProfile()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg> Save
                </button>
            </div>
        </div>
    </div>

    {{-- Profile Permissions Modal --}}
    <div class="modal-overlay" id="permModal" onclick="if(event.target===this)closePermModal()">
        <div class="modal-box" style="max-width:700px">
            <div class="modal-head">
                <span class="modal-title" id="permModalTitle">Default Permissions</span>
                <button class="modal-close" onclick="closePermModal()">✕</button>
            </div>
            <div class="modal-body">
                <div id="permAlert" class="form-alert"></div>
                <p style="font-size:12px;color:var(--txtmu);margin-bottom:14px">
                    Set the default module permissions for this profile. When this profile is assigned to a user, these
                    permissions will be auto-applied.
                </p>
                <div style="display:flex;gap:8px;margin-bottom:14px">
                    <button class="btn btn-outline btn-xs" onclick="checkAllPerm(true)">Check All</button>
                    <button class="btn btn-outline btn-xs" onclick="checkAllPerm(false)">Uncheck All</button>
                </div>
                <div id="permGrid"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closePermModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" onclick="saveProfilePermissions()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg> Save Permissions
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let editProfileId = null, permProfileId = null, filterTimer = null;
        let allModules = [];

        async function init() {
            const res = await apiFetch('/admin/modules');
            if (res?.ok) { const d = await res.json(); allModules = d.data ?? []; }
            loadProfiles();
        }
        init();

        async function loadProfiles() {
            document.getElementById('profileTbody').innerHTML = `<tr><td colspan="5" class="tbl-state"><span class="spinner"></span>Loading…</td></tr>`;
            const params = new URLSearchParams({ per_page: 100 });
            const s = document.getElementById('fSearch').value;
            if (s) params.set('search', s);
            const res = await apiFetch(`/admin/profiles?${params}`);
            if (!res?.ok) return;
            const json = await res.json();
            const rows = json.data?.data ?? [];
            document.getElementById('tableCaption').textContent = `${json.data?.total ?? rows.length} profiles`;
            document.getElementById('profileTbody').innerHTML = rows.length ? rows.map(p => `
          <tr>
            <td style="font-weight:700">${esc(p.name)}</td>
            <td style="color:var(--txtmu)">${esc(p.description ?? '—')}</td>
            <td><span style="font-weight:700;color:var(--g)">${p.users_count ?? 0}</span></td>
            <td>${p.is_active ? '<span class="badge badge-active">● Active</span>' : '<span class="badge badge-inactive">● Inactive</span>'}</td>
            <td><div class="act-btns">
              <button class="btn btn-outline btn-xs" onclick="openProfileModal(${p.id})">Edit</button>
              <button class="btn btn-outline btn-xs" onclick="openPermModal(${p.id},'${esc(p.name)}')" style="color:var(--g)">Permissions</button>
              <button class="btn btn-danger btn-xs" onclick="deleteProfile(${p.id},'${esc(p.name)}')">Delete</button>
            </div></td>
          </tr>
        `).join('') : `<tr><td colspan="5" class="tbl-state">No profiles found.</td></tr>`;
        }

        function openProfileModal(id = null) {
            editProfileId = id;
            document.getElementById('p_name').value = '';
            document.getElementById('p_desc').value = '';
            document.getElementById('p_active').checked = true;
            document.getElementById('profileFormAlert').className = 'form-alert';
            document.getElementById('profileModalTitle').textContent = id ? 'Edit Profile' : 'Add Profile';
            if (id) {
                apiFetch(`/admin/profiles/${id}`).then(r => r.json()).then(d => {
                    const p = d.data;
                    document.getElementById('p_name').value = p.name ?? '';
                    document.getElementById('p_desc').value = p.description ?? '';
                    document.getElementById('p_active').checked = !!p.is_active;
                });
            }
            document.getElementById('profileModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').classList.remove('open');
            document.body.style.overflow = '';
            editProfileId = null;
        }

        async function saveProfile() {
            const payload = { name: document.getElementById('p_name').value, description: document.getElementById('p_desc').value, is_active: document.getElementById('p_active').checked };
            const method = editProfileId ? 'PUT' : 'POST';
            const endpoint = editProfileId ? `/admin/profiles/${editProfileId}` : '/admin/profiles';
            const res = await apiFetch(endpoint, { method, body: JSON.stringify(payload) });
            const data = await res.json();
            if (res.ok && data.status === 'ok') { closeProfileModal(); loadProfiles(); }
            else { const el = document.getElementById('profileFormAlert'); el.className = 'form-alert error'; el.textContent = data.message ?? 'Error.'; }
        }

        async function deleteProfile(id, name) {
            if (!confirm(`Delete profile "${name}"?`)) return;
            const res = await apiFetch(`/admin/profiles/${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (res.ok) loadProfiles();
            else alert(data.message ?? 'Cannot delete.');
        }

        // ── Profile Permissions Modal ──────────────────────────────────
        async function openPermModal(id, name) {
            permProfileId = id;
            document.getElementById('permModalTitle').textContent = `Permissions — ${name}`;
            document.getElementById('permAlert').className = 'form-alert';
            document.getElementById('permGrid').innerHTML = `<div class="tbl-state"><span class="spinner"></span>Loading…</div>`;
            document.getElementById('permModal').classList.add('open');
            document.body.style.overflow = 'hidden';

            const res = await apiFetch(`/admin/profiles/${id}`);
            if (!res?.ok) return;
            const { data: profile } = await res.json();
            const perms = profile.module_permissions ?? [];
            const permMap = {};
            perms.forEach(p => { permMap[p.module_id] = p; });
            renderPermGrid(permMap);
        }

        function closePermModal() {
            document.getElementById('permModal').classList.remove('open');
            document.body.style.overflow = '';
            permProfileId = null;
        }

        function renderPermGrid(permMap) {
            const groups = {};
            allModules.forEach(m => {
                const g = m.group ?? 'Other';
                if (!groups[g]) groups[g] = [];
                groups[g].push(m);
            });
            document.getElementById('permGrid').innerHTML = Object.entries(groups).map(([group, mods]) => `
          <div class="perm-group-label">${esc(group)}</div>
          ${mods.map(m => {
                const p = permMap[m.id] ?? {};
                return `<div class="perm-module">
              <div class="perm-module-name">${esc(m.name)}</div>
              <div class="perm-checks">
                ${['view', 'create', 'edit', 'delete'].map(act => `
                  <label class="perm-check">
                    <input type="checkbox" data-mid="${m.id}" data-act="${act}"
                      ${p[`can_${act}`] ? 'checked' : ''}>
                    ${act.charAt(0).toUpperCase() + act.slice(1)}
                  </label>
                `).join('')}
              </div>
            </div>`;
            }).join('')}
        `).join('');
        }

        function checkAllPerm(val) {
            document.querySelectorAll('#permGrid input[type=checkbox]').forEach(cb => cb.checked = val);
        }

        async function saveProfilePermissions() {
            const moduleMap = {};
            document.querySelectorAll('#permGrid input[type=checkbox]').forEach(cb => {
                const mid = +cb.dataset.mid, act = cb.dataset.act;
                if (!moduleMap[mid]) moduleMap[mid] = { module_id: mid, can_view: false, can_create: false, can_edit: false, can_delete: false };
                moduleMap[mid][`can_${act}`] = cb.checked;
            });
            const permissions = Object.values(moduleMap);
            const res = await apiFetch(`/admin/profiles/${permProfileId}/permissions`, { method: 'PUT', body: JSON.stringify({ permissions }) });
            const data = await res.json();
            if (res.ok && data.status === 'ok') { closePermModal(); }
            else { const el = document.getElementById('permAlert'); el.className = 'form-alert error'; el.textContent = data.message ?? 'Error.'; }
        }

        function onFilter() { clearTimeout(filterTimer); filterTimer = setTimeout(loadProfiles, 350); }
        function esc(s) { if (!s) return ''; return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    </script>
@endpush