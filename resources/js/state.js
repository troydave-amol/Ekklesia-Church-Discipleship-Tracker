// ════════════════════════════════════════
// state.js — Global app state & plan data
// ════════════════════════════════════════

window.state = {
  currentPage: 'page-landing',
  currentStep: 1,
  selectedPlan: null,
  subdomainAvailable: null,
  subdomainTimer: null,
};

window.PLANS = {
  free:     { name: 'Free',     price: 0,    priceStr: '₱0',     period: 'No billing',  paid: false },
  basic:    { name: 'Basic',    price: 800,  priceStr: '₱800',   period: 'Per month',   paid: true  },
  standard: { name: 'Standard', price: 1500, priceStr: '₱1,500', period: 'Per month',   paid: true  },
  premium:  { name: 'Premium',  price: 2500, priceStr: '₱2,500', period: 'Per month',   paid: true  },
};