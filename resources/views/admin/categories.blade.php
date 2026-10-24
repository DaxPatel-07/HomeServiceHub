@extends('layouts.dashboard')
@section('title','Manage Categories')
@section('role-label', auth()->user() && auth()->user()->role === 'master_admin' ? 'Master Admin' : 'Admin Panel')
@section('page-title','Service Categories')

@section('sidebar-links')
@if(auth()->user() && auth()->user()->role === 'master_admin')
<div class="nav-section-title">System</div>
<div class="nav-item"><a href="{{ route('master.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('master.admins') }}"><i class="fas fa-user-shield"></i>Manage Admins</a></div>
<div class="nav-section-title">Platform</div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>All Users</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>All Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar"></i>All Bookings</a></div>
<div class="nav-item"><a href="{{ route('admin.categories') }}" class="active"><i class="fas fa-tags"></i>Categories</a></div>
<div class="nav-section-title">Analytics</div>
<div class="nav-item"><a href="{{ route('master.reports') }}"><i class="fas fa-chart-bar"></i>Reports</a></div>
<div class="nav-item"><a href="{{ route('master.settings') }}"><i class="fas fa-cog"></i>Settings</a></div>
@else
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>Customers</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-section-title">Manage</div>
<div class="nav-item"><a href="{{ route('admin.categories') }}" class="active"><i class="fas fa-tags"></i>Categories</a></div>
<div class="nav-item"><a href="{{ route('admin.complaints') }}"><i class="fas fa-flag"></i>Complaints</a></div>
@endif
@endsection

@push('styles')
<style>
.categories-layout-row {
  margin-left: 0;
  margin-right: 0;
}
.categories-layout-row > [class*="col-"] {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
}
.categories-col {
  min-width: 0;
}
.categories-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 0;
}
.categories-table thead th {
  background: var(--bg-light, #F8FAFC);
  color: var(--text-secondary, #64748B);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 0.95rem 1.15rem;
  border-bottom: 2px solid var(--card-border, #D9DEE8);
  white-space: nowrap;
}
.categories-table tbody td {
  padding: 0.95rem 1.15rem;
  border-bottom: 1px solid var(--card-border, #D9DEE8);
  font-size: 0.875rem;
  vertical-align: middle;
  color: var(--text-primary, #14213D);
}
.categories-table tbody tr:hover {
  background-color: rgba(247, 235, 221, 0.35);
}
.categories-table tbody tr:last-child td {
  border-bottom: none;
}
.cat-icon-badge {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  flex-shrink: 0;
  background: rgba(20, 33, 61, 0.04);
}
.cat-name-text {
  font-weight: 700;
  color: #14213D;
  font-size: 0.9rem;
}
.cat-icon-code {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 0.78rem;
  background: #F1F5F9;
  color: #334155;
  padding: 0.25rem 0.55rem;
  border-radius: 6px;
  border: 1px solid #E2E8F0;
  white-space: nowrap;
  display: inline-block;
}
.cat-color-swatch {
  width: 18px;
  height: 18px;
  border-radius: 4px;
  border: 1px solid rgba(20, 33, 61, 0.15);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  flex-shrink: 0;
  display: inline-block;
}
.cat-color-hex {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 0.82rem;
  font-weight: 600;
  color: #334155;
  letter-spacing: 0.3px;
}
.cat-count-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 28px;
  height: 28px;
  padding: 0 8px;
  border-radius: 14px;
  background: #F8FAFC;
  border: 1px solid var(--card-border, #D9DEE8);
  color: #14213D;
  font-weight: 700;
  font-size: 0.8rem;
}
.add-category-card {
  background: #FFFFFF;
  border-radius: var(--border-radius, 12px);
  border: 1px solid var(--card-border, #D9DEE8);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}
.add-category-card .card-header-custom {
  padding: 1.1rem 1.5rem;
  border-bottom: 1px solid var(--card-border, #D9DEE8);
  background: rgba(247, 235, 221, 0.35);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.add-category-card .card-header-custom h6 {
  margin: 0;
  font-weight: 700;
  color: var(--text-primary, #14213D);
  font-size: 0.95rem;
}
.add-category-body {
  padding: 1.5rem;
}
.add-category-form .form-group-custom {
  margin-bottom: 1.15rem;
}
.add-category-form label {
  font-size: 0.82rem;
  font-weight: 600;
  color: #14213D;
  margin-bottom: 0.4rem;
  display: block;
}
.add-category-form .form-control {
  border: 1.5px solid var(--card-border, #D9DEE8);
  border-radius: 10px;
  padding: 0.65rem 0.95rem;
  font-size: 0.88rem;
  background: #FFFFFF;
  color: #14213D;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.add-category-form .form-control:focus {
  border-color: #2451BD;
  box-shadow: 0 0 0 3px rgba(36, 81, 189, 0.12);
  outline: none;
}
.add-category-form textarea.form-control {
  resize: vertical;
  min-height: 85px;
}
.cat-color-picker-input {
  width: 46px !important;
  height: 42px !important;
  padding: 4px !important;
  border-radius: 10px 0 0 10px !important;
  border: 1.5px solid var(--card-border, #D9DEE8) !important;
  border-right: none !important;
  cursor: pointer;
  background: #FFFFFF;
  flex-shrink: 0;
}
.cat-color-text-input {
  border-radius: 0 10px 10px 0 !important;
  border: 1.5px solid var(--card-border, #D9DEE8) !important;
  border-left: none !important;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-weight: 600;
  text-transform: uppercase;
}
.cat-icon-preview-box {
  width: 44px;
  background: #F8FAFC;
  border: 1.5px solid var(--card-border, #D9DEE8);
  border-right: none;
  border-radius: 10px 0 0 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  color: #2451BD;
  flex-shrink: 0;
}
.cat-icon-text-input {
  border-radius: 0 10px 10px 0 !important;
  border: 1.5px solid var(--card-border, #D9DEE8) !important;
  border-left: none !important;
}
.btn-create-category {
  height: 44px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-weight: 600;
  font-size: 0.92rem;
  border-radius: 10px;
}
</style>
@endpush

@section('content')
<div class="row g-4 categories-layout-row align-items-start">
  {{-- Left: Categories List Table --}}
  <div class="col-12 col-xl-7 col-xxl-8 categories-col">
    <div class="card-custom">
      <div class="card-header-custom d-flex align-items-center justify-content-between">
        <h6><i class="fas fa-tags me-2" style="color:var(--brand-blue);"></i>Categories List</h6>
        <span class="badge" style="background:var(--color-blue-tint, #EAF0FB);color:var(--brand-blue, #2451BD);font-weight:700;font-size:.78rem;border-radius:20px;padding:.3rem .75rem;">{{ $categories->count() }} Total</span>
      </div>
      <div style="overflow-x:auto;">
        <table class="categories-table">
          <thead>
            <tr>
              <th style="width: 32%;">Category</th>
              <th style="width: 28%;">Icon</th>
              <th style="width: 24%;">Color</th>
              <th style="width: 16%; text-align: center;">Providers Count</th>
            </tr>
          </thead>
          <tbody>
            @forelse($categories as $cat)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="cat-icon-badge" style="color: {{ $cat->color }};">
                    <i class="{{ $cat->icon }}"></i>
                  </span>
                  <span class="cat-name-text">{{ $cat->name }}</span>
                </div>
              </td>
              <td>
                <code class="cat-icon-code">{{ $cat->icon }}</code>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="cat-color-swatch" style="background-color: {{ $cat->color }};"></span>
                  <span class="cat-color-hex">{{ strtoupper($cat->color) }}</span>
                </div>
              </td>
              <td class="text-center">
                <span class="cat-count-badge">{{ $cat->providers_count }}</span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">No categories found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Right: Add Category Form --}}
  <div class="col-12 col-xl-5 col-xxl-4 categories-col">
    <div class="add-category-card">
      <div class="card-header-custom">
        <h6><i class="fas fa-plus-circle me-2" style="color:var(--brand-blue);"></i>Add Category</h6>
      </div>
      <div class="add-category-body">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="add-category-form">
          @csrf

          {{-- Category Name --}}
          <div class="form-group-custom">
            <label for="categoryName">Category Name <span class="text-danger">*</span></label>
            <input type="text" id="categoryName" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Gardener" value="{{ old('name') }}" required maxlength="100">
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- FontAwesome Icon Class --}}
          <div class="form-group-custom">
            <label for="categoryIcon">FontAwesome Icon Class</label>
            <div class="d-flex align-items-stretch">
              <span class="cat-icon-preview-box">
                <i class="{{ old('icon', 'fas fa-tools') }}" id="iconLivePreview"></i>
              </span>
              <input type="text" id="categoryIcon" name="icon" class="form-control cat-icon-text-input @error('icon') is-invalid @enderror" placeholder="fas fa-tools" value="{{ old('icon', 'fas fa-tools') }}" maxlength="50">
            </div>
            @error('icon')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          {{-- Color Code --}}
          <div class="form-group-custom">
            <label for="categoryColorText">Color Code</label>
            <div class="d-flex align-items-stretch">
              <input type="color" id="categoryColorPicker" class="cat-color-picker-input" value="{{ old('color', '#F59E0B') }}" title="Choose category color">
              <input type="text" id="categoryColorText" name="color" class="form-control cat-color-text-input @error('color') is-invalid @enderror" value="{{ old('color', '#F59E0B') }}" placeholder="#F59E0B" maxlength="20">
            </div>
            @error('color')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          {{-- Description --}}
          <div class="form-group-custom">
            <label for="categoryDesc">Description</label>
            <textarea id="categoryDesc" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Brief description..." maxlength="1000">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Create Category Button --}}
          <button type="submit" class="btn-primary-custom btn-create-category">
            <i class="fas fa-save"></i>
            <span>Create Category</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colorPicker = document.getElementById('categoryColorPicker');
    const colorText = document.getElementById('categoryColorText');
    const iconInput = document.getElementById('categoryIcon');
    const iconPreview = document.getElementById('iconLivePreview');

    if (colorPicker && colorText) {
        colorPicker.addEventListener('input', function () {
            colorText.value = colorPicker.value.toUpperCase();
        });
        colorText.addEventListener('input', function () {
            let val = colorText.value.trim();
            if (!val.startsWith('#') && val.length > 0) {
                val = '#' + val;
            }
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                colorPicker.value = val;
            }
        });
    }

    if (iconInput && iconPreview) {
        iconInput.addEventListener('input', function () {
            const val = iconInput.value.trim();
            iconPreview.className = val || 'fas fa-tag';
        });
    }
});
</script>
@endpush
