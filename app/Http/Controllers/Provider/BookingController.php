<?php
namespace App\Http\Controllers\Provider;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Provider;
use App\Services\SkillScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller {
    private function getProvider(): Provider {
        return Provider::where('user_id', Auth::id())->firstOrFail();
    }
    public function index() {
        $provider = $this->getProvider();
        $bookings = Booking::where('provider_id', $provider->id)->with(['customer', 'category'])->latest()->paginate(10);
        return view('provider.bookings', compact('bookings', 'provider'));
    }
    public function accept(Booking $booking) {
        $provider = $this->getProvider();
        abort_if($booking->provider_id !== $provider->id, 403);
        abort_if($booking->status !== 'pending', 422, 'Only pending bookings can be accepted.');
        $booking->update(['status' => 'accepted']);
        return back()->with('success', 'Booking accepted!');
    }
    public function reject(Booking $booking, Request $request) {
        $provider = $this->getProvider();
        abort_if($booking->provider_id !== $provider->id, 403);
        abort_if(!in_array($booking->status, ['pending', 'accepted']), 422, 'This booking cannot be rejected.');
        $booking->update(['status' => 'cancelled', 'cancellation_reason' => $request->reason]);
        if ($provider->total_jobs > 0) {
            $provider->decrement('total_jobs');
        }
        return back()->with('success', 'Booking rejected.');
    }
    public function complete(Booking $booking) {
        $provider = $this->getProvider();
        abort_if($booking->provider_id !== $provider->id, 403);
        abort_if($booking->status !== 'accepted', 422, 'Only accepted bookings can be marked complete.');
        $booking->update(['status' => 'completed', 'payment_status' => 'paid']);
        $provider->increment('completed_jobs');
        app(SkillScoreService::class)->updateAndAssignBadges($provider);
        return back()->with('success', 'Booking marked as completed!');
    }
}
