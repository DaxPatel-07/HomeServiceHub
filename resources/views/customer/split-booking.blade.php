@extends('layouts.dashboard')
@section('title','Split Booking')
@section('role-label','Customer Portal')
@section('page-title','Split Booking')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('customer.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('search') }}"><i class="fas fa-search"></i>Search Services</a></div>
<div class="nav-item"><a href="{{ route('customer.bookings') }}"><i class="fas fa-calendar-check"></i>My Bookings</a></div>
<div class="nav-item"><a href="{{ route('customer.split.booking') }}" class="active"><i class="fas fa-layer-group"></i>Split Booking</a></div>
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
  <div class="col-12">
    <div class="booking-steps">
      <div class="step-item active"><div class="step-circle">1</div><div class="step-label">Services</div></div>
      <div class="step-item"><div class="step-circle">2</div><div class="step-label">Providers</div></div>
      <div class="step-item"><div class="step-circle">3</div><div class="step-label">Schedule</div></div>
      <div class="step-item"><div class="step-circle">4</div><div class="step-label">Payment</div></div>
      <div class="step-item"><div class="step-circle">5</div><div class="step-label">Confirm</div></div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card-custom">
      <div class="card-header-custom">
        <h6><i class="fas fa-layer-group me-2" style="color:var(--brand-blue);"></i>Select Services</h6>
      </div>
      <div class="card-body-custom">
        <form method="POST" action="{{ route('customer.split.booking.store') }}" id="splitForm">
          @csrf
          <div class="form-group-custom">
            <label>Booking Date</label>
            <input type="date" name="booking_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
          </div>
          <div class="row g-3">
            <div class="col-6"><div class="form-group-custom"><label>Service Address</label><input type="text" name="address" class="form-control" placeholder="Full address" required></div></div>
            <div class="col-6"><div class="form-group-custom"><label>City</label>
              <select name="city" class="form-control" required>
                <option value="Ahmedabad" {{ (auth()->user()->city ?? '')=='Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                <option value="Gandhinagar" {{ (auth()->user()->city ?? '')=='Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
              </select>
            </div></div>
          </div>

          <div class="divider"></div>
          <div style="font-weight:700;margin-bottom:1rem;">Add Services</div>
          <div id="servicesContainer">
            {{-- Service Row Template --}}
            <div class="service-row" style="background:var(--bg-light);border-radius:12px;padding:1rem;margin-bottom:.75rem;border:1px solid var(--card-border);">
              <div class="d-flex align-items-center gap-2 mb-2">
                <div class="timeline-dot" style="width:32px;height:32px;font-size:.8rem;">1</div>
                <span style="font-weight:600;">Service 1</span>
              </div>
              <div class="row g-2">
                <div class="col-md-5">
                  <select name="services[0][provider_id]" class="form-control provider-select" onchange="updateAmount(this)" required>
                    <option value="">Select Provider</option>
                    @foreach($providers as $p)
                    <option value="{{ $p->id }}" data-amount="{{ $p->hourly_rate }}">{{ $p->name }} ({{ $p->category->name }}) - ₹{{ $p->hourly_rate }}/hr</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3"><input type="time" name="services[0][time]" class="form-control" value="09:00" required></div>
                <div class="col-md-3"><input type="number" name="services[0][amount]" class="form-control amount-input" placeholder="₹ Amount" required></div>
                <div class="col-md-1 d-flex align-items-center"><button type="button" onclick="removeRow(this)" style="background:rgba(239,68,68,.1);border:none;border-radius:8px;padding:.35rem .5rem;color:var(--danger);cursor:pointer;"><i class="fas fa-times"></i></button></div>
              </div>
            </div>
          </div>

          <button type="button" onclick="addServiceRow()" class="btn-outline-custom btn btn-sm w-100 mb-3"><i class="fas fa-plus me-2"></i>Add Another Service</button>

          <div class="form-group-custom">
            <label>Payment Method</label>
            <select name="payment_method" class="form-control">
              <option value="cash">Cash on Delivery</option>
              <option value="card">Credit/Debit Card</option>
              <option value="wallet">Wallet</option>
            </select>
          </div>

          <button type="submit" class="btn-primary-custom btn w-100" style="justify-content:center;padding:.9rem;font-size:1rem;"><i class="fas fa-check-circle me-2"></i>Confirm Split Booking</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Summary Panel --}}
  <div class="col-lg-5">
    <div class="card-custom" style="position:sticky;top:80px;">
      <div class="card-header-custom"><h6><i class="fas fa-receipt me-2" style="color:var(--brand-blue);"></i>Booking Summary</h6></div>
      <div class="card-body-custom">
        <div id="summaryTimeline"></div>
        <div class="divider"></div>
        <div class="d-flex justify-content-between" style="font-size:1rem;font-weight:800;">
          <span>Total Amount</span>
          <span style="color:var(--brand-blue);">₹<span id="totalAmount">0</span></span>
        </div>
        <div style="font-size:.78rem;color:var(--text-secondary);margin-top:.5rem;"><i class="fas fa-info-circle me-1" style="color:var(--brand-blue);"></i>AI has optimized the schedule to minimize your wait time.</div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/split-booking.js') }}"></script>
@endpush
