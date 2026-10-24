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
    <div style="background:rgba(217,119,6,.1);border:1px solid rgba(217,119,6,.25);border-radius:14px;padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
      <i class="fas fa-clock" style="color:#D97706;font-size:1.15rem;"></i>
      <div>
        <div style="font-weight:700;color:#D97706;font-size:.9rem;font-family:'Poppins',sans-serif;">Verification Pending</div>
        <div style="font-size:.82rem;color:var(--color-text-muted);">Your provider profile is currently under review by our admin team. You will be able to receive customer bookings once verified. <a href="{{ route('provider.profile') }}" style="color:#D97706;font-weight:700;">Update Profile</a></div>
      </div>
    </div>
  </div>
  @elseif($provider->verification_status === 'rejected')
  <div class="col-12">
    <div style="background:rgba(179,38,30,.08);border:1px solid rgba(179,38,30,.2);border-radius:14px;padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
      <i class="fas fa-times-circle" style="color:var(--color-danger);font-size:1.15rem;"></i>
      <div>
        <div style="font-weight:700;color:var(--color-danger);font-size:.9rem;font-family:'Poppins',sans-serif;">Verification Rejected</div>
        <div style="font-size:.82rem;color:var(--color-text-muted);">Your application was not approved. Please <a href="{{ route('provider.profile') }}" style="color:var(--color-danger);font-weight:700;">update your profile details</a> or reach out to support.</div>
      </div>
    </div>
  </div>
  @endif

  {{-- Welcome --}}
  <div class="col-12">
    <div class="welcome-banner" style="background:var(--gradient-banner);border-radius:18px;padding:1.75rem 2rem;box-shadow:var(--shadow-sm);">
      <div class="row align-items-center w-100 m-0">
        <div class="col-md-8 p-0">
          <h4 style="color:#fff;font-weight:700;margin:0;font-family:'Poppins',sans-serif;">Welcome back, {{ $provider->name }}! 👋</h4>
          <p style="color:rgba(255,255,255,.8);margin:.3rem 0 0;font-size:.9rem;">{{ $provider->category->name }} Provider &bull; {{ $provider->service_area }}</p>
          <div class="d-flex gap-2 mt-3 flex-wrap">
            @foreach($provider->badges as $badge)
            <span style="background:rgba(230,220,211,.2);color:var(--color-warm-greige);border:1px solid rgba(230,220,211,.3);border-radius:999px;padding:.25rem .75rem;font-size:.72rem;font-weight:700;"><i class="{{ $badge->icon }} me-1"></i>{{ $badge->name }}</span>
            @endforeach
          </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0 p-0">
          <div style="font-size:.8rem;color:rgba(255,255,255,.8);margin-bottom:.35rem;font-weight:600;">Availability Status</div>
          <div id="availToggle" onclick="toggleAvail()" style="display:inline-flex;align-items:center;gap:.5rem;background:{{ $provider->is_available ? 'rgba(30,122,79,.25)' : 'rgba(179,38,30,.25)' }};border:1px solid {{ $provider->is_available ? 'rgba(30,122,79,.5)' : 'rgba(179,38,30,.5)' }};border-radius:999px;padding:.45rem 1.1rem;cursor:pointer;backdrop-filter:blur(4px);">
            <div style="width:8px;height:8px;border-radius:50%;background:{{ $provider->is_available ? 'var(--color-success)' : 'var(--color-danger)' }};"></div>
            <span id="availText" style="color:#fff;font-size:.82rem;font-weight:700;">{{ $provider->is_available ? 'Available' : 'Unavailable' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Stats --}}
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:16px;padding:1.25rem;">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label" style="font-size:.8rem;color:var(--color-text-muted);font-weight:600;">Total Earnings</div><div class="stat-value" style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.5rem;color:var(--color-primary);">₹{{ number_format($stats['earnings']) }}</div></div>
        <div class="stat-icon" style="background:var(--color-blue-tint);width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-rupee-sign" style="color:var(--color-primary);font-size:1.1rem;"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:16px;padding:1.25rem;">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label" style="font-size:.8rem;color:var(--color-text-muted);font-weight:600;">Completed Jobs</div><div class="stat-value" style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.5rem;color:var(--color-success);">{{ $stats['completed'] }}</div></div>
        <div class="stat-icon" style="background:rgba(30,122,79,.12);width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-check-circle" style="color:var(--color-success);font-size:1.1rem;"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:16px;padding:1.25rem;">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label" style="font-size:.8rem;color:var(--color-text-muted);font-weight:600;">Avg Rating</div><div class="stat-value" style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.5rem;color:#D97706;">{{ $provider->average_rating }}★</div></div>
        <div class="stat-icon" style="background:rgba(217,119,6,.12);width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-star" style="color:#D97706;font-size:1.1rem;"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:16px;padding:1.25rem;">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label" style="font-size:.8rem;color:var(--color-text-muted);font-weight:600;">Skill Score</div><div class="stat-value" style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.5rem;color:var(--color-primary);">{{ round($provider->skill_score) }}/100</div></div>
        <div class="stat-icon" style="background:var(--color-blue-tint);width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-award" style="color:var(--color-primary);font-size:1.1rem;"></i></div>
      </div>
    </div>
  </div>

  {{-- Earnings Chart --}}
  <div class="col-lg-8">
    <div class="card-custom" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;overflow:hidden;box-shadow:var(--shadow-sm);">
      <div class="card-header-custom" style="background:#FFFFFF;border-bottom:1px solid var(--color-border);padding:1.25rem 1.5rem;">
        <h6 style="font-weight:700;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;"><i class="fas fa-chart-line me-2" style="color:var(--color-primary);"></i>Earnings Overview</h6>
      </div>
      <div class="card-body-custom p-4"><canvas id="earningsChart" height="80"></canvas></div>
    </div>
  </div>

  {{-- Skill Score Panel --}}
  <div class="col-lg-4">
    <div class="card-custom" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;overflow:hidden;box-shadow:var(--shadow-sm);">
      <div class="card-header-custom" style="background:#FFFFFF;border-bottom:1px solid var(--color-border);padding:1.25rem 1.5rem;">
        <h6 style="font-weight:700;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;"><i class="fas fa-award me-2" style="color:var(--color-primary);"></i>Skill Score Dashboard</h6>
      </div>
      <div class="card-body-custom text-center p-4">
        <div class="skill-score-meter mx-auto mb-3">
          <svg viewBox="0 0 100 100" width="120" height="120">
            <circle cx="50" cy="50" r="45" fill="none" stroke="#E2E8F0" stroke-width="10"/>
            <circle cx="50" cy="50" r="45" fill="none" stroke="var(--color-primary)" stroke-width="10"
              stroke-dasharray="{{ $provider->skill_score * 2.83 }} 283" stroke-linecap="round"/>
          </svg>
          <div class="score-text"><div class="score-num" style="color:var(--color-primary);font-family:'Poppins',sans-serif;font-weight:800;">{{ round($provider->skill_score) }}</div><div class="score-label" style="font-size:.72rem;color:var(--color-text-muted);font-weight:600;">Skill Score</div></div>
        </div>
        @foreach([['Rating','fas fa-star',($provider->average_rating/5)*40],['Completion','fas fa-check',$provider->completion_rate*.3],['Verified','fas fa-shield-alt',$provider->verification_status==='verified'?20:0]] as $m)
        <div class="d-flex align-items-center gap-2 mb-2">
          <i class="{{ $m[1] }}" style="width:16px;color:var(--color-primary);font-size:.8rem;"></i>
          <span style="font-size:.8rem;flex:1;text-align:left;color:var(--color-text-dark);font-weight:500;">{{ $m[0] }}</span>
          <div style="flex:2;background:#E2E8F0;border-radius:10px;height:6px;overflow:hidden;">
            <div style="width:{{ min($m[2],40) }}%;background:var(--color-primary);height:100%;border-radius:10px;"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Provider Booking Notifications --}}
  <div class="col-12">
    <div class="card-custom" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;overflow:hidden;box-shadow:var(--shadow-sm);">
      <div class="card-header-custom d-flex justify-content-between align-items-center" style="background:#FFFFFF;border-bottom:1px solid var(--color-border);padding:1.25rem 1.5rem;">
        <h6 style="font-weight:700;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;"><i class="fas fa-bell me-2" style="color:var(--color-primary);"></i>Booking Notifications ({{ $notifications->count() }})</h6>
        <a href="{{ route('provider.bookings') }}" style="color:var(--color-primary);font-size:.82rem;font-weight:600;">Manage Bookings</a>
      </div>
      <div class="card-body-custom p-0">
        @forelse($notifications as $notif)
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom flex-wrap gap-2" style="background: {{ $notif->status === 'pending' ? 'var(--color-blue-tint)' : '#fff' }};">
          <div class="d-flex align-items-center gap-3">
            <div class="stat-icon" style="background: {{ $notif->status === 'pending' ? 'rgba(36,81,189,0.15)' : 'var(--color-blue-tint)' }}; width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center;">
              <i class="fas {{ $notif->status === 'pending' ? 'fa-calendar-plus text-primary' : 'fa-calendar-check text-primary' }}"></i>
            </div>
            <div>
              <div style="font-weight:700; font-size:.9rem; color:var(--color-text-dark); font-family:'Poppins',sans-serif;">
                New {{ $notif->category->name }} Booking <span style="color:var(--color-primary);font-weight:800;">#{{ $notif->booking_code }}</span> request from <span style="color:var(--color-primary);">{{ $notif->customer->name }}</span>
              </div>
              <div style="font-size:.78rem; color:var(--color-text-muted); margin-top:.2rem;">
                <i class="fas fa-calendar-alt me-1" style="color:var(--color-primary);"></i>{{ $notif->booking_date->format('d M Y') }} at <strong>{{ $notif->formatted_time }}</strong> &bull;
                <i class="fas fa-map-marker-alt me-1" style="color:var(--color-primary);"></i>{{ $notif->city }} &bull;
                <i class="fas fa-receipt me-1" style="color:var(--color-primary);"></i>₹{{ number_format($notif->total_amount) }}
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge-pill badge-status-{{ $notif->status }}">{{ ucfirst(str_replace('_',' ',$notif->status)) }}</span>
            @if($notif->status === 'pending')
              <form method="POST" action="{{ route('provider.booking.accept', $notif) }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-success" style="font-size:.75rem; border-radius:8px; font-weight:600; padding:.35rem .85rem;">Accept</button>
              </form>
              <form method="POST" action="{{ route('provider.booking.reject', $notif) }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger" style="font-size:.75rem; border-radius:8px; font-weight:600; padding:.35rem .85rem;">Reject</button>
              </form>
            @endif
          </div>
        </div>
        @empty
        <div class="p-4 text-center text-muted" style="font-size:.85rem;">
          <i class="fas fa-check-circle me-1" style="color:var(--color-success);"></i>You have no pending booking alerts right now.
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Recent Bookings --}}
  <div class="col-12">
    <div class="card-custom" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;overflow:hidden;box-shadow:var(--shadow-sm);">
      <div class="card-header-custom d-flex justify-content-between align-items-center" style="background:#FFFFFF;border-bottom:1px solid var(--color-border);padding:1.25rem 1.5rem;">
        <h6 style="font-weight:700;margin:0;color:var(--color-text-dark);font-family:'Poppins',sans-serif;"><i class="fas fa-list me-2" style="color:var(--color-primary);"></i>Recent Bookings</h6>
        <a href="{{ route('provider.bookings') }}" style="color:var(--color-primary);font-size:.82rem;font-weight:600;">View All</a>
      </div>
      <div style="overflow-x:auto;">
        <table class="table-custom w-100">
          <thead><tr><th>Customer</th><th>Service</th><th>Date &amp; Time</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($bookings as $b)
            <tr>
              <td><div class="d-flex align-items-center gap-2"><div class="avatar-sm" style="width:32px;height:32px;border-radius:8px;background:var(--color-blue-tint);color:var(--color-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;">{{ strtoupper(substr($b->customer->name,0,1)) }}</div><span style="font-size:.85rem;font-weight:500;color:var(--color-text-dark);">{{ $b->customer->name }}</span></div></td>
              <td style="font-weight:600;color:var(--color-text-dark);">
                {{ $b->category->name }}
                <div style="font-size:.72rem;color:var(--color-text-muted);font-weight:700;">{{ $b->booking_code }}</div>
              </td>
              <td style="font-size:.85rem;color:var(--color-text-dark);">{{ $b->booking_date->format('d M Y') }}<br><span style="color:var(--color-text-muted);font-size:.75rem;">{{ $b->formatted_time }}</span></td>
              <td style="font-weight:700;color:var(--color-primary);">₹{{ number_format($b->total_amount) }}</td>
              <td><span class="badge-pill badge-status-{{ $b->status }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
              <td>
                <div class="d-flex gap-1">
                  @if($b->status==='pending')
                  <form method="POST" action="{{ route('provider.booking.accept',$b) }}">@csrf<button class="btn btn-sm btn-success" style="font-size:.75rem;border-radius:6px;font-weight:600;">Accept</button></form>
                  <form method="POST" action="{{ route('provider.booking.reject',$b) }}">@csrf<button class="btn btn-sm btn-danger" style="font-size:.75rem;border-radius:6px;font-weight:600;">Reject</button></form>
                  @elseif($b->status==='accepted')
                  <form method="POST" action="{{ route('provider.booking.complete',$b) }}">@csrf<button class="btn btn-sm btn-primary-custom" style="font-size:.75rem;border-radius:6px;font-weight:600;">Complete</button></form>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center" style="color:var(--color-text-muted);padding:2rem;">No bookings yet.</td></tr>
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
