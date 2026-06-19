@extends('layouts.institute')

@section('title', 'Institute Profile')

@section('content')

{{-- Locked Info Banner --}}
<div style="background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.28);border-radius:10px;padding:11px 16px;display:flex;align-items:center;gap:10px;margin-bottom:20px;">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="flex-shrink:0;">
    <rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/><circle cx="12" cy="16" r="1"/>
  </svg>
  <span style="font-size:12.5px;color:#92400e;font-weight:500;">
    Fields marked with
    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" style="vertical-align:middle;"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/></svg>
    are fixed and cannot be changed. Contact platform support to modify them.
  </span>
</div>

<form method="POST" action="{{ route('institute.accounts.profile.update') }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  {{-- ─── ROW 1: Logo | Identity locked fields ─── --}}
  <div class="gt-card" style="margin-bottom:18px;">
    <div class="gt-card-header">
      <div class="gt-card-title">Identity & Logo</div>
    </div>

    <div style="display:flex;gap:32px;align-items:flex-start;flex-wrap:wrap;">

      {{-- Logo upload --}}
      <div style="display:flex;flex-direction:column;align-items:center;gap:10px;min-width:120px;">
        <div id="logo-preview-wrap" style="width:108px;height:108px;border-radius:14px;border:2px dashed rgba(108,93,211,.35);background:rgba(108,93,211,.05);display:flex;align-items:center;justify-content:center;overflow:hidden;">
          @if($institute->logo && $institute->logo !== 'images/default-institute.png')
            <img src="{{ asset($institute->logo) }}" style="width:100%;height:100%;object-fit:contain;">
          @else
            <div style="text-align:center;padding:8px;">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="rgba(108,93,211,.35)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
              <div style="font-size:10px;color:rgba(108,93,211,.4);margin-top:3px;">No Logo</div>
            </div>
          @endif
        </div>
        <label for="logo-input" style="cursor:pointer;font-size:12px;color:#6c5dd3;font-weight:600;padding:5px 14px;border:1px solid rgba(108,93,211,.35);border-radius:6px;">
          Change Logo
        </label>
        <input type="file" id="logo-input" name="logo" accept="image/*" style="display:none;" data-preview="logo-preview-wrap">
        <div style="font-size:10px;color:#94a3b8;text-align:center;">JPG, PNG, WEBP · Max 2MB</div>
        @error('logo')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      {{-- Locked identity fields --}}
      <div style="flex:1;display:grid;grid-template-columns:repeat(3,1fr);gap:14px;min-width:280px;">

        <div>
          <label class="gt-label">Institute ID <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" style="vertical-align:middle;margin-left:3px;"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/></svg></label>
          <div class="gt-input" style="background:rgba(0,0,0,.03);color:#64748b;cursor:not-allowed;">{{ $institute->unique_id }}</div>
        </div>

        <div>
          <label class="gt-label">Type <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" style="vertical-align:middle;margin-left:3px;"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/></svg></label>
          <div class="gt-input" style="background:rgba(0,0,0,.03);color:#64748b;cursor:not-allowed;">{{ $institute->type }}</div>
        </div>

        <div>
          <label class="gt-label">Status <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" style="vertical-align:middle;margin-left:3px;"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/></svg></label>
          <div class="gt-input" style="background:rgba(0,0,0,.03);color:#64748b;cursor:not-allowed;text-transform:capitalize;">{{ $institute->status }}</div>
        </div>

        <div style="grid-column:1/-1;">
          <label class="gt-label">Institute Name (Legal) <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" style="vertical-align:middle;margin-left:3px;"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/></svg></label>
          <div class="gt-input" style="background:rgba(0,0,0,.03);color:#64748b;cursor:not-allowed;">{{ $institute->name }}</div>
        </div>

        <div style="grid-column:1/-1;">
          <label class="gt-label">Login Email <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" style="vertical-align:middle;margin-left:3px;"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V8a5 5 0 0 1 10 0v3"/></svg></label>
          <div class="gt-input" style="background:rgba(0,0,0,.03);color:#64748b;cursor:not-allowed;">{{ $institute->email }}</div>
        </div>

      </div>
    </div>
  </div>

  {{-- ─── ROW 2: Institute Details ─── --}}
  <div class="gt-card" style="margin-bottom:18px;">
    <div class="gt-card-header">
      <div class="gt-card-title">Institute Details</div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">

      <div>
        <label class="gt-label" for="short_name">Short Name / Abbreviation</label>
        <input type="text" name="short_name" id="short_name"
          value="{{ old('short_name', $institute->short_name) }}"
          class="gt-input @error('short_name') is-invalid @enderror"
          placeholder="e.g. GTech" maxlength="50">
        @error('short_name')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="gt-label" for="mobile">Institute Mobile <span style="color:#ef4444;">*</span></label>
        <input type="text" name="mobile" id="mobile"
          value="{{ old('mobile', $institute->mobile) }}"
          class="gt-input @error('mobile') is-invalid @enderror"
          required maxlength="15">
        @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="gt-label" for="website">Website</label>
        <input type="url" name="website" id="website"
          value="{{ old('website', $institute->website) }}"
          class="gt-input @error('website') is-invalid @enderror"
          placeholder="https://example.com" maxlength="150">
        @error('website')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="gt-label" for="state">State</label>
        <select name="state" id="state" class="gt-input @error('state') is-invalid @enderror">
          <option value="">— Select State —</option>
          @foreach($states as $s)
            <option value="{{ $s }}" {{ old('state', $institute->state) == $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
        @error('state')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="gt-label" for="district">District</label>
        <select name="district" id="district" class="gt-input @error('district') is-invalid @enderror">
          <option value="">— Select District —</option>
          @if($institute->state && isset($districtsByState[$institute->state]))
            @foreach($districtsByState[$institute->state] as $d)
              <option value="{{ $d }}" {{ old('district', $institute->district) == $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
          @endif
        </select>
        @error('district')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="gt-label" for="pin_code">PIN Code</label>
        <input type="text" name="pin_code" id="pin_code"
          value="{{ old('pin_code', $institute->pin_code) }}"
          class="gt-input @error('pin_code') is-invalid @enderror"
          placeholder="6-digit PIN" maxlength="10">
        @error('pin_code')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div style="grid-column:1/-1;">
        <label class="gt-label" for="address">Address</label>
        <textarea name="address" id="address" rows="2"
          class="gt-input @error('address') is-invalid @enderror"
          placeholder="Full address of your institute" maxlength="500"
          style="resize:vertical;">{{ old('address', $institute->address) }}</textarea>
        @error('address')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

    </div>
  </div>

  {{-- ─── ROW 3: Owner Details ─── --}}
  <div class="gt-card" style="margin-bottom:18px;">
    <div class="gt-card-header">
      <div class="gt-card-title">Owner / Director Details</div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

      <div>
        <label class="gt-label" for="owner_name">Owner Name <span style="color:#ef4444;">*</span></label>
        <input type="text" name="owner_name" id="owner_name"
          value="{{ old('owner_name', $institute->owner_name) }}"
          class="gt-input @error('owner_name') is-invalid @enderror"
          required maxlength="100">
        @error('owner_name')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="gt-label" for="owner_mobile">Owner Mobile <span style="color:#ef4444;">*</span></label>
        <input type="text" name="owner_mobile" id="owner_mobile"
          value="{{ old('owner_mobile', $institute->owner_mobile) }}"
          class="gt-input @error('owner_mobile') is-invalid @enderror"
          required maxlength="15">
        @error('owner_mobile')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

    </div>
  </div>

  {{-- ─── ROW 4: Signature & Stamp ─── --}}
  <div class="gt-card" style="margin-bottom:24px;">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Signature & Stamp</div>
        <div class="text-sm text-muted" style="margin-top:2px;">Upload on a white / transparent background. Use the toggle to control whether each item is printed on certificates and documents.</div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

      {{-- ── Signature ── --}}
      <div style="border:1px dashed rgba(108,93,211,.25);border-radius:10px;padding:18px;">

        {{-- Header row: title + toggle --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
          <div>
            <div style="font-size:13px;font-weight:600;color:var(--text-primary,#1e293b);">Authorised Signature</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:1px;">Print signature on documents?</div>
          </div>
          <label class="gt-toggle" title="Print signature on documents">
            <input type="hidden" name="use_signature" value="0">
            <input type="checkbox" name="use_signature" value="1" id="use-sig-toggle"
              {{ old('use_signature', $institute->use_signature) ? 'checked' : '' }}>
            <span class="gt-toggle-slider"></span>
          </label>
        </div>

        {{-- Status hint --}}
        <div id="sig-status-hint" style="font-size:11px;font-weight:500;padding:6px 10px;border-radius:6px;margin-bottom:12px;
          {{ old('use_signature', $institute->use_signature) ? 'background:rgba(16,185,129,.08);color:#065f46;border:1px solid rgba(16,185,129,.2);' : 'background:rgba(100,116,139,.07);color:#64748b;border:1px solid rgba(100,116,139,.15);' }}">
          {{ old('use_signature', $institute->use_signature) ? '✓ Signature will be printed on documents' : '✗ Signature will NOT be printed — will be added manually' }}
        </div>

        {{-- Preview --}}
        <div id="sig-preview-wrap" style="height:90px;background:rgba(0,0,0,.025);border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:12px;">
          @if($institute->signature)
            <img src="{{ asset($institute->signature) }}" style="max-height:80px;max-width:100%;object-fit:contain;">
          @else
            <div style="text-align:center;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(100,116,139,.3)" stroke-width="1.5"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
              <div style="font-size:10px;color:#94a3b8;margin-top:4px;">No signature uploaded</div>
            </div>
          @endif
        </div>

        <div style="text-align:center;">
          <label for="sig-input" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#6c5dd3;font-weight:600;padding:6px 16px;border:1px solid rgba(108,93,211,.35);border-radius:6px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload Signature
          </label>
          <input type="file" id="sig-input" name="signature" accept="image/*" style="display:none;" data-preview="sig-preview-wrap" data-type="signature">
          <div style="font-size:10px;color:#94a3b8;margin-top:8px;">JPG, PNG, WEBP · Max 1MB</div>
          @error('signature')<div class="gt-error" style="margin-top:6px;">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- ── Stamp ── --}}
      <div style="border:1px dashed rgba(108,93,211,.25);border-radius:10px;padding:18px;">

        {{-- Header row: title + toggle --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
          <div>
            <div style="font-size:13px;font-weight:600;color:var(--text-primary,#1e293b);">Institute Seal / Stamp</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:1px;">Print stamp on documents?</div>
          </div>
          <label class="gt-toggle" title="Print stamp on documents">
            <input type="hidden" name="use_stamp" value="0">
            <input type="checkbox" name="use_stamp" value="1" id="use-stamp-toggle"
              {{ old('use_stamp', $institute->use_stamp) ? 'checked' : '' }}>
            <span class="gt-toggle-slider"></span>
          </label>
        </div>

        {{-- Status hint --}}
        <div id="stamp-status-hint" style="font-size:11px;font-weight:500;padding:6px 10px;border-radius:6px;margin-bottom:12px;
          {{ old('use_stamp', $institute->use_stamp) ? 'background:rgba(16,185,129,.08);color:#065f46;border:1px solid rgba(16,185,129,.2);' : 'background:rgba(100,116,139,.07);color:#64748b;border:1px solid rgba(100,116,139,.15);' }}">
          {{ old('use_stamp', $institute->use_stamp) ? '✓ Stamp will be printed on documents' : '✗ Stamp will NOT be printed — will be applied manually' }}
        </div>

        {{-- Preview --}}
        <div id="stamp-preview-wrap" style="height:90px;background:rgba(0,0,0,.025);border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:12px;">
          @if($institute->stamp)
            <img src="{{ asset($institute->stamp) }}" style="max-height:80px;max-width:100%;object-fit:contain;">
          @else
            <div style="text-align:center;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(100,116,139,.3)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
              <div style="font-size:10px;color:#94a3b8;margin-top:4px;">No stamp uploaded</div>
            </div>
          @endif
        </div>

        <div style="text-align:center;">
          <label for="stamp-input" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#6c5dd3;font-weight:600;padding:6px 16px;border:1px solid rgba(108,93,211,.35);border-radius:6px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload Stamp
          </label>
          <input type="file" id="stamp-input" name="stamp" accept="image/*" style="display:none;" data-preview="stamp-preview-wrap" data-type="stamp">
          <div style="font-size:10px;color:#94a3b8;margin-top:8px;">JPG, PNG, WEBP · Max 1MB</div>
          @error('stamp')<div class="gt-error" style="margin-top:6px;">{{ $message }}</div>@enderror
        </div>
      </div>

    </div>
  </div>

  {{-- ─── ROW 5: Admission Settings ─── --}}
  <div class="gt-card" style="margin-bottom:18px;">
    <div class="gt-card-header">
      <div class="gt-card-title">Admission Settings</div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;">

      <div>
        <label class="gt-label">Seat Booking Validity (days)</label>
        <div style="display:flex;align-items:center;gap:10px;">
          <input type="number" name="seat_booking_validity_days" class="gt-input" min="1" max="365"
            value="{{ old('seat_booking_validity_days', $institute->seat_booking_validity_days ?? 30) }}"
            style="max-width:120px;">
          <span style="font-size:12px;color:#64748b;">days after booking date</span>
        </div>
        <div style="font-size:11.5px;color:#94a3b8;margin-top:5px;">
          OPEN seat bookings older than this will be auto-expired. Default: 30 days.
        </div>
        @error('seat_booking_validity_days')<div class="gt-error">{{ $message }}</div>@enderror
      </div>

    </div>
  </div>

  <div style="display:flex;justify-content:flex-end;padding-bottom:10px;">
    <button type="submit" class="btn btn-primary" style="padding:10px 30px;font-size:14px;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><polyline points="20 6 9 17 4 12"/></svg>
      Save Changes
    </button>
  </div>

</form>

@endsection

@push('scripts')
<script>
const districtsByState = @json($districtsByState);

document.getElementById('state')?.addEventListener('change', function () {
  const districtSel = document.getElementById('district');
  const selected    = this.value;
  const districts   = districtsByState[selected] || [];
  districtSel.innerHTML = '<option value="">— Select District —</option>';
  districts.forEach(d => {
    const opt = document.createElement('option');
    opt.value = d;
    opt.textContent = d;
    districtSel.appendChild(opt);
  });
});

// Generic image preview handler
document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
  input.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const wrap = document.getElementById(this.dataset.preview);
    const reader = new FileReader();
    reader.onload = e => {
      wrap.innerHTML = `<img src="${e.target.result}" style="max-height:${this.dataset.type ? '80px' : '100%'};max-width:100%;object-fit:contain;">`;
    };
    reader.readAsDataURL(file);
  });
});

// Toggle hint updater
function updateHint(toggleEl, hintEl, onText, offText) {
  const on = toggleEl.checked;
  hintEl.textContent = on ? onText : offText;
  hintEl.style.background    = on ? 'rgba(16,185,129,.08)' : 'rgba(100,116,139,.07)';
  hintEl.style.color         = on ? '#065f46'               : '#64748b';
  hintEl.style.borderColor   = on ? 'rgba(16,185,129,.2)'  : 'rgba(100,116,139,.15)';
}

const useSigToggle   = document.getElementById('use-sig-toggle');
const sigHint        = document.getElementById('sig-status-hint');
const useStampToggle = document.getElementById('use-stamp-toggle');
const stampHint      = document.getElementById('stamp-status-hint');

useSigToggle?.addEventListener('change', () =>
  updateHint(useSigToggle, sigHint,
    '✓ Signature will be printed on documents',
    '✗ Signature will NOT be printed — will be added manually'));

useStampToggle?.addEventListener('change', () =>
  updateHint(useStampToggle, stampHint,
    '✓ Stamp will be printed on documents',
    '✗ Stamp will NOT be printed — will be applied manually'));
</script>
@endpush
