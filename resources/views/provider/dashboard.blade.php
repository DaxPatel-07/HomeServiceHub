@extends('layouts.dashboard')
@section('title','Provider Dashboard')
@section('role-label','Provider Portal')
@section('page-title','Provider Dashboard')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('provider.dashboard') }}" class="active"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('provider.bookings') }}"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-item"><a href="{{ route('provider.profile') }}"><i class="fas fa-user-cog"></i>My Profile</a></div>
<div class="nav-section-title">Performance</div>
<div class="nav-item"><a href="{{ route('provider.show', $provider) }}"><i class="fas fa-external-link-alt"></i>Public Profile</a></div>
@endsection
@section('content')
<div class="row g-4">
  {{-- Verification Alert --}}
  @if($provider->verification_status === 'pending')
  <div class="col-12">
    <div style="background:rgba(232,165,26,.1);border:1px solid rgba(232,165,26,.3);border-radius:14px;padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
      <i class="fas fa-clock" style="color:var(--gold);font-size:1.1rem;"></i>
      <div>
        <div style="font-weight:700;color:var(--gold);font-size:.9rem;">Verification Pending</div>
        <div style="font-size:.82rem;color:var(--text-secondary);">Your provider profile is currently under review by our admin team. You will be able to receive customer bookings once verified. <a href="{{ route('provider.profile') }}" style="color:var(--gold);font-weight:600;">Update Profile</a></div>
      </div>
    </div>
  </div>
  @elseif($provider->verification_status === 'rejected')
  <div class="col-12">
    <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:14px;padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
      <i class="fas fa-times-circle" style="color:var(--danger);font-size:1.1rem;"></i>
      <div>
        <div style="font-weight:700;color:var(--danger);font-size:.9rem;">Verification Rejected</div>
        <div style="font-size:.82rem;color:var(--text-secondary);">Your application was not approved. Please <a href="{{ route('provider.profile') }}" style="color:var(--danger);font-weight:600;">update your profile details</a> or reach out to support.</div>
      </div>
    </div>
  </div>
  @endif

  {{-- Welcome --}}
  <div class="col-12">
    <div class="welcome-banner">
      <div class="row align-items-center w-100 m-0">
        <div class="col-md-8 p-0">
          <h4 style="color:#fff;font-weight:800;margin:0;">Welcome back, {{ $provider->name }}! 👋</h4>
          <p style="color:rgba(255,255,255,.65);margin:.3rem 0 0;font-size:.88rem;">{{ $provider->category->name }} Provider &bull; {{ $provider->service_area }}</p>
          <div class="d-flex gap-2 mt-2 flex-wrap">
            @foreach($provider->badges as $badge)
            <span style="background:rgba(232,165,26,.18);color:var(--gold);border-radius:20px;padding:.2rem .7rem;font-size:.72rem;font-weight:700;"><i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}</span>
            @endforeach
          </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0 p-0">
          <div style="font-size:.8rem;color:rgba(255,255,255,.6);margin-bottom:.25rem;">Availability</div>
          <div id="availToggle" onclick="toggleAvail()" style="display:inline-flex;align-items:center;gap:.5rem;background:{{ $provider->is_available ? 'rgba(16,185,129,.2)' : 'rgba(239,68,68,.2)' }};border:1px solid {{ $provider->is_available ? 'rgba(16,185,129,.4)' : 'rgba(239,68,68,.4)' }};border-radius:20px;padding:.4rem 1rem;cursor:pointer;">
            <div style="width:8px;height:8px;border-radius:50%;background:{{ $provider->is_available ? 'var(--success)' : 'var(--danger)' }};"></div>
            <span id="availText" style="color:#fff;font-size:.82rem;font-weight:600;">{{ $provider->is_available ? 'Available' : 'Unavailable' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Stats --}}
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">Total Earnings</div><div class="stat-value" style="font-size:1.4rem;">₹{{ number_format($stats['earnings']) }}</div></div>
        <div class="stat-icon" style="background:rgba(16,185,129,.1);"><i class="fas fa-rupee-sign" style="color:var(--success);"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">Completed Jobs</div><div class="stat-value">{{ $stats['completed'] }}</div></div>
        <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="fas fa-check-circle" style="color:var(--brand-blue);"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">Avg Rating</div><div class="stat-value">{{ $provider->average_rating }}★</div></div>
        <div class="stat-icon" style="background:rgba(232,165,26,.12);"><i class="fas fa-star" style="color:var(--gold);"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">Skill Score</div><div class="stat-value">{{ round($provider->skill_score) }}/100</div></div>
        <div class="stat-icon" style="background:rgba(139,92,246,.1);"><i class="fas fa-award" style="color:var(--purple);"></i></div>
      </div>
    </div>
  </div>

  {{-- Earnings Chart --}}
  <div class="col-lg-8">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-chart-line me-2" style="color:var(--brand-blue);"></i>Earnings Overview</h6></div>
      <div class="card-body-custom"><canvas id="earningsChart" height="80"></canvas></div>
    </div>
  </div>

  {{-- Skill Score Panel --}}
  <div class="col-lg-4">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-award me-2" style="color:var(--purple);"></i>Skill Score Dashboard</h6></div>
      <div class="card-body-custom text-center">
        <div class="skill-score-meter mx-auto mb-3">
          <svg viewBox="0 0 100 100" width="120" height="120">
            <circle cx="50" cy="50" r="45" fill="none" stroke="#E2E8F0" stroke-width="10"/>
            <circle cx="50" cy="50" r="45" fill="none" stroke="var(--purple)" stroke-width="10"
              stroke-dasharray="{{ $provider->skill_score * 2.83 }} 283" stroke-linecap="round"/>
          </svg>
          <div class="score-text"><div class="score-num" style="color:var(--purple);">{{ round($provider->skill_score) }}</div><div class="score-label">Skill Score</div></div>
        </div>
        @foreach([['Rating','fas fa-star',($provider->average_rating/5)*40],['Completion','fas fa-check',$provider->completion_rate*.3],['Verified','fas fa-shield-alt',$provider->verification_status==='verified'?20:0]] as $m)
        <div class="d-flex align-items-center gap-2 mb-2">
          <i class="{{ $m[1] }}" style="width:16px;color:var(--brand-blue);font-size:.8rem;"></i>
          <span style="font-size:.8rem;flex:1;text-align:left;">{{ $m[0] }}</span>
          <div style="flex:2;background:#E2E8F0;border-radius:10px;height:6px;overflow:hidden;">
            <div style="width:{{ min($m[2],40) }}%;background:var(--brand-blue);height:100%;border-radius:10px;"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Provider Booking Notifications --}}
  <div class="col-12">
    <div class="card-custom">
      <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-bell me-2" style="color:var(--brand-blue);"></i>Booking Notifications ({{ $notifications->count() }})</h6>
        <a href="{{ route('provider.bookings') }}" style="color:var(--brand-blue);font-size:.82rem;font-weight:600;">Manage Bookings</a>
      </div>
      <div class="card-body-custom p-0">
        @forelse($notifications as $notif)
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom flex-wrap gap-2" style="background: {{ $notif->status === 'pending' ? 'rgba(232,165,26,.06)' : '#fff' }};">
          <div class="d-flex align-items-center gap-3">
            <div class="stat-icon" style="background: {{ $notif->status === 'pending' ? 'rgba(232,165,26,.15)' : 'rgba(37,99,235,.1)' }}; width:44px; height:44px; border-radius:12px;">
              <i class="fas {{ $notif->status === 'pending' ? 'fa-calendar-plus text-warning' : 'fa-calendar-check text-primary' }}"></i>
            </div>
            <div>
              <div style="font-weight:700; font-size:.9rem;">
                New {{ $notif->category->name }} Booking request from <span style="color:var(--brand-deep-blue);">{{ $notif->customer->name }}</span>
              </div>
              <div style="font-size:.78rem; color:var(--text-muted); margin-top:.2rem;">
                <i class="fas fa-calendar-alt me-1" style="color:var(--brand-blue);"></i>{{ $notif->booking_date->format('d M Y') }} at <strong>{{ $notif->formatted_time }}</strong> &bull;
                <i class="fas fa-map-marker-alt me-1" style="color:var(--brand-blue);"></i>{{ $notif->city }} &bull;
                <i class="fas fa-receipt me-1" style="color:var(--brand-blue);"></i>₹{{ number_format($notif->total_amount) }}
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge-pill badge-status-{{ $notif->status }}">{{ ucfirst(str_replace('_',' ',$notif->status)) }}</span>
            @if($notif->status === 'pending')
              <form method="POST" action="{{ route('provider.booking.accept', $notif) }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-success" style="font-size:.75rem; border-radius:6px; font-weight:600;">Accept</button>
              </form>
              <form method="POST" action="{{ route('provider.booking.reject', $notif) }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger" style="font-size:.75rem; border-radius:6px; font-weight:600;">Reject</button>
              </form>
            @endif
          </div>
        </div>
        @empty
        <div class="p-3 text-center text-muted" style="font-size:.85rem;">
          <i class="fas fa-check-circle me-1 text-success"></i>You have no pending booking alerts right now.
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Recent Bookings --}}
  <div class="col-12">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-list me-2" style="color:var(--brand-blue);"></i>Recent Bookings</h6><a href="{{ route('provider.bookings') }}" style="color:var(--brand-blue);font-size:.82rem;font-weight:600;">View All</a></div>
      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead><tr><th>Customer</th><th>Service</th><th>Date &amp; Time</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($bookings as $b)
            <tr>
              <td><div class="d-flex align-items-center gap-2"><div class="avatar-sm">{{ strtoupper(substr($b->customer->name,0,1)) }}</div><span style="font-size:.85rem;font-weight:500;">{{ $b->customer->name }}</span></div></td>
              <td style="font-weight:600;">{{ $b->category->name }}</td>
              <td style="font-size:.85rem;">{{ $b->booking_date->format('d M Y') }}<br><span style="color:var(--text-muted);font-size:.75rem;">{{ $b->formatted_time }}</span></td>
              <td style="font-weight:700;color:var(--brand-blue);">₹{{ number_format($b->total_amount) }}</td>
              <td><span class="badge-pill badge-status-{{ $b->status }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
              <td>
                <div class="d-flex gap-1">
                  @if($b->status==='pending')
                  <form method="POST" action="{{ route('provider.booking.accept',$b) }}">@csrf<button class="btn btn-sm btn-success" style="font-size:.75rem;border-radius:6px;">Accept</button></form>
                  <form method="POST" action="{{ route('provider.booking.reject',$b) }}">@csrf<button class="btn btn-sm btn-danger" style="font-size:.75rem;border-radius:6px;">Reject</button></form>
                  @elseif($b->status==='accepted')
                  <form method="POST" action="{{ route('provider.booking.complete',$b) }}">@csrf<button class="btn btn-sm btn-primary" style="font-size:.75rem;border-radius:6px;">Complete</button></form>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center" style="color:var(--text-muted);padding:2rem;">No bookings yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const earningsData = @json($earningsMonthly);
const labels = earningsData.map(d => months[d.month-1]);
const data = earningsData.map(d => d.total);
new Chart(document.getElementById('earningsChart'), {
  type: 'line',
  data: { labels, datasets: [{ label: 'Earnings (₹)', data, borderColor: 'var(--brand-blue)', backgroundColor: 'rgba(37,99,235,.08)', borderWidth: 2.5, fill: true, tension: 0.4, pointBackgroundColor: 'var(--brand-blue)' }] },
  options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { grid: { color: '#f0f0f0' } }, x: { grid: { display: false } } } }
});
function toggleAvail() {
  fetch('{{ route('provider.availability') }}', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'} })
  .then(r=>r.json()).then(data => {
    const el = document.getElementById('availToggle');
    const txt = document.getElementById('availText');
    if(data.available) { el.style.background='rgba(16,185,129,.2)'; txt.textContent='Available'; }
    else { el.style.background='rgba(239,68,68,.2)'; txt.textContent='Unavailable'; }
  });
}
</script>
@endpush
