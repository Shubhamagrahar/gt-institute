<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Student Login</title>
<link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<style>
  /* Autofill fix — green tint */
  .sl-page .sl-input:-webkit-autofill,
  .sl-page .sl-input:-webkit-autofill:hover,
  .sl-page .sl-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 40px #071f13 inset !important;
    -webkit-text-fill-color: #fff !important;
    caret-color: #fff;
    transition: background-color 5000s ease-in-out 0s;
    border-color: rgba(16,185,129,.2) !important;
  }

  /* Page */
  .sl-page {
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    background: #050f09;
    position: relative; overflow: hidden;
  }

  /* Background glows */
  .sl-bg { position: absolute; inset: 0; pointer-events: none; }
  .sl-bg::before {
    content: '';
    position: absolute;
    top: -15%; left: -8%;
    width: 55vw; height: 55vw;
    background: radial-gradient(circle, rgba(16,185,129,.22) 0%, transparent 65%);
    border-radius: 50%;
  }
  .sl-bg::after {
    content: '';
    position: absolute;
    bottom: -20%; right: -8%;
    width: 48vw; height: 48vw;
    background: radial-gradient(circle, rgba(5,150,105,.16) 0%, transparent 65%);
    border-radius: 50%;
  }

  /* Glassmorphism card */
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

  /* Logo area */
  .sl-logo {
    display: flex; flex-direction: column; align-items: center;
    padding-bottom: 20px; margin-bottom: 20px;
    border-bottom: 1px solid rgba(255,255,255,.07);
  }
  .sl-logo img { height: 62px; width: auto; object-fit: contain; }

  /* Subtitle */
  .sl-subtitle {
    font-size: 14px; font-weight: 400;
    color: rgba(255,255,255,.6);
    text-align: center; margin-bottom: 22px;
    line-height: 1.5;
  }
  .sl-subtitle strong { color: #34d399; font-weight: 700; }

  /* Alert */
  .sl-alert {
    display: flex; align-items: flex-start; gap: 10px;
    background: rgba(239,68,68,.12); border: 1px solid #ef4444;
    color: #fca5a5; border-radius: 10px;
    padding: 12px 14px; font-size: 13px; font-weight: 600;
    margin-bottom: 16px;
  }
  .sl-alert svg { flex-shrink: 0; margin-top: 1px; }

  /* Form group */
  .sl-group { margin-bottom: 13px; }
  .sl-input-wrap { position: relative; }
  .sl-input-icon {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,.28); pointer-events: none;
  }
  .sl-input-icon-right {
    position: absolute; right: 12px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none; padding: 2px;
    color: rgba(255,255,255,.28); cursor: pointer;
    transition: color .15s;
  }
  .sl-input-icon-right:hover { color: #34d399; }

  /* Input */
  .sl-input {
    width: 100%;
    background: rgba(255,255,255,.06);
    border: 1.5px solid rgba(255,255,255,.09);
    color: #fff;
    padding: 12px 42px;
    font-size: 13.5px; font-weight: 500;
    border-radius: 10px;
    font-family: inherit;
    box-sizing: border-box;
    transition: border-color .15s, box-shadow .15s, background .15s;
  }
  .sl-input::placeholder { color: rgba(255,255,255,.28); }
  .sl-input:focus {
    outline: none;
    border-color: rgba(16,185,129,.5);
    box-shadow: 0 0 0 3px rgba(16,185,129,.13);
    background: rgba(255,255,255,.09);
  }

  /* Remember + forgot row */
  .sl-meta {
    display: flex; justify-content: space-between; align-items: center;
    gap: 12px; margin-top: 8px; margin-bottom: 12px;
  }
  .sl-remember { display: flex; align-items: center; gap: 7px; cursor: pointer; }
  .sl-remember input { accent-color: #10b981; width: 14px; height: 14px; cursor: pointer; }
  .sl-remember label { font-size: 12.5px; color: rgba(255,255,255,.45); cursor: pointer; }

  /* Submit button */
  .sl-btn {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff; font-size: 14px; font-weight: 700;
    border: none; border-radius: 10px; cursor: pointer;
    font-family: inherit; letter-spacing: .1px;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 18px rgba(16,185,129,.32);
    transition: opacity .15s, transform .1s;
    margin-top: 4px;
  }
  .sl-btn:hover { opacity: .92; transform: translateY(-1px); }
  .sl-btn:active { transform: none; }

  /* Footer hint */
  .sl-hint {
    text-align: center; margin-top: 20px;
    font-size: 12px; color: rgba(255,255,255,.28);
  }
  .sl-hint a { color: rgba(52,211,153,.7); font-weight: 600; text-decoration: none; }
  .sl-hint a:hover { color: #34d399; }

  @media(max-width:480px) {
    .sl-card { padding: 28px 22px; }
  }
</style>
</head>
<body>
<div class="sl-page">
  <div class="sl-bg"></div>

  <div class="sl-card">

    {{-- Logo --}}
    <div class="sl-logo">
      <img src="{{ asset('images/gt-icon.png') }}" alt="Gaurangi Technologies"
           onerror="this.style.display='none'">
    </div>

    {{-- Subtitle --}}
    <p class="sl-subtitle">Sign in to your <strong>Student Portal</strong></p>

    {{-- Errors --}}
    @if(session('success'))
    <div class="sl-alert" style="background:rgba(16,185,129,.12);border-color:#10b981;color:#6ee7b7;">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="sl-alert">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ $errors->first() }}
    </div>
    @endif
    @if(session('error'))
    <div class="sl-alert">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('student.login.post') }}">
      @csrf

      {{-- Login field --}}
      <div class="sl-group">
        <div class="sl-input-wrap">
          <span class="sl-input-icon">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <input type="text" name="login" class="sl-input {{ $errors->has('login') ? 'sl-input-err' : '' }}"
                 placeholder="Mobile Number or Email"
                 value="{{ old('login') }}" autocomplete="username" autofocus>
        </div>
      </div>

      {{-- Password field --}}
      <div class="sl-group">
        <div class="sl-input-wrap">
          <span class="sl-input-icon">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/></svg>
          </span>
          <input type="password" name="password" id="pw" class="sl-input"
                 placeholder="Enter Password" autocomplete="current-password"
                 style="padding-right:46px;">
          <button type="button" class="sl-input-icon-right" onclick="togglePw()">
            <svg id="eye-show" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg id="eye-hide" style="display:none" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
      </div>

      {{-- Meta row --}}
      <div class="sl-meta">
        <label class="sl-remember">
          <input type="checkbox" name="remember" value="1">
          <label style="font-size:12.5px;color:rgba(255,255,255,.45);">Keep me signed in</label>
        </label>
        <a href="{{ route('student.password.request') }}" style="color:rgba(52,211,153,.85);text-decoration:none;font-size:12.5px;font-weight:600;">Forgot Password?</a>
      </div>

      <button type="submit" class="sl-btn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Sign In to Student Portal
      </button>
    </form>

    <div class="sl-hint">
      Institute or staff? <a href="{{ route('login') }}">Login here &rarr;</a>
    </div>
  </div>
</div>

<script>
function togglePw() {
  const inp  = document.getElementById('pw');
  const show = document.getElementById('eye-show');
  const hide = document.getElementById('eye-hide');
  if (inp.type === 'password') {
    inp.type = 'text'; show.style.display = 'none'; hide.style.display = 'block';
  } else {
    inp.type = 'password'; show.style.display = 'block'; hide.style.display = 'none';
  }
}
</script>
</body>
</html>
