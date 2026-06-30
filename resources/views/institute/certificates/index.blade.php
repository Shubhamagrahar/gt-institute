@extends('layouts.institute')
@section('title','Certificates & Marksheets')
@section('page-title','Certificates & Marksheets')

@push('styles')
<style>
/* ─── Layout ─── */
.cert-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:24px; }
@media(max-width:900px){ .cert-grid{grid-template-columns:1fr 1fr;} }
@media(max-width:580px){ .cert-grid{grid-template-columns:1fr;} }

/* ─── Stat cards ─── */
.cert-stat { border-radius:14px; padding:20px 22px; display:flex; align-items:center; gap:16px; }
.cs-blue   { background:linear-gradient(135deg,#8a73f5,#5b4ec7); color:#fff; }
.cs-amber  { background:linear-gradient(135deg,#f59e0b,#b45309); color:#fff; }
.cs-green  { background:linear-gradient(135deg,#10b981,#047857); color:#fff; }
.cs-icon   { width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.18);
             display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.cs-icon svg{ width:24px;height:24px;stroke:#fff;fill:none; }
.cs-num  { font-size:28px;font-weight:900;line-height:1; }
.cs-lbl  { font-size:11px;opacity:.85;text-transform:uppercase;letter-spacing:.6px;margin-top:3px; }

/* ─── Section heading ─── */
.sec-head { display:flex;align-items:center;gap:10px;margin:28px 0 14px; }
.sec-head-bar { width:4px;height:20px;border-radius:3px;background:var(--accent,#6366f1); }
.sec-head-txt { font-size:15px;font-weight:800;color:var(--text,#1a1a2e); }

/* ─── Doc type cards ─── */
.doc-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px; }
@media(max-width:700px){ .doc-grid{grid-template-columns:1fr;} }

.doc-card { border-radius:14px;border:1.5px solid var(--border,#e2e8f0);
            background:var(--bg-2,#fff);padding:20px;text-align:center;
            transition:.15s;cursor:default; }
.doc-card:hover{ border-color:var(--accent,#6366f1);box-shadow:0 4px 18px rgba(99,102,241,.12); }
.doc-badge { display:inline-block;font-size:11px;font-weight:900;letter-spacing:.8px;
             text-transform:uppercase;padding:4px 12px;border-radius:20px;margin-bottom:10px; }
.db-marksheet { background:#ede9fe;color:#5b21b6; }
.db-cc        { background:#dcfce7;color:#15803d; }
.doc-title { font-size:15px;font-weight:800;margin-bottom:6px; }
.doc-desc  { font-size:12px;color:var(--text-2,#64748b);line-height:1.6; }
.doc-eligible{ font-size:11px;margin-top:10px;font-weight:700;color:var(--text-2); }

/* ─── Flow cards ─── */
.flow-grid { display:grid; grid-template-columns:1fr; gap:16px; margin-bottom:24px; }

.flow-card { border-radius:14px; border:1.5px solid var(--border,#e2e8f0);
             background:var(--bg-2,#fff); overflow:hidden; }
.flow-card-head { padding:16px 18px; display:flex;align-items:center;gap:12px;
                  border-bottom:1px solid var(--border,#e2e8f0); }
.flow-card-icon { width:38px;height:38px;border-radius:10px;display:flex;align-items:center;
                  justify-content:center;flex-shrink:0; background:#f5f3ff; }
.flow-card-icon svg{ stroke:#8a73f5; }
.flow-card-title { font-size:14px;font-weight:800; }
.flow-card-sub   { font-size:11px;color:var(--text-2,#64748b);margin-top:2px; }
.flow-steps { padding:0; margin:0; list-style:none; display:grid; grid-template-columns:1fr 1fr; }
@media(max-width:700px){ .flow-steps{grid-template-columns:1fr;} }
.flow-step  { display:flex;align-items:flex-start;gap:12px;padding:13px 18px;
              border-bottom:1px solid var(--border,#e2e8f0); }
.step-num { width:22px;height:22px;border-radius:50%;font-size:11px;font-weight:800;
            background:#f5f3ff;color:#8a73f5;
            display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px; }
.step-txt  { font-size:13px;font-weight:600;color:var(--text,#1a1a2e); }
.step-sub  { font-size:11px;color:var(--text-2,#64748b);margin-top:2px; }

/* ─── Action panel ─── */
.action-panel { border-radius:14px;border:1.5px solid var(--border,#e2e8f0);
                background:var(--bg-2,#fff);overflow:hidden; }
.action-row { display:flex;align-items:center;gap:14px;padding:16px 20px;
              border-bottom:1px solid var(--border,#e2e8f0);transition:.1s; }
.action-row:last-child{ border-bottom:none; }
.action-row:hover{ background:var(--bg-3,#f8fafc); }
.action-icon { width:42px;height:42px;border-radius:11px;display:flex;align-items:center;
               justify-content:center;flex-shrink:0; }
.ai-blue   { background:#f5f3ff; } .ai-blue   svg{ stroke:#8a73f5; }
.ai-purple { background:#ede9fe; } .ai-purple svg{ stroke:#5b21b6; }
.ai-amber  { background:#fffbeb; } .ai-amber  svg{ stroke:#f59e0b; }
.action-info { flex:1; }
.action-title { font-size:14px;font-weight:700; }
.action-desc  { font-size:12px;color:var(--text-2,#64748b);margin-top:2px; }
.action-cta a, .action-cta button {
  padding:7px 16px;border-radius:8px;font-size:12px;font-weight:700;
  border:1.5px solid var(--accent,#6366f1);color:var(--accent,#6366f1);
  background:transparent;cursor:pointer;text-decoration:none;transition:.12s; }
.action-cta a:hover, .action-cta button:hover {
  background:var(--accent,#6366f1);color:#fff; }
.action-cta .primary {
  background:var(--accent,#6366f1);color:#fff;border-color:var(--accent,#6366f1); }
.action-cta .primary:hover { opacity:.85; }

/* ─── Coming soon note ─── */
.proto-note { background:linear-gradient(135deg,#fef9c3,#fef3c7);border:1.5px solid #fbbf24;
  border-radius:12px;padding:14px 18px;margin-bottom:24px;
  display:flex;align-items:flex-start;gap:10px; }
.proto-note-icon { font-size:18px;flex-shrink:0; }
.proto-note-txt { font-size:13px;font-weight:600;color:#78350f; }
.proto-note-sub { font-size:12px;color:#92400e;margin-top:3px; }
</style>
@endpush

@section('content')

{{-- Scope notice: only the franchise-request half is still a preview --}}
<div class="proto-note">
  <div class="proto-note-icon">🧪</div>
  <div>
    <div class="proto-note-txt">Franchise Request flow abhi design preview hai</div>
    <div class="proto-note-sub">Direct Generation aur Walk-in Certificate fully working hain. Franchise se request aane wala flow abhi tak sirf UI design hai — backend baad mein implement hoga.</div>
  </div>
</div>

{{-- ─── STAT CARDS ─── --}}
<div class="cert-grid">
  <div class="cert-stat cs-amber">
    <div class="cs-icon">
      <svg viewBox="0 0 24 24" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
    </div>
    <div>
      <div class="cs-num">{{ $pendingCount }}</div>
      <div class="cs-lbl">Pending Requests</div>
    </div>
  </div>
  <div class="cert-stat cs-blue">
    <div class="cs-icon">
      <svg viewBox="0 0 24 24" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <div>
      <div class="cs-num">{{ $generatedThisMonth }}</div>
      <div class="cs-lbl">Generated This Month</div>
    </div>
  </div>
  <div class="cert-stat cs-green">
    <div class="cs-icon">
      <svg viewBox="0 0 24 24" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
    </div>
    <div>
      <div class="cs-num">{{ $totalGenerated }}</div>
      <div class="cs-lbl">Total Generated</div>
    </div>
  </div>
</div>

{{-- ─── DOCUMENT TYPES ─── --}}
<div class="sec-head">
  <div class="sec-head-bar"></div>
  <div class="sec-head-txt">Document Types</div>
</div>

<div class="doc-grid">
  <div class="doc-card">
    <div class="doc-badge db-marksheet">Marksheet</div>
    <div class="doc-title">Marksheet</div>
    <div class="doc-desc">Subject-wise marks, percentage aur grade dikhata hai — sab marks ke basis pe automatically calculate hota hai.</div>
    <div class="doc-eligible">Ek certificate generation se dono document bante hain</div>
  </div>
  <div class="doc-card">
    <div class="doc-badge db-cc">Certificate</div>
    <div class="doc-title">Certificate of Completion</div>
    <div class="doc-desc">Student ne course successfully complete kiya — usi marks-data se grade ke saath formal certificate banta hai.</div>
    <div class="doc-eligible">Ek certificate generation se dono document bante hain</div>
  </div>
</div>

{{-- ─── FLOW EXPLANATION ─── --}}
<div class="sec-head">
  <div class="sec-head-bar"></div>
  <div class="sec-head-txt">How It Works</div>
</div>

<div class="flow-grid">
  <div class="flow-card">
    <div class="flow-card-head">
      <div class="flow-card-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      </div>
      <div>
        <div class="flow-card-title">Direct Generation</div>
        <div class="flow-card-sub">Apne enrolled students ke liye — Marksheet + Certificate dono ek hi data se</div>
      </div>
    </div>
    <ul class="flow-steps">
      <li class="flow-step">
        <div class="step-num">1</div>
        <div><div class="step-txt">Student Search</div><div class="step-sub">Name / Mobile / Enrollment No. se search karo</div></div>
      </li>
      <li class="flow-step">
        <div class="step-num">2</div>
        <div><div class="step-txt">Course Select</div><div class="step-sub">Student jin courses mein enrolled hai, unka status ke saath</div></div>
      </li>
      <li class="flow-step">
        <div class="step-num">3</div>
        <div><div class="step-txt">Subjects Auto-fill</div><div class="step-sub">Course ke subjects + max marks automatically aate hain</div></div>
      </li>
      <li class="flow-step">
        <div class="step-num">4</div>
        <div><div class="step-txt">Obtained Marks Bharo</div><div class="step-sub">Percentage aur grade live calculate hota hai</div></div>
      </li>
      <li class="flow-step">
        <div class="step-num">5</div>
        <div><div class="step-txt">Generate → History List</div><div class="step-sub">Yahan se Print Marksheet ya Print Certificate karo</div></div>
      </li>
    </ul>
  </div>
</div>

{{-- ─── QUICK ACTIONS ─── --}}
<div class="sec-head">
  <div class="sec-head-bar"></div>
  <div class="sec-head-txt">Quick Actions</div>
</div>

<div class="action-panel gt-card" style="padding:0;margin-bottom:24px;">
  <div class="action-row">
    <div class="action-icon ai-blue">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </div>
    <div class="action-info">
      <div class="action-title">Generate Certificate / Marksheet</div>
      <div class="action-desc">Directly apne kisi bhi enrolled student ka document generate karo</div>
    </div>
    <div class="action-cta">
      <a href="{{ route('institute.certificates.generate') }}" class="primary">Generate</a>
    </div>
  </div>
  <div class="action-row">
    <div class="action-icon ai-amber">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
    </div>
    <div class="action-info">
      <div class="action-title">Franchise Requests</div>
      <div class="action-desc">Franchise se aayi pending requests review karo — design preview</div>
    </div>
    <div class="action-cta">
      <a href="{{ route('institute.certificates.requests') }}">View Requests <span style="background:#f59e0b;color:#fff;border-radius:20px;padding:1px 8px;font-size:11px;margin-left:4px;">{{ $pendingCount }}</span></a>
    </div>
  </div>
  <div class="action-row">
    <div class="action-icon ai-purple">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <div class="action-info">
      <div class="action-title">Generated Documents History</div>
      <div class="action-desc">Saare generated certificates ka record — Marksheet/Certificate dobara print bhi kar sako</div>
    </div>
    <div class="action-cta">
      <a href="{{ route('institute.certificates.history') }}">View All</a>
    </div>
  </div>
</div>

@endsection
