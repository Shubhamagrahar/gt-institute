@extends('layouts.institute')
@section('title','Add Staff Member')
@section('page-title','Add Staff Member')
@section('topbar-actions')
  <a href="{{ route('institute.staff.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@push('styles')
<style>
.sf-section-head { display:flex;align-items:center;gap:10px;padding:16px 0 10px;margin-top:8px; }
.sf-section-head:first-of-type { padding-top:0;margin-top:0; }
.sf-section-label { font-size:10px;font-weight:800;color:var(--text-3);text-transform:uppercase;letter-spacing:.09em;white-space:nowrap; }
.sf-line { flex:1;height:1px;background:var(--border); }
.sf-grid { display:grid;gap:14px; }
.sf-2 { grid-template-columns:1fr 1fr; }
.sf-3 { grid-template-columns:1fr 1fr 1fr; }
.sf-4 { grid-template-columns:1fr 1fr 1fr 1fr; }
.gt-label { font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:6px;display:block; }
.gt-input,.gt-select,.gt-textarea { width:100%;height:42px;border:1.5px solid var(--border);border-radius:9px;padding:0 14px;font-size:13px;background:var(--bg);color:var(--text-1);outline:none;transition:border-color .15s,box-shadow .15s;box-sizing:border-box; }
.gt-input:focus,.gt-select:focus,.gt-textarea:focus { border-color:var(--accent);box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 12%,transparent); }
.gt-textarea { height:76px;padding:10px 14px;resize:vertical; }
.gt-input.is-invalid,.gt-select.is-invalid { border-color:#ef4444; }
.gt-error { font-size:11px;color:#ef4444;margin-top:4px; }
.req { color:#ef4444; }
.opt { font-size:10px;color:var(--text-3);font-weight:400;margin-left:3px; }
.photo-area { width:72px;height:72px;border-radius:50%;border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;overflow:hidden;flex-shrink:0;background:var(--bg-3);transition:border-color .15s; }
.photo-area:hover { border-color:var(--accent); }
.photo-area img { width:100%;height:100%;object-fit:cover; }

/* Required field tracker */
.req-item { display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid var(--border);font-size:12px; }
.req-item:last-child { border-bottom:none; }
.req-dot { width:7px;height:7px;border-radius:50%;flex-shrink:0;transition:.2s; }
.req-dot.done { background:#10b981; }
.req-dot.empty { background:var(--border); }

@media(max-width:900px){ .sf-4{grid-template-columns:1fr 1fr 1fr} }
@media(max-width:680px){ .sf-2,.sf-3,.sf-4{grid-template-columns:1fr 1fr} }
@media(max-width:480px){ .sf-2,.sf-3,.sf-4{grid-template-columns:1fr} }
</style>
@endpush

@section('content')

@if($errors->any())
  <div class="alert alert-danger" style="margin-bottom:16px">Please fix the highlighted errors below.</div>
@endif

<form method="POST" action="{{ route('institute.staff.store') }}" enctype="multipart/form-data" id="staffForm">
@csrf

<div style="display:grid;grid-template-columns:1fr 270px;gap:18px;align-items:start;">

  {{-- ── LEFT: FORM ────────────────────────────────────────────────────────── --}}
  <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:22px 24px;">

    {{-- BASIC INFO --}}
    <div class="sf-section-head">
      <span class="sf-section-label">Basic Information</span>
      <span class="sf-line"></span>
    </div>

    <div style="display:flex;gap:18px;align-items:flex-start;margin-bottom:16px;">
      {{-- Photo --}}
      <div style="text-align:center;flex-shrink:0;">
        <div class="photo-area" onclick="document.getElementById('photoInput').click()">
          <img id="photoPreview" src="" style="display:none">
          <span id="photoIcon"><svg width="22" height="22" fill="none" stroke="var(--text-3)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        </div>
        <input type="file" id="photoInput" name="photo" accept="image/*" style="display:none">
        <div style="font-size:10px;color:var(--text-3);margin-top:4px">Photo</div>
      </div>

      <div style="flex:1;min-width:0;">
        <div class="sf-grid sf-2" style="margin-bottom:14px">
          <div>
            <label class="gt-label">Full Name <span class="req">*</span></label>
            <input type="text" name="name" id="fName" class="gt-input @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="e.g. Suresh Kumar" required>
            @error('name')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="gt-label">Mobile Number <span class="req">*</span></label>
            <input type="tel" name="mobile" id="fMobile" class="gt-input @error('mobile') is-invalid @enderror"
                   value="{{ old('mobile') }}" placeholder="10-digit mobile" maxlength="10" inputmode="numeric"
                   oninput="this.value=this.value.replace(/\D/g,'');syncPreview()" required>
            @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="sf-grid sf-4">
          <div>
            <label class="gt-label">Role <span class="req">*</span></label>
            <select name="staff_role_id" id="fRole" class="gt-select @error('staff_role_id') is-invalid @enderror" required onchange="syncPreview()">
              <option value="">Select</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}"
                        data-name="{{ $role->name }}"
                        data-code="{{ $role->short_code }}"
                        data-color="{{ $role->color }}"
                        {{ old('staff_role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
              @endforeach
            </select>
            @error('staff_role_id')<div class="gt-error">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="gt-label">Gender <span class="req">*</span></label>
            <select name="gender" class="gt-select @error('gender') is-invalid @enderror" required>
              <option value="">Select</option>
              <option value="male"   {{ old('gender')==='male'?'selected':'' }}>Male</option>
              <option value="female" {{ old('gender')==='female'?'selected':'' }}>Female</option>
              <option value="other"  {{ old('gender')==='other'?'selected':'' }}>Other</option>
            </select>
          </div>
          <div>
            <label class="gt-label">Joining Date <span class="req">*</span></label>
            <input type="date" name="joining_date" class="gt-input @error('joining_date') is-invalid @enderror"
                   value="{{ old('joining_date', date('Y-m-d')) }}" required>
          </div>
          <div>
            <label class="gt-label">Date of Birth <span class="opt">optional</span></label>
            <input type="date" name="dob" class="gt-input" value="{{ old('dob') }}">
          </div>
        </div>
      </div>
    </div>

    {{-- CONTACT --}}
    <div class="sf-section-head">
      <span class="sf-section-label">Contact</span>
      <span class="sf-line"></span>
    </div>
    <div class="sf-grid sf-3">
      <div>
        <label class="gt-label">Email Address <span class="req">*</span>
          <span class="opt">credentials will be sent here</span>
        </label>
        <input type="email" name="email" id="fEmail" class="gt-input @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="staff@example.com" required
               oninput="syncPreview()">
        @error('email')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="gt-label">WhatsApp <span class="opt">optional</span></label>
        <input type="tel" name="whatsapp" class="gt-input @error('whatsapp') is-invalid @enderror"
               value="{{ old('whatsapp') }}" placeholder="10-digit number" maxlength="10"
               oninput="this.value=this.value.replace(/\D/g,'')">
        @error('whatsapp')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- SALARY --}}
    <div class="sf-section-head">
      <span class="sf-section-label">Salary</span>
      <span class="sf-line"></span>
    </div>
    <div class="sf-grid sf-2">
      <div>
        <label class="gt-label">Monthly Salary (₹) <span class="req">*</span></label>
        <input type="number" name="salary" id="fSalary" class="gt-input @error('salary') is-invalid @enderror"
               value="{{ old('salary') }}" placeholder="30000" min="0" required oninput="syncPreview()">
        @error('salary')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="gt-label">Blood Group <span class="opt">optional</span></label>
        <select name="blood_group" class="gt-select">
          <option value="">Select</option>
          @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
            <option value="{{ $bg }}" {{ old('blood_group')===$bg?'selected':'' }}>{{ $bg }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- PERSONAL --}}
    <div class="sf-section-head">
      <span class="sf-section-label">Personal Details</span>
      <span class="sf-line"></span>
    </div>
    <div class="sf-grid sf-3" style="margin-bottom:14px">
      <div>
        <label class="gt-label">Father's Name <span class="opt">optional</span></label>
        <input type="text" name="father_name" class="gt-input" value="{{ old('father_name') }}" placeholder="Father's full name">
      </div>
      <div>
        <label class="gt-label">Qualification <span class="opt">optional</span></label>
        <input type="text" name="qualification" class="gt-input" value="{{ old('qualification') }}" placeholder="e.g. B.Com, MBA">
      </div>
      <div>
        <label class="gt-label">Experience (years) <span class="opt">optional</span></label>
        <input type="number" name="experience_years" class="gt-input" value="{{ old('experience_years',0) }}" min="0" max="60">
      </div>
    </div>
    <div class="sf-grid sf-4" style="margin-bottom:14px">
      <div style="grid-column:span 2">
        <label class="gt-label">Address <span class="opt">optional</span></label>
        <input type="text" name="address" class="gt-input" value="{{ old('address') }}" placeholder="House no., street, area">
      </div>
      <div>
        <label class="gt-label">State <span class="opt">optional</span></label>
        <select name="state" id="sfState" class="gt-select @error('state') is-invalid @enderror" onchange="loadDistricts()">
          <option value="">Select State</option>
          @foreach($states as $s)
            <option value="{{ $s }}" {{ old('state')===$s?'selected':'' }}>{{ $s }}</option>
          @endforeach
        </select>
        @error('state')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="gt-label">District <span class="opt">optional</span></label>
        <select name="district" id="sfDistrict" class="gt-select">
          <option value="">Select District</option>
          @if(old('district') && old('state'))
            <option value="{{ old('district') }}" selected>{{ old('district') }}</option>
          @endif
        </select>
      </div>
    </div>
    <div class="sf-grid sf-2">
      <div>
        <label class="gt-label">City <span class="opt">optional</span></label>
        <input type="text" name="city" class="gt-input" value="{{ old('city') }}" placeholder="City / Town">
      </div>
      <div>
        <label class="gt-label">PIN Code <span class="opt">optional</span></label>
        <input type="text" name="pin" class="gt-input @error('pin') is-invalid @enderror"
               value="{{ old('pin') }}" placeholder="6 digits" maxlength="6"
               oninput="this.value=this.value.replace(/\D/g,'')">
        @error('pin')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- DOCUMENTS --}}
    <div class="sf-section-head">
      <span class="sf-section-label">Identity Documents</span>
      <span class="sf-line"></span>
    </div>
    <div class="sf-grid sf-2">
      <div>
        <label class="gt-label">Aadhar Number <span class="opt">optional</span></label>
        <input type="text" name="aadhar_no" class="gt-input @error('aadhar_no') is-invalid @enderror"
               value="{{ old('aadhar_no') }}" placeholder="12-digit Aadhar" maxlength="12"
               oninput="this.value=this.value.replace(/\D/g,'')">
        @error('aadhar_no')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="gt-label">PAN Number <span class="opt">optional</span></label>
        <input type="text" name="pan_no" class="gt-input @error('pan_no') is-invalid @enderror"
               value="{{ old('pan_no') }}" placeholder="e.g. ABCDE1234F" maxlength="10"
               style="text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
        @error('pan_no')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- BANK --}}
    <div class="sf-section-head">
      <span class="sf-section-label">Bank Details</span>
      <span class="sf-line"></span>
      <span style="font-size:10px;color:var(--text-3);margin-left:8px;white-space:nowrap">for salary transfer</span>
    </div>
    <div class="sf-grid sf-3" style="margin-bottom:14px">
      <div>
        <label class="gt-label">Bank Name <span class="opt">optional</span></label>
        <input type="text" name="bank_name" class="gt-input" value="{{ old('bank_name') }}" placeholder="e.g. State Bank of India">
      </div>
      <div>
        <label class="gt-label">Account Number <span class="opt">optional</span></label>
        <input type="text" name="account_no" class="gt-input" value="{{ old('account_no') }}" placeholder="Account number">
      </div>
      <div>
        <label class="gt-label">IFSC Code <span class="opt">optional</span></label>
        <input type="text" name="ifsc" class="gt-input @error('ifsc') is-invalid @enderror"
               value="{{ old('ifsc') }}" placeholder="e.g. SBIN0001234" maxlength="11"
               style="text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
        @error('ifsc')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>
    <div style="max-width:300px">
      <label class="gt-label">Branch Name <span class="opt">optional</span></label>
      <input type="text" name="branch_name" class="gt-input" value="{{ old('branch_name') }}" placeholder="Branch name">
    </div>

    {{-- EMERGENCY --}}
    <div class="sf-section-head">
      <span class="sf-section-label">Emergency Contact</span>
      <span class="sf-line"></span>
    </div>
    <div class="sf-grid sf-3">
      <div>
        <label class="gt-label">Name <span class="opt">optional</span></label>
        <input type="text" name="emergency_name" class="gt-input" value="{{ old('emergency_name') }}" placeholder="Contact name">
      </div>
      <div>
        <label class="gt-label">Phone <span class="opt">optional</span></label>
        <input type="tel" name="emergency_phone" class="gt-input @error('emergency_phone') is-invalid @enderror"
               value="{{ old('emergency_phone') }}" placeholder="10-digit phone" maxlength="10"
               oninput="this.value=this.value.replace(/\D/g,'')">
        @error('emergency_phone')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="gt-label">Relation <span class="opt">optional</span></label>
        <input type="text" name="emergency_relation" class="gt-input" value="{{ old('emergency_relation') }}" placeholder="e.g. Father, Spouse">
      </div>
    </div>

    {{-- NOTES --}}
    <div class="sf-section-head">
      <span class="sf-section-label">Notes</span>
      <span class="sf-line"></span>
    </div>
    <textarea name="notes" class="gt-textarea" placeholder="Internal notes (not visible to staff)">{{ old('notes') }}</textarea>

    {{-- ACTIONS --}}
    <div style="display:flex;gap:10px;margin-top:20px;padding-top:18px;border-top:1px solid var(--border);">
      <button type="submit" class="btn btn-primary" id="saveBtn">Save Staff Member</button>
      <a href="{{ route('institute.staff.index') }}" class="btn btn-outline">Cancel</a>
    </div>

  </div>

  {{-- ── RIGHT: STICKY PANEL ──────────────────────────────────────────────── --}}
  <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:14px;">

    {{-- Preview card --}}
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="padding:11px 14px;border-bottom:1px solid var(--border);font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em">Preview</div>
      <div style="padding:16px 14px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
          <div id="prevAvatar" style="width:44px;height:44px;border-radius:50%;background:#6c5dd322;color:#6c5dd3;font-size:15px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;letter-spacing:.02em">?</div>
          <div>
            <div id="prevName" style="font-size:14px;font-weight:700;color:var(--text-1)">Full Name</div>
            <div id="prevRole" style="font-size:11px;margin-top:3px;">
              <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:#6c5dd318;color:#6c5dd3">Role</span>
            </div>
          </div>
        </div>
        <div style="font-size:11px;color:var(--text-3);margin-bottom:4px">Staff ID (preview)</div>
        <code id="prevId" style="font-size:11px;background:var(--bg-3);padding:5px 10px;border-radius:7px;display:block;color:var(--text-2);letter-spacing:.04em">—</code>
        <div style="margin-top:10px;font-size:11px;color:var(--text-3)">Salary</div>
        <div id="prevSalary" style="font-size:15px;font-weight:800;color:var(--text-1);margin-top:2px">—</div>
      </div>
    </div>

    {{-- Required fields checklist --}}
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="padding:11px 14px;border-bottom:1px solid var(--border);font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em">Required Fields</div>
      <div style="padding:10px 14px;">
        <div class="req-item"><span class="req-dot empty" id="dot-name"></span><span style="color:var(--text-2)">Full Name</span></div>
        <div class="req-item"><span class="req-dot empty" id="dot-mobile"></span><span style="color:var(--text-2)">Mobile Number</span></div>
        <div class="req-item"><span class="req-dot empty" id="dot-email"></span><span style="color:var(--text-2)">Email Address</span></div>
        <div class="req-item"><span class="req-dot empty" id="dot-role"></span><span style="color:var(--text-2)">Role</span></div>
        <div class="req-item"><span class="req-dot empty" id="dot-gender"></span><span style="color:var(--text-2)">Gender</span></div>
        <div class="req-item"><span class="req-dot empty" id="dot-salary"></span><span style="color:var(--text-2)">Salary</span></div>
      </div>
    </div>

    {{-- Login info --}}
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:14px;">
      <div style="font-size:11px;font-weight:700;color:var(--text-2);margin-bottom:6px">Login Credentials</div>
      <div style="font-size:12px;color:var(--text-3);line-height:1.7">
        A temporary password is auto-generated on save. You'll see it once on the next screen — save it to share with the staff member.
      </div>
      <div style="margin-top:10px;font-size:11px;color:var(--text-3)">Login URL</div>
      <code style="font-size:11px;color:var(--text-2)">{{ url('/staff/login') }}</code>
    </div>

  </div>
</div>
</form>
@endsection

@push('scripts')
<script>
const instCode = '{{ strtoupper(substr(auth()->guard("institute")->user()->institute->short_name ?? auth()->guard("institute")->user()->institute->name, 0, 3)) }}';
const districtsByState = @json($districtsByState);

function loadDistricts() {
    const state = document.getElementById('sfState').value;
    const sel   = document.getElementById('sfDistrict');
    sel.innerHTML = '<option value="">Select District</option>';
    if (state && districtsByState[state]) {
        districtsByState[state].forEach(d => {
            const o = document.createElement('option');
            o.value = d; o.textContent = d;
            sel.appendChild(o);
        });
    }
}
// Restore district on validation error
(function () {
    const savedState    = '{{ old('state') }}';
    const savedDistrict = '{{ old('district') }}';
    if (savedState) { loadDistricts(); }
    if (savedDistrict) {
        const sel = document.getElementById('sfDistrict');
        for (let o of sel.options) { if (o.value === savedDistrict) { o.selected = true; break; } }
    }
})();

function syncPreview() {
    const name   = document.getElementById('fName').value.trim();
    const mobile = document.getElementById('fMobile').value.trim();
    const salary = document.getElementById('fSalary').value;
    const roleEl = document.getElementById('fRole');
    const roleOpt = roleEl.options[roleEl.selectedIndex];

    // Avatar initials
    const words = name.split(/\s+/).filter(Boolean);
    const initials = words.length >= 2
        ? (words[0][0] + words[words.length-1][0]).toUpperCase()
        : (words[0]?.[0] ?? '?').toUpperCase();

    const roleColor = roleOpt?.dataset?.color ?? '#6c5dd3';
    const roleCode  = roleOpt?.dataset?.code  ?? 'XXX';
    const roleName  = roleOpt?.dataset?.name  ?? 'Role';

    // Avatar
    const av = document.getElementById('prevAvatar');
    av.textContent = name ? initials : '?';
    av.style.background = roleColor + '22';
    av.style.color = roleColor;

    // Name
    document.getElementById('prevName').textContent = name || 'Full Name';

    // Role badge
    const rb = document.getElementById('prevRole');
    rb.innerHTML = roleOpt?.value
        ? `<span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:${roleColor}18;color:${roleColor}">${roleName}</span>`
        : `<span style="font-size:11px;color:var(--text-3)">No role selected</span>`;

    // Staff ID preview
    const year = new Date().getFullYear();
    document.getElementById('prevId').textContent = roleOpt?.value
        ? `${instCode}/${roleCode}/${year}/XXX`
        : '—';

    // Salary
    document.getElementById('prevSalary').textContent = salary
        ? '₹' + Number(salary).toLocaleString('en-IN')
        : '—';

    // Required dots
    const email = document.getElementById('fEmail')?.value.trim() ?? '';
    dot('dot-name',   !!name);
    dot('dot-mobile', mobile.length === 10);
    dot('dot-email',  email.includes('@') && email.includes('.'));
    dot('dot-role',   !!roleOpt?.value);
    dot('dot-gender', !!document.querySelector('select[name="gender"]').value);
    dot('dot-salary', !!salary);
}

function dot(id, filled) {
    const el = document.getElementById(id);
    el.classList.toggle('done',  filled);
    el.classList.toggle('empty', !filled);
}

// Wire up events
document.getElementById('fName').addEventListener('input', syncPreview);
document.getElementById('fRole').addEventListener('change', syncPreview);
document.getElementById('fSalary').addEventListener('input', syncPreview);
document.querySelector('select[name="gender"]').addEventListener('change', syncPreview);

// Photo preview
document.getElementById('photoInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const r = new FileReader();
    r.onload = e => {
        document.getElementById('photoPreview').src = e.target.result;
        document.getElementById('photoPreview').style.display = 'block';
        document.getElementById('photoIcon').style.display    = 'none';
    };
    r.readAsDataURL(file);
});

// Double-submit guard
document.getElementById('staffForm').addEventListener('submit', function () {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true; btn.textContent = 'Saving…';
});

syncPreview();
</script>
@endpush
