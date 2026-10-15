@extends('layouts.app')
@section('title','Find Trusted Service Providers - Home Service Hub')
@section('content')
<div class="container" style="padding:2.5rem 0;">
  {{-- Search Header --}}
  <div class="mb-4">
    <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(232,165,26,.12);color:var(--gold);border-radius:20px;padding:.25rem .75rem;font-size:.78rem;font-weight:800;margin-bottom:.5rem;">
      <i class="fas fa-map-marker-alt"></i> Ahmedabad &amp; Gandhinagar Verified Network
    </div>
    <h1 style="font-size:1.8rem;font-weight:900;margin:0;">Find Local Home Service Providers</h1>
    <p style="color:var(--text-secondary);font-size:.9rem;margin-top:.25rem;">Explore verified electricians, plumbers, carpenters, cleaners, and AC technicians in your neighborhood.</p>
  </div>

  <div class="row g-4">
    {{-- Filters --}}
    <div class="col-lg-3">
      <div class="card-custom p-4">
        <h6 style="font-weight:800;margin-bottom:1.25rem;"><i class="fas fa-filter me-2" style="color:var(--brand-blue);"></i>Filter Providers</h6>
        <form method="GET" action="{{ route('search') }}">
          <div class="form-group-custom">
            <label>Search Keyword</label>
            <input type="text" name="search" class="form-control" placeholder="Name or service..." value="{{ request('search') }}">
          </div>

          <div class="form-group-custom">
            <label>Service Category</label>
            <select name="category" class="form-control">
              <option value="">All Categories</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('category')==$cat->slug?'selected':'' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group-custom">
            <label>City (Gujarat)</label>
            <select name="city" class="form-control">
              <option value="">All Cities (Ahmedabad &amp; Gandhinagar)</option>
              <option value="Ahmedabad" {{ request('city')=='Ahmedabad'?'selected':'' }}>Ahmedabad</option>
              <option value="Gandhinagar" {{ request('city')=='Gandhinagar'?'selected':'' }}>Gandhinagar</option>
            </select>
          </div>

          <div class="form-group-custom">
            <label>Minimum Rating</label>
            <select name="min_rating" class="form-control">
              <option value="">Any Rating</option>
              @foreach([5,4,3] as $r)
                <option value="{{ $r }}" {{ request('min_rating')==$r?'selected':'' }}>{{ $r }}★ &amp; above</option>
              @endforeach
            </select>
          </div>

          <div class="form-group-custom mb-3">
            <div class="form-check p-0" style="background:rgba(236,72,153,.08);border:1px solid rgba(236,72,153,.25);border-radius:10px;padding:.6rem .8rem !important;">
              <input class="form-check-input ms-0 me-2" type="checkbox" name="neighborhood_trusted" id="chkNeighbor" value="1" {{ request('neighborhood_trusted') ? 'checked' : '' }}>
              <label class="form-check-label" for="chkNeighbor" style="font-size:.82rem;font-weight:700;color:var(--pink);cursor:pointer;">
                🏡 Neighborhood Trusted Only
              </label>
            </div>
          </div>

          <button type="submit" class="btn-primary-custom btn w-100" style="justify-content:center;padding:.75rem;"><i class="fas fa-search me-2"></i>Apply Filters</button>
          <a href="{{ route('search') }}" class="btn w-100 mt-2" style="background:var(--bg-light);border:1px solid var(--card-border);border-radius:10px;font-size:.82rem;font-weight:600;color:var(--text-secondary);text-align:center;">Clear All</a>
        </form>
      </div>
    </div>

    {{-- Results --}}
    <div class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div style="font-size:.88rem;color:var(--text-secondary);">
          Found <strong>{{ $providers->total() }}</strong> verified providers in <strong>{{ request('city') ?: 'Ahmedabad & Gandhinagar' }}</strong>
        </div>
        <div style="display:flex;gap:.5rem;">
          @auth
            @if(auth()->user()->isCustomer())
              <a href="{{ route('customer.split.booking') }}" class="btn-primary-custom btn btn-sm"><i class="fas fa-layer-group me-1"></i>Split Booking</a>
            @endif
          @else
            <a href="{{ route('login') }}" class="btn-primary-custom btn btn-sm"><i class="fas fa-layer-group me-1"></i>Split Booking</a>
          @endauth
        </div>
      </div>

      @if($providers->isEmpty())
        <div class="card-custom p-5 text-center">
          <div class="empty-state">
            <i class="fas fa-search" style="font-size:3rem;color:var(--text-muted);margin-bottom:1rem;"></i>
            <h5 style="font-weight:700;">No providers matched your filters</h5>
            <p style="color:var(--text-secondary);font-size:.88rem;">Try selecting a different category or clearing the neighborhood trusted filter.</p>
            <a href="{{ route('search') }}" class="btn-primary-custom btn btn-sm mt-2">Reset Filters</a>
          </div>
        </div>
      @else
      <div class="row g-4">
        @foreach($providers as $provider)
        <div class="col-md-6 col-xl-4">
          <div class="provider-card p-4 d-flex flex-column justify-content-between" style="height:100%;border:{{ $provider->isNeighborhoodTrusted() ? '1.5px solid rgba(236,72,153,.35)' : '1px solid var(--card-border)' }};">
            <div>
              @if($provider->isNeighborhoodTrusted())
              <div class="mb-2">
                <span style="background:rgba(236,72,153,.12);color:var(--pink);border-radius:6px;padding:.2rem .6rem;font-size:.72rem;font-weight:800;display:inline-block;">
                  <i class="fas fa-heart me-1"></i>Neighborhood Trusted
                </span>
              </div>
              @endif

              <div class="d-flex align-items-start gap-3 mb-3">
                <div class="provider-avatar">{{ strtoupper(substr($provider->name,0,1)) }}</div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center gap-1 flex-wrap">
                    <h6 class="mb-0" style="font-weight:700;">{{ $provider->name }}</h6>
                    <span class="verified-badge"><i class="fas fa-check-circle me-1"></i>Verified</span>
                  </div>
                  <div style="font-size:.78rem;color:var(--text-secondary);">{{ $provider->category->name }}</div>
                  <div class="d-flex align-items-center gap-1 mt-1">
                    <div class="stars">
                      @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=round($provider->average_rating)?'filled':'' }}"></i>@endfor
                    </div>
                    <span style="font-size:.78rem;font-weight:700;">{{ $provider->average_rating }}</span>
                    <span style="font-size:.72rem;color:var(--text-muted);">({{ $provider->completed_jobs }} jobs)</span>
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-between mb-2">
                <div style="font-size:.78rem;color:var(--text-secondary);">
                  <div><i class="fas fa-map-marker-alt me-1" style="color:var(--brand-blue);"></i>{{ $provider->service_area }}</div>
                  <div class="mt-1"><i class="fas fa-briefcase me-1" style="color:var(--brand-blue);"></i>{{ $provider->experience_years }} yrs experience</div>
                </div>
                <div style="text-align:right;">
                  <div style="font-size:1.1rem;font-weight:900;color:var(--brand-blue);">₹{{ number_format($provider->hourly_rate) }}/hr</div>
                  <div style="font-size:.72rem;color:var(--success);font-weight:600;"><i class="fas fa-circle me-1" style="font-size:.5rem;"></i>{{ $provider->is_available?'Available':'Busy' }}</div>
                </div>
              </div>

              {{-- Badges --}}
              <div class="d-flex gap-1 flex-wrap mt-2">
                @foreach($provider->badges->take(3) as $b)
                <span class="badge-pill" style="background:rgba(232,165,26,.12);color:var(--gold);font-size:.68rem;"><i class="{{ $b->icon }} me-1"></i>{{ $b->name }}</span>
                @endforeach
              </div>
            </div>

            <div>
              <div class="divider" style="margin:.75rem 0;"></div>
              <div class="d-flex gap-2">
                <a href="{{ route('provider.show', $provider) }}" class="btn-outline-custom btn btn-sm flex-grow-1" style="justify-content:center;padding:.45rem;">View Profile</a>
                @auth
                  @if(auth()->user()->isCustomer())
                    <a href="{{ route('customer.book', $provider) }}" class="btn-primary-custom btn btn-sm flex-grow-1" style="justify-content:center;padding:.45rem;">Book Now</a>
                  @endif
                @else
                  <a href="{{ route('login') }}" class="btn-primary-custom btn btn-sm flex-grow-1" style="justify-content:center;padding:.45rem;">Book Now</a>
                @endauth
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="mt-4">{{ $providers->withQueryString()->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
