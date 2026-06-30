@extends('layouts.franchise')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
.fr-hero{
  background:linear-gradient(135deg,#0f172a 0%,#2d1810 60%,#1a0f08 100%);
  border-radius:20px;padding:28px 32px;margin-bottom:22px;
  display:flex;justify-content:space-between;align-items:center;
  flex-wrap:wrap;gap:20px;position:relative;overflow:hidden;
}
.fr-hero::before{
  content:'';position:absolute;right:-60px;top:-60px;
  width:240px;height:240px;border-radius:50%;
  background:radial-gradient(circle,rgba(249,115,22,.14),transparent 70%);
  pointer-events:none;
}
.fr-hero-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(249,115,22,.15);border:1px solid rgba(251,146,60,.25);
  color:#fb923c;font-size:10px;font-weight:800;letter-spacing:.12em;
  text-transform:uppercase;padding:4px 12px;border-radius:999px;margin-bottom:10px;
}
.fr-hero-name{font-size:28px;font-weight:900;color:#fff;line-height:1.2;}
.fr-hero-inst{font-size:13px;color:rgba(255,255,255,.55);margin-top:4px;}
.fr-hero-id{
  margin-top:14px;display:inline-flex;align-items:center;gap:8px;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
  border-radius:10px;padding:7px 14px;
}
.fr-hero-id span{font-size:11px;color:rgba(255,255,255,.45);}
.fr-hero-id strong{font-size:13px;color:#fff;font-family:monospace;letter-spacing:.06em;}

.fr-balance-box{
  text-align:right;background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:18px 24px;
}
.fr-balance-label{font-size:10px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.1em;}
.fr-balance-amount{font-size:36px;font-weight:900;color:#fb923c;font-family:monospace;margin:4px 0;}
.fr-balance-sub{font-size:11px;color:rgba(255,255,255,.35);}

.fr-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:14px;margin-bottom:22px;}
.fr-stat{
  background:var(--bg-2);border:1px solid var(--border);border-radius:16px;
  padding:16px 18px;display:flex;align-items:center;gap:14px;
}
.fr-stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;}
.fr-stat-icon.orange{background:rgba(234,88,12,.12);color:#ea580c;}
.fr-stat-icon.green{background:rgba(22,163,74,.12);color:#16a34a;}
.fr-stat-icon.amber{background:rgba(245,158,11,.12);color:#f59e0b;}
.fr-stat-icon.blue{background:rgba(138,115,245,.12);color:#8a73f5;}
.fr-stat-icon.purple{background:rgba(139,92,246,.12);color:#8b5cf6;}
.fr-stat-value{font-size:20px;font-weight:800;color:var(--text-1);}
.fr-stat-label{font-size:11px;color:var(--text-3);margin-top:2px;text-transform:uppercase;letter-spacing:.06em;}

.fr-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
.fr-card{background:var(--bg-2);border:1px solid var(--border);border-radius:18px;overflow:hidden;margin-bottom:20px;}
.fr-card-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.fr-card-title{font-size:14px;font-weight:700;color:var(--text-1);}
.fr-card-sub{font-size:11px;color:var(--text-3);}

.fr-tbl{width:100%;border-collapse:collapse;font-size:13px;}
.fr-tbl th{background:var(--bg-3);padding:10px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);white-space:nowrap;}
.fr-tbl td{padding:11px 16px;border-bottom:1px solid var(--border);vertical-align:middle;color:var(--text-1);}
.fr-tbl tbody tr:last-child td{border-bottom:none;}
.fr-tbl tbody tr:hover td{background:var(--bg-3);}

.badge-open{display:inline-block;padding:2px 9px;border-radius:6px;font-size:10.5px;font-weight:700;background:rgba(245,158,11,.12);color:#f59e0b;border:1px solid rgba(245,158,11,.2);}
.badge-run{display:inline-block;padding:2px 9px;border-radius:6px;font-size:10.5px;font-weight:700;background:rgba(22,163,74,.12);color:#16a34a;border:1px solid rgba(22,163,74,.2);}
.badge-cr{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;background:#f0fdf4;color:#15803d;}
.badge-dr{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;background:#fef2f2;color:#b91c1c;}
.badge-mode{display:inline-block;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;background:var(--bg-3);color:var(--text-2);border:1px solid var(--border);}
.amt-cr{color:#16a34a;font-weight:700;font-family:monospace;}
.amt-dr{color:#dc2626;font-weight:700;font-family:monospace;}
.amt-bal{font-family:monospace;font-weight:600;}

@media(max-width:900px){.fr-grid{grid-template-columns:1fr;}}
@media(max-width:768px){
  .fr-hero{padding:20px;}
  .fr-balance-amount{font-size:26px;}
  .fr-hero-name{font-size:22px;}
}
</style>
@endpush

@section('content')
@php
  $balance     = (float) ($franchise->wallet?->balance ?? 0);
  $totalCredit = \App\Models\FranchiseTransaction::where('franchise_id', $franchise->id)->sum('credit');
  $totalDebit  = \App\Models\FranchiseTransaction::where('franchise_id', $franchise->id)->sum('debit');
@endphp

{{-- Hero --}}
<div class="fr-hero">
  <div class="fr-hero-left">
    <div class="fr-hero-badge">
      <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
      Active Partner
    </div>
    <div class="fr-hero-name">{{ $franchise->name }}</div>
    <div class="fr-hero-inst">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      {{ $franchise->institute?->name ?? 'Institute' }}
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;">
      <div class="fr-hero-id">
        <span>Franchise ID</span>
        <strong>{{ $franchise->unique_id }}</strong>
      </div>
      @if($franchise->level)
      <div class="fr-hero-id">
        <span>Level</span>
        <strong>{{ $franchise->level->name }}</strong>
      </div>
      @endif
      @if($franchise->city)
      <div class="fr-hero-id">
        <span>City</span>
        <strong>{{ $franchise->city }}</strong>
      </div>
      @endif
    </div>
  </div>

  <div class="fr-balance-box">
    <div class="fr-balance-label">Wallet Balance</div>
    <div class="fr-balance-amount">₹{{ number_format($balance, 2) }}</div>
    <div class="fr-balance-sub">
      @if($balance <= 0)
        <span style="color:#f87171;">Insufficient — contact institute</span>
      @elseif($balance < 1000)
        <span style="color:#fbbf24;">Low balance</span>
      @else
        <span style="color:#fb923c;">Balance OK</span>
      @endif
      &nbsp;·&nbsp; {{ now()->format('d M Y') }}
    </div>
  </div>
</div>

{{-- Stats Grid --}}
<div class="fr-stats">
  <div class="fr-stat">
    <div class="fr-stat-icon orange">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div>
      <div class="fr-stat-value" style="font-family:monospace;">₹{{ number_format($balance, 0) }}</div>
      <div class="fr-stat-label">Wallet Balance</div>
    </div>
  </div>

  <div class="fr-stat">
    <div class="fr-stat-icon blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
    </div>
    <div>
      <div class="fr-stat-value">{{ $totalStudents }}</div>
      <div class="fr-stat-label">Total Students</div>
    </div>
  </div>

  <div class="fr-stat">
    <div class="fr-stat-icon green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
    </div>
    <div>
      <div class="fr-stat-value">{{ $runningCount }}</div>
      <div class="fr-stat-label">Active Admissions</div>
    </div>
  </div>

  <div class="fr-stat">
    <div class="fr-stat-icon amber">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div>
      <div class="fr-stat-value">{{ $pendingCount }}</div>
      <div class="fr-stat-label">Pending</div>
    </div>
  </div>

  <div class="fr-stat">
    <div class="fr-stat-icon purple">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
    </div>
    <div>
      <div class="fr-stat-value" style="font-family:monospace;font-size:16px;">₹{{ number_format($totalCredit, 0) }}</div>
      <div class="fr-stat-label">Total Credited</div>
    </div>
  </div>

  <div class="fr-stat">
    <div class="fr-stat-icon" style="background:rgba(220,38,38,.1);color:#dc2626;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
    </div>
    <div>
      <div class="fr-stat-value" style="font-family:monospace;font-size:16px;">₹{{ number_format($totalDebit, 0) }}</div>
      <div class="fr-stat-label">Total Debited</div>
    </div>
  </div>
</div>

{{-- Two-column: Recent Students + Recent Transactions --}}
<div class="fr-grid">

  {{-- Recent Students --}}
  <div class="fr-card" style="margin-bottom:0;">
    <div class="fr-card-head">
      <div>
        <div class="fr-card-title">Recent Students</div>
        <div class="fr-card-sub">Last {{ $recentStudents->count() }} admissions</div>
      </div>
      <a href="{{ route('franchise.students.index') }}" style="font-size:11px;color:#fb923c;text-decoration:none;">View All →</a>
    </div>
    <div style="overflow-x:auto;">
      <table class="fr-tbl">
        <thead>
          <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentStudents as $cb)
          <tr>
            <td>
              <div style="font-weight:600;font-size:12.5px;">{{ $cb->student?->profile?->name ?? '—' }}</div>
              <div style="font-size:10.5px;color:var(--text-3);font-family:monospace;">{{ $cb->student?->user_id }}</div>
            </td>
            <td style="font-size:12px;max-width:110px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $cb->course?->name ?? '—' }}</td>
            <td>
              @if($cb->status === 'RUN')
                <span class="badge-run">Active</span>
              @else
                <span class="badge-open">Pending</span>
              @endif
            </td>
            <td style="font-size:11px;color:var(--text-3);white-space:nowrap;">
              {{ \Carbon\Carbon::parse($cb->book_date)->format('d M') }}
            </td>
          </tr>
          @empty
          <tr><td colspan="4" style="text-align:center;padding:28px;color:var(--text-3);font-size:12.5px;">No admissions yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Recent Transactions --}}
  <div class="fr-card" style="margin-bottom:0;">
    <div class="fr-card-head">
      <div>
        <div class="fr-card-title">Recent Wallet Transactions</div>
        <div class="fr-card-sub">Last {{ $recentTransactions->count() }} entries</div>
      </div>
      <a href="{{ route('franchise.wallet') }}" style="font-size:11px;color:#fb923c;text-decoration:none;">Full Ledger →</a>
    </div>
    <div style="overflow-x:auto;">
      <table class="fr-tbl">
        <thead>
          <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Type</th>
            <th style="text-align:right;">Balance</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentTransactions as $txn)
          <tr>
            <td style="white-space:nowrap;color:var(--text-2);font-size:11.5px;">{{ \Carbon\Carbon::parse($txn->date)->format('d M') }}</td>
            <td style="max-width:140px;font-size:12px;">
              <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($txn->description, 30) }}</div>
            </td>
            <td>
              @if($txn->credit > 0)
                <span class="badge-cr" style="font-size:10px;padding:2px 7px;">↑ Credit</span>
              @else
                <span class="badge-dr" style="font-size:10px;padding:2px 7px;">↓ Debit</span>
              @endif
            </td>
            <td style="text-align:right;" class="amt-bal">
              <span style="{{ (float)$txn->cl_bal < 0 ? 'color:#dc2626' : 'color:#fb923c' }};font-size:12px;">
                ₹{{ number_format($txn->cl_bal, 2) }}
              </span>
            </td>
          </tr>
          @empty
          <tr><td colspan="4" style="text-align:center;padding:28px;color:var(--text-3);font-size:12.5px;">No transactions yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
