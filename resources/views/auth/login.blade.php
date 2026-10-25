@extends('layouts.auth')
@section('title', 'Log In - HomeServiceHub')

@push('styles')
<style>
  :root {
    --auth-bg: #F5F7FC;
    --auth-card: #FFFFFF;
    --auth-primary: #2451BD;
    --auth-primary-hover: #064FBA;
    --auth-text-dark: #14213D;
    --auth-muted: #64748B;
    --auth-border: #D9DEE8;
    --auth-tint: #EAF0FB;
    --auth-warm-greige: #E8DFD8;
    --auth-error: #B3261E;
  }

  *, *::before, *::after {
    box-sizing: border-box;
  }

  body {
    background-color: var(--auth-bg);
    color: var(--auth-text-dark);
    margin: 0;
    padding: 0;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    position: relative;
    overflow-x: hidden;
  }

  /* Decorative Background Shapes */
  .bg-shape-top-right {
    position: fixed;
    top: -140px;
    right: -140px;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    background: #EAE2DA;
    pointer-events: none;
    z-index: 0;
  }

  .bg-shape-bottom-left {
    position: fixed;
    bottom: -180px;
    left: -180px;
    width: 660px;
    height: 660px;
    border-radius: 50%;
    background: #E6EEFA;
    opacity: 0.85;
    pointer-events: none;
    z-index: 0;
  }

  /* Page Wrapper */
  .auth-page-wrapper {
    position: relative;
    z-index: 1;
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 36px 20px;
    box-sizing: border-box;
  }

  /* Two-Panel Split Login Card */
  .login-split-card {
    width: 100%;
    max-width: 1000px;
    background: #FFFFFF;
    border-radius: 24px;
    box-shadow: 0 16px 48px rgba(20, 33, 61, 0.08), 0 4px 12px rgba(20, 33, 61, 0.04);
    display: grid;
    grid-template-columns: 1fr 1.05fr;
    overflow: hidden;
    margin: auto 0;
  }

  /* Left Panel: Warm Greige */
  .login-left-panel {
    background: var(--auth-warm-greige);
    padding: 44px 40px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-sizing: border-box;
  }

  .login-brand-wrap {
    margin-bottom: 28px;
  }

  .login-brand-wrap a {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
  }

  .login-brand-wrap .brand-logo-img {
    height: 38px;
    max-height: 38px;
    width: auto;
    object-fit: contain;
  }

  .login-hero-headline {
    font-family: 'Poppins', 'Inter', sans-serif;
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--auth-text-dark);
    line-height: 1.25;
    margin: 0 0 14px 0;
    letter-spacing: -0.4px;
  }

  .login-hero-desc {
    font-size: 0.95rem;
    color: #475569;
    line-height: 1.55;
    margin: 0 0 28px 0;
  }

  /* Benefit Cards List */
  .benefit-cards-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 28px;
  }

  .benefit-card {
    background: #FFFFFF;
    border: 1px solid rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    padding: 13px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(20, 33, 61, 0.03);
  }

  .benefit-icon-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #EAF0FB;
    color: var(--auth-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.92rem;
    flex-shrink: 0;
  }

  .benefit-text {
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--auth-text-dark);
  }

  /* Trust statement */
  .login-trust-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #475569;
    font-size: 0.82rem;
    font-weight: 500;
    margin-top: 10px;
  }

  .login-trust-footer i {
    color: var(--auth-primary);
    font-size: 0.9rem;
  }

  /* Right Panel: White Form */
  .login-right-panel {
    background: #FFFFFF;
    padding: 48px 46px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-sizing: border-box;
  }

  .login-title {
    font-family: 'Poppins', 'Inter', sans-serif;
    font-size: 1.85rem;
    font-weight: 700;
    color: var(--auth-text-dark);
    margin: 0 0 6px 0;
    letter-spacing: -0.4px;
  }

  .login-subtitle {
    font-size: 0.92rem;
    color: var(--auth-muted);
    margin: 0 0 26px 0;
    line-height: 1.5;
  }

  /* Feedback Messages */
  .auth-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 0.88rem;
    line-height: 1.4;
    margin-bottom: 20px;
  }

  .auth-alert-error {
    background: #FDF2F2;
    color: var(--auth-error);
    border: 1px solid #F8B4B4;
  }

  .auth-alert-info {
    background: #EFF6FF;
    color: var(--auth-primary);
    border: 1px solid #BFDBFE;
  }

  .auth-alert-success {
    background: #F0FDF4;
    color: #166534;
    border: 1px solid #BBF7D0;
  }

  /* Form Elements */
  .auth-field-group {
    margin-bottom: 18px;
  }

  .auth-field-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--auth-text-dark);
    margin-bottom: 7px;
  }

  .auth-input-wrap {
    display: flex;
    align-items: center;
    height: 48px;
    background: #FFFFFF;
    border: 1.5px solid var(--auth-border);
    border-radius: 10px;
    padding: 0 14px;
    gap: 12px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .auth-input-wrap:focus-within {
    border-color: var(--auth-primary);
    box-shadow: 0 0 0 3px rgba(36, 81, 189, 0.12);
  }

  .auth-input-icon {
    color: #94A3B8;
    font-size: 0.95rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    pointer-events: none;
  }

  .auth-input-wrap input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.95rem;
    color: var(--auth-text-dark);
    font-family: inherit;
  }

  .auth-input-wrap input::placeholder {
    color: #94A3B8;
  }

  .auth-toggle-pwd {
    background: transparent;
    border: none;
    padding: 0;
    color: #94A3B8;
    cursor: pointer;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    transition: color 0.15s ease;
  }

  .auth-toggle-pwd:hover {
    color: var(--auth-text-dark);
  }

  /* Remember Me & Forgot Password Row */
  .auth-options-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    font-size: 0.88rem;
  }

  .auth-remember {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: var(--auth-text-dark);
    user-select: none;
  }

  .auth-remember input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--auth-primary);
    border-radius: 4px;
    cursor: pointer;
    margin: 0;
  }

  .auth-forgot-link {
    color: var(--auth-primary);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .auth-forgot-link:hover {
    color: var(--auth-primary-hover);
    text-decoration: underline;
  }

  /* Primary Button */
  .auth-btn-primary {
    width: 100%;
    height: 48px;
    background: var(--auth-primary);
    color: #FFFFFF;
    border: none;
    border-radius: 10px;
    font-size: 0.98rem;
    font-weight: 600;
    font-family: inherit;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    box-shadow: 0 4px 14px rgba(36, 81, 189, 0.25);
  }

  .auth-btn-primary:hover {
    background: var(--auth-primary-hover);
    color: #FFFFFF;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(36, 81, 189, 0.32);
  }

  .auth-btn-primary:active {
    transform: translateY(0);
  }

  /* Switch prompt */
  .auth-callout {
    text-align: center;
    font-size: 0.9rem;
    color: var(--auth-muted);
    margin-top: 20px;
  }

  .auth-callout a {
    color: var(--auth-primary);
    font-weight: 700;
    text-decoration: none;
  }

  .auth-callout a:hover {
    color: var(--auth-primary-hover);
    text-decoration: underline;
  }

  /* Horizontal Divider */
  .auth-divider {
    border-top: 1px solid #EEF2F6;
    margin: 28px 0 16px 0;
  }

  /* Footer Links */
  .auth-footer-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-size: 0.8rem;
    color: var(--auth-muted);
    flex-wrap: wrap;
  }

  .auth-footer-nav a {
    color: var(--auth-muted);
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .auth-footer-nav a:hover {
    color: var(--auth-primary);
    text-decoration: underline;
  }

  .auth-footer-nav .nav-dot {
    color: #CBD5E1;
    font-size: 0.75rem;
  }

  /* Responsive Design */
  @media (max-width: 900px) {
    .login-split-card {
      grid-template-columns: 1fr;
      max-width: 520px;
      border-radius: 20px;
    }

    .login-left-panel {
      padding: 36px 30px 30px;
    }

    .login-right-panel {
      padding: 36px 30px;
    }

    .login-hero-headline {
      font-size: 1.6rem;
    }
  }

  @media (max-width: 480px) {
    .auth-page-wrapper {
      padding: 20px 14px;
    }

    .login-split-card {
      border-radius: 16px;
    }

    .login-left-panel {
      padding: 28px 20px 24px;
    }

    .login-right-panel {
      padding: 28px 20px;
    }

    .login-hero-headline {
      font-size: 1.45rem;
    }

    .login-title {
      font-size: 1.55rem;
    }

    .benefit-card {
      padding: 10px 14px;
    }

    .benefit-text {
      font-size: 0.88rem;
    }
  }
</style>
@endpush

@section('content')
<!-- Background Decorative Shapes -->
<div class="bg-shape-top-right" aria-hidden="true"></div>
<div class="bg-shape-bottom-left" aria-hidden="true"></div>

<div class="auth-page-wrapper">
  <div class="login-split-card">

    <!-- Left Panel: Warm-Greige Branding & Benefits -->
    <div class="login-left-panel">
      <div>
        <!-- HomeServiceHub Logo -->
        <div class="login-brand-wrap">
          <a href="{{ route('home') }}" aria-label="HomeServiceHub Home">
            <img src="{{ asset('images/logo-horizontal.svg') }}" alt="HomeServiceHub" height="38" class="brand-logo-img">
          </a>
        </div>

        <!-- Headline & Description -->
        <h1 class="login-hero-headline">Your Home. Our Trusted Pros. Peace of Mind.</h1>
        <p class="login-hero-desc">Book verified local professionals for repairs, cleaning, and everyday home care.</p>

        <!-- Three Stacked Benefit Cards -->
        <div class="benefit-cards-list">
          <div class="benefit-card">
            <div class="benefit-icon-circle">
              <i class="fas fa-user-check"></i>
            </div>
            <div class="benefit-text">Verified Professionals</div>
          </div>

          <div class="benefit-card">
            <div class="benefit-icon-circle">
              <i class="fas fa-tag"></i>
            </div>
            <div class="benefit-text">Upfront Pricing</div>
          </div>

          <div class="benefit-card">
            <div class="benefit-icon-circle">
              <i class="fas fa-calendar-check"></i>
            </div>
            <div class="benefit-text">Easy Booking</div>
          </div>
        </div>
      </div>

      <!-- Trust Statement at Bottom -->
      <div class="login-trust-footer">
        <i class="fas fa-shield-alt"></i>
        <span>Verified service network in Ahmedabad &amp; Gandhinagar</span>
      </div>
    </div>

    <!-- Right Panel: White Login Form -->
    <div class="login-right-panel">
      <h2 class="login-title">Welcome Back</h2>
      <p class="login-subtitle">Log in to manage your bookings and home services.</p>

      <!-- Feedback Messages -->
      @if(session('info'))
        <div class="auth-alert auth-alert-info" role="status">
          <i class="fas fa-info-circle flex-shrink-0"></i>
          <span>{{ session('info') }}</span>
        </div>
      @endif

      @if(session('success'))
        <div class="auth-alert auth-alert-success" role="status">
          <i class="fas fa-check-circle flex-shrink-0"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      @if($errors->any())
        <div class="auth-alert auth-alert-error" role="alert">
          <i class="fas fa-exclamation-circle flex-shrink-0"></i>
          <span>{{ $errors->first() }}</span>
        </div>
      @endif

      <!-- Login Form -->
      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        @if(request('redirect'))
          <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif

        <!-- Email Address -->
        <div class="auth-field-group">
          <label for="loginEmail" class="auth-field-label">Email Address</label>
          <div class="auth-input-wrap">
            <span class="auth-input-icon"><i class="far fa-envelope"></i></span>
            <input
              type="email"
              id="loginEmail"
              name="email"
              placeholder="you@example.com"
              value="{{ old('email') }}"
              required
              autocomplete="email"
              autocapitalize="none"
              autocorrect="off"
              spellcheck="false"
            >
          </div>
        </div>

        <!-- Password -->
        <div class="auth-field-group">
          <label for="loginPassword" class="auth-field-label">Password</label>
          <div class="auth-input-wrap">
            <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
            <input
              type="password"
              name="password"
              id="loginPassword"
              placeholder="Enter your password"
              required
              autocomplete="current-password"
            >
            <button
              type="button"
              class="auth-toggle-pwd"
              onclick="togglePasswordVisibility('loginPassword', document.getElementById('loginEyeIcon'))"
              aria-label="Toggle password visibility"
            >
              <i class="far fa-eye" id="loginEyeIcon"></i>
            </button>
          </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="auth-options-row">
          <label class="auth-remember" for="rememberMe">
            <input type="checkbox" name="remember" id="rememberMe" value="1" {{ old('remember') ? 'checked' : '' }}>
            <span>Remember me</span>
          </label>
          <a href="#" class="auth-forgot-link">Forgot password?</a>
        </div>

        <!-- Primary Submit Button -->
        <button type="submit" class="auth-btn-primary">
          <span>Log In</span>
          <i class="fas fa-arrow-right" aria-hidden="true"></i>
        </button>
      </form>

      <!-- Account Switch -->
      <div class="auth-callout">
        Don't have an account? <a href="{{ route('register') }}">Create Account</a>
      </div>

      <!-- Subtle Divider -->
      <div class="auth-divider"></div>

      <!-- Footer Navigation -->
      <div class="auth-footer-nav">
        <a href="{{ route('home') }}">Home</a>
        <span class="nav-dot" aria-hidden="true">&bull;</span>
        <a href="#">Privacy Policy</a>
        <span class="nav-dot" aria-hidden="true">&bull;</span>
        <a href="#">Terms of Service</a>
        <span class="nav-dot" aria-hidden="true">&bull;</span>
        <a href="{{ route('become-provider') }}">Become a Pro</a>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  function togglePasswordVisibility(inputId, iconEl) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
      input.type = 'text';
      if (iconEl) {
        iconEl.classList.remove('fa-eye');
        iconEl.classList.add('fa-eye-slash');
      }
    } else {
      input.type = 'password';
      if (iconEl) {
        iconEl.classList.remove('fa-eye-slash');
        iconEl.classList.add('fa-eye');
      }
    }
  }
</script>
@endpush
