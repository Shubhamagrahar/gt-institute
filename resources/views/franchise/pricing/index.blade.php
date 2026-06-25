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
  $cap    = (float) ($ch->course?->fee ?? 0);
  $stuFee = (float) ($ch->student_fee ?? 0);
  $margin = $stuFee - $ch->admission_charge;
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
