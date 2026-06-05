@extends('layouts.institute')
@section('title','Edit Course Booking')
@section('page-title','Edit Course Booking')
@section('topbar-actions')
  <a href="{{ route('institute.students.show', $student) }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@section('content')
<div class="gt-card" style="max-width:860px;">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">{{ $student->profile?->name ?? $student->user_id }}</div>
      <div class="text-xs text-muted" style="margin-top:4px;">Course ya batch galat save hua ho to yahan se correct kar sakte ho.</div>
    </div>
  </div>

  <form method="POST" action="{{ route('institute.students.enrollments.update', [$student, $courseBook]) }}">
    @csrf
    @method('PUT')

    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
        <select name="course_id" class="gt-select" required>
          <option value="">Select Course</option>
          @foreach($courses as $course)
            <option value="{{ $course->id }}" {{ old('course_id', $courseBook->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
          @endforeach
        </select>
        @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Batch</label>
        <select name="batch_id" class="gt-select">
          <option value="">No Batch</option>
          @foreach($batches as $batch)
            <option value="{{ $batch->id }}" {{ old('batch_id', $courseBook->batch_id) == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
          @endforeach
        </select>
        @error('batch_id')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Payment Plan <span style="color:var(--danger)">*</span></label>
        <select name="payment_plan_type_id" class="gt-select" required>
          <option value="">Select Plan</option>
          @foreach($plans as $plan)
            <option value="{{ $plan->id }}" {{ old('payment_plan_type_id', $currentPlanId) == $plan->id ? 'selected' : '' }}>
              {{ $plan->name }} ({{ $plan->type }})
            </option>
          @endforeach
        </select>
        @error('payment_plan_type_id')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="gt-alert gt-alert-error" style="margin-top:16px;">
      Same student ka same course dobara active nahi ho sakta. Aur agar student kisi aur active booking me hai to same batch repeat bhi allowed nahi hai.
    </div>

    <div style="display:flex;gap:10px;margin-top:16px;">
      <button type="submit" class="btn btn-primary">Update Booking</button>
      <a href="{{ route('institute.students.show', $student) }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
