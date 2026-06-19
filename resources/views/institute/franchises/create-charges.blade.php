@extends('layouts.institute')
@section('title', 'Set Duration Charges')
@section('page-title', 'Set Duration Charges')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.create') }}" class="btn btn-outline btn-sm">← Edit Details</a>
@endsection

@section('content')

{{-- Step progress --}}
<div class="frn-wizard">
  <div class="frn-wizard-step done">
    <span class="frn-step-dot">✓</span>
    <span class="frn-step-lbl">Franchise Details</span>
  </div>
  <div class="frn-wizard-line done"></div>
  <div class="frn-wizard-step active">
    <span class="frn-step-dot">2</span>
    <span class="frn-step-lbl">Duration Charges</span>
  </div>
  <div class="frn-wizard-line"></div>
  <div class="frn-wizard-step">
    <span class="frn-step-dot">3</span>
    <span class="frn-step-lbl">Review & Payment</span>
  </div>
</div>

<div class="gt-card">

  <div class="gt-card-header" style="border-bottom:1px solid var(--border-1); padding-bottom:14px; margin-bottom:20px;">
    <div>
      <div class="gt-card-title">Duration-wise Wallet Charges</div>
      <div style="font-size:12.5px; color:var(--text-3); margin-top:3px;">
        Set admission &amp; certificate deduction per duration for
        <strong style="color:var(--text-2);">{{ $data['name'] }}</strong>.
        All courses of that duration will use the same charge. Leave blank (₹0) for no deduction.
      </div>
    </div>
  </div>

  @if($durations->isEmpty())
    <div style="text-align:center; padding:40px 20px; color:var(--text-3);">
      <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" opacity=".4" style="margin-bottom:10px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      <div style="font-size:14px; margin-bottom:6px;">No courses with duration found</div>
      <div style="font-size:12px;">Add courses with duration set, then configure charges.</div>
      <a href="{{ route('institute.franchises.preview') }}" class="btn btn-outline btn-sm" style="margin-top:14px;">Skip → Go to Preview</a>
    </div>
  @else

  <form method="POST" action="{{ route('institute.franchises.charges.store') }}">
    @csrf

    <div class="cc-table-wrap">
      <table class="cc-table">
        <thead>
          <tr>
            <th>Duration</th>
            <th style="width:130px; text-align:center;">Courses</th>
            <th style="width:160px;">Admission (₹)</th>
            <th style="width:160px;">Certificate (₹)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($durations as $dur)
          @php
            $saved = $existing[$dur->duration] ?? null;
            $adm   = $saved['admission_charge']   ?? '';
            $cert  = $saved['certificate_charge'] ?? '';
          @endphp
          <tr>
            <td>
              <input type="hidden" name="duration[]" value="{{ $dur->duration }}">
              <span class="cc-dur-badge">{{ $dur->duration }} month{{ $dur->duration > 1 ? 's' : '' }}</span>
            </td>
            <td style="text-align:center;">
              <span style="font-size:12px; color:var(--text-3);">{{ $dur->course_count }} course{{ $dur->course_count != 1 ? 's' : '' }}</span>
            </td>
            <td>
              <div class="cc-input-wrap">
                <span class="cc-rupee">₹</span>
                <input type="number" name="admission_charge[]" class="cc-input"
                       value="{{ $adm }}" min="0" step="0.01" placeholder="0.00">
              </div>
            </td>
            <td>
              <div class="cc-input-wrap">
                <span class="cc-rupee">₹</span>
                <input type="number" name="certificate_charge[]" class="cc-input"
                       value="{{ $cert }}" min="0" step="0.01" placeholder="0.00">
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="cc-footer">
      <div class="cc-footer-note">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Charges apply to all courses of that duration. ₹0 means no deduction. Duration is a snapshot — later course changes won't affect existing charges.
      </div>
      <button type="submit" class="btn btn-primary">
        Continue to Review
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
    </div>

  </form>
  @endif

</div>
@endsection

@push('styles')
<style>
/* ── Wizard ─────────────────────────────── */
.frn-wizard { display:flex; align-items:center; margin-bottom:28px; }
.frn-wizard-step { display:flex; flex-direction:column; align-items:center; gap:5px; flex-shrink:0; }
.frn-step-dot {
  width:34px; height:34px; border-radius:50%;
  border:2px solid var(--border-2);
  display:flex; align-items:center; justify-content:center;
  font-size:13px; font-weight:700;
  color:var(--text-3); background:var(--bg-3);
}
.frn-step-lbl { font-size:11px; color:var(--text-3); white-space:nowrap; }
.frn-wizard-step.active .frn-step-dot { border-color:var(--accent); background:var(--accent); color:#fff; }
.frn-wizard-step.active .frn-step-lbl { color:var(--accent); font-weight:600; }
.frn-wizard-step.done  .frn-step-dot  { border-color:#2a8a4a; background:#2a8a4a; color:#fff; }
.frn-wizard-step.done  .frn-step-lbl  { color:#2a8a4a; }
.frn-wizard-line { flex:1; height:2px; background:var(--border-2); margin:0 10px; margin-bottom:20px; }
.frn-wizard-line.done { background:#2a8a4a; }

/* ── Duration charges table ─────────────── */
.cc-table-wrap { overflow-x:auto; border:1px solid var(--border-2); border-radius:var(--radius); }
.cc-table { width:100%; border-collapse:collapse; font-size:13px; }
.cc-table thead th {
  background:var(--bg-3); color:var(--text-3);
  font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;
  padding:10px 14px; text-align:left;
  border-bottom:1px solid var(--border-2);
}
.cc-table tbody tr { border-bottom:1px solid var(--border-1); }
.cc-table tbody tr:last-child { border-bottom:none; }
.cc-table tbody tr:hover { background:var(--bg-3); }
.cc-table td { padding:12px 14px; vertical-align:middle; }

.cc-dur-badge {
  font-size:12px; font-weight:700;
  background:rgba(138,115,245,.12); color:rgba(138,115,245,.9);
  border:1px solid rgba(138,115,245,.25); border-radius:20px;
  padding:4px 12px; white-space:nowrap;
}

.cc-input-wrap { display:flex; align-items:center; }
.cc-rupee {
  padding:6px 9px; font-size:13px; color:var(--text-3);
  background:var(--bg-3); border:1px solid var(--border-2);
  border-right:none; border-radius:var(--radius-sm) 0 0 var(--radius-sm);
}
.cc-input {
  flex:1; min-width:0;
  background:var(--bg-2); border:1px solid var(--border-2);
  border-radius:0 var(--radius-sm) var(--radius-sm) 0;
  color:var(--text-1); font-size:13px;
  padding:6px 9px; outline:none;
  transition:border-color .15s;
}
.cc-input:focus { border-color:var(--accent); }

.cc-footer {
  display:flex; align-items:center; justify-content:space-between;
  gap:16px; margin-top:20px; flex-wrap:wrap;
}
.cc-footer-note {
  display:flex; align-items:center; gap:7px;
  font-size:12px; color:var(--text-3); flex:1;
}
</style>
@endpush
