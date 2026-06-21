@extends('layouts.institute')
@section('title','Create Staff Role')
@section('page-title','Create Staff Role')
@section('topbar-actions')
  <a href="{{ route('institute.staff-roles.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')

@if($errors->any())
  <div class="alert alert-danger" style="margin-bottom:16px">Please fix the errors below.</div>
@endif

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

  {{-- LEFT: Form --}}
  <form method="POST" action="{{ route('institute.staff-roles.store') }}">
    @csrf
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:24px;display:flex;flex-direction:column;gap:18px;">

      {{-- Name + Code --}}
      <div style="display:grid;grid-template-columns:1fr 130px;gap:14px;">
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">
            Role Name <span style="color:var(--danger)">*</span>
          </label>
          <input type="text" name="name" id="roleName"
                 class="gt-input @error('name') is-invalid @enderror"
                 value="{{ old('name') }}"
                 placeholder="e.g. Accountant, Manager, Teacher"
                 required autofocus>
          @error('name')<div style="font-size:11px;color:var(--danger);margin-top:4px">{{ $message }}</div>@enderror
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">
            Short Code <span style="color:var(--danger)">*</span>
          </label>
          <input type="text" name="short_code" id="roleCode"
                 class="gt-input @error('short_code') is-invalid @enderror"
                 value="{{ old('short_code') }}"
                 placeholder="e.g. ACC"
                 maxlength="5"
                 style="text-transform:uppercase;letter-spacing:.12em;font-weight:700;font-size:14px"
                 required>
          @error('short_code')<div style="font-size:11px;color:var(--danger);margin-top:4px">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Description --}}
      <div>
        <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">
          Description <span style="font-size:10px;font-weight:400;color:var(--text-3)">optional</span>
        </label>
        <input type="text" name="description" class="gt-input"
               value="{{ old('description') }}"
               placeholder="Brief description of what this role does">
      </div>

      {{-- Grace Days + Color --}}
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">
            Grace Days
            <span style="font-size:10px;font-weight:400;color:var(--text-3)">for salary deduction</span>
          </label>
          <input type="number" name="grace_days"
                 class="gt-input @error('grace_days') is-invalid @enderror"
                 value="{{ old('grace_days', 2) }}" min="0" max="31"
                 placeholder="2">
          @error('grace_days')<div style="font-size:11px;color:var(--danger);margin-top:4px">{{ $message }}</div>@enderror
          <div style="font-size:11px;color:var(--text-3);margin-top:5px">Absences allowed before salary cut</div>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">Badge Color</label>
          <div style="display:flex;align-items:center;gap:12px;height:42px;">
            <input type="color" name="color" id="colorPick" value="{{ old('color','#6c5dd3') }}"
                   style="width:42px;height:42px;border-radius:9px;border:1.5px solid var(--border);cursor:pointer;padding:2px;flex-shrink:0">
            <div>
              <div id="colorHex" style="font-size:13px;font-family:monospace;font-weight:600;color:var(--text-1)">#6c5dd3</div>
              <div style="font-size:11px;color:var(--text-3)">Used for role badge</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Preview --}}
      <div style="border-top:1px solid var(--border);padding-top:16px;">
        <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px">Preview</div>
        <div style="display:flex;align-items:center;gap:10px;">
          <div id="prevIcon" style="width:40px;height:40px;border-radius:10px;background:#6c5dd3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div>
            <div style="display:flex;align-items:center;gap:8px;">
              <span id="prevName" style="font-size:14px;font-weight:700;color:var(--text-1)">Role Name</span>
              <code id="prevCode" style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:5px;background:#6c5dd322;color:#6c5dd3;letter-spacing:.06em">CODE</code>
            </div>
            <div style="font-size:12px;color:var(--text-3);margin-top:2px">
              Grace <span id="prevGrace">2</span> days · Active
            </div>
          </div>
        </div>
      </div>

    </div>

    <div style="display:flex;gap:10px;margin-top:16px;">
      <button type="submit" class="btn btn-primary">Create Role</button>
      <a href="{{ route('institute.staff-roles.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>

  {{-- RIGHT: Info panel --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-size:12px;font-weight:800;color:var(--text-2)">What is a Role?</div>
      <div style="padding:14px 16px;font-size:12px;color:var(--text-2);line-height:1.7">
        A role groups staff by their job function. Each role will later have permissions assigned to control which parts of the system that staff can access.
      </div>
    </div>

    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-size:12px;font-weight:800;color:var(--text-2)">Short Code</div>
      <div style="padding:14px 16px;font-size:12px;color:var(--text-2);line-height:1.7">
        Used in the Staff ID. For example if your institute is <strong>AI</strong> and role code is <strong>ACC</strong>, the staff ID will be:
        <code style="display:block;margin:8px 0;padding:7px 10px;background:var(--bg-3);border-radius:7px;font-size:12px;letter-spacing:.04em">AI/ACC/2026/001</code>
        Keep it 2–4 uppercase letters.
      </div>
    </div>

    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-size:12px;font-weight:800;color:var(--text-2)">Common Roles</div>
      <div style="padding:10px 16px;display:flex;flex-direction:column;gap:1px;">
        @foreach([['Manager','MAN'],['Accountant','ACC'],['Teacher','TCH'],['Receptionist','REC'],['Computer Operator','OPR']] as [$n,$c])
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
          <span style="font-size:12px;color:var(--text-1);font-weight:600">{{ $n }}</span>
          <code style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:5px;background:var(--bg-3);color:var(--text-2)">{{ $c }}</code>
        </div>
        @endforeach
        <div style="padding:8px 0;font-size:11px;color:var(--text-3)">These are just suggestions — use any name.</div>
      </div>
    </div>

  </div>
</div>

<script>
const roleName = document.getElementById('roleName');
const roleCode = document.getElementById('roleCode');
const colorPick = document.getElementById('colorPick');
const colorHex  = document.getElementById('colorHex');
const prevName  = document.getElementById('prevName');
const prevCode  = document.getElementById('prevCode');
const prevIcon  = document.getElementById('prevIcon');
const prevGrace = document.getElementById('prevGrace');
const graceIn   = document.querySelector('input[name="grace_days"]');

function updatePreview() {
    const n = roleName.value.trim() || 'Role Name';
    const c = roleCode.value.trim() || 'CODE';
    const col = colorPick.value;
    prevName.textContent = n;
    prevCode.textContent = c;
    prevCode.style.background = col + '22';
    prevCode.style.color = col;
    prevIcon.style.background = col;
    prevGrace.textContent = graceIn.value || '2';
}

// Auto-suggest short code
roleName.addEventListener('input', function () {
    if (roleCode.dataset.manual === '1') { updatePreview(); return; }
    const words = this.value.trim().split(/\s+/).filter(Boolean);
    let s = '';
    if (words.length >= 2) s = (words[0][0] + words[1][0] + (words[2]?.[0] ?? words[1][1] ?? '')).toUpperCase();
    else if (words.length === 1) s = words[0].substring(0, 3).toUpperCase();
    roleCode.value = s;
    updatePreview();
});

roleCode.addEventListener('input', function () {
    this.dataset.manual = '1';
    this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '');
    updatePreview();
});

colorPick.addEventListener('input', function () {
    colorHex.textContent = this.value;
    updatePreview();
});

graceIn.addEventListener('input', updatePreview);
updatePreview();
</script>
@endsection
