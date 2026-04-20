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
              <div class="fw-600">{{ $franchise->name }}</div>
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
              <div class="mono">₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</div>
              <div class="text-xs text-muted">{{ $franchise->wallet_enabled ? 'Wallet On' : 'Wallet Off' }}</div>
            </td>
            <td><span class="badge {{ $franchise->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($franchise->status) }}</span></td>
            <td>
              <div class="flex gap-2">
                <a href="{{ route('institute.franchises.show', $franchise) }}" class="btn btn-outline btn-xs">View</a>
                <a href="{{ route('institute.franchises.edit', $franchise) }}" class="btn btn-outline btn-xs">Edit</a>
                <a href="{{ route('institute.franchises.transactions', $franchise) }}" class="btn btn-outline btn-xs">Ledger</a>
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
            <td colspan="8">
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
