@extends('layouts.institute')
@php
  $isQuick = $builderMode === 'quick';
  $pageTitleText = $isQuick ? 'Quick Form Builder' : 'Admission Form Builder';
  $saveRoute = $isQuick ? route('institute.form-builder.quick.save') : route('institute.form-builder.admission.save');
  $backRoute = route('institute.form-builder.index');
@endphp
@section('title', $pageTitleText)
@section('page-title', $pageTitleText)
@section('topbar-actions')
  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    @unless($isQuick)
      <a href="{{ route('institute.form-builder.admission.print') }}" target="_blank" class="btn btn-outline btn-sm">Download Form</a>
    @endunless
    <a href="{{ $backRoute }}" class="btn btn-outline btn-sm">Back</a>
  </div>
@endsection

@push('styles')
<style>
.fb-shell{display:grid;grid-template-columns:minmax(280px,.58fr) minmax(0,1.42fr);gap:16px;align-items:start}
.fb-card,.preview-wrap{border:1px solid var(--border);border-radius:16px;background:var(--bg-2);overflow:hidden}
.fb-card-head,.preview-head{padding:14px 16px;border-bottom:1px solid var(--border);background:var(--bg-3)}
.fb-card-head h2,.preview-head h3{margin:0;font-size:16px;font-weight:900}
.fb-card-head p,.preview-head p{margin:4px 0 0;font-size:11px;color:var(--text-2);line-height:1.45}
.fb-card-body{padding:8px 14px 14px}
.fb-section{padding:10px 0;border-top:1px solid var(--border)}
.fb-section:first-child{border-top:none}
.fb-section-title{font-size:12px;font-weight:800;color:var(--text);margin-bottom:2px}
.fb-section-sub{font-size:10px;color:var(--text-2);margin-bottom:8px}
.fb-fixed{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px}
.fb-chip{font-size:10px;font-weight:700;padding:4px 8px;border-radius:999px;background:rgba(30,99,210,.08);border:1px solid rgba(30,99,210,.2);color:#1e63d2}
.fb-row{display:grid;grid-template-columns:minmax(0,1fr) 82px 82px;gap:8px;align-items:center;padding:8px 0;border-top:1px solid #edf1f7}
.fb-row:first-of-type{border-top:none}
.fb-field-name{font-size:12px;font-weight:700;color:var(--text)}
.fb-field-meta{font-size:10px;color:var(--text-2);margin-top:2px;text-transform:uppercase}
.fb-toggle{display:flex;align-items:center;justify-content:flex-end;gap:7px;font-size:10px;color:var(--text-2)}
.fb-switch{position:relative;width:36px;height:20px;flex-shrink:0}
.fb-switch input{position:absolute;inset:0;opacity:0;cursor:pointer}
.fb-slider{position:absolute;inset:0;border-radius:999px;background:#df6a6a;transition:.2s}
.fb-slider:after{content:"";position:absolute;left:2px;top:2px;width:16px;height:16px;border-radius:50%;background:#fff;transition:.2s}
.fb-switch input:checked + .fb-slider{background:#2e4db8}
.fb-switch input:checked + .fb-slider:after{transform:translateX(16px)}
.fb-toggle.is-disabled{opacity:.45}
.fb-toggle.is-disabled .fb-switch input{cursor:not-allowed}
.fb-save{display:flex;justify-content:flex-end;padding-top:12px}
.preview-wrap{background:#eef3fb}
.preview-head{display:flex;justify-content:space-between;align-items:center;gap:12px}
.preview-head-actions{display:flex;gap:8px;flex-wrap:wrap}
.preview-body{padding:16px}
.form-paper{background:#fff;border:1px solid #d9e3f7;border-radius:14px;padding:16px;box-shadow:0 16px 36px rgba(15,23,42,.06)}
.paper-header{display:flex;justify-content:space-between;gap:16px;border-bottom:2px solid #1f4ca6;padding-bottom:10px;margin-bottom:12px}
.paper-header h3{margin:0;font-size:18px;font-weight:900;color:#16387e}
.paper-header p{margin:3px 0 0;font-size:10px;color:#64748b;line-height:1.45;max-width:360px}
.paper-meta{display:flex;flex-direction:column;gap:3px;font-size:9px;color:#334155;text-align:right}
.paper-title-row{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;margin-bottom:12px}
.paper-title-row h4{margin:0;font-size:14px;font-weight:900;color:#0f172a}
.paper-title-row p{margin:2px 0 0;font-size:10px;color:#64748b}
.paper-badge{padding:6px 10px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:10px;font-weight:800}
.paper-section{margin-top:12px}
.paper-section-title{font-size:10px;font-weight:900;color:#1e3a8a;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px}
.paper-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px 10px}
.paper-grid-fixed{margin-bottom:6px}
.paper-field{min-height:44px}
.paper-field-box{border:1px solid #e3ebfa;border-radius:10px;padding:8px 9px;background:#fbfcff}
.paper-field-wide{grid-column:1 / -1}
.paper-field label{display:block;font-size:10px;font-weight:700;color:#111827;margin-bottom:5px}
.paper-required{color:#dc2626;margin-left:4px;font-weight:900}
.paper-input{height:28px;border:1px solid #cfdcf4;border-radius:9px;background:#fff}
.paper-input-textarea{height:54px}
.paper-photo-box{height:90px;border:1.4px dashed #9fb0d0;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#64748b;background:#fff}
.paper-input-wrap{background:#fff;border-radius:10px}
.paper-education{border:1px solid #d9e3f7;border-radius:10px;overflow:hidden}
.paper-education table{width:100%;border-collapse:collapse}
.paper-education th,.paper-education td{border:1px solid #e5edfa;padding:6px;font-size:9px;text-align:left}
.paper-education th{background:#f8fbff;color:#1e3a8a}
.paper-footer{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:18px;font-size:10px;font-weight:700;color:#334155}
.paper-sign-line{margin-top:24px;border-bottom:1.6px solid #94a3b8}
.fb-hidden{display:none!important}
@media(max-width:1160px){.fb-shell{grid-template-columns:1fr}.preview-wrap{order:-1}}
@media(max-width:760px){.fb-row{grid-template-columns:1fr 82px 82px}.paper-grid{grid-template-columns:1fr}.paper-footer,.paper-header,.paper-title-row{grid-template-columns:1fr;display:grid}.paper-meta{text-align:left}}
</style>
@endpush

@section('content')
@php
  $groups = [
    ['title' => 'Basic Details', 'sub' => 'Core student details', 'fixed' => $isQuick ? ['Student Name', 'Mobile Number', 'Course', 'Batch', 'Payment Plan'] : ['Student Name', 'Mobile Number', 'Course', 'Batch', 'Payment Plan', 'Fee Summary'], 'keys' => ['email', 'dob', 'gender']],
    ['title' => 'Student Profile', 'sub' => 'Personal and address information', 'fixed' => [], 'keys' => ['photo','whatsapp_no','alternate_mobile','aadhar_no','pan_no','blood_group','category','religion','nationality','address','permanent_address','state','district','pin_code','employment_status','computer_literacy','qualification']],
    ['title' => 'Guardian Details', 'sub' => 'Parent and guardian information', 'fixed' => [], 'keys' => ['father_name','mother_name','guardian_name','guardian_relation','guardian_mobile','guardian_occupation']],
    ['title' => 'Education Details', 'sub' => 'Education section visibility', 'fixed' => [], 'keys' => ['education_details']],
  ];
  $fieldsByKey = collect($allFields)->keyBy('key');
@endphp

<div class="fb-shell">
  <div class="fb-card">
    <div class="fb-card-head">
      <h2>{{ $pageTitleText }}</h2>
      <p>{{ $isQuick ? 'Configure the quick booking fields.' : 'Configure the printable admission form fields.' }}</p>
    </div>

    <div class="fb-card-body">
      <form method="POST" action="{{ $saveRoute }}">
        @csrf
        @foreach($groups as $group)
          <div class="fb-section">
            <div class="fb-section-title">{{ $group['title'] }}</div>
            <div class="fb-section-sub">{{ $group['sub'] }}</div>

            @if(count($group['fixed']))
              <div class="fb-fixed">
                @foreach($group['fixed'] as $label)
                  <span class="fb-chip">{{ $label }}</span>
                @endforeach
              </div>
            @endif

            @foreach($group['keys'] as $key)
              @php
                $field = $fieldsByKey[$key] ?? null;
                $saved = $savedFields[$key] ?? null;
                $isShown = $isQuick ? (bool) ($saved?->quick_is_active) : (!$saved || $saved->is_active);
                $isRequired = $isQuick ? (bool) ($saved?->quick_is_required) : (bool) ($saved?->is_required);
              @endphp
              @continue(!$field)
              <div class="fb-row">
                <div>
                  <div class="fb-field-name">{{ $field['label'] }}</div>
                  <div class="fb-field-meta">{{ $field['type'] }}</div>
                </div>
                <label class="fb-toggle">
                  <span>Show</span>
                  <span class="fb-switch">
                    <input type="checkbox" name="active[]" value="{{ $key }}" class="js-show" data-key="{{ $key }}" {{ $isShown ? 'checked' : '' }}>
                    <span class="fb-slider"></span>
                  </span>
                </label>
                <label class="fb-toggle js-required-wrap {{ $isShown ? '' : 'is-disabled' }}" data-key="{{ $key }}">
                  <span>Req</span>
                  <span class="fb-switch">
                    <input type="checkbox" name="required[]" value="{{ $key }}" class="js-required" data-key="{{ $key }}" {{ $isRequired ? 'checked' : '' }} {{ $isShown ? '' : 'disabled' }}>
                    <span class="fb-slider"></span>
                  </span>
                </label>
              </div>
            @endforeach
          </div>
        @endforeach

        <div class="fb-save">
          <button type="submit" class="btn btn-primary">Save {{ $pageTitleText }}</button>
        </div>
      </form>
    </div>
  </div>

  <div class="preview-wrap">
    <div class="preview-head">
      <div>
        <h3>Form Preview</h3>
        <p>Live layout for print and student entry.</p>
      </div>
      <div class="preview-head-actions">
        @unless($isQuick)
          <a href="{{ route('institute.form-builder.admission.print') }}" target="_blank" class="btn btn-outline btn-sm">Download Form</a>
        @endunless
      </div>
    </div>
    <div class="preview-body">
      @include('institute.form-builder._preview', ['builderMode' => $builderMode, 'fieldsByKey' => $fieldsByKey, 'savedFields' => $savedFields, 'institute' => $institute])
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  function syncField(key) {
    const show = document.querySelector(`.js-show[data-key="${key}"]`);
    const required = document.querySelector(`.js-required[data-key="${key}"]`);
    const wrap = document.querySelector(`.js-required-wrap[data-key="${key}"]`);
    const previews = document.querySelectorAll(`[data-preview-field][data-key="${key}"]`);
    if (!show || !required || !wrap) return;

    required.disabled = !show.checked;
    wrap.classList.toggle('is-disabled', !show.checked);
    if (!show.checked) required.checked = false;

    previews.forEach((preview) => {
      preview.classList.toggle('fb-hidden', !show.checked);
      const star = preview.querySelector('[data-required-mark]');
      if (star) {
        star.classList.toggle('fb-hidden', !required.checked);
      }
    });
  }

  document.querySelectorAll('.js-show,.js-required').forEach((el) => {
    el.addEventListener('change', () => syncField(el.dataset.key));
  });

  document.querySelectorAll('.js-show').forEach((el) => syncField(el.dataset.key));
})();
</script>
@endpush
