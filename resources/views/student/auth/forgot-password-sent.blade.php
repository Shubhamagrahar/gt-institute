<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Link Sent — Student Portal</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .sl-page {
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      background: #050f09; position: relative; overflow: hidden;
    }
    .sl-bg { position: absolute; inset: 0; pointer-events: none; }
    .sl-bg::before {
      content: ''; position: absolute; top: -15%; left: -8%;
      width: 55vw; height: 55vw;
      background: radial-gradient(circle, rgba(16,185,129,.22) 0%, transparent 65%);
      border-radius: 50%;
    }
    .sl-bg::after {
      content: ''; position: absolute; bottom: -20%; right: -8%;
      width: 48vw; height: 48vw;
      background: radial-gradient(circle, rgba(5,150,105,.16) 0%, transparent 65%);
      border-radius: 50%;
    }
    .sl-card {
      width: 100%; max-width: 430px;
      background: rgba(6,22,13,.82);
      border: 1px solid rgba(16,185,129,.18); border-radius: 20px;
      padding: 38px 36px; position: relative; z-index: 1;
      backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
      box-shadow: 0 8px 48px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.03) inset;
      margin: 16px;
    }
    .sl-logo {
      display: flex; flex-direction: column; align-items: center;
      padding-bottom: 20px; margin-bottom: 20px;
      border-bottom: 1px solid rgba(255,255,255,.07);
    }
    .sl-logo img { height: 62px; width: auto; object-fit: contain; }

    .check-circle {
      width: 60px; height: 60px; border-radius: 50%;
      background: rgba(16,185,129,.12); border: 1.5px solid rgba(52,211,153,.3);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 18px; color: #34d399;
    }

    .sl-subtitle {
      font-size: 17px; font-weight: 700; color: #fff;
      text-align: center; margin-bottom: 8px; letter-spacing: -.2px;
    }

    .confirm-box {
      background: rgba(16,185,129,.08); border: 1px solid rgba(52,211,153,.25);
      border-radius: 12px; padding: 16px 18px;
      font-size: 13.5px; color: rgba(255,255,255,.7);
      line-height: 1.65; margin-bottom: 4px;
    }
    .confirm-box strong { color: #6ee7b7; }

    .sl-hint {
      text-align: center; margin-top: 18px;
      font-size: 12.5px; color: rgba(255,255,255,.28);
      display: flex; justify-content: center; gap: 20px;
    }
    .sl-hint a { color: rgba(52,211,153,.8); font-weight: 600; text-decoration: none; }
    .sl-hint a:hover { color: #34d399; }

    .sl-powered {
      text-align: center; margin-top: 20px;
      font-size: 11.5px; color: rgba(255,255,255,.18);
    }
    .sl-powered a { color: rgba(52,211,153,.4); text-decoration: none; }

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
    </div>

    <div class="check-circle">
      <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
        <polyline points="22,6 12,13 2,6"/>
      </svg>
    </div>

    <p class="sl-subtitle">Check your email</p>

    <div class="confirm-box">
      A password reset link has been sent to <strong>{{ $maskedEmail }}</strong>.
      Open the link in that email to set a new password. The link will expire in <strong>60 minutes</strong>.
    </div>

    <div class="sl-hint">
      <a href="{{ route('student.password.request') }}">Try another account</a>
      <a href="{{ route('student.login') }}">Back to Login</a>
    </div>

    <div class="sl-powered">Powered by <a href="#">Gaurangi Technologies</a></div>
  </div>
</div>
</body>
</html>
