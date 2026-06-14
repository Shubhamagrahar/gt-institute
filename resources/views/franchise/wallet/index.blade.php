@extends('layouts.franchise')
@section('title','My Wallet')
@section('page-title','Wallet Transactions')

@push('styles')
<style>
.w-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
@media(max-width:768px){.w-stats{grid-template-columns:repeat(2,1fr)}}
.w-stat{background:var(--bg-2);border:1px solid var(--border);border-radius:16px;padding:16px 20px}
.w-stat-val{font-size:24px;font-weight:900;font-family:monospace}
.w-stat-lbl{font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-top:3px}

.w-tbl{width:100%;border-collapse:collapse;font-size:13px}
.w-tbl th{background:var(--bg-3);padding:10px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);white-space:nowrap}
.w-tbl td{padding:12px 16px;border-bottom:1px solid var(--border);vertical-align:middle;color:var(--text-1)}
.w-tbl tbody tr:last-child td{border-bottom:none}
.w-tbl tbody tr:hover td{background:var(--bg-3)}
.badge-cr{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;background:#f0fdf4;color:#15803d}
.badge-dr{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;background:#fef2f2;color:#b91c1c}
.badge-mode{display:inline-block;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;background:var(--bg-3);color:var(--text-2);border:1px solid var(--border)}
.amt-cr{color:#16a34a;font-weight:700;font-family:monospace}
.amt-dr{color:#dc2626;font-weight:700;font-family:monospace}
.amt-bal{font-family:monospace;font-weight:600}
.filter-bar{background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:14px 18px;margin-bottom:18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
</style>
@endpush

@section('content')
@php
  $balance     = (float) ($franchise->wallet?->balance ?? 0);
  $totalCredit = (float) $transactions->sum('credit');
  $totalDebit  = (float) $transactions->sum('debit');
  $totalTxn    = $transactions->total();
@endphp

{{-- Stats --}}
<div class="w-stats">
  <div class="w-stat">
    <div class="w-stat-val" style="color:{{ $balance >= 1000 ? '#16a34a' : ($balance > 0 ? '#d97706' : '#dc2626') }}">₹{{ number_format($balance, 2) }}</div>
    <div class="w-stat-lbl">Current Balance</div>
  </div>
  <div class="w-stat">
    <div class="w-stat-val">{{ $totalTxn }}</div>
    <div class="w-stat-lbl">Total Entries</div>
  </div>
  <div class="w-stat">
    <div class="w-stat-val" style="color:#16a34a">₹{{ number_format($totalCredit, 0) }}</div>
    <div class="w-stat-lbl">Total Credited (this page)</div>
  </div>
  <div class="w-stat">
    <div class="w-stat-val" style="color:#dc2626">₹{{ number_format($totalDebit, 0) }}</div>
    <div class="w-stat-lbl">Total Debited (this page)</div>
  </div>
</div>

{{-- Filter --}}
<div class="filter-bar">
  <form method="GET" action="{{ route('franchise.wallet') }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;width:100%">
    <input type="date" name="from" class="gt-input" style="width:160px" value="{{ request('from') }}" placeholder="From date">
    <input type="date" name="to"   class="gt-input" style="width:160px" value="{{ request('to') }}" placeholder="To date">
    <select name="type" class="gt-select" style="width:140px">
      <option value="">All Types</option>
      <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credit Only</option>
      <option value="debit"  {{ request('type') === 'debit'  ? 'selected' : '' }}>Debit Only</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="{{ route('franchise.wallet') }}" class="btn btn-outline btn-sm">Reset</a>
    <span style="margin-left:auto;font-size:12px;color:var(--text-3)">
      Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }} entries
    </span>
  </form>
</div>

{{-- Table --}}
<div class="gt-card">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Wallet Ledger — {{ $franchise->name }}</div>
      <div class="text-xs text-muted">All credits and debits from your franchise wallet</div>
    </div>
    <div style="font-size:12px;color:var(--text-3)">
      Balance: <strong style="color:{{ $balance >= 0 ? '#16a34a' : '#dc2626' }}">₹{{ number_format($balance, 2) }}</strong>
    </div>
  </div>

  <div style="overflow-x:auto">
    <table class="w-tbl">
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Txn No.</th>
          <th>Description</th>
          <th>Type</th>
          <th style="text-align:right">Credit (₹)</th>
          <th style="text-align:right">Debit (₹)</th>
          <th style="text-align:right">Balance (₹)</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $txn)
        <tr>
          <td class="text-muted" style="font-size:11px">{{ $transactions->firstItem() + $loop->index }}</td>
          <td style="white-space:nowrap;color:var(--text-2);font-size:12px">
            {{ \Carbon\Carbon::parse($txn->date)->format('d M Y') }}
          </td>
          <td style="font-family:monospace;font-size:11px;color:var(--text-3)">
            {{ $txn->txn_no ?? '—' }}
          </td>
          <td style="max-width:280px">
            <div style="font-size:12px;word-break:break-word;line-height:1.4">{{ $txn->description }}</div>
            @if($txn->payment_mode)
              <span class="badge-mode" style="margin-top:4px;display:inline-block">{{ $txn->payment_mode }}</span>
            @endif
            @if($txn->utr)
              <div style="font-size:10px;color:var(--text-3);margin-top:2px;font-family:monospace">UTR: {{ $txn->utr }}</div>
            @endif
          </td>
          <td>
            @if($txn->credit > 0)
              <span class="badge-cr">↑ Credit</span>
            @elseif($txn->debit > 0)
              <span class="badge-dr">↓ Debit</span>
            @else
              <span style="color:var(--text-3);font-size:11px">—</span>
            @endif
          </td>
          <td style="text-align:right" class="{{ $txn->credit > 0 ? 'amt-cr' : 'text-muted' }}">
            {{ $txn->credit > 0 ? '₹'.number_format($txn->credit, 2) : '—' }}
          </td>
          <td style="text-align:right" class="{{ $txn->debit > 0 ? 'amt-dr' : 'text-muted' }}">
            {{ $txn->debit > 0 ? '₹'.number_format($txn->debit, 2) : '—' }}
          </td>
          <td style="text-align:right" class="amt-bal">
            <span style="{{ (float)$txn->cl_bal < 0 ? 'color:#dc2626' : '' }}">
              ₹{{ number_format($txn->cl_bal, 2) }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div style="padding:50px;text-align:center">
              <div style="font-size:36px;margin-bottom:8px">💳</div>
              <div style="font-size:15px;font-weight:700;color:var(--text-1);margin-bottom:4px">No transactions found</div>
              <div style="font-size:12px;color:var(--text-3)">Wallet recharge hone ke baad entries yahan dikhegi.</div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
      @if($transactions->count())
      <tfoot>
        <tr style="background:var(--bg-3)">
          <td colspan="5" style="padding:10px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-3)">
            Page Totals
          </td>
          <td style="text-align:right;padding:10px 16px" class="amt-cr">
            ₹{{ number_format($transactions->sum('credit'), 2) }}
          </td>
          <td style="text-align:right;padding:10px 16px" class="amt-dr">
            ₹{{ number_format($transactions->sum('debit'), 2) }}
          </td>
          <td style="text-align:right;padding:10px 16px;font-family:monospace;font-weight:700">
            ₹{{ number_format($balance, 2) }}
          </td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>

  @if($transactions->hasPages())
  <div style="padding:14px 18px">
    {{ $transactions->appends(request()->query())->links() }}
  </div>
  @endif
</div>
@endsection
