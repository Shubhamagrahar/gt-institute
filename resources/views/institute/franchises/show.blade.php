@extends('layouts.institute')
@section('title', $franchise->name)
@section('page-title', 'Franchise Details')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.edit', $franchise) }}" class="btn btn-outline btn-sm">Edit</a>
  <a href="{{ route('institute.franchises.transactions', $franchise) }}" class="btn btn-outline btn-sm">View Ledger</a>
  <form method="POST" action="{{ route('institute.franchises.toggle', $franchise) }}" style="display:inline-flex;">
    @csrf
    @method('PATCH')
    <button type="submit" class="btn btn-outline btn-sm">{{ $franchise->status === 'active' ? 'Disable' : 'Enable' }}</button>
  </form>
  <a href="{{ route('institute.franchises.index') }}" class="btn btn-outline btn-sm">Back</a>
@endsection

@section('content')
<div class="gt-stats" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
  <div class="gt-stat">
    <div class="gt-stat-icon green">₹</div>
    <div>
      <div class="gt-stat-value mono">₹{{ number_format($franchise->wallet?->balance ?? 0, 2) }}</div>
      <div class="gt-stat-label">Wallet Balance</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon blue">ID</div>
    <div>
      <div class="gt-stat-value" style="font-size:18px;">{{ $franchise->unique_id }}</div>
      <div class="gt-stat-label">Franchise ID</div>
    </div>
  </div>
  <div class="gt-stat">
    <div class="gt-stat-icon orange">TX</div>
    <div>
      <div class="gt-stat-value">{{ $franchise->transactions->count() }}</div>
      <div class="gt-stat-label">Ledger Entries</div>
    </div>
  </div>
</div>

<div class="gt-grid-2">
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Basic Details</div>
    </div>
    <div class="price-row"><span class="plabel">Name</span><span class="pvalue">{{ $franchise->name }}</span></div>
    <div class="price-row"><span class="plabel">Short Name</span><span class="pvalue">{{ $franchise->short_name ?: 'NA' }}</span></div>
    <div class="price-row"><span class="plabel">Email</span><span class="pvalue">{{ $franchise->email }}</span></div>
    <div class="price-row"><span class="plabel">Mobile</span><span class="pvalue">{{ $franchise->mobile }}</span></div>
    <div class="price-row"><span class="plabel">Website</span><span class="pvalue">{{ $franchise->website ?: 'NA' }}</span></div>
    <div class="price-row"><span class="plabel">Level</span><span class="pvalue">{{ $franchise->level?->name ?? 'NA' }}</span></div>
    <div class="price-row"><span class="plabel">Commission</span><span class="pvalue">{{ number_format($franchise->commission_percent, 2) }}%</span></div>
    <div class="price-row"><span class="plabel">Wallet System</span><span class="pvalue">{{ $franchise->wallet_enabled ? 'Enabled' : 'Disabled' }}</span></div>
    <div class="price-row"><span class="plabel">Low Wallet Alert</span><span class="pvalue">₹{{ number_format($franchise->low_wallet_alert, 2) }}</span></div>
    <div class="price-row"><span class="plabel">Has Own Franchise</span><span class="pvalue">{{ $franchise->has_sub_franchise ? 'Yes' : 'No' }}</span></div>
    <div class="price-row"><span class="plabel">Status</span><span class="pvalue">{{ ucfirst($franchise->status) }}</span></div>
  </div>

  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Head Login</div>
    </div>
    <div class="price-row"><span class="plabel">Owner Name</span><span class="pvalue">{{ $franchise->owner_name }}</span></div>
    <div class="price-row"><span class="plabel">Owner Mobile</span><span class="pvalue">{{ $franchise->owner_mobile }}</span></div>
    <div class="price-row"><span class="plabel">Login ID</span><span class="pvalue">{{ $franchise->head?->user_id ?? 'NA' }}</span></div>
    <div class="price-row"><span class="plabel">Login Email</span><span class="pvalue">{{ $franchise->head?->email ?? 'NA' }}</span></div>
  </div>
</div>

<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Address</div>
  </div>
  <p class="text-sm">{{ $franchise->address ?: 'No address added.' }}</p>
</div>
@endsection
