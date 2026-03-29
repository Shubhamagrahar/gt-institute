@extends('layouts.institute')
@section('title', 'Edit Course')
@section('page-title', 'Edit Course')
@section('topbar-actions')
  <a href="{{ route('institute.course-types.index') }}" class="btn btn-outline btn-sm">Course Types</a>
  <a href="{{ route('institute.courses.index') }}" class="btn btn-outline btn-sm">Back</a>
@endsection
@section('content')
<div class="gt-card">
  <form method="POST" action="{{ route('institute.courses.update', $course) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('institute.courses._form')
    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Update Course</button>
      <a href="{{ route('institute.courses.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
