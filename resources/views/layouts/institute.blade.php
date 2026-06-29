<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — {{ Auth::guard('institute')->user()->institute?->name ?? 'Institute' }}</title>
  <link rel="icon" href="{{ asset('images/gt-favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
  @stack('styles')
  <style>
    @keyframes gt-spin { to { transform: rotate(360deg); } }
    .gt-btn-spinner {
      display: inline-block; width: 13px; height: 13px;
      border: 2px solid rgba(255,255,255,.35);
      border-top-color: #fff; border-radius: 50%;
      animation: gt-spin .55s linear infinite;
      vertical-align: middle; margin-right: 6px;
      flex-shrink: 0;
    }
  </style>
</head>
<body>
<div class="gt-overlay" id="overlay"></div>

<div class="gt-layout">

  {{-- ─── SIDEBAR ─── --}}
  <aside class="gt-sidebar" id="sidebar">

    {{-- Brand: Institute logo/name + ID (merged card) --}}
    <div class="gt-sidebar-brand" style="display:block;padding:10px 12px 8px;">
      <div class="gt-user-card" style="width:100%;padding:9px 10px;">
        <div style="width:34px;height:34px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;overflow:hidden;">
          @php $logo = Auth::guard('institute')->user()->institute?->logo; @endphp
          @if($logo && $logo !== 'images/default-institute.png')
            <img src="{{ asset($logo) }}" alt="logo" style="width:100%;height:100%;object-fit:cover;">
          @else
            {{ strtoupper(substr(Auth::guard('institute')->user()->institute?->short_name ?? Auth::guard('institute')->user()->institute?->name ?? 'IN', 0, 2)) }}
          @endif
        </div>
        <div class="user-info">
          <div class="name">{{ Str::limit(Auth::guard('institute')->user()->institute?->name ?? 'Institute', 20) }}</div>
          <div class="role">Institute Panel</div>
          <div class="role">{{ Auth::guard('institute')->user()->institute?->unique_id ?? '—' }}</div>
        </div>
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

<div style="margin:6px 12px 4px;background:rgba(37,99,235,.12);border:1px solid rgba(37,99,235,.25);border-radius:7px;padding:6px 10px;">
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="2" style="flex-shrink:0;">
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
       style="display:block;font-size:11.5px;color:#93c5fd;font-weight:600;">
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
    <div class="gt-sidebar-search-wrap" style="margin:4px 12px 4px;">
      <svg class="search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" placeholder="Search..." id="sidebar-search">
      <button class="search-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
    </div>

    {{-- Navigation (scrollable) --}}
    <nav class="gt-sidebar-nav">
    @php
      $__sidebarEnqIid = Auth::guard('institute')->user()?->institute_id;
      $__sidebarEnqOpen = $__sidebarEnqIid
        ? \App\Models\Enquiry::where('institute_id', $__sidebarEnqIid)->where('status','OPEN')->count()
        : 0;
      $__sidebarEnqDue = $__sidebarEnqIid
        ? \App\Models\Enquiry::where('institute_id', $__sidebarEnqIid)->where('status','OPEN')
            ->whereDate('next_followup_date','<=',today())->count()
        : 0;
    @endphp
    <div class="gt-sidebar-section">Overview</div>
    <a href="{{ route('institute.dashboard') }}" class="gt-nav-item {{ request()->routeIs('institute.dashboard') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <div class="gt-sidebar-section">Academic Setup</div>
    <a href="{{ route('institute.accounts.profile') }}" class="gt-nav-item {{ request()->routeIs('institute.accounts.profile') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/><circle cx="12" cy="14" r="2"/></svg>
      Institute Profile
    </a>
    <a href="{{ route('institute.sessions.index') }}" class="gt-nav-item {{ request()->routeIs('institute.sessions.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Sessions
    </a>
    <a href="{{ route('institute.courses.index') }}" class="gt-nav-item {{ ((request()->routeIs('institute.courses.*') && !request()->routeIs('institute.courses.fee-bindings*') && !request()->routeIs('institute.courses.enroll*') && !request()->routeIs('institute.courses.enrollments*')) || request()->routeIs('institute.course-types.*')) ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
      Courses
    </a>
    <a href="{{ route('institute.subjects.index') }}" class="gt-nav-item {{ request()->routeIs('institute.subjects.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/><line x1="6" y1="8" x2="6" y2="8"/><line x1="18" y1="8" x2="18" y2="8"/></svg>
      Subjects
    </a>
    <a href="{{ route('institute.batches.index') }}" class="gt-nav-item {{ request()->routeIs('institute.batches.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 2v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 15h.01"/><path d="M12 15h.01"/><path d="M16 15h.01"/></svg>
      Batches
    </a>
    <a href="{{ route('institute.form-builder.index') }}" class="gt-nav-item {{ request()->routeIs('institute.form-builder.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
      Form Builder
    </a>

    <div class="gt-sidebar-section">Fee Configuration</div>
    <a href="{{ route('institute.fee-types.index') }}" class="gt-nav-item {{ request()->routeIs('institute.fee-types.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      Fee Types
    </a>
    <a href="{{ route('institute.courses.fee-bindings') }}" class="gt-nav-item {{ request()->routeIs('institute.courses.fee-bindings*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/><path d="M18 16v4"/><path d="M16 18h4"/></svg>
      Course Fee Setup
    </a>
    <a href="{{ route('institute.franchise-levels.index') }}" class="gt-nav-item {{ request()->routeIs('institute.franchise-levels.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l4 7h-8l4-7z"/><path d="M5 22h14"/><path d="M7 22V10h10v12"/></svg>
      Franchise Levels
    </a>

    <div class="gt-sidebar-section">Enquiries</div>
    <a href="{{ route('institute.enquiries.create') }}" class="gt-nav-item {{ request()->routeIs('institute.enquiries.create') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="12" y1="8" x2="12" y2="14"/><line x1="9" y1="11" x2="15" y2="11"/></svg>
      New Enquiry
    </a>
    <a href="{{ route('institute.enquiries.index', ['tab'=>'open']) }}" class="gt-nav-item {{ request()->routeIs('institute.enquiries.index') && request()->get('tab','open')==='open' ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Open Enquiries
      @if($__sidebarEnqOpen > 0)
        <span style="margin-left:auto;background:rgba(37,99,235,.2);color:#93c5fd;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px;">{{ $__sidebarEnqOpen }}</span>
      @endif
    </a>
    <a href="{{ route('institute.enquiries.index', ['tab'=>'due']) }}" class="gt-nav-item {{ request()->routeIs('institute.enquiries.index') && request()->get('tab')==='due' ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Today's Follow-ups
      @if($__sidebarEnqDue > 0)
        <span style="margin-left:auto;background:rgba(239,68,68,.15);color:#f87171;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px;">{{ $__sidebarEnqDue }}</span>
      @endif
    </a>
    <a href="{{ route('institute.enquiries.index', ['tab'=>'lost']) }}" class="gt-nav-item {{ request()->routeIs('institute.enquiries.index') && request()->get('tab')==='lost' ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      Lost Enquiries
    </a>
    <a href="{{ route('institute.enquiries.index', ['tab'=>'converted']) }}" class="gt-nav-item {{ request()->routeIs('institute.enquiries.index') && request()->get('tab')==='converted' ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      Converted
    </a>

    <div class="gt-sidebar-section">Admissions</div>
    <a href="{{ route('institute.enrollment.choose') }}" class="gt-nav-item {{ request()->routeIs('institute.enrollment.new') || request()->routeIs('institute.enrollment.quick') || request()->routeIs('institute.enrollment.choose') || request()->routeIs('institute.enrollment.find-student') || request()->routeIs('institute.enrollment.profile') || request()->routeIs('institute.enrollment.preview') || request()->routeIs('institute.enrollment.fee') || request()->routeIs('institute.enrollment.save-fee') || request()->routeIs('institute.enrollment.payment-complete') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
      Enroll Student
    </a>
    <a href="{{ route('institute.enrollment.pending') }}" class="gt-nav-item {{ request()->routeIs('institute.enrollment.pending') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
      Pending Admission
    </a>
    <a href="{{ route('institute.students.index') }}" class="gt-nav-item {{ request()->routeIs('institute.students.index') || request()->routeIs('institute.students.show') || request()->routeIs('institute.students.edit') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Active Students
    </a>
    <a href="{{ route('institute.students.closed') }}" class="gt-nav-item {{ request()->routeIs('institute.students.closed') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      Course Completed
    </a>
    <a href="{{ route('institute.students.expired') }}" class="gt-nav-item {{ request()->routeIs('institute.students.expired') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Lapsed
    </a>
    <a href="{{ route('institute.students.cancelled') }}" class="gt-nav-item {{ request()->routeIs('institute.students.cancelled') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      Withdrawn
    </a>
    <a href="{{ route('institute.students.academic') }}" class="gt-nav-item {{ request()->routeIs('institute.students.academic') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
      Academic Records
    </a>

    <div class="gt-sidebar-section">Attendance</div>
    <a href="{{ route('institute.attendance.students') }}" class="gt-nav-item {{ request()->routeIs('institute.attendance.students') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      Mark Attendance
    </a>
    <a href="{{ route('institute.attendance.register') }}" class="gt-nav-item {{ request()->routeIs('institute.attendance.register*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><rect x="7" y="14" width="3" height="3" rx=".5"/><rect x="11" y="14" width="3" height="3" rx=".5"/></svg>
      Attendance Register
    </a>
    <a href="{{ route('institute.attendance.student-report') }}" class="gt-nav-item {{ request()->routeIs('institute.attendance.student-report*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-3.3 3.6-6 8-6s8 2.7 8 6"/><polyline points="16 12 18 14 22 10"/></svg>
      Student Report
    </a>

    <div class="gt-sidebar-section">Finance</div>
    <a href="{{ route('institute.fees-dashboard') }}" class="gt-nav-item {{ (request()->routeIs('institute.fees-dashboard') && request('tab','all-dues') !== 'quick-pay') || request()->routeIs('institute.enrollment.payment-complete') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      Collect Fees
    </a>
    <a href="{{ route('institute.quick-pay') }}" class="gt-nav-item {{ request()->routeIs('institute.quick-pay*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      Quick Pay
    </a>
    <a href="{{ route('institute.wallet-adjustment.index') }}" class="gt-nav-item {{ request()->routeIs('institute.wallet-adjustment.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7H3v10h18V7z"/><path d="M17 12h.01"/><path d="M3 9h18"/><path d="M8 12h.01"/></svg>
      Wallet Adjustment
    </a>
    <a href="{{ route('institute.fees.collection-report') }}" class="gt-nav-item {{ request()->routeIs('institute.fees.collection-report') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Collection Report
    </a>
    <a href="{{ route('institute.enrollment.monthly-fees') }}" class="gt-nav-item {{ request()->routeIs('institute.enrollment.monthly-fees') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/></svg>
      Monthly Dues
    </a>

    <div class="gt-sidebar-section">Certificates</div>
    <a href="{{ route('institute.certificates.index') }}" class="gt-nav-item {{ request()->routeIs('institute.certificates.index') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><polyline points="9 11 12 14 22 4"/></svg>
      Certificate Overview
    </a>
    <a href="{{ route('institute.certificates.generate') }}" class="gt-nav-item {{ request()->routeIs('institute.certificates.generate') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
      Generate Certificate
    </a>
    <a href="{{ route('institute.certificates.walkin') }}" class="gt-nav-item {{ request()->routeIs('institute.certificates.walkin') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
      Walk-in Certificate
    </a>
    <a href="{{ route('institute.certificates.requests') }}" class="gt-nav-item {{ request()->routeIs('institute.certificates.requests') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
      Pending Requests
    </a>
    <a href="{{ route('institute.certificates.history') }}" class="gt-nav-item {{ request()->routeIs('institute.certificates.history') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      Issue History
    </a>

    <div class="gt-sidebar-section">Distribution</div>
    <a href="{{ route('institute.channel-partners.create') }}" class="gt-nav-item {{ request()->routeIs('institute.channel-partners.create') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M17 11h4"/><path d="M19 9v4"/><path d="M2 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
      Add Partner
    </a>
    <a href="{{ route('institute.channel-partners.index') }}" class="gt-nav-item {{ request()->routeIs('institute.channel-partners.index') || request()->routeIs('institute.channel-partners.edit') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="8" r="4"/><path d="M2 21c0-4 3-7 6-7"/><circle cx="17" cy="9" r="3"/><path d="M14 21c0-3 2.5-5 5.5-5"/></svg>
      All Partners
    </a>

    <div class="gt-sidebar-section">Franchise Network</div>
    <a href="{{ route('institute.franchises.create') }}" class="gt-nav-item {{ request()->routeIs('institute.franchises.create') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M12 11v6"/><path d="M9 14h6"/></svg>
      Add Franchise
    </a>
    <a href="{{ route('institute.franchises.index') }}" class="gt-nav-item {{ request()->routeIs('institute.franchises.index') || request()->routeIs('institute.franchises.show') || request()->routeIs('institute.franchises.edit') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h.01"/><path d="M15 9h.01"/><path d="M9 13h.01"/><path d="M15 13h.01"/><path d="M10 21v-4h4v4"/></svg>
      All Franchises
    </a>
    <a href="{{ route('institute.franchises.wallets') }}" class="gt-nav-item {{ request()->routeIs('institute.franchises.wallets') || request()->routeIs('institute.franchises.transactions') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7H3v10h18V7z"/><path d="M17 12h.01"/><path d="M3 9h18"/></svg>
      Franchise Wallets
    </a>

    <div class="gt-sidebar-section">Team</div>
    <a href="{{ route('institute.staff.index') }}" class="gt-nav-item {{ request()->routeIs('institute.staff.index','institute.staff.create','institute.staff.show','institute.staff.edit') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Staff Members
    </a>
    <a href="{{ route('institute.staff-roles.index') }}" class="gt-nav-item {{ request()->routeIs('institute.staff-roles.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Staff Roles
    </a>
    <a href="{{ route('institute.attendance.staff') }}" class="gt-nav-item {{ request()->routeIs('institute.attendance.staff') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
      Staff Attendance
    </a>

    <div class="gt-sidebar-section">Account</div>
    <a href="{{ route('institute.accounts.billing') }}" class="gt-nav-item {{ request()->routeIs('institute.accounts.billing') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><path d="M6 15h3"/></svg>
      Billing & Subscription
    </a>
    <a href="{{ route('institute.accounts.password.edit') }}" class="gt-nav-item {{ request()->routeIs('institute.accounts.password.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/><circle cx="12" cy="16" r="1"/></svg>
      Change Password
    </a>
    @if(auth()->guard('institute')->user()?->role === 'institute_head')
    <a href="{{ route('institute.accounts.security') }}" class="gt-nav-item {{ request()->routeIs('institute.accounts.security') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Security
    </a>
    @endif
    </nav>

    {{-- Footer (fixed) --}}
    <div class="gt-sidebar-footer">
      <div class="gt-footer-brand">
        <img src="{{ asset('images/gt-icon.png') }}" alt="" onerror="this.style.display='none'">
        <span>Powered by Gaurangi Technologies</span>
      </div>
    </div>
  </aside>

  {{-- ─── MAIN ─── --}}
  <div class="gt-main">
    <header class="gt-topbar gt-topbar-compact">
      <button class="gt-hamburger" id="hamburger-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>

      {{-- Page Title --}}
      <div class="gt-topbar-title">@yield('page-title','Dashboard')</div>

      <div style="flex:1;"></div>

      {{-- Right Side --}}
      <div class="gt-topbar-right">

        {{-- Session Switcher --}}
        @if(!empty($allSessions) && $allSessions->isNotEmpty())
        <form method="POST" action="{{ route('institute.sessions.switch') }}" id="topbar-session-form" style="display:flex;align-items:center;">
          @csrf
          <div style="position:relative;display:flex;align-items:center;gap:5px;background:var(--bg-3);border:1px solid var(--border-2);border-radius:7px;padding:4px 8px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" style="flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <select name="session_id" class="gt-topbar-session-select"
              onchange="document.getElementById('topbar-session-form').submit()">
              @foreach($allSessions as $sess)
                <option value="{{ $sess->id }}" {{ $selectedSessId == $sess->id ? 'selected' : '' }}>
                  {{ $sess->name }}{{ $sess->is_active ? ' ●' : '' }}
                </option>
              @endforeach
            </select>
          </div>
        </form>
        @endif

        {{-- Clock --}}
        <div class="gt-clock-widget" style="padding:4px 12px;">
          <span id="clock-time" style="font-size:12px;">00:00:00</span>
          <span class="clock-ampm" id="clock-ampm" style="font-size:8px;">AM</span>
        </div>

        {{-- Fullscreen --}}
        <button class="gt-topbar-iconbtn" onclick="toggleFullscreen()" title="Fullscreen">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
        </button>

        {{-- User Info --}}
        @php
          $__topbarUser = Auth::guard('institute')->user();
          $__displayName = $__topbarUser->institute?->name ?? $__topbarUser->email ?? 'Admin';
          $__roleLabel   = $__topbarUser->role === 'institute_head' ? 'Head' : ucfirst(str_replace('_',' ', $__topbarUser->role ?? 'Staff'));
        @endphp
        <div class="gt-topbar-user">
          <div class="gt-topbar-avatar">{{ strtoupper(substr($__displayName, 0, 1)) }}</div>
          <div style="min-width:0;">
            <div class="gt-topbar-username">{{ Str::limit($__displayName, 18) }}</div>
            <div style="font-size:10px;color:var(--text-3);line-height:1;">{{ $__roleLabel }}</div>
          </div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" data-no-spinner="1">
          @csrf
          <button type="submit" class="gt-topbar-logout-btn">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
          </button>
        </form>

        @yield('topbar-actions')
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@stack('scripts')
<script>
/* ── Global form submit spinner ── */
document.addEventListener('submit', function (e) {
  var form = e.target;
  if (form.dataset.noSpinner) return;

  var btn = form.querySelector('button[type="submit"]:not([data-no-spinner])');
  if (!btn || btn.disabled) return;

  var label = btn.dataset.loadingText || 'Please wait…';
  btn.disabled = true;
  btn.innerHTML = '<span class="gt-btn-spinner"></span>' + label;

  // Safety fallback: re-enable after 20s in case of server error redirect
  setTimeout(function () {
    btn.disabled = false;
  }, 20000);
}, true);
</script>
</body>
</html>
