@php
  $isQuick = $builderMode === 'quick';
  $fixedFields = $isQuick
    ? [
        ['label' => 'Student Name', 'wide' => true],
        ['label' => 'Mobile Number'],
        ['label' => 'Course'],
        ['label' => 'Batch'],
        ['label' => 'Payment Plan'],
      ]
    : [
        ['label' => 'Student Name', 'wide' => true],
        ['label' => 'Mobile Number'],
        ['label' => 'Course'],
        ['label' => 'Batch'],
        ['label' => 'Payment Plan'],
        ['label' => 'Fee Summary'],
      ];

  $sections = [
    ['title' => 'Student Details', 'keys' => ['email', 'dob', 'gender', 'whatsapp_no', 'alternate_mobile', 'aadhar_no', 'pan_no', 'blood_group', 'category', 'religion', 'nationality', 'employment_status', 'computer_literacy', 'qualification', 'photo']],
    ['title' => 'Address Details', 'keys' => ['address', 'permanent_address', 'state', 'district', 'pin_code']],
    ['title' => 'Guardian Details', 'keys' => ['father_name', 'mother_name', 'guardian_name', 'guardian_relation', 'guardian_mobile', 'guardian_occupation']],
    ['title' => 'Education Details', 'keys' => ['education_details']],
  ];
  $wideFields = ['address', 'permanent_address', 'education_details', 'photo'];
  $isVisible = function (string $key) use ($savedFields, $isQuick): array {
    $saved = $savedFields[$key] ?? null;
    return [
      'shown' => $isQuick ? (bool) ($saved?->quick_is_active) : (!$saved || $saved->is_active),
      'required' => $isQuick ? (bool) ($saved?->quick_is_required) : (bool) ($saved?->is_required),
    ];
  };
@endphp

<div class="print-form">
  <div class="print-header">
    <div>
      <div class="print-title">{{ $institute?->name ?? 'Institute Name' }}</div>
      <div class="print-subtitle">{{ $isQuick ? 'Quick Admission Form' : 'Admission Application Form' }}</div>
      <div class="print-address">{{ $institute?->address ?? 'Institute address will appear here' }}</div>
    </div>
    <div class="print-header-side">
      @if($institute?->mobile)
        <div><strong>Phone:</strong> {{ $institute->mobile }}</div>
      @endif
      @if($institute?->email)
        <div><strong>Email:</strong> {{ $institute->email }}</div>
      @endif
      @if($institute?->unique_id)
        <div><strong>ID:</strong> {{ $institute->unique_id }}</div>
      @endif
      <div><strong>Date:</strong> ____________________</div>
    </div>
  </div>

  <div class="print-top-grid">
    <div class="print-section-card">
      <div class="print-section-heading">Basic Information</div>
      <div class="print-grid print-grid-basic">
        @foreach($fixedFields as $field)
          <div class="print-field {{ !empty($field['wide']) ? 'print-field-wide' : '' }}">
            <label>{{ $field['label'] }}</label>
            <div class="print-input-line"></div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="print-photo-card">
      <div class="print-section-heading">Photo</div>
      <div class="print-photo-slot">Paste Passport Size Photo</div>
    </div>
  </div>

  @foreach($sections as $section)
    <div class="print-section-card">
      <div class="print-section-heading">{{ $section['title'] }}</div>
      <div class="print-grid">
        @foreach($section['keys'] as $key)
          @php($field = $fieldsByKey[$key] ?? null)
          @continue(!$field)
          @php($state = $isVisible($key))
          @continue(!$state['shown'])

          <div class="print-field {{ in_array($key, $wideFields, true) ? 'print-field-wide' : '' }}">
            <label>{{ $field['label'] }}@if($state['required'])<span class="print-required">*</span>@endif</label>
            @if($key === 'education_details')
              <div class="print-edu-wrap">
                <table class="print-edu-table">
                  <thead>
                    <tr>
                      <th>Exam</th>
                      <th>Institute</th>
                      <th>Board</th>
                      <th>Year</th>
                      <th>Marks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                  </tbody>
                </table>
              </div>
            @elseif($key === 'photo')
              <div class="print-photo-inline">Student photo can also be attached here if needed</div>
            @elseif(($field['type'] ?? 'text') === 'textarea')
              <div class="print-input-line print-input-textarea"></div>
            @else
              <div class="print-input-line"></div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endforeach

  <div class="print-footer-grid">
    <div class="print-sign-block">
      Student Signature
      <div class="print-sign-line"></div>
    </div>
    <div class="print-sign-block">
      Checked By
      <div class="print-sign-line"></div>
    </div>
    <div class="print-sign-block">
      Authorized Signatory
      <div class="print-sign-line"></div>
    </div>
  </div>
</div>
