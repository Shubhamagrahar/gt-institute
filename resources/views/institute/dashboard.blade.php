@extends('layouts.institute')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('content')

{{-- Welcome Banner --}}
<div class="gt-welcome-banner">
  <div style="z-index:1;">
    <div class="gt-welcome-title">Welcome back 👋</div>
    <div class="gt-welcome-sub">{{ auth()->user()->institute?->name ?? 'Institute' }} &nbsp;·&nbsp; {{ now()->format('l, d M Y') }}</div>
  </div>
  <div class="gt-welcome-actions">
    <a href="{{ route('institute.students.create') }}" class="btn-banner">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
      New Admission
    </a>
    <a href="{{ route('institute.fee.index') }}" class="btn-banner">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      Fee Dues
    </a>
  </div>
</div>

{{-- Stats --}}
<div class="gt-stats" style="grid-template-columns:repeat(auto-fit,minmax(155px,1fr));">
  <div class="gt-stat">
    <div class="gt-stat-icon purple">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">{{ $stats['total_students'] }}</div>
      <div class="gt-stat-label">Enrolled</div>
      <div class="gt-stat-sub">This session</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon red">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div>
      <div class="gt-stat-value mono" style="font-size:17px;">₹{{ number_format($stats['total_fee_due'] ?? 0, 0) }}</div>
      <div class="gt-stat-label">Total Due</div>
      <div class="gt-stat-sub">Pending fees</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    </div>
    <div>
      <div class="gt-stat-value mono" style="font-size:17px;">₹{{ number_format($stats['student_wallet_balance'] ?? 0, 0) }}</div>
      <div class="gt-stat-label">Student Wallet</div>
      <div class="gt-stat-sub">Institute earnings balance</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon teal">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <div>
      <div class="gt-stat-value mono" style="font-size:17px;">₹{{ number_format($stats['fee_today'] ?? 0, 0) }}</div>
      <div class="gt-stat-label">Today</div>
      <div class="gt-stat-sub">Collection</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon orange">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <div>
      <div class="gt-stat-value mono" style="font-size:17px;">₹{{ number_format($stats['fee_this_month'], 0) }}</div>
      <div class="gt-stat-label">This Month</div>
      <div class="gt-stat-sub">Collection</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">{{ $stats['total_staff'] }}</div>
      <div class="gt-stat-label">Staff</div>
      <div class="gt-stat-sub">{{ $stats['total_staff'] }} members</div>
    </div>
  </div>
</div>

{{-- Content Grid --}}
<div class="gt-grid-2" style="gap:20px;">

  {{-- Recent Students --}}
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;opacity:.6;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        Recent Students
      </div>
      <a href="{{ route('institute.students.index') }}" class="btn btn-outline btn-sm">View All</a>
    </div>
    @forelse($recentStudents as $s)
    <div class="flex items-center gap-3" style="padding:9px 0;border-bottom:1px solid var(--border);">
      <div style="width:34px;height:34px;border-radius:50%;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--accent);flex-shrink:0;">
        {{ strtoupper(substr($s->name,0,1)) }}
      </div>
      <div class="flex-1" style="min-width:0;">
        <div style="font-size:13px;font-weight:600;">{{ $s->name }}</div>
        <div class="text-xs text-muted">{{ $s->user_id }} · {{ $s->mobile }}</div>
      </div>
      <span class="badge {{ $s->status==='active'?'badge-success':'badge-neutral' }}">{{ ucfirst($s->status) }}</span>
    </div>
    @empty
    <div class="gt-empty">
      <div class="gt-empty-icon">👨‍🎓</div>
      <div class="gt-empty-title">No students yet</div>
      <a href="{{ route('institute.students.create') }}" class="btn btn-primary btn-sm" style="margin-top:8px;">Add First Student</a>
    </div>
    @endforelse
  </div>

  {{-- Quick Actions --}}
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;opacity:.6;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        Quick Actions
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
      <a href="{{ route('institute.students.create') }}" class="gt-quick-action">
        <div class="qa-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        </div>
        <span class="qa-label">New Admission</span>
      </a>
      <a href="{{ route('institute.students.index') }}" class="gt-quick-action">
        <div class="qa-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <span class="qa-label">Student List</span>
      </a>
      <a href="{{ route('institute.fee.index') }}" class="gt-quick-action">
        <div class="qa-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <span class="qa-label">Fee Dues</span>
      </a>
      <a href="{{ route('institute.courses.create') }}" class="gt-quick-action">
        <div class="qa-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <span class="qa-label">Add Course</span>
      </a>
      <a href="{{ route('institute.fee.index') }}" class="gt-quick-action">
        <div class="qa-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <span class="qa-label">Fee Report</span>
      </a>
      <a href="{{ route('institute.staff.create') }}" class="gt-quick-action">
        <div class="qa-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <span class="qa-label">Add Staff</span>
      </a>
    </div>

    @if($institute->subscription)
    <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
      <span class="text-xs text-muted">Active Plan</span>
      <div style="display:flex;align-items:center;gap:8px;">
        <span class="badge badge-accent">{{ $institute->subscription->plan->name }}</span>
        <span class="text-xs text-muted">Expires {{ \Carbon\Carbon::parse($institute->subscription->end_date)->format('d M Y') }}</span>
      </div>
    </div>
    @endif
  </div>
</div>

@endsection
