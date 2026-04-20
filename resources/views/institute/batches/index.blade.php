@extends('layouts.institute')
@section('title','Batch Setup')
@section('page-title','Batch Setup')

@push('styles')
<style>
.batch-layout{display:grid;grid-template-columns:380px minmax(0,1fr);gap:20px;align-items:start}
.batch-form-card{position:sticky;top:18px}
.batch-time{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:960px){.batch-layout{grid-template-columns:1fr}.batch-form-card{position:static}}
</style>
@endpush

@section('content')
<div class="batch-layout">
  <div class="gt-card batch-form-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Add Batch</div>
        <div class="text-muted text-xs" style="margin-top:4px;">This batch will be available in the registration form.</div>
      </div>
      <span class="badge badge-accent">Create</span>
    </div>

    @if(session('success'))
      <div class="gt-alert gt-alert-success" style="margin-bottom:14px;">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('institute.batches.store') }}">
      @csrf

      <div class="gt-form-group">
        <label class="gt-label">Batch Name <span style="color:var(--danger)">*</span></label>
        <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Morning Batch, Evening Batch" required autofocus>
        @error('name')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="batch-time">
        <div class="gt-form-group">
          <label class="gt-label">Start Time</label>
          <input type="time" name="start_time" class="gt-input @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}">
          @error('start_time')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
        <div class="gt-form-group">
          <label class="gt-label">End Time</label>
          <input type="time" name="end_time" class="gt-input @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}">
          @error('end_time')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Status <span style="color:var(--danger)">*</span></label>
        <select name="status" class="gt-select @error('status') is-invalid @enderror" required>
          <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">Create Batch</button>
    </form>
  </div>

  <div class="gt-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Batch List</div>
        <div class="text-muted text-xs" style="margin-top:4px;">Active batches are available for admission selection.</div>
      </div>
      <span class="badge badge-accent">{{ $batches->count() }} Batches</span>
    </div>

    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead>
          <tr>
            <th>Batch</th>
            <th>Timing</th>
            <th>Status</th>
            <th>Created</th>
            <th style="width:170px;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($batches as $batch)
            <tr>
              <td class="fw-600">{{ $batch->name }}</td>
              <td>
                @if($batch->start_time || $batch->end_time)
                  {{ $batch->start_time ? \Illuminate\Support\Carbon::parse($batch->start_time)->format('h:i A') : '-' }}
                  -
                  {{ $batch->end_time ? \Illuminate\Support\Carbon::parse($batch->end_time)->format('h:i A') : '-' }}
                @else
                  <span class="text-muted">No timing</span>
                @endif
              </td>
              <td><span class="badge badge-accent">{{ ucfirst($batch->status ?? 'active') }}</span></td>
              <td>{{ $batch->created_at?->format('d M Y') ?? '-' }}</td>
              <td>
                <div style="display:flex;gap:8px;align-items:center;">
                  <form method="POST" action="{{ route('institute.batches.toggle', $batch) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline btn-xs">{{ $batch->status === 'active' ? 'Inactive' : 'Active' }}</button>
                  </form>
                  <form method="POST" action="{{ route('institute.batches.destroy', $batch) }}" onsubmit="return confirm('Delete this batch?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-muted">No batches are available yet. Create the first batch from the form.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
