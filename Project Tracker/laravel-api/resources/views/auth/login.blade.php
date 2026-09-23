<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign In | Project Tracker</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        body {
            background: #f8fafc;
        }

        button,
        input {
            font: inherit;
        }

        /* =========================
           MAIN AUTH SCREEN
        ========================= */

        .auth-screen {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            background: #ffffff;
        }

        /* =========================
           LEFT BRAND PANEL
        ========================= */

        .auth-brand {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(
                    circle at 20% 20%,
                    rgba(99, 102, 241, 0.25),
                    transparent 32%
                ),
                linear-gradient(
                    145deg,
                    #111827 0%,
                    #172554 45%,
                    #312e81 100%
                );
            color: #ffffff;
        }

        .auth-brand-inner {
            position: relative;
            z-index: 2;
            max-width: 650px;
            min-height: 100vh;
            margin: 0 auto;
            padding: 64px 70px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 65px;
        }

        .auth-logo-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.35);
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .auth-logo-text {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .auth-headline {
            max-width: 570px;
            margin: 0;
            font-size: clamp(42px, 4vw, 64px);
            line-height: 1.04;
            letter-spacing: -2.8px;
            font-weight: 800;
        }

        .auth-subtext {
            max-width: 510px;
            margin: 24px 0 0;
            color: rgba(255, 255, 255, 0.68);
            font-size: 17px;
            line-height: 1.7;
        }

        /* =========================
           VISUAL CARDS
        ========================= */

        .auth-visual {
            position: relative;
            height: 240px;
            margin-top: 55px;
        }

        .auth-card {
            position: absolute;
            width: 250px;
            padding: 17px 18px;
            border: 1px solid rgba(255, 255, 255, 0.13);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.09);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.18);
        }

        .auth-card-1 {
            top: 10px;
            left: 10px;
            transform: rotate(-3deg);
        }

        .auth-card-2 {
            top: 82px;
            left: 175px;
            transform: rotate(2deg);
        }

        .auth-card-3 {
            top: 158px;
            left: 30px;
            width: 235px;
            transform: rotate(-1deg);
        }

        .auth-card-row {
            display: flex;
            align-items: center;
            gap: 9px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            font-weight: 600;
        }

        .auth-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex: 0 0 auto;
        }

        .auth-dot-success {
            background: #34d399;
            box-shadow: 0 0 12px rgba(52, 211, 153, 0.8);
        }

        .auth-dot-warning {
            background: #fbbf24;
            box-shadow: 0 0 12px rgba(251, 191, 36, 0.7);
        }

        .auth-check {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #34d399;
            color: #064e3b;
            font-size: 11px;
            font-weight: 900;
        }

        .auth-progress {
            height: 5px;
            margin-top: 13px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.13);
        }

        .auth-progress-bar {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #818cf8, #a78bfa);
        }

        .auth-progress-label {
            display: block;
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.52);
            font-size: 10px;
        }

        .auth-shape {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-shape-1 {
            width: 180px;
            height: 180px;
            right: -80px;
            bottom: -100px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .auth-shape-2 {
            width: 260px;
            height: 260px;
            right: -160px;
            top: -130px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* =========================
           RIGHT FORM PANEL
        ========================= */

        .auth-form-side {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 30px;
            background: #ffffff;
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 430px;
        }

        .auth-form-header {
            margin-bottom: 34px;
        }

        .auth-form-header h2 {
            margin: 0;
            color: #111827;
            font-size: 34px;
            line-height: 1.2;
            letter-spacing: -1.2px;
            font-weight: 800;
        }

        .auth-form-header p {
            margin: 11px 0 0;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
        }

        /* =========================
           ALERT
        ========================= */

        .auth-alert {
            margin-bottom: 22px;
            padding: 13px 15px;
            border: 1px solid #fecaca;
            border-radius: 10px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 13px;
            line-height: 1.5;
        }

        /* =========================
           FORM FIELDS
        ========================= */

        .auth-field {
            margin-bottom: 21px;
        }

        .auth-field label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 13px;
            font-weight: 650;
        }

        .auth-field input {
            width: 100%;
            height: 50px;
            padding: 0 15px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            outline: none;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .auth-field input::placeholder {
            color: #9ca3af;
        }

        .auth-field input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .auth-field input.has-error {
            border-color: #ef4444;
        }

        .auth-password-wrap {
            position: relative;
        }

        .auth-password-wrap input {
            padding-right: 48px;
        }

        .auth-toggle-visibility {
            position: absolute;
            top: 50%;
            right: 12px;
            width: 30px;
            height: 30px;
            padding: 0;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #9ca3af;
            cursor: pointer;
            font-size: 16px;
        }

        .auth-toggle-visibility:hover {
            color: #4b5563;
        }

        .auth-field-error {
            display: block;
            margin-top: 7px;
            color: #dc2626;
            font-size: 12px;
        }

        /* =========================
           REMEMBER / FORGOT
        ========================= */

        .auth-row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin: 4px 0 25px;
        }

        .auth-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 13px;
            cursor: pointer;
        }

        .auth-checkbox input {
            width: 15px;
            height: 15px;
            margin: 0;
            accent-color: #6366f1;
            cursor: pointer;
        }

        .auth-link {
            color: #6366f1;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link:hover {
            color: #4f46e5;
            text-decoration: underline;
        }

        /* =========================
           SUBMIT BUTTON
        ========================= */

        .auth-submit {
            width: 100%;
            height: 51px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                opacity 0.2s ease;
        }

        .auth-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(99, 102, 241, 0.28);
        }

        .auth-submit:disabled {
            cursor: not-allowed;
            opacity: 0.7;
            transform: none;
        }

        .auth-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: auth-spin 0.7s linear infinite;
        }

        @keyframes auth-spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* =========================
        ========================= */

        .auth-switch {
            margin: 27px 0 0;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }

        .auth-switch a {
            margin-left: 4px;
            color: #6366f1;
            font-weight: 700;
            text-decoration: none;
        }

        .auth-switch a:hover {
            text-decoration: underline;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {
            .auth-screen {
                grid-template-columns: 1fr;
            }

            .auth-brand {
                display: none;
            }

            .auth-form-side {
                min-height: 100vh;
                padding: 35px 22px;
            }
        }

        @media (max-width: 480px) {
            .auth-form-header h2 {
                font-size: 29px;
            }

            .auth-row-between {
                align-items: flex-start;
                flex-direction: column;
            }

            .auth-link {
                align-self: flex-end;
            }
        }
    </style>
</head>

<body>

<div class="auth-screen">

    {{-- =========================
         LEFT BRAND PANEL
    ========================== --}}
    <div class="auth-brand">

        <div class="auth-brand-inner">

            <div class="auth-logo">
                <span class="auth-logo-mark">PT</span>
                <span class="auth-logo-text">Project Tracker</span>
            </div>

            <h1 class="auth-headline">
                Turn your projects into progress.
            </h1>

            <p class="auth-subtext">
                Plan, manage, and track your team's work from one powerful workspace.
            </p>

            <div class="auth-visual">

                <div class="auth-card auth-card-1">
                    <div class="auth-card-row">
                        <span class="auth-dot auth-dot-success"></span>
                        <span>Website Redesign</span>
                    </div>

                    <div class="auth-progress">
                        <div
                            class="auth-progress-bar"
                            style="width: 72%;"
                        ></div>
                    </div>

                    <span class="auth-progress-label">
                        72% complete
                    </span>
                </div>

                <div class="auth-card auth-card-2">
                    <div class="auth-card-row">
                        <span class="auth-dot auth-dot-warning"></span>
                        <span>Mobile App Sprint</span>
                    </div>

                    <div class="auth-progress">
                        <div
                            class="auth-progress-bar"
                            style="width: 45%;"
                        ></div>
                    </div>

                    <span class="auth-progress-label">
                        45% complete
                    </span>
                </div>

                <div class="auth-card auth-card-3">
                    <div class="auth-card-row">
                        <span class="auth-check">✓</span>
                        <span>12 tasks completed today</span>
                    </div>
                </div>

                <div class="auth-shape auth-shape-1"></div>
                <div class="auth-shape auth-shape-2"></div>

            </div>
        </div>
    </div>

    {{-- =========================
         RIGHT LOGIN FORM
    ========================== --}}
    <div class="auth-form-side">

        <div class="auth-form-wrap">

            <div class="auth-form-header">
                <h2>Welcome back</h2>

                <p>
                    Sign in to continue to your Project Tracker workspace.
                </p>
            </div>

            {{-- General error --}}
            @if ($errors->any())
                <div class="auth-alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="auth-alert" style="
                    border-color: #bbf7d0;
                    background: #f0fdf4;
                    color: #15803d;
                ">
                    {{ session('status') }}
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('login') }}"
                novalidate
            >
                @csrf

                {{-- Email --}}
                <div class="auth-field">

                    <label for="email">
                        Email address
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        placeholder="you@company.com"
                        class="{{ $errors->has('email') ? 'has-error' : '' }}"
                        required
                        autofocus
                    >

                    @if ($errors->has('email'))
                        <span class="auth-field-error">
                            {{ $errors->first('email') }}
                        </span>
                    @endif

                </div>

                {{-- Password --}}
                <div class="auth-field">

                    <label for="password">
                        Password
                    </label>

                    <div class="auth-password-wrap">

                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            class="{{ $errors->has('password') ? 'has-error' : '' }}"
                            required
                        >

                        <button
                            type="button"
                            class="auth-toggle-visibility"
                            id="togglePassword"
                            aria-label="Show password"
                        >
                            <span id="eyeIcon">◉</span>
                        </button>

                    </div>

                    @if ($errors->has('password'))
                        <span class="auth-field-error">
                            {{ $errors->first('password') }}
                        </span>
                    @endif

                </div>

                {{-- Remember me / Forgot password --}}
                <div class="auth-row-between">

                    <label class="auth-checkbox">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        <span>Remember me</span>

                    </label>

                    <span
                        class="auth-link"
                        title="Password recovery is not configured yet"
                    >
                        Forgot password?
                    </span>

                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="auth-submit"
                    id="loginButton"
                >
                    <span id="loginButtonText">
                        Sign In
                    </span>
                </button>

            </form>


        </div>
    </div>

</div>

<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';

        eyeIcon.textContent = isPassword ? '◉' : '◌';

        togglePassword.setAttribute(
            'aria-label',
            isPassword ? 'Hide password' : 'Show password'
        );
    });

    const loginForm = document.querySelector('form');
    const loginButton = document.getElementById('loginButton');
    const loginButtonText = document.getElementById('loginButtonText');

    loginForm.addEventListener('submit', function () {
        loginButton.disabled = true;
        loginButtonText.textContent = 'Signing in...';
    });
</script>

</body>
</html>
