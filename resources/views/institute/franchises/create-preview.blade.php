@extends('layouts.institute')
@section('title', 'Confirm Franchise')
@section('page-title', 'Confirm Franchise')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.create') }}" class="btn btn-outline btn-sm">← Edit Details</a>
@endsection

@section('content')
@php
  $level    = $data['_level'] ?? [];
  $levelFee = (float) ($level['level_fee'] ?? 0);
  $mgmt     = $data['management_type'] ?? 'wallet';
  $isWallet = $mgmt === 'wallet';

  $paymentDue  = $isWallet ? (float) ($data['opening_balance'] ?? 0) : $levelFee;
  $paymentNote = $isWallet
    ? 'Opening wallet balance (credited immediately on creation)'
    : 'One-time onboarding fee payable to ' . auth('institute')->user()->institute->name;
@endphp

{{-- Step progress --}}
<div class="frn-wizard">
  <div class="frn-wizard-step done"><span class="frn-step-dot">✓</span><span class="frn-step-lbl">Franchise Details</span></div>
  <div class="frn-wizard-line done"></div>
  <div class="frn-wizard-step active"><span class="frn-step-dot">2</span><span class="frn-step-lbl">Review & Payment</span></div>
  <div class="frn-wizard-line"></div>
  <div class="frn-wizard-step"><span class="frn-step-dot">3</span><span class="frn-step-lbl">{{ $isWallet ? 'Wallet Ledger' : 'Fee Collection' }}</span></div>
</div>

<div class="frn-preview-layout">

  {{-- ── LEFT: Details + Fee ──────────────────────────────────────── --}}
  <div class="frn-preview-left">

    {{-- Franchise Details Card --}}
    <div class="gt-card frn-detail-card">
      <div class="frn-detail-header">
        <div>
          <div class="frn-detail-franchise-name">{{ $data['name'] }}</div>
          @if(!empty($data['short_name']))
            <div class="frn-detail-sub">{{ $data['short_name'] }}</div>
          @endif
        </div>
        <div class="frn-mode-badge {{ $isWallet ? 'frn-mode-wallet' : 'frn-mode-independent' }}">
          {{ $isWallet ? '💳 Wallet System' : '🏢 Independent' }}
        </div>
      </div>

      <div class="frn-detail-grid">
        <div class="frn-detail-item">
          <span class="frn-detail-label">Owner</span>
          <span class="frn-detail-value">{{ $data['owner_name'] }}</span>
        </div>
        <div class="frn-detail-item">
          <span class="frn-detail-label">Mobile</span>
          <span class="frn-detail-value">{{ $data['mobile'] }}</span>
        </div>
        <div class="frn-detail-item">
          <span class="frn-detail-label">Email</span>
          <span class="frn-detail-value">{{ $data['email'] }}</span>
        </div>
        <div class="frn-detail-item">
          <span class="frn-detail-label">Owner Mobile</span>
          <span class="frn-detail-value">{{ $data['owner_mobile'] }}</span>
        </div>
        <div class="frn-detail-item">
          <span class="frn-detail-label">Level</span>
          <span class="frn-detail-value fw-600">{{ $level['name'] ?? '—' }}</span>
        </div>
        <div class="frn-detail-item">
          <span class="frn-detail-label">Commission</span>
          <span class="frn-detail-value">{{ number_format($data['commission_percent'] ?? 0, 2) }}%</span>
        </div>
        @if(!empty($data['address']))
        <div class="frn-detail-item frn-detail-full">
          <span class="frn-detail-label">Address</span>
          <span class="frn-detail-value">{{ $data['address'] }}{{ !empty($data['state']) ? ', '.$data['state'] : '' }}{{ !empty($data['pin_code']) ? ' — '.$data['pin_code'] : '' }}</span>
        </div>
        @endif
        @if($isWallet)
          <div class="frn-detail-item">
            <span class="frn-detail-label">Per Admission Charge</span>
            <span class="frn-detail-value mono">₹{{ number_format($data['admission_charge'] ?? 0, 2) }}</span>
          </div>
          <div class="frn-detail-item">
            <span class="frn-detail-label">Per Certificate Charge</span>
            <span class="frn-detail-value mono">₹{{ number_format($data['certificate_charge'] ?? 0, 2) }}</span>
          </div>
          <div class="frn-detail-item">
            <span class="frn-detail-label">Low Wallet Alert</span>
            <span class="frn-detail-value mono">₹{{ number_format($data['low_wallet_alert'] ?? 0, 2) }}</span>
          </div>
        @endif
      </div>
    </div>

    {{-- Payment Summary Card --}}
    <div class="gt-card frn-payment-card">
      <div class="gt-card-header" style="border-bottom:1px solid var(--border-1); padding-bottom:12px; margin-bottom:16px;">
        <div class="gt-card-title">{{ $isWallet ? 'Wallet Opening Balance' : 'Onboarding Fee' }}</div>
      </div>

      <div class="frn-pay-table">
        @if($isWallet)
          <div class="frn-pay-row">
            <span>Opening wallet balance</span>
            <span class="mono">₹{{ number_format((float)($data['opening_balance'] ?? 0), 2) }}</span>
          </div>
        @else
          <div class="frn-pay-row">
            <span>Level onboarding fee — {{ $level['name'] ?? '' }}</span>
            <span class="mono">₹{{ number_format($levelFee, 2) }}</span>
          </div>
        @endif
        <div class="frn-pay-row frn-pay-total">
          <span class="fw-600">{{ $isWallet ? 'Amount to Credit' : 'Total Due from Franchise' }}</span>
          <span class="mono fw-600" style="font-size:17px; color:var(--accent);">₹{{ number_format($paymentDue, 2) }}</span>
        </div>
      </div>

      <div class="frn-pay-note {{ $isWallet ? 'frn-pay-note-blue' : 'frn-pay-note-amber' }}">
        {{ $paymentNote }}
      </div>
    </div>

  </div>{{-- /left --}}

  {{-- ── RIGHT: Confirm Panel ─────────────────────────────────────── --}}
  <div class="frn-preview-right">
    <div class="gt-card frn-confirm-card">

      <div class="frn-confirm-title">Ready to Create</div>
      <div class="frn-confirm-sub">
        Credentials will be emailed to<br>
        <strong>{{ $data['email'] }}</strong>
      </div>

      <div class="frn-confirm-amount-box">
        <div class="frn-confirm-amount-label">
          {{ $isWallet ? 'Opening Balance' : 'Fee Due' }}
        </div>
        <div class="frn-confirm-amount-value">₹{{ number_format($paymentDue, 2) }}</div>
        @if($paymentDue <= 0)
          <div class="frn-confirm-amount-sub">No payment required</div>
        @elseif($isWallet)
          <div class="frn-confirm-amount-sub">Credited to wallet on creation</div>
        @else
          <div class="frn-confirm-amount-sub">Collect after creation</div>
        @endif
      </div>

      <div class="frn-confirm-checklist">
        <div class="frn-check-item">✓ Franchise account created</div>
        <div class="frn-check-item">✓ Login credentials emailed</div>
        @if($isWallet && $paymentDue > 0)
          <div class="frn-check-item">✓ ₹{{ number_format($paymentDue, 2) }} opening balance credited</div>
        @elseif(!$isWallet && $paymentDue > 0)
          <div class="frn-check-item pending-check">→ Fee collection recorded as due</div>
        @endif
        <div class="frn-check-item">✓ {{ $isWallet ? 'Wallet system activated' : 'Independent access granted' }}</div>
      </div>

      <form method="POST" action="{{ route('institute.franchises.confirm') }}">
        @csrf
        <button type="submit" class="btn btn-primary frn-confirm-btn">
          ✓ Confirm & Create Franchise
        </button>
      </form>

      <a href="{{ route('institute.franchises.create') }}" class="frn-back-link">← Go back and edit details</a>
    </div>
  </div>

</div>
@endsection

@push('styles')
<style>
/* ── Wizard steps ────────────────────────── */
.frn-wizard {
  display: flex; align-items: center; margin-bottom: 28px;
}
.frn-wizard-step {
  display: flex; flex-direction: column; align-items: center; gap: 5px; flex-shrink: 0;
}
.frn-step-dot {
  width: 34px; height: 34px; border-radius: 50%;
  border: 2px solid var(--border-2);
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700;
  color: var(--text-3); background: var(--bg-3);
}
.frn-step-lbl { font-size: 11px; color: var(--text-3); white-space: nowrap; }
.frn-wizard-step.active .frn-step-dot { border-color: var(--accent); background: var(--accent); color: #fff; }
.frn-wizard-step.active .frn-step-lbl { color: var(--accent); font-weight: 600; }
.frn-wizard-step.done .frn-step-dot { border-color: #2a8a4a; background: #2a8a4a; color: #fff; }
.frn-wizard-step.done .frn-step-lbl { color: #2a8a4a; }
.frn-wizard-line { flex: 1; height: 2px; background: var(--border-2); margin: 0 10px; margin-bottom: 20px; }
.frn-wizard-line.done { background: #2a8a4a; }

/* ── Layout ──────────────────────────────── */
.frn-preview-layout {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 20px;
  align-items: flex-start;
}

/* ── Detail card ─────────────────────────── */
.frn-detail-card { padding: 0; overflow: hidden; }
.frn-detail-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 20px 22px 16px;
  border-bottom: 1px solid var(--border-1);
  gap: 12px;
}
.frn-detail-franchise-name { font-size: 18px; font-weight: 700; color: var(--text-1); }
.frn-detail-sub { font-size: 12px; color: var(--text-3); margin-top: 3px; }
.frn-mode-badge {
  font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; flex-shrink: 0;
}
.frn-mode-wallet { background: rgba(30,120,200,.12); color: #1e78c8; border: 1px solid rgba(30,120,200,.25); }
.frn-mode-independent { background: rgba(42,138,74,.12); color: #2a8a4a; border: 1px solid rgba(42,138,74,.25); }

.frn-detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  padding: 16px 22px;
  gap: 0;
}
.frn-detail-item {
  padding: 10px 0;
  border-bottom: 1px solid var(--border-1, rgba(255,255,255,.05));
  display: flex; flex-direction: column; gap: 3px;
}
.frn-detail-full { grid-column: 1 / -1; }
.frn-detail-label { font-size: 11px; color: var(--text-3); text-transform: uppercase; letter-spacing: .5px; }
.frn-detail-value { font-size: 13px; color: var(--text-1); }

/* ── Payment card ────────────────────────── */
.frn-payment-card { margin-top: 16px; }
.frn-pay-table { display: flex; flex-direction: column; }
.frn-pay-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0; font-size: 13px;
  border-bottom: 1px solid var(--border-1, rgba(255,255,255,.05));
}
.frn-pay-total {
  border-bottom: none;
  padding: 14px 0 4px;
}
.frn-pay-note {
  font-size: 12px; line-height: 1.55; padding: 10px 14px;
  border-radius: var(--radius-sm); margin-top: 14px;
}
.frn-pay-note-blue { background: rgba(30,120,200,.08); border: 1px solid rgba(30,120,200,.2); color: #1e78c8; }
.frn-pay-note-amber { background: rgba(200,146,42,.08); border: 1px solid rgba(200,146,42,.25); color: #8b6520; }

/* ── Confirm card ────────────────────────── */
.frn-confirm-card { position: sticky; top: 80px; padding: 22px; }
.frn-confirm-title { font-size: 16px; font-weight: 700; color: var(--text-1); margin-bottom: 4px; }
.frn-confirm-sub { font-size: 12px; color: var(--text-3); line-height: 1.5; margin-bottom: 18px; }

.frn-confirm-amount-box {
  background: var(--bg-3);
  border: 1px solid var(--border-2);
  border-radius: var(--radius);
  padding: 14px 16px;
  margin-bottom: 16px;
  text-align: center;
}
.frn-confirm-amount-label { font-size: 11px; color: var(--text-3); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.frn-confirm-amount-value { font-size: 26px; font-weight: 800; color: var(--accent); }
.frn-confirm-amount-sub { font-size: 11px; color: var(--text-3); margin-top: 4px; }

.frn-confirm-checklist { margin-bottom: 18px; display: flex; flex-direction: column; gap: 6px; }
.frn-check-item { font-size: 12px; color: var(--text-2); display: flex; align-items: center; gap: 6px; }
.frn-check-item.pending-check { color: #8b6520; }

.frn-confirm-btn { width: 100%; justify-content: center; font-size: 14px; padding: 11px; }
.frn-back-link {
  display: block; text-align: center; margin-top: 12px;
  font-size: 12px; color: var(--text-3); text-decoration: none;
}
.frn-back-link:hover { color: var(--accent); }
</style>
@endpush
