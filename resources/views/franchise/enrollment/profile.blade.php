@extends('layouts.franchise')
@section('title','Student Profile')
@section('page-title','Process Admission')

@push('styles')
<style>
.prof-shell{max-width:1000px;margin:0 auto}
.prof-card{background:var(--bg-2);border:1px solid var(--border);border-radius:16px;overflow:hidden;margin-bottom:16px}
.prof-head{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border);cursor:pointer;user-select:none}
.prof-title{font-weight:800;font-size:13px;display:flex;align-items:center;gap:8px}
.prof-body{padding:18px}
.prof-body.collapsed{display:none}
.sec-toggle{font-size:16px;color:var(--text-2);transition:transform .2s}
.fg3{display:grid;grid-template-columns:repeat(3,1fr);gap:0 14px}
.fg4{display:grid;grid-template-columns:repeat(4,1fr);gap:0 14px}
.fg2{display:grid;grid-template-columns:repeat(2,1fr);gap:0 14px}
@media(max-width:900px){.fg4,.fg3{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.fg4,.fg3,.fg2{grid-template-columns:1fr}}
.gt-form-group.compact{margin-bottom:10px}
.gt-form-group.compact .gt-label{font-size:11px;margin-bottom:3px}
.gt-form-group.compact .gt-input,.gt-form-group.compact .gt-select,.gt-form-group.compact .gt-textarea{padding:6px 10px;font-size:13px}
.field-divider{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3);grid-column:1/-1;margin-top:6px;padding-bottom:5px;border-bottom:1px solid var(--border)}
.stu-hero{background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:16px;padding:18px 22px;margin-bottom:16px;display:flex;align-items:center;gap:18px}
.stu-hero-avatar{width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.15);background:rgba(255,255,255,.1);flex-shrink:0}
.edu-row{display:grid;grid-template-columns:1fr 1fr 1fr 80px;gap:10px;align-items:end;margin-bottom:10px;background:var(--bg-3);border:1px solid var(--border);border-radius:10px;padding:10px 12px}
@media(max-width:768px){.edu-row{grid-template-columns:1fr 1fr}}
</style>
@endpush

@section('content')
@php
  $photoSrc = $profile?->photo ? asset($profile->photo) : asset('images/user.svg');
@endphp

<div class="prof-shell">

  {{-- Hero --}}
  <div class="stu-hero">
    <img src="{{ $photoSrc }}" class="stu-hero-avatar" alt="photo" onerror="this.src='{{ asset('images/user.svg') }}'">
    <div>
      <div style="font-size:20px;font-weight:900;color:#fff">{{ $profile?->name ?? $courseBook->student->user_id }}</div>
      <div style="font-size:13px;color:rgba(255,255,255,.55);margin-top:3px">
        {{ $courseBook->course->name }}
        @if($courseBook->batch) &middot; {{ $courseBook->batch->name }}@endif
        &middot; Booked {{ $courseBook->book_date?->format('d M Y') }}
      </div>
    </div>
    <div style="margin-left:auto;display:flex;gap:8px;flex-shrink:0">
      <a href="{{ route('franchise.enrollment.pending') }}" class="btn btn-outline btn-sm" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">← Back</a>
      <a href="{{ route('franchise.enrollment.fee', $courseBook) }}" class="btn btn-sm" style="background:#16a34a;color:#fff;border:none">💳 Go to Payment →</a>
    </div>
  </div>

  {{-- Personal Details --}}
  <div class="prof-card">
    <div class="prof-head" onclick="toggleSec('personal')">
      <div class="prof-title">
        <span style="width:22px;height:22px;border-radius:50%;background:var(--primary);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:900">1</span>
        Personal Details
        @if($profile?->name)<span class="badge badge-success" style="font-size:10px">Saved</span>@endif
      </div>
      <span class="sec-toggle" id="toggle-personal">▾</span>
    </div>
    <div class="prof-body" id="body-personal">
      @if(session('success'))
        <div class="gt-alert gt-alert-success" style="margin-bottom:12px">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="gt-alert gt-alert-error" style="margin-bottom:12px">Please fix the errors below.</div>
      @endif

      <form method="POST" action="{{ route('franchise.enrollment.save-profile', $courseBook) }}"
            enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_section" value="personal">

        <div class="fg4">
          <div class="field-divider">Basic Info</div>
          <div class="gt-form-group compact">
            <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
            <input type="text" name="name" class="gt-input" required value="{{ old('name', $profile?->name) }}">
            @error('name')<div class="gt-error" style="font-size:11px">{{ $message }}</div>@enderror
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Mobile</label>
            <input type="text" name="mobile" class="gt-input" value="{{ old('mobile', $courseBook->student->mobile) }}" readonly>
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Email</label>
            <input type="email" name="email" class="gt-input" value="{{ old('email', $courseBook->student->email) }}">
            @error('email')<div class="gt-error" style="font-size:11px">{{ $message }}</div>@enderror
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Date of Birth</label>
            <input type="date" name="dob" class="gt-input" value="{{ old('dob', $profile?->dob) }}">
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Gender</label>
            <select name="gender" class="gt-select">
              <option value="">Select</option>
              @foreach(['Male','Female','Other'] as $g)
                <option value="{{ $g }}" {{ old('gender', $profile?->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
              @endforeach
            </select>
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Blood Group</label>
            <select name="blood_group" class="gt-select">
              <option value="">Select</option>
              @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                <option value="{{ $bg }}" {{ old('blood_group', $profile?->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
              @endforeach
            </select>
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Category</label>
            <select name="category" class="gt-select">
              <option value="">Select</option>
              @foreach(['General','OBC','SC','ST','EWS','Other'] as $cat)
                <option value="{{ $cat }}" {{ old('category', $profile?->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
              @endforeach
            </select>
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Qualification</label>
            <input type="text" name="qualification" class="gt-input" value="{{ old('qualification', $profile?->qualification) }}" placeholder="10th / 12th / Graduate...">
          </div>

          <div class="field-divider">Guardian Info</div>
          <div class="gt-form-group compact">
            <label class="gt-label">Father's Name</label>
            <input type="text" name="father_name" class="gt-input" value="{{ old('father_name', $profile?->father_name) }}">
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Mother's Name</label>
            <input type="text" name="mother_name" class="gt-input" value="{{ old('mother_name', $profile?->mother_name) }}">
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Guardian Mobile</label>
            <input type="text" name="guardian_mobile" class="gt-input" value="{{ old('guardian_mobile', $profile?->guardian_mobile) }}" placeholder="Optional">
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">Aadhar No.</label>
            <input type="text" name="aadhar_no" class="gt-input" value="{{ old('aadhar_no', $profile?->aadhar_no) }}" placeholder="12-digit">
          </div>

          <div class="field-divider">Address</div>
          <div class="gt-form-group compact" style="grid-column:1/-1">
            <label class="gt-label">Full Address</label>
            <textarea name="address" class="gt-textarea" rows="2">{{ old('address', $profile?->address) }}</textarea>
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">State</label>
            <select name="state" id="state-sel" class="gt-select">
              <option value="">Select State</option>
              @foreach($states as $s)
                <option value="{{ $s }}" {{ old('state', $profile?->state) === $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">District</label>
            <select name="district" id="district-sel" class="gt-select">
              <option value="">Select District</option>
              @if(old('district', $profile?->district))
                <option value="{{ old('district', $profile?->district) }}" selected>{{ old('district', $profile?->district) }}</option>
              @endif
            </select>
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">City</label>
            <input type="text" name="city" class="gt-input" value="{{ old('city', $profile?->city) }}" placeholder="City / Town">
          </div>
          <div class="gt-form-group compact">
            <label class="gt-label">PIN Code</label>
            <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code', $profile?->pin_code) }}" placeholder="6-digit PIN" maxlength="10">
          </div>
        </div>

        {{-- Photo --}}
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
          <label class="gt-label">Photo (optional)</label>
          <input type="file" name="photo" class="gt-input" accept="image/*" style="padding:4px">
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px">
          <button type="submit" class="btn btn-primary">Save Profile</button>
          <a href="{{ route('franchise.enrollment.fee', $courseBook) }}" class="btn btn-outline" style="border-color:#16a34a;color:#16a34a">Skip → Payment</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Education --}}
  @if($educationEnabled)
  <div class="prof-card">
    <div class="prof-head" onclick="toggleSec('edu')">
      <div class="prof-title">
        <span style="width:22px;height:22px;border-radius:50%;background:var(--primary);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:900">2</span>
        Education Details
        @if($education->count() > 0)<span class="badge badge-success" style="font-size:10px">{{ $education->count() }} added</span>@endif
      </div>
      <span class="sec-toggle" id="toggle-edu">▾</span>
    </div>
    <div class="prof-body" id="body-edu">
      <form method="POST" action="{{ route('franchise.enrollment.save-profile', $courseBook) }}">
        @csrf
        <input type="hidden" name="_section" value="education">

        <div id="edu-rows">
          @forelse($education as $i => $edu)
          <div class="edu-row">
            <div class="gt-form-group compact" style="margin-bottom:0">
              <label class="gt-label">Exam / Board</label>
              <input type="text" name="education[{{ $i }}][exam_name]" class="gt-input" value="{{ $edu->exam_name }}" placeholder="10th, 12th, B.A...">
            </div>
            <div class="gt-form-group compact" style="margin-bottom:0">
              <label class="gt-label">Board / University</label>
              <input type="text" name="education[{{ $i }}][board]" class="gt-input" value="{{ $edu->board }}">
            </div>
            <div class="gt-form-group compact" style="margin-bottom:0">
              <label class="gt-label">Pass Year</label>
              <input type="number" name="education[{{ $i }}][passing_year]" class="gt-input" value="{{ $edu->passing_year }}" placeholder="2023" min="1980" max="{{ date('Y') }}">
            </div>
            <div class="gt-form-group compact" style="margin-bottom:0">
              <label class="gt-label">%</label>
              <input type="number" name="education[{{ $i }}][percentage]" class="gt-input" value="{{ $edu->percentage }}" placeholder="75" min="0" max="100" step="0.01">
            </div>
          </div>
          @empty
          <div class="edu-row" id="edu-row-0">
            <div class="gt-form-group compact" style="margin-bottom:0">
              <label class="gt-label">Exam / Board</label>
              <input type="text" name="education[0][exam_name]" class="gt-input" placeholder="10th, 12th, B.A...">
            </div>
            <div class="gt-form-group compact" style="margin-bottom:0">
              <label class="gt-label">Board / University</label>
              <input type="text" name="education[0][board]" class="gt-input">
            </div>
            <div class="gt-form-group compact" style="margin-bottom:0">
              <label class="gt-label">Pass Year</label>
              <input type="number" name="education[0][passing_year]" class="gt-input" placeholder="{{ date('Y') }}" min="1980" max="{{ date('Y') }}">
            </div>
            <div class="gt-form-group compact" style="margin-bottom:0">
              <label class="gt-label">%</label>
              <input type="number" name="education[0][percentage]" class="gt-input" placeholder="75" min="0" max="100" step="0.01">
            </div>
          </div>
          @endforelse
        </div>

        <button type="button" onclick="addEduRow()" class="btn btn-outline btn-sm" style="margin-top:8px">+ Add Row</button>

        <div style="display:flex;justify-content:flex-end;margin-top:14px">
          <button type="submit" class="btn btn-primary">Save Education</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  {{-- Quick nav --}}
  <div style="display:flex;gap:10px;justify-content:flex-end;margin-bottom:20px">
    <a href="{{ route('franchise.enrollment.pending') }}" class="btn btn-outline">← Back to List</a>
    <a href="{{ route('franchise.enrollment.fee', $courseBook) }}" class="btn btn-primary" style="background:#16a34a;border-color:#16a34a">Proceed to Payment →</a>
  </div>

</div>
@endsection

@push('scripts')
<script>
let _eduIdx = {{ $education->count() > 0 ? $education->count() : 1 }};

function addEduRow() {
  const i = _eduIdx++;
  const html = `<div class="edu-row">
    <div class="gt-form-group compact" style="margin-bottom:0">
      <label class="gt-label">Exam / Board</label>
      <input type="text" name="education[${i}][exam_name]" class="gt-input" placeholder="10th, 12th, B.A...">
    </div>
    <div class="gt-form-group compact" style="margin-bottom:0">
      <label class="gt-label">Board / University</label>
      <input type="text" name="education[${i}][board]" class="gt-input">
    </div>
    <div class="gt-form-group compact" style="margin-bottom:0">
      <label class="gt-label">Pass Year</label>
      <input type="number" name="education[${i}][passing_year]" class="gt-input" min="1980" max="{{ date('Y') }}">
    </div>
    <div class="gt-form-group compact" style="margin-bottom:0">
      <label class="gt-label">%</label>
      <input type="number" name="education[${i}][percentage]" class="gt-input" min="0" max="100" step="0.01">
    </div>
  </div>`;
  document.getElementById('edu-rows').insertAdjacentHTML('beforeend', html);
}

function toggleSec(id) {
  const body = document.getElementById('body-' + id);
  const tog  = document.getElementById('toggle-' + id);
  if (!body) return;
  const isOpen = !body.classList.contains('collapsed');
  body.classList.toggle('collapsed');
  if (tog) tog.textContent = isOpen ? '▸' : '▾';
}

// State → District
const _districts = @json(isset($districtsByState) ? $districtsByState : []);
const stateSel = document.getElementById('state-sel');
if (stateSel) {
  stateSel.addEventListener('change', function () {
    const dsel = document.getElementById('district-sel');
    const state = this.value;
    dsel.innerHTML = '<option value="">Select District</option>';
    if (state && _districts[state]) {
      _districts[state].forEach(d => {
        const o = document.createElement('option');
        o.value = d; o.textContent = d;
        dsel.appendChild(o);
      });
    }
  });

  // Restore saved district
  (function () {
    const savedState = stateSel.value;
    const savedDist  = '{{ old("district", $profile?->district ?? "") }}';
    if (savedState && savedDist && _districts[savedState]) {
      const dsel = document.getElementById('district-sel');
      dsel.innerHTML = '<option value="">Select District</option>';
      _districts[savedState].forEach(d => {
        const o = document.createElement('option');
        o.value = d; o.textContent = d;
        if (d === savedDist) o.selected = true;
        dsel.appendChild(o);
      });
    }
  })();
}
</script>
@endpush
