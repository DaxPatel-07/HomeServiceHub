@extends('layouts.dashboard')
@section('title','My Profile')
@section('role-label','Provider Portal')
@section('page-title','My Profile')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('provider.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('provider.bookings') }}"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-item"><a href="{{ route('provider.profile') }}" class="active"><i class="fas fa-user-cog"></i>My Profile</a></div>
<div class="nav-section-title">Public View</div>
<div class="nav-item"><a href="{{ route('provider.show', $provider) }}"><i class="fas fa-external-link-alt"></i>Public Profile</a></div>
@endsection

@section('content')
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card-custom p-4">
      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
          <h5 style="font-weight:900;color:var(--brand-deep-blue);margin:0;">Edit Provider Profile</h5>
          <p style="color:var(--text-secondary);font-size:.85rem;margin:.2rem 0 0;">Update your trade details, hourly rate, and service areas across Gujarat.</p>
        </div>
        <span class="badge-pill badge-status-{{ $provider->verification_status === 'verified' ? 'completed' : 'pending' }}">
          <i class="fas {{ $provider->verification_status === 'verified' ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
          {{ ucfirst($provider->verification_status) }}
        </span>
      </div>

      @if($errors->any())
        <div class="alert-custom alert-error-custom mb-3"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
      @endif
      @if(session('success'))
        <div class="alert-custom alert-success-custom mb-3"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
      @endif

      <form method="POST" action="{{ route('provider.profile.update') }}">
        @csrf @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>Full Name</label>
              <div class="input-icon-wrap">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>Phone Number</label>
              <div class="input-icon-wrap">
                <i class="fas fa-phone input-icon"></i>
                <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}" required>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group-custom">
              <label>Business / Brand Name</label>
              <div class="input-icon-wrap">
                <i class="fas fa-store input-icon"></i>
                <input type="text" name="business_name" class="form-control" value="{{ $provider->business_name }}" placeholder="e.g. Apex Electrical Solutions">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>Service Category</label>
              <select name="category_id" class="form-control">
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ $provider->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>Experience (years)</label>
              <div class="input-icon-wrap">
                <i class="fas fa-briefcase input-icon"></i>
                <input type="number" name="experience_years" class="form-control" value="{{ $provider->experience_years }}" min="0">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>Hourly Rate (₹)</label>
              <div class="input-icon-wrap">
                <i class="fas fa-rupee-sign input-icon"></i>
                <input type="number" name="hourly_rate" class="form-control" value="{{ $provider->hourly_rate }}" required>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label>City (Gujarat)</label>
              <select name="city" class="form-control">
                <option value="Ahmedabad" {{ auth()->user()->city=='Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                <option value="Gandhinagar" {{ auth()->user()->city=='Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group-custom">
              <label>Service Area &amp; Localities</label>
              <div class="input-icon-wrap">
                <i class="fas fa-map-marker-alt input-icon"></i>
                <input type="text" name="service_area" class="form-control" value="{{ $provider->service_area }}" placeholder="e.g. Ahmedabad, SG Highway, Satellite OR Gandhinagar, Sector 21" required>
              </div>
              <small class="text-muted" style="font-size:.78rem;">Localities where you provide rapid 60-minute service visits.</small>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group-custom">
              <label>Professional Bio / Summary</label>
              <textarea name="bio" class="form-control" rows="4" placeholder="Describe your experience, certifications, and customer guarantees...">{{ $provider->bio }}</textarea>
            </div>
          </div>
        </div>
        <button type="submit" class="btn-primary-custom btn mt-3" style="justify-content:center;padding:.75rem 1.5rem;">
          <i class="fas fa-save me-2"></i>Save Profile Changes
        </button>
      </form>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card-custom p-4 text-center">
      <div class="provider-avatar mx-auto mb-3" style="width:84px;height:84px;font-size:2.2rem;background:linear-gradient(135deg, var(--color-primary), var(--color-support-blue));color:#ffffff;border:3px solid var(--color-blue-tint);box-shadow:0 6px 18px rgba(36,81,189,0.2);">
        {{ strtoupper(substr($provider->name,0,1)) }}
      </div>
      <h5 style="font-weight:900;color:var(--brand-deep-blue);margin-bottom:.2rem;">{{ $provider->name }}</h5>
      <div style="color:var(--text-secondary);font-size:.88rem;font-weight:600;">
        <i class="{{ $provider->category->icon ?? 'fas fa-tools' }} me-1" style="color:var(--brand-blue);"></i>{{ $provider->category->name }}
      </div>
      
      <div class="divider" style="margin:1.25rem 0;"></div>
      
      <div class="d-flex justify-content-between align-items-center py-2" style="font-size:.85rem;border-bottom:1px solid var(--card-border);">
        <span style="color:var(--text-secondary);">Skill Score</span>
        <span style="font-weight:800;color:var(--brand-blue);">{{ round($provider->skill_score) }} / 100</span>
      </div>
      <div class="d-flex justify-content-between align-items-center py-2" style="font-size:.85rem;border-bottom:1px solid var(--card-border);">
        <span style="color:var(--text-secondary);">Customer Rating</span>
        <span style="font-weight:800;color:var(--gold);">{{ $provider->average_rating }} ★</span>
      </div>
      <div class="d-flex justify-content-between align-items-center py-2" style="font-size:.85rem;border-bottom:1px solid var(--card-border);">
        <span style="color:var(--text-secondary);">Jobs Completed</span>
        <span style="font-weight:800;color:var(--brand-deep-blue);">{{ $provider->completed_jobs }}</span>
      </div>
      <div class="d-flex justify-content-between align-items-center py-2" style="font-size:.85rem;">
        <span style="color:var(--text-secondary);">Verification</span>
        <span class="badge-pill badge-status-{{ $provider->verification_status === 'verified' ? 'completed' : 'pending' }}">
          {{ ucfirst($provider->verification_status) }}
        </span>
      </div>

      <div class="divider" style="margin:1.25rem 0;"></div>

      <div class="text-start">
        <label style="font-size:.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:.5rem;">Earned Badges</label>
        <div class="d-flex flex-wrap gap-1">
          @forelse($provider->badges as $badge)
            <span class="badge-pill" style="background:rgba(232,165,26,.12);color:var(--gold);font-size:.75rem;">
              <i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}
            </span>
          @empty
            <span style="font-size:.78rem;color:var(--text-muted);">No badges earned yet. Complete more jobs with 5-star ratings to unlock badges!</span>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
