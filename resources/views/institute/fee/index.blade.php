@extends('layouts.institute')
@section('title','Fee Collection')
@section('page-title','Fee Collection')
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Students — Fee Collection</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr>
        <th>Reg No</th><th>Student</th><th>Fee Type</th><th>Balance</th><th>Action</th>
      </tr></thead>
      <tbody>
        @forelse($students as $s)
        <tr>
          <td><code style="font-size:11px;color:var(--accent);">{{ $s->user_id }}</code></td>
          <td>
            <div class="fw-600">{{ $s->name }}</div>
            <div class="text-xs text-muted">{{ $s->mobile }}</div>
          </td>
          <td>
            @php $ft = $s->studentProfile?->fee_collect_type ?? 'OTP'; @endphp
            <span class="badge {{ $ft==='MONTHLY'?'badge-info':($ft==='PART'?'badge-warning':'badge-neutral') }}">{{ $ft }}</span>
          </td>
          <td class="mono">
            @php $bal = $s->wallet?->main_b ?? 0; @endphp
            <span class="{{ $bal >= 0 ? 'amount-pos' : 'amount-neg' }}">₹{{ number_format($bal,2) }}</span>
          </td>
          <td>
            <button onclick="openFeeModal({{ $s->id }},'{{ addslashes($s->name) }}')" class="btn btn-success btn-xs">Collect Fee</button>
            <a href="{{ route('institute.fee.history',$s) }}" class="btn btn-outline btn-xs">History</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="5">
          <div class="gt-empty"><div class="gt-empty-title">No students found</div></div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="gt-pagination">{{ $students->links() }}</div>
</div>

{{-- Fee Collection Modal --}}
<div id="fee-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:300;align-items:center;justify-content:center;">
  <div class="gt-card" style="width:100%;max-width:480px;margin:20px;">
    <div class="gt-card-header">
      <div class="gt-card-title" id="fee-modal-title">Collect Fee</div>
      <button onclick="closeFeeModal()" style="background:none;border:none;color:var(--text-2);cursor:pointer;font-size:20px;">✕</button>
    </div>
    <form method="POST" action="{{ route('institute.fee.collect') }}">
      @csrf
      <input type="hidden" name="student_id" id="fee-student-id">

      <div class="gt-form-grid-2">
        <div class="gt-form-group">
          <label class="gt-label">Payment Mode <span style="color:var(--danger)">*</span></label>
          <select name="payment_mode" class="gt-select">
            @foreach(['CASH','UPI','NEFT','IMPS','CHEQUE'] as $m)
            <option value="{{ $m }}">{{ $m }}</option>
            @endforeach
          </select>
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Amount (₹) <span style="color:var(--danger)">*</span></label>
          <input type="number" name="amt" class="gt-input" min="1" step="0.01" required placeholder="0.00">
        </div>
      </div>

      <div class="gt-form-grid-2">
        <div class="gt-form-group">
          <label class="gt-label">Date <span style="color:var(--danger)">*</span></label>
          <input type="date" name="date" class="gt-input" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="gt-form-group">
          <label class="gt-label">UTR / Ref</label>
          <input type="text" name="utr" class="gt-input" placeholder="Optional">
        </div>
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Note</label>
        <input type="text" name="note" class="gt-input" placeholder="e.g. August month fee">
      </div>

      <div class="flex gap-3">
        <button type="submit" class="btn btn-primary flex-1" style="justify-content:center;">Collect Fee</button>
        <button type="button" onclick="closeFeeModal()" class="btn btn-outline">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const modal = document.getElementById('fee-modal');
function openFeeModal(id, name) {
  document.getElementById('fee-student-id').value = id;
  document.getElementById('fee-modal-title').textContent = 'Collect Fee — ' + name;
  modal.style.display = 'flex';
}
function closeFeeModal() { modal.style.display = 'none'; }
modal.addEventListener('click', e => { if (e.target === modal) closeFeeModal(); });
</script>
@endpush
@endsection
