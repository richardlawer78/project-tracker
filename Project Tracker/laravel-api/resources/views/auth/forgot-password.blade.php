<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Project Tracker</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at 20% 20%, rgba(99,102,241,.28), transparent 35%),
                radial-gradient(circle at 80% 80%, rgba(6,182,212,.20), transparent 35%),
                linear-gradient(135deg, #111936 0%, #18255c 55%, #111936 100%);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
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

        .brand {
            text-align: center;
            margin-bottom: 26px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            font-size: 22px;
            font-weight: 800;
            box-shadow: 0 10px 25px rgba(99,102,241,.35);
        }

        .brand-name {
            font-size: 18px;
            font-weight: 700;
        }

        h1 {
            margin: 0;
            font-size: 25px;
            font-weight: 750;
            text-align: center;
        }

        .subtitle {
            margin: 9px 0 25px;
            color: #c7cce5;
            font-size: 13px;
            line-height: 1.6;
            text-align: center;
        }

        .message {
            margin-bottom: 18px;
            padding: 11px 13px;
            border-radius: 10px;
            font-size: 13px;
        }

        .success {
            background: rgba(16,185,129,.13);
            border: 1px solid rgba(52,211,153,.3);
            color: #a7f3d0;
        }

        .error {
            background: rgba(239,68,68,.13);
            border: 1px solid rgba(248,113,113,.3);
            color: #fecaca;
        }

        label {
            display: block;
            margin-bottom: 8px;
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

        input::placeholder {
            color: #9ca3af;
        }

        input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99,102,241,.18);
        }

        button {
            width: 100%;
            margin-top: 18px;
            padding: 12px 16px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(99,102,241,.28);
        }

        button:hover {
            filter: brightness(1.08);
        }

        .back {
            display: block;
            margin-top: 22px;
            color: #c7d2fe;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
        }

        .back:hover {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <div class="brand-mark">PT</div>
            <div class="brand-name">Project Tracker</div>
        </div>

        <h1>Forgot your password?</h1>

        <p class="subtitle">
            Enter your email address and we'll send you a password reset link.
        </p>

        @if (session('status'))
            <div class="message success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="message error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label for="email">Email address</label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                autocomplete="email"
                required
                autofocus
            >

            <button type="submit">
                Send reset link
            </button>
        </form>

        <a href="{{ route('login') }}" class="back">
            ← Back to login
        </a>
    </div>
</body>
</html>