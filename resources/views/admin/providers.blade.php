@extends('layouts.dashboard')
@section('title','Manage Providers')
@section('role-label','Admin Panel')
@section('page-title','Manage Service Providers')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>Customers</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}" class="active"><i class="fas fa-hard-hat"></i>Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-section-title">Manage</div>
<div class="nav-item"><a href="{{ route('admin.categories') }}"><i class="fas fa-tags"></i>Categories</a></div>
<div class="nav-item"><a href="{{ route('admin.complaints') }}"><i class="fas fa-flag"></i>Complaints</a></div>
@endsection
@section('content')
<div class="card-custom">
  <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h6 class="mb-0"><i class="fas fa-hard-hat me-2" style="color:var(--brand-blue);"></i>Provider Applications &amp; Review</h6>
      <small class="text-muted">Review, approve, or reject provider registrations</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <span class="badge bg-warning text-dark px-3 py-2">
        <i class="fas fa-clock me-1"></i> {{ $pendingCount }} Pending Approval
      </span>
      <span class="badge bg-success px-3 py-2">
        <i class="fas fa-check-circle me-1"></i> {{ $verifiedCount }} Verified
      </span>
    </div>
  </div>

  {{-- Status Tabs and Search --}}
  <div class="p-3 border-bottom" style="background:#FAFBFE;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('admin.providers', array_merge(request()->except('status', 'page'), [])) }}" 
           class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
          All <span class="badge bg-white text-dark ms-1">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('admin.providers', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
           class="btn {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">
          Pending <span class="badge bg-dark text-white ms-1">{{ $pendingCount }}</span>
        </a>
        <a href="{{ route('admin.providers', array_merge(request()->except('page'), ['status' => 'verified'])) }}" 
           class="btn {{ request('status') === 'verified' ? 'btn-success' : 'btn-outline-success' }}">
          Approved <span class="badge bg-white text-dark ms-1">{{ $verifiedCount }}</span>
        </a>
        <a href="{{ route('admin.providers', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" 
           class="btn {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
          Rejected <span class="badge bg-white text-dark ms-1">{{ $rejectedCount }}</span>
        </a>
      </div>

      <form method="GET" action="{{ route('admin.providers') }}" class="d-flex gap-2 align-items-center">
        @if(request('status'))
          <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Search provider, category, city..." value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-sm btn-primary">Search</button>
        @if(request()->hasAny(['search', 'status']))
          <a href="{{ route('admin.providers') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        @endif
      </form>
    </div>
  </div>

  <div style="overflow-x:auto;">
    <table class="table-custom">
      <thead>
        <tr>
          <th>Provider Profile</th>
          <th>Trade Category</th>
          <th>City &amp; Locality</th>
          <th>Rate / Exp</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($providers as $p)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-sm" style="background:rgba(37,99,235,.1);color:var(--brand-blue);font-weight:800;">
                {{ strtoupper(substr($p->name,0,1)) }}
              </div>
              <div>
                <div style="font-weight:700;font-size:.88rem;">{{ $p->business_name ?: ($p->user?->name ?? 'Service Provider') }}</div>
                <div style="font-size:.75rem;color:var(--text-muted);">
                  <i class="fas fa-user me-1"></i>{{ $p->user?->name ?? 'N/A' }} &bull; 
                  <i class="fas fa-envelope me-1"></i>{{ $p->user?->email ?? 'N/A' }} &bull; 
                  <i class="fas fa-phone me-1"></i>{{ $p->user?->phone ?? 'N/A' }}
                </div>
              </div>
            </div>
          </td>
          <td>
            <span class="badge" style="background:rgba(37,99,235,.08);color:var(--brand-blue);font-weight:700;font-size:.78rem;">
              {{ $p->category?->name ?? 'General' }}
            </span>
          </td>
          <td>
            <div style="font-size:.82rem;font-weight:600;">{{ $p->user?->city ?? 'N/A' }}</div>
            <div style="font-size:.75rem;color:var(--text-muted);">{{ $p->service_area ?: 'Local' }}</div>
          </td>
          <td>
            <div style="font-weight:700;color:var(--brand-blue);font-size:.85rem;">₹{{ number_format($p->hourly_rate) }}/hr</div>
            <div style="font-size:.75rem;color:var(--text-secondary);">{{ $p->experience_years }} yrs exp</div>
          </td>
          <td>
            @if($p->verification_status === 'verified')
              <span class="badge-pill badge-status-completed" style="font-weight:700;">
                <i class="fas fa-check-circle me-1"></i>Approved
              </span>
            @elseif($p->verification_status === 'pending')
              <span class="badge-pill badge-status-pending" style="font-weight:700;">
                <i class="fas fa-clock me-1"></i>Pending
              </span>
            @else
              <span class="badge-pill badge-status-cancelled" style="font-weight:700;">
                <i class="fas fa-times-circle me-1"></i>Rejected
              </span>
            @endif
          </td>
          <td>
            <div class="d-flex gap-1">
              @if($p->verification_status !== 'verified')
              <form method="POST" action="{{ route('admin.providers.verify', $p) }}" onsubmit="return confirm('Approve {{ addslashes($p->name) }} as an active provider?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-success" style="font-size:.75rem;font-weight:600;">
                  <i class="fas fa-check me-1"></i>Accept
                </button>
              </form>
              @endif

              @if($p->verification_status !== 'rejected')
              <form method="POST" action="{{ route('admin.providers.reject', $p) }}" onsubmit="return confirm('Reject application for {{ addslashes($p->name) }}?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger" style="font-size:.75rem;font-weight:600;">
                  <i class="fas fa-times me-1"></i>Reject
                </button>
              </form>
              @endif

              <a href="{{ route('provider.show', $p) }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;" title="Preview Profile">
                <i class="fas fa-eye"></i>
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">No provider applications found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $providers->links() }}</div>
</div>
@endsection
