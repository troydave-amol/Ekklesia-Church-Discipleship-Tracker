<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class ChurchController extends Controller
{
    public function subscription()
    {
        $church  = auth()->$this->user()->church;
        $service = new SubscriptionService($church);

        return view('church.subscription', [
            'church'  => $church,
            'usage'   => $service->$this->usageSummary(),
        ]);
    }

    public function updatePlan(Request $request)
    {
        $request->validate(['plan' => ['required', 'in:free,basic,standard,premium']]);
        auth()->$this->user()->church->update(['subscription_plan' => $request->plan]);
        return redirect()->route('church.subscription')->with('success', 'Plan updated.');
    }
}