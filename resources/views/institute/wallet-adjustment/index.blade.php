@extends('layouts.institute')
@section('title', 'Wallet Adjustment')
@section('page-title', 'Wallet Adjustment')

@push('styles')
<style>
/* ── Search bar ──────────────────────────────────────── */
.wa-search-wrap {
  position: relative;
  max-width: 520px;
}

.wa-search-input {
  width: 100%;
  height: 44px;
  padding: 0 44px 0 44px;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  background: var(--bg);
  color: var(--text);
  font-size: 14px;
  outline: none;
  transition: border-color .15s, box-shadow .15s;
}

.wa-search-input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(108,93,211,.12);
}

.wa-search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: var(--text-3);
  pointer-events: none;
}

.wa-search-spinner {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  border: 2px solid rgba(108,93,211,.2);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: wa-spin .5s linear infinite;
  display: none;
}

@keyframes wa-spin { to { transform: translateY(-50%) rotate(360deg); } }

/* ── Dropdown results ────────────────────────────────── */
.wa-results {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  right: 0;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  box-shadow: 0 8px 32px rgba(0,0,0,.12);
  z-index: 200;
  max-height: 260px;
  overflow-y: auto;
  display: none;
}

.wa-result-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  cursor: pointer;
  border-bottom: 1px solid var(--border);
  transition: background .1s;
}

.wa-result-item:last-child { border-bottom: none; }
.wa-result-item:hover { background: var(--bg-2); }

.wa-result-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(108,93,211,.2), rgba(138,115,245,.2));
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
  color: var(--accent);
  flex-shrink: 0;
}

.wa-result-name  { font-size: 13px; font-weight: 700; color: var(--text); }
.wa-result-sub   { font-size: 11px; color: var(--text-3); margin-top: 1px; }

/* ── Selected student card ───────────────────────────── */
.wa-student-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 20px;
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: 12px;
  margin-bottom: 24px;
}

.wa-student-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(108,93,211,.25), rgba(138,115,245,.25));
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 18px;
  color: var(--accent);
  flex-shrink: 0;
}

.wa-student-name { font-size: 15px; font-weight: 700; color: var(--text); }
.wa-student-sub  { font-size: 12px; color: var(--text-3); margin-top: 2px; }

.wa-balance-pill {
  margin-left: auto;
  padding: 8px 18px;
  border-radius: 30px;
  font-size: 16px;
  font-weight: 800;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  flex-shrink: 0;
}

.wa-balance-pill.negative {
  background: #fef2f2;
  border: 1.5px solid #fecaca;
  color: #dc2626;
}

.wa-balance-pill.zero {
  background: var(--bg-3);
  border: 1.5px solid var(--border);
  color: var(--text-3);
}

.wa-balance-pill.positive {
  background: #f0fdf4;
  border: 1.5px solid #bbf7d0;
  color: #16a34a;
}

.wa-balance-label {
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .5px;
  opacity: .65;
}

/* ── Forms grid ──────────────────────────────────────── */
.wa-forms-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

@media (max-width: 720px) {
  .wa-forms-grid { grid-template-columns: 1fr; }
}

.wa-form-card {
  border-radius: 14px;
  padding: 22px;
  border: 1.5px solid;
}

.wa-form-card.credit {
  background: #f0fdf4;
  border-color: #86efac;
}

.wa-form-card.debit {
  background: #fef2f2;
  border-color: #fca5a5;
}

.wa-form-title {
  font-size: 14px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 18px;
}

.wa-form-card.credit .wa-form-title { color: #15803d; }
.wa-form-card.debit  .wa-form-title { color: #b91c1c; }

.wa-form-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: var(--text-2);
  margin-bottom: 6px;
}

.wa-form-input {
  width: 100%;
  height: 40px;
  padding: 0 12px;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  background: var(--bg);
  color: var(--text);
  font-size: 14px;
  font-weight: 600;
  outline: none;
  transition: border-color .15s, box-shadow .15s;
  margin-bottom: 12px;
}

.wa-form-input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(108,93,211,.12);
}

.wa-form-textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  background: var(--bg);
  color: var(--text);
  font-size: 13px;
  outline: none;
  resize: none;
  height: 72px;
  transition: border-color .15s, box-shadow .15s;
  margin-bottom: 14px;
  font-family: inherit;
}

.wa-form-textarea:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(108,93,211,.12);
}

.wa-preview {
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.wa-form-card.credit .wa-preview { background: #dcfce7; color: #15803d; }
.wa-form-card.debit  .wa-preview { background: #fee2e2; color: #b91c1c; }

.wa-preview-label { font-size: 11px; opacity: .65; }

.wa-submit-btn {
  width: 100%;
  height: 42px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: opacity .15s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
}

.wa-submit-btn:hover { opacity: .88; }

.wa-form-card.credit .wa-submit-btn { background: #16a34a; color: #fff; }
.wa-form-card.debit  .wa-submit-btn { background: #dc2626; color: #fff; }

.wa-empty-state {
  text-align: center;
  padding: 48px 24px;
}

.wa-empty-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: var(--bg-2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  border: 1px solid var(--border);
}
</style>
@endpush

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Wallet Adjustment</div>
      <div class="text-xs text-muted" style="margin-top:3px;">Manually credit or debit a student's wallet with a note for audit trail.</div>
    </div>
  </div>

  @if(session('success'))
    <div style="margin:0 20px 4px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;color:#15803d;font-size:13px;font-weight:600;">
      {{ session('success') }}
    </div>
  @endif

  <div style="padding:20px;">

    {{-- Search --}}
    <div class="wa-search-wrap" style="margin-bottom:24px;" id="search-container">
      <svg class="wa-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
      </svg>
      <input type="text" id="wa-search" class="wa-search-input" placeholder="Search student by name, mobile, ID or enrollment no...">
      <div class="wa-search-spinner" id="wa-spinner"></div>
      <div class="wa-results" id="wa-results"></div>
    </div>

    {{-- Adjustment panel (shown after selecting student) --}}
    <div id="wa-panel" style="display:none;">

      {{-- Selected student --}}
      <div class="wa-student-card" id="wa-student-card">
        <div class="wa-student-avatar" id="wa-avatar">A</div>
        <div>
          <div class="wa-student-name" id="wa-name">—</div>
          <div class="wa-student-sub" id="wa-sub">—</div>
        </div>
        <div class="wa-balance-pill zero" id="wa-balance-pill">
          <div class="wa-balance-label">Current Balance</div>
          <div id="wa-balance-display">₹0.00</div>
        </div>
      </div>

      {{-- Credit / Debit forms side by side --}}
      <div class="wa-forms-grid">

        {{-- CREDIT --}}
        <div class="wa-form-card credit">
          <div class="wa-form-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Credit Wallet
          </div>
          <form id="credit-form" method="POST">
            @csrf
            <label class="wa-form-label">Amount (₹)</label>
            <input type="number" name="amount" id="credit-amount" class="wa-form-input" placeholder="0.00" min="0.01" step="0.01" autocomplete="off">
            <label class="wa-form-label">Reason / Note</label>
            <textarea name="note" class="wa-form-textarea" placeholder="e.g. Mutual agreement — student left city, remaining fee waived"></textarea>
            <div class="wa-preview" id="credit-preview">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <div>
                <div class="wa-preview-label">Balance after credit</div>
                <div id="credit-after">—</div>
              </div>
            </div>
            <button type="submit" class="wa-submit-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Apply Credit
            </button>
          </form>
        </div>

        {{-- DEBIT --}}
        <div class="wa-form-card debit">
          <div class="wa-form-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Debit Wallet
          </div>
          <form id="debit-form" method="POST">
            @csrf
            <label class="wa-form-label">Amount (₹)</label>
            <input type="number" name="amount" id="debit-amount" class="wa-form-input" placeholder="0.00" min="0.01" step="0.01" autocomplete="off">
            <label class="wa-form-label">Reason / Note</label>
            <textarea name="note" class="wa-form-textarea" placeholder="e.g. Lab equipment damage fine — ₹500"></textarea>
            <div class="wa-preview" id="debit-preview">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <div>
                <div class="wa-preview-label">Balance after debit</div>
                <div id="debit-after">—</div>
              </div>
            </div>
            <button type="submit" class="wa-submit-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Apply Debit
            </button>
          </form>
        </div>

      </div>
    </div>

    {{-- Empty state (before search) --}}
    <div id="wa-empty" class="wa-empty-state">
      <div class="wa-empty-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" stroke-width="1.5">
          <path d="M21 7H3v10h18V7z"/><path d="M17 12h.01"/><path d="M3 9h18"/>
        </svg>
      </div>
      <div style="font-size:15px;font-weight:700;color:var(--text-2);">No student selected</div>
      <div style="font-size:13px;color:var(--text-3);margin-top:4px;">Search for a student above to adjust their wallet</div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const searchInput  = document.getElementById('wa-search');
  const resultsBox   = document.getElementById('wa-results');
  const spinner      = document.getElementById('wa-spinner');
  const panel        = document.getElementById('wa-panel');
  const emptyState   = document.getElementById('wa-empty');
  const creditForm   = document.getElementById('credit-form');
  const debitForm    = document.getElementById('debit-form');
  const creditAmount = document.getElementById('credit-amount');
  const debitAmount  = document.getElementById('debit-amount');
  const creditAfter  = document.getElementById('credit-after');
  const debitAfter   = document.getElementById('debit-after');
  const balancePill  = document.getElementById('wa-balance-pill');
  const balanceDisp  = document.getElementById('wa-balance-display');

  let currentBalance = 0;
  let debounceTimer  = null;

  const searchUrl  = "{{ route('institute.wallet-adjustment.search') }}";
  const creditBase = "{{ route('institute.wallet-adjustment.credit', ':id') }}";
  const debitBase  = "{{ route('institute.wallet-adjustment.debit', ':id') }}";

  function fmt(n) {
    return '₹' + parseFloat(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function applyBalanceStyle(balance) {
    balancePill.className = 'wa-balance-pill ' + (balance < 0 ? 'negative' : balance > 0 ? 'positive' : 'zero');
    balanceDisp.textContent = fmt(balance);
  }

  searchInput.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    if (q.length < 2) {
      resultsBox.style.display = 'none';
      return;
    }
    debounceTimer = setTimeout(() => fetchStudents(q), 380);
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('#search-container')) {
      resultsBox.style.display = 'none';
    }
  });

  function fetchStudents(q) {
    spinner.style.display = 'block';
    fetch(searchUrl + '?q=' + encodeURIComponent(q), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
      spinner.style.display = 'none';
      if (!data.length) {
        resultsBox.innerHTML = '<div style="padding:14px 16px;font-size:13px;color:var(--text-3);">No students found</div>';
        resultsBox.style.display = 'block';
        return;
      }
      resultsBox.innerHTML = data.map(s => `
        <div class="wa-result-item" data-student='${JSON.stringify(s)}'>
          <div class="wa-result-avatar">${s.name.charAt(0).toUpperCase()}</div>
          <div>
            <div class="wa-result-name">${s.name}</div>
            <div class="wa-result-sub">${s.user_id} · ${s.mobile || '—'}${s.enrollment ? ' · ' + s.enrollment : ''}</div>
          </div>
          <div style="margin-left:auto;font-size:13px;font-weight:700;color:${s.balance < 0 ? '#dc2626' : s.balance > 0 ? '#16a34a' : 'var(--text-3)'}">
            ${fmt(s.balance)}
          </div>
        </div>
      `).join('');
      resultsBox.style.display = 'block';

      resultsBox.querySelectorAll('.wa-result-item').forEach(item => {
        item.addEventListener('click', function () {
          selectStudent(JSON.parse(this.dataset.student));
        });
      });
    })
    .catch(() => { spinner.style.display = 'none'; });
  }

  function selectStudent(s) {
    resultsBox.style.display = 'none';
    searchInput.value = s.name;
    currentBalance = parseFloat(s.balance) || 0;

    // Fill card
    document.getElementById('wa-avatar').textContent = s.name.charAt(0).toUpperCase();
    document.getElementById('wa-name').textContent   = s.name;
    document.getElementById('wa-sub').textContent    = [s.user_id, s.mobile, s.enrollment].filter(Boolean).join(' · ');
    applyBalanceStyle(currentBalance);

    // Set form actions
    creditForm.action = creditBase.replace(':id', s.id);
    debitForm.action  = debitBase.replace(':id', s.id);

    // Reset previews
    creditAmount.value = '';
    debitAmount.value  = '';
    creditAfter.textContent = '—';
    debitAfter.textContent  = '—';

    panel.style.display     = 'block';
    emptyState.style.display = 'none';
  }

  creditAmount.addEventListener('input', function () {
    const v = parseFloat(this.value) || 0;
    creditAfter.textContent = v > 0 ? fmt(currentBalance + v) : '—';
  });

  debitAmount.addEventListener('input', function () {
    const v = parseFloat(this.value) || 0;
    debitAfter.textContent = v > 0 ? fmt(currentBalance - v) : '—';
  });
})();
</script>
@endpush
