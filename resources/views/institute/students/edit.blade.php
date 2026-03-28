@extends('layouts.institute')
@section('title','Edit Student')
@section('page-title','Edit Student')
@section('topbar-actions')
  <a href="{{ route('institute.students.show',$student) }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-card" style="max-width:700px;">
  <form method="POST" action="{{ route('institute.students.update',$student) }}">
    @csrf @method('PUT')
    @php $sp = $student->studentProfile; @endphp

    <div class="gt-form-group">
      <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input" value="{{ old('name',$student->name) }}" required>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Father's Name</label>
        <input type="text" name="father_name" class="gt-input" value="{{ old('father_name',$sp?->father_name) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Father's Mobile</label>
        <input type="text" name="father_mobile" class="gt-input" value="{{ old('father_mobile',$sp?->father_mobile) }}">
      </div>
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Mother's Name</label>
      <input type="text" name="mother_name" class="gt-input" value="{{ old('mother_name',$sp?->mother_name) }}">
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Date of Birth</label>
        <input type="date" name="dob" class="gt-input" value="{{ old('dob',$sp?->dob) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Gender</label>
        <select name="gender" class="gt-select">
          @foreach(['Male','Female','Other'] as $g)
          <option value="{{ $g }}" {{ old('gender',$sp?->gender)===$g?'selected':'' }}>{{ $g }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Qualification</label>
        <input type="text" name="qualification" class="gt-input" value="{{ old('qualification',$sp?->qualification) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">State</label>
        <input type="text" name="state" class="gt-input" value="{{ old('state',$sp?->state) }}">
      </div>
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Address</label>
      <textarea name="full_add" class="gt-textarea">{{ old('full_add',$sp?->full_add) }}</textarea>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Fee Type</label>
        <select name="fee_collect_type" class="gt-select">
          @foreach(['OTP','MONTHLY','PART'] as $ft)
          <option value="{{ $ft }}" {{ old('fee_collect_type',$sp?->fee_collect_type)===$ft?'selected':'' }}>{{ $ft }}</option>
          @endforeach
        </select>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Monthly Fee (₹)</label>
        <input type="number" name="monthly_fee" class="gt-input" value="{{ old('monthly_fee',$sp?->monthly_fee,0) }}" min="0" step="0.01">
      </div>
    </div>

    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Update Student</button>
      <a href="{{ route('institute.students.show',$student) }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
