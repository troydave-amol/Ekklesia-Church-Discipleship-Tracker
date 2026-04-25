<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\ChurchMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
     * Creates a user with 'pending' membership status for pastor approval.
     */
    public function memberRegister(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:255'],
            'last_name'      => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'requested_role' => ['required', 'in:disciple,program_leader'],
        ]);

        // Resolve the church for this tenant
        $church = Church::first();

        if (! $church) {
            return back()->withErrors(['email' => 'This church workspace is not yet set up.']);
        }

        $role = $validated['requested_role'];
        $roleId = Role::query()->where('slug', $role)->value('id');

        $member = DB::transaction(function () use ($validated, $church, $role, $roleId) {
            $member = User::create([
                'church_id'  => $church->id,
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'role'       => $role,
                'role_id'    => $roleId,
            ]);

            UserProfile::create(['user_id' => $member->id]);

            ChurchMembership::withoutGlobalScopes()->create([
                'user_id'   => $member->id,
                'church_id' => $church->id,
                'status'    => 'new_member',
                'joined_at' => now(),
            ]);

            return $member;
        });

        return redirect()->route('login')
            ->with('success', 'Your request has been submitted! Your church administrator will review and approve your membership.');
    }

    public function login(\App\Http\Requests\Auth\LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
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
}