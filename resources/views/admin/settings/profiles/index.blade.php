{{-- resources/views/admin/settings/profiles/index.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Profiles')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span><span style="color:var(--text-muted)">Settings</span>
    <span style="margin:0 6px;color:var(--border)">/</span><strong>Profiles</strong>
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

        .info-banner {
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 12px;
            line-height: 1.5;
            border: 1px solid
        }

        .info-blue {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af
        }
    </style>
@endpush

@section('content')
    <div class="ph">
        <div>
            <h2>Profiles</h2>
            <p>Job profiles with default module permissions — assign to users to auto-apply access</p>
        </div>
        <button class="btn btn-primary btn-sm" id="btnAddProfile" onclick="openProfileModal()">
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
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <span>All Profiles</span>
            </div>
            <span id="caption" style="font-size:11.5px;color:var(--txtmu)"></span>
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
                        <th style="width:190px">Actions</th>
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

    {{-- PROFILE FORM MODAL --}}
    <div class="modal-overlay" id="profileModal" onclick="if(event.target===this)closeModal('profileModal')">
        <div class="modal-box" style="max-width:480px">
            <div class="modal-head">
                <span class="modal-title" id="profileModalTitle">Add Profile</span>
                <button class="modal-close" onclick="closeModal('profileModal')">✕</button>
            </div>
            <div class="modal-body">
                <div id="profileAlert" class="form-alert"></div>
                <div class="field">
                    <label>Profile Name <span style="color:var(--err)">*</span></label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input class="fi" type="text" id="p_name" placeholder="e.g. Receiver">
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
                <button class="btn btn-outline btn-sm" onclick="closeModal('profileModal')">Cancel</button>
                <button class="btn btn-primary btn-sm" id="profileSaveBtn" onclick="saveProfile()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg> Save Profile
                </button>
            </div>
        </div>
    </div>

    {{-- PERMISSIONS MODAL --}}
    <div class="modal-overlay" id="permModal" onclick="if(event.target===this)closeModal('permModal')">
        <div class="modal-box" style="max-width:700px">
            <div class="modal-head">
                <span class="modal-title" id="permTitle">Permissions</span>
                <button class="modal-close" onclick="closeModal('permModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="info-banner info-blue" style="margin-bottom:14px">
                    Set the default module permissions for this profile. When this profile is assigned to a user,
                    these permissions will be auto-applied.
                </div>
                <div id="permAlert" class="form-alert"></div>
                <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
                    <button class="btn btn-outline btn-xs" onclick="checkAll(true)">Check All</button>
                    <button class="btn btn-outline btn-xs" onclick="checkAll(false)">Uncheck All</button>
                </div>
                <div id="permGrid"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeModal('permModal')">Cancel</button>
                <button class="btn btn-primary btn-sm" id="permSaveBtn" onclick="savePermissions()">
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
        let allProfiles = [], allModules = [];

        (async function init() {
            if (!can('settings_profiles', 'can_create')) document.getElementById('btnAddProfile').style.display = 'none';
            await loadModules();
            loadProfiles();
        })();

        async function loadModules() {
            try {
                const r = await apiFetch('/admin/modules');
                if (r?.ok) { const d = await r.json(); allModules = Array.isArray(d.data) ? d.data : []; }
            } catch (e) { }
        }

        async function loadProfiles() {
            document.getElementById('profileTbody').innerHTML = '<tr><td colspan="5" class="tbl-state"><span class="spinner"></span>Loading…</td></tr>';
            const s = document.getElementById('fSearch').value.trim();
            const p = new URLSearchParams({ per_page: 200 });
            if (s) p.set('search', s);
            const res = await apiFetch('/admin/profiles?' + p);
            if (!res?.ok) { document.getElementById('profileTbody').innerHTML = '<tr><td colspan="5" class="tbl-state" style="color:var(--err)">Failed to load.</td></tr>'; return; }
            const json = await res.json();
            allProfiles = json.data?.data ?? [];
            document.getElementById('caption').textContent = allProfiles.length + ' profiles';
            renderProfiles(allProfiles);
        }

        function renderProfiles(rows) {
            const canEdit = can('settings_profiles', 'can_edit'), canDel = can('settings_profiles', 'can_delete');
            document.getElementById('profileTbody').innerHTML = rows.length ? rows.map(p => {
                const eb = canEdit ? '<button class="btn btn-outline btn-xs" onclick="openProfileModal(' + p.id + ')">Edit</button>' : '';
                const permb = canEdit ? '<button class="btn btn-outline btn-xs" onclick="openPermModal(' + p.id + ',\'' + esc(p.name) + '\')" style="color:var(--g)">Permissions</button>' : '';
                const db = canDel ? '<button class="btn btn-danger btn-xs" onclick="delProfile(' + p.id + ',\'' + esc(p.name) + '\')">Delete</button>' : '';
                return '<tr>'
                    + '<td style="font-weight:700">' + esc(p.name) + '</td>'
                    + '<td style="color:var(--txtmu)">' + esc(p.description || '—') + '</td>'
                    + '<td><span style="font-weight:700;color:var(--g)">' + (p.users_count ?? 0) + '</span></td>'
                    + '<td>' + (p.is_active ? '<span class="badge badge-active">● Active</span>' : '<span class="badge badge-inactive">● Inactive</span>') + '</td>'
                    + '<td><div class="act-btns">' + eb + permb + db + '</div></td>'
                    + '</tr>';
            }).join('') : '<tr><td colspan="5" class="tbl-state">No profiles found.</td></tr>';
        }

        // ── Profile CRUD modal ─────────────────────────────────────────
        function openProfileModal(id = null) {
            editProfileId = id;
            document.getElementById('profileAlert').className = 'form-alert';
            document.getElementById('profileModalTitle').textContent = id ? 'Edit Profile' : 'Add Profile';
            if (id) {
                const prof = allProfiles.find(p => p.id === id);
                document.getElementById('p_name').value = prof?.name ?? '';
                document.getElementById('p_desc').value = prof?.description ?? '';
                document.getElementById('p_active').checked = prof?.is_active ?? true;
            } else {
                document.getElementById('p_name').value = '';
                document.getElementById('p_desc').value = '';
                document.getElementById('p_active').checked = true;
            }
            openModal('profileModal');
            setTimeout(() => document.getElementById('p_name').focus(), 100);
        }

        async function saveProfile() {
            const btn = document.getElementById('profileSaveBtn');
            btn.disabled = true; btn.textContent = 'Saving…';
            const payload = {
                name: document.getElementById('p_name').value.trim(),
                description: document.getElementById('p_desc').value.trim(),
                is_active: document.getElementById('p_active').checked,
            };
            const res = await apiFetch(editProfileId ? '/admin/profiles/' + editProfileId : '/admin/profiles', {
                method: editProfileId ? 'PUT' : 'POST', body: JSON.stringify(payload)
            });
            const data = await res.json();
            btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2"><polyline points="20 6 9 17 4 12"/></svg> Save Profile';
            if (res.ok && data.status === 'ok') { closeModal('profileModal'); loadProfiles(); showToast(editProfileId ? 'Profile updated.' : 'Profile created.'); }
            else { const e = document.getElementById('profileAlert'); e.className = 'form-alert error'; e.textContent = data.message ?? 'Something went wrong.'; }
        }

        async function delProfile(id, name) {
            if (!await showConfirm('Delete profile "' + name + '"? This cannot be undone.')) return;
            const res = await apiFetch('/admin/profiles/' + id, { method: 'DELETE' });
            const data = await res.json();
            if (res.ok) { loadProfiles(); showToast('Profile deleted.'); }
            else alert(data.message ?? 'Cannot delete — profile may have assigned users.');
        }

        // ── Permissions modal ──────────────────────────────────────────
        async function openPermModal(profileId, name) {
            permProfileId = profileId;
            document.getElementById('permTitle').textContent = 'Permissions — ' + esc(name);
            document.getElementById('permAlert').className = 'form-alert';
            renderPermGrid([]);
            openModal('permModal');

            // Load existing permissions for this profile
            const res = await apiFetch('/admin/profiles/' + profileId);
            if (!res?.ok) return;
            const { data } = await res.json();
            const perms = data?.module_permissions ?? [];
            renderPermGrid(perms);
        }

        function renderPermGrid(perms) {
            // Build map: module_id → perm row
            const map = {};
            perms.forEach(p => { map[p.module_id] = p; });

            // Group modules by group
            const groups = {};
            allModules.forEach(m => { const g = m.group || 'Other'; if (!groups[g]) groups[g] = []; groups[g].push(m); });

            const grid = document.getElementById('permGrid');
            if (!allModules.length) {
                grid.innerHTML = '<div style="text-align:center;padding:24px;color:var(--txtmu)">No modules configured. Go to Settings → Modules.</div>';
                return;
            }
            grid.innerHTML = Object.entries(groups).map(([g, mods]) =>
                '<div class="perm-group-label">' + esc(g) + '</div>'
                + mods.map(m => {
                    const p = map[m.id] || {};
                    return '<div class="perm-module"><div class="perm-module-name">' + esc(m.name) + '</div><div class="perm-checks">'
                        + ['view', 'create', 'edit', 'delete'].map(a =>
                            '<label class="perm-check"><input type="checkbox" data-mid="' + m.id + '" data-act="' + a + '" ' + (p['can_' + a] ? 'checked' : '') + '>' + a.charAt(0).toUpperCase() + a.slice(1) + '</label>'
                        ).join('') + '</div></div>';
                }).join('')
            ).join('');
        }

        function checkAll(v) { document.querySelectorAll('#permGrid input[type=checkbox]').forEach(c => c.checked = v); }

        async function savePermissions() {
            const btn = document.getElementById('permSaveBtn');
            btn.disabled = true; btn.textContent = 'Saving…';
            const modMap = {};
            document.querySelectorAll('#permGrid input[type=checkbox]').forEach(c => {
                const mid = +c.dataset.mid, act = c.dataset.act;
                if (!modMap[mid]) modMap[mid] = { module_id: mid, can_view: false, can_create: false, can_edit: false, can_delete: false };
                modMap[mid]['can_' + act] = c.checked;
            });
            const res = await apiFetch('/admin/profiles/' + permProfileId + '/permissions', {
                method: 'PUT', body: JSON.stringify({ permissions: Object.values(modMap) })
            });
            const data = await res.json();
            btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2"><polyline points="20 6 9 17 4 12"/></svg> Save Permissions';
            if (res.ok && data.status === 'ok') { closeModal('permModal'); showToast('Profile permissions saved and applied to assigned users.'); }
            else { const e = document.getElementById('permAlert'); e.className = 'form-alert error'; e.textContent = data.message ?? 'Failed to save.'; }
        }

        // ── Helpers ────────────────────────────────────────────────────
        function onFilter() { clearTimeout(filterTimer); filterTimer = setTimeout(loadProfiles, 350); }
        function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
        function esc(s) { return s == null ? '' : String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
    </script>
@endpush