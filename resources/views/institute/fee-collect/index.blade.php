@extends('layouts.institute')
@section('title','Fee Collection')
@section('page-title','Fee Collection')

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Students with Pending Dues</div>
  </div>
  @if($students->isEmpty())
    <div class="gt-empty">
      <div class="gt-empty-icon">✅</div>
      <div class="gt-empty-title">All dues cleared</div>
      <div class="gt-empty-sub">No pending fees</div>
    </div>
  @else
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr><th>Student ID</th><th>Name</th><th>Mobile</th><th>Due Amount</th><th>Action</th></tr>
      </thead>
      <tbody>
        @foreach($students as $s)
        <tr>
          <td class="mono text-xs">{{ $s->user_id }}</td>
          <td class="fw-600">{{ $s->profile?->name ?? '—' }}</td>
          <td>{{ $s->mobile }}</td>
          <td class="mono amount-neg fw-600">₹{{ number_format(abs($s->studentWallet->balance), 2) }}</td>
          <td>
            <a href="{{ route('institute.fee-collect.show', $s) }}" class="btn btn-primary btn-sm">
              Collect Fee
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection