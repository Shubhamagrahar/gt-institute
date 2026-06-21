@extends('layouts.institute')
@section('title','Fill Admission Details')
@section('page-title','Fill Admission Details')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.pending') }}" class="btn btn-outline btn-sm">← Back to List</a>
@endsection

@push('styles')
<style>
.adm-shell{max-width:1160px;margin:0 auto}
.adm-header{background:linear-gradient(135deg,#1746a2,#1b75d0);color:#fff;border-radius:22px;padding:26px 30px;box-shadow:0 20px 45px rgba(23,70,162,.18)}
.adm-header h2{margin:0;font-size:26px;font-weight:900}
.adm-header p{margin:8px 0 0;opacity:.84;font-size:14px}
.adm-wrap{display:block;margin-top:18px}
.adm-card{background:var(--bg-2);border:1px solid var(--border);border-radius:20px;overflow:hidden}
.adm-steps{display:grid;gap:10px;padding:22px;background:#eef4ff}
.adm-step{padding:12px 8px;border-radius:14px;background:#dce9ff;color:#5878a8;text-align:center;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
.adm-step.active{background:#1746a2;color:#fff}
.adm-body{padding:24px}
.wizard-step{display:none}
.wizard-step.active{display:block}
.adm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.adm-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.adm-section-title{font-size:20px;font-weight:900;margin:0 0 8px}
.adm-section-note{font-size:13px;color:var(--text-2);margin-bottom:18px}
.photo-box{border:1px dashed #b8c9e9;border-radius:18px;padding:18px;background:#f7faff;text-align:center;display:flex;flex-direction:column;gap:8px;align-self:start}
.photo-box img{width:110px;height:110px;border-radius:50%;object-fit:cover;margin:0 auto 4px;border:3px solid #fff;box-shadow:0 8px 24px rgba(0,0,0,.08)}
.basic-layout{display:grid;grid-template-columns:240px minmax(0,1fr);gap:20px;align-items:start}
.basic-side-stack{display:grid;gap:14px}
.basic-fields{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.address-sections{display:grid;gap:18px}
.address-card{border:1px solid var(--border);border-radius:18px;padding:18px;background:var(--bg-3)}
.address-card-title{font-size:15px;font-weight:900;margin:0 0 4px}
.address-card-note{font-size:12px;color:var(--text-2);margin:0 0 14px}
.address-copy-row{display:flex;align-items:center;gap:10px;padding:0 4px;color:var(--text-2);font-size:13px;font-weight:700}
.address-copy-row input{width:16px;height:16px;cursor:pointer}
.address-full-width{grid-column:1/-1}
.edu-toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.edu-row{display:grid;grid-template-columns:repeat(6,minmax(0,1fr)) 44px;gap:10px;padding:12px;border:1px solid var(--border);border-radius:14px;background:var(--bg-3);margin-bottom:10px}
.wizard-actions{display:flex;justify-content:space-between;gap:12px;margin-top:22px}
.field-section-label{font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:#64748b;grid-column:1/-1;margin-top:4px;margin-bottom:-4px;padding-bottom:6px;border-bottom:1px solid var(--border)}
@media(max-width:1080px){.basic-layout{grid-template-columns:1fr}}
@media(max-width:760px){.adm-steps,.adm-grid,.adm-grid-3,.basic-fields{grid-template-columns:repeat(2,1fr)}}
</style>
@endpush

@section('content')
@php
  $definedFields = collect(\App\Models\AdmissionFormField::allDefinedFields())->keyBy('key');
  $resolveField = function (string $key) use ($savedFields, $definedFields) {
      $definition = $definedFields[$key] ?? null;
      if (!$definition) return null;
      $saved = $savedFields[$key] ?? null;
      return (object)[
          'field_key'   => $key,
          'field_label' => $saved?->field_label ?? $definition['label'],
          'field_type'  => $saved?->field_type  ?? $definition['type'],
          'options'     => $saved?->options      ?? ($definition['options'] ?? null),
          'is_active'   => $saved ? (bool)$saved->is_active : true,
          'is_required' => $saved ? (bool)$saved->is_required : false,
      ];
  };

  $p = $profile; // alias for brevity
  $u = $courseBook->student;

  $basicKeys    = ['email','dob','gender','whatsapp_no','alternate_mobile','category',
                   'religion','nationality','qualification','employment_status',
                   'computer_literacy','blood_group','aadhar_no','pan_no'];
  $guardianKeys = ['father_name','mother_name','guardian_name','guardian_relation',
                   'guardian_mobile','guardian_occupation'];

  $presentAddressField   = $resolveField('address');
  $permanentAddressField = $resolveField('permanent_address');
  $stateField            = $resolveField('state');
  $districtField         = $resolveField('district');
  $pinCodeField          = $resolveField('pin_code');

  $stepLabels = $educationEnabled
      ? ['Basic Details','Address','Education','Review']
      : ['Basic Details','Address','Review'];

  $institute = $institute ?? auth('institute')->user()->institute;

  // Pre-fill values from saved profile (for review JS to pick up)
  $prePhoto = $p?->photo ? asset($p->photo) : asset($defaultPhotoPath ?? 'images/user.svg');
@endphp

<div class="adm-shell">

  {{-- Header --}}
  <div class="adm-header">
    <h2>{{ $p?->name ?? $u->user_id }}</h2>
    <p>{{ $courseBook->course->name }}@if($courseBook->batch) &middot; {{ $courseBook->batch->name }}@endif
      &nbsp;&middot;&nbsp; Fill in all required details, review the form, then proceed to payment.</p>
  </div>

  @if($errors->any())
    <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5;border-radius:10px;padding:10px 16px;margin-top:14px;font-size:13px;">
      Please fix the highlighted errors and try again.
    </div>
  @endif

  <form method="POST"
        action="{{ route('institute.enrollment.save-profile', $courseBook) }}"
        enctype="multipart/form-data"
        id="profile-form"
        autocomplete="off">
    @csrf
    <input type="hidden" name="_section" value="all">
    {{-- autocomplete spoof --}}
    <input type="text"     name="_spoof_u" value="" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
    <input type="password" name="_spoof_p" value="" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">

    <div class="adm-wrap">
      <div class="adm-card">

        {{-- Step indicators --}}
        <div class="adm-steps" style="grid-template-columns:repeat({{ count($stepLabels) }},1fr)">
          @foreach($stepLabels as $i => $label)
            <div class="adm-step {{ $i === 0 ? 'active' : '' }}" data-indicator>{{ $label }}</div>
          @endforeach
        </div>

        <div class="adm-body">

          {{-- ══ Step 1: Basic + Guardian ══ --}}
          <div class="wizard-step active" data-step>
            <div class="adm-section-title">Basic &amp; Guardian Details</div>
            <div class="adm-section-note">Enter the student's personal information, guardian details, and upload a photo.</div>
            <div class="basic-layout">
              {{-- Left: photo --}}
              <div class="basic-side-stack">
                <div class="photo-box">
                  <img src="{{ $prePhoto }}" id="photo_preview" alt="Student photo"
                       onerror="this.src='{{ asset('images/user.svg') }}'">
                  <div style="font-size:13px;font-weight:700">Student Photo</div>
                  <div style="font-size:11px;color:var(--text-2)">Upload passport-size photo</div>
                  <input type="file" name="photo" id="photo" class="gt-input" accept="image/*"
                         style="margin-top:4px;font-size:12px">
                  @error('photo')<div class="gt-error">{{ $message }}</div>@enderror
                </div>
              </div>

              {{-- Right: fields --}}
              <div class="basic-fields">
                {{-- Name (always required) --}}
                <div class="gt-form-group">
                  <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
                  <input type="text" name="name" class="gt-input" required
                         value="{{ old('name', $p?->name) }}"
                         id="field-name">
                  @error('name')<div class="gt-error">{{ $message }}</div>@enderror
                </div>

                {{-- Mobile (always required) --}}
                <div class="gt-form-group">
                  <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
                  <input type="tel" name="mobile" id="field-mobile" class="gt-input" required
                         value="{{ old('mobile', $u->mobile) }}"
                         maxlength="10" inputmode="numeric" pattern="[0-9]{10}"
                         oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                  <div class="gt-field-error" id="mobile-error" style="display:none;color:var(--danger);font-size:12px;margin-top:3px;"></div>
                  @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
                </div>

                {{-- Basic fields from form builder --}}
                @foreach($basicKeys as $key)
                  @php $field = $resolveField($key); @endphp
                  @continue(!$field?->is_active)
                  <div class="gt-form-group">
                    <label class="gt-label">
                      {{ $field->field_label }}
                      @if($field->is_required)<span style="color:var(--danger)">*</span>@endif
                    </label>
                    @if($key === 'gender')
                      <select name="{{ $key }}" class="gt-select" id="field-{{ $key }}" {{ $field->is_required?'required':'' }}>
                        <option value="">Select</option>
                        @foreach(['Male','Female','Other'] as $o)
                          <option value="{{ $o }}" {{ old($key, $p?->{$key}) === $o ? 'selected' : '' }}>{{ $o }}</option>
                        @endforeach
                      </select>
                    @elseif(in_array($key, ['religion','nationality'], true))
                      @php
                        $opts = $key === 'religion'
                            ? ['Hindu','Muslim','Sikh','Christian','Jain','Buddhist','Other']
                            : ['Indian','NRI','Other'];
                      @endphp
                      <select name="{{ $key }}" class="gt-select" id="field-{{ $key }}" {{ $field->is_required?'required':'' }}>
                        <option value="">Select</option>
                        @foreach($opts as $o)
                          <option value="{{ $o }}" {{ old($key, $p?->{$key}) === $o ? 'selected' : '' }}>{{ $o }}</option>
                        @endforeach
                      </select>
                    @elseif($field->field_type === 'select')
                      @php $opts = collect(explode(',', $field->options ?? ''))->map(fn($o)=>trim($o))->filter(); @endphp
                      <select name="{{ $key }}" class="gt-select" id="field-{{ $key }}" {{ $field->is_required?'required':'' }}>
                        <option value="">Select</option>
                        @foreach($opts as $o)
                          <option value="{{ $o }}" {{ old($key, $p?->{$key}) === $o ? 'selected' : '' }}>{{ $o }}</option>
                        @endforeach
                      </select>
                    @elseif($key === 'email')
                      <input type="email" name="{{ $key }}" class="gt-input" id="field-{{ $key }}"
                             value="{{ old($key, $u->email) }}"
                             {{ $field->is_required?'required':'' }}>
                    @else
                      <input type="{{ in_array($field->field_type,['date','number','email'],true) ? $field->field_type : 'text' }}"
                             name="{{ $key }}" class="gt-input" id="field-{{ $key }}"
                             value="{{ old($key, $key === 'email' ? $u->email : $p?->{$key}) }}"
                             {{ $field->is_required?'required':'' }}>
                    @endif
                    @error($key)<div class="gt-error">{{ $message }}</div>@enderror
                  </div>
                @endforeach

                {{-- Guardian fields --}}
                @php $shownGHeader = false; @endphp
                @foreach($guardianKeys as $key)
                  @php $field = $resolveField($key); @endphp
                  @continue(!$field?->is_active)
                  @if(!$shownGHeader)
                    <div class="field-section-label">Guardian / Parent Details</div>
                    @php $shownGHeader = true; @endphp
                  @endif
                  <div class="gt-form-group">
                    <label class="gt-label">
                      {{ $field->field_label }}
                      @if($field->is_required)<span style="color:var(--danger)">*</span>@endif
                    </label>
                    @if($field->field_type === 'select')
                      @php $opts = collect(explode(',', $field->options ?? ''))->map(fn($o)=>trim($o))->filter(); @endphp
                      <select name="{{ $key }}" class="gt-select" id="field-{{ $key }}" {{ $field->is_required?'required':'' }}>
                        <option value="">Select</option>
                        @foreach($opts as $o)
                          <option value="{{ $o }}" {{ old($key, $p?->{$key}) === $o ? 'selected' : '' }}>{{ $o }}</option>
                        @endforeach
                      </select>
                    @else
                      <input type="{{ in_array($field->field_type,['date','number'],true) ? $field->field_type : 'text' }}"
                             name="{{ $key }}" class="gt-input" id="field-{{ $key }}"
                             value="{{ old($key, $p?->{$key}) }}"
                             {{ $field->is_required?'required':'' }}>
                    @endif
                    @error($key)<div class="gt-error">{{ $message }}</div>@enderror
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          {{-- ══ Step 2: Address ══ --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Address Details</div>
            <div class="adm-section-note">Enter current and permanent address. You can copy present address to permanent with one click.</div>
            <div class="address-sections">
              <div class="address-card">
                <h4 class="address-card-title">Present Address</h4>
                <p class="address-card-note">Student's current residential address.</p>
                <div class="adm-grid">
                  @if($presentAddressField?->is_active)
                    <div class="gt-form-group address-full-width">
                      <label class="gt-label">{{ $presentAddressField->field_label }}
                        @if($presentAddressField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <textarea name="address" id="present_address" class="gt-textarea"
                                {{ $presentAddressField->is_required?'required':'' }}>{{ old('address', $p?->address) }}</textarea>
                      @error('address')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif
                  @if($stateField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">{{ $stateField->field_label }}
                        @if($stateField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <select name="state" id="state" class="gt-select" {{ $stateField->is_required?'required':'' }}>
                        <option value="">Select State</option>
                        @foreach($states as $s)
                          <option value="{{ $s }}" {{ old('state', $p?->state) === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                      </select>
                      @error('state')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif
                  @if($districtField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">{{ $districtField->field_label }}</label>
                      <select name="district" id="district" class="gt-select" {{ $districtField->is_required?'required':'' }}>
                        <option value="">Select District</option>
                      </select>
                      @error('district')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif
                  <div class="gt-form-group">
                    <label class="gt-label">City</label>
                    <input type="text" name="city" id="city" class="gt-input" value="{{ old('city', $p?->city) }}">
                  </div>
                  @if($pinCodeField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">{{ $pinCodeField->field_label }}
                        @if($pinCodeField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <input type="text" name="pin_code" id="pin_code" class="gt-input"
                             value="{{ old('pin_code', $p?->pin_code) }}"
                             {{ $pinCodeField->is_required?'required':'' }}>
                    </div>
                  @endif
                </div>
              </div>

              @if($presentAddressField?->is_active && $permanentAddressField?->is_active)
                <label class="address-copy-row" for="same_as_present">
                  <input type="checkbox" id="same_as_present"
                    {{ old('address') && old('address') === old('permanent_address') ? 'checked' : '' }}>
                  <span>Permanent address is the same as the present address</span>
                </label>
              @endif

              @if($permanentAddressField?->is_active)
                <div class="address-card">
                  <h4 class="address-card-title">Permanent Address</h4>
                  <p class="address-card-note">Permanent home address for records and communication.</p>
                  <div class="adm-grid">
                    <div class="gt-form-group address-full-width">
                      <label class="gt-label">{{ $permanentAddressField->field_label }}
                        @if($permanentAddressField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <textarea name="permanent_address" id="permanent_address" class="gt-textarea"
                                {{ $permanentAddressField->is_required?'required':'' }}>{{ old('permanent_address', $p?->permanent_address) }}</textarea>
                      @error('permanent_address')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                    @if($stateField?->is_active)
                      <div class="gt-form-group">
                        <label class="gt-label">Permanent State</label>
                        <select name="permanent_state" id="permanent_state" class="gt-select">
                          <option value="">Select State</option>
                          @foreach($states as $s)
                            <option value="{{ $s }}" {{ old('permanent_state', $p?->permanent_state) === $s ? 'selected' : '' }}>{{ $s }}</option>
                          @endforeach
                        </select>
                      </div>
                    @endif
                    @if($districtField?->is_active)
                      <div class="gt-form-group">
                        <label class="gt-label">Permanent District</label>
                        <select name="permanent_district" id="permanent_district" class="gt-select">
                          <option value="">Select District</option>
                        </select>
                      </div>
                    @endif
                    <div class="gt-form-group">
                      <label class="gt-label">Permanent City</label>
                      <input type="text" name="permanent_city" id="permanent_city" class="gt-input"
                             value="{{ old('permanent_city', $p?->permanent_city) }}">
                    </div>
                    @if($pinCodeField?->is_active)
                      <div class="gt-form-group">
                        <label class="gt-label">Permanent PIN</label>
                        <input type="text" name="permanent_pin_code" id="permanent_pin_code" class="gt-input"
                               value="{{ old('permanent_pin_code', $p?->permanent_pin_code) }}">
                      </div>
                    @endif
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- ══ Step 3: Education (if enabled) ══ --}}
          @if($educationEnabled)
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Education Details</div>
            <div class="adm-section-note">Add academic qualifications. Click "+ Add Row" for each record. @if($educationRequired)<strong style="color:var(--danger)">At least one record is required.</strong>@endif</div>
            <div class="edu-toolbar">
              <div style="font-size:13px;font-weight:700">Academic History</div>
              <button type="button" class="btn btn-primary btn-sm" id="add-edu-btn">+ Add Row</button>
            </div>
            <div id="education-rows">
              @foreach($education as $edu)
                <div class="edu-row">
                  <input type="text" class="gt-input" name="education[{{ $loop->index }}][examination]"
                         placeholder="Examination" value="{{ $edu->examination }}">
                  <input type="text" class="gt-input" name="education[{{ $loop->index }}][institute_name]"
                         placeholder="Institute" value="{{ $edu->institute_name }}">
                  <input type="text" class="gt-input" name="education[{{ $loop->index }}][board_university]"
                         placeholder="Board/University" value="{{ $edu->board_university }}">
                  <input type="text" class="gt-input" name="education[{{ $loop->index }}][passing_year]"
                         placeholder="Year" value="{{ $edu->passing_year }}">
                  <input type="text" class="gt-input" name="education[{{ $loop->index }}][division]"
                         placeholder="Division" value="{{ $edu->division }}">
                  <input type="text" class="gt-input" name="education[{{ $loop->index }}][marks_percentage]"
                         placeholder="%" value="{{ $edu->marks_percentage }}">
                  <button type="button" class="btn btn-danger btn-sm remove-edu" style="height:36px">×</button>
                </div>
              @endforeach
            </div>
            @error('education')<div class="gt-error" style="margin-top:8px">{{ $message }}</div>@enderror
          </div>
          @endif

          {{-- ══ Actions ══ --}}
          <div class="wizard-actions">
            <button type="button" class="btn btn-outline" id="prev-btn" style="visibility:hidden">← Previous</button>
            <div style="display:flex;gap:10px">
              <button type="button" class="btn btn-primary" id="next-btn">Next →</button>
              <button type="submit" class="btn btn-success" id="submit-btn" style="display:none"
                      data-loading-text="Saving details...">
                Save &amp; Preview →
              </button>
            </div>
          </div>

        </div>{{-- /adm-body --}}
      </div>{{-- /adm-card --}}
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const steps      = [...document.querySelectorAll('[data-step]')];
  const indicators = [...document.querySelectorAll('[data-indicator]')];
  const nextBtn    = document.getElementById('next-btn');
  const prevBtn    = document.getElementById('prev-btn');
  const submitBtn  = document.getElementById('submit-btn');
  let active = 0;

  const districtsByState = @json($districtsByState ?? []);

  function syncUI() {
    steps.forEach((s,i) => s.classList.toggle('active', i === active));
    indicators.forEach((ind,i) => ind.classList.toggle('active', i === active));
    prevBtn.style.visibility = active === 0 ? 'hidden' : 'visible';
    nextBtn.style.display    = active === steps.length - 1 ? 'none' : '';
    submitBtn.style.display  = active === steps.length - 1 ? '' : 'none';
  }

  function validateStep() {
    const cur = steps[active];
    for (const el of cur.querySelectorAll('input,select,textarea')) {
      if (el.offsetParent !== null && !el.reportValidity()) return false;
    }
    return true;
  }

  nextBtn.addEventListener('click', () => {
    if (!validateStep()) return;
    active = Math.min(active + 1, steps.length - 1);
    syncUI();
    window.scrollTo({top: 0, behavior: 'smooth'});
  });

  prevBtn.addEventListener('click', () => {
    active = Math.max(active - 1, 0);
    syncUI();
    window.scrollTo({top: 0, behavior: 'smooth'});
  });

  // ── State → District ──
  function renderDistricts(stateEl, districtEl, preSelected) {
    if (!stateEl || !districtEl) return;
    const districts = districtsByState[stateEl.value] || [];
    districtEl.innerHTML = '<option value="">Select District</option>' +
      districts.map(d => `<option value="${d}" ${d === preSelected ? 'selected' : ''}>${d}</option>`).join('');
  }

  const stateEl    = document.getElementById('state');
  const districtEl = document.getElementById('district');
  const permStateEl = document.getElementById('permanent_state');
  const permDistEl  = document.getElementById('permanent_district');

  if (stateEl && districtEl) {
    renderDistricts(stateEl, districtEl, @json(old('district', $p?->district)));
    stateEl.addEventListener('change', () => renderDistricts(stateEl, districtEl, ''));
  }
  if (permStateEl && permDistEl) {
    renderDistricts(permStateEl, permDistEl, @json(old('permanent_district', $p?->permanent_district)));
    permStateEl.addEventListener('change', () => renderDistricts(permStateEl, permDistEl, ''));
  }

  // ── Copy present → permanent ──
  const sameChk      = document.getElementById('same_as_present');
  const presAddrEl   = document.getElementById('present_address');
  const permAddrEl   = document.getElementById('permanent_address');
  const cityEl       = document.getElementById('city');
  const permCityEl   = document.getElementById('permanent_city');
  const pinEl        = document.getElementById('pin_code');
  const permPinEl    = document.getElementById('permanent_pin_code');

  function copyToPermAddr() {
    if (!sameChk?.checked) return;
    if (permAddrEl && presAddrEl) permAddrEl.value = presAddrEl.value;
    if (permStateEl && stateEl)   { permStateEl.value = stateEl.value; renderDistricts(permStateEl, permDistEl, districtEl?.value || ''); }
    if (permCityEl && cityEl)     permCityEl.value = cityEl.value;
    if (permPinEl && pinEl)       permPinEl.value  = pinEl.value;
  }

  sameChk?.addEventListener('change', copyToPermAddr);
  presAddrEl?.addEventListener('input', copyToPermAddr);
  stateEl?.addEventListener('change',  copyToPermAddr);
  cityEl?.addEventListener('input',    copyToPermAddr);
  pinEl?.addEventListener('input',     copyToPermAddr);

  // ── Photo preview ──
  document.getElementById('photo')?.addEventListener('change', function() {
    if (!this.files[0]) return;
    const r = new FileReader();
    r.onload = e => { document.getElementById('photo_preview').src = e.target.result; };
    r.readAsDataURL(this.files[0]);
  });

  // ── Education rows ──
  let eduIdx = {{ $education->count() }};
  function addEduRow(data = {}) {
    const i = eduIdx++;
    const div = document.createElement('div');
    div.className = 'edu-row';
    div.innerHTML = `
      <input type="text" class="gt-input" name="education[${i}][examination]"       placeholder="Examination"  value="${data.examination||''}">
      <input type="text" class="gt-input" name="education[${i}][institute_name]"     placeholder="Institute"    value="${data.institute_name||''}">
      <input type="text" class="gt-input" name="education[${i}][board_university]"   placeholder="Board/Univ."  value="${data.board_university||''}">
      <input type="text" class="gt-input" name="education[${i}][passing_year]"       placeholder="Year"         value="${data.passing_year||''}">
      <input type="text" class="gt-input" name="education[${i}][division]"           placeholder="Division"     value="${data.division||''}">
      <input type="text" class="gt-input" name="education[${i}][marks_percentage]"   placeholder="%"            value="${data.marks_percentage||''}">
      <button type="button" class="btn btn-danger btn-sm remove-edu" style="height:36px">×</button>`;
    document.getElementById('education-rows').appendChild(div);
    div.querySelector('.remove-edu').addEventListener('click', () => div.remove());
  }

  document.getElementById('add-edu-btn')?.addEventListener('click', () => addEduRow());
  document.querySelectorAll('.remove-edu').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.edu-row').remove());
  });

  // ── Mobile format ──
  const mobEl = document.getElementById('field-mobile');
  if (mobEl) {
    mobEl.addEventListener('input', () => { mobEl.value = mobEl.value.replace(/\D/g,'').slice(0,10); });
  }

  syncUI();
})();
</script>
@endpush
