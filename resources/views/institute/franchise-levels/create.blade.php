@extends('layouts.institute')
@section('title', 'Add Franchise Level')
@section('page-title', 'Add Franchise Level')
@section('topbar-actions')
  <a href="{{ route('institute.franchise-levels.index') }}" class="btn btn-outline btn-sm">Level List</a>
@endsection

@section('content')
<form method="POST" action="{{ route('institute.franchise-levels.store') }}">
  @csrf
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Level Details</div>
      <span class="text-xs text-muted">Yahi commission franchise add karte time auto-fill hoga.</span>
    </div>
    @include('institute.franchise-levels._form')
    <div style="display:flex; justify-content:flex-end; margin-top:18px;">
      <button type="submit" class="btn btn-primary">Create Level</button>
    </div>
  </div>
</form>
@endsection
