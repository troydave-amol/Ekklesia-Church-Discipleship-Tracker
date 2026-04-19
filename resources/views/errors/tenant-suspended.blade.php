<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:           #0D1117;
            --surface:      #161B22;
            --border:       #30363D;
            --border-light: #21262D;
            --gold:         #D4A853;
            --gold-light:   #E8C47A;
            --gold-dim:     #8B6914;
            --text:         #E6EDF3;
            --text-muted:   #8B949E;
            --text-dim:     #6E7681;
            --red:          #F85149;
            --red-dim:      rgba(248,81,73,.08);
            --radius-lg:    14px;
        }

        html, body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .4; }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 12px rgba(248,81,73,.2); }
            50%      { box-shadow: 0 0 28px rgba(248,81,73,.45); }
        }

        .suspended-shell {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .suspended-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            max-width: 480px;
            width: 100%;
            text-align: center;
            padding: 48px 40px 40px;
            animation: fadeUp .5s cubic-bezier(.16,1,.3,1) both;
            position: relative;
            overflow: hidden;
        }

        /* Red accent line at top */
        .suspended-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--red), transparent);
        }

        .suspended-icon {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: var(--red-dim);
            border: 1px solid rgba(248,81,73,.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: glow 3s ease-in-out infinite;
        }
        .suspended-icon svg {
            color: var(--red);
        }

        .suspended-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
        }

        .suspended-desc {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: 28px;
        }

        .suspended-church {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(248,81,73,.06);
            border: 1px solid rgba(248,81,73,.2);
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            color: var(--red);
            font-weight: 600;
            margin-bottom: 28px;
        }
        .suspended-church .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--red);
            animation: pulse 2s ease-in-out infinite;
        }

        .suspended-divider {
            height: 1px;
            background: var(--border);
            margin: 0 0 20px;
        }

        .suspended-help {
            font-size: 12px;
            color: var(--text-dim);
        }
        .suspended-help a {
            color: var(--gold);
            font-weight: 500;
            text-decoration: none;
        }
        .suspended-help a:hover {
            text-decoration: underline;
            color: var(--gold-light);
        }
    </style>
</head>
<body>
    <div class="suspended-shell">
        <div class="suspended-card">
            {{-- Shield / lock icon --}}
            <div class="suspended-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                    <circle cx="12" cy="16" r="1"/>
                </svg>
            </div>

            <h1 class="suspended-title">Account Suspended</h1>

            <p class="suspended-desc">
                This church workspace has been temporarily suspended by the platform administrator.
                All data remains safe and will be fully restored once the account is reactivated.
            </p>

            <div class="suspended-church">
                <span class="dot"></span>
                {{ $tenant->name ?? 'Church Workspace' }}
            </div>

            <div class="suspended-divider"></div>

            <p class="suspended-help">
                If you believe this is a mistake, please contact your platform administrator<br>
                or reach out to <a href="mailto:support@ekklesia.app">support@ekklesia.app</a>
            </p>
        </div>
    </div>
</body>
</html>
