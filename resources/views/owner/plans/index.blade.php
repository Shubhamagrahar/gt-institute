@extends('layouts.owner')
@section('title','Plans')
@section('page-title','Plans')
@section('topbar-actions')
  <a href="{{ route('owner.plans.create') }}" class="btn btn-primary btn-sm">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Plan
  </a>
@endsection
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Plans ({{ $plans->count() }})</div>
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr>
        <th>#</th><th>Plan Name</th><th>Price</th><th>Duration</th><th>Features</th><th>Status</th><th>Actions</th>
      </tr></thead>
      <tbody>
        @forelse($plans as $plan)
        <tr>
          <td class="text-muted mono">{{ $loop->iteration }}</td>
          <td class="fw-600">{{ $plan->name }}</td>
          <td class="mono text-accent">₹{{ number_format($plan->price,2) }}</td>
          <td class="text-muted">{{ $plan->duration }} months</td>
          <td><span class="badge badge-info">{{ $plan->features_count }} features</span></td>
          <td><span class="badge {{ $plan->status==='active' ? 'badge-success' : 'badge-neutral' }}">{{ ucfirst($plan->status) }}</span></td>
          <td>
            <div class="flex gap-2">
              <a href="{{ route('owner.plans.show',$plan) }}" class="btn btn-outline btn-xs">View</a>
              <a href="{{ route('owner.plans.edit',$plan) }}" class="btn btn-outline btn-xs">Edit</a>
              <form action="{{ route('owner.plans.toggle',$plan) }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn btn-xs {{ $plan->status==='active' ? '' : 'btn-success' }}" style="{{ $plan->status==='active' ? 'background:var(--warning-bg);color:var(--warning);border:1px solid rgba(255,184,77,.2);' : '' }}">
                  {{ $plan->status==='active' ? 'Disable' : 'Enable' }}
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7">
          <div class="gt-empty"><div class="gt-empty-icon">📋</div><div class="gt-empty-title">No plans yet</div>
          <a href="{{ route('owner.plans.create') }}" class="btn btn-primary btn-sm">Create Plan</a></div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
