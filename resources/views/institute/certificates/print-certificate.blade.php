@php
  $iName = $institute?->name ?? 'Institute';
  $iLogo = ($institute?->logo && !str_contains($institute->logo ?? '', 'default')) ? $institute->logo : null;
  $iAddrParts = array_filter([$institute?->address, $institute?->district, $institute?->state]);
  $iAddr = implode(', ', $iAddrParts);
  $qrData = urlencode($certificate->certificate_no);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Certificate — {{ $certificate->certificate_no }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Josefin+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*{ margin:0; padding:0; box-sizing:border-box; font-family:'Josefin Sans',Arial,sans-serif; }
body{ background:#bbb; padding:20px; }

.no-print { display:flex; gap:10px; justify-content:center; padding:0 0 16px; }
.no-print button { padding:8px 22px; border:none; border-radius:5px; cursor:pointer; font-size:13px; font-weight:700; }
.btn-p { background:#111; color:#fff; }
.btn-c { background:#e5e7eb; color:#374151; }

.certificate{
  width:297mm;
  min-height:210mm;
  margin:auto;
  background:#fff;
  position:relative;
  padding:20mm;
  border:15px solid #0b4ea2;
  overflow:hidden;
}

.watermark{
  position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
  width:450px; height:450px;
  @if($iLogo) background:url('{{ asset($iLogo) }}') center no-repeat; @endif
  background-size:contain; opacity:.05;
}

.header{ text-align:center; }
.logo{ width:90px; margin-bottom:10px; }
.header h1{ font-size:30px; color:#0b4ea2; }
.header p{ margin-top:5px; font-size:15px; }

.certificate-title{
  text-align:center; font-size:32px; font-weight:700; margin-top:25px;
  color:#d4a017; letter-spacing:2px;
}

.certificate-no{ margin-top:15px; text-align:right; font-size:15px; }

.content{ text-align:center; margin-top:25px; }
.certify-text{ font-size:19px; }
.content h2{ margin-top:14px; font-size:38px; color:#0b4ea2; }
.content h3{ margin-top:14px; font-size:28px; color:#d62828; }
.content p{ margin-top:10px; font-size:18px; line-height:1.6; }
.description{ width:80%; margin:25px auto 0; }

.bottom{ margin-top:25px; display:flex; justify-content:space-between; align-items:center; }
.qr img{ width:90px; }

.signatures{ margin-top:50px; display:flex; justify-content:space-between; }
.sign-box{ width:220px; text-align:center; border-top:1px solid #000; padding-top:10px; font-weight:600; }

@media print {
  body{ background:#fff; padding:0; }
  .no-print{ display:none; }
  .certificate{ width:100%; min-height:100vh; margin:0; border-width:10px; }
}
</style>
</head>
<body>

<div class="no-print">
  <button class="btn-p" onclick="window.print()">Print</button>
  <button class="btn-c" onclick="window.close()">Close</button>
</div>

<div class="certificate">

    <div class="watermark"></div>

    <div class="header">
        @if($iLogo)<img src="{{ asset($iLogo) }}" class="logo" alt="">@endif
        <h1>{{ strtoupper($iName) }}</h1>
        @if($iAddr)<p>{{ $iAddr }}</p>@endif
    </div>

    <div class="certificate-title">COURSE COMPLETION CERTIFICATE</div>

    <div class="certificate-no">Certificate No : <strong>{{ $certificate->certificate_no }}</strong></div>

    <div class="content">

        <p class="certify-text">This is to certify that</p>

        <h2>{{ strtoupper($certificate->student_name) }}</h2>

        @if($certificate->father_name)
        <p>Son / Daughter of <strong>{{ $certificate->father_name }}</strong></p>
        @endif

        <p>has successfully completed the course</p>

        <h3>{{ strtoupper($certificate->course_name) }}</h3>

        <div style="display:flex;justify-content:center;gap:30px;flex-wrap:wrap;margin-top:10px;">
          @if($certificate->duration)<p>Duration : <strong>{{ $certificate->duration }}</strong></p>@endif
          @if($certificate->academic_session)<p>Session : <strong>{{ $certificate->academic_session }}</strong></p>@endif
          @if($certificate->overall_grade)<p>Grade : <strong>{{ $certificate->overall_grade }}</strong></p>@endif
        </div>

        <p class="description">
            The student has successfully fulfilled all academic requirements prescribed
            by the institute and is hereby awarded this certificate.
        </p>

    </div>

    <div class="bottom">
        <div>
            Issue Date<br>
            <strong>{{ $certificate->created_at->format('d-M-Y') }}</strong>
        </div>
        <div class="qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $qrData }}" alt="QR">
        </div>
    </div>

    <div class="signatures">
        <div class="sign-box">Academic Head</div>
        <div class="sign-box">Controller of Examination</div>
        <div class="sign-box">Director</div>
    </div>

</div>

<script>
window.onload = function(){ window.print(); };
window.oncancelprint = window.close;
window.onafterprint  = window.close;
</script>

</body>
</html>
