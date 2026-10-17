@extends('layouts.dashboard')
@section('title','Customer Dashboard')
@section('role-label','Customer Portal')
@section('page-title','Dashboard')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('customer.dashboard') }}" class="active"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('search') }}"><i class="fas fa-search"></i>Search Services</a></div>
<div class="nav-item"><a href="{{ route('customer.bookings') }}"><i class="fas fa-calendar-check"></i>My Bookings</a></div>
<div class="nav-item"><a href="{{ route('customer.split.booking') }}"><i class="fas fa-layer-group"></i>Split Booking</a></div>
<div class="nav-section-title">Tools</div>
<div class="nav-item"><a href="{{ route('customer.reminders') }}"><i class="fas fa-bell"></i>Reminders</a></div>
<div class="nav-item"><a href="javascript:void(0)" onclick="toggleFloatingChat()"><i class="fas fa-robot"></i>AI Assistant</a></div>
<div class="nav-section-title">Account</div>
<div class="nav-item"><a href="{{ route('customer.profile') }}"><i class="fas fa-user-circle"></i>My Profile</a></div>
<div class="nav-item">
  <form method="POST" action="{{ route('logout') }}" id="sidebarCustomerNavLogoutForm" style="display:none;">
    @csrf
  </form>
  <a href="javascript:void(0)" onclick="document.getElementById('sidebarCustomerNavLogoutForm').submit();" style="color:#EF4444;font-weight:600;">
    <i class="fas fa-sign-out-alt" style="color:#EF4444;"></i>Logout
  </a>
</div>
@endsection
@section('content')
<div class="row g-4">
  {{-- Welcome Banner --}}
  <div class="col-12">
    <div class="welcome-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h4 style="color:#fff;font-weight:800;margin:0;">Good morning, {{ $user->name }}! ☀️</h4>
        <p style="color:rgba(255,255,255,.75);margin:.25rem 0 0;font-size:.88rem;">Welcome to HomeServiceHub — here's your home maintenance overview.</p>
      </div>
      <a href="{{ route('search') }}" class="btn-gold-custom btn" style="border-radius:20px;padding:.5rem 1.4rem;"><i class="fas fa-plus me-1"></i>Book a Service</a>
    </div>
  </div>

  {{-- Active Booking Stepper Card --}}
  @php
    $activeBooking = $bookings->firstWhere('status', 'in_progress') 
        ?? $bookings->firstWhere('status', 'accepted') 
        ?? $bookings->firstWhere('status', 'pending')
        ?? $bookings->first();
  @endphp
  @if($activeBooking)
  <div class="col-12">
    <div class="card-custom p-4" style="background:#FFFFFF;border:1px solid var(--card-border);">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <div class="d-flex align-items-center gap-3">
          <div class="avatar-sm" style="background:var(--brand-cream);color:var(--brand-blue);border:1px solid var(--card-border);font-size:1.1rem;width:44px;height:44px;">
            <i class="{{ $activeBooking->category->icon }}"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2">
              <h5 style="font-weight:800;color:var(--brand-deep-blue);margin:0;">Active Booking: {{ $activeBooking->category->name }}</h5>
              <span class="badge-pill badge-status-{{ $activeBooking->status }}">{{ ucfirst(str_replace('_',' ',$activeBooking->status)) }}</span>
            </div>
            <div style="font-size:.82rem;color:var(--text-muted);margin-top:.2rem;">
              Code: <strong>{{ $activeBooking->booking_code }}</strong> &bull; Scheduled for {{ $activeBooking->booking_date->format('d M Y') }} at {{ $activeBooking->formatted_time }}
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('customer.booking.show', $activeBooking) }}" class="btn-primary-custom btn btn-sm"><i class="fas fa-eye me-1"></i>View Details</a>
        </div>
      </div>

      <!-- 4-Step Stepper -->
      <div class="d-flex align-items-center justify-content-between mt-3 px-3 py-3" style="background:var(--brand-cream);border-radius:12px;border:1px solid var(--card-border);">
        @php
          $isPlaced = true;
          $isAssigned = in_array($activeBooking->status, ['accepted','in_progress','completed']);
          $isInProgress = in_array($activeBooking->status, ['in_progress','completed']);
          $isCompleted = ($activeBooking->status === 'completed');
        @endphp
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:34px;height:34px;border-radius:50%;background:{{ $isPlaced ? 'var(--brand-blue)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;"><i class="fas fa-check"></i></div>
          <div style="font-size:.75rem;font-weight:700;color:var(--brand-deep-blue);margin-top:.3rem;">Booked</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $isAssigned ? 'var(--brand-blue)' : 'var(--card-border)' }};margin:0 5px 15px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:34px;height:34px;border-radius:50%;background:{{ $isAssigned ? 'var(--brand-blue)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;">
            <i class="fas {{ $isAssigned ? 'fa-check' : 'fa-user' }}"></i>
          </div>
          <div style="font-size:.75rem;font-weight:700;color:{{ $isAssigned ? 'var(--brand-deep-blue)' : 'var(--text-muted)' }};margin-top:.3rem;">Pro Assigned</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $isInProgress ? 'var(--brand-blue)' : 'var(--card-border)' }};margin:0 5px 15px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:34px;height:34px;border-radius:50%;background:{{ $isInProgress ? 'var(--brand-blue)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;">
            <i class="fas {{ $isInProgress ? 'fa-tools' : 'fa-circle' }}"></i>
          </div>
          <div style="font-size:.75rem;font-weight:700;color:{{ $isInProgress ? 'var(--brand-deep-blue)' : 'var(--text-muted)' }};margin-top:.3rem;">In Progress</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $isCompleted ? 'var(--success)' : 'var(--card-border)' }};margin:0 5px 15px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:34px;height:34px;border-radius:50%;background:{{ $isCompleted ? 'var(--success)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;">
            <i class="fas fa-flag-checkered"></i>
          </div>
          <div style="font-size:.75rem;font-weight:700;color:{{ $isCompleted ? 'var(--success)' : 'var(--text-muted)' }};margin-top:.3rem;">Completed</div>
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- Stats --}}
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">Total Bookings</div><div class="stat-value">{{ $stats['total'] }}</div></div>
        <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="fas fa-calendar" style="color:var(--brand-blue);"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">Pending</div><div class="stat-value">{{ $stats['pending'] }}</div></div>
        <div class="stat-icon" style="background:rgba(232,165,26,.12);"><i class="fas fa-clock" style="color:var(--gold);"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">Completed</div><div class="stat-value">{{ $stats['completed'] }}</div></div>
        <div class="stat-icon" style="background:rgba(16,185,129,.1);"><i class="fas fa-check-circle" style="color:var(--success);"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">Total Spent</div><div class="stat-value">₹{{ number_format($stats['spent']) }}</div></div>
        <div class="stat-icon" style="background:rgba(23,74,139,.1);"><i class="fas fa-wallet" style="color:var(--brand-deep-blue);"></i></div>
      </div>
    </div>
  </div>

  {{-- Recent Bookings --}}
  <div class="col-lg-8">
    <div class="card-custom">
      <div class="card-header-custom">
        <h6><i class="fas fa-calendar-check me-2" style="color:var(--brand-blue);"></i>Recent Bookings</h6>
        <a href="{{ route('customer.bookings') }}" style="color:var(--brand-blue);font-size:.82rem;font-weight:600;">View All</a>
      </div>
      <div class="card-body-custom p-0">
        @if($bookings->isEmpty())
          <div class="empty-state"><i class="fas fa-calendar-times"></i><p>No bookings yet. <a href="{{ route('search') }}" style="color:var(--brand-blue);font-weight:600;">Book your first service!</a></p></div>
        @else
        <table class="table-custom w-100">
          <thead><tr><th>Service</th><th>Provider</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($bookings as $b)
            <tr>
              <td><div style="font-weight:600;font-size:.88rem;">{{ $b->category->name }}</div><div style="font-size:.75rem;color:var(--text-muted);">{{ $b->booking_code }}</div></td>
              <td><div class="d-flex align-items-center gap-2"><div class="avatar-sm">{{ strtoupper(substr($b->provider->name,0,1)) }}</div><span style="font-size:.85rem;font-weight:500;">{{ $b->provider->name }}</span></div></td>
              <td style="font-size:.85rem;">{{ $b->booking_date->format('d M Y') }}<br><span style="color:var(--text-muted);font-size:.75rem;">{{ $b->formatted_time }}</span></td>
              <td style="font-weight:700;color:var(--brand-blue);">₹{{ number_format($b->total_amount) }}</td>
              <td><span class="badge-pill badge-status-{{ $b->status }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
      </div>
    </div>
  </div>

  {{-- Right Panel --}}
  <div class="col-lg-4 d-flex flex-column gap-4">
    {{-- Maintenance Reminders --}}
    <div class="card-custom">
      <div class="card-header-custom">
        <h6><i class="fas fa-bell me-2" style="color:var(--gold);"></i>Reminders</h6>
        <a href="{{ route('customer.reminders') }}" style="color:var(--brand-blue);font-size:.82rem;font-weight:600;">Manage</a>
      </div>
      <div class="card-body-custom">
        @forelse($reminders as $r)
        <div style="display:flex;align-items:center;gap:.75rem;padding:.65rem 0;border-bottom:1px solid var(--card-border);" class="{{ !$loop->last ? '' : '' }}">
          <div class="stat-icon" style="background:rgba(37,99,235,.08);width:36px;height:36px;border-radius:10px;"><i class="{{ $r->category->icon }}" style="color:var(--brand-blue);font-size:.9rem;"></i></div>
          <div class="flex-grow-1">
            <div style="font-weight:600;font-size:.85rem;">{{ $r->title }}</div>
            <div style="font-size:.75rem;color:{{ $r->next_due_date->diffInDays(now()) <= 7 ? 'var(--danger)' : 'var(--text-muted)' }};">
              Due: {{ $r->next_due_date->format('d M Y') }}
            </div>
          </div>
        </div>
        @empty
        <div class="empty-state" style="padding:1rem;"><i class="fas fa-bell-slash" style="font-size:1.5rem;"></i><p style="font-size:.82rem;">No reminders set</p></div>
        @endforelse
      </div>
    </div>
  </div>{{-- /col-lg-4 right panel --}}

  {{-- Neighbour Trusted Providers (Location-Based) --}}
  <div class="col-12" id="neighbourhoodProvidersSection">
    <div class="card-custom">
      <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <h6 class="mb-0">
            <i class="fas fa-heart me-2" style="color:var(--pink);"></i>Neighbour Trusted Providers
          </h6>
          @if(!$customerLocation['is_missing'])
          <span class="badge" style="background:rgba(37,99,235,.08);color:var(--brand-deep-blue);border:1px solid rgba(37,99,235,.2);border-radius:20px;padding:.3rem .75rem;font-size:.75rem;font-weight:600;">
            <i class="fas fa-map-marker-alt me-1 text-primary"></i>Near {{ $customerLocation['label'] }}
          </span>
          @endif
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          {{-- Radius Selector --}}
          <div class="d-inline-flex align-items-center gap-1">
            <span style="font-size:.75rem;color:var(--text-muted);font-weight:600;"><i class="fas fa-bullseye me-1"></i>Radius:</span>
            <select class="form-select form-select-sm" style="width:auto;display:inline-block;padding:.2rem 1.6rem .2rem .6rem;font-size:.75rem;border-radius:20px;" onchange="changeRadius(this.value)">
              @foreach($radiusOptions as $r)
                <option value="{{ $r }}" {{ (int)$currentRadius === (int)$r ? 'selected' : '' }}>{{ $r }} km</option>
              @endforeach
            </select>
          </div>

          {{-- Browser Location Button --}}
          <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius:20px;padding:.25rem .75rem;font-size:.75rem;font-weight:600;" onclick="detectLocation(this)" title="Detect location via browser permission">
            <i class="fas fa-crosshairs me-1"></i>Detect Location
          </button>

          {{-- Edit Location Button --}}
          <button type="button" class="btn btn-sm" style="border-radius:20px;padding:.25rem .75rem;font-size:.75rem;font-weight:600;background:var(--bg-light);border:1px solid var(--card-border);color:var(--brand-deep-blue);" data-bs-toggle="collapse" data-bs-target="#editLocationCollapse" aria-expanded="false">
            <i class="fas fa-map-pin me-1"></i>Edit Location
          </button>

          <a href="{{ route('search') }}?neighborhood_trusted=1" style="color:var(--brand-blue);font-size:.82rem;font-weight:600;">View All</a>
        </div>
      </div>

      {{-- Collapsible Location Editor --}}
      <div class="collapse {{ $locationStatus === 'missing_location' ? 'show' : '' }}" id="editLocationCollapse">
        <div style="background:var(--brand-cream);border-bottom:1px solid var(--card-border);padding:1.25rem 1.5rem;">
          <form method="POST" action="{{ route('customer.location.update') }}">
            @csrf
            <div class="row g-2 align-items-end">
              <div class="col-md-3">
                <label style="font-size:.75rem;font-weight:700;color:var(--brand-deep-blue);display:block;margin-bottom:.25rem;">Building / Society</label>
                <input type="text" name="building" class="form-control form-control-sm" placeholder="e.g. Shivalik Heights" value="{{ old('building', $customerLocation['building']) }}">
              </div>
              <div class="col-md-3">
                <label style="font-size:.75rem;font-weight:700;color:var(--brand-deep-blue);display:block;margin-bottom:.25rem;">Locality / Neighbourhood</label>
                <input type="text" name="locality" class="form-control form-control-sm" placeholder="e.g. SG Highway, Satellite, Bopal" value="{{ old('locality', $customerLocation['locality']) }}">
              </div>
              <div class="col-md-3">
                <label style="font-size:.75rem;font-weight:700;color:var(--brand-deep-blue);display:block;margin-bottom:.25rem;">City</label>
                <select name="city" class="form-select form-select-sm">
                  <option value="Ahmedabad" {{ ($customerLocation['city'] ?? $userCity) === 'Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                  <option value="Gandhinagar" {{ ($customerLocation['city'] ?? $userCity) === 'Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
                </select>
              </div>
              <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary-custom flex-grow-1" style="padding:.35rem .75rem;font-size:.8rem;">
                  <i class="fas fa-check me-1"></i>Save Location
                </button>
              </div>
              <div class="col-12 mt-1">
                <small class="text-muted" style="font-size:.72rem;">
                  <i class="fas fa-shield-alt me-1 text-primary"></i>Your exact address is kept private and only used to match trusted providers in your immediate vicinity.
                </small>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card-body-custom">
        {{-- State 1: Missing Location Prompt --}}
        @if($locationStatus === 'missing_location')
        <div class="p-4 text-center" style="background:#fff;border-radius:12px;">
          <div style="width:56px;height:56px;border-radius:50%;background:rgba(37,99,235,.1);display:inline-flex;align-items:center;justify-content:center;color:var(--brand-blue);font-size:1.6rem;margin-bottom:.75rem;">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <h6 style="font-weight:800;color:var(--brand-deep-blue);margin-bottom:.35rem;">Set Your Neighbourhood Location</h6>
          <p style="color:var(--text-secondary);font-size:.85rem;max-width:500px;margin:0 auto 1rem;">
            Please enter your address, building, or allow browser location permission above so we can display verified service providers located right near you.
          </p>
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary-custom btn-sm" onclick="detectLocation(this)">
              <i class="fas fa-crosshairs me-1"></i>Allow Location Access
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#editLocationCollapse">
              <i class="fas fa-keyboard me-1"></i>Enter Address Manually
            </button>
          </div>
        </div>

        {{-- State 2: No Matches Found --}}
        @elseif($locationStatus === 'no_matches' || $recommendedProviders->isEmpty())
        <div class="p-4 text-center" style="background:#fff;border-radius:12px;">
          <div style="width:56px;height:56px;border-radius:50%;background:rgba(232,165,26,.15);display:inline-flex;align-items:center;justify-content:center;color:var(--gold);font-size:1.6rem;margin-bottom:.75rem;">
            <i class="fas fa-search-location"></i>
          </div>
          <h6 style="font-weight:800;color:var(--brand-deep-blue);margin-bottom:.35rem;">No trusted providers found near your location yet.</h6>
          <p style="color:var(--text-secondary);font-size:.85rem;max-width:520px;margin:0 auto 1.25rem;">
            We couldn't find verified professionals within <strong>{{ $currentRadius }} km</strong> of <strong>{{ $customerLocation['label'] }}</strong>. Try expanding your search radius or verifying your locality name.
          </p>
          <div class="d-flex justify-content-center gap-2 flex-wrap">
            @if($currentRadius < 15)
              <a href="?radius=15" class="btn btn-primary-custom btn-sm"><i class="fas fa-expand-arrows-alt me-1"></i>Expand Radius to 15 km</a>
            @endif
            @if($currentRadius < 25)
              <a href="?radius=25" class="btn btn-outline-custom btn-sm"><i class="fas fa-expand-arrows-alt me-1"></i>Expand Radius to 25 km</a>
            @endif
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#editLocationCollapse">
              <i class="fas fa-edit me-1"></i>Change Location
            </button>
          </div>
        </div>

        {{-- State 3: Providers Found & Filtered --}}
        @else
        <div class="row g-3">
          @foreach($recommendedProviders as $p)
          <div class="col-md-6 col-xl-3">
            <div class="h-100 d-flex flex-column justify-content-between" style="border:{{ $p->isNeighborhoodTrusted() ? '1.5px solid rgba(236,72,153,.35)' : '1px solid var(--card-border)' }};border-radius:14px;padding:1.2rem;text-align:center;transition:.2s;background:#fff;" onmouseover="this.style.boxShadow='var(--shadow)'" onmouseout="this.style.boxShadow='none'">
              <div>
                {{-- Badges Header --}}
                <div class="d-flex justify-content-between align-items-center gap-1 mb-2 flex-wrap">
                  @if($p->isNeighborhoodTrusted())
                  <span style="background:rgba(236,72,153,.1);color:var(--pink);border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:800;">
                    <i class="fas fa-heart me-1"></i>Neighbour Choice
                  </span>
                  @endif

                  {{-- Match Badge (e.g. Same Building, Serves your area) --}}
                  @if($p->match_type === 'same_building' || (!empty($p->match_badge) && $p->match_badge !== $p->distance_text))
                  <span style="background:rgba(16,185,129,.1);color:var(--success);border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;">
                    <i class="fas {{ $p->match_type === 'same_building' ? 'fa-building' : 'fa-check-circle' }} me-1"></i>{{ $p->match_badge }}
                  </span>
                  @endif

                  {{-- Distance / Proximity Match Badge --}}
                  @if(!empty($p->distance_text))
                  <span style="background:rgba(37,99,235,.1);color:var(--brand-blue);border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;" title="Calculated distance">
                    <i class="fas fa-location-arrow me-1"></i>{{ $p->distance_text }}
                  </span>
                  @endif
                </div>

                <div class="avatar-md mx-auto mb-2">{{ strtoupper(substr($p->name,0,1)) }}</div>
                <div style="font-weight:700;font-size:.9rem;color:var(--brand-deep-blue);">{{ $p->name }}</div>
                <div style="font-size:.78rem;color:var(--text-secondary);">{{ $p->category->name }}</div>
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:.25rem;">
                  <i class="fas fa-map-marker-alt me-1" style="color:var(--brand-blue);"></i>{{ $p->service_area }}
                </div>
                <div class="d-flex justify-content-center align-items-center gap-1 my-2">
                  <i class="fas fa-star" style="font-size:.75rem;color:var(--gold);"></i>
                  <span style="font-weight:700;font-size:.8rem;">{{ $p->average_rating }}</span>
                  <span style="font-size:.72rem;color:var(--text-muted);">({{ $p->completed_jobs }} jobs)</span>
                </div>
              </div>

              <div>
                <div style="font-weight:800;color:var(--brand-blue);font-size:.88rem;margin-bottom:.5rem;">₹{{ number_format($p->hourly_rate) }}/hr</div>
                <a href="{{ route('provider.show',$p) }}" class="btn-primary-custom btn btn-sm w-100" style="justify-content:center;padding:.4rem;">View &amp; Book</a>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
function detectLocation(btn) {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by your browser.');
    return;
  }
  const originalHtml = btn ? btn.innerHTML : '';
  if (btn) btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Detecting...';

  navigator.geolocation.getCurrentPosition(
    function(position) {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;
      const url = new URL(window.location.href);
      url.searchParams.set('lat', lat);
      url.searchParams.set('lng', lng);
      window.location.href = url.toString();
    },
    function(error) {
      if (btn) btn.innerHTML = originalHtml;
      alert('Unable to retrieve location permission. You can enter your neighbourhood address manually below.');
      const collapseEl = document.getElementById('editLocationCollapse');
      if (collapseEl && typeof bootstrap !== 'undefined') {
        const bsCollapse = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl);
        bsCollapse.show();
      }
    },
    { timeout: 10000, enableHighAccuracy: true }
  );
}

function changeRadius(radius) {
  const url = new URL(window.location.href);
  url.searchParams.set('radius', radius);
  window.location.href = url.toString();
}
</script>
@endpush
