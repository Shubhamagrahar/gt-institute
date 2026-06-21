<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — GT Institute</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    /* Fix browser autofill background breaking dark theme */
    .gt-login-page .gt-input:-webkit-autofill,
    .gt-login-page .gt-input:-webkit-autofill:hover,
    .gt-login-page .gt-input:-webkit-autofill:focus,
    .gt-login-page .gt-input:-webkit-autofill:active {
      -webkit-box-shadow: 0 0 0 40px #202645 inset !important;
      -webkit-text-fill-color: #fff !important;
      caret-color: #fff;
      transition: background-color 5000s ease-in-out 0s;
      border-color: rgba(255,255,255,.09) !important;
    }

    /* Password toggle */
    #toggle-pwd { cursor: pointer; }
    .login-meta-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-top: 8px;
      margin-bottom: 12px;
    }
    .login-meta-row a {
      color: rgba(138,115,245,.92);
      text-decoration: none;
      font-size: 13px;
    }
    .login-meta-row a:hover { color: #fff; }
  </style>
</head>
<body>
<div class="gt-login-page">
  <div class="gt-login-bg"></div>

  <div class="gt-login-card">

    {{-- Gaurangi logo --}}
    <div class="gt-login-logo">
      <div class="gt-login-logo-row">
        <img src="{{ asset('images/gt-icon.png') }}" alt="GT" class="gt-logo-img"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="logo-icon-box" style="display:none;">GT</div>
        <div>
          <!-- <div class="logo-wordmark">Gaurangi</div>
          <div class="logo-tagline">Technologies</div> -->
        </div>
      </div>
    </div>

    {{-- Subtitle --}}
    <p class="gt-login-subtitle">Sign in to continue</p>

    {{-- Error --}}
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

      {{-- Login field --}}
      <div class="gt-form-group">
        <div class="login-input-wrap">
          <span class="input-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <input
            type="text"
            name="login"
            class="gt-input {{ $errors->has('login') ? 'is-invalid' : '' }}"
            placeholder="Mobile / Application No / Email"
            value="{{ old('login') }}"
            autocomplete="username"
            autofocus
          >
        </div>
      </div>

      {{-- Password field --}}
      <div class="gt-form-group">
        <div class="login-input-wrap">
          <span class="input-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input
            type="password"
            name="password"
            id="password-field"
            class="gt-input"
            placeholder="Enter Password"
            autocomplete="current-password"
          >
          <button type="button" class="input-icon-right" id="toggle-pwd" onclick="togglePassword()">
            <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <div class="login-meta-row">
        <span style="font-size:12px;color:rgba(255,255,255,.42);">Use email, mobile, or application number</span>
        <a href="{{ route('password.request') }}">Forgot Password?</a>
      </div>

      {{-- Submit --}}
      <button type="submit" class="btn btn-primary w-full" style="margin-top:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Sign In
      </button>
    </form>

    {{-- Powered by --}}
    <div class="gt-login-powered">
      Powered by <a href="#">Gaurangi Technologies</a>
    </div>

    <div style="text-align:center;margin-top:14px;font-size:12.5px;color:rgba(255,255,255,.3);">
      <a href="{{ url('/') }}" style="color:rgba(138,115,245,.7);text-decoration:none;font-weight:600;">← Back to Portal Selection</a>
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
