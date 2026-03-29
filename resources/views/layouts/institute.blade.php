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

    {{-- Session selector --}}
    @php
      $sessions = \App\Models\Owner\Institute::find(Auth::guard('institute')->user()->institute_id)?->sessions ?? collect();
    @endphp
    <div class="gt-session-wrap" style="margin:0 12px 4px;">
      <select>
        <option>JAN-JUNE (2025-26)</option>
        <option>JULY-DEC (2025-26)</option>
      </select>
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

    <div class="gt-sidebar-section">Academics</div>
    <a href="{{ route('institute.courses.index') }}" class="gt-nav-item {{ request()->routeIs('institute.courses.*') || request()->routeIs('institute.course-types.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
      Courses
    </a>
    <a href="{{ route('institute.courses.enrollments') }}" class="gt-nav-item {{ request()->routeIs('institute.courses.enrollments') || request()->routeIs('institute.courses.enroll') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Subjects
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
