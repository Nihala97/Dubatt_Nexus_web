<!DOCTYPE html>
{{--
LOGIN VIEW — DUBATT NEXUS
Logo: place at public/assets/images/dubatt-logo.png
--}}
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — DUBATT NEXUS</title>
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
            --green-light: #e8f5ed;
            --green-mid: #c2e0cc;
            --green-xlight: #f4fbf6;
            --white: #ffffff;
            --text: #1a2e22;
            --text-mid: #3d5a47;
            --text-muted: #7a9985;
            --border: #d0e8d8;
            --error: #d93025;
            --shadow: rgba(26, 122, 58, 0.10);
        }

        html,
        body {
            height: 100%;
            font-family: 'Outfit', sans-serif;
            background: var(--white);
            color: var(--text);
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            background: var(--green);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(32px, 5vw, 64px);
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -120px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .brand-logo-wrap {
            width: 52px;
            height: 52px;
            background: var(--white);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .brand-logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: clamp(18px, 2.2vw, 26px);
            color: var(--white);
            line-height: 1.1;
        }

        .brand-name span {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-size: 11px;
            font-weight: 400;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 3px;
        }

        .left-main {
            position: relative;
            z-index: 1;
        }

        .left-main h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 4vw, 54px);
            color: var(--white);
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .left-main h1 em {
            font-style: normal;
            color: #a8e6be;
        }

        .left-main p {
            font-size: clamp(13px, 1.2vw, 15px);
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.8;
            max-width: 340px;
            margin-bottom: 36px;
        }

        .module-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pill {
            padding: 6px 16px;
            border-radius: 100px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.1);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
        }

        .left-footer {
            position: relative;
            z-index: 1;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.35);
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(32px, 5vw, 64px) clamp(24px, 5vw, 80px);
        }

        .form-box {
            width: 100%;
            max-width: 420px;
            animation: fadeUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-title {
            margin-bottom: 36px;
        }

        .form-title h2 {
            font-size: clamp(22px, 2.5vw, 30px);
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-title p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .dev-banner {
            background: #fff3f3;
            border: 1px solid #f5c6c6;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 600;
            color: var(--error);
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 24px;
        }

        /* ── ALERT BOX (for API errors) ── */
        .alert {
            display: none;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .alert.error {
            background: #fff3f3;
            border: 1px solid #f5c6c6;
            color: var(--error);
        }

        .alert.show {
            display: block;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-mid);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .ico {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 17px;
            height: 17px;
            stroke: var(--text-muted);
            pointer-events: none;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 13px 14px 13px 44px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: var(--green-xlight);
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input::placeholder {
            color: var(--text-muted);
        }

        input:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(26, 122, 58, 0.08);
        }

        input.is-invalid {
            border-color: var(--error);
        }

        input.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(217, 48, 37, 0.08);
        }

        .error-msg {
            margin-top: 6px;
            font-size: 12px;
            color: var(--error);
        }

        .row-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .check-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .check-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--green);
            cursor: pointer;
        }

        .check-wrap span {
            font-size: 13px;
            color: var(--text-muted);
        }

        .forgot-link {
            font-size: 13px;
            color: var(--green);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            color: var(--green-dark);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--green);
            border: none;
            border-radius: 10px;
            color: var(--white);
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            background: var(--green-dark);
            box-shadow: 0 8px 24px rgba(26, 122, 58, 0.25);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login svg {
            width: 18px;
            height: 18px;
            stroke: #fff;
        }

        /* Spinner inside button */
        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-login.loading .btn-icon {
            display: none;
        }

        .btn-login.loading .spinner {
            display: block;
        }

        .btn-login.loading .btn-text {
            opacity: 0.8;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider span {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .register-link {
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .register-link a {
            color: var(--green);
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 860px) {
            .page {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }

            .left-panel {
                padding: 32px 28px 40px;
                flex-direction: column;
                gap: 28px;
            }

            .left-main h1 {
                font-size: 28px;
            }

            .left-main p {
                font-size: 13px;
            }

            .left-footer {
                display: none;
            }

            .right-panel {
                padding: 40px 24px 48px;
                align-items: flex-start;
            }

            .form-box {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .left-panel {
                padding: 24px 20px 32px;
            }

            .left-main p {
                display: none;
            }

            .right-panel {
                padding: 32px 20px 40px;
            }

            .form-title h2 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="page">

        <!-- LEFT BRAND PANEL -->
        <div class="left-panel">
            <div class="brand">
                <div class="brand-logo-wrap">
                    <img src="{{ asset('assets/images/dubatt-logo.png') }}" alt="DUBATT NEXUS"
                        onerror="this.outerHTML='<svg width=\'28\' height=\'28\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#1a7a3a\' stroke-width=\'2\'><path d=\'M12 2L2 7l10 5 10-5-10-5M2 17l10 5 10-5M2 12l10 5 10-5\'/></svg>'">
                </div>
                <div class="brand-name">
                    DUBATT NEXUS
                    <span>Battery Management ERP</span>
                </div>
            </div>

            <div class="left-main">
                <h1>Plant Operations <em>Made Simple</em></h1>
                <p>Integrated end-to-end management for receiving, testing, smelting, and refinery operations across all
                    DUBATT plant facilities.</p>
                <div class="module-pills">
                    <span class="pill">Receiving</span>
                    <span class="pill">Acid Test</span>
                    <span class="pill">BBSU</span>
                    <span class="pill">Smelting</span>
                    <span class="pill">Refinery</span>
                </div>
            </div>

            <div class="left-footer">© {{ date('Y') }} DUBATT NEXUS. All rights reserved.</div>
        </div>

        <!-- RIGHT FORM PANEL -->
        <div class="right-panel">
            <div class="form-box">

                <div class="form-title">
                    <h2>Welcome back 👋</h2>
                    <p>Sign in to access your DUBATT NEXUS dashboard</p>
                </div>



                <!-- API error message box -->
                <div class="alert error" id="alertBox"></div>

                <!-- Changed: no action/method, handled by JS -->
                <form id="loginForm">

                    <div class="field">
                        <label for="login">Email or Username</label>
                        <div class="input-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <input type="text" id="login" name="login" placeholder="email or username" required
                                autofocus>
                        </div>
                        <div class="error-msg" id="loginError"></div>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" id="password" name="password" placeholder="Enter your password"
                                required>
                        </div>
                        <div class="error-msg" id="passwordError"></div>
                    </div>

                    <div class="row-options">
                        <label class="check-wrap">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-login" id="btnLogin">
                        <span class="spinner"></span>
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                        <span class="btn-text">Sign In</span>
                    </button>

                </form>

                <div class="divider"><span>Don't have an account?</span></div>

                <div class="register-link">
                    Contact your administrator to create an account.
                </div>

            </div>
        </div>

    </div>

    <script>
        // ── Config ────────────────────────────────────────────────────
        const API_BASE = '{{ url('/api') }}';

        // ── Helpers ───────────────────────────────────────────────────
        function showAlert(msg) {
            const box = document.getElementById('alertBox');
            box.textContent = msg;
            box.classList.add('show');
        }

        function hideAlert() {
            const box = document.getElementById('alertBox');
            box.textContent = '';
            box.classList.remove('show');
        }

        function setLoading(state) {
            const btn = document.getElementById('btnLogin');
            if (state) {
                btn.classList.add('loading');
                btn.disabled = true;
            } else {
                btn.classList.remove('loading');
                btn.disabled = false;
            }
        }

        function clearFieldErrors() {
            document.getElementById('loginError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            document.getElementById('login').classList.remove('is-invalid');
            document.getElementById('password').classList.remove('is-invalid');
        }

        // ── Login Handler ─────────────────────────────────────────────
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            hideAlert();
            clearFieldErrors();
            setLoading(true);

            const login = document.getElementById('login').value.trim();
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;

            try {
                const response = await fetch(`${API_BASE}/auth/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ login, password }),
                });

                const data = await response.json();

                if (response.ok && data.status === 'ok') {
                    // ── Save token and user to localStorage ───────────
                    localStorage.setItem('auth_token', data.data.token);
                    localStorage.setItem('auth_user', JSON.stringify(data.data.user));

                    // If remember me checked — also persist in sessionStorage
                    if (remember) {
                        localStorage.setItem('remember_me', 'true');
                    }

                    // ── Redirect to dashboard ─────────────────────────
                    window.location.href = '{{ route('admin.dashboard') }}';

                } else if (response.status === 422) {
                    // Validation errors
                    const errors = data.errors ?? {};

                    if (errors.login) {
                        document.getElementById('loginError').textContent = errors.login[0];
                        document.getElementById('login').classList.add('is-invalid');
                    }
                    if (errors.password) {
                        document.getElementById('passwordError').textContent = errors.password[0];
                        document.getElementById('password').classList.add('is-invalid');
                    }
                    if (!errors.login && !errors.password) {
                        showAlert(data.message ?? 'Invalid credentials.');
                    }

                } else if (response.status === 403) {
                    showAlert('Your account has been disabled. Please contact an administrator.');

                } else {
                    showAlert(data.message ?? 'Something went wrong. Please try again.');
                }

            } catch (err) {
                showAlert('Cannot connect to server. Please check your connection.');
                console.error(err);
            } finally {
                setLoading(false);
            }
        });

        // ── Auto-redirect if already logged in ───────────────────────
        (function () {
            const token = localStorage.getItem('auth_token');
            if (token) {
                window.location.href = '{{ route('admin.dashboard') }}';
            }
        })();
    </script>
</body>

</html>