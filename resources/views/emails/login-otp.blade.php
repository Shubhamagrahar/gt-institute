<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login OTP - GT Institute</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fb; color: #1f2937; padding: 32px 16px; }
    .wrap { max-width: 560px; margin: 0 auto; }
    .card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 20px; overflow: hidden; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08); }
    .head { padding: 30px 30px 20px; background: linear-gradient(135deg, #111827 0%, #312e81 100%); }
    .eyebrow { display: inline-block; font-size: 11px; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255,255,255,.72); margin-bottom: 12px; }
    .head h1 { font-size: 24px; margin-bottom: 8px; color: #ffffff; }
    .head p { color: rgba(255,255,255,.78); font-size: 14px; line-height: 1.6; }
    .body { padding: 28px; }
    .body p { color: #4b5563; font-size: 14px; line-height: 1.8; margin-bottom: 16px; }
    .otp-box {
      background: #f8fafc;
      border: 2px dashed #6366f1;
      border-radius: 16px;
      padding: 24px;
      text-align: center;
      margin: 20px 0;
    }
    .otp-label { font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: #6b7280; margin-bottom: 10px; }
    .otp-code {
      font-size: 40px;
      font-weight: 800;
      letter-spacing: 10px;
      color: #4f46e5;
      font-family: 'Courier New', monospace;
    }
    .otp-expiry { font-size: 12px; color: #9ca3af; margin-top: 10px; }
    .warning { background: rgba(245, 158, 11, 0.08); border: 1px solid #fbbf24; border-radius: 12px; padding: 14px 16px; font-size: 13px; color: #92400e; line-height: 1.6; margin-top: 4px; }
    .foot { padding: 18px 28px 24px; color: #94a3b8; font-size: 12px; line-height: 1.7; border-top: 1px solid #e5e7eb; background: #fcfcfd; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="head">
        <div class="eyebrow">GT Institute</div>
        <h1>Login Verification</h1>
        <p>Use the code below to complete your sign-in.</p>
      </div>

      <div class="body">
        <p>Hello {{ $accountName }},</p>
        <p>We received a sign-in request for your account. Enter the OTP below to verify your identity.</p>

        <div class="otp-box">
          <div class="otp-label">Your One-Time Password</div>
          <div class="otp-code">{{ $otp }}</div>
          <div class="otp-expiry">This code expires in 10 minutes</div>
        </div>

        <p>If you did not attempt to log in, please ignore this email. Your account remains secure.</p>

        <div class="warning">
          Never share this OTP with anyone. GT Institute staff will never ask for your OTP.
        </div>
      </div>

      <div class="foot">
        GT Institute Management Platform<br>
        Please do not reply to this automated email.
      </div>
    </div>
  </div>
</body>
</html>
