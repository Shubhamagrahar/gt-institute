@extends('layouts.student')
@section('title','Available Courses')
@section('page-title','Available Courses')

@push('styles')
<style>
.course-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px; }
.course-card { background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:20px;transition:.15s; }
.course-card:hover { border-color:rgba(16,185,129,.35);box-shadow:0 4px 20px rgba(0,0,0,.12); }
.course-type-badge { font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;background:rgba(108,93,211,.12);color:#a89cf5;display:inline-block;margin-bottom:10px; }
.course-name { font-size:14px;font-weight:700;color:var(--text-1);margin-bottom:6px;line-height:1.4; }
.course-meta { font-size:12px;color:var(--text-3);margin-bottom:14px; }
.course-fee { font-size:18px;font-weight:900;color:#10b981; }
.course-fee-label { font-size:10px;color:var(--text-3);margin-bottom:2px; }
.fee-row { display:flex;justify-content:space-between;padding:5px 0;font-size:12px;color:var(--text-3);border-bottom:1px solid var(--border); }
.fee-row:last-child { border-bottom:none; }
.fee-row strong { color:var(--text-2); }
.already-enrolled { background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:8px;padding:8px 12px;font-size:12px;color:#10b981;font-weight:600;text-align:center; }
.apply-btn { width:100%;padding:10px;border-radius:8px;border:none;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px; }
.apply-btn:hover { opacity:.9; }
.type-filter { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px; }
.type-btn { padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--bg-2);color:var(--text-3);font-size:12px;font-weight:600;cursor:pointer;transition:.15s;font-family:inherit; }
.type-btn:hover, .type-btn.active { background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.3);color:#10b981; }
</style>
@endpush

@section('content')

<div style="margin-bottom:20px;">
  <div style="font-size:13px;color:var(--text-3);">Browse available courses offered by your institute and apply directly.</div>
</div>

{{-- Filter by type --}}
@if($courseTypes->isNotEmpty())
<div class="type-filter">
  <button class="type-btn active" onclick="filterType('all', this)">All Courses</button>
  @foreach($courseTypes as $type)
  <button class="type-btn" onclick="filterType('{{ $type->id }}', this)">{{ $type->name }}</button>
  @endforeach
</div>
@endif

@if($courses->isEmpty())
  <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:48px;text-align:center;color:var(--text-3);">
    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:.3;margin-bottom:12px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    <div style="font-size:14px;font-weight:600;">No courses available at the moment.</div>
    <div style="font-size:12px;margin-top:4px;">Please contact your institute for more information.</div>
  </div>
@else
  <div class="course-grid" id="courseGrid">
    @foreach($courses as $course)
    <div class="course-card" data-type="{{ $course->course_type_id }}">
      <div class="course-type-badge">{{ $course->courseType?->name ?? 'Course' }}</div>
      <div class="course-name">{{ $course->name }}</div>
      <div class="course-meta">
        @if($course->duration) {{ $course->duration }} month{{ $course->duration == 1 ? '' : 's' }} &nbsp;·&nbsp; @endif
        {{ $course->course_code ?? '' }}
      </div>

      {{-- Fee breakdown --}}
      @if($course->feeStructures->isNotEmpty())
      <div style="background:var(--bg-3);border-radius:8px;padding:10px 12px;margin-bottom:14px;">
        @foreach($course->feeStructures as $fs)
        <div class="fee-row">
          <span>{{ $fs->feeType?->name ?? 'Fee' }}</span>
          <strong>₹{{ number_format($fs->amount ?? 0) }}</strong>
        </div>
        @endforeach
        <div style="display:flex;justify-content:space-between;padding-top:8px;margin-top:4px;border-top:1px solid var(--border);">
          <span style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Total</span>
          <span style="font-size:15px;font-weight:900;color:#10b981;">₹{{ number_format($course->feeStructures->sum('amount')) }}</span>
        </div>
      </div>
      @elseif($course->fee)
      <div style="margin-bottom:14px;">
        <div class="course-fee-label">Course Fee</div>
        <div class="course-fee">₹{{ number_format($course->fee) }}</div>
      </div>
      @else
      <div style="margin-bottom:14px;font-size:12px;color:var(--text-3);">Fee details not available</div>
      @endif

      {{-- CTA --}}
      @if($bookedCourseIds->contains($course->id))
        <div class="already-enrolled">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg>
          Already Enrolled
        </div>
      @else
        <a href="{{ route('institute.enrollment.choose') }}" target="_blank"
           style="display:block;text-decoration:none;">
          <button class="apply-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Apply / Contact Institute
          </button>
        </a>
      @endif
    </div>
    @endforeach
  </div>

  <div style="margin-top:20px;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.15);border-radius:10px;padding:14px 18px;font-size:13px;color:var(--text-3);">
    <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24" style="display:inline;margin-right:6px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    To apply for a new course, please visit your institute or contact the admin. They will register your seat from their panel.
  </div>
@endif

@endsection

@push('scripts')
<script>
function filterType(typeId, btn) {
  document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('#courseGrid .course-card').forEach(card => {
    card.style.display = (typeId === 'all' || card.dataset.type == typeId) ? '' : 'none';
  });
}
</script>
@endpush
