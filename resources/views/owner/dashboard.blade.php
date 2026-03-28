@extends('layouts.owner')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="gt-stats">
  <div class="gt-stat">
    <div class="gt-stat-icon yellow">🏫</div>
    <div>
      <div class="gt-stat-value">{{ $stats['total_institutes'] }}</div>
      <div class="gt-stat-label">Total Institutes</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon green">✅</div>
    <div>
      <div class="gt-stat-value">{{ $stats['active_institutes'] }}</div>
      <div class="gt-stat-label">Active Institutes</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon blue">📋</div>
    <div>
      <div class="gt-stat-value">{{ $stats['total_plans'] }}</div>
      <div class="gt-stat-label">Plans</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon red">⭐</div>
    <div>
      <div class="gt-stat-value">{{ $stats['total_features'] }}</div>
      <div class="gt-stat-label">Features</div>
    </div>
  </div>
</div>

<div class="gt-grid-2" style="gap:20px;">

  {{-- Recent Institutes --}}
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Recent Institutes</div>
      <a href="{{ route('owner.institutes.index') }}" class="btn btn-outline btn-sm">View All</a>
    </div>
    @forelse($stats['recent_institutes'] as $inst)
    <div class="flex items-center gap-3" style="padding:10px 0; border-bottom:1px solid var(--border);">
      <div class="avatar" style="width:36px;height:36px;border-radius:8px;background:var(--bg-3);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--accent);flex-shrink:0;">
        {{ strtoupper(substr($inst->name,0,2)) }}
      </div>
      <div class="flex-1" style="min-width:0;">
        <div class="fw-600 text-sm" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $inst->name }}</div>
        <div class="text-xs text-muted">{{ $inst->unique_id }} · {{ $inst->subscription?->plan?->name ?? 'No Plan' }}</div>
      </div>
      <span class="badge {{ $inst->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($inst->status) }}</span>
    </div>
    @empty
    <div class="gt-empty">
      <div class="gt-empty-icon">🏫</div>
      <div class="gt-empty-title">No institutes yet</div>
      <a href="{{ route('owner.institutes.create') }}" class="btn btn-primary btn-sm">Add First Institute</a>
    </div>
    @endforelse
  </div>

  {{-- Recent Transactions --}}
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Recent Transactions</div>
    </div>
    @forelse($stats['recent_txns'] as $txn)
    <div class="flex items-center gap-3" style="padding:9px 0; border-bottom:1px solid var(--border);">
      <div style="flex:1;min-width:0;">
        <div class="text-sm fw-600" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $txn->institute?->name }}</div>
        <div class="text-xs text-muted" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($txn->des, 45) }}</div>
      </div>
      <div class="text-sm mono" style="flex-shrink:0;">
        @if($txn->debit > 0)
          <span class="amount-neg">−₹{{ number_format($txn->debit,2) }}</span>
        @else
          <span class="amount-pos">+₹{{ number_format($txn->credit,2) }}</span>
        @endif
      </div>
    </div>
    @empty
    <div class="gt-empty">
      <div class="gt-empty-icon">💳</div>
      <div class="gt-empty-title">No transactions yet</div>
    </div>
    @endforelse
  </div>

</div>
@endsection
