@extends('layouts.institute')
@section('title', 'Channel Partners')
@section('page-title', 'Channel Partners')
@section('topbar-actions')
  <a href="{{ route('institute.channel-partners.create') }}" class="btn btn-primary btn-sm">+ Add Channel Partner</a>
@endsection

@section('content')
<style>
  .cp-toolbar {
    margin-bottom: 16px;
  }
  .cp-column-picker {
    position: relative;
    display: inline-flex;
  }
  .cp-column-panel {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 220px;
    padding: 14px;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: var(--card);
    box-shadow: var(--shadow);
    display: none;
    z-index: 25;
  }
  .cp-column-panel.open {
    display: block;
  }
  .cp-column-grid {
    display: grid;
    gap: 10px;
  }
  .cp-column-heading {
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 10px;
  }
  .cp-column-option {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text);
  }
  .cp-column-option input {
    accent-color: var(--accent);
  }
  .cp-filter-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(220px, .9fr) auto;
    gap: 14px;
    align-items: end;
  }
  .cp-filter-field {
    display: grid;
    gap: 8px;
  }
  .cp-filter-actions {
    display: flex;
    align-items: end;
    gap: 8px;
  }
  .cp-toolbar-bottom {
    display: flex;
    justify-content: flex-end;
    margin-top: 12px;
  }
  .cp-primary {
    font-weight: 600;
    color: var(--text);
  }
  .cp-secondary {
    font-size: 12px;
    color: var(--text-2);
    line-height: 1.5;
  }
  .cp-muted {
    font-size: 12px;
    color: var(--text-2);
  }
  .cp-note-trigger {
    border: 1px solid var(--border);
    background: transparent;
    color: var(--text);
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 12px;
    cursor: pointer;
    transition: background var(--transition), border-color var(--transition);
  }
  .cp-note-trigger:hover {
    background: var(--bg-3);
    border-color: var(--accent);
  }
  .cp-table-shell {
    border: 1px solid var(--border);
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
  }
  .cp-table-head th {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--text-3);
    background: linear-gradient(180deg, #fbfcff 0%, #f5f7fb 100%);
  }
  .cp-table-row td {
    padding-top: 16px;
    padding-bottom: 16px;
    vertical-align: middle;
  }
  .cp-table-row td:first-child {
    min-width: 230px;
  }
  .cp-table-row td:nth-child(2) {
    min-width: 150px;
  }
  .cp-table-row td:nth-child(3) {
    min-width: 220px;
  }
  .cp-table-row td:nth-child(4) {
    min-width: 180px;
  }
  .cp-subline {
    margin-top: 4px;
    font-size: 12px;
    color: var(--text-2);
  }
  .cp-name-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(108,93,211,.14), rgba(138,115,245,.12));
    color: var(--accent);
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
  }
  .cp-name-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .cp-modal {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.52);
    z-index: 300;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
  }
  .cp-modal.open {
    display: flex;
  }
  .cp-modal-card {
    width: min(560px, 100%);
    max-height: 80vh;
    overflow: auto;
  }
  .cp-note-body {
    white-space: pre-wrap;
    line-height: 1.7;
    font-size: 14px;
    color: var(--text);
  }
  .cp-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px 20px;
  }
  .cp-detail-item {
    display: grid;
    gap: 4px;
  }
  .cp-detail-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--text-3);
  }
  .cp-detail-value {
    font-size: 14px;
    color: var(--text);
    line-height: 1.6;
    white-space: pre-wrap;
  }
  .cp-table-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 14px;
    flex-wrap: wrap;
  }
  .cp-table-intro {
    font-size: 12px;
    color: var(--text-2);
  }
  .cp-status-form {
    margin: 0;
  }
  .cp-status-switch {
    position: relative;
    display: inline-flex;
    width: 52px;
    height: 30px;
    border: none;
    background: transparent;
    padding: 0;
    cursor: pointer;
  }
  .cp-status-switch-track {
    width: 52px;
    height: 30px;
    border-radius: 999px;
    background: #dbe4f0;
    transition: all .2s ease;
    box-shadow: inset 0 0 0 1px rgba(15,23,42,.06);
  }
  .cp-status-switch-thumb {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 2px 8px rgba(15,23,42,.15);
    transition: all .2s ease;
  }
  .cp-status-switch.is-active .cp-status-switch-track {
    background: rgb(108 93 211);
  }
  .cp-status-switch.is-active .cp-status-switch-thumb {
    left: 25px;
  }
  .cp-status-text {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-2);
    margin-top: 6px;
  }
  .cp-action-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
  .cp-pagination-wrap {
    margin-top: 14px;
  }
  @media (max-width: 768px) {
    .cp-filter-grid {
      grid-template-columns: 1fr;
    }
    .cp-filter-actions {
      align-items: center;
    }
    .cp-toolbar-bottom {
      justify-content: flex-start;
    }
    .cp-detail-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Channel Partners ({{ $partners->total() }})</div>
    <div class="text-xs text-muted">Monitor channel partner records, contact details, and admission performance.</div>
  </div>
  <div class="cp-toolbar">
    <form method="GET" action="{{ route('institute.channel-partners.index') }}">
      <div class="cp-filter-grid">
        <div class="cp-filter-field">
          <label class="gt-label">Search</label>
          <input
            type="text"
            name="search"
            value="{{ $search }}"
            class="gt-input"
            placeholder="Search by name, mobile, email, city, district, or state"
          >
        </div>
        <div class="cp-filter-field">
          <label class="gt-label">Status</label>
          <select name="status" class="gt-select">
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Partners</option>
            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
          </select>
        </div>
        <div class="cp-filter-actions">
          <button type="submit" class="btn btn-primary btn-sm">Apply</button>
          @if($search !== '' || $status !== 'all')
            <a href="{{ route('institute.channel-partners.index') }}" class="btn btn-outline btn-sm">Reset</a>
          @endif
        </div>
      </div>
      <div class="cp-toolbar-bottom">
        <div class="cp-column-picker">
          <button type="button" class="btn btn-outline btn-sm" id="cp-column-toggle">Manage Columns</button>
          <div class="cp-column-panel" id="cp-column-panel">
            <div class="cp-column-heading">Column Visibility</div>
            <div class="cp-column-grid">
              <label class="cp-column-option"><input type="checkbox" data-column-toggle="name" checked> Name</label>
              <label class="cp-column-option"><input type="checkbox" data-column-toggle="mobile" checked> Mobile</label>
              <label class="cp-column-option"><input type="checkbox" data-column-toggle="contact" checked> Contact</label>
              <label class="cp-column-option"><input type="checkbox" data-column-toggle="location" checked> Location</label>
              <label class="cp-column-option"><input type="checkbox" data-column-toggle="status" checked> Status</label>
              <label class="cp-column-option"><input type="checkbox" data-column-toggle="admissions" checked> Admissions</label>
              <label class="cp-column-option"><input type="checkbox" data-column-toggle="action" checked> Action</label>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="cp-table-shell">
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead class="cp-table-head">
        <tr>
          <th data-column="name">Name</th>
          <th data-column="mobile">Mobile</th>
          <th data-column="contact">Contact</th>
          <th data-column="location">Location</th>
          <th data-column="status">Status</th>
          <th data-column="admissions">Admissions</th>
          <th data-column="action">Action</th>
        </tr>
        </thead>
        <tbody>
        @forelse($partners as $partner)
          <tr class="cp-table-row">
            <td data-column="name">
              <div class="cp-name-wrap">
                <div class="cp-name-badge">{{ strtoupper(substr($partner->name, 0, 1)) }}</div>
                <div>
                  <div class="cp-primary">{{ $partner->name }}</div>
                  <div class="cp-subline">Created {{ $partner->created_at?->format('d M Y') ?? 'NA' }}</div>
                </div>
              </div>
            </td>
            <td data-column="mobile" class="mono text-sm">
              <div class="cp-primary">{{ $partner->mobile ?: 'NA' }}</div>
              @if($partner->alternate_mobile)
                <div class="cp-subline">Alt: {{ $partner->alternate_mobile }}</div>
              @endif
            </td>
            <td data-column="contact">
              <div class="cp-primary">{{ $partner->email ?: 'Not available' }}</div>
              @if($partner->whatsapp_no)
                <div class="cp-subline">WhatsApp: {{ $partner->whatsapp_no }}</div>
              @endif
            </td>
            <td data-column="location">
              <div class="cp-primary">
                {{ $partner->city ?: ($partner->district ?: 'Not available') }}
              </div>
              <div class="cp-subline">{{ $partner->district ?: 'NA' }}{{ $partner->state ? ', '.$partner->state : '' }}</div>
            </td>
            <td data-column="status">
              <form
                method="POST"
                action="{{ route('institute.channel-partners.toggle', $partner) }}"
                class="cp-status-form"
                data-partner-toggle
                data-partner-name="{{ $partner->name }}"
                data-next-status="{{ ($partner->status ?? 'active') === 'active' ? 'inactive' : 'active' }}"
              >
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="page" value="{{ $partners->currentPage() }}">
                <button type="submit" class="cp-status-switch {{ ($partner->status ?? 'active') === 'active' ? 'is-active' : '' }}" aria-label="Toggle status for {{ $partner->name }}">
                  <span class="cp-status-switch-track"></span>
                  <span class="cp-status-switch-thumb"></span>
                </button>
                <div class="cp-status-text">{{ ucfirst($partner->status ?? 'active') }}</div>
              </form>
            </td>
            <td data-column="admissions"><span class="badge badge-accent">{{ $partner->admissions_count }}</span></td>
            <td data-column="action">
              <div class="cp-action-group">
                <button
                  type="button"
                  class="cp-note-trigger"
                  data-partner-view
                  data-name="{{ $partner->name }}"
                  data-mobile="{{ $partner->mobile ?: 'NA' }}"
                  data-alternate-mobile="{{ $partner->alternate_mobile ?: 'NA' }}"
                  data-email="{{ $partner->email ?: 'Not available' }}"
                  data-whatsapp="{{ $partner->whatsapp_no ?: 'NA' }}"
                  data-father-name="{{ $partner->father_name ?: 'Not available' }}"
                  data-gender="{{ $partner->gender ?: 'NA' }}"
                  data-dob="{{ $partner->dob ? $partner->dob->format('d M Y') : 'NA' }}"
                  data-occupation="{{ $partner->occupation ?: 'NA' }}"
                  data-aadhaar="{{ $partner->aadhar_no ?: 'NA' }}"
                  data-pan="{{ $partner->pan_no ?: 'NA' }}"
                  data-address="{{ $partner->address ?: 'Not available' }}"
                  data-city="{{ $partner->city ?: 'NA' }}"
                  data-district="{{ $partner->district ?: 'NA' }}"
                  data-state="{{ $partner->state ?: 'NA' }}"
                  data-pin-code="{{ $partner->pin_code ?: 'NA' }}"
                  data-status="{{ ucfirst($partner->status ?? 'active') }}"
                  data-admissions="{{ $partner->admissions_count }}"
                  data-notes="{{ e($partner->notes ?: 'No notes available.') }}"
                  data-created="{{ $partner->created_at?->format('d M Y') ?? 'NA' }}"
                >
                  View
                </button>
                <a href="{{ route('institute.channel-partners.edit', $partner) }}" class="btn btn-outline btn-xs">Edit</a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7">
              <div class="gt-empty">
                <div class="gt-empty-title">No channel partners found</div>
                <a href="{{ route('institute.channel-partners.create') }}" class="btn btn-primary btn-sm">Add Channel Partner</a>
              </div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="cp-table-meta">
    <div class="cp-table-intro">Showing {{ $partners->firstItem() ?? 0 }}-{{ $partners->lastItem() ?? 0 }} of {{ $partners->total() }} partners.</div>
    <div class="cp-table-intro">{{ $partners->hasPages() ? 'Use pagination below to browse additional records.' : 'All records are visible on this page.' }}</div>
  </div>
  <div class="gt-pagination cp-pagination-wrap">{{ $partners->links() }}</div>
</div>

<div class="cp-modal" id="cp-note-modal">
  <div class="gt-card cp-modal-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title" id="cp-note-modal-title">Partner Details</div>
        <div class="text-xs text-muted">Complete record for the selected channel partner.</div>
      </div>
      <button type="button" class="btn btn-outline btn-xs" id="cp-note-close">Close</button>
    </div>
    <div class="cp-detail-grid" id="cp-note-modal-body"></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (() => {
    const panel = document.getElementById('cp-column-panel');
    const toggle = document.getElementById('cp-column-toggle');
    const checkboxes = document.querySelectorAll('[data-column-toggle]');
    const noteModal = document.getElementById('cp-note-modal');
    const noteTitle = document.getElementById('cp-note-modal-title');
    const noteBody = document.getElementById('cp-note-modal-body');
    const noteClose = document.getElementById('cp-note-close');

    toggle?.addEventListener('click', (event) => {
      event.stopPropagation();
      panel?.classList.toggle('open');
    });

    document.addEventListener('click', (event) => {
      if (!panel?.contains(event.target) && event.target !== toggle) {
        panel?.classList.remove('open');
      }
    });

    function applyColumnVisibility(column, visible) {
      document.querySelectorAll(`[data-column="${column}"]`).forEach((cell) => {
        cell.style.display = visible ? '' : 'none';
      });
    }

    function escapeHtml(value) {
      return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    checkboxes.forEach((checkbox) => {
      applyColumnVisibility(checkbox.dataset.columnToggle, checkbox.checked);
      checkbox.addEventListener('change', () => {
        applyColumnVisibility(checkbox.dataset.columnToggle, checkbox.checked);
      });
    });

    document.querySelectorAll('[data-partner-view]').forEach((button) => {
      button.addEventListener('click', () => {
        noteTitle.textContent = `${button.dataset.name} - Details`;
        const details = [
          ['Name', button.dataset.name],
          ['Mobile', button.dataset.mobile],
          ['Alternate Mobile', button.dataset.alternateMobile],
          ['Email', button.dataset.email],
          ['WhatsApp', button.dataset.whatsapp],
          ['Father Name', button.dataset.fatherName],
          ['Gender', button.dataset.gender],
          ['Date of Birth', button.dataset.dob],
          ['Occupation', button.dataset.occupation],
          ['Aadhaar Number', button.dataset.aadhaar],
          ['PAN Number', button.dataset.pan],
          ['Address', button.dataset.address],
          ['City', button.dataset.city],
          ['District', button.dataset.district],
          ['State', button.dataset.state],
          ['PIN Code', button.dataset.pinCode],
          ['Status', button.dataset.status],
          ['Admissions', button.dataset.admissions],
          ['Created', button.dataset.created],
          ['Notes', button.dataset.notes],
        ];

        noteBody.innerHTML = details.map(([label, value]) => `
          <div class="cp-detail-item">
            <div class="cp-detail-label">${escapeHtml(label)}</div>
            <div class="cp-detail-value">${escapeHtml(value || 'NA')}</div>
          </div>
        `).join('');
        noteModal?.classList.add('open');
      });
    });

    document.querySelectorAll('[data-partner-toggle]').forEach((form) => {
      form.addEventListener('submit', (event) => {
        const partnerName = form.dataset.partnerName || 'this partner';
        const nextStatus = form.dataset.nextStatus || 'inactive';
        if (!window.confirm(`Do you want to mark ${partnerName} as ${nextStatus}?`)) {
          event.preventDefault();
        }
      });
    });

    function closeNoteModal() {
      noteModal?.classList.remove('open');
    }

    noteClose?.addEventListener('click', closeNoteModal);
    noteModal?.addEventListener('click', (event) => {
      if (event.target === noteModal) {
        closeNoteModal();
      }
    });
  })();
</script>
@endpush
