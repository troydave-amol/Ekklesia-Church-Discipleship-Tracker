// ════════════════════════════════════════
// provision.js — Tenant provisioning animation
// ════════════════════════════════════════

function runProvisioning() {
  const subdomain = document.getElementById('subdomain').value.trim();
  const plan      = PLANS[state.selectedPlan];
  const dbName    = 'tenant_' + subdomain;
  const email     = document.getElementById('pastor-email').value.trim();

  // ── Terminal lines ──
  const terminalLines = [
    { delay: 200,  type: 'comment', text: '# Ekklesia TenantProvisioningService' },
    { delay: 600,  type: 'cmd',     text: `php artisan tenant:create --subdomain=${subdomain}` },
    { delay: 1200, type: 'info',    text: `→ Validating subdomain "${subdomain}"... OK` },
    { delay: 1700, type: 'cmd',     text: `mysql -e "CREATE DATABASE ${dbName} CHARACTER SET utf8mb4;"` },
    { delay: 2400, type: 'success', text: `✓ Database "${dbName}" created` },
    { delay: 2900, type: 'cmd',     text: `php artisan migrate --database=tenant --path=database/migrations/tenant` },
    { delay: 3300, type: 'info',    text: '  Migrating: 2024_01_01_create_programs_table' },
    { delay: 3600, type: 'info',    text: '  Migrating: 2024_01_02_create_leaders_table' },
    { delay: 3900, type: 'info',    text: '  Migrating: 2024_01_03_create_disciples_table' },
    { delay: 4200, type: 'info',    text: '  Migrating: 2024_01_04_create_lessons_table' },
    { delay: 4500, type: 'info',    text: '  Migrating: 2024_01_05_create_attendance_table' },
    { delay: 4700, type: 'info',    text: '  Migrating: 2024_01_06_create_devotionals_table' },
    { delay: 4900, type: 'info',    text: '  Migrating: 2024_01_07_create_progress_table' },
    { delay: 5200, type: 'success', text: '✓ All migrations ran successfully' },
    { delay: 5600, type: 'cmd',     text: `php artisan db:seed --class=TenantSeeder --plan=${state.selectedPlan}` },
    { delay: 6100, type: 'info',    text: `  Seeding plan config: ${plan.name}` },
    { delay: 6400, type: 'info',    text: '  Seeding default programs...' },
    { delay: 6800, type: 'success', text: '✓ Tenant seeded successfully' },
    { delay: 7100, type: 'cmd',     text: `php artisan tenant:register-subdomain ${subdomain}` },
    { delay: 7600, type: 'info',    text: `  Wildcard DNS: *.ekklesia.app → 103.x.x.x` },
    { delay: 7900, type: 'success', text: `✓ Subdomain ${subdomain}.ekklesia.app is live` },
    { delay: 8200, type: 'cmd',     text: 'php artisan mail:send --type=TenantWelcomeMail' },
    { delay: 8600, type: 'success', text: `✓ Welcome email sent to ${email}` },
    { delay: 9000, type: 'success', text: '✓ Provisioning complete!' },
  ];

  // ── Progress steps ──
  const provSteps = [
    { delay: 1700, doneDelay: 2400, step: 1, label: dbName },
    { delay: 2900, doneDelay: 5200, step: 2, label: 'Running 7 migrations' },
    { delay: 5600, doneDelay: 6800, step: 3, label: `Plan: ${plan.name}` },
    { delay: 7100, doneDelay: 7900, step: 4, label: `${subdomain}.ekklesia.app` },
    { delay: 8200, doneDelay: 8600, step: 5, label: email },
  ];

  // ── Render terminal ──
  const body = document.getElementById('terminal-body');
  body.innerHTML = '';

  terminalLines.forEach(({ delay, type, text }) => {
    setTimeout(() => {
      const line = document.createElement('div');
      line.className = 't-line';
      if      (type === 'cmd')     line.innerHTML = `<span class="t-prompt">$</span><span class="t-cmd">${text}</span>`;
      else if (type === 'comment') line.innerHTML = `<span class="t-comment">${text}</span>`;
      else if (type === 'success') line.innerHTML = `<span class="t-success">${text}</span>`;
      else                         line.innerHTML = `<span class="t-info">${text}</span>`;
      body.appendChild(line);
      requestAnimationFrame(() => { line.classList.add('show'); body.scrollTop = body.scrollHeight; });
    }, delay);
  });

  // ── Progress step states ──
  provSteps.forEach(({ delay, doneDelay, step, label }) => {
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

  // ── Show success card ──
  setTimeout(() => {
    document.getElementById('prov-title').textContent = 'Platform ready!';
    document.getElementById('prov-sub').textContent   = 'Your church platform is live and accessible.';

    const now = new Date();
    const due = new Date(now); due.setDate(due.getDate() + 30);

    document.getElementById('success-url-text').textContent = subdomain + '.ekklesia.app';
    document.getElementById('success-detail').innerHTML = `
      <div class="success-detail-item">
        <div class="success-detail-label">Database</div>
        <div class="success-detail-value">${dbName}</div>
      </div>
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
  }, 9600);
}