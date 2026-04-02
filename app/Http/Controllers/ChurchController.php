<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChurchController extends Controller
{
    public function subscription()
    {
        return redirect()->to(route('settings.church').'#subscription');
    }

    public function updatePlan(Request $request)
    {
        $request->validate(['plan' => ['required', 'in:free,basic,standard,premium']]);
        $request->user()->church->update(['subscription_plan' => $request->plan]);

        return redirect()->route('settings.church')->with('success', 'Plan updated.');
    }
}
