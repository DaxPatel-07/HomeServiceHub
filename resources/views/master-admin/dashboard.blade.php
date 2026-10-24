@extends('layouts.dashboard')
@section('title','Master Admin Dashboard')
@section('role-label','Master Admin')
@section('page-title','Master Admin Dashboard')
@section('sidebar-links')
<div class="nav-section-title">System</div>
<div class="nav-item"><a href="{{ route('master.dashboard') }}" class="active"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('master.admins') }}"><i class="fas fa-user-shield"></i>Manage Admins</a></div>
<div class="nav-section-title">Platform</div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>All Users</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>All Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar"></i>All Bookings</a></div>
<div class="nav-section-title">Analytics</div>
<div class="nav-item"><a href="{{ route('master.reports') }}"><i class="fas fa-chart-bar"></i>Reports</a></div>
<div class="nav-item"><a href="{{ route('master.settings') }}"><i class="fas fa-cog"></i>Settings</a></div>
@endsection
@section('content')
<div class="row g-4">
  {{-- System Overview Banner --}}
  <div class="col-12">
    <div style="background:linear-gradient(135deg,var(--brand-deep-blue),#1a3a6e);border-radius:var(--border-radius-lg);padding:1.5rem 2rem;">
      <div class="row">
        <div class="col-md-8">
          <h5 style="color:#fff;font-weight:800;margin-bottom:.3rem;">System Overview</h5>
          <p style="color:rgba(255,255,255,.6);font-size:.82rem;margin:0;">Last Updated: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
        <div class="col-md-4 text-md-end">
          <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);border-radius:20px;padding:.35rem .9rem;">
            <div style="width:8px;height:8px;border-radius:50%;background:var(--success);"></div>
            <span style="color:var(--success);font-size:.82rem;font-weight:700;">All Systems Operational</span>
          </div>
        </div>
      </div>
      <div class="row g-3 mt-2">
        @foreach([['Uptime',$health['uptime'],'fas fa-server','success'],['DB Status',$health['database'],'fas fa-database','success'],['API Usage',$health['api_usage'],'fas fa-plug','warning'],['Security',$health['security'],'fas fa-shield-alt','info']] as $h)
        <div class="col-6 col-md-3">
          <div style="background:rgba(255,255,255,.05);border-radius:12px;padding:1rem;text-align:center;border:1px solid rgba(255,255,255,.06);">
            <i class="{{ $h[2] }}" style="color:var(--{{ $h[3] }});font-size:1.2rem;display:block;margin-bottom:.4rem;"></i>
            <div style="color:#fff;font-weight:700;font-size:.95rem;">{{ $h[1] }}</div>
            <div style="color:rgba(255,255,255,.5);font-size:.72rem;">{{ $h[0] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Key Stats --}}
  @foreach([['fas fa-users','Total Users',$systemStats['total_users'],'rgba(37,99,235,.1)','var(--brand-blue)'],['fas fa-hard-hat','Providers',$systemStats['providers'],'rgba(139,92,246,.1)','var(--purple)'],['fas fa-check-circle','Completed Jobs',$systemStats['completed_bookings'],'rgba(16,185,129,.1)','var(--success)'],['fas fa-rupee-sign','Revenue','₹'.number_format($systemStats['total_revenue']),'rgba(232,165,26,.12)','var(--gold)']] as $s)
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between">
        <div><div class="stat-label">{{ $s[1] }}</div><div class="stat-value" style="font-size:1.4rem;">{{ $s[2] }}</div></div>
        <div class="stat-icon" style="background:{{ $s[3] }};"><i class="{{ $s[0] }}" style="color:{{ $s[4] }};"></i></div>
      </div>
    </div>
  </div>
  @endforeach

  {{-- Analytics Chart --}}
  <div class="col-lg-8">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-chart-bar me-2" style="color:var(--brand-blue);"></i>Booking Analytics</h6></div>
      <div class="card-body-custom"><canvas id="analyticsChart" height="80"></canvas></div>
    </div>
  </div>

  {{-- User Growth --}}
  <div class="col-lg-4">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-user-plus me-2" style="color:var(--success);"></i>User Growth</h6></div>
      <div class="card-body-custom">
        @foreach([['Total Users',$systemStats['total_users'],'var(--brand-blue)'],['Customers',$systemStats['customers'],'var(--gold)'],['Providers',$systemStats['providers'],'var(--purple)'],['Verified',$systemStats['verified_providers'],'var(--success)'],['Admins',$systemStats['admins'],'var(--danger)']] as $u)
        <div class="d-flex justify-content-between align-items-center" style="padding:.5rem 0;border-bottom:1px solid var(--card-border);">
          <span style="font-size:.85rem;color:var(--text-secondary);">{{ $u[0] }}</span>
          <span style="font-weight:700;color:{{ $u[2] }};">{{ $u[1] }}</span>
        </div>
        @endforeach
        <div class="mt-3">
          <div style="font-size:.8rem;font-weight:600;color:var(--text-secondary);margin-bottom:.5rem;">Platform Health</div>
          <div class="d-flex justify-content-between" style="font-size:.8rem;"><span>Active Users</span><span style="font-weight:700;color:var(--success);">{{ $health['active_users'] }}</span></div>
          <div class="d-flex justify-content-between mt-1" style="font-size:.8rem;"><span>Security Alerts</span><span style="font-weight:700;color:var(--danger);">{{ $health['security_alerts'] }}</span></div>
          <div class="d-flex justify-content-between mt-1" style="font-size:.8rem;"><span>Last Backup</span><span style="font-weight:700;">{{ $health['last_backup'] }}</span></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Recent Users --}}
  <div class="col-lg-6">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-user-clock me-2" style="color:var(--brand-blue);"></i>Recent Registrations</h6></div>
      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
          <tbody>
            @foreach($recentUsers as $u)
            <tr>
              <td><div class="d-flex align-items-center gap-2"><div class="avatar-sm">{{ strtoupper(substr($u->name,0,1)) }}</div><span style="font-size:.85rem;font-weight:500;">{{ $u->name }}</span></div></td>
              <td style="font-size:.82rem;color:var(--text-secondary);">{{ $u->email }}</td>
              <td><span class="badge-pill badge-status-{{ $u->role==='customer'?'accepted':($u->role==='provider'?'pending':'completed') }}">{{ ucfirst(str_replace('_',' ',$u->role)) }}</span></td>
              <td style="font-size:.78rem;color:var(--text-muted);">{{ $u->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Top Providers --}}
  <div class="col-lg-6">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-trophy me-2" style="color:var(--gold);"></i>Top Providers by Skill Score</h6></div>
      <div class="card-body-custom">
        @foreach($topProviders as $i => $p)
        <div class="d-flex align-items-center gap-3" style="padding:.5rem 0;border-bottom:1px solid var(--card-border);">
          <div style="width:28px;height:28px;border-radius:50%;background:{{ $i<3?'var(--gold)':'var(--bg-light)' }};display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.82rem;color:{{ $i<3?'#fff':'var(--text-secondary)' }};">{{ $i+1 }}</div>
          <div class="flex-grow-1"><div style="font-weight:600;font-size:.85rem;">{{ $p->user->name }}</div></div>
          <div style="font-size:.82rem;font-weight:700;color:var(--purple);">{{ round($p->skill_score) }}/100</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const mData = @json($monthlyBookings);
const months2 = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
new Chart(document.getElementById('analyticsChart'),{
  type:'bar',
  data:{
    labels: mData.map(d=>months2[d.month-1]),
    datasets:[
      {label:'Bookings',data:mData.map(d=>d.bookings),backgroundColor:'rgba(36,81,189,.85)',borderRadius:6},
      {label:'Revenue (₹)',data:mData.map(d=>d.revenue),backgroundColor:'rgba(25,83,188,.65)',borderRadius:6,yAxisID:'y1'}
    ]
  },
  options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{grid:{color:'#f0f0f0'}},y1:{position:'right',grid:{display:false}}}}
});
</script>
@endpush
