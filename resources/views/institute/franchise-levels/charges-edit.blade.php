@extends('layouts.institute')
@section('title', 'Course Charges — ' . $franchiseLevel->name)
@section('page-title', 'Course Charges')
@section('topbar-actions')
  <a href="{{ route('institute.franchise-levels.charges', $franchiseLevel) }}" class="btn btn-outline btn-sm">← Set by Duration</a>
  <a href="{{ route('institute.franchise-levels.index') }}" class="btn btn-outline btn-sm">All Levels</a>
@endsection

@section('content')

{{-- Level info banner --}}
<div style="display:flex; align-items:center; justify-content:space-between; gap:16px;
     background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--radius);
     padding:14px 20px; margin-bottom:18px;">
  <div>
    <div style="font-size:16px; font-weight:700; color:var(--text-1);">{{ $franchiseLevel->name }}</div>
    <div style="font-size:12px; color:var(--text-3); margin-top:3px;">
      Level Fee: <strong>₹{{ number_format($franchiseLevel->level_fee ?? 0, 2) }}</strong>
      &nbsp;·&nbsp; Commission: <strong>{{ $franchiseLevel->commission_percent }}%</strong>
      &nbsp;·&nbsp; <strong>{{ $charges->count() }}</strong> course(s) with charges
    </div>
  </div>
  <div style="font-size:12px; padding:6px 14px; background:rgba(42,138,74,.1); color:#2a7a2a; border:1px solid rgba(42,138,74,.25); border-radius:var(--radius-sm);">
    ✓ Saved
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:14px; padding:10px 16px; background:rgba(42,138,74,.1); border:1px solid rgba(42,138,74,.3); border-radius:6px; color:#2a7a2a; font-size:13px;">
  {{ session('success') }}
</div>
@endif

<div class="gt-card">
  <div class="gt-card-header" style="gap:14px; flex-wrap:wrap; align-items:center;">
    <div class="gt-card-title" style="flex-shrink:0;">All Course Charges</div>

    {{-- Course type filter --}}
    <div style="display:flex; gap:6px; flex-wrap:wrap; flex:1;">
      <button class="lce-type-btn active" data-type="all" onclick="lceFilter('all', this)">All</button>
      @foreach($courseTypes as $ct)
        <button class="lce-type-btn" data-type="{{ $ct->id }}" onclick="lceFilter('{{ $ct->id }}', this)">{{ $ct->name }}</button>
      @endforeach
    </div>

    {{-- Search --}}
    <input type="text" id="lce-search" class="gt-input" style="max-width:240px; flex-shrink:0;"
      placeholder="Search course…" oninput="lceDebouncedSearch(this.value)">
  </div>

  <div class="gt-table-wrap">
    <table class="gt-table" id="lce-table" data-no-dt>
      <thead>
        <tr>
          <th>Course</th>
          <th>Type</th>
          <th style="width:90px; text-align:center;">Duration</th>
          <th style="width:170px;">Admission (₹)</th>
          <th style="width:170px;">Certificate (₹)</th>
          <th style="width:90px;">Action</th>
        </tr>
      </thead>
      <tbody id="lce-tbody">
        @forelse($charges as $charge)
        <tr class="lce-row"
            data-type="{{ $charge->course_type_id ?? '' }}"
            data-name="{{ strtolower($charge->course_name) }}"
            data-id="{{ $charge->id }}">
          <td>
            <div style="font-size:13px; font-weight:600; color:var(--text-1);">{{ $charge->course_name }}</div>
            @if($charge->course_short_name)
              <div style="font-size:11px; color:var(--text-3);">{{ $charge->course_short_name }}</div>
            @endif
          </td>
          <td style="font-size:12.5px; color:var(--text-2);">{{ $charge->type_name ?? '—' }}</td>
          <td style="text-align:center;">
            <span style="font-size:11.5px; font-weight:700;
              background:rgba(138,115,245,.12); color:rgba(138,115,245,.9);
              border:1px solid rgba(138,115,245,.25); border-radius:20px; padding:3px 10px;">
              {{ $charge->duration }}m
            </span>
          </td>
          <td>
            <div class="lce-view-mode">
              <span class="lce-val mono">₹{{ number_format($charge->student_admission_charge, 2) }}</span>
            </div>
            <div class="lce-edit-mode" style="display:none;">
              <div class="lce-inp-wrap">
                <span class="lce-inp-pre">₹</span>
                <input type="number" class="lce-inp lce-adm" value="{{ $charge->student_admission_charge }}"
                       min="0" step="0.01" placeholder="0.00">
              </div>
            </div>
          </td>
          <td>
            <div class="lce-view-mode">
              <span class="lce-val mono">₹{{ number_format($charge->student_certificate_charge, 2) }}</span>
            </div>
            <div class="lce-edit-mode" style="display:none;">
              <div class="lce-inp-wrap">
                <span class="lce-inp-pre">₹</span>
                <input type="number" class="lce-inp lce-cert" value="{{ $charge->student_certificate_charge }}"
                       min="0" step="0.01" placeholder="0.00">
              </div>
            </div>
          </td>
          <td>
            <div class="lce-view-mode">
              <button type="button" class="btn btn-outline btn-xs" onclick="lceStartEdit(this)">Edit</button>
            </div>
            <div class="lce-edit-mode" style="display:none; display:flex; gap:4px; display:none;">
              <button type="button" class="btn btn-xs btn-primary lce-save-btn"
                onclick="lceSave(this, {{ $charge->id }}, '{{ route('institute.franchise-levels.charges.update', [$franchiseLevel, $charge->id]) }}')">
                Save
              </button>
              <button type="button" class="btn btn-xs btn-outline" onclick="lceCancel(this)">✕</button>
            </div>
          </td>
        </tr>
        @empty
        <tr id="lce-empty-row">
          <td colspan="6">
            <div class="gt-empty">
              <div class="gt-empty-title">No charges set yet</div>
              <a href="{{ route('institute.franchise-levels.charges', $franchiseLevel) }}" class="btn btn-primary btn-sm">Set by Duration</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- No results message (hidden by default) --}}
  <div id="lce-no-results" style="display:none; text-align:center; padding:30px; color:var(--text-3); font-size:13px;">
    No courses match your search.
  </div>

  <div style="padding:12px 16px; border-top:1px solid var(--border-1); display:flex; align-items:center; justify-content:space-between; gap:12px;">
    <div id="lce-count" style="font-size:12px; color:var(--text-3);">
      Showing {{ $charges->count() }} course(s)
    </div>
    <a href="{{ route('institute.franchise-levels.charges', $franchiseLevel) }}" class="btn btn-outline btn-sm">
      ← Set Bulk Duration Charges
    </a>
  </div>

</div>
@endsection

@push('styles')
<style>
.lce-type-btn {
  padding:5px 13px; font-size:12px; font-weight:600;
  border:1px solid var(--border-2); border-radius:20px;
  background:var(--bg-3); color:var(--text-3); cursor:pointer;
  transition:all .15s;
}
.lce-type-btn:hover { border-color:var(--accent); color:var(--accent); }
.lce-type-btn.active { background:var(--accent); color:#fff; border-color:var(--accent); }

.lce-val { font-size:13px; color:var(--text-1); }

.lce-inp-wrap { display:flex; align-items:center; max-width:140px; }
.lce-inp-pre {
  padding:5px 8px; font-size:13px; color:var(--text-3);
  background:var(--bg-3); border:1px solid var(--border-2);
  border-right:none; border-radius:var(--radius-sm) 0 0 var(--radius-sm);
}
.lce-inp {
  flex:1; min-width:0; background:var(--bg-2); border:1px solid var(--border-2);
  border-radius:0 var(--radius-sm) var(--radius-sm) 0;
  color:var(--text-1); font-size:13px; padding:5px 8px; outline:none;
  transition:border-color .15s;
}
.lce-inp:focus { border-color:var(--accent); }

.lce-saving { opacity:.5; pointer-events:none; }
.lce-saved-flash { animation: lce-flash .6s ease; }
@keyframes lce-flash {
  0%   { background:rgba(42,138,74,.2); }
  100% { background:transparent; }
}
</style>
@endpush

@push('scripts')
<script>
// ── Filter by course type ─────────────────────────────────────────────
let lceActiveType = 'all';
let lceSearchQ    = '';

function lceFilter(typeId, btn) {
  lceActiveType = typeId;
  document.querySelectorAll('.lce-type-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  lceApplyFilters();
}

let lceSearchTimer;
function lceDebouncedSearch(val) {
  clearTimeout(lceSearchTimer);
  lceSearchTimer = setTimeout(() => {
    lceSearchQ = val.toLowerCase().trim();
    lceApplyFilters();
  }, 300);
}

function lceApplyFilters() {
  const rows = document.querySelectorAll('.lce-row');
  let visible = 0;

  rows.forEach(row => {
    const typeMatch = lceActiveType === 'all' || row.dataset.type === lceActiveType;
    const nameMatch = !lceSearchQ || row.dataset.name.includes(lceSearchQ);
    const show = typeMatch && nameMatch;
    row.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  const noRes = document.getElementById('lce-no-results');
  if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';

  const countEl = document.getElementById('lce-count');
  if (countEl) countEl.textContent = 'Showing ' + visible + ' course(s)';
}

// ── Inline Edit ───────────────────────────────────────────────────────
function lceStartEdit(btn) {
  const row = btn.closest('tr');
  row.querySelectorAll('.lce-view-mode').forEach(el => el.style.display = 'none');
  row.querySelectorAll('.lce-edit-mode').forEach(el => el.style.display = 'flex');
  row.querySelector('.lce-adm')?.focus();
}

function lceCancel(btn) {
  const row = btn.closest('tr');
  row.querySelectorAll('.lce-view-mode').forEach(el => el.style.display = '');
  row.querySelectorAll('.lce-edit-mode').forEach(el => el.style.display = 'none');
}

function lceSave(btn, chargeId, url) {
  const row = btn.closest('tr');
  const adm  = parseFloat(row.querySelector('.lce-adm').value  || 0);
  const cert = parseFloat(row.querySelector('.lce-cert').value || 0);

  row.classList.add('lce-saving');

  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({
      student_admission_charge:   adm,
      student_certificate_charge: cert,
    }),
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Update display values
      const viewCells = row.querySelectorAll('.lce-view-mode');
      viewCells[0].querySelector('.lce-val').textContent = '₹' + adm.toFixed(2);
      viewCells[1].querySelector('.lce-val').textContent = '₹' + cert.toFixed(2);
      lceCancel(btn);
      row.classList.remove('lce-saving');
      row.classList.add('lce-saved-flash');
    }
  })
  .catch(() => {
    row.classList.remove('lce-saving');
    alert('Save failed. Please try again.');
  });
}

// Update course_type on rows from data attribute (set via PHP)
document.addEventListener('DOMContentLoaded', () => {
  // Pull course_type_id from data attribute if needed
  // (Already set via data-type from PHP join)
});
</script>
@endpush
