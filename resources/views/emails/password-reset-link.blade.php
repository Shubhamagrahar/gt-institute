<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset - GT Institute</title>
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
    .meta { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px; margin-bottom: 20px; }
    .meta-label { display:block; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #6b7280; margin-bottom: 6px; }
    .meta strong { color: #111827; font-size: 14px; }
    .btn {
      display: inline-block;
      background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
      color: #ffffff !important;
      text-decoration: none;
      padding: 14px 24px;
      border-radius: 12px;
      font-weight: 700;
      margin: 10px 0 18px;
      box-shadow: 0 10px 24px rgba(79, 70, 229, 0.28);
    }
    .alt-link {
      margin-top: 4px;
      padding: 14px 16px;
      background: #f8fafc;
      border: 1px dashed #cbd5e1;
      border-radius: 12px;
      font-size: 12px;
      color: #64748b;
      line-height: 1.7;
      word-break: break-all;
    }
    .foot { padding: 18px 28px 24px; color: #94a3b8; font-size: 12px; line-height: 1.7; border-top: 1px solid #e5e7eb; background: #fcfcfd; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="head">
        <div class="eyebrow">GT Institute</div>
        <h1>Password Reset Request</h1>
        <p>We received a request to reset the password for your account.</p>
      </div>

      <div class="body">
        <p>Hello {{ $accountName }},</p>
        <p>If you requested this change, click the button below to set a new password. For your security, this link will expire in 60 minutes.</p>

        <div class="meta">
          <span class="meta-label">Account Reference</span>
          <strong>{{ $identifier }}</strong>
        </div>

        <a href="{{ $resetUrl }}" class="btn">Reset Password</a>

        <p>If you did not request a password reset, you can safely ignore this email. Your current password will remain unchanged.</p>

        <div class="alt-link">
          If the button does not work, copy and paste this link into your browser:<br>
          {{ $resetUrl }}
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
