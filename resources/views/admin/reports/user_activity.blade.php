{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/reports/user_activity.blade.php --}}
{{-- User Login/Logout Activity Report for Managers --}}
{{-- ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'User Activity Log')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <span style="color:var(--text-muted);">Reports</span>
    <span style="margin:0 6px;color:var(--border);">/</span>
    <strong>User Activity Log</strong>
@endsection

@push('styles')
    <style>
        :root {
            --g: #1a4f7a;
            --gd: #133d60;
            --gl: #e8f0f8;
            --gxl: #f2f7fc;
            --white: #fff;
            --bg: #f4f6f9;
            --bdr: #d8e3ef;
            --txt: #1e2d3a;
            --txtm: #2d4557;
            --txtmu: #6b849a;
            --err: #dc2626;
            --sh: 0 1px 6px rgba(26, 79, 122, .07), 0 4px 18px rgba(26, 79, 122, .05);
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

        /* PAGE HEADER */
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

        /* STAT CHIPS */
        .stat-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px
        }

        .stat-chip {
            flex: 1;
            min-width: 150px;
            background: var(--white);
            border: 1px solid var(--bdr);
            border-radius: 10px;
            padding: 14px 18px;
            box-shadow: var(--sh);
            display: flex;
            flex-direction: column;
            gap: 4px
        }

        .stat-chip-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--txtmu)
        }

        .stat-chip-val {
            font-size: 28px;
            font-weight: 800;
            color: var(--g);
            line-height: 1;
            letter-spacing: -1px
        }

        .stat-chip-sub {
            font-size: 11px;
            color: var(--txtmu)
        }

        .stat-chip.login-chip {
            border-left: 4px solid #22c55e
        }

        .stat-chip.logout-chip {
            border-left: 4px solid #f59e0b
        }

        .stat-chip.users-chip {
            border-left: 4px solid var(--g)
        }

        /* BUTTONS */
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

        .btn-sm {
            padding: 7px 13px;
            font-size: 12.5px
        }

        .btn-excel {
            background: #217346;
            color: #fff
        }

        .btn-excel:hover {
            background: #185c38;
            box-shadow: 0 4px 14px rgba(33, 115, 70, .3);
            transform: translateY(-1px)
        }

        /* CARD */
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

        .card-body {
            padding: 20px
        }

        /* FILTERS */
        .fg {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
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

        .iw {
            position: relative
        }

        .iw svg.ico {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 13px;
            height: 13px;
            stroke: var(--txtmu);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
            z-index: 1
        }

        input[type=text],
        input[type=date],
        select {
            width: 100%;
            padding: 9px 12px 9px 34px;
            border: 1.5px solid var(--bdr);
            border-radius: 8px;
            background: var(--gxl);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            color: var(--txt);
            outline: none;
            appearance: none;
            transition: border-color .18s, box-shadow .18s, background .18s
        }

        input:focus,
        select:focus {
            border-color: var(--g);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 79, 122, .09)
        }

        select {
            padding-right: 30px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b849a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center
        }

        /* TABLE */
        .tbl-wrap {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--bdr)
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
            padding: 9px 12px;
            border-bottom: 2px solid var(--bdr);
            white-space: nowrap;
            user-select: none
        }

        .dt thead th.sortable {
            cursor: pointer;
            transition: background .15s
        }

        .dt thead th.sortable:hover {
            background: #d5e4f0
        }

        .dt thead th .sort-ico {
            display: inline-flex;
            flex-direction: column;
            gap: 1px;
            margin-left: 5px;
            vertical-align: middle;
            opacity: .35
        }

        .dt thead th.sort-asc .sort-ico,
        .dt thead th.sort-desc .sort-ico {
            opacity: 1
        }

        .dt thead th .sort-ico svg {
            width: 8px;
            height: 8px;
            stroke: var(--g);
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .dt thead th.sort-asc .ico-desc {
            opacity: .2
        }

        .dt thead th.sort-desc .ico-asc {
            opacity: .2
        }

        .dt tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #eaeef3;
            font-size: 12.5px;
            vertical-align: middle
        }

        .dt tbody tr:last-child td {
            border-bottom: none
        }

        .dt tbody tr:hover td {
            background: #f5f8fc
        }

        /* BADGES */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700
        }

        .badge-login {
            background: #dcfce7;
            color: #15803d
        }

        .badge-logout {
            background: #fef3c7;
            color: #92400e
        }

        .badge-admin {
            background: #ede9fe;
            color: #6d28d9
        }

        .badge-management {
            background: #dbeafe;
            color: #1e40af
        }

        .badge-normal {
            background: var(--gl);
            color: var(--g)
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
            font-size: 9.5px;
            padding: 2px 7px
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
            font-size: 9.5px;
            padding: 2px 7px
        }

        /* PAGINATION */
        .pag {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            flex-wrap: wrap;
            gap: 8px;
            border-top: 1px solid var(--bdr)
        }

        .pag-info {
            font-size: 12px;
            color: var(--txtmu)
        }

        .pag-btns {
            display: flex;
            gap: 5px
        }

        .pag-btn {
            padding: 5px 11px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1.5px solid var(--bdr);
            background: var(--white);
            color: var(--txtm);
            cursor: pointer;
            transition: all .15s;
            font-family: 'Outfit', sans-serif
        }

        .pag-btn:hover:not(:disabled) {
            border-color: var(--g);
            color: var(--g);
            background: var(--gxl)
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

        /* STATES */
        .state-row td {
            text-align: center;
            padding: 40px 20px;
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

        @media(max-width:600px) {
            .stat-row {
                flex-direction: column
            }

            .fg {
                grid-template-columns: 1fr
            }
        }
    </style>
@endpush

@section('content')

    {{-- PAGE HEADER --}}
    <div class="ph">
        <div>
            <h2>User Activity Log</h2>
            <p>Login &amp; logout history — session tracking for all users</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn btn-excel btn-sm" id="btnExcel" onclick="exportExcel()">
                <svg viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- SUMMARY STATS --}}
    <div class="stat-row" id="statRow">
        <div class="stat-chip login-chip">
            <span class="stat-chip-label">Total Logins</span>
            <span class="stat-chip-val" id="statLogins">—</span>
            <span class="stat-chip-sub">In filtered period</span>
        </div>
        <div class="stat-chip logout-chip">
            <span class="stat-chip-label">Total Logouts</span>
            <span class="stat-chip-val" id="statLogouts">—</span>
            <span class="stat-chip-sub">In filtered period</span>
        </div>
        <div class="stat-chip users-chip">
            <span class="stat-chip-label">Unique Users</span>
            <span class="stat-chip-val" id="statUsers">—</span>
            <span class="stat-chip-sub">Who logged in</span>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                </svg>
                <span>Filters</span>
            </div>
            <button class="btn btn-outline btn-sm" onclick="resetFilters()">
                <svg viewBox="0 0 24 24">
                    <polyline points="1 4 1 10 7 10" />
                    <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                </svg>
                Reset
            </button>
        </div>
        <div class="card-body">
            <div class="fg">
                <div class="field"><label>Date From</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <input type="date" id="f_date_from" onchange="onFilterChange()">
                    </div>
                </div>
                <div class="field"><label>Date To</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <input type="date" id="f_date_to" onchange="onFilterChange()">
                    </div>
                </div>
                <div class="field"><label>User</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <select id="f_user_id" onchange="onFilterChange()">
                            <option value="">All Users</option>
                        </select>
                    </div>
                </div>
                <div class="field"><label>Action</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <select id="f_action" onchange="onFilterChange()">
                            <option value="">All Actions</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                        </select>
                    </div>
                </div>
                <div class="field"><label>Role</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        <select id="f_role" onchange="onFilterChange()">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="management">Management</option>
                            <option value="normal">Normal</option>
                        </select>
                    </div>
                </div>
                <div class="field"><label>Department</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2" />
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                        </svg>
                        <select id="f_department" onchange="onFilterChange()">
                            <option value="">All Departments</option>
                        </select>
                    </div>
                </div>
                <div class="field"><label>Search</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="f_search" placeholder="Name, username, email…" oninput="onFilterChange()">
                    </div>
                </div>
                <div class="field"><label>Rows / page</label>
                    <div class="iw"><svg class="ico" viewBox="0 0 24 24">
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg>
                        <select id="f_per_page" onchange="onFilterChange()">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card">
        <div class="card-head">
            <div class="card-head-left">
                <svg viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span>Activity Records</span>
            </div>
            <span id="tableCaption" style="font-size:11.5px;color:var(--txtmu)"></span>
        </div>
        <div class="tbl-wrap" style="border-radius:0;border:none">
            <table class="dt" id="activityTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="sortable" data-col="logged_at" onclick="sortBy('logged_at')">
                            Date &amp; Time
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th class="sortable" data-col="name" onclick="sortBy('name')">
                            User
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th>User ID</th>
                        <th class="sortable" data-col="role" onclick="sortBy('role')">
                            Role
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th class="sortable" data-col="department" onclick="sortBy('department')">
                            Department
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th class="sortable" data-col="action" onclick="sortBy('action')">
                            Action
                            <span class="sort-ico">
                                <svg class="ico-asc" viewBox="0 0 24 24">
                                    <polyline points="18 15 12 9 6 15" />
                                </svg>
                                <svg class="ico-desc" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Session</th>
                    </tr>
                </thead>
                <tbody id="activityBody">
                    <tr class="state-row">
                        <td colspan="10"><span class="spinner"></span>Loading…</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pag" id="pagBar" style="display:none">
            <span class="pag-info" id="pagInfo"></span>
            <div class="pag-btns" id="pagBtns"></div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        const API_URL = '/reports/user-activity';

        let currentPage = 1,
            currentSort = 'logged_at',
            currentDir = 'desc';
        let filterTimer = null;

        // ── INIT ──────────────────────────────────────────────────────
        async function init() {
            // Set default dates: today and 30 days ago
            const today = new Date();
            const from = new Date(today);
            from.setDate(from.getDate() - 30);

            document.getElementById('f_date_to').value = today.toISOString().slice(0, 10);
            document.getElementById('f_date_from').value = from.toISOString().slice(0, 10);

            await loadReport();
        }
        init();

        // ── LOAD REPORT ───────────────────────────────────────────────
        async function loadReport(page = 1) {
            currentPage = page;
            setLoading(true);

            const params = new URLSearchParams({
                date_from: document.getElementById('f_date_from').value,
                date_to: document.getElementById('f_date_to').value,
                user_id: document.getElementById('f_user_id').value,
                action: document.getElementById('f_action').value,
                role: document.getElementById('f_role').value,
                department: document.getElementById('f_department').value,
                search: document.getElementById('f_search').value,
                sort_by: currentSort,
                sort_dir: currentDir,
                per_page: document.getElementById('f_per_page').value,
                page: page,
            });
            [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });

            const res = await apiFetch(`${API_URL}?${params.toString()}`);
            setLoading(false);
            if (!res?.ok) { renderError(); return; }

            const json = await res.json();

            // Populate filter dropdowns on first load
            if (page === 1 && json.filters) {
                populateFilters(json.filters);
            }

            // Update summary stats
            if (json.summary) {
                document.getElementById('statLogins').textContent = json.summary.total_logins.toLocaleString();
                document.getElementById('statLogouts').textContent = json.summary.total_logouts.toLocaleString();
                document.getElementById('statUsers').textContent = json.summary.unique_users.toLocaleString();
            }

            renderTable(json.data ?? [], json.meta);
            renderPagination(json.meta);
            updateSortHeaders();
        }

        // ── POPULATE FILTER DROPDOWNS ─────────────────────────────────
        function populateFilters(filters) {
            // Users dropdown
            const userSel = document.getElementById('f_user_id');
            if (userSel.options.length <= 1 && filters.users?.length) {
                filters.users.forEach(u => {
                    const o = document.createElement('option');
                    o.value = u.id;
                    o.textContent = `${u.name} (${u.username})`;
                    userSel.appendChild(o);
                });
            }

            // Departments dropdown
            const deptSel = document.getElementById('f_department');
            if (deptSel.options.length <= 1 && filters.departments?.length) {
                filters.departments.forEach(d => {
                    const o = document.createElement('option');
                    o.value = d; o.textContent = d;
                    deptSel.appendChild(o);
                });
            }
        }

        // ── RENDER TABLE ──────────────────────────────────────────────
        function renderTable(rows, meta) {
            const tbody = document.getElementById('activityBody');

            if (!rows.length) {
                tbody.innerHTML = `<tr class="state-row"><td colspan="10">No activity records found.</td></tr>`;
                document.getElementById('tableCaption').textContent = '';
                return;
            }

            const offset = ((meta?.current_page || 1) - 1) * (meta?.per_page || 50);

            tbody.innerHTML = rows.map((r, i) => {
                const actionBadge = r.action === 'login'
                    ? `<span class="badge badge-login">↗ Login</span>`
                    : `<span class="badge badge-logout">↙ Logout</span>`;

                const roleBadge = r.role === 'admin'
                    ? `<span class="badge badge-admin">Admin</span>`
                    : r.role === 'management'
                        ? `<span class="badge badge-management">Management</span>`
                        : `<span class="badge badge-normal">Normal</span>`;

                const statusBadge = r.is_active
                    ? `<span class="badge badge-active">● Active</span>`
                    : `<span class="badge badge-inactive">● Inactive</span>`;

                return `<tr>
                    <td style="color:var(--txtmu);font-size:11.5px">${offset + i + 1}</td>
                    <td style="white-space:nowrap;font-weight:600">
                        ${escHtml(r.logged_at)}
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--txt)">${escHtml(r.name)}</div>
                        <div style="font-size:10.5px;color:var(--txtmu)">${escHtml(r.username)} &nbsp;·&nbsp; ${escHtml(r.email)}</div>
                    </td>
                    <td style="font-family:monospace;font-size:11.5px;color:var(--txtmu)">#${r.user_id}</td>
                    <td>${roleBadge}</td>
                    <td style="font-size:12px">${escHtml(r.department)}</td>
                    <td>${actionBadge}</td>
                    <td style="font-family:monospace;font-size:11.5px;color:var(--txtmu)">${escHtml(r.ip_address)}</td>
                    <td>${statusBadge}</td>
                    <td style="font-family:monospace;font-size:10.5px;color:var(--txtmu)">${escHtml(r.session_id)}</td>
                </tr>`;
            }).join('');

            document.getElementById('tableCaption').textContent =
                meta ? `Showing ${rows.length} of ${meta.total.toLocaleString()} records` : `${rows.length} records`;
        }

        // ── PAGINATION ────────────────────────────────────────────────
        function renderPagination(meta) {
            const bar = document.getElementById('pagBar');
            const info = document.getElementById('pagInfo');
            const btns = document.getElementById('pagBtns');
            if (!meta || meta.last_page <= 1) { bar.style.display = 'none'; return; }
            bar.style.display = 'flex';
            info.textContent = `${(meta.current_page - 1) * meta.per_page + 1}–${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total.toLocaleString()}`;
            const pages = paginationRange(meta.current_page, meta.last_page);
            btns.innerHTML = [
                `<button class="pag-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="loadReport(${meta.current_page - 1})">‹</button>`,
                ...pages.map(p => p === '…'
                    ? `<button class="pag-btn" disabled>…</button>`
                    : `<button class="pag-btn${p === meta.current_page ? ' active' : ''}" onclick="loadReport(${p})">${p}</button>`),
                `<button class="pag-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="loadReport(${meta.current_page + 1})">›</button>`,
            ].join('');
        }

        function paginationRange(cur, last) {
            const delta = 2, range = [];
            for (let i = Math.max(2, cur - delta); i <= Math.min(last - 1, cur + delta); i++) range.push(i);
            if (range[0] > 2) range.unshift('…');
            if (range[range.length - 1] < last - 1) range.push('…');
            range.unshift(1); if (last !== 1) range.push(last); return range;
        }

        // ── SORT ──────────────────────────────────────────────────────
        function sortBy(col) {
            currentDir = currentSort === col ? (currentDir === 'asc' ? 'desc' : 'asc') : 'desc';
            currentSort = col;
            loadReport(1);
        }
        function updateSortHeaders() {
            document.querySelectorAll('#activityTable thead th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
                if (th.dataset.col === currentSort) th.classList.add(currentDir === 'asc' ? 'sort-asc' : 'sort-desc');
            });
        }

        // ── FILTERS ───────────────────────────────────────────────────
        function onFilterChange() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(() => loadReport(1), 350);
        }
        function resetFilters() {
            ['f_date_from', 'f_date_to', 'f_search'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
            ['f_user_id', 'f_action', 'f_role', 'f_department'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
            document.getElementById('f_per_page').value = '50';

            // Reset to last 30 days
            const today = new Date();
            const from = new Date(today); from.setDate(from.getDate() - 30);
            document.getElementById('f_date_to').value = today.toISOString().slice(0, 10);
            document.getElementById('f_date_from').value = from.toISOString().slice(0, 10);

            currentSort = 'logged_at'; currentDir = 'desc';
            loadReport(1);
        }

        // ── STATES ────────────────────────────────────────────────────
        function setLoading(on) {
            if (on) {
                document.getElementById('activityBody').innerHTML =
                    `<tr class="state-row"><td colspan="10"><span class="spinner"></span>Loading…</td></tr>`;
                document.getElementById('pagBar').style.display = 'none';
            }
        }
        function renderError() {
            document.getElementById('activityBody').innerHTML =
                `<tr class="state-row"><td colspan="10" style="color:var(--err)">Failed to load data. Please try again.</td></tr>`;
        }

        // ── EXPORT EXCEL ──────────────────────────────────────────────
        async function exportExcel() {
            const btn = document.getElementById('btnExcel');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner" style="border-top-color:#fff"></span> Exporting…`;

            // Fetch all pages
            const all = []; let page = 1, lastPage = 1;
            do {
                const params = new URLSearchParams({
                    date_from: document.getElementById('f_date_from').value,
                    date_to: document.getElementById('f_date_to').value,
                    user_id: document.getElementById('f_user_id').value,
                    action: document.getElementById('f_action').value,
                    role: document.getElementById('f_role').value,
                    department: document.getElementById('f_department').value,
                    search: document.getElementById('f_search').value,
                    sort_by: currentSort, sort_dir: currentDir,
                    per_page: 500, page: page,
                });
                [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
                const res = await apiFetch(`${API_URL}?${params.toString()}`);
                if (!res?.ok) break;
                const json = await res.json();
                all.push(...(json.data ?? []));
                lastPage = json.meta?.last_page ?? 1;
                page++;
            } while (page <= lastPage);

            if (!all.length) {
                btn.disabled = false; btn.innerHTML = 'Export Excel'; return;
            }

            const wsData = [
                ['#', 'Date & Time', 'User Name', 'Username', 'Email', 'User ID', 'Role', 'Department', 'Action', 'IP Address', 'User Status', 'Session ID'],
                ...all.map((r, i) => [
                    i + 1,
                    r.logged_at,
                    r.name,
                    r.username,
                    r.email,
                    r.user_id,
                    r.role,
                    r.department,
                    r.action.toUpperCase(),
                    r.ip_address,
                    r.is_active ? 'Active' : 'Inactive',
                    r.session_id,
                ]),
            ];

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            ws['!cols'] = [
                { wch: 5 }, { wch: 20 }, { wch: 22 }, { wch: 16 }, { wch: 28 }, { wch: 8 },
                { wch: 12 }, { wch: 16 }, { wch: 10 }, { wch: 16 }, { wch: 10 }, { wch: 16 }
            ];
            XLSX.utils.book_append_sheet(wb, ws, 'User Activity');
            XLSX.writeFile(wb, `UserActivity_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.xlsx`);

            btn.disabled = false;
            btn.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Export Excel`;
        }

        // ── UTILS ─────────────────────────────────────────────────────
        function escHtml(str) {
            if (str === null || str === undefined) return '—';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
@endpush