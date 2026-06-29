<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Receipt — {{ $fee->invoice_no }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Courier New',Courier,monospace;background:#e8ecf0;padding:12px;color:#000;font-size:12px}
.receipt{width:300px;margin:0 auto;background:#fff;padding:12px 10px}
.center{text-align:center}
.bold{font-weight:bold}
.divider-dashed{border-top:1px dashed #666;margin:7px 0}
.divider-solid{border-top:2px solid #000;margin:7px 0}
.row{display:flex;justify-content:space-between;gap:4px;margin:3px 0;font-size:12px}
.row .lbl{flex-shrink:0;color:#444}
.row .val{text-align:right;font-weight:600;word-break:break-word}
.big-amount{font-size:20px;font-weight:bold;text-align:center;margin:8px 0;letter-spacing:.04em}
.balance-row{display:flex;justify-content:space-between;font-size:12px;margin:3px 0}
.no-print{display:flex;gap:8px;justify-content:center;margin-bottom:14px}
.receipt{position:relative}
.cancelled-stamp-overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;z-index:20}
.cancelled-stamp-box{border:5px double rgba(185,28,28,.55);border-radius:8px;padding:8px 14px;color:rgba(185,28,28,.55);font-size:22pt;font-weight:900;letter-spacing:.15em;text-transform:uppercase;font-family:'Arial Black',Arial,sans-serif;transform:rotate(-22deg);white-space:nowrap;line-height:1}
.no-print button{padding:8px 20px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;border:none}
.btn-print{background:#1e40af;color:#fff}
.btn-close{background:#f1f5f9;color:#374151}
@media print{
  body{background:#fff;padding:0}
  .receipt{width:100%}
  .no-print{display:none!important}
  @page{size:80mm auto;margin:2mm 0}
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
  <button class="btn-print" onclick="window.print()">Print Thermal</button>
  <button class="btn-close" onclick="window.close()">Close</button>
</div>

<div class="receipt">
  @if($fee->isCancelled())
  <div class="cancelled-stamp-overlay">
    <div class="cancelled-stamp-box">CANCELLED</div>
  </div>
  @endif
  <div class="center bold" style="font-size:14px;line-height:1.4">{{ $institute?->name ?? 'Institute' }}</div>
  @if($institute?->address)
    <div class="center" style="font-size:10px;color:#555">{{ $institute->address }}</div>
  @endif
  @if($institute?->mobile)
    <div class="center" style="font-size:10px;color:#555">{{ $institute->mobile }}</div>
  @endif

  <div class="divider-solid"></div>
  <div class="center bold" style="font-size:13px;letter-spacing:.12em">FEE RECEIPT</div>
  <div class="divider-dashed"></div>

  <div class="row"><span class="lbl">Invoice:</span><span class="val">{{ $fee->invoice_no }}</span></div>
  <div class="row"><span class="lbl">Date:</span><span class="val">{{ $fee->date->format('d/m/Y') }}</span></div>

  <div class="divider-dashed"></div>

  <div class="row"><span class="lbl">Student:</span><span class="val">{{ $studentName }}</span></div>
  <div class="row"><span class="lbl">Mobile:</span><span class="val">{{ $courseBook->student->mobile }}</span></div>
  @if($courseBook->enrollment_no)
  <div class="row"><span class="lbl">Enroll:</span><span class="val" style="font-size:11px">{{ $courseBook->enrollment_no }}</span></div>
  @endif
  <div class="row"><span class="lbl">Course:</span><span class="val" style="max-width:170px">{{ $courseBook->course->name }}</span></div>
  @if($courseBook->batch)
  <div class="row"><span class="lbl">Batch:</span><span class="val">{{ $courseBook->batch->name }}</span></div>
  @endif

  <div class="divider-dashed"></div>

  <div class="row"><span class="lbl">Mode:</span><span class="val">{{ $fee->payment_mode }}</span></div>
  @if($fee->utr)
  <div class="row"><span class="lbl">UTR/Ref:</span><span class="val">{{ $fee->utr }}</span></div>
  @endif

  <div class="divider-solid"></div>
  <div class="center" style="font-size:11px;color:#555">AMOUNT PAID</div>
  <div class="big-amount">₹{{ number_format($thisPay, 2) }}</div>
  <div class="divider-dashed"></div>

  <div class="balance-row"><span>Total Fee:</span><span>₹{{ number_format($totalFee, 2) }}</span></div>
  <div class="balance-row"><span>Total Paid:</span><span class="bold">₹{{ number_format($totalPaid, 2) }}</span></div>
  <div class="balance-row"><span>Balance Due:</span><span class="bold">₹{{ number_format($balanceDue, 2) }}</span></div>

  @if($fee->note)
  <div class="divider-dashed"></div>
  <div style="font-size:10px;color:#444">Note: {{ $fee->note }}</div>
  @endif

  <div class="divider-solid"></div>
  <div class="center bold" style="font-size:11px;margin:4px 0">Thank You!</div>
  <div class="center" style="font-size:10px;color:#555">Keep this receipt for your records.</div>
  <div class="divider-dashed"></div>
</div>
</body>
</html>
