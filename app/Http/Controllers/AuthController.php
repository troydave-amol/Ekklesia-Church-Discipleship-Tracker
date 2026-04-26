<?php

namespace App\Http\Controllers;

use App\Models\MemberJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MemberJoinRequestService;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()  { return view('auth.login'); }

    /**
     * Show the member self-registration form (tenant-scoped).
     */
    public function showMemberRegister()
    {
        return view('auth.member-register');
    }

    /**
     * Process member self-registration.
     * Stores a pending join request for pastor approval.
     */
    public function memberRegister(Request $request, MemberJoinRequestService $memberJoinRequestService)
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:255'],
            'last_name'      => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'requested_role' => ['required', 'in:disciple,program_leader'],
        ]);

        try {
            $memberJoinRequestService->submit($validated);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\RuntimeException $e) {
            return back()
                ->withErrors(['email' => $e->getMessage()])
                ->withInput();
        }

        return redirect()->route('login')
            ->with('success', 'Your join request has been submitted. You can sign in after your church administrator approves your membership.');
    }

    public function login(\App\Http\Requests\Auth\LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            if ($message = $this->memberJoinRequestLoginMessage((string) $request->input('email'))) {
                return back()->withErrors(['email' => $message])->onlyInput('email');
            }

            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }
        $request->session()->regenerate();
        return redirect()->route('tenant.dashboard');  // was 'dashboard'
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function memberJoinRequestLoginMessage(string $email): ?string
    {
        $email = strtolower(trim($email));

        if ($email === '') {
            return null;
        }

        $joinRequest = MemberJoinRequest::query()
            ->where('email', $email)
            ->latest('created_at')
            ->first();

        if (! $joinRequest) {
            return null;
        }

        return match ($joinRequest->status) {
            MemberJoinRequest::STATUS_PENDING => 'Your join request is still awaiting church approval.',
            MemberJoinRequest::STATUS_REJECTED => 'Your previous request was not approved. Submit a new request to try again.',
            default => null,
        };
    }
}
