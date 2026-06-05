@extends('layouts.institute')
@section('title','Quick Booking')
@section('page-title','Quick Seat Booking')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@section('content')
@php
  $quickFields = $savedFields->filter(fn ($field) => $field->quick_is_active);
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

  <form method="POST" action="{{ route('institute.enrollment.store-quick') }}" class="gt-card">
    @csrf
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
              <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Payment Plan <span style="color:var(--danger)">*</span></label>
          <select name="payment_plan_type_id" class="gt-select" required>
            <option value="">Select Plan</option>
            @foreach($plans as $plan)
              <option value="{{ $plan->id }}" {{ old('payment_plan_type_id') == $plan->id ? 'selected' : '' }}>
                {{ $plan->name }} ({{ $plan->type }})
              </option>
            @endforeach
          </select>
          @error('payment_plan_type_id')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="gt-form-grid-3">
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

        @if($quickFields->has('email'))
          <div class="gt-form-group">
            <label class="gt-label">Email @if($quickFields['email']->quick_is_required)<span style="color:var(--danger)">*</span>@endif</label>
            <input type="email" name="email" class="gt-input" value="{{ old('email') }}" {{ $quickFields['email']->quick_is_required ? 'required' : '' }}>
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
  const courseSelect = document.getElementById('course_id');
  const courseCatalog = @json($courseCatalog);
  const oldCourseId = @json(old('course_id'));
  const oldCourse = courseCatalog.find((course) => String(course.id) === String(oldCourseId));

  function renderCourseOptions() {
    const selectedTypeId = courseTypeSelect.value;
    const filteredCourses = courseCatalog.filter((course) => {
      if (!selectedTypeId) {
        return false;
      }

      return String(course.course_type_id || '') === String(selectedTypeId);
    });

    courseSelect.innerHTML = '<option value="">Select Course</option>' + filteredCourses.map((course) =>
      `<option value="${course.id}">${course.name} (${course.duration} month${course.duration === 1 ? '' : 's'})</option>`
    ).join('');

    if (filteredCourses.some((course) => String(course.id) === String(oldCourseId))) {
      courseSelect.value = String(oldCourseId);
    }
  }

  if (oldCourse?.course_type_id) {
    courseTypeSelect.value = String(oldCourse.course_type_id);
  }

  courseTypeSelect.addEventListener('change', renderCourseOptions);
  renderCourseOptions();
})();
</script>
@endpush
