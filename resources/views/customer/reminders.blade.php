@extends('layouts.dashboard')
@section('title','Maintenance Reminders')
@section('role-label','Customer Portal')
@section('page-title','Recurring Maintenance Reminders')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('customer.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('search') }}"><i class="fas fa-search"></i>Search Services</a></div>
<div class="nav-item"><a href="{{ route('customer.bookings') }}"><i class="fas fa-calendar-check"></i>My Bookings</a></div>
<div class="nav-item"><a href="{{ route('customer.split.booking') }}"><i class="fas fa-layer-group"></i>Split Booking</a></div>
<div class="nav-section-title">Tools</div>
<div class="nav-item"><a href="{{ route('customer.reminders') }}" class="active"><i class="fas fa-bell"></i>Reminders</a></div>
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
  <div class="col-lg-7">
    <div class="card-custom">
      <div class="card-header-custom">
        <h6><i class="fas fa-bell me-2" style="color:var(--brand-blue);"></i>Active Reminders</h6>
      </div>
      <div class="card-body-custom">
        @forelse($reminders as $r)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem;background:var(--bg-light);border-radius:12px;margin-bottom:.75rem;border:1px solid var(--card-border);">
          <div class="d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(37,99,235,.08);width:44px;height:44px;border-radius:12px;"><i class="{{ $r->category->icon }}" style="color:var(--brand-blue);font-size:1.2rem;"></i></div>
            <div>
              <div style="font-weight:700;font-size:.95rem;">{{ $r->title }}</div>
              <div style="font-size:.78rem;color:var(--text-secondary);">{{ $r->category->name }} &bull; Repeats every {{ $r->repeat_months }} months</div>
              <div style="font-size:.8rem;color:{{ $r->isDueSoon() ? 'var(--danger)' : 'var(--success)' }};font-weight:600;margin-top:.2rem;">
                Next Due: {{ $r->next_due_date->format('d M Y') }} ({{ $r->next_due_date->diffForHumans() }})
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <a href="{{ route('search') }}?category={{ $r->category->slug }}" class="btn-primary-custom btn btn-sm" style="padding:.35rem .8rem;font-size:.78rem;border-radius:8px;">
              <i class="fas fa-calendar-plus me-1"></i>Book Now
            </a>
            <form method="POST" action="{{ route('customer.reminders.destroy', $r) }}" onsubmit="return confirm('Delete this reminder?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm" style="color:var(--danger);background:rgba(239,68,68,.1);border:none;border-radius:8px;padding:.4rem .6rem;" title="Delete Reminder"><i class="fas fa-trash"></i></button>
            </form>
          </div>
        </div>
        @empty
        <div class="empty-state"><i class="fas fa-bell-slash"></i><p>No reminders set. Add one below!</p></div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card-custom p-4 mb-4">
      <h6 style="font-weight:700;margin-bottom:1rem;"><i class="fas fa-plus-circle me-2" style="color:var(--brand-blue);"></i>Add New Reminder</h6>
      <form method="POST" action="{{ route('customer.reminders.store') }}">
        @csrf
        <div class="form-group-custom">
          <label>Reminder Title *</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. AC Filter Service, Water Tank Clean" required>
        </div>
        <div class="form-group-custom">
          <label>Service Category *</label>
          <select name="category_id" class="form-control" required>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group-custom">
          <label>Next Due Date *</label>
          <input type="date" name="next_due_date" class="form-control" min="{{ date('Y-m-d') }}" required>
        </div>
        <div class="form-group-custom">
          <label>Repeat Every (Months)</label>
          <select name="repeat_months" class="form-control">
            <option value="3">3 Months</option>
            <option value="6" selected>6 Months (Recommended)</option>
            <option value="12">12 Months (Annual)</option>
          </select>
        </div>
        <div class="form-group-custom">
          <label>Notes (Optional)</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="Model number, previous service notes..."></textarea>
        </div>
        <button type="submit" class="btn-primary-custom btn w-100" style="justify-content:center;"><i class="fas fa-check me-2"></i>Set Reminder</button>
      </form>
    </div>

    <!-- Notification Info Card (Remainder.jpeg) -->
    <div class="card-custom p-4" style="background:var(--brand-cream);border:1px solid var(--card-border);">
      <div class="d-flex align-items-center gap-3 mb-2">
        <div style="width:40px;height:40px;border-radius:10px;background:rgba(37,99,235,.1);display:flex;align-items:center;justify-content:center;color:var(--brand-blue);font-size:1.1rem;">
          <i class="fas fa-shield-alt"></i>
        </div>
        <div>
          <h6 style="font-weight:800;color:var(--brand-deep-blue);margin:0;">Automated Alerts</h6>
          <div style="font-size:.78rem;color:var(--text-secondary);">SMS &amp; Email Notifications</div>
        </div>
      </div>
      <p style="font-size:.82rem;color:var(--text-secondary);margin:0;line-height:1.5;">
        HomeServiceHub automatically sends proactive maintenance reminders 7 days and 1 day before your scheduled due dates so you never miss an essential appliance service.
      </p>
    </div>
  </div>
</div>
@endsection
