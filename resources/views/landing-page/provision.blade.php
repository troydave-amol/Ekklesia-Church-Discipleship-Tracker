<!-- ════════════════════════════════════════
     pages/provision.html — Provisioning Page Content
     Include inside <div id="page-provision" class="page">
════════════════════════════════════════ -->
    @vite(['resources/css/app.css', 'resources/css/base.css', 'resources/css/landing.css', 'resources/css/register.css', 'resources/css/provision.css'])
    @vite(['resources/js/state.js', 'resources/js/router.js', 'resources/js/register.js', 'resources/js/provision.js'])

  <div class="provision-wrap">

    <div class="provision-header">
      <h2 id="prov-title">Creating your platform…</h2>
      <p id="prov-sub">This usually takes 15–30 seconds. Please don't close this tab.</p>
    </div>

    <!-- Terminal window -->
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

    <!-- Progress steps -->
    <div class="provision-steps" id="prov-steps">

      <div class="prov-step" id="pstep-1">
        <div class="prov-icon">🗄️</div>
        <div class="prov-text">
          <div class="prov-label">Creating tenant database</div>
          <div class="prov-sublabel" id="pslabel-1">Waiting...</div>
        </div>
        <div class="prov-status pending" id="pstatus-1">pending</div>
      </div>

      <div class="prov-step" id="pstep-2">
        <div class="prov-icon">📋</div>
        <div class="prov-text">
          <div class="prov-label">Running schema migrations</div>
          <div class="prov-sublabel" id="pslabel-2">Waiting...</div>
        </div>
        <div class="prov-status pending" id="pstatus-2">pending</div>
      </div>

      <div class="prov-step" id="pstep-3">
        <div class="prov-icon">🌱</div>
        <div class="prov-text">
          <div class="prov-label">Seeding programs & config</div>
          <div class="prov-sublabel" id="pslabel-3">Waiting...</div>
        </div>
        <div class="prov-status pending" id="pstatus-3">pending</div>
      </div>

      <div class="prov-step" id="pstep-4">
        <div class="prov-icon">🌐</div>
        <div class="prov-text">
          <div class="prov-label">Registering subdomain</div>
          <div class="prov-sublabel" id="pslabel-4">Waiting...</div>
        </div>
        <div class="prov-status pending" id="pstatus-4">pending</div>
      </div>

      <div class="prov-step" id="pstep-5">
        <div class="prov-icon">✉️</div>
        <div class="prov-text">
          <div class="prov-label">Sending welcome email</div>
          <div class="prov-sublabel" id="pslabel-5">Waiting...</div>
        </div>
        <div class="prov-status pending" id="pstatus-5">pending</div>
      </div>

    </div>

    <!-- Success card (shown after provisioning) -->
    <div class="success-card" id="success-card">
      <div class="success-icon">🎉</div>
      <h3>Your platform is ready!</h3>
      <p>Your church's dedicated platform has been created and is live right now. Share the link below with your leaders to get started.</p>

      <div class="success-url" id="success-url-link" onclick="alert('Opening your subdomain...')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/></svg>
        <span id="success-url-text"></span>
      </div>

      <!-- Filled dynamically by provision.js -->
      <div class="success-detail" id="success-detail"></div>

      <button class="btn-primary" style="width:100%;justify-content:center;" onclick="alert('In a real implementation, this would open your church dashboard.')">
        Go to my dashboard
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>

      <div style="margin-top:16px;">
        <button class="btn-ghost" onclick="resetAll()">Register another church</button>
      </div>
    </div>

  </div>