@extends('layouts.franchise')
@section('title','My Students')
@section('page-title','Students')

@push('styles')
<style>
.stu-tbl{width:100%;border-collapse:collapse;font-size:13px}
.stu-tbl th{background:var(--bg-3);padding:10px 14px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);white-space:nowrap}
.stu-tbl td{padding:11px 14px;border-bottom:1px solid var(--border);vertical-align:middle}
.stu-tbl tr:last-child td{border-bottom:none}
.stu-tbl tbody tr:hover td{background:var(--bg-3)}
.stu-avatar{width:34px;height:34px;border-radius:50%;object-fit:cover;background:var(--bg-3);border:2px solid var(--border)}
.filter-bar{background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:12px 16px;margin-bottom:16px;display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.s-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:18px}
@media(max-width:600px){.s-stats{grid-template-columns:repeat(2,1fr)}}
.s-stat{background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:14px 16px}
.s-stat-val{font-size:26px;font-weight:900}
.s-stat-lbl{font-size:11px;color:var(--text-3);margin-top:2px}
</style>
@endpush

@section('topbar-actions')
  <a href="{{ route('franchise.enrollment.new') }}" class="btn btn-primary btn-sm">+ New Admission</a>
@endsection

@section('content')

{{-- Stats --}}
<div class="s-stats">
  <div class="s-stat">
    <div class="s-stat-val">{{ $totalStudents }}</div>
    <div class="s-stat-lbl">Total Students</div>
  </div>
  <div class="s-stat">
    <div class="s-stat-val" style="color:#16a34a">{{ $admittedCount }}</div>
    <div class="s-stat-lbl">Admitted (Active)</div>
  </div>
  <div class="s-stat">
    <div class="s-stat-val" style="color:#d97706">{{ $pendingCount }}</div>
    <div class="s-stat-lbl">Pending Admission</div>
  </div>
</div>

{{-- Filter --}}
<div class="filter-bar">
  <form method="GET" action="{{ route('franchise.students.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;width:100%">
    <input type="text" name="search" class="gt-input" style="width:220px" value="{{ request('search') }}" placeholder="Search by name or mobile...">
    <select name="status" class="gt-select" style="width:160px">
      <option value="">All Status</option>
      <option value="RUN"  {{ request('status') === 'RUN'  ? 'selected' : '' }}>Admitted</option>
      <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Pending</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
    <a href="{{ route('franchise.students.index') }}" class="btn btn-outline btn-sm">Reset</a>
    <span style="margin-left:auto;font-size:12px;color:var(--text-3)">
      {{ $students->total() }} results
    </span>
  </form>
</div>

{{-- Table --}}
<div class="gt-card">
  <div style="overflow-x:auto">
    <table class="stu-tbl">
      <thead>
        <tr>
          <th>#</th>
          <th>Student</th>
          <th>Mobile</th>
          <th>Course</th>
          <th>Batch</th>
          <th>Enrollment No.</th>
          <th>Status</th>
          <th>Fee Due</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $book)
        @php
          $due = max(round((float)$book->final_fee - (float)$book->paid_amount, 2), 0);
        @endphp
        <tr>
          <td class="text-muted" style="font-size:11px">{{ $students->firstItem() + $loop->index }}</td>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <img src="{{ asset($book->student->profile?->photo ?? 'images/user.svg') }}"
                   class="stu-avatar" alt="photo" onerror="this.src='{{ asset('images/user.svg') }}'">
              <div>
                <div style="font-weight:700;font-size:13px">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
                <div style="font-size:11px;color:var(--text-3)">{{ $book->student->user_id }}</div>
              </div>
            </div>
          </td>
          <td>{{ $book->student->mobile }}</td>
          <td style="font-size:12px;max-width:160px;word-break:break-word">{{ $book->course?->name }}</td>
          <td style="font-size:12px;color:var(--text-2)">{{ $book->batch?->name ?? '—' }}</td>
          <td>
            @if($book->enrollment_no)
              <span class="mono" style="font-size:12px;color:var(--primary)">{{ $book->enrollment_no }}</span>
            @else
              <span class="text-muted" style="font-size:11px">Pending</span>
            @endif
          </td>
          <td>
            @if($book->status === 'RUN')
              <span class="badge badge-success">Admitted</span>
            @else
              <span class="badge badge-warning">Pending</span>
            @endif
          </td>
          <td>
            @if($due > 0)
              <span style="font-family:monospace;font-weight:700;color:#dc2626;font-size:12px">₹{{ number_format($due, 2) }}</span>
            @elseif($book->status === 'RUN')
              <span style="font-size:11px;color:#16a34a">Paid ✓</span>
            @else
              <span class="text-muted" style="font-size:11px">—</span>
            @endif
          </td>
          <td style="text-align:right">
            <div style="display:flex;gap:5px;justify-content:flex-end">
              @if($book->status === 'RUN')
                <a href="{{ route('franchise.enrollment.payment-complete', $book) }}" class="btn btn-primary btn-sm" style="font-size:11px">
                  Collect Fee
                </a>
              @else
                <a href="{{ route('franchise.enrollment.profile', $book) }}" class="btn btn-primary btn-sm" style="font-size:11px">
                  Process
                </a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div style="padding:50px;text-align:center">
              <div style="font-size:36px;margin-bottom:8px">🎓</div>
              <div style="font-size:15px;font-weight:700;color:var(--text-1);margin-bottom:4px">No students found</div>
              <div style="font-size:12px;color:var(--text-3)">Abhi tak koi student enrolled nahi hai is franchise se.</div>
              <a href="{{ route('franchise.enrollment.new') }}" class="btn btn-primary" style="margin-top:14px">+ New Admission</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($students->hasPages())
  <div style="padding:14px 18px">
    {{ $students->appends(request()->query())->links() }}
  </div>
  @endif
</div>
@endsection
