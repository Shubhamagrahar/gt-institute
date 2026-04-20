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
          <th>Status</th>
          <th>Notes</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($levels as $level)
          <tr>
            <td class="fw-600">{{ $level->name }}</td>
            <td class="mono">{{ number_format($level->commission_percent, 2) }}%</td>
            <td><span class="badge {{ $level->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($level->status) }}</span></td>
            <td>{{ $level->notes ?: 'NA' }}</td>
            <td><a href="{{ route('institute.franchise-levels.edit', $level) }}" class="btn btn-outline btn-xs">Edit</a></td>
          </tr>
        @empty
          <tr>
            <td colspan="5">
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
