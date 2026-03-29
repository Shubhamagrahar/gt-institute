@extends('layouts.institute')
@section('title','Edit Subject')
@section('page-title','Edit Subject')
@section('topbar-actions')
  <a href="{{ route('institute.subjects.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="gt-card" style="max-width:540px;">
  <div class="gt-card-header">
    <div class="gt-card-title">Edit Subject</div>
  </div>

  <form method="POST" action="{{ route('institute.subjects.update', $subject) }}">
    @csrf @method('PUT')

    <div class="gt-form-group">
      <label class="gt-label">Subject Code</label>
      <input type="text" name="subject_code" class="gt-input" value="{{ old('subject_code', $subject->subject_code) }}" placeholder="e.g. CS101">
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Subject Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
        value="{{ old('name', $subject->name) }}" required>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Status</label>
      <select name="status" class="gt-select">
        <option value="active" {{ $subject->status === 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ $subject->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Update Subject</button>
  </form>
</div>
@endsection