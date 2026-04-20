@php($isEdit = isset($course))

@once
@push('styles')
<style>
.course-form-shell { display: flex; flex-direction: column; gap: 16px; }

.course-upload-area {
  border: 2px dashed var(--border-2);
  border-radius: var(--radius);
  padding: 16px 14px;
  text-align: center;
  cursor: pointer;
  transition: all var(--transition);
  position: relative;
  background: var(--bg-3);
}

.course-upload-area:hover { border-color: var(--accent); background: var(--accent-bg); }
.course-upload-area.has-image { border-style: solid; border-color: var(--accent); padding: 14px; }

.course-preview-image {
  width: 72px;
  height: 72px;
  object-fit: contain;
  border-radius: var(--radius-sm);
  display: none;
  margin: 0 auto 6px;
}

.course-upload-icon { font-size: 24px; margin-bottom: 6px; opacity: .45; }
.course-upload-text { font-size: 12px; color: var(--text-2); font-weight: 600; }
.course-upload-hint { font-size: 11px; color: var(--text-3); margin-top: 4px; }
.course-change-hint { display:none; font-size: 12px; color: var(--text-2); margin-top: 6px; }
.course-upload-input { display: none; }

.course-fields-card {
  background: linear-gradient(180deg, rgba(255,255,255, .98), rgba(248,250,252, .98));
  border: 1px solid rgba(15, 23, 42, .08);
  border-radius: 20px;
  padding: 20px;
}

.course-fields-grid {
  display: grid;
  grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: 0 14px;
}

.course-span-3 { grid-column: span 3; }
.course-span-4 { grid-column: span 4; }
.course-span-6 { grid-column: span 6; }
.course-span-8 { grid-column: span 8; }
.course-span-12 { grid-column: span 12; }

.course-fields-card .gt-form-group {
  margin-bottom: 10px;
}

.course-fields-card .gt-input,
.course-fields-card .gt-select,
.course-fields-card .gt-textarea {
  padding: 8px 11px;
  font-size: 12.5px;
}

.course-fields-card .gt-textarea {
  min-height: 72px;
}

@media (max-width: 1200px) {
  .course-span-3 { grid-column: span 4; }
}

@media (max-width: 900px) {
  .course-span-3,
  .course-span-4,
  .course-span-6,
  .course-span-8 { grid-column: span 6; }
}

@media (max-width: 640px) {
  .course-fields-card {
    padding: 16px;
  }

  .course-span-3,
  .course-span-4,
  .course-span-6,
  .course-span-8,
  .course-span-12 { grid-column: span 12; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('course-image-input');
  const panel = document.getElementById('course-image-panel');
  const previewImg = document.getElementById('course-preview-image');
  const placeholder = document.getElementById('course-image-placeholder');
  const changeHint = document.getElementById('course-change-hint');

  if (!input || !panel || !previewImg || !placeholder || !changeHint) return;

  function showPreview(file) {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.src = e.target.result;
      panel.classList.add('has-image');
      previewImg.style.display = 'block';
      placeholder.style.display = 'none';
      changeHint.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }

  input.addEventListener('change', function () {
    showPreview(this.files?.[0]);
  });
});
</script>
@endpush
@endonce

<div class="course-form-shell">
  <div class="gt-form-group">
    <label class="gt-label">Course Image</label>
    <div class="course-upload-area {{ !empty($course?->image) ? 'has-image' : '' }}" id="course-image-panel" onclick="document.getElementById('course-image-input').click()">
      <img id="course-preview-image" class="course-preview-image" src="{{ !empty($course?->image) ? asset($course->image) : '#' }}" alt="Preview" style="{{ !empty($course?->image) ? 'display:block;' : '' }}">
      <div id="course-image-placeholder" style="{{ !empty($course?->image) ? 'display:none;' : '' }}">
        <div class="course-upload-icon">📘</div>
        <div class="course-upload-text">Click to upload course image</div>
        <div class="course-upload-hint">PNG · JPG · WebP · Max 2MB · Stored in `course-images` folder</div>
      </div>
      <div class="course-change-hint" id="course-change-hint" style="{{ !empty($course?->image) ? 'display:block;' : '' }}">Click to change course image</div>
    </div>
    <input type="file" name="image" id="course-image-input" class="course-upload-input" accept="image/*">
    @error('image')<div class="gt-error" style="margin-top:10px;">{{ $message }}</div>@enderror
  </div>

  <div class="course-fields-card">
    <div class="course-fields-grid">
      <div class="gt-form-group course-span-6">
        <label class="gt-label">Course Name <span style="color:var(--danger)">*</span></label>
        <input type="text" name="name" class="gt-input" value="{{ old('name', $course->name ?? '') }}" required>
        @error('name')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group course-span-3">
        <label class="gt-label">Course Code</label>
        <input type="text" name="course_code" class="gt-input" value="{{ old('course_code', $course->course_code ?? '') }}" placeholder="e.g. DCA-001">
        @error('course_code')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group course-span-3">
        <label class="gt-label">Course Short Name</label>
        <input type="text" name="course_short_name" class="gt-input" value="{{ old('course_short_name', $course->course_short_name ?? '') }}" placeholder="e.g. DCA">
        @error('course_short_name')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group course-span-3">
        <label class="gt-label">Course Type</label>
        <select name="course_type_id" class="gt-select">
          <option value="">-- Select Course Type --</option>
          @foreach($types as $t)
          <option value="{{ $t->id }}" {{ old('course_type_id', $course->course_type_id ?? '') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
          @endforeach
        </select>
        @error('course_type_id')<div class="gt-error">{{ $message }}</div>@enderror
        <div class="text-xs text-muted" style="margin-top:6px;">
          Need a new type?
          <a href="{{ route('institute.course-types.index') }}">Manage course types</a>
        </div>
      </div>

      <div class="gt-form-group course-span-3">
        <label class="gt-label">Duration (months) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="duration" class="gt-input" value="{{ old('duration', $course->duration ?? 6) }}" min="1" required>
        @error('duration')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group course-span-3">
        <label class="gt-label">Max Fee (Rs.) @if(\App\Models\CourseDetail::hasMaxFeeColumn())<span style="color:var(--danger)">*</span>@endif</label>
        <input type="number" name="max_fee" class="gt-input" value="{{ old('max_fee', $course->display_max_fee ?? $course->fee ?? 0) }}" min="0" step="0.01" @if(\App\Models\CourseDetail::hasMaxFeeColumn()) required @endif>
        @if(!\App\Models\CourseDetail::hasMaxFeeColumn())
          <div class="text-xs text-muted" style="margin-top:6px;">Database me `max_fee` column abhi nahi hai, isliye abhi ye value display-only fallback mode me hai.</div>
        @endif
        @error('max_fee')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group course-span-3">
        <label class="gt-label">Fee (Rs.) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="fee" class="gt-input" value="{{ old('fee', $course->fee ?? 0) }}" min="0" step="0.01" required>
        @error('fee')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-group course-span-12">
        <label class="gt-label">Description</label>
        <textarea name="description" class="gt-textarea">{{ old('description', $course->description ?? '') }}</textarea>
        @error('description')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      @if($isEdit)
      <div class="gt-form-group course-span-3">
        <label class="gt-label">Status</label>
        <select name="status" class="gt-select">
          <option value="active" {{ old('status', $course->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status', $course->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      @endif
    </div>
  </div>
</div>
