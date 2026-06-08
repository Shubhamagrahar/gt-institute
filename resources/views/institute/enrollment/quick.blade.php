@extends('layouts.institute')
@section('title','Quick Booking')
@section('page-title','Quick Seat Booking')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@section('content')
@php
  $quickFields = $savedFields->filter(fn ($field) => $field->quick_is_active);
  $religionOptions = ['Hindu', 'Muslim', 'Sikh', 'Christian', 'Jain', 'Buddhist', 'Other'];
  $nationalityOptions = ['Indian', 'NRI', 'Other'];
@endphp

<div style="max-width:980px;margin:0 auto;">
  <div class="gt-card" style="margin-bottom:18px;">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Quick Seat Booking</div>
        <div class="text-xs text-muted" style="margin-top:4px;">Basic details save hongi. Final admission tabhi hoga jab full details complete hongi aur required payment aa jayega.</div>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('institute.enrollment.store-quick') }}" class="gt-card" autocomplete="off">
    @csrf
    <input type="text" name="fake_username" value="" autocomplete="username" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
    <input type="password" name="fake_password" value="" autocomplete="new-password" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
    <div class="gt-card-body" style="padding:22px;">
      <div class="gt-form-grid-3">
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
          <select name="course_id" id="course_id" class="gt-select" required>
            <option value="">Select Course</option>
          </select>
          @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Batch</label>
          <select name="batch_id" class="gt-select">
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

      <div class="gt-form-grid-3">
        <div class="gt-form-group">
          <label class="gt-label">Student Name <span style="color:var(--danger)">*</span></label>
          <input type="text" name="name" class="gt-input" value="{{ old('name') }}" autocomplete="off" required>
          @error('name')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
          <input type="text" name="mobile" class="gt-input" value="{{ old('mobile') }}" autocomplete="off" required>
          @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        @if($quickFields->has('email'))
          <div class="gt-form-group">
            <label class="gt-label">Email @if($quickFields['email']->quick_is_required)<span style="color:var(--danger)">*</span>@endif</label>
            <input type="email" name="email" class="gt-input" value="{{ old('email') }}" autocomplete="off" {{ $quickFields['email']->quick_is_required ? 'required' : '' }}>
            @error('email')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
        @endif

        @foreach($quickFields as $field)
          @continue(in_array($field->field_key, ['name', 'mobile', 'email', 'education_details'], true))
          <div class="gt-form-group">
            <label class="gt-label">
              {{ $field->field_label }}
              @if($field->quick_is_required)<span style="color:var(--danger)">*</span>@endif
            </label>
            @if($field->field_key === 'state')
              <select name="{{ $field->field_key }}" class="gt-select" {{ $field->quick_is_required ? 'required' : '' }}>
                <option value="">Select</option>
                @foreach($states as $state)
                  <option value="{{ $state }}" {{ old($field->field_key) === $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
              </select>
            @elseif(in_array($field->field_key, ['religion', 'nationality'], true))
              @php $options = $field->field_key === 'religion' ? collect($religionOptions) : collect($nationalityOptions); @endphp
              <select name="{{ $field->field_key }}" class="gt-select" {{ $field->quick_is_required ? 'required' : '' }}>
                <option value="">Select</option>
                @foreach($options as $option)
                  <option value="{{ $option }}" {{ old($field->field_key) === $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
              </select>
            @elseif($field->field_type === 'select')
              <select name="{{ $field->field_key }}" class="gt-select" {{ $field->quick_is_required ? 'required' : '' }}>
                <option value="">Select</option>
                @foreach(collect(explode(',', $field->options ?? ''))->map(fn ($opt) => trim($opt))->filter() as $option)
                  <option value="{{ $option }}" {{ old($field->field_key) === $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
              </select>
            @elseif($field->field_type === 'textarea')
              <textarea name="{{ $field->field_key }}" class="gt-textarea" {{ $field->quick_is_required ? 'required' : '' }}>{{ old($field->field_key) }}</textarea>
            @else
              <input
                type="{{ in_array($field->field_type, ['email', 'date', 'number'], true) ? $field->field_type : 'text' }}"
                name="{{ $field->field_key }}"
                class="gt-input"
                value="{{ old($field->field_key) }}"
                {{ $field->quick_is_required ? 'required' : '' }}
              >
            @endif
            @error($field->field_key)<div class="gt-error">{{ $message }}</div>@enderror
          </div>
        @endforeach
      </div>

      <div style="display:flex;justify-content:flex-end;margin-top:18px;">
        <button type="submit" class="btn btn-primary">Save Quick Booking</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const courseTypeSelect = document.getElementById('course_type_id');
  const durationSelect = document.getElementById('course_duration_filter');
  const courseSelect = document.getElementById('course_id');
  const admissionSourceSelect = document.getElementById('admission_source');
  const channelPartnerGroup = document.getElementById('channel_partner_group');
  const channelPartnerSelect = document.getElementById('channel_partner_id');
  const courseCatalog = @json($courseCatalog);
  const oldCourseId = @json(old('course_id'));
  const oldCourse = courseCatalog.find((course) => String(course.id) === String(oldCourseId));
  const mobileInput = document.querySelector('input[name="mobile"]');
  const emailInput = document.querySelector('input[name="email"]');
  const uniqueCheckUrl = @json(route('institute.enrollment.validate-field'));
  const uniquenessTimers = new Map();

  function renderDurationOptions() {
    const selectedTypeId = courseTypeSelect.value;
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

  function renderCourseOptions() {
    const selectedTypeId = courseTypeSelect.value;
    const selectedDuration = durationSelect.value;
    const filteredCourses = courseCatalog.filter((course) => {
      if (!selectedTypeId || !selectedDuration) {
        return false;
      }

      return String(course.course_type_id || '') === String(selectedTypeId)
        && String(course.duration || '') === String(selectedDuration);
    });

    courseSelect.innerHTML = '<option value="">Select Course</option>' + filteredCourses.map((course) =>
      `<option value="${course.id}">${course.name} (${course.duration} month${course.duration === 1 ? '' : 's'})</option>`
    ).join('');

    if (filteredCourses.some((course) => String(course.id) === String(oldCourseId))) {
      courseSelect.value = String(oldCourseId);
    }
  }

  function syncAdmissionSource() {
    const isChannelPartner = admissionSourceSelect?.value === 'channel_partner';
    channelPartnerGroup.style.display = isChannelPartner ? '' : 'none';
    channelPartnerSelect.required = isChannelPartner;
    if (!isChannelPartner) {
      channelPartnerSelect.value = '';
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

  if (oldCourse?.course_type_id) {
    courseTypeSelect.value = String(oldCourse.course_type_id);
  }

  courseTypeSelect.addEventListener('change', () => {
    durationSelect.value = '';
    renderDurationOptions();
    renderCourseOptions();
  });
  durationSelect.addEventListener('change', renderCourseOptions);
  admissionSourceSelect?.addEventListener('change', syncAdmissionSource);
  mobileInput?.addEventListener('blur', () => validateUniqueField(mobileInput, 'mobile'));
  emailInput?.addEventListener('blur', () => validateUniqueField(emailInput, 'email'));
  mobileInput?.addEventListener('input', () => {
    mobileInput.setCustomValidity('');
    debounceUniqueField(mobileInput, 'mobile');
  });
  emailInput?.addEventListener('input', () => {
    emailInput.setCustomValidity('');
    debounceUniqueField(emailInput, 'email');
  });
  renderDurationOptions();
  if (oldCourse?.duration) {
    durationSelect.value = String(oldCourse.duration);
  }
  renderCourseOptions();
  syncAdmissionSource();

  const quickForm = document.querySelector('form');
  quickForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const mobileOk = await validateUniqueField(mobileInput, 'mobile');
    const emailOk = await validateUniqueField(emailInput, 'email');
    if (!mobileOk || !emailOk) {
      return;
    }
    if (!quickForm.reportValidity()) {
      return;
    }
    quickForm.submit();
  });
})();
</script>
@endpush

