@php
  $selectedWalletEnabled = old('wallet_enabled', isset($franchise) ? (int) $franchise->wallet_enabled : 1);
  $selectedHasSub = old('has_sub_franchise', isset($franchise) ? (int) $franchise->has_sub_franchise : 0);
  $selectedLevel = old('franchise_level_id', $franchise->franchise_level_id ?? '');
@endphp

<div class="gt-form-group">
  <label class="gt-label">Franchise Logo</label>
  <div class="logo-upload-area" id="logo-drop" onclick="document.getElementById('logo-input').click()">
    <img id="logo-preview-img" class="logo-preview" src="#" alt="Preview">
    <div id="logo-placeholder">
      <div class="logo-upload-icon">🏫</div>
      <div class="logo-upload-text">Click to upload logo</div>
      <div class="logo-upload-hint">PNG · JPG · WebP · Max 2MB</div>
    </div>
    <div class="logo-change-hint" id="logo-change-hint">Click to change logo</div>
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
        <option value="{{ $level->id }}" data-commission="{{ $level->commission_percent }}" {{ (string) $selectedLevel === (string) $level->id ? 'selected' : '' }}>
          {{ $level->name }}
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
    <label class="gt-label">Opening Balance</label>
    <input type="number" name="opening_balance" id="opening_balance" class="gt-input" value="{{ old('opening_balance', isset($franchise) ? ($franchise->wallet?->balance ?? 0) : '0') }}" min="0" step="0.01">
    @error('opening_balance')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>

<div class="gt-form-grid-3">
  <div class="gt-form-group">
    <label class="gt-label">Wallet System <span style="color:var(--danger)">*</span></label>
    <select name="wallet_enabled" id="wallet_enabled" class="gt-select" required>
      <option value="1" {{ (string) $selectedWalletEnabled === '1' ? 'selected' : '' }}>Yes</option>
      <option value="0" {{ (string) $selectedWalletEnabled === '0' ? 'selected' : '' }}>No</option>
    </select>
    @error('wallet_enabled')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
  <div class="gt-form-group">
    <label class="gt-label">Low Wallet Alert</label>
    <input type="number" name="low_wallet_alert" class="gt-input" value="{{ old('low_wallet_alert', $franchise->low_wallet_alert ?? 1000) }}" min="0" step="0.01">
    @error('low_wallet_alert')<div class="gt-error">{{ $message }}</div>@enderror
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

<div class="gt-form-group">
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
  const walletSelect = document.getElementById('wallet_enabled');
  const openingBalance = document.getElementById('opening_balance');

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

  function syncWallet() {
    const enabled = walletSelect?.value === '1';
    openingBalance.readOnly = !enabled;
    openingBalance.style.opacity = enabled ? '1' : '.6';
    if (!enabled) {
      openingBalance.value = '0';
    }
  }

  levelSelect?.addEventListener('change', syncCommission);
  walletSelect?.addEventListener('change', syncWallet);
  syncCommission();
  syncWallet();
})();
</script>
@endpush
