<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterChurchRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Church;
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
    public function showRegister(){ return view('auth.register'); }

    public function register(RegisterChurchRequest $request)
    {
        $pastor = DB::transaction(function () use ($request) {

            $church = Church::create([
                'name'              => $request->church_name,
                'subscription_plan' => $request->subscription_plan,
            ]);

            $pastor = User::create([
                'church_id'  => $church->id,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'role'       => 'pastor',
            ]);

            UserProfile::create(['user_id' => $pastor->id]);

            ChurchMembership::withoutGlobalScopes()->create([
                'user_id'   => $pastor->id,
                'church_id' => $church->id,
                'status'    => 'active',
                'joined_at' => now(),
            ]);

            return $pastor;
        });

        Auth::login($pastor);
        return redirect()->route('login');
    }

    public function login(LoginRequest $request)
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
        return redirect()->route('tenant.login');  // was 'login'
    }
}