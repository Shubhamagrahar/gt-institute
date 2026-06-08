@extends('layouts.institute')
@section('title','Preview')
@section('page-title','Seat Booking Review')

@section('content')
<style>
.preview-hero{background:linear-gradient(135deg,#0f2f6a,#1b75d0);color:#fff;border-radius:24px;padding:24px 28px;box-shadow:0 18px 40px rgba(15,47,106,.18)}
.preview-hero-top{display:flex;justify-content:space-between;gap:20px;align-items:flex-start;flex-wrap:wrap}
.preview-hero h2{margin:0;font-size:28px;font-weight:900;line-height:1.15}
.preview-hero p{margin:8px 0 0;color:rgba(255,255,255,.84)}
.preview-badge{display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;background:rgba(255,255,255,.14);font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
.preview-grid{display:grid;grid-template-columns:1.35fr .95fr;gap:18px;margin-top:18px}
.preview-card{background:var(--bg-2);border:1px solid var(--border);border-radius:22px;padding:18px}
.preview-card-title{font-size:14px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin-bottom:14px}
.preview-photo{width:86px;height:86px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 10px 22px rgba(0,0,0,.08)}
.preview-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.preview-meta-item{border:1px solid var(--border);border-radius:16px;padding:12px;background:var(--bg-3)}
.preview-meta-label{font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:#64748b;margin-bottom:4px}
.preview-meta-value{font-size:14px;font-weight:700;word-break:break-word}
.preview-section{margin-top:18px}
.preview-section-header{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:12px}
.preview-section-header h3{margin:0;font-size:18px;font-weight:900}
.preview-section-header .sub{font-size:12px;color:var(--text-2)}
.preview-rows{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.preview-row{border:1px solid var(--border);border-radius:16px;padding:12px;background:var(--bg-3)}
.preview-row label{display:block;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:#64748b;margin-bottom:4px}
.preview-row .value{font-size:14px;font-weight:700;line-height:1.35}
.preview-table{width:100%;border-collapse:collapse}
.preview-table th,.preview-table td{border-bottom:1px solid var(--border);padding:10px 8px;text-align:left;font-size:13px}
.preview-table th{font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.08em}
.preview-fee-box{border:1px solid var(--border);border-radius:16px;background:var(--bg-3);padding:14px}
.preview-fee-line{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px dashed var(--border);font-size:13px}
.preview-fee-line:last-child{border-bottom:none}
.preview-total{display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:12px;border-top:1px solid var(--border);font-size:16px;font-weight:900}
.preview-actions{display:flex;gap:10px;flex-wrap:wrap}
.preview-note{margin-top:12px;padding:12px 14px;border-radius:14px;background:#eff6ff;color:#1d4ed8;font-size:13px;line-height:1.5}
.preview-note.warning{background:#fff7ed;color:#9a3412;border:1px solid #fed7aa}
@media(max-width:900px){.preview-grid,.preview-rows{grid-template-columns:1fr}}
</style>

@php
  $statusLabel = $courseBook->status === 'RUN' ? 'Admission Active' : 'Seat Booked';
  $statusClass = $courseBook->status === 'RUN' ? 'badge-success' : 'badge-warning';
@endphp

<div class="preview-hero">
  <div class="preview-hero-top">
    <div>
      <div class="preview-badge">{{ $statusLabel }}</div>
      <h2>{{ $courseBook->student->profile?->name ?? $courseBook->student->user_id }}</h2>
      <p>{{ $courseBook->course->name }} @if($courseBook->batch) · {{ $courseBook->batch->name }} @endif</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
      <a href="{{ route('institute.students.show', $courseBook->student) }}" class="btn btn-outline btn-sm">View Student</a>
      <a href="{{ route('institute.students.edit', $courseBook->student) }}" class="btn btn-outline btn-sm">Edit Student</a>
      <a href="{{ route('institute.enrollment.profile', $courseBook) }}" class="btn btn-outline btn-sm">Edit Profile</a>
      <a href="{{ route('institute.enrollment.fee', $courseBook) }}" class="btn btn-outline btn-sm">Edit Fee</a>
      <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
    </div>
  </div>
</div>

<div class="preview-grid">
  <div class="preview-card">
    <div class="preview-card-title">Student Snapshot</div>
    <div style="display:flex;gap:14px;align-items:center;margin-bottom:16px;">
      @if($profile?->photo)
        <img src="{{ asset($profile->photo) }}" class="preview-photo" alt="Student photo">
      @else
        <div class="preview-photo" style="display:flex;align-items:center;justify-content:center;background:#e2e8f0;color:#334155;font-weight:900;">
          {{ strtoupper(substr($courseBook->student->name ?? $courseBook->student->user_id, 0, 1)) }}
        </div>
      @endif
      <div>
        <div style="font-size:18px;font-weight:900;">{{ $profile?->name ?? $courseBook->student->user_id }}</div>
        <div class="text-sm text-muted mono">{{ $courseBook->student->user_id }}</div>
        <div class="text-sm text-muted">{{ $courseBook->student->mobile }}</div>
        <div class="text-sm text-muted">{{ $courseBook->student->email ?? 'No email saved' }}</div>
      </div>
    </div>

    <div class="preview-meta">
      <div class="preview-meta-item">
        <div class="preview-meta-label">Enrollment No</div>
        <div class="preview-meta-value">{{ $courseBook->enrollment_no ?: 'Will generate after final admission' }}</div>
      </div>
      <div class="preview-meta-item">
        <div class="preview-meta-label">Booking Mode</div>
        <div class="preview-meta-value">{{ strtoupper($courseBook->booking_mode ?? 'full') }}</div>
      </div>
      <div class="preview-meta-item">
        <div class="preview-meta-label">Mobile</div>
        <div class="preview-meta-value">{{ $courseBook->student->mobile }}</div>
      </div>
      <div class="preview-meta-item">
        <div class="preview-meta-label">Email</div>
        <div class="preview-meta-value">{{ $courseBook->student->email ?? 'N/A' }}</div>
      </div>
      <div class="preview-meta-item">
        <div class="preview-meta-label">Course</div>
        <div class="preview-meta-value">{{ $courseBook->course->name }}</div>
      </div>
      <div class="preview-meta-item">
        <div class="preview-meta-label">Batch</div>
        <div class="preview-meta-value">{{ $courseBook->batch?->name ?? 'No batch' }}</div>
      </div>
    </div>
  </div>

  <div class="preview-card">
    <div class="preview-card-title">Fee Summary</div>
    <div class="preview-fee-box">
      @foreach($snapshots as $s)
        <div class="preview-fee-line">
          <span>{{ $s->fee_type_name }}</span>
          <strong>₹{{ number_format($s->final_amount, 2) }}</strong>
        </div>
      @endforeach
      <div class="preview-total">
        <span>Total Seat Booking Fee</span>
        <span class="mono">₹{{ number_format($displayTotalFee ?? $courseBook->final_fee, 2) }}</span>
      </div>
    </div>
    <div class="preview-note">
      Course fee is included with extra bound fees in this total.
    </div>
    <div class="preview-note warning">
      Basic details incomplete ho to bhi admission block nahi kiya gaya hai. Institute urgent case me admission complete kar sakta hai, aur baad me student edit page se details/education update kar sakta hai.
    </div>
  </div>
</div>

<div class="preview-section">
  <div class="preview-card">
    <div class="preview-section-header">
      <div>
        <h3>Admission Details</h3>
        <div class="sub">All saved profile data shown in clean summary format.</div>
      </div>
      <div class="preview-actions">
        <form method="POST" action="{{ route('institute.enrollment.confirm', $courseBook) }}" autocomplete="off">
          @csrf
          <button type="submit" class="btn btn-primary btn-lg">Complete Admission</button>
        </form>
      </div>
    </div>

    <div class="preview-rows">
      <div class="preview-row">
        <label>Mobile</label>
        <div class="value">{{ $courseBook->student->mobile }}</div>
      </div>
      <div class="preview-row">
        <label>Email</label>
        <div class="value">{{ $courseBook->student->email ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>Father Name</label>
        <div class="value">{{ $profile?->father_name ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>Mother Name</label>
        <div class="value">{{ $profile?->mother_name ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>Guardian</label>
        <div class="value">{{ $profile?->guardian_name ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>Guardian Mobile</label>
        <div class="value">{{ $profile?->guardian_mobile ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>Gender</label>
        <div class="value">{{ $profile?->gender ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>Date of Birth</label>
        <div class="value">{{ $profile?->dob?->format('d M Y') ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>Qualification</label>
        <div class="value">{{ $profile?->qualification ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>State</label>
        <div class="value">{{ $profile?->state ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>District</label>
        <div class="value">{{ $profile?->district ?? 'N/A' }}</div>
      </div>
      <div class="preview-row">
        <label>Address</label>
        <div class="value">{{ $profile?->address ?? 'N/A' }}</div>
      </div>
    </div>
  </div>
</div>

@if(($educationEnabled ?? true) && $education->count())
  <div class="preview-section">
    <div class="preview-card">
      <div class="preview-section-header">
        <div>
          <h3>Education</h3>
          <div class="sub">Academic history saved with this booking.</div>
        </div>
      </div>

      <div style="overflow:auto;">
        <table class="preview-table">
          <thead>
            <tr>
              <th>Exam</th>
              <th>Institute</th>
              <th>Board / University</th>
              <th>Year</th>
              <th>Division</th>
              <th>Percentage</th>
            </tr>
          </thead>
          <tbody>
            @foreach($education as $e)
              <tr>
                <td>{{ $e->examination }}</td>
                <td>{{ $e->institute_name ?: '-' }}</td>
                <td>{{ $e->board_university ?: '-' }}</td>
                <td>{{ $e->passing_year ?: '-' }}</td>
                <td>{{ $e->division ?: '-' }}</td>
                <td>{{ $e->marks_percentage ?: '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endif
@endsection
