@extends('layouts.institute')
@section('title', 'Add Franchise')
@section('page-title', 'Add Franchise')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.index') }}" class="btn btn-outline btn-sm">Back to Franchises</a>
@endsection

@section('content')

{{-- Progress indicator --}}
<div class="wizard-steps" style="margin-bottom:24px;">
  <div class="wizard-step active">
    <div class="wizard-step-num">1</div>
    <div class="wizard-step-label">Franchise Details</div>
  </div>
  <div class="wizard-step-line"></div>
  <div class="wizard-step">
    <div class="wizard-step-num">2</div>
    <div class="wizard-step-label">Preview & Payment</div>
  </div>
  <div class="wizard-step-line"></div>
  <div class="wizard-step">
    <div class="wizard-step-num">3</div>
    <div class="wizard-step-label">Fee Collection</div>
  </div>
</div>

<form method="POST" action="{{ route('institute.franchises.store') }}" enctype="multipart/form-data">
  @csrf

  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Franchise Details</div>
      <span class="text-xs text-muted">Select the level first — commission and onboarding fee will auto-fill.</span>
    </div>

    @include('institute.franchises._form')

    <div style="display:flex; justify-content:flex-end; margin-top:18px;">
      {{-- For wallet mode: creates directly. For independent mode: goes to preview. --}}
      <button type="submit" class="btn btn-primary" id="submit-btn">Continue →</button>
    </div>
  </div>
</form>
@endsection

@push('styles')
<style>
.wizard-steps { display:flex; align-items:center; gap:0; }
.wizard-step { display:flex; flex-direction:column; align-items:center; gap:6px; }
.wizard-step-num {
  width:32px; height:32px; border-radius:50%;
  border:2px solid var(--border-2);
  display:flex; align-items:center; justify-content:center;
  font-size:13px; font-weight:700; color:var(--text-3); background:var(--bg-3);
}
.wizard-step.active .wizard-step-num { border-color:var(--accent); background:var(--accent); color:#fff; }
.wizard-step.done .wizard-step-num { border-color:#2a7a2a; background:#2a7a2a; color:#fff; }
.wizard-step-label { font-size:11px; color:var(--text-3); white-space:nowrap; }
.wizard-step.active .wizard-step-label { color:var(--accent); font-weight:600; }
.wizard-step-line { flex:1; height:2px; background:var(--border-2); margin:0 8px; margin-bottom:22px; }
</style>
@endpush

@push('scripts')
<script>
// Both modes go through preview — always show "Continue to Preview →"
(function () {
  const btn = document.getElementById('submit-btn');
  if (btn) btn.textContent = 'Continue to Preview →';
})();
</script>
@endpush
