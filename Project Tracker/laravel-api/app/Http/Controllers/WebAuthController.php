<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->must_change_password) {
                return redirect()->route('password.change');
            }

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if (Auth::user()->role === 'investor') {
                return redirect()->route('investor.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $loginAs = $request->input('login_as') === 'admin' ? 'admin' : 'user';

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ])
                ->withInput($request->only('email', 'remember', 'login_as'));
        }

        if ($loginAs === 'admin' && Auth::user()->role !== 'admin') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'email' => 'This account does not have administrator access. Switch to "User" to sign in.',
                ])
                ->withInput($request->only('email', 'remember', 'login_as'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->must_change_password) {
            if ($user->temporary_password_expires_at && $user->temporary_password_expires_at->isPast()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your temporary password has expired. Please contact your administrator for a new one.',
                ]);
            }

            return redirect()->route('password.change');
        }

        if ($loginAs === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'investor') {
            return redirect()->route('investor.dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink([
            'email' => $data['email'],
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput();
    }
    public function showResetPassword(string $token)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                    'must_change_password' => false,
                    'temporary_password_expires_at' => null,
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', 'Your password has been reset successfully. You can now sign in.');
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }
    public function showRegister()
    {
        if (Auth::check()) {
            if (Auth::user()->must_change_password) {
                return redirect()->route('password.change');
            }

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if (Auth::user()->role === 'investor') {
                return redirect()->route('investor.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showInvestorRegister()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'investor') {
                return redirect()->route('investor.dashboard');
            }

            return redirect()->route(Auth::user()->role === 'admin'
                ? 'admin.dashboard'
                : 'dashboard');
        }

        return view('auth.investor-register');
    }

    public function registerInvestor(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'investor',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('investor.dashboard');
    }
    public function showChangePassword()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Auth::validate([
            'email' => $user->email,
            'password' => $request->current_password,
        ])) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => $request->password,
            'must_change_password' => false,
            'temporary_password_expires_at' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password changed successfully. Welcome to Project Tracker!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}





