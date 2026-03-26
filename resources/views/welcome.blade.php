<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Church Management Platform — Ekklesia</title>

  <!-- ── Stylesheets (load order matters) ── -->
  @vite(['resources/css/app.css', 'resources/css/base.css', 'resources/css/landing.css', 'resources/css/register.css', 'resources/css/provision.css'])
  @vite(['resources/js/state.js', 'resources/js/router.js', 'resources/js/register.js', 'resources/js/provision.js'])
  

</head>
<body>

<!-- ════════════════════════════════════════
     SHARED NAV (visible on all pages)
════════════════════════════════════════ -->
<nav>
  <div class="nav-logo">Ek<span>k</span>lesia</div>
  <div class="nav-links">
    <a onclick="navigateAndScroll('plans-section')">Pricing</a>
    <a onclick="navigateAndScroll('flow-section')">How it works</a>
    <button class="btn-nav" onclick="window.location.href='/register'">Register your church</button>
  </div>
</nav>


<!-- ════════════════════════════════════════
     PAGE 1: LANDING
════════════════════════════════════════ -->
<div id="page-landing" class="page active">
  <!--
    Source: pages/landing.html
    Sections: Hero · Stats Row · Plans Grid · How It Works · Footer
  -->
  <!-- [ PASTE OR SERVER-INCLUDE pages/landing.html HERE ] -->

  <!-- Hero -->
  <section class="hero">
    <div class="hero-bg"></div>
    <div>
      <div class="hero-eyebrow">
        <span class="hero-eyebrow-dot"></span>Built for the local church
      </div>
      <h1>Discipleship<br>management,<br><em>done right.</em></h1>
      <p>Ekklesia gives every church its own dedicated platform — track programs, disciples, attendance, and spiritual growth in one place built for your congregation.</p>
      <div class="hero-actions">
        <button class="btn-primary" onclick="window.location.href='/register'">
          Register your church
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
        <button class="btn-secondary" onclick="scrollToPlans()">View pricing</button>
      </div>
      <p class="hero-note">✓ Free tier available &nbsp; ✓ No setup fee &nbsp; ✓ Philippines-based support</p>
    </div>
    <div class="hero-visual">
      <div class="browser-bar">
        <div class="browser-dots">
          <div class="browser-dot"></div><div class="browser-dot"></div><div class="browser-dot"></div>
        </div>
        <div class="browser-url">gracechurch.ekklesia.app</div>
      </div>
      <div class="browser-content">
        <div style="font-size:12px;font-weight:600;color:var(--ink3);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:12px;">Admin Overview</div>
        <div class="mock-dashboard">
          <div class="mock-stat"><div class="mock-stat-label">Total disciples</div><div class="mock-stat-value">147</div><div class="mock-stat-sub">↑ 12 this month</div></div>
          <div class="mock-stat"><div class="mock-stat-label">Active programs</div><div class="mock-stat-value">4</div><div class="mock-stat-sub">All running</div></div>
          <div class="mock-stat"><div class="mock-stat-label">Attendance rate</div><div class="mock-stat-value">87%</div><div class="mock-stat-sub">↑ 3% vs last week</div></div>
          <div class="mock-stat"><div class="mock-stat-label">Leaders</div><div class="mock-stat-value">8</div><div class="mock-stat-sub">across 4 programs</div></div>
        </div>
        <div style="font-size:12px;font-weight:600;color:var(--ink3);text-transform:uppercase;letter-spacing:0.06em;margin:16px 0 8px;">Recent churches</div>
        <div class="mock-church-row">
          <div class="mock-church-avatar" style="background:#4A6741;">G</div>
          <div class="mock-church-info"><div class="mock-church-name">Grace Community Church</div><div class="mock-church-sub">grace.ekklesia.app</div></div>
          <div class="mock-badge badge-premium">Premium</div>
        </div>
        <div class="mock-church-row">
          <div class="mock-church-avatar" style="background:#C49A3C;">L</div>
          <div class="mock-church-info"><div class="mock-church-name">Living Word Ministry</div><div class="mock-church-sub">living.ekklesia.app</div></div>
          <div class="mock-badge badge-standard">Standard</div>
        </div>
        <div class="mock-church-row">
          <div class="mock-church-avatar" style="background:#3B5998;">H</div>
          <div class="mock-church-info"><div class="mock-church-name">Hope Fellowship</div><div class="mock-church-sub">hope.ekklesia.app</div></div>
          <div class="mock-badge badge-basic">Basic</div>
        </div>
      </div>
    </div>
  </section>

  <div class="stats-row">
    <div class="stat-item"><div class="stat-number">200+</div><div class="stat-label">Churches registered</div></div>
    <div class="stat-item"><div class="stat-number">12K+</div><div class="stat-label">Disciples tracked</div></div>
    <div class="stat-item"><div class="stat-number">4</div><div class="stat-label">Discipleship programs</div></div>
    <div class="stat-item"><div class="stat-number">99.9%</div><div class="stat-label">Platform uptime</div></div>
  </div>

  <section class="section" id="plans-section">
    <div class="section-eyebrow">Pricing</div>
    <div class="section-title">One plan for every church size.</div>
    <div class="section-sub">Start free, grow at your own pace. Every church gets its own isolated platform and subdomain the moment you register.</div>
    <div class="plans-grid">
      <div class="plan-card" onclick="goToRegisterWithPlan('free')">
        <div class="plan-name">Free</div><div class="plan-price">₱0<span>/mo</span></div><div class="plan-period">Forever free · No credit card</div><div class="plan-divider"></div>
        <ul class="feature-list">
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>1 program (Life Class)</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Up to 20 disciples</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Up to 5 leaders</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Basic attendance tracking</li>
          <li class="feature-item feature-item-muted"><span class="feature-check" style="background:#F3F4F6;"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"/></svg></span>All programs</li>
          <li class="feature-item feature-item-muted"><span class="feature-check" style="background:#F3F4F6;"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"/></svg></span>Sheets export</li>
        </ul>
        <button class="plan-cta plan-cta-outline" onclick="event.stopPropagation();goToRegisterWithPlan('free')">Get started free</button>
      </div>
      <div class="plan-card" onclick="goToRegisterWithPlan('basic')">
        <div class="plan-name">Basic</div><div class="plan-price">₱800<span>/mo</span></div><div class="plan-period">Per church · Billed monthly</div><div class="plan-divider"></div>
        <ul class="feature-list">
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>All 4 programs</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Up to 50 disciples</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Up to 5 leaders</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Attendance & devotional tracking</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Basic hierarchical dashboard</li>
          <li class="feature-item feature-item-muted"><span class="feature-check" style="background:#F3F4F6;"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"/></svg></span>Sheets export</li>
        </ul>
        <button class="plan-cta plan-cta-outline" onclick="event.stopPropagation();goToRegisterWithPlan('basic')">Get started</button>
      </div>
      <div class="plan-card featured" onclick="goToRegisterWithPlan('standard')">
        <div class="plan-badge-wrap">Most popular</div>
        <div class="plan-name">Standard</div><div class="plan-price">₱1,500<span>/mo</span></div><div class="plan-period">Per church · Billed monthly</div><div class="plan-divider"></div>
        <ul class="feature-list">
          <li class="feature-item"><span class="feature-check" style="background:rgba(107,145,98,0.3)"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="#6B9162" stroke-width="1.5" stroke-linecap="round"/></svg></span>Unlimited disciples</li>
          <li class="feature-item"><span class="feature-check" style="background:rgba(107,145,98,0.3)"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="#6B9162" stroke-width="1.5" stroke-linecap="round"/></svg></span>Up to 10 leaders</li>
          <li class="feature-item"><span class="feature-check" style="background:rgba(107,145,98,0.3)"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="#6B9162" stroke-width="1.5" stroke-linecap="round"/></svg></span>Program completion dashboard</li>
          <li class="feature-item"><span class="feature-check" style="background:rgba(107,145,98,0.3)"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="#6B9162" stroke-width="1.5" stroke-linecap="round"/></svg></span>Sheets export</li>
          <li class="feature-item"><span class="feature-check" style="background:rgba(107,145,98,0.3)"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="#6B9162" stroke-width="1.5" stroke-linecap="round"/></svg></span>All 4 programs</li>
          <li class="feature-item feature-item-muted"><span class="feature-check" style="background:rgba(247,243,238,0.08)"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="rgba(247,243,238,0.2)" stroke-width="1.5" stroke-linecap="round"/></svg></span>Bulk import</li>
        </ul>
        <button class="plan-cta plan-cta-gold" onclick="event.stopPropagation();goToRegisterWithPlan('standard')">Get started</button>
      </div>
      <div class="plan-card" onclick="goToRegisterWithPlan('premium')">
        <div class="plan-name">Premium</div><div class="plan-price">₱2,500<span>/mo</span></div><div class="plan-period">Per church · Billed monthly</div><div class="plan-divider"></div>
        <ul class="feature-list">
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Unlimited disciples & leaders</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Bulk import disciples</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Historical reports per program</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Everything in Standard</li>
          <li class="feature-item"><span class="feature-check"><svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>Priority support</li>
        </ul>
        <button class="plan-cta plan-cta-outline" onclick="event.stopPropagation();goToRegisterWithPlan('premium')">Get started</button>
      </div>
    </div>
  </section>

  <section class="flow-section" id="flow-section">
    <div class="flow-inner">
      <div class="section-eyebrow">How it works</div>
      <div class="section-title">From registration to your<br>own platform in minutes.</div>
      <div class="section-sub">Everything is automated. You fill out a form, choose a plan, and Ekklesia provisions a fully isolated environment just for your church.</div>
      <div class="flow-steps">
        <div class="flow-step"><div class="flow-step-num">01</div><div class="flow-step-content"><div class="flow-step-title">Pastor fills the registration form</div><div class="flow-step-desc">The lead pastor provides basic church information — name, address, contact details, and the desired subdomain for their platform.</div><div class="flow-step-detail"><strong>Fields collected:</strong> Church name · Church address · Pastor full name · Pastor email · Desired subdomain<br><br><strong>Subdomain rules:</strong> Lowercase letters and numbers only. The system checks availability in real time.</div><div class="flow-step-tag">Central DB → tenants table (pending)</div></div></div>
        <div class="flow-step"><div class="flow-step-num">02</div><div class="flow-step-content"><div class="flow-step-title">Choose a subscription plan</div><div class="flow-step-desc">The pastor selects the plan that fits their congregation size and needs.</div><div class="flow-step-detail"><strong>Plan stored as:</strong> <code>subscription_plan_id</code> on the tenant record.<br><br><strong>Subscription dates auto-set:</strong> <code>subscription_started_at = now()</code> · <code>subscription_due_at = now() + 30 days</code></div><div class="flow-step-tag">subscription_plans table · billing_cycles table</div></div></div>
        <div class="flow-step"><div class="flow-step-num">03</div><div class="flow-step-content"><div class="flow-step-title">Payment (paid plans only)</div><div class="flow-step-desc">Paid plan selections go through payment confirmation before provisioning begins.</div><div class="flow-step-detail"><strong>Payment captured:</strong> Reference number · Amount · Date paid<br><br><strong>Recorded in:</strong> <code>billing_cycles</code> with <code>payment_status = paid</code><br><br><strong>Free tier:</strong> Skips this step entirely.</div><div class="flow-step-tag">billing_cycles → payment_status = paid</div></div></div>
        <div class="flow-step"><div class="flow-step-num">04</div><div class="flow-step-content"><div class="flow-step-title">Tenant provisioning begins</div><div class="flow-step-desc">The system creates an isolated environment — a dedicated MySQL database, schema migrations, and seeded config.</div><div class="flow-step-detail">1. New MySQL DB: <code>tenant_{subdomain}</code><br>2. Schema migrations run<br>3. Default programs seeded<br>4. Tenant <code>status = active</code><br>5. Wildcard DNS routed</div><div class="flow-step-tag">TenantProvisioningService::run()</div></div></div>
        <div class="flow-step"><div class="flow-step-num">05</div><div class="flow-step-content"><div class="flow-step-title">Subdomain goes live</div><div class="flow-step-desc">Laravel's tenant middleware resolves the subdomain, loads the tenant, and switches the database connection on every request.</div><div class="flow-step-detail"><code>SubdomainResolver</code> → <code>TenantMiddleware</code> → <code>DB::reconnect('tenant')</code> → <code>PlanFeatureGate</code></div><div class="flow-step-tag">TenantMiddleware · SubdomainResolver</div></div></div>
        <div class="flow-step"><div class="flow-step-num">06</div><div class="flow-step-content"><div class="flow-step-title">Welcome email dispatched</div><div class="flow-step-desc">A welcome email is sent with the subdomain URL, login credentials, plan summary, and next billing date.</div><div class="flow-step-detail"><strong>Email contains:</strong> Church name · Subdomain URL · Temp password link · Plan limits · Next renewal date</div><div class="flow-step-tag">Mail::to($pastor)->send(new TenantWelcomeMail)</div></div></div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="footer-logo">Ek<span>k</span>lesia</div>
    <div class="footer-copy">© 2025 Ekklesia. All rights reserved.</div>
  </footer>
</div>


<!-- ════════════════════════════════════════
     PAGE 2: REGISTER
════════════════════════════════════════ -->
<div id="page-register" class="page">
  <!--
    Source: pages/register.html
    Sections: Step Indicator · Step 1 (Church Info) · Step 2 (Plan) · Step 3 (Payment) · Step 4 (Confirm)
  -->
  <div class="register-wrap">
    <div class="register-header">
      <h2>Register your church</h2>
      <p>Set up your dedicated platform in under 5 minutes.<br>No technical knowledge required.</p>
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
</div>


<!-- ════════════════════════════════════════
     PAGE 3: PROVISIONING
════════════════════════════════════════ -->
<div id="page-provision" class="page">
  <!--
    Source: pages/provision.html
    Sections: Header · Terminal · Progress Steps · Success Card
  -->
  <div class="provision-wrap">
    <div class="provision-header">
      <h2 id="prov-title">Creating your platform…</h2>
      <p id="prov-sub">This usually takes 15–30 seconds. Please don't close this tab.</p>
    </div>
    <div class="terminal">
      <div class="terminal-bar"><div class="terminal-dots"><div class="terminal-dot"></div><div class="terminal-dot"></div><div class="terminal-dot"></div></div><div class="terminal-title">ekklesia — tenant provisioner</div></div>
      <div class="terminal-body" id="terminal-body"></div>
    </div>
    <div class="provision-steps" id="prov-steps">
      <div class="prov-step" id="pstep-1"><div class="prov-icon">🗄️</div><div class="prov-text"><div class="prov-label">Creating tenant database</div><div class="prov-sublabel" id="pslabel-1">Waiting...</div></div><div class="prov-status pending" id="pstatus-1">pending</div></div>
      <div class="prov-step" id="pstep-2"><div class="prov-icon">📋</div><div class="prov-text"><div class="prov-label">Running schema migrations</div><div class="prov-sublabel" id="pslabel-2">Waiting...</div></div><div class="prov-status pending" id="pstatus-2">pending</div></div>
      <div class="prov-step" id="pstep-3"><div class="prov-icon">🌱</div><div class="prov-text"><div class="prov-label">Seeding programs & config</div><div class="prov-sublabel" id="pslabel-3">Waiting...</div></div><div class="prov-status pending" id="pstatus-3">pending</div></div>
      <div class="prov-step" id="pstep-4"><div class="prov-icon">🌐</div><div class="prov-text"><div class="prov-label">Registering subdomain</div><div class="prov-sublabel" id="pslabel-4">Waiting...</div></div><div class="prov-status pending" id="pstatus-4">pending</div></div>
      <div class="prov-step" id="pstep-5"><div class="prov-icon">✉️</div><div class="prov-text"><div class="prov-label">Sending welcome email</div><div class="prov-sublabel" id="pslabel-5">Waiting...</div></div><div class="prov-status pending" id="pstatus-5">pending</div></div>
    </div>
    <div class="success-card" id="success-card">
      <div class="success-icon">🎉</div>
      <h3>Your platform is ready!</h3>
      <p>Your church's dedicated platform has been created and is live right now. Share the link below with your leaders to get started.</p>
      <div class="success-url" id="success-url-link" onclick="alert('Opening your subdomain...')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/></svg><span id="success-url-text"></span></div>
      <div class="success-detail" id="success-detail"></div>
      <button class="btn-primary" style="width:100%;justify-content:center;" onclick="alert('In a real implementation, this would open your church dashboard.')">Go to my dashboard <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
      <div style="margin-top:16px;"><button class="btn-ghost" onclick="resetAll()">Register another church</button></div>
    </div>
  </div>
</div>


</body>
</html>