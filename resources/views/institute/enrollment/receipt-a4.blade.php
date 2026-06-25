<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt — {{ $fee->invoice_no ?? '' }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; background: #bbb; }

.no-print {
  display: flex; gap: 10px; justify-content: center; padding: 16px;
}
.no-print button {
  padding: 8px 22px; border: none; border-radius: 5px;
  cursor: pointer; font-size: 13px; font-weight: 700;
}
.btn-p { background: #111; color: #fff; }
.btn-c { background: #e5e7eb; color: #374151; }

/* A4 Portrait */
.a4 {
  width: 210mm;
  height: 297mm;
  background: #fff;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 20px rgba(0,0,0,.25);
}

/* Top half — two receipts side by side */
.top-half {
  height: 148mm;
  display: flex;
  padding: 4mm 3mm 2mm 3mm;
  gap: 0;
}

/* Dashed cut line */
.cut-line {
  width: 4mm;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  position: relative;
}
.cut-line::before {
  content: '';
  position: absolute;
  top: 0; bottom: 0;
  left: 50%;
  border-left: 1.5px dashed #777;
}
.cut-line::after {
  content: "✂";
  position: relative;
  font-size: 12px;
  color: #666;
  background: #fff;
  padding: 4px 0;
  z-index: 1;
  line-height: 1;
  transform: rotate(90deg);
  display: block;
}

/* Bottom half — blank */
.bottom-half {
  flex: 1;
  border-top: 1.5px dashed #bbb;
  position: relative;
}
.bottom-half::after {
  content: "— Blank (for next student's receipt) —";
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  font-size: 8pt;
  color: #d0d0d0;
  white-space: nowrap;
  font-family: Arial, sans-serif;
}

/* Each receipt = quarter of A4 */
.receipt {
  flex: 1;
  border: 2px solid #000;
  display: flex;
  flex-direction: column;
  font-size: 7.5pt;
  overflow: hidden;
}

/* ── Institute Header ── */
.r-head {
  padding: 3.5px 6px;
  border-bottom: 1.5px solid #000;
  display: flex;
  align-items: center;
  gap: 6px;
}
.r-head-logo {
  width: 34px; height: 34px;
  object-fit: contain;
  flex-shrink: 0;
  border: 1px solid #ddd;
}
.r-head-logo-box {
  width: 34px; height: 34px;
  border: 1.5px solid #000;
  display: flex; align-items: center; justify-content: center;
  font-size: 14pt; font-weight: 900; flex-shrink: 0;
}
.r-head-text { flex: 1; text-align: center; }
.r-head-name { font-size: 11pt; font-weight: 900; line-height: 1.2; }
.r-head-addr { font-size: 6.5pt; color: #333; margin-top: 1px; line-height: 1.5; }

/* ── FEES RECEIPT title ── */
.r-title {
  border-bottom: 1.5px solid #000;
  padding: 4px 0;
  text-align: center;
  font-size: 12pt;
  font-weight: 900;
  letter-spacing: .03em;
  text-transform: uppercase;
  background: #fff;
}

/* ── Table ── */
.rt {
  width: 100%;
  border-collapse: collapse;
  flex: 1;
}
.rt td {
  border: 1px solid #000;
  padding: 2.5px 5px;
  font-size: 7.5pt;
  vertical-align: middle;
}
.rt .lbl { font-weight: 700; white-space: nowrap; }
.rt .val { font-weight: 400; }
.rt .vb  { font-weight: 700; }

/* Header row for fee details */
.rt .det-head td {
  background: #f0f0f0;
  font-weight: 900;
  font-size: 7.5pt;
  text-align: center;
}

/* Fee/Installment row — taller */
.rt .fee-row td {
  height: 28mm;
  vertical-align: top;
  padding: 4px 5px;
  font-size: 8pt;
}

/* Amount paid — shaded bold */
.rt .amt-row td {
  background: #c8c8c8;
  font-weight: 900;
  font-size: 9.5pt;
}
.rt .amt-row .amt-val {
  text-align: right;
  padding-right: 7px;
  font-size: 11pt;
  letter-spacing: .01em;
}

/* Summary rows */
.rt .sum-row td { font-size: 7.5pt; }
.rt .sum-row .sv { text-align: right; padding-right: 7px; font-weight: 700; }
.rt .sum-row.balance td { font-weight: 900; }

/* Amount in words */
.rt .words-row td {
  font-size: 7.5pt;
  font-weight: 700;
  font-style: italic;
  border-top: 1.5px solid #000;
  padding: 3px 5px;
}

/* Signature row */
.rt .sign-row td {
  height: 20mm;
  vertical-align: bottom;
  padding: 3px 5px;
  border-top: 1.5px solid #000;
}
.sign-cell-inner {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}
.sign-box {
  text-align: center;
  font-size: 6.5pt;
}
.sign-img {
  height: 22px;
  max-width: 65px;
  object-fit: contain;
  display: block;
  margin: 0 auto 2px;
}
.sign-line {
  border-top: 1px solid #000;
  width: 70px;
  margin: 0 auto 2px;
}

/* Copy label */
.r-copy {
  border-top: 1.5px solid #000;
  padding: 2px 6px;
  font-size: 6.5pt;
  font-weight: 900;
  letter-spacing: .15em;
  text-transform: uppercase;
  text-align: right;
  background: #f8f8f8;
}

@media print {
  body { background: #fff; }
  .no-print { display: none !important; }
  .a4 { box-shadow: none; margin: 0; }
  .bottom-half::after { display: none; }
  @page { size: A4 portrait; margin: 0; }
}
</style>
</head>
<body>
@php
  $amtCol  = \App\Models\FeeCollectDetail::amountColumn();
  $thisPay = (float) $fee->{$amtCol};
  $totPaid = (float) \App\Models\FeeCollectDetail::where('course_book_id', $courseBook->id)
                 ->whereNull('cancelled_at')->sum($amtCol);
  $totFee  = (float) $courseBook->final_fee;
  $balance = max($totFee - $totPaid, 0);

  $profile = $courseBook->student->profile;
  $sName   = $profile?->name   ?? $courseBook->student->user_id;
  $fName   = $profile?->father_name ?? '';
  $mobile  = $courseBook->student->mobile ?? '';

  $iName   = $institute?->name    ?? 'Institute';
  $iAddr   = $institute?->address ?? '';
  $iCity   = $institute?->city    ?? '';
  $iMob    = $institute?->mobile  ?? '';
  $iLogo   = ($institute?->logo && !str_contains($institute->logo ?? '', 'default')) ? $institute->logo : null;

  $useStamp = $institute?->use_stamp   && $institute?->stamp;
  $useSig   = $institute?->use_signature && $institute?->signature;

  // Collected by: staff name or institute name
  $collectorName = $iName;
  if ($fee->received_by) {
      try {
          $collectorUser = \App\Models\User::find($fee->received_by);
          $collectorName = ($collectorUser && $collectorUser->role !== 'institute_head')
              ? ($collectorUser->profile?->name ?? $collectorUser->user_id ?? $iName)
              : $iName;
      } catch (\Throwable) {
          $collectorName = $iName;
      }
  }

  // Amount in words
  $toWords = function(float $amount) use (&$toWords): string {
      $n = (int) round($amount);
      if ($n === 0) return 'Zero Rupees Only';
      $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
               'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
               'Seventeen','Eighteen','Nineteen'];
      $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
      $conv = function(int $n) use (&$conv, $ones, $tens): string {
          if ($n === 0)   return '';
          if ($n < 20)    return $ones[$n] . ' ';
          if ($n < 100)   return $tens[intdiv($n,10)] . ' ' . ($n%10 ? $ones[$n%10].' ' : '');
          if ($n < 1000)  return $ones[intdiv($n,100)] . ' Hundred ' . $conv($n % 100);
          if ($n < 100000)   return $conv(intdiv($n,1000))    . 'Thousand '  . $conv($n % 1000);
          if ($n < 10000000) return $conv(intdiv($n,100000))  . 'Lakh '      . $conv($n % 100000);
          return                     $conv(intdiv($n,10000000)) . 'Crore '   . $conv($n % 10000000);
      };
      return 'Rupees ' . trim($conv($n)) . ' Only';
  };
@endphp

<div class="no-print">
  <button class="btn-p" onclick="window.print()">Print / Save PDF</button>
  <button class="btn-c" onclick="window.close()">Close</button>
</div>

<div class="a4">
  <div class="top-half">

    @foreach(['OFFICE COPY','STUDENT COPY'] as $copy)
      @if(!$loop->first)<div class="cut-line"></div>@endif

      <div class="receipt">

        {{-- Institute Header --}}
        <div class="r-head">
          @if($iLogo)
            <img src="{{ asset($iLogo) }}" class="r-head-logo" alt="">
          @else
            <div class="r-head-logo-box">{{ strtoupper(substr($iName,0,1)) }}</div>
          @endif
          <div class="r-head-text">
            <div class="r-head-name">{{ $iName }}</div>
            <div class="r-head-addr">
              {{ implode('', array_filter([$iAddr, $iCity], fn($v) => $v !== '')) }}
            </div>
            @if($iMob)<div class="r-head-addr">Ph. : {{ $iMob }}</div>@endif
          </div>
        </div>

        {{-- Title --}}
        <div class="r-title">Fees Receipt</div>

        {{-- All rows in one table --}}
        <table class="rt">
          <colgroup>
            <col style="width:28%"><col style="width:21%"><col style="width:15%"><col style="width:15%"><col style="width:21%">
          </colgroup>

          <tr>
            <td class="lbl">Receipt No.</td>
            <td class="vb" colspan="2">{{ $fee->invoice_no }}</td>
            <td class="lbl">Date :</td>
            <td class="vb">{{ $fee->date->format('d-m-Y') }}</td>
          </tr>
          <tr>
            <td class="lbl">Enroll. No.</td>
            <td class="val" colspan="4">{{ $courseBook->enrollment_no ?? 'Pending' }}</td>
          </tr>
          <tr>
            <td class="lbl">Student Name</td>
            <td class="vb" colspan="4">{{ $sName }}</td>
          </tr>
          @if($fName)
          <tr>
            <td class="lbl">Father's Name</td>
            <td class="val" colspan="4">{{ $fName }}</td>
          </tr>
          @endif
          <tr>
            <td class="lbl">Mobile</td>
            <td class="val">{{ $mobile }}</td>
            <td class="lbl">Pay Mode</td>
            <td class="vb" colspan="2">{{ $fee->payment_mode }}</td>
          </tr>
          <tr>
            <td class="lbl">Course</td>
            <td class="val" colspan="2">{{ $courseBook->course->name }}</td>
            <td class="lbl">Batch</td>
            <td class="val">{{ $courseBook->batch?->name ?? '—' }}</td>
          </tr>
          @if($fee->utr)
          <tr>
            <td class="lbl">UTR / Ref.</td>
            <td class="val" colspan="4">{{ $fee->utr }}</td>
          </tr>
          @endif

          {{-- Fee Details Header --}}
          <tr class="det-head">
            <td colspan="4" style="text-align:left;padding-left:6px">Student's Fee Details</td>
            <td>Amount (₹)</td>
          </tr>

          {{-- Fee / Installment row — tall --}}
          <tr class="fee-row">
            <td colspan="4">{{ $fee->note ?: 'Fee / Installment' }}</td>
            <td style="text-align:right;padding-right:7px;vertical-align:top">
              {{ number_format($thisPay, 2) }}
            </td>
          </tr>

          {{-- Amount Paid shaded --}}
          <tr class="amt-row">
            <td class="lbl" colspan="4" style="padding-left:6px">Amount Paid (This Receipt)</td>
            <td class="amt-val">{{ number_format($thisPay, 2) }}</td>
          </tr>

          {{-- Summary --}}
          <tr class="sum-row">
            <td class="lbl" colspan="4">Total Fee</td>
            <td class="sv">{{ number_format($totFee, 2) }}</td>
          </tr>
          <tr class="sum-row">
            <td class="lbl" colspan="4">Paid Fee</td>
            <td class="sv">{{ number_format($totPaid, 2) }}</td>
          </tr>
          <tr class="sum-row balance">
            <td class="lbl" colspan="4">Balance Fee</td>
            <td class="sv">{{ number_format($balance, 2) }}</td>
          </tr>

          {{-- Amount in Words --}}
          <tr class="words-row">
            <td colspan="5">{{ $toWords($thisPay) }}</td>
          </tr>

          {{-- Signature / Stamp row --}}
          <tr class="sign-row">
            <td colspan="5">
              <div class="sign-cell-inner">
                <div class="sign-box">
                  <div class="sign-line"></div>
                  <div>Collected by: {{ $collectorName }}</div>
                </div>
                <div class="sign-box">
                  @if($useStamp)
                    <img src="{{ asset($institute->stamp) }}" class="sign-img" alt="stamp">
                  @endif
                  @if($useSig)
                    <img src="{{ asset($institute->signature) }}" class="sign-img" alt="signature">
                  @endif
                  <div class="sign-line"></div>
                  <div>Authorised Signatory</div>
                </div>
              </div>
            </td>
          </tr>

        </table>

        {{-- Copy label --}}
        <div class="r-copy">{{ $copy }}</div>

      </div>{{-- /receipt --}}
    @endforeach

  </div>{{-- /top-half --}}

  <div class="bottom-half"></div>
</div>

</body>
</html>
