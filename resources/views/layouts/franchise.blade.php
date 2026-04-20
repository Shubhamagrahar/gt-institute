<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Franchise Panel') - {{ Auth::guard('institute')->user()->franchise?->name ?? 'Franchise' }}</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('styles')
</head>
<body>
  <div class="gt-layout">
    <aside class="gt-sidebar open" style="position:sticky; top:0; height:100vh;">
      <div class="gt-sidebar-brand">
        <div class="brand-icon" style="background:#84cc16;color:#111;">FR</div>
        <div>
          <div class="brand-text">Franchise</div>
          <div class="brand-sub">Control Panel</div>
        </div>
      </div>

      <div class="gt-sidebar-inst" style="margin:10px 12px 12px;">
        <div class="inst-ava" style="background:rgba(132,204,22,.14); color:#84cc16;">
          {{ strtoupper(substr(Auth::guard('institute')->user()->franchise?->short_name ?? Auth::guard('institute')->user()->franchise?->name ?? 'FR', 0, 2)) }}
        </div>
        <div>
          <div class="inst-name">{{ Str::limit(Auth::guard('institute')->user()->franchise?->name ?? 'Franchise', 20) }}</div>
          <div class="inst-role">Franchise Panel</div>
        </div>
      </div>

      <div class="gt-sidebar-section">Overview</div>
      <a href="{{ route('franchise.dashboard') }}" class="gt-nav-item {{ request()->routeIs('franchise.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>

      <div class="gt-sidebar-section">Wallet</div>
      <a href="{{ route('franchise.dashboard') }}" class="gt-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7H3v10h18V7z"/><path d="M17 12h.01"/></svg>
        Transactions
      </a>

      <div class="gt-sidebar-footer">
        <div class="gt-user-card">
          <div class="avatar" style="background:rgba(132,204,22,.16); color:#84cc16;">
            {{ strtoupper(substr(Auth::guard('institute')->user()->profile?->name ?? Auth::guard('institute')->user()->user_id, 0, 1)) }}
          </div>
          <div class="user-info">
            <div class="name">{{ Str::limit(Auth::guard('institute')->user()->profile?->name ?? Auth::guard('institute')->user()->user_id, 16) }}</div>
            <div class="role">{{ ucfirst(str_replace('_', ' ', Auth::guard('institute')->user()->role)) }}</div>
          </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="margin-top:10px;">
          @csrf
          <button type="submit" class="btn btn-outline w-full" style="justify-content:center;">Sign Out</button>
        </form>
      </div>
    </aside>

    <div class="gt-main">
      <header class="gt-topbar">
        <div class="gt-clock-widget">
          <span>{{ now()->format('d M Y') }}</span>
        </div>
        <div style="flex:1;"></div>
        <div class="gt-topbar-actions">@yield('topbar-actions')</div>
      </header>

      <div class="gt-page">
        @if(session('success'))
          <div class="gt-alert gt-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="gt-alert gt-alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
      </div>
    </div>
  </div>
  @stack('scripts')
</body>
</html>
