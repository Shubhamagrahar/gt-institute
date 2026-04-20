@extends('layouts.institute')
@section('title','Fee Types')
@section('page-title','Fee Types')
@section('topbar-actions')
  <a href="{{ route('institute.fee-types.create') }}" class="btn btn-primary btn-sm">+ Add Fee Type</a>
@endsection

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Fee Types</div>
  </div>
  @if($feeTypes->isEmpty())
    <div class="gt-empty">
      <div class="gt-empty-icon">💰</div>
      <div class="gt-empty-title">No fee types yet</div>
      <a href="{{ route('institute.fee-types.create') }}" class="btn btn-primary btn-sm" style="margin-top:8px;">Add First</a>
    </div>
  @else
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr><th>#</th><th>Name</th><th>Mandatory</th><th>Actions</th></tr></thead>
      <tbody>
        @foreach($feeTypes as $i => $ft)
        <tr>
          <td>{{ $i+1 }}</td>
          <td class="fw-600">{{ $ft->name }}</td>
          <td>
            @if($ft->is_mandatory)
              <span class="badge badge-accent">Mandatory</span>
            @else
              <span class="badge badge-neutral">Optional</span>
            @endif
          </td>
          <td style="display:flex;gap:6px;">
            <a href="{{ route('institute.fee-types.edit', $ft) }}" class="btn btn-outline btn-xs">Edit</a>
            <form method="POST" action="{{ route('institute.fee-types.destroy', $ft) }}"
              onsubmit="return confirm('Delete?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-xs">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection