@extends('layouts.institute')
@section('title','Add Fee Type')
@section('page-title','Add Fee Type')
@section('topbar-actions')
  <a href="{{ route('institute.fee-types.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-card" style="max-width:480px;">
  <form method="POST" action="{{ route('institute.fee-types.store') }}">
    @csrf
    <div class="gt-form-group">
      <label class="gt-label">Fee Type Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
        value="{{ old('name') }}" placeholder="e.g. Registration Fee, Practical Fee" required autofocus>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div class="gt-form-group">
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
        <input type="checkbox" name="is_mandatory" value="1" style="accent-color:var(--accent);">
        <span class="gt-label" style="margin:0;">Mark as Mandatory</span>
      </label>
    </div>
    <button type="submit" class="btn btn-primary">Save Fee Type</button>
  </form>
</div>
@endsection