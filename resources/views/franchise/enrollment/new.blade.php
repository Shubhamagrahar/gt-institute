@extends('layouts.franchise')
@section('title','New Admission')
@section('page-title','New Student Admission')

@push('styles')
<style>
.new-shell{max-width:900px;margin:0 auto}
.new-card{background:var(--bg-2);border:1px solid var(--border);border-radius:18px;overflow:hidden;margin-bottom:18px}
.new-head{background:linear-gradient(135deg,#ea580c,#c2410c);color:#fff;padding:18px 24px}
.new-head h2{margin:0;font-size:20px;font-weight:900}
.new-head p{margin:5px 0 0;opacity:.8;font-size:13px}
.new-body{padding:22px}
.sec-label{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3);margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border)}
</style>
@endpush

@section('content')
<div class="new-shell">
  <form method="POST" action="{{ route('franchise.enrollment.store-new') }}">
    @csrf

    {{-- Student Info --}}
    <div class="new-card">
      <div class="new-head">
        <h2>Student Information</h2>
        <p>Fill basic details to book a seat. Complete profile & fee after booking.</p>
      </div>
      <div class="new-body">
        <div class="sec-label">Personal Details</div>
        <div class="gt-form-grid-3">
          <div class="gt-form-group">
            <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
            <input type="text" name="name" class="gt-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   value="{{ old('name') }}" placeholder="Student ka poora naam" required>
            @error('name')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
            <input type="text" name="mobile" class="gt-input {{ $errors->has('mobile') ? 'is-invalid' : '' }}"
                   value="{{ old('mobile') }}" placeholder="10-digit mobile" required>
            @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Email</label>
            <input type="email" name="email" class="gt-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}" placeholder="optional@email.com">
            @error('email')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Date of Birth</label>
            <input type="date" name="dob" class="gt-input" value="{{ old('dob') }}">
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Gender</label>
            <select name="gender" class="gt-select">
              <option value="">Select</option>
              <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
              <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Father's Name</label>
            <input type="text" name="father_name" class="gt-input" value="{{ old('father_name') }}" placeholder="Father ka naam">
          </div>
        </div>

        <div class="sec-label" style="margin-top:16px">Address</div>
        <div class="gt-form-grid-3">
          <div class="gt-form-group">
            <label class="gt-label">State</label>
            <select name="state" id="state-sel" class="gt-select">
              <option value="">Select State</option>
              @foreach($states as $s)
                <option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="gt-form-group">
            <label class="gt-label">District</label>
            <select name="district" id="district-sel" class="gt-select">
              <option value="">Select District</option>
              @if(old('district'))
                <option value="{{ old('district') }}" selected>{{ old('district') }}</option>
              @endif
            </select>
          </div>
          <div class="gt-form-group">
            <label class="gt-label">City</label>
            <input type="text" name="city" class="gt-input" value="{{ old('city') }}" placeholder="City / Town">
          </div>
          <div class="gt-form-group" style="grid-column:1/-1">
            <label class="gt-label">Address</label>
            <textarea name="address" class="gt-textarea" rows="2" placeholder="Full address...">{{ old('address') }}</textarea>
          </div>
          <div class="gt-form-group">
            <label class="gt-label">PIN Code</label>
            <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code') }}" placeholder="6-digit PIN" maxlength="10">
          </div>
        </div>
      </div>
    </div>

    {{-- Course --}}
    <div class="new-card">
      <div class="new-body">
        <div class="sec-label">Course & Batch</div>
        <div class="gt-form-grid-3">
          <div class="gt-form-group" style="grid-column:1/3">
            <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
            <select name="course_id" class="gt-select {{ $errors->has('course_id') ? 'is-invalid' : '' }}" required>
              <option value="">-- Select Course --</option>
              @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                  {{ $c->name }}
                </option>
              @endforeach
            </select>
            @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Batch (optional)</label>
            <select name="batch_id" class="gt-select">
              <option value="">-- No Batch --</option>
              @foreach($batches as $b)
                <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:10px;justify-content:flex-end">
      <a href="{{ route('franchise.enrollment.pending') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary" style="padding:10px 28px;">Book Seat & Continue →</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
const _districts = @json($districtsByState);

document.getElementById('state-sel').addEventListener('change', function () {
  const state = this.value;
  const dsel  = document.getElementById('district-sel');
  dsel.innerHTML = '<option value="">Select District</option>';
  if (state && _districts[state]) {
    _districts[state].forEach(d => {
      const o = document.createElement('option');
      o.value = d; o.textContent = d;
      dsel.appendChild(o);
    });
  }
});

// Restore old district if any
(function () {
  const oldState = '{{ old("state") }}';
  const oldDist  = '{{ old("district") }}';
  if (oldState && oldDist && _districts[oldState]) {
    const dsel = document.getElementById('district-sel');
    dsel.innerHTML = '<option value="">Select District</option>';
    _districts[oldState].forEach(d => {
      const o = document.createElement('option');
      o.value = d; o.textContent = d;
      if (d === oldDist) o.selected = true;
      dsel.appendChild(o);
    });
  }
})();
</script>
@endpush
