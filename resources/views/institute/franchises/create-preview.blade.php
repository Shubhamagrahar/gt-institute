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
  $instituteName = auth('institute')->user()->institute->name ?? 'Institute';
@endphp

{{-- Step progress --}}
<div class="frn-wizard">
  <div class="frn-wizard-step done"><span class="frn-step-dot">✓</span><span class="frn-step-lbl">Franchise Details</span></div>
  <div class="frn-wizard-line done"></div>
  @if($isWallet)
  <div class="frn-wizard-step done"><span class="frn-step-dot">✓</span><span class="frn-step-lbl">Course Access</span></div>
  <div class="frn-wizard-line done"></div>
  <div class="frn-wizard-step active"><span class="frn-step-dot">3</span><span class="frn-step-lbl">Review & Confirm</span></div>
  <div class="frn-wizard-line"></div>
  <div class="frn-wizard-step"><span class="frn-step-dot">4</span><span class="frn-step-lbl">Fee Collection</span></div>
  @else
  <div class="frn-wizard-step active"><span class="frn-step-dot">2</span><span class="frn-step-lbl">Review & Payment</span></div>
  <div class="frn-wizard-line"></div>
  <div class="frn-wizard-step"><span class="frn-step-dot">3</span><span class="frn-step-lbl">Fee Collection</span></div>
  @endif
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
          <span class="frn-detail-value">{{ $data['address'] }}{{ !empty($data['district']) ? ', '.$data['district'] : '' }}{{ !empty($data['state']) ? ', '.$data['state'] : '' }}{{ !empty($data['pin_code']) ? ' — '.$data['pin_code'] : '' }}</span>
        </div>
        @endif
        @if($isWallet)
          <div class="frn-detail-item">
            <span class="frn-detail-label">Low Wallet Alert</span>
            <span class="frn-detail-value mono">₹{{ number_format($data['low_wallet_alert'] ?? 0, 2) }}</span>
          </div>
        @endif
      </div>
    </div>

    {{-- Course Access Summary (wallet only) --}}
    @if($isWallet)
    <div class="gt-card frn-payment-card" style="margin-top:16px;">
      <div class="gt-card-header" style="border-bottom:1px solid var(--border-1); padding-bottom:12px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
        <div class="gt-card-title">Course Access</div>
        <a href="{{ route('institute.franchises.charges') }}" style="font-size:12px; color:var(--accent);">Edit</a>
      </div>
      @if($courseTypes->isEmpty())
        <div style="font-size:12px; color:var(--text-3); text-align:center; padding:14px 0;">
          No course types selected — franchise will have no admission access.
        </div>
      @else
        <table style="width:100%; border-collapse:collapse; font-size:12.5px;">
          <thead>
            <tr>
              <th style="text-align:left; color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:.4px; padding:0 0 8px; border-bottom:1px solid var(--border-1);">Course Type</th>
              <th style="text-align:center; color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:.4px; padding:0 0 8px; border-bottom:1px solid var(--border-1);">Courses</th>
              <th style="text-align:right; color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:.4px; padding:0 0 8px; border-bottom:1px solid var(--border-1);">Admission</th>
              <th style="text-align:right; color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:.4px; padding:0 0 8px; border-bottom:1px solid var(--border-1);">Certificate</th>
            </tr>
          </thead>
          <tbody>
            @foreach($courseTypes as $ct)
            @php $ch = $levelChargesByType[$ct->id] ?? null; @endphp
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid var(--border-1); color:var(--text-1); font-weight:600; font-size:13px;">
                {{ $ct->name }}
              </td>
              <td style="padding:8px 0; border-bottom:1px solid var(--border-1); text-align:center; color:var(--text-3);">
                {{ $ch->course_count ?? 0 }}
              </td>
              <td style="padding:8px 0; border-bottom:1px solid var(--border-1); text-align:right;" class="mono">
                @if($ch && $ch->min_adm > 0)
                  ₹{{ number_format($ch->min_adm, 0) }}{{ $ch->min_adm != $ch->max_adm ? '–'.number_format($ch->max_adm, 0) : '' }}
                @else
                  <span style="color:var(--text-3);">₹0</span>
                @endif
              </td>
              <td style="padding:8px 0; border-bottom:1px solid var(--border-1); text-align:right;" class="mono">
                @if($ch && $ch->min_cert > 0)
                  ₹{{ number_format($ch->min_cert, 0) }}{{ $ch->min_cert != $ch->max_cert ? '–'.number_format($ch->max_cert, 0) : '' }}
                @else
                  <span style="color:var(--text-3);">₹0</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div style="font-size:11.5px; color:var(--text-3); margin-top:10px;">
          Charges inherited from <strong>{{ $data['_level']['name'] ?? 'level' }}</strong> configuration. Fine-tune per course after creation.
        </div>
      @endif
    </div>
    @endif

    {{-- Joining Fee Card (applies to ALL modes) --}}
    <div class="gt-card frn-payment-card" style="margin-top:16px;">
      <div class="gt-card-header" style="border-bottom:1px solid var(--border-1); padding-bottom:12px; margin-bottom:16px;">
        <div class="gt-card-title">Franchise Joining Fee</div>
      </div>

      <div class="frn-pay-table">
        <div class="frn-pay-row">
          <span>Level — {{ $level['name'] ?? '—' }}</span>
          <span class="mono">₹{{ number_format($levelFee, 2) }}</span>
        </div>
        <div class="frn-pay-row frn-pay-total">
          <span class="fw-600">Total Due from Franchise</span>
          <span class="mono fw-600" style="font-size:17px; color:var(--accent);">₹{{ number_format($levelFee, 2) }}</span>
        </div>
      </div>

      <div class="frn-pay-note frn-pay-note-amber">
        @if($levelFee > 0)
          Payable by franchise to {{ $instituteName }}. Collect via the fee collection page after creation.
        @else
          No joining fee for this level.
        @endif
      </div>
    </div>

    {{-- Operational Wallet (wallet mode only) --}}
    @if($isWallet)
    <div class="gt-card frn-payment-card" style="margin-top:12px;">
      <div class="gt-card-header" style="border-bottom:1px solid var(--border-1); padding-bottom:12px; margin-bottom:16px;">
        <div class="gt-card-title">Operational Wallet</div>
      </div>
      <div class="frn-pay-table">
        <div class="frn-pay-row">
          <span>Opening wallet balance</span>
          <span class="mono">₹{{ number_format((float)($data['opening_balance'] ?? 0), 2) }}</span>
        </div>
      </div>
      <div class="frn-pay-note frn-pay-note-blue">
        Credited to franchise wallet immediately on creation. Used for per-admission &amp; certificate deductions.
      </div>
    </div>
    @endif

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
        <div class="frn-confirm-amount-label">Joining Fee Due</div>
        <div class="frn-confirm-amount-value">₹{{ number_format($levelFee, 2) }}</div>
        <div class="frn-confirm-amount-sub">
          {{ $levelFee > 0 ? 'Collect via fee page after creation' : 'No joining fee for this level' }}
        </div>
      </div>

      <div class="frn-confirm-checklist">
        <div class="frn-check-item">✓ Franchise account created</div>
        <div class="frn-check-item">✓ Login credentials emailed</div>
        @if($isWallet)
          <div class="frn-check-item">✓ Operational wallet activated</div>
          @if((float)($data['opening_balance'] ?? 0) > 0)
            <div class="frn-check-item">✓ ₹{{ number_format((float)($data['opening_balance'] ?? 0), 2) }} opening balance credited</div>
          @endif
        @else
          <div class="frn-check-item">✓ Independent access granted</div>
        @endif
        @if($levelFee > 0)
          <div class="frn-check-item pending-check">→ Joining fee ₹{{ number_format($levelFee, 2) }} pending collection</div>
        @endif
      </div>

      <form method="POST" action="{{ route('institute.franchises.confirm') }}">
        @csrf
        <button type="submit" class="btn btn-primary frn-confirm-btn">
          ✓ Confirm & Create Franchise
        </button>
      </form>

      <a href="{{ $isWallet ? route('institute.franchises.charges') : route('institute.franchises.create') }}" class="frn-back-link">
        ← {{ $isWallet ? 'Go back and edit charges' : 'Go back and edit details' }}
      </a>
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
