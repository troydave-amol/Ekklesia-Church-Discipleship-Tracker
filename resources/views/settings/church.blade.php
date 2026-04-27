{{-- resources/views/settings/church.blade.php --}}
@extends('layouts.app')
@section('title', 'Settings')

@section('content')
@php
    $church = auth()->user()->church;
    $plan   = $church->subscription_plan ?? 'free';

    $plans = [
        'free'     => ['label'=>'Free',     'price'=>'₱0',     'color'=>'var(--blue)',   'badge'=>'badge-blue',  'disciples'=>20,      'leaders'=>5,  'programs'=>1,  'features'=>['1 program (Life Class)','Up to 20 disciples','Up to 5 leaders','Basic attendance & lesson tracking']],
        'basic'    => ['label'=>'Basic',    'price'=>'₱800',   'color'=>'var(--purple)', 'badge'=>'badge-purple','disciples'=>50,      'leaders'=>5,  'programs'=>4,  'features'=>['All 4 programs','Up to 50 disciples','Up to 5 leaders','Attendance & devotional tracking','Basic hierarchical dashboard']],
        'standard' => ['label'=>'Standard', 'price'=>'₱1,500', 'color'=>'var(--gold)',   'badge'=>'badge-gold',  'disciples'=>'∞',     'leaders'=>10, 'programs'=>4,  'features'=>['Unlimited disciples','Up to 10 leaders','Dashboard with program summary','Sheets export of attendance & devotionals']],
        'premium'  => ['label'=>'Premium',  'price'=>'₱2,500', 'color'=>'var(--green)',  'badge'=>'badge-green', 'disciples'=>'∞',     'leaders'=>'∞','programs'=>4,  'features'=>['Unlimited disciples & leaders','Bulk import of disciples','Historical reports per program','Priority support']],
    ];
@endphp

<div class="flex items-center justify-between mb-20">
    <h1 class="page-title">Church Settings</h1>
</div>

<div class="grid-2">

    {{-- Church Info --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <div class="card-header"><span class="section-title">Church Information</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.church.update') }}">
                    @csrf @method('PATCH')
                    <div class="form-grid form-grid-1" style="grid-template-columns:1fr;gap:14px;">
                        <div>
                            <label class="field-label">Church Name</label>
                            <input name="name" value="{{ old('name', $church->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-grid form-grid-2">
                            <div>
                                <label class="field-label">City</label>
                                <input name="city" value="{{ old('city', $church->city ?? '') }}">
                            </div>
                            <div>
                                <label class="field-label">Country</label>
                                <select name="country">
                                    <option value="PH" {{ ($church->country??'PH')==='PH' ? 'selected':'' }}>Philippines</option>
                                    <option value="US">United States</option>
                                    <option value="SG">Singapore</option>
                                    <option value="AU">Australia</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="field-label">Address</label>
                            <input name="address" value="{{ old('address', $church->address ?? '') }}">
                        </div>
                        <div>
                            <label class="field-label">Main Satellite / Campus Name</label>
                            <input name="satellite" value="{{ old('satellite', $church->satellite ?? 'Main Campus') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Church Info</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Password change --}}
        <div class="card">
            <div class="card-header"><span class="section-title">Change Password</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf @method('PUT')
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <div>
                            <label class="field-label">Current Password</label>
                            <input type="password" name="current_password">
                        </div>
                        <div>
                            <label class="field-label">New Password</label>
                            <input type="password" name="password">
                        </div>
                        <div>
                            <label class="field-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation">
                        </div>
                        <button type="submit" class="btn btn-secondary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Subscription --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Current Plan --}}
        <div class="card" id="subscription">
            <div class="card-header">
                <span class="section-title">Current Subscription</span>
                <span class="badge {{ $plans[$plan]['badge'] }}">{{ strtoupper($plan) }}</span>
            </div>
            <div class="card-body">
                <div style="background:var(--bg);border-radius:12px;padding:18px;border:1px solid {{ $plans[$plan]['color'] }}44;margin-bottom:16px;">
                    <div class="flex justify-between items-center mb-12">
                        <span style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:{{ $plans[$plan]['color'] }};">{{ $plans[$plan]['label'] }} Plan</span>
                        <span style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:{{ $plans[$plan]['color'] }};">{{ $plans[$plan]['price'] }}<span style="font-size:13px;color:var(--text-muted);font-family:'DM Sans',sans-serif;">/mo</span></span>
                    </div>
                    @foreach($plans[$plan]['features'] as $feature)
                    <div class="flex items-center gap-8" style="font-size:13px;margin-bottom:6px;">
                        @include('partials.icon',['name'=>'check','size'=>13,'color'=>$plans[$plan]['color']])
                        <span>{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                <div style="font-size:12px;color:var(--text-muted);text-align:center;">
                    Status: <span style="color:{{ $church->subscription_status==='active' ? 'var(--green)':'var(--red)' }};font-weight:600;">{{ ucfirst($church->subscription_status ?? 'inactive') }}</span>
                </div>
            </div>
        </div>

        {{-- Plan comparison --}}
        <div class="card">
            <div class="card-header"><span class="section-title">All Plans</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
                @foreach($plans as $key => $p)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:10px;background:{{ $key===$plan ? $p['color'].'11':'var(--bg)' }};border:1px solid {{ $key===$plan ? $p['color'].'44':'var(--border-light)' }};">
                    <div>
                        <span style="font-size:14px;font-weight:{{ $key===$plan?'600':'400' }};color:{{ $key===$plan?$p['color']:'var(--text)' }};">{{ $p['label'] }}</span>
                        @if($key===$plan)
                            <span class="badge {{ $p['badge'] }}" style="margin-left:8px;font-size:9px;">CURRENT</span>
                        @endif
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                            {{ is_numeric($p['disciples']) ? 'Up to '.$p['disciples'] : $p['disciples'] }} disciples ·
                            {{ is_numeric($p['leaders']) ? 'Up to '.$p['leaders'] : $p['leaders'] }} leaders
                        </div>
                    </div>
                    <div class="flex items-center gap-10">
                        <span style="font-family:'DM Mono',monospace;font-size:14px;color:var(--text-muted);">{{ $p['price'] }}/mo</span>
                        @if($key !== $plan)
                            <a href="{{ route('settings.upgrade', $key) }}" class="btn btn-ghost btn-sm" style="border-color:{{ $p['color'] }}44;color:{{ $p['color'] }};">
                                {{ array_search($key, array_keys($plans)) > array_search($plan, array_keys($plans)) ? 'Upgrade' : 'Downgrade' }}
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="card" style="border-color:rgba(248,81,73,.2);">
            <div class="card-header" style="border-bottom-color:rgba(248,81,73,.2);">
                <span class="section-title" style="color:var(--red);">Danger Zone</span>
            </div>
            <div class="card-body">
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:14px;">Permanently delete your church data. This action cannot be undone.</div>
                <button class="btn btn-danger btn-sm" onclick="return confirm('Are you absolutely sure? All church data will be permanently deleted.')">
                    Delete Church Account
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
