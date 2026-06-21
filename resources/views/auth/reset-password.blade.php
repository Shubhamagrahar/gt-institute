<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - GT Institute</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .helper-copy { color: rgba(255,255,255,.55); font-size: 12px; line-height: 1.6; margin: 0 0 18px; }

    /* Password input wrapper */
    .pwd-wrapper { position: relative; }
    .pwd-wrapper .gt-input {
      padding-left: 42px;
      padding-right: 44px;
      width: 100%;
      box-sizing: border-box;
    }

    /* Left lock icon */
    .pwd-icon-left {
      position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
      color: rgba(255,255,255,.35); display: flex; align-items: center; pointer-events: none;
    }
    .pwd-icon-left svg { width: 17px; height: 17px; }

    /* Right eye button */
    .pwd-eye {
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer; padding: 0;
      color: rgba(255,255,255,.4); display: flex; align-items: center;
      transition: color .2s;
    }
    .pwd-eye:hover { color: rgba(255,255,255,.85); }
    .pwd-eye svg { width: 18px; height: 18px; }

    /* Strength checklist — always visible */
    .pwd-strength-box {
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 10px;
      padding: 12px 16px;
      margin-bottom: 16px;
    }

    .pwd-strength-title {
      font-size: 12px;
      font-weight: 600;
      letter-spacing: .3px;
      margin-bottom: 9px;
      color: rgba(255,255,255,.35);
      transition: color .25s;
    }
    .pwd-strength-title.weak    { color: #ef4444; }
    .pwd-strength-title.fair    { color: #f97316; }
    .pwd-strength-title.good    { color: #eab308; }
    .pwd-strength-title.strong  { color: #22c55e; }

    .pwd-rule {
      display: flex; align-items: center; gap: 8px;
      font-size: 12px; color: rgba(255,255,255,.4);
      margin-bottom: 5px; transition: color .25s;
    }
    .pwd-rule:last-child { margin-bottom: 0; }
    .pwd-rule .rule-icon {
      width: 15px; height: 15px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      color: rgba(255,255,255,.2); transition: color .25s;
    }
    .pwd-rule.met { color: rgba(255,255,255,.8); }
    .pwd-rule.met .rule-icon { color: #22c55e; }

    /* Match hint */
    .match-hint {
      display: none; font-size: 11.5px; margin-top: 5px;
      padding-left: 4px; display: flex; align-items: center; gap: 5px;
    }
    .match-hint.match   { color: #22c55e; }
    .match-hint.nomatch { color: #ef4444; }
    .match-hint svg { width: 13px; height: 13px; flex-shrink: 0; }
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

      {{-- New Password --}}
      <div class="gt-form-group">
        <div class="pwd-wrapper">
          <span class="pwd-icon-left">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
          </span>
          <input type="password" id="password" name="password" class="gt-input" placeholder="New Password" required autocomplete="new-password">
          <button type="button" class="pwd-eye" onclick="togglePwd('password', this)" aria-label="Show password">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>
      </div>

      {{-- Confirm Password --}}
      <div class="gt-form-group">
        <div class="pwd-wrapper">
          <span class="pwd-icon-left">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
          </span>
          <input type="password" id="password_confirmation" name="password_confirmation" class="gt-input" placeholder="Confirm Password" required autocomplete="new-password">
          <button type="button" class="pwd-eye" onclick="togglePwd('password_confirmation', this)" aria-label="Show confirm password">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>
        <div id="match-hint" class="match-hint" style="display:none;"></div>
      </div>

      {{-- Strength checklist — always visible --}}
      <div class="pwd-strength-box">
        <div id="strength-title" class="pwd-strength-title">Password Strength</div>
        <div class="pwd-rule" id="rule-length">
          <span class="rule-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="14" height="14">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </span>
          8-15 Characters
        </div>
        <div class="pwd-rule" id="rule-upper">
          <span class="rule-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="14" height="14">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </span>
          1 Uppercase letter
        </div>
        <div class="pwd-rule" id="rule-lower">
          <span class="rule-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="14" height="14">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </span>
          1 Lowercase letter
        </div>
        <div class="pwd-rule" id="rule-number">
          <span class="rule-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="14" height="14">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </span>
          1 Number
        </div>
        <div class="pwd-rule" id="rule-special">
          <span class="rule-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="14" height="14">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </span>
          1 Special character
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-full">Reset Password</button>
    </form>

    <div class="gt-login-powered">
      Powered by <a href="#">Gaurangi Technologies</a>
    </div>
  </div>
</div>

<script>
  /* Eye toggle */
  var EYE_OPEN = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
  var EYE_OFF  = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59"/>';

  function togglePwd(inputId, btn) {
    var input = document.getElementById(inputId);
    var isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.querySelector('svg').innerHTML = isText ? EYE_OPEN : EYE_OFF;
  }

  /* Strength checklist */
  var rules = [
    { id: 'rule-length',  test: function(v){ return v.length >= 8 && v.length <= 15; } },
    { id: 'rule-upper',   test: function(v){ return /[A-Z]/.test(v); } },
    { id: 'rule-lower',   test: function(v){ return /[a-z]/.test(v); } },
    { id: 'rule-number',  test: function(v){ return /[0-9]/.test(v); } },
    { id: 'rule-special', test: function(v){ return /[^A-Za-z0-9]/.test(v); } }
  ];

  var titleLabels  = ['Password Strength', 'Weak', 'Fair', 'Good', 'Strong', 'Strong'];
  var titleClasses = ['', 'weak', 'fair', 'good', 'strong', 'strong'];

  /* Password match checker */
  var ICON_OK  = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
  var ICON_ERR = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';

  function checkMatch() {
    var p1  = document.getElementById('password').value;
    var p2  = document.getElementById('password_confirmation').value;
    var hint = document.getElementById('match-hint');
    if (!p2) { hint.style.display = 'none'; return; }
    hint.style.display = 'flex';
    if (p1 === p2) {
      hint.className = 'match-hint match';
      hint.innerHTML = ICON_OK + ' Passwords match';
    } else {
      hint.className = 'match-hint nomatch';
      hint.innerHTML = ICON_ERR + ' Passwords do not match';
    }
  }

  document.getElementById('password_confirmation').addEventListener('input', checkMatch);
  document.getElementById('password').addEventListener('input', checkMatch);

  document.getElementById('password').addEventListener('input', function () {
    var val = this.value;
    var title = document.getElementById('strength-title');
    var met = 0;

    rules.forEach(function(rule) {
      var pass = val.length > 0 && rule.test(val);
      document.getElementById(rule.id).classList.toggle('met', pass);
      if (pass) met++;
    });

    title.textContent  = val.length ? titleLabels[met]  : 'Password Strength';
    title.className    = 'pwd-strength-title ' + (val.length ? titleClasses[met] : '');
  });
</script>
</body>
</html>
