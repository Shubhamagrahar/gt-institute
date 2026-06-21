<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Salary Slip — {{ $profile?->name }} — {{ $month->format('F Y') }}</title>
<style>
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:'Segoe UI',Arial,sans-serif; font-size:13px; color:#111; background:#f5f5f5; }

  @media print {
    body { background:#fff; }
    .no-print { display:none !important; }
    .slip { box-shadow:none !important; }
    @page { size:A4; margin:16mm; }
  }

  .print-btn {
    display:block; margin:16px auto 0; padding:10px 28px;
    background:#6c5dd3; color:#fff; border:none; border-radius:8px;
    font-size:13px; font-weight:700; cursor:pointer; letter-spacing:.02em;
  }
  .slip {
    max-width:680px; margin:24px auto;
    background:#fff; border-radius:12px;
    box-shadow:0 4px 24px rgba(0,0,0,.1);
    overflow:hidden;
  }
  .slip-header {
    background:linear-gradient(135deg,#6c5dd3,#4f46e5);
    padding:24px 32px; color:#fff;
    display:flex; align-items:center; justify-content:space-between;
  }
  .institute-name { font-size:18px; font-weight:800; letter-spacing:.01em; }
  .slip-title { font-size:12px; opacity:.75; margin-top:4px; }
  .slip-month { font-size:15px; font-weight:700; opacity:.9; text-align:right; }

  .slip-body { padding:24px 32px; }
  .section-title {
    font-size:10px; font-weight:800; color:#999; text-transform:uppercase;
    letter-spacing:.09em; margin:20px 0 10px; padding-bottom:6px;
    border-bottom:1.5px solid #f0f0f0;
  }
  .section-title:first-child { margin-top:0; }
  .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px 24px; }
  .info-item .label { font-size:11px; color:#888; margin-bottom:2px; }
  .info-item .value { font-size:13px; font-weight:600; color:#111; }
  .info-item .value.mono { font-family:monospace; letter-spacing:.05em; }

  .earn-table { width:100%; border-collapse:collapse; }
  .earn-table td { padding:8px 0; border-bottom:1px solid #f0f0f0; }
  .earn-table tr:last-child td { border-bottom:none; }
  .earn-table .amount { text-align:right; font-weight:700; }
  .earn-table .deduct { color:#ef4444; }
  .earn-table .total-row td { padding-top:12px; font-size:15px; font-weight:800; color:#6c5dd3; }

  .att-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; }
  .att-box { background:#f8f8f8; border-radius:8px; padding:10px; text-align:center; }
  .att-box .num { font-size:18px; font-weight:800; color:#111; }
  .att-box .lbl { font-size:10px; color:#888; margin-top:2px; }

  .pay-table { width:100%; border-collapse:collapse; }
  .pay-table th { text-align:left; font-size:10px; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:.06em; padding:6px 0; border-bottom:1.5px solid #f0f0f0; }
  .pay-table td { padding:8px 0; border-bottom:1px solid #f9f9f9; font-size:12px; }
  .pay-table td:last-child { text-align:right; font-weight:600; }

  .total-summary { display:flex; justify-content:space-between; align-items:center; margin-top:16px; padding:14px 18px; background:#f3f0ff; border-radius:10px; }
  .total-summary .ts-label { font-size:13px; font-weight:600; color:#4f46e5; }
  .total-summary .ts-value { font-size:20px; font-weight:900; color:#4f46e5; }

  .slip-footer { padding:14px 32px; background:#fafafa; border-top:1.5px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center; }
  .slip-footer .footer-note { font-size:11px; color:#aaa; }
  .status-badge { font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px;
    {{ $record->status==='paid' ? 'background:#d1fae5;color:#065f46' : ($record->status==='partial' ? 'background:#fef3c7;color:#92400e' : 'background:#fee2e2;color:#991b1b') }} }
</style>
</head>
<body>

<div class="no-print" style="text-align:center;padding:8px 0 0">
  <button class="print-btn" onclick="window.print()">Print / Download PDF</button>
  <a href="{{ route('institute.staff.salary', $staff) }}" style="display:block;margin:8px auto 0;font-size:12px;color:#888;text-decoration:none">← Back to Salary</a>
</div>

<div class="slip">
  {{-- Header --}}
  <div class="slip-header">
    <div>
      <div class="institute-name">{{ $institute->name }}</div>
      <div class="slip-title">SALARY SLIP</div>
    </div>
    <div>
      <div class="slip-month">{{ $month->format('F Y') }}</div>
      <div style="font-size:11px;opacity:.7;text-align:right;margin-top:4px">Generated {{ now()->format('d M Y') }}</div>
    </div>
  </div>

  <div class="slip-body">

    {{-- Employee details --}}
    <div class="section-title">Employee Details</div>
    <div class="info-grid">
      <div class="info-item"><div class="label">Employee Name</div><div class="value">{{ $profile?->name }}</div></div>
      <div class="info-item"><div class="label">Staff ID</div><div class="value mono">{{ $staff->user_id }}</div></div>
      <div class="info-item"><div class="label">Designation</div><div class="value">{{ $profile?->designation ?? ($role?->name ?? '—') }}</div></div>
      <div class="info-item"><div class="label">Department</div><div class="value">{{ $profile?->department ?? '—' }}</div></div>
      <div class="info-item"><div class="label">Mobile</div><div class="value mono">{{ $staff->mobile }}</div></div>
      <div class="info-item"><div class="label">Joining Date</div><div class="value">{{ $profile?->joining_date?->format('d M Y') ?? '—' }}</div></div>
    </div>

    {{-- Earnings & Deductions --}}
    <div class="section-title">Earnings & Deductions</div>
    <table class="earn-table">
      <tr><td style="color:#666">Basic Salary</td><td class="amount">₹{{ number_format($suggestion['monthlySalary']) }}</td></tr>
      @if($suggestion['shortfall'] > 0)
      <tr>
        <td style="color:#666">Leave Deduction <span style="font-size:11px;color:#aaa">({{ $suggestion['shortfall'] }} day{{ $suggestion['shortfall']!=1?'s':'' }} × ₹{{ number_format($suggestion['perDay'],2) }})</span></td>
        <td class="amount deduct">–₹{{ number_format($suggestion['deduction'],2) }}</td>
      </tr>
      @endif
      <tr class="total-row">
        <td><strong>Net Payable</strong></td>
        <td class="amount" style="color:#6c5dd3">₹{{ number_format($record->expected_amount) }}</td>
      </tr>
    </table>

    {{-- Attendance --}}
    <div class="section-title">Attendance — {{ $month->format('F Y') }}</div>
    <div class="att-grid">
      <div class="att-box"><div class="num">{{ $attendanceData['totalDays'] }}</div><div class="lbl">Total Days</div></div>
      <div class="att-box"><div class="num">{{ $attendanceData['workingDays'] }}</div><div class="lbl">Working Days</div></div>
      <div class="att-box"><div class="num" style="color:#10b981">{{ $attendanceData['present'] }}</div><div class="lbl">Present</div></div>
      <div class="att-box"><div class="num" style="color:#f59e0b">{{ $attendanceData['late'] }}</div><div class="lbl">Late</div></div>
      <div class="att-box"><div class="num" style="color:#ef4444">{{ $attendanceData['absent'] }}</div><div class="lbl">Absent</div></div>
    </div>
    <div style="margin-top:8px;font-size:11px;color:#888">Grace days: {{ $suggestion['graceDays'] }} · Min. required: {{ $suggestion['required'] }} days · Attended: {{ $attendanceData['present'] + $attendanceData['late'] }} days</div>

    {{-- Payment history --}}
    @if($record->transactions->isNotEmpty())
    <div class="section-title">Payment Details</div>
    <table class="pay-table">
      <thead><tr><th>Date</th><th>Mode</th><th>Reference</th><th>Amount</th></tr></thead>
      <tbody>
        @foreach($record->transactions as $txn)
        <tr>
          <td>{{ $txn->payment_date->format('d M Y') }}</td>
          <td>{{ ucfirst($txn->payment_mode) }}</td>
          <td style="color:#888">{{ $txn->reference_no ?? '—' }}</td>
          <td>₹{{ number_format($txn->amount) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif

    {{-- Total summary --}}
    <div class="total-summary">
      <div class="ts-label">Total Paid</div>
      <div>
        <span class="ts-value">₹{{ number_format($record->paid_amount) }}</span>
        @if($record->pending > 0)
          <div style="font-size:11px;color:#ef4444;text-align:right;margin-top:2px">Pending: ₹{{ number_format($record->pending) }}</div>
        @endif
      </div>
    </div>
  </div>

  {{-- Footer --}}
  <div class="slip-footer">
    <div class="footer-note">This is a computer generated salary slip.</div>
    <span class="status-badge">{{ ucfirst($record->status) }}</span>
  </div>
</div>
</body>
</html>
