<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Try to authenticate with admin guard
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $admin = Auth::guard('admin')->user();

            // Check if admin account is active
            if ($admin->status !== 'active') {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Your account is inactive.']);
            }

            // Update last login
            $admin->update(['last_login_at' => now()]);

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showPasswordResetForm(): View
    {
        return view('admin.auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $admin = AdminUser::where('email', $request->email)->first();

        if (! $admin) {
            return back()->with('status', 'If an account exists, you will receive a password reset link.');
        }

        // TODO: Implement password reset logic
        return back()->with('status', 'If an account exists, you will receive a password reset link.');
    }
}
