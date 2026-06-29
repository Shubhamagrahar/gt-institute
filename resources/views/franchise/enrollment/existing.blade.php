@extends('layouts.franchise')
@section('title','Enroll Existing Student')
@section('page-title','Enroll Existing Student')
@section('topbar-actions')
  <a href="{{ route('franchise.enrollment.choose') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div style="max-width:700px;margin:0 auto;">

  {{-- Student info card --}}
  <div style="background:rgba(234,88,12,.06);border:1px solid rgba(234,88,12,.2);border-radius:16px;padding:18px 22px;margin-bottom:20px;display:flex;align-items:center;gap:16px;">
    <img src="{{ $student->profile?->photo ? asset($student->profile->photo) : asset('images/user.svg') }}"
         style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid rgba(234,88,12,.3);"
         onerror="this.src='{{ asset('images/user.svg') }}'">
    <div>
      <div style="font-size:17px;font-weight:800;color:var(--text-1);">{{ $student->profile?->name ?? $student->user_id }}</div>
      <div style="font-size:12px;color:var(--text-2);margin-top:2px;">
        {{ $student->user_id }} &middot; {{ $student->mobile }}
        @if($student->profile?->gender) &middot; {{ $student->profile->gender }}@endif
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('franchise.enrollment.store-existing') }}" class="gt-card">
    @csrf
    <input type="hidden" name="student_id" value="{{ $student->id }}">
    <div class="gt-card-body" style="padding:22px;">
      <div class="gt-card-title" style="margin-bottom:16px;">Select New Course</div>
      <div class="gt-form-grid-3">
        <div class="gt-form-group">
          <label class="gt-label">Course Type <span style="color:var(--danger)">*</span></label>
          <select id="course_type_id" class="gt-select" required>
            <option value="">Select Course Type</option>
            @foreach($courseTypes as $ct)
              <option value="{{ $ct->id }}">{{ $ct->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Duration <span style="color:var(--danger)">*</span></label>
          <select id="course_duration_filter" class="gt-select" required>
            <option value="">Select Duration</option>
          </select>
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
          <select name="course_id" id="course_id" class="gt-select" required style="display:none;">
            <option value="">Select Course</option>
          </select>
          <div style="position:relative;">
            <input type="text" id="course_search_display" class="gt-input"
              placeholder="Search &amp; select course…" autocomplete="off" style="width:100%;cursor:pointer;">
            <div id="course_search_dropdown"
              style="display:none;position:absolute;z-index:200;width:100%;top:calc(100% + 3px);background:var(--bg);border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>
          </div>
          @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Batch</label>
          <select name="batch_id" class="gt-select">
            <option value="">No Batch</option>
            @foreach($batches as $b)
              <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
        <a href="{{ route('franchise.enrollment.choose') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary" style="background:#ea580c;border-color:#ea580c;">Enroll →</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
  function escapeHtml(s){ const d=document.createElement('div');d.appendChild(document.createTextNode(String(s||'')));return d.innerHTML; }
  const catalog=@json($courseCatalog);
  const ctSel=document.getElementById('course_type_id');
  const durSel=document.getElementById('course_duration_filter');
  const crsId=document.getElementById('course_id');
  const crsDisp=document.getElementById('course_search_display');
  const crsDrop=document.getElementById('course_search_dropdown');
  let pool=[];

  function renderDurations(){
    const tid=ctSel.value;
    const durs=[...new Set(catalog.filter(c=>String(c.course_type_id)===String(tid)).map(c=>Number(c.duration)).filter(Boolean))].sort((a,b)=>a-b);
    durSel.innerHTML='<option value="">Select Duration</option>'+durs.map(d=>`<option value="${d}">${d} month${d===1?'':'s'}</option>`).join('');
  }
  function renderCourses(){
    const tid=ctSel.value,dur=durSel.value;
    pool=catalog.filter(c=>String(c.course_type_id)===String(tid)&&String(c.duration)===String(dur));
    crsId.innerHTML='<option value="">Select Course</option>'+pool.map(c=>`<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
    crsDisp.value='';crsDrop.style.display='none';
  }
  function renderDropdown(q=''){
    const f=q?pool.filter(c=>c.name.toLowerCase().includes(q.toLowerCase())):pool;
    crsDrop.innerHTML=f.length?f.map(c=>`<div class="crs-opt" data-id="${c.id}" data-name="${escapeHtml(c.name)}" style="padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);">${escapeHtml(c.name)} <span style="color:var(--text-3);font-size:11px;">(${c.duration}m)</span></div>`).join(''):'<div style="padding:10px 14px;font-size:12px;color:var(--text-2);">No courses found</div>';
    crsDrop.querySelectorAll('.crs-opt').forEach(d=>{
      d.addEventListener('mousedown',e=>{e.preventDefault();crsId.value=d.dataset.id;crsDisp.value=d.dataset.name;crsDrop.style.display='none';});
    });
    crsDrop.style.display='block';
  }
  crsDisp.addEventListener('focus',()=>pool.length&&renderDropdown(crsDisp.value));
  crsDisp.addEventListener('input',()=>{crsId.value='';renderDropdown(crsDisp.value);});
  crsDisp.addEventListener('blur',()=>setTimeout(()=>crsDrop.style.display='none',150));
  document.addEventListener('click',e=>{if(!crsDrop.contains(e.target)&&e.target!==crsDisp)crsDrop.style.display='none';});
  ctSel.addEventListener('change',()=>{renderDurations();renderCourses();});
  durSel.addEventListener('change',()=>renderCourses());
})();
</script>
@endpush
