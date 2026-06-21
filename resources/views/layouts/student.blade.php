<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Dashboard') — Student Portal</title>
<link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('styles')
<style>
/* Green active state for student panel */
:root {
  --stu-green: #10b981;
  --stu-green-dim: rgba(16,185,129,.12);
  --stu-green-bdr: rgba(16,185,129,.8);
}
.gt-sidebar .gt-nav-item.active {
  background: var(--stu-green-dim) !important;
  color: #34d399 !important;
  border-left-color: var(--stu-green) !important;
}
.gt-sidebar .gt-nav-item.active svg { opacity: 1; }
</style>
</head>
<body>
<div class="gt-overlay" id="overlay"></div>
<div class="gt-layout">

  {{-- ─── SIDEBAR ─── --}}
  <aside class="gt-sidebar" id="sidebar">

    {{-- Brand — Institute logo + name (left-aligned, same pattern as admin) --}}
    @php
      $me = Auth::guard('student')->user();
      $stuInstitute = \App\Models\CourseBook::where('user_id', $me->id)
          ->join('course_details', 'course_books.course_id', '=', 'course_details.id')
          ->join('institutes', 'course_details.institute_id', '=', 'institutes.id')
          ->select('institutes.name','institutes.short_name','institutes.logo','institutes.unique_id')
          ->first();
    @endphp
    <div class="gt-sidebar-brand" style="display:block;padding:10px 12px 8px;border-bottom:1px solid var(--sidebar-border);">
      <div class="gt-user-card" style="width:100%;padding:9px 10px;">
        <div style="width:34px;height:34px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;overflow:hidden;">
          @if($stuInstitute?->logo && $stuInstitute->logo !== 'images/default-institute.png')
            <img src="{{ asset($stuInstitute->logo) }}" alt="logo" style="width:100%;height:100%;object-fit:cover;">
          @else
            {{ strtoupper(substr($stuInstitute?->short_name ?? $stuInstitute?->name ?? 'IN', 0, 2)) }}
          @endif
        </div>
        <div class="user-info">
          <div class="name">{{ Str::limit($stuInstitute?->name ?? 'Institute', 20) }}</div>
          <div class="role">Student Portal</div>
          <div class="role">{{ $stuInstitute?->unique_id ?? '' }}</div>
        </div>
      </div>
    </div>

    {{-- Nav --}}
    <nav class="gt-sidebar-nav" style="padding:6px 0;">

      <div class="gt-sidebar-section">Overview</div>
      <a href="{{ route('student.dashboard') }}"
         class="gt-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>

      <div class="gt-sidebar-section">My Profile</div>
      <a href="{{ route('student.profile') }}"
         class="gt-nav-item {{ request()->routeIs('student.profile') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        My Profile
      </a>

      <div class="gt-sidebar-section">Courses</div>
      <a href="{{ route('student.dashboard') }}"
         class="gt-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
         onclick="setTimeout(()=>document.getElementById('enrollments')?.scrollIntoView({behavior:'smooth'}),100)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        My Enrollments
      </a>
      <a href="{{ route('student.courses') }}"
         class="gt-nav-item {{ request()->routeIs('student.courses') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        Apply for Course
      </a>

      <div class="gt-sidebar-section">Finance</div>
      <a href="{{ route('student.fees') }}"
         class="gt-nav-item {{ request()->routeIs('student.fees') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        Fee Ledger
      </a>

      <div class="gt-sidebar-section">Academic</div>
      <a href="{{ route('student.attendance') }}"
         class="gt-nav-item {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Attendance
      </a>

    </nav>

    {{-- Footer — exact same as admin --}}
    <div class="gt-sidebar-footer">
      <form action="{{ route('student.logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline w-full"
                style="justify-content:center;border-color:rgba(255,255,255,.1);color:rgba(255,255,255,.5);font-size:12px;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Sign Out
        </button>
      </form>
      <div class="gt-footer-brand">
        <img src="{{ asset('images/gt-icon.png') }}" alt="" onerror="this.style.display='none'">
        <span>Powered by Gaurangi Technologies</span>
      </div>
    </div>

  </aside>

  {{-- ─── MAIN ─── --}}
  <div class="gt-main">
    <header class="gt-topbar">
      <button class="gt-hamburger" id="hamburger-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>

      <div class="gt-clock-widget">
        <span id="clock-time">00:00:00</span>
        <span class="clock-ampm" id="clock-ampm">AM</span>
        <span class="clock-date" id="clock-date"></span>
      </div>

      <div style="flex:1;"></div>

      <div class="gt-topbar-actions">
        @yield('topbar-actions')
        <button class="gt-topbar-iconbtn" onclick="toggleFullscreen()" title="Fullscreen">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
          </svg>
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

@stack('scripts')
<script>
// Clock — same as admin
(function() {
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  function updateClock() {
    const now = new Date();
    let h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    const pad = n => String(n).padStart(2, '0');
    const timeEl = document.getElementById('clock-time');
    const ampmEl = document.getElementById('clock-ampm');
    const dateEl = document.getElementById('clock-date');
    if (timeEl) timeEl.textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;
    if (ampmEl) ampmEl.textContent = ampm;
    if (dateEl) dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
  }
  updateClock();
  setInterval(updateClock, 1000);
})();

// Hamburger — same as admin
const hamburger = document.getElementById('hamburger-btn');
const sidebar   = document.getElementById('sidebar');
const overlay   = document.getElementById('overlay');
if (hamburger) {
  hamburger.addEventListener('click', () => {
    sidebar?.classList.toggle('open');
    overlay?.classList.toggle('open');
  });
}
if (overlay) {
  overlay.addEventListener('click', () => {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('open');
  });
}

// Fullscreen
function toggleFullscreen() {
  if (!document.fullscreenElement) document.documentElement.requestFullscreen?.();
  else document.exitFullscreen?.();
}

// Auto-close alerts
document.querySelectorAll('[data-auto-close]').forEach(el => {
  setTimeout(() => el.style.opacity = '0', 3500);
  setTimeout(() => el.remove(), 4000);
});
</script>
</body>
</html>
