// Note: `state` is now managed globally in `state.js`
// const state = { ... };

// ── STEP NAVIGATION ──
window.nextStep = function(step) {
  if (step === 1) {
    // check Step 1
    if (!validateStep1()) {
      // if subdomain is still null, force availability check
      if (state.subdomainAvailable === null) {
        alert("Checking your subdomain, please wait a moment...");
        return;
      }
      return; // prevent moving forward if invalid
    }
  }
  if (step === 2 && !validateStep2()) return;
  if (step === 3 && !validateStep3()) return;

  // hide current step, show next
  document.getElementById(`step-${step}`).classList.add('hidden');
  const next = step + 1;
  document.getElementById(`step-${next}`).classList.remove('hidden');

  // Trigger dynamic step builders
  if (next === 3) buildPaymentStep();
  if (next === 4) buildConfirmStep();

  state.currentStep = next;
  updatePips(next);
  window.scrollTo(0,0);
}

window.prevStep = function(step) {
  document.getElementById(`step-${step}`).classList.add('hidden');
  const prev = step - 1;
  document.getElementById(`step-${prev}`).classList.remove('hidden');
  state.currentStep = prev;
  updatePips(prev);
}

// ── VALIDATION FUNCTIONS ──
function validateStep1() {
  let valid = true;

  const church = document.getElementById('church-name').value.trim();
  const address = document.getElementById('church-address').value.trim();
  const pastor = document.getElementById('pastor-name').value.trim();
  const email = document.getElementById('pastor-email').value.trim();
  const subdomain = document.getElementById('subdomain').value.trim();

  // Helper to show/hide error
  const toggleError = (id, show) => document.getElementById(id).style.display = show ? 'block' : 'none';

  toggleError('err-church-name', !church);
  toggleError('err-church-address', !address);
  toggleError('err-pastor-name', !pastor);
  toggleError('err-pastor-email', !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email));
  toggleError('err-subdomain', !subdomain || state.subdomainAvailable === false);

  if (!church || !address || !pastor || !email || !subdomain || state.subdomainAvailable === false) valid = false;

  return valid;
}

function validateStep2() {
  const errPlan = document.getElementById('err-plan');
  if (!state.selectedPlan) {
    errPlan.style.display = 'block';
    return false;
  }
  errPlan.style.display = 'none';
  return true;
}

function validateStep3() {
  // optional: show inline error if payment needed
  return true; // can extend for actual payment validation
}

// ── PLAN SELECTION ──
window.selectPlan = function(plan) {
  state.selectedPlan = plan;
  document.querySelectorAll('.plan-option').forEach(p => p.classList.remove('selected'));
  document.getElementById('opt-' + plan).classList.add('selected');
  document.getElementById('err-plan').style.display = 'none';
}

// ── STEP PIPS ──
function updatePips(active) {
  for (let i=1;i<=4;i++) {
    const pip = document.getElementById(`pip-${i}`);
    pip.classList.toggle('done', i<active);
    pip.classList.toggle('active', i===active);
  }
}

// ── SUBDOMAIN CHECK (inline feedback) ──
window.checkSubdomain = function(val) {
  const indicator = document.getElementById('avail-indicator');
  const dot = document.getElementById('avail-dot');
  const text = document.getElementById('avail-text');

  if (!val.match(/^[a-zA-Z0-9]+$/)) {
    state.subdomainAvailable = false;
    indicator.style.display = 'flex';
    dot.style.background = 'red';
    text.textContent = 'Invalid';
    return;
  }

  // mock async check
  setTimeout(()=>{
    const taken = ['grace','test'];
    if (taken.includes(val.toLowerCase())) {
      state.subdomainAvailable = false;
      dot.style.background = 'red';
      text.textContent = 'Taken';
    } else {
      state.subdomainAvailable = true;
      dot.style.background = 'green';
      text.textContent = 'Available';
    }
    indicator.style.display = 'flex';
  }, 300);
}

window.submitRegistration = function() {
    // Final validation
    if (!state.selectedPlan) {
        alert("Please select a plan first.");
        return;
    }

    // Collect all registration data
    const data = {
        church_name:    document.getElementById('church-name').value,
        church_address: document.getElementById('church-address').value,
        pastor_name:    document.getElementById('pastor-name').value,
        email:          document.getElementById('pastor-email').value,
        subdomain:      document.getElementById('subdomain').value,
        plan:           state.selectedPlan || 'free',
    };

    // Determine destination
    const provisionPage = document.getElementById('page-provision');
    
    if (provisionPage) {
        // SPA Mode (welcome.blade.php)
        goToPage('page-provision');
        runProvisioning();
    } else {
        // Form Mode — redirect with all params
        const params = new URLSearchParams(data).toString();
        window.location.href = `/provision-simulation?${params}`;
    }
}

// ── DYNAMIC STEP BUILDERS ──
function buildPaymentStep() {
  const plan = PLANS[state.selectedPlan];
  const body = document.getElementById('payment-body');
  if (!body) return;

  if (plan.price === 0) {
    body.innerHTML = `
      <div class="gcash-note">
        <div class="gcash-note-icon">✨</div>
        <div class="gcash-note-text">You've selected the <strong>Free Plan</strong>. No payment is required. Provisioning will begin immediately.</div>
      </div>`;
    document.getElementById('payment-step-title').textContent = 'No payment required';
    document.getElementById('payment-step-sub').textContent = 'Confirm your free subscription to continue.';
    document.getElementById('payment-next-btn').textContent = 'Confirm & Continue →';
  } else {
    body.innerHTML = `
      <div class="payment-summary">
        <div class="payment-plan-name">${plan.name} Plan</div>
        <div class="payment-plan-price">${plan.priceStr}</div>
        <div class="payment-plan-sub">Billed monthly</div>
      </div>
      <div class="gcash-note">
        <div class="gcash-note-icon">📱</div>
        <div class="gcash-note-text">Please send payment to <strong>0912-345-6789 (Ekklesia)</strong> and enter the reference number below.</div>
      </div>
      <div class="form-group">
        <label>GCash Reference Number</label>
        <input type="text" id="payment-ref" placeholder="12-digit number">
      </div>`;
  }
}

function buildConfirmStep() {
  const body = document.getElementById('confirm-body');
  if (!body) return;

  const plan = PLANS[state.selectedPlan];
  const church = document.getElementById('church-name').value;
  const subdomain = document.getElementById('subdomain').value;
  const email = document.getElementById('pastor-email').value;

  body.innerHTML = `
    <div style="display:flex; flex-direction:column; gap:16px;">
      <div style="background:var(--cream); padding:16px; border-radius:10px; border:1px solid var(--border);">
        <div style="font-size:11px; font-weight:600; text-transform:uppercase; opacity:0.5; margin-bottom:4px;">Church Details</div>
        <div style="font-weight:600;">${church}</div>
        <div style="font-size:13px; color:var(--gold);">${subdomain}.ekklesia.app</div>
      </div>
      <div style="background:var(--cream); padding:16px; border-radius:10px; border:1px solid var(--border);">
        <div style="font-size:11px; font-weight:600; text-transform:uppercase; opacity:0.5; margin-bottom:4px;">Plan Selected</div>
        <div style="font-weight:600;">${plan.name} Plan</div>
        <div style="font-size:13px;">${plan.priceStr} / month</div>
      </div>
      <div style="background:var(--cream); padding:16px; border-radius:10px; border:1px solid var(--border);">
        <div style="font-size:11px; font-weight:600; text-transform:uppercase; opacity:0.5; margin-bottom:4px;">Pastor Account</div>
        <div style="font-weight:600;">${document.getElementById('pastor-name').value}</div>
        <div style="font-size:13px; opacity:0.7;">${email}</div>
      </div>
    </div>`;
}