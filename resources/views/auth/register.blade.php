@extends('layouts.auth')
@section('title', (old('role', $initialRole ?? 'customer') === 'provider' ? 'Become a Service Provider - Provider Application' : 'Create Your Account') . ' - HomeServiceHub')

@push('styles')
<style>
  :root {
    --reg-bg: #F5F7FC;
    --reg-card: #FFFFFF;
    --reg-primary: #2451BD;
    --reg-primary-hover: #064FBA;
    --reg-text-dark: #14213D;
    --reg-muted: #64748B;
    --reg-border: #D9DEE8;
    --reg-tint: #F0F5FF;
    --reg-badge-bg: #F3ECE5;
    --reg-error: #B3261E;
  }

  *, *::before, *::after {
    box-sizing: border-box;
  }

  body {
    background-color: var(--reg-bg);
    color: var(--reg-text-dark);
    margin: 0;
    padding: 0;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    position: relative;
    overflow-x: hidden;
  }

  /* Decorative Background Shapes matching Reference Image 1 */
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
  .register-page-wrapper {
    position: relative;
    z-index: 1;
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    box-sizing: border-box;
  }

  /* Logo positioned horizontally centered directly above the card */
  .register-logo-header {
    text-align: center;
    margin-bottom: 24px;
  }

  .register-logo-header a {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
  }

  .register-logo-header .brand-logo-img {
    height: 38px;
    max-height: 38px;
    width: auto;
    object-fit: contain;
  }

  /* Single White Registration Card */
  .register-card {
    width: 100%;
    max-width: 580px;
    background: #FFFFFF;
    border-radius: 20px;
    box-shadow: 0 16px 48px rgba(20, 33, 61, 0.08), 0 4px 12px rgba(20, 33, 61, 0.04);
    padding: 38px 36px 30px;
    box-sizing: border-box;
    transition: max-width 0.25s ease;
  }

  /* Expanded comfortable width for Provider Onboarding */
  .register-card.provider-mode {
    max-width: 720px;
  }

  /* Provider Application Badge */
  .reg-provider-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--reg-tint);
    color: var(--reg-primary);
    border: 1px solid rgba(36, 81, 189, 0.2);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 12px;
  }

  /* Headings */
  .register-title {
    font-family: 'Poppins', 'Inter', sans-serif;
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--reg-text-dark);
    text-align: center;
    margin: 0 0 6px 0;
    letter-spacing: -0.3px;
  }

  .register-subtitle {
    font-size: 0.88rem;
    color: var(--reg-muted);
    text-align: center;
    margin: 0 0 22px 0;
    line-height: 1.5;
  }

  /* Feedback Alert */
  .reg-alert-error {
    background: #FDF2F2;
    color: var(--reg-error);
    border: 1px solid #F8B4B4;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 0.86rem;
    line-height: 1.4;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 20px;
  }

  /* Account Type Selector */
  .role-section-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--reg-text-dark);
    margin-bottom: 8px;
  }

  .role-selector-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 22px;
  }

  .role-card {
    border-radius: 12px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    border: 1.5px solid var(--reg-border);
    background: #FFFFFF;
  }

  .role-card.selected {
    border-color: var(--reg-primary);
    background: var(--reg-tint);
  }

  /* Radio circle control */
  .role-radio-circle {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 1.5px solid #CBD5E1;
    background: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.15s ease;
  }

  .role-card.selected .role-radio-circle {
    border-color: var(--reg-primary);
    border-width: 5px;
  }

  /* Icon Badge */
  .role-icon-badge {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--reg-badge-bg);
    color: var(--reg-text-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
  }

  .role-text-block {
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .role-card-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--reg-text-dark);
    line-height: 1.2;
    white-space: nowrap;
  }

  .role-card-subtitle {
    font-size: 0.76rem;
    color: var(--reg-muted);
    margin-top: 2px;
    white-space: nowrap;
  }

  /* Section Header Dividers */
  .reg-section-divider {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 22px 0 14px 0;
    padding-bottom: 6px;
    border-bottom: 1.5px solid #EEF2F6;
  }

  .reg-section-title {
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--reg-text-dark);
    display: flex;
    align-items: center;
  }

  /* Form Layout */
  #registerForm {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
  }

  .form-row-two-col {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 14px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
  }

  .form-row-full {
    width: 100%;
    max-width: 100%;
    margin-bottom: 14px;
    box-sizing: border-box;
  }

  .reg-field-group {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
  }

  .reg-field-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--reg-text-dark);
    margin-bottom: 6px;
  }

  .reg-field-label .req {
    color: var(--reg-primary);
  }

  .reg-input-wrap {
    display: flex;
    align-items: center;
    height: 46px;
    background: #FFFFFF;
    border: 1.5px solid var(--reg-border);
    border-radius: 10px;
    padding: 0 13px;
    gap: 10px;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
    position: relative;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .reg-input-wrap:focus-within {
    border-color: var(--reg-primary);
    box-shadow: 0 0 0 3px rgba(36, 81, 189, 0.12);
  }

  .reg-input-icon {
    color: #94A3B8;
    font-size: 0.92rem;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
  }

  .reg-input-wrap input,
  .reg-input-wrap select {
    flex: 1 1 0%;
    width: 100%;
    min-width: 0;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.92rem;
    color: var(--reg-text-dark);
    font-family: inherit;
    padding: 0;
    margin: 0;
    box-sizing: border-box;
  }

  .reg-input-wrap input::placeholder,
  .reg-textarea-wrap textarea::placeholder {
    color: #94A3B8;
  }

  .reg-textarea-wrap {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #FFFFFF;
    border: 1.5px solid var(--reg-border);
    border-radius: 10px;
    padding: 10px 13px;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .reg-textarea-wrap:focus-within {
    border-color: var(--reg-primary);
    box-shadow: 0 0 0 3px rgba(36, 81, 189, 0.12);
  }

  .reg-textarea-wrap textarea {
    flex: 1 1 0%;
    width: 100%;
    min-width: 0;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.92rem;
    color: var(--reg-text-dark);
    font-family: inherit;
    resize: vertical;
    min-height: 54px;
    padding: 0;
    margin: 0;
    box-sizing: border-box;
  }

  .reg-toggle-pwd {
    background: transparent;
    border: none;
    padding: 0;
    margin: 0;
    width: 24px;
    height: 24px;
    flex-shrink: 0;
    color: #94A3B8;
    cursor: pointer;
    font-size: 0.92rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s ease;
  }

  .reg-toggle-pwd:hover {
    color: var(--reg-text-dark);
  }

  .reg-toggle-pwd:focus-visible {
    outline: 2px solid var(--reg-primary);
    border-radius: 4px;
  }

  .field-error-hint {
    color: var(--reg-error);
    font-size: 0.78rem;
    margin-top: 4px;
    font-weight: 500;
  }

  /* Provider Info Box */
  .provider-info-box {
    background: var(--reg-tint);
    border-radius: 12px;
    padding: 12px 16px;
    margin-top: 6px;
    margin-bottom: 18px;
    border: 1px solid rgba(36, 81, 189, 0.2);
  }

  /* Terms Checkbox */
  .terms-check-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
    margin-bottom: 18px;
    font-size: 0.82rem;
    color: #475569;
  }

  .terms-check-row input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--reg-primary);
    border-radius: 4px;
    cursor: pointer;
    margin: 0;
    flex-shrink: 0;
  }

  .terms-check-row label {
    cursor: pointer;
    user-select: none;
    margin: 0;
  }

  .terms-check-row a {
    color: var(--reg-primary);
    text-decoration: none;
    font-weight: 500;
  }

  .terms-check-row a:hover {
    text-decoration: underline;
  }

  /* Primary Button */
  .reg-btn-primary {
    width: 100%;
    height: 48px;
    background: var(--reg-primary);
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

  .reg-btn-primary:hover {
    background: var(--reg-primary-hover);
    color: #FFFFFF;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(36, 81, 189, 0.32);
  }

  .reg-btn-primary:active {
    transform: translateY(0);
  }

  /* Switch prompt */
  .reg-callout {
    text-align: center;
    font-size: 0.88rem;
    color: var(--reg-muted);
    margin-top: 16px;
  }

  .reg-callout a {
    color: var(--reg-primary);
    font-weight: 700;
    text-decoration: none;
  }

  .reg-callout a:hover {
    color: var(--reg-primary-hover);
    text-decoration: underline;
  }

  /* Horizontal Divider */
  .reg-divider {
    border-top: 1px solid #EEF2F6;
    margin: 22px 0 14px 0;
  }

  /* Footer Links */
  .reg-footer-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 18px;
    font-size: 0.8rem;
    color: var(--reg-muted);
    flex-wrap: wrap;
  }

  .reg-footer-nav a {
    color: var(--reg-muted);
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .reg-footer-nav a:hover {
    color: var(--reg-primary);
    text-decoration: underline;
  }

  /* Responsive Adjustments */
  @media (max-width: 640px) {
    .register-page-wrapper {
      padding: 24px 14px;
    }

    .register-card {
      padding: 26px 18px 22px;
      border-radius: 16px;
    }

    .register-title {
      font-size: 1.35rem;
    }

    .form-row-two-col {
      grid-template-columns: 1fr;
      gap: 14px;
    }

    .role-selector-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .reg-footer-nav {
      gap: 12px;
    }
  }

  @media (max-width: 380px) {
    .register-card {
      padding: 20px 14px 18px;
    }

    .register-title {
      font-size: 1.22rem;
    }

    .role-card {
      padding: 10px 12px;
    }
  }
</style>
@endpush

@php
  $activeRole = old('role', $initialRole ?? 'customer');
  if (!in_array($activeRole, ['customer', 'provider'])) {
      $activeRole = 'customer';
  }
@endphp

@section('content')
<!-- Decorative Background Shapes matching Reference Image 1 -->
<div class="bg-shape-top-right" aria-hidden="true"></div>
<div class="bg-shape-bottom-left" aria-hidden="true"></div>

<div class="register-page-wrapper">

  <!-- Logo centered horizontally directly above the card -->
  <div class="register-logo-header">
    <a href="{{ route('home') }}" aria-label="HomeServiceHub Home">
      <img src="{{ asset('images/logo-horizontal.svg') }}" alt="HomeServiceHub" height="38" class="brand-logo-img">
    </a>
  </div>

  <!-- Centered Single White Card (Expands smoothly when Provider mode is active) -->
  <main class="register-card {{ $activeRole === 'provider' ? 'provider-mode' : '' }}" id="registerCard">

    <!-- Header Badge for Provider -->
    <div class="text-center">
      <div id="regBadge" class="reg-provider-badge" style="display: {{ $activeRole === 'provider' ? 'inline-flex' : 'none' }};">
        <i class="fas fa-hard-hat"></i>
        <span>Become a Service Provider &bull; Provider Application</span>
      </div>
    </div>

    <!-- Heading & Description -->
    <h1 class="register-title" id="regTitle">
      {{ $activeRole === 'provider' ? 'Become a Service Provider' : 'Create Your HomeServiceHub Account' }}
    </h1>
    <p class="register-subtitle" id="regSubtitle">
      {{ $activeRole === 'provider' ? 'Provider Application & Onboarding — Join Ahmedabad & Gandhinagar\'s trusted network.' : 'Join in a minute and get trusted home services, or start growing your business.' }}
    </p>

    <!-- Feedback Messages -->
    @if($errors->any())
      <div class="reg-alert-error" role="alert">
        <i class="fas fa-exclamation-circle mt-1 flex-shrink-0"></i>
        <div>
          <strong>Please check the following:</strong>
          <div style="margin-top:2px;">{{ $errors->first() }}</div>
        </div>
      </div>
    @endif

    <!-- Unified Registration Form -->
    <form method="POST" action="{{ route('register.post') }}" id="registerForm">
      @csrf

      <!-- Role Selector State -->
      <input type="hidden" name="role" id="selectedRole" value="{{ $activeRole }}" required>

      <!-- Account Type Selection Cards -->
      <div class="role-section-label">I want to</div>
      <div class="role-selector-grid">
        <!-- Card 1: Book services (Customer) -->
        <div class="role-card {{ $activeRole === 'customer' ? 'selected' : '' }}" 
             id="roleCardCustomer" 
             onclick="setAccountRole('customer')" 
             role="button" 
             tabindex="0"
             onkeydown="if(event.key==='Enter'||event.key===' ')setAccountRole('customer');">
          <div class="role-radio-circle"></div>
          <div class="role-icon-badge">
            <i class="fas fa-home"></i>
          </div>
          <div class="role-text-block">
            <span class="role-card-title">Book services</span>
            <span class="role-card-subtitle">Customer account</span>
          </div>
        </div>

        <!-- Card 2: Offer services (Provider) -->
        <div class="role-card {{ $activeRole === 'provider' ? 'selected' : '' }}" 
             id="roleCardProvider" 
             onclick="setAccountRole('provider')" 
             role="button" 
             tabindex="0"
             onkeydown="if(event.key==='Enter'||event.key===' ')setAccountRole('provider');">
          <div class="role-radio-circle"></div>
          <div class="role-icon-badge">
            <i class="fas fa-wrench"></i>
          </div>
          <div class="role-text-block">
            <span class="role-card-title">Offer services</span>
            <span class="role-card-subtitle">Provider account</span>
          </div>
        </div>
      </div>

      <!-- SECTION 1: Personal & Account Details -->
      <div id="personalSectionHeader" class="reg-section-divider" style="display: {{ $activeRole === 'provider' ? 'flex' : 'none' }};">
        <span class="reg-section-title"><i class="fas fa-user-circle me-2" style="color:var(--reg-primary)"></i>1. Personal &amp; Account Details</span>
      </div>

      <!-- Row 1: Full Name & Phone Number -->
      <div class="form-row-two-col">
        <div class="reg-field-group">
          <label for="regName" class="reg-field-label">Full Name <span class="req">*</span></label>
          <div class="reg-input-wrap">
            <span class="reg-input-icon"><i class="far fa-user"></i></span>
            <input
              type="text"
              id="regName"
              name="name"
              placeholder="Your full name"
              value="{{ old('name') }}"
              required
              autocomplete="name"
            >
          </div>
          @error('name')
            <span class="field-error-hint">{{ $message }}</span>
          @enderror
        </div>

        <div class="reg-field-group">
          <label for="regPhone" class="reg-field-label">Phone Number <span class="req">*</span></label>
          <div class="reg-input-wrap">
            <span class="reg-input-icon"><i class="fas fa-phone"></i></span>
            <input
              type="tel"
              id="regPhone"
              name="phone"
              placeholder="98765 43210"
              value="{{ old('phone') }}"
              required
              autocomplete="tel"
            >
          </div>
          @error('phone')
            <span class="field-error-hint">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- Row 2: Email & City -->
      <!-- When in customer mode, Email is full width; when provider mode, Email and City are 2 columns -->
      <div id="emailCityRow" class="{{ $activeRole === 'provider' ? 'form-row-two-col' : 'form-row-full' }}">
        <div class="reg-field-group">
          <label for="regEmail" class="reg-field-label">Email Address <span class="req">*</span></label>
          <div class="reg-input-wrap">
            <span class="reg-input-icon"><i class="far fa-envelope"></i></span>
            <input
              type="email"
              id="regEmail"
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
          @error('email')
            <span class="field-error-hint">{{ $message }}</span>
          @enderror
        </div>

        <!-- City Dropdown: Enabled in Provider mode -->
        <div class="reg-field-group" id="providerCityGroup" style="display: {{ $activeRole === 'provider' ? 'flex' : 'none' }};">
          <label for="regCity" class="reg-field-label">City (Gujarat) <span class="req">*</span></label>
          <div class="reg-input-wrap">
            <span class="reg-input-icon"><i class="fas fa-map-marker-alt"></i></span>
            <select name="city" id="regCity" {{ $activeRole === 'customer' ? 'disabled' : '' }} style="flex:1; border:none; outline:none; background:transparent; font-size:0.92rem; color:var(--reg-text-dark);" required>
              <option value="Ahmedabad" {{ old('city', 'Ahmedabad') === 'Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
              <option value="Gandhinagar" {{ old('city') === 'Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
            </select>
          </div>
          @error('city')
            <span class="field-error-hint">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- Hidden City fallback when Customer mode is active -->
      <input type="hidden" name="city" id="hiddenCustomerCity" value="{{ old('city', 'Ahmedabad') }}" {{ $activeRole === 'provider' ? 'disabled' : '' }}>

      <!-- Row: Full Address -->
      <div class="form-row-full">
        <div class="reg-field-group">
          <label for="regAddress" class="reg-field-label">Address</label>
          <div class="reg-textarea-wrap">
            <span class="reg-input-icon" style="align-self: flex-start; margin-top: 3px;"><i class="fas fa-map-marker-alt"></i></span>
            <textarea
              id="regAddress"
              name="address"
              rows="2"
              placeholder="Enter your full address"
            >{{ old('address') }}</textarea>
          </div>
          @error('address')
            <span class="field-error-hint">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- Row 3: Password & Confirm Password -->
      <div class="form-row-two-col">
        <div class="reg-field-group">
          <label for="regPassword" class="reg-field-label">Password <span class="req">*</span></label>
          <div class="reg-input-wrap">
            <span class="reg-input-icon"><i class="fas fa-lock"></i></span>
            <input
              type="password"
              id="regPassword"
              name="password"
              placeholder="Create password"
              required
              autocomplete="new-password"
            >
            <button
              type="button"
              class="reg-toggle-pwd"
              onclick="togglePasswordVisibility('regPassword', document.getElementById('pwdEye1'))"
              aria-label="Toggle password visibility"
            >
              <i class="far fa-eye" id="pwdEye1"></i>
            </button>
          </div>
          @error('password')
            <span class="field-error-hint">{{ $message }}</span>
          @enderror
        </div>

        <div class="reg-field-group">
          <label for="regPasswordConfirm" class="reg-field-label">Confirm Password <span class="req">*</span></label>
          <div class="reg-input-wrap">
            <span class="reg-input-icon"><i class="fas fa-lock"></i></span>
            <input
              type="password"
              id="regPasswordConfirm"
              name="password_confirmation"
              placeholder="Repeat password"
              required
              autocomplete="new-password"
            >
            <button
              type="button"
              class="reg-toggle-pwd"
              onclick="togglePasswordVisibility('regPasswordConfirm', document.getElementById('pwdEye2'))"
              aria-label="Toggle confirm password visibility"
            >
              <i class="far fa-eye" id="pwdEye2"></i>
            </button>
          </div>
          @error('password_confirmation')
            <span class="field-error-hint">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- SECTION 2: Professional & Service Profile (Visible when Provider is active) -->
      <div id="providerSection" style="display: {{ $activeRole === 'provider' ? 'block' : 'none' }};">
        <div class="reg-section-divider">
          <span class="reg-section-title"><i class="fas fa-briefcase me-2" style="color:var(--reg-primary)"></i>2. Professional &amp; Service Profile</span>
        </div>

        <!-- Row: Trade / Category & Business Name -->
        <div class="form-row-two-col">
          <div class="reg-field-group">
            <label for="regCategory" class="reg-field-label">Service Category / Trade <span class="req">*</span></label>
            <div class="reg-input-wrap">
              <span class="reg-input-icon"><i class="fas fa-tools"></i></span>
              <select name="category_id" id="regCategory" style="flex:1; border:none; outline:none; background:transparent; font-size:0.92rem; color:var(--reg-text-dark);" {{ $activeRole === 'provider' ? 'required' : '' }}>
                <option value="">Select your main trade...</option>
                @if(isset($categories) && count($categories) > 0)
                  @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                @else
                  <option value="1">Electrician</option>
                  <option value="2">Plumber</option>
                  <option value="3">AC Technician</option>
                  <option value="4">Carpenter</option>
                  <option value="5">Painter</option>
                  <option value="6">Cleaner</option>
                @endif
              </select>
            </div>
            @error('category_id')
              <span class="field-error-hint">{{ $message }}</span>
            @enderror
          </div>

          <div class="reg-field-group">
            <label for="regBusinessName" class="reg-field-label">Business / Brand Name</label>
            <div class="reg-input-wrap">
              <span class="reg-input-icon"><i class="fas fa-building"></i></span>
              <input
                type="text"
                id="regBusinessName"
                name="business_name"
                placeholder="e.g. Patel Electrical Services"
                value="{{ old('business_name') }}"
              >
            </div>
            @error('business_name')
              <span class="field-error-hint">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <!-- Row: Experience Years & Hourly Rate -->
        <div class="form-row-two-col">
          <div class="reg-field-group">
            <label for="regExperience" class="reg-field-label">Years of Experience <span class="req">*</span></label>
            <div class="reg-input-wrap">
              <span class="reg-input-icon"><i class="fas fa-award"></i></span>
              <input
                type="number"
                id="regExperience"
                name="experience_years"
                placeholder="e.g. 5"
                min="0"
                max="50"
                value="{{ old('experience_years', '3') }}"
              >
            </div>
            @error('experience_years')
              <span class="field-error-hint">{{ $message }}</span>
            @enderror
          </div>

          <div class="reg-field-group">
            <label for="regHourlyRate" class="reg-field-label">Standard Hourly Rate (₹) <span class="req">*</span></label>
            <div class="reg-input-wrap">
              <span class="reg-input-icon"><i class="fas fa-rupee-sign"></i></span>
              <input
                type="number"
                id="regHourlyRate"
                name="hourly_rate"
                placeholder="e.g. 450"
                min="0"
                max="10000"
                value="{{ old('hourly_rate', '450') }}"
                {{ $activeRole === 'provider' ? 'required' : '' }}
              >
            </div>
            @error('hourly_rate')
              <span class="field-error-hint">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <!-- Row: Service Areas / Localities -->
        <div class="form-row-full">
          <div class="reg-field-group">
            <label for="regServiceArea" class="reg-field-label">Specific Service Areas / Localities <span class="req">*</span></label>
            <div class="reg-input-wrap">
              <span class="reg-input-icon"><i class="fas fa-map-pin"></i></span>
              <input
                type="text"
                id="regServiceArea"
                name="service_area"
                placeholder="e.g. SG Highway, Satellite, Bopal, Navrangpura, Vastrapur"
                value="{{ old('service_area', 'Ahmedabad & Gandhinagar Localities') }}"
              >
            </div>
            <small style="color:var(--reg-muted);font-size:0.78rem;margin-top:4px;">Localities or sectors in Ahmedabad/Gandhinagar where you provide on-site services.</small>
            @error('service_area')
              <span class="field-error-hint">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <!-- Row: Professional Bio / Summary -->
        <div class="form-row-full">
          <div class="reg-field-group">
            <label for="regBio" class="reg-field-label">Professional Bio / Summary</label>
            <div class="reg-textarea-wrap">
              <textarea
                id="regBio"
                name="bio"
                rows="3"
                placeholder="Explain your skills, tools, certifications, or past project experience to attract customers..."
              >{{ old('bio') }}</textarea>
            </div>
            @error('bio')
              <span class="field-error-hint">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <!-- Provider Verification Info Box -->
        <div class="provider-info-box">
          <div class="d-flex align-items-start gap-2">
            <i class="fas fa-shield-alt mt-1" style="color:var(--reg-primary);font-size:1.1rem;flex-shrink:0;"></i>
            <div>
              <strong style="color:var(--reg-text-dark);font-size:0.86rem;">Fast Admin Verification &amp; Profile Setup</strong>
              <p style="margin:2px 0 0;font-size:0.8rem;color:#475569;line-height:1.45;">
                By registering, your account will be created with <strong>Verification: Pending</strong>. You can immediately access your provider dashboard, manage your calendar, and begin accepting bookings upon verification.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 3: Agreement & Registration -->
      <!-- Terms and Conditions Checkbox -->
      <div class="terms-check-row">
        <input type="checkbox" name="terms" id="termsCheck" value="1" checked required>
        <label for="termsCheck">
          I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
        </label>
      </div>

      <!-- Primary Submit Button -->
      <button type="submit" class="reg-btn-primary" id="regSubmitBtn">
        <span id="submitBtnText">{{ $activeRole === 'provider' ? 'Create Provider Account' : 'Create Account' }}</span>
        <i class="fas fa-arrow-right" aria-hidden="true"></i>
      </button>
    </form>

    <!-- Already have an account prompt -->
    <div class="reg-callout">
      Already have an account? <a href="{{ route('login') }}">Log In</a>
    </div>

    <!-- Subtle Horizontal Divider -->
    <div class="reg-divider"></div>

    <!-- Centered Footer Navigation matching Reference Image 1 -->
    <div class="reg-footer-nav">
      <a href="{{ route('home') }}">Home</a>
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="{{ route('become-provider') }}">Become a Pro</a>
    </div>

  </main>
</div>
@endsection

@push('scripts')
<script>
  function setAccountRole(role) {
    document.getElementById('selectedRole').value = role;
    const cardCustomer = document.getElementById('roleCardCustomer');
    const cardProvider = document.getElementById('roleCardProvider');
    const providerSection = document.getElementById('providerSection');
    const personalSectionHeader = document.getElementById('personalSectionHeader');
    const providerCityGroup = document.getElementById('providerCityGroup');
    const hiddenCustomerCity = document.getElementById('hiddenCustomerCity');
    const providerCitySelect = document.getElementById('regCity');
    const emailCityRow = document.getElementById('emailCityRow');
    const submitBtnText = document.getElementById('submitBtnText');
    const regCard = document.getElementById('registerCard');
    const regTitle = document.getElementById('regTitle');
    const regSubtitle = document.getElementById('regSubtitle');
    const regBadge = document.getElementById('regBadge');

    const categorySelect = document.getElementById('regCategory');
    const hourlyRateInput = document.getElementById('regHourlyRate');

    if (role === 'customer') {
      cardCustomer.classList.add('selected');
      cardProvider.classList.remove('selected');
      if (providerSection) providerSection.style.display = 'none';
      if (personalSectionHeader) personalSectionHeader.style.display = 'none';
      if (providerCityGroup) providerCityGroup.style.display = 'none';
      if (hiddenCustomerCity) hiddenCustomerCity.disabled = false;
      if (providerCitySelect) providerCitySelect.disabled = true;
      if (emailCityRow) {
        emailCityRow.classList.remove('form-row-two-col');
        emailCityRow.classList.add('form-row-full');
      }
      if (submitBtnText) submitBtnText.textContent = 'Create Account';
      if (regCard) regCard.classList.remove('provider-mode');
      if (regBadge) regBadge.style.display = 'none';
      if (regTitle) regTitle.textContent = 'Create Your HomeServiceHub Account';
      if (regSubtitle) regSubtitle.textContent = 'Join in a minute and get trusted home services, or start growing your business.';

      if (categorySelect) categorySelect.required = false;
      if (hourlyRateInput) hourlyRateInput.required = false;

      // Update URL without page reload
      if (window.history && window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('role');
        window.history.replaceState({}, '', url.pathname + url.search);
      }
    } else {
      cardProvider.classList.add('selected');
      cardCustomer.classList.remove('selected');
      if (providerSection) providerSection.style.display = 'block';
      if (personalSectionHeader) personalSectionHeader.style.display = 'flex';
      if (providerCityGroup) providerCityGroup.style.display = 'flex';
      if (hiddenCustomerCity) hiddenCustomerCity.disabled = true;
      if (providerCitySelect) providerCitySelect.disabled = false;
      if (emailCityRow) {
        emailCityRow.classList.remove('form-row-full');
        emailCityRow.classList.add('form-row-two-col');
      }
      if (submitBtnText) submitBtnText.textContent = 'Create Provider Account';
      if (regCard) regCard.classList.add('provider-mode');
      if (regBadge) regBadge.style.display = 'inline-flex';
      if (regTitle) regTitle.textContent = 'Become a Service Provider';
      if (regSubtitle) regSubtitle.textContent = 'Provider Application & Onboarding — Join Ahmedabad & Gandhinagar\'s trusted network.';

      if (categorySelect) categorySelect.required = true;
      if (hourlyRateInput) hourlyRateInput.required = true;

      // Update URL without page reload
      if (window.history && window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.set('role', 'provider');
        window.history.replaceState({}, '', url.pathname + url.search);
      }
    }
  }

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

  document.addEventListener('DOMContentLoaded', function() {
    const role = document.getElementById('selectedRole').value || 'customer';
    setAccountRole(role);
  });
</script>
@endpush
