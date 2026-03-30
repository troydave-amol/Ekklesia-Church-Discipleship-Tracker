<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setting up your platform — Ekklesia</title>
  @vite(['resources/css/app.css', 'resources/css/base.css', 'resources/css/landing.css', 'resources/css/register.css', 'resources/css/provision.css'])
  @vite(['resources/js/state.js', 'resources/js/router.js', 'resources/js/register.js', 'resources/js/provision.js'])
</head>
<body>

<div class="provision-wrap">
  <div class="provision-header">
    <h2 id="prov-title">Creating your platform…</h2>
    <p id="prov-sub">This usually takes 15–30 seconds. Please don't close this tab.</p>
  </div>

  <div class="terminal">
    <div class="terminal-bar">
      <div class="terminal-dots">
        <div class="terminal-dot"></div>
        <div class="terminal-dot"></div>
        <div class="terminal-dot"></div>
      </div>
      <div class="terminal-title">ekklesia — tenant provisioner</div>
    </div>
    <div class="terminal-body" id="terminal-body"></div>
  </div>

    <div class="terminal">
    <div class="terminal-bar">
        <div class="terminal-dots">
        <div class="terminal-dot"></div>
        <div class="terminal-dot"></div>
        <div class="terminal-dot"></div>
        </div>
        <div class="terminal-title">ekklesia — tenant provisioner</div>
    </div>
    <div class="terminal-body" id="terminal-body"></div>
    </div>

  <div class="provision-steps" id="prov-steps">
    <div class="prov-step" id="pstep-1"><div class="prov-icon">🗄️</div><div class="prov-text"><div class="prov-label">Creating tenant database</div><div class="prov-sublabel" id="pslabel-1">Waiting...</div></div><div class="prov-status pending" id="pstatus-1">pending</div></div>
    <div class="prov-step" id="pstep-2"><div class="prov-icon">📋</div><div class="prov-text"><div class="prov-label">Running schema migrations</div><div class="prov-sublabel" id="pslabel-2">Waiting...</div></div><div class="prov-status pending" id="pstatus-2">pending</div></div>
    <div class="prov-step" id="pstep-3"><div class="prov-icon">🌱</div><div class="prov-text"><div class="prov-label">Seeding programs & config</div><div class="prov-sublabel" id="pslabel-3">Waiting...</div></div><div class="prov-status pending" id="pstatus-3">pending</div></div>
    <div class="prov-step" id="pstep-4"><div class="prov-icon">🌐</div><div class="prov-text"><div class="prov-label">Registering subdomain</div><div class="prov-sublabel" id="pslabel-4">Waiting...</div></div><div class="prov-status pending" id="pstatus-4">pending</div></div>
    <div class="prov-step" id="pstep-5"><div class="prov-icon">✉️</div><div class="prov-text"><div class="prov-label">Sending welcome email</div><div class="prov-sublabel" id="pslabel-5">Waiting...</div></div><div class="prov-status pending" id="pstatus-5">pending</div></div>
  </div>

  <div id="prov-error" style="display:none;background:#fee;border:1px solid #c00;border-radius:10px;padding:20px;text-align:center;color:#900;margin-bottom:24px;">
    <strong>Provisioning failed</strong>
    <p id="prov-error-msg" style="margin:8px 0 0;font-size:14px;"></p>
    <button class="btn-ghost" style="margin-top:12px;" onclick="window.location.href='/'">Return to home</button>
  </div>

  <div class="success-card" id="success-card">
    <div class="success-icon">🎉</div>
    <h3>Your platform is ready!</h3>
    <p>Your church's dedicated platform has been created and is live right now.</p>
    <div class="success-url" id="success-url-link" onclick="goToTenantHome()">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/></svg>
      <span id="success-url-text"></span>
    </div>
    <div class="success-detail" id="success-detail"></div>
    <button class="btn-primary" style="width:100%;justify-content:center;" onclick="goToTenantDashboard()">
      Go to my dashboard
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </button>
    <div style="margin-top:16px;">
      <button class="btn-ghost" onclick="window.location.href='/register'">Register another church</button>
    </div>
  </div>
</div>

{{-- Data passed via URL params — read by provision.js --}}
<input type="hidden" id="subdomain"    value="{{ request('subdomain') }}">
<input type="hidden" id="pastor-email" value="{{ request('email') }}">
<input type="hidden" id="church-name"  value="{{ request('church_name') }}">
<input type="hidden" id="plan"         value="{{ request('plan', 'free') }}">

</body>
</html>