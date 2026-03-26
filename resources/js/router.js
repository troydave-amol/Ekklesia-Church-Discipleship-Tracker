// ════════════════════════════════════════
// router.js — Page navigation & scroll helpers
// ════════════════════════════════════════

function goToPage(id) {
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

function scrollToPlans() {
  navigateAndScroll('plans-section');
}

function scrollToFlow() {
  navigateAndScroll('flow-section');
}

function navigateAndScroll(sectionId) {
  if (state.currentPage !== 'page-landing') {
    goToPage('page-landing');

    requestAnimationFrame(() => {
      document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
    });

    return;
  }

  document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
}

function goToRegisterWithPlan(plan) {
  goToPage('page-register');

  requestAnimationFrame(() => {
    if (typeof selectPlan === 'function') {
      selectPlan(plan);
    } else {
      console.warn('selectPlan() not loaded yet');
    }
  });
}