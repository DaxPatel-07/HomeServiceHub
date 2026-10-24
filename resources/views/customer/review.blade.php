@extends('layouts.dashboard')
@section('title','Rate & Review')
@section('role-label','Customer Portal')
@section('page-title','Rate & Review Provider')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('customer.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('search') }}"><i class="fas fa-search"></i>Search Services</a></div>
<div class="nav-item"><a href="{{ route('customer.bookings') }}"><i class="fas fa-calendar-check"></i>My Bookings</a></div>
<div class="nav-section-title">Account</div>
<div class="nav-item"><a href="{{ route('customer.profile') }}"><i class="fas fa-user-circle"></i>My Profile</a></div>
@endsection
@section('content')
<div class="row g-4">
  {{-- Review Form Card --}}
  <div class="col-lg-7">
    <div class="card-custom p-4">
      <!-- Service Summary Header -->
      <div class="d-flex align-items-center gap-3 pb-3 mb-3" style="border-bottom:1px solid var(--card-border);">
        <div class="avatar-md" style="background:var(--brand-blue);color:#fff;font-size:1.3rem;">
          {{ strtoupper(substr($booking->provider->name, 0, 1)) }}
        </div>
        <div>
          <h5 style="font-weight:800;color:var(--brand-deep-blue);margin:0;">{{ $booking->provider->name }}</h5>
          <div style="font-size:.82rem;color:var(--text-secondary);">
            {{ $booking->category->name }} Service &bull; Booking <strong>{{ $booking->booking_code }}</strong>
          </div>
        </div>
      </div>

      <form method="POST" action="{{ route('customer.review.store', $booking) }}">
        @csrf
        
        <!-- Overall Rating -->
        <div class="mb-4 text-center">
          <label style="font-weight:800;display:block;margin-bottom:.5rem;color:var(--brand-deep-blue);font-size:1.05rem;">Overall Rating</label>
          <div class="d-flex justify-content-center gap-2 mb-2" id="starRating">
            @for($i=1;$i<=5;$i++)
            <i class="fas fa-star star-select" data-rating="{{ $i }}" style="font-size:2.2rem;color:var(--gold);cursor:pointer;transition:.2s;"></i>
            @endfor
          </div>
          <div id="ratingLabel" style="font-weight:700;color:var(--gold);font-size:.9rem;">5.0 / 5 - Excellent!</div>
          <input type="hidden" name="rating" id="ratingValue" value="5" required>
        </div>

        <div class="divider"></div>

        <!-- Sub-criteria ratings (Review.jpeg) -->
        <div class="mb-4">
          <div style="font-weight:700;font-size:.88rem;color:var(--brand-deep-blue);margin-bottom:.75rem;">Rate Specific Aspects</div>
          <div class="row g-3">
            @foreach(['Service Quality', 'Professionalism', 'Punctuality', 'Value for Money'] as $aspect)
            <div class="col-12 col-sm-6">
              <div style="background:var(--brand-cream);border:1px solid var(--card-border);border-radius:10px;padding:.65rem .85rem;">
                <div style="font-size:.78rem;font-weight:700;color:var(--brand-deep-blue);margin-bottom:.25rem;">{{ $aspect }}</div>
                <div class="d-flex gap-1" style="color:var(--gold);font-size:.8rem;">
                  <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        <!-- Quick Tags Chips (Review.jpeg) -->
        <div class="mb-3">
          <label style="font-weight:700;font-size:.85rem;display:block;margin-bottom:.5rem;color:var(--brand-deep-blue);">What did you like the most?</label>
          <div class="d-flex gap-2 flex-wrap" id="quickTags">
            @foreach(['On Time', 'Clean Work', 'Polite & Professional', 'Fair Pricing', 'Fast Completion', 'Great Advice'] as $tag)
            <span class="review-tag-chip" onclick="toggleTag(this, '{{ $tag }}')" style="background:var(--brand-cream);border:1px solid var(--card-border);border-radius:20px;padding:.3rem .8rem;font-size:.78rem;font-weight:600;color:var(--brand-deep-blue);cursor:pointer;transition:.2s;">
              + {{ $tag }}
            </span>
            @endforeach
          </div>
        </div>

        <!-- Comments -->
        <div class="form-group-custom">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label style="margin:0;font-weight:700;font-size:.85rem;">Detailed Feedback</label>
            <span id="charCount" style="font-size:.75rem;color:var(--text-muted);">0 / 500</span>
          </div>
          <textarea name="comment" id="commentText" class="form-control" rows="4" placeholder="Share specific details about the technician's punctuality, work cleanliness, and overall satisfaction..." maxlength="500" oninput="updateCharCount(this)"></textarea>
        </div>

        <button type="submit" class="btn-primary-custom btn w-100 mt-2" style="justify-content:center;padding:.85rem;font-size:1rem;border-radius:10px;">
          <i class="fas fa-paper-plane me-2"></i>Submit Honest Review
        </button>
      </form>
    </div>
  </div>

  {{-- Right Side Tips Card (Review.jpeg) --}}
  <div class="col-lg-5">
    <div class="card-custom p-4 mb-3" style="background:var(--brand-cream);border:1px solid var(--card-border);">
      <div style="font-size:1.8rem;margin-bottom:.5rem;">🏡</div>
      <h6 style="font-weight:800;color:var(--brand-deep-blue);margin-bottom:.5rem;">Your Feedback Empowers Neighbors</h6>
      <p style="font-size:.82rem;color:var(--text-secondary);line-height:1.6;margin-bottom:1rem;">
        Your review helps other homeowners in Ahmedabad &amp; Gandhinagar select trusted professionals, while also boosting your technician's verified Skill Score.
      </p>

      <div class="d-flex flex-column gap-2" style="font-size:.8rem;color:var(--brand-deep-blue);">
        <div class="d-flex align-items-center gap-2">
          <i class="fas fa-check-circle text-success"></i>
          <span>100% Verified Customer Reviews</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <i class="fas fa-check-circle text-success"></i>
          <span>Directly impacts Pro Skill Score</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <i class="fas fa-check-circle text-success"></i>
          <span>Helps maintain service safety standards</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const stars = document.querySelectorAll('.star-select');
const input = document.getElementById('ratingValue');
const ratingLabel = document.getElementById('ratingLabel');
const ratingLabels = {
  1: '1.0 / 5 - Poor',
  2: '2.0 / 5 - Fair',
  3: '3.0 / 5 - Good',
  4: '4.0 / 5 - Very Good',
  5: '5.0 / 5 - Excellent!'
};

function setStars(val) {
  input.value = val;
  if(ratingLabel && ratingLabels[val]) {
    ratingLabel.textContent = ratingLabels[val];
  }
  stars.forEach((s, idx) => {
    s.style.color = (idx < val) ? 'var(--gold)' : '#E2E8F0';
  });
}

stars.forEach(s => {
  s.addEventListener('click', () => setStars(parseInt(s.dataset.rating)));
  s.addEventListener('mouseover', () => setStars(parseInt(s.dataset.rating)));
});
setStars(5);

function toggleTag(el, tagText) {
  const textarea = document.getElementById('commentText');
  const isActive = el.classList.toggle('active');
  if(isActive) {
    el.style.background = 'var(--brand-blue)';
    el.style.color = '#fff';
    el.style.borderColor = 'var(--brand-blue)';
    textarea.value = textarea.value ? (textarea.value + '. ' + tagText) : tagText;
  } else {
    el.style.background = 'var(--brand-cream)';
    el.style.color = 'var(--brand-deep-blue)';
    el.style.borderColor = 'var(--card-border)';
  }
  updateCharCount(textarea);
}

function updateCharCount(el) {
  const countEl = document.getElementById('charCount');
  if(countEl) {
    countEl.textContent = el.value.length + ' / 500';
  }
}
</script>
@endpush
