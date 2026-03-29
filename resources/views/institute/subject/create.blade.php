@extends('layouts.institute')
@section('title','Add Subject')
@section('page-title','Add Subject')
@section('topbar-actions')
  <a href="{{ route('institute.subjects.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="gt-card" style="max-width:540px;">
  <div class="gt-card-header">
    <div class="gt-card-title">New Subject</div>
  </div>

  <form method="POST" action="{{ route('institute.subjects.store') }}">
    @csrf

    <div class="gt-form-group">
      <label class="gt-label">Subject Code <span class="text-muted text-xs">(optional)</span></label>
      <input type="text" name="subject_code" class="gt-input" value="{{ old('subject_code') }}" placeholder="e.g. CS101, MATH01">
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Subject Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
        value="{{ old('name') }}" placeholder="e.g. Computer Fundamentals" required autofocus>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Save Subject</button>
  </form>
</div>
@endsection
