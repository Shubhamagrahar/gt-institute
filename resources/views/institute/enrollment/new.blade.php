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
/* ── Admission Form — No Color, Professional A4 ── */
.review-print-shell{width:210mm;max-width:100%;min-height:277mm;margin:0 auto;background:#fff;color:#000;padding:8mm 10mm;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;font-size:10px;display:flex;flex-direction:column}
.review-print-shell *{color:#000 !important;text-decoration:none !important}
/* Institute header */
.rf-inst-header{display:flex;align-items:flex-start;gap:12px;padding-bottom:8px;border-bottom:2.5px solid #000}
.rf-inst-logo img{height:62px;width:62px;object-fit:contain;display:block}
.rf-inst-name{font-size:22px;font-weight:900;line-height:1.15}
.rf-inst-contact{font-size:9.5px;margin-top:4px;line-height:1.5}
.rf-inst-addr{font-size:8.5px;margin-top:2px;line-height:1.4}
/* Form title */
.rf-form-title{text-align:center;font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:.16em;border-bottom:2px solid #000;padding:7px 0;margin:0}
/* Photo + info row */
.rf-top-row{display:flex;border:1.5px solid #000;margin-top:0;flex-shrink:0}
.rf-photo-cell{width:32mm;min-width:32mm;flex-shrink:0;border-right:1.5px solid #000;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:10px 5px 8px}
.rf-photo-box{width:27mm;height:35mm;border:1.5px solid #000;overflow:hidden;background:#fff;flex-shrink:0;display:flex;align-items:stretch}
.rf-photo-box img{width:100%;height:100%;object-fit:cover;display:block}
.rf-photo-lbl{font-size:7.5px;text-align:center;margin-top:5px;line-height:1.3}
/* Top info table — seamless inside rf-top-row border */
.rf-top-table{width:100%;border-collapse:collapse}
.rf-top-table td{border:1px solid #ccc;padding:5px 7px;vertical-align:middle}
.rf-top-table tr:first-child td{border-top:none}
.rf-top-table tr:last-child td{border-bottom:none}
.rf-top-table td:first-child{border-left:none}
.rf-top-table td:last-child{border-right:none}
.rf-top-table td.rf-lbl{font-size:8px;font-weight:700;text-transform:uppercase;white-space:nowrap;width:20%;background:#fafafa}
.rf-top-table td.rf-val{font-size:10.5px;font-weight:600;width:30%}
/* Section headings — NO background, just bold text with rule */
.rf-section-head{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.07em;padding:5px 0 3px;border-top:2px solid #000;border-bottom:1px solid #bbb;margin-top:10px}
/* Field tables */
.rf-table{width:100%;border-collapse:collapse}
.rf-table td{border:1px solid #ccc;padding:4px 7px;vertical-align:top}
.rf-table td.rf-lbl{font-size:8px;font-weight:700;text-transform:uppercase;width:22%;white-space:nowrap;background:#fafafa}
.rf-table td.rf-val{font-size:10px;width:28%}
/* Education */
.review-edu-table{width:100%;border-collapse:collapse}
.review-edu-table th,.review-edu-table td{border:1px solid #ccc;padding:4px 7px;font-size:9px;text-align:left}
.review-edu-table th{font-weight:700;font-size:8.5px;text-transform:uppercase;background:#fafafa}
/* Footer block */
.rf-footer{margin-top:auto;padding-top:10px}
.rf-declaration{padding:6px 9px;border:1px solid #bbb;font-size:8.5px;line-height:1.6;margin-bottom:0}
/* Auth signature block */
.rf-auth-sign{width:200px;text-align:center}
.rf-auth-line{border-top:1.5px solid #000;margin:6px 0 4px}
.rf-auth-label{font-size:10px;font-weight:700}
.rf-auth-sub{font-size:9px}
.plan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.plan-card{border:1px solid var(--border);border-radius:16px;padding:16px;background:var(--bg-3);cursor:pointer}
.plan-card.active{border-color:#1746a2;background:#eef4ff;box-shadow:0 0 0 2px rgba(23,70,162,.12)}
.plan-code{font-size:24px;font-weight:900;color:#1746a2}
.plan-name{font-size:14px;font-weight:800;margin:4px 0}
.wizard-actions{display:flex;justify-content:space-between;gap:12px;margin-top:22px}
.pay-note{margin-top:12px;padding:14px;border-radius:14px;background:#eef4ff;color:#3f587c;font-size:13px;line-height:1.5}
.hidden-input{display:none}
@media print{
  @page{size:A4 portrait;margin:7mm}
  html,body{margin:0;padding:0;background:#fff}
  .gt-sidebar,.gt-topbar,.gt-overlay,.gt-alert{display:none !important}
  .gt-layout{display:block !important}
  .gt-main{display:block !important;margin:0 !important;padding:0 !important}
  .gt-page{padding:0 !important;margin:0 !important}
  .adm-header,.adm-steps,.wizard-actions,.review-toolbar,.adm-section-title,.adm-section-note,.adm-enq-banner{display:none !important}
  .wizard-step{display:none !important}
  #review-wizard-step{display:block !important}
  .review-print-shell{width:auto;min-height:auto;padding:0;box-shadow:none;font-size:9px}
  .rf-inst-header{padding-bottom:5px}
  .rf-inst-name{font-size:16px}
  .rf-inst-contact{font-size:8.5px;margin-top:2px}
  .rf-inst-addr{font-size:7.5px;margin-top:2px}
  .rf-inst-logo img{height:48px;width:48px}
  .rf-form-title{font-size:11px;padding:4px 0}
  .rf-photo-box{width:25mm;height:31mm}
  .rf-photo-cell{width:29mm;min-width:29mm;padding:7px 4px 5px}
  .rf-section-head{margin-top:5px;padding:3px 0 2px;font-size:10.5px}
  .rf-table td{padding:2px 5px;font-size:8.5px}
  .rf-table td.rf-lbl{font-size:7.5px}
  .rf-top-table td{padding:3px 5px;font-size:9px}
  .rf-top-table td.rf-lbl{font-size:7.5px}
  .review-edu-table th,.review-edu-table td{padding:2px 4px;font-size:8px}
  .rf-footer{padding-top:6px}
  .rf-declaration{font-size:7.5px;padding:4px 6px;line-height:1.5}
  .rf-auth-sign{width:180px}
  .rf-auth-label{font-size:9px}
  .rf-auth-sub{font-size:8.5px}
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
  $religionOptions = ['Hindu', 'Muslim', 'Sikh', 'Christian', 'Jain', 'Buddhist', 'Other'];
  $nationalityOptions = ['Indian', 'NRI', 'Other'];
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
      ? ['Course','Basic','Address','Education','Review','Confirm']
      : ['Course','Basic','Address','Review','Confirm'];
  $errorFields = $errors->keys();
  $initialStep = 0;
  $stepErrorMap = [
      0 => ['course_id', 'batch_id', 'admission_source', 'channel_partner_id'],
      1 => ['name', 'mobile', 'email', 'photo', 'father_name', 'mother_name', 'guardian_name', 'guardian_relation', 'guardian_mobile', 'guardian_occupation', 'dob', 'gender', 'category', 'religion', 'nationality', 'whatsapp_no', 'alternate_mobile', 'aadhar_no', 'pan_no', 'blood_group', 'employment_status', 'computer_literacy', 'qualification'],
      2 => ['address', 'permanent_address', 'state', 'district', 'city', 'pin_code', 'permanent_state', 'permanent_district', 'permanent_city', 'permanent_pin_code'],
      3 => ['education'],
  ];
  foreach ($stepErrorMap as $stepIndex => $fieldsInStep) {
      foreach ($errorFields as $errorField) {
          foreach ($fieldsInStep as $prefix) {
              if ($prefix === 'education' ? str_starts_with($errorField, 'education.') : $errorField === $prefix || str_starts_with($errorField, $prefix . '.')) {
                  $initialStep = max($initialStep, $stepIndex);
                  break 3;
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

  @if(isset($enquiryPrefill) && $enquiryPrefill)
    <div class="adm-enq-banner" style="background:var(--accent-bg);border:1.5px solid var(--accent);border-radius:10px;padding:10px 16px;margin-bottom:14px;font-size:13px;">
      <span style="font-weight:700;color:var(--accent);">✓ Enquiry se convert:</span>
      <span style="color:var(--text-2);"> {{ $enquiryPrefill['name'] }} · {{ $enquiryPrefill['mobile'] }}</span>
    </div>
  @endif

  <form method="POST" action="{{ route('institute.enrollment.store-new') }}" enctype="multipart/form-data" id="admission-form" autocomplete="off">
    @csrf
    @if(isset($enquiryPrefill) && $enquiryPrefill)
      <input type="hidden" name="enquiry_id" value="{{ $enquiryPrefill['enquiry_id'] }}">
    @endif
    <input type="text" name="fake_username" value="" autocomplete="username" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
    <input type="password" name="fake_password" value="" autocomplete="new-password" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
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
                         placeholder="Search & select course…" autocomplete="off"
                         style="width:100%;cursor:pointer;">
                  <div id="course_search_dropdown" style="display:none;position:absolute;z-index:200;width:100%;top:calc(100% + 3px);background:var(--bg);border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>
                </div>
                @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

              <div class="gt-form-group">
                <label class="gt-label">Batch</label>
                <select name="batch_id" id="batch_id" class="gt-select">
                  <option value="">No Batch</option>
                  @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}@if($batch->start_time || $batch->end_time) ({{ $batch->start_time ? \Illuminate\Support\Carbon::parse($batch->start_time)->format('h:i A') : '-' }} - {{ $batch->end_time ? \Illuminate\Support\Carbon::parse($batch->end_time)->format('h:i A') : '-' }})@endif</option>
                  @endforeach
                </select>
              </div>

              <div class="gt-form-group">
                <label class="gt-label">Admission Source <span style="color:var(--danger)">*</span></label>
                <select name="admission_source" id="admission_source" class="gt-select" required>
                  <option value="direct" {{ old('admission_source', 'direct') === 'direct' ? 'selected' : '' }}>Direct</option>
                  <option value="channel_partner" {{ old('admission_source') === 'channel_partner' ? 'selected' : '' }}>Channel Partner</option>
                </select>
                @error('admission_source')<div class="gt-error">{{ $message }}</div>@enderror
              </div>

              <div class="gt-form-group" id="channel_partner_group" style="display:none;">
                <label class="gt-label">Channel Partner <span style="color:var(--danger)">*</span></label>
                <select name="channel_partner_id" id="channel_partner_id" class="gt-select">
                  <option value="">Select Channel Partner</option>
                  @foreach($channelPartners as $channelPartner)
                    <option value="{{ $channelPartner->id }}" {{ old('channel_partner_id') == $channelPartner->id ? 'selected' : '' }}>{{ $channelPartner->name }} ({{ $channelPartner->mobile }})</option>
                  @endforeach
                </select>
                @error('channel_partner_id')<div class="gt-error">{{ $message }}</div>@enderror
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
                  <input type="text" name="name" class="gt-input" value="{{ old('name', $enquiryPrefill['name'] ?? '') }}" autocomplete="off" required>
                  @error('name')<div class="gt-error">{{ $message }}</div>@enderror
                </div>

                <div class="gt-form-group">
                  <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
                  <input type="tel" name="mobile" id="mobile" class="gt-input"
                    value="{{ old('mobile', $enquiryPrefill['mobile'] ?? '') }}" autocomplete="off" required
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
                    @if(in_array($key, ['religion', 'nationality'], true))
                      @php
                        $options = $key === 'religion' ? collect($religionOptions) : collect($nationalityOptions);
                      @endphp
                      <select name="{{ $key }}" class="gt-select" {{ $field->is_required ? 'required' : '' }}>
                        <option value="">Select</option>
                        @foreach($options as $option)
                          <option value="{{ $option }}" {{ old($key) === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                      </select>
                    @elseif($field->field_type === 'select')
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

          <div class="wizard-step" data-step id="review-wizard-step">
            <div class="adm-section-title">Confirm and Review</div>
            <div class="adm-section-note">Review the complete admission form before printing or saving.</div>
            <div class="review-toolbar">
              <div class="text-sm text-muted">This is the printable A4 admission form.</div>
              <button type="button" class="btn btn-outline" id="print-review">Print Form</button>
            </div>

            <div class="review-print-shell" id="review-print-area">

              {{-- ① Institute Header ──────────────────────────────── --}}
              <div class="rf-inst-header">
                @if($institute?->logo && !in_array(trim($institute->logo ?? ''), ['images/default-institute.png','']))
                  <div class="rf-inst-logo">
                    <img src="{{ asset($institute->logo) }}" alt="logo">
                  </div>
                @endif
                <div style="flex:1;">
                  <div class="rf-inst-name">{{ $institute?->name ?? 'Institute Name' }}</div>
                  <div class="rf-inst-contact">
                    @if($institute?->mobile)Ph: {{ $institute->mobile }}@endif
                    @if($institute?->email)&nbsp;&nbsp;|&nbsp;&nbsp;{{ $institute->email }}@endif
                    @if($institute?->website)&nbsp;&nbsp;|&nbsp;&nbsp;{{ $institute->website }}@endif
                  </div>
                  <div class="rf-inst-addr">{{ $institute?->address ?? '' }}</div>
                </div>
              </div>

              {{-- ② Form Title ─────────────────────────────────────── --}}
              <div class="rf-form-title">Admission Application Form</div>

              {{-- ③ Photo  LEFT   +   Personal Info  RIGHT ────────── --}}
              <div class="rf-top-row">
                <div class="rf-photo-cell">
                  <div class="rf-photo-box">
                    {{-- Real photo (hidden until uploaded) --}}
                    <img id="review_photo_preview" alt="Photo"
                         style="width:100%;height:100%;object-fit:cover;display:none;">
                    {{-- Placeholder shown when no photo --}}
                    <div id="review-photo-placeholder"
                         style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:#999;">
                      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                      </svg>
                      <span style="font-size:7px;text-align:center;color:#bbb;line-height:1.3;">Affix Passport<br>Size Photo</span>
                    </div>
                  </div>
                  <div class="rf-photo-lbl">Passport Size<br>Photograph</div>
                </div>

                <div style="flex:1;">
                  <table class="rf-top-table">
                    <tr>
                      <td class="rf-lbl">Full Name</td>
                      <td class="rf-val" data-review-field="name">-</td>
                      <td class="rf-lbl">Date of Birth</td>
                      <td class="rf-val" data-review-field="dob">-</td>
                    </tr>
                    <tr>
                      <td class="rf-lbl">Mobile</td>
                      <td class="rf-val" data-review-field="mobile">-</td>
                      <td class="rf-lbl">Gender</td>
                      <td class="rf-val" data-review-field="gender">-</td>
                    </tr>
                    <tr>
                      <td class="rf-lbl">Email</td>
                      <td class="rf-val" data-review-field="email">-</td>
                      <td class="rf-lbl">Category</td>
                      <td class="rf-val" data-review-field="category">-</td>
                    </tr>
                    <tr>
                      <td class="rf-lbl">Course</td>
                      <td class="rf-val" colspan="3" data-review-meta="course">-</td>
                    </tr>
                    <tr>
                      <td class="rf-lbl">Batch</td>
                      <td class="rf-val" data-review-meta="batch">-</td>
                      <td class="rf-lbl">Enrolment No.</td>
                      <td class="rf-val" style="font-style:italic;font-size:9px;">Issued after admission</td>
                    </tr>
                  </table>
                </div>
              </div>

              {{-- ④ Dynamic Sections ───────────────────────────────── --}}
              @foreach($reviewSections as $section)
                @if($section['title'] === 'Education Details' && ! $educationEnabled)
                  @continue
                @endif

                @php
                  $alwaysShow = ['city','permanent_state','permanent_district','permanent_city','permanent_pin_code'];
                  $activeKeys = [];
                  foreach ($section['keys'] as $k) {
                    if ($k === 'education_details') { $activeKeys[] = $k; continue; }
                    $f = $resolveField($k);
                    if (in_array($k, $alwaysShow, true) || $f?->is_active) {
                      $activeKeys[] = $k;
                    }
                  }
                @endphp
                @if(empty($activeKeys)) @continue @endif

                <div class="rf-section-head">{{ $section['title'] }}</div>
                <table class="rf-table">
                  @php $buf = null; @endphp

                  @foreach($activeKeys as $k)

                    @if($k === 'education_details')
                      @if($buf !== null)
                        @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title(); @endphp
                        <tr><td class="rf-lbl">{{$bL}}</td><td class="rf-val" data-review-field="{{$buf}}" colspan="3">-</td></tr>
                        @php $buf=null; @endphp
                      @endif
                      <tr>
                        <td colspan="4" style="padding:0;">
                          <table class="review-edu-table">
                            <thead>
                              <tr>
                                <th>Examination</th><th>Institute / School</th>
                                <th>Board / University</th><th>Year</th><th>Division</th><th>%</th>
                              </tr>
                            </thead>
                            <tbody id="review-education-body">
                              <tr><td colspan="6" style="text-align:center;">No education details added.</td></tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      @continue
                    @endif

                    @php
                      $field  = $resolveField($k);
                      $isWide = in_array($k, $reviewWideFields, true);
                      $lbl    = $field?->field_label ?? \Illuminate\Support\Str::of($k)->replace('_',' ')->title();
                    @endphp

                    @if($isWide)
                      @if($buf !== null)
                        @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title(); @endphp
                        <tr><td class="rf-lbl">{{$bL}}</td><td class="rf-val" data-review-field="{{$buf}}" colspan="3">-</td></tr>
                        @php $buf=null; @endphp
                      @endif
                      <tr>
                        <td class="rf-lbl">{{ $lbl }}</td>
                        <td class="rf-val" colspan="3" data-review-field="{{ $k }}">-</td>
                      </tr>
                    @elseif($buf === null)
                      @php $buf = $k; @endphp
                    @else
                      @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title(); @endphp
                      <tr>
                        <td class="rf-lbl">{{ $bL }}</td>
                        <td class="rf-val" data-review-field="{{ $buf }}">-</td>
                        <td class="rf-lbl">{{ $lbl }}</td>
                        <td class="rf-val" data-review-field="{{ $k }}">-</td>
                      </tr>
                      @php $buf = null; @endphp
                    @endif

                  @endforeach

                  @if($buf !== null)
                    @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title(); @endphp
                    <tr><td class="rf-lbl">{{$bL}}</td><td class="rf-val" data-review-field="{{$buf}}" colspan="3">-</td></tr>
                  @endif
                </table>
              @endforeach

              {{-- ⑤ Footer: Declaration + Signatures ─────────────── --}}
              <div class="rf-footer">
                <div class="rf-declaration">
                  I hereby declare that all the information provided above is true and correct to the best of my knowledge and belief.
                  I agree to abide by the rules, regulations and fee payment schedule of the institute.
                </div>

                {{-- Only Authorized Signature block, right-aligned --}}
              <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                <div class="rf-auth-sign">
                  <div style="display:flex;flex-direction:column;align-items:center;gap:3px;min-height:60px;justify-content:flex-end;">
                    @if($institute?->use_stamp && $institute?->stamp)
                      <img src="{{ asset($institute->stamp) }}" alt="stamp"
                           style="height:58px;width:58px;object-fit:contain;display:block;">
                    @endif
                    @if($institute?->use_signature && $institute?->signature)
                      <img src="{{ asset($institute->signature) }}" alt="signature"
                           style="height:36px;max-width:130px;object-fit:contain;display:block;">
                    @else
                      @if(!($institute?->use_stamp && $institute?->stamp))
                        <div style="height:60px;"></div>
                      @endif
                    @endif
                  </div>
                  <div class="rf-auth-line"></div>
                  <div class="rf-auth-label">For {{ $institute?->name ?? 'Institute' }}</div>
                  <div class="rf-auth-sub">Auth. Signatory</div>
                </div>
              </div>
              </div>

            </div>{{-- /review-print-shell --}}
          </div>{{-- /wizard-step --}}

          <div class="wizard-step" data-step>
            <div class="adm-section-title">Final Confirmation</div>
            <div class="adm-section-note">No payment is collected at seat-booking time. Review everything once and save the seat booking. Payment will happen later from the pending admission list if needed.</div>
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
  const durationSelect = document.getElementById('course_duration_filter');
  const courseSelect = document.getElementById('course_id');
  const admissionSourceSelect = document.getElementById('admission_source');
  const channelPartnerGroup = document.getElementById('channel_partner_group');
  const channelPartnerSelect = document.getElementById('channel_partner_id');
  const courseCatalog = @json($courseCatalog);
  const districtsByState = @json($districtsByState ?? []);
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
  const mobileInput = document.querySelector('input[name="mobile"]');
  const emailInput = document.querySelector('input[name="email"]');
  const uniqueCheckUrl = @json(route('institute.enrollment.validate-field'));
  const errorFields = @json($errorFields);
  const initialStep = Number(@json($initialStep));
  let activeStep = Number.isFinite(initialStep) ? initialStep : 0;
  let educationIndex = 0;
  const uniquenessTimers = new Map();

  function money(value) {
    return 'Rs. ' + Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function selectedCourse() {
    return courseCatalog.find((course) => String(course.id) === String(courseSelect.value)) || null;
  }

  function selectedCourseTypeId() {
    return courseTypeSelect.value || '';
  }

  function selectedDuration() {
    return durationSelect?.value || '';
  }

  function renderDurationOptions() {
    const selectedTypeId = selectedCourseTypeId();
    const filteredCourses = courseCatalog.filter((course) => {
      if (!selectedTypeId) {
        return false;
      }

      return String(course.course_type_id || '') === String(selectedTypeId);
    });

    const durations = [...new Set(filteredCourses.map((course) => Number(course.duration || 0)).filter((duration) => duration > 0))]
      .sort((left, right) => left - right);

    const oldDuration = durationSelect.value;
    durationSelect.innerHTML = '<option value="">Select Duration</option>' + durations.map((duration) =>
      `<option value="${duration}">${duration} month${duration === 1 ? '' : 's'}</option>`
    ).join('');

    if (durations.some((duration) => String(duration) === String(oldDuration))) {
      durationSelect.value = String(oldDuration);
    }
  }

  // ── Searchable course dropdown ────────────────────────────────────
  const courseSearchDisplay  = document.getElementById('course_search_display');
  const courseSearchDropdown = document.getElementById('course_search_dropdown');
  let coursePool = [];

  function renderCourseDropdownOptions(query='') {
    const q = query.trim().toLowerCase();
    const filtered = coursePool.filter(c => !q || c.name.toLowerCase().includes(q));
    courseSearchDropdown.innerHTML = '';
    if (filtered.length === 0) {
      courseSearchDropdown.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:var(--text-2);font-style:italic;">No courses found</div>';
    } else {
      filtered.forEach(c => {
        const d = document.createElement('div');
        d.style.cssText = 'padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);';
        d.textContent = `${c.name} (${c.duration}m)`;
        d.addEventListener('mouseover', () => d.style.background = 'var(--accent-bg)');
        d.addEventListener('mouseout',  () => d.style.background = '');
        d.addEventListener('mousedown', (e) => {
          e.preventDefault();
          courseSelect.value = c.id;
          courseSearchDisplay.value = c.name;
          courseSearchDropdown.style.display = 'none';
          renderCourseSummary();
          updateStep1Summary();
        });
        courseSearchDropdown.appendChild(d);
      });
    }
    courseSearchDropdown.style.display = 'block';
  }

  if (courseSearchDisplay) {
    courseSearchDisplay.addEventListener('focus', () => renderCourseDropdownOptions(courseSearchDisplay.value));
    courseSearchDisplay.addEventListener('input', () => {
      courseSelect.value = '';
      renderCourseDropdownOptions(courseSearchDisplay.value);
    });
    courseSearchDisplay.addEventListener('blur', () => setTimeout(() => courseSearchDropdown.style.display='none', 150));
    document.addEventListener('click', (e) => {
      if (!courseSearchDropdown.contains(e.target) && e.target !== courseSearchDisplay) {
        courseSearchDropdown.style.display = 'none';
      }
    });
  }

  function renderCourseOptions() {
    const selectedTypeId = selectedCourseTypeId();
    const selectedDurationValue = selectedDuration();
    const oldValue = courseSelect.value;
    const filteredCourses = courseCatalog.filter((course) => {
      if (!selectedTypeId || !selectedDurationValue) {
        return false;
      }

      return String(course.course_type_id || '') === String(selectedTypeId)
        && String(course.duration || '') === String(selectedDurationValue);
    });

    coursePool = filteredCourses;
    courseSelect.innerHTML = '<option value="">Select Course</option>' + filteredCourses.map((course) => `
      <option value="${course.id}">${course.name} (${course.duration} month${course.duration === 1 ? '' : 's'})</option>
    `).join('');
    if (courseSearchDisplay) { courseSearchDisplay.value = ''; courseSearchDropdown.style.display='none'; }

    if (filteredCourses.some((course) => String(course.id) === String(oldValue))) {
      courseSelect.value = oldValue;
      const found = filteredCourses.find(c => String(c.id) === String(oldValue));
      if (found && courseSearchDisplay) courseSearchDisplay.value = found.name;
    }
  }

  function renderCourseSummary() {
    const course = selectedCourse();

    sideCourseName.textContent = course ? course.name : '-';
    sideCourseDuration.textContent = course ? `${course.duration} month(s)` : '-';
    if (!course) {
      feeBreakup.innerHTML = '<div class="text-sm text-muted">Select a course to view fee details.</div>';
      feeTotal.textContent = 'Total: Rs. 0.00';
      sideCourseTotal.textContent = 'Rs. 0.00';
      sideCourseRequired.textContent = 'Rs. 0.00';
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

  function syncAdmissionSource() {
    const isChannelPartner = admissionSourceSelect?.value === 'channel_partner';
    if (channelPartnerGroup) {
      channelPartnerGroup.style.display = isChannelPartner ? '' : 'none';
    }
    if (channelPartnerSelect) {
      channelPartnerSelect.required = isChannelPartner;
      if (!isChannelPartner) {
        channelPartnerSelect.value = '';
      }
    }
  }

  async function validateUniqueField(inputEl, fieldName) {
    if (!inputEl || !inputEl.value.trim()) {
      inputEl?.setCustomValidity('');
      return true;
    }

    const params = new URLSearchParams({
      field: fieldName,
      value: inputEl.value.trim(),
    });

    const response = await fetch(`${uniqueCheckUrl}?${params.toString()}`, {
      headers: { 'Accept': 'application/json' },
    });
    const data = await response.json();

    if (data.exists) {
      const label = fieldName === 'mobile' ? 'Mobile number' : 'Email';
      inputEl.setCustomValidity(`${label} already exists.`);
      inputEl.reportValidity();
      return false;
    }

    inputEl.setCustomValidity('');
    return true;
  }

  function debounceUniqueField(inputEl, fieldName, delay = 450) {
    if (!inputEl) {
      return;
    }

    if (uniquenessTimers.has(fieldName)) {
      clearTimeout(uniquenessTimers.get(fieldName));
    }

    const timer = setTimeout(() => {
      validateUniqueField(inputEl, fieldName);
    }, delay);

    uniquenessTimers.set(fieldName, timer);
  }

  async function validateBasicUniqueness() {
    const mobileOk = await validateUniqueField(mobileInput, 'mobile');
    if (!mobileOk) return false;
    const emailOk = await validateUniqueField(emailInput, 'email');
    return emailOk;
  }

  async function validateCurrentStep() {
    const current = steps[activeStep];
    const inputs = [...current.querySelectorAll('input, select, textarea')].filter((field) => field.offsetParent !== null);
    for (const input of inputs) {
      if (!input.reportValidity()) {
        return false;
      }
    }

    if (activeStep === 1) {
      return await validateBasicUniqueness();
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

    // Show real photo in review if actually uploaded, else show placeholder
    const _defaultSrc = '{{ asset($defaultPhotoPath) }}';
    const _realSrc    = photoPreview.src || '';
    const _isDefault  = !_realSrc || _realSrc === _defaultSrc
                        || _realSrc.includes('user.svg') || _realSrc.includes('user.png');
    const _placeholder = document.getElementById('review-photo-placeholder');
    if (_isDefault) {
      reviewPhotoPreview.style.display = 'none';
      if (_placeholder) _placeholder.style.display = 'flex';
    } else {
      reviewPhotoPreview.src = _realSrc;
      reviewPhotoPreview.style.display = 'block';
      if (_placeholder) _placeholder.style.display = 'none';
    }

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

  nextBtn.addEventListener('click', async () => {
    if (!await validateCurrentStep()) {
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
    durationSelect.value = '';
    renderDurationOptions();
    renderCourseOptions();
    renderCourseSummary();
  });
  durationSelect?.addEventListener('change', () => {
    renderCourseOptions();
    renderCourseSummary();
  });
  courseSelect.addEventListener('change', renderCourseSummary);

  admissionSourceSelect?.addEventListener('change', syncAdmissionSource);

  // ── Field format validators ──────────────────────────────────────
  function showFieldError(input, msg) {
    input.style.borderColor = 'var(--danger)';
    const errEl = document.getElementById(input.name + '-error') || input.nextElementSibling;
    if (errEl && errEl.classList.contains('gt-field-error')) { errEl.textContent = msg; errEl.style.display = 'block'; }
    input.setCustomValidity(msg);
  }
  function clearFieldError(input) {
    input.style.borderColor = '';
    const errEl = document.getElementById(input.name + '-error') || input.nextElementSibling;
    if (errEl && errEl.classList.contains('gt-field-error')) { errEl.textContent = ''; errEl.style.display = 'none'; }
    input.setCustomValidity('');
  }

  function validateMobileField(input) {
    const v = input.value.replace(/\D/g, '');
    if (v.length === 0) { clearFieldError(input); return true; }
    if (v.length !== 10) { showFieldError(input, 'Mobile number must be exactly 10 digits.'); return false; }
    clearFieldError(input); return true;
  }
  function validateEmailField(input) {
    const v = input.value.trim();
    if (v.length === 0) { clearFieldError(input); return true; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) { showFieldError(input, 'Please enter a valid email address.'); return false; }
    clearFieldError(input); return true;
  }
  function validateAadharField(input) {
    const v = input.value.replace(/\D/g, '');
    if (v.length === 0) { clearFieldError(input); return true; }
    if (v.length !== 12) { showFieldError(input, 'Aadhar number must be exactly 12 digits.'); return false; }
    clearFieldError(input); return true;
  }

  // Mobile — only digits, max 10
  function wireMobileInput(input) {
    if (!input) return;
    input.addEventListener('input', () => {
      input.value = input.value.replace(/\D/g, '').slice(0, 10);
      validateMobileField(input);
      input.setCustomValidity(''); debounceUniqueField(input, 'mobile');
    });
    input.addEventListener('blur', () => { validateMobileField(input); validateUniqueField(input, 'mobile'); });
  }
  // Email
  function wireEmailInput(input) {
    if (!input) return;
    input.addEventListener('input', () => { validateEmailField(input); input.setCustomValidity(''); debounceUniqueField(input, 'email'); });
    input.addEventListener('blur', () => { validateEmailField(input); validateUniqueField(input, 'email'); });
  }
  // Aadhar — only digits, max 12
  function wireAadharInput(input) {
    if (!input) return;
    input.addEventListener('input', () => { input.value = input.value.replace(/\D/g, '').slice(0, 12); validateAadharField(input); });
    input.addEventListener('blur', () => validateAadharField(input));
  }

  wireMobileInput(mobileInput);
  wireEmailInput(emailInput);
  // Guardian / alternate mobile fields (rendered dynamically, wire after DOM ready)
  document.querySelectorAll('input[name="guardian_mobile"], input[name="whatsapp_no"], input[name="alternate_mobile"]').forEach(wireMobileInput);
  document.querySelectorAll('input[name="aadhar_no"]').forEach(wireAadharInput);

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

  form.addEventListener('submit', async (event) => {
    if (!await validateCurrentStep()) {
      event.preventDefault();
    }
  });

  const oldEducation = @json(old('education', []));
  if (Array.isArray(oldEducation) && oldEducation.length) {
    oldEducation.forEach((row) => addEducationRow(row));
  }

  const oldCourseId = @json(old('course_id'));
  const oldCourse = courseCatalog.find((course) => String(course.id) === String(oldCourseId));
  if (oldCourse?.course_type_id) {
    courseTypeSelect.value = String(oldCourse.course_type_id);
  }

  renderDurationOptions();
  if (oldCourse?.duration) {
    durationSelect.value = String(oldCourse.duration);
  }
  renderCourseOptions();
  if (oldCourseId) {
    courseSelect.value = String(oldCourseId);
  }
  renderDistrictOptions(districtSelect, stateSelect?.value || '', @json(old('district')));
  renderDistrictOptions(permanentDistrictSelect, permanentStateSelect?.value || '', @json(old('permanent_district')));
  syncPermanentAddress();
  syncAdmissionSource();
  renderCourseSummary();
  syncSteps();

  // ── Enquiry pre-fill (auto-select course from enquiry) ────────────
  @if(isset($enquiryPrefill) && $enquiryPrefill && $enquiryPrefill['course_id'])
  (function () {
    const prefillCourseId = @json($enquiryPrefill['course_id']);
    const prefillCourse   = courseCatalog.find(c => String(c.id) === String(prefillCourseId));
    if (!prefillCourse) return;

    // Set type + duration + render options
    if (courseTypeSelect && prefillCourse.course_type_id) {
      courseTypeSelect.value = String(prefillCourse.course_type_id);
    }
    renderDurationOptions();
    if (durationSelect && prefillCourse.duration) {
      durationSelect.value = String(prefillCourse.duration);
    }
    renderCourseOptions();

    // Set the hidden select + visible search input
    courseSelect.value = String(prefillCourseId);
    if (courseSearchDisplay) courseSearchDisplay.value = prefillCourse.name;
    renderCourseSummary();
  })();
  @endif

  // Pre-fill email from enquiry
  @if(isset($enquiryPrefill) && $enquiryPrefill && $enquiryPrefill['email'])
  (function () {
    const emailEl = document.querySelector('input[name="email"]');
    if (emailEl && !emailEl.value) emailEl.value = @json($enquiryPrefill['email']);
  })();
  @endif
})();
</script>
@endpush

