@extends('layouts.franchise')
@section('title', 'Dashboard')

@section('content')
<div class="gt-welcome-banner" style="background:linear-gradient(135deg,#1f2937,#111827);">
  <div>
    <div class="gt-welcome-title">Franchise Dashboard</div>
    <div class="gt-welcome-sub">{{ $franchise->name }} · {{ $franchise->institute?->name }}</div>
  </div>
</div>

<div class="gt-stats" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
  <div class="gt-stat">
    <div class="gt-stat-icon green">Rs</div>
    <div>
      <div class="gt-stat-value mono">₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</div>
      <div class="gt-stat-label">Wallet Balance</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon blue">ID</div>
    <div>
      <div class="gt-stat-value" style="font-size:18px;">{{ $franchise->unique_id }}</div>
      <div class="gt-stat-label">Franchise ID</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon orange">TX</div>
    <div>
      <div class="gt-stat-value">{{ $recentTransactions->count() }}</div>
      <div class="gt-stat-label">Recent Entries</div>
    </div>
  </div>
</div>

<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Recent Transactions</div>
  </div>

  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Closing</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentTransactions as $transaction)
          <tr>
            <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
            <td>{{ $transaction->description }}</td>
            <td class="amount-pos">₹{{ number_format($transaction->credit, 2) }}</td>
            <td class="amount-neg">₹{{ number_format($transaction->debit, 2) }}</td>
            <td class="mono">₹{{ number_format($transaction->cl_bal, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5">
              <div class="gt-empty">
                <div class="gt-empty-title">No ledger entries yet</div>
                <div class="gt-empty-sub">Wallet transactions yahan dikhengi.</div>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
