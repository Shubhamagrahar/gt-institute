<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Franchise Login — GT Institute</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .gt-login-page .gt-input:-webkit-autofill,
    .gt-login-page .gt-input:-webkit-autofill:hover,
    .gt-login-page .gt-input:-webkit-autofill:focus,
    .gt-login-page .gt-input:-webkit-autofill:active {
      -webkit-box-shadow: 0 0 0 40px #201a12 inset !important;
      -webkit-text-fill-color: #fff !important;
      caret-color: #fff;
      transition: background-color 5000s ease-in-out 0s;
      border-color: rgba(255,255,255,.09) !important;
    }

    /* Orange theme overrides */
    .gt-login-bg::before {
      background: radial-gradient(circle, rgba(249,115,22,.2) 0%, transparent 65%) !important;
    }
    .gt-login-bg::after {
      background: radial-gradient(circle, rgba(234,88,12,.15) 0%, transparent 65%) !important;
    }
    .gt-login-card {
      border-color: rgba(251,146,60,.18) !important;
    }
    .gt-login-page .gt-input:focus {
      border-color: rgba(251,146,60,.55) !important;
      box-shadow: 0 0 0 3px rgba(249,115,22,.12) !important;
    }
    .btn-franchise {
      background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
      color: #fff; border: none; width: 100%;
      padding: 13px; border-radius: 10px;
      font-size: 14px; font-weight: 700; cursor: pointer;
      font-family: inherit;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      transition: opacity .15s, transform .15s, box-shadow .15s;
      box-shadow: 0 4px 16px rgba(234,88,12,.4);
    }
    .btn-franchise:hover {
      opacity: .92; transform: translateY(-1px);
      box-shadow: 0 6px 22px rgba(234,88,12,.5);
    }
    .btn-franchise:active { transform: none; }

    #toggle-pwd { cursor: pointer; }
    .login-meta-row {
      display: flex; justify-content: space-between; align-items: center;
      gap: 12px; margin-top: 8px; margin-bottom: 12px;
    }
    .login-meta-row a { color: rgba(251,146,60,.92); text-decoration: none; font-size: 13px; }
    .login-meta-row a:hover { color: #fff; }

    .portal-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(249,115,22,.12); border: 1px solid rgba(251,146,60,.25);
      color: #fdba74; border-radius: 20px; padding: 4px 12px;
      font-size: 11.5px; font-weight: 600; letter-spacing: .3px;
      margin-bottom: 10px;
    }
    .portal-badge svg { width: 12px; height: 12px; }

    .back-link {
      text-align: center; margin-top: 20px;
      font-size: 12.5px; color: rgba(255,255,255,.35);
    }
    .back-link a { color: rgba(251,146,60,.7); text-decoration: none; font-weight: 600; }
    .back-link a:hover { color: #fff; }
  </style>
</head>
<body>
<div class="gt-login-page">
  <div class="gt-login-bg"></div>

  <div class="gt-login-card">

    <div class="gt-login-logo">
      <div class="gt-login-logo-row">
        <img src="{{ asset('images/gt-icon.png') }}" alt="GT" class="gt-logo-img"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="logo-icon-box" style="display:none;">GT</div>
      </div>
      <div class="portal-badge">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
          <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
        </svg>
        Franchise Portal
      </div>
    </div>

    <p class="gt-login-subtitle">Sign in with your franchise credentials</p>

    @if($errors->any())
      <div class="gt-alert gt-alert-error" style="background:rgba(239,68,68,.12);border-color:#ef4444;color:#fca5a5;margin-bottom:16px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        {{ $errors->first() }}
      </div>
    @endif

    @if(session('success'))
      <div class="gt-alert gt-alert-success" style="background:rgba(34,197,94,.12);border-color:#22c55e;color:#bbf7d0;margin-bottom:16px;">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <input type="hidden" name="portal" value="franchise">

      <div class="gt-form-group">
        <div class="login-input-wrap">
          <span class="input-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <input type="text" name="login"
                 class="gt-input {{ $errors->has('login') ? 'is-invalid' : '' }}"
                 placeholder="Mobile / Email"
                 value="{{ old('login') }}"
                 autocomplete="username" autofocus>
        </div>
      </div>

      <div class="gt-form-group">
        <div class="login-input-wrap">
          <span class="input-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input type="password" name="password" id="password-field"
                 class="gt-input" placeholder="Password"
                 autocomplete="current-password">
          <button type="button" class="input-icon-right" id="toggle-pwd" onclick="togglePassword()">
            <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <div class="login-meta-row">
        <span style="font-size:12px;color:rgba(255,255,255,.42);">Use your registered mobile or email</span>
        <a href="{{ route('password.request') }}">Forgot Password?</a>
      </div>

      <button type="submit" class="btn-franchise" style="margin-top:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Sign In to Franchise Portal
      </button>
    </form>

    <div class="gt-login-powered">
      Powered by <a href="#">Gaurangi Technologies</a>
    </div>

    <div class="back-link">
      <a href="{{ url('/') }}">← Back to Portal Selection</a>
    </div>

  </div>
</div>

<script>
function togglePassword() {
  const field = document.getElementById('password-field');
  const icon  = document.getElementById('eye-icon');
  const isText = field.type === 'text';
  field.type = isText ? 'password' : 'text';
  icon.innerHTML = isText
    ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
    : '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
}
</script>
</body>
</html>
