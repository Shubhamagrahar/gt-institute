@extends('layouts.institute')
@section('title','New Student')
@section('page-title','Student Registration')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@push('styles')
<style>
.reg-shell{max-width:1180px;margin:0 auto}
.reg-banner{background:linear-gradient(135deg,#6651d8,#503ab9);border-radius:22px 22px 0 0;color:#fff;padding:26px 30px;display:flex;gap:16px;align-items:center}
.reg-banner-icon{width:58px;height:58px;border-radius:16px;background:rgba(255,255,255,.16);display:grid;place-items:center;font-weight:900}
.reg-banner h2{margin:0;font-size:26px}.reg-banner p{margin:4px 0 0;opacity:.82}
.reg-steps{background:#f7f8fc;display:grid;grid-template-columns:repeat(4,1fr);padding:24px 40px 12px;color:#6c5dd3}
.reg-step{text-align:center;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;position:relative}.reg-step:before{content:"";position:absolute;top:21px;left:-50%;right:50%;height:3px;background:#8d7af0}.reg-step:first-child:before{display:none}
.reg-bubble{position:relative;z-index:1;width:44px;height:44px;margin:0 auto 9px;border-radius:50%;background:#6c5dd3;color:#fff;display:grid;place-items:center;font-size:16px}
.reg-card{background:#fff;border-radius:0 0 22px 22px;padding:30px 40px;box-shadow:0 18px 45px rgba(15,23,42,.08)}
.reg-form-grid{display:grid;grid-template-columns:1fr 1fr 320px;gap:18px;align-items:start}
.fee-preview{grid-row:span 3;background:linear-gradient(180deg,#f5f7ff,#fff);border:1px solid #dfe6ff;border-radius:18px;padding:20px;position:sticky;top:18px}
.fee-preview small{color:#64748b;font-weight:800;text-transform:uppercase;letter-spacing:.1em}.fee-course{font-size:18px;font-weight:900;margin:8px 0 14px;color:#1f2937}
.fee-old{text-decoration:line-through;color:#94a3b8;font-size:18px;font-weight:800}.fee-now{font-size:34px;font-weight:950;color:#503ab9;margin-top:4px}
.fee-note{margin-top:14px;border-radius:12px;background:#eef4ff;color:#475569;padding:12px;font-size:12px;line-height:1.5}
@media(max-width:900px){.reg-form-grid{grid-template-columns:1fr}.fee-preview{grid-row:auto;position:static}.reg-steps{grid-template-columns:1fr;gap:10px}.reg-step:before{display:none}.reg-card{padding:22px}}
</style>
@endpush

@section('content')
@php
  $educationField = $savedFields['education_details'] ?? null;
  $educationEnabled = !$educationField || $educationField->is_active;
  $steps = $educationEnabled ? ['Course','Personal','Guardian','Education'] : ['Course','Personal','Guardian'];
@endphp
<div class="reg-shell">
  <div class="reg-banner">
    <div class="reg-banner-icon">GT</div>
    <div>
      <h2>Student Registration</h2>
      <p>Complete the required student details and confirm the course fee.</p>
    </div>
  </div>

  <div class="reg-steps">
    @foreach($steps as $i => $step)
      <div class="reg-step"><div class="reg-bubble">{{ $i + 1 }}</div>{{ $step }}</div>
    @endforeach
  </div>

  <div class="reg-card">
    <form method="POST" action="{{ route('institute.enrollment.store-new') }}">
      @csrf
      <div class="reg-form-grid">
        <div class="gt-form-group">
          <label class="gt-label">Student Name <span style="color:var(--danger)">*</span></label>
          <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="Enter student full name">
          @error('name')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Mobile Number <span style="color:var(--danger)">*</span></label>
          <input type="text" name="mobile" class="gt-input @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" placeholder="10-digit mobile" required>
          @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="fee-preview">
          <small>Selected Course</small>
          <div class="fee-course" id="fee-course">Select a course</div>
          <div class="fee-old" id="fee-max">₹0.00</div>
          <div class="fee-now" id="fee-now">₹0.00</div>
          <div class="fee-note">After selecting a course, the listed fee and final course fee will be displayed here. Payment type is selected in the payment step.</div>
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
          <select name="course_id" id="course-select" class="gt-select @error('course_id') is-invalid @enderror" required>
            <option value="">Select Course</option>
            @foreach($courses as $course)
              @php $listedFee = $course->display_max_fee ?? $course->max_fee ?? $course->fee ?? 0; @endphp
              <option value="{{ $course->id }}" data-name="{{ $course->name }}" data-fee="{{ $course->fee ?? 0 }}" data-listed-fee="{{ $listedFee }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                {{ $course->name }}
              </option>
            @endforeach
          </select>
          @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Batch</label>
          <select name="batch_id" class="gt-select @error('batch_id') is-invalid @enderror">
            <option value="">No Batch</option>
            @foreach($batches as $batch)
              <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
            @endforeach
          </select>
          @error('batch_id')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div style="display:flex;justify-content:flex-end;margin-top:24px;">
        <button type="submit" class="btn btn-primary btn-lg">Continue to Profile</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const select = document.getElementById('course-select');
  const course = document.getElementById('fee-course');
  const listedFee = document.getElementById('fee-max');
  const feeNow = document.getElementById('fee-now');
  const money = (value) => '₹' + (Number(value || 0)).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  function syncFee() {
    const option = select.options[select.selectedIndex];
    course.textContent = option?.dataset.name || 'Select a course';
    listedFee.textContent = money(option?.dataset.listedFee || 0);
    feeNow.textContent = money(option?.dataset.fee || 0);
  }
  select.addEventListener('change', syncFee);
  syncFee();
})();
</script>
@endpush
