@extends('layouts.franchise')
@section('title','New Admission')
@section('page-title','New Admission')
@section('topbar-actions')
  <a href="{{ route('franchise.enrollment.choose') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@push('styles')
<style>
.adm-shell{max-width:1160px;margin:0 auto}
.adm-header{background:linear-gradient(135deg,#ea580c,#c2410c);color:#fff;border-radius:22px;padding:26px 30px;box-shadow:0 20px 45px rgba(234,88,12,.18)}
.adm-header h2{margin:0;font-size:28px;font-weight:900}
.adm-header p{margin:8px 0 0;opacity:.84;font-size:13px}
.adm-wrap{display:block;margin-top:18px}
.adm-card{background:var(--bg-2);border:1px solid var(--border);border-radius:20px;overflow:hidden}
.adm-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;padding:22px;background:rgba(234,88,12,.04)}
.adm-step{padding:12px 8px;border-radius:14px;background:rgba(234,88,12,.08);color:rgba(180,72,10,.7);text-align:center;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;transition:.2s;cursor:default}
.adm-step.active{background:#ea580c;color:#fff}
.adm-step.done{background:rgba(234,88,12,.15);color:#ea580c}
.adm-body{padding:24px}
.wizard-step{display:none}
.wizard-step.active{display:block}
.adm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.adm-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.adm-section-title{font-size:20px;font-weight:900;margin:0 0 6px;color:var(--text-1)}
.adm-section-note{font-size:13px;color:var(--text-2);margin-bottom:20px}

/* Fee inline panel — same structure as institute but orange */
.inline-fee-panel{margin-top:18px;background:rgba(234,88,12,.04);border:1px solid rgba(234,88,12,.14);border-radius:18px;padding:18px}
.inline-fee-title{font-size:12px;font-weight:800;color:var(--text-3);letter-spacing:.08em;text-transform:uppercase;margin-bottom:12px}
.fee-lines{display:flex;flex-direction:column;gap:10px}
.fee-line{display:flex;justify-content:space-between;font-size:13px}
.fee-line strong{font-weight:800}
.fee-total{margin-top:12px;padding-top:12px;border-top:1px solid rgba(234,88,12,.18);font-size:17px;font-weight:900;color:#ea580c}
.pay-note{margin-top:16px;padding:14px;border-radius:14px;background:rgba(234,88,12,.05);color:var(--text-2);font-size:13px;line-height:1.5;border:1px solid rgba(234,88,12,.1)}

/* Basic step — same two-column layout as institute */
.basic-layout{display:grid;grid-template-columns:260px minmax(0,1fr);gap:20px;align-items:start}
.basic-side-stack{display:grid;gap:14px}
.photo-box{border:1px dashed rgba(234,88,12,.3);border-radius:18px;padding:18px;background:rgba(234,88,12,.03);text-align:center;display:flex;flex-direction:column;gap:8px;align-self:start}
.photo-box img{width:110px;height:110px;border-radius:50%;object-fit:cover;margin:0 auto 4px;border:3px solid #fff;box-shadow:0 8px 24px rgba(0,0,0,.08)}
.basic-side-card{border:1px solid rgba(234,88,12,.2);border-radius:18px;padding:14px;background:rgba(234,88,12,.03)}
.basic-side-title{font-size:12px;font-weight:800;color:var(--text-3);letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px}
.basic-side-line{display:flex;justify-content:space-between;gap:12px;padding:7px 0;border-bottom:1px solid rgba(148,163,184,.18);font-size:13px}
.basic-side-line:last-child{border-bottom:none}
.basic-side-line strong{font-weight:800;color:#ea580c}
.basic-fields{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}

/* Address */
.address-card{border:1px solid var(--border);border-radius:18px;padding:18px;background:var(--bg-3)}
.address-card-title{font-size:15px;font-weight:900;margin:0 0 4px}
.address-card-note{font-size:12px;color:var(--text-2);margin:0 0 14px}
.address-full-width{grid-column:1/-1}

/* Review */
.plan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.review-2col{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.review-section{background:var(--bg-3);border:1px solid var(--border);border-radius:14px;padding:16px}
.review-section-title{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);margin-bottom:12px}
.review-row{display:flex;justify-content:space-between;gap:12px;padding:6px 0;border-bottom:1px solid var(--border-2);font-size:13px}
.review-row:last-child{border-bottom:none}
.review-row strong{font-weight:700;color:#ea580c;text-align:right;max-width:60%}

.wizard-actions{display:flex;justify-content:space-between;gap:12px;padding:0 24px 24px}

@media(max-width:1080px){.basic-layout{grid-template-columns:1fr}}
@media(max-width:880px){.adm-grid{grid-template-columns:repeat(2,1fr)}.adm-grid-3{grid-template-columns:repeat(2,1fr)}.basic-fields{grid-template-columns:repeat(2,1fr)}.adm-steps{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.adm-grid,.adm-grid-3,.basic-fields,.review-2col{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="adm-shell">
  <div class="adm-header">
    <h2>Student Admission Wizard</h2>
    <p>Complete each step and review before booking the seat.</p>
  </div>

  <form method="POST" action="{{ route('franchise.enrollment.store-new') }}" enctype="multipart/form-data" id="admission-form" autocomplete="off">
    @csrf
    <input type="text" name="fake_u" value="" autocomplete="username" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
    <input type="password" name="fake_p" value="" autocomplete="new-password" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">

    <div class="adm-wrap">
      <div class="adm-card">

        {{-- Step indicators --}}
        <div class="adm-steps">
          @foreach(['Course','Basic Info','Address','Review'] as $i => $label)
            <div class="adm-step {{ $i === 0 ? 'active' : '' }}" data-indicator>{{ $label }}</div>
          @endforeach
        </div>

        <div class="adm-body">

          {{-- ══ STEP 1: COURSE ══ --}}
          <div class="wizard-step active" data-step>
            <div class="adm-section-title">Course Setup</div>
            <div class="adm-section-note">Select course type and course. Fee breakdown will appear automatically.</div>
            <div class="adm-grid">
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
                <select name="course_id" id="course_id" class="gt-select" style="display:none;" required>
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
                    <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Fee breakdown panel (shown after course selected) --}}
            <div class="inline-fee-panel" id="fee-panel" style="display:none;">
              <div class="inline-fee-title">Fee Breakdown — Your Pricing</div>
              <div class="fee-lines" id="fee-lines">
                <div class="fee-line" style="color:var(--text-3);font-style:italic;">Select a course to view fees.</div>
              </div>
              <div class="fee-total" id="fee-total">Total: ₹0.00</div>
              <div class="pay-note">
                Course fee reflects your student pricing. Additional enabled fee types (exam, registration, etc.) are included above.
              </div>
            </div>
          </div>

          {{-- ══ STEP 2: BASIC INFO ══ --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Basic &amp; Guardian Details</div>
            <div class="adm-section-note">Fill student's personal details and guardian information.</div>
            <div class="basic-layout">
              <div class="basic-side-stack">
                <div class="photo-box">
                  <img src="{{ asset('images/user.svg') }}" id="photo_preview" alt="Photo">
                  <div style="font-size:13px;font-weight:700;">Student Photo</div>
                  <div style="font-size:11px;color:var(--text-2);">Upload a clear passport-size photo.</div>
                  <input type="file" name="photo" id="photo_file" class="gt-input" accept="image/*" style="margin-top:4px;">
                </div>
                <div class="basic-side-card">
                  <div class="basic-side-title">Admission Summary</div>
                  <div class="basic-side-line"><span>Course</span><strong id="side-course">—</strong></div>
                  <div class="basic-side-line"><span>Duration</span><strong id="side-duration">—</strong></div>
                  <div class="basic-side-line"><span>Total Fee</span><strong id="side-fee">₹0.00</strong></div>
                </div>
              </div>

              <div class="basic-fields">
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
                <div class="gt-form-group">
                  <label class="gt-label">WhatsApp No.</label>
                  <input type="tel" name="whatsapp_no" class="gt-input" value="{{ old('whatsapp_no') }}"
                    maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">Date of Birth</label>
                  <input type="date" name="dob" class="gt-input" value="{{ old('dob') }}">
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">Gender</label>
                  <select name="gender" class="gt-select">
                    <option value="">Select</option>
                    <option value="Male" {{ old('gender')==='Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender')==='Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender')==='Other' ? 'selected' : '' }}>Other</option>
                  </select>
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">Category</label>
                  <select name="category" class="gt-select">
                    <option value="">Select</option>
                    <option value="General" {{ old('category')==='General' ? 'selected' : '' }}>General</option>
                    <option value="OBC" {{ old('category')==='OBC' ? 'selected' : '' }}>OBC</option>
                    <option value="SC" {{ old('category')==='SC' ? 'selected' : '' }}>SC</option>
                    <option value="ST" {{ old('category')==='ST' ? 'selected' : '' }}>ST</option>
                    <option value="EWS" {{ old('category')==='EWS' ? 'selected' : '' }}>EWS</option>
                  </select>
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">Aadhar No.</label>
                  <input type="text" name="aadhar_no" class="gt-input" value="{{ old('aadhar_no') }}"
                    maxlength="12" inputmode="numeric" placeholder="12-digit Aadhar"
                    oninput="this.value=this.value.replace(/\D/g,'').slice(0,12)">
                </div>
                <div style="grid-column:1/-1;height:1px;background:var(--border);margin:4px 0;"></div>
                <div class="gt-form-group">
                  <label class="gt-label">Father's Name</label>
                  <input type="text" name="father_name" class="gt-input" value="{{ old('father_name') }}">
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">Mother's Name</label>
                  <input type="text" name="mother_name" class="gt-input" value="{{ old('mother_name') }}">
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">Guardian Mobile</label>
                  <input type="tel" name="guardian_mobile" class="gt-input" value="{{ old('guardian_mobile') }}"
                    maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">Qualification</label>
                  <input type="text" name="qualification" class="gt-input" value="{{ old('qualification') }}" placeholder="e.g. 10th, 12th, Graduate">
                </div>
              </div>
            </div>
          </div>

          {{-- ══ STEP 3: ADDRESS ══ --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Address Details</div>
            <div class="adm-section-note">Enter the student's current address information.</div>
            <div class="address-card">
              <h4 class="address-card-title">Present Address</h4>
              <p class="address-card-note">Enter the student's current address and location details.</p>
              <div class="adm-grid">
                <div class="gt-form-group address-full-width">
                  <label class="gt-label">Full Address</label>
                  <textarea name="address" class="gt-textarea" rows="2" placeholder="Street, locality, landmark…">{{ old('address') }}</textarea>
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">State</label>
                  <select name="state" id="state-sel" class="gt-select">
                    <option value="">Select State</option>
                    @foreach($states as $s)
                      <option value="{{ $s }}" {{ old('state')===$s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">District</label>
                  <select name="district" id="district-sel" class="gt-select">
                    <option value="">Select District</option>
                    @if(old('district'))
                      <option value="{{ old('district') }}" selected>{{ old('district') }}</option>
                    @endif
                  </select>
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">City</label>
                  <input type="text" name="city" class="gt-input" value="{{ old('city') }}" placeholder="City / Town">
                </div>
                <div class="gt-form-group">
                  <label class="gt-label">PIN Code</label>
                  <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code') }}" placeholder="6-digit PIN" maxlength="10">
                </div>
              </div>
            </div>
          </div>

          {{-- ══ STEP 4: REVIEW ══ --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Review &amp; Submit</div>
            <div class="adm-section-note">Verify all details before booking the seat. You can go back to correct anything.</div>
            <div class="review-2col">
              <div class="review-section">
                <div class="review-section-title">Course</div>
                <div class="review-row"><span>Course</span><strong id="rv-course">—</strong></div>
                <div class="review-row"><span>Duration</span><strong id="rv-duration">—</strong></div>
                <div class="review-row"><span>Total Fee</span><strong id="rv-fee">—</strong></div>
              </div>
              <div class="review-section">
                <div class="review-section-title">Student</div>
                <div class="review-row"><span>Name</span><strong id="rv-name">—</strong></div>
                <div class="review-row"><span>Mobile</span><strong id="rv-mobile">—</strong></div>
                <div class="review-row"><span>Email</span><strong id="rv-email">—</strong></div>
                <div class="review-row"><span>DOB</span><strong id="rv-dob">—</strong></div>
                <div class="review-row"><span>Gender</span><strong id="rv-gender">—</strong></div>
                <div class="review-row"><span>Father</span><strong id="rv-father">—</strong></div>
              </div>
              <div class="review-section" style="grid-column:1/-1">
                <div class="review-section-title">Address</div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">
                  <div><div style="font-size:10px;color:var(--text-3);font-weight:700;text-transform:uppercase;margin-bottom:3px;">State</div><strong id="rv-state" style="font-size:13px;color:var(--text-1);">—</strong></div>
                  <div><div style="font-size:10px;color:var(--text-3);font-weight:700;text-transform:uppercase;margin-bottom:3px;">District</div><strong id="rv-district" style="font-size:13px;color:var(--text-1);">—</strong></div>
                  <div><div style="font-size:10px;color:var(--text-3);font-weight:700;text-transform:uppercase;margin-bottom:3px;">City</div><strong id="rv-city" style="font-size:13px;color:var(--text-1);">—</strong></div>
                  <div><div style="font-size:10px;color:var(--text-3);font-weight:700;text-transform:uppercase;margin-bottom:3px;">PIN</div><strong id="rv-pin" style="font-size:13px;color:var(--text-1);">—</strong></div>
                </div>
                <div style="margin-top:10px;font-size:13px;color:var(--text-2);" id="rv-address">—</div>
              </div>
            </div>
          </div>

        </div>{{-- /.adm-body --}}

        <div class="wizard-actions">
          <button type="button" id="btn-prev" class="btn btn-outline" style="display:none">← Previous</button>
          <div style="flex:1"></div>
          <button type="button" id="btn-next" class="btn btn-primary" style="background:#ea580c;border-color:#ea580c;">Next →</button>
          <button type="submit" id="btn-submit" class="btn btn-primary" style="display:none;background:#ea580c;border-color:#ea580c;">Book Seat →</button>
        </div>

      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const catalog          = @json($courseCatalog);
  const districtsByState = @json($districtsByState);

  // ── Step wizard ──────────────────────────────────────────────────
  const steps      = Array.from(document.querySelectorAll('[data-step]'));
  const indicators = Array.from(document.querySelectorAll('[data-indicator]'));
  let   cur        = 0;

  function showStep(n) {
    steps.forEach((s,i) => s.classList.toggle('active', i===n));
    indicators.forEach((el,i) => {
      el.classList.remove('active','done');
      if (i===n) el.classList.add('active');
      else if (i<n) el.classList.add('done');
    });
    document.getElementById('btn-prev').style.display   = n===0 ? 'none' : '';
    document.getElementById('btn-next').style.display   = n===steps.length-1 ? 'none' : '';
    document.getElementById('btn-submit').style.display = n===steps.length-1 ? '' : 'none';
    if (n===steps.length-1) populateReview();
    cur = n;
  }

  document.getElementById('btn-next').addEventListener('click', () => {
    if (!validateStep()) return;
    showStep(Math.min(cur+1, steps.length-1));
  });
  document.getElementById('btn-prev').addEventListener('click', () => showStep(Math.max(cur-1,0)));

  function validateStep() {
    const step = steps[cur];
    let ok = true;
    step.querySelectorAll('[required]').forEach(el => {
      if (!el.value.trim()) { el.style.borderColor='var(--danger)'; ok=false; }
      else el.style.borderColor='';
    });
    if (!ok) { step.querySelector('[required]:invalid, [required]')?.focus(); return false; }
    if (cur===0 && !document.getElementById('course_id').value) {
      document.getElementById('course_search_display').style.borderColor='var(--danger)';
      return false;
    }
    if (cur===1) {
      const mob = document.getElementById('mobile');
      if (mob.value.length!==10) {
        mob.style.borderColor='var(--danger)';
        document.getElementById('mobile-error').textContent='Mobile must be 10 digits.';
        document.getElementById('mobile-error').style.display='block';
        mob.focus(); return false;
      }
    }
    return true;
  }

  // ── Review populate ──────────────────────────────────────────────
  function populateReview() {
    const c = catalog.find(c=>String(c.id)===String(document.getElementById('course_id').value));
    setText('rv-course',   c ? c.name : '—');
    setText('rv-duration', c ? c.duration+' months' : '—');
    setText('rv-fee',      c ? '₹'+Number(c.total_fee).toLocaleString('en-IN') : '—');
    setText('rv-name',     qv('[name=name]'));
    setText('rv-mobile',   qv('[name=mobile]'));
    setText('rv-email',    qv('[name=email]'));
    setText('rv-dob',      qv('[name=dob]'));
    setText('rv-gender',   qv('[name=gender]'));
    setText('rv-father',   qv('[name=father_name]'));
    setText('rv-state',    qv('[name=state]'));
    setText('rv-district', qv('[name=district]'));
    setText('rv-city',     qv('[name=city]'));
    setText('rv-pin',      qv('[name=pin_code]'));
    setText('rv-address',  qv('[name=address]'));
  }
  function setText(id, v) { const el=document.getElementById(id); if(el) el.textContent=v||'—'; }
  function qv(sel) { return document.querySelector(sel)?.value||''; }

  // ── Course cascade ───────────────────────────────────────────────
  const ctSel   = document.getElementById('course_type_id');
  const durSel  = document.getElementById('course_duration_filter');
  const crsId   = document.getElementById('course_id');
  const crsDisp = document.getElementById('course_search_display');
  const crsDrop = document.getElementById('course_search_dropdown');
  let   pool    = [];

  function renderDurations() {
    const tid=ctSel.value;
    const durs=[...new Set(catalog.filter(c=>String(c.course_type_id)===String(tid)).map(c=>Number(c.duration)).filter(Boolean))].sort((a,b)=>a-b);
    durSel.innerHTML='<option value="">Select Duration</option>'+durs.map(d=>`<option value="${d}">${d} month${d===1?'':'s'}</option>`).join('');
  }

  function renderCourses() {
    const tid=ctSel.value,dur=durSel.value;
    pool=catalog.filter(c=>String(c.course_type_id)===String(tid)&&String(c.duration)===String(dur));
    crsId.innerHTML='<option value="">Select Course</option>'+pool.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
    crsDisp.value=''; crsDrop.style.display='none';
    document.getElementById('fee-panel').style.display='none';
    updateSidePanel(null);
  }

  function renderDropdown(q='') {
    const f=q?pool.filter(c=>c.name.toLowerCase().includes(q.toLowerCase())):pool;
    crsDrop.innerHTML=f.length
      ? f.map(c=>`<div class="crs-opt" data-id="${c.id}" data-name="${c.name}"
          style="padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);">
          ${c.name} <span style="color:var(--text-3);font-size:11px;">(${c.duration}m)</span>
        </div>`).join('')
      : '<div style="padding:10px 14px;font-size:12px;color:var(--text-2);">No courses found</div>';
    crsDrop.querySelectorAll('.crs-opt').forEach(d=>{
      d.addEventListener('mousedown',e=>{
        e.preventDefault();
        crsId.value=d.dataset.id; crsDisp.value=d.dataset.name;
        crsDisp.style.borderColor=''; crsDrop.style.display='none';
        renderFee(d.dataset.id);
      });
    });
    crsDrop.style.display='block';
  }

  crsDisp.addEventListener('focus', ()=>pool.length&&renderDropdown(crsDisp.value));
  crsDisp.addEventListener('input', ()=>{ crsId.value=''; renderDropdown(crsDisp.value); });
  crsDisp.addEventListener('blur',  ()=>setTimeout(()=>crsDrop.style.display='none',150));
  document.addEventListener('click',e=>{ if(!crsDrop.contains(e.target)&&e.target!==crsDisp) crsDrop.style.display='none'; });
  ctSel.addEventListener('change',  ()=>{ renderDurations(); renderCourses(); });
  durSel.addEventListener('change', ()=>renderCourses());

  // ── Fee panel ────────────────────────────────────────────────────
  function renderFee(id) {
    const c=catalog.find(c=>String(c.id)===String(id));
    const panel=document.getElementById('fee-panel');
    if (!c||!c.fee_items?.length) { panel.style.display='none'; return; }
    document.getElementById('fee-lines').innerHTML=c.fee_items.map(f=>
      `<div class="fee-line">
        <span style="color:var(--text-2);">${f.fee_type_name}</span>
        <strong>₹${Number(f.amount).toLocaleString('en-IN',{minimumFractionDigits:2})}</strong>
      </div>`).join('');
    document.getElementById('fee-total').textContent='Total: ₹'+Number(c.total_fee).toLocaleString('en-IN',{minimumFractionDigits:2});
    panel.style.display='block';
    updateSidePanel(c);
  }

  function updateSidePanel(c) {
    setText('side-course',   c ? c.name : '—');
    setText('side-duration', c ? c.duration+' months' : '—');
    setText('side-fee',      c ? '₹'+Number(c.total_fee).toLocaleString('en-IN') : '₹0.00');
  }

  // ── District cascade ─────────────────────────────────────────────
  document.getElementById('state-sel')?.addEventListener('change', function () {
    const dists=districtsByState[this.value]||[];
    document.getElementById('district-sel').innerHTML='<option value="">Select District</option>'+dists.map(d=>`<option value="${d}">${d}</option>`).join('');
  });

  // Restore old district on validation error
  const oldState='{{ old("state") }}', oldDist='{{ old("district") }}';
  if (oldState && oldDist && districtsByState[oldState]) {
    document.getElementById('district-sel').innerHTML='<option value="">Select District</option>'+
      districtsByState[oldState].map(d=>`<option value="${d}"${d===oldDist?' selected':''}>${d}</option>`).join('');
  }

  // ── Mobile validation ─────────────────────────────────────────────
  document.getElementById('mobile')?.addEventListener('blur', function() {
    const e=document.getElementById('mobile-error');
    if (this.value&&this.value.length!==10){
      this.style.borderColor='var(--danger)';
      e.textContent='Mobile must be exactly 10 digits.';e.style.display='block';
    } else {this.style.borderColor='';e.style.display='none';}
  });

  // ── Photo preview ─────────────────────────────────────────────────
  document.getElementById('photo_file')?.addEventListener('change', function() {
    if (this.files[0]) {
      const r=new FileReader();
      r.onload=e=>document.getElementById('photo_preview').src=e.target.result;
      r.readAsDataURL(this.files[0]);
    }
  });

  // ── Restore on validation error ──────────────────────────────────
  @if($errors->any())
    const oldCourseId='{{ old("course_id") }}';
    if (oldCourseId) {
      const oc=catalog.find(c=>String(c.id)===String(oldCourseId));
      if (oc) {
        ctSel.value=String(oc.course_type_id);
        renderDurations();
        durSel.value=String(oc.duration);
        renderCourses();
        crsId.value=oldCourseId;
        crsDisp.value=oc.name;
        renderFee(oldCourseId);
      }
    }
    @if($errors->hasAny(['name','mobile','email','dob','gender','father_name','mother_name','aadhar_no']))
      showStep(1);
    @elseif($errors->hasAny(['address','state','district','city','pin_code']))
      showStep(2);
    @elseif($errors->has('course_id'))
      showStep(0);
    @endif
  @endif
})();
</script>
@endpush
