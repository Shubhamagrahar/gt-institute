@extends('layouts.institute')
@section('title', 'Add Franchise')
@section('page-title', 'Add Franchise')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.index') }}" class="btn btn-outline btn-sm">Back to Franchises</a>
@endsection

@section('content')
<form method="POST" action="{{ route('institute.franchises.store') }}" enctype="multipart/form-data">
  @csrf

  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Franchise Details</div>
      <span class="text-xs text-muted">Pehle level choose hoga, commission auto-fill hoga, wallet system bhi yahin decide hoga.</span>
    </div>

    @include('institute.franchises._form')

    <div style="display:flex; justify-content:flex-end; margin-top:18px;">
      <button type="submit" class="btn btn-primary">Create Franchise & Send Credentials</button>
    </div>
  </div>
</form>
@endsection
