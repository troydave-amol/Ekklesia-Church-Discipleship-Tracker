// ── GLOBAL STATE ──
const state = {
  currentStep: 1,
  selectedPlan: null,
  subdomainAvailable: null
};

// ── STEP NAVIGATION ──
function nextStep(step) {
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
  state.currentStep = next;
  updatePips(next);
  window.scrollTo(0,0);
}

function prevStep(step) {
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
function selectPlan(plan) {
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
function checkSubdomain(val) {
  const indicator = document.getElementById('avail-indicator');
  const dot = document.getElementById('avail-dot');
  const text = document.getElementById('avail-text');

  if (!val.match(/^[a-zA-Z0-9]+$/)) {
    state.subdomainAvailable = false;
    indicator.style.display = 'block';
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
    indicator.style.display = 'block';
  }, 300);
}