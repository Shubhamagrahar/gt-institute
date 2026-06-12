@extends('layouts.institute')
@section('title', $franchise->name)
@section('page-title', 'Franchise Profile')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.edit', $franchise) }}" class="btn btn-primary btn-sm">Edit Franchise</a>
  <a href="{{ route('institute.franchises.index') }}" class="btn btn-outline btn-sm">← All Franchises</a>
@endsection

@section('content')

{{-- ── Profile Hero Card ────────────────────────────────── --}}
<div class="frn-hero">
  <div class="frn-hero-left">
    {{-- Logo --}}
    <div class="frn-hero-logo">
      @if(!empty($franchise->logo) && $franchise->logo !== 'images/default-institute.png')
        <img src="{{ asset($franchise->logo) }}" alt="{{ $franchise->name }}">
      @else
        <div class="frn-hero-logo-ph">{{ strtoupper(substr($franchise->name,0,2)) }}</div>
      @endif
    </div>

    {{-- Core info --}}
    <div class="frn-hero-info">
      <div class="frn-hero-name">{{ $franchise->name }}</div>
      @if($franchise->short_name)
        <div class="frn-hero-short">{{ $franchise->short_name }}</div>
      @endif
      <div class="frn-hero-meta">
        <span class="frn-hero-id">{{ $franchise->unique_id }}</span>
        <span class="frn-hero-dot">·</span>
        <span>{{ $franchise->level?->name ?? 'No Level' }}</span>
        <span class="frn-hero-dot">·</span>
        @if(($franchise->management_type ?? 'wallet') === 'wallet')
          <span class="frn-badge-wallet">Wallet System</span>
        @else
          <span class="frn-badge-indep">Independent</span>
        @endif
        <span class="frn-hero-dot">·</span>
        <span class="frn-badge-status {{ $franchise->status === 'active' ? 'active' : 'inactive' }}">{{ ucfirst($franchise->status) }}</span>
      </div>
      <div class="frn-hero-contact">
        <span>&#128222; {{ $franchise->mobile }}</span>
        <span class="frn-hero-dot">·</span>
        <span>&#9993; {{ $franchise->email }}</span>
        @if($franchise->address)
          <span class="frn-hero-dot">·</span>
          <span>&#127968; {{ $franchise->address }}{{ $franchise->state ? ', '.$franchise->state : '' }}</span>
        @endif
      </div>
    </div>
  </div>

  {{-- Quick actions --}}
  <div class="frn-hero-actions">
    <a href="{{ route('institute.franchises.edit', $franchise) }}" class="btn btn-primary btn-sm">Edit</a>
    @if(($franchise->management_type ?? 'wallet') === 'wallet')
      <a href="{{ route('institute.franchises.transactions', $franchise) }}" class="btn btn-outline btn-sm">Wallet Ledger</a>
    @else
      <a href="{{ route('institute.franchises.fee.index', $franchise) }}" class="btn btn-outline btn-sm">Fee Collection</a>
    @endif
    <a href="{{ route('institute.franchises.certificate', $franchise) }}" target="_blank" class="btn btn-sm frn-btn-cert">Certificate</a>
    <form method="POST" action="{{ route('institute.franchises.toggle', $franchise) }}" style="display:inline;">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-sm {{ $franchise->status === 'active' ? 'frn-btn-disable' : 'btn-success' }}">
        {{ $franchise->status === 'active' ? 'Disable' : 'Enable' }}
      </button>
    </form>
  </div>
</div>

{{-- ── Stat Cards ───────────────────────────────────────── --}}
<div class="frn-stats">
  @if(($franchise->management_type ?? 'wallet') === 'wallet')
    <div class="frn-stat">
      <div class="frn-stat-icon" style="background:rgba(30,120,200,.1); color:#1e78c8;">₹</div>
      <div>
        <div class="frn-stat-val mono">₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</div>
        <div class="frn-stat-lbl">Wallet Balance</div>
      </div>
    </div>
    <div class="frn-stat">
      <div class="frn-stat-icon" style="background:rgba(42,138,74,.1); color:#2a8a4a;">&#8595;</div>
      <div>
        <div class="frn-stat-val">₹{{ number_format($franchise->admission_charge ?? 0, 2) }}</div>
        <div class="frn-stat-lbl">Per Admission</div>
      </div>
    </div>
    <div class="frn-stat">
      <div class="frn-stat-icon" style="background:rgba(139,101,32,.1); color:#8b6520;">&#128196;</div>
      <div>
        <div class="frn-stat-val">₹{{ number_format($franchise->certificate_charge ?? 0, 2) }}</div>
        <div class="frn-stat-lbl">Per Certificate</div>
      </div>
    </div>
    <div class="frn-stat">
      <div class="frn-stat-icon" style="background:rgba(200,80,80,.1); color:#c84040;">!</div>
      <div>
        <div class="frn-stat-val">₹{{ number_format($franchise->low_wallet_alert ?? 0, 2) }}</div>
        <div class="frn-stat-lbl">Low Balance Alert</div>
      </div>
    </div>
  @else
    @php $outstanding = $franchise->feeOutstanding(); @endphp
    <div class="frn-stat">
      <div class="frn-stat-icon" style="background:rgba(42,138,74,.1); color:#2a8a4a;">₹</div>
      <div>
        <div class="frn-stat-val mono">₹{{ number_format($franchise->fee_total ?? 0, 2) }}</div>
        <div class="frn-stat-lbl">Total Onboarding Fee</div>
      </div>
    </div>
    <div class="frn-stat">
      <div class="frn-stat-icon" style="background:rgba(30,120,200,.1); color:#1e78c8;">&#10003;</div>
      <div>
        <div class="frn-stat-val mono">₹{{ number_format($franchise->feePaid(), 2) }}</div>
        <div class="frn-stat-lbl">Fee Collected</div>
      </div>
    </div>
    <div class="frn-stat">
      <div class="frn-stat-icon" style="background:{{ $outstanding > 0 ? 'rgba(220,53,69,.1)' : 'rgba(42,138,74,.1)' }}; color:{{ $outstanding > 0 ? 'var(--danger)' : '#2a8a4a' }};">
        {{ $outstanding > 0 ? '!' : '&#10003;' }}
      </div>
      <div>
        <div class="frn-stat-val mono" style="{{ $outstanding > 0 ? 'color:var(--danger)' : '' }}">₹{{ number_format($outstanding, 2) }}</div>
        <div class="frn-stat-lbl">Outstanding Due</div>
      </div>
    </div>
    <div class="frn-stat">
      <div class="frn-stat-icon" style="background:rgba(139,101,32,.1); color:#8b6520;">%</div>
      <div>
        <div class="frn-stat-val">{{ number_format($franchise->commission_percent, 2) }}%</div>
        <div class="frn-stat-lbl">Commission Rate</div>
      </div>
    </div>
  @endif
</div>

{{-- ── Details Grid ─────────────────────────────────────── --}}
<div class="frn-detail-grid">

  {{-- Franchise Info --}}
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Franchise Information</div>
    </div>
    <div class="frn-info-table">
      <div class="frn-info-row"><span class="frn-info-lbl">Franchise ID</span><span class="frn-info-val mono" style="color:var(--accent);">{{ $franchise->unique_id }}</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Full Name</span><span class="frn-info-val fw-600">{{ $franchise->name }}</span></div>
      @if($franchise->short_name)
        <div class="frn-info-row"><span class="frn-info-lbl">Short Name</span><span class="frn-info-val">{{ $franchise->short_name }}</span></div>
      @endif
      <div class="frn-info-row"><span class="frn-info-lbl">Email</span><span class="frn-info-val">{{ $franchise->email }}</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Mobile</span><span class="frn-info-val">{{ $franchise->mobile }}</span></div>
      @if($franchise->website)
        <div class="frn-info-row"><span class="frn-info-lbl">Website</span><span class="frn-info-val"><a href="{{ $franchise->website }}" target="_blank" style="color:var(--accent);">{{ $franchise->website }}</a></span></div>
      @endif
      <div class="frn-info-row"><span class="frn-info-lbl">Level</span><span class="frn-info-val">{{ $franchise->level?->name ?? 'Not assigned' }}</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Commission</span><span class="frn-info-val">{{ number_format($franchise->commission_percent, 2) }}%</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Sub-Franchise</span><span class="frn-info-val">{{ $franchise->has_sub_franchise ? 'Allowed' : 'Not Allowed' }}</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Status</span>
        <span class="frn-info-val">
          <span class="badge {{ $franchise->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($franchise->status) }}</span>
        </span>
      </div>
      @if($franchise->address)
        <div class="frn-info-row"><span class="frn-info-lbl">Address</span><span class="frn-info-val">{{ $franchise->address }}{{ $franchise->state ? ', '.$franchise->state : '' }}{{ $franchise->pin_code ? ' — '.$franchise->pin_code : '' }}</span></div>
      @endif
    </div>
  </div>

  {{-- Owner & Login --}}
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Owner &amp; Login Details</div>
    </div>
    <div class="frn-info-table">
      <div class="frn-info-row"><span class="frn-info-lbl">Owner Name</span><span class="frn-info-val fw-600">{{ $franchise->owner_name }}</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Owner Mobile</span><span class="frn-info-val">{{ $franchise->owner_mobile }}</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Login User ID</span><span class="frn-info-val mono">{{ $franchise->head?->user_id ?? 'Not created' }}</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Login Email</span><span class="frn-info-val">{{ $franchise->head?->email ?? '—' }}</span></div>
      <div class="frn-info-row"><span class="frn-info-lbl">Account Status</span>
        <span class="frn-info-val">
          <span class="badge {{ ($franchise->head?->status ?? '') === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($franchise->head?->status ?? 'NA') }}</span>
        </span>
      </div>
    </div>

    {{-- Management mode details --}}
    <div style="margin-top:16px; padding-top:16px; border-top:1px solid var(--border-1);">
      <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">Management Mode</div>
      @if(($franchise->management_type ?? 'wallet') === 'wallet')
        <div style="background:rgba(30,120,200,.07); border:1px solid rgba(30,120,200,.18); border-radius:var(--radius); padding:12px 14px;">
          <div style="font-size:13px; font-weight:700; color:#1e78c8; margin-bottom:6px;">Wallet System</div>
          <div class="frn-info-table">
            <div class="frn-info-row"><span class="frn-info-lbl">Wallet</span><span class="frn-info-val">{{ $franchise->wallet_enabled ? 'Enabled' : 'Disabled' }}</span></div>
            <div class="frn-info-row"><span class="frn-info-lbl">Admission Charge</span><span class="frn-info-val">₹{{ number_format($franchise->admission_charge ?? 0, 2) }}</span></div>
            <div class="frn-info-row"><span class="frn-info-lbl">Certificate Charge</span><span class="frn-info-val">₹{{ number_format($franchise->certificate_charge ?? 0, 2) }}</span></div>
            <div class="frn-info-row"><span class="frn-info-lbl">Low Balance Alert</span><span class="frn-info-val">₹{{ number_format($franchise->low_wallet_alert ?? 0, 2) }}</span></div>
          </div>
        </div>
      @else
        <div style="background:rgba(42,138,74,.07); border:1px solid rgba(42,138,74,.18); border-radius:var(--radius); padding:12px 14px;">
          <div style="font-size:13px; font-weight:700; color:#2a8a4a; margin-bottom:6px;">Independent Mode</div>
          <div class="frn-info-table">
            <div class="frn-info-row"><span class="frn-info-lbl">Onboarding Fee</span><span class="frn-info-val">₹{{ number_format($franchise->onboarding_fee ?? 0, 2) }}</span></div>
            <div class="frn-info-row"><span class="frn-info-lbl">Total Fee (Level)</span><span class="frn-info-val">₹{{ number_format($franchise->fee_total ?? 0, 2) }}</span></div>
          </div>
        </div>
      @endif
    </div>
  </div>

</div>

@push('styles')
<style>
/* ─── Hero Card ─────────────────────────────── */
.frn-hero {
  display: flex; align-items: flex-start;
  justify-content: space-between; gap: 20px;
  background: var(--bg-2);
  border: 1px solid var(--border-1);
  border-radius: var(--radius);
  padding: 20px 22px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.frn-hero-left { display: flex; align-items: center; gap: 18px; flex: 1; min-width: 0; }

.frn-hero-logo {
  flex-shrink: 0;
  width: 74px; height: 74px; border-radius: 12px;
  border: 2px solid var(--border-2);
  overflow: hidden; background: var(--bg-3);
}
.frn-hero-logo img { width:100%; height:100%; object-fit:contain; padding:4px; }
.frn-hero-logo-ph {
  width:100%; height:100%;
  display:flex; align-items:center; justify-content:center;
  font-size:22px; font-weight:800; color:var(--accent);
  background:rgba(var(--accent-rgb),.1);
  letter-spacing:1px;
}

.frn-hero-name { font-size:20px; font-weight:800; color:var(--text-1); }
.frn-hero-short { font-size:13px; color:var(--text-3); margin-top:2px; }
.frn-hero-meta { display:flex; align-items:center; flex-wrap:wrap; gap:6px; margin-top:6px; font-size:13px; color:var(--text-2); }
.frn-hero-id { font-family:monospace; font-size:12px; background:var(--bg-3); padding:2px 7px; border-radius:4px; border:1px solid var(--border-2); }
.frn-hero-dot { color:var(--text-3); }
.frn-hero-contact { display:flex; flex-wrap:wrap; gap:10px; margin-top:6px; font-size:12.5px; color:var(--text-3); }

.frn-badge-wallet { background:rgba(30,120,200,.12); color:#1e78c8; border:1px solid rgba(30,120,200,.2); padding:2px 8px; border-radius:20px; font-size:12px; font-weight:600; }
.frn-badge-indep  { background:rgba(42,138,74,.12);  color:#2a8a4a; border:1px solid rgba(42,138,74,.2);  padding:2px 8px; border-radius:20px; font-size:12px; font-weight:600; }
.frn-badge-status.active   { background:rgba(42,138,74,.12); color:#2a8a4a; border:1px solid rgba(42,138,74,.2);  padding:2px 8px; border-radius:20px; font-size:12px; font-weight:600; }
.frn-badge-status.inactive { background:rgba(255,184,77,.12); color:#c87800; border:1px solid rgba(255,184,77,.2); padding:2px 8px; border-radius:20px; font-size:12px; font-weight:600; }

.frn-hero-actions { display:flex; flex-direction:column; gap:8px; flex-shrink:0; }
.frn-hero-actions .btn { white-space:nowrap; text-align:center; }
.frn-btn-cert    { background:rgba(139,101,32,.12); color:#8b6520; border:1px solid rgba(139,101,32,.25); }
.frn-btn-disable { background:rgba(220,53,69,.1);   color:var(--danger); border:1px solid rgba(220,53,69,.2); }

/* ─── Stats ──────────────────────────────────── */
.frn-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 20px;
}
.frn-stat {
  background: var(--bg-2);
  border: 1px solid var(--border-1);
  border-radius: var(--radius);
  padding: 14px 16px;
  display: flex; align-items: center; gap: 14px;
}
.frn-stat-icon {
  width: 42px; height: 42px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; font-weight: 700; flex-shrink: 0;
}
.frn-stat-val { font-size: 18px; font-weight: 800; color: var(--text-1); }
.frn-stat-lbl { font-size: 11px; color: var(--text-3); margin-top: 2px; text-transform: uppercase; letter-spacing: .5px; }

/* ─── Detail Grid ────────────────────────────── */
.frn-detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.frn-info-table { display: flex; flex-direction: column; }
.frn-info-row {
  display: flex; justify-content: space-between; align-items: flex-start;
  padding: 8px 0; border-bottom: 1px solid var(--border-1, rgba(255,255,255,.05));
  gap: 12px;
}
.frn-info-row:last-child { border-bottom: none; }
.frn-info-lbl { font-size: 12px; color: var(--text-3); white-space: nowrap; flex-shrink: 0; padding-top: 1px; }
.frn-info-val { font-size: 13px; color: var(--text-1); text-align: right; }
</style>
@endpush

@endsection
