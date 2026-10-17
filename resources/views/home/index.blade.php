@extends('layouts.app')
@section('title','Home')
@section('meta_description','Find trusted local home service providers near you.')
@section('content')

{{-- HERO --}}
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6">
        <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.2);border-radius:20px;padding:.35rem 1rem;margin-bottom:1.2rem;">
          <i class="fas fa-shield-alt" style="color:var(--brand-blue);font-size:.85rem;"></i>
          <span style="color:var(--brand-deep-blue);font-size:.82rem;font-weight:700;">Trusted by 25,000+ Happy Homeowners</span>
        </div>
        <h1 class="hero-title">Everything Your Home Needs,<br><span>All in One Place.</span></h1>
        <p class="hero-subtitle">Connect with verified local professionals for plumbing, electrical, cleaning, and repairs in Ahmedabad &amp; Gandhinagar with upfront pricing.</p>
        <div class="hero-search">
          <form action="{{ route('search') }}" method="GET">
            <div class="row g-2">
              <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="What does your home need?"></div>
              <div class="col-md-4">
                <select name="city" class="form-control">
                  <option value="">All Areas (Ahmedabad &amp; Gandhinagar)</option>
                  <option value="Ahmedabad">Ahmedabad</option>
                  <option value="Gandhinagar">Gandhinagar</option>
                </select>
              </div>
              <div class="col-md-3"><button type="submit" class="hero-search-btn w-100"><i class="fas fa-search me-2"></i>Search</button></div>
            </div>
          </form>
          <div class="d-flex gap-2 mt-3 flex-wrap">
            @foreach($categories as $cat)
            <a href="{{ route('search') }}?category={{ $cat->slug }}" class="category-pill">
              <i class="{{ $cat->icon }} cat-icon" style="color:{{ $cat->color }};"></i>
              <span class="cat-name">{{ $cat->name }}</span>
            </a>
            @endforeach
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-block">
        <div class="hero-visual-card">
          <!-- Floating Trust Cards -->
          <div class="hero-float-card" style="top:25px;left:20px;">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(232,165,26,.15);display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:1.1rem;"><i class="fas fa-star"></i></div>
            <div>
              <div style="font-weight:800;font-size:.85rem;color:var(--brand-deep-blue);line-height:1.2;">4.9 / 5 Rating</div>
              <div style="font-size:.72rem;color:var(--text-muted);">15,000+ Reviews</div>
            </div>
          </div>

          <div class="hero-float-card" style="top:35px;right:20px;animation-delay:.8s;">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;color:var(--success);font-size:1.1rem;"><i class="fas fa-check-shield"></i></div>
            <div>
              <div style="font-weight:800;font-size:.85rem;color:var(--brand-deep-blue);line-height:1.2;">100% Verified</div>
              <div style="font-size:.72rem;color:var(--text-muted);">Background Checked</div>
            </div>
          </div>

          <!-- Central Home Illustration -->
          <div style="text-align:center;padding:2rem 1rem;">
            <div style="width:130px;height:130px;border-radius:50%;background:#FFFFFF;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 12px 30px rgba(23,74,139,.12);margin-bottom:1rem;border:3px solid var(--brand-beige);">
              <i class="fas fa-home" style="font-size:3.5rem;color:var(--brand-blue);"></i>
            </div>
            <h4 style="font-weight:800;color:var(--brand-deep-blue);margin-bottom:.25rem;">Certified Home Care</h4>
            <p style="font-size:.85rem;color:var(--text-secondary);max-width:260px;margin:0 auto;">Reliable technicians at your doorstep in under 60 minutes.</p>
          </div>

          <div class="hero-float-card" style="bottom:30px;left:25px;animation-delay:1.5s;">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(37,99,235,.12);display:flex;align-items:center;justify-content:center;color:var(--brand-blue);font-size:1.1rem;"><i class="fas fa-bolt"></i></div>
            <div>
              <div style="font-weight:800;font-size:.85rem;color:var(--brand-deep-blue);line-height:1.2;">Fast 60-Min</div>
              <div style="font-size:.72rem;color:var(--text-muted);">Quick Dispatch</div>
            </div>
          </div>

          <div class="hero-float-card" style="bottom:25px;right:25px;animation-delay:2.2s;">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(60,155,145,.15);display:flex;align-items:center;justify-content:center;color:var(--teal);font-size:1.1rem;"><i class="fas fa-lock"></i></div>
            <div>
              <div style="font-weight:800;font-size:.85rem;color:var(--brand-deep-blue);line-height:1.2;">Safe &amp; Secure</div>
              <div style="font-size:.72rem;color:var(--text-muted);">Standard Pricing</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- CATEGORY DISCOVERY --}}
<section class="section" style="background:#FFFFFF;padding:3.5rem 0;">
  <div class="container">
    <div class="text-center mb-5">
      <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(37,99,235,.08);color:var(--brand-blue);border-radius:20px;padding:.3rem .9rem;font-size:.78rem;font-weight:700;margin-bottom:.5rem;">
        <i class="fas fa-sparkles"></i> Comprehensive Services
      </div>
      <h2 class="section-title">What Does Your Home Need <span>Today?</span></h2>
      <p class="section-subtitle">Choose from verified, background-checked professionals ready to assist you</p>
    </div>

    <div class="row g-3 g-md-4">
      @foreach($categories as $cat)
      <div class="col-6 col-md-4 col-lg-3">
        <a href="{{ route('search') }}?category={{ $cat->slug }}" style="text-decoration:none;display:block;">
          <div class="card-custom p-3 p-md-4 text-center h-100" style="transition:.25s;border:1px solid var(--card-border);background:var(--brand-cream);" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow)';this.style.borderColor='var(--brand-blue)';" onmouseout="this.style.transform='none';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--card-border)';">
            <div style="width:58px;height:58px;border-radius:16px;background:#FFFFFF;box-shadow:0 4px 14px rgba(23,74,139,.06);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;border:1px solid var(--card-border);">
              <i class="{{ $cat->icon }}" style="font-size:1.6rem;color:{{ $cat->color }};"></i>
            </div>
            <h6 style="font-weight:800;color:var(--brand-deep-blue);font-size:.95rem;margin-bottom:.35rem;">{{ $cat->name }}</h6>
            <div style="font-size:.78rem;color:var(--text-secondary);margin-bottom:.5rem;">
              {{ $cat->providers()->where('verification_status','verified')->count() }} Verified Pros
            </div>
            <div style="display:inline-block;background:rgba(37,99,235,.08);color:var(--brand-blue);border-radius:20px;padding:.2rem .65rem;font-size:.72rem;font-weight:700;">
              From ₹{{ $cat->providers->min('hourly_rate') ?: '299' }}/hr
            </div>
          </div>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>
<section class="stats-banner">
  <div class="container">
    <div class="row">
      <div class="col-6 col-md-3"><div class="stat-item"><div class="num" data-count="{{ $stats['customers'] }}">0</div><div class="label">Happy Customers</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><div class="num" data-count="{{ $stats['providers'] }}">0</div><div class="label">Trusted Providers</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><div class="num" data-count="{{ $stats['bookings'] }}">0</div><div class="label">Jobs Completed</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><div class="num">{{ $stats['rating'] }}★</div><div class="label">Average Rating</div></div></div>
    </div>
  </div>
</section>

@guest
<section style="background:linear-gradient(135deg, var(--brand-deep-blue), #1a3a6e);padding:3rem 0;">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-7">
        <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(232,165,26,.15);border:1px solid rgba(232,165,26,.3);border-radius:20px;padding:.3rem .9rem;margin-bottom:1rem;">
          <i class="fas fa-hard-hat" style="color:var(--gold);"></i>
          <span style="color:var(--gold);font-size:.8rem;font-weight:700;">Earn With Your Skills</span>
        </div>
        <h2 style="font-size:2rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:.75rem;">Are You a Home Service Professional?<br><span style="color:var(--gold);">Become a Provider Today</span></h2>
        <p style="color:rgba(255,255,255,.6);margin-bottom:1.5rem;">Join 500+ verified professionals in Ahmedabad &amp; Gandhinagar. Get verified, set your rates, and start earning from day one. Background-checked and trusted by thousands of customers.</p>
        <div class="d-flex gap-4 mb-3 flex-wrap">
          @foreach(['Free Registration','Set Your Own Rates','Instant Bookings','Verified Badge'] as $b)
          <div style="display:flex;align-items:center;gap:.4rem;">
            <i class="fas fa-check-circle" style="color:var(--gold);"></i>
            <span style="color:rgba(255,255,255,.8);font-size:.85rem;font-weight:600;">{{ $b }}</span>
          </div>
          @endforeach
        </div>
        <a href="{{ route('become-provider') }}" class="btn-primary-custom btn"><i class="fas fa-hard-hat me-2"></i>Register as Provider</a>
      </div>
      <div class="col-lg-5 d-none d-lg-block">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
          @foreach([['fas fa-bolt','Electrician','₹400-800/hr'],['fas fa-wrench','Plumber','₹350-700/hr'],['fas fa-paint-roller','Painter','₹300-600/hr'],['fas fa-snowflake','AC Technician','₹500-900/hr']] as $item)
          <div style="background:rgba(255,255,255,.06);border-radius:14px;padding:1.25rem;border:1px solid rgba(255,255,255,.1);text-align:center;">
            <i class="{{ $item[0] }}" style="font-size:1.5rem;color:var(--gold);display:block;margin-bottom:.5rem;"></i>
            <div style="color:#fff;font-weight:700;font-size:.88rem;">{{ $item[1] }}</div>
            <div style="color:var(--gold);font-size:.78rem;font-weight:600;margin-top:.25rem;">{{ $item[2] }}</div>
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
<section class="section" style="background:linear-gradient(180deg, rgba(232,165,26,.04) 0%, rgba(255,255,255,1) 100%);padding:4rem 0;">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div>
        <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(236,72,153,.1);color:var(--pink);border-radius:20px;padding:.3rem .8rem;font-size:.75rem;font-weight:800;margin-bottom:.5rem;">
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
             style="border:1.5px solid rgba(236,72,153,.3);box-shadow:0 8px 24px rgba(236,72,153,.08);cursor:pointer;position:relative;background:#fff;"
             onclick="window.location.href='{{ route('provider.show', $provider) }}'"
             title="Click to log in and view full provider profile">
          <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span style="background:var(--pink);color:#fff;border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:800;">
                <i class="fas fa-check me-1"></i>Neighbour Choice
              </span>
              <span style="background:rgba(37,99,235,.08);color:var(--brand-blue);border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;">
                <i class="fas fa-shield-alt me-1"></i>Verified Pro
              </span>
            </div>

            <div class="d-flex align-items-start gap-3 mb-2">
              <div class="provider-avatar" style="width:48px;height:48px;font-size:1.2rem;background:linear-gradient(135deg, var(--brand-blue), var(--brand-deep-blue));">
                <i class="{{ $provider->category->icon ?? 'fas fa-tools' }}"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0 fw-700" style="font-weight:800;font-size:.92rem;color:var(--brand-deep-blue);">
                  Verified {{ $provider->category->name ?? 'Service' }} Pro
                </h6>
                <div style="color:var(--text-secondary);font-size:.78rem;">{{ $provider->category->name ?? 'Professional' }} &bull; Local Expert</div>
                <div class="d-flex align-items-center gap-1 mt-1">
                  <i class="fas fa-star" style="color:var(--gold);font-size:.72rem;"></i>
                  <span style="font-size:.78rem;font-weight:700;color:var(--text-primary);">Top Rated Neighbour Pro</span>
                </div>
              </div>
            </div>

            <div style="font-size:.75rem;color:var(--text-secondary);margin-bottom:.5rem;">
              <i class="fas fa-map-marker-alt me-1" style="color:var(--brand-blue);"></i>Ahmedabad &amp; Gandhinagar Network
            </div>

            <div style="background:rgba(37,99,235,.05);border-radius:8px;padding:.45rem .65rem;margin-bottom:.75rem;font-size:.73rem;color:var(--text-secondary);display:flex;align-items:center;gap:.4rem;">
              <i class="fas fa-lock" style="color:var(--brand-blue);font-size:.72rem;"></i>
              <span>Sign in to unlock full name, rates &amp; reviews</span>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="border-top:1px solid var(--card-border);">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);">
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
        <div class="provider-card p-3 d-flex flex-column justify-content-between h-100" style="border:1.5px solid rgba(236,72,153,.3);box-shadow:0 8px 24px rgba(236,72,153,.08);">
          <div>
            <div class="d-flex align-items-center gap-2 mb-2">
              <span style="background:var(--pink);color:#fff;border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:800;">
                <i class="fas fa-check me-1"></i>Neighbour Choice
              </span>
              <span style="font-size:.72rem;color:var(--text-muted);">{{ round($provider->skill_score) }} Skill Score</span>
            </div>
            <div class="d-flex align-items-start gap-3 mb-2">
              <div class="provider-avatar" style="width:48px;height:48px;font-size:1.2rem;">{{ strtoupper(substr($provider->name,0,1)) }}</div>
              <div class="flex-grow-1">
                <h6 class="mb-0 fw-700" style="font-weight:800;font-size:.92rem;">{{ $provider->name }}</h6>
                <div style="color:var(--text-secondary);font-size:.78rem;">{{ $provider->category->name }}</div>
                <div class="d-flex align-items-center gap-1 mt-1">
                  <i class="fas fa-star" style="color:var(--gold);font-size:.72rem;"></i>
                  <span style="font-size:.78rem;font-weight:700;">{{ $provider->average_rating }}</span>
                  <span style="font-size:.72rem;color:var(--text-muted);">({{ $provider->completed_jobs }} jobs)</span>
                </div>
              </div>
            </div>
            <div style="font-size:.75rem;color:var(--text-secondary);margin-bottom:.5rem;">
              <i class="fas fa-map-marker-alt me-1" style="color:var(--brand-blue);"></i>{{ $provider->service_area }}
            </div>
            <div class="d-flex gap-1 flex-wrap mb-2">
              @foreach($provider->badges->take(2) as $badge)
              <span style="background:rgba(232,165,26,.1);color:var(--gold);border-radius:20px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;"><i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}</span>
              @endforeach
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="border-top:1px solid var(--card-border);">
            <div style="font-weight:800;color:var(--brand-blue);font-size:.88rem;">₹{{ number_format($provider->hourly_rate) }}/hr</div>
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
<section class="section">
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
        <div class="provider-card p-4">
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="provider-avatar">{{ strtoupper(substr($provider->name,0,1)) }}</div>
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0 fw-700" style="font-weight:700;">{{ $provider->name }}</h6>
                <span class="verified-badge"><i class="fas fa-check-circle me-1"></i>Verified</span>
              </div>
              <div style="color:var(--text-secondary);font-size:.8rem;margin-top:.2rem;">{{ $provider->category->name }}</div>
              <div class="d-flex align-items-center gap-2 mt-1">
                <div class="stars">
                  @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i <= round($provider->average_rating) ? 'filled' : '' }}"></i>@endfor
                </div>
                <span style="font-size:.8rem;color:var(--text-secondary);">{{ $provider->average_rating }} ({{ $provider->reviews->count() }})</span>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div style="font-size:.8rem;color:var(--text-secondary);"><i class="fas fa-map-marker-alt me-1" style="color:var(--brand-blue);"></i>{{ $provider->service_area }}</div>
              <div style="font-size:.85rem;font-weight:700;color:var(--brand-blue);margin-top:.25rem;">₹{{ number_format($provider->hourly_rate) }}/hr</div>
            </div>
            <a href="{{ route('provider.show', $provider) }}" class="btn-primary-custom btn btn-sm">View Details</a>
          </div>
          <div class="divider"></div>
          <div class="d-flex gap-2 flex-wrap">
            @foreach($provider->badges->take(3) as $badge)
            <span style="background:rgba(232,165,26,.1);color:var(--gold);border-radius:20px;padding:.2rem .6rem;font-size:.7rem;font-weight:700;"><i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}</span>
            @endforeach
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endauth

{{-- HOW IT WORKS --}}
<section class="section" style="background:var(--bg-light);">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">How It <span>Works</span></h2>
      <p class="section-subtitle">Book a service in 3 simple steps</p>
    </div>
    <div class="row g-4 text-center">
      @foreach([['fas fa-search','Search','Find the right professional by category and location.','1'],['fas fa-calendar-check','Book','Choose your time slot and confirm the booking online.','2'],['fas fa-star','Rate','Get the job done and leave an honest review.','3']] as $step)
      <div class="col-md-4">
        <div class="card-custom p-4">
          <div style="width:64px;height:64px;border-radius:16px;background:rgba(37,99,235,.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
            <i class="{{ $step[0] }}" style="font-size:1.5rem;color:var(--brand-blue);"></i>
          </div>
          <div style="font-size:.75rem;font-weight:800;color:var(--brand-blue);text-transform:uppercase;letter-spacing:1px;margin-bottom:.5rem;">Step {{ $step[3] }}</div>
          <h5 style="font-weight:700;margin-bottom:.5rem;">{{ $step[1] }}</h5>
          <p style="color:var(--text-secondary);font-size:.88rem;">{{ $step[2] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>


{{-- SPLIT BOOKING PROMO --}}
<section class="section">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-6">
        <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:20px;padding:2rem;border:1px solid rgba(16,185,129,.2);">
          <div style="display:flex;flex-direction:column;gap:.75rem;">
            @foreach([['fas fa-bolt','Electrical Work','9:00 AM','₹449'],['fas fa-wrench','Plumbing','11:00 AM','₹399'],['fas fa-paint-roller','Painting','2:00 PM','₹349']] as $item)
            <div class="timeline-item">
              <div class="timeline-dot" style="background:var(--success);"><i class="{{ $item[0] }}" style="color:#fff;"></i></div>
              <div class="flex-grow-1"><div style="font-weight:700;font-size:.9rem;">{{ $item[1] }}</div><div style="font-size:.8rem;color:var(--text-secondary);"><i class="fas fa-clock me-1"></i>{{ $item[2] }}</div></div>
              <div style="font-weight:800;color:var(--success);">{{ $item[3] }}</div>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.75rem 1rem;background:rgba(16,185,129,.08);border-radius:10px;">
              <span style="font-weight:700;">Total Amount</span>
              <span style="font-size:1.2rem;font-weight:900;color:var(--success);">₹1,197</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div style="background:rgba(16,185,129,.08);border-radius:10px;padding:.4rem .9rem;display:inline-flex;align-items:center;gap:.5rem;margin-bottom:1rem;">
          <i class="fas fa-layer-group" style="color:var(--success);"></i>
          <span style="color:var(--success);font-size:.8rem;font-weight:700;">Exclusive Feature</span>
        </div>
        <h2 class="section-title">Book Multiple Services <span>In One Go</span></h2>
        <p style="color:var(--text-secondary);margin-top:.75rem;">Our unique <strong>Split Booking</strong> lets you schedule Electrician + Plumber + Painter all in a single request. We'll find the best providers and create an optimized timeline for you.</p>
        @auth
          @if(auth()->user()->isCustomer())
            <a href="{{ route('customer.split.booking') }}" class="btn-primary-custom btn mt-2"><i class="fas fa-layer-group me-2"></i>Try Split Booking</a>
          @endif
        @else
          <a href="{{ route('register') }}" class="btn-primary-custom btn mt-2"><i class="fas fa-layer-group me-2"></i>Try Split Booking</a>
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
