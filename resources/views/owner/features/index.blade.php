{{-- features/index.blade.php --}}
@extends('layouts.owner')
@section('title','Features')
@section('page-title','Features')
@section('topbar-actions')
  <a href="{{ route('owner.features.create') }}" class="btn btn-primary btn-sm">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Feature
  </a>
@endsection
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Features ({{ $features->count() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr>
        <th>#</th><th>Feature Name</th><th>Slug</th><th>Price</th><th>Status</th><th>Actions</th>
      </tr></thead>
      <tbody>
        @forelse($features as $f)
        <tr>
          <td class="text-muted mono">{{ $loop->iteration }}</td>
          <td class="fw-600">{{ $f->name }}</td>
          <td><code style="background:var(--bg-3);padding:2px 7px;border-radius:4px;font-size:11px;color:var(--accent);">{{ $f->slug }}</code></td>
          <td class="mono">₹{{ number_format($f->price,2) }}</td>
          <td>
            <span class="badge {{ $f->status === 'active' ? 'badge-success' : 'badge-neutral' }}">
              {{ ucfirst($f->status) }}
            </span>
          </td>
          <td>
            <div class="flex gap-2">
              <a href="{{ route('owner.features.edit',$f) }}" class="btn btn-outline btn-xs">Edit</a>
              <form action="{{ route('owner.features.toggle',$f) }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn btn-xs {{ $f->status==='active' ? 'btn-warning' : 'btn-success' }}" style="{{ $f->status==='active' ? 'background:var(--warning-bg);color:var(--warning);border:1px solid rgba(255,184,77,.2);' : '' }}">
                  {{ $f->status === 'active' ? 'Disable' : 'Enable' }}
                </button>
              </form>
              <form action="{{ route('owner.features.destroy',$f) }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-xs" data-confirm="Delete feature '{{ $f->name }}'?">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6">
          <div class="gt-empty"><div class="gt-empty-icon">⭐</div><div class="gt-empty-title">No features yet</div>
          <a href="{{ route('owner.features.create') }}" class="btn btn-primary btn-sm">Add Feature</a></div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
