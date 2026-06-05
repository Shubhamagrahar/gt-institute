@extends('layouts.institute')
@section('title','Pending Admissions')
@section('page-title','Pending Admissions')

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Seat Booked Students</div>
      <div class="text-xs text-muted" style="margin-top:4px;">Yahan woh sab bookings hain jahan admission abhi pending hai.</div>
    </div>
    <form method="GET" action="{{ route('institute.enrollment.pending') }}" style="display:flex;gap:8px;align-items:center;">
      <select name="filter" class="gt-select" style="min-width:190px;">
        <option value="all" {{ ($filter ?? 'all') === 'all' ? 'selected' : '' }}>All Pending</option>
        <option value="details_pending" {{ ($filter ?? 'all') === 'details_pending' ? 'selected' : '' }}>Details Pending</option>
        <option value="payment_pending" {{ ($filter ?? 'all') === 'payment_pending' ? 'selected' : '' }}>Payment Pending</option>
        <option value="ready" {{ ($filter ?? 'all') === 'ready' ? 'selected' : '' }}>Ready For Admission</option>
        <option value="quick" {{ ($filter ?? 'all') === 'quick' ? 'selected' : '' }}>Quick Booking</option>
        <option value="full" {{ ($filter ?? 'all') === 'full' ? 'selected' : '' }}>Full Booking</option>
      </select>
      <button type="submit" class="btn btn-outline btn-sm">Apply</button>
    </form>
  </div>

  @if($pendingBooks->isEmpty())
    <div class="gt-empty">
      <div class="gt-empty-title">No pending admissions</div>
      <div class="gt-empty-sub">Abhi sab booked students ya to complete ho chuke hain ya list empty hai.</div>
    </div>
  @else
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Mode</th>
            <th>Details</th>
            <th>Paid / Required</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($pendingBooks as $book)
            <tr>
              <td>
                <div class="fw-600">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
                <div class="text-xs text-muted">{{ $book->student->mobile }}</div>
              </td>
              <td>
                <div>{{ $book->course?->name }}</div>
                <div class="text-xs text-muted">{{ $book->paymentPlan?->plan_type ?? 'NA' }}</div>
              </td>
              <td>
                <span class="badge {{ $book->booking_mode === 'quick' ? 'badge-warning' : 'badge-accent' }}">
                  {{ strtoupper($book->booking_mode) }}
                </span>
              </td>
              <td>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                  <span class="badge {{ $book->details_complete ? 'badge-success' : 'badge-danger' }}">
                    {{ $book->details_complete ? 'Details Complete' : 'Details Pending' }}
                  </span>
                  <span class="badge {{ $book->paid_amount + 0.01 >= $book->required_amount ? 'badge-success' : 'badge-warning' }}">
                    {{ $book->paid_amount + 0.01 >= $book->required_amount ? 'Payment Ready' : 'Payment Pending' }}
                  </span>
                  @if($book->admission_ready)
                    <span class="badge badge-success">Ready For Admission</span>
                  @endif
                </div>
              </td>
              <td>
                <div class="mono">₹{{ number_format($book->paid_amount, 2) }}</div>
                <div class="text-xs text-muted">Need ₹{{ number_format($book->required_amount, 2) }}</div>
              </td>
              <td style="display:flex;gap:8px;flex-wrap:wrap;">
                @if(!$book->details_complete)
                  <a href="{{ route('institute.enrollment.profile', $book) }}" class="btn btn-outline btn-sm">Complete Details</a>
                @endif
                <a href="{{ route('institute.fee-collect.show', $book->student) }}" class="btn btn-primary btn-sm">Open Payment</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
