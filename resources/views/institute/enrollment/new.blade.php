@extends('layouts.institute')
@section('title','New Admission')
@section('page-title','New Admission')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@push('styles')
<style>
.adm-shell{max-width:1160px;margin:0 auto}
.adm-header{background:linear-gradient(135deg,#1746a2,#1b75d0);color:#fff;border-radius:22px;padding:26px 30px;box-shadow:0 20px 45px rgba(23,70,162,.18)}
.adm-header h2{margin:0;font-size:28px;font-weight:900}
.adm-header p{margin:8px 0 0;opacity:.84}
.adm-wrap{display:block;margin-top:18px}
.adm-card{background:var(--bg-2);border:1px solid var(--border);border-radius:20px;overflow:hidden}
.adm-steps{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;padding:22px;background:#eef4ff}
.adm-step{padding:12px 8px;border-radius:14px;background:#dce9ff;color:#5878a8;text-align:center;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
.adm-step.active{background:#1746a2;color:#fff}
.adm-body{padding:24px}
.wizard-step{display:none}
.wizard-step.active{display:block}
.adm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.adm-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.adm-section-title{font-size:20px;font-weight:900;margin:0 0 8px}
.adm-section-note{font-size:13px;color:var(--text-2);margin-bottom:18px}
.fee-box{background:#f7faff;border:1px solid #d9e7ff;border-radius:18px;padding:18px}
.fee-lines{display:flex;flex-direction:column;gap:10px}
.fee-line{display:flex;justify-content:space-between;font-size:13px}
.fee-line strong{font-weight:800}
.fee-total{margin-top:12px;padding-top:12px;border-top:1px solid #d9e7ff;font-size:17px;font-weight:900;color:#1746a2}
.photo-box{border:1px dashed #b8c9e9;border-radius:18px;padding:18px;background:#f7faff;text-align:center;display:flex;flex-direction:column;gap:8px;align-self:start}
.photo-box img{width:110px;height:110px;border-radius:50%;object-fit:cover;margin:0 auto 4px;border:3px solid #fff;box-shadow:0 8px 24px rgba(0,0,0,.08)}
.basic-layout{display:grid;grid-template-columns:280px minmax(0,1fr);gap:20px;align-items:start}
.basic-side-stack{display:grid;gap:14px}
.basic-fields{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.inline-fee-panel{margin-top:18px;background:#f7faff;border:1px solid #d9e7ff;border-radius:18px;padding:18px}
.inline-fee-title{font-size:12px;font-weight:800;color:#64748b;letter-spacing:.08em;text-transform:uppercase;margin-bottom:12px}
.photo-upload-input{margin-top:4px}
.basic-side-card{border:1px solid #d9e7ff;border-radius:18px;padding:14px;background:#f7faff}
.basic-side-title{font-size:12px;font-weight:800;color:#64748b;letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px}
.basic-side-line{display:flex;justify-content:space-between;gap:12px;padding:7px 0;border-bottom:1px solid rgba(148,163,184,.18);font-size:13px}
.basic-side-line:last-child{border-bottom:none}
.basic-side-line strong{font-weight:800;color:#1746a2}
.address-sections{display:grid;gap:18px}
.address-card{border:1px solid var(--border);border-radius:18px;padding:18px;background:var(--bg-3)}
.address-card-title{font-size:15px;font-weight:900;margin:0 0 4px}
.address-card-note{font-size:12px;color:var(--text-2);margin:0 0 14px}
.address-copy-row{display:flex;align-items:center;gap:10px;padding:0 4px;color:var(--text-2);font-size:13px;font-weight:700}
.address-copy-row input{width:16px;height:16px}
.address-full-width{grid-column:1/-1}
.edu-toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.edu-row{display:grid;grid-template-columns:repeat(6,minmax(0,1fr)) 44px;gap:10px;padding:12px;border:1px solid var(--border);border-radius:14px;background:var(--bg-3);margin-bottom:10px}
.review-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px}
.review-print-shell{width:210mm;max-width:100%;min-height:297mm;margin:0 auto;border:1px solid #000;background:#fff;padding:7mm;box-sizing:border-box;color:#000}
.review-print-header{display:flex;justify-content:space-between;gap:12px;padding-bottom:6px;border-bottom:1px solid #000}
.review-print-title{font-size:20px;font-weight:800;color:#000}
.review-print-subtitle{font-size:11px;color:#000;margin-top:2px}
.review-print-address{font-size:9px;color:#000;margin-top:4px;max-width:560px;line-height:1.3}
.review-photo-slot{width:30mm;height:38mm;border:1px solid #000;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#fff;font-size:10px;color:#000;text-align:center;flex-shrink:0}
.review-photo-slot img{width:100%;height:100%;object-fit:cover}
.review-top-grid{display:block;margin-top:12px}
.review-section-card{border:1px solid #000;padding:7px 8px;background:#fff;break-inside:avoid;margin-top:8px}
.review-section-card:first-child{margin-top:0}
.review-section-heading{font-size:12px;font-weight:800;color:#000;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
.review-field-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:6px}
.review-field{border:1px solid #000;padding:5px 6px;background:#fff}
.review-field-wide{grid-column:1/-1}
.review-field label{display:block;font-size:8px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:#000;margin-bottom:3px}
.review-field-value{font-size:10px;color:#000;line-height:1.25;min-height:14px;word-break:break-word}
.review-top-lines{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:6px}
.review-top-line{border:1px solid #000;padding:5px 6px;background:#fff}
.review-top-line label{display:block;font-size:8px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:#000;margin-bottom:3px}
.review-edu-table{width:100%;border-collapse:collapse}
.review-edu-table th,.review-edu-table td{border:1px solid #000;padding:4px 5px;font-size:9px;text-align:left;color:#000}
.review-edu-table th{background:#fff;font-weight:800;color:#000}
.review-sign-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:12px}
.review-sign-block{padding-top:14px;text-align:center;font-size:9px;color:#000}
.review-sign-line{border-top:1px solid #000;margin-bottom:6px}
.plan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.plan-card{border:1px solid var(--border);border-radius:16px;padding:16px;background:var(--bg-3);cursor:pointer}
.plan-card.active{border-color:#1746a2;background:#eef4ff;box-shadow:0 0 0 2px rgba(23,70,162,.12)}
.plan-code{font-size:24px;font-weight:900;color:#1746a2}
.plan-name{font-size:14px;font-weight:800;margin:4px 0}
.wizard-actions{display:flex;justify-content:space-between;gap:12px;margin-top:22px}
.pay-note{margin-top:12px;padding:14px;border-radius:14px;background:#eef4ff;color:#3f587c;font-size:13px;line-height:1.5}
.hidden-input{display:none}
@media print{
  @page{size:A4 portrait;margin:10mm}
  html,body{margin:0;padding:0;background:#fff}
  body *{visibility:hidden}
  #review-print-area,#review-print-area *{visibility:visible}
  #review-print-area{position:absolute;left:0;top:0;width:190mm;padding:0;border:none}
  .review-toolbar,.adm-steps,.wizard-actions,.adm-header,.adm-section-title,.adm-section-note{display:none !important}
  .review-print-shell{width:190mm;min-height:auto;border:none;padding:0;box-shadow:none}
  .review-section-card{margin-top:6px}
}
@media(max-width:1080px){.basic-layout{grid-template-columns:1fr}}
@media(max-width:760px){.adm-steps{grid-template-columns:repeat(2,1fr)}.adm-grid,.adm-grid-3,.plan-grid,.edu-row,.basic-fields,.review-field-grid,.review-top-lines,.review-sign-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@php
  $definedFields = collect(\App\Models\AdmissionFormField::allDefinedFields())->keyBy('key');
  $resolveField = function (string $key) use ($savedFields, $definedFields) {
      $definition = $definedFields[$key] ?? null;
      if (! $definition) {
          return null;
      }

      $saved = $savedFields[$key] ?? null;

      return (object) [
          'field_key' => $key,
          'field_label' => $saved?->field_label ?? $definition['label'],
          'field_type' => $saved?->field_type ?? $definition['type'],
          'options' => $saved?->options ?? ($definition['options'] ?? null),
          'is_active' => $saved ? (bool) $saved->is_active : true,
          'is_required' => $saved ? (bool) $saved->is_required : false,
      ];
  };
  $educationField = $resolveField('education_details');
  $educationEnabled = (bool) ($educationField?->is_active);
  $basicKeys = ['email','dob','gender','whatsapp_no','alternate_mobile','category','religion','nationality','qualification','employment_status','computer_literacy','blood_group','aadhar_no','pan_no'];
  $guardianKeys = ['father_name','mother_name','guardian_name','guardian_relation','guardian_mobile','guardian_occupation'];
  $presentAddressField = $resolveField('address');
  $permanentAddressField = $resolveField('permanent_address');
  $stateField = $resolveField('state');
  $districtField = $resolveField('district');
  $pinCodeField = $resolveField('pin_code');
  $reviewSections = [
      ['title' => 'Student Details', 'keys' => ['email', 'dob', 'gender', 'whatsapp_no', 'alternate_mobile', 'aadhar_no', 'pan_no', 'blood_group', 'category', 'religion', 'nationality', 'employment_status', 'computer_literacy', 'qualification', 'photo']],
      ['title' => 'Address Details', 'keys' => ['address', 'state', 'district', 'city', 'pin_code', 'permanent_address', 'permanent_state', 'permanent_district', 'permanent_city', 'permanent_pin_code']],
      ['title' => 'Guardian Details', 'keys' => ['father_name', 'mother_name', 'guardian_name', 'guardian_relation', 'guardian_mobile', 'guardian_occupation']],
      ['title' => 'Education Details', 'keys' => ['education_details']],
  ];
  $reviewWideFields = ['address', 'permanent_address', 'education_details', 'photo'];
  $institute = auth('institute')->user()->institute;
  $stepLabels = $educationEnabled
      ? ['Course','Basic','Address','Education','Review','Payment']
      : ['Course','Basic','Address','Review','Payment'];
@endphp

<div class="adm-shell">
  <div class="adm-header">
    <h2>Student Admission Wizard</h2>
    <p>This flow saves the seat booking first. Final admission becomes active only after the required payment and complete details are received.</p>
  </div>

  <form method="POST" action="{{ route('institute.enrollment.store-new') }}" enctype="multipart/form-data" id="admission-form">
    @csrf
    <div class="adm-wrap">
      <div class="adm-card">
        <div class="adm-steps">
          @foreach($stepLabels as $index => $step)
            <div class="adm-step {{ $index === 0 ? 'active' : '' }}" data-indicator>{{ $step }}</div>
          @endforeach
        </div>

        <div class="adm-body">
          <div class="wizard-step active" data-step>
            <div class="adm-section-title">Course Setup</div>
            <div class="adm-section-note">Select a course type first. The course list will then show matching courses with their duration and fee breakup.</div>
            <div class="adm-grid">
              <div class="gt-form-group">
                <label class="gt-label">Course Type <span style="color:var(--danger)">*</span></label>
                <select id="course_type_id" class="gt-select" required>
                  <option value="">Select Course Type</option>
                  @foreach($courseTypes as $courseType)
                    <option value="{{ $courseType->id }}">{{ $courseType->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="gt-form-group">
                <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
                <select name="course_id" id="course_id" class="gt-select" required>
                  <option value="">Select Course</option>
                </select>
                @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
              </div>

              <div class="gt-form-group">
                <label class="gt-label">Batch</label>
                <select name="batch_id" id="batch_id" class="gt-select">
                  <option value="">No Batch</option>
                  @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="gt-form-group">
                <label class="gt-label">Admission Source</label>
                <input type="text" class="gt-input" value="DIRECT" readonly>
              </div>

              <div class="gt-form-group">
                <label class="gt-label">Course Duration</label>
                <input type="text" class="gt-input" id="course_duration_view" value="-" readonly>
              </div>
            </div>

            <div class="inline-fee-panel">
              <div class="inline-fee-title">Fee Preview</div>
              <div class="fee-lines" id="fee-breakup" style="margin-top:12px;">
                <div class="text-sm text-muted">Select a course to view fee details.</div>
              </div>
              <div class="fee-total" id="fee-total">Total: Rs.0.00</div>
              <div class="pay-note" style="margin-top:16px;">
                The base course fee and all bound extra fees are shown here separately. The student will pay the combined total during admission.
              </div>
            </div>
          </div>

          <div class="wizard-step" data-step>
            <div class="adm-section-title">Basic and Guardian Details</div>
            <div class="adm-section-note">Complete the student profile, guardian details, and profile photo in this step.</div>
            <div class="basic-layout">
              <div class="basic-side-stack">
                <div class="photo-box">
                  <img src="{{ asset(old('photo_preview', $defaultPhotoPath)) }}" id="photo_preview" alt="Student photo">
                  <div class="text-sm fw-700">Student Photo</div>
                  <div class="text-xs text-muted">Upload a clear passport-size photo.</div>
                  <input type="file" name="photo" id="photo" class="gt-input photo-upload-input" accept="image/*">
                  @error('photo')<div class="gt-error">{{ $message }}</div>@enderror
                </div>
                <div class="basic-side-card">
                  <div class="basic-side-title">Admission Summary</div>
                  <div class="basic-side-line"><span>Course</span><strong id="side-course-name">-</strong></div>
                  <div class="basic-side-line"><span>Duration</span><strong id="side-course-duration">-</strong></div>
                  <div class="basic-side-line"><span>Total Fee</span><strong id="side-course-total">Rs. 0.00</strong></div>
                  <div class="basic-side-line"><span>Upfront Fee</span><strong id="side-course-required">Rs. 0.00</strong></div>
                </div>
              </div>

              <div class="basic-fields">
                <div class="gt-form-group">
                  <label class="gt-label">Student Name <span style="color:var(--danger)">*</span></label>
                  <input type="text" name="name" class="gt-input" value="{{ old('name') }}" required>
                  @error('name')<div class="gt-error">{{ $message }}</div>@enderror
                </div>

                <div class="gt-form-group">
                  <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
                  <input type="text" name="mobile" class="gt-input" value="{{ old('mobile') }}" required>
                  @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
                </div>

                @foreach($basicKeys as $key)
                  @php $field = $resolveField($key); @endphp
                  @continue(!$field?->is_active)
                  <div class="gt-form-group">
                    <label class="gt-label">
                      {{ $field->field_label }}
                      @if($field->is_required)<span style="color:var(--danger)">*</span>@endif
                    </label>
                    @if($field->field_type === 'select')
                      @php
                        $options = collect(explode(',', $field->options ?? ''))->map(fn($opt) => trim($opt))->filter();
                        if ($key === 'gender') {
                            $options = collect(['Male','Female','Other']);
                        }
                      @endphp
                      <select name="{{ $key }}" class="gt-select" {{ $field->is_required ? 'required' : '' }}>
                        <option value="">Select</option>
                        @foreach($options as $option)
                          <option value="{{ $option }}" {{ old($key) === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                      </select>
                    @elseif($field->field_type === 'textarea')
                      <textarea name="{{ $key }}" class="gt-textarea" {{ $field->is_required ? 'required' : '' }}>{{ old($key) }}</textarea>
                    @else
                      <input
                        type="{{ in_array($field->field_type, ['email','date','number'], true) ? $field->field_type : 'text' }}"
                        name="{{ $key }}"
                        class="gt-input"
                        value="{{ old($key) }}"
                        {{ $field->is_required ? 'required' : '' }}
                      >
                    @endif
                    @error($key)<div class="gt-error">{{ $message }}</div>@enderror
                  </div>
                @endforeach

                @foreach($guardianKeys as $key)
                  @php $field = $resolveField($key); @endphp
                  @continue(!$field?->is_active)
                  <div class="gt-form-group">
                    <label class="gt-label">
                      {{ $field->field_label }}
                      @if($field->is_required)<span style="color:var(--danger)">*</span>@endif
                    </label>
                    @if($field->field_type === 'select')
                      @php $options = collect(explode(',', $field->options ?? ''))->map(fn($opt) => trim($opt))->filter(); @endphp
                      <select name="{{ $key }}" class="gt-select" {{ $field->is_required ? 'required' : '' }}>
                        <option value="">Select</option>
                        @foreach($options as $option)
                          <option value="{{ $option }}" {{ old($key) === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                      </select>
                    @elseif($field->field_type === 'textarea')
                      <textarea name="{{ $key }}" class="gt-textarea" {{ $field->is_required ? 'required' : '' }}>{{ old($key) }}</textarea>
                    @else
                      <input
                        type="{{ in_array($field->field_type, ['email','date','number'], true) ? $field->field_type : 'text' }}"
                        name="{{ $key }}"
                        class="gt-input"
                        value="{{ old($key) }}"
                        {{ $field->is_required ? 'required' : '' }}
                      >
                    @endif
                    @error($key)<div class="gt-error">{{ $message }}</div>@enderror
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="wizard-step" data-step>
            <div class="adm-section-title">Address Details</div>
            <div class="adm-section-note">Capture the current address and permanent address separately. You can copy the current address into the permanent address with one click.</div>
            <div class="address-sections">
              <div class="address-card">
                <h4 class="address-card-title">Present Address</h4>
                <p class="address-card-note">Enter the student's current address and location details.</p>
                <div class="adm-grid">
                  @if($presentAddressField?->is_active)
                    <div class="gt-form-group address-full-width">
                      <label class="gt-label">
                        {{ $presentAddressField->field_label }}
                        @if($presentAddressField->is_required)<span style="color:var(--danger)">*</span>@endif
                      </label>
                      <textarea name="address" id="address" class="gt-textarea" {{ $presentAddressField->is_required ? 'required' : '' }}>{{ old('address') }}</textarea>
                      @error('address')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif

                  @if($stateField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">
                        {{ $stateField->field_label }}
                        @if($stateField->is_required)<span style="color:var(--danger)">*</span>@endif
                      </label>
                      <select name="state" id="state" class="gt-select" {{ $stateField->is_required ? 'required' : '' }}>
                        <option value="">Select State</option>
                        @foreach($states as $state)
                          <option value="{{ $state }}" {{ old('state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                      </select>
                      @error('state')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif

                  @if($districtField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">
                        {{ $districtField->field_label }}
                        @if($districtField->is_required)<span style="color:var(--danger)">*</span>@endif
                      </label>
                      <select name="district" id="district" class="gt-select" {{ $districtField->is_required ? 'required' : '' }}>
                        <option value="">Select District</option>
                      </select>
                      @error('district')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif

                  <div class="gt-form-group">
                    <label class="gt-label">City</label>
                    <input type="text" name="city" id="city" class="gt-input" value="{{ old('city') }}">
                    @error('city')<div class="gt-error">{{ $message }}</div>@enderror
                  </div>

                  @if($pinCodeField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">
                        {{ $pinCodeField->field_label }}
                        @if($pinCodeField->is_required)<span style="color:var(--danger)">*</span>@endif
                      </label>
                      <input
                        type="text"
                        name="pin_code"
                        id="pin_code"
                        class="gt-input"
                        value="{{ old('pin_code') }}"
                        {{ $pinCodeField->is_required ? 'required' : '' }}
                      >
                      @error('pin_code')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif
                </div>
              </div>

              @if($presentAddressField?->is_active && $permanentAddressField?->is_active)
                <label class="address-copy-row" for="same_as_present_address">
                  <input type="checkbox" id="same_as_present_address" {{ old('address') && old('address') === old('permanent_address') ? 'checked' : '' }}>
                  <span>Permanent address is the same as the present address.</span>
                </label>
              @endif

              @if($permanentAddressField?->is_active)
                <div class="address-card">
                  <h4 class="address-card-title">Permanent Address</h4>
                  <p class="address-card-note">Store the permanent address separately for documents and communication.</p>
                  <div class="adm-grid">
                    <div class="gt-form-group address-full-width">
                      <label class="gt-label">
                        {{ $permanentAddressField->field_label }}
                        @if($permanentAddressField->is_required)<span style="color:var(--danger)">*</span>@endif
                      </label>
                      <textarea name="permanent_address" id="permanent_address" class="gt-textarea" {{ $permanentAddressField->is_required ? 'required' : '' }}>{{ old('permanent_address') }}</textarea>
                      @error('permanent_address')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>

                    @if($stateField?->is_active)
                      <div class="gt-form-group">
                        <label class="gt-label">Permanent State</label>
                        <select name="permanent_state" id="permanent_state" class="gt-select">
                          <option value="">Select State</option>
                          @foreach($states as $state)
                            <option value="{{ $state }}" {{ old('permanent_state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                          @endforeach
                        </select>
                        @error('permanent_state')<div class="gt-error">{{ $message }}</div>@enderror
                      </div>
                    @endif

                    @if($districtField?->is_active)
                      <div class="gt-form-group">
                        <label class="gt-label">Permanent District</label>
                        <select name="permanent_district" id="permanent_district" class="gt-select">
                          <option value="">Select District</option>
                        </select>
                        @error('permanent_district')<div class="gt-error">{{ $message }}</div>@enderror
                      </div>
                    @endif

                    <div class="gt-form-group">
                      <label class="gt-label">Permanent City</label>
                      <input type="text" name="permanent_city" id="permanent_city" class="gt-input" value="{{ old('permanent_city') }}">
                      @error('permanent_city')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>

                    @if($pinCodeField?->is_active)
                      <div class="gt-form-group">
                        <label class="gt-label">Permanent PIN Code</label>
                        <input
                          type="text"
                          name="permanent_pin_code"
                          id="permanent_pin_code"
                          class="gt-input"
                          value="{{ old('permanent_pin_code') }}"
                        >
                        @error('permanent_pin_code')<div class="gt-error">{{ $message }}</div>@enderror
                      </div>
                    @endif
                  </div>
                </div>
              @endif
            </div>
          </div>

          @if($educationEnabled)
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Education Details</div>
            <div class="adm-section-note">No education row is added by default. Use `+ Add Row` to add academic records.</div>
            <div class="edu-toolbar">
              <div class="text-sm fw-700">Education Table</div>
              <button type="button" class="btn btn-primary btn-sm" id="add-education-row">+ Add Row</button>
            </div>
            <div id="education-rows"></div>
            @error('education')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
          @endif

          <div class="wizard-step" data-step>
            <div class="adm-section-title">Confirm and Review</div>
            <div class="adm-section-note">Review the complete admission form in the same structure used by the form preview. You can also print this review directly.</div>
            <div class="review-toolbar">
              <div class="text-sm text-muted">This preview follows the same section layout as the Form Builder print view.</div>
              <button type="button" class="btn btn-outline" id="print-review">Print Form</button>
            </div>
            <div class="review-print-shell" id="review-print-area">
              <div class="review-print-header">
                <div>
                  <div class="review-print-title">{{ $institute?->name ?? 'Institute Name' }}</div>
                  <div class="review-print-subtitle">Admission Application Form</div>
                  <div class="review-print-address">{{ $institute?->address ?? 'Institute address will appear here.' }}</div>
                </div>
                <div class="review-photo-slot">
                  <img src="{{ asset(old('photo_preview', $defaultPhotoPath)) }}" id="review_photo_preview" alt="Student photo">
                </div>
              </div>

              <div class="review-top-grid">
                <div class="review-section-card">
                  <div class="review-section-heading">Basic Information</div>
                  <div class="review-top-lines">
                    <div class="review-top-line">
                      <label>Student Name</label>
                      <div class="review-field-value" data-review-field="name">-</div>
                    </div>
                    <div class="review-top-line">
                      <label>Mobile</label>
                      <div class="review-field-value" data-review-field="mobile">-</div>
                    </div>
                    <div class="review-top-line">
                      <label>Course</label>
                      <div class="review-field-value" data-review-meta="course">-</div>
                    </div>
                    <div class="review-top-line">
                      <label>Batch</label>
                      <div class="review-field-value" data-review-meta="batch">-</div>
                    </div>
                  </div>
                </div>
              </div>

              @foreach($reviewSections as $section)
                @if($section['title'] === 'Education Details' && ! $educationEnabled)
                  @continue
                @endif
                <div class="review-section-card" style="margin-top:18px;">
                  <div class="review-section-heading">{{ $section['title'] }}</div>
                  <div class="review-field-grid">
                    @foreach($section['keys'] as $key)
                      @if($key === 'education_details')
                        <div class="review-field review-field-wide">
                          <label>Education Details</label>
                          <div class="review-field-value" data-review-education>
                            <table class="review-edu-table">
                              <thead>
                                <tr>
                                  <th>Examination</th>
                                  <th>Institute</th>
                                  <th>Board / University</th>
                                  <th>Year</th>
                                  <th>Division</th>
                                  <th>Percentage</th>
                                </tr>
                              </thead>
                              <tbody id="review-education-body">
                                <tr>
                                  <td colspan="6">No education details added.</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        @continue
                      @endif

                      @php $field = $resolveField($key); @endphp
                      @continue(!in_array($key, ['city', 'permanent_state', 'permanent_district', 'permanent_city', 'permanent_pin_code'], true) && !$field?->is_active)
                      <div class="review-field {{ in_array($key, $reviewWideFields, true) ? 'review-field-wide' : '' }}">
                        <label>
                          {{ $field?->field_label ?? \Illuminate\Support\Str::of($key)->replace('_', ' ')->title() }}
                        </label>
                        <div class="review-field-value" data-review-field="{{ $key }}">-</div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach

              <div class="review-sign-grid">
                <div class="review-sign-block">
                  <div class="review-sign-line"></div>
                  Student Signature
                </div>
                <div class="review-sign-block">
                  <div class="review-sign-line"></div>
                  Guardian Signature
                </div>
                <div class="review-sign-block">
                  <div class="review-sign-line"></div>
                  Authorized Signature
                </div>
              </div>
            </div>
          </div>

          <div class="wizard-step" data-step>
            <div class="adm-section-title">Payment Setup</div>
            <div class="adm-section-note">The first-payment rule updates automatically based on the selected payment plan.</div>
            <input type="hidden" name="payment_plan_type_id" id="payment_plan_type_id" value="{{ old('payment_plan_type_id') }}">
            <div class="plan-grid">
              @foreach($plans as $plan)
                <div class="plan-card {{ old('payment_plan_type_id') == $plan->id ? 'active' : '' }}" data-plan-card data-plan-id="{{ $plan->id }}" data-plan-type="{{ $plan->type }}" data-grace="{{ $plan->grace_days }}" data-late="{{ $plan->late_fee_per_day }}">
                  <div class="plan-code">{{ $plan->type === 'MONTHLY' ? 'MON' : $plan->type }}</div>
                  <div class="plan-name">{{ $plan->name }}</div>
                  <div class="text-xs text-muted">Grace: {{ $plan->grace_days }} days | Late fee: Rs. {{ number_format($plan->late_fee_per_day, 2) }}/day</div>
                </div>
              @endforeach
            </div>
            @error('payment_plan_type_id')<div class="gt-error">{{ $message }}</div>@enderror

            <div class="adm-grid" style="margin-top:18px;">
              <div class="gt-form-group">
                <label class="gt-label">First Payment Amount</label>
                <input type="number" name="first_payment_amount" id="first_payment_amount" class="gt-input" min="0" step="0.01" value="{{ old('first_payment_amount', 0) }}" required>
                @error('first_payment_amount')<div class="gt-error">{{ $message }}</div>@enderror
              </div>

              <div class="gt-form-group">
                <label class="gt-label">Payment Date</label>
                <input type="date" name="payment_date" id="payment_date" class="gt-input" value="{{ old('payment_date', now()->toDateString()) }}">
              </div>

              <div class="gt-form-group">
                <label class="gt-label">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="gt-select">
                  <option value="">Select</option>
                  @foreach(['CASH','UPI','NEFT','IMPS','CHEQUE'] as $mode)
                    <option value="{{ $mode }}" {{ old('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                  @endforeach
                </select>
                @error('payment_mode')<div class="gt-error">{{ $message }}</div>@enderror
              </div>

              <div class="gt-form-group">
                <label class="gt-label">UTR / Ref No</label>
                <input type="text" name="utr" class="gt-input" value="{{ old('utr') }}">
              </div>

              <div class="gt-form-group" style="grid-column:1/-1;">
                <label class="gt-label">Payment Note</label>
                <textarea name="payment_note" class="gt-textarea">{{ old('payment_note') }}</textarea>
              </div>
            </div>

            <div class="pay-note" id="payment-help">
              Select course and payment plan to see payment rule.
            </div>
          </div>

          <div class="wizard-actions">
            <button type="button" class="btn btn-outline" id="prev-step" style="visibility:hidden;">Previous</button>
            <div style="display:flex;gap:10px;">
              <button type="button" class="btn btn-primary" id="next-step">Next</button>
              <button type="submit" class="btn btn-success" id="submit-step" style="display:none;">Save Seat Booking</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const steps = [...document.querySelectorAll('[data-step]')];
  const indicators = [...document.querySelectorAll('[data-indicator]')];
  const nextBtn = document.getElementById('next-step');
  const prevBtn = document.getElementById('prev-step');
  const submitBtn = document.getElementById('submit-step');
  const form = document.getElementById('admission-form');
  const courseTypeSelect = document.getElementById('course_type_id');
  const courseSelect = document.getElementById('course_id');
  const courseCatalog = @json($courseCatalog);
  const districtsByState = @json($districtsByState ?? []);
  const planCards = [...document.querySelectorAll('[data-plan-card]')];
  const paymentPlanInput = document.getElementById('payment_plan_type_id');
  const paymentHelp = document.getElementById('payment-help');
  const firstPaymentInput = document.getElementById('first_payment_amount');
  const photoInput = document.getElementById('photo');
  const photoPreview = document.getElementById('photo_preview');
  const presentAddressInput = document.getElementById('address');
  const permanentAddressInput = document.getElementById('permanent_address');
  const sameAddressCheckbox = document.getElementById('same_as_present_address');
  const stateSelect = document.getElementById('state');
  const districtSelect = document.getElementById('district');
  const cityInput = document.getElementById('city');
  const pinCodeInput = document.getElementById('pin_code');
  const permanentStateSelect = document.getElementById('permanent_state');
  const permanentDistrictSelect = document.getElementById('permanent_district');
  const permanentCityInput = document.getElementById('permanent_city');
  const permanentPinCodeInput = document.getElementById('permanent_pin_code');
  const educationRows = document.getElementById('education-rows');
  const addEducationButton = document.getElementById('add-education-row');
  const feeBreakup = document.getElementById('fee-breakup');
  const feeTotal = document.getElementById('fee-total');
  const reviewStepIndex = steps.findIndex((step) => step.querySelector('#review-print-area'));
  const reviewEducationBody = document.getElementById('review-education-body');
  const reviewPhotoPreview = document.getElementById('review_photo_preview');
  const printReviewButton = document.getElementById('print-review');
  const sideCourseName = document.getElementById('side-course-name');
  const sideCourseDuration = document.getElementById('side-course-duration');
  const sideCourseTotal = document.getElementById('side-course-total');
  const sideCourseRequired = document.getElementById('side-course-required');
  let activeStep = 0;
  let educationIndex = 0;

  function money(value) {
    return 'Rs. ' + Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function selectedCourse() {
    return courseCatalog.find((course) => String(course.id) === String(courseSelect.value)) || null;
  }

  function selectedCourseTypeId() {
    return courseTypeSelect.value || '';
  }

  function renderCourseOptions() {
    const selectedTypeId = selectedCourseTypeId();
    const oldValue = courseSelect.value;
    const filteredCourses = courseCatalog.filter((course) => {
      if (!selectedTypeId) {
        return false;
      }

      return String(course.course_type_id || '') === String(selectedTypeId);
    });

    courseSelect.innerHTML = '<option value="">Select Course</option>' + filteredCourses.map((course) => `
      <option value="${course.id}">${course.name} (${course.duration} month${course.duration === 1 ? '' : 's'})</option>
    `).join('');

    if (filteredCourses.some((course) => String(course.id) === String(oldValue))) {
      courseSelect.value = oldValue;
    }
  }

  function selectedPlanCard() {
    return planCards.find((card) => card.classList.contains('active')) || null;
  }

  function renderCourseSummary() {
    const course = selectedCourse();
    document.getElementById('course_duration_view').value = course ? `${course.duration} month(s)` : '-';
    sideCourseName.textContent = course ? course.name : '-';
    sideCourseDuration.textContent = course ? `${course.duration} month(s)` : '-';
    if (!course) {
      feeBreakup.innerHTML = '<div class="text-sm text-muted">Select a course to view fee details.</div>';
      feeTotal.textContent = 'Total: Rs. 0.00';
      sideCourseTotal.textContent = 'Rs. 0.00';
      sideCourseRequired.textContent = 'Rs. 0.00';
      updatePaymentHelp();
      return;
    }

    feeBreakup.innerHTML = course.fee_items.map(item => `
      <div class="fee-line">
        <span>${item.fee_type_name}${item.is_mandatory ? ' *' : ''}</span>
        <strong>${money(item.amount)}</strong>
      </div>
    `).join('') + `
      <div class="fee-line">
        <span>Required Upfront Fee</span>
        <strong>${money(course.required_fee)}</strong>
      </div>
    `;
    feeTotal.textContent = `Total: ${money(course.total_fee)}`;
    sideCourseTotal.textContent = money(course.total_fee);
    sideCourseRequired.textContent = money(course.required_fee);
    updatePaymentHelp();
  }

  function updatePaymentHelp() {
    const course = selectedCourse();
    const plan = selectedPlanCard();
    if (!course || !plan) {
      paymentHelp.textContent = 'Select course and payment plan to see payment rule.';
      return;
    }

    if (plan.dataset.planType === 'OTP') {
      firstPaymentInput.value = course.total_fee;
      paymentHelp.textContent = `For OTP, the full amount ${money(course.total_fee)} must be collected at the time of admission.`;
      return;
    }

    const requiredFee = Number(course.required_fee || 0);
    const monthlyAmount = course.duration > 0 ? (Number(course.total_fee) / Number(course.duration)) : Number(course.total_fee);
    if (plan.dataset.planType === 'MONTHLY') {
      paymentHelp.textContent = `For MONTHLY, the total fee is ${money(course.total_fee)}. The monthly amount is approximately ${money(monthlyAmount)} and the required upfront fee is ${money(requiredFee)}.`;
      if (Number(firstPaymentInput.value || 0) < requiredFee) {
        firstPaymentInput.value = requiredFee;
      }
      return;
    }

    paymentHelp.textContent = `For PART, the upfront payment cannot be less than ${money(requiredFee)}. The remaining due amount will be tracked in the wallet ledger.`;
    if (Number(firstPaymentInput.value || 0) < requiredFee) {
      firstPaymentInput.value = requiredFee;
    }
  }

  function syncPermanentAddress() {
    if (!sameAddressCheckbox || !sameAddressCheckbox.checked || !presentAddressInput || !permanentAddressInput) {
      return;
    }

    permanentAddressInput.value = presentAddressInput.value;
    if (permanentStateSelect && stateSelect) {
      permanentStateSelect.value = stateSelect.value;
      renderDistrictOptions(permanentDistrictSelect, permanentStateSelect.value, districtSelect?.value || '');
    }
    if (permanentCityInput && cityInput) {
      permanentCityInput.value = cityInput.value;
    }
    if (permanentPinCodeInput && pinCodeInput) {
      permanentPinCodeInput.value = pinCodeInput.value;
    }
  }

  function renderDistrictOptions(targetSelect, stateName, selectedDistrict = '') {
    if (!targetSelect) {
      return;
    }

    const districts = districtsByState[stateName] || [];
    targetSelect.innerHTML = '<option value="">Select District</option>' + districts.map((district) => `
      <option value="${district}">${district}</option>
    `).join('');

    if (selectedDistrict && districts.includes(selectedDistrict)) {
      targetSelect.value = selectedDistrict;
    }
  }

  function syncSteps() {
    steps.forEach((step, index) => step.classList.toggle('active', index === activeStep));
    indicators.forEach((indicator, index) => indicator.classList.toggle('active', index === activeStep));
    prevBtn.style.visibility = activeStep === 0 ? 'hidden' : 'visible';
    nextBtn.style.display = activeStep === steps.length - 1 ? 'none' : '';
    submitBtn.style.display = activeStep === steps.length - 1 ? '' : 'none';
    if (activeStep === reviewStepIndex) {
      buildReview();
    }
  }

  function validateCurrentStep() {
    const current = steps[activeStep];
    const inputs = [...current.querySelectorAll('input, select, textarea')].filter((field) => field.offsetParent !== null);
    for (const input of inputs) {
      if (!input.reportValidity()) {
        return false;
      }
    }
    return true;
  }

  function addEducationRow(row = {}) {
    const index = educationIndex++;
    const div = document.createElement('div');
    div.className = 'edu-row';
    div.innerHTML = `
      <input type="text" class="gt-input" name="education[${index}][examination]" placeholder="Examination" value="${row.examination || ''}">
      <input type="text" class="gt-input" name="education[${index}][institute_name]" placeholder="Institute" value="${row.institute_name || ''}">
      <input type="text" class="gt-input" name="education[${index}][board_university]" placeholder="Board / University" value="${row.board_university || ''}">
      <input type="text" class="gt-input" name="education[${index}][passing_year]" placeholder="Year" value="${row.passing_year || ''}">
      <input type="text" class="gt-input" name="education[${index}][division]" placeholder="Division" value="${row.division || ''}">
      <input type="text" class="gt-input" name="education[${index}][marks_percentage]" placeholder="Percentage" value="${row.marks_percentage || ''}">
      <button type="button" class="btn btn-danger btn-sm remove-edu">×</button>
    `;
    educationRows.appendChild(div);
    div.querySelector('.remove-edu').addEventListener('click', () => div.remove());
  }

  function buildReview() {
    const course = selectedCourse();
    const fd = new FormData(form);
    const plan = selectedPlanCard();
    const reviewValues = {
      course: course ? course.name : '-',
      batch: document.getElementById('batch_id')?.selectedOptions?.[0]?.textContent || 'No Batch',
    };

    document.querySelectorAll('[data-review-field]').forEach((node) => {
      const key = node.dataset.reviewField;
      let value = fd.get(key);
      if (key === 'photo') {
        value = photoInput.files?.[0]?.name || 'Uploaded photo preview shown above';
      }
      node.textContent = value && String(value).trim() ? value : '-';
    });

    document.querySelectorAll('[data-review-meta]').forEach((node) => {
      const key = node.dataset.reviewMeta;
      node.textContent = reviewValues[key] || '-';
    });

    reviewPhotoPreview.src = photoPreview.src;

    const educationEntries = [...educationRows.querySelectorAll('.edu-row')].map((row) => {
      const inputs = row.querySelectorAll('input');
      return {
        examination: inputs[0]?.value || '',
        institute_name: inputs[1]?.value || '',
        board_university: inputs[2]?.value || '',
        passing_year: inputs[3]?.value || '',
        division: inputs[4]?.value || '',
        marks_percentage: inputs[5]?.value || '',
      };
    }).filter((row) => Object.values(row).some((value) => value));

    reviewEducationBody.innerHTML = educationEntries.length
      ? educationEntries.map((row) => `
          <tr>
            <td>${row.examination || '-'}</td>
            <td>${row.institute_name || '-'}</td>
            <td>${row.board_university || '-'}</td>
            <td>${row.passing_year || '-'}</td>
            <td>${row.division || '-'}</td>
            <td>${row.marks_percentage || '-'}</td>
          </tr>
        `).join('')
      : '<tr><td colspan="6">No education details added.</td></tr>';
  }

  nextBtn.addEventListener('click', () => {
    if (!validateCurrentStep()) {
      return;
    }
    if (activeStep < steps.length - 1) {
      activeStep += 1;
      syncSteps();
    }
  });

  prevBtn.addEventListener('click', () => {
    if (activeStep > 0) {
      activeStep -= 1;
      syncSteps();
    }
  });

  addEducationButton?.addEventListener('click', () => addEducationRow());

  courseTypeSelect.addEventListener('change', () => {
    renderCourseOptions();
    renderCourseSummary();
  });
  courseSelect.addEventListener('change', renderCourseSummary);
  planCards.forEach((card) => {
    card.addEventListener('click', () => {
      planCards.forEach((node) => node.classList.remove('active'));
      card.classList.add('active');
      paymentPlanInput.value = card.dataset.planId;
      updatePaymentHelp();
    });
  });

  photoInput.addEventListener('change', (event) => {
    const file = event.target.files?.[0];
    if (!file) {
      return;
    }
    const reader = new FileReader();
    reader.onload = (loadEvent) => {
      photoPreview.src = loadEvent.target.result;
    };
    reader.readAsDataURL(file);
  });

  printReviewButton?.addEventListener('click', () => {
    buildReview();
    window.print();
  });

  sameAddressCheckbox?.addEventListener('change', () => {
    syncPermanentAddress();
  });

  presentAddressInput?.addEventListener('input', () => {
    syncPermanentAddress();
  });

  stateSelect?.addEventListener('change', () => {
    renderDistrictOptions(districtSelect, stateSelect.value);
    syncPermanentAddress();
  });

  districtSelect?.addEventListener('change', () => {
    syncPermanentAddress();
  });

  cityInput?.addEventListener('input', () => {
    syncPermanentAddress();
  });

  pinCodeInput?.addEventListener('input', () => {
    syncPermanentAddress();
  });

  permanentStateSelect?.addEventListener('change', () => {
    renderDistrictOptions(permanentDistrictSelect, permanentStateSelect.value);
  });

  form.addEventListener('submit', (event) => {
    if (!validateCurrentStep()) {
      event.preventDefault();
    }
  });

  const oldEducation = @json(old('education', []));
  if (Array.isArray(oldEducation) && oldEducation.length) {
    oldEducation.forEach((row) => addEducationRow(row));
  }

  const oldPlanId = paymentPlanInput.value;
  if (oldPlanId) {
    const oldCard = planCards.find((card) => String(card.dataset.planId) === String(oldPlanId));
    oldCard?.click();
  }

  const oldCourseId = @json(old('course_id'));
  const oldCourse = courseCatalog.find((course) => String(course.id) === String(oldCourseId));
  if (oldCourse?.course_type_id) {
    courseTypeSelect.value = String(oldCourse.course_type_id);
  }

  renderCourseOptions();
  if (oldCourseId) {
    courseSelect.value = String(oldCourseId);
  }
  renderDistrictOptions(districtSelect, stateSelect?.value || '', @json(old('district')));
  renderDistrictOptions(permanentDistrictSelect, permanentStateSelect?.value || '', @json(old('permanent_district')));
  syncPermanentAddress();
  renderCourseSummary();
  syncSteps();
})();
</script>
@endpush
