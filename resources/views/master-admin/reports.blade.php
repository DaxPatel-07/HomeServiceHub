@extends('layouts.dashboard')
@section('title','Platform Reports')
@section('role-label','Master Admin')
@section('page-title','Analytics & Financial Reports')
@section('sidebar-links')
<div class="nav-section-title">System</div>
<div class="nav-item"><a href="{{ route('master.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('master.admins') }}"><i class="fas fa-user-shield"></i>Manage Admins</a></div>
<div class="nav-section-title">Platform</div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>All Users</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>All Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar"></i>All Bookings</a></div>
<div class="nav-section-title">Analytics</div>
<div class="nav-item"><a href="{{ route('master.reports') }}" class="active"><i class="fas fa-chart-bar"></i>Reports</a></div>
<div class="nav-item"><a href="{{ route('master.settings') }}"><i class="fas fa-cog"></i>Settings</a></div>
@endsection
@section('content')
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-calendar-alt me-2" style="color:var(--brand-blue);"></i>Monthly Revenue &amp; Bookings</h6></div>
      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead><tr><th>Month / Year</th><th>Total Bookings</th><th>Total Revenue</th></tr></thead>
          <tbody>
            @foreach($monthlyData as $row)
            <tr>
              <td style="font-weight:700;">{{ DateTime::createFromFormat('!m', $row->month)->format('F') }} {{ $row->year }}</td>
              <td><span class="badge-pill bg-light text-dark" style="border:1px solid var(--card-border);">{{ $row->bookings }}</span></td>
              <td style="font-weight:800;color:var(--success);">₹{{ number_format($row->revenue) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-chart-pie me-2" style="color:var(--purple);"></i>Category Breakdown</h6></div>
      <div class="card-body-custom">
        @foreach($categoryStats as $cs)
        <div class="d-flex justify-content-between align-items-center mb-2" style="padding:.5rem 0;border-bottom:1px solid var(--card-border);">
          <span style="font-weight:600;font-size:.88rem;">{{ $cs->name }}</span>
          <span class="badge-pill" style="background:rgba(232,165,26,.12);color:var(--gold);font-weight:700;">{{ $cs->total }} Bookings</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
