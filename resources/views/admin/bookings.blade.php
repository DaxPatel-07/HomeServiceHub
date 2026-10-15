@extends('layouts.dashboard')
@section('title','Manage Bookings')
@section('role-label','Admin Panel')
@section('page-title','All System Bookings')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>Customers</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}" class="active"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-section-title">Manage</div>
<div class="nav-item"><a href="{{ route('admin.categories') }}"><i class="fas fa-tags"></i>Categories</a></div>
<div class="nav-item"><a href="{{ route('admin.complaints') }}"><i class="fas fa-flag"></i>Complaints</a></div>
@endsection
@section('content')
<div class="card-custom">
  <div class="card-header-custom"><h6><i class="fas fa-calendar-check me-2" style="color:var(--brand-blue);"></i>All Bookings</h6></div>
  <div style="overflow-x:auto;">
    <table class="table-custom">
      <thead><tr><th>Code</th><th>Customer</th><th>Provider</th><th>Service</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($bookings as $b)
        <tr>
          <td style="font-size:.78rem;font-weight:600;color:var(--text-muted);">{{ $b->booking_code }}</td>
          <td><div style="font-weight:600;">{{ $b->customer->name }}</div><div style="font-size:.75rem;color:var(--text-muted);">{{ $b->customer->email }}</div></td>
          <td><div style="font-weight:600;">{{ $b->provider->name }}</div><div style="font-size:.75rem;color:var(--text-muted);">{{ $b->provider->service_area }}</div></td>
          <td style="font-weight:600;">{{ $b->category->name }}</td>
          <td style="font-size:.85rem;">{{ $b->booking_date->format('d M Y') }}<br><span style="color:var(--text-muted);font-size:.75rem;">{{ $b->formatted_time }}</span></td>
          <td style="font-weight:700;color:var(--brand-blue);">₹{{ number_format($b->total_amount) }}</td>
          <td><span class="badge-pill badge-status-{{ $b->status }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $bookings->links() }}</div>
</div>
@endsection
