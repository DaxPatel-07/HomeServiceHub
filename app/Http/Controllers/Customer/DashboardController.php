<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Provider;
use App\Models\MaintenanceReminder;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller {
    public function index() {
        $user = Auth::user();
        $userCity = in_array($user->city, ['Ahmedabad', 'Gandhinagar']) ? $user->city : 'Ahmedabad';
        $bookings = Booking::where('customer_id', $user->id)->with(['provider.user','category'])->latest()->take(5)->get();
        $allBookings = Booking::where('customer_id', $user->id);
        $stats = [
            'total' => (clone $allBookings)->count(),
            'pending' => (clone $allBookings)->where('status','pending')->count(),
            'completed' => (clone $allBookings)->where('status','completed')->count(),
            'cancelled' => (clone $allBookings)->where('status','cancelled')->count(),
            'spent' => (clone $allBookings)->where('status','completed')->sum('total_amount'),
        ];
        $reminders = MaintenanceReminder::where('customer_id', $user->id)->where('is_active', true)->with('category')->orderBy('next_due_date')->take(3)->get();
        
        // Neighborhood Trusted Providers for the customer's city
        $recommendedProviders = Provider::with(['user','category','badges'])
            ->where('verification_status','verified')
            ->where('is_active', true)
            ->where(function($q) use ($userCity) {
                $q->where('service_area', 'like', '%'.$userCity.'%')
                  ->orWhereHas('user', fn($sub) => $sub->where('city', $userCity));
            })
            ->neighborhoodTrusted()
            ->orderByDesc('skill_score')
            ->take(4)->get();

        if ($recommendedProviders->count() < 4) {
            $moreProviders = Provider::with(['user','category','badges'])
                ->where('verification_status','verified')
                ->where('is_active', true)
                ->whereNotIn('id', $recommendedProviders->pluck('id'))
                ->orderByDesc('skill_score')
                ->take(4 - $recommendedProviders->count())->get();
            $recommendedProviders = $recommendedProviders->merge($moreProviders);
        }

        return view('customer.dashboard', compact('user','bookings','stats','reminders','recommendedProviders','userCity'));
    }
}
