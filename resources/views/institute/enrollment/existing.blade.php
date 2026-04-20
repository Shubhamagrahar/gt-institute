@extends('layouts.institute')
@section('title','Existing Student')
@section('page-title','Existing Student Enrollment')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="gt-card" style="max-width:640px;">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">{{ $user->profile?->name ?? $user->user_id }}</div>
      <div class="text-xs text-muted" style="margin-top:4px;">{{ $user->user_id }} · {{ $user->mobile }}</div>
    </div>
  </div>

  <form method="POST" action="{{ route('institute.enrollment.store-new') }}">
    @csrf
    <input type="hidden" name="user_id" value="{{ $user->id }}">

    <div class="gt-form-group">
      <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
      <select name="course_id" class="gt-select @error('course_id') is-invalid @enderror" required>
        <option value="">Select Course</option>
        @foreach($courses as $course)
          <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
        @endforeach
      </select>
      @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Batch</label>
      <select name="batch_id" class="gt-select @error('batch_id') is-invalid @enderror">
        <option value="">No Batch</option>
        @foreach($batches as $batch)
          <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
        @endforeach
      </select>
      @error('batch_id')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Continue → Fill Profile</button>
  </form>
</div>
@endsection
