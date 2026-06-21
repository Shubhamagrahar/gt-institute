<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GT Institute — Portal</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      min-height: 100vh;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #060d18;
      overflow-x: hidden;
    }

    /* ── Gradient background ─────────────────────── */
    .home-bg {
      position: fixed; inset: 0; z-index: 0;
      background: radial-gradient(ellipse 70% 60% at 100% 50%, rgba(6,78,44,.55) 0%, transparent 65%),
                  radial-gradient(ellipse 55% 70% at 0% 40%,  rgba(15,40,90,.7)  0%, transparent 65%),
                  radial-gradient(ellipse 80% 50% at 50% 100%, rgba(4,30,18,.8) 0%, transparent 70%),
                  #060d18;
    }
    /* subtle grid overlay */
    .home-bg::after {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
      background-size: 48px 48px;
    }

    /* ── Page wrapper ─────────────────────────────── */
    .home-wrap {
      position: relative; z-index: 1;
      min-height: 100vh;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 48px 24px 56px;
    }

    /* ── Badge ───────────────────────────────────── */
    .home-badge {
      display: inline-flex; align-items: center; gap: 7px;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 999px; padding: 6px 16px;
      font-size: 12px; color: rgba(255,255,255,.55);
      letter-spacing: .4px; margin-bottom: 28px;
    }
    .home-badge svg { width: 13px; height: 13px; opacity: .7; }

    /* ── Heading ─────────────────────────────────── */
    .home-heading {
      font-size: clamp(30px, 5vw, 48px);
      font-weight: 800; color: #fff;
      text-align: center; letter-spacing: -.5px;
      line-height: 1.18; margin-bottom: 14px;
    }
    .home-heading .accent-w { color: #fff; }
    .home-heading .accent-2 {
      background: linear-gradient(90deg, #4ade80, #22d3ee);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .home-sub {
      font-size: 15px; color: rgba(255,255,255,.42);
      text-align: center; max-width: 440px;
      line-height: 1.6; margin-bottom: 52px;
    }

    /* ── Portal grid ─────────────────────────────── */
    .portal-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
      width: 100%; max-width: 1040px;
    }

    /* ── Portal card ─────────────────────────────── */
    .portal-card {
      --c-bg:     rgba(255,255,255,.04);
      --c-border: rgba(255,255,255,.08);
      --c-glow:   transparent;
      --c-btn:    rgba(255,255,255,.1);
      --c-btn-txt:#fff;

      display: flex; flex-direction: column; align-items: center;
      text-align: center;
      background: var(--c-bg);
      border: 1px solid var(--c-border);
      border-radius: 20px;
      padding: 36px 24px 28px;
      text-decoration: none;
      position: relative; overflow: hidden;
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .portal-card::before {
      content: '';
      position: absolute; inset: 0;
      background: var(--c-glow);
      opacity: 0;
      transition: opacity .2s;
      border-radius: inherit;
      pointer-events: none;
    }
    .portal-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 50px rgba(0,0,0,.4);
      border-color: var(--c-border-hover, var(--c-border));
    }
    .portal-card:hover::before { opacity: 1; }

    /* icon circle */
    .portal-icon {
      width: 64px; height: 64px; border-radius: 18px;
      background: var(--c-icon-bg);
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 20px; flex-shrink: 0;
      box-shadow: 0 4px 20px var(--c-icon-shadow, transparent);
    }
    .portal-icon svg { width: 28px; height: 28px; color: var(--c-icon); }

    .portal-name {
      font-size: 16px; font-weight: 700; color: #fff;
      margin-bottom: 8px; letter-spacing: -.1px;
    }
    .portal-desc {
      font-size: 12.5px; color: rgba(255,255,255,.38);
      line-height: 1.6; margin-bottom: 26px; flex: 1;
    }

    /* login button */
    .portal-btn {
      display: flex; align-items: center; justify-content: center; gap: 7px;
      width: 100%; padding: 10px 0;
      background: var(--c-btn);
      color: var(--c-btn-txt);
      border: 1px solid var(--c-btn-border, transparent);
      border-radius: 10px;
      font-size: 13px; font-weight: 600;
      transition: background .15s, box-shadow .15s;
      letter-spacing: .1px;
    }
    .portal-btn svg { width: 14px; height: 14px; }
    .portal-card:hover .portal-btn {
      background: var(--c-btn-hover, var(--c-btn));
      box-shadow: 0 4px 16px var(--c-btn-shadow, transparent);
    }

    /* ── Institute — Purple ──────────────────────── */
    .card-institute {
      --c-bg:          rgba(88,28,135,.28);
      --c-border:      rgba(167,139,250,.15);
      --c-border-hover:rgba(167,139,250,.4);
      --c-glow:        radial-gradient(ellipse 80% 60% at 50% 0%, rgba(139,92,246,.15) 0%, transparent 70%);
      --c-icon-bg:     rgba(139,92,246,.2);
      --c-icon:        #c4b5fd;
      --c-icon-shadow: rgba(139,92,246,.35);
      --c-btn:         rgba(139,92,246,.18);
      --c-btn-border:  rgba(167,139,250,.25);
      --c-btn-txt:     #c4b5fd;
      --c-btn-hover:   rgba(139,92,246,.35);
      --c-btn-shadow:  rgba(139,92,246,.4);
    }

    /* ── Staff — Teal ───────────────────────────── */
    .card-staff {
      --c-bg:          rgba(6,78,59,.3);
      --c-border:      rgba(52,211,153,.14);
      --c-border-hover:rgba(52,211,153,.4);
      --c-glow:        radial-gradient(ellipse 80% 60% at 50% 0%, rgba(16,185,129,.14) 0%, transparent 70%);
      --c-icon-bg:     rgba(16,185,129,.18);
      --c-icon:        #6ee7b7;
      --c-icon-shadow: rgba(16,185,129,.35);
      --c-btn:         rgba(16,185,129,.16);
      --c-btn-border:  rgba(52,211,153,.25);
      --c-btn-txt:     #6ee7b7;
      --c-btn-hover:   rgba(16,185,129,.32);
      --c-btn-shadow:  rgba(16,185,129,.4);
    }

    /* ── Student — Indigo/Blue ──────────────────── */
    .card-student {
      --c-bg:          rgba(30,27,75,.35);
      --c-border:      rgba(129,140,248,.14);
      --c-border-hover:rgba(129,140,248,.4);
      --c-glow:        radial-gradient(ellipse 80% 60% at 50% 0%, rgba(99,102,241,.15) 0%, transparent 70%);
      --c-icon-bg:     rgba(99,102,241,.2);
      --c-icon:        #a5b4fc;
      --c-icon-shadow: rgba(99,102,241,.35);
      --c-btn:         rgba(99,102,241,.18);
      --c-btn-border:  rgba(129,140,248,.25);
      --c-btn-txt:     #a5b4fc;
      --c-btn-hover:   rgba(99,102,241,.35);
      --c-btn-shadow:  rgba(99,102,241,.4);
    }

    /* ── Franchise — Amber/Orange ───────────────── */
    .card-franchise {
      --c-bg:          rgba(120,53,15,.3);
      --c-border:      rgba(251,146,60,.14);
      --c-border-hover:rgba(251,146,60,.4);
      --c-glow:        radial-gradient(ellipse 80% 60% at 50% 0%, rgba(249,115,22,.14) 0%, transparent 70%);
      --c-icon-bg:     rgba(249,115,22,.18);
      --c-icon:        #fed7aa;
      --c-icon-shadow: rgba(249,115,22,.35);
      --c-btn:         rgba(249,115,22,.16);
      --c-btn-border:  rgba(251,146,60,.25);
      --c-btn-txt:     #fdba74;
      --c-btn-hover:   rgba(249,115,22,.32);
      --c-btn-shadow:  rgba(249,115,22,.4);
    }

    /* ── Footer ─────────────────────────────────── */
    .home-footer {
      margin-top: 44px; text-align: center;
      font-size: 12px; color: rgba(255,255,255,.2);
    }
    .home-footer a { color: rgba(255,255,255,.32); text-decoration: none; }
    .home-footer a:hover { color: rgba(255,255,255,.6); }

    /* ── Responsive ──────────────────────────────── */
    @media (max-width: 900px) {
      .portal-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 520px) {
      .portal-grid { grid-template-columns: 1fr; gap: 14px; }
      .portal-card { padding: 28px 20px 22px; }
      .home-heading { font-size: 26px; }
      .home-sub { font-size: 13.5px; margin-bottom: 36px; }
    }
  </style>
</head>
<body>
<div class="home-bg"></div>

<div class="home-wrap">

  {{-- Badge --}}
  <div class="home-badge">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
    </svg>
    Integrated Institute Management System
  </div>

  {{-- Heading --}}
  <h1 class="home-heading">
    Welcome to <span class="accent-2">Computer Institute</span> <span class="accent-2">ERP</span>
  </h1>
  <p class="home-sub">Select your role below to access your personalized dashboard and tools.</p>

  {{-- Cards --}}
  <div class="portal-grid">

    {{-- Institute --}}
    <a href="{{ route('login') }}" class="portal-card card-institute">
      <div class="portal-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
      <div class="portal-name">Institute Admin</div>
      <div class="portal-desc">Manage students, staff, courses, fees & all institute operations.</div>
      <span class="portal-btn">
        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Login
      </span>
    </a>

    {{-- Staff --}}
    <a href="{{ route('staff.login') }}" class="portal-card card-staff">
      <div class="portal-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="portal-name">Staff</div>
      <div class="portal-desc">Teachers & administrative staff — schedules, attendance and tasks.</div>
      <span class="portal-btn">
        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Login
      </span>
    </a>

    {{-- Student --}}
    <a href="{{ route('student.login') }}" class="portal-card card-student">
      <div class="portal-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
          <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
          <path d="M6 12v5c3 3 9 3 12 0v-5"/>
        </svg>
      </div>
      <div class="portal-name">Student</div>
      <div class="portal-desc">Access your results, notices, attendance & student profile.</div>
      <span class="portal-btn">
        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Login
      </span>
    </a>

    {{-- Franchise --}}
    <a href="{{ route('franchise.login') }}" class="portal-card card-franchise">
      <div class="portal-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
          <rect x="2" y="7" width="20" height="14" rx="2"/>
          <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
        </svg>
      </div>
      <div class="portal-name">Franchise</div>
      <div class="portal-desc">Partner admissions, wallet management & commission portal.</div>
      <span class="portal-btn">
        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Login
      </span>
    </a>

  </div>

  <div class="home-footer">
    Powered by <a href="#">Gaurangi Technologies</a>
  </div>

</div>
</body>
</html>
