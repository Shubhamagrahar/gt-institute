@extends('layouts.institute')
@section('title', 'Franchises')
@section('page-title', 'Franchises')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.create') }}" class="btn btn-primary btn-sm">+ Add Franchise</a>
@endsection

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Franchises ({{ $franchises->count() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search franchise...">
  </div>

  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Franchise</th>
          <th>Owner</th>
          <th>Level</th>
          <th>Login ID</th>
          <th>Mode</th>
          <th>Wallet</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($franchises as $franchise)
          <tr>
            <td><code style="font-size:11px;color:var(--accent);">{{ $franchise->unique_id }}</code></td>
            <td>
              <a href="{{ route('institute.franchises.show', $franchise) }}" class="fw-600" style="color:var(--accent); text-decoration:none;">{{ $franchise->name }}</a>
              <div class="text-xs text-muted">{{ $franchise->email }}</div>
            </td>
            <td>
              <div>{{ $franchise->owner_name }}</div>
              <div class="text-xs text-muted">{{ $franchise->owner_mobile }}</div>
            </td>
            <td>
              <div>{{ $franchise->level?->name ?? 'NA' }}</div>
              <div class="text-xs text-muted">{{ number_format($franchise->commission_percent, 2) }}%</div>
            </td>
            <td><span class="mono">{{ $franchise->head?->user_id ?? 'Pending' }}</span></td>
            <td>
              @if(($franchise->management_type ?? 'wallet') === 'wallet')
                <span class="badge badge-info" style="background:rgba(30,120,200,.12);color:#1e78c8;border:1px solid rgba(30,120,200,.2);">💳 Wallet</span>
              @else
                <span class="badge" style="background:rgba(80,180,80,.12);color:#2a7a2a;border:1px solid rgba(80,180,80,.2);">🏢 Independent</span>
                @if($franchise->onboarding_fee > 0)
                  <div class="text-xs text-muted" style="margin-top:2px;">Fee: ₹{{ number_format($franchise->onboarding_fee,0) }}</div>
                @endif
              @endif
            </td>
            <td>
              @if(($franchise->management_type ?? 'wallet') === 'wallet')
                <div class="mono">₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</div>
                <div class="text-xs text-muted">{{ $franchise->wallet_enabled ? 'Active' : 'Disabled' }}</div>
              @else
                <div class="text-xs text-muted" style="color:var(--text-3);">—</div>
              @endif
            </td>
            <td><span class="badge {{ $franchise->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($franchise->status) }}</span></td>
            <td>
              <div class="flex gap-2" style="flex-wrap:wrap;">
                <a href="{{ route('institute.franchises.edit', $franchise) }}" class="btn btn-outline btn-xs">Edit</a>
                @if(($franchise->management_type ?? 'wallet') === 'wallet')
                  <a href="{{ route('institute.franchises.transactions', $franchise) }}" class="btn btn-outline btn-xs">Ledger</a>
                  <button type="button" class="btn btn-outline btn-xs"
                    onclick="openCfModal({{ $franchise->id }}, {{ json_encode($franchise->name) }})">
                    Course Fees
                  </button>
                @else
                  @php $feeOutstanding = $franchise->feeOutstanding(); @endphp
                  <a href="{{ route('institute.franchises.fee.index', $franchise) }}"
                     class="btn btn-xs {{ $feeOutstanding > 0 ? '' : 'btn-outline' }}"
                     style="{{ $feeOutstanding > 0 ? 'background:rgba(220,53,69,.12);color:var(--danger);border:1px solid rgba(220,53,69,.2);' : '' }}">
                    Fee{{ $feeOutstanding > 0 ? ' (₹'.number_format($feeOutstanding,0).' due)' : '' }}
                  </a>
                @endif
                <a href="{{ route('institute.franchises.certificate', $franchise) }}" target="_blank"
                  class="btn btn-xs" style="background:rgba(200,146,42,.12);color:#8b6520;border:1px solid rgba(200,146,42,.3);"
                  title="Download Franchise Certificate">📜 Certificate</a>
                <form method="POST" action="{{ route('institute.franchises.resend-credentials', $franchise) }}"
                  onsubmit="return confirm('Resend login credentials to {{ $franchise->email }}?\n\nThis will reset the current password and email new credentials.')">
                  @csrf
                  <button type="submit" class="btn btn-xs"
                    style="background:rgba(99,102,241,.12);color:#4338ca;border:1px solid rgba(99,102,241,.25);"
                    title="Resend login credentials via email">
                    🔑 Credentials
                  </button>
                </form>
                <form method="POST" action="{{ route('institute.franchises.toggle', $franchise) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn btn-xs {{ $franchise->status === 'active' ? '' : 'btn-success' }}" style="{{ $franchise->status === 'active' ? 'background:var(--warning-bg);color:var(--warning);border:1px solid rgba(255,184,77,.2);' : '' }}">
                    {{ $franchise->status === 'active' ? 'Disable' : 'Enable' }}
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9">
              <div class="gt-empty">
                <div class="gt-empty-title">No franchises added yet</div>
                <a href="{{ route('institute.franchises.create') }}" class="btn btn-primary btn-sm">Add First Franchise</a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Course Fees Modal --}}
<div id="cf-modal"
  style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:1000; align-items:center; justify-content:center;">
  <div style="background:var(--bg-2); border-radius:var(--radius); width:620px; max-width:96vw; max-height:82vh; display:flex; flex-direction:column; box-shadow:0 8px 40px rgba(0,0,0,.35);">

    {{-- Modal header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-1); flex-shrink:0;">
      <div>
        <div id="cf-modal-title" style="font-size:15px; font-weight:700; color:var(--text-1);"></div>
        <div id="cf-modal-sub" style="font-size:11.5px; color:var(--text-3); margin-top:2px;"></div>
      </div>
      <button onclick="closeCfModal()" style="background:none; border:none; color:var(--text-3); cursor:pointer; font-size:18px; padding:4px 8px; line-height:1;">✕</button>
    </div>

    {{-- Search --}}
    <div style="padding:12px 20px; border-bottom:1px solid var(--border-1); flex-shrink:0;">
      <input id="cf-search" type="text" class="gt-input" placeholder="Search course name…"
        style="width:100%;" oninput="cfDebouncedSearch(this.value)">
    </div>

    {{-- Table body --}}
    <div id="cf-modal-body" style="overflow-y:auto; flex:1; padding:0 20px 16px;">
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
let cfAllData = [];

function openCfModal(franchiseId, franchiseName) {
  const modal = document.getElementById('cf-modal');
  document.getElementById('cf-modal-title').textContent = franchiseName + ' — Course Fees';
  document.getElementById('cf-modal-sub').textContent = 'Admission & certificate deduction per course';
  document.getElementById('cf-search').value = '';
  document.getElementById('cf-modal-body').innerHTML =
    '<div style="text-align:center; padding:36px; color:var(--text-3); font-size:13px;">Loading…</div>';
  modal.style.display = 'flex';

  fetch(`/institute/franchises/${franchiseId}/course-charges`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(r => r.json())
    .then(data => {
      cfAllData = data;
      cfRenderTable(data);
    })
    .catch(() => {
      document.getElementById('cf-modal-body').innerHTML =
        '<div style="color:var(--danger); padding:20px 0; font-size:13px;">Failed to load data. Please try again.</div>';
    });
}

function closeCfModal() {
  document.getElementById('cf-modal').style.display = 'none';
  cfAllData = [];
}

function cfRenderTable(data) {
  const body = document.getElementById('cf-modal-body');
  if (!data.length) {
    body.innerHTML =
      '<div style="text-align:center; padding:36px; color:var(--text-3); font-size:13px;">No course charges configured for this franchise.</div>';
    return;
  }

  let html = '<table style="width:100%; border-collapse:collapse; font-size:13px; margin-top:12px;">';
  html += '<thead><tr>';
  html += '<th style="text-align:left; padding:8px 6px; border-bottom:1px solid var(--border-2); color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:.4px;">Course</th>';
  html += '<th style="text-align:center; padding:8px 6px; border-bottom:1px solid var(--border-2); color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:.4px; width:90px;">Duration</th>';
  html += '<th style="text-align:right; padding:8px 6px; border-bottom:1px solid var(--border-2); color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:.4px; width:120px;">Admission</th>';
  html += '<th style="text-align:right; padding:8px 6px; border-bottom:1px solid var(--border-2); color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:.4px; width:120px;">Certificate</th>';
  html += '</tr></thead><tbody>';

  data.forEach(row => {
    const adm  = parseFloat(row.admission_charge  || 0).toFixed(2);
    const cert = parseFloat(row.certificate_charge || 0).toFixed(2);
    html += '<tr>';
    html += `<td style="padding:9px 6px; border-bottom:1px solid var(--border-1); color:var(--text-1);">${cfEsc(row.course_name)}</td>`;
    html += `<td style="padding:9px 6px; border-bottom:1px solid var(--border-1); text-align:center;">
               <span style="font-size:11px; font-weight:600; background:rgba(138,115,245,.12); color:rgba(138,115,245,.9); border:1px solid rgba(138,115,245,.25); border-radius:20px; padding:2px 8px;">${row.duration}m</span>
             </td>`;
    html += `<td style="padding:9px 6px; border-bottom:1px solid var(--border-1); text-align:right; font-family:monospace; color:var(--text-1);">₹${adm}</td>`;
    html += `<td style="padding:9px 6px; border-bottom:1px solid var(--border-1); text-align:right; font-family:monospace; color:var(--text-1);">₹${cert}</td>`;
    html += '</tr>';
  });

  html += '</tbody></table>';
  html += `<div style="font-size:11.5px; color:var(--text-3); margin-top:10px; padding:4px 6px;">${data.length} course${data.length !== 1 ? 's' : ''} configured</div>`;
  body.innerHTML = html;
}

function cfEsc(s) {
  return String(s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

let cfSearchTimer;
function cfDebouncedSearch(val) {
  clearTimeout(cfSearchTimer);
  cfSearchTimer = setTimeout(() => {
    const q = val.toLowerCase().trim();
    cfRenderTable(q
      ? cfAllData.filter(r => r.course_name.toLowerCase().includes(q))
      : cfAllData
    );
  }, 300);
}

// Close on backdrop click
document.getElementById('cf-modal').addEventListener('click', function(e) {
  if (e.target === this) closeCfModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeCfModal();
});
</script>
@endpush
