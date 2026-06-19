@extends('layouts.institute')
@section('title','Enquiry — '.$enquiry->name)
@section('page-title','Enquiry Detail')

@push('styles')
<style>
/* Timeline */
.tl-wrap  { position:relative; padding-left:32px; }
.tl-wrap::before { content:''; position:absolute; left:11px; top:8px; bottom:0;
                   width:2px; background:var(--border); }
.tl-item  { position:relative; margin-bottom:18px; }
.tl-dot   { position:absolute; left:-32px; top:5px; width:22px; height:22px;
            border-radius:50%; background:var(--accent); border:3px solid var(--bg);
            display:flex; align-items:center; justify-content:center;
            font-size:9px; color:#fff; font-weight:700; }
.tl-dot.grey  { background:var(--bg-3); border-color:var(--border); }
.tl-dot.green { background:#22c55e; }
.tl-dot.red   { background:#ef4444; }
.tl-card  { background:var(--bg-2); border:1px solid var(--border); border-radius:10px;
            padding:12px 16px; }
.tl-meta  { font-size:11px; color:var(--text-2); margin-bottom:6px; }
.tl-note  { font-size:13px; line-height:1.6; color:var(--text); }
.outcome-chip { display:inline-block; padding:2px 10px; border-radius:20px;
                font-size:11px; font-weight:700; margin-top:7px; }
.outcome-INTERESTED     { background:#f0fdf4; color:#16a34a; }
.outcome-NOT_INTERESTED { background:#fef2f2; color:#ef4444; }
.outcome-CALLBACK       { background:#fffbeb; color:#d97706; }
.outcome-NO_RESPONSE    { background:var(--bg-3); color:var(--text-2); }

/* Info grid */
.info-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; margin-top:14px; }
.info-cell { background:var(--bg-3); border-radius:8px; padding:10px 14px; }
.info-cell-label { font-size:10px; font-weight:700; letter-spacing:.5px; color:var(--text-2); text-transform:uppercase; }
.info-cell-value { font-size:13px; font-weight:600; color:var(--text); margin-top:3px; }
</style>
@endpush

@section('content')

<div>

  {{-- Back --}}
  <a href="{{ route('institute.enquiries.index') }}"
     style="font-size:12px;color:var(--text-2);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:18px;">
    ← All Enquiries
  </a>

  @if(session('success'))
    <div class="gt-alert gt-alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
  @endif

  {{-- Header --}}
  <div class="gt-card" style="padding:22px 26px;margin-bottom:18px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">

      <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
          <h2 style="font-size:22px;font-weight:800;margin:0;">{{ $enquiry->name }}</h2>

          @if($enquiry->status === 'OPEN')
            @if($enquiry->isOverdue())
              <span style="padding:3px 12px;border-radius:20px;background:#fef2f2;color:#ef4444;font-size:11px;font-weight:700;">⚠ Follow-up Overdue</span>
            @elseif($enquiry->isDueToday())
              <span style="padding:3px 12px;border-radius:20px;background:#fffbeb;color:#d97706;font-size:11px;font-weight:700;">● Due Today</span>
            @else
              <span style="padding:3px 12px;border-radius:20px;background:var(--accent-bg);color:var(--accent);font-size:11px;font-weight:700;">OPEN</span>
            @endif
          @elseif($enquiry->status === 'CONVERTED')
            <span style="padding:3px 12px;border-radius:20px;background:#f0fdf4;color:#16a34a;font-size:11px;font-weight:700;">✓ Converted to Admission</span>
          @else
            <span style="padding:3px 12px;border-radius:20px;background:var(--bg-3);color:var(--text-2);font-size:11px;font-weight:700;">Closed — Lost</span>
          @endif
        </div>

        <div class="info-grid">
          <div class="info-cell">
            <div class="info-cell-label">Mobile</div>
            <div class="info-cell-value">{{ $enquiry->mobile }}</div>
          </div>
          @if($enquiry->email)
          <div class="info-cell">
            <div class="info-cell-label">Email</div>
            <div class="info-cell-value" style="font-size:12px;">{{ $enquiry->email }}</div>
          </div>
          @endif
          <div class="info-cell">
            <div class="info-cell-label">Course Interest</div>
            <div class="info-cell-value">{{ $enquiry->course?->name ?? '—' }}</div>
          </div>
          <div class="info-cell">
            <div class="info-cell-label">Source</div>
            <div class="info-cell-value">{{ str_replace('_',' ', $enquiry->source) }}</div>
          </div>
          <div class="info-cell">
            <div class="info-cell-label">Created</div>
            <div class="info-cell-value">{{ $enquiry->created_at->format('d M Y') }}</div>
          </div>
          @if($enquiry->next_followup_date && $enquiry->status === 'OPEN')
          <div class="info-cell" style="border:1.5px solid {{ $enquiry->isOverdue() ? '#ef4444' : ($enquiry->isDueToday() ? '#f59e0b' : 'var(--border)') }};">
            <div class="info-cell-label">Next Follow-up</div>
            <div class="info-cell-value" style="color:{{ $enquiry->isOverdue() ? '#ef4444' : 'var(--text)' }};">
              {{ $enquiry->next_followup_date->format('d M Y') }}
            </div>
          </div>
          @endif
          <div class="info-cell">
            <div class="info-cell-label">Follow-ups</div>
            <div class="info-cell-value">{{ $enquiry->followups->count() }} logged</div>
          </div>
        </div>

        @if($enquiry->status === 'LOST' && $enquiry->lost_reason)
          <div style="margin-top:12px;padding:8px 14px;background:#fef2f2;border-radius:8px;font-size:13px;color:#ef4444;">
            <strong>Lost reason:</strong> {{ $enquiry->lost_reason }}
          </div>
        @endif
      </div>

      {{-- Actions --}}
      @if($enquiry->status === 'OPEN')
        <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
          <a href="{{ route('institute.enquiries.convert', $enquiry) }}" class="btn btn-primary">
            → Convert to Admission
          </a>
          <button onclick="document.getElementById('lost-modal').style.display='flex'"
                  class="btn btn-secondary" style="text-align:center;">
            Mark as Lost
          </button>
        </div>
      @endif
    </div>
  </div>

  {{-- Body --}}
  <div style="display:grid;grid-template-columns:1fr 320px;gap:18px;align-items:start;">

    {{-- Timeline --}}
    <div class="gt-card" style="padding:22px;">
      <div style="font-size:12px;font-weight:700;letter-spacing:.6px;color:var(--text-2);margin-bottom:18px;">
        ACTIVITY TIMELINE
        <span style="font-weight:400;margin-left:8px;">{{ $enquiry->followups->count() + 1 }} entries</span>
      </div>

      <div class="tl-wrap">
        {{-- Created --}}
        <div class="tl-item">
          <div class="tl-dot grey" style="font-size:8px;">✦</div>
          <div class="tl-card">
            <div class="tl-meta">{{ $enquiry->created_at->format('d M Y, h:i A') }} — Enquiry Created</div>
            @if($enquiry->notes)
              <div class="tl-note">{{ $enquiry->notes }}</div>
            @else
              <div class="tl-note" style="color:var(--text-2);font-style:italic;">No notes at creation.</div>
            @endif
          </div>
        </div>

        {{-- Follow-ups (chronological) --}}
        @foreach($enquiry->followups->sortBy('created_at') as $fu)
          <div class="tl-item">
            <div class="tl-dot">{{ $loop->iteration }}</div>
            <div class="tl-card">
              <div class="tl-meta">
                {{ $fu->created_at->format('d M Y, h:i A') }}
                @if($fu->staff) · {{ $fu->staff->profile?->name ?? $fu->staff->user_id }} @endif
                · <span style="font-weight:600;">{{ $fu->created_at->diffForHumans() }}</span>
              </div>
              <div class="tl-note">{{ $fu->notes }}</div>
              <span class="outcome-chip outcome-{{ $fu->outcome }}">
                {{ str_replace('_',' ', $fu->outcome) }}
              </span>
              @if($fu->next_followup_date)
                <div style="font-size:11px;color:var(--text-2);margin-top:7px;">
                  Next follow-up set: <strong>{{ $fu->next_followup_date->format('d M Y') }}</strong>
                </div>
              @endif
            </div>
          </div>
        @endforeach

        {{-- Converted --}}
        @if($enquiry->status === 'CONVERTED')
          <div class="tl-item">
            <div class="tl-dot green">✓</div>
            <div class="tl-card" style="border-color:#bbf7d0;background:#f0fdf4;">
              <div class="tl-meta" style="color:#16a34a;">
                {{ $enquiry->updated_at->format('d M Y') }} — Converted to Admission
              </div>
            </div>
          </div>
        @endif

        {{-- Lost --}}
        @if($enquiry->status === 'LOST')
          <div class="tl-item">
            <div class="tl-dot red">✕</div>
            <div class="tl-card" style="border-color:#fecaca;background:#fef2f2;">
              <div class="tl-meta" style="color:#ef4444;">
                {{ $enquiry->updated_at->format('d M Y') }} — Marked as Lost
              </div>
              @if($enquiry->lost_reason)
                <div class="tl-note" style="color:#ef4444;">{{ $enquiry->lost_reason }}</div>
              @endif
            </div>
          </div>
        @endif
      </div>
    </div>

    {{-- Follow-up form --}}
    @if($enquiry->status === 'OPEN')
      <div class="gt-card" style="padding:20px;position:sticky;top:80px;">
        <div style="font-size:14px;font-weight:700;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border);">
          Log Follow-up
        </div>

        <form method="POST" action="{{ route('institute.enquiries.followup', $enquiry) }}">
          @csrf

          <div class="gt-form-row" style="margin-bottom:14px;">
            <label class="gt-label">Notes <span class="gt-required">*</span></label>
            <textarea name="notes" class="gt-input @error('notes') is-invalid @enderror"
                      rows="4" placeholder="What was discussed? What was the student's response?"
                      required>{{ old('notes') }}</textarea>
            @error('notes')<div class="gt-error">{{ $message }}</div>@enderror
          </div>

          <div class="gt-form-row" style="margin-bottom:14px;">
            <label class="gt-label">Outcome <span class="gt-required">*</span></label>
            <select name="outcome" class="gt-input" required>
              <option value="INTERESTED"     {{ old('outcome')==='INTERESTED'     ? 'selected':'' }}>Interested</option>
              <option value="CALLBACK"       {{ old('outcome','CALLBACK')==='CALLBACK'?'selected':'' }}>Callback Later</option>
              <option value="NO_RESPONSE"    {{ old('outcome')==='NO_RESPONSE'    ? 'selected':'' }}>No Response</option>
              <option value="NOT_INTERESTED" {{ old('outcome')==='NOT_INTERESTED' ? 'selected':'' }}>Not Interested</option>
            </select>
          </div>

          <div class="gt-form-row" style="margin-bottom:18px;">
            <label class="gt-label">Next Follow-up Date</label>
            <input type="date" name="next_followup_date" class="gt-input"
                   value="{{ old('next_followup_date', now()->addDay()->format('Y-m-d')) }}">
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%;">Save Follow-up</button>
        </form>
      </div>
    @elseif($enquiry->status === 'CONVERTED')
      <div class="gt-card" style="padding:20px;text-align:center;">
        <div style="font-size:32px;margin-bottom:8px;">✓</div>
        <div style="font-size:14px;font-weight:700;color:#16a34a;margin-bottom:6px;">Converted</div>
        <div style="font-size:12px;color:var(--text-2);">This enquiry was successfully converted to an admission.</div>
        <a href="{{ route('institute.enrollment.pending') }}" class="btn btn-secondary btn-sm" style="margin-top:14px;display:inline-block;">
          View Pending Admissions →
        </a>
      </div>
    @else
      <div class="gt-card" style="padding:20px;text-align:center;">
        <div style="font-size:32px;margin-bottom:8px;">✕</div>
        <div style="font-size:14px;font-weight:700;color:var(--text-2);margin-bottom:6px;">Closed</div>
        <div style="font-size:12px;color:var(--text-2);">This enquiry was marked as lost.</div>
      </div>
    @endif
  </div>
</div>

{{-- Lost modal --}}
<div id="lost-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
  <div style="background:var(--bg);border-radius:14px;padding:28px;width:420px;max-width:90vw;box-shadow:0 24px 48px rgba(0,0,0,.2);">
    <div style="font-size:17px;font-weight:800;margin-bottom:6px;">Mark Enquiry as Lost</div>
    <div style="font-size:13px;color:var(--text-2);margin-bottom:20px;">This will close the enquiry and remove it from the active pipeline.</div>
    <form method="POST" action="{{ route('institute.enquiries.mark-lost', $enquiry) }}">
      @csrf @method('PATCH')
      <div class="gt-form-row" style="margin-bottom:20px;">
        <label class="gt-label">Reason <span style="color:var(--text-2);font-weight:400;">(optional)</span></label>
        <select name="lost_reason" class="gt-input">
          <option value="">-- Select a reason --</option>
          <option>Joined a competitor institute</option>
          <option>Fee was too high</option>
          <option>Student shifted to another city</option>
          <option>Not interested anymore</option>
          <option>No response after multiple follow-ups</option>
          <option>Other</option>
        </select>
      </div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-secondary">Confirm Lost</button>
        <button type="button" onclick="document.getElementById('lost-modal').style.display='none'" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>

@endsection
