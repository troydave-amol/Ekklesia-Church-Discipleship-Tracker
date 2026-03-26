<!-- ════════════════════════════════════════
     pages/landing.html — Landing Page Content
     Include inside <div id="page-landing" class="page active">
════════════════════════════════════════ -->

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
        <button class="btn-primary" onclick="goToPage('page-register')">
          Register your church
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
        <button class="btn-secondary" onclick="scrollToPlans()">View pricing</button>
      </div>
      <p class="hero-note">✓ Free tier available &nbsp; ✓ No setup fee &nbsp; ✓ Philippines-based support</p>
    </div>

    <!-- Browser Mockup -->
    <div class="hero-visual">
      <div class="browser-bar">
        <div class="browser-dots">
          <div class="browser-dot"></div>
          <div class="browser-dot"></div>
          <div class="browser-dot"></div>
        </div>
        <div class="browser-url">gracechurch.ekklesia.app</div>
      </div>
      <div class="browser-content">
        <div style="font-size:12px;font-weight:600;color:var(--ink3);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:12px;">Admin Overview</div>
        <div class="mock-dashboard">
          <div class="mock-stat">
            <div class="mock-stat-label">Total disciples</div>
            <div class="mock-stat-value">147</div>
            <div class="mock-stat-sub">↑ 12 this month</div>
          </div>
          <div class="mock-stat">
            <div class="mock-stat-label">Active programs</div>
            <div class="mock-stat-value">4</div>
            <div class="mock-stat-sub">All running</div>
          </div>
          <div class="mock-stat">
            <div class="mock-stat-label">Attendance rate</div>
            <div class="mock-stat-value">87%</div>
            <div class="mock-stat-sub">↑ 3% vs last week</div>
          </div>
          <div class="mock-stat">
            <div class="mock-stat-label">Leaders</div>
            <div class="mock-stat-value">8</div>
            <div class="mock-stat-sub">across 4 programs</div>
          </div>
        </div>
        <div style="font-size:12px;font-weight:600;color:var(--ink3);text-transform:uppercase;letter-spacing:0.06em;margin:16px 0 8px;">Recent churches</div>
        <div class="mock-church-row">
          <div class="mock-church-avatar" style="background:#4A6741;">G</div>
          <div class="mock-church-info">
            <div class="mock-church-name">Grace Community Church</div>
            <div class="mock-church-sub">grace.ekklesia.app</div>
          </div>
          <div class="mock-badge badge-premium">Premium</div>
        </div>
        <div class="mock-church-row">
          <div class="mock-church-avatar" style="background:#C49A3C;">L</div>
          <div class="mock-church-info">
            <div class="mock-church-name">Living Word Ministry</div>
            <div class="mock-church-sub">living.ekklesia.app</div>
          </div>
          <div class="mock-badge badge-standard">Standard</div>
        </div>
        <div class="mock-church-row">
          <div class="mock-church-avatar" style="background:#3B5998;">H</div>
          <div class="mock-church-info">
            <div class="mock-church-name">Hope Fellowship</div>
            <div class="mock-church-sub">hope.ekklesia.app</div>
          </div>
          <div class="mock-badge badge-basic">Basic</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Row -->
  <div class="stats-row">
    <div class="stat-item">
      <div class="stat-number">200+</div>
      <div class="stat-label">Churches registered</div>
    </div>
    <div class="stat-item">
      <div class="stat-number">12K+</div>
      <div class="stat-label">Disciples tracked</div>
    </div>
    <div class="stat-item">
      <div class="stat-number">4</div>
      <div class="stat-label">Discipleship programs</div>
    </div>
    <div class="stat-item">
      <div class="stat-number">99.9%</div>
      <div class="stat-label">Platform uptime</div>
    </div>
  </div>

  <!-- Plans Section -->
  <section class="section" id="plans-section">
    <div class="section-eyebrow">Pricing</div>
    <div class="section-title">One plan for every church size.</div>
    <div class="section-sub">Start free, grow at your own pace. Every church gets its own isolated platform and subdomain the moment you register.</div>

    <div class="plans-grid">

      <!-- Free -->
      <div class="plan-card" onclick="goToRegisterWithPlan('free')">
        <div class="plan-name">Free</div>
        <div class="plan-price">₱0<span>/mo</span></div>
        <div class="plan-period">Forever free · No credit card</div>
        <div class="plan-divider"></div>
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

      <!-- Basic -->
      <div class="plan-card" onclick="goToRegisterWithPlan('basic')">
        <div class="plan-name">Basic</div>
        <div class="plan-price">₱800<span>/mo</span></div>
        <div class="plan-period">Per church · Billed monthly</div>
        <div class="plan-divider"></div>
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

      <!-- Standard (featured) -->
      <div class="plan-card featured" onclick="goToRegisterWithPlan('standard')">
        <div class="plan-badge-wrap">Most popular</div>
        <div class="plan-name">Standard</div>
        <div class="plan-price">₱1,500<span>/mo</span></div>
        <div class="plan-period">Per church · Billed monthly</div>
        <div class="plan-divider"></div>
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

      <!-- Premium -->
      <div class="plan-card" onclick="goToRegisterWithPlan('premium')">
        <div class="plan-name">Premium</div>
        <div class="plan-price">₱2,500<span>/mo</span></div>
        <div class="plan-period">Per church · Billed monthly</div>
        <div class="plan-divider"></div>
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

  <!-- How It Works -->
  <section class="flow-section" id="flow-section">
    <div class="flow-inner">
      <div class="section-eyebrow">How it works</div>
      <div class="section-title">From registration to your<br>own platform in minutes.</div>
      <div class="section-sub">Everything is automated. You fill out a form, choose a plan, and Ekklesia provisions a fully isolated environment just for your church.</div>

      <div class="flow-steps">

        <div class="flow-step">
          <div class="flow-step-num">01</div>
          <div class="flow-step-content">
            <div class="flow-step-title">Pastor fills the registration form</div>
            <div class="flow-step-desc">The lead pastor provides basic church information — name, address, contact details, and the desired subdomain for their platform. This becomes the identity of your church's instance.</div>
            <div class="flow-step-detail">
              <strong>Fields collected:</strong> Church name · Church address · Pastor full name · Pastor email · Desired subdomain (e.g. <code>gracechurch</code>)<br><br>
              <strong>Subdomain rules:</strong> Lowercase letters and numbers only, no spaces. The system checks availability in real time against existing tenants.
            </div>
            <div class="flow-step-tag">Central DB → tenants table (pending)</div>
          </div>
        </div>

        <div class="flow-step">
          <div class="flow-step-num">02</div>
          <div class="flow-step-content">
            <div class="flow-step-title">Choose a subscription plan</div>
            <div class="flow-step-desc">The pastor selects the plan that fits their congregation size and needs. The plan determines feature access, disciple/leader limits, and billing cycle. Free tier starts immediately with no payment needed.</div>
            <div class="flow-step-detail">
              <strong>Plan stored as:</strong> <code>subscription_plan_id</code> on the tenant record.<br><br>
              <strong>Subscription dates auto-set:</strong> <code>subscription_started_at = now()</code> · <code>subscription_due_at = now() + 30 days</code> for paid plans.<br><br>
              <strong>Free tier:</strong> No due date set. Access persists unless manually downgraded.
            </div>
            <div class="flow-step-tag">subscription_plans table · billing_cycles table</div>
          </div>
        </div>

        <div class="flow-step">
          <div class="flow-step-num">03</div>
          <div class="flow-step-content">
            <div class="flow-step-title">Payment (paid plans only)</div>
            <div class="flow-step-desc">Paid plan selections go through payment confirmation before provisioning begins. For this platform, payment is handled via GCash or bank transfer with a reference number confirmation step.</div>
            <div class="flow-step-detail">
              <strong>Payment captured:</strong> Reference number · Amount · Date paid<br><br>
              <strong>Recorded in:</strong> <code>billing_cycles</code> table with <code>payment_status = paid</code><br><br>
              <strong>Free tier:</strong> Skips this step entirely — provisioning starts immediately after form submission.
            </div>
            <div class="flow-step-tag">billing_cycles → payment_status = paid</div>
          </div>
        </div>

        <div class="flow-step">
          <div class="flow-step-num">04</div>
          <div class="flow-step-content">
            <div class="flow-step-title">Tenant provisioning begins</div>
            <div class="flow-step-desc">The system automatically creates an isolated environment for the church. A dedicated MySQL database is created, all schema migrations run, and the tenant config is seeded with plan limits.</div>
            <div class="flow-step-detail">
              <strong>What happens automatically:</strong><br>
              1. New MySQL database created: <code>tenant_{subdomain}</code><br>
              2. Schema migrations run on the new database<br>
              3. Default programs seeded based on plan<br>
              4. Tenant record updated: <code>status = active</code><br>
              5. Wildcard DNS routes <code>{subdomain}.ekklesia.app</code> to the shared Laravel server
            </div>
            <div class="flow-step-tag">TenantProvisioningService::run()</div>
          </div>
        </div>

        <div class="flow-step">
          <div class="flow-step-num">05</div>
          <div class="flow-step-content">
            <div class="flow-step-title">Subdomain goes live</div>
            <div class="flow-step-desc">The church's subdomain is now active. Laravel's tenant middleware resolves the subdomain on every request, loads the tenant from the central DB, and switches the database connection to the church's own isolated database.</div>
            <div class="flow-step-detail">
              <strong>Middleware chain on every request:</strong><br>
              <code>SubdomainResolver</code> → reads <code>{subdomain}.ekklesia.app</code><br>
              <code>TenantMiddleware</code> → looks up tenant in central DB<br>
              <code>DB::reconnect('tenant')</code> → switches to <code>tenant_{subdomain}</code><br>
              <code>PlanFeatureGate</code> → enforces limits
            </div>
            <div class="flow-step-tag">TenantMiddleware · SubdomainResolver</div>
          </div>
        </div>

        <div class="flow-step">
          <div class="flow-step-num">06</div>
          <div class="flow-step-content">
            <div class="flow-step-title">Welcome email dispatched</div>
            <div class="flow-step-desc">A welcome email is sent to the registered pastor with their subdomain URL, initial login credentials, plan summary, and next billing date.</div>
            <div class="flow-step-detail">
              <strong>Email contains:</strong> Church name · Subdomain URL · Temporary password link · Plan name & limits · Next renewal date (if paid) · Support contact
            </div>
            <div class="flow-step-tag">Mail::to($pastor)->send(new TenantWelcomeMail)</div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-logo">Ek<span>k</span>lesia</div>
    <div class="footer-copy">© 2025 Ekklesia. All rights reserved.</div>
  </footer>