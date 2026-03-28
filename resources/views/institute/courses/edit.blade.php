@extends('layouts.institute')
@section('title', isset($course) ? 'Edit Course' : 'Add Course')
@section('page-title', isset($course) ? 'Edit Course' : 'Add Course')
@section('topbar-actions')
  <a href="{{ route('institute.courses.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-card" style="max-width:600px;">
  <form method="POST" action="{{ isset($course) ? route('institute.courses.update',$course) : route('institute.courses.store') }}">
    @csrf @if(isset($course)) @method('PUT') @endif

    <div class="gt-form-group">
      <label class="gt-label">Course Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input" value="{{ old('name',$course->name??'') }}" required>
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Short Name</label>
        <input type="text" name="short_name" class="gt-input" value="{{ old('short_name',$course->short_name??'') }}" placeholder="e.g. DCA">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Course Type</label>
        <select name="course_type_id" class="gt-select">
          <option value="">— None —</option>
          @foreach($types as $t)
          <option value="{{ $t->id }}" {{ old('course_type_id',$course->course_type_id??'') == $t->id ? 'selected':'' }}>{{ $t->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Duration (months) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="duration_months" class="gt-input" value="{{ old('duration_months',$course->duration_months??6) }}" min="1" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Fee (₹) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="fee" class="gt-input" value="{{ old('fee',$course->fee??0) }}" min="0" step="0.01" required>
      </div>
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Description</label>
      <textarea name="description" class="gt-textarea">{{ old('description',$course->description??'') }}</textarea>
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Status</label>
      <select name="status" class="gt-select">
        <option value="active"   {{ old('status',$course->status??'active')==='active'  ?'selected':'' }}>Active</option>
        <option value="inactive" {{ old('status',$course->status??'')==='inactive'?'selected':'' }}>Inactive</option>
      </select>
    </div>
    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">{{ isset($course)?'Update Course':'Add Course' }}</button>
      <a href="{{ route('institute.courses.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
