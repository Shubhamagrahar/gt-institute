@extends('layouts.student')
@section('title','My Profile')
@section('page-title','My Profile')

@section('content')
<div style="display:grid;grid-template-columns:280px 1fr;gap:18px;align-items:start;">

  {{-- Identity card --}}
  <div style="display:flex;flex-direction:column;gap:14px;">
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="background:linear-gradient(135deg,#10b981,#059669);height:70px;position:relative;"></div>
      <div style="padding:0 20px 20px;margin-top:-28px;">
        <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:22px;font-weight:900;display:flex;align-items:center;justify-content:center;border:3px solid var(--bg-2);margin-bottom:12px;">
          {{ strtoupper(substr($student->profile?->name ?? 'S', 0, 1)) }}
        </div>
        <div style="font-size:16px;font-weight:800;color:var(--text-1)">{{ $student->profile?->name ?? '—' }}</div>
        <code style="font-size:11px;color:var(--text-3);font-family:monospace">{{ $student->user_id }}</code>
        <div style="margin-top:10px">
          <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:#10b98118;color:#059669">Active</span>
        </div>
      </div>
      <div style="border-top:1px solid var(--border);padding:14px 20px;display:flex;flex-direction:column;gap:8px;">
        <div style="display:flex;gap:8px;font-size:12px;color:var(--text-2)">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
          {{ $student->mobile }}
        </div>
        @if($student->email)
        <div style="display:flex;gap:8px;font-size:12px;color:var(--text-2)">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          {{ $student->email }}
        </div>
        @endif
        @if($student->profile?->gender)
        <div style="display:flex;gap:8px;font-size:12px;color:var(--text-2)">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          {{ ucfirst($student->profile->gender) }}
        </div>
        @endif
      </div>
    </div>

    {{-- Enrollments summary --}}
    @foreach($student->enrollments as $cb)
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:14px 16px;">
      <div style="font-size:11px;font-weight:800;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Enrolled Course</div>
      <div style="font-size:14px;font-weight:700;color:var(--text-1);margin-bottom:4px">{{ $cb->course?->name ?? '—' }}</div>
      @if($cb->batch)
      <div style="font-size:12px;color:var(--text-3)">{{ $cb->batch->name }}</div>
      @endif
      @if($cb->enrollment_no)
      <code style="font-size:10px;color:var(--text-3);font-family:monospace;display:block;margin-top:4px">{{ $cb->enrollment_no }}</code>
      @endif
    </div>
    @endforeach
  </div>

  {{-- Details --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    @php
    $p = $student->profile;
    function sRow($label, $val) { if(!$val) return ''; return '<div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)"><span style=\"font-size:12px;color:var(--text-3);width:160px;flex-shrink:0\">'.$label.'</span><span style=\"font-size:13px;font-weight:600;color:var(--text-1)\">'.$val.'</span></div>'; }
    @endphp

    {{-- Personal --}}
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:18px 20px;">
      <div style="font-size:12px;font-weight:800;color:var(--text-2);margin-bottom:14px;text-transform:uppercase;letter-spacing:.06em">Personal Details</div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Full Name</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p?->name ?? '—' }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Date of Birth</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p?->dob ? \Carbon\Carbon::parse($p->dob)->format('d M Y') : '—' }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Gender</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p?->gender ? ucfirst($p->gender) : '—' }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Father's Name</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p?->father_name ?? '—' }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Blood Group</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p?->blood_group ?? '—' }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Category</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p?->category ?? '—' }}</span>
      </div>
    </div>

    {{-- Contact --}}
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:18px 20px;">
      <div style="font-size:12px;font-weight:800;color:var(--text-2);margin-bottom:14px;text-transform:uppercase;letter-spacing:.06em">Contact & Address</div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Mobile</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $student->mobile }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Email</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $student->email ?? '—' }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">Address</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p?->address ?? '—' }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">State / District</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ implode(', ', array_filter([$p?->state, $p?->district])) ?: '—' }}</span>
      </div>
      <div style="display:flex;gap:8px;padding:10px 0">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">PIN</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p?->pin_code ?? '—' }}</span>
      </div>
    </div>

    {{-- Guardian --}}
    @if($p?->guardian_name || $p?->father_name || $p?->mother_name)
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:18px 20px;">
      <div style="font-size:12px;font-weight:800;color:var(--text-2);margin-bottom:14px;text-transform:uppercase;letter-spacing:.06em">Guardian Details</div>
      @foreach([['Mother','mother_name'],['Guardian','guardian_name'],['Guardian Mobile','guardian_mobile'],['Guardian Relation','guardian_relation']] as [$lbl,$key])
      @if($p?->{$key})
      <div style="display:flex;gap:8px;padding:10px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:12px;color:var(--text-3);width:160px;flex-shrink:0">{{ $lbl }}</span>
        <span style="font-size:13px;font-weight:600;color:var(--text-1)">{{ $p->{$key} }}</span>
      </div>
      @endif
      @endforeach
    </div>
    @endif

  </div>
</div>
@endsection
