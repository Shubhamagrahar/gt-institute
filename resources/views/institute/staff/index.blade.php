@extends('layouts.institute')
@section('title','Staff Members')
@section('page-title','Staff Members')
@section('topbar-actions')
  <a href="{{ route('institute.staff.create') }}" class="btn btn-primary btn-sm">+ Add Staff</a>
@endsection

@section('content')

{{-- Credential flash (shown once after adding staff) --}}
@if(session('staff_created'))
  @php $cred = session('staff_created'); @endphp
  <div style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);border:1.5px solid #34d399;border-radius:14px;padding:18px 22px;margin-bottom:20px;display:flex;gap:20px;align-items:center;flex-wrap:wrap;">
    <div style="flex:1;min-width:200px;">
      <div style="font-size:13px;font-weight:800;color:#065f46;margin-bottom:6px;">
        Staff Added
        @if($cred['email_sent'] ?? false)
          <span style="font-size:11px;font-weight:600;background:#059669;color:#fff;padding:2px 9px;border-radius:20px;margin-left:6px">Credentials emailed to {{ $cred['email'] }}</span>
        @else
          <span style="font-size:11px;font-weight:600;background:#f59e0b;color:#fff;padding:2px 9px;border-radius:20px;margin-left:6px">Email failed — share manually</span>
        @endif
      </div>
      <div style="display:flex;gap:24px;flex-wrap:wrap;margin-top:8px;">
        <div><span style="font-size:11px;color:#6b7280;display:block">Staff ID</span><span style="font-size:13px;font-weight:700;color:#065f46;font-family:monospace">{{ $cred['staff_id'] }}</span></div>
        <div><span style="font-size:11px;color:#6b7280;display:block">Mobile</span><span style="font-size:13px;font-weight:700;color:#065f46;font-family:monospace">{{ $cred['mobile'] }}</span></div>
        <div><span style="font-size:11px;color:#6b7280;display:block">Password</span><span style="font-size:13px;font-weight:700;color:#065f46;font-family:monospace">{{ $cred['password'] }}</span></div>
      </div>
    </div>
    <div style="font-size:11px;color:#065f46;background:rgba(255,255,255,.5);border-radius:8px;padding:8px 12px;line-height:1.5;">
      Save this now.<br>Not shown again.
    </div>
  </div>
@endif

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger" style="margin-bottom:16px">{{ session('error') }}</div>
@endif

{{-- Filters --}}
<div style="background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:14px 18px;margin-bottom:16px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
  <input type="text" id="searchInput" placeholder="Search name, ID, mobile…"
    style="flex:1;min-width:180px;height:38px;border:1.5px solid var(--border);border-radius:9px;padding:0 14px;font-size:13px;background:var(--bg);color:var(--text-1);outline:none;">
  <select id="roleFilter" style="height:38px;border:1.5px solid var(--border);border-radius:9px;padding:0 12px;font-size:13px;background:var(--bg);color:var(--text-1);cursor:pointer;">
    <option value="">All Roles</option>
    @foreach($roles as $role)
      <option value="{{ $role->id }}">{{ $role->name }}</option>
    @endforeach
  </select>
  <select id="statusFilter" style="height:38px;border:1.5px solid var(--border);border-radius:9px;padding:0 12px;font-size:13px;background:var(--bg);color:var(--text-1);cursor:pointer;">
    <option value="">All Status</option>
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
  </select>
  <span id="filterCount" style="font-size:12px;color:var(--text-3);white-space:nowrap"></span>
</div>

{{-- Table --}}
<div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
  @if($staff->total() === 0)
    <div style="text-align:center;padding:64px 24px;">
      <div style="width:64px;height:64px;border-radius:50%;background:var(--bg-3);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="28" height="28" fill="none" stroke="var(--text-3)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div style="font-size:15px;font-weight:700;color:var(--text-2)">No staff members yet</div>
      <div style="font-size:13px;color:var(--text-3);margin-top:4px">Add your first staff member to get started</div>
      <a href="{{ route('institute.staff.create') }}" class="btn btn-primary btn-sm" style="margin-top:16px">+ Add Staff Member</a>
    </div>
  @else
    <table id="staffTable" style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="border-bottom:1.5px solid var(--border);">
          <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Staff</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Staff ID</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Role</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Mobile</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Joined</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Salary</th>
          <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Status</th>
          <th style="padding:12px 20px;text-align:right;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($staff as $s)
          @php
            $profile  = $s->staffProfile;
            $role     = $profile?->staffRole;
            $initials = collect(explode(' ', $profile?->name ?? 'S'))
                          ->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
            $roleColor = $role?->color ?? '#6c5dd3';
          @endphp
          <tr class="staff-row"
              data-name="{{ strtolower($profile?->name ?? '') }}"
              data-id="{{ strtolower($s->user_id ?? '') }}"
              data-mobile="{{ $s->mobile }}"
              data-role="{{ $role?->id }}"
              data-status="{{ $s->status }}"
              style="border-bottom:1px solid var(--border);transition:background .12s;">
            <td style="padding:13px 20px;">
              <div style="display:flex;align-items:center;gap:11px;">
                <div style="width:38px;height:38px;border-radius:50%;background:{{ $roleColor }}22;color:{{ $roleColor }};font-size:13px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;letter-spacing:.02em">{{ $initials }}</div>
                <div>
                  <div style="font-size:13px;font-weight:700;color:var(--text-1)">{{ $profile?->name ?? '—' }}</div>
                  <div style="font-size:11px;color:var(--text-3);margin-top:1px">{{ $profile?->designation ?? '' }}</div>
                </div>
              </div>
            </td>
            <td style="padding:13px 16px;">
              <code style="font-size:11px;background:var(--bg-3);padding:3px 8px;border-radius:6px;color:var(--text-2);letter-spacing:.04em">{{ $s->user_id ?? '—' }}</code>
            </td>
            <td style="padding:13px 16px;">
              @if($role)
                <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $roleColor }}18;color:{{ $roleColor }}">{{ $role->name }}</span>
              @else
                <span style="color:var(--text-3)">—</span>
              @endif
            </td>
            <td style="padding:13px 16px;font-size:13px;color:var(--text-2);font-variant-numeric:tabular-nums">{{ $s->mobile }}</td>
            <td style="padding:13px 16px;font-size:12px;color:var(--text-3)">{{ $profile?->joining_date?->format('d M Y') ?? '—' }}</td>
            <td style="padding:13px 16px;">
              <span style="font-size:13px;font-weight:600;color:var(--text-1)">₹{{ number_format($profile?->salary ?? 0) }}</span>
              <span style="font-size:11px;color:var(--text-3)">/{{ $profile?->salary_type ?? 'mo' }}</span>
            </td>
            <td style="padding:13px 16px;text-align:center;">
              <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;{{ $s->status==='active' ? 'background:#d1fae5;color:#065f46' : 'background:#fee2e2;color:#991b1b' }}">
                {{ ucfirst($s->status) }}
              </span>
            </td>
            <td style="padding:13px 20px;text-align:right;">
              <div style="display:flex;gap:5px;justify-content:flex-end;">
                <a href="{{ route('institute.staff.show', $s) }}" title="View Profile"
                   style="width:31px;height:31px;border-radius:8px;background:var(--bg-3);color:var(--text-2);display:flex;align-items:center;justify-content:center;text-decoration:none;">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>
                <a href="{{ route('institute.staff.edit', $s) }}" title="Edit"
                   style="width:31px;height:31px;border-radius:8px;background:var(--bg-3);color:var(--text-2);display:flex;align-items:center;justify-content:center;text-decoration:none;">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <a href="{{ route('institute.staff.salary', $s) }}" title="Salary"
                   style="width:31px;height:31px;border-radius:8px;background:var(--bg-3);color:var(--text-2);display:flex;align-items:center;justify-content:center;text-decoration:none;">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </a>
                <a href="{{ route('institute.staff.permissions', $s) }}" title="Permissions"
                   style="width:31px;height:31px;border-radius:8px;background:var(--bg-3);color:var(--text-2);display:flex;align-items:center;justify-content:center;text-decoration:none;">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </a>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @if($staff->hasPages())
      <div style="padding:14px 20px;border-top:1px solid var(--border)">{{ $staff->links() }}</div>
    @endif
  @endif
</div>

<script>
const search  = document.getElementById('searchInput');
const roleF   = document.getElementById('roleFilter');
const statusF = document.getElementById('statusFilter');
const rows    = document.querySelectorAll('.staff-row');
const cnt     = document.getElementById('filterCount');

function applyFilters() {
    const q  = search.value.toLowerCase();
    const r  = roleF.value;
    const st = statusF.value;
    let vis  = 0;
    rows.forEach(row => {
        const ok = (!q  || row.dataset.name.includes(q) || row.dataset.id.includes(q) || row.dataset.mobile.includes(q))
                && (!r  || row.dataset.role   === r)
                && (!st || row.dataset.status === st);
        row.style.display = ok ? '' : 'none';
        if (ok) vis++;
    });
    cnt.textContent = vis < rows.length ? `Showing ${vis} of ${rows.length}` : '';
}

[search, roleF, statusF].forEach(el => el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', applyFilters));

rows.forEach(r => {
    r.addEventListener('mouseenter', () => r.style.background = 'var(--bg-3)');
    r.addEventListener('mouseleave', () => r.style.background = '');
});
</script>
@endsection
