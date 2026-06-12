@extends('layouts.owner')
@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')

@if(session('success'))
  <div class="gt-alert gt-alert-success" style="margin-bottom:20px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
  </div>
@endif

{{-- Security Settings --}}
<div class="gt-card" style="max-width:680px;">
  <div class="gt-card-header" style="display:flex;align-items:center;gap:10px;padding-bottom:16px;border-bottom:1px solid rgba(255,255,255,.06);margin-bottom:20px;">
    <div style="width:36px;height:36px;border-radius:10px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    </div>
    <div>
      <div style="font-weight:600;font-size:15px;color:#f1f5f9;">Security</div>
      <div style="font-size:12px;color:rgba(255,255,255,.4);margin-top:1px;">Authentication &amp; access controls</div>
    </div>
  </div>

  {{-- OTP Toggle --}}
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;padding:16px 0;border-bottom:1px solid rgba(255,255,255,.05);">
    <div style="flex:1;">
      <div style="font-size:14px;font-weight:500;color:#e2e8f0;margin-bottom:4px;">OTP Login Verification</div>
      <div style="font-size:12px;color:rgba(255,255,255,.4);line-height:1.6;">
        When enabled, users must verify a 6-digit OTP sent to their email after entering their password.
        <br>
        <span style="color:rgba(251,191,36,.75);">
          Disable temporarily if your email server is down or users are unable to receive OTP emails.
        </span>
      </div>

      @php $otpEnabled = ($settings['otp_login_enabled']->value ?? '1') === '1'; @endphp

      <div style="margin-top:10px;display:inline-flex;align-items:center;gap:7px;padding:5px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:.4px;
        {{ $otpEnabled ? 'background:rgba(34,197,94,.1);color:#4ade80;' : 'background:rgba(239,68,68,.1);color:#f87171;' }}">
        <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
        {{ $otpEnabled ? 'ACTIVE' : 'DISABLED' }}
      </div>
    </div>

    <form method="POST" action="{{ route('owner.settings.update') }}" style="flex-shrink:0;">
      @csrf
      @method('PATCH')
      @if($otpEnabled)
        <input type="hidden" name="otp_login_enabled" value="0">
        <button type="submit"
          onclick="return confirm('Disable OTP login? Users will only need their password to sign in until you re-enable this.')"
          class="btn"
          style="background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);white-space:nowrap;font-size:13px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
          Disable OTP
        </button>
      @else
        <input type="hidden" name="otp_login_enabled" value="1">
        <button type="submit"
          class="btn btn-primary"
          style="white-space:nowrap;font-size:13px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Enable OTP
        </button>
      @endif
    </form>
  </div>

  {{-- Info note --}}
  <div style="margin-top:18px;padding:14px 16px;background:rgba(99,102,241,.07);border:1px solid rgba(99,102,241,.18);border-radius:12px;font-size:12px;color:rgba(255,255,255,.5);line-height:1.7;">
    <strong style="color:rgba(165,180,252,.8);">Emergency bypass:</strong>
    Even when OTP is enabled, you can always log in using the daily rotating master OTP.
    Run <code style="background:rgba(255,255,255,.08);padding:1px 6px;border-radius:4px;font-size:11px;">php artisan otp:master</code> on the server to get today's code.
  </div>
</div>

@endsection
