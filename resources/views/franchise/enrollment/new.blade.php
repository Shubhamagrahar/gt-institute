@extends('layouts.franchise')
@section('title','New Admission')
@section('page-title','New Admission')
@section('topbar-actions')
  <a href="{{ route('franchise.enrollment.choose') }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@push('styles')
<style>
.adm-shell{max-width:1160px;margin:0 auto}
.adm-header{background:linear-gradient(135deg,#ea580c,#c2410c);color:#fff;border-radius:22px;padding:26px 30px;box-shadow:0 20px 45px rgba(234,88,12,.18)}
.adm-header h2{margin:0;font-size:28px;font-weight:900}
.adm-header p{margin:8px 0 0;opacity:.84}
.adm-wrap{display:block;margin-top:18px}
.adm-card{background:var(--bg-2);border:1px solid var(--border);border-radius:20px;overflow:hidden}
.adm-steps{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;padding:22px;background:rgba(234,88,12,.05)}
.adm-step{padding:12px 8px;border-radius:14px;background:rgba(234,88,12,.1);color:rgba(194,65,12,.7);text-align:center;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
.adm-step.active{background:#ea580c;color:#fff}
.adm-body{padding:24px}
.wizard-step{display:none}
.wizard-step.active{display:block}
.adm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.adm-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.adm-section-title{font-size:20px;font-weight:900;margin:0 0 8px}
.adm-section-note{font-size:13px;color:var(--text-2);margin-bottom:18px}
.fee-box{background:#fff7ed;border:1px solid #fed7aa;border-radius:18px;padding:18px}
.fee-lines{display:flex;flex-direction:column;gap:10px}
.fee-line{display:flex;justify-content:space-between;font-size:13px}
.fee-line strong{font-weight:800}
.fee-total{margin-top:12px;padding-top:12px;border-top:1px solid #fed7aa;font-size:17px;font-weight:900;color:#c2410c}
.photo-box{border:1px dashed rgba(234,88,12,.3);border-radius:18px;padding:18px;background:rgba(234,88,12,.03);text-align:center;display:flex;flex-direction:column;gap:8px;align-self:start}
.photo-box img{width:110px;height:110px;border-radius:50%;object-fit:cover;margin:0 auto 4px;border:3px solid #fff;box-shadow:0 8px 24px rgba(0,0,0,.08)}
.basic-layout{display:grid;grid-template-columns:280px minmax(0,1fr);gap:20px;align-items:start}
.basic-side-stack{display:grid;gap:14px}
.basic-fields{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.inline-fee-panel{margin-top:18px;background:#fff7ed;border:1px solid #fed7aa;border-radius:18px;padding:18px}
.inline-fee-title{font-size:12px;font-weight:800;color:#9a3412;letter-spacing:.08em;text-transform:uppercase;margin-bottom:12px}
.photo-upload-input{margin-top:4px}
.basic-side-card{border:1px solid #fed7aa;border-radius:18px;padding:14px;background:#fff7ed}
.basic-side-title{font-size:12px;font-weight:800;color:#9a3412;letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px}
.basic-side-line{display:flex;justify-content:space-between;gap:12px;padding:7px 0;border-bottom:1px solid rgba(148,163,184,.18);font-size:13px}
.basic-side-line:last-child{border-bottom:none}
.basic-side-line strong{font-weight:800;color:#ea580c}
.address-sections{display:grid;gap:18px}
.address-card{border:1px solid var(--border);border-radius:18px;padding:18px;background:var(--bg-3)}
.address-card-title{font-size:15px;font-weight:900;margin:0 0 4px}
.address-card-note{font-size:12px;color:var(--text-2);margin:0 0 14px}
.address-copy-row{display:flex;align-items:center;gap:10px;padding:0 4px;color:var(--text-2);font-size:13px;font-weight:700}
.address-copy-row input{width:16px;height:16px;accent-color:#ea580c}
.address-full-width{grid-column:1/-1}
.edu-toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.edu-row{display:grid;grid-template-columns:repeat(6,minmax(0,1fr)) 44px;gap:10px;padding:12px;border:1px solid var(--border);border-radius:14px;background:var(--bg-3);margin-bottom:10px}
.review-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px}
/* A4 Print Preview */
.review-print-shell{width:210mm;max-width:100%;min-height:277mm;margin:0 auto;background:#fff;color:#000;padding:8mm 10mm;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;font-size:10px;display:flex;flex-direction:column}
.review-print-shell *{color:#000 !important;text-decoration:none !important}
.rf-inst-header{display:flex;align-items:flex-start;gap:12px;padding-bottom:8px;border-bottom:2.5px solid #000}
.rf-inst-logo img{height:62px;width:62px;object-fit:contain;display:block}
.rf-inst-name{font-size:22px;font-weight:900;line-height:1.15}
.rf-inst-contact{font-size:9.5px;margin-top:4px;line-height:1.5}
.rf-inst-addr{font-size:8.5px;margin-top:2px;line-height:1.4}
.rf-form-title{text-align:center;font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:.16em;border-bottom:2px solid #000;padding:7px 0;margin:0}
.rf-top-row{display:flex;border:1.5px solid #000;margin-top:0;flex-shrink:0}
.rf-photo-cell{width:32mm;min-width:32mm;flex-shrink:0;border-right:1.5px solid #000;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:10px 5px 8px}
.rf-photo-box{width:27mm;height:35mm;border:1.5px solid #000;overflow:hidden;background:#fff;flex-shrink:0;display:flex;align-items:stretch}
.rf-photo-box img{width:100%;height:100%;object-fit:cover;display:block}
.rf-photo-lbl{font-size:7.5px;text-align:center;margin-top:5px;line-height:1.3}
.rf-top-table{width:100%;border-collapse:collapse}
.rf-top-table td{border:1px solid #ccc;padding:5px 7px;vertical-align:middle}
.rf-top-table tr:first-child td{border-top:none}
.rf-top-table tr:last-child td{border-bottom:none}
.rf-top-table td:first-child{border-left:none}
.rf-top-table td:last-child{border-right:none}
.rf-top-table td.rf-lbl{font-size:8px;font-weight:700;text-transform:uppercase;white-space:nowrap;width:20%;background:#fafafa}
.rf-top-table td.rf-val{font-size:10.5px;font-weight:600;width:30%}
.rf-section-head{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.07em;padding:5px 0 3px;border-top:2px solid #000;border-bottom:1px solid #bbb;margin-top:10px}
.rf-table{width:100%;border-collapse:collapse}
.rf-table td{border:1px solid #ccc;padding:4px 7px;vertical-align:top}
.rf-table td.rf-lbl{font-size:8px;font-weight:700;text-transform:uppercase;width:22%;white-space:nowrap;background:#fafafa}
.rf-table td.rf-val{font-size:10px;width:28%}
.review-edu-table{width:100%;border-collapse:collapse}
.review-edu-table th,.review-edu-table td{border:1px solid #ccc;padding:4px 7px;font-size:9px;text-align:left}
.review-edu-table th{font-weight:700;font-size:8.5px;text-transform:uppercase;background:#fafafa}
.rf-footer{margin-top:auto;padding-top:10px}
.rf-declaration{padding:6px 9px;border:1px solid #bbb;font-size:8.5px;line-height:1.6;margin-bottom:0}
.rf-auth-sign{width:200px;text-align:center}
.rf-auth-line{border-top:1.5px solid #000;margin:6px 0 4px}
.rf-auth-label{font-size:10px;font-weight:700}
.rf-auth-sub{font-size:9px}
.plan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.wizard-actions{display:flex;justify-content:space-between;gap:12px;margin-top:22px}
.pay-note{margin-top:12px;padding:14px;border-radius:14px;background:#fff7ed;color:#9a3412;font-size:13px;line-height:1.5}
@media print{
  @page{size:A4 portrait;margin:7mm}
  html,body{margin:0;padding:0;background:#fff}
  .gt-sidebar,.gt-topbar,.gt-overlay,.gt-alert{display:none !important}
  .gt-layout{display:block !important}
  .gt-main{display:block !important;margin:0 !important;padding:0 !important}
  .gt-page{padding:0 !important;margin:0 !important}
  .adm-header,.adm-steps,.wizard-actions,.review-toolbar,.adm-section-title,.adm-section-note{display:none !important}
  .wizard-step{display:none !important}
  #review-wizard-step{display:block !important}
  .review-print-shell{width:auto;min-height:auto;padding:0;box-shadow:none;font-size:9px}
}
@media(max-width:1080px){.basic-layout{grid-template-columns:1fr}}
@media(max-width:760px){.adm-steps{grid-template-columns:repeat(2,1fr)}.adm-grid,.adm-grid-3,.basic-fields,.edu-row{grid-template-columns:1fr}}
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
          'field_key'    => $key,
          'field_label'  => $saved?->field_label ?? $definition['label'],
          'field_type'   => $saved?->field_type ?? $definition['type'],
          'options'      => $saved?->options ?? ($definition['options'] ?? null),
          'is_active'    => $saved ? (bool) $saved->is_active : true,
          'is_required'  => $saved ? (bool) $saved->is_required : false,
      ];
  };
  $educationField   = $resolveField('education_details');
  $educationEnabled = (bool)($educationField?->is_active);
  $basicKeys = ['email','dob','gender','whatsapp_no','alternate_mobile','category','religion','nationality','qualification','employment_status','computer_literacy','blood_group','aadhar_no','pan_no'];
  $religionOptions  = ['Hindu','Muslim','Sikh','Christian','Jain','Buddhist','Other'];
  $nationalityOptions = ['Indian','NRI','Other'];
  $guardianKeys = ['father_name','mother_name','guardian_name','guardian_relation','guardian_mobile','guardian_occupation'];
  $presentAddressField   = $resolveField('address');
  $permanentAddressField = $resolveField('permanent_address');
  $stateField    = $resolveField('state');
  $districtField = $resolveField('district');
  $pinCodeField  = $resolveField('pin_code');
  $reviewSections = [
      ['title'=>'Student Details','keys'=>['email','dob','gender','whatsapp_no','alternate_mobile','aadhar_no','pan_no','blood_group','category','religion','nationality','employment_status','computer_literacy','qualification','photo']],
      ['title'=>'Address Details','keys'=>['address','state','district','city','pin_code','permanent_address','permanent_state','permanent_district','permanent_city','permanent_pin_code']],
      ['title'=>'Guardian Details','keys'=>['father_name','mother_name','guardian_name','guardian_relation','guardian_mobile','guardian_occupation']],
      ['title'=>'Education Details','keys'=>['education_details']],
  ];
  $reviewWideFields = ['address','permanent_address','education_details','photo'];
  $institute  = auth('institute')->user()->institute;
  $franchise  = auth('institute')->user()->franchise;
  $stepLabels = $educationEnabled
      ? ['Course','Basic','Address','Education','Review','Confirm']
      : ['Course','Basic','Address','Review','Confirm'];
  $errorFields = $errors->keys();
  $initialStep = 0;
  $stepErrorMap = [
      0 => ['course_id','batch_id','admission_source'],
      1 => ['name','mobile','email','photo','father_name','mother_name','guardian_name','guardian_relation','guardian_mobile','guardian_occupation','dob','gender','category','religion','nationality','whatsapp_no','alternate_mobile','aadhar_no','pan_no','blood_group','employment_status','computer_literacy','qualification'],
      2 => ['address','permanent_address','state','district','city','pin_code','permanent_state','permanent_district','permanent_city','permanent_pin_code'],
      3 => ['education'],
  ];
  foreach ($stepErrorMap as $stepIndex => $fieldsInStep) {
      foreach ($errorFields as $errorField) {
          foreach ($fieldsInStep as $prefix) {
              if ($prefix==='education' ? str_starts_with($errorField,'education.') : $errorField===$prefix) {
                  $initialStep = max($initialStep, $stepIndex); break 3;
              }
          }
      }
  }
@endphp

<div class="adm-shell">
  <div class="adm-header">
    <h2>Student Admission Wizard</h2>
    <p>This flow saves the seat booking first. Final admission becomes active only after the required payment and complete details are received.</p>
  </div>

  <form method="POST" action="{{ route('franchise.enrollment.store-new') }}" enctype="multipart/form-data" id="admission-form" autocomplete="off">
    @csrf
    <input type="text" name="fake_u" value="" autocomplete="username" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
    <input type="password" name="fake_p" value="" autocomplete="new-password" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">

    <div class="adm-wrap">
      <div class="adm-card">
        <div class="adm-steps">
          @foreach($stepLabels as $index => $step)
            <div class="adm-step {{ $index===0 ? 'active' : '' }}" data-indicator>{{ $step }}</div>
          @endforeach
        </div>

        <div class="adm-body">

          {{-- ══ STEP 1: COURSE ══ --}}
          <div class="wizard-step active" data-step>
            <div class="adm-section-title">Course Setup</div>
            <div class="adm-section-note">Select a course type first. The course list will then show matching courses with their duration and fee breakup.</div>
            <div class="adm-grid">
              <div class="gt-form-group">
                <label class="gt-label">Course Type <span style="color:var(--danger)">*</span></label>
                <select id="course_type_id" class="gt-select" required>
                  <option value="">Select Course Type</option>
                  @foreach($courseTypes as $ct)
                    <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Duration <span style="color:var(--danger)">*</span></label>
                <select id="course_duration_filter" class="gt-select" required>
                  <option value="">Select Duration</option>
                </select>
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
                <select name="course_id" id="course_id" class="gt-select" required style="display:none;">
                  <option value="">Select Course</option>
                </select>
                <div style="position:relative;">
                  <input type="text" id="course_search_display" class="gt-select"
                    placeholder="Search &amp; select course…" autocomplete="off" style="width:100%;cursor:pointer;">
                  <div id="course_search_dropdown" style="display:none;position:absolute;z-index:200;width:100%;top:calc(100% + 3px);background:var(--bg);border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>
                </div>
                @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Batch</label>
                <select name="batch_id" id="batch_id" class="gt-select">
                  <option value="">No Batch</option>
                  @foreach($batches as $b)
                    <option value="{{ $b->id }}" {{ old('batch_id')==$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Admission Source <span style="color:var(--danger)">*</span></label>
                <select name="admission_source" id="admission_source" class="gt-select" required>
                  <option value="direct" {{ old('admission_source','direct')==='direct' ? 'selected':'' }}>Direct</option>
                  <option value="channel_partner" {{ old('admission_source')==='channel_partner' ? 'selected':'' }}>Channel Partner</option>
                </select>
                @error('admission_source')<div class="gt-error">{{ $message }}</div>@enderror
              </div>
              @if($channelPartners->count())
              <div class="gt-form-group" id="channel_partner_group" style="display:none;">
                <label class="gt-label">Channel Partner</label>
                <select name="channel_partner_id" id="channel_partner_id" class="gt-select">
                  <option value="">Select</option>
                  @foreach($channelPartners as $cp)
                    <option value="{{ $cp->id }}" {{ old('channel_partner_id')==$cp->id ? 'selected':'' }}>{{ $cp->name }} ({{ $cp->mobile }})</option>
                  @endforeach
                </select>
              </div>
              @endif
            </div>

            <div class="inline-fee-panel">
              <div class="inline-fee-title">Fee Preview</div>
              <div class="fee-lines" id="fee-breakup" style="margin-top:12px;">
                <div class="text-sm text-muted">Select a course to view fee details.</div>
              </div>
              <div class="fee-total" id="fee-total">Total: ₹0.00</div>
              <div class="pay-note" style="margin-top:16px;">
                The base course fee and all enabled fee types are shown here. The student will pay the combined total during admission.
              </div>
            </div>
          </div>

          {{-- ══ STEP 2: BASIC & GUARDIAN ══ --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Basic and Guardian Details</div>
            <div class="adm-section-note">Complete the student profile, guardian details, and profile photo in this step.</div>
            <div class="basic-layout">
              <div class="basic-side-stack">
                <div class="photo-box">
                  <img src="{{ asset($defaultPhotoPath) }}" id="photo_preview" alt="Student photo">
                  <div class="text-sm fw-700">Student Photo</div>
                  <div class="text-xs text-muted">Upload a clear passport-size photo.</div>
                  <input type="file" name="photo" id="photo" class="gt-input photo-upload-input" accept="image/*">
                  @error('photo')<div class="gt-error">{{ $message }}</div>@enderror
                </div>
                <div class="basic-side-card">
                  <div class="basic-side-title">Admission Summary</div>
                  <div class="basic-side-line"><span>Course</span><strong id="side-course-name">-</strong></div>
                  <div class="basic-side-line"><span>Duration</span><strong id="side-course-duration">-</strong></div>
                  <div class="basic-side-line"><span>Total Fee</span><strong id="side-course-total">₹0.00</strong></div>
                  <div class="basic-side-line"><span>Upfront Fee</span><strong id="side-course-required">₹0.00</strong></div>
                </div>
              </div>

              <div class="basic-fields">
                <div class="gt-form-group">
                  <label class="gt-label">Student Name <span style="color:var(--danger)">*</span></label>
                  <input type="text" name="name" class="gt-input" value="{{ old('name') }}" autocomplete="off" required>
                  @error('name')<div class="gt-error">{{ $message }}</div>@enderror
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
                  <input type="tel" name="mobile" id="mobile" class="gt-input"
                    value="{{ old('mobile') }}" autocomplete="off" required
                    maxlength="10" inputmode="numeric" pattern="[0-9]{10}"
                    oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                  <div class="gt-field-error" id="mobile-error" style="display:none;color:var(--danger);font-size:12px;margin-top:3px;"></div>
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
                    @if(in_array($key,['religion','nationality'],true))
                      @php $opts = $key==='religion' ? collect($religionOptions) : collect($nationalityOptions); @endphp
                      <select name="{{ $key }}" class="gt-select" {{ $field->is_required ? 'required':'' }}>
                        <option value="">Select</option>
                        @foreach($opts as $opt)
                          <option value="{{ $opt }}" {{ old($key)===$opt ? 'selected':'' }}>{{ $opt }}</option>
                        @endforeach
                      </select>
                    @elseif($field->field_type==='select')
                      @php
                        $opts = collect(explode(',',$field->options??''))->map(fn($o)=>trim($o))->filter();
                        if ($key==='gender') $opts=collect(['Male','Female','Other']);
                      @endphp
                      <select name="{{ $key }}" class="gt-select" {{ $field->is_required ? 'required':'' }}>
                        <option value="">Select</option>
                        @foreach($opts as $opt)
                          <option value="{{ $opt }}" {{ old($key)===$opt ? 'selected':'' }}>{{ $opt }}</option>
                        @endforeach
                      </select>
                    @elseif($field->field_type==='textarea')
                      <textarea name="{{ $key }}" class="gt-textarea" {{ $field->is_required ? 'required':'' }}>{{ old($key) }}</textarea>
                    @else
                      <input type="{{ in_array($field->field_type,['email','date','number'],true) ? $field->field_type : 'text' }}"
                        name="{{ $key }}" class="gt-input" value="{{ old($key) }}"
                        {{ $field->is_required ? 'required':'' }}>
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
                    @if($field->field_type==='select')
                      @php $opts=collect(explode(',',$field->options??''))->map(fn($o)=>trim($o))->filter(); @endphp
                      <select name="{{ $key }}" class="gt-select" {{ $field->is_required ? 'required':'' }}>
                        <option value="">Select</option>
                        @foreach($opts as $opt)
                          <option value="{{ $opt }}" {{ old($key)===$opt ? 'selected':'' }}>{{ $opt }}</option>
                        @endforeach
                      </select>
                    @elseif($field->field_type==='textarea')
                      <textarea name="{{ $key }}" class="gt-textarea" {{ $field->is_required ? 'required':'' }}>{{ old($key) }}</textarea>
                    @else
                      <input type="{{ in_array($field->field_type,['email','date','number'],true) ? $field->field_type : 'text' }}"
                        name="{{ $key }}" class="gt-input" value="{{ old($key) }}"
                        {{ $field->is_required ? 'required':'' }}>
                    @endif
                    @error($key)<div class="gt-error">{{ $message }}</div>@enderror
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          {{-- ══ STEP 3: ADDRESS ══ --}}
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
                      <label class="gt-label">{{ $presentAddressField->field_label }}@if($presentAddressField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <textarea name="address" id="address" class="gt-textarea" {{ $presentAddressField->is_required ? 'required':'' }}>{{ old('address') }}</textarea>
                      @error('address')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif
                  @if($stateField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">{{ $stateField->field_label }}@if($stateField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <select name="state" id="state" class="gt-select" {{ $stateField->is_required ? 'required':'' }}>
                        <option value="">Select State</option>
                        @foreach($states as $s)
                          <option value="{{ $s }}" {{ old('state')===$s ? 'selected':'' }}>{{ $s }}</option>
                        @endforeach
                      </select>
                      @error('state')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif
                  @if($districtField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">{{ $districtField->field_label }}@if($districtField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <select name="district" id="district" class="gt-select" {{ $districtField->is_required ? 'required':'' }}>
                        <option value="">Select District</option>
                      </select>
                      @error('district')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif
                  <div class="gt-form-group">
                    <label class="gt-label">City</label>
                    <input type="text" name="city" id="city" class="gt-input" value="{{ old('city') }}">
                  </div>
                  @if($pinCodeField?->is_active)
                    <div class="gt-form-group">
                      <label class="gt-label">{{ $pinCodeField->field_label }}@if($pinCodeField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <input type="text" name="pin_code" id="pin_code" class="gt-input" value="{{ old('pin_code') }}" {{ $pinCodeField->is_required ? 'required':'' }}>
                      @error('pin_code')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                  @endif
                </div>
              </div>

              @if($presentAddressField?->is_active && $permanentAddressField?->is_active)
                <label class="address-copy-row" for="same_as_present_address">
                  <input type="checkbox" id="same_as_present_address" {{ old('address') && old('address')===old('permanent_address') ? 'checked':'' }}>
                  <span>Permanent address is the same as the present address.</span>
                </label>
              @endif

              @if($permanentAddressField?->is_active)
                <div class="address-card">
                  <h4 class="address-card-title">Permanent Address</h4>
                  <p class="address-card-note">Store the permanent address separately for documents and communication.</p>
                  <div class="adm-grid">
                    <div class="gt-form-group address-full-width">
                      <label class="gt-label">{{ $permanentAddressField->field_label }}@if($permanentAddressField->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                      <textarea name="permanent_address" id="permanent_address" class="gt-textarea" {{ $permanentAddressField->is_required ? 'required':'' }}>{{ old('permanent_address') }}</textarea>
                      @error('permanent_address')<div class="gt-error">{{ $message }}</div>@enderror
                    </div>
                    @if($stateField?->is_active)
                      <div class="gt-form-group">
                        <label class="gt-label">Permanent State</label>
                        <select name="permanent_state" id="permanent_state" class="gt-select">
                          <option value="">Select State</option>
                          @foreach($states as $s)
                            <option value="{{ $s }}" {{ old('permanent_state')===$s ? 'selected':'' }}>{{ $s }}</option>
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
                      <input type="text" name="permanent_city" id="permanent_city" class="gt-input" value="{{ old('permanent_city') }}">
                    </div>
                    @if($pinCodeField?->is_active)
                      <div class="gt-form-group">
                        <label class="gt-label">Permanent PIN Code</label>
                        <input type="text" name="permanent_pin_code" id="permanent_pin_code" class="gt-input" value="{{ old('permanent_pin_code') }}">
                      </div>
                    @endif
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- ══ STEP 4: EDUCATION (optional) ══ --}}
          @if($educationEnabled)
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Education Details</div>
            <div class="adm-section-note">No education row is added by default. Use "+ Add Row" to add academic records.</div>
            <div class="edu-toolbar">
              <div class="text-sm fw-700">Education Table</div>
              <button type="button" class="btn btn-primary btn-sm" id="add-education-row" style="background:#ea580c;border-color:#ea580c;">+ Add Row</button>
            </div>
            <div id="education-rows"></div>
            @error('education')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
          @endif

          {{-- ══ REVIEW STEP ══ --}}
          <div class="wizard-step" data-step id="review-wizard-step">
            <div class="adm-section-title">Confirm and Review</div>
            <div class="adm-section-note">Review the complete admission form before printing or saving.</div>
            <div class="review-toolbar">
              <div class="text-sm text-muted">This is the printable A4 admission form.</div>
              <button type="button" class="btn btn-outline" id="print-review">Print Form</button>
            </div>

            <div class="review-print-shell" id="review-print-area">

              {{-- Franchise Header --}}
              <div class="rf-inst-header">
                @php $frLogo = $franchise?->logo; @endphp
                @if($frLogo && !in_array(trim($frLogo), ['', 'images/default-franchise.png', 'images/default-institute.png']))
                  <div class="rf-inst-logo"><img src="{{ asset($frLogo) }}" alt="logo"></div>
                @endif
                <div style="flex:1;">
                  <div class="rf-inst-name">{{ $franchise?->name ?? $institute?->name ?? 'Franchise Name' }}</div>
                  <div class="rf-inst-contact">
                    @if($franchise?->mobile)Ph: {{ $franchise->mobile }}@endif
                    @if($franchise?->email)&nbsp;&nbsp;|&nbsp;&nbsp;{{ $franchise->email }}@endif
                    @if($franchise?->website)&nbsp;&nbsp;|&nbsp;&nbsp;{{ $franchise->website }}@endif
                  </div>
                  <div class="rf-inst-addr">{{ $franchise?->address ?? '' }}</div>
                </div>
              </div>

              <div class="rf-form-title">Admission Application Form</div>

              {{-- Photo + info --}}
              <div class="rf-top-row">
                <div class="rf-photo-cell">
                  <div class="rf-photo-box">
                    <img id="review_photo_preview" alt="Photo" style="width:100%;height:100%;object-fit:cover;display:none;">
                    <div id="review-photo-placeholder" style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:#999;">
                      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                      <span style="font-size:7px;text-align:center;color:#bbb;line-height:1.3;">Affix Passport<br>Size Photo</span>
                    </div>
                  </div>
                  <div class="rf-photo-lbl">Passport Size<br>Photograph</div>
                </div>
                <div style="flex:1;">
                  <table class="rf-top-table">
                    <tr>
                      <td class="rf-lbl">Full Name</td><td class="rf-val" data-review-field="name">-</td>
                      <td class="rf-lbl">Date of Birth</td><td class="rf-val" data-review-field="dob">-</td>
                    </tr>
                    <tr>
                      <td class="rf-lbl">Mobile</td><td class="rf-val" data-review-field="mobile">-</td>
                      <td class="rf-lbl">Gender</td><td class="rf-val" data-review-field="gender">-</td>
                    </tr>
                    <tr>
                      <td class="rf-lbl">Email</td><td class="rf-val" data-review-field="email">-</td>
                      <td class="rf-lbl">Category</td><td class="rf-val" data-review-field="category">-</td>
                    </tr>
                    <tr>
                      <td class="rf-lbl">Course</td><td class="rf-val" colspan="3" data-review-meta="course">-</td>
                    </tr>
                    <tr>
                      <td class="rf-lbl">Batch</td><td class="rf-val" data-review-meta="batch">-</td>
                      <td class="rf-lbl">Enrolment No.</td><td class="rf-val" style="font-style:italic;font-size:9px;">Issued after admission</td>
                    </tr>
                  </table>
                </div>
              </div>

              {{-- Dynamic sections --}}
              @foreach($reviewSections as $section)
                @if($section['title']==='Education Details' && !$educationEnabled) @continue @endif
                @php
                  $alwaysShow = ['city','permanent_state','permanent_district','permanent_city','permanent_pin_code'];
                  $activeKeys = [];
                  foreach ($section['keys'] as $k) {
                      if ($k==='education_details') { $activeKeys[]=$k; continue; }
                      $f=$resolveField($k);
                      if (in_array($k,$alwaysShow,true)||$f?->is_active) $activeKeys[]=$k;
                  }
                @endphp
                @if(empty($activeKeys)) @continue @endif
                <div class="rf-section-head">{{ $section['title'] }}</div>
                <table class="rf-table">
                  @php $buf=null; @endphp
                  @foreach($activeKeys as $k)
                    @if($k==='education_details')
                      @if($buf!==null)
                        @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title(); @endphp
                        <tr><td class="rf-lbl">{{$bL}}</td><td class="rf-val" data-review-field="{{$buf}}" colspan="3">-</td></tr>
                        @php $buf=null; @endphp
                      @endif
                      <tr><td colspan="4" style="padding:0;">
                        <table class="review-edu-table">
                          <thead><tr><th>Examination</th><th>Institute / School</th><th>Board / University</th><th>Year</th><th>Division</th><th>%</th></tr></thead>
                          <tbody id="review-education-body"><tr><td colspan="6" style="text-align:center;">No education details added.</td></tr></tbody>
                        </table>
                      </td></tr>
                      @continue
                    @endif
                    @php
                      $field=$resolveField($k);
                      $isWide=in_array($k,$reviewWideFields,true);
                      $lbl=$field?->field_label??\Illuminate\Support\Str::of($k)->replace('_',' ')->title();
                    @endphp
                    @if($isWide)
                      @if($buf!==null)
                        @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title(); @endphp
                        <tr><td class="rf-lbl">{{$bL}}</td><td class="rf-val" data-review-field="{{$buf}}" colspan="3">-</td></tr>
                        @php $buf=null; @endphp
                      @endif
                      <tr><td class="rf-lbl">{{ $lbl }}</td><td class="rf-val" colspan="3" data-review-field="{{ $k }}">-</td></tr>
                    @elseif($buf===null)
                      @php $buf=$k; @endphp
                    @else
                      @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title(); @endphp
                      <tr>
                        <td class="rf-lbl">{{ $bL }}</td><td class="rf-val" data-review-field="{{ $buf }}">-</td>
                        <td class="rf-lbl">{{ $lbl }}</td><td class="rf-val" data-review-field="{{ $k }}">-</td>
                      </tr>
                      @php $buf=null; @endphp
                    @endif
                  @endforeach
                  @if($buf!==null)
                    @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title(); @endphp
                    <tr><td class="rf-lbl">{{$bL}}</td><td class="rf-val" data-review-field="{{$buf}}" colspan="3">-</td></tr>
                  @endif
                </table>
              @endforeach

              {{-- Footer --}}
              <div class="rf-footer">
                <div class="rf-declaration">
                  I hereby declare that all the information provided above is true and correct to the best of my knowledge and belief.
                  I agree to abide by the rules, regulations and fee payment schedule of the institute.
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                  <div class="rf-auth-sign">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px;min-height:60px;justify-content:flex-end;">
                      @if($institute?->use_stamp && $institute?->stamp)
                        <img src="{{ asset($institute->stamp) }}" alt="stamp" style="height:58px;width:58px;object-fit:contain;display:block;">
                      @endif
                      @if($institute?->use_signature && $institute?->signature)
                        <img src="{{ asset($institute->signature) }}" alt="signature" style="height:36px;max-width:130px;object-fit:contain;display:block;">
                      @else
                        @if(!($institute?->use_stamp && $institute?->stamp))
                          <div style="height:60px;"></div>
                        @endif
                      @endif
                    </div>
                    <div class="rf-auth-line"></div>
                    <div class="rf-auth-label">For {{ $franchise?->name ?? $institute?->name ?? 'Franchise' }}</div>
                    <div class="rf-auth-sub">Auth. Signatory</div>
                  </div>
                </div>
              </div>
            </div>{{-- /review-print-shell --}}
          </div>

          {{-- ══ FINAL CONFIRM ══ --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Final Confirmation</div>
            <div class="adm-section-note">No payment is collected at seat-booking time. Review everything once and save the seat booking. Payment will happen later from the pending admission list.</div>
            <div class="fee-box">
              <div class="fee-lines">
                <div class="fee-line"><span>Seat booking status</span><strong>Pending admission</strong></div>
                <div class="fee-line"><span>Enrollment number</span><strong>Generated after final admission</strong></div>
                <div class="fee-line"><span>Payment</span><strong>Not required now</strong></div>
              </div>
              <div class="pay-note" style="margin-top:16px;">
                This step just confirms the seat booking. If any detail is wrong, go back and correct it before saving.
              </div>
            </div>
          </div>

          <div class="wizard-actions">
            <button type="button" class="btn btn-outline" id="prev-step" style="visibility:hidden;">Previous</button>
            <div style="display:flex;gap:10px;">
              <button type="button" class="btn btn-primary" id="next-step" style="background:#ea580c;border-color:#ea580c;">Next</button>
              <button type="submit" class="btn btn-primary" id="submit-step" style="display:none;background:#ea580c;border-color:#ea580c;">Save Seat Booking</button>
            </div>
          </div>

        </div>{{-- /.adm-body --}}
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const steps      = [...document.querySelectorAll('[data-step]')];
  const indicators = [...document.querySelectorAll('[data-indicator]')];
  const nextBtn    = document.getElementById('next-step');
  const prevBtn    = document.getElementById('prev-step');
  const submitBtn  = document.getElementById('submit-step');
  const form       = document.getElementById('admission-form');

  const courseTypeSelect  = document.getElementById('course_type_id');
  const durationSelect    = document.getElementById('course_duration_filter');
  const courseSelect      = document.getElementById('course_id');
  const admSourceSelect   = document.getElementById('admission_source');
  const cpGroup           = document.getElementById('channel_partner_group');
  const cpSelect          = document.getElementById('channel_partner_id');
  const courseCatalog     = @json($courseCatalog);
  const districtsByState  = @json($districtsByState ?? []);
  const photoInput        = document.getElementById('photo');
  const photoPreview      = document.getElementById('photo_preview');
  const presentAddr       = document.getElementById('address');
  const permAddr          = document.getElementById('permanent_address');
  const sameAddrChk       = document.getElementById('same_as_present_address');
  const stateSelect       = document.getElementById('state');
  const districtSelect    = document.getElementById('district');
  const cityInput         = document.getElementById('city');
  const pinInput          = document.getElementById('pin_code');
  const permStateSelect   = document.getElementById('permanent_state');
  const permDistrictSelect= document.getElementById('permanent_district');
  const permCityInput     = document.getElementById('permanent_city');
  const permPinInput      = document.getElementById('permanent_pin_code');
  const educationRows     = document.getElementById('education-rows');
  const addEduBtn         = document.getElementById('add-education-row');
  const feeBreakup        = document.getElementById('fee-breakup');
  const feeTotal          = document.getElementById('fee-total');
  const reviewStepIndex   = steps.findIndex(s => s.querySelector('#review-print-area'));
  const reviewEduBody     = document.getElementById('review-education-body');
  const reviewPhotoPreview= document.getElementById('review_photo_preview');
  const printBtn          = document.getElementById('print-review');
  const sideCourseName    = document.getElementById('side-course-name');
  const sideCourseDur     = document.getElementById('side-course-duration');
  const sideCourseTotal   = document.getElementById('side-course-total');
  const sideCourseReq     = document.getElementById('side-course-required');
  const mobileInput       = document.querySelector('input[name="mobile"]');
  const emailInput        = document.querySelector('input[name="email"]');
  const validateUrl       = @json(route('franchise.enrollment.validate-field'));
  const initialStep       = Number(@json($initialStep));
  let activeStep = Number.isFinite(initialStep) ? initialStep : 0;
  let eduIndex   = 0;
  const uniqueTimers = new Map();

  function money(v){ return '₹' + Number(v||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}); }

  function selectedCourse(){ return courseCatalog.find(c=>String(c.id)===String(courseSelect.value))||null; }

  function renderDurations(){
    const tid=courseTypeSelect.value;
    const durs=[...new Set(courseCatalog.filter(c=>String(c.course_type_id)===String(tid)).map(c=>Number(c.duration||0)).filter(d=>d>0))].sort((a,b)=>a-b);
    const old=durationSelect.value;
    durationSelect.innerHTML='<option value="">Select Duration</option>'+durs.map(d=>`<option value="${d}">${d} month${d===1?'':'s'}</option>`).join('');
    if (durs.some(d=>String(d)===String(old))) durationSelect.value=String(old);
  }

  const crsDisp = document.getElementById('course_search_display');
  const crsDrop = document.getElementById('course_search_dropdown');
  let coursePool=[];

  function renderCourseDropdown(q=''){
    const filtered=coursePool.filter(c=>!q||c.name.toLowerCase().includes(q.toLowerCase()));
    crsDrop.innerHTML='';
    if (!filtered.length){
      crsDrop.innerHTML='<div style="padding:10px 14px;font-size:12px;color:var(--text-2);font-style:italic;">No courses found</div>';
    } else {
      filtered.forEach(c=>{
        const d=document.createElement('div');
        d.style.cssText='padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);';
        d.textContent=`${c.name} (${c.duration}m)`;
        d.addEventListener('mouseover',()=>d.style.background='var(--accent-bg)');
        d.addEventListener('mouseout',()=>d.style.background='');
        d.addEventListener('mousedown',e=>{
          e.preventDefault();
          courseSelect.value=c.id; crsDisp.value=c.name;
          crsDrop.style.display='none';
          renderCourseSummary(); updateSidePanel();
        });
        crsDrop.appendChild(d);
      });
    }
    crsDrop.style.display='block';
  }

  if (crsDisp){
    crsDisp.addEventListener('focus',()=>renderCourseDropdown(crsDisp.value));
    crsDisp.addEventListener('input',()=>{ courseSelect.value=''; renderCourseDropdown(crsDisp.value); });
    crsDisp.addEventListener('blur',()=>setTimeout(()=>crsDrop.style.display='none',150));
    document.addEventListener('click',e=>{ if(!crsDrop.contains(e.target)&&e.target!==crsDisp) crsDrop.style.display='none'; });
  }

  function renderCourses(){
    const tid=courseTypeSelect.value, dur=durationSelect.value;
    const old=courseSelect.value;
    const filtered=courseCatalog.filter(c=>String(c.course_type_id)===String(tid)&&String(c.duration)===String(dur));
    coursePool=filtered;
    courseSelect.innerHTML='<option value="">Select Course</option>'+filtered.map(c=>`<option value="${c.id}">${c.name} (${c.duration}m)</option>`).join('');
    if (crsDisp){ crsDisp.value=''; crsDrop.style.display='none'; }
    if (filtered.some(c=>String(c.id)===String(old))){
      courseSelect.value=old;
      const f=filtered.find(c=>String(c.id)===String(old));
      if(f&&crsDisp) crsDisp.value=f.name;
    }
  }

  function renderCourseSummary(){
    const c=selectedCourse();
    if(!c){
      feeBreakup.innerHTML='<div class="text-sm text-muted">Select a course to view fee details.</div>';
      feeTotal.textContent='Total: ₹0.00';
      updateSidePanel(); return;
    }
    feeBreakup.innerHTML=c.fee_items.map(i=>`<div class="fee-line"><span>${i.fee_type_name}${i.is_mandatory?' *':''}</span><strong>${money(i.amount)}</strong></div>`).join('')
      +`<div class="fee-line"><span>Required Upfront Fee</span><strong>${money(c.required_fee)}</strong></div>`;
    feeTotal.textContent=`Total: ${money(c.total_fee)}`;
    updateSidePanel();
  }

  function updateSidePanel(){
    const c=selectedCourse();
    if(sideCourseName) sideCourseName.textContent=c?c.name:'-';
    if(sideCourseDur)  sideCourseDur.textContent=c?`${c.duration} month(s)`:'-';
    if(sideCourseTotal) sideCourseTotal.textContent=c?money(c.total_fee):'₹0.00';
    if(sideCourseReq)   sideCourseReq.textContent=c?money(c.required_fee):'₹0.00';
  }

  function renderDistricts(targetSel, state, selected=''){
    if(!targetSel) return;
    const dists=districtsByState[state]||[];
    targetSel.innerHTML='<option value="">Select District</option>'+dists.map(d=>`<option value="${d}">${d}</option>`).join('');
    if(selected&&dists.includes(selected)) targetSel.value=selected;
  }

  function syncPermAddr(){
    if(!sameAddrChk||!sameAddrChk.checked||!presentAddr||!permAddr) return;
    permAddr.value=presentAddr.value;
    if(permStateSelect&&stateSelect){ permStateSelect.value=stateSelect.value; renderDistricts(permDistrictSelect,permStateSelect.value,districtSelect?.value||''); }
    if(permCityInput&&cityInput) permCityInput.value=cityInput.value;
    if(permPinInput&&pinInput) permPinInput.value=pinInput.value;
  }

  function syncAdmSource(){
    const isCp=admSourceSelect?.value==='channel_partner';
    if(cpGroup) cpGroup.style.display=isCp?'':'none';
    if(cpSelect){ cpSelect.required=isCp; if(!isCp) cpSelect.value=''; }
  }

  function syncSteps(){
    steps.forEach((s,i)=>s.classList.toggle('active',i===activeStep));
    indicators.forEach((el,i)=>el.classList.toggle('active',i===activeStep));
    prevBtn.style.visibility=activeStep===0?'hidden':'visible';
    nextBtn.style.display=activeStep===steps.length-1?'none':'';
    submitBtn.style.display=activeStep===steps.length-1?'':'none';
    if(activeStep===reviewStepIndex) buildReview();
  }

  // Field validators
  const uniqueErrors=new Map();
  function showFieldError(inp,msg){ inp.style.borderColor='var(--danger)'; const e=document.getElementById(inp.name+'-error'); if(e){e.textContent=msg;e.style.display='block';} inp.setCustomValidity(msg); }
  function clearFieldError(inp){ inp.style.borderColor=''; const e=document.getElementById(inp.name+'-error'); if(e){e.textContent='';e.style.display='none';} inp.setCustomValidity(''); }

  async function validateUnique(inp,field){
    if(!inp||!inp.value.trim()){ inp?.setCustomValidity(''); return true; }
    const res=await fetch(`${validateUrl}?${new URLSearchParams({field,value:inp.value.trim()})}`,{headers:{'Accept':'application/json'}});
    const d=await res.json();
    if(d.exists){ const lbl=field==='mobile'?'Mobile number':'Email'; inp.setCustomValidity(`${lbl} already exists.`); inp.reportValidity(); return false; }
    inp.setCustomValidity(''); return true;
  }
  function debounceUnique(inp,field){
    if(uniqueTimers.has(field)) clearTimeout(uniqueTimers.get(field));
    uniqueTimers.set(field,setTimeout(()=>validateUnique(inp,field),450));
  }

  async function validateStep(){
    const cur=steps[activeStep];
    const inputs=[...cur.querySelectorAll('input,select,textarea')].filter(el=>el.offsetParent!==null);
    for(const inp of inputs){ if(!inp.reportValidity()) return false; }
    if(activeStep===1){
      const mOk=await validateUnique(mobileInput,'mobile'); if(!mOk) return false;
      const eOk=await validateUnique(emailInput,'email'); if(!eOk) return false;
    }
    return true;
  }

  function addEduRow(row={}){
    const i=eduIndex++;
    const div=document.createElement('div'); div.className='edu-row';
    div.innerHTML=`
      <input type="text" class="gt-input" name="education[${i}][examination]" placeholder="Examination" value="${row.examination||''}">
      <input type="text" class="gt-input" name="education[${i}][institute_name]" placeholder="Institute" value="${row.institute_name||''}">
      <input type="text" class="gt-input" name="education[${i}][board_university]" placeholder="Board / University" value="${row.board_university||''}">
      <input type="text" class="gt-input" name="education[${i}][passing_year]" placeholder="Year" value="${row.passing_year||''}">
      <input type="text" class="gt-input" name="education[${i}][division]" placeholder="Division" value="${row.division||''}">
      <input type="text" class="gt-input" name="education[${i}][marks_percentage]" placeholder="%" value="${row.marks_percentage||''}">
      <button type="button" class="btn btn-danger btn-sm remove-edu">×</button>`;
    educationRows?.appendChild(div);
    div.querySelector('.remove-edu').addEventListener('click',()=>div.remove());
  }

  function buildReview(){
    const c=selectedCourse();
    const fd=new FormData(form);
    document.querySelectorAll('[data-review-field]').forEach(node=>{
      const key=node.dataset.reviewField;
      let val=fd.get(key);
      if(key==='photo') val=photoInput.files?.[0]?.name||'';
      node.textContent=val&&String(val).trim()?val:'-';
    });
    document.querySelectorAll('[data-review-meta]').forEach(node=>{
      const key=node.dataset.reviewMeta;
      if(key==='course') node.textContent=c?c.name:'-';
      if(key==='batch')  node.textContent=document.getElementById('batch_id')?.selectedOptions?.[0]?.textContent||'No Batch';
    });
    // Photo
    const defSrc='{{ asset($defaultPhotoPath) }}';
    const realSrc=photoPreview?.src||'';
    const isDef=!realSrc||realSrc===defSrc||realSrc.includes('user.svg');
    const ph=document.getElementById('review-photo-placeholder');
    if(isDef){ reviewPhotoPreview.style.display='none'; if(ph) ph.style.display='flex'; }
    else { reviewPhotoPreview.src=realSrc; reviewPhotoPreview.style.display='block'; if(ph) ph.style.display='none'; }
    // Education
    const eduEntries=[...( educationRows?.querySelectorAll('.edu-row')||[])].map(row=>{
      const ins=row.querySelectorAll('input');
      return{examination:ins[0]?.value||'',institute_name:ins[1]?.value||'',board_university:ins[2]?.value||'',passing_year:ins[3]?.value||'',division:ins[4]?.value||'',marks_percentage:ins[5]?.value||''};
    }).filter(r=>Object.values(r).some(v=>v));
    if(reviewEduBody) reviewEduBody.innerHTML=eduEntries.length
      ?eduEntries.map(r=>`<tr><td>${r.examination||'-'}</td><td>${r.institute_name||'-'}</td><td>${r.board_university||'-'}</td><td>${r.passing_year||'-'}</td><td>${r.division||'-'}</td><td>${r.marks_percentage||'-'}</td></tr>`).join('')
      :'<tr><td colspan="6" style="text-align:center;">No education details added.</td></tr>';
  }

  // Event listeners
  nextBtn.addEventListener('click',async()=>{ if(await validateStep()&&activeStep<steps.length-1){ activeStep++; syncSteps(); } });
  prevBtn.addEventListener('click',()=>{ if(activeStep>0){ activeStep--; syncSteps(); } });
  courseTypeSelect.addEventListener('change',()=>{ durationSelect.value=''; renderDurations(); renderCourses(); renderCourseSummary(); });
  durationSelect?.addEventListener('change',()=>{ renderCourses(); renderCourseSummary(); });
  courseSelect.addEventListener('change',renderCourseSummary);
  admSourceSelect?.addEventListener('change',syncAdmSource);
  sameAddrChk?.addEventListener('change',syncPermAddr);
  presentAddr?.addEventListener('input',syncPermAddr);
  stateSelect?.addEventListener('change',()=>{ renderDistricts(districtSelect,stateSelect.value); syncPermAddr(); });
  districtSelect?.addEventListener('change',syncPermAddr);
  cityInput?.addEventListener('input',syncPermAddr);
  pinInput?.addEventListener('input',syncPermAddr);
  permStateSelect?.addEventListener('change',()=>renderDistricts(permDistrictSelect,permStateSelect.value));
  addEduBtn?.addEventListener('click',()=>addEduRow());
  printBtn?.addEventListener('click',()=>{ buildReview(); window.print(); });
  photoInput?.addEventListener('change',e=>{ const f=e.target.files?.[0]; if(!f) return; const r=new FileReader(); r.onload=ev=>photoPreview.src=ev.target.result; r.readAsDataURL(f); });
  form.addEventListener('submit',async e=>{ if(!await validateStep()) e.preventDefault(); });

  // Wire mobile/email validation
  if(mobileInput){
    mobileInput.addEventListener('input',()=>{ mobileInput.value=mobileInput.value.replace(/\D/g,'').slice(0,10); mobileInput.setCustomValidity(''); debounceUnique(mobileInput,'mobile'); });
    mobileInput.addEventListener('blur',()=>validateUnique(mobileInput,'mobile'));
  }
  if(emailInput){
    emailInput.addEventListener('input',()=>{ emailInput.setCustomValidity(''); debounceUnique(emailInput,'email'); });
    emailInput.addEventListener('blur',()=>validateUnique(emailInput,'email'));
  }
  document.querySelectorAll('input[name="guardian_mobile"],input[name="whatsapp_no"],input[name="alternate_mobile"]').forEach(inp=>{
    inp.addEventListener('input',()=>inp.value=inp.value.replace(/\D/g,'').slice(0,10));
  });
  document.querySelectorAll('input[name="aadhar_no"]').forEach(inp=>{
    inp.addEventListener('input',()=>inp.value=inp.value.replace(/\D/g,'').slice(0,12));
  });

  // Init
  renderDurations();
  const oldEdu=@json(old('education',[]));
  if(Array.isArray(oldEdu)&&oldEdu.length) oldEdu.forEach(r=>addEduRow(r));
  const oldCourseId=@json(old('course_id'));
  const oldCourse=courseCatalog.find(c=>String(c.id)===String(oldCourseId));
  if(oldCourse?.course_type_id){ courseTypeSelect.value=String(oldCourse.course_type_id); }
  renderDurations();
  if(oldCourse?.duration){ durationSelect.value=String(oldCourse.duration); }
  renderCourses();
  if(oldCourseId){ courseSelect.value=String(oldCourseId); if(oldCourse&&crsDisp) crsDisp.value=oldCourse.name; }
  renderDistricts(districtSelect,stateSelect?.value||'',@json(old('district')));
  renderDistricts(permDistrictSelect,permStateSelect?.value||'',@json(old('permanent_district')));
  syncPermAddr();
  syncAdmSource();
  renderCourseSummary();
  syncSteps();
})();
</script>
@endpush
