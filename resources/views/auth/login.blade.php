<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Horizon Unlimited | Account Access</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" sizes="32x32" href="{{ asset('backend/imgs/logo.png') }}" />
    <style>
        :root {
            --primary: #f5393d;
            --secondary: #ff7b39;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: #d7dfee;
            --panel-bg: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(115deg, #dfe8ff, #eef3ff 55%, #f3f6ff);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            color: var(--text-dark);
            position: relative;
        }

        .bg-ornament {
            position: fixed;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(0, 97, 255, 0.15), transparent 70%);
            filter: blur(2px);
            z-index: 0;
        }

        .bg-ornament-one {
            top: 40px;
            left: 40px;
        }

        .bg-ornament-two {
            bottom: 30px;
            right: 60px;
        }

        .page-wrapper {
            width: min(1200px, 100%);
            background: #ffffff;
            border-radius: 36px;
            box-shadow: 0 35px 90px rgba(15, 23, 42, 0.25);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .brand-panel {
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.3), transparent 45%), linear-gradient(170deg, #f53844 10%, #f24a3a 45%, #ff8743 90%);
            color: #ffffff;
            padding: 56px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            justify-content: space-between;
        }

        .logo-lockup {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .logo-lockup img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            object-fit: contain;
        }

        .logo-lockup span {
            text-transform: uppercase;
            letter-spacing: 0.3em;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .brand-panel h1 {
            font-size: 2.6rem;
            margin: 0;
        }

        .brand-panel .lead {
            font-size: 1.05rem;
            line-height: 1.7;
            margin: 0;
            color: rgba(255, 255, 255, 0.95);
        }

        .benefits {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
            font-size: 0.98rem;
        }

        .benefits li {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .benefits li::before {
            content: '*';
            color: #fff;
            font-weight: 700;
        }

        .metric-grid {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
        }

        .metric-number {
            font-size: 2.1rem;
            font-weight: 600;
            margin: 0;
        }

        .metric-label {
            margin: 4px 0 0;
            opacity: 0.85;
        }

        .auth-panel {
            background: var(--panel-bg);
            padding: 48px;
            position: relative;
        }

        .auth-panel::before {
            content: '';
            position: absolute;
            inset: 22px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            pointer-events: none;
        }

        .auth-panel > * {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.35em;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .auth-panel h2 {
            margin: 6px 0 6px;
            font-size: 2.2rem;
        }

        .helper {
            margin: 0 0 24px;
            color: var(--text-muted);
            max-width: 420px;
        }

        .server-status {
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 0.92rem;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .server-status.error {
            background: #ffe2e0;
            color: #c3261e;
        }

        .server-status.success {
            background: #e0f8ec;
            color: #0f9d58;
        }

        .tab-switcher {
            display: flex;
            gap: 8px;
            background: #fff;
            padding: 6px;
            border-radius: 999px;
            margin-bottom: 24px;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.06);
        }

        .tab-switcher button {
            flex: 1;
            border: none;
            background: transparent;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-switcher button.active {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            color: #fff;
            box-shadow: 0 10px 30px rgba(245, 57, 61, 0.25);
        }

        .form-panels {
            background: #fff;
            border-radius: 28px;
            padding: 32px;
            box-shadow: 0 25px 70px rgba(15, 23, 42, 0.15);
        }

        .form-panel {
            display: none;
            flex-direction: column;
            gap: 16px;
        }

        .form-panel.active {
            display: flex;
        }

        .input-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .input-wrapper {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-wrapper:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 97, 255, 0.12);
        }

        .input-icon {
            width: 20px;
            height: 20px;
            color: var(--primary);
            flex-shrink: 0;
        }

        .input-control {
            border: none;
            outline: none;
            flex: 1;
            font-size: 1rem;
            background: transparent;
            color: var(--text-dark);
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .checkbox input {
            width: 18px;
            height: 18px;
        }

        .link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        .primary-btn {
            border: none;
            border-radius: 18px;
            padding: 14px 16px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            cursor: pointer;
            transition: transform 0.2s ease;
            box-shadow: 0 20px 38px rgba(245, 57, 61, 0.25);
        }

        .primary-btn:hover {
            transform: translateY(-1px);
        }

        .ghost-btn {
            border: 1px solid var(--border);
            background: #f4f6fb;
            color: var(--text-dark);
            border-radius: 14px;
            padding: 12px 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ghost-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .code-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .note {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .status-toast {
            position: absolute;
            right: 48px;
            bottom: 48px;
            min-width: 260px;
            padding: 18px 22px;
            border-radius: 18px;
            color: #fff;
            font-weight: 500;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.35);
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .status-toast.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .status-toast[data-state='success'] {
            background: linear-gradient(160deg, #0f9d58, #2bc870);
        }

        .status-toast[data-state='error'] {
            background: linear-gradient(160deg, #d92d20, #f97066);
        }

        .help-text {
            margin-top: 22px;
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        .help-text a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 1100px) {
            body {
                padding: 18px;
            }

            .brand-panel,
            .auth-panel {
                padding: 36px;
            }
        }

        @media (max-width: 768px) {
            .tab-switcher {
                flex-direction: column;
            }

            .form-panels {
                padding: 22px;
            }

            .status-toast {
                position: relative;
                right: unset;
                bottom: unset;
                margin-top: 18px;
            }
        }
    </style>
</head>
@php
    $activePanel = request('intent', old('intent', 'login'));
    $siteInfo = DB::table('site_information')->first();
@endphp
<body data-active-panel="{{ $activePanel }}">
    <span class="bg-ornament bg-ornament-one" aria-hidden="true"></span>
    <span class="bg-ornament bg-ornament-two" aria-hidden="true"></span>
    <main class="page-wrapper">
        <section class="brand-panel">
            <div>
                <a href="{{route('home.index')}}" style="text-decoration:none; color:white; font=weight:bold">
                <div class="logo-lockup">
                    <img src="{{ asset($siteInfo->logo) }}" alt="Horizon Unlimited logo" />
                    <span>HORIZON UNLIMITED</span>
                </div>
                </a>
                <h1>Grow with Horizon Unlimited</h1>
                <p class="lead">
                    Upskill with immersive lessons, collaborative cohorts, and weekly mentorship sessions.
                    Your next certification is only a few clicks away.
                </p>
            </div>
            <ul class="benefits">
                <li>Unlimited access to premium courses and live cohorts</li>
                <li>Personalized learning paths curated from your goals</li>
                <li>Track progress and badges directly from this dashboard</li>
            </ul>
            <div class="metric-grid">
                <div>
                    <p class="metric-number">120k+</p>
                    <p class="metric-label">Learners worldwide</p>
                </div>
                <div>
                    <p class="metric-number">4.9/5</p>
                    <p class="metric-label">Average satisfaction</p>
                </div>
            </div>
        </section>
        <section class="auth-panel">
            @if ($errors->any())
                <div class="server-status error">
                    {{ $errors->first() }}
                </div>
            @endif
            @if (session('status'))
                <div class="server-status success">
                    {{ session('status') }}
                </div>
            @endif
            <p class="eyebrow">Horizon Portal</p>
            <h2>Access your Horizon Unlimited account</h2>
            <p class="helper">Use your email address to log in, register, or recover your password securely.</p>
            <div class="tab-switcher">
                <button type="button" class="{{ $activePanel === 'login' ? 'active' : '' }}" data-target="login">Login</button>
                <button type="button" class="{{ $activePanel === 'register' ? 'active' : '' }}" data-target="register">Register</button>
                <button type="button" class="{{ $activePanel === 'reset' ? 'active' : '' }}" data-target="reset">Reset Password</button>
            </div>
            <div class="form-panels">
                <form id="loginForm" class="form-panel {{ $activePanel === 'login' ? 'active' : '' }}" data-panel="login" autocomplete="on" method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="intent" value="login" />
                    <div>
                        <label for="loginEmail" class="input-label">Email address</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8" />
                            </svg>
                            <input type="email" id="loginEmail" name="email" class="input-control" placeholder="you@email.com" value="{{ old('email') }}" required />
                        </div>
                    </div>
                    <div>
                        <label for="loginPassword" class="input-label">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V7a5 5 0 00-10 0v4" />
                                <rect width="14" height="9" x="5" y="11" rx="2" />
                            </svg>
                            <input type="password" id="loginPassword" name="password" class="input-control" placeholder="Enter your password" required />
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="checkbox">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                            <span>Keep me signed in</span>
                        </label>
                        <a class="link" href="#" data-open-panel="reset">Forgot password?</a>
                    </div>
                    <button type="submit" class="primary-btn">Login</button>
                </form>
                <form id="registerForm" class="form-panel {{ $activePanel === 'register' ? 'active' : '' }}" data-panel="register" autocomplete="off" method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="intent" value="register" />
                    <div>
                        <label for="registerName" class="input-label">Full name</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0114 0" />
                                <circle cx="12" cy="8" r="4" />
                            </svg>
                            <input type="text" id="registerName" name="name" class="input-control" placeholder="Jane Doe" value="{{ old('name') }}" required />
                        </div>
                    </div>
                    <div>
                        <label for="registerEmail" class="input-label">Email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8" />
                            </svg>
                            <input type="email" id="registerEmail" name="email" class="input-control" placeholder="name@domain.com" value="{{ old('email') }}" required />
                        </div>
                    </div>
                    <!-- <div>
                        <label for="activationCode" class="input-label">Activation code</label>
                        <div class="code-row">
                            <div class="input-wrapper" style="flex: 1">
                                <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5l6 6-6 6-6-6 6-6z" />
                                </svg>
                                <input type="text" id="activationCode" name="activation_code" class="input-control" placeholder="Enter 6 digit code" inputmode="numeric" value="{{ old('activation_code') }}" />
                            </div>
                            <button type="button" id="activationButton" class="ghost-btn" data-endpoint="{{ url('/activation-code/send') }}">Send activation code</button>
                        </div>
                        <p class="note">We will email an activation code so only verified students can enroll.</p>
                    </div> -->
                    <div>
                        <label for="registerPassword" class="input-label">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V7a5 5 0 00-10 0v4" />
                                <rect width="14" height="9" x="5" y="11" rx="2" />
                            </svg>
                            <input type="password" id="registerPassword" name="password" class="input-control" placeholder="Create a password" required minlength="8" />
                        </div>
                    </div>
                    <div>
                        <label for="confirmPassword" class="input-label">Confirm password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V7a5 5 0 00-10 0v4" />
                                <rect width="14" height="9" x="5" y="11" rx="2" />
                            </svg>
                            <input type="password" id="confirmPassword" name="password_confirmation" class="input-control" placeholder="Re-enter password" required minlength="8" />
                        </div>
                    </div>
                    <button type="submit" class="primary-btn">Create account</button>
                </form>
                <form id="resetForm" class="form-panel {{ $activePanel === 'reset' ? 'active' : '' }}" data-panel="reset" autocomplete="off" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <input type="hidden" name="intent" value="reset" />
                    <p class="note">Enter your account email and we will send you a secure password reset link. You can return here with the link code to update your password.</p>
                    <div>
                        <label for="resetEmail" class="input-label">Account email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8" />
                            </svg>
                            <input type="email" id="resetEmail" name="email" class="input-control" placeholder="name@domain.com" value="{{ old('email') }}" required />
                        </div>
                    </div>
                    <button type="submit" class="primary-btn">Email reset link</button>
                </form>
            </div>
            <div class="status-toast" id="statusToast" role="status" aria-live="polite"></div>
            <p class="help-text">Need help with onboarding? <a href="mailto:support@horizononline.com">Contact support</a>.</p>
        </section>
    </main>
    <script>
        (function () {
            const tabButtons = document.querySelectorAll('.tab-switcher button');
            const panels = document.querySelectorAll('.form-panel');
            const statusToast = document.getElementById('statusToast');
            const defaultPanel = document.body.dataset.activePanel || 'login';

            const showToast = (state, message) => {
                if (!message) {
                    return;
                }
                statusToast.textContent = message;
                statusToast.dataset.state = state;
                statusToast.classList.add('visible');
                clearTimeout(showToast.timer);
                showToast.timer = setTimeout(() => statusToast.classList.remove('visible'), 6000);
            };

            const switchPanel = (panelName) => {
                panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.panel === panelName));
                tabButtons.forEach((button) => button.classList.toggle('active', button.dataset.target === panelName));
            };

            switchPanel(defaultPanel);

            tabButtons.forEach((button) => {
                button.addEventListener('click', () => switchPanel(button.dataset.target));
            });

            document.querySelectorAll('[data-open-panel]').forEach((link) => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    switchPanel(link.dataset.openPanel);
                });
            });

            const activationButton = document.getElementById('activationButton');
            if (activationButton) {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                activationButton.addEventListener('click', async () => {
                    const emailInput = document.getElementById('registerEmail');
                    const email = emailInput.value.trim();
                    if (!email) {
                        showToast('error', 'Enter your email first.');
                        emailInput.focus();
                        return;
                    }
                    const endpoint = activationButton.dataset.endpoint;
                    if (!endpoint || endpoint === '#') {
                        showToast('error', 'Activation endpoint is not configured.');
                        return;
                    }
                    activationButton.disabled = true;
                    const originalText = activationButton.textContent;
                    activationButton.textContent = 'Sending...';
                    try {
                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf
                            },
                            body: JSON.stringify({ email })
                        });
                        let data = {};
                        try {
                            data = await response.json();
                        } catch (error) {
                            data = {};
                        }
                        if (!response.ok) {
                            throw new Error(data.message || 'Unable to send the activation code.');
                        }
                        showToast('success', data.message || 'Activation code sent. Check your inbox.');
                    } catch (error) {
                        showToast('error', error.message || 'Unable to send the activation code.');
                    } finally {
                        activationButton.disabled = false;
                        activationButton.textContent = originalText;
                    }
                });
            }
        })();
    </script>
</body>
</html>
