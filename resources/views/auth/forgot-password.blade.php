<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - GT Institute</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .gt-login-links { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:14px; }
    .gt-login-links a { color: rgba(138,115,245,.9); text-decoration:none; font-size:13px; }
    .gt-login-links a:hover { color: #fff; }
    .helper-copy { color: rgba(255,255,255,.55); font-size: 12px; line-height: 1.6; margin: 0 0 18px; }
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
        <div>
          <!-- <div class="logo-wordmark">Gaurangi</div>
          <div class="logo-tagline">Technologies</div> -->
        </div>
      </div>
    </div>

    <p class="gt-login-subtitle">Forgot password</p>
    <p class="helper-copy">Enter your email address, mobile number, or application number / login ID. If we find a match, we will send a reset link to the registered email address.</p>

    @if($errors->any())
      <div class="gt-alert gt-alert-error" style="background:rgba(239,68,68,.12);border-color:#ef4444;color:#fca5a5;margin-bottom:16px;">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="gt-form-group">
        <div class="login-input-wrap">
          <span class="input-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </span>
          <input
            type="text"
            name="login"
            class="gt-input {{ $errors->has('login') ? 'is-invalid' : '' }}"
            placeholder="Email / Mobile / Application No"
            value="{{ old('login') }}"
            autofocus
          >
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-full" style="margin-top:6px;">
        Send Reset Link
      </button>
    </form>

    <div class="gt-login-links">
      <a href="{{ route('login') }}">Back to login</a>
    </div>

    <div class="gt-login-powered">
      Powered by <a href="#">Gaurangi Technologies</a>
    </div>
  </div>
</div>
</body>
</html>
