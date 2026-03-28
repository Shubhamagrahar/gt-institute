@extends('layouts.institute')
@section('title','Add Student')
@section('page-title','Add New Student')
@section('topbar-actions')
  <a href="{{ route('institute.students.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<form method="POST" action="{{ route('institute.students.store') }}">
@csrf
<div class="gt-grid-2" style="gap:20px;align-items:start;">

  <div class="gt-card">
    <div class="gt-card-header"><div class="gt-card-title">Personal Details</div></div>

    <div class="gt-form-group">
      <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
        value="{{ old('name') }}" placeholder="Student's full name" required>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
        <input type="text" name="mobile" class="gt-input @error('mobile') is-invalid @enderror"
          value="{{ old('mobile') }}" placeholder="10-digit" required>
        @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Email</label>
        <input type="email" name="email" class="gt-input" value="{{ old('email') }}" placeholder="optional">
      </div>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Date of Birth</label>
        <input type="date" name="dob" class="gt-input" value="{{ old('dob') }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Gender</label>
        <select name="gender" class="gt-select">
          <option value="">— Select —</option>
          <option value="Male"   {{ old('gender')==='Male'  ?'selected':'' }}>Male</option>
          <option value="Female" {{ old('gender')==='Female'?'selected':'' }}>Female</option>
          <option value="Other"  {{ old('gender')==='Other' ?'selected':'' }}>Other</option>
        </select>
      </div>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Father's Name</label>
        <input type="text" name="father_name" class="gt-input" value="{{ old('father_name') }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Father's Mobile</label>
        <input type="text" name="father_mobile" class="gt-input" value="{{ old('father_mobile') }}">
      </div>
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Mother's Name</label>
      <input type="text" name="mother_name" class="gt-input" value="{{ old('mother_name') }}">
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Qualification</label>
      <input type="text" name="qualification" class="gt-input" value="{{ old('qualification') }}" placeholder="e.g. 12th, Graduate">
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">State</label>
        <input type="text" name="state" class="gt-input" value="{{ old('state') }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">PIN Code</label>
        <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code') }}" placeholder="6-digit">
      </div>
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Address</label>
      <textarea name="full_add" class="gt-textarea" style="min-height:70px;">{{ old('full_add') }}</textarea>
    </div>
  </div>

  <div>
    <div class="gt-card mb-3">
      <div class="gt-card-header"><div class="gt-card-title">Fee & Admission Details</div></div>

      <div class="gt-form-group">
        <label class="gt-label">Registration Date <span style="color:var(--danger)">*</span></label>
        <input type="date" name="r_date" class="gt-input @error('r_date') is-invalid @enderror"
          value="{{ old('r_date', date('Y-m-d')) }}" required>
        @error('r_date')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Fee Collection Type <span style="color:var(--danger)">*</span></label>
        <select name="fee_collect_type" id="fee_collect_type" class="gt-select">
          <option value="OTP"     {{ old('fee_collect_type','OTP')==='OTP'    ?'selected':'' }}>One Time Payment (OTP)</option>
          <option value="MONTHLY" {{ old('fee_collect_type')==='MONTHLY'?'selected':'' }}>Monthly</option>
          <option value="PART"    {{ old('fee_collect_type')==='PART'   ?'selected':'' }}>Part Payment</option>
        </select>
      </div>

      <div id="monthly-fields" style="display:none;">
        <div class="gt-form-grid-2">
          <div class="gt-form-group">
            <label class="gt-label">Monthly Fee (₹)</label>
            <input type="number" name="monthly_fee" class="gt-input" value="{{ old('monthly_fee',0) }}" min="0" step="0.01">
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Late Fee/Day (₹)</label>
            <input type="number" name="daily_late_fee" class="gt-input" value="{{ old('daily_late_fee',0) }}" min="0">
          </div>
        </div>
      </div>
    </div>

    <div class="gt-card">
      <div class="gt-card-header"><div class="gt-card-title">Note</div></div>
      <div class="gt-alert gt-alert-info" style="margin-bottom:0;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        A random login password will be auto-generated for the student. Wallet will be created with ₹0 balance.
      </div>
    </div>

    <div style="margin-top:20px;">
      <button type="submit" class="btn btn-primary w-full btn-lg" style="justify-content:center;">
        Add Student
      </button>
    </div>
  </div>

</div>
</form>
@push('scripts')
<script>
  const fct = document.getElementById('fee_collect_type');
  const mf  = document.getElementById('monthly-fields');
  function toggleMonthly() { mf.style.display = fct.value === 'MONTHLY' ? 'block' : 'none'; }
  fct?.addEventListener('change', toggleMonthly);
  toggleMonthly();
</script>
@endpush
@endsection
