@extends('layouts.franchise')
@section('title', 'Course Pricing')
@section('page-title', 'Course Pricing')

@push('styles')
<style>
.pr-header{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;margin-bottom:20px;}
.pr-info-box{background:rgba(234,88,12,.06);border:1px solid rgba(234,88,12,.2);border-radius:12px;padding:14px 18px;margin-bottom:22px;font-size:13px;color:var(--text-2);line-height:1.7;}
.pr-info-box strong{color:#fb923c;}
.pr-type-section{margin-bottom:28px;}
.pr-type-label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3);margin-bottom:12px;padding:0 4px;display:flex;align-items:center;gap:8px;}
.pr-type-label::after{content:'';flex:1;height:1px;background:var(--border-2);}

.pr-tbl{width:100%;border-collapse:collapse;}
.pr-tbl th{background:var(--bg-3);padding:10px 16px;text-align:left;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-3);}
.pr-tbl td{padding:12px 16px;border-bottom:1px solid var(--border);vertical-align:middle;}
.pr-tbl tbody tr:last-child td{border-bottom:none;}
.pr-tbl tbody tr:hover td{background:var(--bg-3);}

.pr-charge-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700;}
.pr-adm-pill{background:rgba(234,88,12,.1);color:#ea580c;border:1px solid rgba(234,88,12,.2);}
.pr-cert-pill{background:rgba(139,92,246,.1);color:#7c3aed;border:1px solid rgba(139,92,246,.2);}

.pr-cap-badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;color:var(--text-3);background:var(--bg-3);border:1px solid var(--border);padding:2px 9px;border-radius:6px;}

.pr-fee-input{
  width:110px;padding:6px 10px;border:1px solid var(--border);border-radius:8px;
  background:var(--bg-3);color:var(--text-1);font-size:13px;font-weight:600;
  font-family:monospace;text-align:right;
  transition:border-color .15s,box-shadow .15s;
}
.pr-fee-input:focus{outline:none;border-color:#ea580c;box-shadow:0 0 0 3px rgba(234,88,12,.15);}

.pr-save-btn{padding:6px 14px;background:linear-gradient(135deg,#ea580c,#f97316);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;}
.pr-save-btn:hover{opacity:.9;}
.pr-save-btn:disabled{opacity:.5;cursor:not-allowed;}

.profit-display{font-size:11.5px;font-family:monospace;padding:3px 8px;border-radius:6px;}
.profit-pos{color:#16a34a;background:rgba(22,163,74,.08);}
.profit-neg{color:#dc2626;background:rgba(220,38,38,.08);}
.profit-zero{color:var(--text-3);}
</style>
@endpush

@section('content')

<div class="pr-header">
  <div>
    <div style="font-size:18px;font-weight:800;color:var(--text-1);">Course Pricing</div>
    <div style="font-size:12px;color:var(--text-3);margin-top:3px;">Set what fee you charge your students. You pay institute the admission/certificate charge per transaction.</div>
  </div>
</div>

<div class="pr-info-box">
  <strong>How this works:</strong>
  For each student admission, the <strong>Admission Charge</strong> is automatically deducted from your wallet.
  The <strong>Student Fee</strong> is what you collect from the student — set it to cover your costs and margin.
  You cannot set student fee higher than the institute's course price cap.
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

  <div class="pr-type-section">
    <div class="pr-type-label">{{ $typeName }}</div>

    <div class="gt-card" style="overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="pr-tbl">
          <thead>
            <tr>
              <th>Course</th>
              <th>Duration</th>
              <th>Admission Charge <span style="opacity:.5">(you pay institute)</span></th>
              <th>Certificate Charge <span style="opacity:.5">(you pay institute)</span></th>
              <th>Course Fee Cap <span style="opacity:.5">(max student fee)</span></th>
              <th>Your Student Fee</th>
              <th style="text-align:right;">Margin</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($typeCharges as $ch)
            @php
              $cap    = (float) ($ch->course?->fee ?? 0);
              $profit = $cap > 0 ? (($ch->student_fee ?? 0) - $ch->admission_charge) : 0;
            @endphp
            <tr>
              <td style="font-weight:600;font-size:13px;">{{ $ch->course_name }}</td>
              <td style="font-size:12px;color:var(--text-3);">{{ $ch->duration }} months</td>
              <td>
                <span class="pr-charge-pill pr-adm-pill">₹{{ number_format($ch->admission_charge, 2) }}</span>
              </td>
              <td>
                <span class="pr-charge-pill pr-cert-pill">₹{{ number_format($ch->certificate_charge, 2) }}</span>
              </td>
              <td>
                <span class="pr-cap-badge">₹{{ number_format($cap, 2) }}</span>
              </td>
              <td>
                <form method="POST" action="{{ route('franchise.pricing.update', $ch) }}" class="pr-form" style="display:inline-flex;align-items:center;gap:8px;">
                  @csrf @method('PATCH')
                  <span style="color:var(--text-3);font-size:13px;font-weight:600;">₹</span>
                  <input type="number" name="student_fee" class="pr-fee-input"
                    value="{{ number_format((float)($ch->student_fee ?? $ch->admission_charge), 2, '.', '') }}"
                    step="0.01" min="0" max="{{ $cap }}"
                    data-adm="{{ $ch->admission_charge }}"
                    oninput="updateMargin(this)">
              </td>
              <td style="text-align:right;">
                  <span class="profit-display {{ $profit > 0 ? 'profit-pos' : ($profit < 0 ? 'profit-neg' : 'profit-zero') }}" id="margin-{{ $ch->id }}">
                    {{ $profit >= 0 ? '+' : '' }}₹{{ number_format((float)($ch->student_fee ?? 0) - $ch->admission_charge, 2) }}
                  </span>
              </td>
              <td>
                  <button type="submit" class="pr-save-btn">Save</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endforeach

@endif
@endsection

@push('scripts')
<script>
function updateMargin(inp) {
  const row = inp.closest('tr');
  const adm = parseFloat(inp.dataset.adm) || 0;
  const fee = parseFloat(inp.value) || 0;
  const margin = fee - adm;
  const el = row.querySelector('.profit-display');
  if (!el) return;
  el.textContent = (margin >= 0 ? '+' : '') + '₹' + margin.toFixed(2);
  el.className = 'profit-display ' + (margin > 0 ? 'profit-pos' : (margin < 0 ? 'profit-neg' : 'profit-zero'));
}
</script>
@endpush
