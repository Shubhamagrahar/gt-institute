@extends('layouts.owner')
@section('title','Add Institute')
@section('page-title','Add New Institute')
@section('topbar-actions')
  <a href="{{ route('owner.institutes.index') }}" class="btn btn-outline btn-sm">← Back to Institutes</a>
@endsection

@push('styles')
<style>
/* ── Create Institute Layout ── */
.ci-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
  align-items: start;
}

/* Logo Upload */
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

/* Plan Cards */
.plan-cards-wrap { display: flex; flex-direction: column; gap: 8px; }
.plan-card {
  border: 1px solid var(--border-2);
  border-radius: var(--radius-sm);
  padding: 12px 14px;
  cursor: pointer;
  transition: all var(--transition);
}
.plan-card:hover { border-color: var(--text-3); }
.plan-card.selected { border-color: var(--accent); background: var(--accent-bg); }
.plan-card-radio { position: absolute; opacity: 0; pointer-events: none; }
.plan-card-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
.plan-card-name { font-size: 13.5px; font-weight: 600; }
.plan-card-price { font-family: var(--font-mono); font-size: 13px; font-weight: 600; color: var(--accent); }
.plan-card-duration { font-size: 11px; color: var(--text-3); margin-top: 2px; }
.plan-card-dot {
  width: 16px; height: 16px;
  border: 2px solid var(--border-2);
  border-radius: 50%; flex-shrink: 0;
  transition: all var(--transition);
  display: flex; align-items: center; justify-content: center;
}
.plan-card.selected .plan-card-dot { border-color: var(--accent); background: var(--accent); }
.plan-card.selected .plan-card-dot::after { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #000; }
.plan-features-inline {
  margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--border);
  display: none; flex-wrap: wrap; gap: 5px;
}
.plan-card.selected .plan-features-inline { display: flex; }

/* Not-included features */
.not-included-section {
  margin-top: 10px;
  padding: 10px 12px;
  background: rgba(255,77,77,.06);
  border: 1px solid rgba(255,77,77,.15);
  border-radius: var(--radius-sm);
  display: none;
}
.not-included-section.visible { display: block; }
.not-included-label { font-size: 11px; font-weight: 600; color: var(--danger); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 7px; }
.not-included-pills { display: flex; flex-wrap: wrap; gap: 5px; }

/* Discount - always both visible */
.discount-inputs-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 4px; }
.discount-input-box { position: relative; }
.discount-input-box .gt-input { padding-left: 36px; }
.discount-input-prefix { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; font-weight: 600; color: var(--text-3); pointer-events: none; }
.discount-input-box.active .discount-input-prefix { color: var(--accent); }
.discount-input-box.active .gt-input { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(232,255,71,.1); }

/* Price Summary */
.price-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; font-size: 13px; }
.price-row .plabel { color: var(--text-2); }
.price-row .pvalue { font-family: var(--font-mono); font-weight: 500; }
.price-row.total { padding-top: 12px; margin-top: 6px; border-top: 1px solid var(--border); }
.price-row.total .plabel { font-weight: 600; font-size: 14px; color: var(--text); }
.price-row.total .pvalue { font-size: 20px; font-weight: 700; color: var(--accent); }

/* Addon feature checkboxes */
.addon-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
  gap: 7px;
  margin-top: 4px;
}
.addon-check {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 11px;
  background: var(--bg-3);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all var(--transition);
}
.addon-check:hover:not(.disabled-addon) { border-color: var(--border-2); }
.addon-check input[type=checkbox] { accent-color: var(--accent); flex-shrink: 0; }
.addon-check.checked { border-color: var(--accent); background: var(--accent-bg); }
.addon-check.disabled-addon { opacity: .35; pointer-events: none; }
.addon-check-name { font-size: 12px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.addon-check-price { font-size: 11px; color: var(--accent); }

/* Responsive */
@media (max-width: 1100px) { .ci-grid { grid-template-columns: 1fr; } }
@media (max-width: 640px) {
  .discount-inputs-grid { grid-template-columns: 1fr; }
  .addon-grid { grid-template-columns: 1fr 1fr; }
  .gt-form-grid-3 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 400px) {
  .addon-grid { grid-template-columns: 1fr; }
  .gt-form-grid-3 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- Hidden data for JS --}}
<script id="plan-prices-data" type="application/json">
  {!! json_encode($plans->pluck('price','id')) !!}
</script>
<script id="plan-features-data" type="application/json">
  {!! json_encode($plans->mapWithKeys(fn($p) => [$p->id => $p->features->pluck('name','id')])) !!}
</script>
<script id="all-features-data" type="application/json">
  {!! json_encode($features->mapWithKeys(fn($f) => [$f->id => ['name' => $f->name, 'price' => $f->price]])) !!}
</script>

<form method="POST" action="{{ route('owner.institutes.store') }}" enctype="multipart/form-data" id="ci-form">
@csrf

<div class="ci-grid">

  {{-- ════════ LEFT (8-col equivalent) ════════ --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Institute Details Card --}}
    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px;opacity:.7;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          Institute Details
        </div>
      </div>

      {{-- Logo --}}
      <div class="gt-form-group">
        <label class="gt-label">Institute Logo</label>
        <div class="logo-upload-area" id="logo-drop" onclick="document.getElementById('logo-input').click()">
          <img id="logo-preview-img" class="logo-preview" src="#" alt="Preview">
          <div id="logo-placeholder">
            <div class="logo-upload-icon">🏫</div>
            <div class="logo-upload-text">Click to upload logo</div>
            <div class="logo-upload-hint">PNG · JPG · WebP · Max 2MB · Recommended 200×200px</div>
          </div>
          <div class="logo-change-hint" id="logo-change-hint">Click to change logo</div>
        </div>
        <input type="file" name="logo" id="logo-input" accept="image/*">
        @error('logo')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      {{-- Name + Short Name --}}
      <div class="gt-form-grid-2">
        <div class="gt-form-group">
          <label class="gt-label">Institute Name <span style="color:var(--danger)">*</span></label>
          <input type="text" name="name" class="gt-input @error('name') is-invalid @enderror"
            value="{{ old('name') }}" placeholder="Full institute name" required>
          @error('name')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Short Name / Code</label>
          <input type="text" name="short_name" class="gt-input" value="{{ old('short_name') }}" placeholder="e.g. AKY, DPS">
        </div>
      </div>

      {{-- Email + Mobile --}}
      <div class="gt-form-grid-2">
        <div class="gt-form-group">
          <label class="gt-label">Email <span style="color:var(--danger)">*</span></label>
          <input type="email" name="email" class="gt-input @error('email') is-invalid @enderror"
            value="{{ old('email') }}" placeholder="institute@email.com" required>
          @error('email')<div class="gt-error">{{ $message }}</div>@enderror
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
          <input type="text" name="mobile" class="gt-input" value="{{ old('mobile') }}" placeholder="10-digit number" required>
        </div>
      </div>

      {{-- Owner Name + Mobile + Type --}}
      <div class="gt-form-grid-3">
        <div class="gt-form-group">
          <label class="gt-label">Owner Name <span style="color:var(--danger)">*</span></label>
          <input type="text" name="owner_name" class="gt-input" value="{{ old('owner_name') }}" placeholder="Full name" required>
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Owner Mobile <span style="color:var(--danger)">*</span></label>
          <input type="text" name="owner_mobile" class="gt-input" value="{{ old('owner_mobile') }}" placeholder="10-digit number" required>
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Institute Type</label>
          <select name="type" class="gt-select">
            <option value="PRIVATE"   {{ old('type','PRIVATE')=='PRIVATE'   ? 'selected' : '' }}>Private</option>
            <option value="GOVT"      {{ old('type')=='GOVT'                ? 'selected' : '' }}>Government</option>
            <option value="FRANCHISE" {{ old('type')=='FRANCHISE'           ? 'selected' : '' }}>Franchise</option>
          </select>
        </div>
      </div>

      {{-- Address --}}
      <div class="gt-form-group">
        <label class="gt-label">Address</label>
        <textarea name="address" class="gt-textarea" style="min-height:60px;" placeholder="Full address">{{ old('address') }}</textarea>
      </div>

      {{-- State + PIN + Website --}}
      <div class="gt-form-grid-3">
        <div class="gt-form-group" style="margin-bottom:0;">
          <label class="gt-label">State</label>
          <input type="text" name="state" class="gt-input" value="{{ old('state') }}" placeholder="e.g. Uttar Pradesh">
        </div>
        <div class="gt-form-group" style="margin-bottom:0;">
          <label class="gt-label">PIN Code</label>
          <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code') }}" placeholder="6-digit PIN">
        </div>
        <div class="gt-form-group" style="margin-bottom:0;">
          <label class="gt-label">Website</label>
          <input type="url" name="website" class="gt-input" value="{{ old('website') }}" placeholder="https://...">
        </div>
      </div>
    </div>

    {{-- Add-on Features Card --}}
    @if($features->isNotEmpty())
    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px;opacity:.7;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          Add-on Features
        </div>
        <span class="text-xs text-muted">Extra features on top of the selected plan</span>
      </div>
      <div class="addon-grid">
        @foreach($features as $feature)
        <label class="addon-check" id="addon-wrap-{{ $feature->id }}">
          <input type="checkbox" name="addon_feature_ids[]" value="{{ $feature->id }}"
            class="addon-feature-check" data-price="{{ $feature->price }}" data-id="{{ $feature->id }}"
            {{ in_array($feature->id, (array)old('addon_feature_ids',[])) ? 'checked' : '' }}>
          <div style="flex:1;min-width:0;">
            <div class="addon-check-name">{{ $feature->name }}</div>
            <div class="addon-check-price">+₹{{ number_format($feature->price,2) }}</div>
          </div>
        </label>
        @endforeach
      </div>
      <p id="addon-plan-note" style="display:none;margin-top:10px;font-size:12px;color:var(--text-3);">
        ℹ Features already included in the selected plan are grayed out and cannot be added as add-ons.
      </p>
    </div>
    @endif

  </div>{{-- END LEFT --}}


  {{-- ════════ RIGHT (4-col equivalent) ════════ --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Plan Selection --}}
    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px;opacity:.7;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Select Plan <span style="color:var(--danger)">*</span>
        </div>
      </div>

      @if($plans->isEmpty())
        <div class="gt-empty" style="padding:24px 0;">
          <div class="gt-empty-icon">📋</div>
          <div class="gt-empty-title">No active plans</div>
          <div class="gt-empty-sub"><a href="{{ route('owner.plans.create') }}">Create a plan first →</a></div>
        </div>
      @else
        <div class="plan-cards-wrap">
          @foreach($plans as $plan)
          <label class="plan-card {{ old('plan_id') == $plan->id ? 'selected' : '' }}" data-plan-id="{{ $plan->id }}">
            <input type="radio" name="plan_id" value="{{ $plan->id }}" class="plan-card-radio"
              {{ old('plan_id') == $plan->id ? 'checked' : '' }} required>
            <div class="plan-card-top">
              <div>
                <div class="plan-card-name">{{ $plan->name }}</div>
                <div class="plan-card-duration">{{ $plan->duration }} month{{ $plan->duration > 1 ? 's' : '' }}</div>
              </div>
              <div style="display:flex;align-items:center;gap:10px;">
                <div class="plan-card-price">₹{{ number_format($plan->price,0) }}</div>
                <div class="plan-card-dot"></div>
              </div>
            </div>
            <div class="plan-features-inline">
              @forelse($plan->features as $f)
                <span class="badge badge-success" style="font-size:10px;padding:2px 7px;">✓ {{ $f->name }}</span>
              @empty
                <span class="text-xs text-muted">No features assigned to this plan</span>
              @endforelse
            </div>
          </label>
          @endforeach
        </div>
        @error('plan_id')<div class="gt-error" style="margin-top:6px;">{{ $message }}</div>@enderror

        {{-- Features NOT included in selected plan --}}
        <div class="not-included-section" id="not-included-section">
          <div class="not-included-label">⚠ Not included in this plan</div>
          <div class="not-included-pills" id="not-included-pills"></div>
        </div>
      @endif
    </div>

    {{-- Discount --}}
    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px;opacity:.7;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
          Discount
        </div>
        <span class="text-xs text-muted">Optional</span>
      </div>

      <p class="text-xs text-muted" style="margin-bottom:14px;line-height:1.6;">
        Fill either or both fields. If both are entered, the one giving a higher discount will be applied automatically.
      </p>

      <input type="hidden" name="discount_type"  id="discount_type"  value="{{ old('discount_type','NONE') }}">
      <input type="hidden" name="discount_value" id="discount_value" value="{{ old('discount_value',0) }}">

      <div class="discount-inputs-grid">
        <div>
          <label class="gt-label">Percentage</label>
          <div class="discount-input-box" id="pct-box">
            <span class="discount-input-prefix">%</span>
            <input type="number" id="discount_percent_input" class="gt-input"
              placeholder="e.g. 10" min="0" max="100" step="0.01"
              value="{{ old('discount_type')==='PERCENT' ? old('discount_value') : '' }}">
          </div>
        </div>
        <div>
          <label class="gt-label">Flat Amount</label>
          <div class="discount-input-box" id="flat-box">
            <span class="discount-input-prefix">₹</span>
            <input type="number" id="discount_rupee_input" class="gt-input"
              placeholder="e.g. 500" min="0" step="0.01"
              value="{{ old('discount_type')==='FLAT' ? old('discount_value') : '' }}">
          </div>
        </div>
      </div>

      <div id="discount-applied-hint" style="display:none;margin-top:8px;font-size:11.5px;color:var(--accent);"></div>
    </div>

    {{-- Price Summary --}}
    <div class="gt-card" style="border-color:var(--border-2);">
      <div class="gt-card-header">
        <div class="gt-card-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px;opacity:.7;"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          Price Summary
        </div>
      </div>
      <div>
        <div class="price-row">
          <span class="plabel">Plan Price</span>
          <span class="pvalue" id="plan_price_display">₹0.00</span>
        </div>
        <div class="price-row">
          <span class="plabel">Add-ons</span>
          <span class="pvalue" id="addon_total_display">₹0.00</span>
        </div>
        <div class="price-row">
          <span class="plabel">Subtotal</span>
          <span class="pvalue" id="subtotal_display">₹0.00</span>
        </div>
        <div class="price-row">
          <span class="plabel">Discount</span>
          <span class="pvalue amount-neg" id="discount_amt_display">₹0.00</span>
        </div>
        <div class="price-row total">
          <span class="plabel">Final Amount</span>
          <span class="pvalue text-accent" id="final_amt_display">₹0.00</span>
        </div>
        <p class="text-xs text-muted" style="text-align:right;margin-top:10px;">
          This amount will be debited from institute wallet
        </p>
      </div>
    </div>

    {{-- Submit --}}
    <div class="gt-card" style="padding:18px 20px;">
      <button type="submit" class="btn btn-primary w-full btn-lg" style="justify-content:center;gap:8px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Create Institute &amp; Send Credentials
      </button>
      <p class="text-xs text-muted" style="text-align:center;margin-top:8px;">
        Login credentials will be emailed to the institute automatically
      </p>
    </div>

  </div>{{-- END RIGHT --}}

</div>{{-- END ci-grid --}}
</form>
@endsection

@push('scripts')
<script>
(function () {
  const planPrices   = JSON.parse(document.getElementById('plan-prices-data').textContent);
  const planFeatures = JSON.parse(document.getElementById('plan-features-data').textContent);
  const allFeatures  = JSON.parse(document.getElementById('all-features-data').textContent);

  /* ── Logo Upload ── */
  const logoInput       = document.getElementById('logo-input');
  const logoDrop        = document.getElementById('logo-drop');
  const logoPreviewImg  = document.getElementById('logo-preview-img');
  const logoPlaceholder = document.getElementById('logo-placeholder');
  const logoChangeHint  = document.getElementById('logo-change-hint');

  logoInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      logoPreviewImg.src = e.target.result;
      logoPreviewImg.style.display = 'block';
      logoPlaceholder.style.display = 'none';
      logoChangeHint.style.display = 'block';
      logoDrop.classList.add('has-image');
    };
    reader.readAsDataURL(file);
  });

  /* ── Plan Selection ── */
  const planCards          = document.querySelectorAll('.plan-card');
  const notIncSection      = document.getElementById('not-included-section');
  const notIncPills        = document.getElementById('not-included-pills');
  const addonNote          = document.getElementById('addon-plan-note');
  const discountAppliedHint = document.getElementById('discount-applied-hint');

  function updatePlanUI(planId) {
    planCards.forEach(c => c.classList.toggle('selected', c.dataset.planId == planId));

    const includedMap = planId ? (planFeatures[planId] || {}) : {};
    const includedIds = Object.keys(includedMap).map(Number);

    // Gray out plan-included addons
    document.querySelectorAll('.addon-feature-check').forEach(cb => {
      const fid  = parseInt(cb.dataset.id);
      const wrap = document.getElementById('addon-wrap-' + fid);
      if (!wrap) return;
      if (includedIds.includes(fid)) {
        cb.checked = false;
        wrap.classList.add('disabled-addon');
        wrap.classList.remove('checked');
      } else {
        wrap.classList.remove('disabled-addon');
      }
    });

    if (addonNote) addonNote.style.display = (planId && includedIds.length) ? '' : 'none';

    // Not-included features
    if (notIncSection && notIncPills) {
      const notInc = Object.entries(allFeatures).filter(([fid]) => !includedIds.includes(parseInt(fid)));
      notIncPills.innerHTML = '';
      if (planId && notInc.length > 0) {
        notInc.forEach(([fid, f]) => {
          const pill = document.createElement('span');
          pill.className = 'badge badge-danger';
          pill.style.cssText = 'font-size:10px;padding:2px 8px;';
          pill.textContent = '✗ ' + f.name;
          notIncPills.appendChild(pill);
        });
        notIncSection.classList.add('visible');
      } else {
        notIncSection.classList.remove('visible');
      }
    }

    recalc();
  }

  planCards.forEach(card => {
    card.addEventListener('click', function () {
      this.querySelector('.plan-card-radio').checked = true;
      updatePlanUI(this.dataset.planId);
    });
  });

  const preChecked = document.querySelector('.plan-card-radio:checked');
  if (preChecked) updatePlanUI(preChecked.value);

  /* ── Addon Checkboxes ── */
  document.querySelectorAll('.addon-feature-check').forEach(cb => {
    cb.addEventListener('change', function () {
      const wrap = document.getElementById('addon-wrap-' + this.dataset.id);
      if (wrap) wrap.classList.toggle('checked', this.checked);
      recalc();
    });
    if (cb.checked) {
      const wrap = document.getElementById('addon-wrap-' + cb.dataset.id);
      if (wrap) wrap.classList.add('checked');
    }
  });

  /* ── Discount ── */
  const pctInput  = document.getElementById('discount_percent_input');
  const flatInput = document.getElementById('discount_rupee_input');
  const pctBox    = document.getElementById('pct-box');
  const flatBox   = document.getElementById('flat-box');
  const dtHidden  = document.getElementById('discount_type');
  const dvHidden  = document.getElementById('discount_value');

  pctInput.addEventListener('input',  () => recalc());
  flatInput.addEventListener('input', () => recalc());

  /* ── Recalculate ── */
  function fmt(n) {
    return '₹' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function recalc() {
    const planId    = document.querySelector('.plan-card-radio:checked')?.value;
    const planPrice = planId ? (parseFloat(planPrices[planId]) || 0) : 0;

    let addonTotal = 0;
    document.querySelectorAll('.addon-feature-check:checked').forEach(cb => {
      addonTotal += parseFloat(cb.dataset.price) || 0;
    });

    const subtotal = planPrice + addonTotal;
    const pct  = parseFloat(pctInput.value)  || 0;
    const flat = parseFloat(flatInput.value) || 0;

    const pctAmt  = pct  > 0 ? Math.round((subtotal * pct / 100) * 100) / 100 : 0;
    const flatAmt = flat > 0 ? flat : 0;

    let discountAmt = 0, discType = 'NONE', discVal = 0, appliedHint = '';

    if (pctAmt > 0 && flatAmt > 0) {
      if (pctAmt >= flatAmt) {
        discountAmt = pctAmt; discType = 'PERCENT'; discVal = pct;
        appliedHint = `✓ Percentage discount applied (₹${pctAmt.toFixed(2)} > ₹${flatAmt.toFixed(2)})`;
      } else {
        discountAmt = flatAmt; discType = 'FLAT'; discVal = flat;
        appliedHint = `✓ Flat discount applied (₹${flatAmt.toFixed(2)} > ₹${pctAmt.toFixed(2)})`;
      }
    } else if (pctAmt > 0) {
      discountAmt = pctAmt; discType = 'PERCENT'; discVal = pct;
    } else if (flatAmt > 0) {
      discountAmt = flatAmt; discType = 'FLAT'; discVal = flat;
    }

    const finalAmt = Math.max(0, subtotal - discountAmt);

    dtHidden.value = discType;
    dvHidden.value = discVal;

    // Highlight active discount box
    pctBox.classList.toggle('active',  pct  > 0);
    flatBox.classList.toggle('active', flat > 0);

    if (discountAppliedHint) {
      discountAppliedHint.textContent = appliedHint;
      discountAppliedHint.style.display = appliedHint ? '' : 'none';
    }

    document.getElementById('plan_price_display').textContent   = fmt(planPrice);
    document.getElementById('addon_total_display').textContent  = fmt(addonTotal);
    document.getElementById('subtotal_display').textContent     = fmt(subtotal);
    document.getElementById('discount_amt_display').textContent = discountAmt > 0 ? '— ' + fmt(discountAmt) : '₹0.00';
    document.getElementById('final_amt_display').textContent    = fmt(finalAmt);
  }

  recalc();
})();
</script>
@endpush
