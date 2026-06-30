@php
  $path    = request()->path();
  $referer = request()->header('referer', '');
  $check   = $path . ' ' . $referer;

  if (str_contains($check, 'owner/')) {
      $loginUrl    = route('owner.login');
      $portalLabel = 'Owner Portal';
      $portalIcon  = 'shield';
  } elseif (str_contains($check, 'franchise/')) {
      $loginUrl    = route('franchise.login');
      $portalLabel = 'Franchise Portal';
      $portalIcon  = 'briefcase';
  } elseif (str_contains($check, 'staff/')) {
      $loginUrl    = route('staff.login');
      $portalLabel = 'Staff Portal';
      $portalIcon  = 'users';
  } elseif (str_contains($check, 'student/')) {
      $loginUrl    = route('student.login');
      $portalLabel = 'Student Portal';
      $portalIcon  = 'book';
  } else {
      $loginUrl    = route('login');
      $portalLabel = 'Institute Portal';
      $portalIcon  = 'lock';
  }

  $retryAfter = (int) (isset($exception) ? $exception->getHeaders()['Retry-After'] ?? 30 : 30);
  if ($retryAfter < 5) $retryAfter = 30;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Too Many Requests — GT Institute</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .se-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(108,93,211,.15);
      border: 1px solid rgba(108,93,211,.35);
      border-radius: 20px;
      padding: 5px 14px;
      font-size: 11px; font-weight: 700; letter-spacing: 1.2px;
      text-transform: uppercase; color: rgba(138,115,245,.9);
      margin-bottom: 28px;
    }
    .se-ring-wrap { position: relative; width: 90px; height: 90px; margin: 0 auto 28px; }
    .se-ring-svg { transform: rotate(-90deg); }
    .se-ring-track { fill: none; stroke: rgba(255,255,255,.07); stroke-width: 5; }
    .se-ring-progress {
      fill: none; stroke: #6c5dd3; stroke-width: 5; stroke-linecap: round;
      stroke-dasharray: 226.2; stroke-dashoffset: 0;
      animation: se-drain {{ $retryAfter }}s linear forwards;
      filter: drop-shadow(0 0 6px rgba(108,93,211,.6));
    }
    @keyframes se-drain { from { stroke-dashoffset: 0; } to { stroke-dashoffset: 226.2; } }
    .se-ring-inner {
      position: absolute; inset: 0;
      display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;
    }
    .se-clock-icon { color: rgba(138,115,245,.85); }
    .se-count { font-size: 13px; font-weight: 700; color: #fff; line-height: 1; }

    .se-title { font-size: 22px; font-weight: 700; color: #fff; margin: 0 0 10px; letter-spacing: -.3px; }
    .se-subtitle { font-size: 13.5px; color: rgba(255,255,255,.5); margin: 0 0 20px; line-height: 1.6; }

    .se-progress-track { width: 100%; height: 2px; background: rgba(255,255,255,.08); border-radius: 2px; margin-bottom: 12px; overflow: hidden; }
    .se-progress-fill {
      height: 100%; width: 100%;
      background: linear-gradient(90deg, #6c5dd3, #a78bfa);
      border-radius: 2px; transform-origin: left;
      animation: se-shrink {{ $retryAfter }}s linear forwards;
    }
    @keyframes se-shrink { from { transform: scaleX(1); } to { transform: scaleX(0); } }

    .se-redirect-text { font-size: 12px; color: rgba(255,255,255,.35); margin: 0 0 24px; }
    .se-redirect-text span { color: rgba(138,115,245,.8); font-weight: 600; }

    .se-btn-row { display: flex; gap: 10px; justify-content: center; margin-bottom: 24px; }
    .se-btn {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 11px 22px; border-radius: 10px;
      font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer;
      border: none; transition: all .2s;
    }
    .se-btn-primary { background: #6c5dd3; color: #fff; box-shadow: 0 4px 16px rgba(108,93,211,.35); }
    .se-btn-primary:hover { background: #5a4bc0; color: #fff; transform: translateY(-1px); }
    .se-btn-primary[disabled] { opacity: .45; cursor: not-allowed; pointer-events: none; }
    .se-btn-outline { background: rgba(255,255,255,.06); color: rgba(255,255,255,.7); border: 1px solid rgba(255,255,255,.12); }
    .se-btn-outline:hover { background: rgba(255,255,255,.1); color: #fff; transform: translateY(-1px); }

    .se-divider { border: none; border-top: 1px solid rgba(255,255,255,.07); margin: 0 0 20px; }
    .se-powered { font-size: 12px; color: rgba(255,255,255,.25); display: flex; align-items: center; justify-content: center; gap: 6px; }
    .se-powered strong { color: rgba(138,115,245,.65); font-weight: 600; }
  </style>
</head>
<body>
<div class="gt-login-page">
  <div class="gt-login-bg"></div>

  <div class="gt-login-card" style="text-align:center; max-width:420px;">

    <div class="se-badge">
      @if($portalIcon === 'shield')
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      @elseif($portalIcon === 'briefcase')
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      @elseif($portalIcon === 'users')
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      @elseif($portalIcon === 'book')
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      @else
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      @endif
      {{ $portalLabel }}
    </div>

    {{-- Countdown ring --}}
    <div class="se-ring-wrap">
      <svg class="se-ring-svg" width="90" height="90" viewBox="0 0 90 90">
        <circle class="se-ring-track"    cx="45" cy="45" r="36"/>
        <circle class="se-ring-progress" cx="45" cy="45" r="36"/>
      </svg>
      <div class="se-ring-inner">
        <svg class="se-clock-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        <span class="se-count" id="se-count">{{ $retryAfter }}</span>
      </div>
    </div>

    <h1 class="se-title">Too Many Attempts</h1>
    <p class="se-subtitle">You've made too many requests in a short time. Please wait a moment before trying again.</p>

    <div class="se-progress-track">
      <div class="se-progress-fill"></div>
    </div>
    <p class="se-redirect-text">
      You can try again in <span id="se-timer">{{ $retryAfter }}</span> seconds
    </p>

    <div class="se-btn-row">
      <a href="{{ $loginUrl }}" class="se-btn se-btn-primary" id="se-retry-btn" disabled>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
          <polyline points="10 17 15 12 10 7"/>
          <line x1="15" y1="12" x2="3" y2="12"/>
        </svg>
        Back to Login
      </a>
      <a href="{{ url('/') }}" class="se-btn se-btn-outline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Go Home
      </a>
    </div>

    <hr class="se-divider">

    <div class="se-powered">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      Powered by <strong>Gaurangi Technologies</strong>
    </div>

  </div>
</div>

<script>
  let remaining = {{ $retryAfter }};
  const countEl = document.getElementById('se-count');
  const timerEl = document.getElementById('se-timer');
  const retryBtn = document.getElementById('se-retry-btn');

  const tick = setInterval(() => {
    remaining--;
    if (countEl) countEl.textContent = Math.max(remaining, 0);
    if (timerEl) timerEl.textContent = Math.max(remaining, 0);
    if (remaining <= 0) {
      clearInterval(tick);
      if (retryBtn) retryBtn.removeAttribute('disabled');
    }
  }, 1000);
</script>
</body>
</html>
