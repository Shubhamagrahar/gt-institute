@extends('layouts.institute')
@section('title','Edit Student')
@section('page-title','Edit Student Profile')
@section('topbar-actions')
  <a href="{{ route('institute.students.show',$student) }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@section('content')
@php $profile = $student->profile; @endphp
<div class="gt-card" style="max-width:980px;">
  <form method="POST" action="{{ route('institute.students.update',$student) }}">
    @csrf
    @method('PUT')

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Full Name <span style="color:var(--danger)">*</span></label>
        <input type="text" name="name" class="gt-input" value="{{ old('name',$profile?->name ?? $student->user_id) }}" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
        <input type="text" name="mobile" class="gt-input" value="{{ old('mobile',$student->mobile) }}" required>
      </div>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Email</label>
        <input type="email" name="email" class="gt-input" value="{{ old('email',$student->email) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Date of Birth</label>
        <input type="date" name="dob" class="gt-input" value="{{ old('dob',$profile?->dob?->format('Y-m-d')) }}">
      </div>
    </div>

    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Gender</label>
        <select name="gender" class="gt-select">
          <option value="">Select</option>
          @foreach(['Male','Female','Other'] as $option)
            <option value="{{ $option }}" {{ old('gender',$profile?->gender) === $option ? 'selected' : '' }}>{{ $option }}</option>
          @endforeach
        </select>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Category</label>
        <input type="text" name="category" class="gt-input" value="{{ old('category',$profile?->category) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Qualification</label>
        <input type="text" name="qualification" class="gt-input" value="{{ old('qualification',$profile?->qualification) }}">
      </div>
    </div>

    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Father Name</label>
        <input type="text" name="father_name" class="gt-input" value="{{ old('father_name',$profile?->father_name) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Mother Name</label>
        <input type="text" name="mother_name" class="gt-input" value="{{ old('mother_name',$profile?->mother_name) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Guardian Name</label>
        <input type="text" name="guardian_name" class="gt-input" value="{{ old('guardian_name',$profile?->guardian_name) }}">
      </div>
    </div>

    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Guardian Relation</label>
        <input type="text" name="guardian_relation" class="gt-input" value="{{ old('guardian_relation',$profile?->guardian_relation) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Guardian Mobile</label>
        <input type="text" name="guardian_mobile" class="gt-input" value="{{ old('guardian_mobile',$profile?->guardian_mobile) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Guardian Occupation</label>
        <input type="text" name="guardian_occupation" class="gt-input" value="{{ old('guardian_occupation',$profile?->guardian_occupation) }}">
      </div>
    </div>

    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">WhatsApp</label>
        <input type="text" name="whatsapp_no" class="gt-input" value="{{ old('whatsapp_no',$profile?->whatsapp_no) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Alternate Mobile</label>
        <input type="text" name="alternate_mobile" class="gt-input" value="{{ old('alternate_mobile',$profile?->alternate_mobile) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">State</label>
        <select name="state" class="gt-select">
          <option value="">Select</option>
          @foreach($states as $state)
            <option value="{{ $state }}" {{ old('state',$profile?->state) === $state ? 'selected' : '' }}>{{ $state }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">District</label>
        <input type="text" name="district" class="gt-input" value="{{ old('district',$profile?->district) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">PIN Code</label>
        <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code',$profile?->pin_code) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Blood Group</label>
        <input type="text" name="blood_group" class="gt-input" value="{{ old('blood_group',$profile?->blood_group) }}">
      </div>
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Address</label>
      <textarea name="address" class="gt-textarea">{{ old('address',$profile?->address) }}</textarea>
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Permanent Address</label>
      <textarea name="permanent_address" class="gt-textarea">{{ old('permanent_address',$profile?->permanent_address) }}</textarea>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Update Profile</button>
      <a href="{{ route('institute.students.show',$student) }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<div class="gt-card" style="max-width:980px;margin-top:20px;">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Education Details</div>
      <div class="text-xs text-muted">Student ki education history yahin add/delete kar sakte hain.</div>
    </div>
  </div>

  <div style="overflow:auto;margin-bottom:16px;">
    <table class="gt-table" id="education-table">
      <thead>
        <tr>
          <th>Exam</th>
          <th>Institute</th>
          <th>Board / University</th>
          <th>Year</th>
          <th>Division</th>
          <th>Percentage</th>
          <th style="width:80px;">Action</th>
        </tr>
      </thead>
      <tbody id="education-body">
        @forelse($student->education as $edu)
          <tr data-edu-row="{{ $edu->id }}">
            <td>{{ $edu->examination }}</td>
            <td>{{ $edu->institute_name ?: '-' }}</td>
            <td>{{ $edu->board_university ?: '-' }}</td>
            <td>{{ $edu->passing_year ?: '-' }}</td>
            <td>{{ $edu->division ?: '-' }}</td>
            <td>{{ $edu->marks_percentage ?: '-' }}</td>
            <td><button type="button" class="btn btn-outline btn-xs" data-delete-edu="{{ $edu->id }}">Delete</button></td>
          </tr>
        @empty
          <tr id="education-empty"><td colspan="7" class="text-muted">No education details added.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <form id="education-form">
    @csrf
    <input type="hidden" name="user_id" value="{{ $student->id }}">
    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Course / Exam <span style="color:var(--danger)">*</span></label>
        <input type="text" name="examination" class="gt-input" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Institute</label>
        <input type="text" name="institute_name" class="gt-input">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Board / University</label>
        <input type="text" name="board_university" class="gt-input">
      </div>
    </div>
    <div class="gt-form-grid-3">
      <div class="gt-form-group">
        <label class="gt-label">Passing Year</label>
        <input type="text" name="passing_year" class="gt-input">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Division</label>
        <input type="text" name="division" class="gt-input">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Marks / Percentage</label>
        <input type="text" name="marks_percentage" class="gt-input">
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Add Education</button>
  </form>
</div>
@endsection

@push('scripts')
<script>
const educationForm = document.getElementById('education-form');
const educationBody = document.getElementById('education-body');
const csrfToken = '{{ csrf_token() }}';
const deleteEducationUrl = '{{ route("institute.enrollment.education.remove", "__ID__") }}';

function cell(value) {
  const td = document.createElement('td');
  td.textContent = value || '-';
  return td;
}

educationForm?.addEventListener('submit', async (event) => {
  event.preventDefault();
  const formData = new FormData(educationForm);
  const response = await fetch('{{ route("institute.enrollment.education.add") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    body: formData,
  });

  if (!response.ok) {
    alert('Education detail save nahi ho paya.');
    return;
  }

  const result = await response.json();
  document.getElementById('education-empty')?.remove();
  const row = document.createElement('tr');
  row.dataset.eduRow = result.id;
  row.append(
    cell(formData.get('examination')),
    cell(formData.get('institute_name')),
    cell(formData.get('board_university')),
    cell(formData.get('passing_year')),
    cell(formData.get('division')),
    cell(formData.get('marks_percentage'))
  );
  const action = document.createElement('td');
  action.innerHTML = `<button type="button" class="btn btn-outline btn-xs" data-delete-edu="${result.id}">Delete</button>`;
  row.appendChild(action);
  educationBody.appendChild(row);
  educationForm.reset();
});

educationBody?.addEventListener('click', async (event) => {
  const button = event.target.closest('[data-delete-edu]');
  if (!button || !confirm('Remove this education row?')) return;

  const id = button.dataset.deleteEdu;
  const response = await fetch(deleteEducationUrl.replace('__ID__', id), {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
  });

  if (!response.ok) {
    alert('Education detail delete nahi ho paya.');
    return;
  }

  button.closest('tr')?.remove();
  if (!educationBody.querySelector('tr')) {
    educationBody.innerHTML = '<tr id="education-empty"><td colspan="7" class="text-muted">No education details added.</td></tr>';
  }
});
</script>
@endpush
