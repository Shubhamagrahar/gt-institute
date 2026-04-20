@extends('layouts.institute')
@section('title','New Admission')
@section('page-title','New Admission')

@section('content')
<div style="max-width:600px;margin:40px auto;">
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Choose Admission Type</div>
    </div>

    <div class="gt-grid-2" style="gap:14px;">
      <a href="{{ route('institute.enrollment.new') }}"
        style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px;background:var(--accent-bg);border:2px solid var(--accent);border-radius:10px;text-decoration:none;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="8.5" cy="7" r="4"/>
          <line x1="20" y1="8" x2="20" y2="14"/>
          <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        <div style="font-size:14px;font-weight:700;color:var(--accent);">New Admission</div>
        <div class="text-xs text-muted" style="text-align:center;">Create a new student and continue admission</div>
      </a>

      <button type="button" id="existing-student-toggle"
        style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px;background:var(--bg-3);border:2px solid var(--border);border-radius:10px;width:100%;cursor:pointer;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
        </svg>
        <div style="font-size:14px;font-weight:700;color:var(--text);">Existing Student</div>
        <div class="text-xs text-muted" style="text-align:center;">Search existing student by mobile or student ID</div>
      </button>
    </div>

    <div id="existing-student-search-box" style="display:none;margin-top:16px;padding:18px;background:var(--bg-3);border:1px solid var(--border);border-radius:10px;">
      <div style="font-size:14px;font-weight:700;margin-bottom:10px;">Search Existing Student</div>
      <form method="POST" action="{{ route('institute.enrollment.find-student') }}">
        @csrf
        <div style="display:flex;gap:8px;">
          <input type="text" name="search" class="gt-input" placeholder="Mobile or Student ID" required value="{{ old('search') }}">
          <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap;">Search</button>
        </div>
        @error('search')<div class="gt-error">{{ $message }}</div>@enderror
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const existingStudentToggle = document.getElementById('existing-student-toggle');
const existingStudentSearchBox = document.getElementById('existing-student-search-box');

existingStudentToggle?.addEventListener('click', function () {
  if (!existingStudentSearchBox) return;
  existingStudentSearchBox.style.display =
    existingStudentSearchBox.style.display === 'none' ? 'block' : 'none';
});

@if($errors->has('search'))
if (existingStudentSearchBox) {
  existingStudentSearchBox.style.display = 'block';
}
@endif
</script>
@endpush
