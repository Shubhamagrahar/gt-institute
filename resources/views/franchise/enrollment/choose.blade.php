@extends('layouts.franchise')
@section('title','New Admission')
@section('page-title','New Admission')

@section('content')
<div style="max-width:640px;margin:32px auto;">

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">

    <a href="{{ route('franchise.enrollment.new') }}"
       style="display:flex;flex-direction:column;gap:10px;padding:22px 20px;border-radius:12px;text-decoration:none;background:rgba(234,88,12,.07);border:2px solid #ea580c;">
      <div style="width:44px;height:44px;border-radius:10px;background:#ea580c;display:flex;align-items:center;justify-content:center;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="8.5" cy="7" r="4"/>
          <line x1="20" y1="8" x2="20" y2="14"/>
          <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
      </div>
      <div>
        <div style="font-size:14px;font-weight:700;color:#ea580c;margin-bottom:4px;">Full Admission</div>
        <div style="font-size:12px;color:var(--text-2);line-height:1.5;">Step-by-step wizard — course, student details, address, review. Best for walk-in students.</div>
      </div>
    </a>

    <a href="{{ route('franchise.enrollment.quick') }}"
       style="display:flex;flex-direction:column;gap:10px;padding:22px 20px;border-radius:12px;text-decoration:none;background:var(--bg-2);border:2px solid var(--border);">
      <div style="width:44px;height:44px;border-radius:10px;background:var(--bg-3);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2">
          <path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/>
        </svg>
      </div>
      <div>
        <div style="font-size:14px;font-weight:700;color:var(--text-1);margin-bottom:4px;">Quick Booking</div>
        <div style="font-size:12px;color:var(--text-2);line-height:1.5;">Name, mobile, and course only — save the seat now, fill details later. Ideal for rush admissions.</div>
      </div>
    </a>

  </div>

  <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <div style="flex:1;height:1px;background:var(--border);"></div>
    <span style="font-size:12px;color:var(--text-2);font-weight:600;letter-spacing:.5px;">OR ENROLL EXISTING STUDENT</span>
    <div style="flex:1;height:1px;background:var(--border);"></div>
  </div>

  <div class="gt-card" style="padding:20px;">
    <div style="font-size:13px;font-weight:600;margin-bottom:12px;color:var(--text-1);">Search by Mobile or Student ID</div>
    <form method="POST" action="{{ route('franchise.enrollment.find-student') }}">
      @csrf
      <div style="display:flex;gap:8px;">
        <div style="position:relative;flex:1;">
          <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-2);" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" name="search" class="gt-input"
            style="padding-left:34px;"
            placeholder="Mobile number or Student ID..."
            required value="{{ old('search') }}">
        </div>
        <button type="submit" class="btn btn-primary" style="white-space:nowrap;padding:0 18px;">Search</button>
      </div>
      @error('search')<div class="gt-error" style="margin-top:6px;">{{ $message }}</div>@enderror
    </form>
  </div>

  <div style="text-align:center;margin-top:18px;">
    <a href="{{ route('franchise.enrollment.pending') }}" style="font-size:12px;color:var(--text-2);text-decoration:none;">
      ← View Pending Admissions
    </a>
  </div>

</div>
@endsection
