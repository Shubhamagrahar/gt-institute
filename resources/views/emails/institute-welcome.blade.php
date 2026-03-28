<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to GT Institute Platform</title>
  @php
    $logoUrl = $logoUrl ?? asset('images/logo.png');
  @endphp
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0a0a0a; color: #f0f0f0; padding: 40px 20px; }
    .wrapper { max-width: 580px; margin: 0 auto; }

    .header {
      background: #111111;
      border: 1px solid #2a2a2a;
      border-radius: 16px 16px 0 0;
      padding: 32px;
      text-align: center;
    }
    .logo-box {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 74px;
      height: 74px;
      background: #181818;
      border: 1px solid #7c3aed;
      border-radius: 18px;
      overflow: hidden;
      margin-bottom: 16px;
      box-shadow: 0 10px 24px rgba(124, 58, 237, 0.22);
    }
    .logo-box img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 10px;
      background: #ffffff;
    }
    .logo-fallback {
      font-size: 22px;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: 1px;
    }
    .header h1 { font-size: 22px; font-weight: 700; color: #f0f0f0; }
    .header p  { font-size: 14px; color: #b9b9b9; margin-top: 6px; }

    .body {
      background: #111111;
      border-left: 1px solid #2a2a2a;
      border-right: 1px solid #2a2a2a;
      padding: 32px;
    }

    .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; color: #f0f0f0; }
    .message  { font-size: 14px; color: #a0a0a0; line-height: 1.7; margin-bottom: 24px; }

    .cred-box {
      background: #1a1a1a;
      border: 1px solid #6d28d9;
      border-radius: 10px;
      padding: 20px 24px;
      margin-bottom: 24px;
    }
    .cred-title { font-size: 11px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #b9a3ff; margin-bottom: 14px; }
    .cred-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      padding: 10px 0;
      border-bottom: 1px solid #35224f;
      font-size: 13px;
    }
    .cred-row:last-child { border-bottom: none; }
    .cred-label { color: #c7c7c7; min-width: 140px; }
    .cred-value {
      font-weight: 600;
      font-family: 'Courier New', monospace;
      color: #a855f7;
      text-align: left;
      margin-left: 15px;
      word-break: break-word;

    }

    .inst-box {
      background: #1a1a1a;
      border: 1px solid #6d28d9;
      border-radius: 10px;
      padding: 20px 24px;
      margin-bottom: 24px;
    }
    .inst-title { font-size: 11px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #b9a3ff; margin-bottom: 14px; }
    .inst-row {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      padding: 10px 0;
      border-bottom: 1px solid #35224f;
      font-size: 13px;
    }
    .inst-row:last-child { border-bottom: none; }
    .inst-key { color: #c7c7c7; min-width: 140px; }
    .inst-val {
      font-weight: 600;
      color: #f0f0f0;
      text-align: right;
      margin-left: auto;
      word-break: break-word;
    }

    .login-btn {
      display: block;
      background: #7c3aed;
      color: #ffffff;
      text-align: center;
      padding: 14px 24px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      margin-bottom: 24px;
    }

    .warning {
      background: rgba(124,58,237,.08);
      border: 1px solid rgba(124,58,237,.22);
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 12.5px;
      color: #d8b4fe;
      line-height: 1.6;
    }

    .footer {
      background: #0d0d0d;
      border: 1px solid #2a2a2a;
      border-top: none;
      border-radius: 0 0 16px 16px;
      padding: 20px 32px;
      text-align: center;
      font-size: 12px;
      color: #606060;
    }
    .footer a { color: #a855f7; text-decoration: none; }
  </style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="logo-box">
      <img src="{{ $logoUrl }}" alt="GT Institute Logo" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
      <div class="logo-fallback" style="display:none;">GT</div>
    </div>
    <h1>Welcome to GT Institute Platform</h1>
    <p>Your institute account has been created successfully</p>
  </div>

  <div class="body">
    <div class="greeting">Hello, {{ $institute->owner_name }}!</div>
    <div class="message">
      Your institute <strong style="color:#f0f0f0;">{{ $institute->name }}</strong> has been successfully registered
      on the GT Institute Management Platform. Below are your login credentials and institute details.
    </div>

    <div class="cred-box">
      <div class="cred-title">Login Credentials</div>
      <div class="cred-row">
        <span class="cred-label">Login URL</span>
        <span class="cred-value">{{ config('app.url') }}/login</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Mobile / Login ID</span>
        <span class="cred-value">{{ $user->mobile }}</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Email</span>
        <span class="cred-value">{{ $user->email }}</span>
      </div>
    
      <div class="cred-row">
        <span class="cred-label">Password</span>
        <span class="cred-value">{{ $plainPassword }}</span>
      </div>
    </div>

    <div class="inst-box">
      <div class="inst-title">Institute Details</div>
      <div class="inst-row"><span class="inst-key">Institute ID</span><span class="inst-val">{{ $institute->unique_id }}</span></div>
      <div class="inst-row"><span class="inst-key">Name</span><span class="inst-val">{{ $institute->name }}</span></div>
      <div class="inst-row"><span class="inst-key">Email</span><span class="inst-val">{{ $institute->email }}</span></div>
      <div class="inst-row"><span class="inst-key">Mobile</span><span class="inst-val">{{ $institute->mobile }}</span></div>
      <div class="inst-row"><span class="inst-key">Type</span><span class="inst-val">{{ $institute->type }}</span></div>
      @if($institute->address)
      <div class="inst-row"><span class="inst-key">Address</span><span class="inst-val" style="max-width:280px;">{{ $institute->address }}</span></div>
      @endif
    </div>

    <a href="{{ config('app.url') }}/login" class="login-btn">Login to Your Panel</a>

    <div class="warning">
      <strong>Important:</strong> Please change your password after your first login.
      Do not share your credentials with anyone. This email was auto-generated, please do not reply.
    </div>
  </div>

  <div class="footer">
    <p>GT Institute Management Platform &copy; {{ date('Y') }}</p>
    <p style="margin-top:6px;">Need help? Contact support at <a href="mailto:support@gtinstitute.com">support@gtinstitute.com</a></p>
  </div>

</div>
</body>
</html>
