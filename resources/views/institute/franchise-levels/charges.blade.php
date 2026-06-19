@extends('layouts.institute')
@section('title', 'Set Course Charges — ' . $franchiseLevel->name)
@section('page-title', 'Set Course Charges')
@section('topbar-actions')
  <a href="{{ route('institute.franchise-levels.index') }}" class="btn btn-outline btn-sm">← All Levels</a>
@endsection

@section('content')
@php
  $hasAnyCourses = collect($dursByType)->flatten(1)->isNotEmpty();
@endphp

{{-- Level info banner --}}
<div class="lcc-banner">
  <div>
    <div class="lcc-banner-name">{{ $franchiseLevel->name }}</div>
    <div class="lcc-banner-sub">
      Level Fee: <strong>₹{{ number_format($franchiseLevel->level_fee ?? 0, 2) }}</strong>
      &nbsp;·&nbsp; Commission: <strong>{{ $franchiseLevel->commission_percent }}%</strong>
    </div>
  </div>
  <a href="{{ route('institute.franchise-levels.charges.edit', $franchiseLevel) }}"
     class="btn btn-outline btn-sm">View All Charges →</a>
</div>

<div class="gt-card" style="margin-top:16px;">

  <div class="gt-card-header" style="border-bottom:1px solid var(--border-1); padding-bottom:14px; margin-bottom:0;">
    <div>
      <div class="gt-card-title">Duration-wise Charges</div>
      <div style="font-size:12.5px; color:var(--text-3); margin-top:3px;">
        Select a course type, then set admission &amp; certificate charge per duration.
        All courses of that duration &amp; type will be updated.
      </div>
    </div>
  </div>

  @if(!$hasAnyCourses)
    <div style="text-align:center; padding:50px 20px; color:var(--text-3);">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity=".4" style="margin-bottom:12px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      <div style="font-size:14px; margin-bottom:6px; font-weight:600;">No courses with duration found</div>
      <div style="font-size:12px;">Add courses with a duration set, then come back here.</div>
    </div>
  @else

  {{-- Course type tabs --}}
  <div class="lcc-tabs" id="lcc-tabs">
    <button type="button" class="lcc-tab active" data-type="0" onclick="lccSwitchTab(0)">
      All Types
      @if($dursByType[0]->isNotEmpty())
        <span class="lcc-tab-count">{{ $dursByType[0]->sum('course_count') }}</span>
      @endif
    </button>
    @foreach($courseTypes as $ct)
      @if(isset($dursByType[$ct->id]) && $dursByType[$ct->id]->isNotEmpty())
      <button type="button" class="lcc-tab" data-type="{{ $ct->id }}" onclick="lccSwitchTab({{ $ct->id }})">
        {{ $ct->name }}
        <span class="lcc-tab-count">{{ $dursByType[$ct->id]->sum('course_count') }}</span>
      </button>
      @endif
    @endforeach
  </div>

  <form method="POST" action="{{ route('institute.franchise-levels.charges.store', $franchiseLevel) }}" id="lcc-form">
    @csrf

    {{-- All Types section --}}
    <div class="lcc-section active" id="lcc-section-0">
      @if($dursByType[0]->isNotEmpty())
        @include('institute.franchise-levels._charges-table', [
          'durations'   => $dursByType[0],
          'typeId'      => 0,
          'typeName'    => 'All Types',
          'existing'    => $existingByDuration,
          'existingCert'=> $existingCertByDuration,
        ])
      @else
        <div class="lcc-empty-type">No courses with duration set.</div>
      @endif
    </div>

    {{-- Per-type sections --}}
    @foreach($courseTypes as $ct)
    @if(isset($dursByType[$ct->id]) && $dursByType[$ct->id]->isNotEmpty())
    <div class="lcc-section" id="lcc-section-{{ $ct->id }}" style="display:none;">
      @include('institute.franchise-levels._charges-table', [
        'durations'   => $dursByType[$ct->id],
        'typeId'      => $ct->id,
        'typeName'    => $ct->name,
        'existing'    => $existingByDuration,
        'existingCert'=> $existingCertByDuration,
      ])
    </div>
    @endif
    @endforeach

    <div class="lcc-footer">
      <div class="lcc-footer-note">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Charges from specific course types override "All Types" for the same duration.
        ₹0 entries are skipped.
      </div>
      <button type="submit" class="btn btn-primary">
        Save &amp; View All Courses →
      </button>
    </div>
  </form>

  @endif
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-top:12px; padding:10px 16px; background:rgba(42,138,74,.1); border:1px solid rgba(42,138,74,.3); border-radius:6px; color:#2a7a2a; font-size:13px;">
  {{ session('success') }}
</div>
@endif

@endsection

@push('styles')
<style>
.lcc-banner {
  display:flex; align-items:center; justify-content:space-between; gap:16px;
  background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--radius);
  padding:14px 20px;
}
.lcc-banner-name { font-size:16px; font-weight:700; color:var(--text-1); }
.lcc-banner-sub  { font-size:12px; color:var(--text-3); margin-top:3px; }

.lcc-tabs {
  display:flex; gap:2px; padding:12px 20px 0;
  border-bottom:1px solid var(--border-1);
  flex-wrap:wrap;
}
.lcc-tab {
  padding:8px 16px; font-size:12.5px; font-weight:600;
  border:none; background:none; cursor:pointer;
  color:var(--text-3); border-bottom:2.5px solid transparent;
  margin-bottom:-1px; display:flex; align-items:center; gap:6px;
  transition:color .15s, border-color .15s;
  border-radius:0;
}
.lcc-tab:hover { color:var(--text-1); }
.lcc-tab.active { color:var(--accent); border-bottom-color:var(--accent); }
.lcc-tab-count {
  font-size:10px; font-weight:700;
  background:var(--bg-3); color:var(--text-3);
  border-radius:20px; padding:1px 6px; border:1px solid var(--border-2);
}
.lcc-tab.active .lcc-tab-count { background:rgba(var(--accent-rgb),.12); color:var(--accent); border-color:rgba(var(--accent-rgb),.3); }

.lcc-section { padding:20px 20px 0; }
.lcc-empty-type { padding:30px 0; text-align:center; font-size:13px; color:var(--text-3); }

.lcc-table-wrap { border:1px solid var(--border-2); border-radius:var(--radius); overflow:hidden; }
.lcc-table { width:100%; border-collapse:collapse; font-size:13px; }
.lcc-table thead th {
  background:var(--bg-3); color:var(--text-3);
  font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px;
  padding:10px 16px; text-align:left; border-bottom:1px solid var(--border-2);
}
.lcc-table tbody tr { border-bottom:1px solid var(--border-1); }
.lcc-table tbody tr:last-child { border-bottom:none; }
.lcc-table tbody tr:hover { background:var(--bg-3); }
.lcc-table td { padding:12px 16px; vertical-align:middle; }

.lcc-dur-pill {
  font-size:12px; font-weight:700;
  background:rgba(138,115,245,.12); color:rgba(138,115,245,.9);
  border:1px solid rgba(138,115,245,.25); border-radius:20px;
  padding:4px 13px;
}
.lcc-course-count { font-size:12px; color:var(--text-3); }

.lcc-inp-wrap { display:flex; align-items:center; max-width:160px; }
.lcc-inp-pre {
  padding:6px 9px; font-size:13px; color:var(--text-3);
  background:var(--bg-3); border:1px solid var(--border-2);
  border-right:none; border-radius:var(--radius-sm) 0 0 var(--radius-sm);
}
.lcc-inp {
  flex:1; background:var(--bg-2); border:1px solid var(--border-2);
  border-radius:0 var(--radius-sm) var(--radius-sm) 0;
  color:var(--text-1); font-size:13px; padding:6px 9px; outline:none;
  transition:border-color .15s;
}
.lcc-inp:focus { border-color:var(--accent); }

.lcc-footer {
  display:flex; align-items:center; justify-content:space-between;
  gap:16px; padding:16px 20px 20px; flex-wrap:wrap;
}
.lcc-footer-note {
  display:flex; align-items:center; gap:7px;
  font-size:12px; color:var(--text-3); flex:1;
}
</style>
@endpush

@push('scripts')
<script>
function lccSwitchTab(typeId) {
  document.querySelectorAll('.lcc-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.lcc-section').forEach(s => s.style.display = 'none');

  const btn = document.querySelector('.lcc-tab[data-type="' + typeId + '"]');
  const sec = document.getElementById('lcc-section-' + typeId);
  if (btn) btn.classList.add('active');
  if (sec) sec.style.display = 'block';
}
</script>
@endpush
