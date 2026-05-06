@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <strong>Dashboard</strong>
@endsection

@push('styles')
    <style>
        .dash-greeting {
            margin-bottom: 28px;
        }

        .dash-greeting h1 {
            font-size: clamp(20px, 2.5vw, 28px);
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .dash-greeting p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.2s, transform 0.2s;
            text-decoration: none;
            color: inherit;
            cursor: default;
        }

        .stat-card.clickable {
            cursor: pointer;
        }

        .stat-card.clickable:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg {
            width: 22px;
            height: 22px;
        }

        .stat-icon.green {
            background: var(--green-light);
        }

        .stat-icon.green svg {
            stroke: var(--green);
        }

        .stat-icon.amber {
            background: #fef3c7;
        }

        .stat-icon.amber svg {
            stroke: #d97706;
        }

        .stat-icon.blue {
            background: #eff6ff;
        }

        .stat-icon.blue svg {
            stroke: #2563eb;
        }

        /* Skeleton shimmer while loading */
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
            min-width: 40px;
            min-height: 34px;
        }

        .stat-value.loading {
            background: linear-gradient(90deg, #e8f5ed 25%, #d0e8d8 50%, #e8f5ed 75%);
            background-size: 200% 100%;
            animation: shimmer 1.2s infinite;
            border-radius: 6px;
            color: transparent;
        }

        @keyframes shimmer {
            to {
                background-position: -200% 0;
            }
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }

        .module-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--green);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.25s;
        }

        .module-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .module-card:hover::before {
            transform: scaleX(1);
        }

        .module-card-icon {
            width: 44px;
            height: 44px;
            background: var(--green-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .module-card-icon svg {
            width: 22px;
            height: 22px;
            stroke: var(--green);
        }

        .module-card-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }

        .module-card-desc {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .module-card-arrow {
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            color: var(--green);
        }

        .module-card-arrow svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
        }

        .module-card.disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .module-card.disabled .module-card-arrow {
            display: none;
        }

        .soon-tag {
            position: absolute;
            top: 14px;
            right: 14px;
            background: var(--green-light);
            color: var(--green);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 3px 8px;
            border-radius: 20px;
            text-transform: uppercase;
        }
    </style>
@endpush

@section('content')

    {{-- Greeting — filled by JS --}}
    <div class="dash-greeting">
        <h1 id="greetingText">Good day 👋</h1>
        <p id="greetingDate">Loading...</p>
    </div>

    {{-- Stats — values filled by JS API call --}}
    <div class="stat-grid">
        <div class="stat-card clickable" onclick="window.location='#'">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                </svg>
            </div>
            <div>
                <div class="stat-value loading" id="statTotalReceiving">0</div>
                <div class="stat-label">Total Receivings</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
            </div>
            <div>
                <div class="stat-value loading" id="statPendingReceiving">0</div>
                <div class="stat-label">Pending Approval</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </div>
            <div>
                <div class="stat-value loading" id="statTodayReceiving">0</div>
                <div class="stat-label">Received Today</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
            </div>
            <div>
                <div class="stat-value loading" id="statTotalSuppliers">0</div>
                <div class="stat-label">Active Suppliers</div>
            </div>
        </div>
    </div>

    {{-- Modules --}}
    <div class="section-title">Plant Modules</div>

    <div class="module-grid">

        <a href="#" class="module-card">
            <div class="module-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                    <path d="M16.5 9.4 7.55 4.24M3.29 7 12 12l8.71-5M12 22V12" />
                </svg>
            </div>
            <div class="module-card-name">Receiving</div>
            <div class="module-card-desc">Manage inbound battery material receipts, lot tracking and supplier records.</div>
            <div class="module-card-arrow">
                Open module
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </div>
        </a>

        <a href="#" class="module-card">
            <div class="module-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v11" />
                </svg>
            </div>
            <div class="module-card-name">Acid Test</div>
            <div class="module-card-desc">Battery acid testing, results logging and quality control tracking.</div>
            <div class="module-card-arrow">
                Open module
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </div>
        </a>

        <a href="#" class="module-card">
            <div class="module-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" />
                </svg>
            </div>
            <div class="module-card-name">BBSU</div>
            <div class="module-card-desc">Battery breaking and sorting unit operations management.</div>
            <div class="module-card-arrow">
                Open module
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </div>
        </a>

        <a href="#" class="module-card">
            <div class="module-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z" />
                </svg>
            </div>
            <div class="module-card-name">Smelting</div>
            <div class="module-card-desc">Smelting furnace operations, batch tracking and output management.</div>
            <div class="module-card-arrow">
                Open module
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </div>
        </a>

        <a href="#" class="module-card">
            <div class="module-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
            </div>
            <div class="module-card-name">Refinery</div>
            <div class="module-card-desc">Lead refinery process management, quality and output reports.</div>
            <div class="module-card-arrow">
                Open module
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </div>
        </a>

    </div>

@endsection

@push('scripts')
    <script>
        // ── Greeting ──────────────────────────────────────────────────
        const user = JSON.parse(localStorage.getItem('auth_user') || '{}');
        const firstName = (user.name || 'User').split(' ')[0];
        const hour = new Date().getHours();
        const timeOfDay = hour < 12 ? 'morning' : hour < 18 ? 'afternoon' : 'evening';

        document.getElementById('greetingText').textContent =
            `Good ${timeOfDay}, ${firstName} 👋`;

        document.getElementById('greetingDate').textContent =
            `Here's what's happening in the plant today — ` +
            new Date().toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

        // ── Load dashboard stats from API ─────────────────────────────
        async function loadStats() {
            try {
                // Fetch receivings summary and suppliers in parallel
                const [recRes, supRes] = await Promise.all([
                    apiFetch('/receivings?per_page=1'),
                    apiFetch('/suppliers?per_page=1'),
                ]);

                if (recRes && recRes.ok) {
                    const recData = await recRes.json();
                    const pagination = recData.data;

                    document.getElementById('statTotalReceiving').textContent =
                        pagination.total ?? 0;
                }

                if (supRes && supRes.ok) {
                    const supData = await supRes.json();
                    document.getElementById('statTotalSuppliers').textContent =
                        supData.data.total ?? 0;
                }

                // Pending (status=0)
                const pendRes = await apiFetch('/receivings?status=0&per_page=1');
                if (pendRes && pendRes.ok) {
                    const pendData = await pendRes.json();
                    document.getElementById('statPendingReceiving').textContent =
                        pendData.data.total ?? 0;
                }

                // Today's receivings
                const today = new Date().toISOString().split('T')[0];
                const todayRes = await apiFetch(`/receivings?date_from=${today}&date_to=${today}&per_page=1`);
                if (todayRes && todayRes.ok) {
                    const todayData = await todayRes.json();
                    document.getElementById('statTodayReceiving').textContent =
                        todayData.data.total ?? 0;
                }

            } catch (err) {
                console.error('Failed to load stats:', err);
            } finally {
                // Remove shimmer loading state from all stat values
                document.querySelectorAll('.stat-value.loading')
                    .forEach(el => el.classList.remove('loading'));
            }
        }

        loadStats();
    </script>
@endpush