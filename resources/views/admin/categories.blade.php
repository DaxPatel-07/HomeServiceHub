@extends('layouts.dashboard')
@section('title','Manage Categories')
@section('role-label','Admin Panel')
@section('page-title','Service Categories')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>Customers</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-section-title">Manage</div>
<div class="nav-item"><a href="{{ route('admin.categories') }}" class="active"><i class="fas fa-tags"></i>Categories</a></div>
<div class="nav-item"><a href="{{ route('admin.complaints') }}"><i class="fas fa-flag"></i>Complaints</a></div>
@endsection
@section('content')
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card-custom">
      <div class="card-header-custom"><h6><i class="fas fa-tags me-2" style="color:var(--brand-blue);"></i>Categories List</h6></div>
      <div style="overflow-x:auto;">
        <table class="table-custom">
          <thead><tr><th>Category</th><th>Icon</th><th>Color</th><th>Providers Count</th></tr></thead>
          <tbody>
            @foreach($categories as $cat)
            <tr>
              <td style="font-weight:700;"><i class="{{ $cat->icon }} me-2" style="color:{{ $cat->color }};"></i>{{ $cat->name }}</td>
              <td><code>{{ $cat->icon }}</code></td>
              <td><span style="display:inline-block;width:16px;height:16px;border-radius:4px;background:{{ $cat->color }};vertical-align:middle;margin-right:6px;"></span>{{ $cat->color }}</td>
              <td><span class="badge-pill bg-light text-dark" style="border:1px solid var(--card-border);">{{ $cat->providers_count }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card-custom p-4">
      <h6 style="font-weight:700;margin-bottom:1rem;"><i class="fas fa-plus-circle me-2" style="color:var(--brand-blue);"></i>Add Category</h6>
      <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="form-group-custom">
          <label>Category Name *</label>
          <input type="text" name="name" class="form-control" placeholder="e.g. Gardener" required>
        </div>
        <div class="form-group-custom">
          <label>FontAwesome Icon Class</label>
          <input type="text" name="icon" class="form-control" placeholder="fas fa-leaf" value="fas fa-tools">
        </div>
        <div class="form-group-custom">
          <label>Color Code</label>
          <input type="color" name="color" class="form-control form-control-color w-100" value="#F59E0B">
        </div>
        <div class="form-group-custom">
          <label>Description</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
        </div>
        <button type="submit" class="btn-primary-custom btn w-100" style="justify-content:center;"><i class="fas fa-save me-2"></i>Create Category</button>
      </form>
    </div>
  </div>
</div>
@endsection
