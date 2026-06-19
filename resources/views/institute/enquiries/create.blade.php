@extends('layouts.institute')
@section('title','New Enquiry')
@section('page-title','New Enquiry')

@push('styles')
<style>
.enq-form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:640px){ .enq-form-grid { grid-template-columns:1fr; } }

/* Searchable course select */
.course-search-wrap { position:relative; }
.course-search-input { width:100%; }
.course-dropdown {
  position:absolute; z-index:100; width:100%; top:calc(100% + 4px);
  background:var(--bg); border:1px solid var(--border); border-radius:8px;
  box-shadow:0 8px 24px rgba(0,0,0,.12); max-height:220px; overflow-y:auto;
  display:none;
}
.course-dropdown.open { display:block; }
.course-option {
  padding:9px 14px; font-size:13px; cursor:pointer; border-bottom:1px solid var(--border);
  color:var(--text);
}
.course-option:last-child { border-bottom:none; }
.course-option:hover, .course-option.selected { background:var(--accent-bg); color:var(--accent); }
.course-option.hidden { display:none; }
.course-no-results { padding:10px 14px; font-size:12px; color:var(--text-2); font-style:italic; }
</style>
@endpush

@section('content')

@if(session('success'))
  <div class="gt-alert gt-alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

<div class="gt-card" style="padding:28px;">

  {{-- Duplicate warning --}}
  <div id="dup-warning" style="display:none;margin-bottom:20px;padding:14px 18px;background:#fffbeb;border:1.5px solid #f59e0b;border-radius:10px;">
    <div style="font-weight:700;color:#92400e;margin-bottom:4px;">⚠ Existing open enquiry found for this mobile number</div>
    <div id="dup-detail" style="color:#78350f;font-size:13px;"></div>
    <div style="margin-top:10px;display:flex;gap:8px;">
      <a id="dup-link" href="#" class="btn btn-secondary btn-sm">View Existing Enquiry</a>
      <button type="button" onclick="document.getElementById('dup-warning').style.display='none'" class="btn btn-secondary btn-sm">Ignore & Create New</button>
    </div>
  </div>

  <form method="POST" action="{{ route('institute.enquiries.store') }}" id="enq-form">
    @csrf

    {{-- Row 1: Name + Mobile --}}
    <div class="enq-form-grid" style="margin-bottom:16px;">
      <div class="gt-form-row" style="margin:0;">
        <label class="gt-label">Student Name <span class="gt-required">*</span></label>
        <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
               value="{{ old('name') }}" placeholder="Full name" required autofocus>
        @error('name')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-row" style="margin:0;">
        <label class="gt-label">Mobile Number <span class="gt-required">*</span></label>
        <input type="tel" name="mobile" id="enq-mobile" class="gt-input @error('mobile') is-invalid @enderror"
               value="{{ old('mobile') }}" placeholder="10-digit mobile"
               maxlength="10" inputmode="numeric"
               oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);"
               required>
        @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Row 2: Email + Source --}}
    <div class="enq-form-grid" style="margin-bottom:16px;">
      <div class="gt-form-row" style="margin:0;">
        <label class="gt-label">Email <span style="color:var(--text-2);font-weight:400;">(Optional)</span></label>
        <input type="email" name="email" class="gt-input @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="student@email.com">
        @error('email')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-row" style="margin:0;">
        <label class="gt-label">Source <span class="gt-required">*</span></label>
        <select name="source" class="gt-input @error('source') is-invalid @enderror" required>
          <option value="WALK_IN"   {{ old('source','WALK_IN')==='WALK_IN'   ? 'selected' : '' }}>Walk-in</option>
          <option value="PHONE"     {{ old('source')==='PHONE'     ? 'selected' : '' }}>Phone Call</option>
          <option value="ONLINE"    {{ old('source')==='ONLINE'    ? 'selected' : '' }}>Online / Website</option>
          <option value="REFERENCE" {{ old('source')==='REFERENCE' ? 'selected' : '' }}>Reference</option>
        </select>
        @error('source')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Row 3: Course Type + Duration + Course --}}
    <div style="margin-bottom:16px;">
      <label class="gt-label" style="display:block;margin-bottom:8px;">Course Interest <span style="color:var(--text-2);font-weight:400;">(Optional)</span></label>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">

        <div>
          <label class="gt-label" style="font-size:11px;color:var(--text-2);">Course Type</label>
          <select id="enq-course-type" class="gt-input">
            <option value="">All Types</option>
            @foreach($courseTypes as $ct)
              <option value="{{ $ct->id }}">{{ $ct->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="gt-label" style="font-size:11px;color:var(--text-2);">Duration</label>
          <select id="enq-duration" class="gt-input">
            <option value="">All Durations</option>
          </select>
        </div>

        <div>
          <label class="gt-label" style="font-size:11px;color:var(--text-2);">Course</label>
          {{-- Hidden actual select for form submission --}}
          <select name="course_id" id="enq-course-id" style="display:none;">
            <option value=""></option>
            @foreach($courses as $c)
              <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
          </select>

          <div class="course-search-wrap">
            <input type="text" id="enq-course-search" class="gt-input course-search-input"
                   placeholder="Search course…" autocomplete="off"
                   value="{{ old('course_id') ? optional($courses->find(old('course_id')))->name : '' }}">
            <div class="course-dropdown" id="enq-course-dropdown">
              <div class="course-no-results" id="enq-no-results" style="display:none;">No courses found</div>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- Row 4: Next Followup + Notes --}}
    <div class="enq-form-grid" style="margin-bottom:16px;">
      <div class="gt-form-row" style="margin:0;">
        <label class="gt-label">Next Follow-up Date</label>
        <input type="date" name="next_followup_date" class="gt-input @error('next_followup_date') is-invalid @enderror"
               value="{{ old('next_followup_date', now()->addDay()->format('Y-m-d')) }}"
               min="{{ now()->format('Y-m-d') }}">
        @error('next_followup_date')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div class="gt-form-row" style="margin:0;grid-row:span 1;">
        {{-- spacer --}}
      </div>
    </div>

    <div class="gt-form-row" style="margin-bottom:20px;">
      <label class="gt-label">Notes</label>
      <textarea name="notes" class="gt-input" rows="3"
                placeholder="What did they ask? When do they plan to join? Any budget constraints?">{{ old('notes') }}</textarea>
    </div>

    <div style="display:flex;gap:10px;">
      <button type="submit" class="btn btn-primary">Save Enquiry</button>
      <a href="{{ route('institute.enquiries.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<script>
(function () {
  const catalog       = @json($courseCatalog);
  const typeSelect    = document.getElementById('enq-course-type');
  const durSelect     = document.getElementById('enq-duration');
  const courseIdEl    = document.getElementById('enq-course-id');
  const searchInput   = document.getElementById('enq-course-search');
  const dropdown      = document.getElementById('enq-course-dropdown');
  const noResults     = document.getElementById('enq-no-results');
  const mobileInput   = document.getElementById('enq-mobile');

  let filteredCourses = [...catalog];
  let selectedId      = courseIdEl.value || '';

  // ── Duration options ─────────────────────────────────────────────
  function renderDurations() {
    const typeId = typeSelect.value;
    const durations = [...new Set(
      catalog
        .filter(c => !typeId || String(c.course_type_id) === typeId)
        .map(c => c.duration)
    )].sort((a,b) => a-b);

    durSelect.innerHTML = '<option value="">All Durations</option>'
      + durations.map(d => `<option value="${d}">${d} month${d===1?'':'s'}</option>`).join('');
  }

  // ── Course dropdown ───────────────────────────────────────────────
  function renderCourseDropdown(query='') {
    const typeId = typeSelect.value;
    const dur    = durSelect.value;
    const q      = query.trim().toLowerCase();

    filteredCourses = catalog.filter(c => {
      if (typeId && String(c.course_type_id) !== typeId) return false;
      if (dur    && String(c.duration)       !== dur)    return false;
      if (q      && !c.name.toLowerCase().includes(q))   return false;
      return true;
    });

    // Remove old options (keep no-results div)
    dropdown.querySelectorAll('.course-option').forEach(el => el.remove());
    noResults.style.display = filteredCourses.length === 0 ? 'block' : 'none';

    filteredCourses.forEach(c => {
      const div = document.createElement('div');
      div.className = 'course-option' + (String(c.id) === String(selectedId) ? ' selected' : '');
      div.textContent = c.name + ` (${c.duration}m)`;
      div.dataset.id = c.id;
      div.dataset.name = c.name;
      div.addEventListener('mousedown', (e) => {
        e.preventDefault();
        selectCourse(c.id, c.name);
      });
      dropdown.appendChild(div);
    });
  }

  function selectCourse(id, name) {
    selectedId = id;
    courseIdEl.value = id;
    searchInput.value = name;
    closeDropdown();
  }

  function clearCourse() {
    selectedId = '';
    courseIdEl.value = '';
  }

  function openDropdown() {
    renderCourseDropdown(searchInput.value);
    dropdown.classList.add('open');
  }

  function closeDropdown() {
    dropdown.classList.remove('open');
  }

  searchInput.addEventListener('focus', openDropdown);
  searchInput.addEventListener('input', () => {
    clearCourse();
    renderCourseDropdown(searchInput.value);
    dropdown.classList.add('open');
  });
  searchInput.addEventListener('blur', () => {
    setTimeout(closeDropdown, 150);
  });

  typeSelect.addEventListener('change', () => {
    durSelect.value = '';
    renderDurations();
    clearCourse();
    searchInput.value = '';
    renderCourseDropdown('');
  });

  durSelect.addEventListener('change', () => {
    clearCourse();
    searchInput.value = '';
    renderCourseDropdown('');
  });

  // Close dropdown on outside click
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target) && e.target !== searchInput) closeDropdown();
  });

  // Init
  renderDurations();
  renderCourseDropdown('');

  // ── Duplicate mobile check ───────────────────────────────────────
  let dupTimer;
  mobileInput.addEventListener('input', function () {
    clearTimeout(dupTimer);
    const val = this.value.trim();
    if (val.length !== 10) {
      document.getElementById('dup-warning').style.display = 'none';
      return;
    }
    dupTimer = setTimeout(() => checkDuplicate(val), 500);
  });

  function checkDuplicate(mobile) {
    fetch(`{{ route('institute.enquiries.check-duplicate') }}?mobile=${mobile}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.found) {
        document.getElementById('dup-detail').textContent =
          `${data.name} · ${data.course} · ${data.date}`;
        document.getElementById('dup-link').href = `{{ url('dashboard/enquiries') }}/${data.id}`;
        document.getElementById('dup-warning').style.display = 'block';
      } else {
        document.getElementById('dup-warning').style.display = 'none';
      }
    });
  }
})();
</script>
@endsection
