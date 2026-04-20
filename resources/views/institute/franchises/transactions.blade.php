@extends('layouts.institute')
@section('title', 'Franchise Ledger')
@section('page-title', 'Franchise Ledger')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.show', $franchise) }}" class="btn btn-outline btn-sm">Back to Franchise</a>
@endsection

@section('content')
<div class="gt-card" style="margin-bottom:18px;">
  <div class="gt-card-header">
    <div class="gt-card-title">Recharge Wallet</div>
    <span class="text-xs text-muted">Har recharge ki ledger entry auto-save hogi.</span>
  </div>

  <form method="POST" action="{{ route('institute.franchises.recharge', $franchise) }}">
    @csrf
    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Payment Mode</label>
        <select name="payment_mode" class="gt-select" required>
          <option value="CASH">Cash</option>
          <option value="UPI">UPI</option>
          <option value="NEFT">NEFT</option>
          <option value="IMPS">IMPS</option>
          <option value="CHEQUE">Cheque</option>
        </select>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Amount</label>
        <input type="number" name="amount" class="gt-input" min="1" step="0.01" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Date</label>
        <input type="date" name="date" class="gt-input" value="{{ now()->toDateString() }}" required>
      </div>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">UTR / Ref No.</label>
        <input type="text" name="utr" class="gt-input">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Note</label>
        <input type="text" name="note" class="gt-input" placeholder="Optional note">
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Recharge Wallet</button>
  </form>
</div>

<div class="gt-card">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">{{ $franchise->name }} Ledger</div>
      <div class="text-xs text-muted" style="margin-top:4px;">Current wallet: ₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</div>
    </div>
  </div>

  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Txn No</th>
          <th>Description</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Opening</th>
          <th>Closing</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $transaction)
          <tr>
            <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
            <td>{{ $transaction->txn_no ?: '-' }}</td>
            <td>
              <div>{{ $transaction->description }}</div>
              @if($transaction->payment_mode || $transaction->utr)
                <div class="text-xs text-muted" style="margin-top:3px;">
                  {{ $transaction->payment_mode ?: '' }}{{ $transaction->utr ? ' | ' . $transaction->utr : '' }}
                </div>
              @endif
            </td>
            <td class="amount-pos">₹{{ number_format($transaction->credit, 2) }}</td>
            <td class="amount-neg">₹{{ number_format($transaction->debit, 2) }}</td>
            <td class="mono">₹{{ number_format($transaction->op_bal, 2) }}</td>
            <td class="mono">₹{{ number_format($transaction->cl_bal, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7">
              <div class="gt-empty">
                <div class="gt-empty-title">No transactions found</div>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:16px;">
    {{ $transactions->links() }}
  </div>
</div>
@endsection
