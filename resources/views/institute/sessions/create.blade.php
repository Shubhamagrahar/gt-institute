@extends('layouts.institute')
@section('title','Create Session')
@section('page-title','Create Session')

@section('content')
<div style="max-width:500px;margin:0 auto;margin-top:40px;">
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Create Your First Session
      </div>
    </div>

    @if(session('error'))
      <div class="gt-alert gt-alert-error" style="margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    <p class="text-sm text-muted" style="margin-bottom:20px;line-height:1.7;">
      Session is the time period for which you manage students, fees, and attendance.
      Example: <strong>JAN-JUNE (2025-26)</strong> or <strong>2025-26</strong>.
      You need at least one active session to use the software.
    </p>

    <form method="POST" action="{{ route('institute.sessions.store') }}">
      @csrf

      <div class="gt-form-group">
        <label class="gt-label">Session Name <span style="color:var(--danger)">*</span></label>
        <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
          value="{{ old('name') }}"
          placeholder="e.g. JAN-JUNE (2025-26)"
          required autofocus>
        @error('name')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-grid-2">
        <div class="gt-form-group">
          <label class="gt-label">Start Date <span style="color:var(--danger)">*</span></label>
          <input type="date" name="start_date" class="gt-input @error('start_date') is-invalid @enderror"
            value="{{ old('start_date') }}" required>
          @error('start_date')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
        <div class="gt-form-group">
          <label class="gt-label">End Date <span style="color:var(--danger)">*</span></label>
          <input type="date" name="end_date" class="gt-input @error('end_date') is-invalid @enderror"
            value="{{ old('end_date') }}" required>
          @error('end_date')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;margin-top:8px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        Create Session & Start
      </button>
    </form>
  </div>
</div>
@endsection