@extends('layouts.institute')
@section('title', 'Fees Dashboard')
@section('page-title', 'Fees & Enrollment Dashboard')

@push('styles')
<style>
/* ── Layout ── */
.fd-wrap { display:flex; gap:18px; min-height:calc(100vh - 140px); align-items:flex-start; }

/* ── Sidebar ── */
.fd-sidebar {
  width:220px; flex-shrink:0;
  background:var(--bg-1);
  border:1px solid var(--border);
  border-radius:16px;
  padding:14px 0 18px;
  position:sticky; top:74px;
}
.fd-sec-label {
  font-size:10px; font-weight:800; letter-spacing:.12em;
  text-transform:uppercase; color:var(--text-3);
  padding:10px 18px 5px;
}
.fd-nav {
  display:flex; flex-direction:column; gap:1px;
}
.fd-nav-item {
  display:flex; align-items:center; justify-content:space-between;
  padding:8px 18px; font-size:13.5px; font-weight:500;
  color:var(--text-1); cursor:pointer;
  border:none; background:none; text-align:left; width:100%;
  border-radius:0; transition:background .14s;
  text-decoration:none;
}
.fd-nav-item:hover { background:var(--bg-2); }
.fd-nav-item.active {
  background:var(--primary-light, #e8f0fe);
  color:var(--primary, #1a56db);
  font-weight:700;
}
.fd-nav-item .cnt {
  font-size:11px; font-weight:700; padding:2px 7px;
  border-radius:999px; background:var(--bg-3);
  color:var(--text-2); min-width:22px; text-align:center;
}
.fd-nav-item.active .cnt { background:var(--primary,#1a56db); color:#fff; }

/* ── Main content ── */
.fd-main { flex:1; min-width:0; }

/* ── Tab panes ── */
.fd-tab { display:none; }
.fd-tab.fd-active { display:block; }

/* ── Section header ── */
.fd-head {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:16px;
}
.fd-title { font-size:18px; font-weight:800; color:var(--text-1); }
.fd-sub   { font-size:13px; color:var(--text-2); margin-top:2px; }

/* ── Table ── */
.fd-tbl { width:100%; border-collapse:collapse; font-size:13px; }
.fd-tbl th {
  background:var(--bg-3); padding:10px 12px;
  text-align:left; font-size:11px; font-weight:700;
  text-transform:uppercase; letter-spacing:.07em;
  color:var(--text-2); white-space:nowrap;
}
.fd-tbl td { padding:10px 12px; border-bottom:1px solid var(--border); vertical-align:middle; }
.fd-tbl tr:last-child td { border-bottom:none; }
.fd-tbl tbody tr:hover td { background:var(--bg-2); }

.due-badge {
  display:inline-block; padding:2px 8px;
  border-radius:6px; font-size:11px; font-weight:700;
  background:#fef2f2; color:#b91c1c;
}
.late-badge {
  display:inline-block; padding:2px 8px;
  border-radius:6px; font-size:11px; font-weight:700;
  background:#fff7ed; color:#c2410c;
}
.months-badge {
  display:inline-block; padding:2px 8px;
  border-radius:6px; font-size:11px; font-weight:700;
  background:#fef3c7; color:#92400e;
}

/* ── Quick Pay ── */
.qp-search-wrap { position:relative; margin-bottom:16px; }
.qp-search-wrap input[type=search], .qp-search-wrap input[type=text] {
  width:100%; padding:13px 18px 13px 46px;
  border:2px solid var(--border); border-radius:12px;
  font-size:15px; background:var(--bg-1); color:var(--text-1);
  outline:none; transition:border-color .2s;
}
.qp-search-wrap input:focus { border-color:var(--primary,#1a56db); }
.qp-search-icon {
  position:absolute; left:15px; top:50%; transform:translateY(-50%);
  font-size:18px; color:var(--text-3);
}
.qp-loader {
  position:absolute; right:14px; top:50%; transform:translateY(-50%);
  width:18px; height:18px; border:2px solid var(--border);
  border-top-color:var(--primary,#1a56db); border-radius:50%;
  display:none; animation:spin .6s linear infinite;
}
@keyframes spin { to { transform:translateY(-50%) rotate(360deg); } }

.qp-results { display:flex; flex-direction:column; gap:10px; }
.qp-card {
  display:flex; align-items:center; justify-content:space-between;
  gap:14px; padding:14px 18px;
  background:var(--bg-1); border:1px solid var(--border);
  border-radius:12px; transition:box-shadow .15s;
}
.qp-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
.qp-info { flex:1; min-width:0; }
.qp-name { font-size:15px; font-weight:700; color:var(--text-1); }
.qp-meta { font-size:12px; color:var(--text-2); margin-top:2px; }
.qp-due  {
  text-align:right; flex-shrink:0;
}
.qp-due-amt { font-size:20px; font-weight:900; color:#b91c1c; }
.qp-due-lbl { font-size:11px; color:var(--text-3); }
.qp-no-due  { font-size:16px; font-weight:700; color:#16a34a; }

.qp-empty {
  padding:40px; text-align:center;
  color:var(--text-3); font-size:14px;
}

/* ── Enrollment filter bar ── */
.enr-filter {
  display:flex; gap:10px; flex-wrap:wrap;
  margin-bottom:16px;
}
.enr-filter select {
  padding:8px 14px; border:1px solid var(--border);
  border-radius:8px; font-size:13px;
  background:var(--bg-1); color:var(--text-1);
  cursor:pointer;
}
.enr-filter button {
  padding:8px 18px; border-radius:8px; font-size:13px; font-weight:600;
  background:var(--primary,#1a56db); color:#fff; border:none; cursor:pointer;
}

/* ── Stats row ── */
.stat-row { display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
.stat-pill {
  display:flex; align-items:center; gap:8px;
  padding:8px 16px; border-radius:10px;
  background:var(--bg-2); border:1px solid var(--border);
  font-size:13px;
}
.stat-pill .num { font-size:18px; font-weight:900; color:var(--text-1); }
.stat-pill .lbl { font-size:11px; color:var(--text-2); }

/* ── Empty state ── */
.empty-state {
  padding:56px 20px; text-align:center; color:var(--text-3);
}
.empty-state .icon { font-size:48px; margin-bottom:10px; }
.empty-state p { font-size:15px; }
</style>
@endpush

@section('content')
@php $amtCol = \App\Models\FeeCollectDetail::amountColumn(); @endphp

<div class="fd-wrap">

  {{-- ══════════════════════════════════════════
       LEFT SIDEBAR
  ══════════════════════════════════════════ --}}
  <div class="fd-sidebar">

    <div class="fd-sec-label">Fee Collection</div>
    <div class="fd-nav">
      <button class="fd-nav-item {{ $tab==='all-dues' ? 'active':'' }}" data-tab="all-dues">
        All Dues
        <span class="cnt">{{ $allDues->count() }}</span>
      </button>
      <button class="fd-nav-item {{ $tab==='monthly-dues' ? 'active':'' }}" data-tab="monthly-dues">
        Monthly Dues
        <span class="cnt">{{ $monthlyDues->count() }}</span>
      </button>
      <button class="fd-nav-item {{ $tab==='quick-pay' ? 'active':'' }}" data-tab="quick-pay">
        Quick Pay
      </button>
    </div>

    <div class="fd-sec-label" style="margin-top:8px">Enrollment</div>
    <div class="fd-nav">
      <button class="fd-nav-item {{ $tab==='enrollment' ? 'active':'' }}" data-tab="enrollment">
        Session View
        <span class="cnt">{{ $enrollments->total() }}</span>
      </button>
    </div>

  </div>{{-- /sidebar --}}

  {{-- ══════════════════════════════════════════
       MAIN CONTENT
  ══════════════════════════════════════════ --}}
  <div class="fd-main">

    {{-- ══ TAB 1: ALL DUES ══ --}}
    <div class="fd-tab {{ $tab==='all-dues' ? 'fd-active':'' }}" id="tab-all-dues">
      <div class="fd-head">
        <div>
          <div class="fd-title">All Fees Due</div>
          <div class="fd-sub">Students with any outstanding balance</div>
        </div>
        <span style="font-size:12px; color:var(--text-3)">{{ now()->format('d M Y') }}</span>
      </div>

      @if($allDues->isEmpty())
        <div class="gt-card">
          <div class="empty-state">
            <div class="icon">🎉</div>
            <p>No pending dues found.</p>
          </div>
        </div>
      @else
      <div class="gt-card">
        <div style="overflow-x:auto">
          <table class="fd-tbl">
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Enrollment No.</th>
                <th>Course</th>
                <th>Plan</th>
                <th>Total Fee</th>
                <th>Paid</th>
                <th>Due</th>
                <th style="text-align:right">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($allDues as $i => $book)
              <tr>
                <td class="text-muted">{{ $i + 1 }}</td>
                <td>
                  <div style="font-weight:600">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
                  <div style="font-size:11px; color:var(--text-3)">{{ $book->student->mobile }}</div>
                </td>
                <td class="mono text-muted">{{ $book->enrollment_no ?? '—' }}</td>
                <td>{{ $book->course->name }}</td>
                <td>
                  <span style="font-size:11px; padding:2px 7px; background:var(--bg-3); border-radius:5px; font-weight:700">
                    {{ $book->paymentPlan?->plan_type ?? '—' }}
                  </span>
                </td>
                <td class="mono">₹{{ number_format($book->final_fee, 2) }}</td>
                <td class="mono" style="color:#16a34a">₹{{ number_format($book->paid_amount, 2) }}</td>
                <td><span class="due-badge">₹{{ number_format($book->due_amount, 2) }}</span></td>
                <td style="text-align:right">
                  <a href="{{ route('institute.enrollment.payment-complete', $book) }}"
                     class="btn btn-primary btn-xs">Collect</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif
    </div>{{-- /tab all-dues --}}

    {{-- ══ TAB 2: MONTHLY DUES ══ --}}
    <div class="fd-tab {{ $tab==='monthly-dues' ? 'fd-active':'' }}" id="tab-monthly-dues">
      <div class="fd-head">
        <div>
          <div class="fd-title">Monthly Dues</div>
          <div class="fd-sub">Accumulated unpaid months + late fee charges</div>
        </div>
        <span style="font-size:12px; color:var(--text-3)">{{ now()->format('M Y') }}</span>
      </div>

      @if($monthlyDues->isEmpty())
        <div class="gt-card">
          <div class="empty-state">
            <div class="icon">✅</div>
            <p>No monthly dues pending.</p>
          </div>
        </div>
      @else
      <div class="gt-card">
        <div style="overflow-x:auto">
          <table class="fd-tbl">
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Enrollment No.</th>
                <th>Course</th>
                <th>Monthly Amt</th>
                <th>Months Due</th>
                <th>Late Fee</th>
                <th>Total Due</th>
                <th style="text-align:right">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($monthlyDues as $i => $book)
              <tr>
                <td class="text-muted">{{ $i + 1 }}</td>
                <td>
                  <div style="font-weight:600">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
                  <div style="font-size:11px; color:var(--text-3)">{{ $book->student->mobile }}</div>
                </td>
                <td class="mono text-muted">{{ $book->enrollment_no ?? '—' }}</td>
                <td>{{ $book->course->name }}</td>
                <td class="mono">₹{{ number_format($book->monthly_amount, 2) }}</td>
                <td>
                  <span class="months-badge">{{ $book->months_count }} month{{ $book->months_count > 1 ? 's' : '' }}</span>
                  <div style="font-size:11px; color:var(--text-3); margin-top:2px">
                    From {{ $book->next_due->format('M Y') }}
                  </div>
                </td>
                <td>
                  @if($book->late_fee_amt > 0)
                    <span class="late-badge">+₹{{ number_format($book->late_fee_amt, 2) }}</span>
                  @else
                    <span style="color:var(--text-3)">—</span>
                  @endif
                </td>
                <td><span class="due-badge" style="font-size:13px">₹{{ number_format($book->total_due, 2) }}</span></td>
                <td style="text-align:right">
                  <a href="{{ route('institute.enrollment.payment-complete', $book) }}"
                     class="btn btn-primary btn-xs">Collect</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif
    </div>{{-- /tab monthly-dues --}}

    {{-- ══ TAB 3: QUICK PAY ══ --}}
    <div class="fd-tab {{ $tab==='quick-pay' ? 'fd-active':'' }}" id="tab-quick-pay">
      <div class="fd-head">
        <div>
          <div class="fd-title">Quick Pay</div>
          <div class="fd-sub">Search student by name, mobile, email, or enrollment number</div>
        </div>
      </div>

      <div class="gt-card" style="padding:20px">
        <div class="qp-search-wrap">
          <span class="qp-search-icon">🔍</span>
          <input type="text" id="qp-input"
            placeholder="Type name, mobile, email, or enrollment no…"
            autocomplete="off">
          <div class="qp-loader" id="qp-loader"></div>
        </div>

        <div class="qp-results" id="qp-results">
          <div class="qp-empty">Start typing to search students…</div>
        </div>
      </div>
    </div>{{-- /tab quick-pay --}}

    {{-- ══ TAB 4: ENROLLMENT (SESSION VIEW) ══ --}}
    <div class="fd-tab {{ $tab==='enrollment' ? 'fd-active':'' }}" id="tab-enrollment">
      <div class="fd-head">
        <div>
          <div class="fd-title">Enrollment View</div>
          <div class="fd-sub">Session-wise enrollments — filter by session or course</div>
        </div>
      </div>

      {{-- Filter form --}}
      <form method="GET" action="{{ route('institute.fees-dashboard') }}" class="gt-card" style="padding:16px 18px; margin-bottom:16px">
        <input type="hidden" name="tab" value="enrollment">
        <div class="enr-filter">
          <select name="session_id" onchange="this.form.submit()">
            <option value="">— All Sessions —</option>
            @foreach($sessions as $sess)
              <option value="{{ $sess->id }}" {{ (string)$sessionId == (string)$sess->id ? 'selected':'' }}>
                {{ $sess->name }}{{ $sess->is_active ? ' (Active)' : '' }}
              </option>
            @endforeach
          </select>

          <select name="course_id" onchange="this.form.submit()">
            <option value="">— All Courses —</option>
            @foreach($courses as $c)
              <option value="{{ $c->id }}" {{ (string)$courseId == (string)$c->id ? 'selected':'' }}>
                {{ $c->name }}
              </option>
            @endforeach
          </select>

          @if($sessionId || $courseId)
            <a href="{{ route('institute.fees-dashboard', ['tab'=>'enrollment']) }}"
               style="padding:8px 14px; border-radius:8px; font-size:13px; color:var(--text-2); border:1px solid var(--border); background:var(--bg-1); text-decoration:none">
              ✕ Clear
            </a>
          @endif
        </div>
      </form>

      {{-- Stats --}}
      <div class="stat-row">
        <div class="stat-pill">
          <div>
            <div class="num">{{ $enrollments->total() }}</div>
            <div class="lbl">Total</div>
          </div>
        </div>
        <div class="stat-pill">
          <div>
            <div class="num" style="color:#16a34a">{{ $enrollments->filter(fn($b)=>$b->status==='RUN')->count() }}</div>
            <div class="lbl">Admitted</div>
          </div>
        </div>
        <div class="stat-pill">
          <div>
            <div class="num" style="color:#d97706">{{ $enrollments->filter(fn($b)=>$b->status==='OPEN')->count() }}</div>
            <div class="lbl">Seat Booked</div>
          </div>
        </div>
      </div>

      @if($enrollments->isEmpty())
        <div class="gt-card">
          <div class="empty-state">
            <div class="icon">📋</div>
            <p>No enrollments found for selected filter.</p>
          </div>
        </div>
      @else
      <div class="gt-card">
        <div style="overflow-x:auto">
          <table class="fd-tbl">
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Enrollment No.</th>
                <th>Course</th>
                <th>Batch</th>
                <th>Plan</th>
                <th>Start Date</th>
                <th>Status</th>
                <th style="text-align:right">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($enrollments as $i => $book)
              @php
                $ePaid = (float)\App\Models\FeeCollectDetail::where('course_book_id',$book->id)->whereNull('cancelled_at')->sum($amtCol);
                $eDue  = round(max(0,(float)$book->final_fee - $ePaid),2);
              @endphp
              <tr>
                <td class="text-muted">{{ $enrollments->firstItem() + $loop->index }}</td>
                <td>
                  <div style="font-weight:600">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
                  <div style="font-size:11px; color:var(--text-3)">{{ $book->student->mobile }}</div>
                </td>
                <td class="mono" style="font-size:12px; color:var(--text-2)">{{ $book->enrollment_no ?? '—' }}</td>
                <td>{{ $book->course->name }}</td>
                <td>{{ $book->batch?->name ?? '—' }}</td>
                <td>
                  <span style="font-size:11px; padding:2px 7px; background:var(--bg-3); border-radius:5px; font-weight:700">
                    {{ $book->paymentPlan?->plan_type ?? '—' }}
                  </span>
                </td>
                <td style="white-space:nowrap; color:var(--text-2)">
                  {{ $book->start_date ? \Carbon\Carbon::parse($book->start_date)->format('d M Y') : '—' }}
                </td>
                <td>
                  @if($book->status === 'RUN')
                    <span class="badge badge-success">ADMITTED</span>
                  @else
                    <span class="badge badge-warning">BOOKED</span>
                  @endif
                  @if($eDue > 0)
                    <div style="font-size:11px; color:#b91c1c; margin-top:2px">₹{{ number_format($eDue,2) }} due</div>
                  @endif
                </td>
                <td style="text-align:right">
                  <a href="{{ route('institute.enrollment.payment-complete', $book) }}"
                     class="btn btn-outline btn-xs">View</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($enrollments->hasPages())
        <div style="padding:14px 18px; border-top:1px solid var(--border)">
          {{ $enrollments->links() }}
        </div>
        @endif
      </div>
      @endif
    </div>{{-- /tab enrollment --}}

  </div>{{-- /fd-main --}}
</div>{{-- /fd-wrap --}}
@endsection

@push('scripts')
<script>
(function () {
  // ── Tab switching ──────────────────────────────────────────────
  var navItems = document.querySelectorAll('.fd-nav-item[data-tab]');
  var tabs     = document.querySelectorAll('.fd-tab');

  navItems.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = btn.getAttribute('data-tab');

      navItems.forEach(function (b) { b.classList.remove('active'); });
      tabs.forEach(function (t)     { t.classList.remove('fd-active'); });

      btn.classList.add('active');
      var pane = document.getElementById('tab-' + target);
      if (pane) pane.classList.add('fd-active');

      // Focus search input when switching to Quick Pay
      if (target === 'quick-pay') {
        setTimeout(function () { document.getElementById('qp-input').focus(); }, 80);
      }
    });
  });

  // ── Quick Pay search with debounce ────────────────────────────
  var input    = document.getElementById('qp-input');
  var results  = document.getElementById('qp-results');
  var loader   = document.getElementById('qp-loader');
  var timer    = null;
  var searchUrl = '{{ route("institute.fees-search") }}';

  function rupee(n) {
    return '₹' + parseFloat(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function renderResults(data) {
    if (!data || data.length === 0) {
      results.innerHTML = '<div class="qp-empty">No students found for "<strong>' +
        input.value.replace(/</g,'&lt;') + '</strong>"</div>';
      return;
    }

    var html = '';
    data.forEach(function (s) {
      var dueHtml = s.due > 0
        ? '<div class="qp-due-amt">' + rupee(s.due) + '</div><div class="qp-due-lbl">due</div>'
        : '<div class="qp-no-due">Paid ✓</div>';

      html += '<div class="qp-card">' +
        '<div class="qp-info">' +
          '<div class="qp-name">' + s.name + '</div>' +
          '<div class="qp-meta">' +
            '<span>📱 ' + s.mobile + '</span>' +
            ' &nbsp;|&nbsp; ' +
            '<span>ID: <strong>' + s.enrollment_no + '</strong></span>' +
            ' &nbsp;|&nbsp; ' +
            '<span>' + s.course + '</span>' +
          '</div>' +
          '<div style="margin-top:4px; font-size:12px; color:var(--text-3)">' +
            'Total: ' + rupee(s.total_fee) + ' &nbsp; Paid: ' + rupee(s.paid) +
          '</div>' +
        '</div>' +
        '<div class="qp-due">' + dueHtml + '</div>' +
        '<div>' +
          '<a href="' + s.pay_url + '" class="btn btn-primary btn-xs">Pay</a>' +
        '</div>' +
        '</div>';
    });
    results.innerHTML = html;
  }

  if (input) {
    input.addEventListener('input', function () {
      var q = input.value.trim();
      clearTimeout(timer);

      if (q.length < 2) {
        results.innerHTML = '<div class="qp-empty">Start typing to search students…</div>';
        loader.style.display = 'none';
        return;
      }

      loader.style.display = 'block';

      timer = setTimeout(function () {
        fetch(searchUrl + '?q=' + encodeURIComponent(q), {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          loader.style.display = 'none';
          renderResults(data);
        })
        .catch(function () {
          loader.style.display = 'none';
          results.innerHTML = '<div class="qp-empty" style="color:#b91c1c">Search failed. Please try again.</div>';
        });
      }, 320);
    });
  }
})();
</script>
@endpush
