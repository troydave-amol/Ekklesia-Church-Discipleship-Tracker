// ════════════════════════════════════════
// router.js — Page navigation & scroll helpers
// ════════════════════════════════════════

window.goToPage = function(id) {
  const pages = document.querySelectorAll('.page');
  const target = document.getElementById(id);

  if (!target) {
    console.error('Page not found:', id);
    return;
  }

  pages.forEach(p => p.classList.remove('active'));
  target.classList.add('active');

  state.currentPage = id;

  window.scrollTo({ top: 0, behavior: 'instant' });

  console.log('Navigated to:', id);
}

window.scrollToPlans = function() {
  navigateAndScroll('plans-section');
}

window.scrollToFlow = function() {
  navigateAndScroll('flow-section');
}

window.navigateAndScroll = function(sectionId) {
  if (state.currentPage !== 'page-landing') {
    goToPage('page-landing');

    requestAnimationFrame(() => {
      document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
    });

    return;
  }

  document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
}

window.goToRegisterWithPlan = function(plan) {
  goToPage('page-register');

  requestAnimationFrame(() => {
    if (typeof selectPlan === 'function') {
      selectPlan(plan);
    } else {
      console.warn('selectPlan() not loaded yet');
    }
  });
}

// ── TENANT NAVIGATION ──
function getTenantBaseUrl() {
  const subdomain = document.getElementById('subdomain').value.trim();
  const currentHost = window.location.hostname;
  const port = window.location.port ? ':' + window.location.port : '';

  if (currentHost === 'localhost' || currentHost === '127.0.0.1') {
    return `http://${subdomain}.localhost${port}`;
  }
  return `https://${subdomain}.ekklesia.app`;
}

window.goToTenantDashboard = function() {
  window.location.href = getTenantBaseUrl() + '/dashboard';
}

window.goToTenantHome = function() {
  window.location.href = getTenantBaseUrl();
}