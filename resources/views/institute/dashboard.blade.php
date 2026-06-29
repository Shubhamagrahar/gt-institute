@extends('layouts.institute')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('content')

{{-- ── ERP Welcome Banner ───────────────────────────────────────────────── --}}
@php
  $hour = now()->hour;
  $greeting = $hour < 12 ? 'GOOD MORNING' : ($hour < 17 ? 'GOOD AFTERNOON' : 'GOOD EVENING');
  $__bUser = Auth::guard('institute')->user();
  $userName = $__bUser->institute?->name ?? $__bUser->email ?? 'Admin';
@endphp
<div class="gt-erp-banner">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;position:relative;z-index:1;">
    <div>
      <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;opacity:.7;margin-bottom:3px;">{{ $greeting }}</div>
      <div style="font-size:24px;font-weight:700;line-height:1.15;margin-bottom:8px;">{{ $userName }}</div>
      <div style="display:flex;align-items:center;gap:14px;font-size:12px;opacity:.8;flex-wrap:wrap;">
        <span style="display:flex;align-items:center;gap:4px;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          {{ now()->format('d M Y, D') }}
        </span>
        @if(isset($activeSession) && $activeSession)
        <span style="display:flex;align-items:center;gap:4px;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          {{ $activeSession->name }}
        </span>
        @endif
        <span style="display:flex;align-items:center;gap:4px;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/></svg>
          {{ $institute->name }}
        </span>
      </div>
    </div>
    <div style="width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;flex-shrink:0;border:2px solid rgba(255,255,255,.3);">
      {{ strtoupper(substr($userName, 0, 1)) }}
    </div>
  </div>

  {{-- Banner Stats --}}
  <div class="gt-erp-banner-stats">
    <div class="gt-erp-banner-stat">
      <div class="gt-erp-banner-stat-label">Today's Collection</div>
      <div class="gt-erp-banner-stat-value" style="font-family:var(--font-mono);">₹{{ number_format($stats['fee_today'],0) }}</div>
    </div>
    <div class="gt-erp-banner-stat">
      <div class="gt-erp-banner-stat-label">This Month</div>
      <div class="gt-erp-banner-stat-value" style="font-family:var(--font-mono);">₹{{ number_format($stats['fee_this_month'],0) }}</div>
    </div>
    <div class="gt-erp-banner-stat">
      <div class="gt-erp-banner-stat-label">Total Students</div>
      <div class="gt-erp-banner-stat-value">{{ $stats['total_students'] }}</div>
    </div>
    <div class="gt-erp-banner-stat">
      <div class="gt-erp-banner-stat-label">Pending Admissions</div>
      <div class="gt-erp-banner-stat-value" style="color:#fcd34d;">{{ $stats['enrollments_open'] }}</div>
    </div>
  </div>
</div>

{{-- ── Today's Follow-up Alert ─────────────────────────────────────────── --}}
@if($enquiryStats['enquiryDueToday'] > 0 || $enquiryStats['enquiryOverdue'] > 0)
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;
            background:#fffbeb;border:1.5px solid #f59e0b;border-radius:12px;
            padding:12px 18px;margin-bottom:16px;flex-wrap:wrap;">
  <div style="display:flex;align-items:center;gap:12px;">
    <div style="width:36px;height:36px;border-radius:9px;background:#f59e0b;
                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
    </div>
    <div>
      <div style="font-size:13px;font-weight:700;color:#92400e;">
        @if($enquiryStats['enquiryDueToday'] > 0)
          {{ $enquiryStats['enquiryDueToday'] }} follow-up{{ $enquiryStats['enquiryDueToday'] > 1 ? 's' : '' }} due today
          @if($enquiryStats['enquiryOverdue'] > 0) &nbsp;&middot;&nbsp; @endif
        @endif
        @if($enquiryStats['enquiryOverdue'] > 0)
          <span style="color:#dc2626;">{{ $enquiryStats['enquiryOverdue'] }} overdue</span>
        @endif
      </div>
      <div style="font-size:11.5px;color:#a16207;margin-top:2px;">
        Review and call your pending leads before the day ends.
      </div>
    </div>
  </div>
  <a href="{{ route('institute.enquiries.index', ['tab'=>'due']) }}"
     style="font-size:12px;font-weight:700;color:#92400e;border:1.5px solid #f59e0b;
            border-radius:8px;padding:6px 14px;white-space:nowrap;text-decoration:none;
            background:#fef3c7;">
    View Follow-ups &rarr;
  </a>
</div>
@endif

{{-- ── Primary Stats Row ───────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:14px;">

  {{-- Total Students --}}
  <div class="gt-stat">
    <div class="gt-stat-icon blue">
      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div style="min-width:0;">
      <div class="gt-stat-value">{{ $stats['total_students'] }}</div>
      <div class="gt-stat-label">Total Students</div>
      @if($stats['new_students_month'] > 0)
        <div style="font-size:11px;color:var(--success);margin-top:2px;font-weight:500;">+{{ $stats['new_students_month'] }} this month</div>
      @else
        <div style="font-size:10.5px;background:var(--accent-bg);color:var(--accent);border-radius:4px;padding:1px 6px;margin-top:3px;display:inline-block;font-weight:600;">Active</div>
      @endif
    </div>
  </div>

  {{-- Total Admissions (Session) --}}
  <div class="gt-stat">
    <div class="gt-stat-icon teal">
      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <div style="min-width:0;">
      <div class="gt-stat-value">{{ $stats['enrollments_run'] + $stats['enrollments_open'] }}</div>
      <div class="gt-stat-label">Total Admissions</div>
      <div style="font-size:10.5px;background:rgba(20,184,166,.1);color:#14b8a6;border-radius:4px;padding:1px 6px;margin-top:3px;display:inline-block;font-weight:600;">Session</div>
    </div>
  </div>

  {{-- Active Running --}}
  <div class="gt-stat">
    <div class="gt-stat-icon green">
      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
    </div>
    <div style="min-width:0;">
      <div class="gt-stat-value">{{ $stats['enrollments_run'] }}</div>
      <div class="gt-stat-label">Active Running</div>
      @if($stats['month_growth'] !== null)
        <div style="font-size:11px;font-weight:500;margin-top:2px;color:{{ $stats['month_growth'] >= 0 ? 'var(--success)' : 'var(--danger)' }};">
          {{ $stats['month_growth'] >= 0 ? '+' : '' }}{{ $stats['month_growth'] }}% revenue
        </div>
      @else
        <div class="gt-stat-sub">Enrolled</div>
      @endif
    </div>
  </div>

  {{-- Month Collection --}}
  <div class="gt-stat">
    <div class="gt-stat-icon green">
      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div style="min-width:0;">
      <div class="gt-stat-value mono" style="font-size:16px;">₹{{ number_format($stats['fee_this_month'],0) }}</div>
      <div class="gt-stat-label">This Month</div>
      <div class="gt-stat-sub">{{ now()->format('M Y') }}</div>
    </div>
  </div>

</div>

{{-- ── Secondary Stats Row ──────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">

  <div class="gt-stat" style="border-left:3px solid var(--warning);">
    <div class="gt-stat-icon orange">
      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">{{ $stats['enrollments_open'] }}</div>
      <div class="gt-stat-label">Pending Approvals</div>
      @if($stats['enrollments_open'] > 0)
        <div style="font-size:10.5px;color:var(--warning);margin-top:2px;font-weight:600;">Action needed</div>
      @else
        <div class="gt-stat-sub">All clear</div>
      @endif
    </div>
  </div>

  <div class="gt-stat" style="border-left:3px solid var(--info);">
    <div class="gt-stat-icon blue">
      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">{{ $stats['total_staff'] }}</div>
      <div class="gt-stat-label">Staff Members</div>
      <div class="gt-stat-sub">Active</div>
    </div>
  </div>

  <div class="gt-stat" style="border-left:3px solid var(--success);">
    <div class="gt-stat-icon green">
      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">{{ $stats['active_courses'] }}</div>
      <div class="gt-stat-label">Active Courses</div>
      <div class="gt-stat-sub">Running</div>
    </div>
  </div>

  <div class="gt-stat" style="border-left:3px solid var(--accent);">
    <div class="gt-stat-icon purple">
      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7H3v10h18V7z"/><path d="M17 12h.01"/><path d="M3 9h18"/></svg>
    </div>
    <div style="min-width:0;">
      <div class="gt-stat-value mono" style="font-size:16px;">₹{{ number_format($stats['student_wallet_balance'],0) }}</div>
      <div class="gt-stat-label">Wallet Balance</div>
      <div class="gt-stat-sub">Total collected</div>
    </div>
  </div>

</div>

{{-- ── Enquiry Pipeline ─────────────────────────────────────────────────── --}}
<div class="gt-card" style="padding:0;overflow:hidden;margin-bottom:18px;">
  <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
    <div>
      <div class="gt-card-title">Enquiry Pipeline</div>
      <div style="font-size:11.5px;color:var(--text-3);margin-top:2px;">
        Conversion rate:
        <strong style="color:{{ $enquiryStats['conversionRate'] >= 50 ? 'var(--success)' : ($enquiryStats['conversionRate'] >= 25 ? '#f59e0b' : 'var(--danger)') }};">
          {{ $enquiryStats['conversionRate'] }}%
        </strong>
        &nbsp;&middot;&nbsp; {{ $enquiryStats['enquiryTotal'] }} total enquiries
      </div>
    </div>
    <a href="{{ route('institute.enquiries.index') }}" class="btn btn-outline btn-xs">View All</a>
  </div>
  <div style="display:grid;grid-template-columns:repeat(5,1fr);">
    @php
      $pipelineCols = [
        ['label'=>'Open',        'value'=>$enquiryStats['enquiryOpen'],      'color'=>'#2563eb', 'tab'=>'open'],
        ['label'=>'Due Today',   'value'=>$enquiryStats['enquiryDueToday'],  'color'=>'#f59e0b', 'tab'=>'due'],
        ['label'=>'Overdue',     'value'=>$enquiryStats['enquiryOverdue'],   'color'=>'#ef4444', 'tab'=>'due'],
        ['label'=>'Converted',   'value'=>$enquiryStats['enquiryConverted'], 'color'=>'#10b981', 'tab'=>'converted'],
        ['label'=>'Lost',        'value'=>$enquiryStats['enquiryLost'],      'color'=>'#94a3b8', 'tab'=>'lost'],
      ];
    @endphp
    @foreach($pipelineCols as $i => $col)
    <a href="{{ route('institute.enquiries.index', ['tab'=>$col['tab']]) }}"
       style="display:block;padding:16px 18px;border-right:{{ $i < 4 ? '1px solid var(--border)' : 'none' }};
              text-decoration:none;transition:.12s;text-align:center;"
       onmouseover="this.style.background='var(--bg-3)'"
       onmouseout="this.style.background=''">
      <div style="font-size:24px;font-weight:800;color:{{ $col['color'] }};">{{ $col['value'] }}</div>
      <div style="font-size:11px;color:var(--text-2);margin-top:3px;font-weight:600;">{{ $col['label'] }}</div>
    </a>
    @endforeach
  </div>
  @if($enquiryStats['enquiryTotal'] > 0)
  <div style="height:6px;background:var(--bg-4);display:flex;overflow:hidden;">
    @php
      $convPct = $enquiryStats['conversionRate'];
      $lostPct = $enquiryStats['enquiryTotal'] > 0 ? round(($enquiryStats['enquiryLost']/$enquiryStats['enquiryTotal'])*100) : 0;
      $openPct = 100 - $convPct - $lostPct;
    @endphp
    <div style="width:{{ $openPct }}%;background:#2563eb;"></div>
    <div style="width:{{ $convPct }}%;background:#10b981;"></div>
    <div style="width:{{ $lostPct }}%;background:#ef4444;"></div>
  </div>
  @endif
</div>

{{-- ── Charts Row ───────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1.7fr 1fr;gap:18px;margin-bottom:18px;">

  {{-- Revenue Bar Chart --}}
  <div class="gt-card" style="padding:18px 20px;">
    <div class="gt-card-header" style="margin-bottom:14px;">
      <div>
        <div class="gt-card-title">Monthly Revenue</div>
        <div style="font-size:11.5px;color:var(--text-3);margin-top:2px;">Last 6 months fee collection</div>
      </div>
      <div style="text-align:right;">
        <div style="font-size:13px;font-weight:700;color:var(--text);font-family:var(--font-mono);">
          ₹{{ number_format($stats['fee_this_month'],0) }}
        </div>
        <div style="font-size:11px;color:var(--text-3);">{{ now()->format('M Y') }}</div>
      </div>
    </div>
    <div style="position:relative;height:200px;">
      <canvas id="revenueChart"></canvas>
    </div>
  </div>

  {{-- Enrollment Donut --}}
  <div class="gt-card" style="padding:18px 20px;">
    <div class="gt-card-header" style="margin-bottom:14px;">
      <div>
        <div class="gt-card-title">Enrollment Status</div>
        <div style="font-size:11.5px;color:var(--text-3);margin-top:2px;">Active vs pending</div>
      </div>
    </div>
    <div style="position:relative;height:160px;display:flex;align-items:center;justify-content:center;">
      <canvas id="enrollmentChart"></canvas>
    </div>
    <div style="display:flex;justify-content:center;gap:20px;margin-top:14px;">
      <div style="text-align:center;">
        <div style="font-size:18px;font-weight:700;color:#10b981;">{{ $stats['enrollments_run'] }}</div>
        <div style="display:flex;align-items:center;gap:5px;margin-top:2px;">
          <span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block;"></span>
          <span style="font-size:11px;color:var(--text-2);">Active</span>
        </div>
      </div>
      <div style="width:1px;background:var(--border);"></div>
      <div style="text-align:center;">
        <div style="font-size:18px;font-weight:700;color:#f59e0b;">{{ $stats['enrollments_open'] }}</div>
        <div style="display:flex;align-items:center;gap:5px;margin-top:2px;">
          <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
          <span style="font-size:11px;color:var(--text-2);">Pending</span>
        </div>
      </div>
      <div style="width:1px;background:var(--border);"></div>
      <div style="text-align:center;">
        <div style="font-size:18px;font-weight:700;color:var(--text);">{{ $stats['enrollments_run'] + $stats['enrollments_open'] }}</div>
        <div style="font-size:11px;color:var(--text-2);margin-top:2px;">Total</div>
      </div>
    </div>
  </div>

</div>

{{-- ── Data Tables Row ──────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;">

  {{-- Recent Fee Collections --}}
  <div class="gt-card" style="padding:0;overflow:hidden;">
    <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
      <div class="gt-card-title">Recent Collections</div>
      <a href="{{ route('institute.fees-dashboard') }}" class="btn btn-outline btn-xs">View All</a>
    </div>
    @forelse($recentFees as $fee)
    @php
      $feeStudent = $feeUserMap[$fee->user_id] ?? null;
      $feeName = $feeStudent?->profile?->name ?? $feeStudent?->user_id ?? 'Student';
      $feeAmount = $fee->amount ?? $fee->amt ?? 0;
    @endphp
    <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--border);">
      <div style="width:32px;height:32px;border-radius:8px;background:var(--success-bg);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--success);flex-shrink:0;">
        {{ strtoupper(substr($feeName,0,1)) }}
      </div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:12.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $feeName }}</div>
        <div style="font-size:11px;color:var(--text-2);">{{ $fee->payment_mode }} · {{ \Carbon\Carbon::parse($fee->date)->format('d M') }}</div>
      </div>
      <div style="font-size:13px;font-weight:700;color:var(--success);font-family:var(--font-mono);flex-shrink:0;">
        +₹{{ number_format($feeAmount,0) }}
      </div>
    </div>
    @empty
    <div class="gt-empty" style="padding:28px 16px;">
      <div class="gt-empty-title">No collections yet</div>
    </div>
    @endforelse
  </div>

  {{-- Course-wise Enrollments --}}
  <div class="gt-card" style="padding:0;overflow:hidden;">
    <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
      <div class="gt-card-title">Course Enrollments</div>
      <a href="{{ route('institute.courses.enrollments') }}" class="btn btn-outline btn-xs">View All</a>
    </div>
    @php $maxEnroll = $courseEnrollments->max('total') ?: 1; @endphp
    @forelse($courseEnrollments as $ce)
    <div style="padding:10px 16px;border-bottom:1px solid var(--border);">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
        <div style="font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:65%;">
          {{ $ce['name'] }}
        </div>
        <div style="display:flex;gap:6px;flex-shrink:0;">
          <span style="font-size:11px;color:var(--success);font-weight:600;">{{ $ce['run'] }} active</span>
          @if($ce['open'] > 0)
            <span style="font-size:11px;color:var(--warning);">{{ $ce['open'] }} pending</span>
          @endif
        </div>
      </div>
      <div style="height:4px;background:var(--bg-4);border-radius:99px;overflow:hidden;">
        <div style="height:100%;border-radius:inherit;background:var(--accent);width:{{ round(($ce['total']/$maxEnroll)*100) }}%;"></div>
      </div>
    </div>
    @empty
    <div class="gt-empty" style="padding:28px 16px;">
      <div class="gt-empty-title">No enrollments yet</div>
    </div>
    @endforelse
  </div>

</div>

{{-- ── Emergency Login Code ─────────────────────────────────────────────── --}}
@if(auth()->user()->role === 'institute_head')
<div class="gt-card" style="border-color:rgba(251,191,36,.2);">
  <div class="gt-card-header" style="display:flex;align-items:center;gap:10px;">
    <div style="width:32px;height:32px;border-radius:8px;background:rgba(251,191,36,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    </div>
    <div class="flex-1">
      <div class="gt-card-title" style="color:#fbbf24;">Emergency Login Code</div>
      <div class="text-xs text-muted">For staff / students when OTP email is not received</div>
    </div>
  </div>

  @if(session('emergency_code'))
    <div style="background:rgba(251,191,36,.07);border:1px dashed rgba(251,191,36,.3);border-radius:10px;padding:18px;text-align:center;margin-bottom:12px;">
      <div style="font-size:10px;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px;">Today's Code — {{ now()->format('d M Y') }}</div>
      <div style="font-family:var(--font-mono);font-size:30px;font-weight:800;letter-spacing:10px;color:#fbbf24;">
        {{ session('emergency_code') }}
      </div>
      <div style="font-size:11px;color:rgba(255,255,255,.35);margin-top:6px;">Rotates daily at midnight · Share verbally only</div>
    </div>
    <div style="font-size:12px;color:rgba(255,255,255,.4);line-height:1.7;">
      Tell this code to your staff or student over the phone. Do <strong style="color:#f87171;">not</strong> share via chat or email.
    </div>
  @else
    <form method="POST" action="{{ route('institute.accounts.emergency-code') }}">
      @csrf
      <div style="font-size:12.5px;color:var(--text-2);margin-bottom:12px;line-height:1.6;">
        Enter your password to reveal today's emergency code.
      </div>
      <div style="display:flex;gap:10px;align-items:flex-start;">
        <div style="flex:1;">
          <input type="password" name="password"
            class="gt-input @error('password') is-invalid @enderror"
            placeholder="Your account password" autocomplete="current-password">
          @error('password')
            <div class="gt-error" style="margin-top:5px;">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn" style="background:rgba(251,191,36,.12);color:#fbbf24;border:1px solid rgba(251,191,36,.25);white-space:nowrap;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          Show Code
        </button>
      </div>
    </form>
  @endif
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
  const accent  = '#2563eb';
  const accentL = 'rgba(37,99,235,.12)';
  const green   = '#10b981';
  const amber   = '#f59e0b';
  const gridCol = 'rgba(26,31,60,.07)';
  const textCol = '#6b7280';

  Chart.defaults.font.family = "'Inter', sans-serif";
  Chart.defaults.font.size   = 11;

  /* ── Revenue Bar Chart ── */
  const revLabels  = @json($monthlyRevenue->pluck('label'));
  const revAmounts = @json($monthlyRevenue->pluck('amount'));

  new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
      labels: revLabels,
      datasets: [{
        label: 'Collection (₹)',
        data: revAmounts,
        backgroundColor: revAmounts.map((v, i) =>
          i === revAmounts.length - 1 ? accent : accentL),
        borderColor: revAmounts.map((v, i) =>
          i === revAmounts.length - 1 ? accent : 'rgba(108,93,211,.35)'),
        borderWidth: 1.5,
        borderRadius: 5,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ' ₹' + ctx.parsed.y.toLocaleString('en-IN')
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: textCol }
        },
        y: {
          grid: { color: gridCol },
          ticks: {
            color: textCol,
            callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v)
          },
          beginAtZero: true
        }
      }
    }
  });

  /* ── Enrollment Donut ── */
  const runCount  = {{ $stats['enrollments_run'] }};
  const openCount = {{ $stats['enrollments_open'] }};
  const total     = runCount + openCount;

  new Chart(document.getElementById('enrollmentChart'), {
    type: 'doughnut',
    data: {
      labels: ['Active (RUN)', 'Pending (OPEN)'],
      datasets: [{
        data: total > 0 ? [runCount, openCount] : [1, 0],
        backgroundColor: total > 0 ? [green, amber] : ['#e5e7ef'],
        borderWidth: 0,
        hoverOffset: 4,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ' ' + ctx.label + ': ' + ctx.parsed
          }
        }
      }
    }
  });

})();
</script>
@endpush
