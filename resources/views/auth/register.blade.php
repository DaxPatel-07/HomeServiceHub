@extends('layouts.auth')
@section('title', 'Create Your Account - HomeServiceHub')

@push('styles')
<style>
  /* =========================================================
     HOMESERVICEHUB SINGLE-COLUMN REGISTRATION PAGE
     EXACT SPECIFICATIONS & 4PX-BASED DESIGN SYSTEM
     ========================================================= */
  :root {
    --site-background: #FDF9F0;
    --register-card-background: #FFFFFF;
    --register-primary: #2563EB;
    --register-primary-hover: #1D4ED8;
    --register-text: #172033;
    --register-muted: #64748B;
    --register-border: #E5E7EB;
    --register-input-border: #D1D5DB;
    --register-selected-background: #EFF6FF;
    --register-success: #16A34A;
  }

  *, *::before, *::after {
    box-sizing: border-box;
  }

  body {
    background: var(--site-background);
    margin: 0;
    padding: 0;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: var(--register-text);
  }

  /* Registration Page Wrapper */
  .register-page {
    min-height: 100vh;
    min-height: 100svh;
    box-sizing: border-box;
    background: var(--site-background);
    padding-top: 16px;
    padding-bottom: 48px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* Header Layout */
  .register-header {
    width: min(1200px, calc(100% - 48px));
    min-height: 76px;
    margin-inline: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 0 4px;
    box-sizing: border-box;
  }

  .register-brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: var(--register-text);
    transition: opacity 0.2s ease;
  }
  .register-brand:hover {
    opacity: 0.9;
    color: var(--register-text);
  }
  .register-brand-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .register-brand-icon svg {
    width: 100%;
    height: 100%;
  }
  .register-brand-text {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--register-text);
    letter-spacing: -0.3px;
  }
  .register-brand-text span {
    color: var(--register-primary);
  }

  .register-header-login {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .register-login-prompt {
    font-size: 14px;
    color: var(--register-muted);
    font-weight: 500;
  }
  .register-login-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: #FFFFFF;
    color: var(--register-primary);
    border: 1px solid var(--register-border);
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  }
  .register-login-link:hover {
    background: var(--register-selected-background);
    border-color: #BFDBFE;
    color: var(--register-primary-hover);
    transform: translateY(-1px);
  }

  /* Centered Registration Card — Exact Dimensions */
  .register-card {
    width: min(720px, calc(100% - 48px));
    margin: 32px auto 0;
    padding: 36px;
    box-sizing: border-box;
    height: auto;
    background: var(--register-card-background);
    border: 1px solid var(--register-border);
    border-radius: 24px;
    box-shadow: 0 12px 36px rgba(23, 32, 51, 0.08);
  }

  /* Heading & Subtitle */
  .register-heading {
    font-size: 28px;
    font-weight: 700;
    line-height: 1.25;
    color: var(--register-text);
    margin: 0 0 8px 0;
    text-align: left;
    letter-spacing: -0.4px;
  }
  .register-subtitle {
    font-size: 15px;
    line-height: 1.5;
    color: var(--register-muted);
    margin: 0 0 24px 0;
    text-align: left;
  }

  /* Stepper Progress Indicator (4 Steps) */
  .register-stepper {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    margin-bottom: 28px;
    padding: 0;
  }
  .register-stepper-track {
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 2px;
    background: var(--register-border);
    z-index: 0;
  }
  .register-stepper-fill {
    height: 100%;
    background: var(--register-primary);
    transition: width 0.3s ease;
    width: 0%;
  }

  .register-step-node {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
    text-align: center;
  }
  .register-step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #F3F4F6;
    border: 2px solid var(--register-border);
    color: var(--register-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.25s ease;
    background-color: var(--register-card-background);
  }
  .register-step-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--register-muted);
    margin-top: 8px;
    transition: color 0.25s ease;
    white-space: nowrap;
  }

  /* Stepper Active State */
  .register-step-node.active .register-step-circle {
    background: var(--register-primary);
    border-color: var(--register-primary);
    color: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.16);
  }
  .register-step-node.active .register-step-label {
    color: var(--register-primary);
    font-weight: 700;
  }

  /* Stepper Completed State */
  .register-step-node.done .register-step-circle {
    background: var(--register-success);
    border-color: var(--register-success);
    color: #FFFFFF;
  }
  .register-step-node.done .register-step-label {
    color: var(--register-success);
    font-weight: 700;
  }

  /* Alert Notifications */
  .register-alert {
    background: #FEF2F2;
    border: 1px solid #FCA5A5;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13.5px;
    color: #DC2626;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
  }

  /* Role Selection (Step 1) */
  .register-role-section-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--register-text);
    margin-bottom: 16px;
  }
  .register-role-options {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 0;
  }
  .register-role-card {
    min-height: 140px;
    padding: 20px;
    background: #FFFFFF;
    border: 1px solid var(--register-border);
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    user-select: none;
    box-sizing: border-box;
  }
  .register-role-card:hover {
    border-color: #93C5FD;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.08);
  }
  .register-role-card.selected {
    border: 2px solid var(--register-primary);
    background: var(--register-selected-background);
    box-shadow: 0 4px 18px rgba(37, 99, 235, 0.12);
  }
  .register-role-radio {
    position: absolute;
    top: 18px;
    right: 18px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid var(--register-input-border);
    background: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: transparent;
    transition: all 0.2s ease;
  }
  .register-role-card.selected .register-role-radio {
    border-color: var(--register-primary);
    background: var(--register-primary);
    color: #FFFFFF;
  }
  .register-role-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #F1F5F9;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
    transition: all 0.2s ease;
  }
  .register-role-card.selected .register-role-icon {
    background: rgba(37, 99, 235, 0.14);
    color: var(--register-primary);
  }
  .register-role-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--register-text);
    margin-bottom: 4px;
  }
  .register-role-desc {
    font-size: 13px;
    line-height: 1.45;
    color: var(--register-muted);
  }

  /* Form Fields (Steps 2 & 3) */
  .register-form-grid {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }
  .register-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  .register-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--register-text);
    margin: 0;
  }
  .register-label .req {
    color: #DC2626;
    margin-left: 2px;
  }

  .register-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
  }
  .register-input-icon {
    position: absolute;
    left: 14px;
    color: #94A3B8;
    font-size: 14px;
    pointer-events: none;
    z-index: 2;
  }
  .register-input,
  .register-select {
    width: 100%;
    min-height: 48px;
    padding: 12px 14px 12px 42px;
    box-sizing: border-box;
    border: 1px solid var(--register-input-border);
    border-radius: 10px;
    font-size: 15px;
    color: var(--register-text);
    font-family: inherit;
    background-color: #FFFFFF;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .register-input:focus,
  .register-select:focus {
    outline: none;
    border-color: var(--register-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }
  .register-input::placeholder {
    color: #9CA3AF;
  }
  .register-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 14px 10px;
    padding-right: 38px;
    cursor: pointer;
  }
  textarea.register-input {
    min-height: 100px;
    padding-left: 14px;
    resize: vertical;
  }

  .register-pwd-toggle {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #94A3B8;
    cursor: pointer;
    font-size: 15px;
    padding: 6px;
    border-radius: 6px;
    transition: color 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .register-pwd-toggle:hover {
    color: var(--register-primary);
  }
  .register-input.has-toggle {
    padding-right: 44px;
  }

  .register-field-hint {
    font-size: 12px;
    color: var(--register-muted);
  }

  /* Two Column Row inside form */
  .register-two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }

  /* Profile Tip Note */
  .register-note-box {
    background: #FFF5E5;
    border: 1px solid #FED7AA;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13.5px;
    color: #9A3412;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* Actions & Navigation */
  .register-actions {
    display: flex;
    gap: 12px;
    margin-top: 28px;
  }

  .register-button {
    min-height: 48px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-sizing: border-box;
    font-family: inherit;
    text-decoration: none;
  }
  .register-btn-primary {
    background: var(--register-primary);
    color: #FFFFFF;
    border: none;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
  }
  .register-btn-primary:hover {
    background: var(--register-primary-hover);
    color: #FFFFFF;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.26);
  }
  .register-btn-primary:disabled {
    background: #93C5FD;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }

  .register-btn-secondary {
    background: #FFFFFF;
    color: var(--register-text);
    border: 1px solid var(--register-input-border);
    padding: 0 20px;
  }
  .register-btn-secondary:hover {
    background: #F8FAFC;
    border-color: #94A3B8;
    color: var(--register-text);
  }

  /* Responsive Breakpoints */
  @media (max-width: 1023px) {
    .register-header {
      width: calc(100% - 40px);
    }
    .register-card {
      width: calc(100% - 40px);
      margin-top: 24px;
      padding: 28px;
      border-radius: 20px;
    }
  }

  @media (max-width: 599px) {
    .register-page {
      padding-top: 12px;
      padding-bottom: 24px;
    }
    .register-header {
      width: calc(100% - 32px);
      min-height: 64px;
    }
    .register-brand-text {
      font-size: 1.2rem;
    }
    .register-login-prompt {
      display: none;
    }
    .register-card {
      width: calc(100% - 32px);
      margin-top: 20px;
      padding: 20px;
      border-radius: 18px;
      box-shadow: 0 6px 20px rgba(23, 32, 51, 0.06);
    }
    .register-heading {
      font-size: 24px;
    }
    .register-subtitle {
      font-size: 14px;
    }
    .register-step-circle {
      width: 34px;
      height: 34px;
      font-size: 13px;
    }
    .register-step-label {
      font-size: 12px;
    }
    .register-stepper-track {
      top: 17px;
      left: 30px;
      right: 30px;
    }
    .register-two-col {
      grid-template-columns: 1fr;
      gap: 18px;
    }
    .register-actions {
      flex-direction: column-reverse;
      gap: 10px;
    }
    .register-button {
      width: 100%;
    }
  }

  @media (max-width: 379px) {
    .register-role-options {
      grid-template-columns: 1fr;
      gap: 12px;
    }
  }
</style>
@endpush

@section('content')
<div class="register-page">

  {{-- Header Layout --}}
  <header class="register-header">
    <a href="{{ route('home') }}" class="register-brand" title="HomeServiceHub Homepage">
      <div class="register-brand-icon">
        <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M15 2.5L27.5 13V27H20V18H10V27H2.5V13L15 2.5Z" fill="#2563EB"/>
          <rect x="10" y="17" width="10" height="10" rx="0.5" fill="#2563EB"/>
          <rect x="11.5" y="12" width="7" height="6" rx="0.8" fill="white" opacity="0.85"/>
          <rect x="20" y="7" width="3.5" height="7" rx="0.4" fill="#2563EB"/>
        </svg>
      </div>
      <span class="register-brand-text">Home<span>Service</span>Hub</span>
    </a>

    <div class="register-header-login">
      <span class="register-login-prompt">Already have an account?</span>
      <a href="{{ route('login') }}" class="register-login-link">
        <i class="fas fa-sign-in-alt"></i> Log in
      </a>
    </div>
  </header>

  {{-- Centered Registration Card --}}
  <main class="register-card">

    {{-- Title & Subtitle --}}
    <h1 class="register-heading">Create Your Account</h1>
    <p class="register-subtitle">Choose how you want to use HomeServiceHub.</p>

    {{-- Four-Step Registration Progress Indicator --}}
    <div class="register-stepper" aria-label="Registration Progress">
      <div class="register-stepper-track">
        <div class="register-stepper-fill" id="stepperFill"></div>
      </div>

      <div class="register-step-node active" id="stepNode1">
        <div class="register-step-circle" id="stepCircle1">1</div>
        <span class="register-step-label">Role</span>
      </div>

      <div class="register-step-node" id="stepNode2">
        <div class="register-step-circle" id="stepCircle2">2</div>
        <span class="register-step-label">Details</span>
      </div>

      <div class="register-step-node" id="stepNode3">
        <div class="register-step-circle" id="stepCircle3">3</div>
        <span class="register-step-label">Profile</span>
      </div>

      <div class="register-step-node" id="stepNode4">
        <div class="register-step-circle" id="stepCircle4">4</div>
        <span class="register-step-label">Done</span>
      </div>
    </div>

    {{-- Backend Validation Alert --}}
    @if($errors->any())
      <div class="register-alert" role="alert">
        <i class="fas fa-exclamation-circle flex-shrink-0"></i>
        <div>{{ $errors->first() }}</div>
      </div>
    @endif

    {{-- Client-side Inline Alert --}}
    <div id="inlineAlert" class="register-alert" style="display:none;" role="alert">
      <i class="fas fa-exclamation-circle flex-shrink-0"></i>
      <div id="inlineAlertMsg"></div>
    </div>

    {{-- Registration Form --}}
    <form method="POST" action="{{ route('register.post') }}" id="registerForm" novalidate>
      @csrf

      {{-- Hidden input for selected role --}}
      <input type="hidden" name="role" id="selectedRole" value="{{ old('role', 'customer') }}" required>

      {{-- ========================================================
           STEP 1: ROLE SELECTION
           ======================================================== --}}
      <div id="regStep1">
        <div class="register-role-section-title">Select account type:</div>

        <div class="register-role-options">
          {{-- Customer Card --}}
          <div class="register-role-card {{ old('role', 'customer') === 'customer' ? 'selected' : '' }}" 
               id="roleCardCustomer" 
               onclick="setAccountRole('customer')" 
               role="button" 
               tabindex="0"
               onkeydown="if(event.key==='Enter'||event.key===' ')setAccountRole('customer');">
            <div class="register-role-radio">
              <i class="fas fa-check"></i>
            </div>
            <div class="register-role-icon">
              <i class="fas fa-user"></i>
            </div>
            <div class="register-role-title">Customer</div>
            <div class="register-role-desc">Book trusted professionals for your home.</div>
          </div>

          {{-- Service Pro Card --}}
          <div class="register-role-card {{ old('role') === 'provider' ? 'selected' : '' }}" 
               id="roleCardProvider" 
               onclick="setAccountRole('provider')" 
               role="button" 
               tabindex="0"
               onkeydown="if(event.key==='Enter'||event.key===' ')setAccountRole('provider');">
            <div class="register-role-radio">
              <i class="fas fa-check"></i>
            </div>
            <div class="register-role-icon">
              <i class="fas fa-tools"></i>
            </div>
            <div class="register-role-title">Service Pro</div>
            <div class="register-role-desc">Offer your services and connect with customers.</div>
          </div>
        </div>

        <div class="register-actions">
          <button type="button" class="register-button register-btn-primary w-100" onclick="goToStep(2)">
            Continue <i class="fas fa-arrow-right ms-2"></i>
          </button>
        </div>
      </div>

      {{-- ========================================================
           STEP 2: DETAILS FORM
           ======================================================== --}}
      <div id="regStep2" style="display:none;">
        <div class="register-form-grid">

          {{-- Full Name --}}
          <div class="register-field">
            <label class="register-label" for="regName">Full Name <span class="req">*</span></label>
            <div class="register-input-wrap">
              <i class="fas fa-user register-input-icon"></i>
              <input type="text" 
                     name="name" 
                     id="regName" 
                     class="register-input" 
                     placeholder="e.g. Rahul Sharma" 
                     value="{{ old('name') }}" 
                     autocomplete="name" 
                     required>
            </div>
          </div>

          {{-- Email Address --}}
          <div class="register-field">
            <label class="register-label" for="regEmail">Email Address <span class="req">*</span></label>
            <div class="register-input-wrap">
              <i class="fas fa-envelope register-input-icon"></i>
              <input type="email" 
                     name="email" 
                     id="regEmail" 
                     class="register-input" 
                     placeholder="your@email.com" 
                     value="{{ old('email') }}" 
                     autocomplete="email" 
                     required>
            </div>
          </div>

          {{-- Password & Confirm Password Row --}}
          <div class="register-two-col">
            <div class="register-field">
              <label class="register-label" for="regPassword">Password <span class="req">*</span></label>
              <div class="register-input-wrap">
                <i class="fas fa-lock register-input-icon"></i>
                <input type="password" 
                       name="password" 
                       id="regPassword" 
                       class="register-input has-toggle" 
                       placeholder="Min 8 characters" 
                       autocomplete="new-password" 
                       required>
                <button type="button" 
                        class="register-pwd-toggle" 
                        onclick="togglePasswordVisibility('regPassword', this.querySelector('i'))" 
                        aria-label="Toggle password visibility">
                  <i class="far fa-eye"></i>
                </button>
              </div>
              <span class="register-field-hint">Minimum 8 characters</span>
            </div>

            <div class="register-field">
              <label class="register-label" for="regPasswordConfirm">Confirm Password <span class="req">*</span></label>
              <div class="register-input-wrap">
                <i class="fas fa-lock register-input-icon"></i>
                <input type="password" 
                       name="password_confirmation" 
                       id="regPasswordConfirm" 
                       class="register-input has-toggle" 
                       placeholder="Repeat password" 
                       autocomplete="new-password" 
                       required>
                <button type="button" 
                        class="register-pwd-toggle" 
                        onclick="togglePasswordVisibility('regPasswordConfirm', this.querySelector('i'))" 
                        aria-label="Toggle confirm password visibility">
                  <i class="far fa-eye"></i>
                </button>
              </div>
              <span class="register-field-hint">Must match password</span>
            </div>
          </div>

          {{-- Phone & City Row --}}
          <div class="register-two-col">
            <div class="register-field">
              <label class="register-label" for="regPhone">Phone Number <span class="req">*</span></label>
              <div class="register-input-wrap">
                <i class="fas fa-phone register-input-icon"></i>
                <input type="tel" 
                       name="phone" 
                       id="regPhone" 
                       class="register-input" 
                       placeholder="+91 98765 43210" 
                       value="{{ old('phone') }}" 
                       autocomplete="tel" 
                       required>
              </div>
            </div>

            <div class="register-field">
              <label class="register-label" for="regCity">City (Gujarat) <span class="req">*</span></label>
              <div class="register-input-wrap">
                <i class="fas fa-map-marker-alt register-input-icon"></i>
                <select name="city" id="regCity" class="register-select" required>
                  <option value="Ahmedabad" {{ old('city', 'Ahmedabad') === 'Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                  <option value="Gandhinagar" {{ old('city') === 'Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
                </select>
              </div>
            </div>
          </div>

        </div>

        {{-- Step 2 Actions --}}
        <div class="register-actions">
          <button type="button" class="register-button register-btn-secondary" onclick="goToStep(1)">
            <i class="fas fa-arrow-left me-1"></i> Back
          </button>
          <button type="button" class="register-button register-btn-primary flex-grow-1" onclick="validateAndGoStep3()">
            Continue <i class="fas fa-arrow-right ms-1"></i>
          </button>
        </div>
      </div>

      {{-- ========================================================
           STEP 3: PROFILE FORM
           ======================================================== --}}
      <div id="regStep3" style="display:none;">

        {{-- Customer Profile --}}
        <div id="customerProfileFields">
          <div class="register-form-grid">
            <div class="register-note-box">
              <i class="fas fa-home"></i>
              <span>Provide your primary home address for service bookings.</span>
            </div>

            <div class="register-field">
              <label class="register-label" for="regAddress">Service Address / Home Locality</label>
              <div class="register-input-wrap">
                <i class="fas fa-map-pin register-input-icon"></i>
                <input type="text" 
                       name="address" 
                       id="regAddress" 
                       class="register-input" 
                       placeholder="e.g. Flat 402, Shivalik Heights, SG Highway" 
                       value="{{ old('address') }}" 
                       autocomplete="street-address">
              </div>
              <span class="register-field-hint">You can update or add addresses anytime during bookings.</span>
            </div>
          </div>
        </div>

        {{-- Service Pro Profile --}}
        <div id="providerProfileFields" style="display:none;">
          <div class="register-form-grid">
            <div class="register-note-box">
              <i class="fas fa-tools"></i>
              <span>Enter your professional service details to connect with clients.</span>
            </div>

            {{-- Business Name --}}
            <div class="register-field">
              <label class="register-label" for="regBusinessName">Business / Trade Name</label>
              <div class="register-input-wrap">
                <i class="fas fa-briefcase register-input-icon"></i>
                <input type="text" 
                       name="business_name" 
                       id="regBusinessName" 
                       class="register-input" 
                       placeholder="e.g. Apex Electrical Solutions or your name" 
                       value="{{ old('business_name') }}">
              </div>
            </div>

            {{-- Service Category --}}
            <div class="register-field">
              <label class="register-label" for="regCategory">Primary Service Category <span class="req">*</span></label>
              <div class="register-input-wrap">
                <i class="fas fa-wrench register-input-icon"></i>
                <select name="category_id" id="regCategory" class="register-select">
                  @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Experience & Hourly Rate --}}
            <div class="register-two-col">
              <div class="register-field">
                <label class="register-label" for="regExp">Experience (Years)</label>
                <div class="register-input-wrap">
                  <i class="fas fa-award register-input-icon"></i>
                  <input type="number" 
                         name="experience_years" 
                         id="regExp" 
                         class="register-input" 
                         placeholder="e.g. 5" 
                         min="0" 
                         value="{{ old('experience_years', '3') }}">
                </div>
              </div>

              <div class="register-field">
                <label class="register-label" for="regHourlyRate">Hourly Rate (₹) <span class="req">*</span></label>
                <div class="register-input-wrap">
                  <i class="fas fa-rupee-sign register-input-icon"></i>
                  <input type="number" 
                         name="hourly_rate" 
                         id="regHourlyRate" 
                         class="register-input" 
                         placeholder="e.g. 499" 
                         min="0" 
                         value="{{ old('hourly_rate', '499') }}">
                </div>
              </div>
            </div>

            {{-- Service Area --}}
            <div class="register-field">
              <label class="register-label" for="regServiceArea">Service Areas / Localities</label>
              <div class="register-input-wrap">
                <i class="fas fa-location-arrow register-input-icon"></i>
                <input type="text" 
                       name="service_area" 
                       id="regServiceArea" 
                       class="register-input" 
                       placeholder="e.g. Bodakdev, Satellite, SG Highway, Gandhinagar" 
                       value="{{ old('service_area') }}">
              </div>
              <span class="register-field-hint">Neighborhoods you cover</span>
            </div>

            {{-- Bio / Specialties --}}
            <div class="register-field">
              <label class="register-label" for="regBio">Professional Bio &amp; Specialties</label>
              <textarea name="bio" 
                        id="regBio" 
                        class="register-input" 
                        placeholder="Tell customers about your skills, certifications, and standards...">{{ old('bio') }}</textarea>
            </div>
          </div>
        </div>

        {{-- Step 3 Actions --}}
        <div class="register-actions">
          <button type="button" class="register-button register-btn-secondary" onclick="goToStep(2)">
            <i class="fas fa-arrow-left me-1"></i> Back
          </button>
          <button type="submit" id="submitRegBtn" class="register-button register-btn-primary flex-grow-1">
            <i class="fas fa-check-circle me-1"></i> Complete Registration
          </button>
        </div>
      </div>

    </form>

  </main>
</div>
@endsection

@push('scripts')
<script>
  let currentStep = 1;

  function setAccountRole(role) {
    document.getElementById('selectedRole').value = role;

    const customerCard = document.getElementById('roleCardCustomer');
    const providerCard = document.getElementById('roleCardProvider');

    if (role === 'provider') {
      providerCard.classList.add('selected');
      customerCard.classList.remove('selected');
    } else {
      customerCard.classList.add('selected');
      providerCard.classList.remove('selected');
    }
  }

  function updateStepper(step) {
    const fillPercent = ((step - 1) / 3) * 100;
    const fillElem = document.getElementById('stepperFill');
    if (fillElem) {
      fillElem.style.width = fillPercent + '%';
    }

    for (let i = 1; i <= 4; i++) {
      const node = document.getElementById('stepNode' + i);
      const circle = document.getElementById('stepCircle' + i);
      if (!node || !circle) continue;

      node.classList.remove('active', 'done');

      if (i < step) {
        node.classList.add('done');
        circle.innerHTML = '<i class="fas fa-check"></i>';
      } else if (i === step) {
        node.classList.add('active');
        circle.innerText = i;
      } else {
        circle.innerText = i;
      }
    }
  }

  function hideAlert() {
    const alertBox = document.getElementById('inlineAlert');
    if (alertBox) alertBox.style.display = 'none';
  }

  function showAlert(message) {
    const alertBox = document.getElementById('inlineAlert');
    const msgBox = document.getElementById('inlineAlertMsg');
    if (alertBox && msgBox) {
      msgBox.innerText = message;
      alertBox.style.display = 'flex';
      alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }

  function goToStep(step) {
    hideAlert();
    currentStep = step;

    document.getElementById('regStep1').style.display = (step === 1) ? 'block' : 'none';
    document.getElementById('regStep2').style.display = (step === 2) ? 'block' : 'none';
    document.getElementById('regStep3').style.display = (step === 3) ? 'block' : 'none';

    if (step === 3) {
      const role = document.getElementById('selectedRole').value;
      const isProvider = (role === 'provider');
      document.getElementById('customerProfileFields').style.display = isProvider ? 'none' : 'block';
      document.getElementById('providerProfileFields').style.display = isProvider ? 'block' : 'none';
    }

    updateStepper(step);
  }

  function validateAndGoStep3() {
    hideAlert();

    const name = document.getElementById('regName').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const password = document.getElementById('regPassword').value;
    const passwordConfirm = document.getElementById('regPasswordConfirm').value;
    const phone = document.getElementById('regPhone').value.trim();

    if (!name) {
      showAlert('Please enter your full name.');
      document.getElementById('regName').focus();
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
      showAlert('Please enter a valid email address.');
      document.getElementById('regEmail').focus();
      return;
    }

    if (!password || password.length < 8) {
      showAlert('Password must be at least 8 characters long.');
      document.getElementById('regPassword').focus();
      return;
    }

    if (password !== passwordConfirm) {
      showAlert('Passwords do not match. Please verify your password confirmation.');
      document.getElementById('regPasswordConfirm').focus();
      return;
    }

    if (!phone) {
      showAlert('Please provide your contact phone number.');
      document.getElementById('regPhone').focus();
      return;
    }

    goToStep(3);
  }

  // Handle Form Submission
  document.getElementById('registerForm').addEventListener('submit', function(e) {
    const role = document.getElementById('selectedRole').value;
    if (role === 'provider') {
      const category = document.getElementById('regCategory').value;
      const hourlyRate = document.getElementById('regHourlyRate').value;
      if (!category) {
        e.preventDefault();
        showAlert('Please select your primary service category.');
        return;
      }
      if (!hourlyRate || hourlyRate < 0) {
        e.preventDefault();
        showAlert('Please provide your valid hourly rate (₹).');
        return;
      }
    }

    // Advance stepper to Step 4 (Done)
    updateStepper(4);

    const submitBtn = document.getElementById('submitRegBtn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Creating account...';
    }
  });

  // Initialization: check URL params & old values
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedRole = urlParams.get('role');

    @if($errors->any())
      const role = document.getElementById('selectedRole').value;
      @if($errors->has('category_id') || $errors->has('hourly_rate') || $errors->has('business_name'))
        setAccountRole(role);
        goToStep(3);
      @else
        setAccountRole(role);
        goToStep(2);
      @endif
    @else
      if (preselectedRole === 'provider') {
        setAccountRole('provider');
        goToStep(2);
      } else {
        setAccountRole('{{ old("role", "customer") }}');
        goToStep(1);
      }
    @endif
  });
</script>
@endpush
