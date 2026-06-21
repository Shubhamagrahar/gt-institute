@extends('layouts.institute')
@php $profile = $staff->staffProfile; $role = $profile?->staffRole; @endphp
@section('title','Permissions — '.($profile?->name ?? 'Staff'))
@section('page-title','Staff Permissions')
@section('topbar-actions')
  <a href="{{ route('institute.staff.show', $staff) }}" class="btn btn-outline btn-sm">← Profile</a>
@endsection

@push('styles')
<style>
.perm-group { margin-bottom:16px; }
.perm-group-head {
    display:flex; align-items:center; gap:10px;
    padding:10px 14px; border-radius:9px;
    background:var(--bg-3); cursor:pointer;
    user-select:none; margin-bottom:8px;
}
.perm-group-head:hover { background:var(--bg-4,var(--bg-3)); }
.perm-group-label { font-size:12px; font-weight:800; color:var(--text-1); flex:1; }
.perm-group-count { font-size:11px; color:var(--text-3); font-weight:500; }
.perm-toggle { font-size:11px; color:var(--accent); font-weight:700; cursor:pointer; }
.perm-items { display:grid; grid-template-columns:1fr 1fr; gap:6px; padding:0 4px; }
.perm-item { display:flex; align-items:center; gap:9px; padding:9px 12px; border-radius:8px; cursor:pointer; }
.perm-item:hover { background:var(--bg-3); }
.perm-item input[type=checkbox] { width:16px; height:16px; accent-color:var(--accent); cursor:pointer; flex-shrink:0; }
.perm-item-label { font-size:12px; color:var(--text-1); line-height:1.3; }
.perm-item-key { font-size:10px; color:var(--text-3); font-family:monospace; }
</style>
@endpush

@section('content')
@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:260px 1fr;gap:16px;align-items:start;max-width:900px">

  {{-- Left: Staff info card --}}
  <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
    <div style="padding:20px;text-align:center;border-bottom:1px solid var(--border);">
      @php $initials = collect(explode(' ',$profile?->name??'S'))->map(fn($w)=>strtoupper($w[0]??''))->take(2)->join(''); $rc=$role?->color??'#6c5dd3'; @endphp
      <div style="width:56px;height:56px;border-radius:50%;background:{{ $rc }}22;color:{{ $rc }};font-size:20px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 10px">{{ $initials }}</div>
      <div style="font-size:14px;font-weight:800;color:var(--text-1)">{{ $profile?->name }}</div>
      @if($role)
        <span style="font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;background:{{ $rc }}18;color:{{ $rc }};display:inline-block;margin-top:5px">{{ $role->name }}</span>
      @endif
    </div>
    <div style="padding:14px 16px;">
      {{-- Mode indicator --}}
      <div style="margin-bottom:16px;padding:10px 14px;border-radius:10px;{{ $isCustom ? 'background:#fef3c7;border:1px solid #f59e0b' : 'background:#d1fae5;border:1px solid #34d399' }}">
        <div style="font-size:11px;font-weight:800;color:{{ $isCustom ? '#92400e' : '#065f46' }};margin-bottom:2px">
          {{ $isCustom ? 'Custom Permissions' : 'Using Role Permissions' }}
        </div>
        <div style="font-size:11px;color:{{ $isCustom ? '#92400e' : '#065f46' }};opacity:.8">
          @if($isCustom)
            Role changes won't apply here
          @else
            Permissions sync with role automatically
          @endif
        </div>
      </div>

      @if($role)
      <div style="font-size:11px;color:var(--text-3);margin-bottom:6px">Role grace days</div>
      <div style="font-size:22px;font-weight:800;color:var(--text-1);margin-bottom:16px">{{ $role->grace_days }} <span style="font-size:13px;font-weight:500;color:var(--text-3)">days</span></div>
      @endif

      {{-- Sync to role button --}}
      @if($isCustom)
      <form method="POST" action="{{ route('institute.staff.permissions.save', $staff) }}" onsubmit="return confirm('This will remove all custom permissions and sync back to the role. Continue?')">
        @csrf
        <input type="hidden" name="mode" value="role">
        <button type="submit" class="btn btn-outline" style="width:100%;font-size:12px">Sync to Role Permissions</button>
      </form>
      @endif
    </div>
  </div>

  {{-- Right: Permission matrix --}}
  <form method="POST" action="{{ route('institute.staff.permissions.save', $staff) }}" id="permsForm">
    @csrf
    <input type="hidden" name="mode" value="custom">

    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div>
          <div style="font-size:13px;font-weight:800;color:var(--text-1)">Custom Permissions</div>
          <div style="font-size:11px;color:var(--text-3);margin-top:2px">Override individual access for this staff member</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
          <span id="permCount" style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:var(--bg-3);color:var(--text-2)">0 selected</span>
          <button type="button" onclick="selectAll(true)" style="font-size:11px;font-weight:700;color:var(--accent);background:none;border:none;cursor:pointer;padding:4px 8px">Select All</button>
          <button type="button" onclick="selectAll(false)" style="font-size:11px;color:var(--text-3);background:none;border:none;cursor:pointer;padding:4px 8px">Clear All</button>
        </div>
      </div>

      <div style="padding:16px 20px;">
        @foreach($allPermissions as $group => $perms)
          <div class="perm-group">
            <div class="perm-group-head" onclick="toggleGroup('{{ $group }}')">
              <span class="perm-group-label">{{ $group }}</span>
              <span class="perm-group-count" id="gc-{{ Str::slug($group) }}">0 / {{ count($perms) }}</span>
              <span class="perm-toggle">Toggle all</span>
            </div>
            <div class="perm-items" data-group="{{ $group }}">
              @foreach($perms as $key => $label)
                <label class="perm-item">
                  <input type="checkbox" name="permissions[]" value="{{ $key }}"
                         {{ in_array($key, $activePerms) ? 'checked' : '' }}
                         onchange="updateCount()">
                  <span>
                    <span class="perm-item-label">{{ $label }}</span><br>
                    <span class="perm-item-key">{{ $key }}</span>
                  </span>
                </label>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>

      <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end">
        <a href="{{ route('institute.staff.show', $staff) }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Custom Permissions</button>
      </div>
    </div>
  </form>
</div>

<script>
function updateCount() {
    const all   = document.querySelectorAll('.perm-items input[type=checkbox]');
    const total = [...all].filter(c => c.checked).length;
    document.getElementById('permCount').textContent = total + ' selected';

    // Per-group count
    document.querySelectorAll('.perm-items').forEach(group => {
        const g    = group.dataset.group;
        const gAll = group.querySelectorAll('input');
        const gChk = [...gAll].filter(c => c.checked).length;
        const el   = document.getElementById('gc-' + '{{ Str::slug("") }}'.replace('',g.toLowerCase().replace(/[^a-z0-9]+/g,'-')));
        const key  = 'gc-' + g.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const gcEl = document.getElementById(key);
        if (gcEl) gcEl.textContent = gChk + ' / ' + gAll.length;
    });
}
function toggleGroup(g) {
    const items = document.querySelector(`.perm-items[data-group="${g}"]`);
    if (!items) return;
    const cbs    = items.querySelectorAll('input');
    const allChk = [...cbs].every(c => c.checked);
    cbs.forEach(c => c.checked = !allChk);
    updateCount();
}
function selectAll(val) {
    document.querySelectorAll('.perm-items input').forEach(c => c.checked = val);
    updateCount();
}

// Init group counts properly
document.querySelectorAll('.perm-items').forEach(group => {
    const g    = group.dataset.group;
    const gAll = group.querySelectorAll('input');
    const gChk = [...gAll].filter(c => c.checked).length;
    const key  = 'gc-' + g.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    const el   = document.getElementById(key);
    if (el) el.textContent = gChk + ' / ' + gAll.length;
});
updateCount();
</script>
@endsection
