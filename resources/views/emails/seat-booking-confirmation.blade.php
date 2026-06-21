<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Seat Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:'Segoe UI',Arial,sans-serif;color:#333;">

<div style="max-width:560px;margin:40px auto;padding:0 16px 40px;">
<div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 2px 20px rgba(0,0,0,.09);">

  {{-- Header --}}
  <div style="background:linear-gradient(135deg,#0f766e 0%,#059669 100%);padding:42px 40px 34px;text-align:center;">
    @if($instituteName)
    <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,.75);letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;">
      {{ $instituteName }}
    </div>
    @endif
    <div style="font-size:27px;font-weight:800;color:#fff;line-height:1.3;margin-bottom:10px;">
      Your Seat is Confirmed!
    </div>
    <div style="font-size:14px;color:rgba(255,255,255,.82);line-height:1.65;">
      Congratulations, {{ $user->profile?->name ?? 'Student' }}! We're thrilled to have you with us.
    </div>
  </div>

  {{-- Body --}}
  <div style="padding:36px 40px 32px;">

    {{-- Welcome message --}}
    <p style="font-size:15px;font-weight:700;color:#111;margin:0 0 10px;">
      Dear {{ $user->profile?->name ?? 'Student' }},
    </p>
    <p style="font-size:14px;color:#4b5563;line-height:1.8;margin:0 0 10px;">
      Thank you for choosing
      <strong style="color:#059669;">{{ $instituteName ?: 'us' }}</strong>.
      We are delighted to welcome you and look forward to being a part of your learning journey.
    </p>
    <p style="font-size:14px;color:#4b5563;line-height:1.8;margin:0 0 28px;">
      Your seat for <strong style="color:#111;">{{ $courseBook->course?->name ?? 'the selected course' }}</strong>
      has been successfully reserved. We can't wait to see you in class!
    </p>

    {{-- Soft deadline reminder --}}
    @php $deadline = now()->addDays($validityDays)->format('d M Y'); @endphp
    <div style="background:#f0fdf4;border-left:4px solid #059669;border-radius:0 10px 10px 0;padding:16px 20px;margin-bottom:28px;">
      <div style="font-size:13px;font-weight:700;color:#065f46;margin-bottom:5px;">A gentle reminder</div>
      <div style="font-size:13.5px;color:#047857;line-height:1.7;">
        To secure your seat, we kindly request you to visit the institute and complete your admission
        within <strong>{{ $validityDays }} days</strong> &mdash; by <strong>{{ $deadline }}</strong>.
        We're here to help you through every step of the process.
      </div>
    </div>

    {{-- Booking details --}}
    <div style="border:1.5px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:28px;">
      <div style="background:#f9fafb;padding:10px 18px;border-bottom:1px solid #e5e7eb;">
        <span style="font-size:11px;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;">Booking Details</span>
      </div>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:12px;color:#9ca3af;font-weight:600;width:130px;">Course</td>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:13px;font-weight:600;color:#111;">{{ $courseBook->course?->name ?? '&mdash;' }}</td>
        </tr>
        @if($courseBook->batch?->name)
        <tr>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:12px;color:#9ca3af;font-weight:600;">Batch</td>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:13px;font-weight:600;color:#111;">{{ $courseBook->batch->name }}</td>
        </tr>
        @endif
        <tr>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:12px;color:#9ca3af;font-weight:600;">Student</td>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:13px;font-weight:600;color:#111;">{{ $user->profile?->name ?? '&mdash;' }}</td>
        </tr>
        <tr>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:12px;color:#9ca3af;font-weight:600;">Booking Date</td>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:13px;font-weight:600;color:#111;">{{ now()->format('d M Y') }}</td>
        </tr>
        <tr>
          <td style="padding:12px 18px;font-size:12px;color:#9ca3af;font-weight:600;">Admission By</td>
          <td style="padding:12px 18px;font-size:13px;font-weight:700;color:#059669;">{{ $deadline }}</td>
        </tr>
      </table>
    </div>

    {{-- Warm closing --}}
    <p style="font-size:13.5px;color:#6b7280;line-height:1.8;margin:0;">
      If you have any questions or need assistance, please feel free to reach out to us directly at the institute.
      We are always happy to help.
    </p>
    <p style="font-size:14px;color:#374151;font-weight:600;margin:16px 0 0;">
      Warm regards,<br>
      <span style="color:#059669;">{{ $instituteName ?: 'The Institute Team' }}</span>
    </p>

  </div>

  {{-- Footer --}}
  <div style="border-top:1px solid #f3f4f6;padding:18px 40px;text-align:center;">
    <div style="font-size:11px;color:#9ca3af;line-height:1.8;">
      This is an automated email &mdash; please do not reply directly.<br>
      &copy; {{ date('Y') }} {{ $instituteName ?: '' }} &mdash; All rights reserved.
    </div>
    <div style="font-size:11px;color:#d1d5db;margin-top:6px;">Powered by Gaurangi Technologies</div>
  </div>

</div>
</div>
</body>
</html>
