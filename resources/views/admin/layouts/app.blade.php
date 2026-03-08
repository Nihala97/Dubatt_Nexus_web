<!DOCTYPE html>
{{-- resources/views/admin/layouts/app.blade.php --}}
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — DUBATT NEXUS</title>

    {{-- ── PWA: manifest + theme colour ── --}}
    <!-- <link rel="manifest" href="{{ asset('manifest.json') }}"> -->
    <meta name="theme-color" content="#1a7a3a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DUBATT NEXUS">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    {{-- ── END PWA HEAD ── --}}

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green:        #1a7a3a;
            --green-dark:   #145f2d;
            --green-hover:  #1d8840;
            --green-light:  #e8f5ed;
            --green-mid:    #c2e0cc;
            --green-xlight: #f4fbf6;
            --white:        #ffffff;
            --text:         #1a2e22;
            --text-mid:     #3d5a47;
            --text-muted:   #7a9985;
            --border:       #ddeae1;
            --sidebar-w:    240px;
            --topbar-h:     60px;
            --error:        #d93025;
            --warning:      #f59e0b;
            --info:         #3b82f6;
            --shadow-sm:    0 1px 4px rgba(26,122,58,0.08);
            --shadow-md:    0 4px 16px rgba(26,122,58,0.10);
        }

        html, body { height: 100%; font-family: 'Outfit', sans-serif; background: var(--green-xlight); color: var(--text); }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: #111827;
            display: flex;
            flex-direction: column;
            z-index: 200;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            flex-shrink: 0;
        }

        .sidebar-brand-logo {
            width: 34px; height: 34px;
            background: var(--white);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; flex-shrink: 0;
        }

        .sidebar-brand-logo img { width: 100%; height: 100%; object-fit: contain; }

        .sidebar-brand-text {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            color: var(--white);
            line-height: 1.1;
        }

        .sidebar-brand-text span {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-size: 9px;
            font-weight: 400;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.55);
            margin-top: 2px;
        }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 16px 0; }

        .nav-section-label {
            padding: 8px 20px 4px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.15s;
            cursor: pointer;
        }

        .nav-item:hover { background: rgba(255,255,255,0.08); color: var(--white); border-left-color: rgba(255,255,255,0.3); }
        .nav-item.active { background: rgba(255,255,255,0.13); color: var(--white); border-left-color: #a8e6be; }
        .nav-item svg { width: 17px; height: 17px; stroke: currentColor; flex-shrink: 0; }

        .nav-item .badge {
            margin-left: auto;
            background: rgba(255,255,255,0.2);
            color: #fff;
            font-size: 10px;
            padding: 1px 7px;
            border-radius: 20px;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.12);
            flex-shrink: 0;
        }

        .user-card { display: flex; align-items: center; gap: 10px; }

        .user-avatar {
            width: 34px; height: 34px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info { flex: 1; overflow: hidden; }

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
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-logout {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: rgba(255,255,255,0.5);
            transition: color 0.2s;
        }

        .btn-logout:hover { color: #fff; }
        .btn-logout svg { width: 16px; height: 16px; stroke: currentColor; }

        /* ── TOPBAR ── */
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
        }

        .topbar-toggle { display: none; background: none; border: none; cursor: pointer; padding: 6px; color: var(--text-muted); }
        .topbar-toggle svg { width: 22px; height: 22px; stroke: currentColor; }

        .topbar-breadcrumb { flex: 1; font-size: 14px; color: var(--text-muted); }
        .topbar-breadcrumb strong { color: var(--text); font-weight: 600; }

        .topbar-actions { display: flex; align-items: center; gap: 12px; }

        .topbar-btn {
            width: 36px; height: 36px;
            background: var(--green-xlight);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.2s;
            text-decoration: none;
        }

        .topbar-btn:hover { background: var(--green-light); color: var(--green); border-color: var(--green-mid); }
        .topbar-btn svg { width: 16px; height: 16px; stroke: currentColor; }

        /* ── MAIN CONTENT ── */
        .main-wrap { margin-left: var(--sidebar-w); padding-top: var(--topbar-h); min-height: 100vh; }
        .main-content { padding: clamp(20px, 3vw, 32px); }

        /* ── FLASH MESSAGES ── */
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

        .flash-success { background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
        .flash-error   { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
        .flash svg { width: 17px; height: 17px; stroke: currentColor; flex-shrink: 0; }

        /* ── RESPONSIVE ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 150;
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .topbar { left: 0; }
            .topbar-toggle { display: flex; }
            .main-wrap { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
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

        <a href="{{ route('admin.dashboard') }}"
            class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        <div class="nav-section-label" style="margin-top:8px;">MES Modules</div>

        <a href="{{ route('admin.mes.receiving.index') }}" class="nav-item {{ request()->routeIs('admin.mes.receiving.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                <path d="M16.5 9.4 7.55 4.24M3.29 7 12 12l8.71-5M12 22V12"/>
            </svg>
            Receiving
        </a>

        <a href="{{ route('admin.mes.acidTesting.index') }}" class="nav-item {{ request()->routeIs('admin.mes.acidTesting.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v11"/>
            </svg>
            Acid Test
        </a>

        <a href="{{ route('admin.mes.bbsu.index') }}" class="nav-item {{ request()->routeIs('admin.mes.bbsu.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
            BBSU
        </a>

        <a href="{{ route('admin.mes.smelting.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/>
                <path d="M12 6v6l4 2"/>
            </svg>
            Smelting
        </a>

        <a href="{{ route('admin.mes.refining.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
            Refinery
            <span class="badge">Soon</span>
        </a>

        <div class="nav-section-label" style="margin-top:8px;">Masters</div>

        <a href="#" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Suppliers
        </a>

        <a href="#" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.91 8.84 8.56 2.23a1 1 0 0 0-1.26.22L2 9.91a1 1 0 0 0 0 1.36l5.3 7.46a1 1 0 0 0 1.26.22l12.35-6.6a1 1 0 0 0 0-1.51z"/>
            </svg>
            Materials
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar" id="sidebarAvatar">?</div>
            <div class="user-info">
                <div class="user-name" id="sidebarName">Loading...</div>
                <div class="user-role" id="sidebarRole">—</div>
            </div>
            <button class="btn-logout" id="btnLogout" title="Logout">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </button>
        </div>
    </div>
</aside>

<!-- TOPBAR -->
<header class="topbar">
    <button class="topbar-toggle" onclick="openSidebar()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <div class="topbar-breadcrumb">
        @yield('breadcrumb', '<strong>Dashboard</strong>')
    </div>

    <div class="topbar-actions">
        <a href="#" class="topbar-btn" title="Notifications">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
        </a>
        <a href="#" class="topbar-btn" title="Settings">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
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

    // ── Auth guard ────────────────────────────────────────────────
    const _token = localStorage.getItem('auth_token');
    const _user  = JSON.parse(localStorage.getItem('auth_user') || 'null');

    if (!_token || !_user) {
        window.location.href = LOGIN_URL;
    } else {
        document.getElementById('sidebarAvatar').textContent = _user.name
            ? _user.name.charAt(0).toUpperCase()
            : '?';
        document.getElementById('sidebarName').textContent = _user.name  ?? 'User';
        document.getElementById('sidebarRole').textContent = _user.role  ?? '—';
    }

    // ── Global API helper ─────────────────────────────────────────
    async function apiFetch(endpoint, options = {}) {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${API_BASE}${endpoint}`, {
            ...options,
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
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

    // ── Logout ────────────────────────────────────────────────────
    document.getElementById('btnLogout').addEventListener('click', async function () {
        try {
            await apiFetch('/auth/logout', { method: 'POST' });
        } catch (e) {
            console.warn('Logout API failed, clearing storage anyway.');
        }

        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        localStorage.removeItem('remember_me');
        window.location.href = LOGIN_URL;
    });

    // ── Sidebar mobile ────────────────────────────────────────────
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('show');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }
</script>

{{-- ── PWA Boot — loads SW + offline sync system ── --}}
<!-- <script type="module" src="{{ asset('pwa/pwa-boot.js') }}"></script> -->

@stack('scripts')
</body>
</html>