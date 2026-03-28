@extends('layouts.owner')
@section('title','Institutes')
@section('page-title','Institutes')
@section('topbar-actions')
  <a href="{{ route('owner.institutes.create') }}" class="btn btn-primary btn-sm">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Institute
  </a>
@endsection
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Institutes ({{ $institutes->count() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr>
        <th>ID</th><th>Institute</th><th>Owner</th><th>Plan</th><th>Balance</th><th>Status</th><th>Actions</th>
      </tr></thead>
      <tbody>
        @forelse($institutes as $inst)
        <tr>
          <td><code style="font-size:11px;color:var(--accent);">{{ $inst->unique_id }}</code></td>
          <td>
            <div class="fw-600">{{ $inst->name }}</div>
            <div class="text-xs text-muted">{{ $inst->email }}</div>
          </td>
          <td>
            <div class="text-sm">{{ $inst->owner_name }}</div>
            <div class="text-xs text-muted">{{ $inst->owner_mobile }}</div>
          </td>
          <td>
            @if($inst->subscription)
              <span class="badge badge-accent">{{ $inst->subscription->plan->name }}</span>
              <div class="text-xs text-muted" style="margin-top:3px;">Till {{ \Carbon\Carbon::parse($inst->subscription->end_date)->format('d M Y') }}</div>
            @else
              <span class="badge badge-neutral">No Plan</span>
            @endif
          </td>
          <td class="mono">
            @php $bal = $inst->wallet?->main_b ?? 0; @endphp
            <span class="{{ $bal >= 0 ? 'amount-pos' : 'amount-neg' }}">₹{{ number_format($bal,2) }}</span>
          </td>
          <td><span class="badge {{ $inst->status==='active' ? 'badge-success' : ($inst->status==='suspended' ? 'badge-warning' : 'badge-danger') }}">{{ ucfirst($inst->status) }}</span></td>
          <td>
            <div class="flex gap-2">
              <a href="{{ route('owner.institutes.show',$inst) }}" class="btn btn-outline btn-xs">View</a>
              <a href="{{ route('owner.institutes.transactions',$inst) }}" class="btn btn-outline btn-xs">Ledger</a>
              <form action="{{ route('owner.institutes.toggle',$inst) }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn btn-xs {{ $inst->status==='active' ? '' : 'btn-success' }}" style="{{ $inst->status==='active' ? 'background:var(--warning-bg);color:var(--warning);border:1px solid rgba(255,184,77,.2);' : '' }}">
                  {{ $inst->status==='active' ? 'Suspend' : 'Activate' }}
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7">
          <div class="gt-empty"><div class="gt-empty-icon">🏫</div><div class="gt-empty-title">No institutes yet</div>
          <a href="{{ route('owner.institutes.create') }}" class="btn btn-primary btn-sm">Add Institute</a></div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
