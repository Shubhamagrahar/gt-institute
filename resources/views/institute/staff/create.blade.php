@extends('layouts.institute')
@section('title','Add Staff')
@section('page-title','Add Staff Member')
@section('topbar-actions')
  <a href="{{ route('institute.staff.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-card" style="max-width:600px;">
  <form method="POST" action="{{ route('institute.staff.store') }}">
    @csrf
    <div class="gt-form-group">
      <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
        <input type="text" name="mobile" class="gt-input @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" required>
        @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Email</label>
        <input type="email" name="email" class="gt-input" value="{{ old('email') }}">
      </div>
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Designation</label>
        <input type="text" name="designation" class="gt-input" value="{{ old('designation') }}" placeholder="e.g. Computer Instructor">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Salary (₹)</label>
        <input type="number" name="salary" class="gt-input" value="{{ old('salary',0) }}" min="0" step="0.01">
      </div>
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Joining Date</label>
      <input type="date" name="joining_date" class="gt-input" value="{{ old('joining_date',date('Y-m-d')) }}">
    </div>
    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Add Staff Member</button>
      <a href="{{ route('institute.staff.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
