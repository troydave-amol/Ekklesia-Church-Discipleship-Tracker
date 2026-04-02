<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function church()
    {
        return view('settings.church');
    }

    public function updateChurch(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'address'   => ['nullable', 'string', 'max:500'],
            'city'      => ['nullable', 'string', 'max:100'],
            'country'   => ['nullable', 'string', 'max:2'],
            'satellite' => ['nullable', 'string', 'max:255'],
        ]);

        $request->user()->church->update($request->only(['name', 'address']));

        return back()->with('success', 'Church details saved.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'           => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => $request->input('password'),
        ]);

        return back()->with('success', 'Password updated.');
    }

    public function upgrade(string $plan)
    {
        return redirect()
            ->route('settings.church')
            ->with('selected_plan', $plan)
            ->withFragment('subscription');
    }
}
