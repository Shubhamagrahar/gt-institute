@extends('layouts.institute')
@section('title','Edit Staff')
@section('page-title','Edit Staff')
@section('topbar-actions')
  <a href="{{ route('institute.staff.show',$staff) }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-card" style="max-width:600px;">
  <form method="POST" action="{{ route('institute.staff.update',$staff) }}">
    @csrf @method('PUT')
    @php $sp = $staff->staffProfile; @endphp
    <div class="gt-form-group">
      <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input" value="{{ old('name',$staff->name) }}" required>
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Designation</label>
        <input type="text" name="designation" class="gt-input" value="{{ old('designation',$sp?->designation) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Salary (₹)</label>
        <input type="number" name="salary" class="gt-input" value="{{ old('salary',$sp?->salary,0) }}" min="0" step="0.01">
      </div>
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Joining Date</label>
      <input type="date" name="joining_date" class="gt-input" value="{{ old('joining_date',$sp?->joining_date) }}">
    </div>
    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Update Staff</button>
      <a href="{{ route('institute.staff.show',$staff) }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
