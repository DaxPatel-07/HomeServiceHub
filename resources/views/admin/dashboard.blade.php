@extends('layouts.dashboard')
@section('title','Admin Dashboard')
@section('role-label','Admin Panel')
@section('page-title','Admin Dashboard')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>Customers</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-section-title">Manage</div>
<div class="nav-item"><a href="{{ route('admin.categories') }}"><i class="fas fa-tags"></i>Categories</a></div>
<div class="nav-item"><a href="{{ route('admin.complaints') }}"><i class="fas fa-flag"></i>Complaints</a></div>
@endsection
@section('content')
<div class="row g-4">
  {{-- Stats --}}
  @foreach([['fas fa-users','Total Users',$stats['users'],'rgba(37,99,235,.1)','var(--brand-blue)'],['fas fa-hard-hat','Providers',$stats['providers'],'rgba(139,92,246,.1)','var(--purple)'],['fas fa-calendar-check','Bookings',$stats['bookings'],'rgba(232,165,26,.12)','var(--gold)'],['fas fa-rupee-sign','Revenue','₹'.number_format($stats['revenue']),'rgba(16,185,129,.1)','var(--success)']] as $s)
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">{{ $s[1] }}</div><div class="stat-value" style="font-size:1.5rem;">{{ $s[2] }}</div></div>
        <div class="stat-icon" style="background:{{ $s[3] }};"><i class="{{ $s[0] }}" style="color:{{ $s[4] }};"></i></div>
      </div>
    </div>
  </div>
  @endforeach

  {{-- Booking Status Chart + Pending Providers --}}
  <div class="col-lg-5">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-chart-pie me-2" style="color:var(--brand-blue);"></i>Bookings Overview</h6></div>
      <div class="card-body-custom"><canvas id="bookingChart" height="200"></canvas></div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card-custom">
      <div class="card-header-custom">
        <h6><i class="fas fa-user-clock me-2" style="color:var(--gold);"></i>Pending Provider Approvals <span class="badge-pill badge-status-pending ms-2">{{ $stats['pending_providers'] }}</span></h6>
        <a href="{{ route('admin.providers') }}" style="color:var(--brand-blue);font-size:.82rem;font-weight:600;">View All</a>
      </div>
      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead><tr><th>Name</th><th>Category</th><th>City</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($pendingProviders as $p)
            <tr>
              <td><div class="d-flex align-items-center gap-2"><div class="avatar-sm">{{ strtoupper(substr($p->user->name,0,1)) }}</div><div><div style="font-weight:600;font-size:.85rem;">{{ $p->business_name ?: $p->user->name }}</div><div style="font-size:.72rem;color:var(--text-muted);">{{ $p->user->email }}</div></div></div></td>
              <td style="font-size:.85rem;">{{ $p->category->name }}</td>
              <td style="font-size:.85rem;">{{ $p->user->city }}</td>
              <td>
                <div class="d-flex gap-1">
                  <form method="POST" action="{{ route('admin.providers.verify',$p) }}">@csrf<button class="btn btn-sm btn-success" style="font-size:.75rem;border-radius:6px;">Verify</button></form>
                  <form method="POST" action="{{ route('admin.providers.reject',$p) }}">@csrf<button class="btn btn-sm btn-danger" style="font-size:.75rem;border-radius:6px;">Reject</button></form>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:1.5rem;">No pending approvals</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Recent Bookings + Open Complaints --}}
  <div class="col-lg-7">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-calendar-check me-2" style="color:var(--brand-blue);"></i>Recent Bookings</h6></div>
      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead><tr><th>Customer</th><th>Provider</th><th>Service</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($recentBookings as $b)
            <tr>
              <td style="font-size:.85rem;">{{ $b->customer->name }}</td>
              <td style="font-size:.85rem;">{{ $b->provider->name }}</td>
              <td style="font-size:.85rem;">{{ $b->category->name }}</td>
              <td style="font-weight:700;color:var(--brand-blue);">₹{{ number_format($b->total_amount) }}</td>
              <td><span class="badge-pill badge-status-{{ $b->status }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-flag me-2" style="color:var(--danger);"></i>Open Complaints <span class="badge-pill badge-status-cancelled ms-2">{{ $stats['open_complaints'] }}</span></h6></div>
      <div class="card-body-custom">
        @forelse($openComplaints as $c)
        <div style="border-bottom:1px solid var(--card-border);padding:.65rem 0;">
          <div style="font-weight:600;font-size:.85rem;">{{ $c->subject }}</div>
          <div style="font-size:.75rem;color:var(--text-muted);">{{ $c->customer->name }} &bull; {{ $c->created_at->diffForHumans() }}</div>
        </div>
        @empty
        <div class="empty-state" style="padding:1rem;"><i class="fas fa-check-circle" style="color:var(--success);"></i><p>No open complaints</p></div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const bData = @json($bookingsByStatus);
new Chart(document.getElementById('bookingChart'),{
  type:'doughnut',
  data:{
    labels:['Completed','Pending','Accepted','Cancelled'],
    datasets:[{data:[bData.completed||0,bData.pending||0,bData.accepted||0,bData.cancelled||0],backgroundColor:['#1E7A4F','#D97706','#2451BD','#B3261E'],borderWidth:0}]
  },
  options:{responsive:true,cutout:'70%',plugins:{legend:{position:'bottom'}}}
});
</script>
@endpush
