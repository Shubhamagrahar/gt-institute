@extends('layouts.institute')
@section('title','Student Profile')
@section('page-title','Registration Details')

@push('styles')
<style>
.reg-shell{max-width:1180px;margin:0 auto}.reg-card{background:var(--bg-2);border:1px solid var(--border);border-radius:18px;margin-bottom:18px;overflow:hidden}
.reg-card-head{background:linear-gradient(135deg,#6651d8,#503ab9);color:#fff;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.reg-card-title{font-size:18px;font-weight:900}.reg-card-sub{font-size:12px;opacity:.82;margin-top:3px}.reg-card-body{padding:22px}
.reg-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:0 18px}.profile-photo-row{display:grid;grid-template-columns:180px minmax(0,1fr);gap:20px;align-items:start}
.photo-box{border:1px dashed var(--border-2);border-radius:18px;background:var(--bg-3);min-height:180px;display:flex;align-items:center;justify-content:center;text-align:center;padding:16px}
.photo-avatar{width:92px;height:92px;border-radius:50%;object-fit:cover;background:linear-gradient(135deg,#dbe4ff,#f1f5ff);display:grid;place-items:center;margin:0 auto 10px;color:#6c5dd3;font-size:34px;font-weight:900}
.edu-empty{color:var(--text-2);font-size:13px}.edu-form{background:var(--bg-3);border:1px solid var(--border);border-radius:14px;padding:16px;margin-top:12px}
@media(max-width:720px){.profile-photo-row{grid-template-columns:1fr}.reg-card-body{padding:16px}}
</style>
@endpush

@section('content')
@php
  $fieldMap = $fields->keyBy('field_key');
  $studentKeys = ['photo','email','dob','gender','category','religion','nationality','whatsapp_no','alternate_mobile','aadhar_no','pan_no','blood_group','employment_status','computer_literacy','qualification','address','permanent_address','state','district','pin_code'];
  $guardianKeys = ['father_name','mother_name','guardian_name','guardian_relation','guardian_mobile','guardian_occupation'];
@endphp

<div class="reg-shell">
  <div class="gt-card-header" style="margin-bottom:16px;">
    <div>
      <div class="gt-card-title">{{ $courseBook->student->profile?->name ?? $courseBook->student->user_id }}</div>
      <div class="text-muted text-xs" style="margin-top:4px;">{{ $courseBook->course->name }} registration profile</div>
    </div>
    <span class="badge badge-accent">{{ $educationEnabled ? 'Step 2-4' : 'Step 2-3' }}</span>
  </div>

  <form method="POST" action="{{ route('institute.enrollment.save-profile', $courseBook) }}" enctype="multipart/form-data">
    @csrf

    @if($educationEnabled)
    <div class="reg-card">
      <div class="reg-card-head">
        <div>
          <div class="reg-card-title">2. Student Profile</div>
          <div class="reg-card-sub">Photo and personal details</div>
        </div>
      </div>
      <div class="reg-card-body">
        <div class="profile-photo-row">
          <div class="photo-box">
            <div>
              @if($profile?->photo)
                <img src="{{ asset($profile->photo) }}" class="photo-avatar" alt="student">
              @else
                <div class="photo-avatar">{{ strtoupper(substr($courseBook->student->profile?->name ?? 'S', 0, 1)) }}</div>
              @endif
              <div class="fw-600">Default Profile Image</div>
              <div class="text-muted text-xs" style="margin-top:4px;">The default image is used when no photo is uploaded.</div>
            </div>
          </div>

          <div class="reg-grid">
            @foreach($studentKeys as $key)
              @php $field = $fieldMap[$key] ?? null; @endphp
              @continue(!$field || !$field->is_active)
              <div class="gt-form-group">
                <label class="gt-label">
                  {{ $field->field_label }}
                  @if($field->is_required)<span style="color:var(--danger)">*</span>@endif
                </label>

                @if($field->field_type === 'textarea')
                  <textarea name="{{ $field->field_key }}" class="gt-textarea" {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_key, $profile?->{$field->field_key}) }}</textarea>
                @elseif($field->field_type === 'select')
                  @php
                    $options = $field->field_key === 'state'
                      ? ($states ?? collect())
                      : collect(explode(',', $field->options ?? ''))->map(fn ($opt) => trim($opt))->filter();
                    $selectedValue = old($field->field_key, $profile?->{$field->field_key});
                  @endphp
                  <select name="{{ $field->field_key }}" class="gt-select" {{ $field->is_required ? 'required' : '' }}>
                    <option value="">Select</option>
                    @foreach($options as $opt)
                      <option value="{{ trim($opt) }}" {{ $selectedValue == trim($opt) ? 'selected' : '' }}>{{ trim($opt) }}</option>
                    @endforeach
                  </select>
                @elseif($field->field_type === 'file')
                  <input type="file" name="{{ $field->field_key }}" class="gt-input" accept="image/*" {{ $field->is_required && !$profile?->photo ? 'required' : '' }}>
                @else
                  <input type="{{ $field->field_type }}" name="{{ $field->field_key }}" class="gt-input @error($field->field_key) is-invalid @enderror" value="{{ old($field->field_key, in_array($field->field_key, ['mobile', 'email'], true) ? $courseBook->student->{$field->field_key} : $profile?->{$field->field_key}) }}" {{ $field->is_required ? 'required' : '' }}>
                @endif
                @error($field->field_key)<div class="gt-error">{{ $message }}</div>@enderror
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div class="reg-card">
      <div class="reg-card-head">
        <div>
          <div class="reg-card-title">3. Guardian Details</div>
          <div class="reg-card-sub">Parent / guardian contact and occupation</div>
        </div>
      </div>
      <div class="reg-card-body">
        <div class="reg-grid">
          @foreach($guardianKeys as $key)
            @php $field = $fieldMap[$key] ?? null; @endphp
            @continue(!$field || !$field->is_active)
            <div class="gt-form-group">
              <label class="gt-label">{{ $field->field_label }} @if($field->is_required)<span style="color:var(--danger)">*</span>@endif</label>
              <input type="{{ $field->field_type }}" name="{{ $field->field_key }}" class="gt-input" value="{{ old($field->field_key, $profile?->{$field->field_key}) }}" {{ $field->is_required ? 'required' : '' }}>
              @error($field->field_key)<div class="gt-error">{{ $message }}</div>@enderror
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="reg-card">
      <div class="reg-card-head">
        <div>
          <div class="reg-card-title">4. Education Details</div>
          <div class="reg-card-sub">Course / Exam, Institute, Board / University, Year, Division, Percentage</div>
        </div>
        <button type="button" class="btn btn-outline btn-sm" id="add-edu-btn">+ Add Row</button>
      </div>
      <div class="reg-card-body">
        <div class="gt-table-wrap">
          <table class="gt-table" id="edu-table">
            <thead>
              <tr>
                <th>Course / Exam</th>
                <th>Institute</th>
                <th>Board / University</th>
                <th>Year</th>
                <th>Division</th>
                <th>Percentage</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="edu-tbody">
              @forelse($education as $edu)
                <tr data-id="{{ $edu->id }}">
                  <td>{{ $edu->examination }}</td>
                  <td>-</td>
                  <td>{{ $edu->board_university }}</td>
                  <td>{{ $edu->passing_year }}</td>
                  <td>-</td>
                  <td>{{ $edu->marks_percentage }}</td>
                  <td><button type="button" class="btn btn-danger btn-xs remove-edu" data-id="{{ $edu->id }}">Remove</button></td>
                </tr>
              @empty
                <tr id="edu-empty"><td colspan="7" class="edu-empty">Add at least one education record if required.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div id="edu-form" class="edu-form" style="display:none;">
          <div class="reg-grid">
            <div class="gt-form-group">
              <label class="gt-label">Course / Exam @if($educationRequired)<span style="color:var(--danger)">*</span>@endif</label>
              <input type="text" id="edu-exam" class="gt-input" placeholder="10th, 12th, Diploma">
            </div>
            <div class="gt-form-group">
              <label class="gt-label">Institute</label>
              <input type="text" id="edu-institute" class="gt-input" placeholder="Institute name">
            </div>
            <div class="gt-form-group">
              <label class="gt-label">Board / University</label>
              <input type="text" id="edu-board" class="gt-input" placeholder="CBSE, UP Board">
            </div>
            <div class="gt-form-group">
              <label class="gt-label">Year</label>
              <input type="number" id="edu-year" class="gt-input" placeholder="2020" min="1980" max="2035">
            </div>
            <div class="gt-form-group">
              <label class="gt-label">Division</label>
              <input type="text" id="edu-division" class="gt-input" placeholder="First">
            </div>
            <div class="gt-form-group">
              <label class="gt-label">Percentage</label>
              <input type="text" id="edu-marks" class="gt-input" placeholder="75.5">
            </div>
          </div>
          <div style="display:flex;gap:8px;">
            <button type="button" class="btn btn-primary btn-sm" id="save-edu-btn">Save Row</button>
            <button type="button" class="btn btn-outline btn-sm" id="cancel-edu-btn">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    @error('education_details')<div class="gt-error" style="margin-bottom:14px;">{{ $message }}</div>@enderror
    @endif

    <div style="display:flex;justify-content:flex-end;">
      <button type="submit" class="btn btn-primary btn-lg">Continue to Payment</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
@if($educationEnabled)
<script>
const userId = {{ $courseBook->user_id }};
const addBtn = document.getElementById('add-edu-btn');
const eduForm = document.getElementById('edu-form');
const saveBtn = document.getElementById('save-edu-btn');
const cancelBtn = document.getElementById('cancel-edu-btn');
const tbody = document.getElementById('edu-tbody');

addBtn.addEventListener('click', () => { eduForm.style.display = 'block'; addBtn.style.display = 'none'; });
cancelBtn.addEventListener('click', () => { eduForm.style.display = 'none'; addBtn.style.display = ''; });

saveBtn.addEventListener('click', async () => {
  const exam = document.getElementById('edu-exam').value;
  const institute = document.getElementById('edu-institute').value;
  const board = document.getElementById('edu-board').value;
  const year = document.getElementById('edu-year').value;
  const division = document.getElementById('edu-division').value;
  const marks = document.getElementById('edu-marks').value;
  if (!exam) { alert('Please enter Course / Exam.'); return; }

  const res = await fetch('{{ route("institute.enrollment.education.add") }}', {
    method: 'POST',
    headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}'},
    body: JSON.stringify({ user_id: userId, examination: exam, institute, board_university: board, passing_year: year, division, marks_percentage: marks })
  });
  const json = await res.json();
  if (json.success) {
    document.getElementById('edu-empty')?.remove();
    const tr = document.createElement('tr');
    tr.dataset.id = json.id;
    tr.innerHTML = `<td>${exam}</td><td>${institute || '-'}</td><td>${board || '-'}</td><td>${year || '-'}</td><td>${division || '-'}</td><td>${marks || '-'}</td><td><button type="button" class="btn btn-danger btn-xs remove-edu" data-id="${json.id}">Remove</button></td>`;
    tbody.appendChild(tr);
    eduForm.style.display = 'none';
    addBtn.style.display = '';
    ['edu-exam','edu-institute','edu-board','edu-year','edu-division','edu-marks'].forEach(id => document.getElementById(id).value = '');
  }
});

document.addEventListener('click', async (e) => {
  if (!e.target.classList.contains('remove-edu')) return;
  const id = e.target.dataset.id;
  if (!confirm('Remove this education row?')) return;
  await fetch(`/dashboard/enrollment/education/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
  e.target.closest('tr').remove();
});
</script>
@endif
@endpush
