<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seat Booking Confirmed</title>
</head>
<body style="margin:0;background:#f6f8fc;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
  <div style="max-width:680px;margin:0 auto;padding:24px;">
    <div style="background:#ffffff;border:1px solid #e5e7eb;border-radius:18px;padding:28px;">
      <h1 style="margin:0 0 12px;font-size:24px;color:#111827;">Seat Booking Confirmed</h1>
      <p style="margin:0 0 18px;line-height:1.6;">Hello {{ $user->profile?->name ?? 'Student' }}, your seat booking has been saved successfully. Your final admission will complete from the pending admissions flow when the remaining steps are done.</p>

      <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:18px 0;">
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;width:42%;color:#6b7280;">Student ID</td>
          <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;font-weight:700;">{{ $user->user_id }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;color:#6b7280;">Mobile</td>
          <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;font-weight:700;">{{ $user->mobile }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;color:#6b7280;">Course</td>
          <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;font-weight:700;">{{ $courseBook->course?->name ?? '-' }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;color:#6b7280;">Batch</td>
          <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;font-weight:700;">{{ $courseBook->batch?->name ?? 'No batch' }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;color:#6b7280;">Status</td>
          <td style="padding:10px 0;font-weight:700;">Seat booked, admission pending</td>
        </tr>
      </table>

      <p style="margin:0;line-height:1.6;color:#374151;">If any detail looks incorrect, please contact the institute before final admission is completed.</p>
    </div>
  </div>
</body>
</html>
