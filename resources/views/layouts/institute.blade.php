<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — {{ Auth::guard('institute')->user()->institute?->name ?? 'Institute' }}</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('styles')
</head>
<body>
<div class="gt-overlay" id="overlay"></div>

<div class="gt-layout">

  {{-- ─── SIDEBAR ─── --}}
  <aside class="gt-sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="gt-sidebar-brand">
      <div class="brand-icon">GT</div>
      <div>
        <div class="brand-text">Gaurangi</div>
        <div class="brand-sub">Technologies</div>
      </div>
    </div>

    {{-- Institute badge --}}
    <div class="gt-sidebar-inst" style="margin:10px 12px 6px;">
      <div class="inst-ava">
        @php $logo = Auth::guard('institute')->user()->institute?->logo; @endphp
        @if($logo && $logo !== 'images/default-institute.png')
          <img src="{{ asset($logo) }}" alt="logo">
        @else
          {{ strtoupper(substr(Auth::guard('institute')->user()->institute?->short_name ?? Auth::guard('institute')->user()->institute?->name ?? 'IN', 0, 2)) }}
        @endif
      </div>
      <div>
        <div class="inst-name">{{ Str::limit(Auth::guard('institute')->user()->institute?->name ?? 'Institute', 20) }}</div>
        <div class="inst-role">Institute Panel</div>
      </div>
    </div>

   {{-- Active Session Display --}}
{{-- Session Dropdown --}}
@php
  $instituteId    = Auth::guard('institute')->user()->institute_id;
  $allSessions    = \App\Models\InstituteSession::where('institute_id', $instituteId)
                      ->orderByDesc('is_active')->orderByDesc('start_date')->get();
  $activeSession  = $allSessions->where('is_active', true)->first();
  $selectedSessId = session('selected_session_id', $activeSession?->id);
@endphp

<div style="margin:8px 12px 4px;background:rgba(108,93,211,.12);border:1px solid rgba(108,93,211,.25);border-radius:7px;padding:9px 12px;">
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#a89cf5" stroke-width="2" style="flex-shrink:0;">
      <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
      <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
    <span style="font-size:9px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.8px;">Session</span>
    @if($activeSession)
      <span style="width:6px;height:6px;border-radius:50%;background:#10b981;flex-shrink:0;margin-left:auto;"></span>
    @endif
  </div>

  @if($allSessions->isEmpty())
    <a href="{{ route('institute.sessions.create') }}"
       style="display:block;font-size:11.5px;color:#a89cf5;font-weight:600;">
      + Create First Session
    </a>
  @else
    <form method="POST" action="{{ route('institute.sessions.switch') }}" id="session-switch-form">
      @csrf
      <select name="session_id"
        onchange="document.getElementById('session-switch-form').submit()"
        style="width:100%;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:5px;color:rgba(255,255,255,.85);font-size:12px;font-weight:500;padding:5px 8px;outline:none;cursor:pointer;appearance:none;font-family:inherit;">
        @foreach($allSessions as $sess)
          <option value="{{ $sess->id }}"
            {{ $selectedSessId == $sess->id ? 'selected' : '' }}
            style="background:#1a1f3c;color:#fff;">
            {{ $sess->name }}{{ $sess->is_active ? ' ●' : '' }}
          </option>
        @endforeach
      </select>
    </form>
  @endif
</div>

    {{-- Search --}}
    <div class="gt-sidebar-search-wrap" style="margin:4px 12px 8px;">
      <svg class="search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" placeholder="Search..." id="sidebar-search">
      <button class="search-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
    </div>

    {{-- Navigation --}}
    <div class="gt-sidebar-section">Overview</div>
    <a href="{{ route('institute.dashboard') }}" class="gt-nav-item {{ request()->routeIs('institute.dashboard') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <div class="gt-sidebar-section">Session</div>
    <a href="{{ route('institute.sessions.index') }}" class="gt-nav-item">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
  Manage Sessions
</a>

    <div class="gt-sidebar-section">Setup</div>
    <a href="{{ route('institute.courses.index') }}" class="gt-nav-item {{ request()->routeIs('institute.courses.*') || request()->routeIs('institute.course-types.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
      Courses
    </a>
    <a href="{{ route('institute.subjects.index') }}" class="gt-nav-item {{ request()->routeIs('institute.subjects.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/><line x1="6" y1="8" x2="6" y2="8"/><line x1="18" y1="8" x2="18" y2="8"/></svg>
      Subjects
    </a>
    <a href="{{ route('institute.batches.index') }}" class="gt-nav-item {{ request()->routeIs('institute.batches.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 2v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 15h.01"/><path d="M12 15h.01"/><path d="M16 15h.01"/></svg>
      Batch
    </a>
    <a href="{{ route('institute.form-builder.index') }}" class="gt-nav-item {{ request()->routeIs('institute.form-builder.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
      Form Builder
    </a>

    <div class="gt-sidebar-section">Admission</div>
<a href="{{ route('institute.enrollment.choose') }}" class="gt-nav-item {{ request()->routeIs('institute.enrollment.*') ? 'active' : '' }}">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
  New Admission
</a>
<a href="{{ route('institute.fee-collect.index') }}" class="gt-nav-item {{ request()->routeIs('institute.fee-collect.*') ? 'active' : '' }}">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
  Fee Collection
</a>

<div class="gt-sidebar-section">Fee Setup</div>
<a href="{{ route('institute.fee-types.index') }}" class="gt-nav-item {{ request()->routeIs('institute.fee-types.*') ? 'active' : '' }}">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
  Fee Types
</a>

    <div class="gt-sidebar-section">Accounts</div>
    <a href="{{ route('institute.accounts.billing') }}" class="gt-nav-item {{ request()->routeIs('institute.accounts.billing') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><path d="M6 15h3"/></svg>
      Billing & Subscription
    </a>
    <a href="{{ route('institute.accounts.password.edit') }}" class="gt-nav-item {{ request()->routeIs('institute.accounts.password.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/><circle cx="12" cy="16" r="1"/></svg>
      Change Password
    </a>

    <div class="gt-sidebar-section">Franchise</div>
    <a href="{{ route('institute.franchise-levels.create') }}" class="gt-nav-item {{ request()->routeIs('institute.franchise-levels.create') || request()->routeIs('institute.franchise-levels.edit') || request()->routeIs('institute.franchise-levels.index') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l4 7h-8l4-7z"/><path d="M5 22h14"/><path d="M7 22V10h10v12"/></svg>
      Add Level
    </a>
    <a href="{{ route('institute.franchises.create') }}" class="gt-nav-item {{ request()->routeIs('institute.franchises.create') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M12 11v6"/><path d="M9 14h6"/></svg>
      Franchise
    </a>
    <a href="{{ route('institute.franchises.index') }}" class="gt-nav-item {{ request()->routeIs('institute.franchises.index') || request()->routeIs('institute.franchises.show') || request()->routeIs('institute.franchises.edit') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h.01"/><path d="M15 9h.01"/><path d="M9 13h.01"/><path d="M15 13h.01"/><path d="M10 21v-4h4v4"/></svg>
      Franchise List
    </a>
    <a href="{{ route('institute.franchises.wallets') }}" class="gt-nav-item {{ request()->routeIs('institute.franchises.wallets') || request()->routeIs('institute.franchises.transactions') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7H3v10h18V7z"/><path d="M17 12h.01"/><path d="M3 9h18"/></svg>
      Franchise Wallet
    </a>

    

    <div class="gt-sidebar-section">Students</div>
    <a href="{{ route('institute.students.index') }}" class="gt-nav-item {{ request()->routeIs('institute.students.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Student
    </a>
    <a href="{{ route('institute.students.create') }}" class="gt-nav-item" style="display:none;">
      Add Student
    </a>
    <a href="{{ route('institute.courses.enrollments') }}" class="gt-nav-item {{ request()->routeIs('institute.courses.enrollments') || request()->routeIs('institute.courses.enroll') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7"/><line x1="15" y1="19" x2="15" y2="13"/><line x1="18" y1="16" x2="12" y2="16"/></svg>
      Running Enrollment
    </a>
    <a href="{{ route('institute.students.index') }}" class="gt-nav-item {{ request()->routeIs('institute.students.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Search Student
    </a>
    <a href="{{ route('institute.students.index') }}" class="gt-nav-item {{ request()->routeIs('institute.students.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 11 21 7 17 3"/><line x1="21" y1="7" x2="9" y2="7"/><polyline points="7 21 3 17 7 13"/><line x1="3" y1="17" x2="15" y2="17"/></svg>
      Student Promotion
    </a>
    <a href="{{ route('institute.students.index') }}" class="gt-nav-item {{ request()->routeIs('institute.students.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M14 14H7a5 5 0 0 0-5 5v2h20v-2a5 5 0 0 0-5-5h-3"/><polyline points="14 12 16 14 20 10"/></svg>
      Student Certificate
    </a>
    <a href="{{ route('institute.attendance.student') }}" class="gt-nav-item {{ request()->routeIs('institute.attendance.student') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      Academic History
    </a>


    <div class="gt-sidebar-section">Fees</div>
    <a href="{{ route('institute.fee.index') }}" class="gt-nav-item {{ request()->routeIs('institute.fee.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      Fee Collection
    </a>

    <div class="gt-sidebar-section">Team</div>
    <a href="{{ route('institute.staff.index') }}" class="gt-nav-item {{ request()->routeIs('institute.staff.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Staff
    </a>

    {{-- Footer --}}
    <div class="gt-sidebar-footer">
      <div class="gt-user-card">
        <div class="avatar">{{ strtoupper(substr(Auth::guard('institute')->user()->studentProfile?->name ?? Auth::guard('institute')->user()->staffProfile?->name ?? Auth::guard('institute')->user()->user_id, 0, 1)) }}</div>
        <div class="user-info">
          <div class="name">{{ Str::limit(Auth::guard('institute')->user()->studentProfile?->name ?? Auth::guard('institute')->user()->staffProfile?->name ?? Auth::guard('institute')->user()->user_id, 16) }}</div>
          <div class="role">{{ ucfirst(str_replace('_',' ', Auth::guard('institute')->user()->role)) }}</div>
        </div>
      </div>
      <form action="{{ route('logout') }}" method="POST" style="margin-top:10px;">
        @csrf
        <button type="submit" class="btn btn-outline w-full" style="justify-content:center;border-color:rgba(255,255,255,.1);color:rgba(255,255,255,.5);font-size:12px;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Sign Out
        </button>
      </form>
    </div>
  </aside>

  {{-- ─── MAIN ─── --}}
  <div class="gt-main">
    <header class="gt-topbar">
      <button class="gt-hamburger" id="hamburger-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>

      {{-- Clock widget --}}
      <div class="gt-clock-widget">
        <span id="clock-time">00:00:00</span>
        <span class="clock-ampm" id="clock-ampm">AM</span>
        <span class="clock-date" id="clock-date"></span>
      </div>

      <div style="flex:1;"></div>

      <div class="gt-topbar-actions">
        @yield('topbar-actions')
        {{-- Fullscreen & grid icons --}}
        <button class="gt-topbar-iconbtn" onclick="toggleFullscreen()" title="Fullscreen">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
        </button>
        <button class="gt-topbar-iconbtn" title="Apps">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </button>
      </div>
    </header>

    <div class="gt-page">
      @if(session('success'))
        <div class="gt-alert gt-alert-success" data-auto-close>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><polyline points="20 6 9 17 4 12"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="gt-alert gt-alert-error" data-auto-close>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          {{ session('error') }}
        </div>
      @endif

      @yield('content')
    </div>
  </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
<script>
/* Clock */
function updateClock() {
  const now = new Date();
  let h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  const pad = n => String(n).padStart(2, '0');
  document.getElementById('clock-time').textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;
  document.getElementById('clock-ampm').textContent = ampm;
  // Date
  const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  document.getElementById('clock-date').textContent = `${days[now.getDay()]}, ${pad(now.getDate())} ${months[now.getMonth()]}`;
}
updateClock();
setInterval(updateClock, 1000);

/* Fullscreen */
function toggleFullscreen() {
  if (!document.fullscreenElement) document.documentElement.requestFullscreen?.();
  else document.exitFullscreen?.();
}

/* Hamburger */
document.getElementById('hamburger-btn')?.addEventListener('click', function() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('open');
});
document.getElementById('overlay')?.addEventListener('click', function() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('open');
});
</script>
@stack('scripts')
</body>
</html>
