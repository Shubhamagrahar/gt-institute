@extends('layouts.institute')
@section('title','Payment Plans')
@section('page-title','Payment Plans')
@section('topbar-actions')
  <a href="{{ route('institute.payment-plans.create') }}" class="btn btn-primary btn-sm">+ Add Payment Plan</a>
@endsection

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Payment Plans</div>
  </div>

  @if($plans->isEmpty())
    <div class="gt-empty">
      <div class="gt-empty-title">No payment plans yet</div>
      <a href="{{ route('institute.payment-plans.create') }}" class="btn btn-primary btn-sm" style="margin-top:8px;">Add First Plan</a>
    </div>
  @else
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Type</th>
            <th>Grace Days</th>
            <th>Late Fee/Day</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($plans as $i => $plan)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td class="fw-600">{{ $plan->name }}</td>
              <td><span class="badge badge-accent">{{ $plan->type }}</span></td>
              <td>{{ $plan->grace_days }}</td>
              <td>₹{{ number_format($plan->late_fee_per_day, 2) }}</td>
              <td>
                @if($plan->is_active)
                  <span class="badge badge-success">Active</span>
                @else
                  <span class="badge badge-neutral">Inactive</span>
                @endif
              </td>
              <td style="display:flex;gap:6px;">
                <a href="{{ route('institute.payment-plans.edit', $plan) }}" class="btn btn-outline btn-xs">Edit</a>
                <form method="POST" action="{{ route('institute.payment-plans.destroy', $plan) }}" onsubmit="return confirm('Delete this payment plan?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
