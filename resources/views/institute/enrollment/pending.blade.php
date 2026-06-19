@extends('layouts.institute')
@section('title','Pending Admissions')
@section('page-title','Pending Admissions')

@push('styles')
<style>
/* Stats */
.adm-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:22px; }
@media(max-width:640px){ .adm-stats { grid-template-columns:1fr 1fr; } }
.adm-stat { background:var(--bg-2); border:1px solid var(--border); border-radius:12px; padding:14px 18px; }
.adm-stat-num { font-size:26px; font-weight:900; line-height:1; }
.adm-stat-label { font-size:11px; color:var(--text-2); margin-top:4px; font-weight:600; letter-spacing:.4px; }

/* Filter pills */
.adm-filters { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:18px; }
.adm-pill { padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600; cursor:pointer;
            border:1.5px solid var(--border); background:var(--bg-2); color:var(--text-2);
            text-decoration:none; transition:.12s; }
.adm-pill:hover, .adm-pill.active { border-color:var(--accent); color:var(--accent); background:var(--accent-bg); }

/* Student card */
.adm-card { display:grid; grid-template-columns:44px 1fr auto; gap:14px; align-items:start;
            padding:16px 20px; border-bottom:1px solid var(--border); transition:.12s; }
.adm-card:last-child { border-bottom:none; }
.adm-card:hover { background:var(--bg-3); }
.adm-avatar { width:44px; height:44px; border-radius:50%; background:var(--accent);
              display:flex; align-items:center; justify-content:center;
              font-size:15px; font-weight:800; color:#fff; overflow:hidden; flex-shrink:0; }
.adm-avatar img { width:100%; height:100%; object-fit:cover; }
.adm-name { font-weight:700; font-size:14px; }
.adm-sub { font-size:12px; color:var(--text-2); margin-top:2px; }

/* Progress bar */
.adm-progress { display:flex; align-items:center; gap:0; margin-top:10px; }
.adm-step { display:flex; align-items:center; gap:5px; font-size:11px; font-weight:600; }
.adm-step-dot { width:20px; height:20px; border-radius:50%; display:flex; align-items:center;
                justify-content:center; font-size:10px; flex-shrink:0; }
.adm-step-dot.done  { background:var(--accent); color:#fff; }
.adm-step-dot.next  { background:#f59e0b; color:#fff; }
.adm-step-dot.idle  { background:var(--bg-3); color:var(--text-2); border:1.5px solid var(--border); }
.adm-step-line { width:28px; height:2px; margin: 0 2px; background:var(--border); flex-shrink:0; }
.adm-step-line.done { background:var(--accent); }
.adm-step-label { color:var(--text-2); white-space:nowrap; }
.adm-step-label.done { color:var(--accent); }
.adm-step-label.next { color:#f59e0b; }

.adm-actions { display:flex; flex-direction:column; gap:6px; align-items:flex-end; flex-shrink:0; }
</style>
@endpush

@section('content')

{{-- Stats --}}
<div class="adm-stats">
  <div class="adm-stat">
    <div class="adm-stat-num">{{ $countTotal }}</div>
    <div class="adm-stat-label">TOTAL PENDING</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-num" style="color:#ef4444">{{ $countDetailsPending }}</div>
    <div class="adm-stat-label">DETAILS PENDING</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-num" style="color:#f59e0b">{{ $countPaymentPending }}</div>
    <div class="adm-stat-label">PAYMENT PENDING</div>
  </div>
</div>

{{-- Filter pills + search --}}
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:18px;">
  <div class="adm-filters" style="margin-bottom:0;flex:1;min-width:0;">
    <a href="{{ route('institute.enrollment.pending') }}" class="adm-pill {{ !request('filter') ? 'active' : '' }}">All ({{ $countTotal }})</a>
    <a href="{{ route('institute.enrollment.pending', ['filter'=>'details']) }}" class="adm-pill {{ request('filter')==='details' ? 'active' : '' }}">Details Pending ({{ $countDetailsPending }})</a>
    <a href="{{ route('institute.enrollment.pending', ['filter'=>'payment']) }}" class="adm-pill {{ request('filter')==='payment' ? 'active' : '' }}">Payment Pending ({{ $countPaymentPending }})</a>
  </div>
  <div style="position:relative;flex-shrink:0;">
    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-2);pointer-events:none;"
         width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="pending-search" placeholder="Search name or mobile…"
           style="padding:6px 10px 6px 30px;border:1px solid var(--border);border-radius:20px;
                  font-size:12px;background:var(--bg-2);color:var(--text);width:220px;outline:none;">
  </div>
</div>

{{-- Cards --}}
<div class="gt-card" style="padding:0;">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
    <div class="gt-card-title" style="margin:0;">Seat Booked — Pending</div>
    <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-primary btn-sm">+ New Booking</a>
  </div>

  @php
    $filter = request('filter');
    $shown  = $openBooks->when($filter === 'details', fn($c) => $c->where('details_complete', false))
                        ->when($filter === 'payment', fn($c) => $c->where('details_complete', true));
  @endphp

  @if($shown->isEmpty())
    <div style="padding:48px;text-align:center;color:var(--text-2);">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.4;margin-bottom:10px;"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M9 12l2 2 4-4"/></svg>
      <div style="font-size:14px;font-weight:600;">No pending bookings</div>
      <div style="font-size:12px;margin-top:4px;">All students have been admitted or there are no bookings.</div>
    </div>
  @else
    @foreach($shown as $book)
      @php
        $profile = $book->student?->profile;
        $photo   = $profile?->photo;
        $name    = $profile?->name ?? $book->student?->user_id ?? '—';
        $initials = strtoupper(substr($name, 0, 1));
        $stage   = $book->stage; // 1=booked only, 2=details filled
        $daysAgo = $book->created_at?->diffInDays(now());
      @endphp
      <div class="adm-card" data-search="{{ strtolower($name . ' ' . ($book->student?->mobile ?? '')) }}">

        {{-- Avatar --}}
        <div class="adm-avatar">
          @if($photo && !in_array($photo, ['images/user.svg','images/user.png']))
            <img src="{{ asset($photo) }}" alt="">
          @else
            {{ $initials }}
          @endif
        </div>

        {{-- Info --}}
        <div>
          <div class="adm-name">{{ $name }}</div>
          <div class="adm-sub">
            {{ $book->student?->mobile }}
            &nbsp;·&nbsp; {{ $book->course?->name ?? '—' }}
            @if($book->batch) &nbsp;·&nbsp; {{ $book->batch->name }} @endif
          </div>
          <div class="adm-sub">
            Booked {{ $book->book_date?->format('d M Y') }}
            @if($daysAgo > 0) &nbsp;·&nbsp; {{ $daysAgo }} din pehle @endif
            &nbsp;·&nbsp;
            <span style="font-size:11px;font-weight:600;color:{{ $book->booking_mode==='quick' ? '#f59e0b' : 'var(--accent)' }}">
              {{ strtoupper($book->booking_mode) }}
            </span>
          </div>

          {{-- 4-Step Progress --}}
          <div class="adm-progress">
            {{-- Step 1: Booked --}}
            <div class="adm-step">
              <div class="adm-step-dot done">✓</div>
              <span class="adm-step-label done">Booked</span>
            </div>
            <div class="adm-step-line done"></div>

            {{-- Step 2: Details --}}
            <div class="adm-step">
              <div class="adm-step-dot {{ $stage >= 2 ? 'done' : 'next' }}">{{ $stage >= 2 ? '✓' : '2' }}</div>
              <span class="adm-step-label {{ $stage >= 2 ? 'done' : 'next' }}">Details</span>
            </div>
            <div class="adm-step-line {{ $stage >= 2 ? 'done' : '' }}"></div>

            {{-- Step 3: Payment --}}
            <div class="adm-step">
              <div class="adm-step-dot {{ $stage >= 2 ? 'next' : 'idle' }}">{{ $stage >= 2 ? '3' : '3' }}</div>
              <span class="adm-step-label {{ $stage >= 2 ? 'next' : '' }}">Payment</span>
            </div>
            <div class="adm-step-line"></div>

            {{-- Step 4: Admitted --}}
            <div class="adm-step">
              <div class="adm-step-dot idle">4</div>
              <span class="adm-step-label">Admitted</span>
            </div>
          </div>
        </div>

        {{-- Actions --}}
        <div class="adm-actions">
          @if($stage === 1)
            <a href="{{ route('institute.enrollment.profile', $book) }}" class="btn btn-primary btn-sm" style="white-space:nowrap;">
              Fill Details →
            </a>
            <span style="font-size:11px;color:#ef4444;font-weight:600;">Details pending</span>
          @else
            <a href="{{ route('institute.fee-collect.show', $book->student) }}" class="btn btn-primary btn-sm" style="white-space:nowrap;">
              Collect Fee →
            </a>
            <span style="font-size:11px;color:#f59e0b;font-weight:600;">Payment pending</span>
          @endif
        </div>

      </div>
    @endforeach
  @endif
</div>

@endsection

@push('scripts')
<script>
(function () {
  const input = document.getElementById('pending-search');
  if (!input) return;
  let timer;
  input.addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      const q = input.value.trim().toLowerCase();
      document.querySelectorAll('.adm-card[data-search]').forEach(card => {
        card.style.display = !q || card.dataset.search.includes(q) ? '' : 'none';
      });
    }, 300);
  });
})();
</script>
@endpush
