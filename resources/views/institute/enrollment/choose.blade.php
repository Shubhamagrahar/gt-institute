@extends('layouts.institute')
@section('title','New Admission')
@section('page-title','New Admission')

@push('styles')
<style>
.choose-wrap { max-width:640px; margin:32px auto; }
.choose-enquiry-banner { background:var(--accent-bg); border:1.5px solid var(--accent); border-radius:10px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:10px; }
.choose-enquiry-title { font-size:13px; font-weight:700; color:var(--accent); }
.choose-enquiry-sub { font-size:12px; color:var(--text-2); }
.choose-mode-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px; }
.choose-mode-card { display:flex; flex-direction:column; gap:10px; padding:22px 20px; border-radius:12px; text-decoration:none; transition:box-shadow .15s; }
.choose-mode-card--primary { background:var(--accent-bg); border:2px solid var(--accent); }
.choose-mode-card--default { background:var(--bg-2); border:2px solid var(--border); }
.choose-mode-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.choose-mode-icon--primary { background:var(--accent); }
.choose-mode-icon--default { background:var(--bg-3); border:1px solid var(--border); }
.choose-mode-title { font-size:14px; font-weight:700; margin-bottom:4px; }
.choose-mode-title--primary { color:var(--accent); }
.choose-mode-title--default { color:var(--text); }
.choose-mode-desc { font-size:12px; color:var(--text-2); line-height:1.5; }
.choose-divider { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
.choose-divider-line { flex:1; height:1px; background:var(--border); }
.choose-divider-label { font-size:12px; color:var(--text-2); font-weight:600; letter-spacing:.5px; }
.choose-search-heading { font-size:13px; font-weight:600; margin-bottom:12px; color:var(--text); }
.choose-search-row { display:flex; gap:8px; }
.choose-search-input-wrap { position:relative; flex:1; }
.choose-search-icon { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-2); }
.choose-search-field { padding-left:34px; }
.choose-footer { text-align:center; margin-top:18px; }
.choose-footer-link { font-size:12px; color:var(--text-2); }
</style>
@endpush

@section('content')
@php $prefill = session('enquiry_prefill'); @endphp
<div class="choose-wrap">

  {{-- Enquiry pre-fill banner --}}
  @if($prefill)
    <div class="choose-enquiry-banner">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <div>
        <div class="choose-enquiry-title">Converting from Enquiry</div>
        <div class="choose-enquiry-sub">{{ $prefill['name'] }} &middot; {{ $prefill['mobile'] }} &mdash; name and mobile have been pre-filled.</div>
      </div>
    </div>
  @endif

  {{-- 2 main entry cards --}}
  <div class="choose-mode-grid">

    <a href="{{ route('institute.enrollment.new', $prefill ? ['enquiry_id'=>$prefill['enquiry_id']] : []) }}"
      class="choose-mode-card choose-mode-card--primary">
      <div class="choose-mode-icon choose-mode-icon--primary">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="8.5" cy="7" r="4"/>
          <line x1="20" y1="8" x2="20" y2="14"/>
          <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
      </div>
      <div>
        <div class="choose-mode-title choose-mode-title--primary">Full Booking</div>
        <div class="choose-mode-desc">Complete form — name, address, course, payment plan, all at once. Best for walk-in students.</div>
      </div>
    </a>

    <a href="{{ route('institute.enrollment.quick', $prefill ? ['enquiry_id'=>$prefill['enquiry_id']] : []) }}"
      class="choose-mode-card choose-mode-card--default">
      <div class="choose-mode-icon choose-mode-icon--default">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2">
          <path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/>
        </svg>
      </div>
      <div>
        <div class="choose-mode-title choose-mode-title--default">Quick Booking</div>
        <div class="choose-mode-desc">Name, mobile, and course only — save the seat now, fill details later. Ideal for rush admissions.</div>
      </div>
    </a>

  </div>

  {{-- Divider --}}
  <div class="choose-divider">
    <div class="choose-divider-line"></div>
    <div class="choose-divider-label">OR SEARCH EXISTING STUDENT</div>
    <div class="choose-divider-line"></div>
  </div>

  {{-- Existing student search --}}
  <div class="gt-card" style="padding:20px;">
    <div class="choose-search-heading">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      Enroll an Existing Student in a New Course
    </div>
    <form method="POST" action="{{ route('institute.enrollment.find-student') }}">
      @csrf
      <div class="choose-search-row">
        <div class="choose-search-input-wrap">
          <svg class="choose-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" name="search" class="gt-input choose-search-field"
            placeholder="Mobile number or Student ID..."
            required value="{{ old('search') }}"
            autofocus>
        </div>
        <button type="submit" class="btn btn-primary" style="white-space:nowrap;padding:0 18px;">
          Search
        </button>
      </div>
      @error('search')<div class="gt-error" style="margin-top:6px;">{{ $message }}</div>@enderror
    </form>
  </div>

  {{-- Footer hint --}}
  <div class="choose-footer">
    <a href="{{ route('institute.enrollment.pending') }}" class="choose-footer-link">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Click here to view Pending Admissions &rarr;
    </a>
  </div>

</div>
@endsection
