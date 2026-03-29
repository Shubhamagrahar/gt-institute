@extends('layouts.institute')
@section('title','Edit Course Type')
@section('page-title','Edit Course Type')
@section('topbar-actions')
  <a href="{{ route('institute.course-types.index') }}" class="btn btn-outline btn-sm">Back</a>
@endsection
@section('content')
<div class="gt-card" style="max-width:600px;">
  <form method="POST" action="{{ route('institute.course-types.update', $courseType) }}">
    @csrf
    @method('PUT')
    <div class="gt-form-group">
      <label class="gt-label">Type Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input" value="{{ old('name', $courseType->name) }}" required>
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Status</label>
      <select name="status" class="gt-select">
        <option value="active" {{ old('status', $courseType->status) === 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ old('status', $courseType->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>
    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Update Type</button>
      <a href="{{ route('institute.course-types.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
