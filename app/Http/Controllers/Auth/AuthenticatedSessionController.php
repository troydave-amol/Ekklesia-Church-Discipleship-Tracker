<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Services\AuthService;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    // GET /login — KEEP exactly as Breeze generated it
    public function create(): View
    {
        return view('auth.login');
    }

    // POST /login — only the last line changes
    public function store(LoginRequest $request): RedirectResponse
    {
        // Breeze's LoginRequest handles Auth::attempt() + rate throttle
        $request->authenticate();
        $request->session()->regenerate();

        // YOUR ADDITION: church check + role-based redirect
        $redirect = $this->authService->getRoleRedirect(Auth::user());

        return redirect()->intended($redirect);
    }

    // DELETE /logout — KEEP exactly as Breeze generated it
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}