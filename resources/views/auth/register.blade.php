@extends('layouts.auth')
@section('title','Register')
@section('content')
<div class="auth-page">
  <div style="width:100%;max-width:980px;">
    <div class="text-center mb-4">
      <a href="{{ route('home') }}" style="font-size:1.5rem;font-weight:900;color:var(--brand-deep-blue);text-decoration:none;">
        <i class="fas fa-tools me-2" style="color:var(--brand-blue);"></i>Home<span style="color:var(--brand-blue)">Service</span>Hub
      </a>
    </div>

    <div class="row g-4 align-items-stretch">
      {{-- Left Side Illustration & Benefits --}}
      <div class="col-lg-5 d-none d-lg-block">
        <div style="background:linear-gradient(135deg, #FFFDF8 0%, var(--brand-beige) 100%);border-radius:var(--border-radius-lg);padding:2.5rem 2rem;border:1px solid var(--card-border);box-shadow:0 10px 30px rgba(23,74,139,.06);height:100%;display:flex;flex-direction:column;justify-content:space-between;">
          <div>
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(37,99,235,.08);color:var(--brand-blue);border-radius:20px;padding:.3rem .9rem;font-size:.78rem;font-weight:700;margin-bottom:1.5rem;">
              <i class="fas fa-user-plus"></i> Join The Community
            </div>
            <h3 style="font-weight:800;color:var(--brand-deep-blue);line-height:1.2;font-size:1.6rem;margin-bottom:.85rem;">
              Your Reliable Home Services Network
            </h3>
            <p style="color:var(--text-secondary);font-size:.9rem;line-height:1.6;">
              Create your account in under a minute to book top-tier local pros or build your service business with direct local client requests.
            </p>
          </div>

          <!-- Feature checkmarks -->
          <div style="display:flex;flex-direction:column;gap:.9rem;margin:2rem 0;">
            <div class="d-flex align-items-center gap-2">
              <div style="width:26px;height:26px;border-radius:50%;background:rgba(16,185,129,.15);display:flex;align-items:center;justify-content:center;color:var(--success);font-size:.75rem;"><i class="fas fa-check"></i></div>
              <span style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">100% Background-Verified Specialists</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div style="width:26px;height:26px;border-radius:50%;background:rgba(37,99,235,.15);display:flex;align-items:center;justify-content:center;color:var(--brand-blue);font-size:.75rem;"><i class="fas fa-check"></i></div>
              <span style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">Upfront Pricing &amp; No Hidden Fees</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div style="width:26px;height:26px;border-radius:50%;background:rgba(232,165,26,.18);display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:.75rem;"><i class="fas fa-check"></i></div>
              <span style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">Multi-Trade Split Booking Scheduling</span>
            </div>
          </div>

          <div style="background:#FFFFFF;border-radius:12px;padding:.85rem 1rem;border:1px solid var(--card-border);display:flex;align-items:center;gap:.75rem;">
            <div style="font-size:1.6rem;">🪴</div>
            <div style="font-size:.78rem;font-weight:700;color:var(--brand-deep-blue);line-height:1.3;">
              "Better Services, Happier Homes ♡"<br><span style="font-size:.7rem;color:var(--text-muted);font-weight:500;">Trusted across Ahmedabad &amp; Gandhinagar</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Right Side Form Card --}}
      <div class="col-lg-7">
        <div class="auth-card" style="max-width:100%;">
          <h1 class="auth-title">Create Your Account 🚀</h1>
          <p class="auth-subtitle mb-4">Join as a homeowner or verified service professional</p>

          {{-- Step indicators --}}
          <div class="booking-steps mb-4">
            <div class="step-item active" id="step-ind-1"><div class="step-circle">1</div><div class="step-label">Role</div></div>
            <div class="step-item" id="step-ind-2"><div class="step-circle">2</div><div class="step-label">Details</div></div>
            <div class="step-item" id="step-ind-3"><div class="step-circle">3</div><div class="step-label">Profile</div></div>
            <div class="step-item" id="step-ind-4"><div class="step-circle">4</div><div class="step-label">Done</div></div>
          </div>

          @if($errors->any())
            <div class="alert-custom alert-error-custom mb-3"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div>
          @endif

          <form method="POST" action="{{ route('register.post') }}" id="registerForm">
            @csrf

            {{-- Step 1: Role --}}
            <div id="regStep1">
              <p style="font-weight:700;color:var(--brand-deep-blue);margin-bottom:.85rem;font-size:.9rem;">Select account type:</p>
              <div class="row g-3">
                <div class="col-6">
                  <div class="role-card" id="roleCustomer" onclick="selectRole('customer')" style="background:rgba(37,99,235,.05);border:2px solid var(--brand-blue);border-radius:14px;padding:1.4rem 1rem;text-align:center;cursor:pointer;transition:.2s;">
                    <div style="width:48px;height:48px;border-radius:12px;background:rgba(37,99,235,.1);display:inline-flex;align-items:center;justify-content:center;margin-bottom:.6rem;">
                      <i class="fas fa-user" style="font-size:1.3rem;color:var(--brand-blue);"></i>
                    </div>
                    <div style="font-weight:700;color:var(--brand-deep-blue);font-size:.92rem;">Customer</div>
                    <div style="font-size:.75rem;color:var(--text-secondary);margin-top:.2rem;">Book services for your home</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="role-card" id="roleProvider" onclick="selectRole('provider')" style="background:var(--bg-light);border:2px solid var(--card-border);border-radius:14px;padding:1.4rem 1rem;text-align:center;cursor:pointer;transition:.2s;">
                    <div style="width:48px;height:48px;border-radius:12px;background:rgba(232,165,26,.12);display:inline-flex;align-items:center;justify-content:center;margin-bottom:.6rem;">
                      <i class="fas fa-hard-hat" style="font-size:1.3rem;color:var(--gold);"></i>
                    </div>
                    <div style="font-weight:700;color:var(--brand-deep-blue);font-size:.92rem;">Service Pro</div>
                    <div style="font-size:.75rem;color:var(--text-secondary);margin-top:.2rem;">Offer services &amp; get clients</div>
                  </div>
                </div>
              </div>
              <input type="hidden" name="role" id="selectedRole" value="customer" required>
              <button type="button" class="btn-primary-custom btn w-100 mt-4" style="justify-content:center;padding:.75rem;" onclick="nextStep(1)">
                Continue <i class="fas fa-arrow-right ms-2"></i>
              </button>
            </div>

            {{-- Step 2: Personal Details --}}
            <div id="regStep2" style="display:none;">
              <div class="row g-3">
                <div class="col-12">
                  <div class="form-group-custom">
                    <label>Full Name *</label>
                    <div class="input-icon-wrap">
                      <i class="fas fa-user input-icon"></i>
                      <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group-custom">
                    <label>Email Address *</label>
                    <div class="input-icon-wrap">
                      <i class="fas fa-envelope input-icon"></i>
                      <input type="email" name="email" class="form-control" placeholder="your@email.com" value="{{ old('email') }}" required>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group-custom">
                    <label>Password *</label>
                    <div class="input-icon-wrap has-toggle">
                      <i class="fas fa-lock input-icon"></i>
                      <input type="password" name="password" id="regPassword" class="form-control" placeholder="Min 8 chars" required>
                      <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('regPassword', this)" title="Show/Hide Password"></i>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group-custom">
                    <label>Confirm Password *</label>
                    <div class="input-icon-wrap has-toggle">
                      <i class="fas fa-lock input-icon"></i>
                      <input type="password" name="password_confirmation" id="regPasswordConfirm" class="form-control" placeholder="Repeat password" required>
                      <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('regPasswordConfirm', this)" title="Show/Hide Password"></i>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group-custom">
                    <label>Phone *</label>
                    <div class="input-icon-wrap">
                      <i class="fas fa-phone input-icon"></i>
                      <input type="text" name="phone" class="form-control" placeholder="+91 9999999999" value="{{ old('phone') }}" required>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group-custom">
                    <label>City (Gujarat) *</label>
                    <select name="city" class="form-control" required>
                      <option value="Ahmedabad" {{ old('city')=='Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                      <option value="Gandhinagar" {{ old('city')=='Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn-outline-custom btn" onclick="prevStep(2)"><i class="fas fa-arrow-left me-2"></i>Back</button>
                <button type="button" class="btn-primary-custom btn flex-grow-1" style="justify-content:center;" onclick="nextStep(2)">Next <i class="fas fa-arrow-right ms-2"></i></button>
              </div>
            </div>

            {{-- Step 3: Additional Info --}}
            <div id="regStep3" style="display:none;">
              <div id="customerExtra">
                <div class="form-group-custom">
                  <label>Service Address / Home Locality</label>
                  <div class="input-icon-wrap">
                    <i class="fas fa-map-marker-alt input-icon"></i>
                    <input type="text" name="address" class="form-control" placeholder="123, Street Name, SG Highway" value="{{ old('address') }}">
                  </div>
                </div>
              </div>
              <div id="providerExtra" style="display:none;">
                <div class="row g-3">
                  <div class="col-12">
                    <div class="form-group-custom">
                      <label>Business / Trade Name</label>
                      <input type="text" name="business_name" class="form-control" placeholder="e.g. Apex Electrical Solutions">
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group-custom">
                      <label>Primary Service Category *</label>
                      <select name="category_id" class="form-control">
                        @foreach($categories as $cat)
                          <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group-custom">
                      <label>Experience (years)</label>
                      <input type="number" name="experience_years" class="form-control" placeholder="3" min="0">
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group-custom">
                      <label>Hourly Rate (₹)</label>
                      <input type="number" name="hourly_rate" class="form-control" placeholder="499" min="0">
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group-custom">
                      <label>Professional Bio / Specialties</label>
                      <textarea name="bio" class="form-control" rows="3" placeholder="Describe your experience, certifications, and areas covered..."></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn-outline-custom btn" onclick="prevStep(3)"><i class="fas fa-arrow-left me-2"></i>Back</button>
                <button type="submit" class="btn-primary-custom btn flex-grow-1" style="justify-content:center;"><i class="fas fa-check me-2"></i>Complete Registration</button>
              </div>
            </div>
          </form>

          <p style="text-align:center;margin-top:1.5rem;font-size:.88rem;color:var(--text-secondary);">
            Already have an account? <a href="{{ route('login') }}" style="color:var(--brand-blue);font-weight:700;">Login</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
let currentStep = 1;
function selectRole(role) {
  document.getElementById('selectedRole').value = role;
  document.querySelectorAll('.role-card').forEach(c => { 
    c.style.borderColor='var(--card-border)'; 
    c.style.background='var(--bg-light)'; 
  });
  const el = document.getElementById('role' + role.charAt(0).toUpperCase() + role.slice(1));
  if(el){ 
    el.style.borderColor='var(--brand-blue)'; 
    el.style.background='rgba(37,99,235,.05)'; 
  }
}
function nextStep(from){
  document.getElementById('regStep'+from).style.display='none';
  document.getElementById('regStep'+(from+1)).style.display='block';
  document.querySelectorAll('[id^=step-ind-]').forEach((el,i) => {
    el.classList.remove('active','done');
    if(i < from) el.classList.add('done');
    else if(i === from) el.classList.add('active');
  });
  if(from+1 === 3){
    const role = document.getElementById('selectedRole').value;
    document.getElementById('customerExtra').style.display = role==='customer'?'block':'none';
    document.getElementById('providerExtra').style.display = role==='provider'?'block':'none';
  }
}
function prevStep(from){
  document.getElementById('regStep'+from).style.display='none';
  document.getElementById('regStep'+(from-1)).style.display='block';
  document.querySelectorAll('[id^=step-ind-]').forEach((el,i) => {
    el.classList.remove('active','done');
    if(i < (from-2)) el.classList.add('done');
    else if(i === (from-2)) el.classList.add('active');
  });
}
const urlParams = new URLSearchParams(window.location.search);
const preselect = urlParams.get('role');
if (preselect === 'provider') {
  selectRole('provider');
  document.getElementById('selectedRole').value = 'provider';
  document.getElementById('regStep1').style.display = 'none';
  document.getElementById('regStep2').style.display = 'block';
  document.getElementById('step-ind-1').classList.remove('active');
  document.getElementById('step-ind-1').classList.add('done');
  document.getElementById('step-ind-2').classList.add('active');
} else {
  selectRole('customer');
}
</script>
@endpush
