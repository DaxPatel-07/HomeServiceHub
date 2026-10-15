@extends('layouts.dashboard')
@section('title','Manage Bookings')
@section('role-label','Provider Portal')
@section('page-title','Manage Bookings')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('provider.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('provider.bookings') }}" class="active"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-item"><a href="{{ route('provider.profile') }}"><i class="fas fa-user-cog"></i>My Profile</a></div>
<div class="nav-section-title">Public View</div>
<div class="nav-item"><a href="{{ route('provider.show', $provider) }}"><i class="fas fa-external-link-alt"></i>Public Profile</a></div>
@endsection

@section('content')
<div class="row g-4">
  <div class="col-12">
    <div class="card-custom">
      <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <i class="fas fa-calendar-check" style="color:var(--brand-blue);font-size:1.1rem;"></i>
          <span style="font-weight:800;font-size:.95rem;color:var(--brand-deep-blue);">Customer Service Bookings</span>
          <span class="badge" style="background:rgba(37,99,235,.1);color:var(--brand-blue);font-size:.78rem;font-weight:700;">
            {{ $bookings->total() }} total
          </span>
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Customer</th>
              <th>Service</th>
              <th>Date &amp; Time</th>
              <th>Payout</th>
              <th>Status</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookings as $b)
            <tr>
              <td>
                <span style="font-size:.82rem;font-weight:800;color:var(--brand-deep-blue);">{{ $b->booking_code }}</span>
                @if($b->is_split)
                  <span class="badge ms-1" style="background:rgba(139,92,246,.12);color:var(--purple);font-size:.65rem;font-weight:700;">Split</span>
                @endif
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar-sm" style="background:var(--brand-cream);color:var(--brand-deep-blue);font-weight:800;border:1px solid var(--brand-beige);">
                    {{ strtoupper(substr($b->customer->name,0,1)) }}
                  </div>
                  <div>
                    <div style="font-weight:700;font-size:.85rem;color:var(--brand-deep-blue);">{{ $b->customer->name }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);"><i class="fas fa-map-marker-alt me-1"></i>{{ $b->city }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="badge" style="background:rgba(37,99,235,.08);color:var(--brand-blue);font-weight:700;font-size:.78rem;">
                  {{ $b->category->name }}
                </span>
              </td>
              <td>
                <div style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">{{ $b->booking_date->format('d M Y') }}</div>
                <div style="color:var(--text-muted);font-size:.75rem;"><i class="fas fa-clock me-1" style="color:var(--gold);"></i>{{ $b->formatted_time }}</div>
              </td>
              <td>
                <div style="font-weight:800;color:var(--brand-blue);font-size:.92rem;">₹{{ number_format($b->total_amount) }}</div>
                <div style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;">{{ $b->payment_method }}</div>
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
                  @if($b->status==='pending')
                    <form method="POST" action="{{ route('provider.booking.accept',$b) }}" class="m-0">
                      @csrf
                      <button class="btn btn-sm btn-success" style="font-size:.75rem;border-radius:6px;padding:.25rem .6rem;font-weight:600;">
                        <i class="fas fa-check me-1"></i>Accept
                      </button>
                    </form>
                    <form method="POST" action="{{ route('provider.booking.reject',$b) }}" class="m-0">
                      @csrf
                      <button class="btn btn-sm btn-danger" style="font-size:.75rem;border-radius:6px;padding:.25rem .6rem;font-weight:600;">
                        <i class="fas fa-times me-1"></i>Reject
                      </button>
                    </form>
                  @elseif($b->status==='accepted')
                    <form method="POST" action="{{ route('provider.booking.complete',$b) }}" class="m-0">
                      @csrf
                      <button class="btn btn-sm btn-primary-custom" style="font-size:.75rem;border-radius:6px;padding:.25rem .75rem;font-weight:600;">
                        <i class="fas fa-check-double me-1"></i>Mark Completed
                      </button>
                    </form>
                  @else
                    <span style="font-size:.75rem;color:var(--text-muted);font-weight:600;">No actions</span>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center" style="padding:3rem 1rem;color:var(--text-muted);">
                <i class="fas fa-calendar-times" style="font-size:2rem;margin-bottom:.5rem;display:block;color:var(--text-muted);"></i>
                No bookings found.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3 border-top" style="border-color:var(--card-border) !important;">
        {{ $bookings->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
