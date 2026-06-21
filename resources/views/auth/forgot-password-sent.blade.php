<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Link Sent - GT Institute</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .confirmation-box {
      margin-top: 18px;
      padding: 16px 18px;
      border: 1px solid rgba(138,115,245,.35);
      border-radius: 14px;
      background: rgba(138,115,245,.08);
      color: rgba(255,255,255,.86);
      line-height: 1.7;
    }
    .confirmation-box strong { color: #fff; }
    .gt-login-actions { margin-top: 18px; display:flex; gap:12px; flex-wrap:wrap; }
    .gt-login-actions a { color: rgba(138,115,245,.95); text-decoration:none; font-size:13px; }
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

    <p class="gt-login-subtitle">Check your email</p>

    <div class="confirmation-box">
      A password reset link has been sent to <strong>{{ $maskedEmail }}</strong>.
      Open the link in that email to create a new password.
    </div>

    <div class="gt-login-actions">
      <a href="{{ route('password.request') }}">Try another account</a>
      <a href="{{ route('login') }}">Back to login</a>
    </div>

    <div class="gt-login-powered">
      Powered by <a href="#">Gaurangi Technologies</a>
    </div>
  </div>
</div>
</body>
</html>
