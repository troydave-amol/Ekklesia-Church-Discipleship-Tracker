<!DOCTYPE html>
<html lang="en">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Church Management Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --cream: #F7F3EE;
  --ink: #1A1612;
  --ink2: #3D3630;
  --ink3: #7A7068;
  --gold: #C49A3C;
  --gold-light: #E8C97A;
  --gold-pale: #F5EDD6;
  --sage: #4A6741;
  --sage-light: #6B9162;
  --sage-pale: #E8F0E6;
  --red: #C0392B;
  --red-pale: #FDECEA;
  --border: rgba(26,22,18,0.12);
  --border-strong: rgba(26,22,18,0.25);
  --shadow: 0 2px 16px rgba(26,22,18,0.08);
  --shadow-lg: 0 8px 48px rgba(26,22,18,0.14);
  --radius: 12px;
  --font-display: 'Playfair Display', Georgia, serif;
  --font-body: 'DM Sans', system-ui, sans-serif;
  --font-mono: 'DM Mono', monospace;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: var(--font-body);
  background: var(--cream);
  color: var(--ink);
  min-height: 100vh;
  overflow-x: hidden;
}

/* ── PAGE SYSTEM ── */
.page { display: none; min-height: 100vh; }
.page.active { display: block; }

/* ── NAV ── */
nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 100;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 48px; height: 64px;
  background: rgba(247,243,238,0.92);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
}
.nav-logo {
  font-family: var(--font-display);
  font-size: 22px; font-weight: 700;
  color: var(--ink); letter-spacing: -0.02em;
}
.nav-logo span { color: var(--gold); }
.nav-links { display: flex; gap: 32px; align-items: center; }
.nav-links a {
  font-size: 14px; font-weight: 500;
  color: var(--ink2); text-decoration: none;
  transition: color 0.2s;
  cursor: pointer;
}
.nav-links a:hover { color: var(--gold); }
.btn-nav {
  background: var(--ink); color: var(--cream);
  border: none; border-radius: 8px;
  padding: 8px 20px; font-size: 14px; font-weight: 500;
  font-family: var(--font-body);
  cursor: pointer; transition: background 0.2s, transform 0.15s;
}
.btn-nav:hover { background: var(--ink2); transform: translateY(-1px); }

/* ── BUTTONS ── */
.btn-primary {
  background: var(--gold); color: var(--ink);
  border: none; border-radius: 10px;
  padding: 14px 32px; font-size: 15px; font-weight: 600;
  font-family: var(--font-body);
  cursor: pointer; transition: all 0.2s;
  display: inline-flex; align-items: center; gap: 8px;
}
.btn-primary:hover { background: var(--gold-light); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(196,154,60,0.35); }
.btn-secondary {
  background: transparent; color: var(--ink2);
  border: 1.5px solid var(--border-strong); border-radius: 10px;
  padding: 13px 32px; font-size: 15px; font-weight: 500;
  font-family: var(--font-body);
  cursor: pointer; transition: all 0.2s;
}
.btn-secondary:hover { border-color: var(--ink); background: rgba(26,22,18,0.04); }
.btn-ghost {
  background: transparent; color: var(--ink3);
  border: none; font-size: 14px; font-weight: 500;
  font-family: var(--font-body);
  cursor: pointer; padding: 8px 0; transition: color 0.2s;
}
.btn-ghost:hover { color: var(--ink); }

/* ════════════════════════════════════════
   LANDING PAGE
════════════════════════════════════════ */
#page-landing {
  padding-top: 64px;
}

/* Hero */
.hero {
  position: relative;
  padding: 100px 48px 80px;
  display: grid; grid-template-columns: 1fr 480px;
  gap: 80px; align-items: center;
  max-width: 1200px; margin: 0 auto;
  overflow: hidden;
}
.hero-bg {
  position: absolute; inset: 0; pointer-events: none;
  background:
    radial-gradient(ellipse 600px 400px at 80% 50%, rgba(196,154,60,0.08) 0%, transparent 70%),
    radial-gradient(ellipse 400px 300px at 20% 80%, rgba(74,103,65,0.07) 0%, transparent 70%);
}
.hero-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  background: var(--gold-pale); border: 1px solid rgba(196,154,60,0.3);
  border-radius: 100px; padding: 5px 14px 5px 8px;
  font-size: 12px; font-weight: 600; color: var(--gold);
  letter-spacing: 0.06em; text-transform: uppercase;
  margin-bottom: 24px;
}
.hero-eyebrow-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--gold); display: inline-block;
}
.hero h1 {
  font-family: var(--font-display);
  font-size: clamp(42px, 5vw, 64px);
  font-weight: 700; line-height: 1.08;
  letter-spacing: -0.03em; color: var(--ink);
  margin-bottom: 24px;
}
.hero h1 em { color: var(--gold); font-style: normal; }
.hero p {
  font-size: 17px; line-height: 1.7;
  color: var(--ink3); max-width: 480px;
  margin-bottom: 40px;
}
.hero-actions { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.hero-note { font-size: 13px; color: var(--ink3); margin-top: 16px; }

/* Hero visual — mini browser mockup */
.hero-visual {
  background: white;
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-lg), 0 0 0 6px rgba(196,154,60,0.06);
  overflow: hidden;
  position: relative;
}
.browser-bar {
  background: #F0EDE8; padding: 10px 16px;
  display: flex; align-items: center; gap: 10px;
  border-bottom: 1px solid var(--border);
}
.browser-dots { display: flex; gap: 6px; }
.browser-dot { width: 10px; height: 10px; border-radius: 50%; }
.browser-dot:nth-child(1) { background: #FF5F56; }
.browser-dot:nth-child(2) { background: #FFBD2E; }
.browser-dot:nth-child(3) { background: #27C93F; }
.browser-url {
  flex: 1; background: white; border-radius: 6px;
  padding: 4px 12px; font-size: 11px; font-family: var(--font-mono);
  color: var(--ink3); border: 1px solid var(--border);
}
.browser-content { padding: 24px; }
.mock-dashboard {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
}
.mock-stat {
  background: var(--cream); border-radius: 10px;
  padding: 16px; border: 1px solid var(--border);
}
.mock-stat-label { font-size: 10px; color: var(--ink3); font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px; }
.mock-stat-value { font-family: var(--font-display); font-size: 26px; font-weight: 700; color: var(--ink); }
.mock-stat-sub { font-size: 10px; color: var(--sage); margin-top: 2px; }
.mock-church-row {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 12px; border-radius: 8px;
  border: 1px solid var(--border); background: white;
  margin-top: 10px;
}
.mock-church-avatar {
  width: 32px; height: 32px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 700; color: white;
  flex-shrink: 0;
}
.mock-church-info { flex: 1; min-width: 0; }
.mock-church-name { font-size: 12px; font-weight: 600; color: var(--ink); }
.mock-church-sub { font-size: 10px; color: var(--ink3); margin-top: 1px; }
.mock-badge {
  font-size: 9px; font-weight: 600; border-radius: 100px;
  padding: 3px 8px; text-transform: uppercase; letter-spacing: 0.05em;
}
.badge-premium { background: var(--gold-pale); color: var(--gold); }
.badge-standard { background: var(--sage-pale); color: var(--sage); }
.badge-basic { background: #EEF2FF; color: #4F46E5; }
.badge-free { background: #F3F4F6; color: #6B7280; }

/* Stats row */
.stats-row {
  background: var(--ink);
  padding: 48px;
  display: grid; grid-template-columns: repeat(4,1fr);
  gap: 2px;
}
.stat-item {
  padding: 32px; text-align: center;
  border-right: 1px solid rgba(255,255,255,0.08);
}
.stat-item:last-child { border-right: none; }
.stat-number {
  font-family: var(--font-display);
  font-size: 44px; font-weight: 700; color: var(--gold);
  line-height: 1;
}
.stat-label { font-size: 14px; color: rgba(247,243,238,0.6); margin-top: 8px; }

/* Plans section */
.section { padding: 96px 48px; max-width: 1200px; margin: 0 auto; }
.section-eyebrow {
  font-size: 12px; font-weight: 600; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--gold);
  margin-bottom: 12px;
}
.section-title {
  font-family: var(--font-display);
  font-size: clamp(30px, 3.5vw, 44px);
  font-weight: 700; line-height: 1.15;
  letter-spacing: -0.02em; color: var(--ink);
  margin-bottom: 16px;
}
.section-sub { font-size: 17px; line-height: 1.65; color: var(--ink3); max-width: 560px; }

.plans-grid {
  display: grid; grid-template-columns: repeat(4,1fr);
  gap: 20px; margin-top: 56px;
}
.plan-card {
  background: white; border-radius: var(--radius);
  border: 1.5px solid var(--border);
  padding: 28px; position: relative;
  transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
  cursor: pointer;
}
.plan-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.plan-card.featured {
  border-color: var(--gold); background: var(--ink);
}
.plan-card.featured .plan-name,
.plan-card.featured .plan-price,
.plan-card.featured .plan-period,
.plan-card.featured .feature-item { color: var(--cream); }
.plan-card.featured .feature-item-muted { color: rgba(247,243,238,0.5); }
.plan-badge-wrap {
  position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
  background: var(--gold); color: var(--ink);
  border-radius: 100px; padding: 4px 14px;
  font-size: 11px; font-weight: 700; letter-spacing: 0.04em;
  white-space: nowrap;
}
.plan-name { font-size: 13px; font-weight: 600; color: var(--ink3); letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 16px; }
.plan-price { font-family: var(--font-display); font-size: 38px; font-weight: 700; color: var(--ink); line-height: 1; }
.plan-price span { font-size: 16px; font-weight: 400; }
.plan-period { font-size: 13px; color: var(--ink3); margin-top: 4px; margin-bottom: 24px; }
.plan-divider { height: 1px; background: var(--border); margin-bottom: 20px; }
.plan-card.featured .plan-divider { background: rgba(247,243,238,0.12); }
.feature-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
.feature-item { font-size: 13px; color: var(--ink2); display: flex; gap: 8px; align-items: flex-start; }
.feature-item-muted { color: var(--ink3); }
.feature-check { flex-shrink: 0; width: 16px; height: 16px; border-radius: 50%; background: var(--sage-pale); display: flex; align-items: center; justify-content: center; margin-top: 1px; }
.feature-check svg { color: var(--sage); }
.plan-card.featured .feature-check { background: rgba(107,145,98,0.25); }
.plan-cta { margin-top: 28px; width: 100%; text-align: center; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600; font-family: var(--font-body); cursor: pointer; border: none; transition: all 0.2s; }
.plan-cta-outline { background: transparent; color: var(--ink); border: 1.5px solid var(--border-strong); }
.plan-cta-outline:hover { background: var(--cream); }
.plan-cta-gold { background: var(--gold); color: var(--ink); }
.plan-cta-gold:hover { background: var(--gold-light); }

/* Flow section */
.flow-section { background: white; padding: 96px 0; }
.flow-inner { max-width: 1200px; margin: 0 auto; padding: 0 48px; }
.flow-steps { margin-top: 64px; display: flex; flex-direction: column; gap: 0; }
.flow-step {
  display: grid; grid-template-columns: 80px 1fr;
  gap: 32px; align-items: start;
  padding: 40px 0; border-bottom: 1px solid var(--border);
  position: relative;
}
.flow-step:last-child { border-bottom: none; }
.flow-step-num {
  font-family: var(--font-display); font-size: 56px; font-weight: 700;
  color: var(--gold-pale); line-height: 1; text-align: right;
  padding-top: 4px;
}
.flow-step-content { }
.flow-step-title { font-family: var(--font-display); font-size: 22px; font-weight: 600; color: var(--ink); margin-bottom: 10px; }
.flow-step-desc { font-size: 15px; line-height: 1.7; color: var(--ink3); max-width: 560px; }
.flow-step-detail {
  margin-top: 16px; background: var(--cream);
  border-radius: 10px; padding: 16px 20px;
  border: 1px solid var(--border);
  font-size: 13px; line-height: 1.6; color: var(--ink2);
}
.flow-step-detail strong { color: var(--ink); }
.flow-step-tag {
  display: inline-block; margin-top: 12px;
  background: var(--ink); color: var(--cream);
  border-radius: 100px; padding: 4px 12px;
  font-size: 11px; font-weight: 600; font-family: var(--font-mono);
}

/* Footer */
.footer {
  background: var(--ink); padding: 48px;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 16px;
}
.footer-logo { font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--cream); }
.footer-logo span { color: var(--gold); }
.footer-copy { font-size: 13px; color: rgba(247,243,238,0.4); }

/* ════════════════════════════════════════
   REGISTRATION FLOW
════════════════════════════════════════ */
#page-register {
  padding-top: 64px;
  background: var(--cream);
  min-height: 100vh;
}
.register-wrap {
  max-width: 720px; margin: 0 auto;
  padding: 56px 24px 96px;
}
.register-header { text-align: center; margin-bottom: 48px; }
.register-header h2 {
  font-family: var(--font-display); font-size: 34px; font-weight: 700;
  color: var(--ink); letter-spacing: -0.02em; margin-bottom: 10px;
}
.register-header p { font-size: 15px; color: var(--ink3); line-height: 1.6; }

/* Step indicator */
.step-indicator {
  display: flex; align-items: center; justify-content: center;
  gap: 0; margin-bottom: 48px;
}
.step-pip {
  display: flex; flex-direction: column; align-items: center; gap: 6px;
  position: relative;
}
.step-pip-circle {
  width: 36px; height: 36px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 600;
  border: 2px solid var(--border);
  background: white; color: var(--ink3);
  transition: all 0.3s; z-index: 2;
}
.step-pip.active .step-pip-circle { border-color: var(--gold); background: var(--gold); color: var(--ink); }
.step-pip.done .step-pip-circle { border-color: var(--sage); background: var(--sage); color: white; }
.step-pip-label { font-size: 11px; font-weight: 500; color: var(--ink3); white-space: nowrap; }
.step-pip.active .step-pip-label { color: var(--gold); }
.step-pip.done .step-pip-label { color: var(--sage); }
.step-line {
  height: 2px; width: 80px; background: var(--border);
  margin-bottom: 22px; transition: background 0.3s;
}
.step-line.done { background: var(--sage); }

/* Form card */
.form-card {
  background: white; border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  overflow: hidden;
}
.form-card-header {
  padding: 24px 32px 20px;
  border-bottom: 1px solid var(--border);
  background: var(--cream);
}
.form-card-title { font-family: var(--font-display); font-size: 20px; font-weight: 600; color: var(--ink); margin-bottom: 4px; }
.form-card-sub { font-size: 13px; color: var(--ink3); }
.form-card-body { padding: 32px; }

/* Form elements */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-grid.full { grid-template-columns: 1fr; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.span2 { grid-column: span 2; }
label { font-size: 13px; font-weight: 600; color: var(--ink2); }
label .label-hint { font-weight: 400; color: var(--ink3); margin-left: 4px; }
input[type=text], input[type=email], select {
  height: 44px; border-radius: 10px;
  border: 1.5px solid var(--border);
  background: var(--cream); color: var(--ink);
  font-size: 14px; font-family: var(--font-body);
  padding: 0 14px; width: 100%;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}
input[type=text]:focus, input[type=email]:focus, select:focus {
  border-color: var(--gold); box-shadow: 0 0 0 3px rgba(196,154,60,0.15);
  background: white;
}
input.error { border-color: var(--red); }
input.error:focus { box-shadow: 0 0 0 3px rgba(192,57,43,0.12); }
.field-error { font-size: 12px; color: var(--red); margin-top: 2px; display: none; }
.field-error.show { display: block; }

/* Subdomain field */
.subdomain-wrap {
  display: flex; align-items: stretch;
  border: 1.5px solid var(--border); border-radius: 10px;
  overflow: hidden; background: var(--cream);
  transition: border-color 0.2s, box-shadow 0.2s;
}
.subdomain-wrap:focus-within {
  border-color: var(--gold); box-shadow: 0 0 0 3px rgba(196,154,60,0.15);
  background: white;
}
.subdomain-input {
  border: none !important; border-radius: 0 !important;
  background: transparent !important;
  box-shadow: none !important; flex: 1;
}
.subdomain-suffix {
  display: flex; align-items: center; padding: 0 14px;
  background: rgba(26,22,18,0.05); border-left: 1px solid var(--border);
  font-size: 13px; font-family: var(--font-mono); color: var(--ink3);
  white-space: nowrap;
}

/* Availability indicator */
.avail-indicator {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; margin-top: 4px;
}
.avail-dot { width: 7px; height: 7px; border-radius: 50%; }
.avail-dot.checking { background: var(--ink3); animation: pulse 1s infinite; }
.avail-dot.available { background: var(--sage); }
.avail-dot.taken { background: var(--red); }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

/* Plan picker */
.plan-picker { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.plan-option {
  border: 2px solid var(--border); border-radius: 12px;
  padding: 20px; cursor: pointer;
  transition: all 0.2s; position: relative;
  background: white;
}
.plan-option:hover { border-color: var(--gold-light); background: var(--gold-pale); }
.plan-option.selected { border-color: var(--gold); background: var(--gold-pale); }
.plan-option-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.plan-option-name { font-size: 14px; font-weight: 700; color: var(--ink); }
.plan-option-price { font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--gold); }
.plan-option-price .psmall { font-size: 12px; font-weight: 400; color: var(--ink3); }
.plan-option-features { font-size: 12px; color: var(--ink3); line-height: 1.6; }
.plan-option-radio {
  position: absolute; top: 16px; right: 16px;
  width: 18px; height: 18px; border-radius: 50%;
  border: 2px solid var(--border); background: white;
  transition: all 0.2s; display: flex; align-items: center; justify-content: center;
}
.plan-option.selected .plan-option-radio { border-color: var(--gold); background: var(--gold); }
.plan-option.selected .plan-option-radio::after {
  content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--ink); display: block;
}

/* Form nav */
.form-nav {
  display: flex; justify-content: space-between; align-items: center;
  padding: 20px 32px; border-top: 1px solid var(--border); background: var(--cream);
}

/* Payment section */
.payment-summary {
  background: var(--ink); color: var(--cream);
  border-radius: 12px; padding: 24px; margin-bottom: 24px;
}
.payment-summary-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
.payment-plan-name { font-size: 13px; color: rgba(247,243,238,0.6); margin-bottom: 4px; }
.payment-plan-price { font-family: var(--font-display); font-size: 32px; font-weight: 700; color: var(--gold); }
.payment-plan-sub { font-size: 12px; color: rgba(247,243,238,0.5); }
.payment-due {
  background: rgba(196,154,60,0.15); border-radius: 8px; padding: 10px 14px;
  display: flex; gap: 10px; align-items: flex-start; margin-top: 12px;
}
.payment-due-icon { color: var(--gold); font-size: 15px; flex-shrink: 0; margin-top: 1px; }
.payment-due-text { font-size: 12px; color: rgba(247,243,238,0.7); line-height: 1.5; }
.payment-due-text strong { color: var(--gold); }

.gcash-note {
  display: flex; gap: 10px; align-items: flex-start;
  background: var(--sage-pale); border-radius: 10px; padding: 14px 16px;
  margin-bottom: 20px; border: 1px solid rgba(74,103,65,0.2);
}
.gcash-note-icon { color: var(--sage); font-size: 16px; flex-shrink: 0; }
.gcash-note-text { font-size: 13px; color: var(--sage); line-height: 1.5; }

/* ════════════════════════════════════════
   PROVISIONING PAGE
════════════════════════════════════════ */
#page-provision {
  padding-top: 64px; background: var(--ink);
  min-height: 100vh; display: flex; flex-direction: column;
}
.provision-wrap {
  max-width: 680px; margin: 0 auto;
  padding: 72px 24px 96px; flex: 1;
  display: flex; flex-direction: column;
}
.provision-header { text-align: center; margin-bottom: 56px; }
.provision-header h2 {
  font-family: var(--font-display); font-size: 32px; font-weight: 700;
  color: var(--cream); letter-spacing: -0.02em; margin-bottom: 10px;
}
.provision-header p { font-size: 15px; color: rgba(247,243,238,0.5); }

/* Terminal */
.terminal {
  background: #0D0B08; border-radius: 14px;
  border: 1px solid rgba(255,255,255,0.08);
  overflow: hidden; margin-bottom: 24px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.terminal-bar {
  background: #1A1714; padding: 10px 16px;
  display: flex; align-items: center; gap: 10px;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.terminal-dots { display: flex; gap: 6px; }
.terminal-dot { width: 10px; height: 10px; border-radius: 50%; }
.terminal-dot:nth-child(1) { background: #FF5F56; }
.terminal-dot:nth-child(2) { background: #FFBD2E; }
.terminal-dot:nth-child(3) { background: #27C93F; }
.terminal-title { flex: 1; text-align: center; font-size: 12px; font-family: var(--font-mono); color: rgba(255,255,255,0.3); }
.terminal-body { padding: 24px; min-height: 280px; font-family: var(--font-mono); font-size: 13px; line-height: 1.8; }
.t-line { display: flex; gap: 8px; align-items: flex-start; opacity: 0; transform: translateY(4px); transition: all 0.3s; }
.t-line.show { opacity: 1; transform: none; }
.t-prompt { color: rgba(196,154,60,0.7); flex-shrink: 0; }
.t-cmd { color: rgba(247,243,238,0.85); }
.t-comment { color: rgba(247,243,238,0.25); }
.t-success { color: #27C93F; }
.t-info { color: #5BA3F0; }
.t-warn { color: #FFBD2E; }
.t-cursor { display: inline-block; width: 8px; height: 14px; background: rgba(247,243,238,0.7); animation: blink 1s infinite; vertical-align: middle; margin-left: 2px; }
@keyframes blink { 0%,50%{opacity:1} 51%,100%{opacity:0} }

/* Provision steps */
.provision-steps { display: flex; flex-direction: column; gap: 12px; margin-bottom: 32px; }
.prov-step {
  display: flex; align-items: center; gap: 14px;
  background: rgba(255,255,255,0.04); border-radius: 10px;
  padding: 14px 18px; border: 1px solid rgba(255,255,255,0.06);
  transition: all 0.3s;
}
.prov-step.running { border-color: rgba(196,154,60,0.3); background: rgba(196,154,60,0.06); }
.prov-step.done { border-color: rgba(39,201,63,0.3); background: rgba(39,201,63,0.05); }
.prov-icon {
  width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 15px;
  background: rgba(255,255,255,0.06); transition: all 0.3s;
}
.prov-step.running .prov-icon { background: rgba(196,154,60,0.15); animation: iconPulse 1.2s ease-in-out infinite; }
.prov-step.done .prov-icon { background: rgba(39,201,63,0.15); }
@keyframes iconPulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.1)} }
.prov-text { flex: 1; }
.prov-label { font-size: 14px; font-weight: 500; color: rgba(247,243,238,0.8); }
.prov-sublabel { font-size: 12px; color: rgba(247,243,238,0.35); margin-top: 2px; font-family: var(--font-mono); }
.prov-step.running .prov-label { color: var(--gold); }
.prov-step.done .prov-label { color: rgba(247,243,238,0.9); }
.prov-status { font-size: 12px; font-family: var(--font-mono); }
.prov-status.pending { color: rgba(247,243,238,0.2); }
.prov-status.running { color: var(--gold); }
.prov-status.done { color: #27C93F; }

/* Spinner */
.spinner {
  width: 14px; height: 14px; border: 2px solid rgba(196,154,60,0.3);
  border-top-color: var(--gold); border-radius: 50%;
  animation: spin 0.8s linear infinite; display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Success card */
.success-card {
  background: white; border-radius: 16px; padding: 36px;
  text-align: center; display: none;
  animation: fadeUp 0.5s ease;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
.success-card.show { display: block; }
.success-icon { font-size: 48px; margin-bottom: 16px; }
.success-card h3 { font-family: var(--font-display); font-size: 26px; font-weight: 700; color: var(--ink); margin-bottom: 8px; }
.success-card p { font-size: 15px; color: var(--ink3); line-height: 1.6; margin-bottom: 24px; }
.success-url {
  display: inline-flex; align-items: center; gap: 8px;
  background: var(--gold-pale); border: 1px solid rgba(196,154,60,0.3);
  border-radius: 8px; padding: 8px 16px;
  font-family: var(--font-mono); font-size: 15px; font-weight: 500; color: var(--gold);
  margin-bottom: 28px; cursor: pointer;
}
.success-detail {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  text-align: left; margin-bottom: 28px;
}
.success-detail-item {
  background: var(--cream); border-radius: 10px; padding: 14px 16px;
  border: 1px solid var(--border);
}
.success-detail-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ink3); margin-bottom: 4px; }
.success-detail-value { font-size: 14px; font-weight: 600; color: var(--ink); font-family: var(--font-mono); }

/* ── UTILITY ── */
.hidden { display: none !important; }
.mt-4 { margin-top: 16px; }
.mt-6 { margin-top: 24px; }

/* Responsive */
@media (max-width: 768px) {
  .hero { grid-template-columns: 1fr; padding: 60px 24px 40px; }
  .hero-visual { display: none; }
  .plans-grid { grid-template-columns: 1fr 1fr; }
  .stats-row { grid-template-columns: 1fr 1fr; padding: 32px; }
  nav { padding: 0 24px; }
  .nav-links { gap: 16px; }
  .form-grid { grid-template-columns: 1fr; }
  .form-grid .form-group.span2 { grid-column: span 1; }
  .plan-picker { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- ── NAV ── -->
<nav>
  <div class="nav-logo">Ek<span>k</span>lesia</div>
  <div class="nav-links">
    <a onclick="scrollToPlans()">Pricing</a>
    <a onclick="scrollToFlow()">How it works</a>
    <button class="btn-nav" onclick="goToPage('page-register')">Register your church</button>
  </div>
</nav>

<!-- ════════════════════════════════════════
     PAGE 1: LANDING
════════════════════════════════════════ -->
<div id="page-landing" class="page active">

  <!-- Hero -->
  <section class="hero">
    <div class="hero-bg"></div>
    <div>
      <div class="hero-eyebrow"><span class="hero-eyebrow-dot"></span>Built for the local church</div>
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

  <!-- Stats -->
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

  <!-- Plans -->
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

      <!-- Standard — featured -->
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

  <!-- How it works -->
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
            <div class="flow-step-desc">The system automatically creates an isolated environment for the church. This is the core technical step — a dedicated MySQL database is created, all schema migrations run, and the tenant config is seeded with plan limits.</div>
            <div class="flow-step-detail">
              <strong>What happens automatically:</strong><br>
              1. New MySQL database created: <code>tenant_{subdomain}</code><br>
              2. Schema migrations run on the new database (programs, leaders, disciples, attendance, devotionals, progress tables)<br>
              3. Default programs seeded based on plan<br>
              4. Tenant record updated: <code>status = active</code> · <code>db_name = tenant_{subdomain}</code><br>
              5. Wildcard DNS routes <code>{subdomain}.ekklesia.app</code> to the shared Laravel server
            </div>
            <div class="flow-step-tag">TenantProvisioningService::run()</div>
          </div>
        </div>

        <div class="flow-step">
          <div class="flow-step-num">05</div>
          <div class="flow-step-content">
            <div class="flow-step-title">Subdomain goes live</div>
            <div class="flow-step-desc">The church's subdomain is now active and accessible. Laravel's tenant middleware resolves the subdomain on every request, loads the tenant from the central DB, and switches the database connection to the church's own isolated database.</div>
            <div class="flow-step-detail">
              <strong>Middleware chain on every request:</strong><br>
              <code>SubdomainResolver</code> → reads <code>{subdomain}.ekklesia.app</code><br>
              <code>TenantMiddleware</code> → looks up tenant in central DB<br>
              <code>DB::reconnect('tenant')</code> → switches to <code>tenant_{subdomain}</code><br>
              <code>PlanFeatureGate</code> → enforces disciple/leader/feature limits
            </div>
            <div class="flow-step-tag">TenantMiddleware · SubdomainResolver</div>
          </div>
        </div>

        <div class="flow-step">
          <div class="flow-step-num">06</div>
          <div class="flow-step-content">
            <div class="flow-step-title">Welcome email dispatched</div>
            <div class="flow-step-desc">A welcome email is sent to the registered pastor with their subdomain URL, initial login credentials, plan summary, and next billing date. The pastor can log in immediately and start adding leaders and disciples.</div>
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

</div>

<!-- ════════════════════════════════════════
     PAGE 2: REGISTER
════════════════════════════════════════ -->
<div id="page-register" class="page">
  <div class="register-wrap">
    <div class="register-header">
      <h2>Register your church</h2>
      <p>Set up your dedicated platform in under 5 minutes.<br>No technical knowledge required.</p>
    </div>

    <!-- Step indicator -->
    <div class="step-indicator">
      <div class="step-pip active" id="pip-1">
        <div class="step-pip-circle" id="pip-circle-1">1</div>
        <div class="step-pip-label">Church info</div>
      </div>
      <div class="step-line" id="line-1"></div>
      <div class="step-pip" id="pip-2">
        <div class="step-pip-circle" id="pip-circle-2">2</div>
        <div class="step-pip-label">Choose plan</div>
      </div>
      <div class="step-line" id="line-2"></div>
      <div class="step-pip" id="pip-3">
        <div class="step-pip-circle" id="pip-circle-3">3</div>
        <div class="step-pip-label">Payment</div>
      </div>
      <div class="step-line" id="line-3"></div>
      <div class="step-pip" id="pip-4">
        <div class="step-pip-circle" id="pip-circle-4">4</div>
        <div class="step-pip-label">Confirm</div>
      </div>
    </div>

    <!-- STEP 1: Church Info -->
    <div id="step-1">
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">Church information</div>
          <div class="form-card-sub">Tell us about your church and the pastor registering this account.</div>
        </div>
        <div class="form-card-body">
          <div class="form-grid">
            <div class="form-group span2">
              <label for="church-name">Name of church</label>
              <input type="text" id="church-name" placeholder="e.g. Grace Community Church">
              <div class="field-error" id="err-church-name">Please enter your church name.</div>
            </div>
            <div class="form-group span2">
              <label for="church-address">Church address</label>
              <input type="text" id="church-address" placeholder="e.g. 123 Rizal Ave, Quezon City">
              <div class="field-error" id="err-church-address">Please enter the church address.</div>
            </div>
            <div class="form-group">
              <label for="pastor-name">Pastor's full name</label>
              <input type="text" id="pastor-name" placeholder="e.g. Rev. Juan dela Cruz">
              <div class="field-error" id="err-pastor-name">Please enter the pastor's name.</div>
            </div>
            <div class="form-group">
              <label for="pastor-email">Pastor's email</label>
              <input type="email" id="pastor-email" placeholder="pastor@church.org">
              <div class="field-error" id="err-pastor-email">Please enter a valid email address.</div>
            </div>
            <div class="form-group span2">
              <label for="subdomain">Subdomain <span class="label-hint">(your church's URL)</span></label>
              <div class="subdomain-wrap" id="subdomain-wrap">
                <input type="text" class="subdomain-input" id="subdomain" placeholder="gracechurch" oninput="checkSubdomain(this.value)" maxlength="32">
                <div class="subdomain-suffix">.ekklesia.app</div>
              </div>
              <div class="avail-indicator" id="avail-indicator" style="display:none;">
                <div class="avail-dot" id="avail-dot"></div>
                <span id="avail-text"></span>
              </div>
              <div class="field-error" id="err-subdomain">Please enter a valid subdomain (letters and numbers only).</div>
            </div>
          </div>
        </div>
        <div class="form-nav">
          <button class="btn-ghost" onclick="goToPage('page-landing')">← Back to home</button>
          <button class="btn-primary" onclick="nextStep(1)">Continue to plan selection →</button>
        </div>
      </div>
    </div>

    <!-- STEP 2: Plan Selection -->
    <div id="step-2" class="hidden">
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">Choose your subscription plan</div>
          <div class="form-card-sub">You can upgrade or change your plan at any time from your dashboard.</div>
        </div>
        <div class="form-card-body">
          <div class="plan-picker">
            <div class="plan-option" id="opt-free" onclick="selectPlan('free')">
              <div class="plan-option-radio" id="radio-free"></div>
              <div class="plan-option-top">
                <div>
                  <div class="plan-option-name">Free</div>
                  <div class="plan-option-price">₱0 <span class="psmall">/ month</span></div>
                </div>
              </div>
              <div class="plan-option-features">1 program only · Up to 20 disciples · Up to 5 leaders · Basic tracking</div>
            </div>
            <div class="plan-option" id="opt-basic" onclick="selectPlan('basic')">
              <div class="plan-option-radio" id="radio-basic"></div>
              <div class="plan-option-top">
                <div>
                  <div class="plan-option-name">Basic</div>
                  <div class="plan-option-price">₱800 <span class="psmall">/ month</span></div>
                </div>
              </div>
              <div class="plan-option-features">All 4 programs · Up to 50 disciples · 5 leaders · Devotional tracking · Dashboard</div>
            </div>
            <div class="plan-option" id="opt-standard" onclick="selectPlan('standard')">
              <div class="plan-option-radio" id="radio-standard"></div>
              <div class="plan-option-top">
                <div>
                  <div class="plan-option-name">Standard</div>
                  <div class="plan-option-price">₱1,500 <span class="psmall">/ month</span></div>
                </div>
              </div>
              <div class="plan-option-features">Unlimited disciples · Up to 10 leaders · Dashboard · Sheets export</div>
            </div>
            <div class="plan-option" id="opt-premium" onclick="selectPlan('premium')">
              <div class="plan-option-radio" id="radio-premium"></div>
              <div class="plan-option-top">
                <div>
                  <div class="plan-option-name">Premium</div>
                  <div class="plan-option-price">₱2,500 <span class="psmall">/ month</span></div>
                </div>
              </div>
              <div class="plan-option-features">Unlimited disciples & leaders · Bulk import · Historical reports · Priority support</div>
            </div>
          </div>
          <div class="field-error mt-4" id="err-plan" style="display:none;">Please select a plan to continue.</div>
        </div>
        <div class="form-nav">
          <button class="btn-ghost" onclick="prevStep(2)">← Back</button>
          <button class="btn-primary" onclick="nextStep(2)">Continue →</button>
        </div>
      </div>
    </div>

    <!-- STEP 3: Payment -->
    <div id="step-3" class="hidden">
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title" id="payment-step-title">Payment confirmation</div>
          <div class="form-card-sub" id="payment-step-sub">Complete payment to activate your subscription.</div>
        </div>
        <div class="form-card-body" id="payment-body">
          <!-- Filled dynamically -->
        </div>
        <div class="form-nav">
          <button class="btn-ghost" onclick="prevStep(3)">← Back</button>
          <button class="btn-primary" id="payment-next-btn" onclick="nextStep(3)">Continue →</button>
        </div>
      </div>
    </div>

    <!-- STEP 4: Confirm -->
    <div id="step-4" class="hidden">
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">Review & confirm</div>
          <div class="form-card-sub">Double-check your details before we create your platform.</div>
        </div>
        <div class="form-card-body" id="confirm-body">
          <!-- Filled dynamically -->
        </div>
        <div class="form-nav">
          <button class="btn-ghost" onclick="prevStep(4)">← Back</button>
          <button class="btn-primary" onclick="submitRegistration()">
            Create my church platform
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ════════════════════════════════════════
     PAGE 3: PROVISIONING
════════════════════════════════════════ -->
<div id="page-provision" class="page">
  <div class="provision-wrap">
    <div class="provision-header">
      <h2 id="prov-title">Creating your platform…</h2>
      <p id="prov-sub">This usually takes 15–30 seconds. Please don't close this tab.</p>
    </div>

    <div class="terminal">
      <div class="terminal-bar">
        <div class="terminal-dots">
          <div class="terminal-dot"></div><div class="terminal-dot"></div><div class="terminal-dot"></div>
        </div>
        <div class="terminal-title">ekklesia — tenant provisioner</div>
      </div>
      <div class="terminal-body" id="terminal-body"></div>
    </div>

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

    <div class="success-card" id="success-card">
      <div class="success-icon">🎉</div>
      <h3>Your platform is ready!</h3>
      <p>Your church's dedicated platform has been created and is live right now. Share the link below with your leaders to get started.</p>
      <div class="success-url" id="success-url-link" onclick="alert('Opening your subdomain...')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/></svg>
        <span id="success-url-text"></span>
      </div>
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
</div>

<script>
// ════════════════════════════════════════
// STATE
// ════════════════════════════════════════
const state = {
  currentPage: 'page-landing',
  currentStep: 1,
  selectedPlan: null,
  subdomainAvailable: null,
  subdomainTimer: null,
  takenSubdomains: ['grace','living','hope','metro','faith','light','word']
};

const PLANS = {
  free:     { name:'Free',     price: 0,    priceStr:'₱0',     period:'No billing', paid:false },
  basic:    { name:'Basic',    price: 800,  priceStr:'₱800',   period:'Per month', paid:true },
  standard: { name:'Standard', price: 1500, priceStr:'₱1,500', period:'Per month', paid:true },
  premium:  { name:'Premium',  price: 2500, priceStr:'₱2,500', period:'Per month', paid:true },
};

// ════════════════════════════════════════
// PAGE ROUTING
// ════════════════════════════════════════
function goToPage(id) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  state.currentPage = id;
  window.scrollTo(0,0);
}

function scrollToPlans() {
  if (state.currentPage !== 'page-landing') { goToPage('page-landing'); setTimeout(()=>document.getElementById('plans-section').scrollIntoView({behavior:'smooth'}),100); return; }
  document.getElementById('plans-section').scrollIntoView({behavior:'smooth'});
}
function scrollToFlow() {
  if (state.currentPage !== 'page-landing') { goToPage('page-landing'); setTimeout(()=>document.getElementById('flow-section').scrollIntoView({behavior:'smooth'}),100); return; }
  document.getElementById('flow-section').scrollIntoView({behavior:'smooth'});
}

function goToRegisterWithPlan(plan) {
  goToPage('page-register');
  setTimeout(() => { selectPlan(plan); if (state.currentStep === 1) {} }, 50);
}

// ════════════════════════════════════════
// SUBDOMAIN CHECK
// ════════════════════════════════════════
function checkSubdomain(val) {
  const ind = document.getElementById('avail-indicator');
  const dot = document.getElementById('avail-dot');
  const txt = document.getElementById('avail-text');
  clearTimeout(state.subdomainTimer);
  state.subdomainAvailable = null;
  if (!val) { ind.style.display='none'; return; }
  const clean = val.toLowerCase().replace(/[^a-z0-9]/g,'');
  document.getElementById('subdomain').value = clean;
  if (!clean) { ind.style.display='none'; return; }
  ind.style.display='flex';
  dot.className='avail-dot checking'; txt.textContent='Checking availability…';
  state.subdomainTimer = setTimeout(async () => {
    try {
        const res = await fetch(`/check-subdomain?subdomain=${clean}`);
        const data = await res.json();
        state.subdomainAvailable = data.available;
        dot.className = data.available ? 'avail-dot available' : 'avail-dot taken';
        txt.textContent = data.available
        ? `"${clean}.ekklesia.app" is available!`
        : `"${clean}.ekklesia.app" is already taken`;
    } catch (err) {
        dot.className = 'avail-dot available';
        state.subdomainAvailable = true;
        txt.textContent = 'Could not verify — proceeding anyway';
    }
    }, 900);

}

// ════════════════════════════════════════
// PLAN SELECTION
// ════════════════════════════════════════
function selectPlan(plan) {
  state.selectedPlan = plan;
  document.querySelectorAll('.plan-option').forEach(el=>el.classList.remove('selected'));
  document.getElementById('opt-'+plan).classList.add('selected');
  document.getElementById('err-plan').style.display='none';
}

// ════════════════════════════════════════
// STEP NAVIGATION
// ════════════════════════════════════════
function nextStep(from) {
  if (from === 1) {
    if (!validateStep1()) return;
  }
  if (from === 2) {
    if (!state.selectedPlan) { document.getElementById('err-plan').style.display='block'; return; }
    buildPaymentStep();
    buildConfirmStep();
  }
  if (from === 3) {
    const plan = PLANS[state.selectedPlan];
    if (plan.paid) {
      const ref = document.getElementById('payment-ref');
      if (!ref || !ref.value.trim()) {
        document.getElementById('err-ref').style.display='block'; return;
      }
    }
  }
  const next = from + 1;
  document.getElementById('step-'+from).classList.add('hidden');
  document.getElementById('step-'+next).classList.remove('hidden');
  state.currentStep = next;
  updatePips(next);
  window.scrollTo(0,0);
}

function prevStep(from) {
  const prev = from - 1;
  document.getElementById('step-'+from).classList.add('hidden');
  document.getElementById('step-'+prev).classList.remove('hidden');
  state.currentStep = prev;
  updatePips(prev);
}

function updatePips(active) {
  for (let i=1;i<=4;i++) {
    const pip = document.getElementById('pip-'+i);
    const circle = document.getElementById('pip-circle-'+i);
    pip.classList.remove('active','done');
    if (i < active) {
      pip.classList.add('done');
      circle.innerHTML = `<svg width="12" height="10" viewBox="0 0 12 10" fill="none"><path d="M1 5L4.5 8.5L11 1" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>`;
    } else if (i === active) {
      pip.classList.add('active');
      circle.textContent = i;
    } else {
      circle.textContent = i;
    }
  }
  for (let i=1;i<=3;i++) {
    const line = document.getElementById('line-'+i);
    line.className = 'step-line' + (i < active ? ' done' : '');
  }
}

// ════════════════════════════════════════
// VALIDATION
// ════════════════════════════════════════
function validateStep1() {
  let ok = true;
  const fields = [
    ['church-name','err-church-name'],
    ['church-address','err-church-address'],
    ['pastor-name','err-pastor-name'],
    ['subdomain','err-subdomain'],
  ];
  fields.forEach(([id,eid])=>{
    const val = document.getElementById(id).value.trim();
    const err = document.getElementById(eid);
    if (!val) { err.classList.add('show'); document.getElementById(id).classList.add('error'); ok=false; }
    else { err.classList.remove('show'); document.getElementById(id).classList.remove('error'); }
  });
  // email
  const email = document.getElementById('pastor-email').value.trim();
  const emailErr = document.getElementById('err-pastor-email');
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    emailErr.classList.add('show'); document.getElementById('pastor-email').classList.add('error'); ok=false;
  } else { emailErr.classList.remove('show'); document.getElementById('pastor-email').classList.remove('error'); }
  // subdomain availability
  if (state.subdomainAvailable === false) {
    document.getElementById('err-subdomain').textContent = 'That subdomain is already taken. Please choose another.';
    document.getElementById('err-subdomain').classList.add('show'); ok=false;
  }
  return ok;
}

// ════════════════════════════════════════
// PAYMENT STEP
// ════════════════════════════════════════
function buildPaymentStep() {
  const plan = PLANS[state.selectedPlan];
  const body = document.getElementById('payment-body');
  const title = document.getElementById('payment-step-title');
  const sub = document.getElementById('payment-step-sub');
  const btn = document.getElementById('payment-next-btn');

  if (!plan.paid) {
    title.textContent = 'No payment required';
    sub.textContent = 'The free tier starts immediately — no credit card needed.';
    btn.textContent = 'Continue →';
    const now = new Date();
    body.innerHTML = `
      <div style="text-align:center;padding:32px 0;">
        <div style="font-size:48px;margin-bottom:16px;">🎁</div>
        <div style="font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--ink);margin-bottom:8px;">Free tier activated</div>
        <div style="font-size:15px;color:var(--ink3);line-height:1.6;">Your platform will be created immediately after confirmation.<br>You can upgrade to a paid plan anytime from your dashboard.</div>
        <div style="margin-top:24px;background:var(--cream);border-radius:10px;padding:16px 20px;border:1px solid var(--border);text-align:left;font-size:13px;color:var(--ink2);line-height:1.6;">
          <strong>Registered on:</strong> ${now.toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}<br>
          <strong>Billing cycle:</strong> None — Free tier has no renewal date<br>
          <strong>Disciple limit:</strong> 20 &nbsp;·&nbsp; <strong>Leader limit:</strong> 5 &nbsp;·&nbsp; <strong>Programs:</strong> 1 (Life Class)
        </div>
      </div>`;
    return;
  }

  title.textContent = 'Payment confirmation';
  sub.textContent = 'Send payment and enter your GCash or bank reference number below.';
  btn.textContent = 'Confirm payment →';
  const now = new Date();
  const due = new Date(now); due.setDate(due.getDate()+30);
  body.innerHTML = `
    <div class="payment-summary">
      <div class="payment-summary-top">
        <div>
          <div class="payment-plan-name">${plan.name} subscription</div>
          <div class="payment-plan-price">${plan.priceStr}</div>
          <div class="payment-plan-sub">per month · billed monthly</div>
        </div>
        <div style="background:rgba(196,154,60,0.15);border-radius:8px;padding:6px 12px;font-size:12px;color:var(--gold);font-weight:600;">MONTHLY</div>
      </div>
      <div class="payment-due">
        <div class="payment-due-icon">📅</div>
        <div class="payment-due-text">
          Subscription starts today — <strong>${now.toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}</strong><br>
          Next renewal: <strong>${due.toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}</strong>
        </div>
      </div>
    </div>
    <div class="gcash-note">
      <div class="gcash-note-icon">ℹ️</div>
      <div class="gcash-note-text">Send <strong>${plan.priceStr}</strong> to GCash number <strong>09XX-XXX-XXXX</strong> or BDO account <strong>1234-5678-90</strong>. Enter your reference number below to confirm.</div>
    </div>
    <div class="form-group">
      <label for="payment-ref">GCash / bank reference number</label>
      <input type="text" id="payment-ref" placeholder="e.g. GCash ref: 1234567890" oninput="document.getElementById('err-ref').style.display='none'">
      <div class="field-error" id="err-ref">Please enter your payment reference number.</div>
    </div>`;
}

// ════════════════════════════════════════
// CONFIRM STEP
// ════════════════════════════════════════
function buildConfirmStep() {
  const plan = PLANS[state.selectedPlan];
  const churchName = document.getElementById('church-name').value.trim();
  const churchAddr = document.getElementById('church-address').value.trim();
  const pastorName = document.getElementById('pastor-name').value.trim();
  const pastorEmail = document.getElementById('pastor-email').value.trim();
  const subdomain = document.getElementById('subdomain').value.trim();
  const now = new Date();
  const due = new Date(now); due.setDate(due.getDate()+30);

  const rows = [
    ['Church name', churchName],
    ['Church address', churchAddr],
    ['Pastor name', pastorName],
    ['Pastor email', pastorEmail],
    ['Subdomain', subdomain+'.ekklesia.app'],
    ['Plan', plan.name],
    ['Monthly cost', plan.priceStr],
    ['Platform DB', 'tenant_'+subdomain],
    ['Subscription start', plan.paid ? now.toLocaleDateString('en-PH') : 'N/A'],
    ['Next renewal', plan.paid ? due.toLocaleDateString('en-PH') : 'No billing'],
  ];

  document.getElementById('confirm-body').innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px;">
      ${rows.map(([label,val])=>`
        <div style="background:var(--cream);border-radius:10px;padding:14px 16px;border:1px solid var(--border);">
          <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink3);margin-bottom:4px;">${label}</div>
          <div style="font-size:13px;font-weight:600;color:var(--ink);font-family:var(--font-mono);word-break:break-all;">${val}</div>
        </div>`).join('')}
    </div>
    <div style="background:var(--gold-pale);border:1px solid rgba(196,154,60,0.3);border-radius:10px;padding:14px 16px;font-size:13px;color:var(--ink2);line-height:1.6;">
      ✓ &nbsp;By confirming, a dedicated database <strong>tenant_${subdomain}</strong> will be created and <strong>${subdomain}.ekklesia.app</strong> will go live immediately.
    </div>`;
}

// ════════════════════════════════════════
// SUBMIT — start provisioning
// ════════════════════════════════════════
async function submitRegistration() {
  const payload = {
    church_name:    document.getElementById('church-name').value.trim(),
    church_address: document.getElementById('church-address').value.trim(),
    pastor_name:    document.getElementById('pastor-name').value.trim(),
    pastor_email:   document.getElementById('pastor-email').value.trim(),
    subdomain:      document.getElementById('subdomain').value.trim(),
    plan:           state.selectedPlan,
  };

  goToPage('page-provision');

  try {
    const res = await fetch('/register-tenant', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(payload),
    });

    const data = await res.json();
    if (data.success) {
      runProvisioning();
    } else {
      alert('Registration failed: ' + (data.message || 'Please try again.'));
      goToPage('page-register');
    }
  } catch (err) {
    alert('Something went wrong. Please try again.');
    goToPage('page-register');
  }
}

// ════════════════════════════════════════
// PROVISIONING SIMULATION
// ════════════════════════════════════════
function runProvisioning() {
  const subdomain = document.getElementById('subdomain').value.trim();
  const plan = PLANS[state.selectedPlan];
  const dbName = 'tenant_' + subdomain;

  const terminalLines = [
    { delay: 200,  type:'comment', text:'# Ekklesia TenantProvisioningService' },
    { delay: 600,  type:'cmd',     text:`php artisan tenant:create --subdomain=${subdomain}` },
    { delay: 1200, type:'info',    text:`→ Validating subdomain "${subdomain}"... OK` },
    { delay: 1700, type:'cmd',     text:`mysql -e "CREATE DATABASE ${dbName} CHARACTER SET utf8mb4;"` },
    { delay: 2400, type:'success', text:`✓ Database "${dbName}" created` },
    { delay: 2900, type:'cmd',     text:`php artisan migrate --database=tenant --path=database/migrations/tenant` },
    { delay: 3300, type:'info',    text:'  Migrating: 2024_01_01_create_programs_table' },
    { delay: 3600, type:'info',    text:'  Migrating: 2024_01_02_create_leaders_table' },
    { delay: 3900, type:'info',    text:'  Migrating: 2024_01_03_create_disciples_table' },
    { delay: 4200, type:'info',    text:'  Migrating: 2024_01_04_create_lessons_table' },
    { delay: 4500, type:'info',    text:'  Migrating: 2024_01_05_create_attendance_table' },
    { delay: 4700, type:'info',    text:'  Migrating: 2024_01_06_create_devotionals_table' },
    { delay: 4900, type:'info',    text:'  Migrating: 2024_01_07_create_progress_table' },
    { delay: 5200, type:'success', text:'✓ All migrations ran successfully' },
    { delay: 5600, type:'cmd',     text:`php artisan db:seed --class=TenantSeeder --plan=${state.selectedPlan}` },
    { delay: 6100, type:'info',    text:`  Seeding plan config: ${plan.name}` },
    { delay: 6400, type:'info',    text:'  Seeding default programs...' },
    { delay: 6800, type:'success', text:'✓ Tenant seeded successfully' },
    { delay: 7100, type:'cmd',     text:`php artisan tenant:register-subdomain ${subdomain}` },
    { delay: 7600, type:'info',    text:`  Wildcard DNS: *.ekklesia.app → 103.x.x.x` },
    { delay: 7900, type:'success', text:`✓ Subdomain ${subdomain}.ekklesia.app is live` },
    { delay: 8200, type:'cmd',     text:'php artisan mail:send --type=TenantWelcomeMail' },
    { delay: 8600, type:'success', text:`✓ Welcome email sent to ${document.getElementById('pastor-email').value.trim()}` },
    { delay: 9000, type:'success', text:'✓ Provisioning complete!' },
  ];

  const provSteps = [
    { delay: 1700,  doneDelay: 2400,  step: 1, label: dbName },
    { delay: 2900,  doneDelay: 5200,  step: 2, label: 'Running 7 migrations' },
    { delay: 5600,  doneDelay: 6800,  step: 3, label: `Plan: ${plan.name}` },
    { delay: 7100,  doneDelay: 7900,  step: 4, label: `${subdomain}.ekklesia.app` },
    { delay: 8200,  doneDelay: 8600,  step: 5, label: document.getElementById('pastor-email').value.trim() },
  ];

  const body = document.getElementById('terminal-body');
  body.innerHTML = '';

  terminalLines.forEach(({delay, type, text})=>{
    setTimeout(()=>{
      const line = document.createElement('div');
      line.className = 't-line';
      if (type==='cmd') {
        line.innerHTML = `<span class="t-prompt">$</span><span class="t-cmd">${text}</span>`;
      } else if (type==='comment') {
        line.innerHTML = `<span class="t-comment">${text}</span>`;
      } else if (type==='success') {
        line.innerHTML = `<span class="t-success">${text}</span>`;
      } else {
        line.innerHTML = `<span class="t-info">${text}</span>`;
      }
      body.appendChild(line);
      requestAnimationFrame(()=>{ line.classList.add('show'); body.scrollTop = body.scrollHeight; });
    }, delay);
  });

  provSteps.forEach(({delay, doneDelay, step, label})=>{
    setTimeout(()=>{
      const el = document.getElementById('pstep-'+step);
      const stat = document.getElementById('pstatus-'+step);
      const sublabel = document.getElementById('pslabel-'+step);
      el.classList.add('running');
      stat.className = 'prov-status running';
      stat.innerHTML = `<span class="spinner"></span>`;
      sublabel.textContent = label;
    }, delay);
    setTimeout(()=>{
      const el = document.getElementById('pstep-'+step);
      const stat = document.getElementById('pstatus-'+step);
      el.classList.remove('running'); el.classList.add('done');
      stat.className = 'prov-status done';
      stat.textContent = '✓ done';
    }, doneDelay);
  });

  // Show success
  setTimeout(()=>{
    document.getElementById('prov-title').textContent = 'Platform ready!';
    document.getElementById('prov-sub').textContent = 'Your church platform is live and accessible.';
    const card = document.getElementById('success-card');
    const now = new Date();
    const due = new Date(now); due.setDate(due.getDate()+30);
    document.getElementById('success-url-text').textContent = subdomain+'.ekklesia.app';
    document.getElementById('success-detail').innerHTML = `
      <div class="success-detail-item"><div class="success-detail-label">Database</div><div class="success-detail-value">${dbName}</div></div>
      <div class="success-detail-item"><div class="success-detail-label">Plan</div><div class="success-detail-value">${plan.name}</div></div>
      <div class="success-detail-item"><div class="success-detail-label">Activated on</div><div class="success-detail-value">${now.toLocaleDateString('en-PH')}</div></div>
      <div class="success-detail-item"><div class="success-detail-label">Next renewal</div><div class="success-detail-value">${plan.paid ? due.toLocaleDateString('en-PH') : 'N/A'}</div></div>`;
    card.classList.add('show');
  }, 9600);
}

// ════════════════════════════════════════
// RESET
// ════════════════════════════════════════
function resetAll() {
  state.selectedPlan = null;
  state.currentStep = 1;
  state.subdomainAvailable = null;
  document.querySelectorAll('input[type=text],input[type=email]').forEach(i=>i.value='');
  document.querySelectorAll('.plan-option').forEach(el=>el.classList.remove('selected'));
  document.getElementById('avail-indicator').style.display='none';
  for(let i=1;i<=4;i++){
    document.getElementById('step-'+i).classList.toggle('hidden', i!==1);
  }
  document.getElementById('success-card').classList.remove('show');
  document.getElementById('terminal-body').innerHTML='';
  document.querySelectorAll('.prov-step').forEach(el=>{el.classList.remove('running','done');});
  for(let i=1;i<=5;i++){
    document.getElementById('pstatus-'+i).className='prov-status pending';
    document.getElementById('pstatus-'+i).textContent='pending';
    document.getElementById('pslabel-'+i).textContent='Waiting...';
  }
  updatePips(1);
  goToPage('page-register');
}

// Pre-select standard on load
window.addEventListener('DOMContentLoaded', ()=>{ });
</script>
</body>
</html>