@extends('layouts.institute')
@section('title','Enquiries')
@section('page-title','Enquiries')

@push('styles')
<style>
.enq-tabs { display:flex; gap:4px; flex-wrap:wrap; margin-bottom:18px; }
.enq-tab { padding:6px 16px; border-radius:20px; font-size:12px; font-weight:600; cursor:pointer;
           border:1.5px solid var(--border); background:var(--bg-2); color:var(--text-2);
           text-decoration:none; transition:.12s; }
.enq-tab:hover, .enq-tab.active { border-color:var(--accent); color:var(--accent); background:var(--accent-bg); }
.enq-tab.danger.active { border-color:#ef4444; color:#ef4444; background:#fef2f2; }

.enq-row { display:grid; grid-template-columns:40px 1fr auto; gap:14px; align-items:start;
           padding:14px 18px; border-bottom:1px solid var(--border); transition:.12s; }
.enq-row:last-child { border-bottom:none; }
.enq-row:hover { background:var(--bg-3); }
.enq-avatar { width:40px; height:40px; border-radius:50%; background:var(--accent);
              display:flex; align-items:center; justify-content:center;
              font-size:14px; font-weight:800; color:#fff; flex-shrink:0; }
.enq-name { font-size:14px; font-weight:700; }
.enq-sub  { font-size:12px; color:var(--text-2); margin-top:2px; }
.enq-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:10px;
             font-size:11px; font-weight:700; }
.badge-overdue { background:#fef2f2; color:#ef4444; }
.badge-today   { background:#fffbeb; color:#f59e0b; }
.badge-future  { background:var(--bg-3); color:var(--text-2); }
.badge-conv    { background:#f0fdf4; color:#22c55e; }
.badge-lost    { background:var(--bg-3); color:var(--text-2); }

.enq-actions { display:flex; gap:6px; align-items:center; flex-shrink:0; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
  <div>
    <div style="font-size:11px;color:var(--text-2);font-weight:600;letter-spacing:.5px;">ENQUIRY PIPELINE</div>
    <div style="font-size:12px;color:var(--text-2);margin-top:3px;">
      {{ $total }} total &nbsp;&middot;&nbsp;
      Conversion rate:
      <strong style="color:{{ $convRate >= 50 ? 'var(--success)' : ($convRate >= 25 ? '#f59e0b' : 'var(--danger)') }};">
        {{ $convRate }}%
      </strong>
      ({{ $counts['converted'] }} of {{ $total }} converted)
    </div>
  </div>
  <a href="{{ route('institute.enquiries.create') }}" class="btn btn-primary">+ New Enquiry</a>
</div>

{{-- Tabs --}}
<div class="enq-tabs">
  <a href="{{ route('institute.enquiries.index', ['tab'=>'open','q'=>$search]) }}"
     class="enq-tab {{ $tab==='open' && !$outcome ? 'active' : '' }}">
     Open <span style="opacity:.7">({{ $counts['open'] }})</span>
  </a>
  <a href="{{ route('institute.enquiries.index', ['tab'=>'due','q'=>$search]) }}"
     class="enq-tab danger {{ $tab==='due' ? 'active' : '' }}">
     Follow-up Due <span style="opacity:.7">({{ $counts['due'] }})</span>
  </a>
  <a href="{{ route('institute.enquiries.index', ['tab'=>'converted','q'=>$search]) }}"
     class="enq-tab {{ $tab==='converted' ? 'active' : '' }}">
     Converted <span style="opacity:.7">({{ $counts['converted'] }})</span>
  </a>
  <a href="{{ route('institute.enquiries.index', ['tab'=>'lost','q'=>$search]) }}"
     class="enq-tab {{ $tab==='lost' ? 'active' : '' }}">
     Lost <span style="opacity:.7">({{ $counts['lost'] }})</span>
  </a>
</div>

{{-- Outcome filter (only for open tab) --}}
@if($tab === 'open')
@php
  $outcomeLabels = [
    'INTERESTED'     => ['label'=>'Interested',     'color'=>'#16a34a','bg'=>'#f0fdf4'],
    'CALLBACK'       => ['label'=>'Callback',        'color'=>'#d97706','bg'=>'#fffbeb'],
    'NOT_INTERESTED' => ['label'=>'Not Interested',  'color'=>'#ef4444','bg'=>'#fef2f2'],
    'NO_RESPONSE'    => ['label'=>'No Response',     'color'=>'#64748b','bg'=>'var(--bg-3)'],
  ];
@endphp
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;align-items:center;">
  <span style="font-size:11px;color:var(--text-2);font-weight:600;">Filter by last outcome:</span>
  @foreach($outcomeLabels as $key => $meta)
    @php $cnt = $outcomeCounts[$key] ?? 0; @endphp
    <a href="{{ route('institute.enquiries.index', ['tab'=>'open','outcome'=>$key,'q'=>$search]) }}"
       style="padding:3px 12px;border-radius:20px;font-size:11.5px;font-weight:700;text-decoration:none;
              border:1.5px solid {{ $outcome===$key ? $meta['color'] : 'var(--border)' }};
              color:{{ $outcome===$key ? $meta['color'] : 'var(--text-2)' }};
              background:{{ $outcome===$key ? $meta['bg'] : 'var(--bg-2)' }};">
      {{ $meta['label'] }} ({{ $cnt }})
    </a>
  @endforeach
  @if($outcome)
    <a href="{{ route('institute.enquiries.index', ['tab'=>'open','q'=>$search]) }}"
       style="padding:3px 12px;border-radius:20px;font-size:11.5px;font-weight:600;
              text-decoration:none;color:var(--text-2);border:1.5px solid var(--border);">
      &times; Clear
    </a>
  @endif
</div>
@endif

{{-- Search --}}
<form method="GET" action="{{ route('institute.enquiries.index') }}" id="enq-search-form" style="margin-bottom:16px;">
  <input type="hidden" name="tab" value="{{ $tab }}">
  @if($outcome)<input type="hidden" name="outcome" value="{{ $outcome }}">@endif
  <div style="display:flex;gap:8px;max-width:480px;position:relative;">
    <div style="position:relative;flex:1;">
      <svg style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-2);pointer-events:none;"
           width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" name="q" id="enq-search-input" value="{{ $search }}" class="gt-input"
             placeholder="Search by name, mobile or email…"
             style="padding-left:34px;flex:1;width:100%;"
             autocomplete="off">
    </div>
    @if($search)
      <a href="{{ route('institute.enquiries.index', ['tab'=>$tab,'outcome'=>$outcome]) }}"
         class="btn btn-secondary" title="Clear search">&times;</a>
    @endif
  </div>
</form>

@push('scripts')
<script>
(function () {
  const input = document.getElementById('enq-search-input');
  const form  = document.getElementById('enq-search-form');
  if (!input || !form) return;

  let timer;
  input.addEventListener('keyup', function (e) {
    // Submit immediately on Enter
    if (e.key === 'Enter') {
      clearTimeout(timer);
      form.submit();
      return;
    }
    clearTimeout(timer);
    timer = setTimeout(() => form.submit(), 380);
  });
})();
</script>
@endpush

{{-- Session flash --}}
@if(session('success'))
  <div class="gt-alert gt-alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

{{-- List --}}
<div class="gt-card" style="padding:0;">
  <div style="padding:12px 18px;border-bottom:1px solid var(--border);font-size:13px;font-weight:600;color:var(--text-2);">
    {{ $enquiries->total() }} enquiries
  </div>

  @forelse($enquiries as $enq)
    @php
      $initial = strtoupper(substr($enq->name, 0, 1));
      $lastFollowup = $enq->followups->first();

      if ($enq->status === 'CONVERTED') {
          $badgeClass = 'badge-conv'; $badgeText = 'Converted';
      } elseif ($enq->status === 'LOST') {
          $badgeClass = 'badge-lost'; $badgeText = 'Lost';
      } elseif ($enq->isOverdue()) {
          $badgeClass = 'badge-overdue'; $badgeText = '⚠ Overdue';
      } elseif ($enq->isDueToday()) {
          $badgeClass = 'badge-today'; $badgeText = '● Due Today';
      } else {
          $badgeClass = 'badge-future';
          $badgeText = $enq->next_followup_date ? 'Follow-up: '.$enq->next_followup_date->format('d M') : 'No date set';
      }
    @endphp

    <div class="enq-row">
      {{-- Avatar --}}
      <div class="enq-avatar" style="{{ $enq->status==='LOST' ? 'opacity:.5' : '' }}">{{ $initial }}</div>

      {{-- Info --}}
      <div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
          <span class="enq-name">{{ $enq->name }}</span>
          <span class="enq-badge {{ $badgeClass }}">{{ $badgeText }}</span>
        </div>
        <div class="enq-sub">
          {{ $enq->mobile }}
          @if($enq->course) &nbsp;·&nbsp; {{ $enq->course->name }} @endif
          &nbsp;·&nbsp;
          <span style="text-transform:uppercase;font-size:10px;font-weight:700;">{{ str_replace('_',' ',$enq->source) }}</span>
          &nbsp;·&nbsp; {{ $enq->created_at->format('d M Y') }}
        </div>
        @if($lastFollowup)
          <div class="enq-sub" style="margin-top:4px;font-style:italic;color:var(--text-2);">
            Last note ({{ $lastFollowup->created_at->diffForHumans() }}): "{{ Str::limit($lastFollowup->notes, 80) }}"
          </div>
        @elseif($enq->notes)
          <div class="enq-sub" style="margin-top:4px;font-style:italic;">
            "{{ Str::limit($enq->notes, 80) }}"
          </div>
        @endif
        <div style="margin-top:4px;font-size:11px;color:var(--text-2);">
          {{ $enq->followups->count() }} follow-up{{ $enq->followups->count() !== 1 ? 's' : '' }} logged
        </div>
      </div>

      {{-- Actions --}}
      <div class="enq-actions">
        <a href="{{ route('institute.enquiries.show', $enq) }}" class="btn btn-secondary btn-sm">View</a>
        @if($enq->status === 'OPEN')
          <a href="{{ route('institute.enquiries.convert', $enq) }}" class="btn btn-primary btn-sm" style="white-space:nowrap;">
            → Admission
          </a>
        @endif
      </div>
    </div>
  @empty
    <div style="padding:48px;text-align:center;color:var(--text-2);">
      <div style="font-size:14px;font-weight:600;">No enquiries found</div>
      <div style="font-size:12px;margin-top:4px;">
        @if($search) No results for "{{ $search }}".
        @else No enquiries in this tab yet.
        @endif
      </div>
    </div>
  @endforelse
</div>

{{-- Pagination --}}
@if($enquiries->hasPages())
  <div style="margin-top:16px;">{{ $enquiries->links() }}</div>
@endif

@endsection
