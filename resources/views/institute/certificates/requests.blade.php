@extends('layouts.institute')
@section('title','Certificate Requests')
@section('page-title','Franchise Certificate Requests')

@push('styles')
<style>
.filter-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px; align-items:center; }
.filter-btn { padding:6px 16px; border-radius:20px; font-size:12px; font-weight:700; border:1.5px solid var(--border); background:var(--bg-2); color:var(--text-2); cursor:pointer; transition:.12s; text-decoration:none; }
.filter-btn.active, .filter-btn:hover { border-color:var(--accent); color:var(--accent); background:var(--bg-3); }
.filter-btn .count { background:var(--accent); color:#fff; border-radius:10px; padding:1px 7px; font-size:10px; margin-left:5px; }

.req-card { border:1.5px solid var(--border); border-radius:14px; background:var(--bg-2); margin-bottom:14px; overflow:hidden; transition:.12s; }
.req-card:hover { border-color:var(--accent); box-shadow:0 4px 16px rgba(0,0,0,.07); }
.req-card-head { display:flex; align-items:center; gap:14px; padding:14px 18px; border-bottom:1px solid var(--border); }
.req-avatar { width:42px; height:42px; border-radius:50%; background:var(--accent); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:800; flex-shrink:0; }
.req-name { font-size:15px; font-weight:800; }
.req-meta { font-size:12px; color:var(--text-2); margin-top:2px; }
.req-badge { margin-left:auto; }

.badge { display:inline-block; font-size:10px; font-weight:800; padding:4px 12px; border-radius:20px; text-transform:uppercase; letter-spacing:.5px; }
.badge-pending  { background:#fef3c7; color:#92400e; }
.badge-approved { background:#d1fae5; color:#065f46; }
.badge-rejected { background:#fee2e2; color:#991b1b; }
.badge-generated{ background:#dbeafe; color:#1e40af; }

.doc-chip { display:inline-block; font-size:10px; font-weight:800; padding:2px 9px; border-radius:5px; text-transform:uppercase; }
.dc-ms { background:#ede9fe; color:#5b21b6; }
.dc-tc { background:#dbeafe; color:#1e40af; }
.dc-cc { background:#dcfce7; color:#15803d; }

.req-body { display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:0; }
.req-field { padding:12px 18px; border-right:1px solid var(--border); }
.req-field:last-child { border-right:none; }
.rf-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--text-2); margin-bottom:4px; }
.rf-val   { font-size:13px; font-weight:600; }
.req-actions { padding:12px 18px; display:flex; align-items:center; gap:8px; flex-direction:column; justify-content:center; }

@media(max-width:700px) { .req-body { grid-template-columns:1fr 1fr; } }

/* Reject modal */
.modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:200; display:none; align-items:center; justify-content:center; }
.modal-backdrop.open { display:flex; }
.modal { background:var(--bg-1); border-radius:16px; padding:28px; width:420px; max-width:90vw; }
.modal-title { font-size:17px; font-weight:800; margin-bottom:16px; }
.modal textarea { width:100%; padding:10px 12px; border:1.5px solid var(--border); border-radius:9px; font-size:13px; background:var(--bg-3); color:var(--text); resize:vertical; min-height:90px; outline:none; box-sizing:border-box; }
.modal textarea:focus { border-color:var(--accent); }
.modal-foot { display:flex; gap:10px; justify-content:flex-end; margin-top:14px; }

.empty-state { text-align:center; padding:60px 20px; color:var(--text-2); }
</style>
@endpush

@section('content')

{{-- Filter tabs --}}
<div class="filter-bar">
  <a href="?status=pending"   class="filter-btn {{ ($status??'pending')==='pending'  ?'active':'' }}">Pending   <span class="count">{{ $pendingCount  ?? 3 }}</span></a>
  <a href="?status=approved"  class="filter-btn {{ ($status??'')==='approved' ?'active':'' }}">Approved</a>
  <a href="?status=rejected"  class="filter-btn {{ ($status??'')==='rejected' ?'active':'' }}">Rejected</a>
  <a href="?status=generated" class="filter-btn {{ ($status??'')==='generated'?'active':'' }}">Generated</a>
  <a href="?status=all"       class="filter-btn {{ ($status??'')==='all'      ?'active':'' }}">All</a>
  <div style="margin-left:auto;">
    <form method="GET" style="display:flex;gap:6px;">
      <input type="hidden" name="status" value="{{ $status ?? 'pending' }}">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search student…"
        style="padding:6px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;width:200px;">
      <button class="btn btn-outline btn-sm">Search</button>
    </form>
  </div>
</div>

{{-- Request cards (prototype — dummy data) --}}
@php
$dummyRequests = [
  ['id'=>1,'name'=>'Priya Sharma','uid'=>'INST20260002/ENR/0003','mobile'=>'9876543210','course'=>'Computer Fundamentals','type'=>'MARKSHEET','franchise'=>'ABC Franchise','date'=>'21 Jun 2026','status'=>'pending','note'=>'Student needs marksheet for job application.'],
  ['id'=>2,'name'=>'Rahul Verma','uid'=>'INST20260002/ENR/0007','mobile'=>'9812345678','course'=>'Tally Prime','type'=>'TC','franchise'=>'XYZ Center','date'=>'22 Jun 2026','status'=>'pending','note'=>'Transfer to another institute.'],
  ['id'=>3,'name'=>'Anita Patel','uid'=>'INST20260002/ENR/0011','mobile'=>'9900112233','course'=>'DTP & Graphics','type'=>'CC','franchise'=>'ABC Franchise','date'=>'23 Jun 2026','status'=>'pending','note'=>''],
];
@endphp

@if(count($dummyRequests) === 0)
<div class="gt-card">
  <div class="empty-state">
    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="opacity:.3;margin-bottom:12px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    <div style="font-size:14px;font-weight:700;">No requests found</div>
    <div style="font-size:12px;margin-top:4px;">Koi pending franchise request nahi hai.</div>
  </div>
</div>
@else
@foreach($dummyRequests as $req)
<div class="req-card">
  <div class="req-card-head">
    <div class="req-avatar">{{ strtoupper(substr($req['name'],0,1)) }}</div>
    <div style="flex:1;">
      <div class="req-name">{{ $req['name'] }}</div>
      <div class="req-meta">{{ $req['uid'] }} &nbsp;·&nbsp; {{ $req['mobile'] }}</div>
    </div>
    <span class="doc-chip dc-{{ strtolower($req['type']) }}">{{ $req['type'] }}</span>
    <span class="badge badge-{{ $req['status'] }}" style="margin-left:8px;">{{ ucfirst($req['status']) }}</span>
  </div>
  <div class="req-body">
    <div class="req-field">
      <div class="rf-label">Course</div>
      <div class="rf-val">{{ $req['course'] }}</div>
    </div>
    <div class="req-field">
      <div class="rf-label">Franchise</div>
      <div class="rf-val">{{ $req['franchise'] }}</div>
    </div>
    <div class="req-field">
      <div class="rf-label">Requested</div>
      <div class="rf-val">{{ $req['date'] }}</div>
    </div>
    @if($req['note'])
    <div class="req-field" style="grid-column:span 3;border-right:none;">
      <div class="rf-label">Note from Franchise</div>
      <div class="rf-val" style="color:var(--text-2);font-size:12px;">{{ $req['note'] }}</div>
    </div>
    @endif
    @if($req['status'] === 'pending')
    <div class="req-actions">
      {{-- Approve → goes to generate page pre-filled --}}
      <a href="{{ route('institute.certificates.generate') }}?request_id={{ $req['id'] }}" class="btn btn-primary btn-xs" style="width:100%;justify-content:center;text-align:center;">
        ✓ Approve &amp; Generate
      </a>
      <button class="btn btn-outline btn-xs" style="width:100%;color:#dc2626;border-color:#fca5a5;"
              onclick="openReject({{ $req['id'] }})">
        ✕ Reject
      </button>
    </div>
    @elseif($req['status'] === 'generated')
    <div class="req-actions">
      <a href="#" class="btn btn-outline btn-xs" style="width:100%;text-align:center;">Reprint</a>
    </div>
    @else
    <div class="req-actions" style="color:var(--text-2);font-size:12px;text-align:center;">—</div>
    @endif
  </div>
</div>
@endforeach
@endif

{{-- Reject Modal --}}
<div class="modal-backdrop" id="reject-modal">
  <div class="modal">
    <div class="modal-title">Reject Request</div>
    <form method="POST" action="{{ route('institute.certificates.reject', 0) }}" id="reject-form">
      @csrf
      <input type="hidden" name="_method" value="POST">
      <input type="hidden" name="request_id" id="reject-req-id">
      <label style="font-size:12px;font-weight:700;color:var(--text-2);display:block;margin-bottom:6px;">Rejection Reason (franchise ko dikhega)</label>
      <textarea name="reject_note" required placeholder="Reason likhao…"></textarea>
      <div class="modal-foot">
        <button type="button" class="btn btn-outline btn-sm" onclick="closeReject()">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm" style="background:#dc2626;border-color:#dc2626;">Reject</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openReject(id) {
  document.getElementById('reject-req-id').value = id;
  document.getElementById('reject-modal').classList.add('open');
}
function closeReject() {
  document.getElementById('reject-modal').classList.remove('open');
}
document.getElementById('reject-modal').addEventListener('click', function(e) {
  if (e.target === this) closeReject();
});
</script>
@endpush
