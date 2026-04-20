@extends('layouts.institute')

@section('title', 'Change Password')

@section('content')
<div class="account-form-wrap">
  <div class="gt-alert gt-alert-warning">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div>Use a new unique password. Add uppercase, lowercase, number, and special character, and avoid reusing your old/common password.</div>
  </div>

  <div class="gt-card account-form-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Change Password</div>
        <div class="text-sm text-muted">Old password, new password, confirm password, and live strength check in one place.</div>
      </div>
    </div>

    <form method="POST" action="{{ route('institute.accounts.password.update') }}" id="change-password-form">
      @csrf

      <div class="gt-form-group">
        <label class="gt-label" for="old_password">Old Password</label>
        <input type="password" name="old_password" id="old_password" class="gt-input @error('old_password') is-invalid @enderror" required>
        @error('old_password')
          <div class="gt-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label" for="new_password">New Password</label>
        <input type="password" name="new_password" id="new_password" class="gt-input @error('new_password') is-invalid @enderror" required>
        @error('new_password')
          <div class="gt-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label" for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" class="gt-input @error('confirm_password') is-invalid @enderror" required>
        @error('confirm_password')
          <div class="gt-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="password-strength-box">
        <div class="password-strength-head">
          <span>Password Strength</span>
          <span id="password-strength-text">Too weak</span>
        </div>
        <div class="password-strength-meter">
          <span id="password-strength-fill"></span>
        </div>
        <div class="password-rule-list">
          <div data-rule="length">At least 8 characters</div>
          <div data-rule="uppercase">At least 1 uppercase letter</div>
          <div data-rule="lowercase">At least 1 lowercase letter</div>
          <div data-rule="number">At least 1 number</div>
          <div data-rule="special">At least 1 special character</div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="margin-top:14px;">Update Password</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const newPasswordInput = document.getElementById('new_password');
  const confirmPasswordInput = document.getElementById('confirm_password');
  const strengthText = document.getElementById('password-strength-text');
  const strengthFill = document.getElementById('password-strength-fill');
  const rules = {
    length: document.querySelector('[data-rule="length"]'),
    uppercase: document.querySelector('[data-rule="uppercase"]'),
    lowercase: document.querySelector('[data-rule="lowercase"]'),
    number: document.querySelector('[data-rule="number"]'),
    special: document.querySelector('[data-rule="special"]')
  };

  function updateRuleState(element, passed) {
    element.classList.toggle('passed', passed);
  }

  function calculateStrength(password) {
    const checks = {
      length: password.length >= 8,
      uppercase: /[A-Z]/.test(password),
      lowercase: /[a-z]/.test(password),
      number: /[0-9]/.test(password),
      special: /[^A-Za-z0-9]/.test(password)
    };

    Object.entries(checks).forEach(([key, passed]) => updateRuleState(rules[key], passed));

    const score = Object.values(checks).filter(Boolean).length;
    let label = 'Too weak';
    let width = '12%';
    let color = '#ef4444';

    if (score >= 5) {
      label = 'Very strong';
      width = '100%';
      color = '#10b981';
    } else if (score === 4) {
      label = 'Strong';
      width = '80%';
      color = '#22c55e';
    } else if (score === 3) {
      label = 'Medium';
      width = '60%';
      color = '#f59e0b';
    } else if (score === 2) {
      label = 'Weak';
      width = '40%';
      color = '#f97316';
    }

    if (!password.length) {
      label = 'Too weak';
      width = '0%';
      color = '#d1d5db';
    }

    strengthText.textContent = label;
    strengthFill.style.width = width;
    strengthFill.style.background = color;
  }

  function syncConfirmPattern() {
    if (!confirmPasswordInput.value) {
      confirmPasswordInput.setCustomValidity('');
      return;
    }

    if (confirmPasswordInput.value !== newPasswordInput.value) {
      confirmPasswordInput.setCustomValidity('Confirm password must match the new password.');
    } else {
      confirmPasswordInput.setCustomValidity('');
    }
  }

  newPasswordInput?.addEventListener('input', function () {
    calculateStrength(this.value);
    syncConfirmPattern();
  });

  confirmPasswordInput?.addEventListener('input', syncConfirmPattern);
  calculateStrength(newPasswordInput?.value || '');
</script>
@endpush
