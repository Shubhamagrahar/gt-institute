@extends('layouts.institute')
@section('title','Link Subjects to Courses')
@section('page-title','Course - Subject Binding')
@section('topbar-actions')
  <a href="{{ route('institute.subjects.index') }}" class="btn btn-outline btn-sm">Subjects</a>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const courseSelect = document.querySelector('select[name="course_id"]');
  const subjectSelect = document.querySelector('select[name="subject_id"]');
  const boundMap = @json($boundSubjectMap ?? []);

  function filterSubjects() {
    if (!courseSelect || !subjectSelect) return;

    const selectedCourseId = courseSelect.value;
    const blockedSubjects = new Set((boundMap[selectedCourseId] || []).map(String));

    Array.from(subjectSelect.options).forEach((option, index) => {
      if (index === 0) {
        option.hidden = false;
        option.disabled = false;
        return;
      }

      const shouldHide = blockedSubjects.has(option.value);
      option.hidden = shouldHide;
      option.disabled = shouldHide;

      if (shouldHide && option.selected) {
        subjectSelect.value = '';
      }
    });
  }

  courseSelect?.addEventListener('change', filterSubjects);
  filterSubjects();

  document.querySelectorAll('[data-binding-remove]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const label = this.dataset.bindingLabel || 'this binding';

      Swal.fire({
        title: 'Remove linked subject?',
        text: `Do you want to remove ${label}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#6c5dd3',
        cancelButtonColor: '#94a3b8',
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush

@section('content')
<div class="gt-grid-2" style="gap:20px;align-items:start;">

  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Link Subject to Course</div>
    </div>

    <form method="POST" action="{{ route('institute.subjects.bind.store') }}">
      @csrf

      <div class="gt-form-group">
        <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
        <select name="course_id" class="gt-select @error('course_id') is-invalid @enderror" required>
          <option value="">-- Select Course --</option>
          @foreach($courses as $course)
            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
              {{ $course->name }}
            </option>
          @endforeach
        </select>
        @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Subject <span style="color:var(--danger)">*</span></label>
        <select name="subject_id" class="gt-select @error('subject_id') is-invalid @enderror" required>
          <option value="">-- Select Subject --</option>
          @foreach($subjects as $subject)
            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
              {{ $subject->name }} {{ $subject->subject_code ? '('.$subject->subject_code.')' : '' }}
            </option>
          @endforeach
        </select>
        @error('subject_id')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Maximum Marks <span style="color:var(--danger)">*</span></label>
        <input type="number" name="max_marks" class="gt-input @error('max_marks') is-invalid @enderror"
          value="{{ old('max_marks', 100) }}" min="1" max="1000" required>
        @error('max_marks')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">Link Subject</button>
    </form>
  </div>

  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Linked Subjects</div>
      <span class="text-xs text-muted">{{ $bindings->count() }} total</span>
    </div>

    @if($bindings->isEmpty())
      <div class="gt-empty" style="padding:24px 0;">
        <div class="gt-empty-icon">Links</div>
        <div class="gt-empty-title">No bindings yet</div>
        <div class="gt-empty-sub">Link a subject to a course from the form</div>
      </div>
    @else
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead>
          <tr>
            <th>Course</th>
            <th>Subject</th>
            <th>Max Marks</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($bindings as $b)
          <tr>
            <td class="fw-600" style="font-size:12.5px;">{{ $b->course->name }}</td>
            <td>
              <div style="font-size:12.5px;">{{ $b->subject->name }}</div>
              @if($b->subject->subject_code)
                <div class="text-xs text-muted mono">{{ $b->subject->subject_code }}</div>
              @endif
            </td>
            <td><span class="badge badge-accent">{{ $b->max_marks }}</span></td>
            <td>
              <form
                method="POST"
                action="{{ route('institute.subjects.bind.destroy', $b) }}"
                data-binding-remove
                data-binding-label="{{ $b->subject->name }} from {{ $b->course->name }}"
              >
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-xs">Remove</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>
@endsection
