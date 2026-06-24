@extends('layouts.institute')
@section('title','Edit Profile')
@section('topbar-actions')
  <a href="{{ route('institute.students.show', $student) }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@push('styles')
<style>
.sec-nav{display:flex;gap:4px;flex-wrap:wrap;background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:5px;margin-bottom:16px;position:sticky;top:68px;z-index:40;}
.sec-nav a{padding:5px 14px;border-radius:8px;font-size:12.5px;font-weight:700;color:var(--text-2);text-decoration:none;transition:all .13s;}
.sec-nav a:hover{background:var(--bg-3);color:var(--text);}
.sec-nav a.on{background:var(--accent);color:#fff;}

.sec{background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:14px;scroll-margin-top:120px;}
.sec-hd{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;}
.sec-hd-icon{width:30px;height:30px;border-radius:8px;background:var(--bg-3);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.sec-hd-icon svg{color:var(--accent);}
.sec-hd-text .t{font-size:14px;font-weight:900;}
.sec-hd-text .s{font-size:11px;color:var(--text-2);margin-top:1px;}
.sec-body{padding:14px 16px;}
.sec-ft{padding:10px 16px;border-top:1px solid var(--border);background:var(--bg-3);}

.g2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;}
.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
.g5{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;}
@media(max-width:820px){.g4,.g5{grid-template-columns:repeat(3,1fr);}.g3{grid-template-columns:1fr 1fr;}}
@media(max-width:560px){.g2,.g3,.g4,.g5{grid-template-columns:1fr 1fr;}}

/* Compact form group */
.fg{display:flex;flex-direction:column;gap:3px;}
.fg label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-2);}

/* Photo row */
.photo-row{display:flex;align-items:center;gap:16px;}
.photo-circle{width:72px;height:72px;border-radius:50%;overflow:hidden;border:2px solid var(--border);flex-shrink:0;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:900;color:#fff;}
.photo-circle img{width:100%;height:100%;object-fit:cover;}

/* Success toast */
.s-ok{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;border-radius:8px;padding:7px 12px;font-size:12.5px;font-weight:600;display:flex;align-items:center;gap:6px;margin-bottom:10px;}

/* Edu table */
.edu-tbl{width:100%;border-collapse:collapse;font-size:12.5px;}
.edu-tbl th{padding:7px 9px;background:var(--bg-3);font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-2);border-bottom:1px solid var(--border);text-align:left;}
.edu-tbl td{padding:7px 9px;border-bottom:1px solid var(--border);vertical-align:middle;}
.edu-tbl tr:last-child td{border-bottom:none;}

/* Divider label */
.div-lbl{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--text-2);padding:10px 0 8px;border-bottom:1px solid var(--border);margin-bottom:12px;}
</style>
@endpush

@section('content')
@php
  $p = $student->profile;
  $photo = $p?->photo;
  $hasPhoto = $photo && $photo !== 'images/user.svg' && $photo !== 'images/user.png';
  $name = $p?->name ?? $student->user_id;
@endphp

{{-- Section Nav --}}
<div class="sec-nav">
  <a href="#photo">Photo</a>
  <a href="#basic">Basic Info</a>
  <a href="#guardian">Guardian</a>
  <a href="#address">Address</a>
  <a href="#education">Education</a>
</div>

{{-- ── PHOTO ── --}}
<div class="sec" id="photo">
  <div class="sec-hd">
    <div class="sec-hd-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
    <div class="sec-hd-text"><div class="t">Profile Photo</div></div>
  </div>
  @if(session('success_photo'))
    <div class="s-ok" style="margin:10px 16px 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ session('success_photo') }}</div>
  @endif
  <form method="POST" action="{{ route('institute.students.update', $student) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <input type="hidden" name="_section" value="photo">
    <div class="sec-body">
      <div class="photo-row">
        <div class="photo-circle" id="photo-wrap">
          @if($hasPhoto)<img src="{{ asset($photo) }}" id="photo-img" alt="">
          @else<span>{{ strtoupper(substr($name,0,1)) }}</span>@endif
        </div>
        <div>
          <input type="file" name="photo" id="photo-file" class="gt-input" accept="image/*" required style="font-size:12.5px;max-width:260px;">
          <div style="font-size:11px;color:var(--text-2);margin-top:4px;">JPG / PNG / WEBP — max 2 MB</div>
          @error('photo')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
    <div class="sec-ft"><button class="btn btn-primary btn-sm">Save Photo</button></div>
  </form>
</div>

{{-- ── BASIC INFO ── --}}
<div class="sec" id="basic">
  <div class="sec-hd">
    <div class="sec-hd-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
    <div class="sec-hd-text"><div class="t">Basic Information</div><div class="s">Name, contact, personal details</div></div>
  </div>
  @if(session('success_basic'))
    <div class="s-ok" style="margin:10px 16px 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ session('success_basic') }}</div>
  @endif
  <form method="POST" action="{{ route('institute.students.update', $student) }}">
    @csrf @method('PUT')
    <input type="hidden" name="_section" value="basic">
    <div class="sec-body">

      <div class="g3" style="margin-bottom:12px;">
        <div class="fg">
          <label>Full Name <span style="color:var(--danger)">*</span></label>
          <input type="text" name="name" class="gt-input" required value="{{ old('name',$p?->name) }}">
          @error('name')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
        <div class="fg">
          <label>Mobile <span style="color:var(--danger)">*</span></label>
          <input type="tel" name="mobile" class="gt-input" required maxlength="10"
                 value="{{ old('mobile',$student->mobile) }}"
                 oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
          @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
        <div class="fg">
          <label>Email</label>
          <input type="email" name="email" class="gt-input" value="{{ old('email',$student->email) }}">
        </div>
      </div>

      <div class="g5" style="margin-bottom:12px;">
        <div class="fg">
          <label>Date of Birth</label>
          <input type="date" name="dob" class="gt-input" value="{{ old('dob',$p?->dob?->format('Y-m-d')) }}">
        </div>
        <div class="fg">
          <label>Gender</label>
          <select name="gender" class="gt-select">
            <option value="">—</option>
            @foreach(['Male','Female','Other'] as $o)
              <option value="{{ $o }}" {{ old('gender',$p?->gender)===$o?'selected':'' }}>{{ $o }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>Blood Group</label>
          <select name="blood_group" class="gt-select">
            <option value="">—</option>
            @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $o)
              <option value="{{ $o }}" {{ old('blood_group',$p?->blood_group)===$o?'selected':'' }}>{{ $o }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>Category</label>
          <select name="category" class="gt-select">
            <option value="">—</option>
            @foreach(['General','OBC','SC','ST','EWS','Other'] as $o)
              <option value="{{ $o }}" {{ old('category',$p?->category)===$o?'selected':'' }}>{{ $o }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>Religion</label>
          <select name="religion" class="gt-select">
            <option value="">—</option>
            @foreach(['Hindu','Muslim','Sikh','Christian','Jain','Buddhist','Other'] as $o)
              <option value="{{ $o }}" {{ old('religion',$p?->religion)===$o?'selected':'' }}>{{ $o }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="g5">
        <div class="fg">
          <label>Nationality</label>
          <select name="nationality" class="gt-select">
            <option value="">—</option>
            @foreach(['Indian','NRI','Other'] as $o)
              <option value="{{ $o }}" {{ old('nationality',$p?->nationality)===$o?'selected':'' }}>{{ $o }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>Qualification</label>
          <input type="text" name="qualification" class="gt-input" value="{{ old('qualification',$p?->qualification) }}">
        </div>
        <div class="fg">
          <label>Aadhar No.</label>
          <input type="text" name="aadhar_no" class="gt-input" maxlength="12"
                 value="{{ old('aadhar_no',$p?->aadhar_no) }}"
                 oninput="this.value=this.value.replace(/\D/g,'').slice(0,12)">
        </div>
        <div class="fg">
          <label>PAN No.</label>
          <input type="text" name="pan_no" class="gt-input" maxlength="10"
                 value="{{ old('pan_no',$p?->pan_no) }}"
                 oninput="this.value=this.value.toUpperCase()" style="text-transform:uppercase">
        </div>
        <div class="fg">
          <label>WhatsApp</label>
          <input type="tel" name="whatsapp_no" class="gt-input" maxlength="10"
                 value="{{ old('whatsapp_no',$p?->whatsapp_no) }}"
                 oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
        </div>
      </div>

    </div>
    <div class="sec-ft"><button class="btn btn-primary btn-sm">Save Basic Info</button></div>
  </form>
</div>

{{-- ── GUARDIAN ── --}}
<div class="sec" id="guardian">
  <div class="sec-hd">
    <div class="sec-hd-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
    <div class="sec-hd-text"><div class="t">Guardian / Parent</div><div class="s">Father, mother, guardian details</div></div>
  </div>
  @if(session('success_guardian'))
    <div class="s-ok" style="margin:10px 16px 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ session('success_guardian') }}</div>
  @endif
  <form method="POST" action="{{ route('institute.students.update', $student) }}">
    @csrf @method('PUT')
    <input type="hidden" name="_section" value="guardian">
    <div class="sec-body">
      <div class="g3" style="margin-bottom:12px;">
        <div class="fg">
          <label>Father's Name</label>
          <input type="text" name="father_name" class="gt-input" value="{{ old('father_name',$p?->father_name) }}">
        </div>
        <div class="fg">
          <label>Mother's Name</label>
          <input type="text" name="mother_name" class="gt-input" value="{{ old('mother_name',$p?->mother_name) }}">
        </div>
        <div class="fg">
          <label>Guardian Name</label>
          <input type="text" name="guardian_name" class="gt-input" value="{{ old('guardian_name',$p?->guardian_name) }}">
        </div>
      </div>
      <div class="g3">
        <div class="fg">
          <label>Relation with Student</label>
          <select name="guardian_relation" class="gt-select">
            <option value="">—</option>
            @foreach(['Father','Mother','Brother','Sister','Uncle','Aunt','Spouse','Other'] as $o)
              <option value="{{ $o }}" {{ old('guardian_relation',$p?->guardian_relation)===$o?'selected':'' }}>{{ $o }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>Guardian Mobile</label>
          <input type="tel" name="guardian_mobile" class="gt-input" maxlength="10"
                 value="{{ old('guardian_mobile',$p?->guardian_mobile) }}"
                 oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
        </div>
        <div class="fg">
          <label>Occupation</label>
          <input type="text" name="guardian_occupation" class="gt-input" value="{{ old('guardian_occupation',$p?->guardian_occupation) }}">
        </div>
      </div>
    </div>
    <div class="sec-ft"><button class="btn btn-primary btn-sm">Save Guardian</button></div>
  </form>
</div>

{{-- ── ADDRESS ── --}}
<div class="sec" id="address">
  <div class="sec-hd">
    <div class="sec-hd-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
    <div class="sec-hd-text"><div class="t">Address</div><div class="s">Present and permanent address</div></div>
  </div>
  @if(session('success_address'))
    <div class="s-ok" style="margin:10px 16px 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ session('success_address') }}</div>
  @endif
  <form method="POST" action="{{ route('institute.students.update', $student) }}">
    @csrf @method('PUT')
    <input type="hidden" name="_section" value="address">
    <div class="sec-body">

      {{-- Present Address --}}
      <div class="div-lbl">Present Address</div>
      <div class="fg" style="margin-bottom:12px;">
        <label>Street / Locality</label>
        <textarea name="address" class="gt-textarea" rows="2" style="resize:vertical">{{ old('address',$p?->address) }}</textarea>
      </div>
      <div class="g4" style="margin-bottom:16px;">
        <div class="fg">
          <label>State</label>
          <select name="state" id="state" class="gt-select" onchange="syncDistricts('state','district')">
            <option value="">—</option>
            @foreach($states as $s)
              <option value="{{ $s }}" {{ old('state',$p?->state)===$s?'selected':'' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>District</label>
          <select name="district" id="district" class="gt-select">
            <option value="">—</option>
          </select>
        </div>
        <div class="fg">
          <label>City</label>
          <input type="text" name="city" class="gt-input" value="{{ old('city',$p?->city) }}">
        </div>
        <div class="fg">
          <label>PIN Code</label>
          <input type="text" name="pin_code" class="gt-input" maxlength="6"
                 value="{{ old('pin_code',$p?->pin_code) }}"
                 oninput="this.value=this.value.replace(/\D/g,'').slice(0,6)">
        </div>
      </div>

      {{-- Same as checkbox --}}
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;cursor:pointer;margin-bottom:12px;" for="same_chk">
        <input type="checkbox" id="same_chk" style="width:15px;height:15px;">
        Permanent address same as present
      </label>

      {{-- Permanent Address --}}
      <div class="div-lbl">Permanent Address</div>
      <div class="fg" style="margin-bottom:12px;">
        <label>Street / Locality</label>
        <textarea name="permanent_address" id="perm_addr" class="gt-textarea" rows="2" style="resize:vertical">{{ old('permanent_address',$p?->permanent_address) }}</textarea>
      </div>
      <div class="g4">
        <div class="fg">
          <label>State</label>
          <select name="permanent_state" id="perm_state" class="gt-select" onchange="syncDistricts('perm_state','perm_district')">
            <option value="">—</option>
            @foreach($states as $s)
              <option value="{{ $s }}" {{ old('permanent_state',$p?->permanent_state)===$s?'selected':'' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>District</label>
          <select name="permanent_district" id="perm_district" class="gt-select">
            <option value="">—</option>
          </select>
        </div>
        <div class="fg">
          <label>City</label>
          <input type="text" name="permanent_city" id="perm_city" class="gt-input" value="{{ old('permanent_city',$p?->permanent_city) }}">
        </div>
        <div class="fg">
          <label>PIN Code</label>
          <input type="text" name="permanent_pin_code" id="perm_pin" class="gt-input" maxlength="6"
                 value="{{ old('permanent_pin_code',$p?->permanent_pin_code) }}"
                 oninput="this.value=this.value.replace(/\D/g,'').slice(0,6)">
        </div>
      </div>

    </div>
    <div class="sec-ft"><button class="btn btn-primary btn-sm">Save Address</button></div>
  </form>
</div>

{{-- ── EDUCATION ── --}}
<div class="sec" id="education">
  <div class="sec-hd">
    <div class="sec-hd-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></div>
    <div class="sec-hd-text"><div class="t">Education</div><div class="s">Academic qualifications</div></div>
  </div>

  {{-- Existing rows --}}
  <div style="overflow:auto;">
    <table class="edu-tbl">
      <thead>
        <tr>
          <th>Examination</th><th>Institute</th><th>Board / Univ.</th>
          <th>Year</th><th>Division</th><th>%</th><th style="width:60px"></th>
        </tr>
      </thead>
      <tbody id="edu-body">
        @forelse($student->education as $edu)
          <tr id="edu-{{ $edu->id }}">
            <td>{{ $edu->examination }}</td>
            <td>{{ $edu->institute_name ?: '—' }}</td>
            <td>{{ $edu->board_university ?: '—' }}</td>
            <td>{{ $edu->passing_year ?: '—' }}</td>
            <td>{{ $edu->division ?: '—' }}</td>
            <td>{{ $edu->marks_percentage ?: '—' }}</td>
            <td><button type="button" class="btn btn-outline btn-xs" style="color:var(--danger);font-size:11px;" data-del="{{ $edu->id }}">✕</button></td>
          </tr>
        @empty
          <tr id="edu-empty"><td colspan="7" style="padding:16px 10px;color:var(--text-2);font-size:12.5px;text-align:center">No records yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Add row --}}
  <div style="padding:12px 16px;border-top:1px solid var(--border);background:var(--bg-3);">
    <div style="font-size:12px;font-weight:700;margin-bottom:10px;color:var(--text-2);">ADD RECORD</div>
    <form id="edu-form">
      @csrf
      <input type="hidden" name="user_id" value="{{ $student->id }}">
      <div class="g3" style="margin-bottom:10px;">
        <div class="fg"><label>Examination <span style="color:var(--danger)">*</span></label>
          <input type="text" name="examination" class="gt-input" required placeholder="10th / 12th / B.A…"></div>
        <div class="fg"><label>Institute / School</label>
          <input type="text" name="institute_name" class="gt-input" placeholder="School name"></div>
        <div class="fg"><label>Board / University</label>
          <input type="text" name="board_university" class="gt-input" placeholder="UP Board, CBSE…"></div>
      </div>
      <div class="g4" style="margin-bottom:10px;">
        <div class="fg"><label>Year</label>
          <input type="text" name="passing_year" class="gt-input" placeholder="2022" maxlength="4"
                 oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"></div>
        <div class="fg"><label>Division</label>
          <select name="division" class="gt-select">
            <option value="">—</option>
            @foreach(['First','Second','Third','Pass','Distinction'] as $d)
              <option value="{{ $d }}">{{ $d }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg"><label>Marks %</label>
          <input type="text" name="marks_percentage" class="gt-input" placeholder="85.5"></div>
        <div class="fg" style="justify-content:flex-end;padding-top:16px;">
          <button type="submit" class="btn btn-primary btn-sm" id="edu-btn">+ Add</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
const districtsByState = @json($districtsByState ?? []);

// Populate district dropdown based on state
function syncDistricts(stateId, districtId, preSelected) {
  const stateEl    = document.getElementById(stateId);
  const districtEl = document.getElementById(districtId);
  if (!stateEl || !districtEl) return;
  const list = districtsByState[stateEl.value] || [];
  districtEl.innerHTML = '<option value="">—</option>' +
    list.map(d => `<option value="${d}"${d === preSelected ? ' selected' : ''}>${d}</option>`).join('');
}

// Init dropdowns on load
syncDistricts('state',      'district',      @json(old('district',      $p?->district)));
syncDistricts('perm_state', 'perm_district', @json(old('permanent_district', $p?->permanent_district)));

// Photo preview
document.getElementById('photo-file')?.addEventListener('change', function() {
  if (!this.files[0]) return;
  const r = new FileReader();
  r.onload = e => {
    const w = document.getElementById('photo-wrap');
    w.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
  };
  r.readAsDataURL(this.files[0]);
});

// Same as present
document.getElementById('same_chk')?.addEventListener('change', function() {
  if (!this.checked) return;
  document.getElementById('perm_addr').value = document.querySelector('[name="address"]').value;
  const st = document.getElementById('state').value;
  document.getElementById('perm_state').value = st;
  syncDistricts('perm_state','perm_district', document.getElementById('district').value);
  document.querySelector('[name="permanent_city"]').value  = document.querySelector('[name="city"]').value;
  document.getElementById('perm_pin').value = document.querySelector('[name="pin_code"]').value;
});

// Education AJAX
const csrf   = '{{ csrf_token() }}';
const addUrl = '{{ route("institute.enrollment.education.add") }}';
const delUrl = '{{ route("institute.enrollment.education.remove","__ID__") }}';

document.getElementById('edu-form')?.addEventListener('submit', async e => {
  e.preventDefault();
  const btn = document.getElementById('edu-btn');
  btn.disabled = true; btn.textContent = '…';
  const fd  = new FormData(e.target);
  const res = await fetch(addUrl, {
    method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body:fd
  });
  btn.disabled = false; btn.textContent = '+ Add';
  if (!res.ok) { alert('Could not save.'); return; }
  const d = await res.json();
  document.getElementById('edu-empty')?.remove();
  const tbody = document.getElementById('edu-body');
  const tr = document.createElement('tr');
  tr.id = `edu-${d.id}`;
  ['examination','institute_name','board_university','passing_year','division','marks_percentage']
    .forEach(k => { const td=document.createElement('td'); td.textContent=fd.get(k)||'—'; tr.appendChild(td); });
  const td = document.createElement('td');
  td.innerHTML = `<button type="button" class="btn btn-outline btn-xs" style="color:var(--danger);font-size:11px;" data-del="${d.id}">✕</button>`;
  tr.appendChild(td);
  tbody.appendChild(tr);
  e.target.reset();
});

document.getElementById('edu-body')?.addEventListener('click', async e => {
  const btn = e.target.closest('[data-del]');
  if (!btn || !confirm('Remove this record?')) return;
  const id = btn.dataset.del;
  const res = await fetch(delUrl.replace('__ID__',id),{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}});
  if (!res.ok) { alert('Could not delete.'); return; }
  document.getElementById(`edu-${id}`)?.remove();
  if (!document.querySelector('#edu-body tr[id]'))
    document.getElementById('edu-body').innerHTML = '<tr id="edu-empty"><td colspan="7" style="padding:16px 10px;color:var(--text-2);font-size:12.5px;text-align:center">No records yet.</td></tr>';
});

// Section nav scroll highlight
const navLinks = [...document.querySelectorAll('.sec-nav a')];
const secs = [...document.querySelectorAll('.sec')];
window.addEventListener('scroll', () => {
  let cur='';
  secs.forEach(s => { if(window.scrollY >= s.offsetTop - 140) cur=s.id; });
  navLinks.forEach(a => a.classList.toggle('on', a.getAttribute('href')==='#'+cur));
},{passive:true});
</script>
@endpush
