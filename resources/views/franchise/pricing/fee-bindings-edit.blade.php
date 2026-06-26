@extends('layouts.franchise')
@section('title', 'Extra Fee Bindings — ' . $charge->course_name)
@section('page-title', 'Extra Fee Bindings')
@section('topbar-actions')
  <a href="{{ route('franchise.pricing.index') }}" class="btn btn-outline btn-sm">Back to Pricing</a>
@endsection

@push('styles')
<style>
.fb-shell{max-width:860px;margin:0 auto;display:grid;gap:20px;}
.fb-info{background:rgba(79,70,229,.06);border:1px solid rgba(79,70,229,.18);border-radius:14px;padding:14px 18px;font-size:13px;color:var(--text-2);line-height:1.7;}
.fb-info strong{color:#4f46e5;}
.fb-course-pill{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:12px;background:linear-gradient(135deg,#ea580c,#c2410c);color:#fff;font-size:14px;font-weight:800;}
.fb-card{background:var(--bg-2);border:1px solid var(--border);border-radius:18px;overflow:hidden;}
.fb-card-head{padding:16px 20px;border-bottom:1px solid var(--border);font-size:15px;font-weight:900;}
.fb-card-body{padding:20px;}
.fb-add-grid{display:grid;grid-template-columns:1fr 180px auto;gap:12px;align-items:end;}
.fb-row{display:flex;align-items:center;gap:12px;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-3);margin-bottom:10px;}
.fb-row-name{flex:1;font-size:14px;font-weight:700;}
.fb-row-type{font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;}
.fb-row-type.mandatory{background:rgba(234,88,12,.1);color:#ea580c;border:1px solid rgba(234,88,12,.2);}
.fb-row-type.optional{background:rgba(100,116,139,.1);color:#64748b;border:1px solid rgba(100,116,139,.2);}
.fb-row-amt{font-size:15px;font-weight:900;color:#4f46e5;min-width:90px;text-align:right;}
.fb-remove-btn{padding:5px 10px;background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2);border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;}
.fb-remove-btn:hover{background:rgba(220,38,38,.15);}
.fb-empty{text-align:center;padding:28px;font-size:13px;color:var(--text-3);font-style:italic;}
.fb-total{margin-top:14px;padding:12px 16px;background:rgba(79,70,229,.06);border:1px solid rgba(79,70,229,.15);border-radius:12px;display:flex;justify-content:space-between;font-size:14px;font-weight:800;}
.fb-note{margin-top:10px;font-size:12px;color:var(--text-3);line-height:1.6;}
</style>
@endpush

@section('content')
<div class="fb-shell">

  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <div class="fb-course-pill">{{ $charge->course_name }}</div>
    <div style="font-size:13px;color:var(--text-2);">{{ $charge->duration }} months &nbsp;·&nbsp; Student Fee: ₹{{ number_format($charge->student_fee ?: ($charge->course?->fee ?? 0), 2) }}</div>
  </div>

  <div class="fb-info">
    <strong>How this works:</strong> Your institute has created some additional fee types (Exam Fee, Registration Fee, etc.).
    You can attach any of them to this course with your own amount. These extra fees will be shown to the student during admission and added on top of the base course fee.
    <br>
    <strong>Note:</strong> You cannot create new fee types — only your institute can do that.
  </div>

  @if(session('success'))
    <div class="gt-alert gt-alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="gt-alert gt-alert-danger">{{ session('error') }}</div>
  @endif

  {{-- Add new binding --}}
  <div class="fb-card">
    <div class="fb-card-head">Bind a New Fee Type</div>
    <div class="fb-card-body">
      @if($instFeeTypes->isEmpty())
        <div class="fb-empty">Your institute has not created any fee types yet. Ask them to add fee types from their panel first.</div>
      @else
        @php
          $alreadyBound = $myBindings->pluck('fee_type_id')->toArray();
          $available    = $instFeeTypes->whereNotIn('id', $alreadyBound)->values();
        @endphp
        @if($available->isEmpty())
          <div class="fb-empty">All available fee types are already bound to this course.</div>
        @else
          <form method="POST" action="{{ route('franchise.pricing.fee-bindings.save', $charge) }}">
            @csrf
            <div class="fb-add-grid">
              <div class="gt-form-group" style="margin:0;">
                <label class="gt-label">Fee Type</label>
                <select name="fee_type_id" class="gt-select" required>
                  <option value="">Select fee type</option>
                  @foreach($available as $ft)
                    <option value="{{ $ft->id }}" {{ old('fee_type_id')==$ft->id ? 'selected':'' }}>
                      {{ $ft->name }}{{ $ft->is_mandatory ? ' (Mandatory)' : '' }}
                    </option>
                  @endforeach
                </select>
                @error('fee_type_id')<div class="gt-error">{{ $message }}</div>@enderror
              </div>
              <div class="gt-form-group" style="margin:0;">
                <label class="gt-label">Amount (₹)</label>
                <input type="number" name="amount" class="gt-input" step="0.01" min="1" max="99999"
                  value="{{ old('amount') }}" placeholder="e.g. 500" required>
                @error('amount')<div class="gt-error">{{ $message }}</div>@enderror
              </div>
              <div>
                <button type="submit" class="btn btn-primary" style="background:#4f46e5;border-color:#4f46e5;white-space:nowrap;">+ Bind</button>
              </div>
            </div>
            <div class="fb-note">
              * Mandatory fee types will always be charged to the student. Optional types may be waived during fee setup.
            </div>
          </form>
        @endif
      @endif
    </div>
  </div>

  {{-- Existing bindings --}}
  <div class="fb-card">
    <div class="fb-card-head">Current Fee Bindings for this Course</div>
    <div class="fb-card-body">
      @if($myBindings->isEmpty())
        <div class="fb-empty">No extra fee types bound yet. Add one above.</div>
      @else
        @foreach($myBindings as $b)
          <div class="fb-row">
            <div class="fb-row-name">{{ $b->fee_type_name }}</div>
            <div class="fb-row-type {{ $b->feeType?->is_mandatory ? 'mandatory' : 'optional' }}">
              {{ $b->feeType?->is_mandatory ? 'Mandatory' : 'Optional' }}
            </div>
            <div class="fb-row-amt">₹{{ number_format($b->amount, 2) }}</div>
            <form method="POST" action="{{ route('franchise.pricing.fee-bindings.remove', $b) }}" onsubmit="return confirm('Remove this fee binding?')">
              @csrf @method('DELETE')
              <button type="submit" class="fb-remove-btn">Remove</button>
            </form>
          </div>
        @endforeach

        <div class="fb-total">
          <span>Total extra fees per student</span>
          <span>₹{{ number_format($myBindings->sum('amount'), 2) }}</span>
        </div>
        <div class="fb-note">
          Student total = Base Course Fee (₹{{ number_format($charge->student_fee ?: ($charge->course?->fee ?? 0), 2) }}) + Extra Fees (₹{{ number_format($myBindings->sum('amount'), 2) }}) = <strong>₹{{ number_format(($charge->student_fee ?: ($charge->course?->fee ?? 0)) + $myBindings->sum('amount'), 2) }}</strong>
        </div>
      @endif
    </div>
  </div>

</div>
@endsection
