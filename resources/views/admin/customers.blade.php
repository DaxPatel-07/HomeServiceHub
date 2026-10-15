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
  <div class="card-header-custom"><h6><i class="fas fa-users me-2" style="color:var(--brand-blue);"></i>Registered Customers</h6></div>
  <div style="overflow-x:auto;">
    <table class="table-custom">
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Joined Date</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($customers as $c)
        <tr>
          <td><div class="d-flex align-items-center gap-2"><div class="avatar-sm">{{ strtoupper(substr($c->name,0,1)) }}</div><span style="font-weight:600;">{{ $c->name }}</span></div></td>
          <td style="font-size:.85rem;">{{ $c->email }}</td>
          <td style="font-size:.85rem;">{{ $c->phone ?: 'N/A' }}</td>
          <td style="font-size:.85rem;">{{ $c->city ?: 'N/A' }}</td>
          <td style="font-size:.82rem;color:var(--text-muted);">{{ $c->created_at->format('d M Y') }}</td>
          <td><span class="badge-pill badge-status-completed">{{ $c->is_active ? 'Active' : 'Inactive' }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $customers->links() }}</div>
</div>
@endsection
