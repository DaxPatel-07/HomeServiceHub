@extends('layouts.dashboard')
@section('title','Manage Customers')
@section('role-label','Admin Panel')
@section('page-title','Customer Accounts')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.customers') }}" class="active"><i class="fas fa-users"></i>Customers</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-section-title">Manage</div>
<div class="nav-item"><a href="{{ route('admin.categories') }}"><i class="fas fa-tags"></i>Categories</a></div>
<div class="nav-item"><a href="{{ route('admin.complaints') }}"><i class="fas fa-flag"></i>Complaints</a></div>
@endsection
@section('content')
<div class="card-custom">
  <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h6 class="mb-0"><i class="fas fa-users me-2" style="color:var(--brand-blue);"></i>Registered Customers</h6>
      <small class="text-muted">Customer accounts registered in the database</small>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-primary px-3 py-2">
        <i class="fas fa-users me-1"></i> {{ $totalCustomers ?? $customers->total() }} Customers
      </span>
    </div>
  </div>

  {{-- Search & Filter Toolbar --}}
  <div class="p-3 border-bottom" style="background:#FAFBFE;">
    <form method="GET" action="{{ route('admin.customers') }}" class="row g-2 align-items-center">
      <div class="col-md-5 col-sm-12">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone, city..." value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
        </select>
      </div>
      <div class="col-md-4 col-sm-6 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary">Search</button>
        @if(request()->hasAny(['search', 'status']))
          <a href="{{ route('admin.customers') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        @endif
      </div>
    </form>
  </div>

  <div style="overflow-x:auto;">
    <table class="table-custom">
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Joined Date</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($customers as $c)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-sm">{{ strtoupper(substr($c->name,0,1)) }}</div>
              <div>
                <span style="font-weight:600;">{{ $c->name }}</span>
                @if($c->address)
                  <div style="font-size:.72rem;color:var(--text-muted);">{{ \Illuminate\Support\Str::limit($c->address, 35) }}</div>
                @endif
              </div>
            </div>
          </td>
          <td style="font-size:.85rem;">{{ $c->email }}</td>
          <td style="font-size:.85rem;">{{ $c->phone ?: 'N/A' }}</td>
          <td style="font-size:.85rem;">{{ $c->city ?: 'N/A' }}</td>
          <td style="font-size:.82rem;color:var(--text-muted);">{{ $c->created_at ? $c->created_at->format('d M Y') : 'N/A' }}</td>
          <td>
            <span class="badge-pill {{ $c->is_active ? 'badge-status-completed' : 'badge-status-cancelled' }}">
              {{ $c->is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">
            <i class="fas fa-users-slash me-2"></i>No registered customers found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $customers->links() }}</div>
</div>
@endsection
