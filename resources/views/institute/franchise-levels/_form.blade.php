<div class="gt-form-grid-2">
  <div class="gt-form-group">
    <label class="gt-label">Level Name <span style="color:var(--danger)">*</span></label>
    <input type="text" name="name" class="gt-input" value="{{ old('name', $level->name ?? '') }}" required>
    @error('name')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Commission % <span style="color:var(--danger)">*</span></label>
    <input type="number" name="commission_percent" class="gt-input" value="{{ old('commission_percent', $level->commission_percent ?? '') }}" min="0" max="100" step="0.01" required>
    @error('commission_percent')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>

<div class="gt-form-grid-2">
  <div class="gt-form-group">
    <label class="gt-label">Status</label>
    <select name="status" class="gt-select">
      <option value="active" {{ old('status', $level->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $level->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Notes</label>
    <input type="text" name="notes" class="gt-input" value="{{ old('notes', $level->notes ?? '') }}">
    @error('notes')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>
