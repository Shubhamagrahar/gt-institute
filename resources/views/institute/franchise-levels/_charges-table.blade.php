{{--
  Variables expected:
  $durations    — collection of {duration, course_count}
  $typeId       — int (0 = all types, >0 = specific course_type_id)
  $typeName     — string
  $existing     — array[duration => adm_charge]
  $existingCert — array[duration => cert_charge]
--}}

@if($durations->isEmpty())
  <div class="lcc-empty-type">No courses with duration set for "{{ $typeName }}".</div>
@else
<div class="lcc-table-wrap">
  <table class="lcc-table">
    <thead>
      <tr>
        <th>Duration</th>
        <th style="width:140px;">Courses</th>
        <th style="width:190px;">Admission (₹)</th>
        <th style="width:190px;">Certificate (₹)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($durations as $dur)
      @php
        $adm  = $existing[$dur->duration]  ?? '';
        $cert = $existingCert[$dur->duration] ?? '';
      @endphp
      <tr>
        <td>
          <input type="hidden" name="rows[{{ $typeId }}_{{ $dur->duration }}][course_type_id]" value="{{ $typeId }}">
          <input type="hidden" name="rows[{{ $typeId }}_{{ $dur->duration }}][duration]"       value="{{ $dur->duration }}">
          <span class="lcc-dur-pill">{{ $dur->duration }} month{{ $dur->duration > 1 ? 's' : '' }}</span>
        </td>
        <td>
          <span class="lcc-course-count">
            {{ $dur->course_count }} course{{ $dur->course_count != 1 ? 's' : '' }}
          </span>
        </td>
        <td>
          <div class="lcc-inp-wrap">
            <span class="lcc-inp-pre">₹</span>
            <input type="number" name="rows[{{ $typeId }}_{{ $dur->duration }}][adm]"
                   class="lcc-inp" value="{{ $adm }}" min="0" step="0.01" placeholder="0.00">
          </div>
        </td>
        <td>
          <div class="lcc-inp-wrap">
            <span class="lcc-inp-pre">₹</span>
            <input type="number" name="rows[{{ $typeId }}_{{ $dur->duration }}][cert]"
                   class="lcc-inp" value="{{ $cert }}" min="0" step="0.01" placeholder="0.00">
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif
