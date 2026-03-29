@extends('layouts.institute')
@section('title','Sessions')
@section('page-title','Sessions')
@section('topbar-actions')
  <a href="{{ route('institute.sessions.create') }}" class="btn btn-primary btn-sm">+ New Session</a>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-session-delete]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const sessionName = this.dataset.sessionName || 'this session';

      Swal.fire({
        title: 'Delete session?',
        text: `Do you want to permanently delete ${sessionName}? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Sessions</div>
    <span class="text-xs text-muted">{{ $sessions->count() }} total</span>
  </div>

  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Session Name</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sessions as $s)
        <tr>
          <td class="fw-600">{{ $s->name }}</td>
          <td class="text-sm">{{ $s->start_date->format('d M Y') }}</td>
          <td class="text-sm">{{ $s->end_date->format('d M Y') }}</td>
          <td>
            @if($s->is_active)
              <span class="badge badge-success">● Active</span>
            @else
              <span class="badge badge-neutral">Inactive</span>
            @endif
          </td>
          <td style="display:flex;gap:6px;align-items:center;">
            @if(!$s->is_active)
              {{-- Activate button --}}
              <form method="POST" action="{{ route('institute.sessions.toggle', $s) }}">
                @csrf @method('PATCH')
                <button class="btn btn-success btn-xs">Set Active</button>
              </form>
              {{-- Delete button --}}
              <form
                method="POST"
                action="{{ route('institute.sessions.destroy', $s) }}"
                data-session-delete
                data-session-name="{{ $s->name }}"
              >
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-xs">Delete</button>
              </form>
            @else
              <span class="text-xs text-muted">Current session</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="5">
          <div class="gt-empty">
            <div class="gt-empty-icon">📅</div>
            <div class="gt-empty-title">No sessions yet</div>
            <a href="{{ route('institute.sessions.create') }}" class="btn btn-primary btn-sm" style="margin-top:8px;">Create Session</a>
          </div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
