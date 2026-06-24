@extends('layouts.institute')
@section('title','Certificate History')
@section('page-title','Generated Documents')

@push('styles')
<style>
.filter-row { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px; align-items:center; }
.gt-tbl-wrap { overflow-x:auto; }
.gt-tbl { width:100%; border-collapse:collapse; font-size:13px; }
.gt-tbl thead th { background:var(--bg-3); padding:10px 14px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--text-2); border-bottom:1px solid var(--border); white-space:nowrap; }
.gt-tbl tbody td { padding:11px 14px; border-bottom:1px solid var(--border); vertical-align:middle; }
.gt-tbl tbody tr:last-child td { border-bottom:none; }
.gt-tbl tbody tr:hover { background:var(--bg-3); }
.doc-chip { display:inline-block; font-size:10px; font-weight:800; padding:2px 9px; border-radius:5px; text-transform:uppercase; }
.dc-ms { background:#ede9fe; color:#5b21b6; }
.dc-tc { background:#dbeafe; color:#1e40af; }
.dc-cc { background:#dcfce7; color:#15803d; }
.source-chip { display:inline-block; font-size:10px; font-weight:700; padding:2px 9px; border-radius:5px; }
.sc-direct  { background:#f1f5f9; color:#64748b; }
.sc-franchise{ background:#fef3c7; color:#92400e; }
.sc-walkin  { background:#fce7f3; color:#9d174d; }
.cert-no { font-family:monospace; font-size:12px; color:var(--accent); }
</style>
@endpush

@section('content')

<div class="filter-row">
  <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;width:100%;">
    <select name="doc_type" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;">
      <option value="">All Types</option>
      <option value="MARKSHEET">Marksheet</option>
      <option value="TC">TC</option>
      <option value="CC">CC</option>
    </select>
    <select name="source" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;">
      <option value="">All Sources</option>
      <option value="direct">Direct</option>
      <option value="franchise">Franchise</option>
      <option value="walkin">Walk-in</option>
    </select>
    <input type="date" name="from" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;">
    <input type="date" name="to"   style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;">
    <input type="text" name="q" placeholder="Student / Cert No." style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;flex:1;min-width:160px;">
    <button class="btn btn-outline btn-sm">Filter</button>
    <a href="{{ route('institute.certificates.history') }}" class="btn btn-outline btn-sm">Reset</a>
  </form>
</div>

<div class="gt-card" style="padding:0;overflow:hidden;">
  <div class="gt-tbl-wrap">
    <table class="gt-tbl">
      <thead>
        <tr>
          <th>Cert No.</th>
          <th>Student</th>
          <th>Course</th>
          <th>Type</th>
          <th>Source</th>
          <th>Generated On</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        {{-- Prototype dummy rows --}}
        <tr>
          <td><span class="cert-no">GT/MS/240624-001</span></td>
          <td>
            <div style="font-weight:700;">Shubham Agrahari</div>
            <div style="font-size:11px;color:var(--text-2);">INST20260002/ENR/0001</div>
          </td>
          <td style="font-size:12px;">Course on Computer Concept</td>
          <td><span class="doc-chip dc-ms">Marksheet</span></td>
          <td><span class="source-chip sc-direct">Direct</span></td>
          <td style="font-size:12px;color:var(--text-2);">24 Jun 2026</td>
          <td style="text-align:right;">
            <div style="display:flex;gap:6px;justify-content:flex-end;">
              <a href="#" class="btn btn-outline btn-xs">Reprint</a>
            </div>
          </td>
        </tr>
        <tr>
          <td><span class="cert-no">GT/TC/240622-001</span></td>
          <td>
            <div style="font-weight:700;">Rahul Verma</div>
            <div style="font-size:11px;color:var(--text-2);">INST20260002/ENR/0007</div>
          </td>
          <td style="font-size:12px;">Tally Prime</td>
          <td><span class="doc-chip dc-tc">TC</span></td>
          <td><span class="source-chip sc-franchise">Franchise</span></td>
          <td style="font-size:12px;color:var(--text-2);">22 Jun 2026</td>
          <td style="text-align:right;">
            <div style="display:flex;gap:6px;justify-content:flex-end;">
              <a href="#" class="btn btn-outline btn-xs">Reprint</a>
            </div>
          </td>
        </tr>
        <tr>
          <td><span class="cert-no">GT/CC/240620-001</span></td>
          <td>
            <div style="font-weight:700;">Sonu Kumar (Walk-in)</div>
            <div style="font-size:11px;color:var(--text-2);">No Enrollment</div>
          </td>
          <td style="font-size:12px;">MS Office</td>
          <td><span class="doc-chip dc-cc">CC</span></td>
          <td><span class="source-chip sc-walkin">Walk-in</span></td>
          <td style="font-size:12px;color:var(--text-2);">20 Jun 2026</td>
          <td style="text-align:right;">
            <div style="display:flex;gap:6px;justify-content:flex-end;">
              <a href="#" class="btn btn-outline btn-xs">Reprint</a>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div style="padding:12px 16px;border-top:1px solid var(--border);font-size:12px;color:var(--text-2);">
    Showing 3 records
  </div>
</div>

@endsection
