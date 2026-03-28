@extends('layouts.owner')
@section('title', isset($plan) ? 'Edit Plan' : 'Create Plan')
@section('page-title', isset($plan) ? 'Edit Plan' : 'Create Plan')
@section('topbar-actions')
  <a href="{{ route('owner.plans.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="gt-card" style="max-width:700px;">
  <form method="POST" action="{{ isset($plan) ? route('owner.plans.update',$plan) : route('owner.plans.store') }}">
    @csrf
    @if(isset($plan)) @method('PUT') @endif

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Plan Name <span style="color:var(--danger)">*</span></label>
        <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
          value="{{ old('name', $plan->name ?? '') }}" placeholder="e.g. Basic Plan" required>
        @error('name')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Status</label>
        <select name="status" class="gt-select">
          <option value="active"   {{ old('status', $plan->status ?? 'active')   === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status', $plan->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
    </div>

    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Price (₹) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="price" class="gt-input @error('price') is-invalid @enderror"
          value="{{ old('price', $plan->price ?? '') }}" min="0" step="0.01" required>
        @error('price')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Duration (months) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="duration" class="gt-input @error('duration') is-invalid @enderror"
          value="{{ old('duration', $plan->duration ?? '12') }}" min="1" required>
        @error('duration')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="gt-form-group">
      <label class="gt-label">Description</label>
      <textarea name="description" class="gt-textarea">{{ old('description', $plan->description ?? '') }}</textarea>
    </div>

    <div class="gt-form-section">Included Features</div>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:8px; margin-bottom:20px; margin-top:12px;">
      @foreach($features as $feature)
      <label class="gt-check {{ in_array($feature->id, $planFeatures ?? []) || in_array($feature->id, (array)old('features',[])) ? 'checked' : '' }}">
        <input type="checkbox" name="features[]" value="{{ $feature->id }}"
          {{ in_array($feature->id, $planFeatures ?? []) || in_array($feature->id, (array)old('features',[])) ? 'checked' : '' }}>
        <div>
          <div class="gt-check-label">{{ $feature->name }}</div>
          <div class="gt-check-price">₹{{ number_format($feature->price,2) }}</div>
        </div>
      </label>
      @endforeach
    </div>

    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">{{ isset($plan) ? 'Update Plan' : 'Create Plan' }}</button>
      <a href="{{ route('owner.plans.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
