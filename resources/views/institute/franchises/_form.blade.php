@php
  $selectedHasSub = old('has_sub_franchise', isset($franchise) ? (int) $franchise->has_sub_franchise : 0);
  $selectedLevel  = old('franchise_level_id', $franchise->franchise_level_id ?? '');
@endphp

@php
  $currentLogo = $franchise->logo ?? null;
  $hasLogo = $currentLogo && $currentLogo !== 'images/default-institute.png';
@endphp
<div class="gt-form-group">
  <label class="gt-label">Franchise Logo</label>

  @if($hasLogo)
  <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; padding:12px 14px; background:var(--bg-3); border:1px solid var(--border-1); border-radius:var(--radius);">
    <img src="{{ asset($currentLogo) }}" alt="Current Logo"
         style="width:72px; height:72px; object-fit:contain; border-radius:8px; border:1px solid var(--border-2); background:#fff; padding:4px;">
    <div>
      <div style="font-size:13px; font-weight:600; color:var(--text-1); margin-bottom:3px;">Current Logo</div>
      <div style="font-size:12px; color:var(--text-3);">Upload a new image below to replace it.</div>
    </div>
  </div>
  @endif

  <div class="logo-upload-area" id="logo-drop" onclick="document.getElementById('logo-input').click()">
    @if($hasLogo)
      <img id="logo-preview-img" class="logo-preview" src="{{ asset($currentLogo) }}" alt="Preview" style="display:block;">
      <div id="logo-placeholder" style="display:none;">
        <div class="logo-upload-icon">🏫</div>
        <div class="logo-upload-text">Click to upload logo</div>
        <div class="logo-upload-hint">PNG · JPG · WebP · Max 2MB</div>
      </div>
      <div class="logo-change-hint" id="logo-change-hint" style="display:block;">Click to change logo</div>
    @else
      <img id="logo-preview-img" class="logo-preview" src="#" alt="Preview">
      <div id="logo-placeholder">
        <div class="logo-upload-icon">🏫</div>
        <div class="logo-upload-text">Click to upload logo</div>
        <div class="logo-upload-hint">PNG · JPG · WebP · Max 2MB</div>
      </div>
      <div class="logo-change-hint" id="logo-change-hint">Click to change logo</div>
    @endif
  </div>
  <input type="file" name="logo" id="logo-input" accept="image/*">
  @error('logo')<div class="gt-error">{{ $message }}</div>@enderror
</div>

<div class="gt-form-grid-2">
  <div class="gt-form-group">
    <label class="gt-label">Franchise Name <span style="color:var(--danger)">*</span></label>
    <input type="text" name="name" class="gt-input" value="{{ old('name', $franchise->name ?? '') }}" required>
    @error('name')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Short Name / Code</label>
    <input type="text" name="short_name" class="gt-input" value="{{ old('short_name', $franchise->short_name ?? '') }}">
  </div>
</div>

<div class="gt-form-grid-2">
  <div class="gt-form-group">
    <label class="gt-label">Email <span style="color:var(--danger)">*</span></label>
    <input type="email" name="email" class="gt-input" value="{{ old('email', $franchise->email ?? '') }}" required>
    @error('email')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
    <input type="tel" name="mobile" class="gt-input" value="{{ old('mobile', $franchise->mobile ?? '') }}"
           maxlength="10" inputmode="numeric" pattern="[0-9]{10}" placeholder="10-digit mobile" required>
    @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>

<div class="gt-form-grid-2">
  <div class="gt-form-group">
    <label class="gt-label">Franchise Level <span style="color:var(--danger)">*</span></label>
    <select name="franchise_level_id" id="franchise_level_id" class="gt-select" required>
      <option value="">Select level</option>
      @foreach($levels as $level)
        <option value="{{ $level->id }}"
          data-commission="{{ $level->commission_percent }}"
          {{ (string) $selectedLevel === (string) $level->id ? 'selected' : '' }}>
          {{ $level->name }}{{ ($level->level_fee ?? 0) > 0 ? ' (Joining Fee: ₹'.number_format($level->level_fee,0).')' : '' }}
        </option>
      @endforeach
    </select>
    @error('franchise_level_id')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Commission %</label>
    <input type="number" name="commission_percent" id="commission_percent" class="gt-input"
           value="{{ old('commission_percent', $franchise->commission_percent ?? '') }}"
           step="0.01" min="0" max="100" readonly>
    @error('commission_percent')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>

<div class="gt-form-grid-3">
  <div class="gt-form-group">
    <label class="gt-label">Owner Name <span style="color:var(--danger)">*</span></label>
    <input type="text" name="owner_name" class="gt-input" value="{{ old('owner_name', $franchise->owner_name ?? '') }}" required>
    @error('owner_name')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Owner Mobile <span style="color:var(--danger)">*</span></label>
    <input type="tel" name="owner_mobile" class="gt-input" value="{{ old('owner_mobile', $franchise->owner_mobile ?? '') }}"
           maxlength="10" inputmode="numeric" pattern="[0-9]{10}" placeholder="10-digit mobile" required>
    @error('owner_mobile')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Has Own Sub-Franchise?</label>
    <select name="has_sub_franchise" class="gt-select">
      <option value="0" {{ (string) $selectedHasSub === '0' ? 'selected' : '' }}>No</option>
      <option value="1" {{ (string) $selectedHasSub === '1' ? 'selected' : '' }}>Yes</option>
    </select>
  </div>
</div>

{{-- ── Wallet Settings ─────────────────────────────────────────────────── --}}
<div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border-1);">
  <div style="font-size:11px; font-weight:700; color:var(--text-3); text-transform:uppercase; letter-spacing:.6px; margin-bottom:14px;">Wallet Settings</div>

  <div class="wallet-charges-info" style="margin-bottom:14px;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    All franchises use the wallet system. Course-wise admission &amp; certificate charges will be set on the <strong>next step</strong>.
  </div>

  <div class="gt-form-grid-2">
    <div class="gt-form-group">
      <label class="gt-label">Low Wallet Alert (₹)</label>
      <input type="number" name="low_wallet_alert" class="gt-input"
        value="{{ old('low_wallet_alert', $franchise->low_wallet_alert ?? 1000) }}" min="0" step="0.01">
      @error('low_wallet_alert')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Opening Wallet Balance (₹)</label>
      <input type="number" name="opening_balance" class="gt-input"
        value="{{ old('opening_balance', isset($franchise) ? ($franchise->wallet?->balance ?? 0) : '0') }}" min="0" step="0.01">
      @error('opening_balance')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

{{-- Always wallet mode --}}
<input type="hidden" name="management_type" value="wallet">
<input type="hidden" name="wallet_enabled" value="1">

<div class="gt-form-group" style="margin-top:16px;">
  <label class="gt-label">Address</label>
  <textarea name="address" class="gt-textarea" style="min-height:80px;">{{ old('address', $franchise->address ?? '') }}</textarea>
  @error('address')<div class="gt-error">{{ $message }}</div>@enderror
</div>

@php
  $selectedStateName = old('state', $franchise->state ?? '');
  $selectedDistrict  = old('district', $franchise->district ?? '');
  $selectedStateObj  = ($states ?? collect())->firstWhere('name', $selectedStateName);
  $selectedStateId   = $selectedStateObj?->id ?? '';
@endphp

<div class="gt-form-grid-2">
  <div class="gt-form-group">
    <label class="gt-label">State</label>
    <select name="state" id="frm-state" class="gt-select" onchange="frmLoadDistricts(this)">
      <option value="">Select State</option>
      @foreach($states ?? [] as $st)
        <option value="{{ $st->name }}" data-state-id="{{ $st->id }}"
          {{ $selectedStateName === $st->name ? 'selected' : '' }}>
          {{ $st->name }}
        </option>
      @endforeach
    </select>
    @error('state')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">District</label>
    <select name="district" id="frm-district" class="gt-select">
      <option value="">Select District</option>
      @if($selectedStateId && isset($districtsMap[$selectedStateId]))
        @foreach($districtsMap[$selectedStateId] as $d)
          <option value="{{ $d }}" {{ $selectedDistrict === $d ? 'selected' : '' }}>{{ $d }}</option>
        @endforeach
      @endif
    </select>
    @error('district')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>

<div class="gt-form-grid-2">
  <div class="gt-form-group">
    <label class="gt-label">PIN Code</label>
    <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code', $franchise->pin_code ?? '') }}"
           maxlength="6" inputmode="numeric" placeholder="6-digit PIN">
    @error('pin_code')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Website</label>
    <input type="url" name="website" class="gt-input" value="{{ old('website', $franchise->website ?? '') }}">
    @error('website')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>

@push('styles')
<style>
.logo-upload-area {
  border: 2px dashed var(--border-2);
  border-radius: var(--radius);
  padding: 22px 16px;
  text-align: center;
  cursor: pointer;
  transition: all var(--transition);
  position: relative;
  background: var(--bg-3);
}
.logo-upload-area:hover { border-color: var(--accent); background: var(--accent-bg); }
.logo-upload-area.has-image { border-style: solid; border-color: var(--accent); padding: 14px; }
.logo-preview { width: 84px; height: 84px; object-fit: contain; border-radius: var(--radius-sm); display: none; margin: 0 auto 8px; }
.logo-upload-icon { font-size: 30px; margin-bottom: 8px; opacity: .45; }
.logo-upload-text { font-size: 13px; color: var(--text-2); font-weight: 500; }
.logo-upload-hint { font-size: 11px; color: var(--text-3); margin-top: 4px; }
.logo-change-hint { display:none; font-size: 12px; color: var(--text-2); margin-top: 6px; }
#logo-input { display: none; }

.wallet-charges-info {
  display: flex; align-items: center; gap: 8px;
  font-size: 12.5px; color: var(--text-2);
  background: var(--bg-3); border: 1px solid var(--border-2);
  border-radius: var(--radius-sm); padding: 9px 13px;
}
</style>
@endpush

@push('scripts')
<script>
const FRM_DISTRICTS = @json(($districtsMap ?? collect())->toArray());

function frmLoadDistricts(stateSelect) {
  const stateId = stateSelect.selectedOptions[0]?.dataset?.stateId || '';
  const distSel = document.getElementById('frm-district');
  const current = distSel.value;
  distSel.innerHTML = '<option value="">Select District</option>';

  if (stateId && FRM_DISTRICTS[stateId]) {
    FRM_DISTRICTS[stateId].forEach(function (name) {
      const opt = document.createElement('option');
      opt.value = name;
      opt.textContent = name;
      if (name === current) opt.selected = true;
      distSel.appendChild(opt);
    });
  }
}

document.querySelectorAll('input[name="mobile"], input[name="owner_mobile"]').forEach(function (inp) {
  inp.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
  });
});

(function () {
  const logoInput       = document.getElementById('logo-input');
  const logoDrop        = document.getElementById('logo-drop');
  const logoPreviewImg  = document.getElementById('logo-preview-img');
  const logoPlaceholder = document.getElementById('logo-placeholder');
  const logoChangeHint  = document.getElementById('logo-change-hint');
  const levelSelect     = document.getElementById('franchise_level_id');
  const commissionInput = document.getElementById('commission_percent');

  if (logoInput) {
    logoInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = function (e) {
        logoPreviewImg.src = e.target.result;
        logoPreviewImg.style.display = 'block';
        logoPlaceholder.style.display = 'none';
        logoChangeHint.style.display = 'block';
        logoDrop.classList.add('has-image');
      };
      reader.readAsDataURL(file);
    });
  }

  function syncCommission() {
    const option = levelSelect?.selectedOptions?.[0];
    commissionInput.value = option?.dataset?.commission || '';
  }

  levelSelect?.addEventListener('change', syncCommission);
  syncCommission();
})();
</script>
@endpush
