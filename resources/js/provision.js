// resources/js/provision.js

let tenantUrl   = null;
let animationDone = false;
let apiDone       = false;
let apiSuccess    = false;

if (!document.getElementById('subdomain')) {
    throw new Error('provision.js: not on provision page, stopping.');
}

window.addEventListener('DOMContentLoaded', () => {
  // Read plan from hidden input (set by Laravel from URL param)
  const plan = document.getElementById('plan')?.value || 'free';

  // Wait for PLANS to load from state.js
  const checkState = setInterval(() => {
    if (window.PLANS) {
      clearInterval(checkState);
      if (window.state) window.state.selectedPlan = plan;
      runProvisioningSteps();
      createTenantViaAPI();
    }
  }, 100);
});

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
      tenantUrl  = data.url;
      apiDone    = true;
      apiSuccess = true;
      showSuccessIfReady();
    } else {
      showProvError(data.message || 'Could not create your platform.');
    }
  })
  .catch(err => {
    console.error('Network error:', err);
    showProvError('A network error occurred. Please try again.');
  });
}

function runProvisioningSteps() {
  const subdomain = document.getElementById('subdomain').value.trim();
  const email     = document.getElementById('pastor-email').value.trim();
  const plan      = document.getElementById('plan').value.trim();
  const planLabel = window.PLANS?.[plan]?.name || plan;
  const dbName    = 'church_' + subdomain;

  const steps = [
    { delay: 1700, doneDelay: 2400, step: 1, label: dbName },
    { delay: 2900, doneDelay: 5200, step: 2, label: 'Running migrations' },
    { delay: 5600, doneDelay: 6800, step: 3, label: 'Plan: ' + planLabel },
    { delay: 7100, doneDelay: 7900, step: 4, label: subdomain + '.ekklesia.app' },
    { delay: 8200, doneDelay: 8600, step: 5, label: email },
  ];

  steps.forEach(({ delay, doneDelay, step, label }) => {
    setTimeout(() => {
      document.getElementById('pstep-' + step).classList.add('running');
      const stat = document.getElementById('pstatus-' + step);
      stat.className = 'prov-status running';
      stat.innerHTML = '<span class="spinner"></span>';
      document.getElementById('pslabel-' + step).textContent = label;
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

  setTimeout(() => {
    animationDone = true;
    showSuccessIfReady();
  }, 9600);
}

function showSuccessIfReady() {
  if (!animationDone || !apiDone || !apiSuccess) return;

  const subdomain = document.getElementById('subdomain').value.trim();
  const plan      = document.getElementById('plan').value.trim();
  const planLabel = window.PLANS?.[plan]?.name || plan;
  const planPaid  = window.PLANS?.[plan]?.paid  || false;
  const dbName    = 'church_' + subdomain;
  const now       = new Date();
  const due       = new Date(now);
  due.setDate(due.getDate() + 30);

  document.getElementById('prov-title').textContent = 'Platform ready!';
  document.getElementById('prov-sub').textContent   = 'Your church platform is live and accessible.';
  document.getElementById('success-url-text').textContent = subdomain + '.ekklesia.app';
  document.getElementById('success-detail').innerHTML = `
    <div class="success-detail-item"><div class="success-detail-label">Database</div><div class="success-detail-value">${dbName}</div></div>
    <div class="success-detail-item"><div class="success-detail-label">Plan</div><div class="success-detail-value">${planLabel}</div></div>
    <div class="success-detail-item"><div class="success-detail-label">Activated on</div><div class="success-detail-value">${now.toLocaleDateString('en-PH')}</div></div>
    <div class="success-detail-item"><div class="success-detail-label">Next renewal</div><div class="success-detail-value">${planPaid ? due.toLocaleDateString('en-PH') : 'N/A'}</div></div>`;
  document.getElementById('success-card').classList.add('show');
}

function showProvError(msg) {
  document.getElementById('prov-error-msg').textContent = msg;
  document.getElementById('prov-error').style.display  = 'block';
}

function getTenantBaseUrl() {
  if (tenantUrl) return tenantUrl;
  const subdomain   = document.getElementById('subdomain').value.trim();
  const currentHost = window.location.hostname;
  const port        = window.location.port ? ':' + window.location.port : '';
  if (currentHost === 'localhost' || currentHost === '127.0.0.1') {
    return `http://${subdomain}.localhost${port}`;
  }
  return `https://${subdomain}.ekklesia.app`;
}

function goToTenantDashboard() {
  window.location.href = getTenantBaseUrl() + '/login';
}

function goToTenantHome() {
  window.location.href = getTenantBaseUrl();
}