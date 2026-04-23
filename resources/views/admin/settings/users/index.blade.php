{{--
resources/views/admin/settings/users/index.blade.php
User Management — list, create, edit, permissions, status toggle
--}}
@extends('admin.layouts.app')
@section('title', 'User Management')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none">Dashboard</a>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <span style="color:var(--text-muted)">Settings</span>
    <span style="margin:0 6px;color:var(--border)">/</span>
    <strong>Users</strong>
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* ── Page header ── */
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

        /* ── Buttons ── */
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

        /* ── Card ── */
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

        /* ── Filter bar ── */
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
            min-width: 220px
        }

        /* ── Table ── */
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

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700
        }

        .badge-admin {
            background: #ede9fe;
            color: #5b21b6
        }

        .badge-management {
            background: #fef3c7;
            color: #92400e
        }

        .badge-normal {
            background: #e0f2fe;
            color: #075985
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b
        }

        /* ── Avatar ── */
        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--gl);
            border: 2px solid var(--bdr);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            color: var(--g);
            flex-shrink: 0;
            text-transform: uppercase
        }

        /* ── Actions ── */
        .act-btns {
            display: flex;
            gap: 5px;
            align-items: center
        }

        /* ── Empty / loading ── */
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

        /* ── Pagination ── */
        .pag {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 18px;
            border-top: 1px solid var(--bdr);
            flex-wrap: wrap;
            gap: 8px
        }

        .pag-info {
            font-size: 12px;
            color: var(--txtmu)
        }

        .pag-btns {
            display: flex;
            gap: 4px
        }

        .pag-btn {
            padding: 5px 11px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1.5px solid var(--bdr);
            background: #fff;
            color: var(--txtm);
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: all .15s
        }

        .pag-btn:hover:not(:disabled) {
            border-color: var(--g);
            color: var(--g)
        }

        .pag-btn.active {
            background: var(--g);
            color: #fff;
            border-color: var(--g)
        }

        .pag-btn:disabled {
            opacity: .4;
            cursor: default
        }

        /* ── Modal ── */
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

        /* ── Form ── */
        .fg2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 20px
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

        .fi-err {
            border-color: var(--err) !important
        }

        .err-msg {
            font-size: 11px;
            color: var(--err);
            min-height: 14px
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

        .form-alert.success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            display: block
        }

        /* ── Permission grid ── */
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

        .chip-multi {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 4px
        }

        .chip-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: var(--gl);
            color: var(--g);
            border: 1px solid var(--bdr)
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
            <h2>User Management</h2>
            <p>Manage system users, roles, profiles and module permissions</p>
        </div>
        <button class="btn btn-primary btn-sm" onclick="openUserModal()">
            <svg viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Add User
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
                <span>All Users</span>
            </div>
            <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>

        <div class="filter-bar">
            <input type="text" id="fSearch" placeholder="Search name, email, username…" oninput="onFilter()">
            <select id="fRole" onchange="onFilter()">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="management">Management</option>
                <option value="normal">Normal</option>
            </select>
            <select id="fStatus" onchange="onFilter()">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <div class="tbl-wrap">
            <table class="dt">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Profiles</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th style="width:150px">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTbody">
                    <tr>
                        <td colspan="8" class="tbl-state"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pag" id="pagBar" style="display:none">
            <span class="pag-info" id="pagInfo"></span>
            <div class="pag-btns" id="pagBtns"></div>
        </div>
    </div>

    {{-- ═══ USER FORM MODAL ══════════════════════════════════════════ --}}
    <div class="modal-overlay" id="userModal" onclick="onModalOverlayClick(event,'userModal')">
        <div class="modal-box" style="max-width:680px">
            <div class="modal-head">
                <span class="modal-title" id="userModalTitle">Add User</span>
                <button class="modal-close" onclick="closeModal('userModal')">✕</button>
            </div>
            <div class="modal-body">
                <div id="userFormAlert" class="form-alert"></div>
                <div class="fg2">

                    <div class="field">
                        <label>Full Name <span class="req">*</span></label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            <input class="fi" type="text" id="u_name" placeholder="John Doe">
                        </div>
                    </div>

                    <div class="field">
                        <label>Username <span class="req">*</span></label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            <input class="fi" type="text" id="u_username" placeholder="johndoe">
                        </div>
                    </div>

                    <div class="field">
                        <label>Email <span class="req">*</span></label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            <input class="fi" type="email" id="u_email" placeholder="john@example.com">
                        </div>
                    </div>

                    <div class="field">
                        <label>System Role <span class="req">*</span></label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <select class="fi" id="u_role" style="padding-left:32px">
                                <option value="">Select role…</option>
                                <option value="admin">Admin</option>
                                <option value="management">Management</option>
                                <option value="normal">Normal</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label>Password <span class="req" id="pwReqMark">*</span> <span id="pwHint"
                                style="font-size:10px;color:var(--txtmu);font-weight:400;text-transform:none"></span></label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input class="fi" type="password" id="u_password" placeholder="Min 8 characters">
                        </div>
                    </div>

                    <div class="field">
                        <label>Confirm Password</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input class="fi" type="password" id="u_password_confirmation" placeholder="Repeat password">
                        </div>
                    </div>

                    <div class="field">
                        <label>Department</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                            <input class="fi" type="text" id="u_department" placeholder="e.g. Operations">
                        </div>
                    </div>

                    <div class="field">
                        <label>Phone</label>
                        <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 17.08z" />
                            </svg>
                            <input class="fi" type="text" id="u_phone" placeholder="+971 50 000 0000">
                        </div>
                    </div>

                </div>

                {{-- Roles (multi-select) --}}
                <div style="margin-top:16px">
                    <div
                        style="font-size:10.5px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--txtm);margin-bottom:8px">
                        Assign Roles</div>
                    <div id="roleCheckboxes" style="display:flex;flex-wrap:wrap;gap:8px"></div>
                </div>

                {{-- Profiles (multi-select) --}}
                <div style="margin-top:14px">
                    <div
                        style="font-size:10.5px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--txtm);margin-bottom:8px">
                        Assign Profiles
                        <span style="font-size:10px;font-weight:400;text-transform:none;color:var(--txtmu)">— permissions
                            will be auto-applied from selected profiles</span>
                    </div>
                    <div id="profileCheckboxes" style="display:flex;flex-wrap:wrap;gap:8px"></div>
                </div>

                {{-- Active toggle --}}
                <div style="margin-top:14px;display:flex;align-items:center;gap:10px">
                    <label
                        style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--txtm);font-weight:600">
                        <input type="checkbox" id="u_is_active" checked
                            style="width:16px;height:16px;accent-color:var(--g)">
                        Active Account
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeModal('userModal')">Cancel</button>
                <button class="btn btn-primary btn-sm" id="userSaveBtn" onclick="saveUser()">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    Save User
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ PERMISSIONS MODAL ══════════════════════════════════════════ --}}
    <div class="modal-overlay" id="permModal" onclick="onModalOverlayClick(event,'permModal')">
        <div class="modal-box" style="max-width:700px">
            <div class="modal-head">
                <span class="modal-title" id="permModalTitle">Module Permissions</span>
                <button class="modal-close" onclick="closeModal('permModal')">✕</button>
            </div>
            <div class="modal-body">
                <div id="permAlert" class="form-alert"></div>
                <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
                    <button class="btn btn-outline btn-xs" onclick="checkAll(true)">Check All</button>
                    <button class="btn btn-outline btn-xs" onclick="checkAll(false)">Uncheck All</button>
                </div>
                <div id="permGrid"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline btn-sm" onclick="closeModal('permModal')">Cancel</button>
                <button class="btn btn-primary btn-sm" onclick="savePermissions()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Save Permissions
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ── State ──────────────────────────────────────────────────────
        let currentPage = 1, filterTimer = null;
        let editingUserId = null, permUserId = null;
        let allRoles = [], allProfiles = [], allModules = [];

        // ── Init ───────────────────────────────────────────────────────
        async function init() {
            await Promise.all([loadRoles(), loadProfiles(), loadModules()]);
            loadUsers();
        }
        init();

        // ── Load dropdowns ─────────────────────────────────────────────
        async function loadRoles() {
            const res = await apiFetch('/admin/roles?per_page=100');
            if (res?.ok) { const d = await res.json(); allRoles = d.data?.data ?? []; }
        }
        async function loadProfiles() {
            const res = await apiFetch('/admin/profiles?per_page=100');
            if (res?.ok) { const d = await res.json(); allProfiles = d.data?.data ?? []; }
        }
        async function loadModules() {
            const res = await apiFetch('/admin/modules');
            if (res?.ok) { const d = await res.json(); allModules = d.data ?? []; }
        }

        // ── Load users ─────────────────────────────────────────────────
        async function loadUsers(page = 1) {
            currentPage = page;
            setTbodyLoading('userTbody', 8);
            const params = new URLSearchParams({
                search: document.getElementById('fSearch').value,
                role: document.getElementById('fRole').value,
                is_active: document.getElementById('fStatus').value,
                per_page: 20, page,
            });
            [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
            const res = await apiFetch(`/admin/users?${params}`);
            if (!res?.ok) { setTbodyError('userTbody', 8); return; }
            const json = await res.json();
            renderUsers(json.data?.data ?? []);
            renderPag(json.data, loadUsers);
            document.getElementById('tableCaption').textContent =
                `${json.data?.total ?? 0} users`;
        }

        function renderUsers(rows) {
            const tbody = document.getElementById('userTbody');
            if (!rows.length) {
                tbody.innerHTML = `<tr><td colspan="8" class="tbl-state">No users found.</td></tr>`;
                return;
            }
            tbody.innerHTML = rows.map(u => {
                const initials = u.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
                const roleBadge = `<span class="badge badge-${u.role}">${u.role}</span>`;
                const statusBadge = u.is_active
                    ? `<span class="badge badge-active">● Active</span>`
                    : `<span class="badge badge-inactive">● Inactive</span>`;
                const profiles = (u.profiles ?? []).map(p =>
                    `<span class="chip-tag">${esc(p.name)}</span>`).join('') || '—';
                const lastLogin = u.last_login_at
                    ? new Date(u.last_login_at).toLocaleDateString()
                    : '—';
                return `<tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="avatar">${initials}</div>
                <div>
                  <div style="font-weight:700;font-size:13px">${esc(u.name)}</div>
                  <div style="font-size:11px;color:var(--txtmu)">${esc(u.email)}</div>
                </div>
              </div>
            </td>
            <td><span style="font-family:monospace;font-size:12px;background:var(--gl);padding:2px 7px;border-radius:5px;color:var(--g)">${esc(u.username)}</span></td>
            <td>${roleBadge}</td>
            <td><div class="chip-multi">${profiles}</div></td>
            <td>${esc(u.department ?? '—')}</td>
            <td>${statusBadge}</td>
            <td style="font-size:11.5px;color:var(--txtmu)">${lastLogin}</td>
            <td>
              <div class="act-btns">
                <button class="btn btn-outline btn-xs" onclick="openUserModal(${u.id})" title="Edit">
                  <svg viewBox="0 0 24 24" style="width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button class="btn btn-outline btn-xs" onclick="openPermModal(${u.id},'${esc(u.name)}')" title="Permissions" style="color:var(--g)">
                  <svg viewBox="0 0 24 24" style="width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </button>
                <button class="btn btn-xs ${u.is_active ? 'btn-danger' : 'btn-outline'}" onclick="toggleStatus(${u.id})" title="${u.is_active ? 'Disable' : 'Enable'}">
                  <svg viewBox="0 0 24 24" style="width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                </button>
                <button class="btn btn-danger btn-xs" onclick="deleteUser(${u.id},'${esc(u.name)}')" title="Delete">
                  <svg viewBox="0 0 24 24" style="width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button>
              </div>
            </td>
          </tr>`;
            }).join('');
        }

        // ── User form modal ─────────────────────────────────────────────
        async function openUserModal(userId = null) {
            editingUserId = userId;
            resetUserForm();
            document.getElementById('userModalTitle').textContent = userId ? 'Edit User' : 'Add User';
            document.getElementById('pwHint').textContent = userId ? '(leave blank to keep current)' : '';
            document.getElementById('pwReqMark').style.display = userId ? 'none' : '';

            buildCheckboxes('roleCheckboxes', allRoles, 'role');
            buildCheckboxes('profileCheckboxes', allProfiles, 'profile');

            if (userId) {
                const res = await apiFetch(`/admin/users/${userId}`);
                if (!res?.ok) return;
                const { data: u } = await res.json();
                document.getElementById('u_name').value = u.name ?? '';
                document.getElementById('u_username').value = u.username ?? '';
                document.getElementById('u_email').value = u.email ?? '';
                document.getElementById('u_role').value = u.role ?? '';
                document.getElementById('u_department').value = u.department ?? '';
                document.getElementById('u_phone').value = u.phone ?? '';
                document.getElementById('u_is_active').checked = !!u.is_active;
                // Tick assigned roles
                (u.roles ?? []).forEach(r => {
                    const cb = document.getElementById(`chk_role_${r.id}`);
                    if (cb) cb.checked = true;
                });
                (u.profiles ?? []).forEach(p => {
                    const cb = document.getElementById(`chk_profile_${p.id}`);
                    if (cb) cb.checked = true;
                });
            }

            openModal('userModal');
        }

        function buildCheckboxes(containerId, items, type) {
            const div = document.getElementById(containerId);
            div.innerHTML = items.map(item => `
          <label style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border:1.5px solid var(--bdr);border-radius:8px;font-size:12.5px;font-weight:600;color:var(--txtm);cursor:pointer;background:var(--gxl);transition:all .15s"
            onmouseenter="this.style.borderColor='var(--g)'" onmouseleave="this.style.borderColor='var(--bdr)'">
            <input type="checkbox" id="chk_${type}_${item.id}" value="${item.id}"
              style="width:14px;height:14px;accent-color:var(--g)">
            ${esc(item.name)}
          </label>
        `).join('');
        }

        function resetUserForm() {
            ['u_name', 'u_username', 'u_email', 'u_department', 'u_phone', 'u_password', 'u_password_confirmation']
                .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
            document.getElementById('u_role').value = '';
            document.getElementById('u_is_active').checked = true;
            document.getElementById('userFormAlert').className = 'form-alert';
            document.getElementById('userFormAlert').textContent = '';
        }

        async function saveUser() {
            const btn = document.getElementById('userSaveBtn');
            btn.disabled = true;

            const roleIds = [...document.querySelectorAll('#roleCheckboxes input:checked')].map(cb => +cb.value);
            const profileIds = [...document.querySelectorAll('#profileCheckboxes input:checked')].map(cb => +cb.value);

            const payload = {
                name: document.getElementById('u_name').value,
                username: document.getElementById('u_username').value,
                email: document.getElementById('u_email').value,
                role: document.getElementById('u_role').value,
                department: document.getElementById('u_department').value,
                phone: document.getElementById('u_phone').value,
                is_active: document.getElementById('u_is_active').checked,
                role_ids: roleIds,
                profile_ids: profileIds,
                password: document.getElementById('u_password').value || undefined,
                password_confirmation: document.getElementById('u_password_confirmation').value || undefined,
            };
            if (!payload.password) { delete payload.password; delete payload.password_confirmation; }

            const method = editingUserId ? 'PUT' : 'POST';
            const endpoint = editingUserId ? `/admin/users/${editingUserId}` : '/admin/users';
            const res = await apiFetch(endpoint, { method, body: JSON.stringify(payload) });
            btn.disabled = false;

            const data = await res.json();
            if (res.ok && data.status === 'ok') {
                closeModal('userModal');
                loadUsers(currentPage);
            } else {
                const alertEl = document.getElementById('userFormAlert');
                alertEl.className = 'form-alert error';
                alertEl.textContent = data.message ?? 'Something went wrong.';
            }
        }

        async function toggleStatus(userId) {
            const res = await apiFetch(`/admin/users/${userId}/toggle-status`, { method: 'PATCH', body: '{}' });
            if (res?.ok) loadUsers(currentPage);
        }

        async function deleteUser(userId, name) {
            if (!confirm(`Delete user "${name}"? This cannot be undone.`)) return;
            const res = await apiFetch(`/admin/users/${userId}`, { method: 'DELETE' });
            if (res?.ok) loadUsers(currentPage);
        }

        // ── Permissions modal ──────────────────────────────────────────
        async function openPermModal(userId, userName) {
            permUserId = userId;
            document.getElementById('permModalTitle').textContent = `Permissions — ${userName}`;
            document.getElementById('permAlert').className = 'form-alert';
            renderPermGrid([]);
            openModal('permModal');

            const res = await apiFetch(`/admin/users/${userId}/permissions`);
            if (!res?.ok) return;
            const { data } = await res.json();

            // data is array of UserModulePermission or {full_access: true}
            const perms = Array.isArray(data) ? data : [];
            renderPermGrid(perms);
        }

        function renderPermGrid(perms) {
            const permMap = {};
            perms.forEach(p => { permMap[p.module_id] = p; });

            // Group modules by group
            const groups = {};
            allModules.forEach(m => {
                if (!groups[m.group ?? 'Other']) groups[m.group ?? 'Other'] = [];
                groups[m.group ?? 'Other'].push(m);
            });

            const grid = document.getElementById('permGrid');
            grid.innerHTML = Object.entries(groups).map(([group, mods]) => `
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

        function checkAll(val) {
            document.querySelectorAll('#permGrid input[type=checkbox]').forEach(cb => cb.checked = val);
        }

        async function savePermissions() {
            const permissions = [];
            const moduleMap = {};
            document.querySelectorAll('#permGrid input[type=checkbox]').forEach(cb => {
                const mid = +cb.dataset.mid, act = cb.dataset.act;
                if (!moduleMap[mid]) { moduleMap[mid] = { module_id: mid, can_view: false, can_create: false, can_edit: false, can_delete: false }; }
                moduleMap[mid][`can_${act}`] = cb.checked;
            });
            Object.values(moduleMap).forEach(p => permissions.push(p));

            const res = await apiFetch(`/admin/users/${permUserId}/permissions`, {
                method: 'PUT', body: JSON.stringify({ permissions }),
            });
            const data = await res.json();
            if (res.ok && data.status === 'ok') {
                closeModal('permModal');
            } else {
                const el = document.getElementById('permAlert');
                el.className = 'form-alert error';
                el.textContent = data.message ?? 'Failed to save.';
            }
        }

        // ── Filter ─────────────────────────────────────────────────────
        function onFilter() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(() => loadUsers(1), 350);
        }

        // ── Modal helpers ───────────────────────────────────────────────
        function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
        function onModalOverlayClick(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }

        // ── Table helpers ───────────────────────────────────────────────
        function setTbodyLoading(id, cols) {
            document.getElementById(id).innerHTML = `<tr><td colspan="${cols}" class="tbl-state"><span class="spinner"></span>Loading…</td></tr>`;
        }
        function setTbodyError(id, cols) {
            document.getElementById(id).innerHTML = `<tr><td colspan="${cols}" class="tbl-state" style="color:var(--err)">Failed to load. Please refresh.</td></tr>`;
        }

        // ── Pagination ─────────────────────────────────────────────────
        function renderPag(meta, loadFn) {
            const bar = document.getElementById('pagBar');
            if (!meta || meta.last_page <= 1) { bar.style.display = 'none'; return; }
            bar.style.display = 'flex';
            const from = (meta.current_page - 1) * meta.per_page + 1;
            const to = Math.min(meta.current_page * meta.per_page, meta.total);
            document.getElementById('pagInfo').textContent = `${from}–${to} of ${meta.total}`;
            const pages = pagRange(meta.current_page, meta.last_page);
            document.getElementById('pagBtns').innerHTML = [
                `<button class="pag-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="loadUsers(${meta.current_page - 1})">‹</button>`,
                ...pages.map(p => p === '…' ? `<button class="pag-btn" disabled>…</button>`
                    : `<button class="pag-btn${p === meta.current_page ? ' active' : ''}" onclick="loadUsers(${p})">${p}</button>`),
                `<button class="pag-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="loadUsers(${meta.current_page + 1})">›</button>`,
            ].join('');
        }

        function pagRange(cur, last) {
            const d = 2, r = [];
            for (let i = Math.max(2, cur - d); i <= Math.min(last - 1, cur + d); i++) r.push(i);
            if (r[0] > 2) r.unshift('…');
            if (r[r.length - 1] < last - 1) r.push('…');
            r.unshift(1); if (last !== 1) r.push(last);
            return r;
        }

        function esc(s) {
            if (!s) return '';
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
@endpush