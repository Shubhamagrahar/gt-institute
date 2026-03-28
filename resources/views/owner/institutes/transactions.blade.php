@extends('layouts.owner')
@section('title','Ledger — '.$institute->name)
@section('page-title','Wallet Ledger')
@section('topbar-actions')
  <a href="{{ route('owner.institutes.show',$institute) }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-stats" style="margin-bottom:20px;">
  <div class="gt-stat">
    <div class="gt-stat-icon {{ ($wallet->main_b??0)>=0 ? 'green' : 'red' }}">💰</div>
    <div>
      <div class="gt-stat-value mono {{ ($wallet->main_b??0)>=0 ? 'amount-pos' : 'amount-neg' }}">₹{{ number_format($wallet->main_b??0,2) }}</div>
      <div class="gt-stat-label">Current Balance</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon blue">📄</div>
    <div>
      <div class="gt-stat-value">{{ $transactions->total() }}</div>
      <div class="gt-stat-label">Total Entries</div>
    </div>
  </div>
</div>
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">{{ $institute->name }} — Transaction Ledger</div>
    <code style="font-size:11px;color:var(--accent);">{{ $institute->unique_id }}</code>
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr><th>#</th><th>Date</th><th>Description</th><th>Debit</th><th>Credit</th><th>Op. Bal</th><th>Cl. Bal</th><th>Type</th></tr></thead>
      <tbody>
        @php $typeLabels=[1=>'Subscription',2=>'Add-on',3=>'Payment',4=>'Discount',5=>'Manual']; $typeBadges=[1=>'badge-info',2=>'badge-warning',3=>'badge-success',4=>'badge-accent',5=>'badge-neutral']; @endphp
        @forelse($transactions as $txn)
        <tr>
          <td class="text-muted mono text-xs">{{ $txn->id }}</td>
          <td class="text-xs text-muted">{{ \Carbon\Carbon::parse($txn->date)->format('d M Y') }}</td>
          <td style="max-width:260px;"><div class="text-sm" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $txn->des }}</div>@if($txn->invoice_no)<div class="text-xs" style="color:var(--accent);margin-top:2px;">{{ $txn->invoice_no }}</div>@endif</td>
          <td class="mono text-sm amount-neg">{{ $txn->debit>0 ? '₹'.number_format($txn->debit,2) : '—' }}</td>
          <td class="mono text-sm amount-pos">{{ $txn->credit>0 ? '+₹'.number_format($txn->credit,2) : '—' }}</td>
          <td class="mono text-xs text-muted">₹{{ number_format($txn->op_bal,2) }}</td>
          <td class="mono text-sm fw-600 {{ $txn->cl_bal>=0?'amount-pos':'amount-neg' }}">₹{{ number_format($txn->cl_bal,2) }}</td>
          <td><span class="badge {{ $typeBadges[$txn->type]??'badge-neutral' }}">{{ $typeLabels[$txn->type]??'Other' }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8"><div class="gt-empty"><div class="gt-empty-icon">📄</div><div class="gt-empty-title">No transactions</div></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="gt-pagination">{{ $transactions->links() }}</div>
</div>
@endsection
