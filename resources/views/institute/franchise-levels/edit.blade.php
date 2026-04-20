@extends('layouts.institute')
@section('title', 'Edit Franchise Level')
@section('page-title', 'Edit Franchise Level')
@section('topbar-actions')
  <a href="{{ route('institute.franchise-levels.index') }}" class="btn btn-outline btn-sm">Level List</a>
@endsection

@section('content')
<form method="POST" action="{{ route('institute.franchise-levels.update', $level) }}">
  @csrf
  @method('PUT')
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Update Level</div>
    </div>
    @include('institute.franchise-levels._form')
    <div style="display:flex; justify-content:flex-end; margin-top:18px;">
      <button type="submit" class="btn btn-primary">Update Level</button>
    </div>
  </div>
</form>
@endsection
