<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password — Student Portal</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .sl-page .sl-input:-webkit-autofill,
    .sl-page .sl-input:-webkit-autofill:hover,
    .sl-page .sl-input:-webkit-autofill:focus {
      -webkit-box-shadow: 0 0 0 40px #071f13 inset !important;
      -webkit-text-fill-color: #fff !important;
      caret-color: #fff;
      transition: background-color 5000s ease-in-out 0s;
      border-color: rgba(16,185,129,.2) !important;
    }

    .sl-page {
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      background: #050f09;
      position: relative; overflow: hidden;
    }
    .sl-bg { position: absolute; inset: 0; pointer-events: none; }
    .sl-bg::before {
      content: '';
      position: absolute; top: -15%; left: -8%;
      width: 55vw; height: 55vw;
      background: radial-gradient(circle, rgba(16,185,129,.22) 0%, transparent 65%);
      border-radius: 50%;
    }
    .sl-bg::after {
      content: '';
      position: absolute; bottom: -20%; right: -8%;
      width: 48vw; height: 48vw;
      background: radial-gradient(circle, rgba(5,150,105,.16) 0%, transparent 65%);
      border-radius: 50%;
    }

    .sl-card {
      width: 100%; max-width: 430px;
      background: rgba(6,22,13,.82);
      border: 1px solid rgba(16,185,129,.18);
      border-radius: 20px;
      padding: 38px 36px;
      position: relative; z-index: 1;
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      box-shadow: 0 8px 48px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.03) inset;
      margin: 16px;
    }

    .sl-logo {
      display: flex; flex-direction: column; align-items: center;
      padding-bottom: 20px; margin-bottom: 20px;
      border-bottom: 1px solid rgba(255,255,255,.07);
    }
    .sl-logo img { height: 62px; width: auto; object-fit: contain; }

    .portal-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(16,185,129,.15); border: 1px solid rgba(52,211,153,.3);
      color: #6ee7b7; border-radius: 20px; padding: 5px 14px;
      font-size: 12px; font-weight: 600; letter-spacing: .3px; margin-top: 12px;
    }
    .portal-badge svg { width: 13px; height: 13px; }

    .sl-subtitle {
      font-size: 17px; font-weight: 700; color: #fff;
      text-align: center; margin-bottom: 8px; letter-spacing: -.2px;
    }
    .sl-helper {
      font-size: 13px; color: rgba(255,255,255,.45);
      text-align: center; line-height: 1.6; margin-bottom: 22px;
    }

    .sl-alert {
      display: flex; align-items: flex-start; gap: 10px;
      background: rgba(239,68,68,.12); border: 1px solid #ef4444;
      color: #fca5a5; border-radius: 10px;
      padding: 12px 14px; font-size: 13px; font-weight: 600;
      margin-bottom: 16px;
    }
    .sl-alert svg { flex-shrink: 0; margin-top: 1px; }

    .sl-group { margin-bottom: 13px; }
    .sl-input-wrap { position: relative; }
    .sl-input-icon {
      position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
      color: rgba(255,255,255,.28); pointer-events: none;
    }
    .sl-input {
      width: 100%;
      background: rgba(255,255,255,.06);
      border: 1.5px solid rgba(255,255,255,.09);
      color: #fff; padding: 12px 12px 12px 42px;
      font-size: 13.5px; font-weight: 500; border-radius: 10px;
      font-family: inherit; box-sizing: border-box;
      transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .sl-input::placeholder { color: rgba(255,255,255,.28); }
    .sl-input:focus {
      outline: none;
      border-color: rgba(16,185,129,.5);
      box-shadow: 0 0 0 3px rgba(16,185,129,.13);
      background: rgba(255,255,255,.09);
    }

    .sl-btn {
      width: 100%; padding: 13px;
      background: linear-gradient(135deg, #10b981, #059669);
      color: #fff; font-size: 14px; font-weight: 700;
      border: none; border-radius: 10px; cursor: pointer;
      font-family: inherit; letter-spacing: .1px;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      box-shadow: 0 4px 18px rgba(16,185,129,.32);
      transition: opacity .15s, transform .1s; margin-top: 4px;
    }
    .sl-btn:hover { opacity: .92; transform: translateY(-1px); }
    .sl-btn:active { transform: none; }

    .sl-hint {
      text-align: center; margin-top: 18px;
      font-size: 12.5px; color: rgba(255,255,255,.28);
    }
    .sl-hint a { color: rgba(52,211,153,.8); font-weight: 600; text-decoration: none; }
    .sl-hint a:hover { color: #34d399; }

    .sl-powered {
      text-align: center; margin-top: 20px;
      font-size: 11.5px; color: rgba(255,255,255,.18);
    }
    .sl-powered a { color: rgba(52,211,153,.4); text-decoration: none; }
    .sl-powered a:hover { color: rgba(52,211,153,.8); }

    @media(max-width:480px) { .sl-card { padding: 28px 22px; } }
  </style>
</head>
<body>
<div class="sl-page">
  <div class="sl-bg"></div>
  <div class="sl-card">

    <div class="sl-logo">
      <img src="{{ asset('images/gt-icon.png') }}" alt="Gaurangi Technologies"
           onerror="this.style.display='none'">
      <div class="portal-badge">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
        </svg>
        Student Portal
      </div>
    </div>

    <p class="sl-subtitle">Forgot Password</p>
    <p class="sl-helper">Enter your registered mobile number or email address. We will send a password reset link to your email.</p>

    @if($errors->any())
    <div class="sl-alert">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('student.password.email') }}">
      @csrf
      <div class="sl-group">
        <div class="sl-input-wrap">
          <span class="sl-input-icon">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
          </span>
          <input type="text" name="login"
                 class="sl-input {{ $errors->has('login') ? 'sl-input-err' : '' }}"
                 placeholder="Mobile Number / Email"
                 value="{{ old('login') }}" autofocus>
        </div>
      </div>

      <button type="submit" class="sl-btn">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Send Reset Link
      </button>
    </form>

    <div class="sl-hint">
      <a href="{{ route('student.login') }}">← Back to Login</a>
    </div>

    <div class="sl-powered">Powered by <a href="#">Gaurangi Technologies</a></div>
  </div>
</div>
</body>
</html>
