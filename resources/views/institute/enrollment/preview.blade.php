@extends('layouts.institute')
@section('title','Preview')
@section('page-title','Step 4 — Review & Confirm')

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Admission Preview</div>
    <div style="display:flex;gap:8px;">
      <a href="{{ route('institute.enrollment.profile', $courseBook) }}" class="btn btn-outline btn-sm">✏ Edit Profile</a>
      <a href="{{ route('institute.enrollment.fee', $courseBook) }}" class="btn btn-outline btn-sm">✏ Edit Fee</a>
    </div>
  </div>

  <div class="gt-grid-2" style="gap:20px;">
    {{-- Profile --}}
    <div>
      <div class="gt-form-section">Student Information</div>
      <div style="display:flex;flex-direction:column;gap:8px;margin-top:12px;">
        @if($profile?->photo)
          <img src="{{ asset($profile->photo) }}"
            style="width:80px;height:80px;object-fit:cover;border-radius:50%;margin-bottom:8px;">
        @endif
        @foreach($fields as $field)
          @php
            $value = in_array($field->field_key, ['mobile', 'email'], true)
              ? $courseBook->student->{$field->field_key}
              : $profile?->{$field->field_key};
          @endphp
          @if($value)
          <div style="display:flex;gap:8px;">
            <span class="text-muted text-xs" style="min-width:140px;">{{ $field->field_label }}</span>
            <span style="font-size:13px;font-weight:500;">{{ $value }}</span>
          </div>
          @endif
        @endforeach
      </div>

      @if(($educationEnabled ?? true) && $education->count())
      <div class="gt-form-section" style="margin-top:16px;">Education</div>
      <div class="gt-table-wrap" style="margin-top:10px;">
        <table class="gt-table">
          <thead><tr><th>Course / Exam</th><th>Institute</th><th>Board / University</th><th>Year</th><th>Division</th><th>Percentage</th></tr></thead>
          <tbody>
            @foreach($education as $e)
            <tr>
              <td>{{ $e->examination }}</td>
              <td>-</td>
              <td>{{ $e->board_university }}</td>
              <td>{{ $e->passing_year }}</td>
              <td>-</td>
              <td>{{ $e->marks_percentage }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>

    {{-- Course & Fee --}}
    <div>
      <div class="gt-form-section">Course & Fee</div>
      <div style="display:flex;flex-direction:column;gap:8px;margin-top:12px;">
        <div style="display:flex;gap:8px;">
          <span class="text-muted text-xs" style="min-width:140px;">Course</span>
          <span class="fw-600">{{ $courseBook->course->name }}</span>
        </div>
        <div style="display:flex;gap:8px;">
          <span class="text-muted text-xs" style="min-width:140px;">Enrollment No</span>
          <span class="mono">{{ $courseBook->enrollment_no }}</span>
        </div>
        @if($courseBook->batch)
        <div style="display:flex;gap:8px;">
          <span class="text-muted text-xs" style="min-width:140px;">Batch</span>
          <span>{{ $courseBook->batch->name }}</span>
        </div>
        @endif
        <div style="display:flex;gap:8px;">
          <span class="text-muted text-xs" style="min-width:140px;">Payment Type</span>
          <span class="badge badge-accent">{{ $plan?->plan_type === 'MONTHLY' ? 'MONTH' : $plan?->plan_type }}</span>
        </div>
        @if($plan?->plan_type === 'MONTHLY')
        <div style="display:flex;gap:8px;">
          <span class="text-muted text-xs" style="min-width:140px;">Monthly Amount</span>
          <span class="mono fw-600">₹{{ number_format($plan->monthly_amount, 2) }}</span>
        </div>
        @endif
      </div>

      <div class="gt-form-section" style="margin-top:16px;">Fee Breakdown</div>
      <div style="margin-top:10px;">
        @foreach($snapshots as $s)
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);font-size:13px;">
          <span>{{ $s->fee_type_name }}</span>
          <div style="text-align:right;">
            @if($s->discount_amount > 0)
              <div class="text-xs amount-neg">-₹{{ number_format($s->discount_amount,2) }}</div>
            @endif
            <div class="mono fw-600">₹{{ number_format($s->final_amount,2) }}</div>
          </div>
        </div>
        @endforeach
        <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:16px;font-weight:700;">
          <span>Total</span>
          <span class="mono text-accent">₹{{ number_format($courseBook->final_fee,2) }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="gt-divider"></div>
  <div style="display:flex;gap:10px;">
    <form method="POST" action="{{ route('institute.enrollment.confirm', $courseBook) }}">
      @csrf
      <button type="submit" class="btn btn-primary btn-lg">
        ✓ Confirm Admission & Proceed to Fee Collection
      </button>
    </form>
  </div>
</div>
@endsection
