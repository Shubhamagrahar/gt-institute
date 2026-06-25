@extends('layouts.franchise')
@section('title', 'Course Pricing')
@section('page-title', 'Course Pricing')

@push('styles')
<style>
.pr-info-box{background:rgba(234,88,12,.06);border:1px solid rgba(234,88,12,.2);border-radius:12px;padding:14px 18px;margin-bottom:22px;font-size:13px;color:var(--text-2);line-height:1.7;}
.pr-info-box strong{color:#fb923c;}

.pr-type-label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3);margin:24px 0 12px;padding:0 2px;display:flex;align-items:center;gap:8px;}
.pr-type-label::after{content:'';flex:1;height:1px;background:var(--border-2);}

/* Course card */
.pr-course-card{background:var(--bg-2);border:1px solid var(--border);border-radius:14px;margin-bottom:14px;overflow:hidden;}
.pr-course-head{padding:16px 20px;display:grid;grid-template-columns:1fr auto;gap:16px;align-items:start;}
.pr-course-name{font-size:14px;font-weight:700;color:var(--text-1);margin-bottom:4px;}
.pr-course-dur{font-size:12px;color:var(--text-3);}
.pr-charges-row{display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;align-items:center;}

.pr-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 11px;border-radius:20px;font-size:12px;font-weight:700;}
.pr-pill-adm{background:rgba(234,88,12,.1);color:#ea580c;border:1px solid rgba(234,88,12,.2);}
.pr-pill-cert{background:rgba(139,92,246,.1);color:#7c3aed;border:1px solid rgba(139,92,246,.2);}
.pr-pill-cap{background:var(--bg-3);color:var(--text-3);border:1px solid var(--border);font-weight:600;}
.pr-pill-margin-pos{background:rgba(22,163,74,.1);color:#16a34a;border:1px solid rgba(22,163,74,.2);}
.pr-pill-margin-neg{background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.15);}
.pr-pill-margin-zero{background:var(--bg-3);color:var(--text-3);border:1px solid var(--border);}

.pr-fee-input{width:110px;padding:6px 10px;border:1px solid var(--border);border-radius:8px;background:var(--bg-3);color:var(--text-1);font-size:13px;font-weight:600;font-family:monospace;text-align:right;transition:border-color .15s,box-shadow .15s;}
.pr-fee-input:focus{outline:none;border-color:#ea580c;box-shadow:0 0 0 3px rgba(234,88,12,.15);}

.pr-save-btn{padding:7px 16px;background:linear-gradient(135deg,#ea580c,#f97316);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;}
.pr-save-btn:hover{opacity:.9;}

/* Fee structures section */
.pr-fee-section{border-top:1px solid var(--border);padding:14px 20px;background:var(--bg-3);}
.pr-fee-section-title{font-size:11.5px;font-weight:700;color:var(--text-2);margin-bottom:12px;text-transform:uppercase;letter-spacing:.06em;}
.pr-fee-row{display:grid;grid-template-columns:1fr 100px 120px;gap:10px;align-items:center;padding:7px 0;border-bottom:1px solid var(--border-2);}
.pr-fee-row:last-child{border-bottom:none;}
.pr-fee-row-head{display:grid;grid-template-columns:1fr 100px 120px;gap:10px;padding:5px 0 8px;border-bottom:1px solid var(--border);}
.pr-fee-row-head span{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-3);}

.pr-toggle{display:flex;align-items:center;gap:6px;font-size:12.5px;color:var(--text-2);cursor:pointer;}
.pr-toggle input[type=checkbox]{width:16px;height:16px;accent-color:#ea580c;cursor:pointer;}

.pr-fee-save-btn{padding:5px 12px;background:rgba(234,88,12,.12);color:#ea580c;border:1px solid rgba(234,88,12,.25);border-radius:7px;font-size:11.5px;font-weight:700;cursor:pointer;white-space:nowrap;}
.pr-fee-save-btn:hover{background:rgba(234,88,12,.2);}

.no-fee-note{font-size:12px;color:var(--text-3);font-style:italic;padding:8px 0;}
</style>
@endpush

@section('content')

<div class="pr-info-box">
  <strong>How fees work:</strong>
  Set your <strong>Student Fee</strong> (what you charge students, max = course cap).
  Additionally, you can enable optional fee types (exam fee, registration, etc.) — these are added on top of the student fee in the enrollment.
  Your wallet is deducted by the <strong>Admission Charge</strong> per student when you confirm admission.
</div>

@if($charges->isEmpty())
  <div class="gt-card" style="padding:48px;text-align:center;">
    <div style="font-size:40px;margin-bottom:12px;">📋</div>
    <div style="font-size:15px;font-weight:700;color:var(--text-1);margin-bottom:6px;">No Courses Assigned</div>
    <div style="font-size:13px;color:var(--text-3);">Contact your institute to assign courses to your franchise.</div>
  </div>
@else

@foreach($charges->groupBy('course_type_id') as $typeId => $typeCharges)
@php $typeName = $typeCharges->first()?->courseType?->name ?? 'General'; @endphp

<div class="pr-type-label">{{ $typeName }}</div>

@foreach($typeCharges as $ch)
@php
  $cap      = (float) ($ch->course?->fee ?? 0);
  $stuFee   = (float) ($ch->student_fee ?? 0);
  $margin   = $stuFee - $ch->admission_charge;
  $instFees = $ch->course?->feeStructures ?? collect();
  $myFees   = ($feeStructures[$ch->course_id] ?? collect())->keyBy('fee_type_id');
@endphp

<div class="pr-course-card">
  {{-- Course header: fee input --}}
  <div class="pr-course-head">
    <div>
      <div class="pr-course-name">{{ $ch->course_name }}</div>
      <div class="pr-course-dur">{{ $ch->duration }} months</div>
      <div class="pr-charges-row">
        <span class="pr-pill pr-pill-adm" title="Deducted from your wallet per admission">Admission: ₹{{ number_format($ch->admission_charge, 2) }}</span>
        <span class="pr-pill pr-pill-cert" title="Deducted from your wallet per certificate">Certificate: ₹{{ number_format($ch->certificate_charge, 2) }}</span>
        <span class="pr-pill pr-pill-cap" title="Max student fee (set by institute)">Cap: ₹{{ number_format($cap, 2) }}</span>
        <span class="pr-pill {{ $margin > 0 ? 'pr-pill-margin-pos' : ($margin < 0 ? 'pr-pill-margin-neg' : 'pr-pill-margin-zero') }}"
              id="margin-pill-{{ $ch->id }}">
          Margin: {{ $margin >= 0 ? '+' : '' }}₹{{ number_format($margin, 2) }}
        </span>
      </div>
    </div>
    <div>
      <form method="POST" action="{{ route('franchise.pricing.update', $ch) }}" style="display:flex;align-items:center;gap:8px;">
        @csrf @method('PATCH')
        <div style="text-align:right;margin-bottom:4px;font-size:10px;color:var(--text-3);text-transform:uppercase;letter-spacing:.05em;">Your Student Fee</div>
        <div style="display:flex;align-items:center;gap:6px;">
          <span style="color:var(--text-3);font-size:13px;font-weight:600;">₹</span>
          <input type="number" name="student_fee" class="pr-fee-input"
            value="{{ number_format($stuFee ?: $cap, 2, '.', '') }}"
            step="0.01" min="0" max="{{ $cap }}"
            data-adm="{{ $ch->admission_charge }}"
            data-pill="margin-pill-{{ $ch->id }}"
            oninput="updateMargin(this)">
          <button type="submit" class="pr-save-btn">Save</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Additional fee structures --}}
  <div class="pr-fee-section">
    <div class="pr-fee-section-title">Additional Fee Types (optional)</div>

    @if($instFees->isEmpty())
      <div class="no-fee-note">No additional fee types configured for this course by the institute.</div>
    @else
      <form method="POST" action="{{ route('franchise.pricing.fee-structures', $ch) }}">
        @csrf
        <div class="pr-fee-row-head">
          <span>Fee Type</span>
          <span>Amount (₹)</span>
          <span>Apply to students</span>
        </div>
        @foreach($instFees as $fs)
        @php
          $existing = $myFees[$fs->fee_type_id] ?? null;
          $isEnabled = $existing !== null && $existing->enabled;
          $myAmount  = $existing ? $existing->amount : $fs->amount;
        @endphp
        <div class="pr-fee-row">
          <div>
            <div style="font-size:13px;font-weight:600;color:var(--text-1);">{{ $fs->fee_type_name }}</div>
            <div style="font-size:11px;color:var(--text-3);">Institute rate: ₹{{ number_format($fs->amount, 2) }}</div>
            <input type="hidden" name="fees[{{ $loop->index }}][fee_type_id]" value="{{ $fs->fee_type_id }}">
            <input type="hidden" name="fees[{{ $loop->index }}][fee_type_name]" value="{{ $fs->fee_type_name }}">
            <input type="hidden" name="fees[{{ $loop->index }}][sort_order]" value="{{ $loop->index }}">
          </div>
          <div>
            <input type="number" name="fees[{{ $loop->index }}][amount]"
              class="pr-fee-input" style="width:90px;"
              value="{{ number_format($myAmount, 2, '.', '') }}"
              step="0.01" min="0">
          </div>
          <div>
            <label class="pr-toggle">
              <input type="checkbox" name="fees[{{ $loop->index }}][enabled]" value="1"
                {{ $isEnabled ? 'checked' : '' }}>
              Enable
            </label>
          </div>
        </div>
        @endforeach
        <div style="margin-top:12px;">
          <button type="submit" class="pr-fee-save-btn">Save Fee Types for {{ $ch->course_name }}</button>
        </div>
      </form>
    @endif
  </div>
</div>
@endforeach

@endforeach

@endif
@endsection

@push('scripts')
<script>
function updateMargin(inp) {
  const adm    = parseFloat(inp.dataset.adm) || 0;
  const fee    = parseFloat(inp.value) || 0;
  const margin = fee - adm;
  const pill   = document.getElementById(inp.dataset.pill);
  if (!pill) return;
  pill.textContent = 'Margin: ' + (margin >= 0 ? '+' : '') + '₹' + margin.toFixed(2);
  pill.className = 'pr-pill ' + (margin > 0 ? 'pr-pill-margin-pos' : (margin < 0 ? 'pr-pill-margin-neg' : 'pr-pill-margin-zero'));
}
</script>
@endpush
