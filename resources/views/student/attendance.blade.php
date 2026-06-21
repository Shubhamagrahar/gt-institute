@extends('layouts.student')
@section('title','Attendance')
@section('page-title','Attendance')

@section('content')

{{-- Monthly summary cards --}}
@if($months->isNotEmpty())
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:20px;">
  @foreach($months as $m)
  @php
    $attPct = $m->total > 0 ? round(($m->present / $m->total)*100) : 0;
    $monthName = \Carbon\Carbon::createFromDate($m->yr, $m->mo, 1)->format('M Y');
    $color = $attPct >= 75 ? '#10b981' : ($attPct >= 50 ? '#f59e0b' : '#ef4444');
  @endphp
  <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;">
    <div style="font-size:11px;font-weight:700;color:var(--text-3);margin-bottom:8px">{{ $monthName }}</div>
    <div style="font-size:24px;font-weight:900;color:{{ $color }};">{{ $attPct }}%</div>
    <div style="font-size:11px;color:var(--text-3);margin-top:4px">{{ $m->present }}/{{ $m->total }} days</div>
    <div style="height:5px;border-radius:100px;background:var(--bg-3);margin-top:8px;overflow:hidden">
      <div style="height:100%;border-radius:100px;background:{{ $color }};width:{{ $attPct }}%"></div>
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- Detailed records --}}
<div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
  <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
    <div style="font-size:13px;font-weight:800;color:var(--text-1)">Attendance Records</div>
  </div>
  @if($records->isNotEmpty())
  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr style="background:var(--bg-3)">
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:left">Date</th>
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:left">Day</th>
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:left">Status</th>
        <th style="padding:10px 20px;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;text-align:left">Remark</th>
      </tr>
    </thead>
    <tbody>
    @foreach($records as $r)
    @php
      $dt = \Carbon\Carbon::parse($r->date);
      $isPresent = $r->status === 'present';
    @endphp
    <tr style="border-bottom:1px solid var(--border)">
      <td style="padding:11px 20px;font-size:13px;font-weight:600;color:var(--text-1)">{{ $dt->format('d M Y') }}</td>
      <td style="padding:11px 20px;font-size:12px;color:var(--text-3)">{{ $dt->format('D') }}</td>
      <td style="padding:11px 20px">
        @if($isPresent)
          <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#d1fae5;color:#065f46">
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Present
          </span>
        @else
          <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#fee2e2;color:#b91c1c">
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Absent
          </span>
        @endif
      </td>
      <td style="padding:11px 20px;font-size:12px;color:var(--text-3)">{{ $r->note ?? $r->remark ?? '—' }}</td>
    </tr>
    @endforeach
    </tbody>
  </table>
  @else
  <div style="text-align:center;padding:48px;color:var(--text-3)">
    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    <div style="font-size:14px;font-weight:600;margin-bottom:4px">No attendance records yet</div>
    <div style="font-size:12px">Your attendance will appear here once it is marked.</div>
  </div>
  @endif
</div>
@endsection
