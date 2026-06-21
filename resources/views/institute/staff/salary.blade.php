@extends('layouts.institute')
@php
  $profile = $staff->staffProfile;
  $role    = $profile?->staffRole;
  $initials = collect(explode(' ', $profile?->name ?? 'S'))->map(fn($w)=>strtoupper($w[0]??''))->take(2)->join('');
  $rc = $role?->color ?? '#6c5dd3';
@endphp
@section('title','Salary — '.($profile?->name ?? 'Staff'))
@section('page-title','Salary Management')
@section('topbar-actions')
  <a href="{{ route('institute.staff.show', $staff) }}" class="btn btn-outline btn-sm">← Profile</a>
@endsection

@push('styles')
<style>
.stat-box { background:var(--bg-2);border:1px solid var(--border);border-radius:12px;padding:16px 20px; }
.stat-label { font-size:11px;color:var(--text-3);margin-bottom:4px; }
.stat-value { font-size:22px;font-weight:800;color:var(--text-1); }
.stat-sub { font-size:11px;color:var(--text-3);margin-top:2px; }
.calc-row { display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px; }
.calc-row:last-child { border-bottom:none; }
.calc-label { color:var(--text-2); }
.calc-value { font-weight:700;color:var(--text-1); }
.calc-value.deduct { color:#ef4444; }
.calc-value.final  { color:#10b981;font-size:16px; }
.pay-mode-btn { flex:1;padding:9px 8px;border:1.5px solid var(--border);border-radius:9px;text-align:center;cursor:pointer;font-size:12px;font-weight:700;color:var(--text-2);background:var(--bg);transition:all .15s; }
.pay-mode-btn.active { border-color:var(--accent);background:color-mix(in srgb,var(--accent) 12%,transparent);color:var(--accent); }
.record-card { background:var(--bg-2);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:12px; }
.record-head { padding:12px 16px;background:var(--bg-3);display:flex;align-items:center;gap:12px;justify-content:space-between; }
</style>
@endpush

@section('content')
@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:300px 1fr;gap:16px;align-items:start;">

  {{-- ── LEFT PANEL ────────────────────────────────────────────────────────── --}}
  <div>
    {{-- Staff info --}}
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:14px;">
      <div style="padding:18px;text-align:center;border-bottom:1px solid var(--border);">
        <div style="width:52px;height:52px;border-radius:50%;background:{{ $rc }}22;color:{{ $rc }};font-size:18px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 10px">{{ $initials }}</div>
        <div style="font-size:15px;font-weight:800;color:var(--text-1)">{{ $profile?->name }}</div>
        @if($role)<span style="font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;background:{{ $rc }}18;color:{{ $rc }};margin-top:5px;display:inline-block">{{ $role->name }}</span>@endif
      </div>
      <div style="padding:14px 16px;display:grid;gap:10px;">
        <div><div style="font-size:11px;color:var(--text-3)">Monthly Salary</div><div style="font-size:18px;font-weight:800;color:var(--text-1)">₹{{ number_format($profile?->salary ?? 0) }}</div></div>
        <div><div style="font-size:11px;color:var(--text-3)">Grace Days (per role)</div><div style="font-size:15px;font-weight:700;color:var(--text-1)">{{ $role?->grace_days ?? 2 }} days</div></div>
        <div><div style="font-size:11px;color:var(--text-3)">Per Day Rate</div><div style="font-size:15px;font-weight:700;color:var(--text-1)">₹{{ number_format($suggestion['perDay'], 2) }}</div></div>
      </div>
    </div>

    {{-- This month calculation --}}
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:14px;">
      <div style="padding:12px 16px;border-bottom:1px solid var(--border);">
        <div style="font-size:12px;font-weight:800;color:var(--text-1)">{{ $now->format('F Y') }} — Calculation</div>
        <div style="font-size:11px;color:var(--text-3);margin-top:2px">Based on attendance data</div>
      </div>
      <div style="padding:14px 16px;">
        <div class="calc-row">
          <span class="calc-label">Total days</span>
          <span class="calc-value">{{ $attendanceData['totalDays'] }}</span>
        </div>
        <div class="calc-row">
          <span class="calc-label">Sundays</span>
          <span class="calc-value">{{ $attendanceData['sundays'] }}</span>
        </div>
        <div class="calc-row">
          <span class="calc-label">Working days</span>
          <span class="calc-value">{{ $attendanceData['workingDays'] }}</span>
        </div>
        <div class="calc-row">
          <span class="calc-label">Grace days</span>
          <span class="calc-value">{{ $suggestion['graceDays'] }}</span>
        </div>
        <div class="calc-row">
          <span class="calc-label">Min. required</span>
          <span class="calc-value">{{ $suggestion['required'] }}</span>
        </div>
        <div class="calc-row" style="background:var(--bg-3);margin:6px -4px;padding:8px 4px;border-radius:7px;border:none">
          <span class="calc-label" style="font-weight:700;color:var(--text-1)">Days attended</span>
          <span class="calc-value" style="font-size:16px">{{ $attendanceData['present'] + $attendanceData['late'] }}</span>
        </div>
        @if($suggestion['shortfall'] > 0)
        <div class="calc-row">
          <span class="calc-label">Shortfall</span>
          <span class="calc-value deduct">–{{ $suggestion['shortfall'] }} days</span>
        </div>
        <div class="calc-row">
          <span class="calc-label">Deduction</span>
          <span class="calc-value deduct">–₹{{ number_format($suggestion['deduction'], 2) }}</span>
        </div>
        @endif
        <div class="calc-row" style="margin-top:4px;padding-top:12px;border-top:1.5px solid var(--border);border-bottom:none">
          <span class="calc-label" style="font-weight:800;color:var(--text-1)">Suggested salary</span>
          <span class="calc-value final">₹{{ number_format($suggestion['suggested']) }}</span>
        </div>
        @if($attendanceData['present'] === 0 && $attendanceData['late'] === 0)
        <div style="margin-top:10px;padding:8px 12px;background:#fef3c7;border-radius:7px;font-size:11px;color:#92400e">
          No attendance marked yet for this month. Calculation is an estimate.
        </div>
        @endif
      </div>

      {{-- Generate record for this month --}}
      @if(!$currentRecord)
      <div style="padding:0 16px 16px;">
        <form method="POST" action="{{ route('institute.staff.salary.record', $staff) }}" id="genForm">
          @csrf
          <input type="hidden" name="month" value="{{ $now->format('Y-m') }}">
          <div style="margin-bottom:10px;">
            <label style="font-size:11px;font-weight:600;color:var(--text-2);display:block;margin-bottom:5px">Final salary amount</label>
            <input type="number" name="expected_amount" id="expectedAmt"
                   value="{{ $suggestion['suggested'] }}" min="0" step="1"
                   style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:9px;padding:0 12px;font-size:14px;font-weight:700;background:var(--bg);color:var(--text-1);outline:none;box-sizing:border-box">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;font-size:13px">Generate {{ $now->format('M Y') }} Record</button>
        </form>
      </div>
      @else
      <div style="padding:0 16px 16px;">
        <div style="padding:10px 14px;border-radius:9px;background:var(--bg-3);font-size:12px;color:var(--text-2);">
          Record exists · Expected: <strong>₹{{ number_format($currentRecord->expected_amount) }}</strong>
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- ── RIGHT PANEL ───────────────────────────────────────────────────────── --}}
  <div>
    {{-- Add payment --}}
    @if($currentRecord)
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:16px;">
      <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div>
          <div style="font-size:13px;font-weight:800;color:var(--text-1)">Record Payment — {{ $now->format('F Y') }}</div>
          <div style="font-size:11px;color:var(--text-3);margin-top:1px">Expected ₹{{ number_format($currentRecord->expected_amount) }} · Paid ₹{{ number_format($currentRecord->paid_amount) }} · Pending ₹{{ number_format($currentRecord->pending) }}</div>
        </div>
        <span style="font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;
          {{ $currentRecord->status==='paid' ? 'background:#d1fae5;color:#065f46' : ($currentRecord->status==='partial' ? 'background:#fef3c7;color:#92400e' : 'background:#fee2e2;color:#991b1b') }}">
          {{ ucfirst($currentRecord->status) }}
        </span>
      </div>
      <form method="POST" action="{{ route('institute.staff.salary.pay', $staff) }}" style="padding:16px 18px;">
        @csrf
        <input type="hidden" name="salary_record_id" value="{{ $currentRecord->id }}">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:12px;">
          <div>
            <label style="font-size:11px;font-weight:600;color:var(--text-2);display:block;margin-bottom:5px">Amount (₹) *</label>
            <input type="number" name="amount" placeholder="{{ $currentRecord->pending }}" min="1" required
                   style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:9px;padding:0 12px;font-size:13px;background:var(--bg);color:var(--text-1);outline:none;box-sizing:border-box">
          </div>
          <div>
            <label style="font-size:11px;font-weight:600;color:var(--text-2);display:block;margin-bottom:5px">Date *</label>
            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                   style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:9px;padding:0 12px;font-size:13px;background:var(--bg);color:var(--text-1);outline:none;box-sizing:border-box">
          </div>
          <div>
            <label style="font-size:11px;font-weight:600;color:var(--text-2);display:block;margin-bottom:5px">Reference No.</label>
            <input type="text" name="reference_no" placeholder="UTR / Cheque no."
                   style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:9px;padding:0 12px;font-size:13px;background:var(--bg);color:var(--text-1);outline:none;box-sizing:border-box">
          </div>
        </div>
        {{-- Payment mode --}}
        <div style="margin-bottom:12px;">
          <label style="font-size:11px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">Payment Mode *</label>
          <div style="display:flex;gap:8px;">
            @foreach(['cash'=>'Cash','bank'=>'Bank','upi'=>'UPI','cheque'=>'Cheque'] as $val=>$lbl)
              <label class="pay-mode-btn {{ $val==='cash'?'active':'' }}" id="mode-{{ $val }}">
                <input type="radio" name="payment_mode" value="{{ $val }}" {{ $val==='cash'?'checked':'' }}
                       onchange="document.querySelectorAll('.pay-mode-btn').forEach(b=>b.classList.remove('active'));document.getElementById('mode-{{ $val }}').classList.add('active')"
                       style="display:none">
                {{ $lbl }}
              </label>
            @endforeach
          </div>
        </div>
        <div style="display:flex;gap:12px;align-items:flex-end;">
          <div style="flex:1">
            <label style="font-size:11px;font-weight:600;color:var(--text-2);display:block;margin-bottom:5px">Notes</label>
            <input type="text" name="notes" placeholder="Optional note"
                   style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:9px;padding:0 12px;font-size:13px;background:var(--bg);color:var(--text-1);outline:none;box-sizing:border-box">
          </div>
          <button type="submit" class="btn btn-primary" style="height:40px;white-space:nowrap">Record Payment</button>
        </div>
      </form>
      {{-- Transactions for current record --}}
      @if($currentRecord->transactions->isNotEmpty())
      <div style="border-top:1px solid var(--border);padding:0 18px 14px;">
        <div style="font-size:11px;font-weight:700;color:var(--text-3);padding:10px 0 8px;text-transform:uppercase;letter-spacing:.06em">Payments Made</div>
        @foreach($currentRecord->transactions as $txn)
        <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-top:1px solid var(--border);">
          <div style="flex:1">
            <span style="font-size:13px;font-weight:700;color:var(--text-1)">₹{{ number_format($txn->amount) }}</span>
            <span style="font-size:11px;color:var(--text-3);margin-left:8px">{{ $txn->payment_date->format('d M Y') }} · {{ ucfirst($txn->payment_mode) }}</span>
            @if($txn->reference_no)<span style="font-size:11px;color:var(--text-3)"> · {{ $txn->reference_no }}</span>@endif
          </div>
          <form method="POST" action="{{ route('institute.staff.salary.transaction.delete', $txn) }}"
                onsubmit="return confirm('Delete this payment entry?')">
            @csrf @method('DELETE')
            <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-3);padding:4px 8px;border-radius:6px;" title="Delete">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </button>
          </form>
        </div>
        @endforeach
      </div>
      @endif
    </div>
    @endif

    {{-- All records --}}
    <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="padding:14px 18px;border-bottom:1px solid var(--border);">
        <div style="font-size:13px;font-weight:800;color:var(--text-1)">Salary History</div>
      </div>

      @if($records->isEmpty())
        <div style="padding:40px;text-align:center;color:var(--text-3);font-size:13px">No salary records yet.</div>
      @else
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="border-bottom:1.5px solid var(--border);">
              <th style="padding:10px 18px;text-align:left;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em">Month</th>
              <th style="padding:10px 14px;text-align:right;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em">Expected</th>
              <th style="padding:10px 14px;text-align:right;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em">Paid</th>
              <th style="padding:10px 14px;text-align:right;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em">Pending</th>
              <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em">Status</th>
              <th style="padding:10px 18px;text-align:center;font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.06em">Slip</th>
            </tr>
          </thead>
          <tbody>
            @foreach($records as $rec)
            <tr style="border-bottom:1px solid var(--border);transition:background .12s;" onmouseenter="this.style.background='var(--bg-3)'" onmouseleave="this.style.background=''">
              <td style="padding:12px 18px;">
                <div style="font-size:13px;font-weight:700;color:var(--text-1)">{{ \Carbon\Carbon::parse($rec->month)->format('F Y') }}</div>
                <div style="font-size:11px;color:var(--text-3)">{{ $rec->transactions->count() }} payment{{ $rec->transactions->count()!=1?'s':'' }}</div>
              </td>
              <td style="padding:12px 14px;text-align:right;font-size:13px;font-weight:600;color:var(--text-1)">₹{{ number_format($rec->expected_amount) }}</td>
              <td style="padding:12px 14px;text-align:right;font-size:13px;font-weight:600;color:#10b981">₹{{ number_format($rec->paid_amount) }}</td>
              <td style="padding:12px 14px;text-align:right;font-size:13px;font-weight:600;color:{{ $rec->pending > 0 ? '#ef4444' : 'var(--text-3)' }}">
                {{ $rec->pending > 0 ? '₹'.number_format($rec->pending) : '—' }}
              </td>
              <td style="padding:12px 14px;text-align:center;">
                <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;
                  {{ $rec->status==='paid' ? 'background:#d1fae5;color:#065f46' : ($rec->status==='partial' ? 'background:#fef3c7;color:#92400e' : 'background:#fee2e2;color:#991b1b') }}">
                  {{ ucfirst($rec->status) }}
                </span>
              </td>
              <td style="padding:12px 18px;text-align:center;">
                <a href="{{ route('institute.staff.salary.slip', [$staff, $rec->id]) }}"
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;color:var(--accent);text-decoration:none;padding:5px 10px;border-radius:7px;border:1.5px solid var(--accent)20;background:var(--accent)08">
                  <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  Slip
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @if($records->hasPages())
          <div style="padding:12px 18px;border-top:1px solid var(--border)">{{ $records->links() }}</div>
        @endif
      @endif
    </div>
  </div>
</div>
@endsection
