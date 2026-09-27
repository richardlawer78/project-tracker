<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Project Tracker</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: linear-gradient(135deg, #111936, #18255c, #111936);
            font-family: Inter, system-ui, sans-serif;
            color: #fff;
        }
        .card {
            width: 100%;
            max-width: 410px;
            padding: 34px;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            box-shadow: 0 25px 60px rgba(0,0,0,.28);
            backdrop-filter: blur(18px);
        }
        .brand { text-align: center; margin-bottom: 24px; }
        .brand-mark {
            width: 48px; height: 48px; margin: 0 auto 12px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            font-size: 20px; font-weight: 800;
        }
        .brand-name { font-size: 18px; font-weight: 700; }
        h1 { margin: 0; text-align: center; font-size: 24px; }
        .subtitle {
            margin: 9px 0 24px;
            color: #c7cce5;
            font-size: 13px;
            line-height: 1.6;
            text-align: center;
        }
        .error {
            margin-bottom: 18px;
            padding: 11px 13px;
            border-radius: 10px;
            background: rgba(239,68,68,.13);
            border: 1px solid rgba(248,113,113,.3);
            color: #fecaca;
            font-size: 13px;
        }
        .field { margin-bottom: 16px; }
        label {
            display: block;
            margin-bottom: 7px;
            color: #e5e7eb;
            font-size: 13px;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 10px;
            outline: none;
            background: rgba(255,255,255,.09);
            color: #fff;
            font-size: 14px;
        }
        input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99,102,241,.18);
        }
        input::placeholder { color: #9ca3af; }
        button {
            width: 100%;
            margin-top: 4px;
            padding: 12px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }
        .back {
            display: block;
            margin-top: 20px;
            color: #c7d2fe;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <div class="brand-mark">PT</div>
            <div class="brand-name">Project Tracker</div>
        </div>

        <h1>Reset your password</h1>

        <p class="subtitle">
            Choose a new password for your Project Tracker account.
        </p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="field">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email', $email) }}"
                       autocomplete="email" required>
            </div>

            <div class="field">
                <label for="password">New password</label>
                <input id="password" type="password" name="password"
                       autocomplete="new-password" minlength="8" required
                       placeholder="At least 8 characters">
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" type="password"
                       name="password_confirmation"
                       autocomplete="new-password" minlength="8" required
                       placeholder="Repeat your new password">
            </div>

            <button type="submit">Reset password</button>
        </form>

        <a href="{{ route('login') }}" class="back">← Back to login</a>
    </div>
</body>
</html>