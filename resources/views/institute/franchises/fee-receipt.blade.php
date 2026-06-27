<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Receipt — {{ $payment->invoice_no }}</title>
  <style>
    /* ── Reset ─────────────────────────────────────── */
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI', Arial, sans-serif; background:#e8e8e8; padding:28px 16px; font-size:13px; color:#111; }

    /* ── Toolbar ────────────────────────────────────── */
    .toolbar {
      display:flex; align-items:center; gap:10px; justify-content:center;
      margin-bottom:22px;
    }
    .btn-tool {
      padding:8px 20px; font-size:13px; font-weight:600; border-radius:5px;
      border:1.5px solid #ccc; cursor:pointer; background:#fff; color:#333;
      transition:background .15s;
    }
    .btn-tool:hover { background:#f0f0f0; }
    .btn-tool.primary { background:#1a3c6e; color:#fff; border-color:#1a3c6e; }
    .btn-tool.primary:hover { background:#15326a; }
    .btn-tool.thermal { background:#444; color:#fff; border-color:#333; }
    .btn-tool.thermal:hover { background:#333; }
    .mode-label {
      font-size:11.5px; color:#888; padding:0 6px;
    }
    #mode-indicator { font-weight:700; color:#1a3c6e; }

    /* ── A4 Receipt ─────────────────────────────────── */
    .receipt-wrap {
      max-width: 750px;
      margin: 0 auto;
    }

    .receipt {
      background:#fff;
      border:1px solid #c8c8c8;
      border-radius:4px;
      box-shadow:0 2px 12px rgba(0,0,0,.12);
    }

    /* Header band */
    .rec-header {
      padding:22px 30px 18px;
      border-bottom:3px solid #1a3c6e;
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:20px;
    }
    .rec-header-left { display:flex; align-items:flex-start; gap:14px; }
    .rec-logo {
      width:58px; height:58px; object-fit:contain; border:1px solid #e0e0e0; border-radius:4px;
    }
    .rec-inst-name { font-size:17px; font-weight:800; color:#1a3c6e; line-height:1.2; }
    .rec-inst-sub { font-size:11.5px; color:#666; margin-top:4px; }
    .rec-header-right { text-align:right; flex-shrink:0; }
    .rec-doc-type {
      font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase;
      color:#888; margin-bottom:5px;
    }
    .rec-inv-no { font-size:20px; font-weight:800; font-family:'Courier New',monospace; color:#1a3c6e; }
    .rec-date { font-size:12px; color:#555; margin-top:4px; }

    /* Watermark for cancelled */
    .rec-watermark {
      background:#fff3cd; border:1px solid #f0b400;
      color:#7a5000; font-size:12px; font-weight:700; letter-spacing:.5px;
      text-align:center; padding:6px;
    }

    /* Body */
    .rec-body { padding:22px 30px; }

    .rec-section-title {
      font-size:10px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase;
      color:#888; padding-bottom:7px; border-bottom:1px solid #e8e8e8;
      margin-bottom:14px; margin-top:20px;
    }
    .rec-section-title:first-child { margin-top:0; }

    /* 2-col info grid */
    .rec-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px 30px; margin-bottom:18px; }
    .rec-field-label { font-size:10.5px; color:#888; margin-bottom:2px; text-transform:uppercase; letter-spacing:.5px; }
    .rec-field-value { font-size:13px; color:#111; font-weight:500; }

    /* Amount box */
    .rec-amount-box {
      border:1.5px solid #1a3c6e;
      border-radius:4px;
      padding:14px 20px;
      display:flex; justify-content:space-between; align-items:center;
      margin-bottom:18px;
      background:#f8faff;
    }
    .rec-amount-label { font-size:12px; color:#444; line-height:1.5; }
    .rec-amount-value { font-size:30px; font-weight:900; color:#1a3c6e; font-family:'Courier New',monospace; }

    /* Balance row */
    .rec-balance-row {
      display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:18px;
    }
    .rec-bal-cell {
      border:1px solid #e0e0e0; border-radius:3px;
      padding:10px 14px;
    }
    .rec-bal-label { font-size:10px; color:#888; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
    .rec-bal-value { font-size:16px; font-weight:700; color:#111; font-family:'Courier New',monospace; }
    .rec-bal-value.due    { color:#b72020; }
    .rec-bal-value.clear  { color:#2a7a2a; }
    .rec-bal-value.accent { color:#1a3c6e; }

    /* Status pill */
    .rec-status { font-size:11px; font-weight:700; letter-spacing:.5px; }

    /* Note */
    .rec-note { font-size:12px; color:#666; border-left:3px solid #ddd; padding-left:10px; margin-top:14px; }

    /* Footer */
    .rec-footer {
      border-top:1px solid #e0e0e0;
      padding:16px 30px;
      display:flex; justify-content:space-between; align-items:flex-end;
      background:#f9f9f9;
    }
    .rec-footer-text { font-size:10.5px; color:#aaa; line-height:1.7; }
    .rec-sig-col { text-align:right; }
    .rec-sig-line { width:120px; height:1px; background:#aaa; margin-left:auto; margin-bottom:6px; }
    .rec-sig-label { font-size:10.5px; color:#666; font-weight:600; }

    /* ── THERMAL overrides ──────────────────────────── */
    body.thermal .receipt-wrap { max-width:308px; }
    body.thermal .receipt { border-radius:0; border:none; box-shadow:none; }
    body.thermal .rec-header {
      flex-direction:column; align-items:center; text-align:center;
      padding:14px 14px 10px; border-bottom:1px dashed #333;
    }
    body.thermal .rec-header-left { flex-direction:column; align-items:center; gap:8px; }
    body.thermal .rec-logo { width:44px; height:44px; }
    body.thermal .rec-inst-name { font-size:14px; }
    body.thermal .rec-header-right { text-align:center; margin-top:8px; border-top:1px dashed #ccc; padding-top:8px; width:100%; }
    body.thermal .rec-inv-no { font-size:15px; }
    body.thermal .rec-body { padding:10px 14px; }
    body.thermal .rec-section-title { font-size:9px; margin-top:12px; border-bottom-style:dashed; }
    body.thermal .rec-grid { grid-template-columns:1fr 1fr; gap:8px 12px; }
    body.thermal .rec-field-label { font-size:9.5px; }
    body.thermal .rec-field-value { font-size:11.5px; }
    body.thermal .rec-amount-box {
      flex-direction:column; align-items:center; text-align:center; gap:4px;
      border-style:dashed; padding:10px; background:#fff;
    }
    body.thermal .rec-amount-value { font-size:24px; }
    body.thermal .rec-balance-row { grid-template-columns:1fr 1fr; gap:8px; }
    body.thermal .rec-bal-cell { padding:7px 10px; }
    body.thermal .rec-bal-label { font-size:9px; }
    body.thermal .rec-bal-value { font-size:13px; }
    body.thermal .rec-footer {
      flex-direction:column; align-items:center; text-align:center; gap:12px;
      padding:10px 14px; border-top-style:dashed;
    }
    body.thermal .rec-sig-col { text-align:center; }
    body.thermal .rec-sig-line { margin:0 auto 6px; }

    /* ── PRINT ──────────────────────────────────────── */
    @media print {
      body { background:#fff; padding:0; }
      .toolbar { display:none !important; }
      .receipt { box-shadow:none; border:none; }
      .receipt-wrap { max-width:100%; }
    }
    @media print {
      body:not(.thermal) { }
      body:not(.thermal) @page { size:A4 portrait; margin:1.5cm 2cm; }
    }
    @media print {
      body.thermal @page { size:80mm auto; margin:4mm; }
      body.thermal .receipt-wrap { max-width:72mm; }
    }
  </style>
</head>
<body>

<div class="toolbar no-print">
  <button class="btn-tool" onclick="history.back()">← Back</button>
  <span class="mode-label">Mode: <span id="mode-indicator">A4</span></span>
  <button class="btn-tool primary" onclick="printA4()">🖨 Print A4</button>
  <button class="btn-tool thermal" onclick="printThermal()">🧾 Print Thermal (80mm)</button>
</div>

<div class="receipt-wrap">
<div class="receipt">

  {{-- Header --}}
  <div class="rec-header">
    <div class="rec-header-left">
      @if(!empty($franchise->institute->logo) && $franchise->institute->logo !== 'images/default-institute.png')
        <img src="{{ asset($franchise->institute->logo) }}" alt="Logo" class="rec-logo">
      @endif
      <div>
        <div class="rec-inst-name">{{ $franchise->institute->name }}</div>
        <div class="rec-inst-sub">Franchise Onboarding Fee Receipt</div>
        @if(!empty($franchise->institute->address))
          <div class="rec-inst-sub" style="margin-top:3px;">{{ $franchise->institute->address }}</div>
        @endif
      </div>
    </div>
    <div class="rec-header-right">
      <div class="rec-doc-type">Receipt</div>
      <div class="rec-inv-no">{{ $payment->invoice_no }}</div>
      <div class="rec-date">{{ \Carbon\Carbon::parse($payment->date)->format('d F Y') }}</div>
    </div>
  </div>

  @if($payment->cancelled_at)
    <div class="rec-watermark">⚠ CANCELLED — {{ \Carbon\Carbon::parse($payment->cancelled_at)->format('d M Y') }}{{ $payment->cancel_reason ? ' · ' . $payment->cancel_reason : '' }}</div>
  @endif

  <div class="rec-body">

    {{-- Franchise Info --}}
    <div class="rec-section-title">Franchise Details</div>
    <div class="rec-grid">
      <div>
        <div class="rec-field-label">Franchise Name</div>
        <div class="rec-field-value">{{ $franchise->name }}</div>
      </div>
      <div>
        <div class="rec-field-label">Franchise ID</div>
        <div class="rec-field-value" style="font-family:'Courier New',monospace;">{{ $franchise->unique_id }}</div>
      </div>
      <div>
        <div class="rec-field-label">Owner</div>
        <div class="rec-field-value">{{ $franchise->owner_name }}</div>
      </div>
      <div>
        <div class="rec-field-label">Level</div>
        <div class="rec-field-value">{{ $franchise->level?->name ?? 'Standard' }}</div>
      </div>
      @if($franchise->mobile)
      <div>
        <div class="rec-field-label">Mobile</div>
        <div class="rec-field-value">{{ $franchise->mobile }}</div>
      </div>
      @endif
      @if($franchise->address)
      <div>
        <div class="rec-field-label">Address</div>
        <div class="rec-field-value">{{ $franchise->address }}</div>
      </div>
      @endif
    </div>

    {{-- Payment --}}
    <div class="rec-section-title">Payment Details</div>

    <div class="rec-amount-box">
      <div class="rec-amount-label">
        <strong>Amount Received</strong><br>
        <span style="font-size:11.5px; color:#555;">
          {{ $payment->payment_mode ?? 'Cash' }}
          @if($payment->utr) &nbsp;·&nbsp; Ref: {{ $payment->utr }} @endif
        </span>
      </div>
      <div class="rec-amount-value">₹{{ number_format($payment->amount, 2) }}</div>
    </div>

    <div class="rec-balance-row">
      <div class="rec-bal-cell">
        <div class="rec-bal-label">Joining Fee (Total)</div>
        <div class="rec-bal-value accent">₹{{ number_format($franchise->fee_total, 2) }}</div>
      </div>
      <div class="rec-bal-cell">
        <div class="rec-bal-label">Total Paid</div>
        <div class="rec-bal-value clear">₹{{ number_format($totalPaid, 2) }}</div>
      </div>
      <div class="rec-bal-cell">
        <div class="rec-bal-label">Outstanding</div>
        <div class="rec-bal-value {{ $outstanding > 0 ? 'due' : 'clear' }}">₹{{ number_format($outstanding, 2) }}</div>
      </div>
    </div>

    <div style="display:flex; gap:14px; align-items:center;">
      <div>
        <span style="font-size:10.5px; color:#888; text-transform:uppercase; letter-spacing:.5px;">Status:</span>
        <span class="rec-status" style="color:{{ $outstanding <= 0 ? '#2a7a2a' : '#8b6520' }}; margin-left:4px;">
          {{ $outstanding <= 0 ? 'FULLY PAID' : 'PARTIALLY PAID' }}
        </span>
      </div>
      @if($payment->note)
      <div class="rec-note">Note: {{ $payment->note }}</div>
      @endif
    </div>

  </div>

  {{-- Footer --}}
  <div class="rec-footer">
    <div class="rec-footer-text">
      Generated: {{ now()->format('d M Y, h:i A') }}<br>
      {{ $franchise->institute->name }}
    </div>
    <div class="rec-sig-col">
      <div class="rec-sig-line"></div>
      <div class="rec-sig-label">Authorised Signatory</div>
    </div>
  </div>

</div>
</div>

<script>
function printA4() {
  document.body.classList.remove('thermal');
  document.getElementById('mode-indicator').textContent = 'A4';
  setTimeout(() => window.print(), 80);
}
function printThermal() {
  document.body.classList.add('thermal');
  document.getElementById('mode-indicator').textContent = 'Thermal 80mm';
  setTimeout(() => { window.print(); }, 80);
}
</script>
</body>
</html>
