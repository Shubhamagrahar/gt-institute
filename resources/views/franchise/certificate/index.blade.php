@extends('layouts.franchise')
@section('title','My Certificate')
@section('page-title','Franchise Certificate')

@push('styles')
<style>
.cert-page{max-width:700px;margin:0 auto}
.cert-card{background:var(--bg-2);border:1px solid var(--border);border-radius:20px;overflow:hidden}
.cert-header{background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 60%,#0f2d1a 100%);padding:28px 32px;color:#fff;text-align:center}
.cert-header h2{font-size:22px;font-weight:900;margin-bottom:6px}
.cert-header p{opacity:.65;font-size:13px}
.cert-body{padding:28px 32px}
.cert-info-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px}
.cert-info-row:last-child{border-bottom:none}
</style>
@endpush

@section('content')
<div class="cert-page">
  <div class="cert-card">
    <div class="cert-header">
      <h2>🏆 Franchise Certificate</h2>
      <p>Your official franchise partnership certificate</p>
    </div>
    <div class="cert-body">
      <div style="margin-bottom:24px">
        <div class="cert-info-row">
          <span class="text-muted">Franchise Name</span>
          <strong>{{ $franchise->name }}</strong>
        </div>
        <div class="cert-info-row">
          <span class="text-muted">Franchise ID</span>
          <span class="mono" style="color:var(--primary)">{{ $franchise->unique_id }}</span>
        </div>
        <div class="cert-info-row">
          <span class="text-muted">Level</span>
          <strong>{{ $franchise->level?->name ?? '—' }}</strong>
        </div>
        <div class="cert-info-row">
          <span class="text-muted">Owner</span>
          <strong>{{ $franchise->owner_name }}</strong>
        </div>
        @if($franchise->address)
        <div class="cert-info-row">
          <span class="text-muted">Address</span>
          <strong>{{ $franchise->address }}{{ $franchise->state ? ', '.$franchise->state : '' }}</strong>
        </div>
        @endif
        <div class="cert-info-row">
          <span class="text-muted">Institute</span>
          <strong>{{ $franchise->institute?->name ?? '—' }}</strong>
        </div>
        <div class="cert-info-row">
          <span class="text-muted">Status</span>
          <span class="badge {{ $franchise->status === 'active' ? 'badge-success' : 'badge-danger' }}">
            {{ ucfirst($franchise->status) }}
          </span>
        </div>
      </div>

      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="{{ route('franchise.certificate.view') }}" target="_blank"
           class="btn btn-primary" style="padding:12px 32px;font-size:15px;background:#16a34a;border-color:#16a34a">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;vertical-align:middle"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          View / Print Certificate
        </a>
      </div>

      <div style="margin-top:20px;padding:14px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;font-size:12px;color:#166534;text-align:center">
        Certificate ko <strong>PDF save</strong> karne ke liye browser ke Print (Ctrl+P) mein <strong>"Save as PDF"</strong> select karo.
      </div>
    </div>
  </div>
</div>
@endsection
