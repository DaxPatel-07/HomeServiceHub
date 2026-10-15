<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\SplitBooking;
use App\Models\SplitBookingItem;
use App\Models\Provider;
use App\Models\ServiceCategory;
use App\Services\SkillScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller {
    public function index() {
        $bookings = Booking::where('customer_id', Auth::id())->with(['provider.user','category'])->latest()->paginate(10);
        return view('customer.bookings', compact('bookings'));
    }
    public function show(Booking $booking) {
        abort_if($booking->customer_id !== Auth::id(), 403);
        $booking->load(['provider.user','category','review','complaint']);
        return view('customer.booking-show', compact('booking'));
    }
    public function showBooking(Provider $provider) {
        abort_if($provider->verification_status !== 'verified' || !$provider->is_active, 404, 'This provider is not currently available for bookings.');
        $provider->load(['user','category']);
        return view('customer.book-service', compact('provider'));
    }
    public function store(Request $request) {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'address' => 'required|string',
            'city' => 'required|in:Ahmedabad,Gandhinagar',
        ]);
        $provider = Provider::where('verification_status', 'verified')->where('is_active', true)->findOrFail($request->provider_id);
        $booking = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => Auth::id(),
            'provider_id' => $provider->id,
            'category_id' => $provider->category_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'address' => $request->address,
            'city' => $request->city,
            'description' => $request->description,
            'service_charge' => $provider->hourly_rate,
            'total_amount' => $provider->hourly_rate + 50,
            'payment_method' => $request->payment_method ?? 'cash',
            'status' => 'pending',
        ]);
        $provider->increment('total_jobs');
        return redirect()->route('customer.booking.show', $booking)->with('success', 'Booking placed successfully!');
    }
    public function showSplitBooking() {
        $categories = ServiceCategory::where('is_active', true)->get();
        $providers = Provider::with(['user','category'])->where('verification_status','verified')->where('is_active',true)->get();
        return view('customer.split-booking', compact('categories','providers'));
    }
    public function storeSplitBooking(Request $request) {
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'address'      => 'required|string',
            'city'         => 'required|in:Ahmedabad,Gandhinagar',
            'services'     => 'required|array|min:1',
            'services.*.provider_id' => 'required|exists:providers,id',
            'services.*.time'        => 'required',
            'services.*.amount'      => 'required|numeric|min:0',
        ]);
        DB::transaction(function() use ($request) {
            $split = SplitBooking::create([
                'split_code'     => SplitBooking::generateCode(),
                'customer_id'    => Auth::id(),
                'booking_date'   => $request->booking_date,
                'address'        => $request->address,
                'city'           => $request->city,
                'total_amount'   => collect($request->services)->sum('amount'),
                'payment_method' => $request->payment_method ?? 'cash',
                'status'         => 'pending',
            ]);
            foreach ($request->services as $i => $service) {
                $provider = Provider::where('verification_status', 'verified')->where('is_active', true)->findOrFail($service['provider_id']);
                
                SplitBookingItem::create([
                    'split_booking_id' => $split->id,
                    'provider_id'      => $provider->id,
                    'category_id'      => $provider->category_id,
                    'scheduled_time'   => $service['time'],
                    'amount'           => $service['amount'],
                    'status'           => 'pending',
                ]);

                Booking::create([
                    'booking_code'     => Booking::generateCode(),
                    'customer_id'      => Auth::id(),
                    'provider_id'      => $provider->id,
                    'category_id'      => $provider->category_id,
                    'booking_date'     => $request->booking_date,
                    'booking_time'     => $service['time'],
                    'address'          => $request->address,
                    'city'             => $request->city,
                    'description'      => 'Split Booking #' . $split->split_code,
                    'service_charge'   => $service['amount'],
                    'total_amount'     => $service['amount'],
                    'payment_method'   => $request->payment_method ?? 'cash',
                    'payment_status'   => 'pending',
                    'status'           => 'pending',
                    'is_split'         => true,
                    'split_booking_id' => $split->id,
                ]);

                $provider->increment('total_jobs');
            }
        });
        return redirect()->route('customer.dashboard')->with('success', 'Split booking placed successfully! Each appointment has been queued in your pending bookings.');
    }
    public function cancel(Booking $booking) {
        abort_if($booking->customer_id !== Auth::id(), 403);
        abort_if(!in_array($booking->status, ['pending','accepted']), 422, 'Cannot cancel this booking.');
        $booking->update(['status' => 'cancelled', 'cancellation_reason' => request('reason')]);
        $provider = $booking->provider;
        if ($provider && $provider->total_jobs > 0) {
            $provider->decrement('total_jobs');
        }
        return back()->with('success', 'Booking cancelled.');
    }
}
