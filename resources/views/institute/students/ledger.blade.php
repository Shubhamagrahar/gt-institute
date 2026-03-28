@extends('layouts.institute')
@section('title', $student->name . ' — Ledger')
@section('page-title', $student->name . ' — Account Ledger')
@section('topbar-actions')
  <a href="{{ route('institute.students.show',$student) }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')

<div class="gt-stats" style="margin-bottom:20px;">
  <div class="gt-stat">
    <div class="gt-stat-icon {{ ($wallet?->main_b ?? 0) >= 0 ? 'green' : 'red' }}">💰</div>
    <div>
      <div class="gt-stat-value mono {{ ($wallet?->main_b ?? 0) >= 0 ? 'amount-pos' : 'amount-neg' }}">
        ₹{{ number_format($wallet?->main_b ?? 0,2) }}
      </div>
      <div class="gt-stat-label">Current Balance</div>
    </div>
  </div>
</div>

<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Transaction Ledger — {{ $student->name }}</div>
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr>
        <th>Date</th><th>Description</th><th>Credit</th><th>Debit</th><th>Opening</th><th>Closing</th>
      </tr></thead>
      <tbody>
        @forelse($transactions as $txn)
        <tr>
          <td class="text-sm">{{ \Carbon\Carbon::parse($txn->date)->format('d M Y') }}</td>
          <td style="max-width:280px;">
            <div class="text-sm" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $txn->des }}</div>
          </td>
          <td class="mono {{ $txn->credit > 0 ? 'amount-pos' : 'text-muted' }}">{{ $txn->credit > 0 ? '+₹'.number_format($txn->credit,2) : '—' }}</td>
          <td class="mono {{ $txn->debit > 0 ? 'amount-neg' : 'text-muted' }}">{{ $txn->debit > 0 ? '−₹'.number_format($txn->debit,2) : '—' }}</td>
          <td class="mono text-muted">₹{{ number_format($txn->op_bal,2) }}</td>
          <td class="mono fw-600 {{ $txn->cl_bal >= 0 ? 'amount-pos' : 'amount-neg' }}">₹{{ number_format($txn->cl_bal,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6">
          <div class="gt-empty"><div class="gt-empty-icon">📊</div><div class="gt-empty-title">No transactions</div></div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="gt-pagination">{{ $transactions->links() }}</div>
</div>
@endsection
