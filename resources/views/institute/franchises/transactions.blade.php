@extends('layouts.institute')
@section('title', 'Wallet Ledger — ' . $franchise->name)
@section('page-title', 'Wallet Ledger')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.show', $franchise) }}" class="btn btn-outline btn-sm">Back to Franchise</a>
@endsection

@section('content')

@if(($franchise->management_type ?? 'wallet') === 'wallet')
<div class="gt-card" style="margin-bottom:18px;">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Recharge Wallet</div>
      <span class="text-xs text-muted">Enter the amount received. Recharge amount defaults to paid amount — edit it to add a bonus.</span>
    </div>
    <div style="text-align:right; min-width:180px;">
      <div class="text-xs text-muted">Current Balance</div>
      <div style="font-size:20px; font-weight:700; color:var(--accent);" id="wallet-bal-display">
        ₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('institute.franchises.recharge-bonus', $franchise) }}">
    @csrf

    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Payment Mode <span style="color:var(--danger)">*</span></label>
        <select name="payment_mode" class="gt-select" required>
          <option value="CASH">Cash</option>
          <option value="UPI">UPI</option>
          <option value="NEFT">NEFT</option>
          <option value="IMPS">IMPS</option>
          <option value="CHEQUE">Cheque</option>
        </select>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">
          Paid Amount (₹)
          <span style="color:var(--danger)">*</span>
        </label>
        <input type="number" name="paid_amount" id="paid-amount" class="gt-input"
          min="1" step="0.01" required placeholder="Amount received from franchise">
      </div>
      <div class="gt-form-group">
        <label class="gt-label" style="display:flex; justify-content:space-between;">
          <span>Recharge Amount (₹) <span style="color:var(--danger)">*</span></span>
          <span id="bonus-tag" style="display:none; font-size:10px; color:#2a8a4a; background:rgba(42,138,74,.1); padding:1px 6px; border-radius:10px; font-weight:600;">+ BONUS</span>
        </label>
        <input type="number" name="recharge_amount" id="recharge-amount" class="gt-input"
          min="1" step="0.01" required placeholder="Amount to credit to wallet">
        <div class="text-xs" id="bonus-diff-hint" style="margin-top:4px; color:#2a8a4a; display:none;">
          Bonus credited: <strong id="bonus-diff-val"></strong>
        </div>
      </div>
    </div>

    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Date <span style="color:var(--danger)">*</span></label>
        <input type="date" name="date" class="gt-input" value="{{ now()->toDateString() }}" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">UTR / Ref No.</label>
        <input type="text" name="utr" class="gt-input" placeholder="Optional">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Note</label>
        <input type="text" name="note" class="gt-input" placeholder="e.g. Diwali Offer 2026">
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Recharge Wallet</button>
  </form>
</div>
@endif

{{-- Ledger --}}
<div class="gt-card">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">{{ $franchise->name }} — Wallet Ledger</div>
      @if(($franchise->management_type ?? 'wallet') === 'wallet')
        <div class="text-xs text-muted" style="margin-top:4px;">
          Balance: <strong>₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</strong>
          &nbsp;·&nbsp; Admission charge: ₹{{ number_format($franchise->admission_charge ?? 0, 2) }}
          &nbsp;·&nbsp; Certificate charge: ₹{{ number_format($franchise->certificate_charge ?? 0, 2) }}
        </div>
      @else
        <div class="text-xs text-muted" style="margin-top:4px;">Independent mode — Onboarding fee: ₹{{ number_format($franchise->onboarding_fee ?? 0, 2) }}</div>
      @endif
    </div>
  </div>

  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Txn No.</th>
          <th>Description</th>
          <th>Type</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Opening</th>
          <th>Closing</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $txn)
          <tr>
            <td>{{ \Carbon\Carbon::parse($txn->date)->format('d M Y') }}</td>
            <td><span class="mono" style="font-size:11px;">{{ $txn->txn_no ?: '—' }}</span></td>
            <td>
              <div>{{ $txn->description }}</div>
              @if($txn->payment_mode || $txn->utr)
                <div class="text-xs text-muted" style="margin-top:2px;">{{ $txn->payment_mode }}{{ $txn->utr ? ' · '.$txn->utr : '' }}</div>
              @endif
            </td>
            <td>
              @php
                $types = [1=>['Opening','#2a8a4a'],2=>['Recharge','#1e78c8'],3=>['Recharge+Bonus','#8b6520'],4=>['Admission','#c84040'],5=>['Certificate','#6b4e1a'],6=>['Manual','#5a5a5a'],7=>['Refund','#2a8a4a']];
                [$lbl,$clr] = $types[$txn->type] ?? ['—','#888'];
              @endphp
              <span style="font-size:11px; color:{{ $clr }}; font-weight:600;">{{ $lbl }}</span>
            </td>
            <td class="amount-pos">{{ $txn->credit > 0 ? '₹'.number_format($txn->credit,2) : '—' }}</td>
            <td class="amount-neg">{{ $txn->debit  > 0 ? '₹'.number_format($txn->debit,2)  : '—' }}</td>
            <td class="mono">₹{{ number_format($txn->op_bal,2) }}</td>
            <td class="mono">₹{{ number_format($txn->cl_bal,2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="8"><div class="gt-empty"><div class="gt-empty-title">No transactions yet</div></div></td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:16px;">{{ $transactions->links() }}</div>
</div>

@push('scripts')
<script>
(function () {
  const paidEl    = document.getElementById('paid-amount');
  const rchgEl    = document.getElementById('recharge-amount');
  const bonusTag  = document.getElementById('bonus-tag');
  const diffHint  = document.getElementById('bonus-diff-hint');
  const diffVal   = document.getElementById('bonus-diff-val');

  // Keep recharge in sync with paid unless user manually edits recharge
  let userEditedRecharge = false;

  paidEl?.addEventListener('keyup', function () {
    if (!userEditedRecharge) {
      rchgEl.value = this.value;
    }
    updateBonus();
  });

  paidEl?.addEventListener('input', function () {
    if (!userEditedRecharge) {
      rchgEl.value = this.value;
    }
    updateBonus();
  });

  rchgEl?.addEventListener('input', function () {
    userEditedRecharge = this.value !== paidEl?.value;
    updateBonus();
  });

  function updateBonus() {
    const paid  = parseFloat(paidEl?.value)  || 0;
    const rchg  = parseFloat(rchgEl?.value) || 0;
    const diff  = rchg - paid;

    if (diff > 0.009) {
      bonusTag.style.display = '';
      diffHint.style.display = '';
      diffVal.textContent = '₹' + diff.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    } else {
      bonusTag.style.display = 'none';
      diffHint.style.display = 'none';
    }
  }
})();
</script>
@endpush

@endsection
