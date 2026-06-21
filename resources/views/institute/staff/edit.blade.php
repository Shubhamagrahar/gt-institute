@extends('layouts.institute')
@php $profile = $staff->staffProfile; @endphp
@section('title','Edit — '.($profile?->name ?? 'Staff'))
@section('page-title','Edit Staff Member')
@section('topbar-actions')
  <a href="{{ route('institute.staff.show', $staff) }}" class="btn btn-outline btn-sm">← Profile</a>
@endsection

@push('styles')
<style>
.sf-section-head { display:flex;align-items:center;gap:10px;padding:14px 0 10px;margin-top:24px; }
.sf-section-head:first-of-type { margin-top:0; }
.sf-section-label { font-size:11px;font-weight:800;color:var(--text-3);text-transform:uppercase;letter-spacing:.08em;white-space:nowrap; }
.sf-section-line { flex:1;height:1px;background:var(--border); }
.sf-grid { display:grid;gap:14px; }
.sf-grid-2 { grid-template-columns:1fr 1fr; }
.sf-grid-3 { grid-template-columns:1fr 1fr 1fr; }
.sf-grid-4 { grid-template-columns:1fr 1fr 1fr 1fr; }
.gt-label { font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:6px;display:block; }
.gt-input,.gt-select,.gt-textarea { width:100%;height:42px;border:1.5px solid var(--border);border-radius:9px;padding:0 14px;font-size:13px;background:var(--bg);color:var(--text-1);outline:none;transition:border-color .15s,box-shadow .15s;box-sizing:border-box; }
.gt-input:focus,.gt-select:focus,.gt-textarea:focus { border-color:var(--accent);box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 12%,transparent); }
.gt-textarea { height:80px;padding:10px 14px;resize:vertical; }
.gt-input.is-invalid,.gt-select.is-invalid { border-color:var(--danger,#ef4444); }
.gt-error { font-size:11px;color:var(--danger,#ef4444);margin-top:4px; }
.req { color:var(--danger,#ef4444); }
.optional-tag { font-size:10px;color:var(--text-3);font-weight:500;margin-left:4px; }
.sf-save-bar { position:sticky;bottom:0;left:0;right:0;background:var(--bg-2);border-top:1px solid var(--border);padding:14px 24px;display:flex;gap:10px;align-items:center;justify-content:flex-end;margin:32px -24px -24px;z-index:10; }
@media(max-width:680px){.sf-grid-2,.sf-grid-3,.sf-grid-4{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.sf-grid-2,.sf-grid-3,.sf-grid-4{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@if($errors->any())
  <div class="alert alert-danger" style="margin-bottom:20px">Please fix the errors below before saving.</div>
@endif

<form method="POST" action="{{ route('institute.staff.update', $staff) }}" id="staffForm">
@csrf @method('PUT')
<div style="max-width:860px">
<div style="background:var(--bg-2);border:1px solid var(--border);border-radius:16px;padding:24px;">

  {{-- Status banner for inactive staff --}}
  @if($staff->status === 'inactive')
  <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:10px;padding:10px 16px;margin-bottom:20px;font-size:13px;color:#92400e;display:flex;align-items:center;gap:8px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    This staff member is currently <strong>Inactive</strong>. Change status to Active to allow login.
  </div>
  @endif

  {{-- ── BASIC INFO ─────────────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Basic Information</span>
    <span class="sf-section-line"></span>
  </div>
  <div class="sf-grid sf-grid-2" style="margin-bottom:14px">
    <div>
      <label class="gt-label">Full Name <span class="req">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
             value="{{ old('name', $profile?->name) }}" required>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="gt-label">Mobile <span style="font-size:11px;color:var(--text-3)">(cannot change)</span></label>
      <input type="tel" value="{{ $staff->mobile }}" class="gt-input" disabled style="opacity:.6;cursor:not-allowed">
    </div>
  </div>
  <div class="sf-grid sf-grid-4">
    <div>
      <label class="gt-label">Role <span class="req">*</span></label>
      <select name="staff_role_id" class="gt-select @error('staff_role_id') is-invalid @enderror" required>
        @foreach($roles as $r)
          <option value="{{ $r->id }}" {{ old('staff_role_id', $profile?->staff_role_id) == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="gt-label">Gender <span class="req">*</span></label>
      <select name="gender" class="gt-select" required>
        @foreach(['male','female','other'] as $g)
          <option value="{{ $g }}" {{ old('gender',$profile?->gender)===$g?'selected':'' }}>{{ ucfirst($g) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="gt-label">Joining Date <span class="req">*</span></label>
      <input type="date" name="joining_date" class="gt-input" value="{{ old('joining_date', $profile?->joining_date?->format('Y-m-d')) }}" required>
    </div>
    <div>
      <label class="gt-label">Date of Birth</label>
      <input type="date" name="dob" class="gt-input" value="{{ old('dob', $profile?->dob?->format('Y-m-d')) }}">
    </div>
  </div>

  {{-- ── STATUS ───────────────────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Account Status</span>
    <span class="sf-section-line"></span>
  </div>
  <div style="max-width:200px">
    <label class="gt-label">Status <span class="req">*</span></label>
    <select name="status" class="gt-select">
      <option value="active"   {{ old('status',$staff->status)==='active'?'selected':'' }}>Active</option>
      <option value="inactive" {{ old('status',$staff->status)==='inactive'?'selected':'' }}>Inactive</option>
    </select>
  </div>

  {{-- ── CONTACT ──────────────────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Contact</span>
    <span class="sf-section-line"></span>
  </div>
  <div class="sf-grid sf-grid-3">
    <div>
      <label class="gt-label">Email <span class="optional-tag">optional</span></label>
      <input type="email" name="email" class="gt-input @error('email') is-invalid @enderror"
             value="{{ old('email', $staff->email) }}">
      @error('email')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="gt-label">WhatsApp <span class="optional-tag">optional</span></label>
      <input type="tel" name="whatsapp" class="gt-input @error('whatsapp') is-invalid @enderror"
             value="{{ old('whatsapp', $profile?->whatsapp) }}" maxlength="10"
             oninput="this.value=this.value.replace(/\D/g,'')">
      @error('whatsapp')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="gt-label">Department <span class="optional-tag">optional</span></label>
      <input type="text" name="department" class="gt-input" value="{{ old('department',$profile?->department) }}">
    </div>
  </div>

  {{-- ── SALARY ───────────────────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Salary</span>
    <span class="sf-section-line"></span>
  </div>
  <div class="sf-grid sf-grid-3">
    <div>
      <label class="gt-label">Monthly Salary (₹) <span class="req">*</span></label>
      <input type="number" name="salary" class="gt-input @error('salary') is-invalid @enderror"
             value="{{ old('salary', $profile?->salary) }}" min="0" required>
      @error('salary')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="gt-label">Salary Type <span class="req">*</span></label>
      <select name="salary_type" class="gt-select" required>
        @foreach(['monthly','daily','hourly'] as $t)
          <option value="{{ $t }}" {{ old('salary_type',$profile?->salary_type)===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="gt-label">Blood Group</label>
      <select name="blood_group" class="gt-select">
        <option value="">Select</option>
        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
          <option value="{{ $bg }}" {{ old('blood_group',$profile?->blood_group)===$bg?'selected':'' }}>{{ $bg }}</option>
        @endforeach
      </select>
    </div>
  </div>

  {{-- ── PERSONAL DETAILS ─────────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Personal Details</span>
    <span class="sf-section-line"></span>
  </div>
  <div class="sf-grid sf-grid-3" style="margin-bottom:14px">
    <div>
      <label class="gt-label">Father's Name</label>
      <input type="text" name="father_name" class="gt-input" value="{{ old('father_name',$profile?->father_name) }}">
    </div>
    <div>
      <label class="gt-label">Qualification</label>
      <input type="text" name="qualification" class="gt-input" value="{{ old('qualification',$profile?->qualification) }}">
    </div>
    <div>
      <label class="gt-label">Experience (years)</label>
      <input type="number" name="experience_years" class="gt-input" value="{{ old('experience_years',$profile?->experience_years) }}" min="0" max="60">
    </div>
  </div>
  <div class="sf-grid sf-grid-4">
    <div style="grid-column:span 2">
      <label class="gt-label">Address</label>
      <input type="text" name="address" class="gt-input" value="{{ old('address',$profile?->address) }}">
    </div>
    <div>
      <label class="gt-label">City</label>
      <input type="text" name="city" class="gt-input" value="{{ old('city',$profile?->city) }}">
    </div>
    <div>
      <label class="gt-label">State</label>
      <input type="text" name="state" class="gt-input" value="{{ old('state',$profile?->state) }}">
    </div>
  </div>
  <div style="margin-top:14px;max-width:140px">
    <label class="gt-label">PIN Code</label>
    <input type="text" name="pin" class="gt-input @error('pin') is-invalid @enderror"
           value="{{ old('pin',$profile?->pin) }}" maxlength="6" oninput="this.value=this.value.replace(/\D/g,'')">
    @error('pin')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  {{-- ── IDENTITY DOCUMENTS ──────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Identity Documents</span>
    <span class="sf-section-line"></span>
  </div>
  <div class="sf-grid sf-grid-2">
    <div>
      <label class="gt-label">Aadhar Number</label>
      <input type="text" name="aadhar_no" class="gt-input @error('aadhar_no') is-invalid @enderror"
             value="{{ old('aadhar_no',$profile?->aadhar_no) }}" maxlength="12"
             oninput="this.value=this.value.replace(/\D/g,'')">
      @error('aadhar_no')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="gt-label">PAN Number</label>
      <input type="text" name="pan_no" class="gt-input @error('pan_no') is-invalid @enderror"
             value="{{ old('pan_no',$profile?->pan_no) }}" maxlength="10"
             style="text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
      @error('pan_no')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
  </div>

  {{-- ── BANK DETAILS ─────────────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Bank Details</span>
    <span class="sf-section-line"></span>
  </div>
  <div class="sf-grid sf-grid-3" style="margin-bottom:14px">
    <div>
      <label class="gt-label">Bank Name</label>
      <input type="text" name="bank_name" class="gt-input" value="{{ old('bank_name',$profile?->bank_name) }}">
    </div>
    <div>
      <label class="gt-label">Account Number</label>
      <input type="text" name="account_no" class="gt-input" value="{{ old('account_no',$profile?->account_no) }}">
    </div>
    <div>
      <label class="gt-label">IFSC Code</label>
      <input type="text" name="ifsc" class="gt-input @error('ifsc') is-invalid @enderror"
             value="{{ old('ifsc',$profile?->ifsc) }}" maxlength="11"
             style="text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
      @error('ifsc')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
  </div>
  <div style="max-width:300px">
    <label class="gt-label">Branch Name</label>
    <input type="text" name="branch_name" class="gt-input" value="{{ old('branch_name',$profile?->branch_name) }}">
  </div>

  {{-- ── EMERGENCY CONTACT ─────────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Emergency Contact</span>
    <span class="sf-section-line"></span>
  </div>
  <div class="sf-grid sf-grid-3">
    <div>
      <label class="gt-label">Contact Name</label>
      <input type="text" name="emergency_name" class="gt-input" value="{{ old('emergency_name',$profile?->emergency_name) }}">
    </div>
    <div>
      <label class="gt-label">Contact Phone</label>
      <input type="tel" name="emergency_phone" class="gt-input @error('emergency_phone') is-invalid @enderror"
             value="{{ old('emergency_phone',$profile?->emergency_phone) }}" maxlength="10"
             oninput="this.value=this.value.replace(/\D/g,'')">
      @error('emergency_phone')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="gt-label">Relation</label>
      <input type="text" name="emergency_relation" class="gt-input" value="{{ old('emergency_relation',$profile?->emergency_relation) }}">
    </div>
  </div>

  {{-- ── NOTES ────────────────────────────────────────────────────────────── --}}
  <div class="sf-section-head">
    <span class="sf-section-label">Notes</span>
    <span class="sf-section-line"></span>
  </div>
  <textarea name="notes" class="gt-textarea">{{ old('notes',$profile?->notes) }}</textarea>

  {{-- ── SAVE BAR ──────────────────────────────────────────────────────────── --}}
  <div class="sf-save-bar">
    <a href="{{ route('institute.staff.show', $staff) }}" class="btn btn-outline">Cancel</a>
    <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
  </div>

</div>
</div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('staffForm').addEventListener('submit', function () {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true; btn.textContent = 'Saving…';
});
</script>
@endpush
