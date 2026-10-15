@extends('layouts.dashboard')
@section('title','My Bookings')
@section('role-label','Customer Portal')
@section('page-title','My Bookings')
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
  {{-- Header with action buttons --}}
  <div class="col-12">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h4 style="font-weight:900;color:var(--brand-deep-blue);margin:0;">My Bookings History</h4>
        <p style="color:var(--text-secondary);font-size:.88rem;margin:.25rem 0 0;">
          Track live service progress, review completed jobs, or schedule upcoming home maintenance.
        </p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('customer.split.booking') }}" class="btn-outline-custom btn btn-sm" style="padding:.5rem 1rem;">
          <i class="fas fa-layer-group me-1"></i>Split Booking
        </a>
        <a href="{{ route('search') }}" class="btn-primary-custom btn btn-sm" style="padding:.5rem 1rem;">
          <i class="fas fa-plus me-1"></i>Book New Service
        </a>
      </div>
    </div>
  </div>

  {{-- Bookings Table Card --}}
  <div class="col-12">
    <div class="card-custom">
      <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <i class="fas fa-calendar-alt" style="color:var(--brand-blue);font-size:1.1rem;"></i>
          <span style="font-weight:800;font-size:.95rem;color:var(--brand-deep-blue);">All Scheduled Services</span>
          <span class="badge" style="background:rgba(37,99,235,.1);color:var(--brand-blue);font-size:.78rem;font-weight:700;">
            {{ $bookings->total() }} total
          </span>
        </div>
      </div>

      @if($bookings->isEmpty())
        <div class="p-5 text-center">
          <div class="empty-state">
            <div style="width:70px;height:70px;border-radius:20px;background:var(--brand-cream);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;border:1px solid var(--brand-beige);">
              <i class="fas fa-calendar-times" style="font-size:2rem;color:var(--brand-blue);"></i>
            </div>
            <h5 style="font-weight:800;color:var(--brand-deep-blue);margin-bottom:.5rem;">No bookings found</h5>
            <p style="color:var(--text-secondary);font-size:.88rem;max-width:420px;margin:0 auto 1.25rem;">
              You have not booked any services yet. Discover verified local experts in your area today!
            </p>
            <a href="{{ route('search') }}" class="btn-primary-custom btn" style="display:inline-flex;padding:.6rem 1.4rem;">
              <i class="fas fa-search me-2"></i>Explore Services
            </a>
          </div>
        </div>
      @else
      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Service Category</th>
              <th>Assigned Professional</th>
              <th>Appointment Schedule</th>
              <th>Total Amount</th>
              <th>Status</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bookings as $b)
            <tr>
              <td>
                <a href="{{ route('customer.booking.show', $b) }}" style="font-size:.82rem;font-weight:800;color:var(--brand-blue);text-decoration:none;">
                  {{ $b->booking_code }}
                </a>
                @if($b->is_split)
                  <span class="badge ms-1" style="background:rgba(139,92,246,.12);color:var(--purple);font-size:.65rem;font-weight:700;">Split</span>
                @endif
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div style="width:34px;height:34px;border-radius:8px;background:rgba(37,99,235,.08);display:flex;align-items:center;justify-content:center;color:var(--brand-blue);font-size:.85rem;">
                    <i class="{{ $b->category->icon ?? 'fas fa-tools' }}"></i>
                  </div>
                  <div>
                    <div style="font-weight:700;color:var(--brand-deep-blue);font-size:.88rem;">{{ $b->category->name }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);"><i class="fas fa-map-marker-alt me-1"></i>{{ $b->city }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar-sm" style="background:var(--brand-cream);color:var(--brand-deep-blue);font-weight:800;border:1px solid var(--brand-beige);">
                    {{ strtoupper(substr($b->provider->name, 0, 1)) }}
                  </div>
                  <div>
                    <div style="font-weight:600;font-size:.85rem;color:var(--brand-deep-blue);">{{ $b->provider->name }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);">{{ $b->provider->service_area }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">
                  {{ $b->booking_date->format('d M Y') }}
                </div>
                <div style="color:var(--text-muted);font-size:.75rem;">
                  <i class="fas fa-clock me-1" style="color:var(--gold);"></i>{{ $b->formatted_time }}
                </div>
              </td>
              <td>
                <div style="font-weight:800;color:var(--brand-blue);font-size:.92rem;">
                  ₹{{ number_format($b->total_amount) }}
                </div>
                <div style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;">
                  {{ $b->payment_method }}
                </div>
              </td>
              <td>
                <span class="badge-pill badge-status-{{ $b->status }}">
                  @if($b->status === 'completed')
                    <i class="fas fa-check-circle me-1"></i>
                  @elseif($b->status === 'in_progress')
                    <i class="fas fa-spinner fa-spin me-1"></i>
                  @elseif($b->status === 'pending')
                    <i class="fas fa-clock me-1"></i>
                  @elseif($b->status === 'accepted')
                    <i class="fas fa-calendar-check me-1"></i>
                  @elseif($b->status === 'cancelled')
                    <i class="fas fa-times-circle me-1"></i>
                  @endif
                  {{ ucfirst(str_replace('_',' ',$b->status)) }}
                </span>
              </td>
              <td style="text-align:right;">
                <div class="d-inline-flex gap-1 align-items-center">
                  <a href="{{ route('customer.booking.show', $b) }}" class="btn btn-sm" style="font-size:.75rem;border-radius:6px;background:rgba(37,99,235,.08);color:var(--brand-blue);font-weight:600;border:1px solid rgba(37,99,235,.2);padding:.25rem .6rem;">
                    Details
                  </a>
                  @if($b->status==='completed' && !$b->review)
                    <a href="{{ route('customer.review.create', $b) }}" class="btn btn-sm btn-success" style="font-size:.75rem;border-radius:6px;padding:.25rem .6rem;font-weight:600;">
                      <i class="fas fa-star me-1"></i>Review
                    </a>
                  @endif
                  @if(in_array($b->status,['pending','accepted']))
                    <form method="POST" action="{{ route('customer.booking.cancel', $b) }}" onsubmit="return confirm('Are you sure you want to cancel this booking?')" class="m-0">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger" style="font-size:.75rem;border-radius:6px;padding:.25rem .6rem;font-weight:600;">
                        Cancel
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-3 border-top" style="border-color:var(--card-border) !important;">
        {{ $bookings->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
