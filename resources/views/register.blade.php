<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Your Church — Ekklesia</title>
  @vite(['resources/css/app.css', 'resources/css/base.css', 'resources/css/landing.css', 'resources/css/register.css', 'resources/css/provision.css'])
  @vite(['resources/js/state.js', 'resources/js/router.js', 'resources/js/register.js'])
</head>
<body>

<nav>
  <div class="nav-logo">Ek<span>k</span>lesia</div>
  <div class="nav-links">
    <a href="/">← Back to home</a>
  </div>
</nav>

{{-- Copy everything inside your old <div id="page-register"> here --}}

  <div class="register-wrap">
    <div class="register-header">
      <h2>Register your church</h2>
    
    </div>
    <div class="step-indicator">
      <div class="step-pip active" id="pip-1"><div class="step-pip-circle" id="pip-circle-1">1</div><div class="step-pip-label">Church info</div></div>
      <div class="step-line" id="line-1"></div>
      <div class="step-pip" id="pip-2"><div class="step-pip-circle" id="pip-circle-2">2</div><div class="step-pip-label">Choose plan</div></div>
      <div class="step-line" id="line-2"></div>
      <div class="step-pip" id="pip-3"><div class="step-pip-circle" id="pip-circle-3">3</div><div class="step-pip-label">Payment</div></div>
      <div class="step-line" id="line-3"></div>
      <div class="step-pip" id="pip-4"><div class="step-pip-circle" id="pip-circle-4">4</div><div class="step-pip-label">Confirm</div></div>
    </div>
    <div id="step-1">
      <div class="form-card">
        <div class="form-card-header"><div class="form-card-title">Church information</div><div class="form-card-sub">Tell us about your church and the pastor registering this account.</div></div>
        <div class="form-card-body">
          <div class="form-grid">
            <div class="form-group span2"><label for="church-name">Name of church</label><input type="text" id="church-name" placeholder="e.g. Grace Community Church"><div class="field-error" id="err-church-name">Please enter your church name.</div></div>
            <div class="form-group span2"><label for="church-address">Church address</label><input type="text" id="church-address" placeholder="e.g. 123 Rizal Ave, Quezon City"><div class="field-error" id="err-church-address">Please enter the church address.</div></div>
            <div class="form-group"><label for="pastor-name">Pastor's full name</label><input type="text" id="pastor-name" placeholder="e.g. Rev. Juan dela Cruz"><div class="field-error" id="err-pastor-name">Please enter the pastor's name.</div></div>
            <div class="form-group"><label for="pastor-email">Pastor's email</label><input type="email" id="pastor-email" placeholder="pastor@church.org"><div class="field-error" id="err-pastor-email">Please enter a valid email address.</div></div>
            <!-- Password -->
            <div class="form-group">
              <label for="pastor-password">Password</label>
              <input 
                type="password" 
                id="pastor-password" 
                name="password"
                placeholder="Create a strong password"
              >
              <div class="field-error" id="err-pastor-password">
                Password must be at least 8 characters.
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
              <label for="pastor-password-confirm">Confirm password</label>
              <input 
                type="password" 
                id="pastor-password-confirm" 
                name="password_confirmation"
                placeholder="Re-enter your password"
              >
              <div class="field-error" id="err-password-confirm">
                Passwords do not match.
              </div>
            </div>
            <div class="form-group span2">
              <label for="subdomain">Subdomain <span class="label-hint">(your church's URL)</span></label>
              <div class="subdomain-wrap" id="subdomain-wrap">
                <input type="text" class="subdomain-input" id="subdomain" placeholder="gracechurch" oninput="checkSubdomain(this.value)" maxlength="32">
                <div class="subdomain-suffix">.ekklesia.app</div>
              </div>
              <div class="avail-indicator" id="avail-indicator" style="display:none;"><div class="avail-dot" id="avail-dot"></div><span id="avail-text"></span></div>
              <div class="field-error" id="err-subdomain">Please enter a valid subdomain (letters and numbers only).</div>
            </div>
          </div>
        </div>
        <div class="form-nav"><button class="btn-ghost" onclick="goToPage('page-landing')">← Back to home</button><button class="btn-primary" onclick="nextStep(1)">Continue to plan selection →</button></div>
      </div>
    </div>
    <div id="step-2" class="hidden">
      <div class="form-card">
        <div class="form-card-header"><div class="form-card-title">Choose your subscription plan</div><div class="form-card-sub">You can upgrade or change your plan at any time from your dashboard.</div></div>
        <div class="form-card-body">
          <div class="plan-picker">
            <div class="plan-option" id="opt-free" onclick="selectPlan('free')"><div class="plan-option-radio" id="radio-free"></div><div class="plan-option-top"><div><div class="plan-option-name">Free</div><div class="plan-option-price">₱0 <span class="psmall">/ month</span></div></div></div><div class="plan-option-features">1 program only · Up to 20 disciples · Up to 5 leaders · Basic tracking</div></div>
            <div class="plan-option" id="opt-basic" onclick="selectPlan('basic')"><div class="plan-option-radio" id="radio-basic"></div><div class="plan-option-top"><div><div class="plan-option-name">Basic</div><div class="plan-option-price">₱800 <span class="psmall">/ month</span></div></div></div><div class="plan-option-features">All 4 programs · Up to 50 disciples · 5 leaders · Devotional tracking · Dashboard</div></div>
            <div class="plan-option" id="opt-standard" onclick="selectPlan('standard')"><div class="plan-option-radio" id="radio-standard"></div><div class="plan-option-top"><div><div class="plan-option-name">Standard</div><div class="plan-option-price">₱1,500 <span class="psmall">/ month</span></div></div></div><div class="plan-option-features">Unlimited disciples · Up to 10 leaders · Dashboard · Sheets export</div></div>
            <div class="plan-option" id="opt-premium" onclick="selectPlan('premium')"><div class="plan-option-radio" id="radio-premium"></div><div class="plan-option-top"><div><div class="plan-option-name">Premium</div><div class="plan-option-price">₱2,500 <span class="psmall">/ month</span></div></div></div><div class="plan-option-features">Unlimited disciples & leaders · Bulk import · Historical reports · Priority support</div></div>
          </div>
          <div class="field-error mt-4" id="err-plan" style="display:none;">Please select a plan to continue.</div>
        </div>
        <div class="form-nav"><button class="btn-ghost" onclick="prevStep(2)">← Back</button><button class="btn-primary" onclick="nextStep(2)">Continue →</button></div>
      </div>
    </div>
    <div id="step-3" class="hidden">
      <div class="form-card">
        <div class="form-card-header"><div class="form-card-title" id="payment-step-title">Payment confirmation</div><div class="form-card-sub" id="payment-step-sub">Complete payment to activate your subscription.</div></div>
        <div class="form-card-body" id="payment-body"></div>
        <div class="form-nav"><button class="btn-ghost" onclick="prevStep(3)">← Back</button><button class="btn-primary" id="payment-next-btn" onclick="nextStep(3)">Continue →</button></div>
      </div>
    </div>
    <div id="step-4" class="hidden">
      <div class="form-card">
        <div class="form-card-header"><div class="form-card-title">Review & confirm</div><div class="form-card-sub">Double-check your details before we create your platform.</div></div>
        <div class="form-card-body" id="confirm-body"></div>
        <div class="form-nav"><button class="btn-ghost" onclick="prevStep(4)">← Back</button><button class="btn-primary" onclick="submitRegistration()">Create my church platform <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>
    </div>
  </div>


</body>
</html>