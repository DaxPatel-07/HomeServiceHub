@extends('layouts.app')
@section('title', 'Become a Service Provider - Home Service Hub')
@section('meta_description', 'Join Home Service Hub as a verified professional in Ahmedabad and Gandhinagar. Earn more with flexible bookings.')

@section('content')
<div style="background:linear-gradient(135deg, var(--brand-cream) 0%, var(--brand-beige) 100%); padding:3.5rem 0 2.5rem; border-bottom:1px solid var(--card-border);">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-7">
        <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.2);border-radius:20px;padding:.35rem .9rem;margin-bottom:1rem;">
          <i class="fas fa-hard-hat" style="color:var(--brand-blue);"></i>
          <span style="color:var(--brand-blue);font-size:.82rem;font-weight:700;">Service Partner Onboarding</span>
        </div>
        <h1 style="font-size:2.4rem;font-weight:900;line-height:1.2;margin-bottom:1rem;color:var(--brand-deep-blue);">
          Grow Your Business as a <br><span style="color:var(--brand-blue);">Verified Home Service Pro</span>
        </h1>
        <p style="color:var(--text-secondary);font-size:1.02rem;line-height:1.6;margin-bottom:1.5rem;max-width:580px;">
          Join the trusted network across Ahmedabad &amp; Gandhinagar. Get direct bookings from verified homeowners, set your own hourly rates, and build your local reputation with community badges.
        </p>
        <div class="d-flex flex-wrap gap-4">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-check-circle" style="color:var(--success);font-size:1.1rem;"></i>
            <span style="font-size:.9rem;font-weight:700;color:var(--brand-deep-blue);">100% Free Registration</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-check-circle" style="color:var(--success);font-size:1.1rem;"></i>
            <span style="font-size:.9rem;font-weight:700;color:var(--brand-deep-blue);">Direct Customer Bookings</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-check-circle" style="color:var(--success);font-size:1.1rem;"></i>
            <span style="font-size:.9rem;font-weight:700;color:var(--brand-deep-blue);">Fast Admin Verification</span>
          </div>
        </div>
      </div>
      <div class="col-lg-5 text-center d-none d-lg-block">
        <div style="background:#FFFFFF;border:1px solid var(--card-border);border-radius:20px;padding:2rem;box-shadow:0 8px 30px rgba(23,74,139,.08);">
          <div style="font-size:3rem;margin-bottom:.5rem;">🏡 ⭐ 🛠️</div>
          <h5 style="color:var(--brand-deep-blue);font-weight:800;margin-bottom:.5rem;">High Demand Trade Opportunities</h5>
          <p style="color:var(--text-secondary);font-size:.85rem;margin-bottom:1rem;">Electricians, Plumbers, AC Techs, Carpenters, Painters &amp; Cleaners in high demand daily.</p>
          <div class="d-flex justify-content-center gap-2 flex-wrap">
            <span class="badge" style="background:rgba(232,165,26,.15);color:var(--gold);font-size:.78rem;padding:.4rem .8rem;font-weight:700;">⚡ Electrician</span>
            <span class="badge" style="background:rgba(37,99,235,.12);color:var(--brand-blue);font-size:.78rem;padding:.4rem .8rem;font-weight:700;">🔧 Plumber</span>
            <span class="badge" style="background:rgba(60,155,145,.15);color:var(--teal);font-size:.78rem;padding:.4rem .8rem;font-weight:700;">❄️ AC Tech</span>
            <span class="badge" style="background:rgba(236,72,153,.15);color:var(--pink);font-size:.78rem;padding:.4rem .8rem;font-weight:700;">🎨 Painter</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding:3rem 0;">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
      <div class="card-custom p-4 p-md-5" style="box-shadow:0 12px 36px rgba(23,74,139,.08);border:1px solid var(--card-border);">
        <div class="text-center mb-4">
          <div style="width:60px;height:60px;border-radius:16px;background:rgba(37,99,235,.1);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
            <i class="fas fa-id-card" style="font-size:1.8rem;color:var(--brand-blue);"></i>
          </div>
          <h2 style="font-size:1.8rem;font-weight:900;color:var(--brand-deep-blue);margin-bottom:.4rem;">Provider Application &amp; Onboarding</h2>
          <p style="color:var(--text-secondary);font-size:.92rem;">Fill out your business profile below to get registered and start accepting jobs.</p>
        </div>

        @if($errors->any())
          <div class="alert-custom alert-error-custom mb-4">
            <i class="fas fa-exclamation-circle"></i>
            <div>
              <strong>Please fix the following issues:</strong>
              <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('become-provider.post') }}" id="providerOnboardingForm">
          @csrf
          <input type="hidden" name="role" value="provider">

          <h5 style="font-weight:800;color:var(--brand-deep-blue);border-bottom:2px solid var(--bg-light);padding-bottom:.5rem;margin-bottom:1.25rem;">
            <i class="fas fa-user-circle me-2" style="color:var(--brand-blue);"></i>1. Personal &amp; Account Details
          </h5>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Full Name <span class="text-danger">*</span></label>
                <div class="input-icon-wrap">
                  <i class="fas fa-user input-icon"></i>
                  <input type="text" name="name" class="form-control" placeholder="e.g. Ramesh Patel" value="{{ old('name') }}" required>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Email Address <span class="text-danger">*</span></label>
                <div class="input-icon-wrap">
                  <i class="fas fa-envelope input-icon"></i>
                  <input type="email" name="email" class="form-control" placeholder="ramesh@example.com" value="{{ old('email') }}" required>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Mobile Phone <span class="text-danger">*</span></label>
                <div class="input-icon-wrap">
                  <i class="fas fa-phone input-icon"></i>
                  <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210" value="{{ old('phone') }}" required>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>City (Gujarat) <span class="text-danger">*</span></label>
                <select name="city" class="form-control" required>
                  <option value="Ahmedabad" {{ old('city')=='Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                  <option value="Gandhinagar" {{ old('city')=='Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Create Password <span class="text-danger">*</span></label>
                <div class="input-icon-wrap has-toggle">
                  <i class="fas fa-lock input-icon"></i>
                  <input type="password" name="password" id="bpPassword" class="form-control" placeholder="Min. 8 characters" required>
                  <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('bpPassword', this)" title="Show/Hide Password"></i>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Confirm Password <span class="text-danger">*</span></label>
                <div class="input-icon-wrap has-toggle">
                  <i class="fas fa-lock input-icon"></i>
                  <input type="password" name="password_confirmation" id="bpPasswordConfirm" class="form-control" placeholder="Repeat password" required>
                  <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('bpPasswordConfirm', this)" title="Show/Hide Password"></i>
                </div>
              </div>
            </div>
          </div>

          <h5 style="font-weight:800;color:var(--brand-deep-blue);border-bottom:2px solid var(--bg-light);padding-bottom:.5rem;margin-bottom:1.25rem;">
            <i class="fas fa-briefcase me-2" style="color:var(--brand-blue);"></i>2. Professional &amp; Service Profile
          </h5>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Service Category <span class="text-danger">*</span></label>
                <select name="category_id" class="form-control" required>
                  <option value="">Select your main trade...</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Business / Brand Name</label>
                <input type="text" name="business_name" class="form-control" placeholder="e.g. Patel Electrical Services" value="{{ old('business_name') }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Years of Experience <span class="text-danger">*</span></label>
                <input type="number" name="experience_years" class="form-control" placeholder="e.g. 5" min="0" max="50" value="{{ old('experience_years', 3) }}" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group-custom">
                <label>Standard Hourly Rate (₹) <span class="text-danger">*</span></label>
                <input type="number" name="hourly_rate" class="form-control" placeholder="e.g. 450" min="50" max="10000" value="{{ old('hourly_rate', 450) }}" required>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group-custom">
                <label>Specific Service Areas / Localities <span class="text-danger">*</span></label>
                <input type="text" name="service_area" class="form-control" placeholder="e.g. SG Highway, Satellite, Bopal, Navrangpura, Vastrapur" value="{{ old('service_area') }}" required>
                <small class="text-muted" style="font-size:.78rem;">List localities or sectors in Ahmedabad/Gandhinagar where you provide on-site services.</small>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group-custom">
                <label>Professional Bio / Summary</label>
                <textarea name="bio" class="form-control" rows="3" placeholder="Explain your skills, tools, certifications, or past project experience to attract customers...">{{ old('bio') }}</textarea>
              </div>
            </div>
          </div>

          <div style="background:var(--brand-cream);border-radius:12px;padding:1rem;margin-bottom:1.5rem;border:1px solid var(--brand-beige);">
            <div class="d-flex align-items-start gap-2">
              <i class="fas fa-info-circle text-primary mt-1" style="color:var(--brand-blue) !important;"></i>
              <div style="font-size:.82rem;color:var(--text-secondary);">
                By submitting this form, you register as a provider on HomeServiceHub. Your profile will be created with <strong>Verification: Pending</strong>. You can immediately access your provider dashboard, manage your calendar, and complete profile verification.
              </div>
            </div>
          </div>

          <button type="submit" class="btn-primary-custom btn w-100" style="justify-content:center;padding:.85rem;font-size:1.05rem;">
            <i class="fas fa-check-circle me-2"></i>Complete Registration &amp; Enter Dashboard
          </button>
        </form>

        <p class="text-center mt-4 mb-0" style="font-size:.88rem;color:var(--text-secondary);">
          Already have a provider account? <a href="{{ route('login') }}" style="color:var(--brand-blue);font-weight:700;">Login here</a>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
function togglePasswordVisibility(inputId, icon) {
  const input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
</script>
@endpush
