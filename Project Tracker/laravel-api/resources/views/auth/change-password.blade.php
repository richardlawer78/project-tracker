<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password | Project Tracker</title>

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

        .auth-screen {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
        }

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
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(42px, 4vw, 64px);
            line-height: 1.04;
            letter-spacing: -2px;
            font-weight: 700;
        }

        .auth-subtext {
            max-width: 510px;
            margin: 24px 0 0;
            color: rgba(255, 255, 255, 0.68);
            font-size: 17px;
            line-height: 1.7;
        }

        .auth-security-card {
            max-width: 470px;
            margin-top: 48px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.13);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .security-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .security-icon {
            width: 36px;
            height: 36px;
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(129, 140, 248, 0.18);
            color: #c7d2fe;
            font-size: 16px;
        }

        .security-title {
            margin: 0 0 4px;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
        }

        .security-text {
            margin: 0;
            color: rgba(255, 255, 255, 0.62);
            font-size: 13px;
            line-height: 1.55;
        }

        .auth-form-panel {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            background: #ffffff;
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 440px;
        }

        .form-kicker {
            margin: 0 0 10px;
            color: #6366f1;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .form-title {
            margin: 0;
            color: #111827;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 38px;
            line-height: 1.15;
            letter-spacing: -1px;
        }

        .form-intro {
            margin: 14px 0 28px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.65;
        }

        .required-notice {
            margin-bottom: 22px;
            padding: 13px 14px;
            border: 1px solid #e0e7ff;
            border-radius: 10px;
            background: #eef2ff;
            color: #3730a3;
            font-size: 13px;
            line-height: 1.55;
        }

        .alert-danger {
            margin-bottom: 20px;
            padding: 14px 16px;
            border: 1px solid #fecaca;
            border-radius: 10px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 13px;
        }

        .alert-danger ul {
            margin: 8px 0 0;
            padding-left: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 18px;
        }

        .form-group label {
            color: #374151;
            font-size: 13px;
            font-weight: 700;
        }

        .form-group input {
            width: 100%;
            min-height: 46px;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            background: #fff;
            color: #111827;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.10);
        }

        .form-help {
            margin: 0;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
        }

        .field-error {
            margin: 0;
            color: #dc2626;
            font-size: 12px;
        }

        .submit-btn {
            width: 100%;
            min-height: 46px;
            margin-top: 8px;
            border: 0;
            border-radius: 9px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(79, 70, 229, 0.20);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 13px 28px rgba(79, 70, 229, 0.26);
        }

        .form-footer {
            margin-top: 20px;
            color: #9ca3af;
            font-size: 12px;
            line-height: 1.55;
            text-align: center;
        }

        @media (max-width: 900px) {
            .auth-screen {
                grid-template-columns: 1fr;
            }

            .auth-brand {
                display: none;
            }

            .auth-form-panel {
                min-height: 100vh;
                padding: 32px 22px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-screen">
        <section class="auth-brand">
            <div class="auth-brand-inner">
                <div class="auth-logo">
                    <div class="auth-logo-mark">PT</div>
                    <div class="auth-logo-text">Project Tracker</div>
                </div>

                <h1 class="auth-headline">
                    One secure step before you get started.
                </h1>

                <p class="auth-subtext">
                    Your account was created with a temporary password.
                    Choose a permanent password to continue using Project Tracker.
                </p>

                <div class="auth-security-card">
                    <div class="security-row">
                        <div class="security-icon">✓</div>
                        <div>
                            <p class="security-title">Your account is protected</p>
                            <p class="security-text">
                                Your temporary password is only for initial access.
                                Your new password will become your permanent sign-in credential.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="auth-form-panel">
            <div class="auth-form-wrap">
                <p class="form-kicker">Account security</p>

                <h2 class="form-title">Set your new password</h2>

                <p class="form-intro">
                    Please enter your current temporary password, then choose a new
                    password that you will use for future sign-ins.
                </p>

                <div class="required-notice">
                    A password change is required before you can continue to Project Tracker.
                </div>

                @if($errors->any())
                    <div class="alert-danger">
                        <strong>Please correct the following:</strong>

                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.change.update') }}">
                    @csrf

                    <div class="form-group">
                        <label for="current_password">Current password</label>

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            autocomplete="current-password"
                            required
                        >

                        @error('current_password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">New password</label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                        <p class="form-help">
                            Your new password must contain at least 8 characters.
                        </p>

                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm new password</label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                        @error('password_confirmation')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="submit-btn">
                        Set New Password
                    </button>
                </form>

                <p class="form-footer">
                    Your temporary password expires after 24 hours.
                </p>
            </div>
        </main>
    </div>
</body>
</html>
