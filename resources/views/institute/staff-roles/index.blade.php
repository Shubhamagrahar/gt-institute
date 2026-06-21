@extends('layouts.institute')
@section('title','Staff Roles')
@section('page-title','Staff Roles')
@section('topbar-actions')
  <a href="{{ route('institute.staff-roles.create') }}" class="btn btn-primary btn-sm">+ Create Role</a>
@endsection

@section('content')

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger" style="margin-bottom:16px">{{ session('error') }}</div>
@endif

@if($roles->isEmpty())
  <div style="text-align:center;padding:64px 24px;background:var(--bg-2);border:1px solid var(--border);border-radius:14px;">
    <div style="width:60px;height:60px;border-radius:50%;background:var(--bg-3);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <svg width="26" height="26" fill="none" stroke="var(--text-3)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    </div>
    <div style="font-size:15px;font-weight:700;color:var(--text-2)">No roles created yet</div>
    <div style="font-size:13px;color:var(--text-3);margin-top:4px">Create roles like Manager, Accountant, or Teacher first</div>
    <a href="{{ route('institute.staff-roles.create') }}" class="btn btn-primary btn-sm" style="margin-top:16px">+ Create First Role</a>
  </div>
@else
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;">
    @foreach($roles as $role)
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:13px;overflow:hidden;transition:box-shadow .15s;" onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,.07)'" onmouseleave="this.style.boxShadow=''">

      {{-- Color strip + header --}}
      <div style="padding:16px 18px;display:flex;align-items:center;gap:12px;border-bottom:1px solid var(--border);">
        <div style="width:44px;height:44px;border-radius:11px;background:{{ $role->color }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="19" height="19" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:14px;font-weight:800;color:var(--text-1)">{{ $role->name }}</span>
            <code style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:5px;background:{{ $role->color }}18;color:{{ $role->color }};letter-spacing:.06em">{{ $role->short_code }}</code>
          </div>
          @if($role->description)
            <div style="font-size:12px;color:var(--text-3);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $role->description }}</div>
          @endif
        </div>
        <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap;{{ $role->status==='active' ? 'background:#d1fae5;color:#065f46' : 'background:var(--bg-3);color:var(--text-3)' }}">
          {{ ucfirst($role->status) }}
        </span>
      </div>

      {{-- Stats row --}}
      <div style="padding:12px 18px;display:flex;gap:20px;border-bottom:1px solid var(--border);">
        <div>
          <div style="font-size:10px;color:var(--text-3);margin-bottom:2px">Staff</div>
          <div style="font-size:16px;font-weight:800;color:var(--text-1)">{{ $role->staff_count }}</div>
        </div>
        <div>
          <div style="font-size:10px;color:var(--text-3);margin-bottom:2px">Grace Days</div>
          <div style="font-size:16px;font-weight:800;color:var(--text-1)">{{ $role->grace_days }}</div>
        </div>
        <div>
          <div style="font-size:10px;color:var(--text-3);margin-bottom:2px">Permissions</div>
          <div style="font-size:13px;font-weight:600;color:var(--text-3)">{{ count($role->permissions ?? []) > 0 ? count($role->permissions).' set' : 'Not configured' }}</div>
        </div>
      </div>

      {{-- Actions --}}
      <div style="padding:10px 18px;display:flex;align-items:center;justify-content:flex-end;gap:8px;">
        <a href="{{ route('institute.staff-roles.edit', $role) }}" class="btn btn-outline btn-xs">Edit</a>
        @if($role->staff_count === 0)
          <form method="POST" action="{{ route('institute.staff-roles.destroy', $role) }}"
                onsubmit="return confirm('Delete role {{ $role->name }}?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline btn-xs" style="color:#ef4444;border-color:#fca5a5">Delete</button>
          </form>
        @endif
      </div>
    </div>
    @endforeach
  </div>
@endif

@endsection
