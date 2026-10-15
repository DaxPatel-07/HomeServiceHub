@extends('layouts.dashboard')
@section('title','Platform Settings')
@section('role-label','Master Admin')
@section('page-title','System Configuration & Settings')
@section('sidebar-links')
<div class="nav-section-title">System</div>
<div class="nav-item"><a href="{{ route('master.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('master.admins') }}"><i class="fas fa-user-shield"></i>Manage Admins</a></div>
<div class="nav-section-title">Platform</div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>All Users</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>All Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar"></i>All Bookings</a></div>
<div class="nav-section-title">Analytics</div>
<div class="nav-item"><a href="{{ route('master.reports') }}"><i class="fas fa-chart-bar"></i>Reports</a></div>
<div class="nav-item"><a href="{{ route('master.settings') }}" class="active"><i class="fas fa-cog"></i>Settings</a></div>
@endsection
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card-custom p-4">
      <h5 style="font-weight:800;margin-bottom:1.5rem;"><i class="fas fa-sliders-h me-2" style="color:var(--brand-blue);"></i>General Settings</h5>
      <form method="POST" action="#">
        @csrf
        <div class="row g-3">
          <div class="col-12"><div class="form-group-custom"><label>Application Name</label><input type="text" class="form-control" value="Home Service Hub" readonly></div></div>
          <div class="col-6"><div class="form-group-custom"><label>Platform Fee (₹)</label><input type="number" class="form-control" value="50"></div></div>
          <div class="col-6"><div class="form-group-custom"><label>Default Currency</label><input type="text" class="form-control" value="INR (₹)" readonly></div></div>
          <div class="col-12"><div class="form-group-custom"><label>Gemini API Status</label><input type="text" class="form-control" value="Configured (gemini-2.0-flash)" readonly></div></div>
          <div class="col-12"><div class="form-group-custom"><label>Support Email</label><input type="email" class="form-control" value="support@homeservicehub.com"></div></div>
        </div>
        <button type="button" class="btn-primary-custom btn mt-3" onclick="alert('Settings saved successfully!')"><i class="fas fa-save me-2"></i>Save Configuration</button>
      </form>
    </div>
  </div>
</div>
@endsection
