@extends('layouts.app')
@section('title', $provider->name . ' - Provider Profile')
@section('content')
<div class="container" style="padding:2.5rem 0;">
  <div class="row g-4">
    <div class="col-lg-8">
      {{-- Profile Header --}}
      <div class="card-custom p-4 mb-4" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-sm);">
        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start text-center text-sm-start gap-3 gap-sm-4">
          <div class="provider-avatar" style="width:84px;height:84px;font-size:2.2rem;background:linear-gradient(135deg, var(--color-primary), var(--color-support-blue));color:#fff;border-radius:20px;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($provider->name,0,1)) }}</div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 flex-wrap mb-1">
              <h1 style="font-size:1.6rem;font-weight:800;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $provider->name }}</h1>
              <span class="verified-badge"><i class="fas fa-check-circle me-1"></i>Verified Provider</span>
              @if($provider->isNeighborhoodTrusted())
              <span style="background:var(--color-blue-tint);color:var(--color-primary);border:1px solid rgba(36,81,189,.2);border-radius:999px;padding:.25rem .75rem;font-size:.75rem;font-weight:700;">
                <i class="fas fa-heart me-1"></i>Neighborhood Trusted
              </span>
              @endif
            </div>
            <div style="color:var(--color-text-muted);font-size:.9rem;margin-bottom:.5rem;"><i class="{{ $provider->category->icon }} me-1" style="color:var(--color-primary);"></i>{{ $provider->category->name }}</div>
            <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-3 flex-wrap">
              <div class="stars">@for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=round($provider->average_rating)?'filled':'' }}"></i>@endfor</div>
              <span style="font-size:.85rem;font-weight:700;color:var(--color-text-dark);">{{ $provider->average_rating }} ({{ $provider->reviews->count() }} reviews)</span>
              <span style="font-size:.85rem;color:var(--color-text-muted);"><i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>{{ $provider->service_area }}</span>
            </div>
            <div class="d-flex gap-2 mt-3 flex-wrap justify-content-center justify-content-sm-start">
              @foreach($provider->badges as $badge)
              <span class="badge-pill" style="background:var(--color-blue-tint);color:var(--color-primary);border:1px solid rgba(36,81,189,.15);border-radius:999px;font-size:.72rem;padding:.2rem .65rem;"><i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}</span>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Neighborhood Endorsement Banner --}}
      @if($provider->isNeighborhoodTrusted())
      <div class="p-3 mb-4" style="background:linear-gradient(135deg, var(--color-blue-tint) 0%, #FFFFFF 100%);border:1.5px solid rgba(36,81,189,.2);border-radius:16px;">
        <div class="d-flex align-items-center gap-3">
          <div style="width:46px;height:46px;background:var(--color-primary);color:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
            <i class="fas fa-handshake"></i>
          </div>
          <div>
            <div style="font-weight:700;color:var(--color-text-dark);font-size:.92rem;font-family:'Poppins',sans-serif;">Neighborhood Endorsed Professional</div>
            <div style="font-size:.82rem;color:var(--color-text-muted);line-height:1.5;">
              This provider is actively trusted by verified homeowners in <strong>{{ $provider->service_area }}</strong> with a <strong>{{ $provider->average_rating }}★</strong> rating and {{ $provider->completed_jobs }} successfully completed jobs.
            </div>
          </div>
        </div>
      </div>
      @endif

      {{-- Tabs --}}
      <ul class="nav nav-tabs mb-4" style="border-bottom:2px solid var(--color-border);">
        <li class="nav-item"><a class="nav-link active fw-600" style="font-weight:600;" data-bs-toggle="tab" href="#about">Overview</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reviews">Reviews ({{ $recentReviews->count() }})</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#availability">Availability</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="about">
          <div class="card-custom p-4 mb-3" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;">
            <h5 style="font-weight:700;margin-bottom:.75rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">About Provider</h5>
            <p style="color:var(--color-text-muted);font-size:.92rem;line-height:1.7;">{{ $provider->bio ?: 'Professional home service provider with years of experience.' }}</p>
            <div class="row g-3 mt-2">
              @foreach([['fas fa-briefcase','Experience',$provider->experience_years.' Years'],['fas fa-check-circle','Completed Jobs',$provider->completed_jobs],['fas fa-star','Average Rating',$provider->average_rating.'/5'],['fas fa-percentage','Completion Rate',$provider->completion_rate.'%']] as $item)
              <div class="col-12 col-sm-6">
                <div style="display:flex;align-items:center;gap:.75rem;padding:.85rem;background:var(--color-bg);border-radius:12px;border:1px solid var(--color-border);">
                  <i class="{{ $item[0] }}" style="color:var(--color-primary);width:16px;"></i>
                  <div><div style="font-size:.72rem;color:var(--color-text-muted);">{{ $item[1] }}</div><div style="font-weight:700;font-size:.9rem;color:var(--color-text-dark);">{{ $item[2] }}</div></div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="reviews">
          <div class="card-custom p-4" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 style="font-weight:700;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">Neighbor &amp; Customer Reviews</h5>
              <div style="font-size:.85rem;color:var(--color-text-muted);">
                <strong style="color:#D97706;">{{ $provider->average_rating }}</strong> out of 5 stars
              </div>
            </div>

            @forelse($recentReviews as $review)
            <div style="border-bottom:1px solid var(--color-border);padding:1rem 0;">
              <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <div class="avatar-sm" style="width:34px;height:34px;border-radius:8px;background:var(--color-blue-tint);color:var(--color-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;">{{ strtoupper(substr($review->customer->name,0,1)) }}</div>
                <div>
                  <div style="font-weight:700;font-size:.88rem;color:var(--color-text-dark);">{{ $review->customer->name }}</div>
                  <div style="font-size:.75rem;color:var(--color-text-muted);">
                    <i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>{{ $review->customer->city ?? 'Ahmedabad' }} &bull; {{ $review->created_at->diffForHumans() }}
                  </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-2">
                  <span style="background:rgba(30,122,79,.1);color:var(--color-success);border-radius:6px;padding:.15rem .5rem;font-size:.68rem;font-weight:700;">
                    <i class="fas fa-check-circle me-1"></i>Verified Service
                  </span>
                  <div class="stars">@for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=$review->rating?'filled':'' }}"></i>@endfor</div>
                </div>
              </div>
              <p style="font-size:.87rem;color:var(--color-text-muted);margin:0;line-height:1.6;">{{ $review->comment }}</p>
            </div>
            @empty
            <div class="empty-state p-4 text-center"><i class="fas fa-star" style="font-size:2rem;color:var(--color-text-muted);margin-bottom:.5rem;"></i><p style="color:var(--color-text-muted);">No reviews yet.</p></div>
            @endforelse
          </div>
        </div>

        <div class="tab-pane fade" id="availability">
          <div class="card-custom p-4" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;">
            <h5 style="font-weight:700;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">Availability Status</h5>
            <div class="d-flex align-items-center gap-2 mt-3">
              <div class="{{ $provider->is_available ? 'dot-online' : 'dot-offline' }}"></div>
              <span style="font-weight:600;color:var(--color-text-dark);">{{ $provider->is_available ? 'Available for Booking in Ahmedabad & Gandhinagar' : 'Currently Busy with Active Jobs' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Booking Sidebar --}}
    <div class="col-lg-4">
      <div class="card-custom p-4" style="position:sticky;top:80px;background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-sm);">
        <div class="text-center mb-4">
          <div style="font-size:1.75rem;font-weight:900;color:var(--color-primary);font-family:'Poppins',sans-serif;">₹{{ number_format($provider->hourly_rate) }}<span style="font-size:.9rem;font-weight:500;color:var(--color-text-muted)">/hr</span></div>
          <div style="font-size:.82rem;color:var(--color-text-muted);margin-top:.25rem;"><i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>Servicing {{ $provider->service_area }}</div>
        </div>
        {{-- Skill Score Ring --}}
        <div class="text-center mb-4">
          <div class="skill-score-meter mx-auto">
            <svg viewBox="0 0 100 100" width="120" height="120">
              <circle cx="50" cy="50" r="45" fill="none" stroke="#E2E8F0" stroke-width="10"/>
              <circle cx="50" cy="50" r="45" fill="none" stroke="var(--color-primary)" stroke-width="10"
                stroke-dasharray="{{ $provider->skill_score * 2.83 }} 283"
                stroke-linecap="round"/>
            </svg>
            <div class="score-text"><div class="score-num" style="color:var(--color-primary);font-family:'Poppins',sans-serif;font-weight:800;">{{ round($provider->skill_score) }}</div><div class="score-label" style="font-size:.72rem;color:var(--color-text-muted);font-weight:600;">AI Trust Score</div></div>
          </div>
        </div>
        @auth
          @if(auth()->user()->isCustomer())
            <a href="{{ route('customer.book', $provider) }}" class="btn-primary-custom btn w-100 mb-2" style="justify-content:center;padding:.85rem;font-size:1rem;"><i class="fas fa-calendar-plus me-2"></i>Book Now</a>
          @endif
        @else
          <a href="{{ route('login') }}" class="btn-primary-custom btn w-100 mb-2" style="justify-content:center;padding:.85rem;"><i class="fas fa-sign-in-alt me-2"></i>Login to Book</a>
        @endauth

      </div>
    </div>
  </div>
</div>
@endsection
