@extends('layouts.institute')
@section('title','Cancelled Bookings')
@section('page-title','Cancelled Bookings')

@push('styles')
<style>
.gt-tbl-wrap{overflow-x:auto}
.gt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.gt-tbl thead th{background:var(--bg-3);padding:10px 12px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text-2);white-space:nowrap;border-bottom:1px solid var(--border)}
.gt-tbl tbody td{padding:10px 12px;border-bottom:1px solid var(--border);vertical-align:middle}
.gt-tbl tbody tr:last-child td{border-bottom:none}
.gt-tbl tbody tr:hover{background:var(--bg-3)}
.avatar-sm{width:34px;height:34px;border-radius:50%;background:#94a3b8;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;overflow:hidden}
.avatar-sm img{width:100%;height:100%;object-fit:cover}
</style>
@endpush

@section('content')

<div class="gt-card">
  <div class="gt-card-header" style="flex-wrap:wrap;gap:10px;">
    <div class="gt-card-title">Cancelled Bookings</div>
    <div style="display:flex;align-items:center;gap:8px;margin-left:auto;">
      <form id="filter-form" method="GET" action="{{ route('institute.students.cancelled') }}" style="display:contents;">
        <input type="hidden" name="q" id="hidden-q" value="{{ $search }}">
      </form>
      <div style="position:relative;">
        <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--text-2);" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="search-input" placeholder="Search name / mobile…" value="{{ $search }}"
          style="padding:6px 10px 6px 30px;border:1px solid var(--border);border-radius:7px;font-size:12.5px;background:var(--bg-3);color:var(--text);width:240px;outline:none;">
      </div>
    </div>
  </div>

  <div class="gt-tbl-wrap">
    <table class="gt-tbl">
      <thead>
        <tr>
          <th>#</th>
          <th>Student</th>
          <th>Mobile</th>
          <th>Course</th>
          <th>Booked On</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($enrollments as $enr)
          @php $stu = $enr->student; $profile = $stu?->profile; $photo = $profile?->photo; @endphp
          <tr>
            <td>
              <div class="avatar-sm">
                @if($photo && $photo !== 'images/user.svg')
                  <img src="{{ asset($photo) }}" alt="">
                @else
                  {{ strtoupper(substr($profile?->name ?? 'S', 0, 1)) }}
                @endif
              </div>
            </td>
            <td>
              <div style="font-weight:600;">{{ $profile?->name ?? $stu?->user_id }}</div>
              <div style="font-size:11px;color:var(--text-2);">{{ $stu?->user_id }}</div>
            </td>
            <td style="color:var(--text-2);">{{ $stu?->mobile ?? '—' }}</td>
            <td>{{ $enr->course?->name ?? '—' }}</td>
            <td style="font-size:12px;color:var(--text-2);">
              {{ $enr->book_date ? \Carbon\Carbon::parse($enr->book_date)->format('d M Y') : '—' }}
            </td>
            <td>
              @if($stu)
                <a href="{{ route('institute.students.show', $stu) }}" class="btn btn-outline btn-sm">View</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-2);">No cancelled bookings.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($enrollments->hasPages())
    <div style="padding:14px 16px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
      <div style="font-size:12px;color:var(--text-2);">Showing {{ $enrollments->firstItem() }}–{{ $enrollments->lastItem() }} of {{ $enrollments->total() }}</div>
      <div style="display:flex;gap:4px;">
        @if(!$enrollments->onFirstPage())
          <a href="{{ $enrollments->previousPageUrl() }}" class="btn btn-outline btn-sm">← Prev</a>
        @endif
        @if($enrollments->hasMorePages())
          <a href="{{ $enrollments->nextPageUrl() }}" class="btn btn-outline btn-sm">Next →</a>
        @endif
      </div>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
const searchInput = document.getElementById('search-input');
const hiddenQ = document.getElementById('hidden-q');
const filterForm = document.getElementById('filter-form');
let debounceTimer;
searchInput?.addEventListener('keyup', function () {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => { hiddenQ.value = this.value.trim(); filterForm.submit(); }, 350);
});
</script>
@endpush
