@extends('layouts.institute')
@section('title', 'Edit Channel Partner')
@section('page-title', 'Edit Channel Partner')
@section('topbar-actions')
  <a href="{{ route('institute.channel-partners.index') }}" class="btn btn-outline btn-sm">Partner List</a>
@endsection

@section('content')
<form method="POST" action="{{ route('institute.channel-partners.update', $channelPartner) }}">
  @csrf
  @method('PUT')
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Update Partner</div>
    </div>
    <div class="gt-card-body">
      @include('institute.channel-partners._form')
      <div style="display:flex;justify-content:flex-end;margin-top:18px;">
        <button type="submit" class="btn btn-primary">Update Partner</button>
      </div>
    </div>
  </div>
</form>
@endsection
