@extends('layouts.institute')
@section('title','Edit Payment Plan')
@section('page-title','Edit Payment Plan')
@section('topbar-actions')
  <a href="{{ route('institute.payment-plans.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="gt-card" style="max-width:560px;">
  <form method="POST" action="{{ route('institute.payment-plans.update', $paymentPlan) }}">
    @csrf
    @method('PUT')

    <div class="gt-form-group">
      <label class="gt-label">Plan Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
        value="{{ old('name', $paymentPlan->name) }}" required autofocus>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Plan Type <span style="color:var(--danger)">*</span></label>
      <select name="type" class="gt-select @error('type') is-invalid @enderror" required>
        <option value="">Select Type</option>
        <option value="OTP" {{ old('type', $paymentPlan->type) === 'OTP' ? 'selected' : '' }}>OTP</option>
        <option value="MONTHLY" {{ old('type', $paymentPlan->type) === 'MONTHLY' ? 'selected' : '' }}>MONTHLY</option>
        <option value="PART" {{ old('type', $paymentPlan->type) === 'PART' ? 'selected' : '' }}>PART</option>
      </select>
      @error('type')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <div class="gt-grid-2" style="gap:16px;">
      <div class="gt-form-group">
        <label class="gt-label">Grace Days <span style="color:var(--danger)">*</span></label>
        <input type="number" name="grace_days" class="gt-input @error('grace_days') is-invalid @enderror"
          value="{{ old('grace_days', $paymentPlan->grace_days) }}" min="0" required>
        @error('grace_days')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Late Fee Per Day <span style="color:var(--danger)">*</span></label>
        <input type="number" name="late_fee_per_day" class="gt-input @error('late_fee_per_day') is-invalid @enderror"
          value="{{ old('late_fee_per_day', $paymentPlan->late_fee_per_day) }}" min="0" step="0.01" required>
        @error('late_fee_per_day')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Update Payment Plan</button>
  </form>
</div>
@endsection
