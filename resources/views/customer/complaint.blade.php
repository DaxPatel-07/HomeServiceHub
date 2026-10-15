@extends('layouts.dashboard')
@section('title','File a Complaint')
@section('role-label','Customer Portal')
@section('page-title','File a Complaint')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('customer.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('search') }}"><i class="fas fa-search"></i>Find Services</a></div>
<div class="nav-item"><a href="{{ route('customer.bookings') }}" ><i class="fas fa-calendar-check"></i>My Bookings</a></div>
<div class="nav-section-title">Account</div>
<div class="nav-item"><a href="{{ route('customer.profile') }}"><i class="fas fa-user-circle"></i>My Profile</a></div>
@endsection

@section('content')
<div class="row g-4">
  {{-- Left Column: Complaint Form --}}
  <div class="col-lg-7">
    <div class="card-custom p-4">
      <!-- Booking Preview Card (complaint.jpeg) -->
      <div class="d-flex align-items-center justify-content-between p-3 mb-4" style="background:var(--brand-cream);border-radius:12px;border:1px solid var(--card-border);">
        <div class="d-flex align-items-center gap-3">
          <div class="avatar-sm" style="background:var(--brand-blue);color:#fff;">
            {{ strtoupper(substr($booking->provider->name, 0, 1)) }}
          </div>
          <div>
            <div style="font-weight:700;font-size:.9rem;color:var(--brand-deep-blue);">{{ $booking->category->name }} Service</div>
            <div style="font-size:.78rem;color:var(--text-secondary);">Provider: <strong>{{ $booking->provider->name }}</strong> &bull; Code: {{ $booking->booking_code }}</div>
          </div>
        </div>
        <div style="text-align:right;">
          <div style="font-weight:800;color:var(--brand-blue);font-size:.95rem;">₹{{ number_format($booking->total_amount) }}</div>
          <div style="font-size:.72rem;color:var(--text-muted);">{{ $booking->booking_date->format('d M Y') }}</div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-3 mb-3">
        <div style="width:40px;height:40px;border-radius:10px;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;">
          <i class="fas fa-flag" style="color:var(--danger);font-size:1.1rem;"></i>
        </div>
        <div>
          <h5 style="font-weight:800;color:var(--brand-deep-blue);margin:0;">Complaint Details</h5>
          <div style="font-size:.8rem;color:var(--text-secondary);">Tell us what went wrong so we can make it right</div>
        </div>
      </div>

      <form method="POST" action="{{ route('customer.complaint.store', $booking) }}">
        @csrf
        <div class="form-group-custom">
          <label>Complaint Subject *</label>
          <input type="text" name="subject" class="form-control" placeholder="e.g. Incomplete work, Late arrival, Damage to property..." required maxlength="255" value="{{ old('subject') }}">
        </div>
        <div class="form-group-custom">
          <label>Describe the Problem *</label>
          <textarea name="description" class="form-control" rows="5" placeholder="Please describe the issue thoroughly with dates, times, and actions taken..." required>{{ old('description') }}</textarea>
        </div>

        <div class="form-group-custom">
          <label>Upload Supporting Images (Optional)</label>
          <div style="border:2px dashed var(--card-border);border-radius:12px;padding:1.5rem;text-align:center;background:var(--brand-cream);cursor:pointer;">
            <i class="fas fa-cloud-upload-alt" style="font-size:1.8rem;color:var(--brand-blue);margin-bottom:.5rem;"></i>
            <div style="font-size:.82rem;font-weight:700;color:var(--brand-deep-blue);">Click to upload photos or drag and drop</div>
            <div style="font-size:.72rem;color:var(--text-muted);">PNG, JPG up to 5MB</div>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn-primary-custom btn" style="justify-content:center;padding:.75rem 1.8rem;border-radius:10px;">
            <i class="fas fa-paper-plane me-2"></i>Submit Complaint
          </button>
          <a href="{{ route('customer.booking.show', $booking) }}" class="btn" style="background:#fff;border:1.5px solid var(--card-border);border-radius:10px;padding:.75rem 1.5rem;font-weight:600;font-size:.88rem;color:var(--text-secondary);">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Right Column: Guidance Card (complaint.jpeg) --}}
  <div class="col-lg-5">
    <div class="card-custom p-4 mb-3" style="background:var(--brand-cream);border:1px solid var(--card-border);">
      <div style="font-size:1.8rem;margin-bottom:.5rem;">🛡️</div>
      <h6 style="font-weight:800;color:var(--brand-deep-blue);margin-bottom:.5rem;">We're Here to Make It Right</h6>
      <p style="font-size:.82rem;color:var(--text-secondary);line-height:1.6;margin-bottom:1rem;">
        Customer safety and job quality are our top priorities. All complaints are managed directly by senior HomeServiceHub dispute resolution agents.
      </p>

      <div class="d-flex flex-column gap-3">
        <div class="d-flex gap-3 align-items-start">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(37,99,235,.12);color:var(--brand-blue);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">1</div>
          <div>
            <div style="font-weight:700;font-size:.85rem;color:var(--brand-deep-blue);">24-Hour Review</div>
            <div style="font-size:.78rem;color:var(--text-secondary);">An agent assesses the complaint and contacts the service professional.</div>
          </div>
        </div>

        <div class="d-flex gap-3 align-items-start">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(232,165,26,.15);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">2</div>
          <div>
            <div style="font-weight:700;font-size:.85rem;color:var(--brand-deep-blue);">Free Re-Work or Refund</div>
            <div style="font-size:.78rem;color:var(--text-secondary);">If work doesn't meet standards, we re-dispatch for free or issue a full refund.</div>
          </div>
        </div>

        <div class="d-flex gap-3 align-items-start">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(16,185,129,.15);color:var(--success);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">3</div>
          <div>
            <div style="font-weight:700;font-size:.85rem;color:var(--brand-deep-blue);">Customer Protection</div>
            <div style="font-size:.78rem;color:var(--text-secondary);">Service professionals with repeated complaints are suspended from the platform.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
