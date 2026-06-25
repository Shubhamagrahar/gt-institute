@extends('layouts.institute')
@section('title', 'Course Access — ' . $data['name'])
@section('page-title', 'Course Access Setup')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.create') }}" class="btn btn-outline btn-sm">← Edit Details</a>
@endsection

@section('content')

{{-- Wizard --}}
<div class="frn-wizard">
  <div class="frn-wizard-step done">
    <span class="frn-step-dot">✓</span>
    <span class="frn-step-lbl">Franchise Details</span>
  </div>
  <div class="frn-wizard-line done"></div>
  <div class="frn-wizard-step active">
    <span class="frn-step-dot">2</span>
    <span class="frn-step-lbl">Course Access</span>
  </div>
  <div class="frn-wizard-line"></div>
  <div class="frn-wizard-step">
    <span class="frn-step-dot">3</span>
    <span class="frn-step-lbl">Review & Confirm</span>
  </div>
</div>

{{-- Info Banner --}}
<div class="ca-info-banner">
  <div>
    <div class="ca-info-name">{{ $data['name'] }}</div>
    <div class="ca-info-sub">
      Level: <strong>{{ $data['_level']['name'] ?? '—' }}</strong>
      &nbsp;·&nbsp; Management: <strong>Wallet System</strong>
    </div>
  </div>
  <div class="ca-info-note">
    Charges are inherited from the <strong>{{ $data['_level']['name'] ?? 'level' }}</strong> configuration.
    You can fine-tune them after creation.
  </div>
</div>

<form method="POST" action="{{ route('institute.franchises.charges.store') }}" id="ca-form">
  @csrf

  <div class="gt-card" style="margin-top:16px;">
    <div class="gt-card-header" style="border-bottom:1px solid var(--border-1);padding-bottom:14px;">
      <div>
        <div class="gt-card-title">Select Course Types</div>
        <div style="font-size:12.5px;color:var(--text-3);margin-top:3px;">
          Choose which course types this franchise can admit students for.
          Charges are pulled from the level configuration automatically.
        </div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;">
        <button type="button" class="btn btn-outline btn-sm" onclick="caSelectAll()">Select All</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="caClearAll()">Clear</button>
      </div>
    </div>

    @if($courseTypes->isEmpty())
      <div style="text-align:center;padding:48px 20px;color:var(--text-3);">
        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" opacity=".35" style="margin-bottom:12px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <div style="font-size:14px;font-weight:600;margin-bottom:6px;">No course types found</div>
        <div style="font-size:12px;">Add course types from Academic Setup → Courses first.</div>
      </div>
    @else
      <div class="ca-grid">
        @foreach($courseTypes as $ct)
          @php
            $isChecked   = in_array($ct->id, $selected);
            $levelData   = $levelChargesByType[$ct->id] ?? null;
            $hasCharges  = $levelData && $levelData->configured_count > 0;
          @endphp
          <label class="ca-card {{ $isChecked ? 'selected' : '' }}" data-id="{{ $ct->id }}">
            <div class="ca-card-top">
              <input type="checkbox" name="course_type_ids[]" value="{{ $ct->id }}"
                     class="ca-checkbox" {{ $isChecked ? 'checked' : '' }}
                     onchange="caToggle(this)">
              <div class="ca-card-check {{ $isChecked ? 'checked' : '' }}">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <div class="ca-card-name">{{ $ct->name }}</div>
            </div>

            <div class="ca-card-stat">
              <span class="ca-stat-num">{{ $ct->active_courses }}</span>
              <span class="ca-stat-lbl">course{{ $ct->active_courses != 1 ? 's' : '' }}</span>
            </div>

            @if($hasCharges)
              <div class="ca-charge-pills">
                <div class="ca-cpill ca-cpill-adm">
                  Adm: ₹{{ number_format($levelData->min_adm, 0) }}{{ $levelData->min_adm != $levelData->max_adm ? '–'.number_format($levelData->max_adm, 0) : '' }}
                </div>
                <div class="ca-cpill ca-cpill-cert">
                  Cert: ₹{{ number_format($levelData->min_cert, 0) }}{{ $levelData->min_cert != $levelData->max_cert ? '–'.number_format($levelData->max_cert, 0) : '' }}
                </div>
                <div class="ca-cpill ca-cpill-info">
                  {{ $levelData->configured_count }} configured
                </div>
              </div>
            @else
              <div class="ca-no-charges">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                No charges configured in level — ₹0 will be used
              </div>
            @endif
          </label>
        @endforeach
      </div>

      <div style="padding:16px 20px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-top:1px solid var(--border-1);">
        <div style="font-size:12px;color:var(--text-3);">
          <span id="ca-selected-count">{{ count($selected) }}</span> course type(s) selected
        </div>
        <button type="submit" class="btn btn-primary" id="ca-submit-btn">
          Save & Continue to Review
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    @endif
  </div>

  {{-- ── Charge Editor ─────────────────────────────────────────────────── --}}
  @if(!$courseTypes->isEmpty())
  <div id="charge-editor-wrap" style="{{ count($selected) === 0 ? 'display:none;' : '' }}margin-top:16px;">
    <div class="gt-card">
      <div class="gt-card-header" style="border-bottom:1px solid var(--border-1);padding-bottom:14px;">
        <div>
          <div class="gt-card-title">Configure Charges</div>
          <div style="font-size:12.5px;color:var(--text-3);margin-top:3px;">
            Pre-filled from <strong>{{ $data['_level']['name'] ?? 'level' }}</strong> defaults. Edit to customise for this franchise.
          </div>
        </div>
      </div>

      @foreach($courseTypes as $ct)
        @php $courses = $coursesByType[$ct->id] ?? []; @endphp
        @if(count($courses))
        <div class="ce-type-section" data-type-id="{{ $ct->id }}"
             style="{{ !in_array($ct->id, $selected) ? 'display:none;' : '' }}">
          <div class="ce-type-header">
            <span class="ce-type-name">{{ $ct->name }}</span>
            <span class="ce-type-count">{{ count($courses) }} course{{ count($courses) != 1 ? 's' : '' }}</span>
          </div>
          <div class="ce-table-wrap">
            <table class="ce-table">
              <thead>
                <tr>
                  <th>Course</th>
                  <th style="width:90px;">Duration</th>
                  <th style="width:150px;">Admission (₹) <span style="color:#dc2626">*</span></th>
                  <th style="width:150px;">Certificate (₹) <span style="color:#dc2626">*</span></th>
                </tr>
              </thead>
              <tbody>
                @foreach($courses as $course)
                @php
                  $savedAdm  = $savedCharges[$course->course_id]['admission']   ?? $course->default_adm;
                  $savedCert = $savedCharges[$course->course_id]['certificate']  ?? $course->default_cert;
                @endphp
                <tr>
                  <td class="ce-course-name">{{ $course->course_name }}</td>
                  <td style="color:var(--text-3);font-size:12px;">
                    {{ $course->duration ? $course->duration.' mo' : '—' }}
                  </td>
                  <td>
                    <div class="ce-input-wrap">
                      <span class="ce-rupee">₹</span>
                      <input type="number" class="ce-input"
                             name="course_charges[{{ $course->course_id }}][admission]"
                             value="{{ $savedAdm }}"
                             min="0" step="0.01" placeholder="0.00">
                    </div>
                  </td>
                  <td>
                    <div class="ce-input-wrap">
                      <span class="ce-rupee">₹</span>
                      <input type="number" class="ce-input"
                             name="course_charges[{{ $course->course_id }}][certificate]"
                             value="{{ $savedCert }}"
                             min="0" step="0.01" placeholder="0.00">
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @endif
      @endforeach

      <div id="ce-empty" style="{{ count($selected) > 0 ? 'display:none;' : '' }}padding:28px;text-align:center;color:var(--text-3);font-size:13px;">
        Select course types above to configure charges.
      </div>
    </div>
  </div>
  @endif

</form>

@endsection

@push('styles')
<style>
/* Wizard */
.frn-wizard{display:flex;align-items:center;margin-bottom:24px}
.frn-wizard-step{display:flex;flex-direction:column;align-items:center;gap:5px;flex-shrink:0}
.frn-step-dot{width:34px;height:34px;border-radius:50%;border:2px solid var(--border-2);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--text-3);background:var(--bg-3)}
.frn-step-lbl{font-size:11px;color:var(--text-3);white-space:nowrap}
.frn-wizard-step.active .frn-step-dot{border-color:var(--accent);background:var(--accent);color:#fff}
.frn-wizard-step.active .frn-step-lbl{color:var(--accent);font-weight:600}
.frn-wizard-step.done .frn-step-dot{border-color:#16a34a;background:#16a34a;color:#fff}
.frn-wizard-step.done .frn-step-lbl{color:#16a34a}
.frn-wizard-line{flex:1;height:2px;background:var(--border-2);margin:0 10px;margin-bottom:20px}
.frn-wizard-line.done{background:#16a34a}

/* Info Banner */
.ca-info-banner{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;background:var(--bg-2);border:1px solid var(--border-2);border-radius:var(--radius);padding:14px 20px}
.ca-info-name{font-size:15px;font-weight:700;color:var(--text-1)}
.ca-info-sub{font-size:12px;color:var(--text-3);margin-top:2px}
.ca-info-note{font-size:12px;color:var(--text-2);padding:8px 14px;background:rgba(var(--accent-rgb),.06);border-radius:var(--radius-sm);border:1px solid rgba(var(--accent-rgb),.15)}

/* Course Type Grid */
.ca-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;padding:20px}

/* Course Type Card */
.ca-card{display:block;border:1.5px solid var(--border-2);border-radius:12px;padding:14px 16px;cursor:pointer;transition:border-color .15s,background .15s;background:var(--bg-2);position:relative;user-select:none}
.ca-card:hover{border-color:var(--accent);background:rgba(var(--accent-rgb),.03)}
.ca-card.selected{border-color:var(--accent);background:rgba(var(--accent-rgb),.06)}
.ca-card-top{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.ca-checkbox{position:absolute;opacity:0;pointer-events:none}
.ca-card-check{width:20px;height:20px;border-radius:5px;border:2px solid var(--border-2);background:var(--bg-1);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:border-color .15s,background .15s}
.ca-card-check svg{display:none;color:#fff}
.ca-card-check.checked{border-color:var(--accent);background:var(--accent)}
.ca-card-check.checked svg{display:block}
.ca-card-name{font-size:13.5px;font-weight:700;color:var(--text-1);flex:1}
.ca-card-stat{display:flex;align-items:baseline;gap:4px;margin-bottom:10px}
.ca-stat-num{font-size:22px;font-weight:900;color:var(--accent)}
.ca-stat-lbl{font-size:11px;color:var(--text-3)}
.ca-charge-pills{display:flex;gap:5px;flex-wrap:wrap}
.ca-cpill{font-size:10.5px;font-weight:600;padding:3px 8px;border-radius:20px;white-space:nowrap}
.ca-cpill-adm{background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2)}
.ca-cpill-cert{background:rgba(234,179,8,.1);color:#a16207;border:1px solid rgba(234,179,8,.2)}
.ca-cpill-info{background:var(--bg-3);color:var(--text-3);border:1px solid var(--border-2)}
.ca-no-charges{font-size:11px;color:#b45309;background:rgba(234,179,8,.08);border:1px solid rgba(234,179,8,.2);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px}

/* Charge Editor */
.ce-type-section{border-top:1px solid var(--border-1)}
.ce-type-section:first-child{border-top:none}
.ce-type-header{display:flex;align-items:center;justify-content:space-between;padding:12px 20px;background:var(--bg-3)}
.ce-type-name{font-size:13px;font-weight:700;color:var(--text-1)}
.ce-type-count{font-size:11.5px;color:var(--text-3);background:var(--bg-2);border:1px solid var(--border-2);border-radius:20px;padding:2px 10px}
.ce-table-wrap{overflow-x:auto}
.ce-table{width:100%;border-collapse:collapse;font-size:13px}
.ce-table thead th{background:var(--bg-2);color:var(--text-3);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;padding:8px 14px;text-align:left;border-bottom:1px solid var(--border-1)}
.ce-table tbody tr{border-bottom:1px solid var(--border-1)}
.ce-table tbody tr:last-child{border-bottom:none}
.ce-table tbody tr:hover{background:var(--bg-3)}
.ce-table td{padding:10px 14px;vertical-align:middle}
.ce-course-name{font-weight:500;color:var(--text-1)}
.ce-input-wrap{display:flex;align-items:center;gap:4px;background:var(--bg-2);border:1px solid var(--border-2);border-radius:var(--radius-sm);padding:0 8px;transition:border-color .15s}
.ce-input-wrap:focus-within{border-color:var(--accent)}
.ce-rupee{font-size:12px;color:var(--text-3);flex-shrink:0}
.ce-input{border:none;background:transparent;color:var(--text-1);font-size:13px;width:100%;padding:7px 4px;outline:none}
.ce-input::-webkit-outer-spin-button,.ce-input::-webkit-inner-spin-button{-webkit-appearance:none}
</style>
@endpush

@push('scripts')
<script>
function caToggle(cb) {
  const card  = cb.closest('.ca-card');
  const check = card.querySelector('.ca-card-check');
  if (cb.checked) {
    card.classList.add('selected');
    check.classList.add('checked');
  } else {
    card.classList.remove('selected');
    check.classList.remove('checked');
  }
  caUpdateCount();
  caSyncEditor();
}

function caSelectAll() {
  document.querySelectorAll('.ca-checkbox').forEach(cb => {
    cb.checked = true;
    cb.closest('.ca-card').classList.add('selected');
    cb.closest('.ca-card').querySelector('.ca-card-check').classList.add('checked');
  });
  caUpdateCount();
  caSyncEditor();
}

function caClearAll() {
  document.querySelectorAll('.ca-checkbox').forEach(cb => {
    cb.checked = false;
    cb.closest('.ca-card').classList.remove('selected');
    cb.closest('.ca-card').querySelector('.ca-card-check').classList.remove('checked');
  });
  caUpdateCount();
  caSyncEditor();
}

function caUpdateCount() {
  const n = document.querySelectorAll('.ca-checkbox:checked').length;
  document.getElementById('ca-selected-count').textContent = n;
}

function caSyncEditor() {
  const selectedIds = [...document.querySelectorAll('.ca-checkbox:checked')].map(cb => cb.value);
  const wrap = document.getElementById('charge-editor-wrap');
  const empty = document.getElementById('ce-empty');

  if (wrap) {
    wrap.style.display = selectedIds.length > 0 ? '' : 'none';
  }

  document.querySelectorAll('.ce-type-section').forEach(function (sec) {
    const show = selectedIds.includes(sec.dataset.typeId);
    sec.style.display = show ? '' : 'none';
    // Disable inputs of hidden sections so they don't submit
    sec.querySelectorAll('input').forEach(function (inp) {
      inp.disabled = !show;
    });
  });

  if (empty) {
    empty.style.display = selectedIds.length > 0 ? 'none' : '';
  }
}

// Run on load to sync disabled state
document.addEventListener('DOMContentLoaded', caSyncEditor);
</script>
@endpush
