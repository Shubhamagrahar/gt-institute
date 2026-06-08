<div class="gt-form-grid-3">
  <div class="gt-form-group">
    <label class="gt-label">Name <span style="color:var(--danger)">*</span></label>
    <input type="text" name="name" class="gt-input" value="{{ old('name', $channelPartner->name ?? '') }}" required>
    @error('name')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
    <input type="text" name="mobile" class="gt-input" value="{{ old('mobile', $channelPartner->mobile ?? '') }}" required>
    @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">WhatsApp Number</label>
    <input type="text" name="whatsapp_no" class="gt-input" value="{{ old('whatsapp_no', $channelPartner->whatsapp_no ?? '') }}">
    @error('whatsapp_no')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Alternate Mobile</label>
    <input type="text" name="alternate_mobile" class="gt-input" value="{{ old('alternate_mobile', $channelPartner->alternate_mobile ?? '') }}">
    @error('alternate_mobile')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Email</label>
    <input type="email" name="email" class="gt-input" value="{{ old('email', $channelPartner->email ?? '') }}">
    @error('email')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Father Name</label>
    <input type="text" name="father_name" class="gt-input" value="{{ old('father_name', $channelPartner->father_name ?? '') }}">
    @error('father_name')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Date of Birth</label>
    <input type="date" name="dob" class="gt-input" value="{{ old('dob', isset($channelPartner) && $channelPartner->dob ? $channelPartner->dob->format('Y-m-d') : '') }}">
    @error('dob')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Gender</label>
    <select name="gender" class="gt-select">
      <option value="">Select Gender</option>
      @foreach(['Male', 'Female', 'Other'] as $option)
        <option value="{{ $option }}" {{ old('gender', $channelPartner->gender ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
      @endforeach
    </select>
    @error('gender')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Occupation</label>
    <input type="text" name="occupation" class="gt-input" value="{{ old('occupation', $channelPartner->occupation ?? '') }}">
    @error('occupation')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Aadhar Card Number</label>
    <input type="text" name="aadhar_no" class="gt-input" value="{{ old('aadhar_no', $channelPartner->aadhar_no ?? '') }}">
    @error('aadhar_no')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">PAN Number</label>
    <input type="text" name="pan_no" class="gt-input" value="{{ old('pan_no', $channelPartner->pan_no ?? '') }}">
    @error('pan_no')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">State</label>
    <select name="state" id="channel_partner_state" class="gt-select">
      <option value="">Select State</option>
      @foreach($states as $state)
        <option value="{{ $state }}" {{ old('state', $channelPartner->state ?? '') === $state ? 'selected' : '' }}>{{ $state }}</option>
      @endforeach
    </select>
    @error('state')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">District</label>
    <select name="district" id="channel_partner_district" class="gt-select">
      <option value="">Select District</option>
    </select>
    @error('district')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">City</label>
    <input type="text" name="city" class="gt-input" value="{{ old('city', $channelPartner->city ?? '') }}">
    @error('city')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">PIN Code</label>
    <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code', $channelPartner->pin_code ?? '') }}">
    @error('pin_code')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group">
    <label class="gt-label">Status</label>
    <select name="status" class="gt-select">
      <option value="active" {{ old('status', $channelPartner->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $channelPartner->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group" style="grid-column: 1 / -1;">
    <label class="gt-label">Address</label>
    <textarea name="address" class="gt-textarea">{{ old('address', $channelPartner->address ?? '') }}</textarea>
    @error('address')<div class="gt-error">{{ $message }}</div>@enderror
  </div>

  <div class="gt-form-group" style="grid-column: 1 / -1;">
    <label class="gt-label">Notes</label>
    <textarea name="notes" class="gt-textarea">{{ old('notes', $channelPartner->notes ?? '') }}</textarea>
    @error('notes')<div class="gt-error">{{ $message }}</div>@enderror
  </div>
</div>

@push('scripts')
<script>
(() => {
  const stateSelect = document.getElementById('channel_partner_state');
  const districtSelect = document.getElementById('channel_partner_district');
  const districtsByState = @json($districtsByState ?? []);
  const oldDistrict = @json(old('district', $channelPartner->district ?? ''));

  function renderDistrictOptions(stateName, selectedDistrict = '') {
    if (!districtSelect) {
      return;
    }

    const districts = districtsByState[stateName] || [];
    districtSelect.innerHTML = '<option value="">Select District</option>' + districts.map((district) =>
      `<option value="${district}">${district}</option>`
    ).join('');

    if (selectedDistrict && districts.includes(selectedDistrict)) {
      districtSelect.value = selectedDistrict;
    }
  }

  stateSelect?.addEventListener('change', () => renderDistrictOptions(stateSelect.value));
  renderDistrictOptions(stateSelect?.value || '', oldDistrict);
})();
</script>
@endpush
