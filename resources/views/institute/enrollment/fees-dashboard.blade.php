@extends('layouts.institute')
@section('title', 'Fees Dashboard')
@section('page-title', 'Fees & Enrollment Dashboard')

@push('styles')
<style>
/* ── Tab pills ── */
.fd-tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:20px; }
.fd-tab-btn {
  display:flex; align-items:center; gap:7px;
  padding:8px 18px; border-radius:20px; font-size:13px; font-weight:600;
  border:1.5px solid var(--border); background:var(--bg-2); color:var(--text-2);
  cursor:pointer; transition:.12s; white-space:nowrap;
}
.fd-tab-btn:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-bg); }
.fd-tab-btn.active { border-color:var(--accent); color:var(--accent); background:var(--accent-bg); }
.fd-tab-btn .cnt {
  font-size:11px; font-weight:700; padding:2px 7px;
  border-radius:999px; background:var(--accent); color:#fff; min-width:22px; text-align:center;
}
.fd-tab-btn:not(.active) .cnt { background:var(--bg-3); color:var(--text-2); }

/* ── Tab panes ── */
.fd-tab { display:none; }
.fd-tab.fd-active { display:block; }

/* ── Section header ── */
.fd-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.fd-title { font-size:18px; font-weight:800; }
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
.fd-tbl tbody tr:hover td { background:var(--bg-3); }

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
.qp-wrap { max-width:580px; margin:0 auto; }
.qp-search-wrap { position:relative; margin-bottom:12px; }
.qp-search-wrap input {
  width:100%; padding:12px 18px 12px 44px;
  border:2px solid var(--border); border-radius:10px;
  font-size:14px; background:var(--bg-2); color:var(--text);
  outline:none; transition:border-color .2s;
}
.qp-search-wrap input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(108,93,211,.1); }
.qp-search-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:16px; color:var(--text-3); }
.qp-loader {
  position:absolute; right:12px; top:50%; transform:translateY(-50%);
  width:16px; height:16px; border:2px solid var(--border);
  border-top-color:var(--accent); border-radius:50%;
  display:none; animation:spin .6s linear infinite;
}
@keyframes spin { to { transform:translateY(-50%) rotate(360deg); } }
.qp-results { display:flex; flex-direction:column; gap:8px; }
.qp-card {
  background:var(--bg-2); border:1px solid var(--border);
  border-radius:12px; padding:14px 16px;
  transition:box-shadow .15s;
}
.qp-card:hover { box-shadow:var(--shadow); }
.qp-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:10px; }
.qp-info { flex:1; min-width:0; }
.qp-name { font-size:14px; font-weight:700; }
.qp-meta { font-size:12px; color:var(--text-2); margin-top:3px; }
.qp-due-amt { font-size:18px; font-weight:900; color:#b91c1c; text-align:right; }
.qp-due-lbl { font-size:11px; color:var(--text-3); text-align:right; }
.qp-no-due  { font-size:14px; font-weight:700; color:#16a34a; }
.qp-proceed-btn {
  display:flex; align-items:center; justify-content:center; gap:6px;
  width:100%; padding:10px; border-radius:8px;
  background:var(--accent); color:#fff; font-size:13px; font-weight:600;
  text-decoration:none; border:none; cursor:pointer;
  transition:opacity .15s;
}
.qp-proceed-btn:hover { opacity:.88; color:#fff; }
.qp-empty { padding:32px; text-align:center; color:var(--text-3); font-size:13px; }

/* ── Enrollment filter bar ── */
.enr-filter { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px; }
.enr-filter select {
  padding:8px 14px; border:1px solid var(--border);
  border-radius:8px; font-size:13px;
  background:var(--bg-2); color:var(--text);
  cursor:pointer;
}

/* ── Empty state ── */
.empty-state { padding:56px 20px; text-align:center; color:var(--text-3); }
.empty-state .icon { font-size:48px; margin-bottom:10px; }
.empty-state p { font-size:15px; }
</style>
@endpush

@section('content')
@php $amtCol = \App\Models\FeeCollectDetail::amountColumn(); @endphp

{{-- Tab Navigation Pills (URL-based) --}}
<div class="fd-tabs">
  <a href="{{ route('institute.fees-dashboard', ['tab'=>'all-dues']) }}" class="fd-tab-btn {{ $tab==='all-dues' ? 'active':'' }}">
    All Dues
    <span class="cnt">{{ $allDues->count() }}</span>
  </a>
  <a href="{{ route('institute.fees-dashboard', ['tab'=>'monthly-dues']) }}" class="fd-tab-btn {{ $tab==='monthly-dues' ? 'active':'' }}">
    Monthly Dues
    <span class="cnt">{{ $monthlyDues->count() }}</span>
  </a>
  <a href="{{ route('institute.fees-dashboard', ['tab'=>'quick-pay']) }}" class="fd-tab-btn {{ $tab==='quick-pay' ? 'active':'' }}">
    Quick Pay
  </a>
  <a href="{{ route('institute.fees-dashboard', ['tab'=>'enrollment']) }}" class="fd-tab-btn {{ $tab==='enrollment' ? 'active':'' }}">
    Session View
    <span class="cnt">{{ $enrollments->total() }}</span>
  </a>
</div>

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
  <div class="gt-card" style="padding:0;overflow:hidden;">
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
  <div class="gt-card" style="padding:0;overflow:hidden;">
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
  <div class="qp-wrap">
    <div class="gt-card" style="padding:20px">
      <div style="font-size:15px;font-weight:700;margin-bottom:14px;">Quick Pay</div>
      <div class="qp-search-wrap">
        <span class="qp-search-icon">🔍</span>
        <input type="text" id="qp-input"
          placeholder="Name, mobile, email, or enrollment no…"
          autocomplete="off">
        <div class="qp-loader" id="qp-loader"></div>
      </div>
      <div class="qp-results" id="qp-results">
        <div class="qp-empty">Start typing to search students…</div>
      </div>
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
           style="padding:8px 14px; border-radius:8px; font-size:13px; color:var(--text-2); border:1px solid var(--border); background:var(--bg-2); text-decoration:none">
          ✕ Clear
        </a>
      @endif
    </div>
  </form>

  @if($enrollments->isEmpty())
    <div class="gt-card">
      <div class="empty-state">
        <div class="icon">📋</div>
        <p>No enrollments found for selected filter.</p>
      </div>
    </div>
  @else
  <div class="gt-card" style="padding:0;overflow:hidden;">
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

@endsection

@push('scripts')
<script>
(function () {
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
        '<div class="qp-card-top">' +
          '<div class="qp-info">' +
            '<div class="qp-name">' + s.name + '</div>' +
            '<div class="qp-meta">' +
              '📱 ' + s.mobile +
              ' &nbsp;|&nbsp; ID: <strong>' + s.enrollment_no + '</strong>' +
              ' &nbsp;|&nbsp; ' + s.course +
            '</div>' +
            '<div style="margin-top:3px; font-size:11px; color:var(--text-3)">' +
              'Total: ' + rupee(s.total_fee) + ' &nbsp;&nbsp; Paid: ' + rupee(s.paid) +
            '</div>' +
          '</div>' +
          '<div>' + dueHtml + '</div>' +
        '</div>' +
        '<a href="' + s.pay_url + '" class="qp-proceed-btn">' +
          '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>' +
          'Proceed to Pay' +
          '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>' +
        '</a>' +
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
          results.innerHTML = '<div class="qp-empty">Search failed. Please try again.</div>';
        });
      }, 350);
    });
  }
})();
</script>
@endpush
