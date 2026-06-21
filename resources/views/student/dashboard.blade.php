@extends('layouts.student')
@section('title','Dashboard')
@section('page-title','Dashboard')

@push('styles')
<style>
/* ── Stat cards ── */
.stu-stats { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px; }
.stu-stat  { background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:18px 20px; }
.stu-stat-icon { width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px; }
.stu-stat-label { font-size:10px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:5px; }
.stu-stat-val   { font-size:22px;font-weight:900;color:var(--text-1);line-height:1; }
.stu-stat-sub   { font-size:11px;color:var(--text-3);margin-top:4px; }

/* ── Section card ── */
.stu-card { background:var(--bg-2);border:1px solid var(--border);border-radius:12px;margin-bottom:18px; }
.stu-card-head { padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between; }
.stu-card-title { font-size:13px;font-weight:700;color:var(--text-1); }
.stu-card-body  { padding:16px 20px; }

/* ── Status banner ── */
.stu-banner { border-radius:12px;padding:18px 22px;margin-bottom:22px;display:flex;align-items:center;gap:16px; }
.stu-banner.pending  { background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.22); }
.stu-banner.admitted { background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.22); }
.stu-banner-icon { width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.stu-banner-icon.amber { background:rgba(245,158,11,.15); }
.stu-banner-icon.green { background:rgba(16,185,129,.15); }
.stu-banner-title { font-size:14px;font-weight:700; }
.stu-banner-sub   { font-size:12px;color:var(--text-3);margin-top:2px; }

/* ── Table ── */
.stu-table { width:100%;border-collapse:collapse; }
.stu-table th { font-size:10px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;padding:0 14px 10px;text-align:left; }
.stu-table td { padding:11px 14px;font-size:13px;color:var(--text-2);border-top:1px solid var(--border); }
.stu-table tr:hover td { background:rgba(255,255,255,.02); }

/* ── Status badges ── */
.badge { display:inline-block;font-size:10px;font-weight:700;padding:2px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:.05em; }
.badge-amber  { background:rgba(245,158,11,.15);color:#f59e0b; }
.badge-green  { background:rgba(16,185,129,.15);color:#10b981; }
.badge-blue   { background:rgba(99,102,241,.15);color:#818cf8; }
.badge-red    { background:rgba(239,68,68,.15);color:#f87171; }

/* ── Fee bar ── */
.fee-bar-bg   { height:6px;border-radius:100px;background:var(--bg-3);overflow:hidden;margin:6px 0; }
.fee-bar-fill { height:100%;border-radius:100px;background:linear-gradient(90deg,#10b981,#059669); }

/* ── 2-col grid ── */
.stu-grid { display:grid;grid-template-columns:1fr 320px;gap:18px; }

/* ── Empty state ── */
.stu-empty { text-align:center;padding:32px 20px;color:var(--text-3);font-size:13px; }
.stu-empty svg { opacity:.3;margin-bottom:10px; }

@media(max-width:900px) {
  .stu-stats { grid-template-columns:repeat(2,1fr); }
  .stu-grid  { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')
@php
  $attPct = $total > 0 ? round($present / $total * 100) : 0;
  $enrollmentStatus = $enrollment?->status ?? null;
  $isAdmitted = in_array($enrollmentStatus, ['RUNNING', 'COMPLETE']);
  $amtCol = \App\Models\FeeCollectDetail::amountColumn();
@endphp

{{-- Status Banner --}}
@if($enrollment)
  @if(!$isAdmitted)
  <div class="stu-banner pending">
    <div class="stu-banner-icon amber">
      <svg width="20" height="20" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div>
      <div class="stu-banner-title" style="color:#f59e0b;">Seat Booked — Admission Pending</div>
      <div class="stu-banner-sub">Your seat has been reserved for <strong style="color:var(--text-2);">{{ $enrollment->course?->name }}</strong>. Final admission will be confirmed by the institute.</div>
    </div>
    <div style="margin-left:auto;flex-shrink:0;">
      <span class="badge badge-amber">Pending</span>
    </div>
  </div>
  @else
  <div class="stu-banner admitted">
    <div class="stu-banner-icon green">
      <svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div>
      <div class="stu-banner-title" style="color:#10b981;">Admission Confirmed</div>
      <div class="stu-banner-sub">You are enrolled in <strong style="color:var(--text-2);">{{ $enrollment->course?->name }}</strong>{{ $enrollment->batch ? ' · ' . $enrollment->batch->name : '' }}.</div>
    </div>
    <div style="margin-left:auto;flex-shrink:0;">
      <span class="badge badge-green">Active</span>
    </div>
  </div>
  @endif
@endif

{{-- Stat Cards --}}
<div class="stu-stats">
  <div class="stu-stat">
    <div class="stu-stat-icon" style="background:rgba(16,185,129,.12);">
      <svg width="18" height="18" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div class="stu-stat-label">Total Fee</div>
    <div class="stu-stat-val">₹{{ number_format($totalFee) }}</div>
    <div class="stu-stat-sub">Course fee</div>
  </div>

  <div class="stu-stat">
    <div class="stu-stat-icon" style="background:rgba(99,102,241,.12);">
      <svg width="18" height="18" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div class="stu-stat-label">Paid</div>
    <div class="stu-stat-val" style="color:#818cf8;">₹{{ number_format($paidFee) }}</div>
    <div class="stu-stat-sub">Received</div>
  </div>

  <div class="stu-stat">
    <div class="stu-stat-icon" style="background:{{ $balance > 0 ? 'rgba(239,68,68,.12)' : 'rgba(16,185,129,.12)' }};">
      <svg width="18" height="18" fill="none" stroke="{{ $balance > 0 ? '#f87171' : '#10b981' }}" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
    </div>
    <div class="stu-stat-label">Balance Due</div>
    <div class="stu-stat-val" style="color:{{ $balance > 0 ? '#f87171' : '#10b981' }};">₹{{ number_format($balance) }}</div>
    <div class="stu-stat-sub">{{ $balance > 0 ? 'Pending payment' : 'Fully paid' }}</div>
  </div>

  <div class="stu-stat">
    <div class="stu-stat-icon" style="background:rgba(245,158,11,.12);">
      <svg width="18" height="18" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <div class="stu-stat-label">Attendance</div>
    <div class="stu-stat-val" style="color:#f59e0b;">{{ $attPct }}%</div>
    <div class="stu-stat-sub">{{ $present }}/{{ $total }} days — {{ $now->format('M Y') }}</div>
  </div>
</div>

{{-- 2-column grid --}}
<div class="stu-grid">

  {{-- LEFT COLUMN --}}
  <div>

    {{-- My Enrollments --}}
    <div class="stu-card" id="enrollments">
      <div class="stu-card-head">
        <div class="stu-card-title">My Enrollments</div>
        <a href="{{ route('student.courses') }}" style="font-size:12px;color:#10b981;font-weight:600;">+ Apply for Course</a>
      </div>
      @if($allEnrollments->isEmpty())
        <div class="stu-empty">
          <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          <div>No enrollments yet. <a href="{{ route('student.courses') }}" style="color:#10b981;">Browse courses →</a></div>
        </div>
      @else
        <table class="stu-table">
          <thead>
            <tr>
              <th>Course</th>
              <th>Batch</th>
              <th>Booked On</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($allEnrollments as $enr)
            <tr>
              <td style="font-weight:600;color:var(--text-1);">{{ $enr->course?->name ?? '—' }}</td>
              <td>{{ $enr->batch?->name ?? 'No Batch' }}</td>
              <td>{{ $enr->book_date ? \Carbon\Carbon::parse($enr->book_date)->format('d M Y') : '—' }}</td>
              <td>
                @php $s = strtoupper($enr->status ?? 'OPEN'); @endphp
                @if($s === 'OPEN')
                  <span class="badge badge-amber">Seat Booked</span>
                @elseif($s === 'RUNNING')
                  <span class="badge badge-green">Admitted</span>
                @elseif($s === 'COMPLETE')
                  <span class="badge badge-blue">Completed</span>
                @else
                  <span class="badge" style="background:var(--bg-3);color:var(--text-3);">{{ $s }}</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    {{-- Attendance Table --}}
    <div class="stu-card">
      <div class="stu-card-head">
        <div class="stu-card-title">Attendance — {{ $now->format('F Y') }}</div>
        <a href="{{ route('student.attendance') }}" style="font-size:12px;color:#10b981;font-weight:600;">View All →</a>
      </div>
      @php
        try {
          $attRecords = \Illuminate\Support\Facades\DB::table('attendance_students')
            ->where('user_id', $student->id)
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->orderByDesc('date')
            ->limit(10)
            ->get();
        } catch(\Exception $e) { $attRecords = collect(); }
      @endphp
      @if($attRecords->isEmpty())
        <div class="stu-empty">No attendance records for this month.</div>
      @else
        <table class="stu-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Day</th>
              <th>Status</th>
              <th>Remark</th>
            </tr>
          </thead>
          <tbody>
            @foreach($attRecords as $rec)
            <tr>
              <td style="font-weight:600;color:var(--text-1);">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</td>
              <td style="color:var(--text-3);">{{ \Carbon\Carbon::parse($rec->date)->format('D') }}</td>
              <td>
                @if(strtolower($rec->status) === 'present')
                  <span class="badge badge-green">Present</span>
                @else
                  <span class="badge badge-red">Absent</span>
                @endif
              </td>
              <td style="color:var(--text-3);">{{ $rec->remark ?? '—' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

  </div>

  {{-- RIGHT COLUMN --}}
  <div>

    {{-- Student Info Card --}}
    <div class="stu-card" style="margin-bottom:18px;">
      <div class="stu-card-body" style="padding:20px;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
          <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#fff;flex-shrink:0;">
            {{ strtoupper(substr($student->profile?->name ?? 'S', 0, 1)) }}
          </div>
          <div>
            <div style="font-size:15px;font-weight:700;color:var(--text-1);">{{ $student->profile?->name ?? 'Student' }}</div>
            <div style="font-size:11px;color:var(--text-3);font-family:monospace;margin-top:2px;">{{ $student->user_id }}</div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
          <div style="background:var(--bg-3);border-radius:8px;padding:10px 12px;">
            <div style="font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Mobile</div>
            <div style="font-size:12px;font-weight:600;color:var(--text-1);">{{ $student->mobile ?? '—' }}</div>
          </div>
          <div style="background:var(--bg-3);border-radius:8px;padding:10px 12px;">
            <div style="font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Gender</div>
            <div style="font-size:12px;font-weight:600;color:var(--text-1);">{{ ucfirst($student->profile?->gender ?? '—') }}</div>
          </div>
        </div>
        <div style="margin-top:10px;background:var(--bg-3);border-radius:8px;padding:10px 12px;">
          <div style="font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Email</div>
          <div style="font-size:12px;font-weight:600;color:var(--text-1);word-break:break-all;">{{ $student->email ?? '—' }}</div>
        </div>
      </div>
    </div>

    {{-- Fee Summary --}}
    <div class="stu-card" style="margin-bottom:18px;">
      <div class="stu-card-head">
        <div class="stu-card-title">Fee Summary</div>
        <a href="{{ route('student.fees') }}" style="font-size:12px;color:#10b981;font-weight:600;">Ledger →</a>
      </div>
      <div class="stu-card-body">
        @php $feePct = $totalFee > 0 ? min(100, round($paidFee / $totalFee * 100)) : 0; @endphp
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
          <span style="font-size:12px;color:var(--text-3);">Paid</span>
          <span style="font-size:12px;font-weight:700;color:#10b981;">{{ $feePct }}%</span>
        </div>
        <div class="fee-bar-bg"><div class="fee-bar-fill" style="width:{{ $feePct }}%;"></div></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px;">
          <div style="background:var(--bg-3);border-radius:8px;padding:10px 12px;text-align:center;">
            <div style="font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Total</div>
            <div style="font-size:15px;font-weight:800;color:var(--text-1);">₹{{ number_format($totalFee) }}</div>
          </div>
          <div style="background:var(--bg-3);border-radius:8px;padding:10px 12px;text-align:center;">
            <div style="font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Balance</div>
            <div style="font-size:15px;font-weight:800;color:{{ $balance > 0 ? '#f87171' : '#10b981' }};">₹{{ number_format($balance) }}</div>
          </div>
        </div>

        {{-- Recent transactions --}}
        @if($recentFees->count())
        <div style="margin-top:14px;border-top:1px solid var(--border);padding-top:12px;">
          <div style="font-size:10px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">Recent Payments</div>
          @foreach($recentFees as $fee)
          <div style="display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);">
            <div>
              <div style="font-size:12px;font-weight:600;color:var(--text-1);">{{ $fee->invoice_no ?? '—' }}</div>
              <div style="font-size:11px;color:var(--text-3);">{{ $fee->date ? \Carbon\Carbon::parse($fee->date)->format('d M Y') : '' }}</div>
            </div>
            <div style="font-size:13px;font-weight:800;color:#10b981;">₹{{ number_format($fee->$amtCol ?? 0) }}</div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>

    {{-- Attendance Summary --}}
    <div class="stu-card">
      <div class="stu-card-head">
        <div class="stu-card-title">Attendance Summary</div>
      </div>
      <div class="stu-card-body">
        <div style="display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
          {{-- Donut --}}
          @php
            $r = 36; $circ = 2 * 3.14159 * $r;
            $dash = $total > 0 ? ($present / $total) * $circ : 0;
          @endphp
          <svg width="90" height="90" viewBox="0 0 90 90">
            <circle cx="45" cy="45" r="{{ $r }}" fill="none" stroke="var(--bg-3)" stroke-width="10"/>
            <circle cx="45" cy="45" r="{{ $r }}" fill="none" stroke="#10b981" stroke-width="10"
              stroke-dasharray="{{ round($dash,1) }} {{ round($circ,1) }}"
              stroke-linecap="round" transform="rotate(-90 45 45)"/>
            <text x="45" y="49" text-anchor="middle" fill="var(--text-1)" font-size="14" font-weight="800" font-family="inherit">{{ $attPct }}%</text>
          </svg>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;text-align:center;">
          <div style="background:rgba(16,185,129,.08);border-radius:8px;padding:8px 4px;">
            <div style="font-size:16px;font-weight:900;color:#10b981;">{{ $present }}</div>
            <div style="font-size:10px;color:var(--text-3);margin-top:2px;">Present</div>
          </div>
          <div style="background:rgba(239,68,68,.08);border-radius:8px;padding:8px 4px;">
            <div style="font-size:16px;font-weight:900;color:#f87171;">{{ $absent }}</div>
            <div style="font-size:10px;color:var(--text-3);margin-top:2px;">Absent</div>
          </div>
          <div style="background:var(--bg-3);border-radius:8px;padding:8px 4px;">
            <div style="font-size:16px;font-weight:900;color:var(--text-1);">{{ $total }}</div>
            <div style="font-size:10px;color:var(--text-3);margin-top:2px;">Total</div>
          </div>
        </div>
        <a href="{{ route('student.attendance') }}" style="display:block;text-align:center;margin-top:14px;font-size:12px;color:#10b981;font-weight:600;">View Full Attendance →</a>
      </div>
    </div>

  </div>
</div>
@endsection
