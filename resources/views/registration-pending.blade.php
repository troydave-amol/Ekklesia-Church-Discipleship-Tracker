<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration received — Ekklesia</title>
    @vite(['resources/css/app.css', 'resources/css/base.css', 'resources/css/landing.css', 'resources/css/pending.css'])
</head>
<body>

<nav>
  <div class="nav-logo" style="cursor:pointer" onclick="window.location.href='/'">Ek<span>k</span>lesia</div>
  <div class="nav-links">
    <a href="/#plans-section">Pricing</a>
    <a href="/#flow-section">How it works</a>
    <button class="btn-nav" onclick="window.location.href='/'">Home</button>
  </div>
</nav>

<div class="pending-page">
  <div class="pending-wrap">

    {{-- Animated pending icon --}}
    <div class="pending-icon-wrap">
      <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="4" width="20" height="16" rx="2"/>
        <path d="M22 7l-10 7L2 7"/>
      </svg>
    </div>

    <div class="pending-card">
      <div class="pending-card-body">

        <h1 class="pending-card-title">Registration received</h1>
        <p class="pending-card-desc">
          Thank you for registering your church. The <strong>central app</strong> team will review your request before creating your subdomain and database. You will receive an email when your workspace is ready.
        </p>

        {{-- What happens next timeline --}}
        <div class="pending-timeline">
          <div class="pending-timeline-title">What happens next</div>

          <div class="pending-step">
            <div class="pending-step-dot done">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
            <div class="pending-step-info">
              <div class="pending-step-label">Registration submitted</div>
              <div class="pending-step-sub">Your church info and plan selection have been recorded.</div>
            </div>
          </div>

          <div class="pending-step is-active">
            <div class="pending-step-dot active">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
            </div>
            <div class="pending-step-info">
              <div class="pending-step-label">Under review</div>
              <div class="pending-step-sub">An administrator is reviewing your registration request.</div>
            </div>
          </div>

          <div class="pending-step is-upcoming">
            <div class="pending-step-dot upcoming">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>
              </svg>
            </div>
            <div class="pending-step-info">
              <div class="pending-step-label">Platform goes live</div>
              <div class="pending-step-sub">Your subdomain and database will be provisioned automatically.</div>
            </div>
          </div>
        </div>

        {{-- Billing notice --}}
        <div class="pending-notice">
          <div class="pending-notice-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="16" x2="12" y2="12"/>
              <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
          </div>
          <div class="pending-notice-text">
            <strong>Billing notice —</strong> Monthly plans (Free, Basic ₱800, Standard ₱1,500, Premium ₱2,500) are billed per church after approval, according to the plan you selected.
          </div>
        </div>

      </div>

      <div class="pending-card-footer">
        <a href="{{ route('home') }}" class="btn-primary" style="text-decoration:none;">
          Back to home
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>

  </div>

  <footer class="footer">
    <div class="footer-logo">Ek<span>k</span>lesia</div>
    <div class="footer-copy">© 2025 Ekklesia. All rights reserved.</div>
  </footer>
</div>

</body>
</html>
