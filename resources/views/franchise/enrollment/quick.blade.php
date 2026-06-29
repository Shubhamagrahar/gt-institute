@extends('layouts.franchise')
@section('title','Quick Booking')
@section('page-title','Quick Seat Booking')
@section('topbar-actions')
  <a href="{{ route('franchise.enrollment.choose') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div style="max-width:900px;margin:0 auto;">

  <div style="background:rgba(234,88,12,.06);border:1px solid rgba(234,88,12,.18);border-radius:14px;padding:14px 20px;margin-bottom:20px;font-size:13px;color:var(--text-2);">
    Basic details only — seat is saved instantly. Fill complete profile from the admission list later.
  </div>

  <form method="POST" action="{{ route('franchise.enrollment.store-quick') }}" class="gt-card" autocomplete="off">
    @csrf
    <div class="gt-card-body" style="padding:22px;">
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
              placeholder="Search &amp; select course…" autocomplete="off"
              style="width:100%;cursor:pointer;">
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
              <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Student Name <span style="color:var(--danger)">*</span></label>
          <input type="text" name="name" class="gt-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
            value="{{ old('name') }}" autocomplete="off" required>
          @error('name')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
          <input type="tel" name="mobile" id="mobile" class="gt-input {{ $errors->has('mobile') ? 'is-invalid' : '' }}"
            value="{{ old('mobile') }}" autocomplete="off" required
            maxlength="10" inputmode="numeric"
            oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
          <div id="mobile-error" style="display:none;color:var(--danger);font-size:12px;margin-top:3px;"></div>
          @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Email</label>
          <input type="email" name="email" class="gt-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
            value="{{ old('email') }}" autocomplete="off">
          @error('email')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

      </div>

      {{-- Fee preview --}}
      <div id="fee-breakdown-box" style="display:none;background:rgba(234,88,12,.04);border:1px solid rgba(234,88,12,.15);border-radius:10px;padding:14px 18px;margin-top:16px;">
        <div style="font-size:11px;font-weight:700;color:var(--text-3);letter-spacing:.4px;text-transform:uppercase;margin-bottom:10px;">Fee Breakdown (Your Pricing)</div>
        <div id="fee-breakdown-rows"></div>
        <div style="border-top:1px solid rgba(234,88,12,.15);margin-top:10px;padding-top:10px;display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:13px;font-weight:700;">Total Payable</span>
          <span id="fee-breakdown-total" style="font-size:18px;font-weight:900;color:#ea580c;">₹0</span>
        </div>
      </div>

      <div style="display:flex;justify-content:flex-end;margin-top:20px;gap:10px;">
        <a href="{{ route('franchise.enrollment.choose') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary" style="background:#ea580c;border-color:#ea580c;">Save Quick Booking →</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
  function escapeHtml(s){ const d=document.createElement('div');d.appendChild(document.createTextNode(String(s||'')));return d.innerHTML; }
  const catalog = @json($courseCatalog);
  const ctSel   = document.getElementById('course_type_id');
  const durSel  = document.getElementById('course_duration_filter');
  const crsId   = document.getElementById('course_id');
  const crsDisp = document.getElementById('course_search_display');
  const crsDrop = document.getElementById('course_search_dropdown');
  let   pool    = [];

  function renderDurations() {
    const tid = ctSel.value;
    const durs = [...new Set(
      catalog.filter(c => String(c.course_type_id) === String(tid))
        .map(c => Number(c.duration)).filter(Boolean)
    )].sort((a,b)=>a-b);
    durSel.innerHTML = '<option value="">Select Duration</option>' +
      durs.map(d => `<option value="${d}">${d} month${d===1?'':'s'}</option>`).join('');
  }

  function renderCourses() {
    const tid=ctSel.value, dur=durSel.value;
    pool = catalog.filter(c => String(c.course_type_id)===String(tid) && String(c.duration)===String(dur));
    crsId.innerHTML = '<option value="">Select Course</option>' + pool.map(c=>`<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
    crsDisp.value=''; crsDrop.style.display='none';
  }

  function renderDropdown(q='') {
    const f = q ? pool.filter(c=>c.name.toLowerCase().includes(q.toLowerCase())) : pool;
    crsDrop.innerHTML = f.length
      ? f.map(c=>`<div class="crs-opt" data-id="${c.id}" data-name="${escapeHtml(c.name)}"
          style="padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);">
          ${escapeHtml(c.name)} <span style="color:var(--text-3);font-size:11px;">(${c.duration}m)</span>
        </div>`).join('')
      : '<div style="padding:10px 14px;font-size:12px;color:var(--text-2);">No courses found</div>';
    crsDrop.querySelectorAll('.crs-opt').forEach(d => {
      d.addEventListener('mousedown', e => {
        e.preventDefault();
        crsId.value=d.dataset.id; crsDisp.value=d.dataset.name;
        crsDrop.style.display='none';
        renderFee(d.dataset.id);
      });
    });
    crsDrop.style.display='block';
  }

  crsDisp.addEventListener('focus', ()=>pool.length&&renderDropdown(crsDisp.value));
  crsDisp.addEventListener('input', ()=>{ crsId.value=''; renderDropdown(crsDisp.value); });
  crsDisp.addEventListener('blur',  ()=>setTimeout(()=>crsDrop.style.display='none',150));
  document.addEventListener('click', e=>{ if(!crsDrop.contains(e.target)&&e.target!==crsDisp) crsDrop.style.display='none'; });
  ctSel.addEventListener('change',  ()=>{ renderDurations(); renderCourses(); });
  durSel.addEventListener('change', ()=>renderCourses());

  function renderFee(id) {
    const c=catalog.find(c=>String(c.id)===String(id));
    const box=document.getElementById('fee-breakdown-box');
    if (!c||!c.fee_items?.length){box.style.display='none';return;}
    document.getElementById('fee-breakdown-rows').innerHTML=c.fee_items.map(f=>
      `<div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;">
        <span style="color:var(--text-2);">${f.fee_type_name}</span>
        <span style="font-weight:600;">₹${Number(f.amount).toLocaleString('en-IN')}</span>
      </div>`).join('');
    document.getElementById('fee-breakdown-total').textContent='₹'+Number(c.total_fee).toLocaleString('en-IN');
    box.style.display='block';
  }

  const mob=document.getElementById('mobile');
  mob?.addEventListener('blur',()=>{
    const e=document.getElementById('mobile-error');
    if(mob.value&&mob.value.length!==10){
      mob.style.borderColor='var(--danger)';e.textContent='Mobile must be 10 digits.';e.style.display='block';
    }else{mob.style.borderColor='';e.style.display='none';}
  });
})();
</script>
@endpush
