<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify OTP — GT Institute</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @php $isFranchise = ($portal ?? '') === 'franchise'; @endphp
  <style>
    .otp-inputs {
      display: flex; gap: 10px; justify-content: center; margin: 8px 0 4px;
    }
    .otp-digit {
      width: 48px; height: 56px; text-align: center;
      font-size: 22px; font-weight: 700;
      border-radius: 12px; border: 1.5px solid {{ $isFranchise ? 'rgba(251,146,60,.35)' : 'rgba(138,115,245,.35)' }};
      background: rgba(255,255,255,.06); color: #fff;
      outline: none; caret-color: transparent;
      transition: border-color .2s, box-shadow .2s;
    }
    .otp-digit:focus {
      border-color: {{ $isFranchise ? 'rgba(251,146,60,.85)' : 'rgba(138,115,245,.85)' }};
      box-shadow: 0 0 0 3px {{ $isFranchise ? 'rgba(249,115,22,.18)' : 'rgba(138,115,245,.18)' }};
    }
    .otp-digit.filled {
      border-color: {{ $isFranchise ? 'rgba(251,146,60,.7)' : 'rgba(138,115,245,.7)' }};
      background: {{ $isFranchise ? 'rgba(249,115,22,.1)' : 'rgba(138,115,245,.1)' }};
    }
    .resend-row {
      text-align: center; margin-top: 16px; font-size: 13px;
      color: rgba(255,255,255,.45);
    }
    .resend-row a, .resend-row button {
      color: {{ $isFranchise ? 'rgba(251,146,60,.92)' : 'rgba(138,115,245,.92)' }};
      text-decoration: none; background: none; border: none;
      cursor: pointer; font-size: 13px; padding: 0;
    }
    .resend-row a:hover, .resend-row button:hover { color: #fff; }
    .masked-email {
      color: rgba(255,255,255,.65); font-size: 13px;
      text-align: center; margin-bottom: 20px;
    }
    .back-link {
      display: flex; align-items: center; gap: 6px;
      color: {{ $isFranchise ? 'rgba(251,146,60,.8)' : 'rgba(138,115,245,.8)' }};
      font-size: 12px; text-decoration: none; margin-bottom: 20px;
    }
    .back-link:hover { color: #fff; }
    #otp-hidden { display: none; }
    #verifying-msg { color: {{ $isFranchise ? 'rgba(251,146,60,.85)' : 'rgba(138,115,245,.85)' }}; }
    @keyframes spin { to { transform: rotate(360deg); } }

    @if($isFranchise)
    .gt-login-bg::before {
      background: radial-gradient(circle, rgba(249,115,22,.2) 0%, transparent 65%) !important;
    }
    .gt-login-bg::after {
      background: radial-gradient(circle, rgba(234,88,12,.15) 0%, transparent 65%) !important;
    }
    .gt-login-card { border-color: rgba(251,146,60,.18) !important; }
    @endif
  </style>
</head>
<body>
<div class="gt-login-page">
  <div class="gt-login-bg"></div>

  <div class="gt-login-card">

    {{-- Logo --}}
    <div class="gt-login-logo">
      <div class="gt-login-logo-row">
        <img src="{{ asset('images/gt-icon.png') }}" alt="GT" class="gt-logo-img"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="logo-icon-box" style="display:none;">GT</div>
      </div>
    </div>

    @if($isFranchise)
      <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(249,115,22,.12);border:1px solid rgba(251,146,60,.25);color:#fdba74;font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;padding:4px 12px;border-radius:999px;margin-bottom:14px;">
        🏪 Franchise Portal
      </div>
    @endif

    <a href="{{ $isFranchise ? route('franchise.login') : route('login') }}" class="back-link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Login
    </a>

    <p class="gt-login-subtitle" style="font-size:17px;margin-bottom:6px;">Verify Your Identity</p>
    <p class="masked-email">A 6-digit OTP has been sent to <strong>{{ $maskedEmail }}</strong></p>

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

    <form method="POST" action="{{ route('login.otp.verify') }}" id="otp-form">
      @csrf
      <input type="hidden" name="otp" id="otp-hidden">

      <div class="otp-inputs">
        @for($i = 0; $i < 6; $i++)
          <input type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                 class="otp-digit" id="otp-{{ $i }}" autocomplete="off">
        @endfor
      </div>

      <button type="submit" id="verify-btn" style="display:none;" aria-hidden="true"></button>

      <div id="verifying-msg" style="display:none;text-align:center;margin-top:20px;font-size:14px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:6px;animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-dasharray="31 31" stroke-dashoffset="0"/></svg>
        Verifying…
      </div>
    </form>

    <div class="resend-row">
      Didn't receive the code?
      <form method="POST" action="{{ route('login.otp.resend') }}" style="display:inline;">
        @csrf
        <button type="submit">Resend OTP</button>
      </form>
    </div>

    <div class="gt-login-powered">
      Powered by <a href="#">Gaurangi Technologies</a>
    </div>

  </div>
</div>

<script>
(function () {
  const digits = Array.from(document.querySelectorAll('.otp-digit'));
  const hidden = document.getElementById('otp-hidden');

  function sync() {
    const val = digits.map(d => d.value).join('');
    hidden.value = val;
    digits.forEach(d => d.classList.toggle('filled', d.value !== ''));
    if (val.length === 6) {
      digits.forEach(d => d.disabled = true);
      document.getElementById('verifying-msg').style.display = 'block';
      document.getElementById('otp-form').submit();
    }
  }

  digits.forEach((el, idx) => {
    el.addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(-1);
      sync();
      if (this.value && idx < 5) digits[idx + 1].focus();
    });

    el.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && !this.value && idx > 0) {
        digits[idx - 1].focus();
        digits[idx - 1].value = '';
        sync();
      }
    });

    el.addEventListener('paste', function (e) {
      e.preventDefault();
      const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
      pasted.split('').forEach((ch, i) => { if (digits[i]) digits[i].value = ch; });
      digits[Math.min(pasted.length, 5)].focus();
      sync();
    });
  });

  digits[0].focus();
})();
</script>
</body>
</html>
