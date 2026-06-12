@php
  $selectedWalletEnabled = old('wallet_enabled', isset($franchise) ? (int) $franchise->wallet_enabled : 1);
  $selectedHasSub = old('has_sub_franchise', isset($franchise) ? (int) $franchise->has_sub_franchise : 0);
  $selectedLevel = old('franchise_level_id', $franchise->franchise_level_id ?? '');
  $selectedMgmt = old('management_type', $franchise->management_type ?? 'wallet');
@endphp

@php
  $currentLogo = $franchise->logo ?? null;
  $hasLogo = $currentLogo && $currentLogo !== 'images/default-institute.png';
@endphp
<div class="gt-form-group">
  <label class="gt-label">Franchise Logo</label>

  @if($hasLogo)
  {{-- Current logo shown when editing --}}
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
    <input type="text" name="mobile" class="gt-input" value="{{ old('mobile', $franchise->mobile ?? '') }}" required>
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
          data-level-fee="{{ $level->level_fee ?? 0 }}"
          {{ (string) $selectedLevel === (string) $level->id ? 'selected' : '' }}>
          {{ $level->name }}{{ ($level->level_fee ?? 0) > 0 ? ' (Fee: ₹'.number_format($level->level_fee,0).')' : '' }}
        </option>
      @endforeach
    </select>
    @error('franchise_level_id')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Commission %</label>
    <input type="number" name="commission_percent" id="commission_percent" class="gt-input" value="{{ old('commission_percent', $franchise->commission_percent ?? '') }}" step="0.01" min="0" max="100" readonly>
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
    <input type="text" name="owner_mobile" class="gt-input" value="{{ old('owner_mobile', $franchise->owner_mobile ?? '') }}" required>
    @error('owner_mobile')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Has Own Franchise? <span style="color:var(--danger)">*</span></label>
    <select name="has_sub_franchise" class="gt-select" required>
      <option value="0" {{ (string) $selectedHasSub === '0' ? 'selected' : '' }}>No</option>
      <option value="1" {{ (string) $selectedHasSub === '1' ? 'selected' : '' }}>Yes</option>
    </select>
    @error('has_sub_franchise')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>

{{-- ===== MANAGEMENT TYPE SELECTOR ===== --}}
<div style="margin-top:20px; margin-bottom:8px;">
  <div class="gt-card-title" style="font-size:13px; margin-bottom:12px;">Franchise Management Type <span style="color:var(--danger)">*</span></div>
  <div class="mgmt-type-cards" id="mgmt-type-cards">

    <label class="mgmt-type-card {{ $selectedMgmt === 'wallet' ? 'selected' : '' }}" for="mgmt_wallet">
      <input type="radio" name="management_type" id="mgmt_wallet" value="wallet" {{ $selectedMgmt === 'wallet' ? 'checked' : '' }}>
      <div class="mgmt-type-icon">💳</div>
      <div class="mgmt-type-title">Wallet System</div>
      <div class="mgmt-type-desc">Franchise pre-loads a wallet. Each admission and certificate generation automatically deducts the set amount. Admissions are blocked when balance runs out.</div>
    </label>

    <label class="mgmt-type-card {{ $selectedMgmt === 'independent' ? 'selected' : '' }}" for="mgmt_independent">
      <input type="radio" name="management_type" id="mgmt_independent" value="independent" {{ $selectedMgmt === 'independent' ? 'checked' : '' }}>
      <div class="mgmt-type-icon">🏢</div>
      <div class="mgmt-type-title">Independent</div>
      <div class="mgmt-type-desc">Collect a one-time onboarding fee and grant full franchise access. The franchise manages its own admissions independently with no per-transaction deductions.</div>
    </label>

  </div>
  @error('management_type')<div class="gt-error">{{ $message }}</div>@enderror
</div>

{{-- ===== WALLET MODE FIELDS ===== --}}
<div id="wallet-fields" style="{{ $selectedMgmt !== 'wallet' ? 'display:none;' : '' }}">
  <div class="gt-form-grid-2" style="margin-top:12px;">
    <div class="gt-form-group">
      <label class="gt-label">Per Admission Charge (₹) <span style="color:var(--danger)">*</span></label>
      <input type="number" name="admission_charge" id="admission_charge" class="gt-input"
        value="{{ old('admission_charge', $franchise->admission_charge ?? 0) }}" min="0" step="0.01">
      <div class="text-xs text-muted" style="margin-top:4px;">Deducted from wallet on each new admission</div>
      @error('admission_charge')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Per Certificate Charge (₹) <span style="color:var(--danger)">*</span></label>
      <input type="number" name="certificate_charge" id="certificate_charge" class="gt-input"
        value="{{ old('certificate_charge', $franchise->certificate_charge ?? 0) }}" min="0" step="0.01">
      <div class="text-xs text-muted" style="margin-top:4px;">Deducted from wallet on each certificate generation</div>
      @error('certificate_charge')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="gt-form-grid-3" style="margin-top:4px;">
    <div class="gt-form-group">
      <label class="gt-label">Low Wallet Alert (₹)</label>
      <input type="number" name="low_wallet_alert" id="low_wallet_alert" class="gt-input"
        value="{{ old('low_wallet_alert', $franchise->low_wallet_alert ?? 1000) }}" min="0" step="0.01">
      @error('low_wallet_alert')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Opening Wallet Balance (₹)</label>
      <input type="number" name="opening_balance" id="opening_balance" class="gt-input"
        value="{{ old('opening_balance', isset($franchise) ? ($franchise->wallet?->balance ?? 0) : '0') }}" min="0" step="0.01">
      @error('opening_balance')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div class="gt-form-group">
      <label class="gt-label">Wallet Active?</label>
      <select name="wallet_enabled" id="wallet_enabled" class="gt-select">
        <option value="1" {{ (string) $selectedWalletEnabled === '1' ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ (string) $selectedWalletEnabled === '0' ? 'selected' : '' }}>No</option>
      </select>
      @error('wallet_enabled')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

{{-- ===== INDEPENDENT MODE FIELDS ===== --}}
<div id="independent-fields" style="{{ $selectedMgmt !== 'independent' ? 'display:none;' : '' }}">
  <div class="gt-form-grid-2" style="margin-top:12px;">
    <div class="gt-form-group">
      <label class="gt-label">Onboarding Fee (₹)</label>
      <input type="number" name="onboarding_fee" id="onboarding_fee" class="gt-input"
        value="{{ old('onboarding_fee', $franchise->onboarding_fee ?? 0) }}" min="0" step="0.01" readonly>
      <div class="text-xs text-muted" id="onboarding-fee-hint" style="margin-top:4px;">Auto-filled from selected level. Editable below if needed.</div>
      @error('onboarding_fee')<div class="gt-error">{{ $message }}</div>@enderror
    </div>
    <div class="gt-form-group" style="display:flex; align-items:flex-end;">
      <div class="mgmt-info-box" style="width:100%;">
        <div style="font-size:12px; color:var(--text-2); line-height:1.5;">
          In independent mode the wallet system is disabled. The franchise gets direct admission access with no per-transaction deductions. The onboarding fee is tracked separately.
        </div>
      </div>
    </div>
  </div>
  {{-- Hidden fields for independent mode so validation passes --}}
  <input type="hidden" name="wallet_enabled" value="0">
  <input type="hidden" name="admission_charge" value="0">
  <input type="hidden" name="certificate_charge" value="0">
  <input type="hidden" name="low_wallet_alert" value="0">
  <input type="hidden" name="opening_balance" value="0">
</div>

<div class="gt-form-group" style="margin-top:16px;">
  <label class="gt-label">Address</label>
  <textarea name="address" class="gt-textarea" style="min-height:80px;">{{ old('address', $franchise->address ?? '') }}</textarea>
  @error('address')<div class="gt-error">{{ $message }}</div>@enderror
</div>

<div class="gt-form-grid-3">
  <div class="gt-form-group">
    <label class="gt-label">State</label>
    <input type="text" name="state" class="gt-input" value="{{ old('state', $franchise->state ?? '') }}">
    @error('state')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">PIN Code</label>
    <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code', $franchise->pin_code ?? '') }}">
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

/* Management type card selector */
.mgmt-type-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.mgmt-type-card {
  border: 2px solid var(--border-2);
  border-radius: var(--radius);
  padding: 18px 16px;
  cursor: pointer;
  transition: all .18s;
  background: var(--bg-3);
  display: block;
  position: relative;
}
.mgmt-type-card input[type=radio] {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 16px;
  height: 16px;
  accent-color: var(--accent);
}
.mgmt-type-card:hover { border-color: var(--accent); background: var(--accent-bg); }
.mgmt-type-card.selected { border-color: var(--accent); background: var(--accent-bg); }
.mgmt-type-icon { font-size: 26px; margin-bottom: 8px; }
.mgmt-type-title { font-size: 14px; font-weight: 700; color: var(--text-1); margin-bottom: 6px; }
.mgmt-type-desc { font-size: 12px; color: var(--text-3); line-height: 1.55; }
.mgmt-info-box {
  background: var(--bg-3);
  border: 1px solid var(--border-2);
  border-radius: var(--radius-sm);
  padding: 12px 14px;
}
</style>
@endpush

@push('scripts')
<script>
(function () {
  const logoInput = document.getElementById('logo-input');
  const logoDrop = document.getElementById('logo-drop');
  const logoPreviewImg = document.getElementById('logo-preview-img');
  const logoPlaceholder = document.getElementById('logo-placeholder');
  const logoChangeHint = document.getElementById('logo-change-hint');
  const levelSelect = document.getElementById('franchise_level_id');
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

  const onboardingFeeInput = document.getElementById('onboarding_fee');
  const onboardingFeeHint = document.getElementById('onboarding-fee-hint');

  function syncCommission() {
    const option = levelSelect?.selectedOptions?.[0];
    commissionInput.value = option?.dataset?.commission || '';

    // Auto-fill onboarding fee from level (only in independent fields section)
    if (onboardingFeeInput) {
      const fee = parseFloat(option?.dataset?.levelFee || 0);
      onboardingFeeInput.value = fee > 0 ? fee : 0;
      if (onboardingFeeHint && fee > 0) {
        onboardingFeeHint.textContent = 'Level fee: ₹' + fee.toLocaleString('en-IN') + ' (auto-filled from selected level)';
      }
    }
  }

  levelSelect?.addEventListener('change', syncCommission);
  syncCommission();

  // Management type toggle
  const mgmtRadios = document.querySelectorAll('input[name="management_type"]');
  const walletFields = document.getElementById('wallet-fields');
  const independentFields = document.getElementById('independent-fields');
  const mgmtCards = document.querySelectorAll('.mgmt-type-card');

  function syncMgmtType() {
    const val = document.querySelector('input[name="management_type"]:checked')?.value;
    if (val === 'wallet') {
      walletFields.style.display = '';
      independentFields.style.display = 'none';
      // Disable ALL independent-fields controls so they never shadow wallet values on submit
      independentFields.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
      walletFields.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
    } else {
      walletFields.style.display = 'none';
      independentFields.style.display = '';
      // Disable ALL wallet-fields controls so they don't submit stale values
      walletFields.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
      independentFields.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
    }
    // Highlight selected card
    mgmtCards.forEach(card => {
      const radio = card.querySelector('input[type=radio]');
      card.classList.toggle('selected', radio?.checked);
    });
  }

  mgmtRadios.forEach(radio => radio.addEventListener('change', syncMgmtType));
  mgmtCards.forEach(card => {
    card.addEventListener('click', function () {
      const radio = this.querySelector('input[type=radio]');
      if (radio) { radio.checked = true; syncMgmtType(); }
    });
  });

  syncMgmtType();
})();
</script>
@endpush
