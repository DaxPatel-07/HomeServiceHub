@extends('layouts.app')
@section('title','Book Service')
@section('content')
<div class="container" style="padding:2.5rem 0;max-width:850px;">
  <div class="booking-steps mb-4">
    <div class="step-item done"><div class="step-circle"><i class="fas fa-check"></i></div><div class="step-label">Service</div></div>
    <div class="step-item active"><div class="step-circle">2</div><div class="step-label">Date &amp; Time</div></div>
    <div class="step-item"><div class="step-circle">3</div><div class="step-label">Address</div></div>
    <div class="step-item"><div class="step-circle">4</div><div class="step-label">Payment</div></div>
    <div class="step-item"><div class="step-circle">5</div><div class="step-label">Confirm</div></div>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card-custom p-4" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-sm);">
        <h4 style="font-weight:800;margin-bottom:1.5rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">Book {{ $provider->name }}</h4>
        @if($errors->any())
          <div class="alert-custom alert-error-custom mb-3"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('customer.book.store') }}" id="bookingForm">
          @csrf
          <input type="hidden" name="provider_id" value="{{ $provider->id }}">
          <div class="form-group-custom">
            <label style="font-weight:600;font-size:.82rem;color:var(--color-text-dark);">Select Date</label>
            <input type="date" name="booking_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('booking_date', date('Y-m-d', strtotime('+1 day'))) }}" required>
          </div>
          <div class="form-group-custom">
            <label style="font-weight:600;font-size:.82rem;color:var(--color-text-dark);">Select Time (12-Hour AM/PM)</label>
            <div class="row g-2">
              @php
                $timeSlots = [
                  ['value' => '08:00', 'label' => '08:00 AM'],
                  ['value' => '09:00', 'label' => '09:00 AM'],
                  ['value' => '10:00', 'label' => '10:00 AM'],
                  ['value' => '11:00', 'label' => '11:00 AM'],
                  ['value' => '12:00', 'label' => '12:00 PM'],
                  ['value' => '14:00', 'label' => '02:00 PM'],
                  ['value' => '15:00', 'label' => '03:00 PM'],
                  ['value' => '16:00', 'label' => '04:00 PM'],
                  ['value' => '17:00', 'label' => '05:00 PM'],
                  ['value' => '18:00', 'label' => '06:00 PM'],
                ];
              @endphp
              @foreach($timeSlots as $slot)
              <div class="col-6 col-sm-4">
                <input type="radio" name="booking_time" id="time_{{ $loop->index }}" value="{{ $slot['value'] }}" class="d-none" {{ old('booking_time')==$slot['value']?'checked':'' }}>
                <label for="time_{{ $loop->index }}" style="display:block;text-align:center;padding:.6rem .4rem;border:1.5px solid var(--color-border);border-radius:10px;cursor:pointer;font-size:.82rem;font-weight:600;transition:.2s;" class="time-slot-label">{{ $slot['label'] }}</label>
              </div>
              @endforeach
            </div>
          </div>
          <div class="form-group-custom">
            <label style="font-weight:600;font-size:.82rem;color:var(--color-text-dark);">Service Address</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Full address where service is needed" required>{{ old('address') }}</textarea>
          </div>
          <div class="form-group-custom">
            <label style="font-weight:600;font-size:.82rem;color:var(--color-text-dark);">City</label>
            <select name="city" class="form-control" required>
              <option value="Ahmedabad" {{ old('city', auth()->user()->city ?? '')=='Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
              <option value="Gandhinagar" {{ old('city', auth()->user()->city ?? '')=='Gandhinagar' ? 'selected' : '' }}>Gandhinagar</option>
            </select>
          </div>
          <div class="form-group-custom">
            <label style="font-weight:600;font-size:.82rem;color:var(--color-text-dark);">Describe the Issue (Optional)</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Brief description of the work needed...">{{ old('description') }}</textarea>
          </div>
          <div class="form-group-custom">
            <label style="font-weight:600;font-size:.82rem;color:var(--color-text-dark);">Payment Method</label>
            <div class="d-flex gap-2 gap-sm-3 flex-wrap">
              @foreach([['cash','Cash on Delivery'],['card','Credit/Debit Card'],['wallet','Wallet']] as $pm)
              <div style="flex:1 1 120px;">
                <input type="radio" name="payment_method" id="pm_{{ $pm[0] }}" value="{{ $pm[0] }}" class="d-none" {{ old('payment_method','cash')==$pm[0]?'checked':'' }}>
                <label for="pm_{{ $pm[0] }}" style="display:block;text-align:center;padding:.75rem .5rem;border:1.5px solid var(--color-border);border-radius:12px;cursor:pointer;font-size:.82rem;font-weight:600;transition:.2s;" class="pm-label">{{ $pm[1] }}</label>
              </div>
              @endforeach
            </div>
          </div>
          <button type="submit" class="btn-primary-custom btn w-100 mt-2" style="justify-content:center;padding:.9rem;font-size:1rem;"><i class="fas fa-calendar-check me-2"></i>Confirm Booking</button>
        </form>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card-custom p-4" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-sm);position:sticky;top:80px;">
        <h6 style="font-weight:700;margin-bottom:1rem;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">Booking Summary</h6>
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="provider-avatar" style="width:50px;height:50px;font-size:1.2rem;background:linear-gradient(135deg, var(--color-primary), var(--color-support-blue));color:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ strtoupper(substr($provider->name,0,1)) }}</div>
          <div><div style="font-weight:700;color:var(--color-text-dark);font-family:'Poppins',sans-serif;">{{ $provider->name }}</div><div style="font-size:.8rem;color:var(--color-text-muted);">{{ $provider->category->name }}</div></div>
        </div>
        <div class="divider" style="margin:.85rem 0;"></div>
        @foreach([['Service Charge','₹'.number_format($provider->hourly_rate)],['Platform Fee','₹50'],['Total Amount','₹'.number_format($provider->hourly_rate+50)]] as $row)
        <div class="d-flex justify-content-between" style="padding:.4rem 0;font-size:.88rem;{{ $loop->last ? 'font-weight:800;font-size:1.05rem;color:var(--color-primary);font-family:\'Poppins\',sans-serif;' : 'color:var(--color-text-dark);' }}">
          <span>{{ $row[0] }}</span><span>{{ $row[1] }}</span>
        </div>
        @endforeach
        <div class="divider" style="margin:.85rem 0;"></div>
        <div class="d-flex align-items-center gap-2" style="font-size:.82rem;color:var(--color-success);font-weight:600;">
          <i class="fas fa-shield-alt"></i><span>Secure &amp; Safe Booking</span>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2" style="font-size:.82rem;color:var(--color-text-muted);">
          <i class="fas fa-info-circle" style="color:var(--color-primary);"></i><span>Free cancellation up to 2 hours before</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.querySelectorAll('input[name=booking_time]').forEach(radio => {
  const label = document.querySelector('label[for='+radio.id+']');
  radio.addEventListener('change', () => {
    document.querySelectorAll('.time-slot-label').forEach(l => { 
      l.style.borderColor='var(--color-border)'; 
      l.style.background='#FFFFFF'; 
      l.style.color='var(--color-text-dark)'; 
      l.style.boxShadow='none';
    });
    if(radio.checked){ 
      label.style.borderColor='var(--color-primary)'; 
      label.style.background='var(--color-primary)'; 
      label.style.color='#FFFFFF'; 
      label.style.boxShadow='0 4px 12px rgba(36,81,189,.28)';
    }
  });
  if(radio.checked){ 
    label.style.borderColor='var(--color-primary)'; 
    label.style.background='var(--color-primary)'; 
    label.style.color='#FFFFFF'; 
    label.style.boxShadow='0 4px 12px rgba(36,81,189,.28)';
  }
});
document.querySelectorAll('input[name=payment_method]').forEach(radio => {
  const label = document.querySelector('label[for='+radio.id+']');
  radio.addEventListener('change', () => {
    document.querySelectorAll('.pm-label').forEach(l => { 
      l.style.borderColor='var(--color-border)'; 
      l.style.background='#FFFFFF'; 
      l.style.color='var(--color-text-dark)';
    });
    if(radio.checked){ 
      label.style.borderColor='var(--color-primary)'; 
      label.style.background='var(--color-blue-tint)'; 
      label.style.color='var(--color-primary)';
    }
  });
  if(radio.checked){ 
    label.style.borderColor='var(--color-primary)'; 
    label.style.background='var(--color-blue-tint)'; 
    label.style.color='var(--color-primary)';
  }
});
</script>
@endpush
