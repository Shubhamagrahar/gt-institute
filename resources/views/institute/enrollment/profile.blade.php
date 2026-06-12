@extends('layouts.institute')
@section('title','Student Profile')
@section('page-title','Process Admission')

@push('styles')
<style>
/* ── Layout ── */
.adm-wrap{display:flex;gap:20px;align-items:flex-start;max-width:1300px;margin:0 auto}
.adm-sidebar{width:200px;min-width:200px;position:sticky;top:16px}
.adm-main{flex:1;min-width:0}

/* ── Sidebar ── */
.sid-photo-wrap{position:relative;width:88px;height:88px;margin:0 auto 10px}
.sid-photo{width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--border);display:block}
.sid-edit-btn{position:absolute;bottom:1px;right:1px;width:26px;height:26px;border-radius:50%;background:var(--primary);color:#fff;border:2px solid var(--bg-1);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;line-height:1}
.sid-name{font-weight:800;font-size:14px;text-align:center;line-height:1.3;word-break:break-word}
.sid-course{font-size:11px;color:var(--text-2);text-align:center;margin-top:3px;line-height:1.4}
.sid-enroll{font-family:monospace;font-size:11px;color:var(--primary);font-weight:700;text-align:center;margin-top:4px}

.sid-nav{margin-top:14px;display:flex;flex-direction:column;gap:2px}
.sid-nav-item{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:10px;cursor:pointer;font-size:12px;font-weight:600;color:var(--text-2);transition:.15s;text-decoration:none;border:none;background:none;width:100%;text-align:left}
.sid-nav-item:hover{background:var(--bg-3);color:var(--text-1)}
.sid-nav-item.active{background:rgba(108,93,211,.12);color:var(--primary)}
.sid-nav-dot{width:22px;height:22px;border-radius:50%;background:var(--bg-3);border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;flex-shrink:0}
.sid-nav-item.active .sid-nav-dot{background:var(--primary);border-color:var(--primary);color:#fff}
.sid-nav-item.done .sid-nav-dot{background:#16a34a;border-color:#16a34a;color:#fff}

.sid-proceed{margin-top:14px;display:block;text-align:center;width:100%;padding:10px;background:var(--primary);color:#fff;border-radius:12px;font-weight:700;font-size:13px;text-decoration:none;transition:.15s}
.sid-proceed:hover{opacity:.88}

/* ── Section cards ── */
.sec-card{background:var(--bg-2);border:1px solid var(--border);border-radius:16px;margin-bottom:14px;overflow:hidden}
.sec-head{display:flex;align-items:center;justify-content:space-between;padding:11px 16px;border-bottom:1px solid var(--border);cursor:pointer;user-select:none}
.sec-title{font-weight:800;font-size:13px;display:flex;align-items:center;gap:8px}
.sec-toggle{font-size:16px;color:var(--text-2);transition:transform .2s}
.sec-body{padding:16px}
.sec-body.collapsed{display:none}

/* ── Compact form grid ── */
.fg4{display:grid;grid-template-columns:repeat(4,1fr);gap:0 14px}
.fg3{display:grid;grid-template-columns:repeat(3,1fr);gap:0 14px}
.fg2{display:grid;grid-template-columns:repeat(2,1fr);gap:0 14px}
@media(max-width:1100px){.fg4{grid-template-columns:repeat(3,1fr)}.fg3{grid-template-columns:repeat(2,1fr)}}
@media(max-width:800px){.fg4,.fg3,.fg2{grid-template-columns:repeat(2,1fr)}.adm-sidebar{display:none}}

.gt-form-group.compact{margin-bottom:10px}
.gt-form-group.compact .gt-label{font-size:11px;margin-bottom:3px}
.gt-form-group.compact .gt-input,
.gt-form-group.compact .gt-select,
.gt-form-group.compact .gt-textarea{padding:6px 10px;font-size:13px}

.sec-footer{display:flex;justify-content:flex-end;padding-top:6px;border-top:1px solid var(--border);margin-top:10px}

/* ── Divider label ── */
.field-group-label{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--text-2);grid-column:1/-1;margin-top:6px;margin-bottom:2px;padding-bottom:4px;border-bottom:1px solid var(--border)}

/* ── Alert inline ── */
.sec-alert{padding:8px 12px;border-radius:8px;font-size:12px;margin-bottom:10px}
.sec-alert-success{background:#f0fdf4;color:#15803d;border:1px solid #86efac}
.sec-alert-error{background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5}
</style>
@endpush

@section('content')
@php
  $fieldMap     = $fields->keyBy('field_key');
  $personalKeys = ['dob','gender','category','religion','nationality','whatsapp_no',
                   'alternate_mobile','aadhar_no','pan_no','blood_group',
                   'employment_status','computer_literacy','qualification'];
  $contactKeys  = ['mobile','email'];
  $addressKeys  = ['address','permanent_address','state','district','city','pin_code',
                   'permanent_state','permanent_district','permanent_city','permanent_pin_code'];
  $guardianKeys = ['father_name','mother_name','guardian_name','guardian_relation',
                   'guardian_mobile','guardian_occupation'];

  $photoSrc = $profile?->photo ? asset($profile->photo) : asset('images/user.svg');

  $hasGuardian  = collect($guardianKeys)->filter(fn($k)=>($fieldMap[$k]??null)?->is_active)->isNotEmpty();
  $personalDone = (bool)$courseBook->profile_completed_at;
  $guardianDone = $profile?->father_name || $profile?->mother_name;
  $eduDone      = $education->count() > 0;
@endphp

{{-- Top breadcrumb --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px">
  <div>
    <div style="font-size:18px;font-weight:900">{{ $profile?->name ?? $courseBook->student->user_id }}</div>
    <div class="text-xs text-muted" style="margin-top:2px">
      {{ $courseBook->course->name }}
      @if($courseBook->batch) &middot; {{ $courseBook->batch->name }}@endif
      &middot; Booked {{ $courseBook->book_date?->format('d M Y') }}
    </div>
  </div>
  <a href="{{ route('institute.enrollment.pending') }}" class="btn btn-outline btn-sm">← Back</a>
</div>

<div class="adm-wrap">

  {{-- ── Sidebar ── --}}
  <div class="adm-sidebar">
    <div class="sec-card" style="padding:16px">
      {{-- Photo --}}
      <div class="sid-photo-wrap">
        <img src="{{ $photoSrc }}" class="sid-photo" id="sid-photo-img" alt="photo"
             onerror="this.src='{{ asset('images/user.svg') }}'">
        <label for="photo-file-input" class="sid-edit-btn" title="Change photo">✎</label>
      </div>
      <div class="sid-name" id="sid-name-display">{{ $profile?->name ?? $courseBook->student->user_id }}</div>
      <div class="sid-course">{{ $courseBook->course->name }}</div>
      @if($courseBook->enrollment_no)
        <div class="sid-enroll">{{ $courseBook->enrollment_no }}</div>
      @else
        <div class="sid-enroll" style="color:var(--text-2)">Pending Admission</div>
      @endif

      {{-- Step Nav --}}
      <div class="sid-nav">
        <button type="button" class="sid-nav-item {{ $personalDone ? 'done' : 'active' }}" onclick="scrollTo('sec-personal')">
          <span class="sid-nav-dot">{{ $personalDone ? '✓' : '1' }}</span>
          Personal Details
        </button>
        @if($hasGuardian)
        <button type="button" class="sid-nav-item {{ $guardianDone ? 'done' : '' }}" onclick="scrollTo('sec-guardian')">
          <span class="sid-nav-dot">{{ $guardianDone ? '✓' : '2' }}</span>
          Guardian Details
        </button>
        @endif
        @if($educationEnabled)
        <button type="button" class="sid-nav-item {{ $eduDone ? 'done' : '' }}" onclick="scrollTo('sec-education')">
          <span class="sid-nav-dot">{{ $eduDone ? '✓' : ($hasGuardian ? '3' : '2') }}</span>
          Education
        </button>
        @endif
      </div>

      <a href="{{ route('institute.enrollment.fee', $courseBook) }}"
         class="sid-proceed" style="margin-top:16px">
        Proceed to Payment →
      </a>
    </div>
  </div>

  {{-- ── Main content ── --}}
  <div class="adm-main">

    {{-- ══ Personal Details ══ --}}
    <div class="sec-card" id="sec-personal">
      <div class="sec-head" onclick="toggleSec(this)">
        <div class="sec-title">
          <span style="width:20px;height:20px;border-radius:50%;background:var(--primary);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:900">1</span>
          Personal Details
          @if($personalDone)<span class="badge badge-success" style="font-size:10px">Saved</span>@endif
        </div>
        <span class="sec-toggle">▾</span>
      </div>
      <div class="sec-body" id="body-personal">
        @if(session('success_personal'))
          <div class="sec-alert sec-alert-success">{{ session('success_personal') }}</div>
        @endif
        @if($errors->has('name') || collect(['mobile','email','dob','gender','state'])->filter(fn($k)=>$errors->has($k))->isNotEmpty())
          <div class="sec-alert sec-alert-error">Please fix the errors below.</div>
        @endif

        <form method="POST" action="{{ route('institute.enrollment.save-profile', $courseBook) }}"
              enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_section" value="personal">

          {{-- Hidden photo input (triggered by sidebar edit btn) --}}
          <input type="file" name="photo" id="photo-file-input" accept="image/*" style="display:none">

          {{-- Name + Contact --}}
          <div class="fg4">
            <div class="field-group-label">Basic Info</div>
            <div class="gt-form-group compact">
              <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
              <input type="text" name="name" class="gt-input" required
                     value="{{ old('name', $profile?->name) }}"
                     oninput="document.getElementById('sid-name-display').textContent=this.value||'Student'">
              @error('name')<div class="gt-error" style="font-size:11px">{{ $message }}</div>@enderror
            </div>
            @foreach($contactKeys as $key)
              @php $field = $fieldMap[$key] ?? null; @endphp
              @continue(!$field || !$field->is_active)
              <div class="gt-form-group compact">
                <label class="gt-label">{{ $field->field_label }}@if($field->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                <input type="{{ $field->field_type }}" name="{{ $field->field_key }}" class="gt-input"
                       value="{{ old($field->field_key, $courseBook->student->{$field->field_key}) }}"
                       {{ $field->is_required ? 'required' : '' }}>
                @error($field->field_key)<div class="gt-error" style="font-size:11px">{{ $message }}</div>@enderror
              </div>
            @endforeach

            {{-- Personal fields --}}
            @php $shownPersonal = false; @endphp
            @foreach($personalKeys as $key)
              @php $field = $fieldMap[$key] ?? null; @endphp
              @continue(!$field || !$field->is_active)
              @if(!$shownPersonal)
                <div class="field-group-label">Personal Info</div>
                @php $shownPersonal = true; @endphp
              @endif
              <div class="gt-form-group compact">
                <label class="gt-label">{{ $field->field_label }}@if($field->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                @if($field->field_type === 'select')
                  @php
                    $opts = collect(explode(',', $field->options ?? ''))->map(fn($o)=>trim($o))->filter();
                    $sel  = old($field->field_key, $profile?->{$field->field_key});
                  @endphp
                  <select name="{{ $field->field_key }}" class="gt-select" {{ $field->is_required?'required':'' }}>
                    <option value="">Select</option>
                    @foreach($opts as $o)<option value="{{ $o }}" {{ $sel==$o?'selected':'' }}>{{ $o }}</option>@endforeach
                  </select>
                @elseif($field->field_type === 'file')
                  {{-- handled via sidebar photo --}}
                @else
                  <input type="{{ $field->field_type }}" name="{{ $field->field_key }}" class="gt-input"
                         value="{{ old($field->field_key, $profile?->{$field->field_key}) }}"
                         {{ $field->is_required?'required':'' }}>
                @endif
                @error($field->field_key)<div class="gt-error" style="font-size:11px">{{ $message }}</div>@enderror
              </div>
            @endforeach

            {{-- Address fields --}}
            @php $shownAddr = false; @endphp
            @foreach($addressKeys as $key)
              @php $field = $fieldMap[$key] ?? null; @endphp
              @continue(!$field || !$field->is_active)
              @if(!$shownAddr)
                <div class="field-group-label">Address</div>
                @php $shownAddr = true; @endphp
              @endif
              <div class="gt-form-group compact {{ in_array($key,['address','permanent_address']) ? 'fg2-span' : '' }}">
                <label class="gt-label">{{ $field->field_label }}@if($field->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                @if($field->field_type === 'textarea' || in_array($key, ['address','permanent_address']))
                  <textarea name="{{ $field->field_key }}" class="gt-input" rows="2"
                            {{ $field->is_required?'required':'' }}>{{ old($field->field_key, $profile?->{$field->field_key}) }}</textarea>
                @elseif($field->field_type === 'select' || in_array($key, ['state','permanent_state']))
                  @php
                    $opts = ($states ?? collect());
                    $sel  = old($field->field_key, $profile?->{$field->field_key});
                  @endphp
                  <select name="{{ $field->field_key }}" class="gt-select" {{ $field->is_required?'required':'' }}>
                    <option value="">Select State</option>
                    @foreach($opts as $o)<option value="{{ $o }}" {{ $sel==$o?'selected':'' }}>{{ $o }}</option>@endforeach
                  </select>
                @else
                  <input type="{{ $field->field_type }}" name="{{ $field->field_key }}" class="gt-input"
                         value="{{ old($field->field_key, $profile?->{$field->field_key}) }}"
                         {{ $field->is_required?'required':'' }}>
                @endif
                @error($field->field_key)<div class="gt-error" style="font-size:11px">{{ $message }}</div>@enderror
              </div>
            @endforeach
          </div>

          <div class="sec-footer">
            <button type="submit" class="btn btn-primary btn-sm">Save Personal Details</button>
          </div>
        </form>
      </div>
    </div>

    {{-- ══ Guardian Details ══ --}}
    @if($hasGuardian)
    <div class="sec-card" id="sec-guardian">
      <div class="sec-head" onclick="toggleSec(this)">
        <div class="sec-title">
          <span style="width:20px;height:20px;border-radius:50%;background:#6366f1;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:900">2</span>
          Guardian / Parent Details
          @if($guardianDone)<span class="badge badge-success" style="font-size:10px">Saved</span>@endif
        </div>
        <span class="sec-toggle">▾</span>
      </div>
      <div class="sec-body" id="body-guardian">
        @if(session('success_guardian'))
          <div class="sec-alert sec-alert-success">{{ session('success_guardian') }}</div>
        @endif

        <form method="POST" action="{{ route('institute.enrollment.save-profile', $courseBook) }}">
          @csrf
          <input type="hidden" name="_section" value="guardian">
          <div class="fg3">
            @foreach($guardianKeys as $key)
              @php $field = $fieldMap[$key] ?? null; @endphp
              @continue(!$field || !$field->is_active)
              <div class="gt-form-group compact">
                <label class="gt-label">{{ $field->field_label }}@if($field->is_required)<span style="color:var(--danger)">*</span>@endif</label>
                <input type="{{ $field->field_type }}" name="{{ $field->field_key }}" class="gt-input"
                       value="{{ old($field->field_key, $profile?->{$field->field_key}) }}"
                       {{ $field->is_required?'required':'' }}>
                @error($field->field_key)<div class="gt-error" style="font-size:11px">{{ $message }}</div>@enderror
              </div>
            @endforeach
          </div>
          <div class="sec-footer">
            <button type="submit" class="btn btn-primary btn-sm">Save Guardian Details</button>
          </div>
        </form>
      </div>
    </div>
    @endif

    {{-- ══ Education Details ══ --}}
    @if($educationEnabled)
    <div class="sec-card" id="sec-education">
      <div class="sec-head" onclick="toggleSec(this)">
        <div class="sec-title">
          <span style="width:20px;height:20px;border-radius:50%;background:#0ea5e9;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:900">{{ $hasGuardian ? '3' : '2' }}</span>
          Education Details
          @if($eduDone)<span class="badge badge-success" style="font-size:10px">{{ $education->count() }} record(s)</span>@endif
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <button type="button" class="btn btn-outline btn-sm" id="add-edu-btn" onclick="event.stopPropagation();showEduForm()">+ Add Row</button>
          <span class="sec-toggle">▾</span>
        </div>
      </div>
      <div class="sec-body" id="body-education">
        @error('education_details')<div class="sec-alert sec-alert-error">{{ $message }}</div>@enderror

        <div style="overflow-x:auto">
          <table class="gt-table" id="edu-table" style="min-width:580px;font-size:12px">
            <thead>
              <tr>
                <th>Exam / Course</th>
                <th>Institute</th>
                <th>Board / Univ.</th>
                <th>Year</th>
                <th>Division</th>
                <th>%</th>
                <th style="width:60px"></th>
              </tr>
            </thead>
            <tbody id="edu-tbody">
              @forelse($education as $edu)
                <tr data-id="{{ $edu->id }}">
                  <td>{{ $edu->examination }}</td>
                  <td>{{ $edu->institute_name ?: '—' }}</td>
                  <td>{{ $edu->board_university ?: '—' }}</td>
                  <td>{{ $edu->passing_year ?: '—' }}</td>
                  <td>{{ $edu->division ?: '—' }}</td>
                  <td>{{ $edu->marks_percentage ?: '—' }}</td>
                  <td>
                    <button type="button" class="btn btn-sm remove-edu" data-id="{{ $edu->id }}"
                      style="background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5;padding:2px 8px;font-size:11px">✕</button>
                  </td>
                </tr>
              @empty
                <tr id="edu-empty">
                  <td colspan="7" style="text-align:center;color:var(--text-2);padding:14px;font-size:12px">
                    No education records added yet.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Add row inline form --}}
        <div id="edu-form-inline" style="display:none;background:var(--bg-3);border:1px solid var(--border);border-radius:12px;padding:14px;margin-top:12px">
          <div class="fg3" style="margin-bottom:8px">
            <div class="gt-form-group compact">
              <label class="gt-label">Exam / Course @if($educationRequired)<span style="color:var(--danger)">*</span>@endif</label>
              <input type="text" id="edu-exam" class="gt-input" placeholder="10th, 12th, B.A.">
            </div>
            <div class="gt-form-group compact">
              <label class="gt-label">Institute</label>
              <input type="text" id="edu-institute" class="gt-input" placeholder="School / College">
            </div>
            <div class="gt-form-group compact">
              <label class="gt-label">Board / University</label>
              <input type="text" id="edu-board" class="gt-input" placeholder="CBSE, UP Board">
            </div>
            <div class="gt-form-group compact">
              <label class="gt-label">Passing Year</label>
              <input type="number" id="edu-year" class="gt-input" placeholder="2022" min="1980" max="2035">
            </div>
            <div class="gt-form-group compact">
              <label class="gt-label">Division</label>
              <input type="text" id="edu-div" class="gt-input" placeholder="First / Second">
            </div>
            <div class="gt-form-group compact">
              <label class="gt-label">Percentage / Marks</label>
              <input type="text" id="edu-marks" class="gt-input" placeholder="75.5">
            </div>
          </div>
          <div style="display:flex;gap:8px">
            <button type="button" class="btn btn-primary btn-sm" id="save-edu-btn">Save Row</button>
            <button type="button" class="btn btn-outline btn-sm" onclick="hideEduForm()">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    @endif

  </div>{{-- /adm-main --}}
</div>{{-- /adm-wrap --}}
@endsection

@push('scripts')
<script>
// ── Photo preview via sidebar button ──
document.getElementById('photo-file-input').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => { document.getElementById('sid-photo-img').src = e.target.result; };
  reader.readAsDataURL(file);
  // Submit the personal form automatically after photo select
  this.closest('form') || document.querySelector('form[action*="profile"]').submit();
});

// ── Section toggle ──
function toggleSec(head) {
  const body = head.nextElementSibling;
  const arrow = head.querySelector('.sec-toggle');
  const collapsed = body.classList.toggle('collapsed');
  arrow.style.transform = collapsed ? 'rotate(-90deg)' : '';
}

// ── Scroll to section ──
function scrollTo(id) {
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Education ──
@if($educationEnabled)
const userId = {{ $courseBook->user_id }};

function showEduForm() {
  document.getElementById('edu-form-inline').style.display = '';
}
function hideEduForm() {
  document.getElementById('edu-form-inline').style.display = 'none';
}

document.getElementById('save-edu-btn').addEventListener('click', async () => {
  const exam     = document.getElementById('edu-exam').value.trim();
  const institute= document.getElementById('edu-institute').value.trim();
  const board    = document.getElementById('edu-board').value.trim();
  const year     = document.getElementById('edu-year').value.trim();
  const div      = document.getElementById('edu-div').value.trim();
  const marks    = document.getElementById('edu-marks').value.trim();
  if (!exam) { alert('Exam / Course is required.'); return; }

  const res  = await fetch('{{ route("institute.enrollment.education.add") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ user_id: userId, examination: exam, institute_name: institute,
                           board_university: board, passing_year: year, division: div,
                           marks_percentage: marks })
  });
  const json = await res.json();
  if (json.success) {
    document.getElementById('edu-empty')?.remove();
    const tr = document.createElement('tr');
    tr.dataset.id = json.id;
    tr.innerHTML = `<td>${exam}</td><td>${institute||'—'}</td><td>${board||'—'}</td>
                    <td>${year||'—'}</td><td>${div||'—'}</td><td>${marks||'—'}</td>
                    <td><button type="button" class="btn btn-sm remove-edu" data-id="${json.id}"
                        style="background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5;padding:2px 8px;font-size:11px">✕</button></td>`;
    document.getElementById('edu-tbody').appendChild(tr);
    hideEduForm();
    ['edu-exam','edu-institute','edu-board','edu-year','edu-div','edu-marks']
      .forEach(id => document.getElementById(id).value = '');
  }
});

document.addEventListener('click', async e => {
  if (!e.target.classList.contains('remove-edu')) return;
  const id = e.target.dataset.id;
  if (!confirm('Remove this education record?')) return;
  await fetch(`/dashboard/enrollment/education/${id}`, {
    method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  });
  e.target.closest('tr').remove();
});
@endif
</script>
@endpush
