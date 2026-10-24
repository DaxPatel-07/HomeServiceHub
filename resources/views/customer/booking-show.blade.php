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
@endsection
@section('content')
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card-custom p-4 mb-4" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-sm);">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
          <span class="badge-pill badge-status-{{ $booking->status }} mb-2">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
          <h4 style="font-weight:800;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $booking->category->name }} Service</h4>
          <div style="font-size:.85rem;color:var(--color-text-muted);margin-top:.2rem;">Booking Code: <strong style="color:var(--color-text-dark);">{{ $booking->booking_code }}</strong></div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:1.5rem;font-weight:900;color:var(--color-primary);font-family:'Poppins',sans-serif;">₹{{ number_format($booking->total_amount) }}</div>
          <span class="badge-pill bg-light text-dark" style="border:1px solid var(--color-border);font-size:.72rem;">{{ strtoupper($booking->payment_method) }} &bull; {{ ucfirst($booking->payment_status) }}</span>
        </div>
      </div>

      <!-- 5-Step Progress Stepper -->
      @php
        $step1 = true;
        $step2 = in_array($booking->status, ['accepted','in_progress','completed']);
        $step3 = in_array($booking->status, ['accepted','in_progress','completed']);
        $step4 = in_array($booking->status, ['in_progress','completed']);
        $step5 = ($booking->status === 'completed');
      @endphp
      <div class="d-flex align-items-center justify-content-between my-3 px-3 py-3" style="background:var(--color-bg);border-radius:14px;border:1px solid var(--color-border);">
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step1 ? 'var(--color-primary)' : '#E2E8F0' }};color:{{ $step1 ? '#fff' : '#475569' }};border:1.5px solid {{ $step1 ? 'var(--color-primary)' : 'var(--color-border)' }};display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas fa-check"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:var(--color-text-dark);margin-top:.25rem;">Placed</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $step2 ? 'var(--color-primary)' : 'var(--color-border)' }};margin:0 4px 12px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step2 ? 'var(--color-primary)' : '#E2E8F0' }};color:{{ $step2 ? '#fff' : '#475569' }};border:1.5px solid {{ $step2 ? 'var(--color-primary)' : 'var(--color-border)' }};display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas {{ $step2 ? 'fa-check' : 'fa-clock' }}"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $step2 ? 'var(--color-text-dark)' : 'var(--color-text-muted)' }};margin-top:.25rem;">Confirmed</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $step3 ? 'var(--color-primary)' : 'var(--color-border)' }};margin:0 4px 12px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step3 ? 'var(--color-primary)' : '#E2E8F0' }};color:{{ $step3 ? '#fff' : '#475569' }};border:1.5px solid {{ $step3 ? 'var(--color-primary)' : 'var(--color-border)' }};display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas {{ $step3 ? 'fa-check' : 'fa-user' }}"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $step3 ? 'var(--color-text-dark)' : 'var(--color-text-muted)' }};margin-top:.25rem;">Assigned</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $step4 ? 'var(--color-primary)' : 'var(--color-border)' }};margin:0 4px 12px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step4 ? 'var(--color-primary)' : '#E2E8F0' }};color:{{ $step4 ? '#fff' : '#475569' }};border:1.5px solid {{ $step4 ? 'var(--color-primary)' : 'var(--color-border)' }};display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas {{ $step4 ? 'fa-tools' : 'fa-circle' }}"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $step4 ? 'var(--color-text-dark)' : 'var(--color-text-muted)' }};margin-top:.25rem;">Started</div>
        </div>
        <div style="flex-grow:2;height:2px;background:{{ $step5 ? 'var(--color-success)' : 'var(--color-border)' }};margin:0 4px 12px;"></div>
        <div class="text-center flex-grow-1 position-relative">
          <div style="width:32px;height:32px;border-radius:50%;background:{{ $step5 ? 'var(--color-success)' : '#E2E8F0' }};color:{{ $step5 ? '#fff' : '#475569' }};border:1.5px solid {{ $step5 ? 'var(--color-success)' : 'var(--color-border)' }};display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;"><i class="fas fa-flag-checkered"></i></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $step5 ? 'var(--color-success)' : 'var(--color-text-muted)' }};margin-top:.25rem;">Completed</div>
        </div>
      </div>
      
      <div class="divider" style="margin:1rem 0;"></div>
      
      <div class="row g-3 mb-3">
        <div class="col-6">
          <div style="font-size:.8rem;color:var(--color-text-muted);"><i class="fas fa-calendar me-1" style="color:var(--color-primary);"></i>Scheduled Date</div>
          <div style="font-weight:700;color:var(--color-text-dark);margin-top:.15rem;">{{ $booking->booking_date->format('d M Y') }} at {{ $booking->formatted_time }}</div>
        </div>
        <div class="col-6">
          <div style="font-size:.8rem;color:var(--color-text-muted);"><i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>Location</div>
          <div style="font-weight:700;color:var(--color-text-dark);margin-top:.15rem;">{{ $booking->city }}</div>
        </div>
        <div class="col-12">
          <div style="font-size:.8rem;color:var(--color-text-muted);"><i class="fas fa-home me-1" style="color:var(--color-primary);"></i>Address</div>
          <div style="font-weight:500;font-size:.9rem;color:var(--color-text-dark);margin-top:.15rem;">{{ $booking->address }}</div>
        </div>
        @if($booking->description)
        <div class="col-12">
          <div style="font-size:.8rem;color:var(--color-text-muted);"><i class="fas fa-align-left me-1" style="color:var(--color-primary);"></i>Notes / Problem Description</div>
          <div style="background:var(--color-bg);padding:.85rem;border-radius:10px;font-size:.88rem;color:var(--color-text-dark);border:1px solid var(--color-border);margin-top:.25rem;">{{ $booking->description }}</div>
        </div>
        @endif
      </div>

      @if($booking->status === 'completed' && !$booking->review)
      <div class="p-3 mb-3" style="background:rgba(217,119,6,.08);border-radius:14px;border:1px solid rgba(217,119,6,.2);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div>
            <div style="font-weight:700;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">How was your service?</div>
            <div style="font-size:.8rem;color:var(--color-text-muted);">Leave a rating and review to help the community.</div>
          </div>
          <a href="{{ route('customer.review.create', $booking) }}" class="btn-primary-custom btn btn-sm"><i class="fas fa-star me-1"></i>Write Review</a>
        </div>
      </div>
      @endif

      @if($booking->status === 'completed')
        @if($booking->complaint)
        <div class="p-3" style="background:rgba(179,38,30,.06);border-radius:14px;border:1px solid rgba(179,38,30,.2);">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span style="font-weight:700;color:var(--color-danger);font-size:.85rem;"><i class="fas fa-flag me-1"></i>Complaint Filed</span>
            <span class="badge-pill badge-status-{{ $booking->complaint->status === 'resolved' ? 'completed' : 'pending' }}">{{ ucfirst($booking->complaint->status) }}</span>
          </div>
          <div style="font-size:.82rem;font-weight:600;color:var(--color-text-dark);">{{ $booking->complaint->subject }}</div>
          <div style="font-size:.78rem;color:var(--color-text-muted);margin-top:.2rem;">{{ $booking->complaint->description }}</div>
          @if($booking->complaint->resolution)
          <div style="margin-top:.5rem;padding:.5rem;background:#fff;border-radius:8px;font-size:.78rem;color:var(--color-success);border:1px solid rgba(30,122,79,0.2);">
            <strong>Resolution:</strong> {{ $booking->complaint->resolution }}
          </div>
          @endif
        </div>
        @else
        <div class="d-flex justify-content-end mt-2">
          <a href="{{ route('customer.complaint.create', $booking) }}" style="font-size:.8rem;color:var(--color-danger);text-decoration:none;font-weight:600;">
            <i class="fas fa-flag me-1"></i>Have an issue with this service? File a Complaint
          </a>
        </div>
        @endif
      @endif

      @if(in_array($booking->status, ['pending', 'accepted']))
      <div class="p-3 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:rgba(239,68,68,.06);border-radius:12px;border:1px solid rgba(239,68,68,.2);">
        <div>
          <div style="font-weight:700;color:var(--color-danger);font-size:.88rem;">Need to reschedule or cancel?</div>
          <div style="font-size:.78rem;color:var(--color-text-muted);">Free cancellation up to 2 hours before scheduled service.</div>
        </div>
        <form method="POST" action="{{ route('customer.booking.cancel', $booking) }}" onsubmit="return confirm('Are you sure you want to cancel this booking?')" class="m-0">
          @csrf
          <button type="submit" class="btn btn-sm btn-danger" style="font-size:.8rem;border-radius:8px;padding:.4rem 1rem;font-weight:600;">
            <i class="fas fa-times-circle me-1"></i>Cancel Booking
          </button>
        </form>
      </div>
      @endif
    </div>

    @if($booking->review)
    <div class="card-custom p-4" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-sm);">
      <h6 style="font-weight:700;margin-bottom:1rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;"><i class="fas fa-star me-2" style="color:#D97706;"></i>Your Review</h6>
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="stars">
          @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=$booking->review->rating?'filled':'' }}"></i>@endfor
        </div>
        <span style="font-weight:700;color:var(--color-text-dark);">{{ $booking->review->rating }}/5</span>
      </div>
      <p style="font-size:.9rem;color:var(--color-text-muted);margin:0;line-height:1.6;">{{ $booking->review->comment }}</p>
    </div>
    @endif
  </div>

  <div class="col-lg-4">
    <div class="card-custom p-4 text-center" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-sm);">
      <h6 style="font-weight:700;margin-bottom:1rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">Service Provider</h6>
      <div class="provider-avatar mx-auto mb-2" style="width:68px;height:68px;font-size:1.6rem;background:linear-gradient(135deg, var(--color-primary), var(--color-support-blue));color:#fff;border-radius:18px;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ strtoupper(substr($booking->provider->name,0,1)) }}</div>
      <h5 style="font-weight:800;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $booking->provider->name }}</h5>
      <div style="font-size:.82rem;color:var(--color-text-muted);margin-bottom:.5rem;">{{ $booking->provider->category->name }}</div>
      <div class="d-flex justify-content-center align-items-center gap-1 mb-3">
        <i class="fas fa-star" style="color:#D97706;font-size:.8rem;"></i>
        <span style="font-weight:700;font-size:.85rem;color:var(--color-text-dark);">{{ $booking->provider->average_rating }}</span>
        <span style="color:var(--color-text-muted);font-size:.8rem;">({{ $booking->provider->total_jobs }} jobs)</span>
      </div>
      <a href="{{ route('provider.show', $booking->provider) }}" class="btn-outline-custom btn btn-sm w-100" style="justify-content:center;padding:.5rem;"><i class="fas fa-user me-1"></i>View Profile</a>
    </div>
  </div>
</div>
@endsection
