@extends('layouts.app')
@section('title','Find Trusted Service Providers - Home Service Hub')
@section('content')
<div class="search-page-wrapper py-4 py-lg-5">
  <div class="search-page-container">
    {{-- Search Header --}}
    <div class="search-header-section mb-4">
      <div class="verified-network-pill">
        <i class="fas fa-map-marker-alt"></i> Ahmedabad &amp; Gandhinagar Verified Network
      </div>
      <h1 class="search-page-title">Find Local Home Service Providers</h1>
      <p class="search-page-subtitle">Explore verified electricians, plumbers, carpenters, cleaners, and AC technicians in your neighborhood.</p>
    </div>

    {{-- Mobile Filter Toggle Button --}}
    <div class="d-lg-none mb-3">
      @php
        $activeFiltersCount = (request('search') ? 1 : 0) + (request('category') ? 1 : 0) + (request('city') ? 1 : 0) + (request('min_rating') ? 1 : 0) + (request('neighborhood_trusted') ? 1 : 0);
      @endphp
      <button class="btn search-mobile-filter-btn w-100 d-flex align-items-center justify-content-between py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#searchFiltersPanel" aria-expanded="{{ $activeFiltersCount > 0 ? 'true' : 'false' }}" aria-controls="searchFiltersPanel">
        <span class="d-flex align-items-center gap-2">
          <i class="fas fa-sliders-h text-primary"></i>
          <span style="font-weight:700;">Filter &amp; Search Providers</span>
        </span>
        <span class="d-flex align-items-center gap-2">
          @if($activeFiltersCount > 0)
            <span class="badge bg-primary text-white rounded-pill px-2 py-1" style="font-size:.72rem;">{{ $activeFiltersCount }} active</span>
          @endif
          <i class="fas fa-chevron-down filter-toggle-chevron"></i>
        </span>
      </button>
    </div>

    <div class="search-layout-row">
      {{-- Filters Sidebar --}}
      <aside class="search-sidebar-col">
        <div class="collapse d-lg-block" id="searchFiltersPanel">
          <div class="search-filter-card">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <h3 class="filter-card-heading mb-0">
                <i class="fas fa-filter text-primary me-2"></i>Filter Providers
              </h3>
              @if(request()->hasAny(['search', 'category', 'city', 'min_rating', 'neighborhood_trusted']))
                <a href="{{ route('search') }}" class="small text-decoration-none text-muted" title="Reset all filters">Reset</a>
              @endif
            </div>

            <form method="GET" action="{{ route('search') }}">
              <div class="filter-form-group">
                <label for="filterSearch" class="filter-label">Search Keyword</label>
                <div class="filter-input-wrap">
                  <i class="fas fa-search filter-input-icon"></i>
                  <input type="text" id="filterSearch" name="search" class="form-control filter-control with-icon" placeholder="Name or service..." value="{{ request('search') }}">
                </div>
              </div>

              <div class="filter-form-group">
                <label for="filterCategory" class="filter-label">Service Category</label>
                <select id="filterCategory" name="category" class="form-select filter-control">
                  <option value="">All Categories</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category')==$cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="filter-form-group">
                <label for="filterCity" class="filter-label">City (Gujarat)</label>
                <select id="filterCity" name="city" class="form-select filter-control">
                  <option value="">All Cities (Ahmedabad &amp; Gandhinagar)</option>
                  <option value="Ahmedabad" {{ request('city')=='Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                  <option value="Gandhinagar" {{ request('city')=='Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
                </select>
              </div>

              <div class="filter-form-group">
                <label for="filterRating" class="filter-label">Minimum Rating</label>
                <select id="filterRating" name="min_rating" class="form-select filter-control">
                  <option value="">Any Rating</option>
                  @foreach([5,4,3] as $r)
                    <option value="{{ $r }}" {{ request('min_rating')==$r ? 'selected' : '' }}>{{ $r }}★ &amp; above</option>
                  @endforeach
                </select>
              </div>

              <div class="filter-form-group">
                <div class="trusted-checkbox-card {{ request('neighborhood_trusted') ? 'checked' : '' }}">
                  <div class="form-check m-0 d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" name="neighborhood_trusted" id="chkNeighbor" value="1" {{ request('neighborhood_trusted') ? 'checked' : '' }}>
                    <label class="form-check-label trusted-check-label" for="chkNeighbor">
                      🏡 Neighborhood Trusted Only
                    </label>
                  </div>
                </div>
              </div>

              <div class="filter-actions mt-3">
                <button type="submit" class="btn btn-primary-custom w-100 filter-btn-apply">
                  <i class="fas fa-search me-2"></i>Apply Filters
                </button>
                <a href="{{ route('search') }}" class="btn filter-btn-clear w-100 mt-2">Clear All</a>
              </div>
            </form>
          </div>
        </div>
      </aside>

      {{-- Results Column --}}
      <main class="search-results-col">
        <div class="results-header-bar d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <div class="results-count-text">
            Found <strong class="text-dark">{{ $providers->total() }}</strong> verified providers in <strong class="text-dark">{{ request('city') ?: 'Ahmedabad & Gandhinagar' }}</strong>
          </div>
          <div class="results-actions">
            @auth
              @if(auth()->user()->isCustomer())
                <a href="{{ route('customer.split.booking') }}" class="btn btn-primary-custom btn-sm"><i class="fas fa-layer-group me-1"></i>Split Booking</a>
              @endif
            @else
              <a href="{{ route('login') }}" class="btn btn-primary-custom btn-sm"><i class="fas fa-layer-group me-1"></i>Split Booking</a>
            @endauth
          </div>
        </div>

        @if($providers->isEmpty())
          <div class="search-empty-state card-custom p-5 text-center">
            <div class="empty-icon-wrap mb-3">
              <i class="fas fa-search"></i>
            </div>
            <h4 class="empty-title">No providers matched your filters</h4>
            <p class="empty-text">Try selecting a different category, expanding your city radius, or clearing filters.</p>
            <a href="{{ route('search') }}" class="btn btn-primary-custom btn-sm">Reset Filters</a>
          </div>
        @else
          <div class="provider-cards-grid">
            @foreach($providers as $provider)
            <div class="search-provider-card {{ $provider->isNeighborhoodTrusted() ? 'is-trusted' : '' }}">
              <div class="card-inner-top">
                @if($provider->isNeighborhoodTrusted())
                  <div class="card-badge-header mb-2">
                    <span class="badge-trusted">
                      <i class="fas fa-heart text-primary me-1"></i>Neighborhood Trusted
                    </span>
                  </div>
                @endif

                <div class="card-identity-header d-flex align-items-start gap-3 mb-3">
                  <div class="card-provider-avatar">
                    {{ strtoupper(substr($provider->name, 0, 1)) }}
                  </div>
                  <div class="card-identity-meta flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-1 flex-wrap mb-1">
                      <h4 class="card-provider-name text-truncate mb-0" title="{{ $provider->name }}">
                        {{ $provider->name }}
                      </h4>
                      @if($provider->verification_status === 'verified')
                        <span class="card-verified-tag" title="Verified Professional">
                          <i class="fas fa-check-circle me-1"></i>Verified
                        </span>
                      @endif
                    </div>
                    <div class="card-category-name">
                      <i class="{{ $provider->category->icon ?? 'fas fa-tools' }} me-1 text-primary"></i>{{ $provider->category->name }}
                    </div>
                    <div class="card-rating-row d-flex align-items-center gap-1 mt-1">
                      <div class="stars">
                        @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=round($provider->average_rating)?'filled':'' }}"></i>@endfor
                      </div>
                      <span class="card-rating-score">{{ number_format($provider->average_rating, 1) }}</span>
                      <span class="card-rating-jobs">({{ $provider->completed_jobs }} jobs)</span>
                    </div>
                  </div>
                </div>

                {{-- Location & Experience --}}
                <div class="card-meta-details mb-3">
                  <div class="meta-item">
                    <i class="fas fa-map-marker-alt text-primary"></i>
                    <span class="text-truncate" title="{{ $provider->service_area }}">{{ $provider->service_area }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="fas fa-briefcase text-primary"></i>
                    <span>{{ $provider->experience_years }} yrs experience</span>
                  </div>
                </div>

                {{-- Pricing & Availability Banner --}}
                <div class="card-pricing-bar d-flex align-items-center justify-content-between px-3 py-2 mb-3">
                  <div class="pricing-col">
                    <span class="pricing-label">Starting from</span>
                    <div class="pricing-figure">
                      ₹{{ number_format($provider->hourly_rate) }}<span class="pricing-unit">/hr</span>
                    </div>
                  </div>
                  <div class="availability-col">
                    <span class="status-indicator {{ $provider->is_available ? 'avail-online' : 'avail-busy' }}">
                      <i class="fas fa-circle"></i>
                      <span>{{ $provider->is_available ? 'Available' : 'Busy' }}</span>
                    </span>
                  </div>
                </div>

                {{-- Specialization Badges / Chips --}}
                @if($provider->badges->isNotEmpty())
                  <div class="card-chips-wrap d-flex flex-wrap gap-1 mb-3">
                    @foreach($provider->badges->take(2) as $badge)
                      <span class="provider-spec-chip" title="{{ $badge->description ?? $badge->name }}">
                        <i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}
                      </span>
                    @endforeach
                    @if($provider->badges->count() > 2)
                      <span class="provider-spec-chip chip-more" title="{{ $provider->badges->slice(2)->pluck('name')->join(', ') }}">
                        +{{ $provider->badges->count() - 2 }} more
                      </span>
                    @endif
                  </div>
                @endif
              </div>

              {{-- Action Buttons --}}
              <div class="card-actions-footer mt-auto pt-3 border-top">
                <div class="d-flex gap-2">
                  <a href="{{ route('provider.show', $provider) }}" class="btn btn-outline-custom flex-grow-1 card-btn">View Profile</a>
                  @auth
                    @if(auth()->user()->isCustomer())
                      <a href="{{ route('customer.book', $provider) }}" class="btn btn-primary-custom flex-grow-1 card-btn">Book Now</a>
                    @endif
                  @else
                    <a href="{{ route('login') }}" class="btn btn-primary-custom flex-grow-1 card-btn">Book Now</a>
                  @endauth
                </div>
              </div>
            </div>
            @endforeach
          </div>

          {{-- Pagination --}}
          <div class="search-pagination-wrap mt-4 pt-2">
            {{ $providers->withQueryString()->links() }}
          </div>
        @endif
      </main>
    </div>
  </div>
</div>
@endsection
