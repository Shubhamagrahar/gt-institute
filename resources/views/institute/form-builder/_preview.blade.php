@php
  $isQuick = $builderMode === 'quick';
  $fixedLabels = $isQuick
    ? ['Student Name', 'Mobile Number', 'Course', 'Batch', 'Payment Plan']
    : ['Student Name', 'Mobile Number', 'Course', 'Batch', 'Payment Plan', 'Fee Summary'];
  $previewGroups = [
    ['title' => 'Student Details', 'keys' => ['email', 'dob', 'gender', 'photo', 'whatsapp_no', 'alternate_mobile', 'aadhar_no', 'pan_no', 'blood_group', 'category', 'religion', 'nationality', 'employment_status', 'computer_literacy', 'qualification']],
    ['title' => 'Address Details', 'keys' => ['address', 'permanent_address', 'state', 'district', 'pin_code']],
    ['title' => 'Guardian Details', 'keys' => ['father_name', 'mother_name', 'guardian_name', 'guardian_relation', 'guardian_mobile', 'guardian_occupation']],
    ['title' => 'Education Details', 'keys' => ['education_details']],
  ];
  $wideFields = ['photo', 'address', 'permanent_address', 'education_details'];
  $renderField = function (string $key) use ($fieldsByKey, $savedFields, $isQuick) {
    $field = $fieldsByKey[$key] ?? null;
    if (!$field) {
      return null;
    }

    $saved = $savedFields[$key] ?? null;
    $isShown = $isQuick ? (bool) ($saved?->quick_is_active) : (!$saved || $saved->is_active);
    $isRequired = $isQuick ? (bool) ($saved?->quick_is_required) : (bool) ($saved?->is_required);

    return [
      'field' => $field,
      'shown' => $isShown,
      'required' => $isRequired,
    ];
  };
@endphp

<div class="form-paper" id="form-preview-paper">
  <div class="paper-header">
    <div>
      <h3>{{ $institute?->name ?? 'Institute Name' }}</h3>
      <p>{{ $institute?->address ?? 'Institute address will appear here' }}</p>
    </div>
    <div class="paper-meta">
      @if($institute?->mobile)
        <span>Phone: {{ $institute->mobile }}</span>
      @endif
      @if($institute?->email)
        <span>Email: {{ $institute->email }}</span>
      @endif
      @if($institute?->unique_id)
        <span>ID: {{ $institute->unique_id }}</span>
      @endif
    </div>
  </div>

  <div class="paper-title-row">
    <div>
      <h4>{{ $isQuick ? 'Quick Admission Form' : 'Admission Form' }}</h4>
      <p>{{ $isQuick ? 'Basic student details for quick booking' : 'Student registration and admission details' }}</p>
    </div>
    @unless($isQuick)
      <div class="paper-badge">Printable Preview</div>
    @endunless
  </div>

  <div class="paper-grid paper-grid-fixed">
    @foreach($fixedLabels as $label)
      <div class="paper-field paper-field-fixed paper-field-box">
        <label>{{ $label }}</label>
        <div class="paper-input"></div>
      </div>
    @endforeach
  </div>

  @foreach($previewGroups as $group)
    <div class="paper-section">
      <div class="paper-section-title">{{ $group['title'] }}</div>
      <div class="paper-grid">
        @foreach($group['keys'] as $key)
          @php($entry = $renderField($key))
          @continue(!$entry)
          <div class="paper-field paper-field-box {{ in_array($key, $wideFields, true) ? 'paper-field-wide' : '' }} {{ $entry['shown'] ? '' : 'fb-hidden' }}" data-preview-field data-key="{{ $key }}">
            <label>
              {{ $entry['field']['label'] }}
              <span class="paper-required {{ $entry['required'] ? '' : 'fb-hidden' }}" data-required-mark>*</span>
            </label>

            @if($key === 'photo')
              <div class="paper-photo-box">Paste / Attach Photo</div>
            @elseif($key === 'education_details')
              <div class="paper-education paper-input-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Exam</th>
                      <th>Institute</th>
                      <th>Board</th>
                      <th>Year</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>&nbsp;</td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            @elseif(($entry['field']['type'] ?? 'text') === 'textarea')
              <div class="paper-input paper-input-textarea"></div>
            @else
              <div class="paper-input"></div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endforeach

  <div class="paper-footer">
    <div>
      Student Signature
      <div class="paper-sign-line"></div>
    </div>
    <div>
      Verified By
      <div class="paper-sign-line"></div>
    </div>
    <div>
      Authorized Signatory
      <div class="paper-sign-line"></div>
    </div>
  </div>
</div>
