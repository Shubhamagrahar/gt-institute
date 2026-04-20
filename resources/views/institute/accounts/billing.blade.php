@extends('layouts.institute')

@section('title', 'Billing & Subscription')

@section('content')
@php
  $statusBadge = match ($subscriptionStatus) {
      'Active' => 'badge-success',
      'Expiring soon' => 'badge-warning',
      'Expired' => 'badge-danger',
      default => 'badge-neutral',
  };
@endphp

<div class="gt-card mb-3" style="background:linear-gradient(135deg, #1b2140 0%, #2a3161 100%); color:#fff; border:none; overflow:hidden; position:relative;">
  <div style="position:absolute; inset:auto -60px -80px auto; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.06);"></div>
  <div class="gt-card-header" style="position:relative; z-index:1; margin-bottom:12px;">
    <div>
      <div class="gt-card-title" style="color:#fff; font-size:18px;">Billing & Subscription</div>
      <div style="font-size:12px; color:rgba(255,255,255,.72);">Institute plan, payments, dues, and subscription validity.</div>
    </div>
    <span class="badge {{ $statusBadge }}">{{ $subscriptionStatus }}</span>
  </div>

  <div class="gt-grid-2" style="gap:20px; position:relative; z-index:1;">
    <div class="account-highlight-card">
      <div class="account-highlight-label">Current Plan</div>
      <div class="account-highlight-value">{{ $subscription?->plan?->name ?? 'No active plan' }}</div>
      <div class="account-highlight-sub">
        @if($subscription)
          {{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}
        @else
          Subscription details are not available yet.
        @endif
      </div>
    </div>

    <div class="account-highlight-card">
      <div class="account-highlight-label">Days Remaining</div>
      <div class="account-highlight-value">{{ $daysRemaining !== null ? $daysRemaining . ' days' : '--' }}</div>
      <div class="account-highlight-sub">
        @if($subscription)
          Renewal date: {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}
        @else
          Add a subscription to see expiry tracking.
        @endif
      </div>
    </div>
  </div>
</div>

<div class="gt-stats">
  <div class="gt-stat">
    <div class="gt-stat-icon purple">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">₹{{ number_format($subscription?->final_price ?? 0, 2) }}</div>
      <div class="gt-stat-label">Current Plan Amount</div>
      <div class="gt-stat-sub">Final billed amount for active subscription</div>
    </div>
  </div>

  <div class="gt-stat">
    <div class="gt-stat-icon green">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">₹{{ number_format($totalPaid, 2) }}</div>
      <div class="gt-stat-label">Amount Paid</div>
      <div class="gt-stat-sub">Total payments received from institute</div>
    </div>
  </div>

  <div class="gt-stat">
    <div class="gt-stat-icon red">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">₹{{ number_format($totalDue, 2) }}</div>
      <div class="gt-stat-label">Current Due</div>
      <div class="gt-stat-sub">Outstanding amount based on ledger debits</div>
    </div>
  </div>

  <div class="gt-stat">
    <div class="gt-stat-icon {{ $walletBalance >= 0 ? 'blue' : 'orange' }}">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7H3v10h18V7z"/><path d="M17 12h.01"/><path d="M3 9h18"/></svg>
    </div>
    <div>
      <div class="gt-stat-value">₹{{ number_format(abs($walletBalance), 2) }}</div>
      <div class="gt-stat-label">{{ $walletBalance >= 0 ? 'Wallet Balance' : 'Ledger Deficit' }}</div>
      <div class="gt-stat-sub">{{ $walletBalance >= 0 ? 'Available balance in institute wallet' : 'Negative wallet balance on ledger' }}</div>
    </div>
  </div>
</div>

<div class="gt-grid-2" style="align-items:start;">
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Subscription Summary</div>
      @if($subscription)
        <span class="badge {{ $statusBadge }}">{{ $subscription?->status }}</span>
      @endif
    </div>

    <table class="gt-table">
      <tbody>
        <tr>
          <td class="text-muted" style="width:180px;">Institute ID</td>
          <td class="fw-600">{{ $institute->unique_id }}</td>
        </tr>
        <tr>
          <td class="text-muted">Plan Name</td>
          <td>{{ $subscription?->plan?->name ?? 'Not assigned' }}</td>
        </tr>
        <tr>
          <td class="text-muted">Plan Start Date</td>
          <td>{{ $subscription ? \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') : '--' }}</td>
        </tr>
        <tr>
          <td class="text-muted">Plan End Date</td>
          <td>{{ $subscription ? \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') : '--' }}</td>
        </tr>
        <tr>
          <td class="text-muted">Original Price</td>
          <td>₹{{ number_format($subscription?->price ?? 0, 2) }}</td>
        </tr>
        <tr>
          <td class="text-muted">Discount</td>
          <td>{{ $subscription ? '₹' . number_format($subscription->discount_amount ?? 0, 2) : '--' }}</td>
        </tr>
        <tr>
          <td class="text-muted">Final Price</td>
          <td class="fw-700 text-accent">₹{{ number_format($subscription?->final_price ?? 0, 2) }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Payment Snapshot</div>
      <span class="badge {{ $totalDue > 0 ? 'badge-warning' : 'badge-success' }}">{{ $totalDue > 0 ? 'Payment Pending' : 'Paid Up' }}</span>
    </div>

    <div class="payment-progress-wrap">
      <div class="payment-progress-label">
        <span>Paid vs Due</span>
        <span>{{ $totalDebits > 0 ? round(($totalPaid / max($totalDebits, 1)) * 100) : 0 }}%</span>
      </div>
      <div class="payment-progress-bar">
        <span style="width:{{ min($totalDebits > 0 ? ($totalPaid / max($totalDebits, 1)) * 100 : 0, 100) }}%;"></span>
      </div>
    </div>

    <div class="gt-grid-2" style="gap:14px; margin-top:18px;">
      <div class="account-mini-card">
        <div class="account-mini-label">Amount Paid</div>
        <div class="account-mini-value amount-pos">₹{{ number_format($totalPaid, 2) }}</div>
      </div>
      <div class="account-mini-card">
        <div class="account-mini-label">Amount Due</div>
        <div class="account-mini-value {{ $totalDue > 0 ? 'amount-neg' : 'amount-pos' }}">₹{{ number_format($totalDue, 2) }}</div>
      </div>
    </div>

    <div class="account-note" style="margin-top:16px;">
      @if($subscription && $daysRemaining !== null)
        {{ $daysRemaining > 0 ? "Your current plan is active for {$daysRemaining} more days." : 'Your subscription period has ended. Please renew the plan.' }}
      @else
        No active subscription found for this institute account.
      @endif
    </div>
  </div>
</div>

<div class="gt-card" style="margin-top:18px;">
  <div class="gt-card-header">
    <div class="gt-card-title">Recent Payments</div>
    <span class="text-sm text-muted">Latest 10 received entries</span>
  </div>

  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Invoice</th>
          <th>Mode</th>
          <th>UTR / Ref</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentPayments as $payment)
          <tr>
            <td>{{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</td>
            <td><code class="mono text-accent">{{ $payment->invoice_no }}</code></td>
            <td>{{ $payment->payment_mode }}</td>
            <td>{{ $payment->utr ?: '---' }}</td>
            <td class="mono amount-pos">₹{{ number_format($payment->amt, 2) }}</td>
            <td><span class="badge badge-success">{{ ucfirst($payment->status) }}</span></td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted">No payment records available yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
