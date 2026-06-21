<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password — Student Portal</title>
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
    }

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

    .sl-subtitle {
      font-size: 17px; font-weight: 700; color: #fff;
      text-align: center; margin-bottom: 6px; letter-spacing: -.2px;
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

    /* Password input */
    .pwd-wrap { position: relative; }
    .pwd-wrap .sl-input { padding-left: 42px; padding-right: 46px; width: 100%; box-sizing: border-box; }
    .pwd-icon-l {
      position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
      color: rgba(255,255,255,.28); pointer-events: none; display: flex;
    }
    .pwd-icon-l svg { width: 16px; height: 16px; }
    .pwd-eye {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer; padding: 2px;
      color: rgba(255,255,255,.28); display: flex; align-items: center;
      transition: color .15s;
    }
    .pwd-eye:hover { color: #34d399; }
    .pwd-eye svg { width: 16px; height: 16px; }

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

    /* Strength box */
    .strength-box {
      background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
      border-radius: 10px; padding: 12px 14px; margin-bottom: 14px;
    }
    .strength-title {
      font-size: 11.5px; font-weight: 600; letter-spacing: .3px;
      margin-bottom: 8px; color: rgba(255,255,255,.3); transition: color .25s;
    }
    .strength-title.weak   { color: #ef4444; }
    .strength-title.fair   { color: #f97316; }
    .strength-title.good   { color: #eab308; }
    .strength-title.strong { color: #22c55e; }

    .pwd-rule {
      display: flex; align-items: center; gap: 7px;
      font-size: 12px; color: rgba(255,255,255,.35);
      margin-bottom: 4px; transition: color .2s;
    }
    .pwd-rule:last-child { margin-bottom: 0; }
    .pwd-rule .ri { width: 14px; height: 14px; flex-shrink: 0; color: rgba(255,255,255,.18); transition: color .2s; display: flex; align-items: center; }
    .pwd-rule.met { color: rgba(255,255,255,.75); }
    .pwd-rule.met .ri { color: #22c55e; }

    .match-hint { display: none; font-size: 12px; margin-top: 5px; align-items: center; gap: 5px; }
    .match-hint.match   { color: #34d399; }
    .match-hint.nomatch { color: #ef4444; }
    .match-hint svg { width: 13px; height: 13px; flex-shrink: 0; }

    .sl-btn {
      width: 100%; padding: 13px;
      background: linear-gradient(135deg, #10b981, #059669);
      color: #fff; font-size: 14px; font-weight: 700;
      border: none; border-radius: 10px; cursor: pointer;
      font-family: inherit; letter-spacing: .1px;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      box-shadow: 0 4px 18px rgba(16,185,129,.32);
      transition: opacity .15s, transform .1s;
    }
    .sl-btn:hover { opacity: .92; transform: translateY(-1px); }
    .sl-btn:active { transform: none; }

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

    <p class="sl-subtitle">Create a new password</p>
    <p class="sl-helper">Use a strong password — uppercase, lowercase, a number, and a special character.</p>

    @if($errors->any())
    <div class="sl-alert">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('student.password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ $email }}">

      <div class="sl-group">
        <div class="pwd-wrap">
          <span class="pwd-icon-l"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
          <input type="password" id="password" name="password" class="sl-input" placeholder="New Password" required autocomplete="new-password">
          <button type="button" class="pwd-eye" onclick="togglePwd('password',this)">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <div class="sl-group">
        <div class="pwd-wrap">
          <span class="pwd-icon-l"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
          <input type="password" id="password_confirmation" name="password_confirmation" class="sl-input" placeholder="Confirm New Password" required autocomplete="new-password">
          <button type="button" class="pwd-eye" onclick="togglePwd('password_confirmation',this)">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div id="match-hint" class="match-hint"></div>
      </div>

      <div class="strength-box">
        <div id="s-title" class="strength-title">Password Strength</div>
        <div class="pwd-rule" id="r-len"><span class="ri"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="12" height="12"><path d="M5 13l4 4L19 7"/></svg></span>8–15 Characters</div>
        <div class="pwd-rule" id="r-up"> <span class="ri"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="12" height="12"><path d="M5 13l4 4L19 7"/></svg></span>1 Uppercase letter</div>
        <div class="pwd-rule" id="r-lo"> <span class="ri"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="12" height="12"><path d="M5 13l4 4L19 7"/></svg></span>1 Lowercase letter</div>
        <div class="pwd-rule" id="r-nu"> <span class="ri"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="12" height="12"><path d="M5 13l4 4L19 7"/></svg></span>1 Number</div>
        <div class="pwd-rule" id="r-sp"> <span class="ri"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="12" height="12"><path d="M5 13l4 4L19 7"/></svg></span>1 Special character</div>
      </div>

      <button type="submit" class="sl-btn">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Reset Password
      </button>
    </form>

    <div class="sl-powered">Powered by <a href="#">Gaurangi Technologies</a></div>
  </div>
</div>

<script>
var EYE_OPEN = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
var EYE_OFF  = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';

function togglePwd(id, btn) {
  var inp = document.getElementById(id), isText = inp.type === 'text';
  inp.type = isText ? 'password' : 'text';
  btn.querySelector('svg').innerHTML = isText ? EYE_OPEN : EYE_OFF;
}

var rules = [
  { id:'r-len', test:function(v){ return v.length>=8&&v.length<=15; }},
  { id:'r-up',  test:function(v){ return /[A-Z]/.test(v); }},
  { id:'r-lo',  test:function(v){ return /[a-z]/.test(v); }},
  { id:'r-nu',  test:function(v){ return /[0-9]/.test(v); }},
  { id:'r-sp',  test:function(v){ return /[^A-Za-z0-9]/.test(v); }}
];
var tLabels  = ['Password Strength','Weak','Fair','Good','Strong','Strong'];
var tClasses = ['','weak','fair','good','strong','strong'];
var OK  = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="13" height="13"><path d="M5 13l4 4L19 7"/></svg>';
var ERR = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="13" height="13"><path d="M6 18L18 6M6 6l12 12"/></svg>';

function checkMatch() {
  var p1=document.getElementById('password').value, p2=document.getElementById('password_confirmation').value, h=document.getElementById('match-hint');
  if(!p2){h.style.display='none';return;}
  h.style.display='flex';
  if(p1===p2){h.className='match-hint match';h.innerHTML=OK+' Passwords match';}
  else{h.className='match-hint nomatch';h.innerHTML=ERR+' Passwords do not match';}
}

document.getElementById('password').addEventListener('input', function(){
  var v=this.value, met=0, title=document.getElementById('s-title');
  rules.forEach(function(r){ var p=v.length>0&&r.test(v); document.getElementById(r.id).classList.toggle('met',p); if(p) met++; });
  title.textContent=v.length?tLabels[met]:'Password Strength';
  title.className='strength-title '+(v.length?tClasses[met]:'');
  checkMatch();
});
document.getElementById('password_confirmation').addEventListener('input', checkMatch);
</script>
</body>
</html>
