@extends('layouts.institute')
@section('title','Edit — '.$staffRole->name)
@section('page-title','Edit Role')
@section('topbar-actions')
  <a href="{{ route('institute.staff-roles.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div style="max-width:560px">

  @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:16px">Please fix the errors below.</div>
  @endif

  <form method="POST" action="{{ route('institute.staff-roles.update', $staffRole) }}">
    @csrf @method('PUT')

    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:24px;display:flex;flex-direction:column;gap:16px;">

      <div style="display:grid;grid-template-columns:1fr 110px;gap:14px;">
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">
            Role Name <span style="color:var(--danger)">*</span>
          </label>
          <input type="text" name="name"
                 class="gt-input @error('name') is-invalid @enderror"
                 value="{{ old('name', $staffRole->name) }}"
                 required>
          @error('name')<div style="font-size:11px;color:var(--danger);margin-top:4px">{{ $message }}</div>@enderror
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">
            Short Code <span style="color:var(--danger)">*</span>
          </label>
          <input type="text" name="short_code"
                 class="gt-input @error('short_code') is-invalid @enderror"
                 value="{{ old('short_code', $staffRole->short_code) }}"
                 maxlength="5"
                 style="text-transform:uppercase;letter-spacing:.12em;font-weight:700;font-size:14px"
                 required>
          @error('short_code')<div style="font-size:11px;color:var(--danger);margin-top:4px">{{ $message }}</div>@enderror
        </div>
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">Description <span style="font-size:10px;font-weight:400;color:var(--text-3)">optional</span></label>
        <input type="text" name="description" class="gt-input"
               value="{{ old('description', $staffRole->description) }}">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">Grace Days</label>
          <input type="number" name="grace_days"
                 class="gt-input @error('grace_days') is-invalid @enderror"
                 value="{{ old('grace_days', $staffRole->grace_days) }}" min="0" max="31">
          @error('grace_days')<div style="font-size:11px;color:var(--danger);margin-top:4px">{{ $message }}</div>@enderror
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">Badge Color</label>
          <div style="display:flex;align-items:center;gap:10px;">
            <input type="color" name="color" id="colorPick" value="{{ old('color', $staffRole->color) }}"
                   style="width:42px;height:42px;border-radius:9px;border:1.5px solid var(--border);cursor:pointer;padding:2px;">
            <span id="colorHex" style="font-size:12px;font-family:monospace;color:var(--text-2)">{{ $staffRole->color }}</span>
          </div>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">Status</label>
          <select name="status" class="gt-select">
            <option value="active"   {{ old('status', $staffRole->status) === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $staffRole->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
      </div>

      @if($staffRole->staff_count > 0)
      <div style="padding:10px 14px;background:#fef3c7;border-radius:9px;font-size:12px;color:#92400e">
        {{ $staffRole->staff_count }} staff member{{ $staffRole->staff_count !== 1 ? 's' : '' }} use this role.
        Status change to Inactive will prevent them from logging in.
      </div>
      @endif

    </div>

    <div style="display:flex;gap:10px;margin-top:16px;">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="{{ route('institute.staff-roles.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<script>
const pick = document.getElementById('colorPick');
const hex  = document.getElementById('colorHex');
pick.addEventListener('input', () => hex.textContent = pick.value);
</script>
@endsection
