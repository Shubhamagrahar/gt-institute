@extends('layouts.institute')
@section('title','Courses')
@section('page-title','Courses')
@section('topbar-actions')
  <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm">+ Add Course</a>
@endsection
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Courses ({{ $courses->count() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr><th>Name</th><th>Duration</th><th>Fee</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($courses as $c)
        <tr>
          <td>
            <div class="fw-600">{{ $c->name }}</div>
            @if($c->short_name)<div class="text-xs text-muted">{{ $c->short_name }}</div>@endif
          </td>
          <td class="text-muted">{{ $c->duration_months }} months</td>
          <td class="mono text-accent">₹{{ number_format($c->fee,2) }}</td>
          <td><span class="badge {{ $c->status==='active'?'badge-success':'badge-neutral' }}">{{ ucfirst($c->status) }}</span></td>
          <td>
            <div class="flex gap-2">
              <a href="{{ route('institute.courses.edit',$c) }}" class="btn btn-outline btn-xs">Edit</a>
              <form action="{{ route('institute.courses.destroy',$c) }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-xs" data-confirm="Delete course '{{ $c->name }}'?">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5">
          <div class="gt-empty"><div class="gt-empty-icon">📚</div><div class="gt-empty-title">No courses yet</div>
          <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm">Add First Course</a></div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
