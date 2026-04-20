@extends('layouts.institute')
@section('title','Form Builder')
@section('page-title','Admission Form Builder')

@push('styles')
<style>
.fb-shell{display:grid;grid-template-columns:430px minmax(0,1fr);gap:22px;align-items:start}
.fb-panel,.fb-preview{position:sticky;top:18px}
.fb-hero{background:linear-gradient(135deg,#5f49d7,#7b64e8);border-radius:18px;padding:22px;color:#fff;margin-bottom:16px;box-shadow:0 18px 44px rgba(95,73,215,.22)}
.fb-hero small{display:block;opacity:.78;font-weight:700;text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px}
.fb-hero h2{margin:0;font-size:24px}
.fb-step{border:1px solid var(--border);border-radius:16px;background:var(--bg-2);margin-bottom:12px;overflow:hidden}
.fb-step-head{display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--bg-3)}
.fb-step-no{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:#6c5dd3;color:#fff;font-weight:800}
.fb-step-title{font-weight:800;color:var(--text)}
.fb-step-sub{font-size:12px;color:var(--text-2);margin-top:2px}
.fb-field-row{display:grid;grid-template-columns:minmax(0,1fr) auto auto;gap:12px;align-items:center;padding:12px 14px;border-top:1px solid var(--border)}
.fb-field-name{font-weight:700;font-size:13px}
.fb-fixed{font-size:11px;border:1px solid rgba(108,93,211,.28);background:rgba(108,93,211,.12);color:#a89cf5;border-radius:99px;padding:4px 8px}
.fb-toggle{display:flex;align-items:center;gap:7px;color:var(--text-2);font-size:12px;white-space:nowrap}
.fb-switch{position:relative;width:40px;height:22px}.fb-switch input{position:absolute;inset:0;opacity:0;cursor:pointer}
.fb-slider{position:absolute;inset:0;border-radius:999px;background:#ef4444;transition:.2s}.fb-slider:after{content:"";position:absolute;left:2px;top:2px;width:18px;height:18px;border-radius:50%;background:#fff;transition:.2s}
.fb-switch input:checked+.fb-slider{background:#6c5dd3}.fb-switch input:checked+.fb-slider:after{transform:translateX(18px)}
.reg-frame{background:#f5f7fb;border-radius:18px;overflow:hidden;border:1px solid #e7ebf3;color:#1f2937;box-shadow:0 18px 44px rgba(15,23,42,.12)}
.reg-top{background:linear-gradient(135deg,#6651d8,#503ab9);color:#fff;padding:22px 26px;display:flex;gap:14px;align-items:center}
.reg-icon{width:54px;height:54px;border-radius:15px;background:rgba(255,255,255,.16);display:grid;place-items:center;font-size:24px}
.reg-title{font-size:24px;font-weight:900}.reg-sub{opacity:.82;margin-top:3px;font-size:13px}
.reg-steps{display:grid;grid-template-columns:repeat(5,1fr);gap:0;padding:20px 34px 12px;position:relative}
.reg-step{text-align:center;position:relative;color:#6c5dd3;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.08em}
.reg-step:before{content:"";position:absolute;top:19px;left:-50%;right:50%;height:3px;background:#8f7cf1;z-index:0}.reg-step:first-child:before{display:none}
.reg-bubble{position:relative;z-index:1;width:40px;height:40px;margin:0 auto 9px;border-radius:50%;background:#6c5dd3;color:#fff;display:grid;place-items:center;font-size:15px}
.reg-card{margin:18px 34px 28px;background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 12px 35px rgba(15,23,42,.08)}
.reg-card-head{background:#6250cf;color:#fff;padding:18px 22px;display:flex;justify-content:space-between;align-items:center}
.reg-card-title{font-size:18px;font-weight:900}
.reg-body{padding:22px}.reg-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.reg-field{display:flex;flex-direction:column;gap:6px}.reg-field label{font-size:12px;font-weight:800;color:#4b5563}
.reg-input{border:1px solid #dbe2ef;border-radius:10px;padding:11px 12px;background:#fbfcff;color:#111827;font-size:13px}
.reg-photo{border:1px dashed #c5cfdf;background:#f8fafc;border-radius:14px;min-height:126px;display:grid;place-items:center;text-align:center;color:#64748b}
.fee-side{background:#f1f5ff;border:1px solid #dbe5ff;border-radius:14px;padding:14px}
.fee-old{text-decoration:line-through;color:#94a3b8;font-weight:700}.fee-now{font-size:24px;font-weight:900;color:#503ab9}
.edu-table{width:100%;border-collapse:collapse}.edu-table th{font-size:11px;text-align:left;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;padding:10px}.edu-table td{padding:12px 10px;border-bottom:1px solid #eef2f7;color:#94a3b8}
.pay-types{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.pay-type{border:1px solid #e0e7ff;background:#f8faff;border-radius:14px;padding:14px}.pay-type strong{display:block;color:#503ab9;font-size:18px;margin-bottom:4px}
.fb-hidden{display:none!important}
@media(max-width:1200px){.fb-shell{grid-template-columns:1fr}.fb-panel,.fb-preview{position:static}}
@media(max-width:760px){.reg-steps{grid-template-columns:1fr;gap:8px}.reg-step:before{display:none}.reg-grid,.pay-types{grid-template-columns:1fr}.reg-card{margin:14px}.fb-field-row{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@php
  $groups = [
    ['no'=>1,'title'=>'Important Details','sub'=>'Name, mobile, course, fee, batch','fixed'=>[
      ['key'=>'name','label'=>'Student Name'],['key'=>'mobile','label'=>'Mobile Number'],['label'=>'Course'],['label'=>'Listed Fee + Course Fee'],['label'=>'Batch'],
    ],'keys'=>['email','dob','gender']],
    ['no'=>2,'title'=>'Student Profile','sub'=>'Photo and personal details','fixed'=>[
      ['label'=>'Default Profile Image'],
    ],'keys'=>['photo','whatsapp_no','alternate_mobile','aadhar_no','pan_no','blood_group','category','religion','nationality','address','permanent_address','state','district','pin_code','employment_status','computer_literacy','qualification']],
    ['no'=>3,'title'=>'Guardian Details','sub'=>'Parent and guardian information','fixed'=>[],'keys'=>['father_name','mother_name','guardian_name','guardian_relation','guardian_mobile','guardian_occupation']],
    ['no'=>4,'title'=>'Education Details','sub'=>'Course / exam table','fixed'=>[],'keys'=>['education_details']],
    ['no'=>5,'title'=>'Payment','sub'=>'OTP, PART, MONTH with discount','fixed'=>[
      ['label'=>'OTP - One Time Payment'],['label'=>'PART - Partial Payment'],['label'=>'MONTH - Monthly Payment'],['label'=>'Discount'],
    ],'keys'=>[]],
  ];
  $fieldsByKey = collect($allFields)->keyBy('key');
@endphp

<div class="fb-shell">
  <div class="fb-panel">
    <div class="fb-hero">
      <small>Step Wise Builder</small>
      <h2>Control the admission form structure</h2>
      <p style="margin:8px 0 0;opacity:.84;">Fixed fields remain part of the registration flow. Additional predefined fields can be enabled, marked required, and saved to the database configuration.</p>
    </div>

    <form method="POST" action="{{ route('institute.form-builder.save') }}">
      @csrf
      @foreach($groups as $group)
        <div class="fb-step">
          <div class="fb-step-head">
            <div class="fb-step-no">{{ $group['no'] }}</div>
            <div>
              <div class="fb-step-title">{{ $group['title'] }}</div>
              <div class="fb-step-sub">{{ $group['sub'] }}</div>
            </div>
          </div>

          @foreach($group['fixed'] as $fixed)
            <div class="fb-field-row">
              <div class="fb-field-name">{{ $fixed['label'] }}</div>
              <span class="fb-fixed">Fixed</span>
              <span class="fb-fixed">Required</span>
              @if(!empty($fixed['key']))
                <input type="hidden" name="active[]" value="{{ $fixed['key'] }}">
                <input type="hidden" name="required[]" value="{{ $fixed['key'] }}">
              @endif
            </div>
          @endforeach

          @foreach($group['keys'] as $key)
            @php
              $field = $fieldsByKey[$key] ?? null;
              $saved = $savedFields[$key] ?? null;
              $isActive = !$saved || $saved->is_active;
              $isRequired = (bool) ($saved?->is_required);
            @endphp
            @continue(!$field)
            <div class="fb-field-row">
              <div class="fb-field-name">{{ $field['label'] }}</div>
              <label class="fb-toggle">
                Required
                <span class="fb-switch">
                  <input type="checkbox" name="required[]" value="{{ $key }}" class="js-required-toggle" data-key="{{ $key }}" {{ $isRequired ? 'checked' : '' }}>
                  <span class="fb-slider"></span>
                </span>
              </label>
              <label class="fb-toggle">
                Show
                <span class="fb-switch">
                  <input type="checkbox" name="active[]" value="{{ $key }}" class="js-show-toggle" data-key="{{ $key }}" {{ $isActive ? 'checked' : '' }}>
                  <span class="fb-slider"></span>
                </span>
              </label>
            </div>
          @endforeach
        </div>
      @endforeach

      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">Save Form Builder</button>
    </form>
  </div>

  <div class="fb-preview">
    <div class="reg-frame">
      <div class="reg-top">
        <div class="reg-icon">GT</div>
        <div>
          <div class="reg-title">Student Registration</div>
          <div class="reg-sub">Fill all steps carefully to complete admission</div>
        </div>
      </div>

      <div class="reg-steps">
        @foreach(['Course','Personal','Guardian','Education','Payment'] as $i => $step)
          <div class="reg-step" @if($step === 'Education') data-preview-field data-key="education_details" @endif><div class="reg-bubble">{{ $i + 1 }}</div>{{ $step }}</div>
        @endforeach
      </div>

      @php $educationSaved = $savedFields['education_details'] ?? null; @endphp
      <div class="reg-card">
        <div class="reg-card-head">
          <div class="reg-card-title">1. Important Details</div>
          <span style="font-size:12px;font-weight:800;">Live Preview</span>
        </div>
        <div class="reg-body">
          <div class="reg-grid">
            <div class="reg-field"><label>Student Name *</label><input class="reg-input" placeholder="Student full name"></div>
            <div class="reg-field"><label>Mobile Number *</label><input class="reg-input" placeholder="10 digit mobile"></div>
            <div class="reg-field"><label>Course *</label><select class="reg-input"><option>DCA - Diploma in Computer Applications</option></select></div>
            <div class="reg-field"><label>Batch *</label><select class="reg-input"><option>Morning Batch</option></select></div>
            <div class="fee-side">
              <div style="font-size:12px;color:#64748b;font-weight:800;">Course Fee</div>
              <div class="fee-old">₹5,999</div>
              <div class="fee-now">₹4,999</div>
            </div>
          </div>
        </div>
      </div>

      <div class="reg-card">
        <div class="reg-card-head"><div class="reg-card-title">2. Student Profile</div></div>
        <div class="reg-body">
          <div class="reg-grid">
            <div class="reg-photo">Default profile image<br><small>The default image is saved when no photo is uploaded.</small></div>
            @foreach(['email','dob','gender','address','state','district'] as $key)
              @php $field = $fieldsByKey[$key] ?? null; $saved = $savedFields[$key] ?? null; @endphp
              @if($field)
                <div class="reg-field {{ (!$saved || $saved->is_active) ? '' : 'fb-hidden' }}" data-preview-field data-key="{{ $key }}">
                  <label>{{ $field['label'] }} <span data-required-mark>{{ $saved?->is_required ? '*' : '' }}</span></label>
                  <input class="reg-input" placeholder="{{ $field['label'] }}">
                </div>
              @endif
            @endforeach
          </div>
        </div>
      </div>

      <div class="reg-card">
        <div class="reg-card-head"><div class="reg-card-title">3. Guardian Details</div></div>
        <div class="reg-body"><div class="reg-grid">
          @foreach(['father_name','mother_name','guardian_mobile'] as $key)
            @php $field = $fieldsByKey[$key] ?? null; $saved = $savedFields[$key] ?? null; @endphp
            @if($field)
              <div class="reg-field {{ (!$saved || $saved->is_active) ? '' : 'fb-hidden' }}" data-preview-field data-key="{{ $key }}">
                <label>{{ $field['label'] }} <span data-required-mark>{{ $saved?->is_required ? '*' : '' }}</span></label>
                <input class="reg-input" placeholder="{{ $field['label'] }}">
              </div>
            @endif
          @endforeach
        </div></div>
      </div>

      <div class="reg-card {{ (!$educationSaved || $educationSaved->is_active) ? '' : 'fb-hidden' }}" data-preview-field data-key="education_details">
        <div class="reg-card-head"><div class="reg-card-title">4. Education Details <span data-required-mark></span></div><button class="btn btn-sm" type="button">+ Add Row</button></div>
        <div class="reg-body">
          <table class="edu-table"><thead><tr><th>Course / Exam</th><th>Institute</th><th>Board / University</th><th>Year</th><th>Division</th><th>Percentage</th></tr></thead><tbody><tr><td colspan="6">Add education record</td></tr></tbody></table>
        </div>
      </div>

      <div class="reg-card">
        <div class="reg-card-head"><div class="reg-card-title">5. Payment</div></div>
        <div class="reg-body">
          <div class="pay-types">
            <div class="pay-type"><strong>OTP</strong>One time full payment</div>
            <div class="pay-type"><strong>PART</strong>Advance + due amount</div>
            <div class="pay-type"><strong>MONTH</strong>Monthly installments</div>
          </div>
          <div class="reg-grid" style="margin-top:14px;">
            <div class="reg-field"><label>Discount</label><input class="reg-input" value="₹500"></div>
            <div class="reg-field"><label>Payable</label><input class="reg-input" value="₹4,499"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  function syncField(key) {
    const show = document.querySelector(`.js-show-toggle[data-key="${key}"]`);
    const required = document.querySelector(`.js-required-toggle[data-key="${key}"]`);
    const previews = document.querySelectorAll(`[data-preview-field][data-key="${key}"]`);
    if (!show || !required) return;
    required.disabled = !show.checked;
    required.closest('.fb-toggle').style.opacity = show.checked ? '1' : '.45';
    previews.forEach((preview) => {
      preview.classList.toggle('fb-hidden', !show.checked);
      const mark = preview.querySelector('[data-required-mark]');
      if (mark) mark.textContent = show.checked && required.checked ? '*' : '';
    });
  }
  document.querySelectorAll('.js-show-toggle,.js-required-toggle').forEach((el) => {
    el.addEventListener('change', () => syncField(el.dataset.key));
  });
  document.querySelectorAll('.js-show-toggle').forEach((el) => syncField(el.dataset.key));
})();
</script>
@endpush
