@extends('layouts.dashboard')
@section('title','Manage Admins')
@section('role-label','Master Admin')
@section('page-title','Admin Accounts')
@section('sidebar-links')
<div class="nav-section-title">System</div>
<div class="nav-item"><a href="{{ route('master.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('master.admins') }}" class="active"><i class="fas fa-user-shield"></i>Manage Admins</a></div>
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
  <div class="col-lg-8">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-user-shield me-2" style="color:var(--brand-blue);"></i>Admin Users</h6></div>
      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Created</th><th>Action</th></tr></thead>
          <tbody>
            @foreach($admins as $a)
            <tr>
              <td><div class="d-flex align-items-center gap-2"><div class="avatar-sm" style="background:rgba(37,99,235,.1);color:var(--brand-blue);font-weight:800;">{{ strtoupper(substr($a->name,0,1)) }}</div><span style="font-weight:700;">{{ $a->name }}</span></div></td>
              <td style="font-size:.85rem;">{{ $a->email }}</td>
              <td style="font-size:.85rem;">{{ $a->phone }}</td>
              <td style="font-size:.82rem;color:var(--text-muted);">{{ $a->created_at->format('d M Y') }}</td>
              <td>
                <form method="POST" action="{{ route('master.admins.destroy', $a) }}" onsubmit="return confirm('Remove this admin?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger" style="font-size:.75rem;">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $admins->links() }}</div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card-custom p-4">
      <h6 style="font-weight:700;margin-bottom:1rem;"><i class="fas fa-plus-circle me-2" style="color:var(--brand-blue);"></i>Create New Admin</h6>
      <form method="POST" action="{{ route('master.admins.store') }}">
        @csrf
        <div class="form-group-custom">
          <label>Full Name *</label>
          <input type="text" name="name" class="form-control" placeholder="Admin Name" required>
        </div>
        <div class="form-group-custom">
          <label>Email *</label>
          <input type="email" name="email" class="form-control" placeholder="admin@domain.com" required>
        </div>
        <div class="form-group-custom">
          <label>Phone *</label>
          <input type="text" name="phone" class="form-control" placeholder="+91 9876543210" required>
        </div>
        <div class="form-group-custom">
          <label>Password *</label>
          <div class="input-icon-wrap has-toggle">
            <input type="password" name="password" id="adminPassword" class="form-control" placeholder="Min 8 characters" required style="padding-left:.9rem;">
            <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('adminPassword', this)" title="Show/Hide Password"></i>
          </div>
        </div>
        <button type="submit" class="btn-primary-custom btn w-100" style="justify-content:center;"><i class="fas fa-user-plus me-2"></i>Create Admin</button>
      </form>
    </div>
  </div>
</div>
@endsection
