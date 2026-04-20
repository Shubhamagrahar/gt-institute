<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to GT Franchise Panel</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #0a0a0a; color: #f2f2f2; padding: 40px 16px; }
    .wrap { max-width: 600px; margin: 0 auto; }
    .card { background: #121212; border: 1px solid #2c2c2c; border-radius: 18px; overflow: hidden; }
    .head { padding: 28px 30px; border-bottom: 1px solid #232323; background: linear-gradient(135deg, #151515, #1b1b1b); }
    .head h1 { font-size: 22px; margin-bottom: 8px; }
    .head p { font-size: 14px; color: #bbbbbb; line-height: 1.7; }
    .body { padding: 28px 30px; }
    .box { background: #181818; border: 1px solid #2f2f2f; border-radius: 12px; padding: 18px 20px; margin-bottom: 18px; }
    .box h2 { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #c4b5fd; margin-bottom: 14px; }
    .row { display: flex; justify-content: space-between; gap: 16px; padding: 9px 0; border-bottom: 1px solid #272727; font-size: 13px; }
    .row:last-child { border-bottom: none; }
    .label { color: #a3a3a3; min-width: 130px; }
    .value { color: #f7f7f7; font-weight: 600; text-align: right; word-break: break-word; }
    .value.code { font-family: 'Courier New', monospace; color: #fde68a; }
    .btn { display: block; text-align: center; text-decoration: none; background: #84cc16; color: #111 !important; font-weight: 700; padding: 14px 18px; border-radius: 10px; margin: 24px 0 18px; }
    .note { font-size: 12px; color: #d4d4d4; line-height: 1.7; background: rgba(132, 204, 22, .08); border: 1px solid rgba(132, 204, 22, .2); border-radius: 10px; padding: 12px 14px; }
    .foot { padding: 18px 30px; border-top: 1px solid #232323; font-size: 12px; color: #8d8d8d; text-align: center; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="head">
        <h1>Welcome to GT Franchise Panel</h1>
        <p>Your franchise account has been created successfully under <strong>{{ $franchise->institute?->name }}</strong>.</p>
      </div>

      <div class="body">
        <p style="font-size:15px; margin-bottom:18px;">Hello, {{ $franchise->owner_name }}!</p>

        <div class="box">
          <h2>Login Credentials</h2>
          <div class="row"><span class="label">Login URL</span><span class="value">{{ config('app.url') }}/login</span></div>
          <div class="row"><span class="label">Login ID</span><span class="value code">{{ $user->user_id }}</span></div>
          <div class="row"><span class="label">Email</span><span class="value">{{ $user->email }}</span></div>
          <div class="row"><span class="label">Password</span><span class="value code">{{ $plainPassword }}</span></div>
        </div>

        <div class="box">
          <h2>Franchise Details</h2>
          <div class="row"><span class="label">Franchise ID</span><span class="value code">{{ $franchise->unique_id }}</span></div>
          <div class="row"><span class="label">Franchise Name</span><span class="value">{{ $franchise->name }}</span></div>
          <div class="row"><span class="label">Owner Name</span><span class="value">{{ $franchise->owner_name }}</span></div>
          <div class="row"><span class="label">Opening Wallet</span><span class="value">Rs. {{ number_format($franchise->wallet?->balance ?? 0, 2) }}</span></div>
        </div>

        <a href="{{ config('app.url') }}/login" class="btn">Login to Franchise Panel</a>

        <div class="note">
          Please keep these credentials safe and change the password after first login. This email was generated automatically.
        </div>
      </div>

      <div class="foot">
        GT Institute Management Platform &copy; {{ date('Y') }}
      </div>
    </div>
  </div>
</body>
</html>
