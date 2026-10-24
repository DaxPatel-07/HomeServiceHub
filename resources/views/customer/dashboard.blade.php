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
@endsection
@section('content')
<div class="row g-3 g-md-4">
  {{-- Welcome Banner --}}
  <div class="col-12">
    <div class="welcome-banner d-flex justify-content-between align-items-center flex-wrap gap-3" style="box-shadow:var(--shadow-sm);">
      <div>
        <h4 style="color:#fff;font-weight:700;margin:0;font-family:'Poppins',sans-serif;"><span id="greetingText">Good morning</span>, {{ $user->name }}! <span id="greetingEmoji">☀️</span></h4>
        <p style="color:rgba(255,255,255,.85);margin:.35rem 0 0;font-size:.9rem;">Welcome to HomeServiceHub — here's your home maintenance overview.</p>
      </div>
      <a href="{{ route('search') }}" class="btn-banner-cta"><i class="fas fa-plus"></i>Book a Service</a>
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
    <div class="card-custom p-3 p-md-4" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-sm);">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <div class="d-flex align-items-center gap-3">
          <div class="avatar-sm" style="background:var(--color-blue-tint);color:var(--color-primary);border:1px solid rgba(36,81,189,0.15);font-size:1.15rem;width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="{{ $activeBooking->category->icon }}"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <h5 style="font-weight:700;color:var(--color-text-dark);margin:0;font-family:'Poppins',sans-serif;">Active Booking: {{ $activeBooking->category->name }}</h5>
              <span class="badge-pill badge-status-{{ $activeBooking->status }}">{{ ucfirst(str_replace('_',' ',$activeBooking->status)) }}</span>
            </div>
            <div style="font-size:.82rem;color:var(--color-text-muted);margin-top:.2rem;">
              Code: <strong style="color:var(--color-text-dark);">{{ $activeBooking->booking_code }}</strong> &bull; Scheduled for {{ $activeBooking->booking_date->format('d M Y') }} at {{ $activeBooking->formatted_time }}
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('customer.booking.show', $activeBooking) }}" class="btn-primary-custom btn btn-sm"><i class="fas fa-eye me-1"></i>View Details</a>
        </div>
      </div>

      <!-- 4-Step Stepper -->
      <div class="active-booking-stepper mt-3">
        @php
          $isPlaced = true;
          $isAssigned = in_array($activeBooking->status, ['accepted','in_progress','completed']);
          $isInProgress = in_array($activeBooking->status, ['in_progress','completed']);
          $isCompleted = ($activeBooking->status === 'completed');
        @endphp
        <div class="stepper-step">
          <div class="stepper-circle {{ $isPlaced ? 'active' : 'inactive' }}"><i class="fas fa-check"></i></div>
          <div class="stepper-label" style="color:var(--color-text-dark);">Booked</div>
        </div>
        <div class="stepper-line {{ $isAssigned ? 'active' : '' }}"></div>
        <div class="stepper-step">
          <div class="stepper-circle {{ $isAssigned ? 'active' : 'inactive' }}">
            <i class="fas {{ $isAssigned ? 'fa-check' : 'fa-user' }}"></i>
          </div>
          <div class="stepper-label" style="color:{{ $isAssigned ? 'var(--color-text-dark)' : 'var(--color-text-muted)' }};">Pro Assigned</div>
        </div>
        <div class="stepper-line {{ $isInProgress ? 'active' : '' }}"></div>
        <div class="stepper-step">
          <div class="stepper-circle {{ $isInProgress ? 'active' : 'inactive' }}">
            <i class="fas {{ $isInProgress ? 'fa-tools' : 'fa-circle' }}"></i>
          </div>
          <div class="stepper-label" style="color:{{ $isInProgress ? 'var(--color-text-dark)' : 'var(--color-text-muted)' }};">In Progress</div>
        </div>
        <div class="stepper-line {{ $isCompleted ? 'success' : '' }}"></div>
        <div class="stepper-step">
          <div class="stepper-circle {{ $isCompleted ? 'success' : 'inactive' }}">
            <i class="fas fa-flag-checkered"></i>
          </div>
          <div class="stepper-label" style="color:{{ $isCompleted ? 'var(--color-success)' : 'var(--color-text-muted)' }};">Completed</div>
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- Stats --}}
  <div class="col-6 col-md-3">
    <div class="dashboard-stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div style="min-width:0;flex:1;">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value text-truncate" style="color:var(--color-text-dark);">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-icon-wrap ms-2" style="background:var(--color-blue-tint);">
          <i class="fas fa-calendar" style="color:var(--color-primary);"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="dashboard-stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div style="min-width:0;flex:1;">
          <div class="stat-label">Pending</div>
          <div class="stat-value text-truncate" style="color:#D97706;">{{ $stats['pending'] }}</div>
        </div>
        <div class="stat-icon-wrap ms-2" style="background:rgba(217,119,6,.12);">
          <i class="fas fa-clock" style="color:#D97706;"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="dashboard-stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div style="min-width:0;flex:1;">
          <div class="stat-label">Completed</div>
          <div class="stat-value text-truncate" style="color:var(--color-success);">{{ $stats['completed'] }}</div>
        </div>
        <div class="stat-icon-wrap ms-2" style="background:rgba(30,122,79,.12);">
          <i class="fas fa-check-circle" style="color:var(--color-success);"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="dashboard-stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div style="min-width:0;flex:1;">
          <div class="stat-label">Total Spent</div>
          <div class="stat-value text-truncate" style="color:var(--color-primary);">₹{{ number_format($stats['spent']) }}</div>
        </div>
        <div class="stat-icon-wrap ms-2" style="background:var(--color-blue-tint);">
          <i class="fas fa-wallet" style="color:var(--color-primary);"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Recent Bookings --}}
  <div class="col-lg-8">
    <div class="card-custom" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;overflow:hidden;box-shadow:var(--shadow-sm);">
      <div class="card-header-custom" style="background:#FFFFFF;border-bottom:1px solid var(--color-border);padding:1.25rem 1.5rem;display:flex;align-items:center;justify-content:space-between;">
        <h6 style="font-weight:700;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;"><i class="fas fa-calendar-check me-2" style="color:var(--color-primary);"></i>Recent Bookings</h6>
        <a href="{{ route('customer.bookings') }}" style="color:var(--color-primary);font-size:.82rem;font-weight:600;">View All</a>
      </div>
      <div class="card-body-custom p-0">
        @if($bookings->isEmpty())
          <div class="empty-state p-4 text-center"><i class="fas fa-calendar-times" style="font-size:2rem;color:var(--color-text-muted);margin-bottom:.5rem;"></i><p style="color:var(--color-text-muted);font-size:.88rem;">No bookings yet. <a href="{{ route('search') }}" style="color:var(--color-primary);font-weight:600;">Book your first service!</a></p></div>
        @else
        <div class="table-responsive" style="overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%;">
          <table class="table-custom w-100">
            <thead><tr><th>Service</th><th>Provider</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
              @foreach($bookings as $b)
              <tr>
                <td><div style="font-weight:600;font-size:.88rem;color:var(--color-text-dark);">{{ $b->category->name }}</div><div style="font-size:.75rem;color:var(--color-text-muted);">{{ $b->booking_code }}</div></td>
                <td><div class="d-flex align-items-center gap-2"><div class="avatar-sm" style="width:32px;height:32px;border-radius:8px;background:var(--color-blue-tint);color:var(--color-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;">{{ strtoupper(substr($b->provider->name,0,1)) }}</div><span style="font-size:.85rem;font-weight:500;color:var(--color-text-dark);">{{ $b->provider->name }}</span></div></td>
                <td style="font-size:.85rem;color:var(--color-text-dark);">{{ $b->booking_date->format('d M Y') }}<br><span style="color:var(--color-text-muted);font-size:.75rem;">{{ $b->formatted_time }}</span></td>
                <td style="font-weight:700;color:var(--color-primary);">₹{{ number_format($b->total_amount) }}</td>
                <td><span class="badge-pill badge-status-{{ $b->status }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Right Panel --}}
  <div class="col-lg-4 d-flex flex-column gap-4">
    {{-- Maintenance Reminders --}}
    <div class="card-custom" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;overflow:hidden;box-shadow:var(--shadow-sm);">
      <div class="card-header-custom" style="background:#FFFFFF;border-bottom:1px solid var(--color-border);padding:1.1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;">
        <h6 style="font-weight:700;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;"><i class="fas fa-bell me-2" style="color:#D97706;"></i>Reminders</h6>
        <a href="{{ route('customer.reminders') }}" class="btn btn-sm" style="border-radius:20px;padding:.2rem .75rem;font-size:.78rem;font-weight:600;background:var(--color-bg);border:1px solid var(--color-border);color:var(--color-primary);">Manage</a>
      </div>
      <div class="card-body-custom p-3">
        @forelse($reminders as $r)
        <div style="display:flex;align-items:center;gap:.75rem;padding:.65rem 0;border-bottom:1px solid var(--color-border);" class="{{ !$loop->last ? '' : '' }}">
          <div class="stat-icon" style="background:var(--color-blue-tint);width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;"><i class="{{ $r->category->icon }}" style="color:var(--color-primary);font-size:.9rem;"></i></div>
          <div class="flex-grow-1">
            <div style="font-weight:600;font-size:.85rem;color:var(--color-text-dark);">{{ $r->title }}</div>
            <div style="font-size:.75rem;color:{{ $r->next_due_date->diffInDays(now()) <= 7 ? 'var(--color-danger)' : 'var(--color-text-muted)' }};">
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
    <div class="card-custom" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;overflow:hidden;box-shadow:var(--shadow-sm);">
      <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:#FFFFFF;border-bottom:1px solid var(--color-border);padding:1.25rem 1.5rem;">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <h6 class="mb-0" style="font-weight:700;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">
            <i class="fas fa-heart me-2" style="color:var(--color-primary);"></i>Neighbour Trusted Providers
          </h6>
          @if(!$customerLocation['is_missing'])
          <span class="badge" style="background:var(--color-blue-tint);color:var(--color-primary);border:1px solid rgba(36,81,189,.2);border-radius:999px;padding:.35rem .85rem;font-size:.75rem;font-weight:600;">
            <i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>Near {{ $customerLocation['label'] }}
          </span>
          @endif
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          {{-- Radius Selector --}}
          <div class="d-inline-flex align-items-center gap-1">
            <span style="font-size:.75rem;color:var(--color-text-muted);font-weight:600;"><i class="fas fa-bullseye me-1"></i>Radius:</span>
            <select class="form-select form-select-sm" style="width:auto;display:inline-block;padding:.2rem 1.6rem .2rem .6rem;font-size:.75rem;border-radius:20px;border-color:var(--color-border);" onchange="changeRadius(this.value)">
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
          <button type="button" class="btn btn-sm" style="border-radius:20px;padding:.25rem .75rem;font-size:.75rem;font-weight:600;background:var(--color-bg);border:1px solid var(--color-border);color:var(--color-text-dark);" data-bs-toggle="collapse" data-bs-target="#editLocationCollapse" aria-expanded="false">
            <i class="fas fa-map-pin me-1"></i>Edit Location
          </button>

          <a href="{{ route('search') }}?neighborhood_trusted=1" style="color:var(--color-primary);font-size:.82rem;font-weight:600;">View All</a>
        </div>
      </div>

      {{-- Collapsible Location Editor --}}
      <div class="collapse {{ $locationStatus === 'missing_location' ? 'show' : '' }}" id="editLocationCollapse">
        <div style="background:var(--color-bg);border-bottom:1px solid var(--color-border);padding:1.25rem 1.5rem;">
          <form method="POST" action="{{ route('customer.location.update') }}">
            @csrf
            <div class="row g-2 align-items-end">
              <div class="col-12 col-sm-6 col-md-3">
                <label style="font-size:.75rem;font-weight:700;color:var(--color-text-dark);display:block;margin-bottom:.25rem;">Building / Society</label>
                <input type="text" name="building" class="form-control form-control-sm" placeholder="e.g. Shivalik Heights" value="{{ old('building', $customerLocation['building']) }}">
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <label style="font-size:.75rem;font-weight:700;color:var(--color-text-dark);display:block;margin-bottom:.25rem;">Locality / Neighbourhood</label>
                <input type="text" name="locality" class="form-control form-control-sm" placeholder="e.g. SG Highway, Satellite, Bopal" value="{{ old('locality', $customerLocation['locality']) }}">
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <label style="font-size:.75rem;font-weight:700;color:var(--color-text-dark);display:block;margin-bottom:.25rem;">City</label>
                <select name="city" class="form-select form-select-sm">
                  <option value="Ahmedabad" {{ ($customerLocation['city'] ?? $userCity) === 'Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                  <option value="Gandhinagar" {{ ($customerLocation['city'] ?? $userCity) === 'Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
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

      <div class="card-body-custom p-4">
        {{-- State 1: Missing Location Prompt --}}
        @if($locationStatus === 'missing_location')
        <div class="p-4 text-center" style="background:#fff;border-radius:14px;border:1px dashed var(--color-border);">
          <div style="width:56px;height:56px;border-radius:50%;background:var(--color-blue-tint);display:inline-flex;align-items:center;justify-content:center;color:var(--color-primary);font-size:1.6rem;margin-bottom:.75rem;">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <h6 style="font-weight:700;color:var(--color-text-dark);margin-bottom:.35rem;font-family:'Poppins',sans-serif;">Set Your Neighbourhood Location</h6>
          <p style="color:var(--color-text-muted);font-size:.85rem;max-width:500px;margin:0 auto 1rem;line-height:1.5;">
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
        <div class="p-4 text-center" style="background:#fff;border-radius:14px;border:1px dashed var(--color-border);">
          <div style="width:56px;height:56px;border-radius:50%;background:rgba(217,119,6,.12);display:inline-flex;align-items:center;justify-content:center;color:#D97706;font-size:1.6rem;margin-bottom:.75rem;">
            <i class="fas fa-search-location"></i>
          </div>
          <h6 style="font-weight:700;color:var(--color-text-dark);margin-bottom:.35rem;font-family:'Poppins',sans-serif;">No trusted providers found near your location yet.</h6>
          <p style="color:var(--color-text-muted);font-size:.85rem;max-width:520px;margin:0 auto 1.25rem;line-height:1.5;">
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
            <div class="h-100 d-flex flex-column justify-content-between" style="border:{{ $p->isNeighborhoodTrusted() ? '1.5px solid rgba(36,81,189,.3)' : '1px solid var(--color-border)' }};border-radius:16px;padding:1.25rem;text-align:center;transition:.2s;background:#fff;box-shadow:var(--shadow-sm);" onmouseover="this.style.boxShadow='var(--shadow)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
              <div>
                {{-- Badges Header --}}
                <div class="d-flex justify-content-between align-items-center gap-1 mb-2 flex-wrap">
                  @if($p->isNeighborhoodTrusted())
                  <span style="background:var(--color-primary);color:#fff;border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;">
                    <i class="fas fa-heart me-1"></i>Neighbour Choice
                  </span>
                  @endif

                  {{-- Match Badge (e.g. Same Building, Serves your area) --}}
                  @if($p->match_type === 'same_building' || (!empty($p->match_badge) && $p->match_badge !== $p->distance_text))
                  <span style="background:rgba(30,122,79,.1);color:var(--color-success);border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;">
                    <i class="fas {{ $p->match_type === 'same_building' ? 'fa-building' : 'fa-check-circle' }} me-1"></i>{{ $p->match_badge }}
                  </span>
                  @endif

                  {{-- Distance / Proximity Match Badge --}}
                  @if(!empty($p->distance_text))
                  <span style="background:var(--color-blue-tint);color:var(--color-primary);border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;" title="Calculated distance">
                    <i class="fas fa-location-arrow me-1"></i>{{ $p->distance_text }}
                  </span>
                  @endif
                </div>

                <div class="avatar-md mx-auto mb-2" style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg, var(--color-primary), var(--color-support-blue));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;">{{ strtoupper(substr($p->name,0,1)) }}</div>
                <div style="font-weight:700;font-size:.9rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $p->name }}</div>
                <div style="font-size:.78rem;color:var(--color-text-muted);">{{ $p->category->name }}</div>
                <div style="font-size:.75rem;color:var(--color-text-muted);margin-top:.25rem;">
                  <i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>{{ $p->service_area }}
                </div>
                <div class="d-flex justify-content-center align-items-center gap-1 my-2">
                  <i class="fas fa-star" style="font-size:.75rem;color:#D97706;"></i>
                  <span style="font-weight:700;font-size:.8rem;color:var(--color-text-dark);">{{ $p->average_rating }}</span>
                  <span style="font-size:.72rem;color:var(--color-text-muted);">({{ $p->completed_jobs }} jobs)</span>
                </div>
              </div>

              <div>
                <div style="font-weight:800;color:var(--color-primary);font-size:.9rem;margin-bottom:.5rem;font-family:'Poppins',sans-serif;">₹{{ number_format($p->hourly_rate) }}/hr</div>
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
function getTimeBasedGreeting(date = new Date()) {
  const hour = date.getHours();

  if (hour >= 5 && hour < 12) {
    return "Good morning";
  }

  if (hour >= 12 && hour < 17) {
    return "Good afternoon";
  }

  if (hour >= 17 && hour < 21) {
    return "Good evening";
  }

  return "Good night";
}

function getGreetingEmoji(greeting) {
  switch (greeting) {
    case 'Good morning':
      return '☀️';
    case 'Good afternoon':
      return '🌤️';
    case 'Good evening':
      return '🌆';
    case 'Good night':
      return '🌙';
    default:
      return '☀️';
  }
}

function updateGreeting() {
  const textEl = document.getElementById('greetingText');
  const emojiEl = document.getElementById('greetingEmoji');
  if (!textEl) return;

  const greeting = getTimeBasedGreeting();
  if (textEl.textContent !== greeting) {
    textEl.textContent = greeting;
  }
  if (emojiEl) {
    const emoji = getGreetingEmoji(greeting);
    if (emojiEl.textContent !== emoji) {
      emojiEl.textContent = emoji;
    }
  }
}

// Update greeting immediately when script executes
updateGreeting();

// Ensure greeting is updated when DOM is ready if script ran early
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', updateGreeting);
}

// Automatically refresh greeting when tab regains visibility
document.addEventListener('visibilitychange', function() {
  if (!document.hidden) {
    updateGreeting();
  }
});

// Update automatically if current time crosses a greeting boundary while dashboard remains open
setInterval(updateGreeting, 60000);

// Expose on window for testing and programmatic checks
window.getTimeBasedGreeting = getTimeBasedGreeting;
window.getGreetingEmoji = getGreetingEmoji;
window.updateGreeting = updateGreeting;

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
