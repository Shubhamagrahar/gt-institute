<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Welcome — {{ $institute->name }}</title>
<!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
<style>
  * { box-sizing:border-box; margin:0; padding:0; }
  body { background:#f4f4f7; font-family:'Segoe UI',Arial,sans-serif; font-size:14px; color:#333; -webkit-font-smoothing:antialiased; }
  .wrapper { max-width:580px; margin:32px auto; background:#f4f4f7; }
  .card { background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.07); }

  /* Header */
  .header { background:linear-gradient(135deg,#6c5dd3,#4f46e5); padding:36px 40px; text-align:center; }
  .header-icon { width:56px; height:56px; border-radius:16px; background:rgba(255,255,255,.15); display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px; }
  .header-title { font-size:22px; font-weight:800; color:#fff; letter-spacing:-.01em; }
  .header-sub { font-size:13px; color:rgba(255,255,255,.75); margin-top:5px; }

  /* Body */
  .body { padding:36px 40px; }
  .greeting { font-size:16px; font-weight:700; color:#111; margin-bottom:8px; }
  .intro { font-size:14px; color:#555; line-height:1.7; margin-bottom:28px; }

  /* Credentials box */
  .cred-box { background:#f8f7ff; border:1.5px solid #e5e2f8; border-radius:10px; overflow:hidden; margin-bottom:24px; }
  .cred-header { background:#6c5dd3; padding:10px 18px; font-size:11px; font-weight:800; color:#fff; text-transform:uppercase; letter-spacing:.08em; }
  .cred-body { padding:18px 18px 14px; }
  .cred-row { display:flex; align-items:center; padding:9px 0; border-bottom:1px solid #ede9fb; }
  .cred-row:last-child { border-bottom:none; }
  .cred-label { font-size:12px; color:#888; width:130px; flex-shrink:0; }
  .cred-value { font-size:13px; font-weight:700; color:#111; font-family:'Courier New',monospace; word-break:break-all; }
  .cred-value.highlight { color:#6c5dd3; font-size:15px; letter-spacing:.04em; }

  /* CTA button */
  .btn-wrap { text-align:center; margin:24px 0; }
  .btn { display:inline-block; background:#6c5dd3; color:#ffffff; font-size:14px; font-weight:700; padding:13px 32px; border-radius:8px; text-decoration:none; letter-spacing:.01em; }

  /* Note */
  .note { background:#fef9ec; border-left:3px solid #f59e0b; border-radius:0 8px 8px 0; padding:12px 16px; font-size:13px; color:#78350f; line-height:1.6; margin-bottom:24px; }

  /* Info row */
  .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px; }
  .info-item { background:#f8f8f8; border-radius:8px; padding:12px 14px; }
  .info-item-label { font-size:10px; color:#999; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px; }
  .info-item-value { font-size:13px; font-weight:700; color:#111; }

  /* Footer */
  .footer { padding:24px 40px; text-align:center; }
  .footer-inst { font-size:13px; font-weight:700; color:#555; margin-bottom:4px; }
  .footer-note { font-size:11px; color:#aaa; line-height:1.6; }
  .footer-divider { height:1px; background:#eee; margin:16px 0; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">

    {{-- Header --}}
    <div class="header">
      <img src="{{ url('images/gt-icon.png') }}" alt="Gaurangi Technologies"
           style="height:32px;width:auto;margin:0 auto 14px;display:block;opacity:.9;"
           onerror="this.style.display='none'">
      <div class="header-title">Welcome Aboard!</div>
      <div class="header-sub">{{ $institute->name }}</div>
    </div>

    {{-- Body --}}
    <div class="body">
      <div class="greeting">Hi {{ $name }},</div>
      <div class="intro">
        You've been added as a staff member at <strong>{{ $institute->name }}</strong>.
        Your account is now active. Please find your login credentials below.
      </div>

      {{-- Credentials box --}}
      <div class="cred-box">
        <div class="cred-header">Your Login Credentials</div>
        <div class="cred-body">
          <div class="cred-row">
            <span class="cred-label">Staff ID</span>
            <span class="cred-value highlight">{{ $staffId }}</span>
          </div>
          <div class="cred-row">
            <span class="cred-label">Email</span>
            <span class="cred-value">{{ $email }}</span>
          </div>
          <div class="cred-row">
            <span class="cred-label">Mobile</span>
            <span class="cred-value">{{ $mobile }}</span>
          </div>
          <div class="cred-row">
            <span class="cred-label">Temporary Password</span>
            <span class="cred-value highlight">{{ $password }}</span>
          </div>
        </div>
      </div>

      {{-- Warning note --}}
      <div class="note">
        <strong>Important:</strong> This is a temporary password. Please log in and change it immediately from your profile settings. Do not share your password with anyone.
      </div>

      {{-- Login button --}}
      <div class="btn-wrap">
        <a href="{{ $loginUrl }}" class="btn">Login to Staff Panel &rarr;</a>
      </div>

      {{-- Info grid --}}
      <div class="info-grid">
        <div class="info-item">
          <div class="info-item-label">Login URL</div>
          <div class="info-item-value" style="font-size:11px;word-break:break-all;">{{ $loginUrl }}</div>
        </div>
        <div class="info-item">
          <div class="info-item-label">Institute</div>
          <div class="info-item-value">{{ $institute->name }}</div>
        </div>
      </div>

      <div style="font-size:13px;color:#666;line-height:1.7">
        You can log in using your <strong>mobile number</strong> or <strong>email address</strong> along with the password above.
        If you face any issues, please contact your institute admin.
      </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
      <div class="footer-divider"></div>
      <div class="footer-inst">{{ $institute->name }}</div>
      <div class="footer-note">
        This is an automated email. Please do not reply to this message.<br>
        If you did not expect this email, please contact your institute administrator.
      </div>
    </div>

  </div>

  {{-- Bottom note --}}
  <div style="text-align:center;padding:16px;font-size:11px;color:#aaa">
    &copy; {{ date('Y') }} {{ $institute->name }}. All rights reserved.
  </div>
</div>
</body>
</html>
