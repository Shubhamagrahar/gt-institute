@extends('layouts.institute')
@section('title', 'Add Channel Partner')
@section('page-title', 'Add Channel Partner')
@section('topbar-actions')
  <a href="{{ route('institute.channel-partners.index') }}" class="btn btn-outline btn-sm">Partner List</a>
@endsection

@section('content')
<form method="POST" action="{{ route('institute.channel-partners.store') }}">
  @csrf
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Partner Details</div>
    </div>
    <div class="gt-card-body">
      @include('institute.channel-partners._form')
      <div style="display:flex;justify-content:flex-end;margin-top:18px;">
        <button type="submit" class="btn btn-primary">Save Partner</button>
      </div>
    </div>
  </div>
</form>
@endsection
