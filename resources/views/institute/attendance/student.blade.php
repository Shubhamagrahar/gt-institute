@extends('layouts.institute')
@section('title', 'Mark Attendance')
@section('page-title', 'Mark Attendance')

@push('styles')
<style>
/* ════════════════════════════════════════════
   MARK ATTENDANCE — PROFESSIONAL UI
════════════════════════════════════════════ */

/* ── Filter Card ── */
.maf-card {
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 20px;
}
.maf-card-head {
  padding: 14px 22px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 10px;
}
.maf-card-head-icon {
  width: 32px; height: 32px; border-radius: 8px;
  background: var(--accent-bg, #ede9fe);
  display: flex; align-items: center; justify-content: center;
  color: var(--accent);
}
.maf-card-head-icon svg { width: 16px; height: 16px; }
.maf-card-head-title {
  font-size: 13px; font-weight: 800; color: var(--text-1);
}
.maf-card-head-sub {
  font-size: 12px; color: var(--text-2); margin-left: auto;
  font-weight: 500;
}

/* ── 3-column filter grid ── */
.maf-filters {
  display: grid;
  grid-template-columns: 1fr 2fr 2fr;
  gap: 0;
}
.maf-field {
  padding: 18px 22px;
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.maf-field:last-child { border-right: none; }
.maf-label {
  font-size: 11px; font-weight: 800; color: var(--text-2);
  letter-spacing: .6px; text-transform: uppercase;
  display: flex; align-items: center; gap: 6px;
}
.maf-label svg { width: 13px; height: 13px; opacity: .7; }
.maf-select, .maf-date {
  width: 100%;
  padding: 10px 14px;
  border-radius: 10px;
  border: 1.5px solid var(--border);
  background: var(--bg-2);
  color: var(--text-1);
  font-size: 14px;
  font-weight: 600;
  transition: border-color .15s, box-shadow .15s;
  appearance: none;
}
.maf-select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%239ca3af' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 36px;
}
.maf-select:focus, .maf-date:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-bg, rgba(124,58,237,.12));
}
.maf-batch-timing {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: var(--accent);
  min-height: 20px;
}
.maf-batch-timing svg { width: 12px; height: 12px; flex-shrink: 0; }

@media(max-width: 700px) {
  .maf-filters { grid-template-columns: 1fr; }
  .maf-field   { border-right: none; border-bottom: 1px solid var(--border); }
  .maf-field:last-child { border-bottom: none; }
}

/* ── Stats strip ── */
.maf-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 16px;
}
@media(max-width: 580px) { .maf-stats { grid-template-columns: repeat(2,1fr); } }

.maf-stat {
  border-radius: 14px;
  border: 1px solid var(--border);
  background: var(--bg-2);
  padding: 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  position: relative;
  overflow: hidden;
}
.maf-stat::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  border-radius: 14px 14px 0 0;
}
.maf-stat.s-present::before { background: #22c55e; }
.maf-stat.s-absent::before  { background: #ef4444; }
.maf-stat.s-pending::before { background: #f59e0b; }
.maf-stat.s-total::before   { background: var(--accent); }

.maf-stat-val {
  font-size: 32px;
  font-weight: 900;
  line-height: 1;
}
.s-present .maf-stat-val { color: #16a34a; }
.s-absent  .maf-stat-val { color: #dc2626; }
.s-pending .maf-stat-val { color: #d97706; }
.s-total   .maf-stat-val { color: var(--accent); }

.maf-stat-label {
  font-size: 11px;
  font-weight: 700;
  color: var(--text-2);
  letter-spacing: .5px;
  text-transform: uppercase;
}

/* ── Toolbar ── */
.maf-toolbar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 14px;
}
.maf-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 9px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  transition: all .15s;
  white-space: nowrap;
}
.maf-btn svg { width: 15px; height: 15px; flex-shrink: 0; }
.maf-btn-present {
  background: #16a34a;
  color: #fff;
  box-shadow: 0 2px 8px rgba(22,163,74,.3);
}
.maf-btn-present:hover { background: #15803d; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(22,163,74,.35); }
.maf-btn-absent {
  background: #dc2626;
  color: #fff;
  box-shadow: 0 2px 8px rgba(220,38,38,.3);
}
.maf-btn-absent:hover { background: #b91c1c; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(220,38,38,.35); }
.maf-btn:disabled { opacity: .45; cursor: not-allowed; transform: none; box-shadow: none; }

.maf-pending {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 600;
  color: var(--text-2);
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 6px 14px;
}
.maf-pending-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #f59e0b;
  flex-shrink: 0;
  animation: blink 1.4s infinite;
}
.maf-pending.all-done .maf-pending-dot { background: #22c55e; animation: none; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

/* ── Load button (inside filter card) ── */
.maf-load-row {
  padding: 14px 22px;
  border-top: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.maf-load-hint {
  font-size: 12px; color: var(--text-2); font-weight: 500;
  display: flex; align-items: center; gap: 6px;
}
.maf-load-hint svg { width: 13px; height: 13px; opacity: .6; }
.maf-load-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 22px; border-radius: 10px; border: none;
  background: var(--accent); color: #fff;
  font-size: 13px; font-weight: 700; cursor: pointer;
  transition: all .15s;
  box-shadow: 0 2px 8px rgba(108,93,211,.35);
}
.maf-load-btn:hover { opacity: .88; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(108,93,211,.4); }
.maf-load-btn svg { width: 15px; height: 15px; }
.maf-load-btn:disabled { opacity: .45; cursor: not-allowed; transform: none; box-shadow: none; }

/* ── Late inline time input ── */
.maf-late-wrap {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(217,119,6,.1); border: 1.5px solid rgba(217,119,6,.35);
  border-radius: 8px; padding: 4px 10px;
}
.maf-late-wrap svg { width: 12px; height: 12px; color: #d97706; flex-shrink: 0; }
.maf-late-label { font-size: 10px; font-weight: 700; color: #b45309; letter-spacing: .4px; white-space: nowrap; }
.maf-late-input {
  border: none; background: transparent; outline: none;
  font-size: 12px; font-weight: 700; color: #d97706;
  font-family: inherit; width: 96px; cursor: pointer;
}
.maf-late-input::-webkit-calendar-picker-indicator { opacity: .6; cursor: pointer; }
.maf-late-saved {
  font-size: 11px; font-weight: 700; min-width: 14px;
  transition: opacity .3s;
}
.maf-late-saved.saving { color: #d97706; }
.maf-late-saved.done   { color: #16a34a; }

/* ── Search bar ── */
.maf-search-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
  padding: 12px 16px;
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: 12px;
}
.maf-search-bar svg { width: 15px; height: 15px; color: var(--text-2); flex-shrink: 0; }
.maf-search-input {
  flex: 1; border: none; background: transparent;
  color: var(--text-1); font-size: 13px; font-weight: 500;
  outline: none; font-family: inherit;
}
.maf-search-input::placeholder { color: var(--text-2); }
.maf-search-count {
  font-size: 11px; font-weight: 700; color: var(--text-2);
  background: var(--bg-3); border-radius: 20px; padding: 3px 10px;
  white-space: nowrap;
}

/* ── Student List ── */
.maf-list {
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
}

/* Flex-based header — no grid stretch */
.maf-list-head {
  display: flex;
  align-items: center;
  padding: 0 16px 0 20px;
  background: var(--bg-3);
  border-bottom: 1px solid var(--border);
}
.maf-lh-num  { width: 32px; flex-shrink: 0; }
.maf-lh-ava  { width: 44px; flex-shrink: 0; }
.maf-lh-info { flex: 1; min-width: 0; padding: 0 12px; }
.maf-lh-mark { width: 152px; flex-shrink: 0; text-align: right; }
.maf-list-head > div {
  padding: 11px 4px;
  font-size: 10px;
  font-weight: 800;
  color: var(--text-2);
  letter-spacing: .7px;
  text-transform: uppercase;
}

/* Student row — flex */
.maf-row {
  display: flex;
  align-items: center;
  padding: 0 16px 0 20px;
  border-bottom: 1px solid var(--border);
  transition: background .1s;
  position: relative;
  min-height: 64px;
}
.maf-row:last-child { border-bottom: none; }
.maf-row:hover { background: var(--bg-3); }

/* Left color strip */
.maf-row::before {
  content: '';
  position: absolute;
  left: 0; top: 0; bottom: 0;
  width: 3px;
  transition: background .2s;
}
.maf-row.is-P::before { background: #22c55e; }
.maf-row.is-A::before { background: #ef4444; }
.maf-row.is-L::before { background: #f59e0b; }

/* Row columns */
.maf-rc-num  { width: 32px; flex-shrink: 0; font-size: 11px; font-weight: 700; color: var(--text-2); }
.maf-rc-ava  { width: 44px; flex-shrink: 0; }
.maf-rc-info { flex: 1; min-width: 0; padding: 10px 12px; }
.maf-rc-mark { width: 152px; flex-shrink: 0; display: flex; justify-content: flex-end; gap: 6px; }

/* Avatar */
.maf-ava {
  width: 38px; height: 38px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 900; color: #fff;
  overflow: hidden; flex-shrink: 0;
  background: var(--accent);
}
.maf-ava img { width: 100%; height: 100%; object-fit: cover; }

/* Student info */
.maf-info-name   { font-size: 13px; font-weight: 700; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.maf-info-meta   { display: flex; align-items: center; gap: 10px; margin-top: 3px; flex-wrap: wrap; }
.maf-info-enroll {
  font-size: 11px; color: var(--text-2); font-weight: 500;
  display: flex; align-items: center; gap: 3px;
}
.maf-info-enroll svg { width: 10px; height: 10px; flex-shrink: 0; }
.maf-info-time {
  font-size: 11px; font-weight: 600;
  display: flex; align-items: center; gap: 4px;
  color: var(--text-2);
}
.maf-info-time svg { width: 10px; height: 10px; flex-shrink: 0; }
.maf-info-time span { color: var(--accent); font-weight: 700; }

/* Status buttons */
.maf-s-btn {
  width: 42px; height: 36px;
  border-radius: 9px;
  border: 2px solid var(--border);
  background: var(--bg-2);
  color: var(--text-2);
  font-size: 12px;
  font-weight: 800;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all .13s;
  user-select: none;
  flex-shrink: 0;
}
.maf-s-btn:hover { transform: translateY(-2px); }
.maf-s-btn.active-P { background: #16a34a; color: #fff; border-color: #15803d; box-shadow: 0 3px 10px rgba(22,163,74,.35); }
.maf-s-btn.active-A { background: #dc2626; color: #fff; border-color: #b91c1c; box-shadow: 0 3px 10px rgba(220,38,38,.35); }
.maf-s-btn.active-L { background: #d97706; color: #fff; border-color: #b45309; box-shadow: 0 3px 10px rgba(217,119,6,.35); }
.maf-s-btn.saving   { opacity: .4; pointer-events: none; }

/* ── Pagination ── */
.maf-pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 20px;
  border-top: 1px solid var(--border);
  background: var(--bg-3);
  flex-wrap: wrap;
  gap: 10px;
}
.maf-pg-info { font-size: 12px; font-weight: 600; color: var(--text-2); }
.maf-pg-btns { display: flex; gap: 4px; align-items: center; }
.maf-pg-btn {
  min-width: 32px; height: 32px; padding: 0 10px;
  border-radius: 8px; border: 1.5px solid var(--border);
  background: var(--bg-2); color: var(--text-1);
  font-size: 12px; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: .12s;
}
.maf-pg-btn:hover:not(:disabled) { border-color: var(--accent); color: var(--accent); }
.maf-pg-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }
.maf-pg-btn:disabled { opacity: .35; cursor: not-allowed; }

/* ── Empty / Loading / Placeholder states ── */
.maf-state {
  padding: 60px 24px;
  text-align: center;
  color: var(--text-2);
}
.maf-state svg   { display: block; margin: 0 auto 16px; opacity: .2; }
.maf-state h3    { font-size: 15px; font-weight: 700; margin: 0 0 6px; color: var(--text-1); }
.maf-state p     { font-size: 13px; margin: 0; }
.maf-spinner {
  width: 36px; height: 36px;
  border: 3px solid var(--border);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: spin .55s linear infinite;
  margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')

{{-- ══ PAGE HEADER ══════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,var(--accent) 0%,#5b21b6 100%);border-radius:16px;padding:22px 28px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div style="display:flex;align-items:center;gap:16px;">
    <div style="width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
        <polyline points="9 11 12 14 22 4"/>
        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
      </svg>
    </div>
    <div>
      <div style="font-size:18px;font-weight:900;color:#fff;line-height:1.2;">Mark Attendance</div>
      <div style="font-size:12px;color:rgba(255,255,255,.65);margin-top:3px;font-weight:500;">Select course &amp; batch, then load students</div>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border-radius:10px;padding:8px 14px;">
    <svg width="14" height="14" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2" viewBox="0 0 24 24">
      <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
      <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
    <span style="font-size:13px;font-weight:700;color:rgba(255,255,255,.9);">{{ now()->format('D, d M Y') }}</span>
  </div>
</div>

{{-- ══ FILTER CARD ══════════════════════════════════════════ --}}
<div class="maf-card">
  <div class="maf-card-head">
    <div class="maf-card-head-icon">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
    </div>
    <span class="maf-card-head-title">Attendance Setup</span>
    <span class="maf-card-head-sub" id="maf-head-sub">Select date, course &amp; batch</span>
  </div>

  <div class="maf-filters">

    {{-- Date --}}
    <div class="maf-field">
      <div class="maf-label">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Date
      </div>
      <input type="date" id="maf-date" class="maf-date" value="{{ now()->toDateString() }}">
    </div>

    {{-- Course --}}
    <div class="maf-field">
      <div class="maf-label">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
        </svg>
        Course
      </div>
      <select id="maf-course" class="maf-select">
        <option value="">— Select Course —</option>
        @foreach($courses as $c)
          <option value="{{ $c->id }}">
            {{ $c->name }}{{ $c->course_code ? ' ('.$c->course_code.')' : '' }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Batch --}}
    <div class="maf-field">
      <div class="maf-label">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        Batch
      </div>
      <select id="maf-batch" class="maf-select">
        <option value="">— Select Batch —</option>
        @foreach($batches as $b)
          @php
            $st = $b->start_time ? \Carbon\Carbon::parse('2000-01-01 '.$b->start_time)->format('h:i A') : '';
            $et = $b->end_time   ? \Carbon\Carbon::parse('2000-01-01 '.$b->end_time)->format('h:i A')   : '';
          @endphp
          <option value="{{ $b->id }}" data-start="{{ $st }}" data-end="{{ $et }}">
            {{ $b->name }}{{ $st ? ' — '.$st : '' }}
          </option>
        @endforeach
      </select>
      <div class="maf-batch-timing" id="maf-timing-tag" style="display:none">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        <span id="maf-timing-text"></span>
      </div>
    </div>

  </div>

  {{-- Load button row --}}
  <div class="maf-load-row">
    <div class="maf-load-hint">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      Select date, course &amp; batch — your selection is saved and restored on reload
    </div>
    <button class="maf-load-btn" id="maf-load-btn" onclick="handleLoad()">
      <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/>
      </svg>
      Load Attendance
    </button>
  </div>
</div>

{{-- ══ STATS ════════════════════════════════════════════════ --}}
<div class="maf-stats" id="maf-stats" style="display:none">
  <div class="maf-stat s-present">
    <div class="maf-stat-val" id="cnt-present">0</div>
    <div class="maf-stat-label">Present</div>
  </div>
  <div class="maf-stat s-absent">
    <div class="maf-stat-val" id="cnt-absent">0</div>
    <div class="maf-stat-label">Absent</div>
  </div>
  <div class="maf-stat s-pending">
    <div class="maf-stat-val" id="cnt-pending">0</div>
    <div class="maf-stat-label">Pending</div>
  </div>
  <div class="maf-stat s-total">
    <div class="maf-stat-val" id="cnt-total">0</div>
    <div class="maf-stat-label">Total</div>
  </div>
</div>

{{-- ══ TOOLBAR ══════════════════════════════════════════════ --}}
<div class="maf-toolbar" id="maf-toolbar" style="display:none">
  <button class="maf-btn maf-btn-present" id="btn-all-p" onclick="markAll('P')">
    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    Mark All Present
  </button>
  <button class="maf-btn maf-btn-absent" id="btn-all-a" onclick="markAll('A')">
    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    Mark All Absent
  </button>
  <div class="maf-pending" id="maf-pending">
    <span class="maf-pending-dot"></span>
    <span id="maf-pending-text">Loading…</span>
  </div>
</div>

{{-- ══ SEARCH BAR (hidden until list loads) ═════════════════ --}}
<div class="maf-search-bar" id="maf-search-wrap" style="display:none">
  <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
  </svg>
  <input type="text" id="maf-search" class="maf-search-input"
         placeholder="Search by student name or enrollment number…"
         oninput="debouncedSearch()">
  <span class="maf-search-count" id="maf-search-count"></span>
</div>

{{-- ══ STUDENT LIST ══════════════════════════════════════════ --}}
<div id="maf-list-wrap" style="margin-bottom:32px;">
  <div class="maf-state">
    <svg width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
      <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    <h3>Attendance List</h3>
    <p>Select a date, course and batch above, then click Load Attendance.</p>
  </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const R = {
  load      : '{{ route("institute.attendance.students.load") }}',
  setStatus : '{{ route("institute.attendance.students.set-status") }}',
  markAll   : '{{ route("institute.attendance.students.mark-all") }}',
};

const $date    = document.getElementById('maf-date');
const $course  = document.getElementById('maf-course');
const $batch   = document.getElementById('maf-batch');
const $wrap    = document.getElementById('maf-list-wrap');
const $stats   = document.getElementById('maf-stats');
const $toolbar = document.getElementById('maf-toolbar');

// ── Restore selection from URL params on page load ────────────────────────────
(function restoreFromUrl() {
  const p = new URLSearchParams(window.location.search);
  if (p.get('course_id')) $course.value = p.get('course_id');
  if (p.get('date'))      $date.value   = p.get('date');
  if (p.get('batch_id')) {
    $batch.value = p.get('batch_id');
    updateBatchTimingTag(); // show timing tag for restored batch
  }
  // Auto-load if all three were in URL
  if (p.get('course_id') && p.get('batch_id') && p.get('date')) {
    loadAttendance();
  }
})();

// ── Batch timing display ──────────────────────────────────────────────────────
$batch.addEventListener('change', updateBatchTimingTag);

function updateBatchTimingTag() {
  const opt  = $batch.options[$batch.selectedIndex];
  const st   = opt?.dataset?.start ?? '';
  const et   = opt?.dataset?.end   ?? '';
  const tag  = document.getElementById('maf-timing-tag');
  const text = document.getElementById('maf-timing-text');
  if (st && et && $batch.value) {
    text.textContent  = `${st}  →  ${et}`;
    tag.style.display = 'flex';
  } else {
    tag.style.display = 'none';
  }
}

// ── Load button handler ───────────────────────────────────────────────────────
function handleLoad() {
  if (!$course.value || !$batch.value || !$date.value) {
    // Highlight empty fields
    [$course, $batch, $date].forEach(el => {
      if (!el.value) {
        el.style.borderColor = '#ef4444';
        el.addEventListener('change', () => el.style.borderColor = '', { once: true });
      }
    });
    return;
  }
  // Save to URL (survives reload)
  const params = new URLSearchParams({ course_id: $course.value, batch_id: $batch.value, date: $date.value });
  history.replaceState(null, '', `?${params}`);
  loadAttendance();
}

// ── Load ─────────────────────────────────────────────────────────────────────
async function loadAttendance() {
  showLoading();

  // Update card subtitle
  const cName = $course.options[$course.selectedIndex]?.text ?? '';
  const bName = $batch.options[$batch.selectedIndex]?.text  ?? '';
  document.getElementById('maf-head-sub').textContent = cName && bName ? `${cName}  ·  ${bName}` : 'Loading…';

  try {
    const res  = await fetch(R.load, {
      method : 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body   : JSON.stringify({ course_id: $course.value, batch_id: $batch.value, date: $date.value }),
    });
    const data = await res.json();
    if (!data.success) { showEmpty(data.message ?? 'No students found.'); return; }
    renderAll(data);
  } catch {
    showEmpty('Network error. Please try again.');
  }
}

/* ── Pagination + Search State ── */
const PER_PAGE = 15;
let allRecords = [];
let filtered   = [];
let curPage    = 1;

// ── Render all (called after load / markAll) ──────────────────────────────────
function renderAll(data) {
  allRecords = data.records;
  $stats.style.display   = 'grid';
  $toolbar.style.display = 'flex';
  document.getElementById('maf-search-wrap').style.display = 'flex';
  document.getElementById('maf-search').value = '';

  setCounters(data.present, data.absent, data.unmarked, data.total);
  setPending(data.unmarked, data.total);

  filtered = [...allRecords];
  curPage  = 1;
  renderPage();
}

// ── Search (debounced 300ms) ──────────────────────────────────────────────────
let _searchTimer = null;
function debouncedSearch() {
  clearTimeout(_searchTimer);
  _searchTimer = setTimeout(applySearch, 300);
}

function applySearch() {
  const q = document.getElementById('maf-search').value.toLowerCase().trim();
  filtered = q
    ? allRecords.filter(r =>
        r.name.toLowerCase().includes(q) ||
        (r.enrollment_no ?? '').toLowerCase().includes(q)
      )
    : [...allRecords];
  curPage = 1;
  renderPage();
}

// ── Render current page ───────────────────────────────────────────────────────
function renderPage() {
  const total  = filtered.length;
  const pages  = Math.max(1, Math.ceil(total / PER_PAGE));
  curPage      = Math.min(curPage, pages);
  const start  = (curPage - 1) * PER_PAGE;
  const slice  = filtered.slice(start, start + PER_PAGE);

  document.getElementById('maf-search-count').textContent =
    `${total} student${total !== 1 ? 's' : ''}`;

  if (!total) {
    $wrap.innerHTML = `<div class="maf-state">
      <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <h3>No Results Found</h3>
      <p>Try a different search term or clear the filter.</p></div>`;
    return;
  }

  const rows = slice.map((r, idx) => {
    const num   = start + idx + 1;
    const inits = r.name.trim().split(/\s+/).map(w => w[0]).join('').slice(0, 2).toUpperCase();
    const ava   = r.photo ? `<img src="${r.photo}" alt="">` : inits;
    const st    = r.status;
    const isLate = st === 'L';
    const batchTime = (r.in_time && r.out_time) ? `${r.in_time} – ${r.out_time}` : (r.in_time || '');

    return `<div class="maf-row${st ? ' is-'+st : ''}" id="row-${r.id}">
      <div class="maf-rc-num">${num}</div>
      <div class="maf-rc-ava"><div class="maf-ava">${ava}</div></div>
      <div class="maf-rc-info">
        <div class="maf-info-name">${esc(r.name)}</div>
        <div class="maf-info-meta">
          <span class="maf-info-enroll">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            ${esc(r.enrollment_no)}
          </span>
          ${batchTime ? `<span class="maf-info-time" id="bt-${r.id}"${isLate ? ' style="display:none"' : ''}>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>${batchTime}</span>
          </span>` : ''}
          <span id="lw-${r.id}"${!isLate ? ' style="display:none"' : ''}>
            ${lateInputHtml(r.id, r.in_time)}
          </span>
        </div>
      </div>
      <div class="maf-rc-mark" id="btns-${r.id}">
        ${sBtn(r.id,'P',st)}${sBtn(r.id,'A',st)}${sBtn(r.id,'L',st)}
      </div>
    </div>`;
  }).join('');

  // Pagination controls
  const pgBtns = buildPagination(curPage, pages);

  $wrap.innerHTML = `
    <div class="maf-list">
      <div class="maf-list-head">
        <div class="maf-lh-num">#</div>
        <div class="maf-lh-ava"></div>
        <div class="maf-lh-info">Student</div>
        <div class="maf-lh-mark">P &nbsp; A &nbsp; L</div>
      </div>
      ${rows}
      ${pages > 1 ? `<div class="maf-pagination">
        <div class="maf-pg-info">Showing ${start+1}–${Math.min(start+PER_PAGE,total)} of ${total}</div>
        <div class="maf-pg-btns">${pgBtns}</div>
      </div>` : ''}
    </div>`;
}

function sBtn(id, label, cur) {
  return `<div class="maf-s-btn${cur===label?' active-'+label:''}" id="sb-${id}-${label}" onclick="tap(${id},'${label}')">${label}</div>`;
}

function lateInputHtml(id, inTime) {
  const val = inTime ? inTime.slice(0,5) : '';
  return `<span class="maf-late-wrap">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    <span class="maf-late-label">LATE IN</span>
    <input type="time" class="maf-late-input" id="lt-${id}" value="${val}"
           onchange="saveTime(${id}, this.value)">
    <span class="maf-late-saved" id="ls-${id}"></span>
  </span>`;
}

function buildPagination(cur, total) {
  let btns = '';
  // Prev
  btns += `<button class="maf-pg-btn" onclick="goPage(${cur-1})" ${cur<=1?'disabled':''}>&#8592;</button>`;
  // Page numbers — show max 5 around current
  const start = Math.max(1, cur - 2);
  const end   = Math.min(total, start + 4);
  if (start > 1) btns += `<button class="maf-pg-btn" onclick="goPage(1)">1</button>${start>2?'<span style="padding:0 4px;color:var(--text-2)">…</span>':''}`;
  for (let p = start; p <= end; p++) {
    btns += `<button class="maf-pg-btn${p===cur?' active':''}" onclick="goPage(${p})">${p}</button>`;
  }
  if (end < total) btns += `${end<total-1?'<span style="padding:0 4px;color:var(--text-2)">…</span>':''}<button class="maf-pg-btn" onclick="goPage(${total})">${total}</button>`;
  // Next
  btns += `<button class="maf-pg-btn" onclick="goPage(${cur+1})" ${cur>=total?'disabled':''}>&#8594;</button>`;
  return btns;
}

function goPage(p) {
  curPage = p;
  renderPage();
  $wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Tap a status button ───────────────────────────────────────────────────────
async function tap(id, label) {
  const wrap  = document.getElementById(`btns-${id}`);
  const curEl = wrap?.querySelector('[class*="active-"]');
  const cur   = curEl ? curEl.id.split('-').pop() : null;
  const next  = cur === label ? null : label;

  // Optimistic: update DOM + allRecords
  paintRow(id, next);
  const rec = allRecords.find(r => r.id === id);
  if (rec) rec.status = next;
  recountAll();

  wrap?.querySelectorAll('.maf-s-btn').forEach(b => b.classList.add('saving'));

  try {
    const body = { id, status: next };
    // When marking Late, send current in_time (so it's preserved)
    if (next === 'L') {
      const lt = document.getElementById(`lt-${id}`);
      if (lt?.value) body.in_time = lt.value;
    }
    const res  = await fetch(R.setStatus, {
      method : 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body   : JSON.stringify(body),
    });
    if (!(await res.json()).success) throw new Error();
  } catch {
    paintRow(id, cur);
    if (rec) rec.status = cur;
    recountAll();
  } finally {
    wrap?.querySelectorAll('.maf-s-btn').forEach(b => b.classList.remove('saving'));
  }
}

// ── Save late in_time (debounced, with visual feedback) ───────────────────────
let _timeTimers = {};
function saveTime(id, timeVal) {
  const saved = document.getElementById(`ls-${id}`);
  if (saved) { saved.textContent = '…'; saved.className = 'maf-late-saved saving'; }

  clearTimeout(_timeTimers[id]);
  _timeTimers[id] = setTimeout(async () => {
    const rec = allRecords.find(r => r.id === id);
    if (rec) rec.in_time = timeVal;
    try {
      const res = await fetch(R.setStatus, {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body   : JSON.stringify({ id, status: 'L', in_time: timeVal }),
      });
      if ((await res.json()).success) {
        if (saved) {
          saved.textContent = '✓';
          saved.className = 'maf-late-saved done';
          setTimeout(() => { if (saved) { saved.textContent = ''; saved.className = 'maf-late-saved'; } }, 2000);
        }
      }
    } catch {
      if (saved) { saved.textContent = '!'; saved.className = 'maf-late-saved'; }
    }
  }, 600);
}

function paintRow(id, status) {
  const row = document.getElementById(`row-${id}`);
  if (row) row.className = `maf-row${status ? ' is-'+status : ''}`;

  ['P','A','L'].forEach(s => {
    const btn = document.getElementById(`sb-${id}-${s}`);
    if (btn) btn.className = `maf-s-btn${s === status ? ' active-'+s : ''}`;
  });

  // Show/hide late time input
  const lw = document.getElementById(`lw-${id}`);  // late-wrap container
  const bt = document.getElementById(`bt-${id}`);  // batch-time span
  if (lw) lw.style.display = status === 'L' ? 'inline' : 'none';
  if (bt) bt.style.display = status === 'L' ? 'none'   : '';

  // If switching to L and no time set yet, populate from batch start time
  if (status === 'L') {
    const lt = document.getElementById(`lt-${id}`);
    if (lt && !lt.value) {
      const rec = allRecords.find(r => r.id === id);
      if (rec?.in_time) lt.value = rec.in_time.slice(0, 5);
    }
    // If late wrap doesn't exist yet (first render), inject it
    if (!lw) {
      const metaEl = row?.querySelector('.maf-info-meta');
      if (metaEl) {
        const rec = allRecords.find(r => r.id === id);
        const span = document.createElement('span');
        span.id = `lw-${id}`;
        span.innerHTML = lateInputHtml(id, rec?.in_time);
        metaEl.appendChild(span);
      }
    }
  }
}

// ── Mark All ──────────────────────────────────────────────────────────────────
async function markAll(status) {
  document.getElementById('btn-all-p').disabled = true;
  document.getElementById('btn-all-a').disabled = true;

  try {
    const res  = await fetch(R.markAll, {
      method : 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body   : JSON.stringify({ course_id: $course.value, batch_id: $batch.value, date: $date.value, status }),
    });
    const data = await res.json();
    if (data.success) renderAll(data);
  } finally {
    document.getElementById('btn-all-p').disabled = false;
    document.getElementById('btn-all-a').disabled = false;
  }
}

// ── Counter helpers ───────────────────────────────────────────────────────────
function recountAll() {
  // Count from allRecords array (source of truth, not DOM — pagination means not all rows visible)
  let p = 0, a = 0, l = 0, u = 0;
  allRecords.forEach(r => {
    if      (r.status === 'P') p++;
    else if (r.status === 'A') a++;
    else if (r.status === 'L') l++;
    else                        u++;
  });
  setCounters(p, a, u, p+a+l+u);
  setPending(u, p+a+l+u);
}

function setCounters(p, a, u, t) {
  document.getElementById('cnt-present').textContent = p;
  document.getElementById('cnt-absent').textContent  = a;
  document.getElementById('cnt-pending').textContent = u;
  document.getElementById('cnt-total').textContent   = t;
}

function setPending(u, t) {
  const el   = document.getElementById('maf-pending');
  const text = document.getElementById('maf-pending-text');
  if (u === 0) {
    el.classList.add('all-done');
    text.textContent = 'All students marked';
  } else {
    el.classList.remove('all-done');
    text.textContent = `${u} of ${t} pending`;
  }
}

// ── UI states ─────────────────────────────────────────────────────────────────
function showLoading() {
  $stats.style.display   = 'none';
  $toolbar.style.display = 'none';
  document.getElementById('maf-search-wrap').style.display = 'none';
  $wrap.innerHTML = `<div class="maf-state"><div class="maf-spinner"></div><h3>Loading…</h3><p>Fetching student list, please wait.</p></div>`;
}

function showEmpty(msg) {
  $stats.style.display   = 'none';
  $toolbar.style.display = 'none';
  document.getElementById('maf-search-wrap').style.display = 'none';
  $wrap.innerHTML = `<div class="maf-state">
    <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <h3>No Students Found</h3>
    <p>${msg}</p></div>`;
}

function esc(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
@endpush
