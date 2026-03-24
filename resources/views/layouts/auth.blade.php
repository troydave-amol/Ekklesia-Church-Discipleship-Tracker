<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Discipleship Tracker</title>
    <meta name="description" content="Discipleship Tracker — A complete platform for churches to manage destiny training, track attendance, and monitor spiritual growth.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root{
            --ink:#1a1209;--warm-900:#2d1f0e;--warm-800:#3d2c12;--warm-700:#4d3a1a;
            --amber:#c9831f;--gold:#e9a83a;--gold-lt:#f5c96a;
            --cream:#fdf6e8;--parchment:#f5ead0;--paper:#fffdf7;
            --sage:#5a7a5f;--terracotta:#b85a3a;--muted:#8a7560;
            --border:#e2d4b8;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%}
        body{
            font-family:'DM Sans',sans-serif;
            background:var(--cream);
            color:var(--ink);
            min-height:100vh;
            display:flex;
        }

        /* ─── Left panel ─── */
        .auth-panel-left{
            width:420px;flex-shrink:0;
            background:linear-gradient(175deg, #3a2814 0%, var(--warm-900) 40%, #1a0f05 100%);
            position:relative;overflow:hidden;
            display:flex;flex-direction:column;
            justify-content:space-between;
            padding:48px 44px;
        }
        .auth-panel-left::before{
            content:'';position:absolute;inset:0;
            background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.025'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index:0;
        }
        .auth-panel-left::after{
            content:'';position:absolute;
            width:550px;height:550px;border-radius:50%;
            background:radial-gradient(circle,rgba(201,131,31,.15) 0%,rgba(201,131,31,.05) 40%,transparent 70%);
            bottom:-180px;left:-120px;
            z-index:0;
        }
        .apl-brand{position:relative;z-index:1;}

        /* Logo */
        .apl-logo{
            display:flex;align-items:center;gap:14px;margin-bottom:52px;
        }
        .apl-logo-icon{
            width:46px;height:46px;border-radius:12px;
            background:linear-gradient(135deg,var(--gold),var(--amber));
            display:flex;align-items:center;justify-content:center;
            font-size:20px;color:var(--warm-900);
            box-shadow:0 4px 16px rgba(201,131,31,.3);
            transition:transform .3s ease,box-shadow .3s ease;
        }
        .apl-logo-icon:hover{
            transform:scale(1.05) rotate(-3deg);
            box-shadow:0 6px 24px rgba(201,131,31,.4);
        }
        .apl-logo-text{
            font-family:'Cormorant Garamond',serif;
            font-size:22px;font-weight:700;color:#fff;
            line-height:1.2;
        }
        .apl-logo-sub{
            font-size:10px;color:rgba(255,255,255,.45);
            letter-spacing:.1em;text-transform:uppercase;
            margin-top:2px;
        }

        /* Headline */
        .apl-headline{
            font-family:'Cormorant Garamond',serif;
            font-size:38px;font-weight:600;color:#fff;
            line-height:1.2;margin-bottom:18px;
        }
        .apl-headline em{
            color:var(--gold-lt);font-style:italic;
            text-shadow:0 0 40px rgba(233,168,58,.2);
        }
        .apl-desc{
            font-size:14px;color:rgba(255,255,255,.55);
            line-height:1.75;max-width:300px;
        }

        /* Features */
        .apl-features{
            position:relative;z-index:1;margin-top:40px;
        }
        .apl-feature{
            display:flex;align-items:center;gap:14px;
            margin-bottom:16px;
            opacity:0;animation:fadeSlideIn .5s ease forwards;
        }
        .apl-feature:nth-child(1){animation-delay:.1s}
        .apl-feature:nth-child(2){animation-delay:.2s}
        .apl-feature:nth-child(3){animation-delay:.3s}
        .apl-feature:nth-child(4){animation-delay:.4s}
        .apl-feature-icon{
            width:32px;height:32px;border-radius:8px;
            background:rgba(255,255,255,.07);
            border:1px solid rgba(255,255,255,.06);
            display:flex;align-items:center;justify-content:center;
            font-size:13px;color:var(--gold);flex-shrink:0;
            transition:background .2s,transform .2s;
        }
        .apl-feature:hover .apl-feature-icon{
            background:rgba(201,131,31,.15);
            transform:scale(1.08);
        }
        .apl-feature-text{
            font-size:13.5px;color:rgba(255,255,255,.65);
            transition:color .2s;
        }
        .apl-feature:hover .apl-feature-text{
            color:rgba(255,255,255,.85);
        }

        /* Footer */
        .apl-footer{
            position:relative;z-index:1;
            font-size:11px;color:rgba(255,255,255,.25);
            letter-spacing:.02em;
        }

        /* ─── Right panel ─── */
        .auth-panel-right{
            flex:1;display:flex;align-items:center;justify-content:center;
            padding:40px 24px;
            background:var(--paper);
            position:relative;
        }
        .auth-panel-right::before{
            content:'';position:absolute;inset:0;
            background:radial-gradient(ellipse at 30% 20%, rgba(201,131,31,.04) 0%, transparent 60%);
            pointer-events:none;
        }
        .auth-box{
            width:100%;max-width:430px;
            position:relative;z-index:1;
            animation:fadeUp .6s ease;
        }

        /* Title */
        .auth-title{
            font-family:'Cormorant Garamond',serif;
            font-size:30px;font-weight:600;color:var(--warm-900);
            margin-bottom:6px;
            letter-spacing:-.01em;
        }
        .auth-subtitle{
            font-size:13.5px;color:var(--muted);margin-bottom:28px;
            line-height:1.5;
        }
        .auth-subtitle a{
            color:var(--amber);text-decoration:none;
            font-weight:500;transition:color .2s;
        }
        .auth-subtitle a:hover{color:var(--gold);text-decoration:underline;}

        /* ─── Form Elements ─── */
        .form-group{margin-bottom:16px;}
        .form-label{
            display:block;font-size:11.5px;font-weight:600;
            color:var(--warm-800);margin-bottom:6px;
            letter-spacing:.03em;
        }
        .input-wrap{position:relative;}
        .input-icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            color:var(--muted);font-size:13px;pointer-events:none;
            transition:color .2s;
        }
        .input-wrap:focus-within .input-icon{
            color:var(--amber);
        }
        .form-control{
            width:100%;padding:11px 14px 11px 40px;
            background:var(--cream);border:1.5px solid var(--border);
            border-radius:10px;font-size:13.5px;font-family:'DM Sans',sans-serif;
            color:var(--ink);outline:none;
            transition:border-color .2s ease,box-shadow .2s ease,background .2s ease;
        }
        .form-control:hover{
            border-color:rgba(201,131,31,.4);
        }
        .form-control:focus{
            border-color:var(--amber);background:var(--paper);
            box-shadow:0 0 0 3px rgba(201,131,31,.1),0 2px 8px rgba(201,131,31,.08);
        }
        .form-control.no-icon{padding-left:14px;}
        .form-control::placeholder{color:var(--muted);opacity:.7;}

        /* Password toggle */
        .password-toggle{
            position:absolute;right:14px;top:50%;transform:translateY(-50%);
            background:none;border:none;cursor:pointer;
            color:var(--muted);font-size:14px;padding:2px;
            transition:color .2s;
        }
        .password-toggle:hover{color:var(--amber);}

        /* Submit button */
        .btn-submit{
            width:100%;padding:13px;border-radius:10px;border:none;
            background:linear-gradient(135deg,var(--amber),#a06516);
            color:#fff;font-size:14px;font-weight:600;
            font-family:'DM Sans',sans-serif;cursor:pointer;
            transition:all .25s ease;margin-top:8px;
            box-shadow:0 4px 16px rgba(201,131,31,.3);
            position:relative;overflow:hidden;
        }
        .btn-submit::before{
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(255,255,255,.15),transparent);
            opacity:0;transition:opacity .3s;
        }
        .btn-submit:hover{
            background:linear-gradient(135deg,var(--gold),var(--amber));
            box-shadow:0 6px 24px rgba(201,131,31,.4);
            transform:translateY(-2px);
        }
        .btn-submit:hover::before{opacity:1;}
        .btn-submit:active{
            transform:translateY(0);
            box-shadow:0 2px 8px rgba(201,131,31,.3);
        }

        /* Divider */
        .divider{
            display:flex;align-items:center;gap:12px;
            margin:22px 0;color:var(--muted);font-size:11.5px;
            letter-spacing:.03em;
        }
        .divider::before,.divider::after{
            content:'';flex:1;height:1px;
            background:linear-gradient(90deg,transparent,var(--border),transparent);
        }

        /* Checkbox */
        .form-check{
            display:flex;align-items:center;gap:8px;
            font-size:12.5px;color:var(--muted);
            cursor:pointer;transition:color .2s;
        }
        .form-check:hover{color:var(--warm-800);}
        .form-check input{
            accent-color:var(--amber);
            width:15px;height:15px;cursor:pointer;
        }

        /* Auth links */
        .auth-links{
            text-align:center;margin-top:22px;
            font-size:13px;color:var(--muted);
        }
        .auth-links a{
            color:var(--amber);text-decoration:none;
            font-weight:500;transition:color .2s;
        }
        .auth-links a:hover{color:var(--gold);}

        /* Alerts */
        .alert-error{
            background:rgba(184,90,58,.08);color:var(--terracotta);
            border:1px solid rgba(184,90,58,.18);
            border-radius:10px;padding:12px 14px;
            font-size:13px;margin-bottom:16px;
            display:flex;gap:10px;align-items:flex-start;
            animation:fadeUp .3s ease;
        }
        .alert-error i{margin-top:1px;flex-shrink:0;}

        /* ─── Plan Cards ─── */
        .plan-cards{
            display:grid;grid-template-columns:repeat(2,1fr);
            gap:12px;margin-bottom:20px;
        }
        .plan-card{
            border:2px solid var(--border);border-radius:12px;
            padding:14px 16px;cursor:pointer;
            transition:all .22s ease;
            position:relative;
            background:transparent;
        }
        .plan-card:hover{
            border-color:rgba(201,131,31,.5);
            background:rgba(201,131,31,.03);
            transform:translateY(-1px);
            box-shadow:0 4px 12px rgba(201,131,31,.08);
        }
        .plan-card.selected{
            border-color:var(--amber);
            background:rgba(201,131,31,.06);
            box-shadow:0 2px 12px rgba(201,131,31,.12);
        }
        .plan-card.selected::after{
            content:'\f00c';font-family:'Font Awesome 6 Free';
            font-weight:900;font-size:10px;
            position:absolute;top:8px;right:8px;
            width:18px;height:18px;border-radius:50%;
            background:var(--amber);color:#fff;
            display:flex;align-items:center;justify-content:center;
        }
        .plan-card input[type=radio]{position:absolute;opacity:0;}
        .plan-card-name{font-weight:600;font-size:13.5px;color:var(--warm-900);}
        .plan-card-price{
            font-family:'Cormorant Garamond',serif;
            font-size:19px;font-weight:700;color:var(--amber);
            margin-top:2px;
        }
        .plan-card-desc{font-size:11px;color:var(--muted);margin-top:4px;}

        /* ─── Church info section ─── */
        .church-info-section{
            background:rgba(201,131,31,.05);
            border:1.5px solid rgba(201,131,31,.18);
            border-radius:12px;padding:16px 18px;
            margin-bottom:20px;
        }
        .church-info-header{
            font-size:11px;font-weight:700;
            letter-spacing:.08em;text-transform:uppercase;
            color:var(--amber);margin-bottom:14px;
            display:flex;align-items:center;gap:8px;
        }
        .church-info-header i{font-size:12px;}

        /* ─── Animations ─── */
        @keyframes fadeUp{
            from{opacity:0;transform:translateY(16px)}
            to{opacity:1;transform:translateY(0)}
        }
        @keyframes fadeSlideIn{
            from{opacity:0;transform:translateX(-12px)}
            to{opacity:1;transform:translateX(0)}
        }

        /* ─── Responsive ─── */
        @media(max-width:768px){
            .auth-panel-left{display:none;}
            .auth-panel-right{padding:32px 20px;align-items:flex-start;padding-top:48px;}
            .auth-box{max-width:100%;}
        }
        @media(max-width:480px){
            .plan-cards{grid-template-columns:1fr;}
            .auth-title{font-size:26px;}
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="auth-panel-left">
    <div class="apl-brand">
        <div class="apl-logo">
            <div class="apl-logo-icon"><i class="fas fa-seedling"></i></div>
            <div>
                <div class="apl-logo-text">Discipleship Tracker</div>
                <div class="apl-logo-sub">MINISTRY MANAGEMENT PLATFORM</div>
            </div>
        </div>
        <h2 class="apl-headline">Nurture every <em>disciple</em> in your flock</h2>
        <p class="apl-desc">A complete platform for churches to manage destiny training, track attendance, and monitor spiritual growth.</p>
        <div class="apl-features" style="margin-top:36px;">
            <div class="apl-feature">
                <div class="apl-feature-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="apl-feature-text">Life Class & School of Leaders programs</div>
            </div>
            <div class="apl-feature">
                <div class="apl-feature-icon"><i class="fas fa-clipboard-check"></i></div>
                <div class="apl-feature-text">Attendance & devotional tracking</div>
            </div>
            <div class="apl-feature">
                <div class="apl-feature-icon"><i class="fas fa-chart-line"></i></div>
                <div class="apl-feature-text">Pastor & leader dashboards</div>
            </div>
            <div class="apl-feature">
                <div class="apl-feature-icon"><i class="fas fa-users"></i></div>
                <div class="apl-feature-text">Multi-role, multi-church support</div>
            </div>
        </div>
    </div>
    <div class="apl-footer">© {{ date('Y') }} Discipleship Tracker. All rights reserved.</div>
</div>
<div class="auth-panel-right">
    <div class="auth-box">
        @yield('content')
    </div>
</div>
@stack('scripts')
</body>
</html>
