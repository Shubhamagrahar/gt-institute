@extends('layouts.institute')
@section('title','Review & Preview')
@section('page-title','Admission Form Preview')

@push('styles')
<style>
/* ── Action toolbar ── */
.preview-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}

/* ── Print shell (A4 style, same as new.blade.php) ── */
.review-print-shell{width:210mm;max-width:100%;min-height:160mm;margin:0 auto;background:#fff;color:#000;padding:8mm 10mm;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;font-size:10px;display:flex;flex-direction:column;border:1px solid #d1d5db;border-radius:8px;box-shadow:0 2px 16px rgba(0,0,0,.07)}
.review-print-shell *{color:#000 !important;text-decoration:none !important}
/* Institute header */
.rf-inst-header{display:flex;align-items:flex-start;gap:12px;padding-bottom:8px;border-bottom:2.5px solid #000}
.rf-inst-logo img{height:62px;width:62px;object-fit:contain;display:block}
.rf-inst-name{font-size:20px;font-weight:900;line-height:1.15}
.rf-inst-contact{font-size:9.5px;margin-top:4px;line-height:1.5}
.rf-inst-addr{font-size:8.5px;margin-top:2px;line-height:1.4}
/* Form title */
.rf-form-title{text-align:center;font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:.16em;border-bottom:2px solid #000;padding:7px 0;margin:0}
/* Photo row */
.rf-top-row{display:flex;border:1.5px solid #000;margin-top:0;flex-shrink:0}
.rf-photo-cell{width:32mm;min-width:32mm;flex-shrink:0;border-right:1.5px solid #000;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:10px 5px 8px}
.rf-photo-box{width:27mm;height:35mm;border:1.5px solid #000;overflow:hidden;background:#fff;flex-shrink:0;display:flex;align-items:stretch}
.rf-photo-box img{width:100%;height:100%;object-fit:cover;display:block}
.rf-photo-lbl{font-size:7.5px;text-align:center;margin-top:5px;line-height:1.3}
/* Top info table */
.rf-top-table{width:100%;border-collapse:collapse}
.rf-top-table td{border:1px solid #ccc;padding:5px 7px;vertical-align:middle}
.rf-top-table tr:first-child td{border-top:none}
.rf-top-table tr:last-child td{border-bottom:none}
.rf-top-table td:first-child{border-left:none}
.rf-top-table td:last-child{border-right:none}
.rf-top-table td.rf-lbl{font-size:8px;font-weight:700;text-transform:uppercase;white-space:nowrap;width:20%;background:#fafafa}
.rf-top-table td.rf-val{font-size:10.5px;font-weight:600;width:30%}
/* Section headings */
.rf-section-head{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.07em;padding:5px 0 3px;border-top:2px solid #000;border-bottom:1px solid #bbb;margin-top:10px}
/* Field table */
.rf-table{width:100%;border-collapse:collapse}
.rf-table td{border:1px solid #ccc;padding:4px 7px;vertical-align:top}
.rf-table td.rf-lbl{font-size:8px;font-weight:700;text-transform:uppercase;width:22%;white-space:nowrap;background:#fafafa}
.rf-table td.rf-val{font-size:10px;width:28%;min-height:14px}
/* Education */
.review-edu-table{width:100%;border-collapse:collapse}
.review-edu-table th,.review-edu-table td{border:1px solid #ccc;padding:4px 7px;font-size:9px;text-align:left}
.review-edu-table th{font-weight:700;font-size:8.5px;text-transform:uppercase;background:#fafafa}
/* Fee table */
.rf-fee-table{width:100%;border-collapse:collapse}
.rf-fee-table td{border:1px solid #ccc;padding:4px 7px;font-size:9.5px}
.rf-fee-table tr:last-child td{font-weight:900;background:#f0f4ff}
/* Footer */
.rf-footer{margin-top:auto;padding-top:10px}
.rf-declaration{padding:6px 9px;border:1px solid #bbb;font-size:8.5px;line-height:1.6}
.rf-auth-sign{width:200px;text-align:center}
.rf-auth-line{border-top:1.5px solid #000;margin:6px 0 4px}
.rf-auth-label{font-size:10px;font-weight:700}
.rf-auth-sub{font-size:9px}

/* ── Print ── */
@media print {
  .no-print{display:none !important}
  .review-print-shell{box-shadow:none;border:none;width:auto;min-height:auto;padding:0}
  body{background:#fff !important}
  @page{size:A4 portrait;margin:7mm}
  .gt-sidebar,.gt-topbar,.gt-overlay,.preview-toolbar{display:none !important}
  .gt-layout{display:block !important}
  .gt-main{margin:0 !important;padding:0 !important}
  .gt-page{padding:0 !important}
}
</style>
@endpush

@section('content')
@php
  $p         = $profile ?? $courseBook->student->profile;
  $u         = $courseBook->student;
  $institute = $institute ?? \App\Models\Owner\Institute::find($courseBook->institute_id);

  $fieldMap     = $fields->keyBy('field_key');
  $definedFields = collect(\App\Models\AdmissionFormField::allDefinedFields())->keyBy('key');
  $resolveField = function (string $key) use ($savedFields, $definedFields) {
      $definition = $definedFields[$key] ?? null;
      if (!$definition) return null;
      $saved = $savedFields[$key] ?? null;
      return (object)[
          'field_key'   => $key,
          'field_label' => $saved?->field_label ?? $definition['label'],
          'field_type'  => $saved?->field_type  ?? $definition['type'],
          'options'     => $saved?->options ?? ($definition['options'] ?? null),
          'is_active'   => $saved ? (bool)$saved->is_active : true,
          'is_required' => $saved ? (bool)$saved->is_required : false,
      ];
  };

  $alwaysShow  = ['city','permanent_state','permanent_district','permanent_city','permanent_pin_code'];
  $reviewWideFields = ['address','permanent_address','photo'];
  $reviewSections = [
      ['title' => 'Student Details',   'keys' => ['email','dob','gender','whatsapp_no','alternate_mobile','aadhar_no','pan_no','blood_group','category','religion','nationality','employment_status','computer_literacy','qualification']],
      ['title' => 'Address Details',   'keys' => ['address','state','district','city','pin_code','permanent_address','permanent_state','permanent_district','permanent_city','permanent_pin_code']],
      ['title' => 'Guardian Details',  'keys' => ['father_name','mother_name','guardian_name','guardian_relation','guardian_mobile','guardian_occupation']],
  ];

  // Get value for a key
  $val = function (string $key) use ($u, $p) {
      return match ($key) {
          'mobile' => $u->mobile,
          'email'  => $u->email,
          default  => $p?->{$key},
      };
  };
@endphp

{{-- ── Action toolbar ── --}}
<div class="preview-toolbar no-print">
  <div>
    @if(session('success'))
      <div style="background:#f0fdf4;color:#15803d;border:1px solid #86efac;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;">
        {{ session('success') }}
      </div>
    @else
      <div>
        <div style="font-size:16px;font-weight:900">{{ $p?->name ?? $u->user_id }}</div>
        <div style="font-size:12px;color:var(--text-2)">{{ $courseBook->course->name }}@if($courseBook->batch) &middot; {{ $courseBook->batch->name }}@endif</div>
      </div>
    @endif
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
    <a href="{{ route('institute.enrollment.profile', $courseBook) }}" class="btn btn-outline btn-sm">← Edit Details</a>
    <button onclick="window.print()" class="btn btn-outline btn-sm">Print / Download PDF</button>
    @if($courseBook->status === 'RUN')
      <a href="{{ route('institute.enrollment.payment-complete', $courseBook) }}"
         class="btn btn-primary" style="padding:9px 24px;font-weight:800;">
        View Fee History →
      </a>
    @elseif(($paidTotal ?? 0) <= 0)
      <a href="{{ route('institute.enrollment.fee', $courseBook) }}"
         class="btn btn-primary" style="padding:9px 24px;font-weight:800;">
        Proceed to Payment →
      </a>
    @else
      <form method="POST" action="{{ route('institute.enrollment.confirm', $courseBook) }}" style="display:inline">
        @csrf
        <button type="submit" class="btn btn-primary" style="padding:9px 24px;font-weight:800;"
                data-loading-text="Confirming...">Complete Admission</button>
      </form>
    @endif
  </div>
</div>

{{-- ── Printable A4 Admission Form ── --}}
<div class="review-print-shell">

  {{-- ① Institute Header --}}
  <div class="rf-inst-header">
    @if($institute?->logo && !in_array(trim($institute->logo ?? ''),['images/default-institute.png','']))
      <div class="rf-inst-logo"><img src="{{ asset($institute->logo) }}" alt="logo"></div>
    @endif
    <div style="flex:1">
      <div class="rf-inst-name">{{ $institute?->name ?? 'Institute Name' }}</div>
      <div class="rf-inst-contact">
        @if($institute?->mobile)Ph: {{ $institute->mobile }}@endif
        @if($institute?->email)&nbsp;&nbsp;|&nbsp;&nbsp;{{ $institute->email }}@endif
        @if($institute?->website)&nbsp;&nbsp;|&nbsp;&nbsp;{{ $institute->website }}@endif
      </div>
      <div class="rf-inst-addr">{{ $institute?->address ?? '' }}</div>
    </div>
  </div>

  {{-- ② Form Title --}}
  <div class="rf-form-title">Admission Application Form</div>

  {{-- ③ Photo + Personal Info --}}
  <div class="rf-top-row">
    <div class="rf-photo-cell">
      <div class="rf-photo-box">
        @if($p?->photo && !str_contains($p->photo, 'user.svg'))
          <img src="{{ asset($p->photo) }}" alt="Photo">
        @else
          <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;color:#bbb">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <span style="font-size:7px;text-align:center;line-height:1.3;">Passport<br>Photo</span>
          </div>
        @endif
      </div>
      <div class="rf-photo-lbl">Passport Size<br>Photograph</div>
    </div>
    <div style="flex:1">
      <table class="rf-top-table">
        <tr>
          <td class="rf-lbl">Full Name</td>
          <td class="rf-val">{{ $p?->name ?: '—' }}</td>
          <td class="rf-lbl">Date of Birth</td>
          <td class="rf-val">{{ $p?->dob ? \Carbon\Carbon::parse($p->dob)->format('d M Y') : '—' }}</td>
        </tr>
        <tr>
          <td class="rf-lbl">Mobile</td>
          <td class="rf-val">{{ $u->mobile ?: '—' }}</td>
          <td class="rf-lbl">Gender</td>
          <td class="rf-val">{{ $p?->gender ?: '—' }}</td>
        </tr>
        <tr>
          <td class="rf-lbl">Email</td>
          <td class="rf-val">{{ $u->email ?: '—' }}</td>
          <td class="rf-lbl">Category</td>
          <td class="rf-val">{{ $p?->category ?: '—' }}</td>
        </tr>
        <tr>
          <td class="rf-lbl">Course</td>
          <td class="rf-val" colspan="3">{{ $courseBook->course->name }}</td>
        </tr>
        <tr>
          <td class="rf-lbl">Batch</td>
          <td class="rf-val">{{ $courseBook->batch?->name ?: 'No Batch' }}</td>
          <td class="rf-lbl">Enrolment No.</td>
          <td class="rf-val" style="font-style:italic;font-size:9px">{{ $courseBook->enrollment_no ?: 'Issued after admission' }}</td>
        </tr>
      </table>
    </div>
  </div>

  {{-- ④ Dynamic Sections --}}
  @foreach($reviewSections as $section)
    @php
      $activeKeys = [];
      foreach ($section['keys'] as $k) {
          $f = $resolveField($k);
          if (in_array($k, $alwaysShow, true) || $f?->is_active) $activeKeys[] = $k;
      }
    @endphp
    @if(empty($activeKeys)) @continue @endif

    <div class="rf-section-head">{{ $section['title'] }}</div>
    <table class="rf-table">
      @php $buf = null; @endphp
      @foreach($activeKeys as $k)
        @php
          $field  = $resolveField($k);
          $isWide = in_array($k, $reviewWideFields, true);
          $lbl    = $field?->field_label ?? \Illuminate\Support\Str::of($k)->replace('_',' ')->title();
          $v      = $val($k);
          if ($k === 'dob' && $v) $v = \Carbon\Carbon::parse($v)->format('d M Y');
        @endphp
        @if($isWide)
          @if($buf !== null)
            @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title();$bV=$val($buf); @endphp
            <tr><td class="rf-lbl">{{$bL}}</td><td class="rf-val" colspan="3">{{$bV?:'—'}}</td></tr>
            @php $buf = null; @endphp
          @endif
          <tr><td class="rf-lbl">{{ $lbl }}</td><td class="rf-val" colspan="3">{{ $v ?: '—' }}</td></tr>
        @elseif($buf === null)
          @php $buf = $k; @endphp
        @else
          @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title();$bV=$val($buf); @endphp
          <tr>
            <td class="rf-lbl">{{ $bL }}</td><td class="rf-val">{{ $bV ?: '—' }}</td>
            <td class="rf-lbl">{{ $lbl }}</td><td class="rf-val">{{ $v ?: '—' }}</td>
          </tr>
          @php $buf = null; @endphp
        @endif
      @endforeach
      @if($buf !== null)
        @php $bF=$resolveField($buf);$bL=$bF?->field_label??\Illuminate\Support\Str::of($buf)->replace('_',' ')->title();$bV=$val($buf); @endphp
        <tr><td class="rf-lbl">{{$bL}}</td><td class="rf-val" colspan="3">{{$bV?:'—'}}</td></tr>
      @endif
    </table>
  @endforeach

  {{-- ⑤ Education --}}
  @if(($educationEnabled ?? true) && $education->count())
    <div class="rf-section-head">Education Details</div>
    <table class="review-edu-table">
      <thead>
        <tr><th>Examination</th><th>Institute / School</th><th>Board / University</th><th>Year</th><th>Division</th><th>%</th></tr>
      </thead>
      <tbody>
        @foreach($education as $e)
          <tr>
            <td>{{ $e->examination }}</td>
            <td>{{ $e->institute_name ?: '—' }}</td>
            <td>{{ $e->board_university ?: '—' }}</td>
            <td>{{ $e->passing_year ?: '—' }}</td>
            <td>{{ $e->division ?: '—' }}</td>
            <td>{{ $e->marks_percentage ?: '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  {{-- ⑥ Fee Breakdown --}}
  @if($snapshots->count())
    <div class="rf-section-head">Fee Breakdown</div>
    <table class="rf-fee-table">
      @foreach($snapshots as $s)
        <tr>
          <td>{{ $s->fee_type_name }}</td>
          <td style="text-align:right;width:110px">₹{{ number_format($s->final_amount, 2) }}</td>
        </tr>
      @endforeach
      <tr>
        <td>Total Payable</td>
        <td style="text-align:right">₹{{ number_format($displayTotalFee ?? $courseBook->final_fee, 2) }}</td>
      </tr>
    </table>
  @endif

  {{-- ⑦ Declaration + Signature --}}
  <div class="rf-footer">
    <div class="rf-declaration">
      I hereby declare that all the information provided above is true and correct to the best of my knowledge and belief.
      I agree to abide by the rules, regulations and fee payment schedule of the institute.
    </div>
    <div style="display:flex;justify-content:flex-end;margin-top:18px">
      <div class="rf-auth-sign">
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:flex-end;min-height:60px;gap:3px">
          @if($institute?->use_stamp && $institute?->stamp)
            <img src="{{ asset($institute->stamp) }}" alt="stamp" style="height:58px;width:58px;object-fit:contain">
          @endif
          @if($institute?->use_signature && $institute?->signature)
            <img src="{{ asset($institute->signature) }}" alt="sig" style="height:36px;max-width:130px;object-fit:contain">
          @else
            @if(!($institute?->use_stamp && $institute?->stamp))<div style="height:60px"></div>@endif
          @endif
        </div>
        <div class="rf-auth-line"></div>
        <div class="rf-auth-label">For {{ $institute?->name ?? 'Institute' }}</div>
        <div class="rf-auth-sub">Auth. Signatory</div>
      </div>
    </div>
  </div>

</div>{{-- /review-print-shell --}}

{{-- Bottom action bar --}}
<div class="no-print" style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;max-width:210mm;margin-left:auto;margin-right:auto">
  <a href="{{ route('institute.enrollment.profile', $courseBook) }}" class="btn btn-outline">← Edit Details</a>
  <button onclick="window.print()" class="btn btn-outline">Print / Download PDF</button>
  @if($courseBook->status === 'RUN')
    <a href="{{ route('institute.enrollment.payment-complete', $courseBook) }}"
       class="btn btn-primary" style="padding:11px 28px;font-weight:800;">View Fee History →</a>
  @elseif(($paidTotal ?? 0) <= 0)
    <a href="{{ route('institute.enrollment.fee', $courseBook) }}"
       class="btn btn-primary" style="padding:11px 28px;font-weight:800;">Proceed to Payment →</a>
  @else
    <form method="POST" action="{{ route('institute.enrollment.confirm', $courseBook) }}">
      @csrf
      <button type="submit" class="btn btn-primary" style="padding:11px 28px;font-weight:800;"
              data-loading-text="Confirming...">Complete Admission</button>
    </form>
  @endif
</div>
@endsection
