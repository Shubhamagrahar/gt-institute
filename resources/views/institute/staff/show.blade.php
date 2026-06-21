@extends('layouts.institute')
@php
  $profile   = $staff->staffProfile;
  $role      = $profile?->staffRole;
  $initials  = collect(explode(' ', $profile?->name ?? 'S'))->map(fn($w)=>strtoupper($w[0]??''))->take(2)->join('');
  $roleColor = $role?->color ?? '#6c5dd3';
@endphp
@section('title', $profile?->name ?? 'Staff Profile')
@section('page-title', 'Staff Profile')
@section('topbar-actions')
  <a href="{{ route('institute.staff.index') }}" class="btn btn-outline btn-sm">← Back</a>
  <a href="{{ route('institute.staff.edit', $staff) }}" class="btn btn-outline btn-sm">Edit</a>
  <a href="{{ route('institute.staff.salary', $staff) }}" class="btn btn-primary btn-sm">Salary</a>
@endsection

@push('styles')
<style>
.sp-label { font-size:11px; color:var(--text-3); margin-bottom:3px; }
.sp-value { font-size:13px; font-weight:600; color:var(--text-1); }
.sp-value.mono { font-family:monospace; letter-spacing:.04em; }
.sp-field { margin-bottom:14px; }
.sp-section-head { display:flex;align-items:center;gap:10px;padding:18px 0 10px;border-top:1px solid var(--border); margin-top:4px; }
.sp-section-head:first-child { border-top:none; padding-top:0; }
.sp-section-label { font-size:11px;font-weight:800;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;white-space:nowrap; }
.sp-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:0 20px; }
.sp-grid-2 { grid-template-columns:repeat(2,1fr); }
@media(max-width:600px){.sp-grid{grid-template-columns:1fr 1fr}.sp-grid-2{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:280px 1fr;gap:16px;align-items:start;">

  {{-- LEFT: Identity card --}}
  <div>
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="background:linear-gradient(135deg,{{ $roleColor }}22,{{ $roleColor }}08);padding:28px 20px;text-align:center;border-bottom:1px solid var(--border);">
        <div style="width:72px;height:72px;border-radius:50%;background:{{ $roleColor }}22;color:{{ $roleColor }};font-size:24px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;letter-spacing:.02em">{{ $initials }}</div>
        <div style="font-size:16px;font-weight:800;color:var(--text-1)">{{ $profile?->name ?? '—' }}</div>
        @if($role)
          <span style="font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;background:{{ $roleColor }}22;color:{{ $roleColor }};display:inline-block;margin-top:6px">{{ $role->name }}</span>
        @endif
      </div>
      <div style="padding:16px 20px;">
        <div class="sp-field">
          <div class="sp-label">Staff ID</div>
          <div class="sp-value mono" style="font-size:12px">{{ $staff->user_id ?? '—' }}</div>
        </div>
        <div class="sp-field">
          <div class="sp-label">Mobile</div>
          <div class="sp-value">{{ $staff->mobile }}</div>
        </div>
        @if($staff->email)
        <div class="sp-field">
          <div class="sp-label">Email</div>
          <div class="sp-value" style="font-size:12px;word-break:break-all">{{ $staff->email }}</div>
        </div>
        @endif
        <div class="sp-field">
          <div class="sp-label">Status</div>
          <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;{{ $staff->status==='active' ? 'background:#d1fae5;color:#065f46' : 'background:#fee2e2;color:#991b1b' }}">
            {{ ucfirst($staff->status) }}
          </span>
        </div>
        <div class="sp-field">
          <div class="sp-label">Joined</div>
          <div class="sp-value">{{ $profile?->joining_date?->format('d M Y') ?? '—' }}</div>
        </div>
      </div>
      {{-- Quick actions --}}
      <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:8px;">
        <a href="{{ route('institute.staff.permissions', $staff) }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg-3);text-decoration:none;color:var(--text-2);font-size:13px;font-weight:600;transition:background .15s;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Manage Permissions
        </a>
        <a href="{{ route('institute.staff.salary', $staff) }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg-3);text-decoration:none;color:var(--text-2);font-size:13px;font-weight:600;transition:background .15s;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          Salary & Payments
        </a>
        <a href="{{ route('institute.staff.edit', $staff) }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg-3);text-decoration:none;color:var(--text-2);font-size:13px;font-weight:600;transition:background .15s;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit Profile
        </a>
      </div>
    </div>

    {{-- Recent salary --}}
    @if($recentSalary->isNotEmpty())
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-top:14px;">
      <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-size:12px;font-weight:800;color:var(--text-2)">Recent Salary</div>
      @foreach($recentSalary as $rec)
        <div style="padding:10px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;">
          <div>
            <div style="font-size:12px;font-weight:600;color:var(--text-1)">{{ \Carbon\Carbon::parse($rec->month)->format('M Y') }}</div>
            <div style="font-size:11px;color:var(--text-3)">₹{{ number_format($rec->paid_amount) }} / ₹{{ number_format($rec->expected_amount) }}</div>
          </div>
          <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;
            {{ $rec->status==='paid' ? 'background:#d1fae5;color:#065f46' : ($rec->status==='partial' ? 'background:#fef3c7;color:#92400e' : 'background:#fee2e2;color:#991b1b') }}">
            {{ ucfirst($rec->status) }}
          </span>
        </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- RIGHT: Details --}}
  <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:22px 24px;">

    {{-- Professional --}}
    <div class="sp-section-head">
      <span class="sp-section-label">Professional</span>
    </div>
    <div class="sp-grid">
      <div class="sp-field">
        <div class="sp-label">Designation</div>
        <div class="sp-value">{{ $profile?->designation ?? '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Department</div>
        <div class="sp-value">{{ $profile?->department ?? '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Experience</div>
        <div class="sp-value">{{ $profile?->experience_years ? $profile->experience_years.' yrs' : '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Qualification</div>
        <div class="sp-value">{{ $profile?->qualification ?? '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Salary</div>
        <div class="sp-value">₹{{ number_format($profile?->salary ?? 0) }} / {{ $profile?->salary_type ?? 'month' }}</div>
      </div>
    </div>

    {{-- Personal --}}
    <div class="sp-section-head">
      <span class="sp-section-label">Personal</span>
    </div>
    <div class="sp-grid">
      <div class="sp-field">
        <div class="sp-label">Father's Name</div>
        <div class="sp-value">{{ $profile?->father_name ?? '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Date of Birth</div>
        <div class="sp-value">{{ $profile?->dob?->format('d M Y') ?? '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Gender</div>
        <div class="sp-value">{{ $profile?->gender ? ucfirst($profile->gender) : '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Blood Group</div>
        <div class="sp-value">{{ $profile?->blood_group ?? '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">WhatsApp</div>
        <div class="sp-value">{{ $profile?->whatsapp ?? '—' }}</div>
      </div>
    </div>

    {{-- Address --}}
    @if($profile?->address || $profile?->city)
    <div class="sp-section-head">
      <span class="sp-section-label">Address</span>
    </div>
    <div class="sp-field" style="max-width:500px">
      <div class="sp-value" style="font-weight:400;line-height:1.6">
        {{ implode(', ', array_filter([$profile->address, $profile->city, $profile->state, $profile->pin])) ?: '—' }}
      </div>
    </div>
    @endif

    {{-- Documents --}}
    @if($profile?->aadhar_no || $profile?->pan_no)
    <div class="sp-section-head">
      <span class="sp-section-label">Identity Documents</span>
    </div>
    <div class="sp-grid sp-grid-2">
      <div class="sp-field">
        <div class="sp-label">Aadhar Number</div>
        <div class="sp-value mono">{{ $profile?->aadhar_no ? substr($profile->aadhar_no,0,4).' XXXX '.substr($profile->aadhar_no,8,4) : '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">PAN Number</div>
        <div class="sp-value mono">{{ $profile?->pan_no ?? '—' }}</div>
      </div>
    </div>
    @endif

    {{-- Bank --}}
    @if($profile?->bank_name || $profile?->account_no)
    <div class="sp-section-head">
      <span class="sp-section-label">Bank Details</span>
    </div>
    <div class="sp-grid">
      <div class="sp-field">
        <div class="sp-label">Bank</div>
        <div class="sp-value">{{ $profile?->bank_name ?? '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Account No.</div>
        <div class="sp-value mono">{{ $profile?->account_no ? 'XXXX'.substr($profile->account_no,-4) : '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">IFSC</div>
        <div class="sp-value mono">{{ $profile?->ifsc ?? '—' }}</div>
      </div>
    </div>
    @endif

    {{-- Emergency --}}
    @if($profile?->emergency_name)
    <div class="sp-section-head">
      <span class="sp-section-label">Emergency Contact</span>
    </div>
    <div class="sp-grid">
      <div class="sp-field">
        <div class="sp-label">Name</div>
        <div class="sp-value">{{ $profile->emergency_name }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Phone</div>
        <div class="sp-value">{{ $profile->emergency_phone ?? '—' }}</div>
      </div>
      <div class="sp-field">
        <div class="sp-label">Relation</div>
        <div class="sp-value">{{ $profile->emergency_relation ?? '—' }}</div>
      </div>
    </div>
    @endif

    {{-- Notes --}}
    @if($profile?->notes)
    <div class="sp-section-head">
      <span class="sp-section-label">Notes</span>
    </div>
    <div style="font-size:13px;color:var(--text-2);line-height:1.6;background:var(--bg-3);border-radius:9px;padding:12px 14px;">
      {{ $profile->notes }}
    </div>
    @endif

  </div>
</div>
@endsection
