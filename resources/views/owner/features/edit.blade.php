@extends('layouts.owner')
@section('title', isset($feature) ? 'Edit Feature' : 'Add Feature')
@section('page-title', isset($feature) ? 'Edit Feature' : 'Add Feature')
@section('topbar-actions')
  <a href="{{ route('owner.features.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="gt-card" style="max-width:560px;">
  <form method="POST" action="{{ isset($feature) ? route('owner.features.update',$feature) : route('owner.features.store') }}">
    @csrf
    @if(isset($feature)) @method('PUT') @endif

    <div class="gt-form-group">
      <label class="gt-label">Feature Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
        value="{{ old('name', $feature->name ?? '') }}" placeholder="e.g. LMS System" required>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Description</label>
      <textarea name="description" class="gt-textarea" placeholder="Brief description of this feature...">{{ old('description', $feature->description ?? '') }}</textarea>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Price (₹) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="price" class="gt-input @error('price') is-invalid @enderror"
          value="{{ old('price', $feature->price ?? '0') }}" min="0" step="0.01" required>
        @error('price')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group">
        <label class="gt-label">Status</label>
        <select name="status" class="gt-select">
          <option value="active"   {{ old('status', $feature->status ?? 'active') === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status', $feature->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
    </div>

    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">
        {{ isset($feature) ? 'Update Feature' : 'Add Feature' }}
      </button>
      <a href="{{ route('owner.features.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
