@extends('layouts.student')
@section('title','Fee Payments')
@section('page-title','Fee Payments')

@section('content')
@php $amtCol = \App\Models\FeeCollectDetail::amountColumn(); @endphp

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;">
  <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:18px 20px;">
    <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Total Fee</div>
    <div style="font-size:26px;font-weight:900;color:var(--text-1)">₹{{ number_format($totalFee) }}</div>
  </div>
  <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:18px 20px;">
    <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Paid</div>
    <div style="font-size:26px;font-weight:900;color:#10b981">₹{{ number_format($paidFee) }}</div>
  </div>
  <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:18px 20px;">
    <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Balance Due</div>
    <div style="font-size:26px;font-weight:900;color:{{ $balance > 0 ? '#ef4444' : '#10b981' }}">₹{{ number_format($balance) }}</div>
  </div>
</div>

{{-- Progress bar --}}
@php $pct = $totalFee > 0 ? min(100, round(($paidFee/$totalFee)*100)) : 0; @endphp
<div style="background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:18px 20px;margin-bottom:20px;">
  <div style="display:flex;justify-content:space-between;font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:8px">
    <span>Fee Progress</span><span style="color:{{ $pct>=100?'#10b981':($pct>=50?'#f59e0b':'#ef4444') }}">{{ $pct }}% Paid</span>
  </div>
  <div style="height:10px;border-radius:100px;background:var(--bg-3);overflow:hidden">
    <div style="height:100%;border-radius:100px;background:linear-gradient(90deg,#10b981,#059669);width:{{ $pct }}%;transition:width .6s"></div>
  </div>
</div>

{{-- Transactions table --}}
<div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
  <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
    <div style="font-size:13px;font-weight:800;color:var(--text-1)">Payment History</div>
  </div>
  @if($fees->isNotEmpty())
  <table style="width:100%;border-collapse:collapse;">
    <thead>
      <tr style="background:var(--bg-3);">
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:left">#</th>
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:left">Invoice</th>
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:left">Date</th>
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:left">Mode</th>
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:right">Amount</th>
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:center">Status</th>
      </tr>
    </thead>
    <tbody>
    @foreach($fees as $i => $fee)
    <tr style="border-bottom:1px solid var(--border);{{ $fee->cancelled_at ? 'opacity:.5' : '' }}">
      <td style="padding:12px 20px;font-size:12px;color:var(--text-3)">{{ $i+1 }}</td>
      <td style="padding:12px 20px;font-size:12px;font-family:monospace;color:var(--text-2);font-weight:600">{{ $fee->invoice_no ?? '—' }}</td>
      <td style="padding:12px 20px;font-size:12px;color:var(--text-2)">{{ $fee->date?->format('d M Y') ?? '—' }}</td>
      <td style="padding:12px 20px;font-size:12px;color:var(--text-2)">{{ ucfirst($fee->payment_mode ?? '—') }}</td>
      <td style="padding:12px 20px;font-size:13px;font-weight:800;color:{{ $fee->cancelled_at ? 'var(--text-3)' : '#10b981' }};text-align:right">₹{{ number_format($fee->{$amtCol}) }}</td>
      <td style="padding:12px 20px;text-align:center">
        @if($fee->cancelled_at)
          <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#fee2e2;color:#b91c1c">Cancelled</span>
        @else
          <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#d1fae5;color:#065f46">Paid</span>
        @endif
      </td>
    </tr>
    @endforeach
    </tbody>
  </table>
  @else
  <div style="text-align:center;padding:48px;color:var(--text-3)">
    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    <div style="font-size:14px;font-weight:600;margin-bottom:4px">No payments recorded yet</div>
    <div style="font-size:12px">Your fee payment history will appear here.</div>
  </div>
  @endif
</div>
@endsection
