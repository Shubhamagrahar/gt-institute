<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Receipt — {{ $fee->invoice_no }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',Arial,sans-serif;background:#e8ecf0;padding:24px;color:#1f2937}
.receipt-wrap{max-width:780px;margin:0 auto;background:#fff;box-shadow:0 4px 28px rgba(0,0,0,.12);border-radius:4px;overflow:hidden}
.r-header{background:linear-gradient(135deg,#1b1464,#2980b9);color:#fff;padding:24px 32px;display:flex;gap:18px;align-items:center}
.r-logo{width:60px;height:60px;border-radius:10px;object-fit:contain;background:#fff;padding:4px;flex-shrink:0}
.r-logo-placeholder{width:60px;height:60px;border-radius:10px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;flex-shrink:0}
.r-inst-name{font-size:20px;font-weight:800;line-height:1.2}
.r-inst-detail{font-size:12px;opacity:.82;margin-top:4px;line-height:1.6}
.r-title-bar{background:#f8faff;border-bottom:2px solid #e2e8f0;padding:12px 32px;display:flex;justify-content:space-between;align-items:center}
.r-title{font-size:19px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#1e40af}
.r-inv{font-size:13px;color:#475569;text-align:right;line-height:1.8}
.r-body{padding:26px 32px}
.r-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;border-bottom:1px dashed #e2e8f0;padding-bottom:22px}
.r-block-title{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:9px;padding-bottom:5px;border-bottom:1px solid #f1f5f9}
.r-row{display:flex;justify-content:space-between;gap:12px;padding:5px 0;font-size:13px;border-bottom:1px solid #f8faff}
.r-row .lbl{color:#64748b;flex-shrink:0}
.r-row .val{font-weight:600;text-align:right}
.r-amount-box{background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #bfdbfe;border-radius:12px;padding:16px 22px;display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.r-amount-label{font-size:14px;color:#1e40af;font-weight:700}
.r-amount-value{font-size:30px;font-weight:900;color:#1e3a8a}
.r-balance-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px}
.r-balance-item{border:1px solid #e2e8f0;border-radius:10px;padding:11px;background:#f8faff;text-align:center}
.r-balance-item .bi-lbl{font-size:11px;color:#94a3b8;text-transform:uppercase;font-weight:700;margin-bottom:3px}
.r-balance-item .bi-val{font-size:15px;font-weight:800;color:#1f2937}
.r-balance-item.due .bi-val{color:#dc2626}
.r-balance-item.paid .bi-val{color:#16a34a}
.r-note{background:#fefce8;border:1px solid #fde047;border-radius:8px;padding:9px 14px;font-size:13px;color:#713f12;margin-bottom:18px}
.r-footer{border-top:1px solid #e2e8f0;margin:0 32px;padding:16px 0 24px;display:flex;justify-content:space-between;align-items:flex-end;font-size:12px;color:#94a3b8}
.r-sign-line{width:140px;border-top:1px solid #cbd5e1;padding-top:6px;margin-top:28px;text-align:center;font-size:11px;color:#64748b}
.no-print{display:flex;gap:10px;justify-content:center;margin-bottom:20px}
.no-print button{padding:10px 24px;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;border:none}
.btn-print{background:#1e40af;color:#fff}
.btn-close{background:#f1f5f9;color:#374151}
@media print{
  body{background:#fff;padding:0}
  .receipt-wrap{box-shadow:none;max-width:100%;border-radius:0}
  .no-print{display:none!important}
  @page{size:A4;margin:12mm}
}
</style>
</head>
<body>
@php
$amountColumn = \App\Models\FeeCollectDetail::amountColumn();
$thisPay = (float) $fee->{$amountColumn};
$totalPaid = (float) \App\Models\FeeCollectDetail::where('course_book_id', $courseBook->id)->sum($amountColumn);
$totalFee = (float) $courseBook->final_fee;
$balanceDue = max($totalFee - $totalPaid, 0);
$studentName = $courseBook->student->profile?->name ?? $courseBook->student->user_id;
@endphp

<div class="no-print">
  <button class="btn-print" onclick="window.print()">Print / Save PDF</button>
  <button class="btn-close" onclick="window.close()">Close</button>
</div>

<div class="receipt-wrap">
  {{-- Header --}}
  <div class="r-header">
    @if($institute?->logo)
      <img src="{{ asset($institute->logo) }}" class="r-logo" alt="logo">
    @else
      <div class="r-logo-placeholder">{{ strtoupper(substr($institute?->name ?? 'I', 0, 1)) }}</div>
    @endif
    <div style="flex:1">
      <div class="r-inst-name">{{ $institute?->name ?? 'Institute' }}</div>
      <div class="r-inst-detail">
        @if($institute?->address){{ $institute->address }}@endif
        @if($institute?->mobile) &nbsp;|&nbsp; {{ $institute->mobile }}@endif
        @if($institute?->email) &nbsp;|&nbsp; {{ $institute->email }}@endif
      </div>
    </div>
    @if($institute?->unique_id)
    <div style="text-align:right;font-size:12px;opacity:.8">
      <div>Reg. ID</div>
      <div style="font-weight:700;">{{ $institute->unique_id }}</div>
    </div>
    @endif
  </div>

  {{-- Title bar --}}
  <div class="r-title-bar">
    <div class="r-title">Fee Receipt</div>
    <div class="r-inv">
      <div><strong>Invoice No:</strong> {{ $fee->invoice_no }}</div>
      <div><strong>Date:</strong> {{ $fee->date->format('d F Y') }}</div>
    </div>
  </div>

  <div class="r-body">
    {{-- Student + Payment Info --}}
    <div class="r-info-grid">
      <div>
        <div class="r-block-title">Student Information</div>
        <div class="r-row"><span class="lbl">Name</span><span class="val">{{ $studentName }}</span></div>
        <div class="r-row"><span class="lbl">Mobile</span><span class="val">{{ $courseBook->student->mobile }}</span></div>
        @if($courseBook->student->email)
        <div class="r-row"><span class="lbl">Email</span><span class="val">{{ $courseBook->student->email }}</span></div>
        @endif
        <div class="r-row"><span class="lbl">Enrollment No.</span><span class="val">{{ $courseBook->enrollment_no ?? 'Pending' }}</span></div>
      </div>
      <div>
        <div class="r-block-title">Course & Payment Details</div>
        <div class="r-row"><span class="lbl">Course</span><span class="val">{{ $courseBook->course->name }}</span></div>
        @if($courseBook->batch)
        <div class="r-row"><span class="lbl">Batch</span><span class="val">{{ $courseBook->batch->name }}</span></div>
        @endif
        <div class="r-row"><span class="lbl">Payment Mode</span><span class="val">{{ $fee->payment_mode }}</span></div>
        @if($fee->utr)
        <div class="r-row"><span class="lbl">UTR / Reference</span><span class="val">{{ $fee->utr }}</span></div>
        @endif
      </div>
    </div>

    {{-- Amount paid this receipt --}}
    <div class="r-amount-box">
      <div class="r-amount-label">Amount Paid (This Receipt)</div>
      <div class="r-amount-value">₹{{ number_format($thisPay, 2) }}</div>
    </div>

    {{-- Balance grid --}}
    <div class="r-balance-grid">
      <div class="r-balance-item">
        <div class="bi-lbl">Course Fee</div>
        <div class="bi-val">₹{{ number_format($totalFee, 2) }}</div>
      </div>
      <div class="r-balance-item paid">
        <div class="bi-lbl">Total Paid</div>
        <div class="bi-val">₹{{ number_format($totalPaid, 2) }}</div>
      </div>
      <div class="r-balance-item {{ $balanceDue > 0 ? 'due' : '' }}">
        <div class="bi-lbl">Balance Due</div>
        <div class="bi-val">₹{{ number_format($balanceDue, 2) }}</div>
      </div>
    </div>

    @if($fee->note)
    <div class="r-note"><strong>Note:</strong> {{ $fee->note }}</div>
    @endif
  </div>

  {{-- Footer --}}
  <div class="r-footer">
    <div style="line-height:1.7">
      This is a computer-generated receipt. No signature required.<br>
      For queries, contact the institute.
    </div>
    <div>
      <div class="r-sign-line">Authorized Signatory</div>
    </div>
  </div>
</div>
</body>
</html>
