<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Receipt — {{ $franchise->name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; padding: 24px; }
    .no-print { text-align: center; margin-bottom: 20px; display: flex; gap: 10px; justify-content: center; }
    .no-print button { padding: 8px 24px; font-size: 13px; border-radius: 5px; border: none; cursor: pointer; font-weight: 600; }
    .btn-print { background: #1a3c6e; color: #fff; }
    .btn-back  { background: #eee; color: #333; }

    .receipt {
      max-width: 720px;
      margin: 0 auto;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
    }
    .receipt-header {
      background: linear-gradient(135deg, #1a3c6e 0%, #2a5ea8 100%);
      color: #fff;
      padding: 20px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .receipt-header-logo { width: 54px; height: 54px; object-fit: contain; border-radius: 6px; background: rgba(255,255,255,.15); }
    .receipt-header-title { font-size: 20px; font-weight: 700; letter-spacing: .3px; }
    .receipt-header-sub { font-size: 12px; opacity: .8; margin-top: 3px; }
    .receipt-header-right { text-align: right; }
    .receipt-header-inv { font-size: 18px; font-weight: 700; font-family: monospace; }
    .receipt-header-date { font-size: 12px; opacity: .8; margin-top: 3px; }

    .receipt-body { padding: 24px 28px; }
    .receipt-section-title {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: #888;
      margin-bottom: 12px;
      padding-bottom: 6px;
      border-bottom: 1px solid #f0f0f0;
    }
    .receipt-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 24px; margin-bottom: 20px; }
    .receipt-field { }
    .receipt-field-label { font-size: 11px; color: #999; margin-bottom: 2px; }
    .receipt-field-value { font-size: 13px; color: #222; font-weight: 500; }

    .receipt-amount-box {
      background: linear-gradient(135deg, #f0f7ff 0%, #e8f4ff 100%);
      border: 1.5px solid #b8d4f5;
      border-radius: 8px;
      padding: 16px 20px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .receipt-amount-label { font-size: 13px; color: #555; }
    .receipt-amount-value { font-size: 28px; font-weight: 800; color: #1a3c6e; }

    .receipt-balance { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
    .receipt-bal-item { background: #f8f8f8; border-radius: 6px; padding: 12px 16px; }
    .receipt-bal-label { font-size: 11px; color: #888; margin-bottom: 4px; }
    .receipt-bal-value { font-size: 16px; font-weight: 700; }

    .receipt-footer {
      border-top: 1px solid #f0f0f0;
      padding: 14px 28px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fafafa;
    }
    .receipt-footer-text { font-size: 11px; color: #aaa; }
    .receipt-sig { text-align: right; }
    .receipt-sig-line { width: 100px; height: 1px; background: #ccc; margin-left: auto; margin-bottom: 4px; }
    .receipt-sig-name { font-size: 11px; color: #666; font-weight: 600; }

    @media print {
      body { background: white; padding: 0; }
      .no-print { display: none !important; }
      .receipt { box-shadow: none; border: none; max-width: 100%; }
      @page { size: A5 landscape; margin: 1cm; }
    }
  </style>
</head>
<body>

<div class="no-print">
  <button class="btn-back" onclick="history.back()">← Back</button>
  <button class="btn-print" onclick="window.print()">🖨 Print Receipt</button>
</div>

<div class="receipt">
  <div class="receipt-header">
    <div>
      @if($franchise->institute->logo && $franchise->institute->logo !== 'images/default-institute.png')
        <img src="{{ asset($franchise->institute->logo) }}" alt="Logo" class="receipt-header-logo">
      @endif
      <div class="receipt-header-title" style="margin-top:6px;">{{ $franchise->institute->name }}</div>
      <div class="receipt-header-sub">Franchise Onboarding Fee Receipt</div>
    </div>
    <div class="receipt-header-right">
      <div class="receipt-header-inv">{{ $collection->invoice_no }}</div>
      <div class="receipt-header-date">{{ \Carbon\Carbon::parse($collection->date)->format('d M Y') }}</div>
    </div>
  </div>

  <div class="receipt-body">
    <div class="receipt-section-title">Franchise Details</div>
    <div class="receipt-grid" style="margin-bottom:18px;">
      <div class="receipt-field">
        <div class="receipt-field-label">Franchise Name</div>
        <div class="receipt-field-value">{{ $franchise->name }}</div>
      </div>
      <div class="receipt-field">
        <div class="receipt-field-label">Franchise ID</div>
        <div class="receipt-field-value" style="font-family:monospace;">{{ $franchise->unique_id }}</div>
      </div>
      <div class="receipt-field">
        <div class="receipt-field-label">Owner</div>
        <div class="receipt-field-value">{{ $franchise->owner_name }}</div>
      </div>
      <div class="receipt-field">
        <div class="receipt-field-label">Level</div>
        <div class="receipt-field-value">{{ $franchise->level?->name ?? 'Standard' }}</div>
      </div>
    </div>

    <div class="receipt-section-title">Payment Details</div>
    <div class="receipt-amount-box">
      <div>
        <div class="receipt-amount-label">Amount Received</div>
        @if($collection->payment_mode)
          <div style="font-size:12px; color:#666; margin-top:3px;">via {{ $collection->payment_mode }}{{ $collection->utr ? ' · ' . $collection->utr : '' }}</div>
        @endif
      </div>
      <div class="receipt-amount-value">₹{{ number_format($collection->amount, 2) }}</div>
    </div>

    <div class="receipt-balance">
      <div class="receipt-bal-item">
        <div class="receipt-bal-label">Total Onboarding Fee</div>
        <div class="receipt-bal-value" style="color:#1a3c6e;">₹{{ number_format($franchise->fee_total, 2) }}</div>
      </div>
      <div class="receipt-bal-item">
        <div class="receipt-bal-label">Total Paid (incl. this)</div>
        <div class="receipt-bal-value" style="color:#2a7a2a;">₹{{ number_format($totalPaid, 2) }}</div>
      </div>
      <div class="receipt-bal-item">
        <div class="receipt-bal-label">Outstanding Balance</div>
        <div class="receipt-bal-value" style="color:{{ $outstanding > 0 ? '#c84040' : '#2a7a2a' }};">₹{{ number_format($outstanding, 2) }}</div>
      </div>
      <div class="receipt-bal-item" style="background:#fff3cd;">
        <div class="receipt-bal-label">Status</div>
        <div class="receipt-bal-value" style="font-size:13px; color:{{ $outstanding <= 0 ? '#2a7a2a' : '#8b6520' }};">
          {{ $outstanding <= 0 ? 'Fully Paid' : 'Partially Paid' }}
        </div>
      </div>
    </div>

    @if($collection->note)
      <div style="font-size:12px; color:#888; margin-bottom:8px;">Note: {{ $collection->note }}</div>
    @endif
  </div>

  <div class="receipt-footer">
    <div class="receipt-footer-text">
      Generated: {{ now()->format('d M Y, h:i A') }} &bull; {{ $franchise->institute->name }}
    </div>
    <div class="receipt-sig">
      <div class="receipt-sig-line"></div>
      <div class="receipt-sig-name">Authorised Signatory</div>
    </div>
  </div>
</div>

</body>
</html>
