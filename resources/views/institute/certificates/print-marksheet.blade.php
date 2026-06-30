@php
  $iName = $institute?->name ?? 'Institute';
  $iLogo = ($institute?->logo && !str_contains($institute->logo ?? '', 'default')) ? $institute->logo : null;
  $iAddrParts = array_filter([$institute?->address, $institute?->district, $institute?->state]);
  $iAddr = implode(', ', $iAddrParts);
  $photoSrc = ($certificate->photo && $certificate->photo !== 'images/user.svg' && $certificate->photo !== 'images/user.png')
      ? asset($certificate->photo) : asset('images/user.svg');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Marksheet — {{ $certificate->certificate_no }}</title>
<style>
*{ margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
body{ background:#eee; padding:20px; }

.no-print { display:flex; gap:10px; justify-content:center; padding:0 0 16px; }
.no-print button { padding:8px 22px; border:none; border-radius:5px; cursor:pointer; font-size:13px; font-weight:700; }
.btn-p { background:#111; color:#fff; }
.btn-c { background:#e5e7eb; color:#374151; }

.marksheet{
  width:297mm; min-height:210mm; margin:auto;
  background:#fff; border:12px solid #0b4ea2; padding:15mm;
}

.header{ display:flex; align-items:center; justify-content:space-between; border-bottom:2px solid #ddd; padding-bottom:14px; }
.logo{ width:90px; flex-shrink:0; }
.title{ text-align:center; flex:1; }
.title h1{ font-size:26px; color:#0b4ea2; }
.title p{ font-size:13px; margin-top:4px; }
.title h2{ margin-top:8px; color:#d62828; }
.title h3{ margin-top:4px; color:#444; font-size:16px; }
.serial{ text-align:right; flex-shrink:0; }
.serial h3{ font-size:13px; }
.serial p{ font-family:monospace; }

.student-info{ display:flex; justify-content:space-between; margin-top:18px; }
.student-info table td{ padding:6px 8px; font-size:14px; }
.student-info table td:first-child{ font-weight:bold; width:160px; }
.photo img{ width:120px; height:145px; border:2px solid #0b4ea2; object-fit:cover; }

.marks-table{ width:100%; border-collapse:collapse; margin-top:18px; }
.marks-table th{ background:#0b4ea2; color:#fff; padding:9px; border:1px solid #000; font-size:13px; }
.marks-table td{ padding:9px; border:1px solid #000; font-size:13px; }

.result{ display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-top:18px; padding:14px; background:#f7f7f7; border:1px solid #ddd; }
.result div{ font-size:14px; }

.footer{ display:flex; justify-content:space-between; margin-top:60px; font-weight:bold; font-size:13px; }
.footer div{ text-align:center; border-top:1px solid #000; padding-top:8px; width:200px; }

@media print {
  body{ background:#fff; padding:0; }
  .no-print{ display:none; }
  .marksheet{ width:100%; min-height:100vh; margin:0; border-width:8px; }
}
</style>
</head>
<body>

<div class="no-print">
  <button class="btn-p" onclick="window.print()">Print</button>
  <button class="btn-c" onclick="window.close()">Close</button>
</div>

<div class="marksheet">

    <div class="header">
        @if($iLogo)<img src="{{ asset($iLogo) }}" class="logo" alt="">@endif
        <div class="title">
            <h1>{{ strtoupper($iName) }}</h1>
            @if($iAddr)<p>{{ $iAddr }}</p>@endif
            <h2>MARKSHEET</h2>
            <h3>{{ strtoupper($certificate->course_name) }}</h3>
        </div>
        <div class="serial">
            <h3>Sr. No.</h3>
            <p>{{ $certificate->certificate_no }}</p>
        </div>
    </div>

    <div class="student-info">
        <div class="left">
            <table>
                <tr><td>Student Name</td><td>{{ $certificate->student_name }}</td></tr>
                @if($certificate->father_name)<tr><td>Father's Name</td><td>{{ $certificate->father_name }}</td></tr>@endif
                @if($certificate->mother_name)<tr><td>Mother's Name</td><td>{{ $certificate->mother_name }}</td></tr>@endif
                <tr><td>Enrollment No.</td><td>{{ $certificate->enrollment_no ?: '—' }}</td></tr>
                <tr><td>Course</td><td>{{ $certificate->course_name }}</td></tr>
                @if($certificate->academic_session)<tr><td>Session</td><td>{{ $certificate->academic_session }}</td></tr>@endif
                @if($certificate->duration)<tr><td>Duration</td><td>{{ $certificate->duration }}</td></tr>@endif
            </table>
        </div>
        <div class="photo">
            <img src="{{ $photoSrc }}" alt="">
        </div>
    </div>

    <table class="marks-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Subject</th>
                <th>Max Marks</th>
                <th>Obtained</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($certificate->subjects as $s)
            <tr>
                <td>{{ $s->subject_code ?: '—' }}</td>
                <td>{{ $s->subject_name }}</td>
                <td>{{ rtrim(rtrim(number_format($s->max_marks, 2), '0'), '.') }}</td>
                <td>{{ rtrim(rtrim(number_format($s->obtained_marks, 2), '0'), '.') }}</td>
                <td>{{ $s->grade ?: '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;">No subjects recorded</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="result">
        <div><strong>Total Marks :</strong> {{ rtrim(rtrim(number_format($certificate->total_obtained, 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($certificate->total_max, 2), '0'), '.') }}</div>
        <div><strong>Percentage :</strong> {{ $certificate->percentage }}%</div>
        <div><strong>Grade :</strong> {{ $certificate->overall_grade ?: '—' }}</div>
        <div><strong>Result :</strong> {{ $certificate->result ?: '—' }}</div>
    </div>

    <div class="footer">
        <div>Head / Principal</div>
        <div>Controller of Examination</div>
        <div>Director</div>
    </div>

</div>

<script>
window.onload = function(){ window.print(); };
window.oncancelprint = window.close;
window.onafterprint  = window.close;
</script>

</body>
</html>
