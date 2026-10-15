@extends('layouts.dashboard')
@section('title','Manage Complaints')
@section('role-label','Admin Panel')
@section('page-title','Customer Complaints')
@section('sidebar-links')
<div class="nav-section-title">Main Menu</div>
<div class="nav-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i>Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.customers') }}"><i class="fas fa-users"></i>Customers</a></div>
<div class="nav-item"><a href="{{ route('admin.providers') }}"><i class="fas fa-hard-hat"></i>Providers</a></div>
<div class="nav-item"><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-check"></i>Bookings</a></div>
<div class="nav-section-title">Manage</div>
<div class="nav-item"><a href="{{ route('admin.categories') }}"><i class="fas fa-tags"></i>Categories</a></div>
<div class="nav-item"><a href="{{ route('admin.complaints') }}" class="active"><i class="fas fa-flag"></i>Complaints</a></div>
@endsection
@section('content')
<div class="card-custom">
  <div class="card-header-custom"><h6><i class="fas fa-flag me-2" style="color:var(--danger);"></i>Complaints List</h6></div>
  <div style="overflow-x:auto;">
    <table class="table-custom">
      <thead><tr><th>Customer</th><th>Booking</th><th>Subject</th><th>Status</th><th>Resolution</th><th>Action</th></tr></thead>
      <tbody>
        @forelse($complaints as $c)
        <tr>
          <td><div style="font-weight:600;">{{ $c->customer->name }}</div><div style="font-size:.75rem;color:var(--text-muted);">{{ $c->customer->email }}</div></td>
          <td style="font-size:.78rem;color:var(--text-muted);">{{ $c->booking->booking_code }}</td>
          <td><div style="font-weight:700;">{{ $c->subject }}</div><div style="font-size:.8rem;color:var(--text-secondary);">{{ $c->description }}</div></td>
          <td><span class="badge-pill badge-status-{{ $c->status==='resolved'?'completed':'cancelled' }}">{{ ucfirst($c->status) }}</span></td>
          <td style="font-size:.82rem;">{{ $c->resolution ?: 'Pending resolution' }}</td>
          <td>
            @if($c->status !== 'resolved')
            <button class="btn btn-sm btn-primary" style="font-size:.75rem;" onclick="openResolveModal('{{ $c->id }}', '{{ addslashes($c->subject) }}')">Resolve</button>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center" style="padding:2rem;color:var(--text-muted);">No complaints recorded.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $complaints->links() }}</div>
</div>

<!-- Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" id="resolveForm" action="">
      @csrf
      <div class="modal-content" style="border-radius:14px;">
        <div class="modal-header"><h5 class="modal-title">Resolve Complaint</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <p id="modalSubject" style="font-weight:600;"></p>
          <div class="form-group-custom">
            <label>Resolution Notes *</label>
            <textarea name="resolution" class="form-control" rows="3" placeholder="Action taken..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-primary-custom btn"><i class="fas fa-check me-1"></i>Mark Resolved</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<script>
function openResolveModal(id, subject) {
  document.getElementById('resolveForm').action = '/admin/complaints/' + id + '/resolve';
  document.getElementById('modalSubject').textContent = subject;
  new bootstrap.Modal(document.getElementById('resolveModal')).show();
}
</script>
@endpush
