@extends('layouts.app')
@section('title','Home')
@section('meta_description','Find trusted local home service providers near you.')
@section('content')

{{-- HERO --}}
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center gy-4 gy-lg-5">
      <div class="col-lg-6">
        <div class="hero-badge">
          <i class="fas fa-shield-alt" style="color:var(--color-primary);font-size:.85rem;"></i>
          <span>Trusted by Verified Local Homeowners</span>
        </div>
        <h1 class="hero-title">Everything Your Home Needs,<br class="d-none d-sm-inline"><span>All in One Place.</span></h1>
        <p class="hero-subtitle">Connect with verified local professionals for plumbing, electrical, cleaning, and repairs in Ahmedabad &amp; Gandhinagar with upfront pricing.</p>
        <div class="hero-search">
          <form action="{{ route('search') }}" method="GET">
            <div class="row g-2 align-items-center">
              <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control" placeholder="What does your home need?">
              </div>
              <div class="col-12 col-md-4">
                <select name="city" class="form-control">
                  <option value="">All Areas (Ahmedabad &amp; Gandhinagar)</option>
                  <option value="Ahmedabad">Ahmedabad</option>
                  <option value="Gandhinagar">Gandhinagar</option>
                </select>
              </div>
              <div class="col-12 col-md-3">
                <button type="submit" class="hero-search-btn w-100">
                  <i class="fas fa-search me-1"></i>Search
                </button>
              </div>
            </div>
          </form>
          <div class="hero-category-shortcuts mt-3">
            <div class="hero-pills-row">
              @foreach($categories as $cat)
              <a href="{{ route('search') }}?category={{ $cat->slug }}" class="category-pill" title="{{ $cat->name }}">
                <i class="{{ $cat->icon }} cat-icon" style="color:var(--color-primary);"></i>
                <span class="cat-name">{{ $cat->name }}</span>
              </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="hero-visual-card">
          <!-- Header: Certified Home Care Guarantee -->
          <div class="hero-trust-header">
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:var(--color-blue-tint);color:var(--color-primary);border-radius:999px;padding:.3rem .85rem;font-size:.78rem;font-weight:700;margin-bottom:.5rem;">
              <i class="fas fa-check-circle"></i> Verified Quality Guarantee
            </div>
            <h3 style="font-weight:800;color:var(--color-text-dark);margin:0;font-family:'Poppins',sans-serif;font-size:1.4rem;">Certified Home Care</h3>
            <p style="font-size:.88rem;color:var(--color-text-muted);margin:.3rem 0 0;line-height:1.5;">Reliable, background-checked technicians at your doorstep in under 60 minutes.</p>
          </div>

          <!-- Structured 2x2 Trust Metrics Grid -->
          <div class="hero-trust-grid">
            <!-- 4.9 / 5 Rating -->
            <div class="hero-trust-card">
              <div class="hero-trust-icon" style="background:rgba(217,119,6,.12);color:#D97706;">
                <i class="fas fa-star"></i>
              </div>
              <div>
                <div class="hero-trust-value">4.9 / 5 Rating</div>
                <div class="hero-trust-label">Verified Reviews</div>
              </div>
            </div>

            <!-- 100% Verified -->
            <div class="hero-trust-card">
              <div class="hero-trust-icon" style="background:rgba(30,122,79,.12);color:var(--color-success);">
                <i class="fas fa-shield-alt"></i>
              </div>
              <div>
                <div class="hero-trust-value">100% Verified</div>
                <div class="hero-trust-label">Background Checked</div>
              </div>
            </div>

            <!-- Fast 60-Min -->
            <div class="hero-trust-card">
              <div class="hero-trust-icon" style="background:var(--color-blue-tint);color:var(--color-primary);">
                <i class="fas fa-bolt"></i>
              </div>
              <div>
                <div class="hero-trust-value">Fast 60-Min</div>
                <div class="hero-trust-label">Quick Dispatch</div>
              </div>
            </div>

            <!-- Safe & Secure -->
            <div class="hero-trust-card">
              <div class="hero-trust-icon" style="background:rgba(25,83,188,.12);color:var(--color-support-blue);">
                <i class="fas fa-lock"></i>
              </div>
              <div>
                <div class="hero-trust-value">Safe &amp; Secure</div>
                <div class="hero-trust-label">Standard Pricing</div>
              </div>
            </div>
          </div>

          <!-- Trust Badges Strip -->
          <div class="hero-trust-footer">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-certificate" style="color:var(--color-primary);font-size:.9rem;"></i>
              <span style="font-size:.8rem;font-weight:600;color:var(--color-text-dark);">7-Day Service Warranty</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-headset" style="color:var(--color-primary);font-size:.9rem;"></i>
              <span style="font-size:.8rem;font-weight:600;color:var(--color-text-dark);">24/7 Dedicated Support</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- CATEGORY DISCOVERY --}}
<section class="section" style="background:#FFFFFF;padding:4rem 0;">
  <div class="container">
    <div class="text-center mb-5">
      <div style="display:inline-flex;align-items:center;gap:.4rem;background:var(--color-blue-tint);color:var(--color-primary);border:1px solid rgba(36,81,189,0.15);border-radius:999px;padding:.35rem 1rem;font-size:.78rem;font-weight:700;margin-bottom:.65rem;">
        <i class="fas fa-sparkles"></i> Comprehensive Services
      </div>
      <h2 class="section-title">What Does Your Home Need <span>Today?</span></h2>
      <p class="section-subtitle">Choose from verified, background-checked professionals ready to assist you</p>
    </div>

    <div class="row g-3 g-md-4">
      @foreach($categories as $cat)
      <div class="col-6 col-md-6 col-lg-3">
        <a href="{{ route('search') }}?category={{ $cat->slug }}" style="text-decoration:none;display:block;height:100%;">
          <div class="service-category-card">
            <div class="service-category-icon-box">
              <i class="{{ $cat->icon }}"></i>
            </div>
            <h6 class="service-category-name">{{ $cat->name }}</h6>
            <div class="service-category-pros">
              {{ $cat->providers()->where('verification_status','verified')->count() }} Verified Pros
            </div>
            <div class="service-category-price">
              From ₹{{ $cat->providers->min('hourly_rate') ?: '299' }}/hr
            </div>
          </div>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- STATISTICS BANNER --}}
<section class="stats-banner">
  <div class="container">
    <div class="row g-3 g-md-4 align-items-center text-center">
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="num" data-count="{{ $stats['customers'] }}">0</div>
          <div class="label">Happy Customers</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="num" data-count="{{ $stats['providers'] }}">0</div>
          <div class="label">Trusted Providers</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="num" data-count="{{ $stats['bookings'] }}">0</div>
          <div class="label">Jobs Completed</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="num">{{ $stats['rating'] }}★</div>
          <div class="label">Average Rating</div>
        </div>
      </div>
    </div>
  </div>
</section>

@guest
<section class="provider-cta-section">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-7">
        <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(230,220,211,.15);border:1px solid rgba(230,220,211,.25);border-radius:999px;padding:.35rem 1rem;margin-bottom:1.15rem;">
          <i class="fas fa-hard-hat" style="color:var(--color-warm-greige);font-size:.85rem;"></i>
          <span style="color:var(--color-warm-greige);font-size:.8rem;font-weight:700;">Earn With Your Skills</span>
        </div>
        <h2 style="font-size:clamp(1.75rem, 2.6vw, 2.25rem);font-weight:800;color:#FFFFFF;line-height:1.25;margin-bottom:.85rem;font-family:'Poppins',sans-serif;">
          Are You a Home Service Professional?<br><span style="color:var(--color-warm-greige);">Become a Provider Today</span>
        </h2>
        <p style="color:rgba(255,255,255,.85);margin-bottom:1.5rem;line-height:1.6;font-size:.95rem;max-width:560px;">Join 500+ verified professionals in Ahmedabad &amp; Gandhinagar. Get verified, set your rates, and start earning from day one. Background-checked and trusted by thousands of customers.</p>
        <div class="d-flex gap-3 gap-md-4 mb-4 flex-wrap align-items-center">
          @foreach(['Free Registration','Set Your Own Rates','Instant Bookings','Verified Badge'] as $b)
          <div style="display:flex;align-items:center;gap:.45rem;">
            <i class="fas fa-check-circle" style="color:var(--color-warm-greige);font-size:.95rem;"></i>
            <span style="color:rgba(255,255,255,.95);font-size:.86rem;font-weight:600;">{{ $b }}</span>
          </div>
          @endforeach
        </div>
        <a href="{{ route('become-provider') }}" class="btn" style="background:#FFFFFF;color:var(--color-primary);font-weight:700;font-size:.95rem;padding:.8rem 2rem;border-radius:12px;display:inline-flex;align-items:center;gap:.6rem;box-shadow:0 6px 20px rgba(0,0,0,0.18);transition:all .2s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.25)';" onmouseout="this.style.transform='none';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.18)';">
          <i class="fas fa-hard-hat"></i>Become a Provider
        </a>
      </div>
      <div class="col-lg-5 d-none d-lg-block">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
          @foreach([['fas fa-bolt','Electrician','₹400-800/hr'],['fas fa-wrench','Plumber','₹350-700/hr'],['fas fa-paint-roller','Painter','₹300-600/hr'],['fas fa-snowflake','AC Technician','₹500-900/hr']] as $item)
          <div class="provider-cta-card">
            <i class="{{ $item[0] }}" style="font-size:1.6rem;color:var(--color-warm-greige);display:block;margin-bottom:.5rem;"></i>
            <div style="color:#fff;font-weight:700;font-size:.9rem;font-family:'Poppins',sans-serif;">{{ $item[1] }}</div>
            <div style="color:var(--color-warm-greige);font-size:.8rem;font-weight:600;margin-top:.25rem;">{{ $item[2] }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
@endguest

{{-- NEIGHBOUR TRUSTED PROVIDERS (Ahmedabad & Gandhinagar) --}}
@if(isset($neighborhoodProviders) && $neighborhoodProviders->isNotEmpty())
<section class="section" style="background:linear-gradient(180deg, var(--color-bg) 0%, #FFFFFF 100%);padding:4rem 0;">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div>
        <div style="display:inline-flex;align-items:center;gap:.4rem;background:var(--color-blue-tint);color:var(--color-primary);border:1px solid rgba(36,81,189,.15);border-radius:999px;padding:.35rem .9rem;font-size:.75rem;font-weight:700;margin-bottom:.5rem;">
          <i class="fas fa-heart"></i> Local Community Verified
        </div>
        <h2 class="section-title">🏡 Neighbour <span>Trusted Providers</span></h2>
        @guest
          <p class="section-subtitle">Discover community-endorsed, background-checked professionals serving your local neighbourhood in Ahmedabad &amp; Gandhinagar. Sign in to view verified full profiles, direct pricing, ratings, reviews, and book trusted providers.</p>
        @else
          <p class="section-subtitle">Verified, highly-rated professionals trusted and endorsed by neighbours across Ahmedabad &amp; Gandhinagar</p>
        @endguest
      </div>
      <a href="{{ route('search') }}?neighborhood_trusted=1" class="btn-primary-custom btn btn-sm"><i class="fas fa-filter me-1"></i>View All Neighbour Trusted</a>
    </div>

    @guest
    {{-- Attractive Preview Cards for Logged-Out Visitors (No sensitive info disclosed) --}}
    <div class="row g-4">
      @foreach($neighborhoodProviders as $provider)
      <div class="col-md-6 col-lg-3">
        <div class="provider-card p-3 d-flex flex-column justify-content-between h-100"
             style="border:1.5px solid rgba(36,81,189,.18);box-shadow:0 8px 24px rgba(20,33,61,.06);border-radius:16px;cursor:pointer;position:relative;background:#fff;transition:transform .2s ease, box-shadow .2s ease;"
             onclick="window.location.href='{{ route('provider.show', $provider) }}'"
             title="Click to log in and view full provider profile">
          <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span style="background:var(--color-primary);color:#fff;border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;">
                <i class="fas fa-check me-1"></i>Neighbour Choice
              </span>
              <span style="background:var(--color-blue-tint);color:var(--color-primary);border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;">
                <i class="fas fa-shield-alt me-1"></i>Verified Pro
              </span>
            </div>

            <div class="d-flex align-items-start gap-3 mb-2">
              <div class="provider-avatar" style="width:48px;height:48px;font-size:1.2rem;background:linear-gradient(135deg, var(--color-primary), var(--color-support-blue));color:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;">
                <i class="{{ $provider->category->icon ?? 'fas fa-tools' }}"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0 fw-700" style="font-weight:700;font-size:.92rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">
                  Verified {{ $provider->category->name ?? 'Service' }} Pro
                </h6>
                <div style="color:var(--color-text-muted);font-size:.78rem;">{{ $provider->category->name ?? 'Professional' }} &bull; Local Expert</div>
                <div class="d-flex align-items-center gap-1 mt-1">
                  <i class="fas fa-star" style="color:#D97706;font-size:.72rem;"></i>
                  <span style="font-size:.78rem;font-weight:700;color:var(--color-text-dark);">Top Rated Neighbour Pro</span>
                </div>
              </div>
            </div>

            <div style="font-size:.75rem;color:var(--color-text-muted);margin-bottom:.5rem;">
              <i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>Ahmedabad &amp; Gandhinagar Network
            </div>

            <div style="background:var(--color-blue-tint);border:1px solid rgba(36,81,189,.12);border-radius:8px;padding:.45rem .65rem;margin-bottom:.75rem;font-size:.73rem;color:var(--color-text-dark);display:flex;align-items:center;gap:.4rem;">
              <i class="fas fa-lock" style="color:var(--color-primary);font-size:.72rem;"></i>
              <span>Sign in to unlock full name, rates &amp; reviews</span>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="border-top:1px solid var(--color-border);">
            <div style="font-size:.78rem;font-weight:700;color:var(--color-text-muted);">
              <i class="fas fa-tag me-1"></i>Protected Rate
            </div>
            <a href="{{ route('provider.show', $provider) }}" class="btn-primary-custom btn btn-sm" style="padding:.35rem .85rem;font-size:.78rem;" onclick="event.stopPropagation();">
              <i class="fas fa-lock me-1"></i>View Details
            </a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    {{-- Full Details Cards for Logged-In Customers / Authenticated Users --}}
    <div class="row g-4">
      @foreach($neighborhoodProviders as $provider)
      <div class="col-md-6 col-lg-3">
        <div class="provider-card p-3 d-flex flex-column justify-content-between h-100" style="border:1px solid var(--color-border);box-shadow:0 4px 16px rgba(20,33,61,.05);border-radius:16px;background:#fff;transition:transform .2s ease, box-shadow .2s ease;">
          <div>
            <div class="d-flex align-items-center gap-2 mb-2">
              <span style="background:var(--color-primary);color:#fff;border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;">
                <i class="fas fa-check me-1"></i>Neighbour Choice
              </span>
              <span style="font-size:.72rem;color:var(--color-text-muted);font-weight:600;">{{ round($provider->skill_score) }} Skill Score</span>
            </div>
            <div class="d-flex align-items-start gap-3 mb-2">
              <div class="provider-avatar" style="width:48px;height:48px;font-size:1.2rem;background:linear-gradient(135deg, var(--color-primary), var(--color-support-blue));color:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ strtoupper(substr($provider->name,0,1)) }}</div>
              <div class="flex-grow-1">
                <h6 class="mb-0 fw-700" style="font-weight:700;font-size:.92rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $provider->name }}</h6>
                <div style="color:var(--color-text-muted);font-size:.78rem;">{{ $provider->category->name }}</div>
                <div class="d-flex align-items-center gap-1 mt-1">
                  <i class="fas fa-star" style="color:#D97706;font-size:.72rem;"></i>
                  <span style="font-size:.78rem;font-weight:700;color:var(--color-text-dark);">{{ $provider->average_rating }}</span>
                  <span style="font-size:.72rem;color:var(--color-text-muted);">({{ $provider->completed_jobs }} jobs)</span>
                </div>
              </div>
            </div>
            <div style="font-size:.75rem;color:var(--color-text-muted);margin-bottom:.5rem;">
              <i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>{{ $provider->service_area }}
            </div>
            <div class="d-flex gap-1 flex-wrap mb-2">
              @foreach($provider->badges->take(2) as $badge)
              <span style="background:var(--color-blue-tint);color:var(--color-primary);border-radius:20px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;"><i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}</span>
              @endforeach
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="border-top:1px solid var(--color-border);">
            <div style="font-weight:800;color:var(--color-primary);font-size:.88rem;">₹{{ number_format($provider->hourly_rate) }}/hr</div>
            <a href="{{ route('provider.show', $provider) }}" class="btn-primary-custom btn btn-sm" style="padding:.35rem .85rem;font-size:.78rem;">View Details</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endguest
  </div>
</section>
@endif

{{-- POPULAR PROVIDERS (Only for Authenticated Users) --}}
@auth
<section class="section" style="padding:4rem 0;">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h2 class="section-title">Popular <span>Providers</span></h2>
        <p class="section-subtitle">Top-rated professionals near you in Ahmedabad &amp; Gandhinagar</p>
      </div>
      <a href="{{ route('search') }}" class="btn-outline-custom"><i class="fas fa-arrow-right"></i>View All</a>
    </div>
    <div class="row g-4">
      @foreach($topProviders as $provider)
      <div class="col-md-6 col-lg-4 animate-fade-up">
        <div class="provider-card p-4 h-100 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="provider-avatar" style="width:52px;height:52px;font-size:1.25rem;background:linear-gradient(135deg, var(--color-primary), var(--color-support-blue));color:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ strtoupper(substr($provider->name,0,1)) }}</div>
              <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2">
                  <h6 class="mb-0 fw-700" style="font-weight:700;font-size:.95rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $provider->name }}</h6>
                  <span class="verified-badge"><i class="fas fa-check-circle me-1"></i>Verified</span>
                </div>
                <div style="color:var(--color-text-muted);font-size:.8rem;margin-top:.2rem;">{{ $provider->category->name }}</div>
                <div class="d-flex align-items-center gap-2 mt-1">
                  <div class="stars">
                    @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i <= round($provider->average_rating) ? 'filled' : '' }}"></i>@endfor
                  </div>
                  <span style="font-size:.8rem;color:var(--color-text-muted);font-weight:600;">{{ $provider->average_rating }} ({{ $provider->reviews->count() }})</span>
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <div style="font-size:.8rem;color:var(--color-text-muted);"><i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>{{ $provider->service_area }}</div>
                <div style="font-size:.9rem;font-weight:800;color:var(--color-primary);margin-top:.25rem;">₹{{ number_format($provider->hourly_rate) }}/hr</div>
              </div>
              <a href="{{ route('provider.show', $provider) }}" class="btn-primary-custom btn btn-sm">View Details</a>
            </div>
          </div>
          <div>
            <div class="divider" style="margin:.75rem 0;"></div>
            <div class="d-flex gap-2 flex-wrap">
              @foreach($provider->badges->take(3) as $badge)
              <span style="background:var(--color-blue-tint);color:var(--color-primary);border-radius:20px;padding:.2rem .6rem;font-size:.7rem;font-weight:700;"><i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}</span>
              @endforeach
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endauth

{{-- HOW IT WORKS --}}
<section class="section" style="background:var(--color-bg);padding:4rem 0;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">How It <span>Works</span></h2>
      <p class="section-subtitle">Book a service in 3 simple steps</p>
    </div>
    <div class="row g-4 text-center">
      @foreach([['fas fa-search','Search','Find the right professional by category and location.','1'],['fas fa-calendar-check','Book','Choose your time slot and confirm the booking online.','2'],['fas fa-star','Rate','Get the job done and leave an honest review.','3']] as $step)
      <div class="col-md-4">
        <div class="card-custom p-4 h-100" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;">
          <div style="width:64px;height:64px;border-radius:18px;background:var(--color-blue-tint);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;border:1px solid rgba(36,81,189,.15);">
            <i class="{{ $step[0] }}" style="font-size:1.5rem;color:var(--color-primary);"></i>
          </div>
          <div style="font-size:.75rem;font-weight:800;color:var(--color-primary);text-transform:uppercase;letter-spacing:1px;margin-bottom:.5rem;">Step {{ $step[3] }}</div>
          <h5 style="font-weight:700;margin-bottom:.5rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $step[1] }}</h5>
          <p style="color:var(--color-text-muted);font-size:.88rem;line-height:1.5;">{{ $step[2] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>


{{-- SPLIT BOOKING PROMO --}}
@php
  $splitServices = [
    ['icon' => 'fas fa-bolt', 'name' => 'Electrical Work', 'time' => '9:00 AM', 'price' => 449],
    ['icon' => 'fas fa-wrench', 'name' => 'Plumbing', 'time' => '11:00 AM', 'price' => 399],
    ['icon' => 'fas fa-paint-roller', 'name' => 'Painting', 'time' => '2:00 PM', 'price' => 349],
  ];
  $splitTotal = array_sum(array_column($splitServices, 'price'));
@endphp
<section class="section" style="padding:4.5rem 0;background:#FFFFFF;">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-6">
        <div class="split-booking-card">
          <div style="display:flex;flex-direction:column;gap:.75rem;">
            @foreach($splitServices as $item)
            <div class="split-booking-item">
              <div class="split-booking-icon-box">
                <i class="{{ $item['icon'] }}"></i>
              </div>
              <div class="flex-grow-1">
                <div style="font-weight:700;font-size:.92rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $item['name'] }}</div>
                <div class="d-flex align-items-center gap-2 mt-1">
                  <span style="font-size:.8rem;color:var(--color-text-muted);"><i class="fas fa-clock me-1" style="color:var(--color-primary);"></i>{{ $item['time'] }}</span>
                  <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:0.68rem;font-weight:700;background:rgba(30,122,79,0.1);color:var(--color-success);padding:.12rem .45rem;border-radius:4px;"><i class="fas fa-check"></i>Confirmed</span>
                </div>
              </div>
              <div style="font-weight:800;font-size:1rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">₹{{ number_format($item['price']) }}</div>
            </div>
            @endforeach
            <div class="split-booking-total">
              <span style="font-weight:700;color:var(--color-text-dark);font-size:.95rem;">Total Amount</span>
              <span style="font-size:1.25rem;font-weight:900;color:var(--color-primary);font-family:'Poppins',sans-serif;">₹{{ number_format($splitTotal) }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div style="background:var(--color-blue-tint);border:1px solid rgba(36,81,189,.15);border-radius:999px;padding:.4rem 1rem;display:inline-flex;align-items:center;gap:.5rem;margin-bottom:1rem;color:var(--color-primary);font-size:.8rem;font-weight:700;">
          <i class="fas fa-layer-group"></i>
          <span>Exclusive Feature</span>
        </div>
        <h2 class="section-title">Book Multiple Services <span>In One Go</span></h2>
        <p style="color:var(--color-text-muted);margin-top:.75rem;line-height:1.6;font-size:1rem;">Our unique <strong>Split Booking</strong> lets you schedule Electrician + Plumber + Painter all in a single request. We'll find the best providers and create an optimized timeline for you.</p>
        @auth
          @if(auth()->user()->isCustomer())
            <a href="{{ route('customer.split.booking') }}" class="btn-primary-custom btn mt-2" style="padding:.75rem 1.85rem;font-size:.95rem;font-weight:700;border-radius:12px;box-shadow:0 6px 20px rgba(36,81,189,0.25);"><i class="fas fa-layer-group me-2"></i>Try Split Booking</a>
          @else
            <a href="{{ route('customer.split.booking') }}" class="btn-primary-custom btn mt-2" style="padding:.75rem 1.85rem;font-size:.95rem;font-weight:700;border-radius:12px;box-shadow:0 6px 20px rgba(36,81,189,0.25);"><i class="fas fa-layer-group me-2"></i>Try Split Booking</a>
          @endif
        @else
          <a href="{{ route('register') }}" class="btn-primary-custom btn mt-2" style="padding:.75rem 1.85rem;font-size:.95rem;font-weight:700;border-radius:12px;box-shadow:0 6px 20px rgba(36,81,189,0.25);"><i class="fas fa-layer-group me-2"></i>Try Split Booking</a>
        @endauth
      </div>
    </div>
  </div>
</section>
@endsection
@push('scripts')
<script>
document.querySelectorAll('.stat-item .num[data-count]').forEach(el => {
  const target = parseInt(el.dataset.count);
  let count = 0;
  const step = Math.ceil(target / 50);
  const timer = setInterval(() => {
    count = Math.min(count + step, target);
    el.textContent = count.toLocaleString() + '+';
    if (count >= target) clearInterval(timer);
  }, 30);
});
</script>
@endpush
