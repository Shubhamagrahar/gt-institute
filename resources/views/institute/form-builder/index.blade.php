@extends('layouts.institute')
@section('title','Form Builder')
@section('page-title','Form Builder')

@push('styles')
<style>
.fb-home{max-width:980px;margin:0 auto}
.fb-home-hero{background:linear-gradient(135deg,#114aa4,#1b78d3);border-radius:20px;padding:22px 24px;color:#fff;box-shadow:0 18px 40px rgba(17,74,164,.16);margin-bottom:16px}
.fb-home-hero h2{margin:0;font-size:24px;font-weight:900}
.fb-home-hero p{margin:6px 0 0;opacity:.88;line-height:1.55;max-width:700px;font-size:13px}
.fb-home-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.fb-home-card{display:flex;flex-direction:column;gap:12px;padding:20px;border-radius:18px;background:var(--bg-2);border:1px solid var(--border);text-decoration:none;color:inherit;box-shadow:0 14px 36px rgba(15,23,42,.06)}
.fb-home-card:hover{transform:translateY(-2px);transition:.2s ease}
.fb-home-icon{width:50px;height:50px;border-radius:14px;display:grid;place-items:center;font-size:21px;font-weight:900}
.fb-home-card h3{margin:0;font-size:18px;font-weight:900}
.fb-home-card p{margin:0;color:var(--text-2);line-height:1.55;font-size:12px}
.fb-home-action{margin-top:4px}
.fb-home-action .btn{font-size:12px;padding:10px 14px;border-radius:12px}
@media(max-width:760px){.fb-home-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="fb-home">
  <div class="fb-home-hero">
    <h2>Choose Builder</h2>
    <p>Manage Admission Form and Quick Form on separate pages with independent settings and live preview.</p>
  </div>

  <div class="fb-home-grid">
    <a href="{{ route('institute.form-builder.admission') }}" class="fb-home-card">
      <div class="fb-home-icon" style="background:rgba(27,120,211,.12);color:#1b78d3;">A</div>
      <h3>Admission Form Builder</h3>
      <p>Control detailed fields for the full admission and full seat-booking flow.</p>
      <div class="fb-home-action">
        <span class="btn btn-outline btn-sm">Open Builder</span>
      </div>
    </a>

    <a href="{{ route('institute.form-builder.quick') }}" class="fb-home-card">
      <div class="fb-home-icon" style="background:rgba(249,115,22,.12);color:#c2410c;">Q</div>
      <h3>Quick Form Builder</h3>
      <p>Design and save a compact form for fast seat-booking and counter rush.</p>
      <div class="fb-home-action">
        <span class="btn btn-outline btn-sm">Open Builder</span>
      </div>
    </a>
  </div>
</div>
@endsection
