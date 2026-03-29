@extends('layouts.institute')
@section('title','Course Types')
@section('page-title','Course Types')
@section('topbar-actions')
  <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm">+ Add Course</a>
@endsection
@section('content')
<div class="gt-form-grid-2">
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Add Course Type</div>
    </div>
    <form method="POST" action="{{ route('institute.course-types.store') }}">
      @csrf
      <div class="gt-form-group">
        <label class="gt-label">Type Name <span style="color:var(--danger)">*</span></label>
        <input type="text" name="name" class="gt-input" value="{{ old('name') }}" placeholder="e.g. Diploma, Certificate" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Status</label>
        <select name="status" class="gt-select">
          <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Save Type</button>
    </form>
  </div>

  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Available Types</div>
    </div>
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead><tr><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          @forelse($courseTypes as $type)
          <tr>
            <td>{{ $type->name }}</td>
            <td><span class="badge {{ $type->status === 'active' ? 'badge-success' : 'badge-neutral' }}">{{ ucfirst($type->status) }}</span></td>
            <td>
              <div class="flex gap-2">
                <a href="{{ route('institute.course-types.edit', $type) }}" class="btn btn-outline btn-xs">Edit</a>
                <form action="{{ route('institute.course-types.destroy', $type) }}" method="POST">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-xs" data-confirm="Delete course type '{{ $type->name }}'?">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-muted">No course types added yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
