<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Services\SkillScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller {
    public function create(Booking $booking) {
        abort_if($booking->customer_id !== Auth::id(), 403);
        abort_if($booking->status !== 'completed', 403, 'You can only review completed bookings.');
        if ($booking->review()->exists()) {
            return redirect()->route('customer.booking.show', $booking)->with('error', 'You have already reviewed this booking.');
        }
        return view('customer.review', compact('booking'));
    }
    public function store(Request $request, Booking $booking) {
        abort_if($booking->customer_id !== Auth::id(), 403);
        abort_if($booking->status !== 'completed', 403, 'You can only review completed bookings.');
        if ($booking->review()->exists()) {
            return back()->with('error', 'You have already reviewed this booking.');
        }
        $request->validate(['rating' => 'required|integer|min:1|max:5', 'comment' => 'nullable|string|max:1000']);
        Review::create([
            'booking_id'  => $booking->id,
            'customer_id' => Auth::id(),
            'provider_id' => $booking->provider_id,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
        ]);
        $provider = $booking->provider;
        $avgRating = $provider->reviews()->avg('rating');
        $provider->update(['average_rating' => round($avgRating, 2)]);
        app(SkillScoreService::class)->updateAndAssignBadges($provider);
        return redirect()->route('customer.dashboard')->with('success', 'Review submitted!');
    }
}
