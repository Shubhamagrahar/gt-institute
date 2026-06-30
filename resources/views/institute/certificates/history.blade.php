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
.source-chip { display:inline-block; font-size:10px; font-weight:700; padding:2px 9px; border-radius:5px; }
.sc-direct  { background:#f1f5f9; color:#64748b; }
.sc-walkin  { background:#fce7f3; color:#9d174d; }
.cert-no { font-family:monospace; font-size:12px; color:var(--accent); }
.result-chip { display:inline-block; font-size:10px; font-weight:800; padding:2px 9px; border-radius:5px; text-transform:uppercase; }
.rc-pass { background:#dcfce7; color:#15803d; }
.rc-fail { background:#fee2e2; color:#991b1b; }
</style>
@endpush

@section('content')

<div class="filter-row">
  <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;width:100%;">
    <select name="source" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;">
      <option value="">All Sources</option>
      <option value="direct" {{ request('source')==='direct' ? 'selected' : '' }}>Direct</option>
      <option value="walkin" {{ request('source')==='walkin' ? 'selected' : '' }}>Walk-in</option>
    </select>
    <input type="date" name="from" value="{{ request('from') }}" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;">
    <input type="date" name="to" value="{{ request('to') }}" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Student / Cert No." style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--bg-3);color:var(--text);outline:none;flex:1;min-width:160px;">
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
          <th>Result</th>
          <th>Source</th>
          <th>Generated On</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($certificates as $c)
        <tr>
          <td><span class="cert-no">{{ $c->certificate_no }}</span></td>
          <td>
            <div style="font-weight:700;">{{ $c->student_name }}</div>
            <div style="font-size:11px;color:var(--text-2);">{{ $c->enrollment_no ?: 'No Enrollment No' }}</div>
          </td>
          <td style="font-size:12px;">{{ $c->course_name }}</td>
          <td>
            @if($c->result)
              <span class="result-chip {{ $c->result === 'PASS' ? 'rc-pass' : 'rc-fail' }}">{{ $c->result }}</span>
              <div style="font-size:11px;color:var(--text-2);margin-top:2px;">{{ $c->percentage }}% · {{ $c->overall_grade }}</div>
            @else
              <span style="color:var(--text-2);">—</span>
            @endif
          </td>
          <td><span class="source-chip {{ $c->source === 'walkin' ? 'sc-walkin' : 'sc-direct' }}">{{ ucfirst($c->source) }}</span></td>
          <td style="font-size:12px;color:var(--text-2);">{{ $c->created_at->format('d M Y') }}</td>
          <td style="text-align:right;">
            <div style="display:flex;gap:6px;justify-content:flex-end;">
              <a href="{{ route('institute.certificates.marksheet', $c) }}" target="_blank" class="btn btn-outline btn-xs">Marksheet</a>
              <a href="{{ route('institute.certificates.certificate', $c) }}" target="_blank" class="btn btn-outline btn-xs">Certificate</a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <div style="padding:48px;text-align:center;color:var(--text-2);">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.4;margin-bottom:10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <div style="font-size:14px;font-weight:600;">No certificates generated yet</div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($certificates->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--border);">
      {{ $certificates->links() }}
    </div>
  @endif
</div>

@endsection
