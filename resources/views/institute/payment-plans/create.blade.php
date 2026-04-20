@extends('layouts.institute')
@section('title','Add Payment Plan')
@section('page-title','Add Payment Plan')
@section('topbar-actions')
  <a href="{{ route('institute.payment-plans.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="gt-card" style="max-width:560px;">
  <form method="POST" action="{{ route('institute.payment-plans.store') }}">
    @csrf

    <div class="gt-form-group">
      <label class="gt-label">Plan Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
        value="{{ old('name') }}" placeholder="e.g. Monthly Plan" required autofocus>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Plan Type <span style="color:var(--danger)">*</span></label>
      <select name="type" class="gt-select @error('type') is-invalid @enderror" required>
        <option value="">Select Type</option>
        <option value="OTP" {{ old('type') === 'OTP' ? 'selected' : '' }}>OTP</option>
        <option value="MONTHLY" {{ old('type') === 'MONTHLY' ? 'selected' : '' }}>MONTHLY</option>
        <option value="PART" {{ old('type') === 'PART' ? 'selected' : '' }}>PART</option>
      </select>
      @error('type')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <div class="gt-grid-2" style="gap:16px;">
      <div class="gt-form-group">
        <label class="gt-label">Grace Days <span style="color:var(--danger)">*</span></label>
        <input type="number" name="grace_days" class="gt-input @error('grace_days') is-invalid @enderror"
          value="{{ old('grace_days', 0) }}" min="0" required>
        @error('grace_days')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Late Fee Per Day <span style="color:var(--danger)">*</span></label>
        <input type="number" name="late_fee_per_day" class="gt-input @error('late_fee_per_day') is-invalid @enderror"
          value="{{ old('late_fee_per_day', 0) }}" min="0" step="0.01" required>
        @error('late_fee_per_day')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Save Payment Plan</button>
  </form>
</div>
@endsection
