@extends('layouts.institute')
@section('title', 'Franchises')
@section('page-title', 'Franchises')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.create') }}" class="btn btn-primary btn-sm">+ Add Franchise</a>
@endsection

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Franchises ({{ $franchises->count() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search franchise...">
  </div>

  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Franchise</th>
          <th>Owner</th>
          <th>Level</th>
          <th>Login ID</th>
          <th>Mode</th>
          <th>Wallet</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($franchises as $franchise)
          <tr>
            <td><code style="font-size:11px;color:var(--accent);">{{ $franchise->unique_id }}</code></td>
            <td>
              <a href="{{ route('institute.franchises.show', $franchise) }}" class="fw-600" style="color:var(--accent); text-decoration:none;">{{ $franchise->name }}</a>
              <div class="text-xs text-muted">{{ $franchise->email }}</div>
            </td>
            <td>
              <div>{{ $franchise->owner_name }}</div>
              <div class="text-xs text-muted">{{ $franchise->owner_mobile }}</div>
            </td>
            <td>
              <div>{{ $franchise->level?->name ?? 'NA' }}</div>
              <div class="text-xs text-muted">{{ number_format($franchise->commission_percent, 2) }}%</div>
            </td>
            <td><span class="mono">{{ $franchise->head?->user_id ?? 'Pending' }}</span></td>
            <td>
              @if(($franchise->management_type ?? 'wallet') === 'wallet')
                <span class="badge badge-info" style="background:rgba(30,120,200,.12);color:#1e78c8;border:1px solid rgba(30,120,200,.2);">💳 Wallet</span>
                @if($franchise->admission_charge > 0)
                  <div class="text-xs text-muted" style="margin-top:2px;">Adm: ₹{{ number_format($franchise->admission_charge,0) }}</div>
                @endif
              @else
                <span class="badge" style="background:rgba(80,180,80,.12);color:#2a7a2a;border:1px solid rgba(80,180,80,.2);">🏢 Independent</span>
                @if($franchise->onboarding_fee > 0)
                  <div class="text-xs text-muted" style="margin-top:2px;">Fee: ₹{{ number_format($franchise->onboarding_fee,0) }}</div>
                @endif
              @endif
            </td>
            <td>
              @if(($franchise->management_type ?? 'wallet') === 'wallet')
                <div class="mono">₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</div>
                <div class="text-xs text-muted">{{ $franchise->wallet_enabled ? 'Active' : 'Disabled' }}</div>
              @else
                <div class="text-xs text-muted" style="color:var(--text-3);">—</div>
              @endif
            </td>
            <td><span class="badge {{ $franchise->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($franchise->status) }}</span></td>
            <td>
              <div class="flex gap-2" style="flex-wrap:wrap;">
                <a href="{{ route('institute.franchises.edit', $franchise) }}" class="btn btn-outline btn-xs">Edit</a>
                @if(($franchise->management_type ?? 'wallet') === 'wallet')
                  <a href="{{ route('institute.franchises.transactions', $franchise) }}" class="btn btn-outline btn-xs">Ledger</a>
                @else
                  @php $feeOutstanding = $franchise->feeOutstanding(); @endphp
                  <a href="{{ route('institute.franchises.fee.index', $franchise) }}"
                     class="btn btn-xs {{ $feeOutstanding > 0 ? '' : 'btn-outline' }}"
                     style="{{ $feeOutstanding > 0 ? 'background:rgba(220,53,69,.12);color:var(--danger);border:1px solid rgba(220,53,69,.2);' : '' }}">
                    Fee{{ $feeOutstanding > 0 ? ' (₹'.number_format($feeOutstanding,0).' due)' : '' }}
                  </a>
                @endif
                <a href="{{ route('institute.franchises.certificate', $franchise) }}" target="_blank"
                  class="btn btn-xs" style="background:rgba(200,146,42,.12);color:#8b6520;border:1px solid rgba(200,146,42,.3);"
                  title="Download Franchise Certificate">📜 Certificate</a>
                <form method="POST" action="{{ route('institute.franchises.toggle', $franchise) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn btn-xs {{ $franchise->status === 'active' ? '' : 'btn-success' }}" style="{{ $franchise->status === 'active' ? 'background:var(--warning-bg);color:var(--warning);border:1px solid rgba(255,184,77,.2);' : '' }}">
                    {{ $franchise->status === 'active' ? 'Disable' : 'Enable' }}
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9">
              <div class="gt-empty">
                <div class="gt-empty-title">No franchises added yet</div>
                <a href="{{ route('institute.franchises.create') }}" class="btn btn-primary btn-sm">Add First Franchise</a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
