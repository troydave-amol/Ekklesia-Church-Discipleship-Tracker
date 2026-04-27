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
    const dot       = document.getElementById('avail-dot');
    const text      = document.getElementById('avail-text');

    if (!val || !val.match(/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/) || val.length < 3) {
        state.subdomainAvailable = false;
        indicator.style.display = 'flex';
        dot.style.background = 'red';
        text.textContent = 'Invalid — letters and numbers only';
        return;
    }

    indicator.style.display = 'flex';
    dot.style.background = 'orange';
    text.textContent = 'Checking...';

    clearTimeout(window._subdomainTimer);
    window._subdomainTimer = setTimeout(() => {
        fetch(`/check-subdomain?subdomain=${encodeURIComponent(val)}`)
            .then(r => r.json())
            .then(data => {
                state.subdomainAvailable = data.available;
                dot.style.background = data.available ? 'green' : 'red';
                text.textContent = data.message;
                indicator.style.display = 'flex';
            })
            .catch(() => {
                dot.style.background = 'orange';
                text.textContent = 'Could not check — try again';
            });
    }, 500);
}

window.submitRegistration = async function() {
  // Step 1: validate all steps
  if (!validateStep1()) return;
  if (!validateStep2()) return;

  // Collect data
  const payload = {
    church_name:    document.getElementById('church-name').value.trim(),
    church_address: document.getElementById('church-address').value.trim(),
    pastor_name:    document.getElementById('pastor-name').value.trim(),
    pastor_email:   document.getElementById('pastor-email').value.trim(),
    pastor_password: document.getElementById('pastor-password').value, // raw password
    subdomain:      document.getElementById('subdomain').value.trim(),
    plan:           state.selectedPlan || 'free',
  };

  // Optional: disable button to prevent double submit
  const btn = document.getElementById('submit-btn');
  if (btn) btn.disabled = true;

  try {
    const res = await fetch('/register-tenant', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    const data = await res.json();

    if (data.success && data.pending_approval) {
      window.location.href = '/registration-pending';
      return;
    }

    if (data.success) {
      const params = new URLSearchParams({
        subdomain: payload.subdomain,
        email: payload.pastor_email,
        church_name: payload.church_name,
        plan: payload.plan
      });

      window.location.href = '/provision?' + params.toString();
    } else {
      alert(data.message || 'Registration failed. Please try again.');
      if (btn) btn.disabled = false;
    }
  } catch (err) {
    console.error(err);
    alert('A network error occurred. Please try again.');
    if (btn) btn.disabled = false;
  }
}

function buildPaymentStep() {
  const body = document.getElementById('payment-body');
  if (!body) return;

  const planKey = state.selectedPlan || 'free';
  const plan = PLANS[planKey];
  const isFree = plan.price === 0;

  body.innerHTML = `
    <div style="background:var(--cream); padding:20px; border-radius:10px; border:1px solid var(--border); text-align:center;">
        <div style="font-weight:600; margin-bottom:10px; font-size:18px;">Plan: ${plan.name}</div>
        <div style="margin-bottom:20px;">
            ${isFree 
                ? 'No payment required for the Free plan.' 
                : `<span style="font-size:24px; font-weight:700;">${plan.priceStr}</span>/month`
            }
        </div>
        <div style="font-size:14px; color:var(--text-muted); margin-bottom:20px;">
            Payment processing will be available in a future update.<br>
            Click Continue to complete your registration.
        </div>
    </div>
  `;
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

