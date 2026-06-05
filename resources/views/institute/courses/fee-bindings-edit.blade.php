@extends('layouts.institute')
@section('title','Manage Course Fee Setup')
@section('page-title','Manage Course Fee Setup')
@section('topbar-actions')
  <a href="{{ route('institute.courses.fee-bindings') }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@push('styles')
<style>
.binding-shell {
  max-width: 980px;
  margin: 0 auto;
}

.binding-grid {
  display: grid;
  grid-template-columns: minmax(0, 360px) minmax(0, 1fr);
  gap: 18px;
}

.binding-form-card,
.binding-list-card {
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 20px;
}

.binding-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.binding-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border: 1px solid var(--border);
  border-radius: 16px;
  background: var(--bg-3);
}

.binding-item-title {
  font-size: 14px;
  font-weight: 700;
}

.binding-item-sub {
  font-size: 12px;
  color: var(--text-2);
  margin-top: 4px;
}

.binding-empty {
  padding: 18px;
  border: 1px dashed var(--border);
  border-radius: 16px;
  color: var(--text-2);
  background: var(--bg-3);
}

@media (max-width: 900px) {
  .binding-grid {
    grid-template-columns: 1fr;
  }
}
</style>
@endpush

@section('content')
<div class="binding-shell">
  <div class="gt-card" style="margin-bottom:18px;">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">{{ $course->name }}</div>
        <div class="text-xs text-muted" style="margin-top:4px;">
          {{ $course->courseType?->name ?? 'No type' }} | {{ $course->duration }} months | Base fee: Rs. {{ number_format($course->fee, 2) }}
        </div>
      </div>
    </div>
  </div>

  <div class="binding-grid">
    <div class="binding-form-card">
      <div class="gt-card-title" style="margin-bottom:6px;">Bind Fee Type</div>
      <div class="text-xs text-muted" style="margin-bottom:16px;">
        Select a fee type, enter the amount, and save it for this course.
      </div>

      @if($feeTypes->isEmpty())
        <div class="binding-empty">
          No active fee types found. Create fee types first from <a href="{{ route('institute.fee-types.index') }}">Fee Types</a>.
        </div>
      @else
        @php
          $boundFeeTypeIds = $course->feeStructures->pluck('fee_type_id')->all();
          $availableFeeTypes = $feeTypes->reject(fn ($feeType) => in_array($feeType->id, $boundFeeTypeIds, true))->values();
        @endphp
        <form method="POST" action="{{ route('institute.courses.fee-bindings.update', $course) }}">
          @csrf
          @method('PUT')

          <div class="gt-form-group">
            <label class="gt-label">Fee Type</label>
            <select name="fee_type_id" id="fee_type_id" class="gt-select" required>
              <option value="">Select fee type</option>
              @foreach($availableFeeTypes as $feeType)
                <option
                  value="{{ $feeType->id }}"
                  data-name="{{ $feeType->name }}"
                  data-mandatory="{{ $feeType->is_mandatory ? 'Mandatory' : 'Optional' }}"
                  {{ old('fee_type_id') == $feeType->id ? 'selected' : '' }}
                >
                  {{ $feeType->name }}
                </option>
              @endforeach
            </select>
            @error('fee_type_id')<div class="gt-error">{{ $message }}</div>@enderror
          </div>

          <div class="gt-form-group">
            <label class="gt-label">Amount</label>
            <input type="number" name="amount" id="binding_amount" class="gt-input" min="0.01" step="0.01" value="{{ old('amount') }}" required>
            @error('amount')<div class="gt-error">{{ $message }}</div>@enderror
          </div>

          <div class="text-xs text-muted" id="binding-meta" style="margin-bottom:16px;">Choose a fee type to continue.</div>

          <div style="display:flex;justify-content:flex-end;gap:10px;">
            <a href="{{ route('institute.courses.fee-bindings') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Binding</button>
          </div>
        </form>
      @endif
    </div>

    <div class="binding-list-card">
      <div class="gt-card-title" style="margin-bottom:6px;">Bound Fee Types</div>
      <div class="text-xs text-muted" style="margin-bottom:16px;">
        Review existing bindings and load any item into the form to update its amount.
      </div>

      @if($course->feeStructures->isEmpty())
        <div class="binding-empty">No fee types are currently bound to this course.</div>
      @else
        <div class="binding-list">
          @foreach($course->feeStructures as $binding)
            <div class="binding-item">
              <div>
                <div class="binding-item-title">{{ $binding->fee_type_name }}</div>
                <div class="binding-item-sub">Amount: Rs. {{ number_format($binding->amount, 2) }}</div>
              </div>
              <button
                type="button"
                class="btn btn-outline btn-xs"
                data-edit-binding
                data-fee-type-id="{{ $binding->fee_type_id }}"
                data-fee-type-name="{{ $binding->fee_type_name }}"
                data-fee-type-mandatory="{{ $binding->feeType?->is_mandatory ? 'Mandatory' : 'Optional' }}"
                data-amount="{{ $binding->amount }}"
              >
                Edit
              </button>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const feeTypeSelect = document.getElementById('fee_type_id');
  const amountInput = document.getElementById('binding_amount');
  const meta = document.getElementById('binding-meta');
  const editButtons = document.querySelectorAll('[data-edit-binding]');

  function updateMeta() {
    if (!feeTypeSelect || !meta) return;
    const selectedOption = feeTypeSelect.options[feeTypeSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
      meta.textContent = 'Choose a fee type to continue.';
      return;
    }

    meta.textContent = `${selectedOption.dataset.name} | ${selectedOption.dataset.mandatory} fee type`;
  }

  feeTypeSelect?.addEventListener('change', updateMeta);

  editButtons.forEach((button) => {
    button.addEventListener('click', function () {
      if (feeTypeSelect) {
        let option = [...feeTypeSelect.options].find((node) => node.value === this.dataset.feeTypeId);
        if (!option) {
          option = new Option(this.dataset.feeTypeName, this.dataset.feeTypeId, true, true);
          option.dataset.name = this.dataset.feeTypeName;
          option.dataset.mandatory = this.dataset.feeTypeMandatory;
          feeTypeSelect.add(option);
        }
        feeTypeSelect.value = this.dataset.feeTypeId;
      }
      if (amountInput) {
        amountInput.value = this.dataset.amount;
      }
      updateMeta();
      feeTypeSelect?.focus();
    });
  });

  updateMeta();
});
</script>
@endpush
