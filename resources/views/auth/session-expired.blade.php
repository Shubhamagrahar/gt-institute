<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Session Expired</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .se-portal-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(108,93,211,.15);
      border: 1px solid rgba(108,93,211,.30);
      color: rgba(190,182,255,.85);
      font-size: 10.5px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase;
      padding: 5px 14px; border-radius: 20px; margin-bottom: 26px;
    }

    .se-ring-wrap {
      position: relative; width: 104px; height: 104px;
      margin: 0 auto 22px;
    }
    .se-ring-svg { transform: rotate(-90deg); }
    .se-ring-track { fill: none; stroke: rgba(255,255,255,.07); stroke-width: 5; }
    .se-ring-fill  {
      fill: none; stroke: #6c5dd3; stroke-width: 5;
      stroke-linecap: round;
      stroke-dasharray: 263.9;
      stroke-dashoffset: 0;
      transition: stroke-dashoffset 1s linear, stroke .4s;
    }
    .se-ring-inner {
      position: absolute; inset: 0;
      display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;
    }
    .se-lock-icon { color: rgba(160,148,255,.8); }
    .se-count-num { font-size: 22px; font-weight: 800; color: #fff; line-height: 1; }

    .se-title    { font-size: 25px; font-weight: 800; color: #fff; margin: 0 0 9px; letter-spacing: -.3px; }
    .se-subtitle { font-size: 13.5px; color: rgba(255,255,255,.52); line-height: 1.65; margin: 0 0 22px; padding: 0 6px; }

    .se-divider  { border: none; border-top: 1px solid rgba(255,255,255,.07); margin: 0 0 16px; }

    .se-redirect-row {
      display: flex; align-items: center; justify-content: center; gap: 6px;
      font-size: 12px; color: rgba(255,255,255,.38); margin-bottom: 9px;
    }
    .se-prog-track {
      width: 100%; height: 3px; background: rgba(255,255,255,.08);
      border-radius: 2px; overflow: hidden; margin-bottom: 22px;
    }
    .se-prog-fill {
      height: 100%; background: #6c5dd3; border-radius: 2px; width: 100%;
      transition: width 1s linear, background .4s;
    }

    .se-btn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .se-btn {
      display: flex; align-items: center; justify-content: center; gap: 7px;
      border-radius: 10px; padding: 12px 8px; font-size: 13.5px; font-weight: 600;
      text-decoration: none; cursor: pointer; border: none; transition: .15s;
    }
    .se-btn-primary { background: #6c5dd3; color: #fff; }
    .se-btn-primary:hover { background: #5a4bc0; color: #fff; }
    .se-btn-outline {
      background: transparent; color: rgba(255,255,255,.62);
      border: 1.5px solid rgba(255,255,255,.13);
    }
    .se-btn-outline:hover { border-color: rgba(255,255,255,.28); color: #fff; }
  </style>
</head>
<body>
<div class="gt-login-page">
  <div class="gt-login-bg"></div>

  <div class="gt-login-card" style="text-align:center;max-width:420px;padding:36px 32px 28px;">

    {{-- Portal badge --}}
    <div>
      <span class="se-portal-badge">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        {{ $guardLabel }}
      </span>
    </div>

    {{-- Circular countdown --}}
    <div class="se-ring-wrap">
      <svg class="se-ring-svg" width="104" height="104" viewBox="0 0 104 104">
        <circle class="se-ring-track" cx="52" cy="52" r="42"/>
        <circle class="se-ring-fill"  cx="52" cy="52" r="42" id="se-ring"/>
      </svg>
      <div class="se-ring-inner">
        <svg class="se-lock-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <rect x="3" y="11" width="18" height="11" rx="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        <div class="se-count-num" id="se-count">7</div>
      </div>
    </div>

    {{-- Title --}}
    <h2 class="se-title">Session Expired</h2>
    <p class="se-subtitle">Your session has expired. Please log in again to&nbsp;continue.</p>

    <hr class="se-divider">

    {{-- Progress bar + redirect text --}}
    <div class="se-redirect-row">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
      <span id="se-redirect-text">Auto-redirecting to login in 7 seconds</span>
    </div>
    <div class="se-prog-track">
      <div class="se-prog-fill" id="se-prog"></div>
    </div>

    {{-- Buttons --}}
    <div class="se-btn-row">
      <a href="{{ $loginUrl }}" class="se-btn se-btn-primary" id="se-login-btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
          <polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
        </svg>
        Login Again
      </a>
      <a href="{{ url('/') }}" class="se-btn se-btn-outline">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Go Home
      </a>
    </div>

    {{-- Footer --}}
    <div class="gt-login-powered" style="margin-top:22px;">
      © Gaurangi &nbsp;<a href="#">Gaurangi Technologies</a>
    </div>
  </div>
</div>

<script>
(function () {
  const TOTAL   = 7;
  const CIRCUM  = 2 * Math.PI * 42; // 263.9
  const LOGIN   = @json($loginUrl);

  let remaining = TOTAL;

  const ring    = document.getElementById('se-ring');
  const countEl = document.getElementById('se-count');
  const textEl  = document.getElementById('se-redirect-text');
  const progEl  = document.getElementById('se-prog');

  ring.style.strokeDasharray  = CIRCUM;
  ring.style.strokeDashoffset = 0;
  progEl.style.width = '100%';

  function tick() {
    remaining--;

    if (remaining <= 0) {
      window.location.href = LOGIN;
      return;
    }

    const ratio = remaining / TOTAL;
    countEl.textContent          = remaining;
    ring.style.strokeDashoffset  = CIRCUM * (1 - ratio);
    progEl.style.width           = (ratio * 100) + '%';

    if (remaining <= 3) {
      ring.style.stroke  = 'rgba(220,50,50,.85)';
      progEl.style.background = 'rgba(220,50,50,.85)';
    }

    textEl.textContent = 'Auto-redirecting to login in ' + remaining + ' second' + (remaining !== 1 ? 's' : '');

    setTimeout(tick, 1000);
  }

  setTimeout(tick, 1000);
})();
</script>
</body>
</html>
