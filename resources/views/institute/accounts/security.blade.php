@extends('layouts.institute')
@section('title', 'Security Settings')

@section('content')
<div class="account-form-wrap">

  @if(session('success'))
    <div class="gt-alert gt-alert-success" style="margin-bottom:20px;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><polyline points="20 6 9 17 4 12"/></svg>
      {{ session('success') }}
    </div>
  @endif

  {{-- Detect stale hashed value from old implementation --}}
  @php
    $hasValidOtp = $user->backup_otp && !str_starts_with($user->backup_otp, '$2y$');
  @endphp

  <div class="gt-card" style="max-width:560px;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:12px;padding-bottom:18px;border-bottom:1px solid rgba(255,255,255,.06);margin-bottom:22px;">
      <div style="width:40px;height:40px;border-radius:12px;background:rgba(99,102,241,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <div>
        <div style="font-size:15px;font-weight:600;color:#f1f5f9;">My Emergency OTP</div>
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-top:2px;">
          Email OTP na aaye to is OTP se login karo
        </div>
      </div>
    </div>

    @if($hasValidOtp)
      {{-- OTP display box --}}
      <div style="background:rgba(99,102,241,.07);border:1.5px solid rgba(99,102,241,.2);border-radius:16px;padding:24px 20px;text-align:center;margin-bottom:20px;">
        <div style="font-size:11px;letter-spacing:1.4px;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:14px;">
          Tumhara Emergency OTP
        </div>

        {{-- 6 individual digit tiles --}}
        <div style="display:flex;justify-content:center;gap:8px;margin-bottom:14px;">
          @foreach(str_split($user->backup_otp) as $digit)
            <div style="width:44px;height:54px;border-radius:10px;background:rgba(99,102,241,.15);border:1.5px solid rgba(99,102,241,.3);display:flex;align-items:center;justify-content:center;font-family:'Courier New',monospace;font-size:24px;font-weight:800;color:#a5b4fc;">
              {{ $digit }}
            </div>
          @endforeach
        </div>

        <div style="font-size:11px;color:rgba(255,255,255,.3);">
          Set kiya: {{ \Carbon\Carbon::parse($user->backup_otp_set_at)->format('d M Y, h:i A') }}
        </div>
      </div>

      {{-- How to use --}}
      <div style="background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.18);border-radius:12px;padding:13px 16px;font-size:12px;color:rgba(251,191,36,.8);line-height:1.7;margin-bottom:22px;">
        <strong>Use kaise karo:</strong> Login ke time OTP page aayega —
        wahan yahi 6-digit number type karo. Email OTP nahi aaya to bhi login ho jayega.
        Ye OTP sirf tumhare paas hona chahiye, <strong>kisi ko mat batao.</strong>
      </div>

    @else
      {{-- No OTP or stale hash --}}
      <div style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.18);border-radius:14px;padding:18px 20px;display:flex;gap:12px;align-items:flex-start;margin-bottom:22px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
          <div style="font-size:13px;font-weight:500;color:#fca5a5;margin-bottom:4px;">Emergency OTP set nahi hai</div>
          <div style="font-size:12px;color:rgba(255,255,255,.4);line-height:1.6;">
            Neeche password confirm karke apna personal emergency OTP generate karo.
            Ye wahi 6-digit number hoga jo OTP field me kaam karega jab email nahi aaye.
          </div>
        </div>
      </div>
    @endif

    {{-- Generate / Regenerate form --}}
    <form method="POST" action="{{ route('institute.accounts.backup-otp.generate') }}">
      @csrf

      <div class="gt-form-group">
        <label class="gt-label" for="password">Password Confirm Karo</label>
        <div class="login-input-wrap">
          <span class="input-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input
            type="password" name="password" id="password"
            class="gt-input @error('password') is-invalid @enderror"
            placeholder="Apna current password"
            autocomplete="current-password"
            style="padding-left:38px;"
          >
        </div>
        @error('password')
          <div class="gt-error" style="margin-top:6px;">{{ $message }}</div>
        @enderror
      </div>

      @if($hasValidOtp)
        <p style="font-size:12px;color:rgba(239,68,68,.65);margin-bottom:14px;margin-top:-4px;">
          Naya generate karne pe purana OTP turant kaam karna band ho jayega.
        </p>
      @endif

      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        {{ $hasValidOtp ? 'Naya Emergency OTP Generate Karo' : 'Emergency OTP Generate Karo' }}
      </button>
    </form>

  </div>

</div>
@endsection
