<!DOCTYPE html>
{{-- resources/views/admin/layouts/app.blade.php --}}
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — DUBATT NEXUS</title>

    {{-- ── PWA: manifest + theme colour ── --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#1a7a3a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DUBATT NEXUS">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    {{-- ── END PWA HEAD ── --}}

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --green: #1a7a3a;
            --green-dark: #145f2d;
            --green-hover: #1d8840;
            --green-light: #e8f5ed;
            --green-mid: #c2e0cc;
            --green-xlight: #f4fbf6;
            --white: #ffffff;
            --text: #1a2e22;
            --text-mid: #3d5a47;
            --text-muted: #7a9985;
            --border: #ddeae1;
            --sidebar-w: 240px;
            --sidebar-icon-w: 60px;
            --topbar-h: 60px;
            --error: #d93025;
            --warning: #f59e0b;
            --info: #3b82f6;
            --shadow-sm: 0 1px 4px rgba(26, 122, 58, .08);
            --shadow-md: 0 4px 16px rgba(26, 122, 58, .10);
            --sb-speed: 0.22s;
        }

        html,
        body {
            height: 100%;
            font-family: 'Outfit', sans-serif;
            background: var(--green-xlight);
            color: var(--text);
        }

        /* ══ SIDEBAR ══════════════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: #111827;
            display: flex;
            flex-direction: column;
            z-index: 200;
            transition: width var(--sb-speed) ease, transform var(--sb-speed) ease;
            overflow: hidden;
        }

        /* Icon-only collapsed state (desktop) */
        .sidebar.collapsed {
            width: var(--sidebar-icon-w);
        }

        /* ── Brand ── */
        .sidebar-brand {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 13px;
            border-bottom: 1px solid rgba(255, 255, 255, .12);
            flex-shrink: 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar-brand-logo {
            width: 34px;
            height: 34px;
            background: var(--white);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar-brand-text {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            color: var(--white);
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--sb-speed), max-width var(--sb-speed);
            max-width: 200px;
        }

        .sidebar-brand-text span {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-size: 9px;
            font-weight: 400;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .55);
            margin-top: 2px;
        }

        .sidebar.collapsed .sidebar-brand-text {
            opacity: 0;
            max-width: 0;
        }

        /* ── Nav ── */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 16px 0;
        }

        .nav-section-label {
            padding: 8px 20px 4px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .35);
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--sb-speed);
        }

        .sidebar.collapsed .nav-section-label {
            opacity: 0;
            pointer-events: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0 10px 13px;
            color: rgba(255, 255, 255, .75);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: background .15s, border-color .15s, color .15s;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, .08);
            color: var(--white);
            border-left-color: rgba(255, 255, 255, .3);
        }

        .nav-item.active {
            background: rgba(255, 255, 255, .13);
            color: var(--white);
            border-left-color: #a8e6be;
        }

        .nav-item svg {
            width: 17px;
            height: 17px;
            stroke: currentColor;
            flex-shrink: 0;
        }

        /* Text label & badge */
        .nav-label,
        .nav-item .badge {
            transition: opacity var(--sb-speed);
        }

        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .nav-item .badge {
            opacity: 0;
            pointer-events: none;
        }

        .nav-item .badge {
            margin-left: auto;
            background: rgba(255, 255, 255, .2);
            color: #fff;
            font-size: 10px;
            padding: 1px 7px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Tooltip when collapsed */
        .sidebar.collapsed .nav-item::after {
            content: attr(data-label);
            position: fixed;
            left: calc(var(--sidebar-icon-w) + 10px);
            background: #1f2937;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 7px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity .15s;
            z-index: 9999;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .3);
        }

        .sidebar.collapsed .nav-item:hover::after {
            opacity: 1;
        }

        /* ── Footer ── */
        .sidebar-footer {
            padding: 16px 13px;
            border-top: 1px solid rgba(255, 255, 255, .12);
            flex-shrink: 0;
            overflow: hidden;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            background: rgba(255, 255, 255, .2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            overflow: hidden;
            transition: opacity var(--sb-speed);
        }

        .sidebar.collapsed .user-info,
        .sidebar.collapsed .btn-logout {
            opacity: 0;
            pointer-events: none;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 10px;
            color: rgba(255, 255, 255, .5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-logout {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: rgba(255, 255, 255, .5);
            transition: color .2s, opacity var(--sb-speed);
        }

        .btn-logout:hover {
            color: #fff;
        }

        .btn-logout svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
        }

        /* ══ TOPBAR ═══════════════════════════════════════════════ */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            z-index: 100;
            box-shadow: var(--shadow-sm);
            transition: left var(--sb-speed) ease;
        }

        body.sb-collapsed .topbar {
            left: var(--sidebar-icon-w);
        }

        .topbar-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 7px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            flex-shrink: 0;
            transition: background .15s, color .15s;
        }

        .topbar-toggle:hover {
            background: var(--green-light);
            color: var(--green);
        }

        .topbar-toggle svg {
            width: 22px;
            height: 22px;
            stroke: currentColor;
        }

        .topbar-breadcrumb {
            flex: 1;
            font-size: 14px;
            color: var(--text-muted);
        }

        .topbar-breadcrumb strong {
            color: var(--text);
            font-weight: 600;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-btn {
            width: 36px;
            height: 36px;
            background: var(--green-xlight);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all .2s;
            text-decoration: none;
        }

        .topbar-btn:hover {
            background: var(--green-light);
            color: var(--green);
            border-color: var(--green-mid);
        }

        .topbar-btn svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
        }

        /* ══ MAIN CONTENT ════════════════════════════════════════ */
        .main-wrap {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
            transition: margin-left var(--sb-speed) ease;
        }

        body.sb-collapsed .main-wrap {
            margin-left: var(--sidebar-icon-w);
        }

        .main-content {
            padding: clamp(20px, 3vw, 32px);
        }

        /* ── Flash messages ── */
        .flash {
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .flash-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .flash svg {
            width: 17px;
            height: 17px;
            stroke: currentColor;
            flex-shrink: 0;
        }

        /* ══ RESPONSIVE ══════════════════════════════════════════ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            z-index: 150;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: var(--sidebar-w) !important;
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .topbar {
                left: 0 !important;
            }

            .main-wrap {
                margin-left: 0 !important;
            }

            body.sb-collapsed .topbar {
                left: 0 !important;
            }

            body.sb-collapsed .main-wrap {
                margin-left: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <!-- Sidebar Overlay (mobile only) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-logo">
                <img src="{{ asset('assets/images/dubatt-logo.png') }}" alt="DUBATT"
                    onerror="this.outerHTML='<svg width=\'18\' height=\'18\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#1a7a3a\' stroke-width=\'2\'><path d=\'M12 2L2 7l10 5 10-5-10-5M2 17l10 5 10-5M2 12l10 5 10-5\'/></svg>'">
            </div>
            <div class="sidebar-brand-text">
                DUBATT NEXUS
                <span>ERP Platform</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>

            <a href="{{ route('admin.dashboard') }}" data-label="Dashboard"
                class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span class="nav-label">Dashboard</span>
            </a>

            <div class="nav-section-label" style="margin-top:8px;">MES Modules</div>

            <a href="{{ route('admin.mes.receiving.index') }}" data-label="Receiving" data-permission="receiving"
                class="nav-item {{ request()->routeIs('admin.mes.receiving.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                    <path d="M16.5 9.4 7.55 4.24M3.29 7 12 12l8.71-5M12 22V12" />
                </svg>
                <span class="nav-label">Receiving</span>
            </a>

            <a href="{{ route('admin.mes.acidTesting.index') }}" data-label="Acid Test" data-permission="acid_testing"
                class="nav-item {{ request()->routeIs('admin.mes.acidTesting.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v11" />
                </svg>
                <span class="nav-label">Acid Test</span>
            </a>

            <a href="{{ route('admin.mes.bbsu.index') }}" data-label="BBSU" data-permission="bbsu"
                class="nav-item {{ request()->routeIs('admin.mes.bbsu.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18M3 12h18M3 18h18" />
                </svg>
                <span class="nav-label">BBSU</span>
            </a>

            <a href="{{ route('admin.mes.smelting.index') }}" data-label="Smelting" data-permission="smelting"
                class="nav-item {{ request()->routeIs('admin.mes.smelting.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z" />
                    <path d="M12 6v6l4 2" />
                </svg>
                <span class="nav-label">Smelting</span>
            </a>

            <a href="{{ route('admin.mes.refining.index') }}" data-label="Refining" data-permission="refining"
                class="nav-item {{ request()->routeIs('admin.mes.refining.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
                <span class="nav-label">Refining</span>
            </a>

            <div class="nav-section-label" style="margin-top:8px;">Reports &amp; Dashboard</div>

            <a href="{{ route('admin.reports.materialInward') }}" data-permission="report_material_inward"
                class="nav-item {{ request()->routeIs('admin.reports.materialInward') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                <span>Material Inward</span>
            </a>

            <a href="{{ route('admin.reports.acidTestStatus') }}" data-permission="report_acid_test_status"
                class="nav-item {{ request()->routeIs('admin.reports.acidTestStatus') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4" />
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                </svg>
                <span>Acid Test Status</span>
            </a>

            <div class="nav-section-label" style="margin-top:8px;">Masters</div>

            <a href="{{ route('admin.mes.supplier.index') }}" data-label="Supplier" data-permission="suppliers"
                class="nav-item {{ request()->routeIs('admin.mes.supplier.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span class="nav-label">Suppliers</span>
            </a>

            <a href="{{ route('admin.mes.material.index') }}" data-label="Material" data-permission="materials"
                class="nav-item {{ request()->routeIs('admin.mes.material.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M20.91 8.84 8.56 2.23a1 1 0 0 0-1.26.22L2 9.91a1 1 0 0 0 0 1.36l5.3 7.46a1 1 0 0 0 1.26.22l12.35-6.6a1 1 0 0 0 0-1.51z" />
                </svg>
                <span class="nav-label">Materials</span>
            </a>
            {{-- ── SETTINGS ─────────────────────────────────────────────────── --}}
            <div id="settingsMenu" style="display: none;">
                <div class="nav-section-label" style="margin-top:8px;">Settings</div>


                <a href="{{ route('admin.settings.users') }}"
                    class="nav-item {{ request()->routeIs('admin.settings.users') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span>Users</span>
                </a>

                <a href="{{ route('admin.settings.roles') }}"
                    class="nav-item {{ request()->routeIs('admin.settings.roles') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    <span>Roles</span>
                </a>

                <a href="{{ route('admin.settings.profiles') }}"
                    class="nav-item {{ request()->routeIs('admin.settings.profiles') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <span>Profiles</span>
                </a>

                <a href="{{ route('admin.settings.modules') }}"
                    class="nav-item {{ request()->routeIs('admin.settings.modules') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                    <span>Modules</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar" id="sidebarAvatar">?</div>
                <div class="user-info">
                    <div class="user-name" id="sidebarName">Loading...</div>
                    <div class="user-role" id="sidebarRole">—</div>
                </div>
                <button class="btn-logout" id="btnLogout" title="Logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    <!-- TOPBAR -->
    <header class="topbar">
        <button class="topbar-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>

        <div class="topbar-breadcrumb">
            @yield('breadcrumb', '<strong>Dashboard</strong>')
        </div>

        <div class="topbar-actions">
            <a href="#" class="topbar-btn" title="Notifications">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
            </a>
            <a href="#" class="topbar-btn" title="Settings">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
            </a>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <div class="main-wrap">
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <script>
        const API_BASE = '{{ url('/api') }}';
        const LOGIN_URL = '{{ route('login') }}';

        // ── Auth guard ──────────────────────────────────────────────
        const _token = localStorage.getItem('auth_token');
        const _user = JSON.parse(localStorage.getItem('auth_user') || 'null');
        if (!_token || !_user) {
            window.location.href = LOGIN_URL;
        } else {
            document.getElementById('sidebarAvatar').textContent = _user.name ? _user.name.charAt(0).toUpperCase() : '?';
            document.getElementById('sidebarName').textContent = _user.name ?? 'User';
            document.getElementById('sidebarRole').textContent = _user.role ?? '—';
        }

        // ── Permissions System ─────────────────────────────────────
        window.userPermissions = JSON.parse(localStorage.getItem('user_permissions') || '[]');

        /**
         * Fetch latest user info and permissions
         */
        async function loadUserPermissions() {
            try {
                const res = await apiFetch('/auth/me');
                if (!res || !res.ok) return;
                const json = await res.json();
                if (json.status === 'ok') {
                    // Update user info in storage
                    localStorage.setItem('auth_user', JSON.stringify(json.data));
                    // Update permissions in storage
                    const perms = json.data.permissions || [];
                    localStorage.setItem('user_permissions', JSON.stringify(perms));
                    window.userPermissions = perms;

                    // If full_access is true, we store a special flag
                    if (json.data.full_access) {
                        localStorage.setItem('is_admin', '1');
                    } else {
                        localStorage.removeItem('is_admin');
                    }
                }
            } catch (e) {
                console.error('Failed to load permissions:', e);
            }
        }

        /**
         * Check if user has permission for a module/action
         * @param {string} moduleSlug 
         * @param {string} action 'can_view','can_create','can_edit','can_delete'
         */
        function can(moduleSlug, action = 'can_view') {
            // Admins always have access
            if (localStorage.getItem('is_admin') === '1') return true;

            const perm = window.userPermissions.find(p => p.module === moduleSlug);
            if (!perm) return false;

            return !!perm[action];
        }

        function enforceUIPermissions() {
            document.querySelectorAll('[data-permission]').forEach(el => {
                const parts = el.getAttribute('data-permission').split(',');
                const mod = parts[0];
                const act = parts[1] || 'can_view';
                
                if (!can(mod, act)) {
                    el.style.display = 'none';
                } else {
                    el.style.display = '';
                }
            });

            // Handle Settings Menu visibility for Admins
            if (localStorage.getItem('is_admin') === '1') {
                const settingsMenu = document.getElementById('settingsMenu');
                if (settingsMenu) settingsMenu.style.display = 'block';
            }
        }

        // Initial load
        if (_token) {
            loadUserPermissions().then(() => {
                enforceUIPermissions();
            });
        } else {
            enforceUIPermissions();
        }

        // ── Global API helper ───────────────────────────────────────
        async function apiFetch(endpoint, options = {}) {
            const token = localStorage.getItem('auth_token');
            const res = await fetch(`${API_BASE}${endpoint}`, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    ...(options.headers || {}),
                },
            });
            if (res.status === 401) {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('auth_user');
                window.location.href = LOGIN_URL;
                return null;
            }
            return res;
        }

        // ── Logout ──────────────────────────────────────────────────
        document.getElementById('btnLogout').addEventListener('click', async function () {
            try { await apiFetch('/auth/logout', { method: 'POST' }); }
            catch (e) { console.warn('Logout API failed, clearing storage anyway.'); }
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            localStorage.removeItem('remember_me');
            window.location.href = LOGIN_URL;
        });

        // ════════════════════════════════════════════════════════════
        // SIDEBAR COLLAPSE / EXPAND
        //  Desktop → shrinks to 60px icon-only rail; state saved in localStorage
        //  Mobile  → slides in/out as full overlay
        // ════════════════════════════════════════════════════════════
        const sidebar = document.getElementById('sidebar');
        const isMobile = () => window.innerWidth <= 900;

        function toggleSidebar() {
            if (isMobile()) {
                sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
            } else {
                sidebar.classList.contains('collapsed') ? expandSidebar() : collapseSidebar();
            }
        }

        function collapseSidebar() {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sb-collapsed');
            localStorage.setItem('sidebarCollapsed', '1');
        }

        function expandSidebar() {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('sb-collapsed');
            localStorage.setItem('sidebarCollapsed', '0');
        }

        // Mobile open/close
        function openSidebar() {
            sidebar.classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('show');
        }
        function closeSidebar() {
            sidebar.classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('show');
        }

        // Restore saved state on page load
        (function () {
            if (!isMobile() && localStorage.getItem('sidebarCollapsed') === '1') {
                sidebar.classList.add('collapsed');
                document.body.classList.add('sb-collapsed');
            }
        })();

        // Clean up state on resize
        window.addEventListener('resize', () => {
            if (!isMobile()) {
                sidebar.classList.remove('open');
                document.getElementById('sidebarOverlay').classList.remove('show');
                if (localStorage.getItem('sidebarCollapsed') === '1') {
                    sidebar.classList.add('collapsed');
                    document.body.classList.add('sb-collapsed');
                }
            } else {
                sidebar.classList.remove('collapsed');
                document.body.classList.remove('sb-collapsed');
            }
        });
        async function deleteBatch(id, batchNo, url) {

            if (!await showConfirm(`Delete batch ${batchNo}? This cannot be undone.`)) return;

            const btn = document.getElementById(`del-${id}`);
            btn.disabled = true;

            const res = await apiFetch(url, { method: 'DELETE' });
            const data = await res.json();

            if (res.ok && data.status === 'ok') {

                showToast('Batch deleted successfully.', 'success');

                const row = document.getElementById(`row-${id}`);

                if (row) {
                    row.style.transition = 'opacity .25s, transform .25s';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';

                    setTimeout(() => row.remove(), 250);
                }

            } else {
                showToast(data.message ?? 'Failed to delete.', 'error');
                btn.disabled = false;
            }
        }
        function showToast(message, type = 'success') {
            const existing = document.getElementById('_toast');
            if (existing) existing.remove();

            const bg = type === 'success' ? '#166534' : '#991b1b';
            const icon = type === 'success'
                ? `<polyline points="20 6 9 17 4 12"/>`
                : `<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>`;

            const toast = document.createElement('div');
            toast.id = '_toast';
            toast.style.cssText = `
                position:fixed; bottom:24px; right:24px;
                background:${bg}; color:#fff;
                padding:12px 20px; border-radius:10px;
                font-size:13px; font-weight:600;
                box-shadow:0 4px 20px rgba(0,0,0,.15);
                z-index:10000;
                display:flex; align-items:center; gap:8px;
                animation:_toastIn .2s ease;
            `;
            toast.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="width:16px;height:16px;flex-shrink:0">${icon}</svg>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = '_toastOut .2s ease forwards';
                setTimeout(() => toast.remove(), 200);
            }, 3500);
        }

        /* ─────────────────────────────────────────────
        showConfirm(message) → Promise<boolean>
        ───────────────────────────────────────────── */
        function showConfirm(message) {
            return new Promise(resolve => {
                const overlay = document.createElement('div');
                overlay.id = '_confirmOverlay';
                overlay.style.cssText = `
                    position:fixed; inset:0;
                    background:rgba(0,0,0,.45);
                    z-index:10001;
                    display:flex; align-items:center; justify-content:center;
                    animation:_fadeIn .15s ease;
                `;

                overlay.innerHTML = `
                    <div style="
                        background:var(--white, #fff);
                        border:1px solid var(--border, #e5e7eb);
                        border-radius:16px;
                        padding:28px 28px 24px;
                        width:100%; max-width:380px;
                        box-shadow:0 20px 60px rgba(0,0,0,.18);
                        animation:_slideUp .18s ease;
                        font-family:inherit;
                    ">
                        <!-- Icon -->
                        <div style="
                            width:48px; height:48px;
                            background:#fee2e2; border-radius:12px;
                            display:flex; align-items:center; justify-content:center;
                            margin-bottom:16px;
                        ">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="width:22px;height:22px">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                            </svg>
                        </div>

                        <!-- Title & message -->
                        <p style="font-size:15px;font-weight:700;color:var(--text,#111);margin:0 0 6px">
                            Confirm Delete
                        </p>
                        <p style="font-size:13px;color:var(--text-muted,#6b7280);margin:0 0 24px;line-height:1.5">
                            ${message}
                        </p>

                        <!-- Buttons -->
                        <div style="display:flex;gap:10px;justify-content:flex-end">
                            <button id="_confirmCancel" style="
                                padding:9px 18px; border-radius:9px;
                                font-size:13px; font-weight:600;
                                cursor:pointer; font-family:inherit;
                                background:var(--white,#fff);
                                color:var(--text,#111);
                                border:1.5px solid var(--border,#e5e7eb);
                                transition:all .15s;
                            ">Cancel</button>
                            <button id="_confirmOk" style="
                                padding:9px 18px; border-radius:9px;
                                font-size:13px; font-weight:600;
                                cursor:pointer; font-family:inherit;
                                background:#ef4444; color:#fff;
                                border:1.5px solid #ef4444;
                                transition:all .15s;
                            ">Delete</button>
                        </div>
                    </div>
                `;

                document.body.appendChild(overlay);

                // Hover effects
                const cancelBtn = overlay.querySelector('#_confirmCancel');
                const okBtn = overlay.querySelector('#_confirmOk');

                cancelBtn.onmouseenter = () => { cancelBtn.style.borderColor = 'var(--green)'; cancelBtn.style.color = 'var(--green)'; };
                cancelBtn.onmouseleave = () => { cancelBtn.style.borderColor = 'var(--border,#e5e7eb)'; cancelBtn.style.color = 'var(--text,#111)'; };
                okBtn.onmouseenter = () => { okBtn.style.background = '#dc2626'; };
                okBtn.onmouseleave = () => { okBtn.style.background = '#ef4444'; };

                function close(result) {
                    overlay.style.animation = '_fadeOut .15s ease forwards';
                    setTimeout(() => { overlay.remove(); resolve(result); }, 150);
                }

                okBtn.onclick = () => close(true);
                cancelBtn.onclick = () => close(false);
                overlay.onclick = e => { if (e.target === overlay) close(false); };

                document.addEventListener('keydown', function esc(e) {
                    if (e.key === 'Escape') { close(false); document.removeEventListener('keydown', esc); }
                });
            });
        }

        /* ── Keyframe animations (injected once) ── */
        if (!document.getElementById('_utilStyles')) {
            const s = document.createElement('style');
            s.id = '_utilStyles';
            s.textContent = `
                @keyframes _toastIn  { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
                @keyframes _toastOut { from { opacity:1; transform:translateY(0); }   to { opacity:0; transform:translateY(10px); } }
                @keyframes _fadeIn   { from { opacity:0; }  to { opacity:1; } }
                @keyframes _fadeOut  { from { opacity:1; }  to { opacity:0; } }
                @keyframes _slideUp  { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
            `;
            document.head.appendChild(s);
        }
    </script>

    {{-- ── PWA Boot ── --}}
    <!-- <script type="module" src="{{ asset('pwa/pwa-boot.js') }}"></script> -->

    @stack('scripts')
</body>

</html>