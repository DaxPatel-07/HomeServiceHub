@extends('layouts.dashboard')
@section('title','My Profile')
@section('role-label','Customer Portal')
@section('page-title','My Profile')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('customer.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('search') }}"><i class="fas fa-search"></i>Find Services</a></div>
<div class="nav-item"><a href="{{ route('customer.bookings') }}"><i class="fas fa-calendar-check"></i>My Bookings</a></div>
<div class="nav-item"><a href="{{ route('customer.split.booking') }}"><i class="fas fa-layer-group"></i>Split Booking</a></div>
<div class="nav-section-title">Tools</div>
<div class="nav-item"><a href="{{ route('customer.reminders') }}"><i class="fas fa-bell"></i>Reminders</a></div>
<div class="nav-item"><a href="javascript:void(0)" onclick="toggleFloatingChat()"><i class="fas fa-robot"></i>AI Assistant</a></div>
<div class="nav-section-title">Account</div>
<div class="nav-item"><a href="{{ route('customer.profile') }}" class="active"><i class="fas fa-user-circle"></i>My Profile</a></div>
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
  {{-- Profile Card --}}
  <div class="col-lg-4">
    <div class="card-custom p-4 text-center">
      <div class="profile-avatar-wrap mx-auto mb-3">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <h5 style="font-weight:800;margin:0;">{{ auth()->user()->name }}</h5>
      <div style="font-size:.85rem;color:var(--text-secondary);margin:.25rem 0 1rem;">Customer Account</div>

      <div class="divider"></div>

      <div class="text-start">
        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.85rem;">
          <span style="color:var(--text-muted);"><i class="fas fa-envelope me-2" style="color:var(--primary);"></i>Email</span>
          <span style="font-weight:600;">{{ auth()->user()->email }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.85rem;">
          <span style="color:var(--text-muted);"><i class="fas fa-phone me-2" style="color:var(--primary);"></i>Phone</span>
          <span style="font-weight:600;">{{ auth()->user()->phone ?: 'Not set' }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.85rem;">
          <span style="color:var(--text-muted);"><i class="fas fa-map-marker-alt me-2" style="color:var(--primary);"></i>City</span>
          <span style="font-weight:600;">{{ auth()->user()->city ?: 'Not set' }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center" style="font-size:.85rem;">
          <span style="color:var(--text-muted);"><i class="fas fa-calendar me-2" style="color:var(--primary);"></i>Joined</span>
          <span style="font-weight:600;">{{ auth()->user()->created_at->format('M Y') }}</span>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Stats --}}
      <div class="row g-2">
        <div class="col-4">
          <div style="background:var(--bg-light);border-radius:10px;padding:.75rem .5rem;text-align:center;">
            <div style="font-size:1.3rem;font-weight:900;color:var(--primary);">{{ $stats['total'] }}</div>
            <div style="font-size:.68rem;color:var(--text-muted);font-weight:600;">Total</div>
          </div>
        </div>
        <div class="col-4">
          <div style="background:var(--bg-light);border-radius:10px;padding:.75rem .5rem;text-align:center;">
            <div style="font-size:1.3rem;font-weight:900;color:var(--success);">{{ $stats['completed'] }}</div>
            <div style="font-size:.68rem;color:var(--text-muted);font-weight:600;">Done</div>
          </div>
        </div>
        <div class="col-4">
          <div style="background:var(--bg-light);border-radius:10px;padding:.75rem .5rem;text-align:center;">
            <div style="font-size:1.1rem;font-weight:900;color:var(--dark);">₹{{ number_format($stats['spent'],0) }}</div>
            <div style="font-size:.68rem;color:var(--text-muted);font-weight:600;">Spent</div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <form method="POST" action="{{ route('logout') }}" class="m-0">
        @csrf
        <button type="submit" class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2" style="background:rgba(239,68,68,.08);color:#EF4444;border:1px solid rgba(239,68,68,.25);border-radius:10px;padding:.6rem;font-weight:700;font-size:.85rem;transition:var(--transition);" onmouseover="this.style.background='#EF4444';this.style.color='#fff';" onmouseout="this.style.background='rgba(239,68,68,.08)';this.style.color='#EF4444';">
          <i class="fas fa-sign-out-alt"></i>Logout from Account
        </button>
      </form>
    </div>
  </div>

  {{-- Edit Forms --}}
  <div class="col-lg-8">
    {{-- Personal Info --}}
    <div class="card-custom p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 style="font-weight:800;margin:0;"><i class="fas fa-user-edit me-2" style="color:var(--brand-blue);"></i>Personal Information</h6>
        <span class="badge-pill" style="background:rgba(16,185,129,.1);color:var(--success);border:1px solid rgba(16,185,129,.2);">
          <i class="fas fa-check-circle me-1"></i>Verified Profile
        </span>
      </div>
      <form method="POST" action="{{ route('customer.profile.update') }}">
        @csrf @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>Full Name *</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>Email Address *</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>Phone Number</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+91 9876543210">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>City</label>
              <select name="city" class="form-control">
                <option value="Ahmedabad" {{ old('city', auth()->user()->city)=='Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                <option value="Gandhinagar" {{ old('city', auth()->user()->city)=='Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group-custom">
              <label>Default Service Address</label>
              <textarea name="address" class="form-control" rows="2" placeholder="House no, Street, Area...">{{ old('address', auth()->user()->address) }}</textarea>
            </div>
          </div>
        </div>
        @if($errors->any())
          <div class="alert-custom alert-error-custom mt-2">{{ $errors->first() }}</div>
        @endif
        <button type="submit" class="btn-primary-custom btn mt-3" style="justify-content:center;padding:.65rem 1.8rem;"><i class="fas fa-save me-2"></i>Save Changes</button>
      </form>
    </div>

    {{-- Saved Addresses Card (Customer Profile.jpeg) --}}
    <div class="card-custom p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 style="font-weight:800;margin:0;"><i class="fas fa-map-marked-alt me-2" style="color:var(--brand-blue);"></i>Saved Addresses</h6>
        <button type="button" class="btn btn-sm" style="background:rgba(37,99,235,.08);color:var(--brand-blue);border:1px solid rgba(37,99,235,.2);border-radius:8px;font-weight:700;font-size:.78rem;">
          <i class="fas fa-plus me-1"></i>Add Address
        </button>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <div style="background:var(--brand-cream);border:1.5px solid var(--brand-blue);border-radius:12px;padding:1rem;position:relative;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span style="font-weight:700;font-size:.85rem;color:var(--brand-deep-blue);"><i class="fas fa-home me-1" style="color:var(--brand-blue);"></i>Home</span>
              <span style="background:var(--brand-blue);color:#fff;border-radius:10px;padding:.1rem .5rem;font-size:.65rem;font-weight:700;">DEFAULT</span>
            </div>
            <p style="font-size:.8rem;color:var(--text-secondary);margin:0;line-height:1.4;">{{ auth()->user()->address ?: 'B-402, Shivalik Heights, Bodakdev' }}</p>
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:.3rem;"><i class="fas fa-city me-1"></i>{{ auth()->user()->city ?: 'Ahmedabad' }}</div>
          </div>
        </div>
        <div class="col-md-6">
          <div style="background:#FFFFFF;border:1px solid var(--card-border);border-radius:12px;padding:1rem;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span style="font-weight:700;font-size:.85rem;color:var(--brand-deep-blue);"><i class="fas fa-building me-1" style="color:var(--teal);"></i>Office</span>
              <span style="color:var(--text-muted);font-size:.72rem;">Secondary</span>
            </div>
            <p style="font-size:.8rem;color:var(--text-secondary);margin:0;line-height:1.4;">Infocity Tower 2, Ground Floor, Sector 0</p>
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:.3rem;"><i class="fas fa-city me-1"></i>Gandhinagar</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Change Password --}}
    <div class="card-custom p-4 mb-4">
      <h6 style="font-weight:800;margin-bottom:1.25rem;"><i class="fas fa-lock me-2" style="color:var(--gold);"></i>Password &amp; Security</h6>
      <form method="POST" action="{{ route('customer.password.update') }}">
        @csrf @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <div class="form-group-custom">
              <label>Current Password *</label>
              <div class="input-icon-wrap has-toggle">
                <input type="password" name="current_password" id="cpCurrent" class="form-control" required style="padding-left:.9rem;">
                <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('cpCurrent', this)" title="Show/Hide Password"></i>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group-custom">
              <label>New Password *</label>
              <div class="input-icon-wrap has-toggle">
                <input type="password" name="password" id="cpNew" class="form-control" required minlength="8" style="padding-left:.9rem;">
                <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('cpNew', this)" title="Show/Hide Password"></i>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group-custom">
              <label>Confirm Password *</label>
              <div class="input-icon-wrap has-toggle">
                <input type="password" name="password_confirmation" id="cpConfirm" class="form-control" required style="padding-left:.9rem;">
                <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('cpConfirm', this)" title="Show/Hide Password"></i>
              </div>
            </div>
          </div>
        </div>
        <button type="submit" class="btn mt-3" style="background:rgba(232,165,26,.12);color:var(--primary-dark);border:1px solid rgba(232,165,26,.3);border-radius:10px;padding:.6rem 1.5rem;font-weight:700;font-size:.85rem;"><i class="fas fa-key me-2"></i>Update Password</button>
      </form>
    </div>

    {{-- Notification Preferences Card (Customer Profile.jpeg) --}}
    <div class="card-custom p-4">
      <h6 style="font-weight:800;margin-bottom:1.25rem;"><i class="fas fa-bell me-2" style="color:var(--teal);"></i>Notification Preferences</h6>
      <div class="d-flex flex-column gap-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div style="font-weight:700;font-size:.88rem;color:var(--brand-deep-blue);">Booking Status Updates</div>
            <div style="font-size:.78rem;color:var(--text-secondary);">Receive real-time notifications when a pro accepts or arrives</div>
          </div>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" checked style="cursor:pointer;width:2.5rem;height:1.25rem;">
          </div>
        </div>
        <div class="divider m-0"></div>
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div style="font-weight:700;font-size:.88rem;color:var(--brand-deep-blue);">Maintenance Reminders</div>
            <div style="font-size:.78rem;color:var(--text-secondary);">Automated alerts 7 days before recurring service due dates</div>
          </div>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" checked style="cursor:pointer;width:2.5rem;height:1.25rem;">
          </div>
        </div>
        <div class="divider m-0"></div>
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div style="font-weight:700;font-size:.88rem;color:var(--brand-deep-blue);">Special Offers &amp; Discounts</div>
            <div style="font-size:.78rem;color:var(--text-secondary);">Exclusive seasonal deals on home repairs and cleaning</div>
          </div>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" style="cursor:pointer;width:2.5rem;height:1.25rem;">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
