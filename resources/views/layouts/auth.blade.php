<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Discipleship Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root{
            --ink:#1a1209;--warm-900:#2d1f0e;--amber:#c9831f;--gold:#e9a83a;
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
        /* Left panel */
        .auth-panel-left{
            width:420px;flex-shrink:0;
            background:var(--warm-900);
            position:relative;overflow:hidden;
            display:flex;flex-direction:column;
            justify-content:space-between;
            padding:48px 44px;
        }
        .auth-panel-left::before{
            content:'';position:absolute;inset:0;
            background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .auth-panel-left::after{
            content:'';position:absolute;
            width:500px;height:500px;border-radius:50%;
            background:radial-gradient(circle,rgba(201,131,31,.18) 0%,transparent 70%);
            bottom:-150px;left:-100px;
        }
        .apl-brand{position:relative;z-index:1;}
        .apl-logo{
            display:flex;align-items:center;gap:12px;margin-bottom:48px;
        }
        .apl-logo-icon{
            width:44px;height:44px;border-radius:12px;
            background:linear-gradient(135deg,var(--gold),var(--amber));
            display:flex;align-items:center;justify-content:center;
            font-size:20px;color:var(--warm-900);
        }
        .apl-logo-text{
            font-family:'Cormorant Garamond',serif;
            font-size:22px;font-weight:700;color:#fff;
        }
        .apl-logo-sub{font-size:11px;color:rgba(255,255,255,.5);letter-spacing:.06em;}
        .apl-headline{
            font-family:'Cormorant Garamond',serif;
            font-size:36px;font-weight:600;color:#fff;
            line-height:1.2;margin-bottom:16px;
        }
        .apl-headline em{color:var(--gold-lt,#f5c96a);font-style:italic;}
        .apl-desc{
            font-size:14px;color:rgba(255,255,255,.6);
            line-height:1.7;max-width:300px;
        }
        .apl-features{
            position:relative;z-index:1;margin-top:40px;
        }
        .apl-feature{
            display:flex;align-items:center;gap:12px;
            margin-bottom:14px;
        }
        .apl-feature-icon{
            width:30px;height:30px;border-radius:8px;
            background:rgba(255,255,255,.08);
            display:flex;align-items:center;justify-content:center;
            font-size:13px;color:var(--gold);flex-shrink:0;
        }
        .apl-feature-text{font-size:13px;color:rgba(255,255,255,.7);}
        .apl-footer{
            position:relative;z-index:1;
            font-size:11.5px;color:rgba(255,255,255,.3);
        }
        /* Right panel */
        .auth-panel-right{
            flex:1;display:flex;align-items:center;justify-content:center;
            padding:40px 24px;
            background:var(--paper);
        }
        .auth-box{width:100%;max-width:420px;}
        .auth-title{
            font-family:'Cormorant Garamond',serif;
            font-size:28px;font-weight:600;color:var(--warm-900);
            margin-bottom:6px;
        }
        .auth-subtitle{font-size:13.5px;color:var(--muted);margin-bottom:30px;}
        .auth-subtitle a{color:var(--amber);text-decoration:none;}
        .auth-subtitle a:hover{text-decoration:underline;}
        /* Form */
        .form-group{margin-bottom:16px;}
        .form-label{
            display:block;font-size:12px;font-weight:600;
            color:var(--warm-800,#3d2c12);margin-bottom:6px;
        }
        .input-wrap{position:relative;}
        .input-icon{
            position:absolute;left:13px;top:50%;transform:translateY(-50%);
            color:var(--muted);font-size:13px;pointer-events:none;
        }
        .form-control{
            width:100%;padding:10px 13px 10px 38px;
            background:var(--cream);border:1px solid var(--border);
            border-radius:10px;font-size:13.5px;font-family:'DM Sans',sans-serif;
            color:var(--ink);outline:none;transition:border-color .18s,box-shadow .18s;
        }
        .form-control:focus{
            border-color:var(--amber);background:var(--paper);
            box-shadow:0 0 0 3px rgba(201,131,31,.12);
        }
        .form-control.no-icon{padding-left:13px;}
        .form-control::placeholder{color:var(--muted);}
        .btn-submit{
            width:100%;padding:12px;border-radius:10px;border:none;
            background:linear-gradient(135deg,var(--amber),#a06516);
            color:#fff;font-size:14px;font-weight:600;
            font-family:'DM Sans',sans-serif;cursor:pointer;
            transition:all .2s;margin-top:8px;
            box-shadow:0 3px 12px rgba(201,131,31,.35);
        }
        .btn-submit:hover{
            background:linear-gradient(135deg,var(--gold),var(--amber));
            box-shadow:0 5px 18px rgba(201,131,31,.45);transform:translateY(-1px);
        }
        .divider{
            display:flex;align-items:center;gap:12px;
            margin:20px 0;color:var(--muted);font-size:12px;
        }
        .divider::before,.divider::after{
            content:'';flex:1;height:1px;background:var(--border);
        }
        .form-check{
            display:flex;align-items:center;gap:8px;
            font-size:12.5px;color:var(--muted);
        }
        .form-check input{accent-color:var(--amber);}
        .auth-links{
            text-align:center;margin-top:20px;
            font-size:13px;color:var(--muted);
        }
        .auth-links a{color:var(--amber);text-decoration:none;font-weight:500;}
        .alert-error{
            background:rgba(184,90,58,.1);color:var(--terracotta);
            border:1px solid rgba(184,90,58,.2);
            border-radius:10px;padding:11px 14px;
            font-size:13px;margin-bottom:16px;
            display:flex;gap:8px;align-items:flex-start;
        }
        /* Plan cards */
        .plan-cards{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:20px;}
        .plan-card{
            border:2px solid var(--border);border-radius:12px;
            padding:14px;cursor:pointer;transition:all .18s;
            position:relative;
        }
        .plan-card:hover{border-color:var(--amber);}
        .plan-card.selected{border-color:var(--amber);background:rgba(201,131,31,.05);}
        .plan-card input[type=radio]{position:absolute;opacity:0;}
        .plan-card-name{font-weight:600;font-size:13.5px;color:var(--warm-900);}
        .plan-card-price{font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;color:var(--amber);}
        .plan-card-desc{font-size:11px;color:var(--muted);margin-top:4px;}
        @media(max-width:768px){
            .auth-panel-left{display:none;}
            .auth-panel-right{padding:24px 20px;}
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
        <div class="apl-features" style="margin-top:32px;">
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
