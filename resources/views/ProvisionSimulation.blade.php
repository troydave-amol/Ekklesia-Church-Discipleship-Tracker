<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Provisioning Your Platform — Ekklesia</title>
    @vite(['resources/css/app.css', 'resources/css/base.css', 'resources/css/landing.css', 'resources/css/register.css', 'resources/css/provision.css'])
    @vite(['resources/js/state.js', 'resources/js/router.js', 'resources/js/register.js', 'resources/js/provision.js'])
</head>
<body>

<div id="page-provision" class="page active">
  <div class="provision-wrap">
    <div class="provision-header">
      <h2 id="prov-title">Creating your platform…</h2>
      <p id="prov-sub">This usually takes 15–30 seconds. Please don't close this tab.</p>
    </div>
    <div class="provision-steps" id="prov-steps">
      <div class="prov-step" id="pstep-1"><div class="prov-icon">🗄️</div><div class="prov-text"><div class="prov-label">Creating tenant database</div><div class="prov-sublabel" id="pslabel-1">Waiting...</div></div><div class="prov-status pending" id="pstatus-1">pending</div></div>
      <div class="prov-step" id="pstep-2"><div class="prov-icon">📋</div><div class="prov-text"><div class="prov-label">Running schema migrations</div><div class="prov-sublabel" id="pslabel-2">Waiting...</div></div><div class="prov-status pending" id="pstatus-2">pending</div></div>
      <div class="prov-step" id="pstep-3"><div class="prov-icon">🌱</div><div class="prov-text"><div class="prov-label">Seeding programs & config</div><div class="prov-sublabel" id="pslabel-3">Waiting...</div></div><div class="prov-status pending" id="pstatus-3">pending</div></div>
      <div class="prov-step" id="pstep-4"><div class="prov-icon">🌐</div><div class="prov-text"><div class="prov-label">Registering subdomain</div><div class="prov-sublabel" id="pslabel-4">Waiting...</div></div><div class="prov-status pending" id="pstatus-4">pending</div></div>
      <div class="prov-step" id="pstep-5"><div class="prov-icon">✉️</div><div class="prov-text"><div class="prov-label">Sending welcome email</div><div class="prov-sublabel" id="pslabel-5">Waiting...</div></div><div class="prov-status pending" id="pstatus-5">pending</div></div>
    </div>

    {{-- Error message (hidden by default) --}}
    <div id="prov-error" style="display:none; background:#fee; border:1px solid #c00; border-radius:10px; padding:20px; text-align:center; color:#900; margin-bottom:24px;">
      <strong>Provisioning failed</strong>
      <p id="prov-error-msg" style="margin:8px 0 0;font-size:14px;"></p>
      <button class="btn-ghost" style="margin-top:12px;" onclick="window.location.href='/'">Return to home</button>
    </div>

    <div class="success-card" id="success-card">
      <div class="success-icon">🎉</div>
      <h3>Your platform is ready!</h3>
      <p>Your church's dedicated platform has been created and is live right now. Share the link below with your leaders to get started.</p>
      <div class="success-url" id="success-url-link" onclick="goToTenantHome()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/></svg><span id="success-url-text"></span></div>
      <div class="success-detail" id="success-detail"></div>
      <button class="btn-primary" style="width:100%;justify-content:center;" id="btn-go-dashboard" onclick="goToTenantDashboard()">Go to my platform <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
      <div style="margin-top:16px;"><button class="btn-ghost" onclick="window.location.href='/'">Return to home</button></div>
    </div>
  </div>
</div>

<!-- Hidden inputs to store data passed from registration -->
<input type="hidden" id="subdomain" value="{{ request('subdomain') }}">
<input type="hidden" id="pastor-email" value="{{ request('email') }}">

<script>
// ════════════════════════════════════════
// Global state: tenant URL set after real provisioning
// ════════════════════════════════════════
let tenantUrl = null;
let animationDone = false;
let apiDone = false;
let apiSuccess = false;

function showSuccessIfReady() {
    // Only show when BOTH animation AND API are done
    if (!animationDone || !apiDone || !apiSuccess) return;

    const subdomain = document.getElementById('subdomain').value.trim();
    const plan = PLANS[state.selectedPlan];
    const dbName = 'church_' + subdomain;

    document.getElementById('prov-title').textContent = 'Platform ready!';
    document.getElementById('prov-sub').textContent   = 'Your church platform is live and accessible.';

    const now = new Date();
    const due = new Date(now);
    due.setDate(due.getDate() + 30);

    document.getElementById('success-url-text').textContent = subdomain + '.ekklesia.app';
    document.getElementById('success-detail').innerHTML = `
        <div class="success-detail-item">
            <div class="success-detail-label">Database</div>
            <div class="success-detail-value">${dbName}</div>
        </div>s
        <div class="success-detail-item">
            <div class="success-detail-label">Plan</div>
            <div class="success-detail-value">${plan.name}</div>
        </div>
        <div class="success-detail-item">
            <div class="success-detail-label">Activated on</div>
            <div class="success-detail-value">${now.toLocaleDateString('en-PH')}</div>
        </div>
        <div class="success-detail-item">
            <div class="success-detail-label">Next renewal</div>
            <div class="success-detail-value">${plan.paid ? due.toLocaleDateString('en-PH') : 'N/A'}</div>
        </div>`;

    document.getElementById('success-card').classList.add('show');
}

// ════════════════════════════════════════
// Initialize on page load
// ════════════════════════════════════════
window.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const plan = urlParams.get('plan') || 'free';

  const checkState = setInterval(() => {
    if (window.state && window.PLANS) {
      clearInterval(checkState);
      window.state.selectedPlan = plan;

      // Step 1: Start the visual animation
      runProvisioningSteps();

      // Step 2: Actually create the tenant via API (runs in parallel)
      createTenantViaAPI();
    }
  }, 100);
});

// ════════════════════════════════════════
// REAL TENANT CREATION (POST /register-tenant)
// ════════════════════════════════════════
function createTenantViaAPI() {
  const urlParams = new URLSearchParams(window.location.search);

  const payload = {
    church_name:    urlParams.get('church_name')    || '',
    church_address: urlParams.get('church_address') || '',
    pastor_name:    urlParams.get('pastor_name')    || '',
    pastor_email:   urlParams.get('email')          || '',
    subdomain:      urlParams.get('subdomain')      || '',
    plan:           urlParams.get('plan')            || 'free',
  };

  fetch('/register-tenant', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: JSON.stringify(payload),
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
        tenantUrl = data.url;
        apiDone = true;
        apiSuccess = true;
        showSuccessIfReady();  // ← try to show success card
    } else {
        showError(data.message || 'Could not create your platform. The subdomain may already be taken.');
    }
})
  .catch(err => {
    console.error('Network error during tenant creation:', err);
    showError('A network error occurred. Please try again.');
  });
}

function showError(msg) {
  document.getElementById('prov-error-msg').textContent = msg;
  document.getElementById('prov-error').style.display = 'block';
}

// ════════════════════════════════════════
// PROVISIONING ANIMATION (Steps + Success)
// ════════════════════════════════════════
function runProvisioningSteps() {
  const subdomain = document.getElementById('subdomain').value.trim();
  const plan = PLANS[state.selectedPlan];
  const dbName = 'tenant_' + subdomain;
  const email = document.getElementById('pastor-email').value.trim();

  const provSteps = [
    { delay: 1700,  doneDelay: 2400,  step: 1, label: dbName },
    { delay: 2900,  doneDelay: 5200,  step: 2, label: 'Running 7 migrations' },
    { delay: 5600,  doneDelay: 6800,  step: 3, label: `Plan: ${plan.name}` },
    { delay: 7100,  doneDelay: 7900,  step: 4, label: `${subdomain}.ekklesia.app` },
    { delay: 8200,  doneDelay: 8600,  step: 5, label: email },
  ];

  provSteps.forEach(({delay, doneDelay, step, label}) => {
    setTimeout(() => {
      const el       = document.getElementById('pstep-' + step);
      const stat     = document.getElementById('pstatus-' + step);
      const sublabel = document.getElementById('pslabel-' + step);
      el.classList.add('running');
      stat.className   = 'prov-status running';
      stat.innerHTML   = `<span class="spinner"></span>`;
      sublabel.textContent = label;
    }, delay);

    setTimeout(() => {
      const el   = document.getElementById('pstep-' + step);
      const stat = document.getElementById('pstatus-' + step);
      el.classList.remove('running');
      el.classList.add('done');
      stat.className   = 'prov-status done';
      stat.textContent = '✓ done';
    }, doneDelay);
  });

  // Show success card
  setTimeout(() => {
    animationDone = true;
    showSuccessIfReady();  // ← try to show success card
  }, 9600);
}

// ════════════════════════════════════════
// NAVIGATION HELPERS — Uses real tenant URL from API
// ════════════════════════════════════════
function getTenantBaseUrl() {
  // If API returned a real URL, use it
  if (tenantUrl) return tenantUrl;

  // Fallback: construct manually
  const subdomain = document.getElementById('subdomain').value.trim();
  const currentHost = window.location.hostname;
  const port = window.location.port ? ':' + window.location.port : '';
  if (currentHost === 'localhost' || currentHost === '127.0.0.1') {
    return `http://${subdomain}.localhost${port}`;
  }
  return `https://${subdomain}.ekklesia.app`;
}

function goToTenantDashboard() {
  // Redirect to tenant login (user needs to authenticate on the subdomain)
  window.location.href = getTenantBaseUrl() + '/login';
}

function goToTenantHome() {
  window.location.href = getTenantBaseUrl();
}
</script>

</body>
</html>
