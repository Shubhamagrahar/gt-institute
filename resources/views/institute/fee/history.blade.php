@extends('layouts.institute')
@section('title', $student->name . ' — Fee History')
@section('page-title', $student->name . ' — Fee History')
@section('topbar-actions')
  <a href="{{ route('institute.fee.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Fee Collection History</div>
    <div class="text-sm text-muted">Total: <span class="mono text-accent fw-600">₹{{ number_format($collections->sum('amt'),2) }}</span></div>
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr>
        <th>Invoice No</th><th>Date</th><th>Mode</th><th>UTR</th><th>Amount</th>
      </tr></thead>
      <tbody>
        @forelse($collections as $c)
        <tr>
          <td><code style="color:var(--accent);font-size:11px;">{{ $c->invoice_no ?? '—' }}</code></td>
          <td class="text-sm">{{ date('d M Y',strtotime($c->date)) }}</td>
          <td><span class="badge badge-info">{{ $c->payment_mode }}</span></td>
          <td class="text-muted text-xs">{{ $c->utr ?? '—' }}</td>
          <td class="mono fw-600 amount-pos">+₹{{ number_format($c->amt,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5">
          <div class="gt-empty"><div class="gt-empty-title">No fee collected yet</div></div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
