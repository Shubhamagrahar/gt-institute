@extends('layouts.owner')
@section('title','Edit — '.$institute->name)
@section('page-title','Edit Institute')
@section('topbar-actions')
  <a href="{{ route('owner.institutes.show',$institute) }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-card" style="max-width:700px;">
  <form method="POST" action="{{ route('owner.institutes.update',$institute) }}">
    @csrf @method('PUT')

    <div class="gt-form-group">
      <label class="gt-label">Institute Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror" value="{{ old('name',$institute->name) }}" required>
      @error('name')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Short Name</label>
        <input type="text" name="short_name" class="gt-input" value="{{ old('short_name',$institute->short_name) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Type</label>
        <select name="type" class="gt-select">
          @foreach(['PRIVATE','GOVT','FRANCHISE'] as $t)
          <option value="{{ $t }}" {{ old('type',$institute->type)===$t?'selected':'' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Mobile</label>
        <input type="text" name="mobile" class="gt-input" value="{{ old('mobile',$institute->mobile) }}" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Website</label>
        <input type="url" name="website" class="gt-input" value="{{ old('website',$institute->website) }}">
      </div>
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">Owner Name</label>
        <input type="text" name="owner_name" class="gt-input" value="{{ old('owner_name',$institute->owner_name) }}" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Owner Mobile</label>
        <input type="text" name="owner_mobile" class="gt-input" value="{{ old('owner_mobile',$institute->owner_mobile) }}" required>
      </div>
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Address</label>
      <textarea name="address" class="gt-textarea">{{ old('address',$institute->address) }}</textarea>
    </div>
    <div class="gt-form-grid-2">
      <div class="gt-form-group">
        <label class="gt-label">State</label>
        <input type="text" name="state" class="gt-input" value="{{ old('state',$institute->state) }}">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">PIN Code</label>
        <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code',$institute->pin_code) }}">
      </div>
    </div>
    <hr class="gt-divider">
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Update Institute</button>
      <a href="{{ route('owner.institutes.show',$institute) }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
