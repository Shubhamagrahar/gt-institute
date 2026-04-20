@extends('layouts.institute')
@section('title', 'Franchise Wallets')
@section('page-title', 'Franchise Wallets')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.create') }}" class="btn btn-primary btn-sm">+ Add Franchise</a>
@endsection

@section('content')
<div class="gt-card" style="margin-bottom:18px;">
  <div class="gt-card-header">
    <div class="gt-card-title">Low Wallet Franchises ({{ $lowWalletFranchises->count() }})</div>
    <span class="text-xs text-muted">Jinka wallet threshold se neeche ya equal hai.</span>
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Franchise</th>
          <th>Level</th>
          <th>Balance</th>
          <th>Alert</th>
          <th>Recharge</th>
        </tr>
      </thead>
      <tbody>
        @forelse($lowWalletFranchises as $franchise)
          <tr>
            <td>
              <div class="fw-600">{{ $franchise->name }}</div>
              <div class="text-xs text-muted">{{ $franchise->unique_id }}</div>
            </td>
            <td>{{ $franchise->level?->name ?? 'NA' }}</td>
            <td class="mono amount-neg">₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</td>
            <td class="mono">₹{{ number_format($franchise->low_wallet_alert, 2) }}</td>
            <td><a href="{{ route('institute.franchises.transactions', $franchise) }}" class="btn btn-outline btn-xs">Recharge</a></td>
          </tr>
        @empty
          <tr>
            <td colspan="5">
              <div class="gt-empty">
                <div class="gt-empty-title">No low wallet franchise right now</div>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Franchise Wallets</div>
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Franchise</th>
          <th>Wallet</th>
          <th>Status</th>
          <th>Open</th>
        </tr>
      </thead>
      <tbody>
        @foreach($franchises as $franchise)
          <tr>
            <td>
              <div class="fw-600">{{ $franchise->name }}</div>
              <div class="text-xs text-muted">{{ $franchise->email }}</div>
            </td>
            <td>
              <div class="mono">₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</div>
              <div class="text-xs text-muted">{{ $franchise->wallet_enabled ? 'Enabled' : 'Disabled' }}</div>
            </td>
            <td>
              @if($franchise->wallet_enabled && (($franchise->wallet?->balance ?? 0) <= $franchise->low_wallet_alert))
                <span class="badge badge-warning">Low Wallet</span>
              @elseif($franchise->wallet_enabled)
                <span class="badge badge-success">Healthy</span>
              @else
                <span class="badge badge-neutral">Wallet Off</span>
              @endif
            </td>
            <td><a href="{{ route('institute.franchises.transactions', $franchise) }}" class="btn btn-outline btn-xs">Open</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
