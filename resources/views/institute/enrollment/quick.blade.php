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
        <div class="text-xs text-muted" style="margin-top:4px;">Basic details will be saved. Final admission will be confirmed once complete details and required payment are received.</div>
      </div>
    </div>
  </div>

  @if(isset($enquiryPrefill) && $enquiryPrefill)
    <div style="background:var(--accent-bg);border:1.5px solid var(--accent);border-radius:10px;padding:10px 16px;margin-bottom:14px;font-size:13px;">
      <span style="font-weight:700;color:var(--accent);">&#10003; Converting from Enquiry:</span>
      <span style="color:var(--text-2);"> {{ $enquiryPrefill['name'] }} &middot; {{ $enquiryPrefill['mobile'] }}</span>
    </div>
  @endif

  <form method="POST" action="{{ route('institute.enrollment.store-quick') }}" class="gt-card" autocomplete="off">
    @csrf
    @if(isset($enquiryPrefill) && $enquiryPrefill)
      <input type="hidden" name="enquiry_id" value="{{ $enquiryPrefill['enquiry_id'] }}">
    @endif
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
          <select name="course_id" id="course_id" class="gt-select" required style="display:none;">
            <option value="">Select Course</option>
          </select>
          <div style="position:relative;">
            <input type="text" id="course_search_display" class="gt-select"
                   placeholder="Search & select course…" autocomplete="off" required
                   style="width:100%;cursor:pointer;">
            <div id="course_search_dropdown" style="display:none;position:absolute;z-index:200;width:100%;top:calc(100% + 3px);background:var(--bg);border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>
          </div>
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

      {{-- Fee Breakdown (shown after course selected) --}}
      <div id="fee-breakdown-box" style="display:none;background:var(--bg-3);border:1px solid var(--border);border-radius:10px;padding:14px 18px;margin-bottom:16px;">
        <div style="font-size:12px;font-weight:700;color:var(--text-2);letter-spacing:.4px;margin-bottom:10px;">FEE BREAKDOWN</div>
        <div id="fee-breakdown-rows"></div>
        <div style="border-top:1px solid var(--border);margin-top:10px;padding-top:10px;display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:13px;font-weight:700;">Total Payable</span>
          <span id="fee-breakdown-total" style="font-size:18px;font-weight:900;color:var(--accent);">₹0</span>
        </div>
      </div>

      <div class="gt-form-grid-3">
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
          renderFeeBreakdown(c.id);
        });
        courseSearchDropdown.appendChild(d);
      });
    }
    courseSearchDropdown.style.display = 'block';
  }

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

    coursePool = filteredCourses;
    courseSelect.innerHTML = '<option value="">Select Course</option>' + filteredCourses.map((course) =>
      `<option value="${course.id}">${course.name} (${course.duration} month${course.duration === 1 ? '' : 's'})</option>`
    ).join('');
    courseSearchDisplay.value = '';
    courseSearchDropdown.style.display = 'none';

    if (filteredCourses.some((course) => String(course.id) === String(oldCourseId))) {
      courseSelect.value = String(oldCourseId);
      const found = filteredCourses.find(c => String(c.id) === String(oldCourseId));
      if (found) courseSearchDisplay.value = found.name;
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

  // ── Field format validators ──────────────────────────────────────
  function showFieldError(input, msg) {
    input.style.borderColor = 'var(--danger)';
    const errEl = document.getElementById(input.name + '-error');
    if (errEl) { errEl.textContent = msg; errEl.style.display = 'block'; }
    input.setCustomValidity(msg);
  }
  function clearFieldError(input) {
    input.style.borderColor = '';
    const errEl = document.getElementById(input.name + '-error');
    if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; }
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

  if (mobileInput) {
    mobileInput.addEventListener('input', () => {
      mobileInput.value = mobileInput.value.replace(/\D/g, '').slice(0, 10);
      validateMobileField(mobileInput);
      mobileInput.setCustomValidity(''); debounceUniqueField(mobileInput, 'mobile');
    });
    mobileInput.addEventListener('blur', () => { validateMobileField(mobileInput); validateUniqueField(mobileInput, 'mobile'); });
  }
  if (emailInput) {
    emailInput.addEventListener('input', () => { validateEmailField(emailInput); emailInput.setCustomValidity(''); debounceUniqueField(emailInput, 'email'); });
    emailInput.addEventListener('blur', () => { validateEmailField(emailInput); validateUniqueField(emailInput, 'email'); });
  }
  document.querySelectorAll('input[name="guardian_mobile"], input[name="whatsapp_no"], input[name="alternate_mobile"]').forEach(inp => {
    inp.addEventListener('input', () => { inp.value = inp.value.replace(/\D/g, '').slice(0, 10); validateMobileField(inp); });
    inp.addEventListener('blur', () => validateMobileField(inp));
  });
  document.querySelectorAll('input[name="aadhar_no"]').forEach(inp => {
    inp.addEventListener('input', () => { inp.value = inp.value.replace(/\D/g, '').slice(0, 12); validateAadharField(inp); });
    inp.addEventListener('blur', () => validateAadharField(inp));
  });
  // ── Fee Breakdown ────────────────────────────────────────────────
  const feeBox   = document.getElementById('fee-breakdown-box');
  const feeRows  = document.getElementById('fee-breakdown-rows');
  const feeTotal = document.getElementById('fee-breakdown-total');

  function renderFeeBreakdown(courseId) {
    const course = courseCatalog.find(c => String(c.id) === String(courseId));
    if (!course || !course.fee_items || course.fee_items.length === 0) {
      feeBox.style.display = 'none'; return;
    }
    feeRows.innerHTML = course.fee_items.map(item => `
      <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;font-size:13px;">
        <span style="color:var(--text-2);">${item.fee_type_name}</span>
        <span style="font-weight:600;">₹${item.amount.toLocaleString('en-IN')}</span>
      </div>`).join('');
    feeTotal.textContent = '₹' + Number(course.total_fee).toLocaleString('en-IN');
    feeBox.style.display = 'block';
  }

  const courseSelect = document.getElementById('course_id');
  courseSelect.addEventListener('change', () => renderFeeBreakdown(courseSelect.value));

  renderDurationOptions();
  if (oldCourse?.duration) {
    durationSelect.value = String(oldCourse.duration);
  }
  renderCourseOptions();
  if (oldCourseId) renderFeeBreakdown(oldCourseId);
  syncAdmissionSource();

  const quickForm = document.querySelector('form');
  quickForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const mobileFormatOk = validateMobileField(mobileInput);
    const emailFormatOk  = validateEmailField(emailInput);
    if (!mobileFormatOk || !emailFormatOk) {
      if (!mobileFormatOk) mobileInput?.reportValidity();
      else emailInput?.reportValidity();
      return;
    }
    const mobileOk = await validateUniqueField(mobileInput, 'mobile');
    const emailOk  = await validateUniqueField(emailInput, 'email');
    if (!mobileOk || !emailOk) return;
    if (!quickForm.reportValidity()) return;
    quickForm.submit();
  });
})();
</script>
@endpush

