@extends('layouts.institute')
@section('title', 'Franchise Levels')
@section('page-title', 'Franchise Levels')
@section('topbar-actions')
  <a href="{{ route('institute.franchise-levels.create') }}" class="btn btn-primary btn-sm">+ Add Level</a>
@endsection

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Levels ({{ $levels->count() }})</div>
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Commission</th>
          <th>Joining Fee</th>
          <th>Courses Configured</th>
          <th>Franchises</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($levels as $level)
          <tr>
            <td class="fw-600">{{ $level->name }}</td>
            <td class="mono">{{ number_format($level->commission_percent, 2) }}%</td>
            <td>
              @if(($level->level_fee ?? 0) > 0)
                <span class="mono" style="color:var(--accent);">₹{{ number_format($level->level_fee, 2) }}</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @if($level->course_charges_count > 0)
                <a href="{{ route('institute.franchise-levels.charges.edit', $level) }}"
                   style="color:var(--accent); text-decoration:none; font-size:13px;">
                  {{ $level->course_charges_count }} course(s) →
                </a>
              @else
                <a href="{{ route('institute.franchise-levels.charges', $level) }}"
                   style="color:var(--danger); text-decoration:none; font-size:12px;">
                  Not set — Configure ↗
                </a>
              @endif
            </td>
            <td><span class="badge badge-info">{{ $level->franchises()->count() }}</span></td>
            <td><span class="badge {{ $level->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($level->status) }}</span></td>
            <td>
              <div class="flex gap-2">
                <a href="{{ route('institute.franchise-levels.edit', $level) }}" class="btn btn-outline btn-xs">Edit</a>
                <a href="{{ route('institute.franchise-levels.charges', $level) }}" class="btn btn-outline btn-xs">Charges</a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7">
              <div class="gt-empty">
                <div class="gt-empty-title">No levels added yet</div>
                <a href="{{ route('institute.franchise-levels.create') }}" class="btn btn-primary btn-sm">Add First Level</a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
