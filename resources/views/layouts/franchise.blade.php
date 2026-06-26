<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Franchise Panel') — {{ Auth::guard('institute')->user()->franchise?->name ?? 'Franchise' }}</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    .gt-nav-item.active{background:rgba(234,88,12,.15)!important;color:#fb923c!important;border-left:3px solid #ea580c!important;}
    .gt-nav-item.active svg{stroke:#fb923c!important;}
  </style>
  @stack('styles')
</head>
<body>
  <div class="gt-layout">

    {{-- Sidebar --}}
    <aside class="gt-sidebar open" id="sidebar">

      {{-- Brand: Franchise logo + name --}}
      <div class="gt-sidebar-brand" style="display:block;padding:10px 12px 8px;">
        <div class="gt-user-card" style="width:100%;padding:9px 10px;">
          @php $frLogo = Auth::guard('institute')->user()->franchise?->logo; @endphp
          <div style="width:36px;height:36px;border-radius:50%;background:#ea580c;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;overflow:hidden;">
            @if($frLogo && !in_array(trim($frLogo), ['', 'images/default-franchise.png', 'images/default-institute.png']))
              <img src="{{ asset($frLogo) }}" alt="logo" style="width:100%;height:100%;object-fit:cover;">
            @else
              {{ strtoupper(substr(Auth::guard('institute')->user()->franchise?->name ?? 'FR', 0, 2)) }}
            @endif
          </div>
          <div class="user-info">
            <div class="name">{{ Str::limit(Auth::guard('institute')->user()->franchise?->name ?? 'Franchise', 20) }}</div>
            <div class="role">Franchise Panel</div>
            <div class="role">{{ Auth::guard('institute')->user()->franchise?->unique_id ?? Auth::guard('institute')->user()->franchise?->franchise_id ?? '—' }}</div>
          </div>
        </div>
      </div>

      {{-- Wallet chip --}}
      @php $frBalance = Auth::guard('institute')->user()->franchise?->wallet?->balance ?? 0; @endphp
      <div style="margin:0 12px 6px;background:rgba(234,88,12,.1);border:1px solid rgba(234,88,12,.2);border-radius:7px;padding:6px 10px;display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:9px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.8px;">Wallet</span>
        <span style="font-size:13px;font-weight:800;color:#fb923c;font-family:monospace;">₹{{ number_format($frBalance, 2) }}</span>
      </div>

      {{-- Nav (scrollbar hidden by gt-sidebar-nav CSS) --}}
      <nav class="gt-sidebar-nav">

        <div class="gt-sidebar-section">Overview</div>
        <a href="{{ route('franchise.dashboard') }}" class="gt-nav-item {{ request()->routeIs('franchise.dashboard') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Dashboard
        </a>

        <div class="gt-sidebar-section">Admission</div>
        <a href="{{ route('franchise.enrollment.pending') }}" class="gt-nav-item {{ request()->routeIs('franchise.enrollment.pending') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Pending Admissions
        </a>
        <a href="{{ route('franchise.enrollment.choose') }}" class="gt-nav-item {{ request()->routeIs('franchise.enrollment.choose','franchise.enrollment.new','franchise.enrollment.quick') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
          New Admission
        </a>

        <div class="gt-sidebar-section">Students</div>
        <a href="{{ route('franchise.students.index') }}" class="gt-nav-item {{ request()->routeIs('franchise.students.*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          All Students
        </a>

        <div class="gt-sidebar-section">Fee</div>
        <a href="{{ route('franchise.students.index') }}?status=RUN" class="gt-nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          Collect Fee
        </a>

        <div class="gt-sidebar-section">Wallet</div>
        <a href="{{ route('franchise.wallet') }}" class="gt-nav-item {{ request()->routeIs('franchise.wallet') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          My Wallet
        </a>

        <div class="gt-sidebar-section">Setup</div>
        <a href="{{ route('franchise.batches.index') }}" class="gt-nav-item {{ request()->routeIs('franchise.batches.*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          My Batches
        </a>

        <div class="gt-sidebar-section">Charges</div>
        <a href="{{ route('franchise.pricing.index') }}" class="gt-nav-item {{ request()->routeIs('franchise.pricing.*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          Course Pricing
        </a>

        <div class="gt-sidebar-section">Certificate</div>
        <a href="{{ route('franchise.certificate.index') }}" class="gt-nav-item {{ request()->routeIs('franchise.certificate.*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
          My Certificate
        </a>

      </nav>

      {{-- Footer --}}
      <div class="gt-sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline w-full" style="justify-content:center;border-color:rgba(255,255,255,.1);color:rgba(255,255,255,.5);font-size:12px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sign Out
          </button>
        </form>
        <div class="gt-footer-brand">
          <img src="{{ asset('images/gt-icon.png') }}" alt="" onerror="this.style.display='none'">
          <span>Powered by Gaurangi Technologies</span>
        </div>
      </div>
    </aside>

    {{-- Main --}}
    <div class="gt-main">
      <header class="gt-topbar">
        <div style="display:flex;align-items:center;gap:8px;">
          <span style="font-size:13px;font-weight:600;color:var(--text-1);">@yield('page-title', 'Dashboard')</span>
        </div>
        <div style="flex:1;"></div>
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="font-size:12px;color:var(--text-3);background:var(--bg-3);padding:5px 12px;border-radius:8px;border:1px solid var(--border);">
            <span id="fr-clock"></span> &nbsp;·&nbsp; {{ now()->format('D, d M Y') }}
          </div>
          @yield('topbar-actions')
        </div>
      </header>

      <div class="gt-page">
        @if(session('success'))
          <div class="gt-alert gt-alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="gt-alert gt-alert-error" style="margin-bottom:16px;">{{ session('error') }}</div>
        @endif

        @yield('content')
      </div>
    </div>
  </div>

  <script>
  (function(){
    function tick(){
      var d=new Date();
      var h=d.getHours(),m=d.getMinutes(),s=d.getSeconds();
      var ampm=h>=12?'PM':'AM';
      h=h%12||12;
      var el=document.getElementById('fr-clock');
      if(el) el.textContent=(h<10?'0':'')+h+':'+(m<10?'0':'')+m+':'+(s<10?'0':'')+s+' '+ampm;
    }
    tick(); setInterval(tick,1000);
  })();
  </script>
  @stack('scripts')
</body>
</html>
