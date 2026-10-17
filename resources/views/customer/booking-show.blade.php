@extends('layouts.dashboard')
@section('title','Booking Details')
@section('role-label','Customer Portal')
@section('page-title','Booking #' . $booking->booking_code)
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('customer.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('search') }}"><i class="fas fa-search"></i>Search Services</a></div>
<div class="nav-item"><a href="{{ route('customer.bookings') }}" class="active"><i class="fas fa-calendar-check"></i>My Bookings</a></div>
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
  <div class="col-lg-8">
    <div class="card-custom p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <span class="badge-pill badge-status-{{ $booking->status }} mb-2">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
          <h4 style="font-weight:800;margin:0;">{{ $booking->category->name }} Service</h4>
          <div style="font-size:.85rem;color:var(--text-muted);">Booking Code: <strong>{{ $booking->booking_code }}</strong></div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:1.4rem;font-weight:900;color:var(--brand-blue);">₹{{ number_format($booking->total_amount) }}</div>
          <span class="badge-pill bg-light text-dark" style="border:1px solid var(--card-border);">{{ strtoupper($booking->payment_method) }} &bull; {{ ucfirst($booking->payment_status) }}</span>
        </div>
      </div>

      <!-- 5-Step Progress Stepper matching Bookings details.jpeg -->
      @php
        $step1 = true;
        $step2 = in_array($booking->status, ['accepted','in_progress','completed']);
        $step3 = in_array($booking->status, ['accepted','in_progress','completed']);
        $step4 = in_array($booking->status, ['in_progress','completed']);
        $step5 = ($booking->status === 'completed');
      @endphp
      <div class="d-flex align-items-center justify-content-between my-3 px-3 py-3" style="background:var(--brand-cream);border-radius:12px;border:1px solid var(--card-border);">
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step1 ? 'var(--brand-blue)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas fa-check"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:var(--brand-deep-blue);margin-top:.25rem;">Placed</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $step2 ? 'var(--brand-blue)' : 'var(--card-border)' }};margin:0 4px 12px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step2 ? 'var(--brand-blue)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas {{ $step2 ? 'fa-check' : 'fa-clock' }}"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $step2 ? 'var(--brand-deep-blue)' : 'var(--text-muted)' }};margin-top:.25rem;">Confirmed</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $step3 ? 'var(--brand-blue)' : 'var(--card-border)' }};margin:0 4px 12px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step3 ? 'var(--brand-blue)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas {{ $step3 ? 'fa-check' : 'fa-user' }}"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $step3 ? 'var(--brand-deep-blue)' : 'var(--text-muted)' }};margin-top:.25rem;">Assigned</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $step4 ? 'var(--brand-blue)' : 'var(--card-border)' }};margin:0 4px 12px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step4 ? 'var(--brand-blue)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas {{ $step4 ? 'fa-tools' : 'fa-circle' }}"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $step4 ? 'var(--brand-deep-blue)' : 'var(--text-muted)' }};margin-top:.25rem;">Started</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $step5 ? 'var(--success)' : 'var(--card-border)' }};margin:0 4px 12px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step5 ? 'var(--success)' : '#E2E8F0' }};color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas fa-flag-checkered"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $step5 ? 'var(--success)' : 'var(--text-muted)' }};margin-top:.25rem;">Completed</div>
        </div>
      </div>
      
      <div class="divider"></div>
      
      <div class="row g-3 mb-3">
        <div class="col-6">
          <div style="font-size:.8rem;color:var(--text-muted);"><i class="fas fa-calendar me-1" style="color:var(--brand-blue);"></i>Scheduled Date</div>
          <div style="font-weight:700;">{{ $booking->booking_date->format('d M Y') }} at {{ $booking->formatted_time }}</div>
        </div>
        <div class="col-6">
          <div style="font-size:.8rem;color:var(--text-muted);"><i class="fas fa-map-marker-alt me-1" style="color:var(--brand-blue);"></i>Location</div>
          <div style="font-weight:700;">{{ $booking->city }}</div>
        </div>
        <div class="col-12">
          <div style="font-size:.8rem;color:var(--text-muted);"><i class="fas fa-home me-1" style="color:var(--brand-blue);"></i>Address</div>
          <div style="font-weight:500;font-size:.9rem;">{{ $booking->address }}</div>
        </div>
        @if($booking->description)
        <div class="col-12">
          <div style="font-size:.8rem;color:var(--text-muted);"><i class="fas fa-align-left me-1" style="color:var(--brand-blue);"></i>Notes / Problem Description</div>
          <div style="background:var(--bg-light);padding:.75rem;border-radius:8px;font-size:.88rem;">{{ $booking->description }}</div>
        </div>
        @endif
      </div>

      @if($booking->status === 'completed' && !$booking->review)
      <div class="p-3 mb-3" style="background:rgba(232,165,26,.08);border-radius:12px;border:1px solid rgba(232,165,26,.2);">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div style="font-weight:700;color:var(--text-primary);">How was your service?</div>
            <div style="font-size:.8rem;color:var(--text-secondary);">Leave a rating and review to help the community.</div>
          </div>
          <a href="{{ route('customer.review.create', $booking) }}" class="btn-gold-custom btn btn-sm"><i class="fas fa-star me-1"></i>Write Review</a>
        </div>
      </div>
      @endif

      @if($booking->status === 'completed')
        @if($booking->complaint)
        <div class="p-3" style="background:rgba(239,68,68,.06);border-radius:12px;border:1px solid rgba(239,68,68,.2);">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span style="font-weight:700;color:var(--danger);font-size:.85rem;"><i class="fas fa-flag me-1"></i>Complaint Filed</span>
            <span class="badge-pill badge-status-{{ $booking->complaint->status === 'resolved' ? 'completed' : 'pending' }}">{{ ucfirst($booking->complaint->status) }}</span>
          </div>
          <div style="font-size:.82rem;font-weight:600;">{{ $booking->complaint->subject }}</div>
          <div style="font-size:.78rem;color:var(--text-secondary);margin-top:.2rem;">{{ $booking->complaint->description }}</div>
          @if($booking->complaint->resolution)
          <div style="margin-top:.5rem;padding:.5rem;background:#fff;border-radius:6px;font-size:.78rem;color:var(--success);">
            <strong>Resolution:</strong> {{ $booking->complaint->resolution }}
          </div>
          @endif
        </div>
        @else
        <div class="d-flex justify-content-end mt-2">
          <a href="{{ route('customer.complaint.create', $booking) }}" style="font-size:.8rem;color:var(--danger);text-decoration:none;">
            <i class="fas fa-flag me-1"></i>Have an issue with this service? File a Complaint
          </a>
        </div>
        @endif
      @endif
    </div>

    @if($booking->review)
    <div class="card-custom p-4">
      <h6 style="font-weight:700;margin-bottom:1rem;"><i class="fas fa-star me-2" style="color:var(--gold);"></i>Your Review</h6>
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="stars">
          @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=$booking->review->rating?'filled':'' }}"></i>@endfor
        </div>
        <span style="font-weight:700;">{{ $booking->review->rating }}/5</span>
      </div>
      <p style="font-size:.9rem;color:var(--text-secondary);margin:0;">{{ $booking->review->comment }}</p>
    </div>
    @endif
  </div>

  <div class="col-lg-4">
    <div class="card-custom p-4 text-center">
      <h6 style="font-weight:700;margin-bottom:1rem;">Service Provider</h6>
      <div class="provider-avatar mx-auto mb-2" style="width:64px;height:64px;font-size:1.5rem;">{{ strtoupper(substr($booking->provider->name,0,1)) }}</div>
      <h5 style="font-weight:800;margin:0;">{{ $booking->provider->name }}</h5>
      <div style="font-size:.82rem;color:var(--text-secondary);margin-bottom:.5rem;">{{ $booking->provider->category->name }}</div>
      <div class="d-flex justify-content-center align-items-center gap-1 mb-3">
        <i class="fas fa-star" style="color:var(--gold);font-size:.8rem;"></i>
        <span style="font-weight:700;font-size:.85rem;">{{ $booking->provider->average_rating }}</span>
        <span style="color:var(--text-muted);font-size:.8rem;">({{ $booking->provider->total_jobs }} jobs)</span>
      </div>
      <a href="{{ route('provider.show', $booking->provider) }}" class="btn-outline-custom btn btn-sm w-100"><i class="fas fa-user me-1"></i>View Profile</a>
    </div>
  </div>
</div>
@endsection
