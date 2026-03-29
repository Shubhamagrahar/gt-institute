@extends('layouts.institute')
@section('title','Subjects')
@section('page-title','Subjects')
@section('topbar-actions')
  <a href="{{ route('institute.subjects.create') }}" class="btn btn-primary btn-sm">+ Add Subject</a>
@endsection

@push('styles')
<style>
.subject-cta-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 18px 20px;
  margin-bottom: 16px;
  border-radius: 18px;
  background: linear-gradient(135deg, rgba(108,93,211,.10), rgba(59,130,246,.08));
  border: 1px solid rgba(108,93,211,.14);
}

.subject-cta-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--text);
}

.subject-cta-sub {
  font-size: 12px;
  color: var(--text-2);
  margin-top: 4px;
  max-width: 640px;
}

.subject-toggle-form { margin: 0; }

.subject-switch {
  position: relative;
  display: inline-flex;
  width: 52px;
  height: 30px;
  border: none;
  background: transparent;
  padding: 0;
  cursor: pointer;
}

.subject-switch-track {
  width: 52px;
  height: 30px;
  border-radius: 999px;
  background: #dbe4f0;
  transition: all .2s ease;
  box-shadow: inset 0 0 0 1px rgba(15,23,42,.06);
}

.subject-switch-thumb {
  position: absolute;
  top: 3px;
  left: 3px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 2px 8px rgba(15,23,42,.15);
  transition: all .2s ease;
}

.subject-switch.is-active .subject-switch-track {
  background: rgb(134 92 240);
}

.subject-switch.is-active .subject-switch-thumb {
  left: 25px;
}

.subject-status-text {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-2);
  margin-top: 6px;
}

@media (max-width: 768px) {
  .subject-cta-banner {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-subject-toggle]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const subjectName = this.dataset.subjectName || 'this subject';
      const nextStatus = this.dataset.nextStatus || 'inactive';

      Swal.fire({
        title: 'Change subject status?',
        text: `Do you want to mark ${subjectName} as ${nextStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it',
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
<div class="subject-cta-banner">
  <div>
    <div class="subject-cta-title">Important: Link Subjects To Courses</div>
    <div class="subject-cta-sub">After creating subjects, it is necessary to bind them to a course; only then will proper mapping be established and they will appear correctly in the workflow.</div>
  </div>
  <a href="{{ route('institute.subjects.bind') }}" class="btn btn-primary">Link To Course</a>
</div>

<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Subjects</div>
    <span class="text-xs text-muted">{{ $subjects->count() }} total</span>
  </div>

  @if($subjects->isEmpty())
    <div class="gt-empty">
      <div class="gt-empty-icon">Subjects</div>
      <div class="gt-empty-title">No subjects yet</div>
      <a href="{{ route('institute.subjects.create') }}" class="btn btn-primary btn-sm" style="margin-top:8px;">Add First Subject</a>
    </div>
  @else
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Code</th>
          <th>Subject Name</th>
          <th>Created</th>
          <th>Actions</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($subjects as $i => $s)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td><span class="mono text-xs">{{ $s->subject_code ?? '—' }}</span></td>
          <td class="fw-600">{{ $s->name }}</td>
          <td class="text-xs text-muted">{{ $s->created_at?->format('d M Y') }}</td>
          <td>
            <a href="{{ route('institute.subjects.edit', $s) }}" class="btn btn-outline btn-xs">Edit</a>
          </td>
          <td>
            <form
              method="POST"
              action="{{ route('institute.subjects.toggle', $s) }}"
              class="subject-toggle-form"
              data-subject-toggle
              data-subject-name="{{ $s->name }}"
              data-next-status="{{ $s->status === 'active' ? 'inactive' : 'active' }}"
            >
              @csrf
              @method('PATCH')
              <button type="submit" class="subject-switch {{ $s->status === 'active' ? 'is-active' : '' }}" aria-label="Toggle status for {{ $s->name }}">
                <span class="subject-switch-track"></span>
                <span class="subject-switch-thumb"></span>
              </button>
              <div class="subject-status-text">{{ $s->status === 'active' ? 'Active' : 'Inactive' }}</div>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
