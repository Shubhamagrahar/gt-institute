<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - GT Institute</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .helper-copy { color: rgba(255,255,255,.55); font-size: 12px; line-height: 1.6; margin: 0 0 18px; }
  </style>
</head>
<body>
<div class="gt-login-page">
  <div class="gt-login-bg"></div>

  <div class="gt-login-card">
    <div class="gt-login-logo">
      <div class="gt-login-logo-row">
        <div class="logo-icon-box">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 8v4l3 3"/>
          </svg>
        </div>
        <div>
          <div class="logo-wordmark">Gaurangi</div>
          <div class="logo-tagline">Technologies</div>
        </div>
      </div>
    </div>

    <p class="gt-login-subtitle">Create a new password</p>
    <p class="helper-copy">Use a strong password with at least 8 characters, including uppercase, lowercase, a number, and a special character.</p>

    @if($errors->any())
      <div class="gt-alert gt-alert-error" style="background:rgba(239,68,68,.12);border-color:#ef4444;color:#fca5a5;margin-bottom:16px;">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ $email }}">
      <input type="hidden" name="type" value="{{ $type }}">

      <div class="gt-form-group">
        <input type="password" name="password" class="gt-input" placeholder="New Password" required>
      </div>

      <div class="gt-form-group">
        <input type="password" name="password_confirmation" class="gt-input" placeholder="Confirm Password" required>
      </div>

      <button type="submit" class="btn btn-primary w-full">Reset Password</button>
    </form>

    <div class="gt-login-powered">
      Powered by <a href="#">Gaurangi Technologies</a>
    </div>
  </div>
</div>
</body>
</html>
