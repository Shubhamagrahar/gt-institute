<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admission Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:'Segoe UI',Arial,sans-serif;color:#333;">

<div style="max-width:560px;margin:40px auto;padding:0 16px 40px;">
<div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 2px 20px rgba(0,0,0,.09);">

  {{-- Header --}}
  <div style="background:linear-gradient(135deg,#4c1d95 0%,#6c5dd3 100%);padding:42px 40px 34px;text-align:center;">
    @if($instituteName)
    <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,.7);letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;">
      {{ $instituteName }}
    </div>
    @endif
    <div style="font-size:27px;font-weight:800;color:#fff;line-height:1.3;margin-bottom:10px;">
      Admission Confirmed!
    </div>
    <div style="font-size:14px;color:rgba(255,255,255,.82);line-height:1.65;">
      Welcome to the {{ $instituteName ?: 'institute' }} family, {{ $user->profile?->name ?? 'Student' }}!
    </div>
  </div>

  {{-- Body --}}
  <div style="padding:36px 40px 32px;">

    <p style="font-size:15px;font-weight:700;color:#111;margin:0 0 10px;">
      Dear {{ $user->profile?->name ?? 'Student' }},
    </p>
    <p style="font-size:14px;color:#4b5563;line-height:1.8;margin:0 0 10px;">
      Thank you for choosing
      <strong style="color:#6c5dd3;">{{ $instituteName ?: 'us' }}</strong>.
      We are truly delighted to have you as our student and look forward to supporting you every step of the way.
    </p>
    <p style="font-size:14px;color:#4b5563;line-height:1.8;margin:0 0 28px;">
      Your admission for <strong style="color:#111;">{{ $courseBook->course?->name ?? 'the selected course' }}</strong>
      is now officially confirmed. Your learning journey begins here!
    </p>

    {{-- Admission details --}}
    <div style="border:1.5px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:24px;">
      <div style="background:#f9fafb;padding:10px 18px;border-bottom:1px solid #e5e7eb;">
        <span style="font-size:11px;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;">Admission Details</span>
      </div>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:12px;color:#9ca3af;font-weight:600;width:140px;">Course</td>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:13px;font-weight:600;color:#111;">{{ $courseBook->course?->name ?? '&mdash;' }}</td>
        </tr>
        @if($courseBook->batch?->name)
        <tr>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:12px;color:#9ca3af;font-weight:600;">Batch</td>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:13px;font-weight:600;color:#111;">{{ $courseBook->batch->name }}</td>
        </tr>
        @endif
        @if($courseBook->enrollment_no)
        <tr>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:12px;color:#9ca3af;font-weight:600;">Enrollment No.</td>
          <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:13px;font-weight:700;color:#6c5dd3;font-family:'Courier New',monospace;">{{ $courseBook->enrollment_no }}</td>
        </tr>
        @endif
        <tr>
          <td style="padding:12px 18px;font-size:12px;color:#9ca3af;font-weight:600;">Admission Date</td>
          <td style="padding:12px 18px;font-size:13px;font-weight:600;color:#111;">{{ now()->format('d M Y') }}</td>
        </tr>
      </table>
    </div>

    {{-- Login credentials --}}
    <div style="border:1.5px solid #ddd6fe;border-radius:10px;overflow:hidden;margin-bottom:24px;">
      <div style="background:linear-gradient(135deg,#6c5dd3,#7c3aed);padding:11px 18px;">
        <span style="font-size:11px;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.07em;">Your Student Portal Login</span>
      </div>
      <div style="background:#faf8ff;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="padding:12px 18px;border-bottom:1px solid #ede9fb;font-size:12px;color:#9ca3af;font-weight:600;width:140px;">Login ID</td>
            <td style="padding:12px 18px;border-bottom:1px solid #ede9fb;font-size:15px;font-weight:800;color:#6c5dd3;font-family:'Courier New',monospace;letter-spacing:.04em;">{{ $user->user_id }}</td>
          </tr>
          <tr>
            <td style="padding:12px 18px;border-bottom:1px solid #ede9fb;font-size:12px;color:#9ca3af;font-weight:600;">Password</td>
            <td style="padding:12px 18px;border-bottom:1px solid #ede9fb;font-size:15px;font-weight:800;color:#6c5dd3;font-family:'Courier New',monospace;letter-spacing:.06em;">{{ $plainPassword }}</td>
          </tr>
          <tr>
            <td style="padding:12px 18px;border-bottom:1px solid #ede9fb;font-size:12px;color:#9ca3af;font-weight:600;">Email / Mobile</td>
            <td style="padding:12px 18px;border-bottom:1px solid #ede9fb;font-size:13px;font-weight:600;color:#374151;word-break:break-all;">{{ $user->email ?? $user->mobile }}</td>
          </tr>
          <tr>
            <td style="padding:12px 18px;font-size:12px;color:#9ca3af;font-weight:600;">Portal Link</td>
            <td style="padding:12px 18px;font-size:12px;font-weight:600;">
              <a href="{{ url('/student/login') }}" style="color:#6c5dd3;text-decoration:none;">{{ url('/student/login') }}</a>
            </td>
          </tr>
        </table>
      </div>
    </div>

    {{-- Soft security note --}}
    <div style="background:#f5f3ff;border-left:4px solid #6c5dd3;border-radius:0 10px 10px 0;padding:14px 18px;margin-bottom:28px;">
      <div style="font-size:13px;color:#4c1d95;line-height:1.75;">
        For your security, we recommend logging in and updating your password at your earliest convenience.
        Please keep your login credentials safe and do not share them with anyone.
      </div>
    </div>

    {{-- Portal button --}}
    <div style="text-align:center;margin-bottom:28px;">
      <a href="{{ url('/student/login') }}"
         style="display:inline-block;background:linear-gradient(135deg,#6c5dd3,#7c3aed);color:#fff;font-size:14px;font-weight:700;padding:14px 44px;border-radius:10px;text-decoration:none;letter-spacing:.02em;">
        Go to Student Portal
      </a>
    </div>

    {{-- Warm closing --}}
    <p style="font-size:13.5px;color:#6b7280;line-height:1.8;margin:0 0 16px;">
      We are excited to have you on board and wish you a wonderful and successful learning experience.
      Should you need any assistance, please don't hesitate to reach out to us at the institute.
    </p>
    <p style="font-size:14px;color:#374151;font-weight:600;margin:0;">
      Best wishes,<br>
      <span style="color:#6c5dd3;">{{ $instituteName ?: 'The Institute Team' }}</span>
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
