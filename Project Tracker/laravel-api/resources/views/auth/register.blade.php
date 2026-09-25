<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up | Project Tracker</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        body {
            background: #111827;
        }

        button,
        input {
            font: inherit;
        }

        /* =========================
           MAIN AUTH SCREEN
           (mirrored: form panel first/left, brand panel second/right)
        ========================= */

        .auth-screen {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
        }

        /* =========================
           BRAND PANEL
        ========================= */

        .auth-brand {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(
                    circle at 80% 20%,
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
            left: -80px;
            bottom: -100px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .auth-shape-2 {
            width: 260px;
            height: 260px;
            left: -160px;
            top: -130px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* =========================
           FORM PANEL
           (same navy / indigo palette as the brand panel)
        ========================= */

        .auth-form-side {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 30px;
            overflow: hidden;
            background:
                radial-gradient(
                    circle at 20% 15%,
                    rgba(99, 102, 241, 0.22),
                    transparent 38%
                ),
                linear-gradient(
                    145deg,
                    #111827 0%,
                    #172554 45%,
                    #312e81 100%
                );
        }

        /* colorful blobs behind the glass so the blur has something to show */
        .auth-form-side::before,
        .auth-form-side::after {
            content: '';
            position: absolute;
            z-index: 0;
            border-radius: 50%;
            filter: blur(70px);
            pointer-events: none;
        }

        .auth-form-side::before {
            width: 340px;
            height: 340px;
            top: 6%;
            left: 8%;
            background: rgba(139, 92, 246, 0.55);
        }

        .auth-form-side::after {
            width: 300px;
            height: 300px;
            bottom: 4%;
            right: 6%;
            background: rgba(47, 214, 255, 0.28);
        }

        .auth-form-glow {
            position: absolute;
            z-index: 0;
            width: 560px;
            height: 560px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.3), transparent 62%);
            pointer-events: none;
        }

        /* =========================
           GLASS CARD
        ========================= */

        .auth-card-frame {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            background: linear-gradient(
                145deg,
                rgba(255, 255, 255, 0.14) 0%,
                rgba(255, 255, 255, 0.05) 100%
            );
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(26px) saturate(150%);
            -webkit-backdrop-filter: blur(26px) saturate(150%);
            box-shadow:
                0 30px 70px rgba(2, 6, 23, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                inset 0 0 40px rgba(255, 255, 255, 0.03);
        }

        /* animated gradient border ring (masked so only the ring shows
           and the glass card underneath stays see-through) */
        @property --auth-angle {
            syntax: '<angle>';
            initial-value: 0deg;
            inherits: false;
        }

        .auth-card-frame::before,
        .auth-card-frame::after {
            content: '';
            position: absolute;
            inset: -2px;
            padding: 3px;
            border-radius: inherit;
            background: conic-gradient(
                from var(--auth-angle),
                rgba(255, 255, 255, 0.16) 0%,
                #06b6d4 5%,
                #22d3ee 9%,
                #ffffff 13%,
                rgba(255, 255, 255, 0.16) 30%,
                rgba(255, 255, 255, 0.16) 50%,
                #f59e0b 55%,
                #fbbf24 59%,
                #ffffff 63%,
                rgba(255, 255, 255, 0.16) 80%,
                rgba(255, 255, 255, 0.16) 100%
            );
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask:
                linear-gradient(#000 0 0) content-box exclude,
                linear-gradient(#000 0 0);
            pointer-events: none;
            animation: auth-border-spin 3.5s linear infinite;
        }

        .auth-card-frame::before {
            z-index: 2;
        }

        /* blurred copy of the ring = glow that follows the trails */
        .auth-card-frame::after {
            z-index: 0;
            filter: blur(9px);
            opacity: 0.9;
        }

        @keyframes auth-border-spin {
            to {
                --auth-angle: 360deg;
            }
        }

        .auth-form-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            border-radius: 24px;
            padding: 44px 40px 38px;
            background: transparent;
        }

        .auth-form-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .auth-form-header h2 {
            margin: 0;
            color: #ffffff;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 0 0 22px rgba(167, 139, 250, 0.6);
        }

        .auth-form-header p {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, 0.62);
            font-size: 13px;
            line-height: 1.6;
        }

        /* =========================
           ALERT
        ========================= */

        .auth-alert {
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid rgba(248, 113, 113, 0.4);
            border-radius: 10px;
            background: rgba(248, 113, 113, 0.1);
            color: #fecaca;
            font-size: 13px;
            line-height: 1.5;
        }

        /* =========================
           FORM FIELDS
        ========================= */

        .auth-field {
            position: relative;
            margin-bottom: 26px;
        }

        .auth-field label {
            display: block;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.75);
            font-size: 12px;
            font-weight: 650;
            letter-spacing: 0.3px;
        }

        .auth-field-icon {
            position: absolute;
            left: 2px;
            bottom: 11px;
            color: #a5b4fc;
            font-size: 14px;
            pointer-events: none;
        }

        .auth-field input {
            width: 100%;
            height: 38px;
            padding: 0 26px 0 24px;
            border: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.28);
            outline: none;
            background: transparent;
            color: #ffffff;
            font-size: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .auth-field input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .auth-field input:focus {
            border-color: #a5b4fc;
            box-shadow: 0 1px 0 0 #a5b4fc;
        }

        .auth-field input.has-error {
            border-color: #f87171;
        }

        /* keep browser autofill from painting a white box over the glass */
        .auth-field input:-webkit-autofill,
        .auth-field input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff;
            transition: background-color 9999s ease-in-out 0s;
        }

        .auth-password-wrap {
            position: relative;
        }

        .auth-password-wrap input {
            padding-right: 34px;
        }

        .auth-toggle-visibility {
            position: absolute;
            right: 0;
            bottom: 6px;
            width: 26px;
            height: 26px;
            padding: 0;
            border: 0;
            background: transparent;
            color: rgba(165, 180, 252, 0.75);
            cursor: pointer;
            font-size: 14px;
        }

        .auth-toggle-visibility:hover {
            color: #ffffff;
        }

        .auth-field-error {
            display: block;
            margin-top: 7px;
            color: #fca5a5;
            font-size: 12px;
        }

        /* =========================
           SUBMIT BUTTON
        ========================= */

        .auth-submit {
            width: 100%;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .auth-submit:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
            box-shadow: 0 14px 36px rgba(99, 102, 241, 0.55);
        }

        .auth-submit:disabled {
            cursor: not-allowed;
            opacity: 0.6;
            transform: none;
        }

        /* =========================
           SWITCH ROW
        ========================= */

        .auth-switch {
            margin: 24px 0 0;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
        }

        .auth-switch a {
            margin-left: 4px;
            color: #a5b4fc;
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
                font-size: 27px;
            }

            .auth-form-wrap {
                padding: 34px 24px 30px;
            }
        }
    </style>
</head>

<body>

<div class="auth-screen">

    {{-- =========================
         LEFT: SIGN UP FORM
    ========================== --}}
    <div class="auth-form-side">

        <div class="auth-form-glow"></div>

        <div class="auth-card-frame">
            <div class="auth-form-wrap">

                <div class="auth-form-header">
                    <h2>Sign Up</h2>

                    <p>
                        Create an account to start using Project Tracker.
                    </p>
                </div>

                {{-- General error --}}
                @if ($errors->any())
                    <div class="auth-alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('signup') }}"
                    novalidate
                >
                    @csrf

                    {{-- Name --}}
                    <div class="auth-field">

                        <label for="name">
                            Full name
                        </label>

                        <span class="auth-field-icon">&#128100;</span>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            placeholder="Jane Doe"
                            class="{{ $errors->has('name') ? 'has-error' : '' }}"
                            required
                            autofocus
                        >

                        @if ($errors->has('name'))
                            <span class="auth-field-error">
                                {{ $errors->first('name') }}
                            </span>
                        @endif

                    </div>

                    {{-- Email --}}
                    <div class="auth-field">

                        <label for="email">
                            Email address
                        </label>

                        <span class="auth-field-icon">&#9993;</span>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            placeholder="you@company.com"
                            class="{{ $errors->has('email') ? 'has-error' : '' }}"
                            required
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

                        <span class="auth-field-icon">&#128274;</span>

                        <div class="auth-password-wrap">

                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Create a password"
                                class="{{ $errors->has('password') ? 'has-error' : '' }}"
                                required
                            >

                            <button
                                type="button"
                                class="auth-toggle-visibility"
                                id="togglePassword"
                                aria-label="Show password"
                            >
                                <span id="eyeIcon">&#9673;</span>
                            </button>

                        </div>

                        @if ($errors->has('password'))
                            <span class="auth-field-error">
                                {{ $errors->first('password') }}
                            </span>
                        @endif

                    </div>

                    {{-- Confirm Password --}}
                    <div class="auth-field">

                        <label for="password_confirmation">
                            Confirm password
                        </label>

                        <span class="auth-field-icon">&#128274;</span>

                        <div class="auth-password-wrap">

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Re-enter your password"
                                required
                            >

                            <button
                                type="button"
                                class="auth-toggle-visibility"
                                id="togglePasswordConfirm"
                                aria-label="Show password"
                            >
                                <span id="eyeIconConfirm">&#9673;</span>
                            </button>

                        </div>

                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="auth-submit"
                        id="signupButton"
                    >
                        <span id="signupButtonText">
                            Create Account
                        </span>
                    </button>

                </form>

                <div class="auth-switch">
                    Already have an account?
                    <a href="{{ route('login') }}">Log in</a>
                </div>

            </div>
        </div>

    </div>

    {{-- =========================
         RIGHT: BRAND PANEL
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
                        <span class="auth-check">&#10003;</span>
                        <span>12 tasks completed today</span>
                    </div>
                </div>

                <div class="auth-shape auth-shape-1"></div>
                <div class="auth-shape auth-shape-2"></div>

            </div>
        </div>
    </div>

</div>

<script>
    function wirePasswordToggle(inputId, buttonId, iconId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        const icon = document.getElementById(iconId);

        button.addEventListener('click', function () {
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';

            icon.textContent = isPassword ? '\u25C9' : '\u25CC';

            button.setAttribute(
                'aria-label',
                isPassword ? 'Hide password' : 'Show password'
            );
        });
    }

    wirePasswordToggle('password', 'togglePassword', 'eyeIcon');
    wirePasswordToggle('password_confirmation', 'togglePasswordConfirm', 'eyeIconConfirm');

    const signupForm = document.querySelector('form');
    const signupButton = document.getElementById('signupButton');
    const signupButtonText = document.getElementById('signupButtonText');

    signupForm.addEventListener('submit', function () {
        signupButton.disabled = true;
        signupButtonText.textContent = 'Creating account...';
    });
</script>

</body>
</html>