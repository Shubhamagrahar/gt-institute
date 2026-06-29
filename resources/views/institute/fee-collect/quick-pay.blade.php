@extends('layouts.institute')
@section('title', 'Quick Pay')
@section('page-title', 'Quick Pay')

@push('styles')
<style>
.qp-wrap {
  max-width: 660px;
  margin: 0 auto;
  padding: 4px 0 40px;
}

.qp-hero { margin-bottom: 20px; }
.qp-hero h1 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 3px; }
.qp-hero p  { font-size: 13px; color: var(--text-2); line-height: 1.5; }

.qp-search-card {
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  padding: 28px 28px 24px;
}

.qp-search-label {
  font-size: 11px; font-weight: 600; letter-spacing: .8px;
  text-transform: uppercase; color: var(--text-3);
  margin-bottom: 10px; display: block;
}

.qp-search-box { position: relative; }
.qp-search-box svg.qp-search-ico {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  color: var(--text-3); pointer-events: none; transition: color .2s;
}
.qp-search-box.focused svg.qp-search-ico { color: var(--accent); }

#qp-input {
  width: 100%;
  padding: 13px 46px 13px 44px;
  font-size: 14.5px; font-family: var(--font);
  border: 2px solid var(--border);
  border-radius: 10px;
  background: var(--bg-3);
  color: var(--text);
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}
#qp-input:focus {
  border-color: var(--accent);
  background: #fff;
  box-shadow: 0 0 0 4px rgba(108,93,211,.1);
}
#qp-input::placeholder { color: var(--text-3); }

.qp-clear-btn {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: var(--text-3); padding: 4px; border-radius: 50%;
  display: none; align-items: center; justify-content: center;
  transition: color .15s, background .15s;
}
.qp-clear-btn:hover { color: var(--text); background: var(--bg-4); }
.qp-clear-btn.visible { display: flex; }

.qp-hint { font-size: 12px; color: var(--text-3); margin-top: 8px; display: flex; align-items: center; gap: 6px; }
.qp-hint-tags { display: flex; gap: 5px; flex-wrap: wrap; }
.qp-hint-tag {
  background: var(--bg-4); border: 1px solid var(--border);
  border-radius: 5px; padding: 2px 8px; font-size: 11px;
  color: var(--text-2); font-weight: 500;
}

.qp-results {
  margin-top: 8px;
  border: 1px solid var(--border);
  border-radius: 10px;
  background: var(--bg-2);
  box-shadow: var(--shadow-md);
  overflow: hidden;
  display: none;
}
.qp-results.visible { display: block; }

.qp-result-item {
  display: flex; align-items: center; gap: 14px;
  padding: 13px 16px;
  cursor: pointer;
  border-bottom: 1px solid var(--border);
  transition: background .15s;
}
.qp-result-item:last-child { border-bottom: none; }
.qp-result-item:hover, .qp-result-item.highlighted { background: var(--accent-bg); }

.qp-result-avatar {
  width: 38px; height: 38px; border-radius: 10px;
  background: linear-gradient(135deg, rgba(108,93,211,.15), rgba(108,93,211,.08));
  border: 1px solid rgba(108,93,211,.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 700; color: var(--accent);
  flex-shrink: 0;
}
.qp-result-body { flex: 1; min-width: 0; }
.qp-result-name { font-size: 14px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.qp-result-meta { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 3px; }
.qp-result-meta span { font-size: 11.5px; color: var(--text-2); display: flex; align-items: center; gap: 4px; }
.qp-result-meta span svg { color: var(--text-3); flex-shrink: 0; }
.qp-result-enr {
  font-size: 11.5px; font-family: var(--font-mono);
  background: var(--accent-bg); color: var(--accent);
  border-radius: 5px; padding: 2px 7px;
  margin-left: auto; white-space: nowrap; flex-shrink: 0; font-weight: 500;
}
.qp-result-arrow { color: var(--text-3); flex-shrink: 0; margin-left: 6px; transition: transform .15s, color .15s; }
.qp-result-item:hover .qp-result-arrow { transform: translateX(3px); color: var(--accent); }

.qp-state { padding: 28px 16px; text-align: center; color: var(--text-3); font-size: 13px; }
.qp-state svg { margin-bottom: 8px; color: var(--text-3); }
.qp-state p { margin-top: 4px; }

.qp-spinner {
  width: 20px; height: 20px;
  border: 2px solid var(--border);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: qp-spin .6s linear infinite;
  margin: 0 auto 8px;
}
@keyframes qp-spin { to { transform: rotate(360deg); } }

/* Selected student card */
.qp-selected {
  background: var(--bg-2);
  border: 1.5px solid var(--accent);
  border-radius: var(--radius-lg);
  box-shadow: 0 0 0 4px rgba(108,93,211,.08), var(--shadow);
  padding: 22px 24px;
  margin-top: 20px;
  display: none;
  animation: qp-slide-in .2s ease;
}
.qp-selected.visible { display: block; }
@keyframes qp-slide-in { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

.qp-selected-header { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
.qp-selected-ava {
  width: 48px; height: 48px; border-radius: 12px;
  background: linear-gradient(135deg, #6c5dd3, #a89cf5);
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; font-weight: 700; color: #fff; flex-shrink: 0;
}
.qp-selected-info { flex: 1; }
.qp-selected-name { font-size: 16px; font-weight: 700; color: var(--text); }
.qp-selected-uid  { font-size: 12px; color: var(--text-3); font-family: var(--font-mono); margin-top: 2px; }
.qp-badge-enr {
  font-size: 12px; font-family: var(--font-mono);
  background: var(--accent-bg); color: var(--accent);
  border: 1px solid rgba(108,93,211,.2);
  padding: 4px 10px; border-radius: 6px; font-weight: 600;
}

.qp-selected-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
.qp-field { background: var(--bg-3); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; }
.qp-field-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .7px; color: var(--text-3); margin-bottom: 4px; }
.qp-field-val { font-size: 13.5px; font-weight: 500; color: var(--text); word-break: break-all; }
.qp-field-val.mono { font-family: var(--font-mono); }

.qp-actions { display: flex; gap: 10px; align-items: center; }
.qp-proceed-btn {
  flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  background: linear-gradient(135deg, #6c5dd3, #7c6fe0);
  color: #fff; font-size: 14px; font-weight: 600;
  padding: 13px 24px; border-radius: 10px; border: none;
  cursor: pointer; text-decoration: none;
  box-shadow: 0 4px 14px rgba(108,93,211,.35);
  transition: opacity .15s, transform .1s, box-shadow .15s;
}
.qp-proceed-btn:hover { opacity:1; color:#fff; box-shadow: 0 6px 20px rgba(108,93,211,.45); transform: translateY(-1px); }
.qp-deselect-btn {
  padding: 12px 16px; border-radius: 10px;
  border: 1.5px solid var(--border);
  background: none; color: var(--text-2);
  font-size: 13px; font-weight: 500;
  cursor: pointer; transition: border-color .15s, color .15s;
  display: flex; align-items: center; gap: 6px;
}
.qp-deselect-btn:hover { border-color: var(--danger); color: var(--danger); }

@media (max-width: 600px) {
  .qp-search-card { padding: 18px 16px; }
  .qp-selected-grid { grid-template-columns: 1fr; }
  .qp-actions { flex-direction: column; }
  .qp-proceed-btn, .qp-deselect-btn { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="qp-wrap">

  <div class="qp-hero">
    <h1>Quick Pay</h1>
    <p>Student ko naam, enrollment no., mobile ya email se search karke seedha fee collect karo</p>
  </div>

  <div class="qp-search-card">
    <span class="qp-search-label">Find Student</span>

    <div class="qp-search-box" id="qp-search-box">
      <svg class="qp-search-ico" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="qp-input" placeholder="Type name, enrollment no., mobile or email..." autocomplete="off" spellcheck="false">
      <button class="qp-clear-btn" id="qp-clear" title="Clear" type="button">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <div class="qp-hint">
      <span>Search by:</span>
      <div class="qp-hint-tags">
        <span class="qp-hint-tag">Name</span>
        <span class="qp-hint-tag">Enrollment No.</span>
        <span class="qp-hint-tag">Mobile</span>
        <span class="qp-hint-tag">Email</span>
        <span class="qp-hint-tag">Student ID</span>
      </div>
    </div>

    <div class="qp-results" id="qp-results"></div>
  </div>

  <div class="qp-selected" id="qp-selected">
    <div class="qp-selected-header">
      <div class="qp-selected-ava" id="qp-sel-ava"></div>
      <div class="qp-selected-info">
        <div class="qp-selected-name" id="qp-sel-name"></div>
        <div class="qp-selected-uid"  id="qp-sel-uid"></div>
      </div>
      <div class="qp-badge-enr" id="qp-sel-enr" style="display:none;"></div>
    </div>

    <div class="qp-selected-grid">
      <div class="qp-field">
        <div class="qp-field-label">Mobile</div>
        <div class="qp-field-val mono" id="qp-sel-mobile">—</div>
      </div>
      <div class="qp-field">
        <div class="qp-field-label">Email</div>
        <div class="qp-field-val" id="qp-sel-email" style="font-size:12.5px;">—</div>
      </div>
      <div class="qp-field">
        <div class="qp-field-label">Student ID</div>
        <div class="qp-field-val mono" id="qp-sel-userid"></div>
      </div>
      <div class="qp-field">
        <div class="qp-field-label">Enrollment No.</div>
        <div class="qp-field-val mono" id="qp-sel-enrno">—</div>
      </div>
    </div>

    <div class="qp-actions">
      <a href="#" id="qp-proceed" class="qp-proceed-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="1" x2="12" y2="23"/>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
        </svg>
        Proceed to Payment
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
        </svg>
      </a>
      <button class="qp-deselect-btn" id="qp-deselect" type="button">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
        Clear
      </button>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
  const input      = document.getElementById('qp-input');
  const clearBtn   = document.getElementById('qp-clear');
  const searchBox  = document.getElementById('qp-search-box');
  const resultsEl  = document.getElementById('qp-results');
  const selectedEl = document.getElementById('qp-selected');
  const deselectBtn= document.getElementById('qp-deselect');
  const proceedBtn = document.getElementById('qp-proceed');
  const SEARCH_URL = '{{ route("institute.quick-pay.search") }}';
  let debounceTimer = null, activeIndex = -1, currentResults = [];

  function initials(name) { return name.split(' ').map(w=>w[0]||'').join('').toUpperCase().slice(0,2)||'?'; }
  function escHtml(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
  function escRe(s) { return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
  function highlight(text, q) {
    if (!q) return escHtml(text);
    return escHtml(text).replace(new RegExp('('+escRe(q)+')','gi'),'<mark style="background:rgba(108,93,211,.18);color:var(--accent);border-radius:2px;padding:0 1px;">$1</mark>');
  }

  input.addEventListener('input', function () {
    const val = this.value;
    clearBtn.classList.toggle('visible', val.length > 0);
    clearSelection();
    clearTimeout(debounceTimer);
    if (val.trim().length < 2) { hideResults(); return; }
    showLoading();
    debounceTimer = setTimeout(() => doSearch(val.trim()), 300);
  });

  input.addEventListener('focus', function () {
    searchBox.classList.add('focused');
    if (currentResults.length && this.value.trim().length >= 2) showResultsEl();
  });
  input.addEventListener('blur', function () {
    searchBox.classList.remove('focused');
    setTimeout(hideResults, 180);
  });
  input.addEventListener('keydown', function (e) {
    const items = resultsEl.querySelectorAll('.qp-result-item');
    if (!items.length) return;
    if (e.key==='ArrowDown')  { e.preventDefault(); activeIndex=Math.min(activeIndex+1,items.length-1); updateHL(items); }
    else if (e.key==='ArrowUp')   { e.preventDefault(); activeIndex=Math.max(activeIndex-1,0); updateHL(items); }
    else if (e.key==='Enter' && activeIndex>=0) { e.preventDefault(); selectStudent(currentResults[activeIndex]); }
    else if (e.key==='Escape') hideResults();
  });
  function updateHL(items) {
    items.forEach((it,i)=>it.classList.toggle('highlighted',i===activeIndex));
    items[activeIndex]?.scrollIntoView({block:'nearest'});
  }

  clearBtn.addEventListener('click', function () {
    input.value=''; clearBtn.classList.remove('visible'); hideResults(); clearSelection(); input.focus();
  });
  deselectBtn.addEventListener('click', function () {
    clearSelection(); input.value=''; clearBtn.classList.remove('visible'); input.focus();
  });

  function doSearch(q) {
    activeIndex = -1;
    fetch(SEARCH_URL+'?q='+encodeURIComponent(q), { headers:{'X-Requested-With':'XMLHttpRequest'} })
      .then(r=>r.json()).then(data=>{ currentResults=data; renderResults(data,q); })
      .catch(()=>renderError());
  }

  function showLoading() {
    resultsEl.innerHTML='<div class="qp-state"><div class="qp-spinner"></div><p>Searching...</p></div>';
    showResultsEl();
  }
  function renderError() {
    resultsEl.innerHTML='<div class="qp-state"><p>Could not fetch results. Please try again.</p></div>';
    showResultsEl();
  }
  function renderResults(data, q) {
    if (!data.length) {
      resultsEl.innerHTML='<div class="qp-state"><p>No students found for <strong>"'+escHtml(q)+'"</strong></p></div>';
      showResultsEl(); return;
    }
    let html='';
    data.forEach((s,i) => {
      const av=initials(s.name);
      const enrBadge=s.enrollment_no?`<span class="qp-result-enr">${escHtml(s.enrollment_no)}</span>`:'';
      const mob=s.mobile?`<span>${highlight(s.mobile,q)}</span>`:'';
      const eml=s.email?`<span>${highlight(s.email,q)}</span>`:'';
      html+=`<div class="qp-result-item" data-index="${i}">
        <div class="qp-result-avatar">${av}</div>
        <div class="qp-result-body">
          <div class="qp-result-name">${highlight(s.name,q)}</div>
          <div class="qp-result-meta"><span>${highlight(s.user_id,q)}</span>${mob}${eml}</div>
        </div>
        ${enrBadge}
        <svg class="qp-result-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
        </svg>
      </div>`;
    });
    resultsEl.innerHTML=html; showResultsEl();
    resultsEl.querySelectorAll('.qp-result-item').forEach((el,i)=>{
      el.addEventListener('mousedown',e=>{e.preventDefault();selectStudent(currentResults[i]);});
    });
  }
  function showResultsEl() { resultsEl.classList.add('visible'); }
  function hideResults()   { resultsEl.classList.remove('visible'); }

  function selectStudent(s) {
    hideResults();
    document.getElementById('qp-sel-ava').textContent    = initials(s.name);
    document.getElementById('qp-sel-name').textContent   = s.name;
    document.getElementById('qp-sel-uid').textContent    = s.user_id;
    document.getElementById('qp-sel-mobile').textContent = s.mobile||'—';
    document.getElementById('qp-sel-email').textContent  = s.email||'—';
    document.getElementById('qp-sel-userid').textContent = s.user_id;
    document.getElementById('qp-sel-enrno').textContent  = s.enrollment_no||'—';
    const enrEl = document.getElementById('qp-sel-enr');
    if (s.enrollment_no) { enrEl.textContent=s.enrollment_no; enrEl.style.display=''; }
    else { enrEl.style.display='none'; }
    proceedBtn.href = s.url;
    input.value = s.name;
    clearBtn.classList.add('visible');
    selectedEl.classList.add('visible');
    selectedEl.scrollIntoView({behavior:'smooth',block:'nearest'});
  }
  function clearSelection() {
    selectedEl.classList.remove('visible');
    proceedBtn.href='#';
    currentResults=[]; activeIndex=-1;
  }
})();
</script>
@endpush
