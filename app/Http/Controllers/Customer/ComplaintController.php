<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller {
    public function create(Booking $booking) {
        abort_if($booking->customer_id !== Auth::id(), 403);
        abort_if($booking->status !== 'completed', 422, 'You can only file a complaint for completed bookings.');
        if ($booking->complaint()->exists()) {
            return redirect()->route('customer.booking.show', $booking)->with('error', 'A complaint has already been filed for this booking.');
        }
        $booking->load(['provider', 'category']);
        return view('customer.complaint', compact('booking'));
    }

    public function store(Request $request, Booking $booking) {
        abort_if($booking->customer_id !== Auth::id(), 403);
        abort_if($booking->status !== 'completed', 422, 'You can only file a complaint for completed bookings.');
        if ($booking->complaint()->exists()) {
            return redirect()->route('customer.booking.show', $booking)->with('error', 'A complaint has already been filed for this booking.');
        }
        $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|min:10',
        ]);
        Complaint::create([
            'customer_id' => Auth::id(),
            'booking_id'  => $booking->id,
            'subject'     => $request->subject,
            'description' => $request->description,
        ]);
        return redirect()->route('customer.booking.show', $booking)
            ->with('success', 'Complaint filed successfully. We\'ll review it within 24-48 hours.');
    }
}
