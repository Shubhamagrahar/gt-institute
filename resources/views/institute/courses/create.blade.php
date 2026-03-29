@extends('layouts.institute')
@section('title', 'Add Course')
@section('page-title', 'Add Course')
@section('topbar-actions')
  <a href="{{ route('institute.course-types.index') }}" class="btn btn-outline btn-sm">Course Types</a>
  <a href="{{ route('institute.courses.index') }}" class="btn btn-outline btn-sm">Back</a>
@endsection
@section('content')
<div class="gt-card">
  <form method="POST" action="{{ route('institute.courses.store') }}" enctype="multipart/form-data">
    @csrf
    @include('institute.courses._form')
    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Add Course</button>
      <a href="{{ route('institute.courses.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
