<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Franchise Certificate — {{ $franchise->name }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cinzel:wght@400;600;700;900&family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

    body {
      background: #60697a;
      min-height: 100vh;
      display: flex; flex-direction: column;
      align-items: center; padding: 24px 12px 60px;
    }
    .toolbar { display:flex; gap:12px; margin-bottom:20px; }
    .toolbar button {
      padding:10px 28px; font-size:14px; font-weight:700;
      border:none; border-radius:5px; cursor:pointer;
      letter-spacing:.5px; font-family:Arial,sans-serif;
    }
    .btn-back  { background:#444; color:#fff; }
    .btn-print { background:#1a237e; color:#fff; }

    /* ── Responsive wrap ── */
    .cert-wrap { width:100%; max-width:1122px; }

    /* ═══════════════════════════════════════════════════════
       CERTIFICATE  1122 × 794 px  (A4 LANDSCAPE @ 96 dpi)
       Reference: wave corners · double border · 3-col header
       · divider · Cinzel title · Great Vibes name · teal line
       · body text · 3 signature blocks
    ═══════════════════════════════════════════════════════ */
    .cert {
      width: 1122px;
      height: 794px;
      background: #ffffff;
      position: relative;
      transform-origin: top left;
      font-family: 'EB Garamond', Georgia, serif;
      box-shadow: 0 20px 80px rgba(0,0,0,.65);
      overflow: hidden;
    }

    /* ══ WAVE SVG LAYER ══ */
    .cert-waves {
      position: absolute; inset: 0;
      width: 100%; height: 100%;
      pointer-events: none; z-index: 1;
    }

    /* ══ DOUBLE BORDER ══ */
    .cert-frame {
      position: absolute; inset: 14px;
      border: 1.5px solid #1a3d99;
      pointer-events: none; z-index: 5;
    }
    .cert-frame::after {
      content: ''; position: absolute; inset: 5px;
      border: 1px solid #3a6cc8;
    }

    /* ══ CONTENT ══ */
    .cert-body {
      position: absolute;
      inset: 28px 28px 24px 28px;
      z-index: 10;
      display: flex; flex-direction: column;
    }

    /* ── Header ── */
    .hdr { display:flex; align-items:center; gap:10px; flex-shrink:0; }

    .hdr-logo {
      flex-shrink:0; width:82px; height:82px; border-radius:50%;
      border:2.5px solid #1a3d99; overflow:hidden;
      background:#eef2ff;
      display:flex; align-items:center; justify-content:center;
    }
    .hdr-logo img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
    .hdr-logo-ph {
      width:100%; height:100%;
      background: radial-gradient(circle at 42% 38%, #2a4fd8, #0e2080);
      display:flex; flex-direction:column;
      align-items:center; justify-content:center;
    }
    .hdr-logo-ph span {
      font-family:'Cinzel',serif; color:#fff;
      font-size:9px; font-weight:700; line-height:1.7;
      text-align:center; padding:0 4px;
    }

    .hdr-center { flex:1; text-align:center; }
    .hdr-reg { font-family:'EB Garamond',serif; font-size:11.5px; color:#444; margin-bottom:2px; }
    .hdr-name {
      font-family:'Cinzel',serif; font-size:20px; font-weight:900;
      color:#0d1f6e; letter-spacing:.5px; line-height:1.15;
      text-transform:uppercase;
    }
    .hdr-details { font-family:'EB Garamond',serif; font-size:11px; color:#555; line-height:1.7; margin-top:3px; }

    .hdr-badge {
      flex-shrink:0; width:82px; height:82px; border-radius:50%;
      border:2.5px solid #1a3d99;
      background: radial-gradient(circle at 40% 35%, #2a4fd8, #0e2080);
      display:flex; flex-direction:column;
      align-items:center; justify-content:center;
      position:relative;
    }
    .hdr-badge::before {
      content:''; position:absolute; inset:7px;
      border-radius:50%; border:1.5px solid rgba(255,255,255,.28);
    }
    .hdr-badge-inner {
      display:flex; flex-direction:column;
      align-items:center; justify-content:center;
      text-align:center; z-index:1;
    }
    .badge-top  { font-family:'Cinzel',serif; font-size:6.5px; font-weight:700; color:rgba(255,255,255,.7); letter-spacing:1px; }
    .badge-mid  { font-family:'Cinzel',serif; font-size:24px; font-weight:900; color:#ffd700; line-height:.85; }
    .badge-sub  { font-family:'Cinzel',serif; font-size:6.5px; font-weight:700; color:rgba(255,255,255,.7); letter-spacing:1px; }
    .badge-name { font-family:'Cinzel',serif; font-size:8px; font-weight:900; color:#ffd700; letter-spacing:1.5px; }

    /* ── Header divider line ── */
    .hdr-divider {
      flex-shrink:0; margin:9px 0 5px; height:1.5px;
      background:linear-gradient(90deg, transparent 0%, #1a3d99 5%, #1a3d99 95%, transparent 100%);
    }

    /* ── Title ── */
    .cert-title-area { text-align:center; flex-shrink:0; margin:6px 0 3px; }
    .cert-title {
      font-family:'Cinzel',serif; font-size:30px; font-weight:900;
      color:#0d1f6e; letter-spacing:2px; text-transform:uppercase; line-height:1.1;
    }
    .cert-title-sub {
      font-family:'EB Garamond',serif; font-size:16px; font-style:italic;
      color:#444; margin-top:5px;
    }

    /* ── Name + teal underline ── */
    .name-area { text-align:center; flex-shrink:0; margin:10px 0 3px; }
    .name-script {
      font-family:'Great Vibes',cursive; font-size:54px;
      color:#111827; line-height:1;
    }
    .name-underline {
      width:320px; height:2.5px; margin:4px auto 0;
      background:linear-gradient(90deg, transparent 0%, #0891b2 8%, #06b6d4 50%, #0891b2 92%, transparent 100%);
    }

    /* ── Body text ── */
    .cert-text {
      text-align:center; flex-shrink:0;
      font-family:'EB Garamond',serif; font-size:14.5px; color:#1a1a1a;
      line-height:1.8; margin:8px 18px 0;
    }
    .cert-text p { margin-bottom:5px; }
    .cert-text .bold { font-weight:700; }
    .cert-text .navy { color:#0d1f6e; font-weight:700; }
    .cert-text .ital { font-style:italic; color:#555; }

    /* ── Footer: 3 sig blocks ── */
    .cert-footer {
      display:flex; align-items:flex-end;
      justify-content:space-between;
      flex-shrink:0; margin-top:auto; padding-top:6px;
    }
    .sig-block { text-align:center; min-width:155px; }
    .sig-space  { height:38px; }
    .sig-line   { height:1.5px; width:100%; background:#1a3d99; margin-bottom:4px; }
    .sig-title  {
      font-family:'Cinzel',serif; font-size:11px; font-weight:700;
      color:#0d1f6e; letter-spacing:.4px;
    }
    .sig-sub { font-family:'EB Garamond',serif; font-size:12.5px; font-style:italic; color:#666; }

    /* ─── Print ─── */
    @media print {
      html, body { margin:0; padding:0; background:white; }
      .toolbar { display:none !important; }
      .cert-wrap { max-width:none; }
      .cert { width:297mm !important; height:210mm !important; transform:none !important; box-shadow:none; }
      @page { size:A4 landscape; margin:0; }
    }
  </style>
</head>
<body>

<div class="toolbar">
  <button class="btn-back" onclick="history.back()">&#8592; Back</button>
  <button class="btn-print" onclick="window.print()">&#128424; Print / Save PDF</button>
</div>

<div class="cert-wrap" id="certWrap">
<div class="cert" id="cert">

  {{-- ══ CORNER WAVES — landscape 1122×794 ══ --}}
  <svg class="cert-waves" viewBox="0 0 1122 794" xmlns="http://www.w3.org/2000/svg">
    {{-- ★ TOP-LEFT group (4 layers, lightest back → darkest front) --}}
    <path d="M0,0 L398,0 C488,0 533,80 506,210 C479,340 386,413 340,472 L0,472 Z"
          fill="#4a84e0" opacity="0.42"/>
    <path d="M0,0 L348,0 C448,0 488,88 458,218 C428,348 336,421 288,472 L0,472 Z"
          fill="#2358c2" opacity="0.68"/>
    <path d="M0,0 L294,0 C402,0 438,98 406,228 C374,358 282,430 234,472 L0,472 Z"
          fill="#1545aa"/>
    <path d="M0,0 L238,0 C354,0 390,110 356,240 C322,370 232,442 184,472 L0,472 Z"
          fill="#0a2e84"/>

    {{-- ★ BOTTOM-RIGHT group (180° mirror: x→1122-x, y→794-y) --}}
    <path d="M1122,794 L724,794 C634,794 589,714 616,584 C643,454 736,381 782,322 L1122,322 Z"
          fill="#4a84e0" opacity="0.42"/>
    <path d="M1122,794 L774,794 C674,794 634,706 664,576 C694,446 786,373 834,322 L1122,322 Z"
          fill="#2358c2" opacity="0.68"/>
    <path d="M1122,794 L828,794 C720,794 684,696 716,566 C748,436 840,364 888,322 L1122,322 Z"
          fill="#1545aa"/>
    <path d="M1122,794 L884,794 C768,794 732,684 766,554 C800,424 890,352 938,322 L1122,322 Z"
          fill="#0a2e84"/>
  </svg>

  {{-- ══ DOUBLE BORDER ══ --}}
  <div class="cert-frame"></div>

  {{-- ══ CONTENT ══ --}}
  <div class="cert-body">

    {{-- Header --}}
    <div class="hdr">
      <div class="hdr-logo">
        @if(!empty($franchise->institute->logo) && $franchise->institute->logo !== 'images/default-institute.png')
          <img src="{{ asset($franchise->institute->logo) }}" alt="Logo">
        @else
          <div class="hdr-logo-ph">
            <span>{{ strtoupper(substr($franchise->institute->name, 0, 3)) }}</span>
          </div>
        @endif
      </div>

      <div class="hdr-center">
        @if(!empty($franchise->institute->short_name))
          <div class="hdr-reg">{{ $franchise->institute->short_name }} &reg;</div>
        @endif
        <div class="hdr-name">{{ $franchise->institute->name }}</div>
        <div class="hdr-details">
          Authorized Franchise Programme &nbsp;|&nbsp; ISO 9001:2015 Certified
          @if(!empty($franchise->institute->address))
            <br>{{ $franchise->institute->address }}
          @endif
        </div>
      </div>

      <div class="hdr-badge">
        <div class="hdr-badge-inner">
          <span class="badge-top">OFFICIAL</span>
          <span class="badge-mid">FP</span>
          <span class="badge-sub">FRANCHISE</span>
          <span class="badge-name">PARTNER</span>
        </div>
      </div>
    </div>

    {{-- Header divider --}}
    <div class="hdr-divider"></div>

    {{-- Title --}}
    <div class="cert-title-area">
      <div class="cert-title">Franchise Certificate</div>
      <div class="cert-title-sub">This is to certify that</div>
    </div>

    {{-- Franchise name + teal underline --}}
    <div class="name-area">
      <div class="name-script">{{ $franchise->name }}</div>
      <div class="name-underline"></div>
    </div>

    {{-- Body text --}}
    <div class="cert-text">
      <p>
        is hereby appointed as an <span class="bold">Authorised Franchise Partner</span> of
        <span class="navy">{{ strtoupper($franchise->institute->name) }}</span>.
        The franchise, operated by <span class="bold">{{ $franchise->owner_name }}</span>
        @if(!empty($franchise->address) || !empty($franchise->state))
          and located at <span class="bold">{{ trim(($franchise->address ? $franchise->address.', ' : '') . ($franchise->state ?? ''), ', ') }}</span>,
        @endif
        is authorized to promote, manage, and operate services under the official franchise programme in accordance with company policies.
      </p>
      <p>
        This certificate is valid from <span class="bold">{{ \Carbon\Carbon::parse($franchise->created_at)->format('d M Y') }}</span>
        to <span class="bold">{{ $franchise->valid_till ? \Carbon\Carbon::parse($franchise->valid_till)->format('d M Y') : \Carbon\Carbon::parse($franchise->created_at)->addYears(5)->format('d M Y') }}</span>.
      </p>
      <p class="ital" style="font-size:12.5px;color:#777;margin-top:2px">
        Certificate No: {{ $franchise->unique_id }} &nbsp;|&nbsp; Level: {{ $franchise->level?->name ?? 'Standard' }} &nbsp;|&nbsp; Issued: {{ \Carbon\Carbon::parse($franchise->created_at)->format('d M Y') }}
      </p>
    </div>

    {{-- Footer --}}
    <div class="cert-footer">
      <div class="sig-block">
        <div class="sig-space"></div>
        <div class="sig-line"></div>
        <div class="sig-title">{{ strtoupper($franchise->owner_name) }}</div>
        <div class="sig-sub">Franchise Head</div>
      </div>
      <div class="sig-block">
        <div class="sig-space"></div>
        <div class="sig-line"></div>
        <div class="sig-title">OFFICIAL SEAL</div>
        <div class="sig-sub">{{ $franchise->institute->name }}</div>
      </div>
      <div class="sig-block">
        <div class="sig-space"></div>
        <div class="sig-line"></div>
        <div class="sig-title">{{ strtoupper($franchise->institute->owner_name ?? 'DIRECTOR') }}</div>
        <div class="sig-sub">Authorised Signatory</div>
      </div>
    </div>

  </div>
</div>
</div>

<script>
  function scaleCert() {
    var wrap = document.getElementById('certWrap');
    var cert = document.getElementById('cert');
    if (!wrap || !cert) return;
    var scale = wrap.clientWidth / 1122;
    cert.style.transform = 'scale(' + scale + ')';
    cert.style.transformOrigin = 'top left';
    wrap.style.height = Math.ceil(794 * scale) + 'px';
  }
  window.addEventListener('load', scaleCert);
  window.addEventListener('resize', scaleCert);
</script>
</body>
</html>
