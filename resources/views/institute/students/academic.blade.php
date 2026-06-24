@extends('layouts.institute')
@section('title','Student Academic History')
@section('page-title','Student Academic History')

@push('styles')
<style>
.ac-search-wrap { max-width:600px; margin:0 auto 28px; position:relative; }
.ac-search-box { display:flex; gap:0; border:1.5px solid var(--border); border-radius:12px; overflow:hidden; background:var(--bg-2); transition:border-color .15s; }
.ac-search-box:focus-within { border-color:var(--accent); }
.ac-search-input { flex:1; border:none; background:transparent; padding:13px 16px; font-size:15px; color:var(--text); outline:none; }
.ac-search-btn { padding:0 22px; background:var(--accent); color:#fff; border:none; font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap; }
.ac-search-hint { font-size:12px; color:var(--text-2); margin-top:8px; text-align:center; }

/* Suggestion dropdown */
.ac-suggest { position:absolute; top:calc(100% + 4px); left:0; right:0; background:var(--bg-1);
  border:1.5px solid var(--accent); border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.15);
  z-index:100; overflow:hidden; display:none; }
.ac-suggest.open { display:block; }
.ac-suggest-item { display:flex; align-items:center; gap:12px; padding:10px 16px; cursor:pointer;
  transition:background .1s; border-bottom:1px solid var(--border); }
.ac-suggest-item:last-child { border-bottom:none; }
.ac-suggest-item:hover, .ac-suggest-item.focused { background:var(--accent-bg); }
.ac-suggest-avatar { width:34px; height:34px; border-radius:50%; background:var(--accent);
  display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800;
  color:#fff; flex-shrink:0; }
.ac-suggest-name { font-size:14px; font-weight:700; }
.ac-suggest-meta { font-size:11px; color:var(--text-2); margin-top:1px; }
.ac-suggest-empty { padding:16px; text-align:center; font-size:13px; color:var(--text-2); }

.ac-profile-card { display:flex; align-items:center; gap:16px; padding:18px 20px; border-bottom:1px solid var(--border); }
.ac-avatar { width:52px; height:52px; border-radius:50%; background:var(--accent); display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:900; color:#fff; overflow:hidden; flex-shrink:0; }
.ac-avatar img { width:100%; height:100%; object-fit:cover; }
.ac-name { font-size:18px; font-weight:800; }
.ac-meta { font-size:12px; color:var(--text-2); margin-top:3px; }

.enr-row { display:grid; grid-template-columns:1fr auto; gap:12px; align-items:start; padding:16px 20px; border-bottom:1px solid var(--border); transition:.12s; }
.enr-row:last-child { border-bottom:none; }
.enr-row:hover { background:var(--bg-3); }
.enr-course { font-weight:700; font-size:14px; }
.enr-meta { font-size:12px; color:var(--text-2); margin-top:3px; }
.enr-fee-row { display:flex; gap:10px; margin-top:8px; flex-wrap:wrap; }
.enr-fee-chip { font-size:11px; font-weight:700; padding:2px 10px; border-radius:6px; }
.chip-total { background:var(--bg-3); color:var(--text-2); }
.chip-paid  { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
.chip-due   { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.chip-nodue { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }

.status-badge { display:inline-block; font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; padding:2px 9px; border-radius:20px; }
.s-run    { background:#dcfce7; color:#16a34a; }
.s-open   { background:#fef9c3; color:#a16207; }
.s-close  { background:#f1f5f9; color:#64748b; }
.s-expired{ background:#fef2f2; color:#dc2626; }
.s-cancel { background:#f3f4f6; color:#6b7280; }
</style>
@endpush

@section('content')

<div class="ac-search-wrap">
  <form id="ac-form" method="GET" action="{{ route('institute.students.academic') }}">
    <div class="ac-search-box">
      <input type="text" id="ac-input" name="q" class="ac-search-input"
             placeholder="Search student by name, mobile, or ID…"
             value="{{ $search }}" autocomplete="off">
      <button type="submit" class="ac-search-btn">Search</button>
    </div>
  </form>
  <div class="ac-suggest" id="ac-suggest"></div>
  <div class="ac-search-hint">Type at least 2 characters to see suggestions</div>
</div>

@if($search)
  @if(!$student)
    <div class="gt-card" style="padding:48px;text-align:center;color:var(--text-2);">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.35;margin-bottom:10px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <div style="font-size:14px;font-weight:600;">No student found for "{{ $search }}"</div>
      <div style="font-size:12px;margin-top:4px;">Try mobile number or student ID.</div>
    </div>
  @else
    @php
      $profile  = $student->profile;
      $photo    = $profile?->photo;
      $hasPhoto = $photo && !in_array($photo, ['images/user.svg','images/user.png']);
      $name     = $profile?->name ?? $student->user_id;
    @endphp
    <div class="gt-card" style="overflow:hidden;padding:0;">

      <div class="ac-profile-card">
        <div class="ac-avatar">
          @if($hasPhoto)<img src="{{ asset($photo) }}" alt="">@else{{ strtoupper(substr($name,0,1)) }}@endif
        </div>
        <div style="flex:1">
          <div class="ac-name">{{ $name }}</div>
          <div class="ac-meta">
            <code style="color:var(--accent)">{{ $student->user_id }}</code>
            &nbsp;·&nbsp; {{ $student->mobile }}
            @if($student->email) &nbsp;·&nbsp; {{ $student->email }} @endif
          </div>
        </div>
        <a href="{{ route('institute.students.show', $student) }}" class="btn btn-outline btn-sm">View Profile</a>
      </div>

      @php $counts = $enrollments->groupBy('status')->map->count(); @endphp
      <div style="display:flex;gap:0;border-bottom:1px solid var(--border);flex-wrap:wrap;">
        @foreach(['RUN'=>'Running','OPEN'=>'Seat Booked','CLOSE'=>'Completed','EXPIRED'=>'Expired','CANCEL'=>'Cancelled'] as $st => $lbl)
          @if(($counts[$st] ?? 0) > 0)
            <div style="padding:8px 16px;font-size:12px;font-weight:600;border-right:1px solid var(--border);">
              <span class="status-badge s-{{ strtolower($st) }}">{{ $lbl }}</span>
              <span style="margin-left:5px;font-size:13px;font-weight:800;">{{ $counts[$st] }}</span>
            </div>
          @endif
        @endforeach
        <div style="padding:8px 16px;font-size:12px;font-weight:700;color:var(--text-2);margin-left:auto;">
          {{ $enrollments->count() }} total
        </div>
      </div>

      @forelse($enrollments as $e)
        <div class="enr-row">
          <div>
            <div class="enr-course">{{ $e->course?->name ?? '—' }}</div>
            <div class="enr-meta">
              {{ $e->batch?->name ?? 'No Batch' }}
              @if($e->enrollment_no) &nbsp;·&nbsp; <code style="font-size:11px">{{ $e->enrollment_no }}</code> @endif
              &nbsp;·&nbsp; Booked {{ $e->book_date ? \Carbon\Carbon::parse($e->book_date)->format('d M Y') : '—' }}
            </div>
            <div class="enr-fee-row">
              <span class="enr-fee-chip chip-total">Total ₹{{ number_format($e->final_fee,2) }}</span>
              <span class="enr-fee-chip chip-paid">Paid ₹{{ number_format($e->paid_total,2) }}</span>
              @if($e->due_total > 0)
                <span class="enr-fee-chip chip-due">Due ₹{{ number_format($e->due_total,2) }}</span>
              @else
                <span class="enr-fee-chip chip-nodue">Fully Paid</span>
              @endif
            </div>
          </div>
          <div style="text-align:right;flex-shrink:0;">
            <span class="status-badge s-{{ strtolower($e->status) }}">
              {{ match($e->status){ 'OPEN'=>'Seat Booked','RUN'=>'Running','CLOSE'=>'Completed','EXPIRED'=>'Expired','CANCEL'=>'Cancelled', default=>$e->status } }}
            </span>
            <div style="margin-top:8px;display:flex;gap:5px;justify-content:flex-end;">
              <a href="{{ route('institute.enrollment.payment-complete', $e) }}" class="btn btn-outline btn-xs">Details</a>
              @if(in_array($e->status, ['OPEN','RUN']))
                <a href="{{ route('institute.enrollment.fee', $e) }}" class="btn btn-primary btn-xs">Fee</a>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div style="padding:36px;text-align:center;color:var(--text-2);font-size:13px;">No enrollments found.</div>
      @endforelse
    </div>
  @endif
@else
  <div style="text-align:center;padding:60px 20px;color:var(--text-2);">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="opacity:.3;margin-bottom:14px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    <div style="font-size:15px;font-weight:700;">Search a student to view their academic history</div>
    <div style="font-size:13px;margin-top:6px;">All course enrollments, fee status, and admission history.</div>
  </div>
@endif

@endsection

@push('scripts')
<script>
(function () {
  const input   = document.getElementById('ac-input');
  const suggest = document.getElementById('ac-suggest');
  const form    = document.getElementById('ac-form');
  const url     = '{{ route("institute.students.suggest") }}';
  let timer, focusIdx = -1, results = [];

  if (!input) return;

  input.addEventListener('input', function () {
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) { closeSuggest(); return; }
    timer = setTimeout(() => fetchSuggest(q), 250);
  });

  input.addEventListener('keydown', function (e) {
    const items = suggest.querySelectorAll('.ac-suggest-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); focusIdx = Math.min(focusIdx + 1, items.length - 1); highlightItem(items); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); focusIdx = Math.max(focusIdx - 1, -1); highlightItem(items); }
    else if (e.key === 'Enter' && focusIdx >= 0) { e.preventDefault(); items[focusIdx]?.click(); }
    else if (e.key === 'Escape') { closeSuggest(); }
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.ac-search-wrap')) closeSuggest();
  });

  function fetchSuggest(q) {
    fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        results = data;
        renderSuggest(data);
      });
  }

  function renderSuggest(data) {
    focusIdx = -1;
    if (!data.length) {
      suggest.innerHTML = '<div class="ac-suggest-empty">No students found</div>';
      suggest.classList.add('open');
      return;
    }
    suggest.innerHTML = data.map((s, i) => `
      <div class="ac-suggest-item" data-idx="${i}" data-mobile="${s.mobile}">
        <div class="ac-suggest-avatar">${s.name.charAt(0).toUpperCase()}</div>
        <div>
          <div class="ac-suggest-name">${s.name}</div>
          <div class="ac-suggest-meta">${s.uid} &nbsp;·&nbsp; ${s.mobile}</div>
        </div>
      </div>
    `).join('');
    suggest.classList.add('open');

    suggest.querySelectorAll('.ac-suggest-item').forEach(item => {
      item.addEventListener('click', function () {
        input.value = this.dataset.mobile;
        closeSuggest();
        form.submit();
      });
    });
  }

  function highlightItem(items) {
    items.forEach((el, i) => el.classList.toggle('focused', i === focusIdx));
    if (focusIdx >= 0) items[focusIdx]?.scrollIntoView({ block: 'nearest' });
  }

  function closeSuggest() {
    suggest.classList.remove('open');
    focusIdx = -1;
  }
})();
</script>
@endpush
