@extends('layouts.institute')
@section('title', 'Edit Franchise')
@section('page-title', 'Edit Franchise')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.show', $franchise) }}" class="btn btn-outline btn-sm">Back to Details</a>
@endsection

@section('content')
<form method="POST" action="{{ route('institute.franchises.update', $franchise) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Update Franchise</div>
      <span class="text-xs text-muted">Level ke hisaab se commission field auto-sync hoti hai.</span>
    </div>

    @include('institute.franchises._form')

    <div style="display:flex; justify-content:flex-end; margin-top:18px;">
      <button type="submit" class="btn btn-primary">Update Franchise</button>
    </div>
  </div>
</form>
@endsection
