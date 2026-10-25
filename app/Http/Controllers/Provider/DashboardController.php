<?php
namespace App\Http\Controllers\Provider;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller {
    public function index() {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->with(['category','badges'])->first();
        if (!$provider) {
            $defaultCat = \App\Models\ServiceCategory::first();
            $provider = Provider::create([
                'user_id'             => $user->id,
                'category_id'         => $defaultCat ? $defaultCat->id : 1,
                'business_name'       => $user->name,
                'service_area'        => $user->city ?: 'Ahmedabad',
                'hourly_rate'         => 450,
                'verification_status' => 'pending',
                'is_active'           => true,
                'is_available'        => true,
            ]);
            $provider->load(['category','badges']);
        }
        $bookings = Booking::where('provider_id', $provider->id)->with(['customer','category'])->latest()->take(5)->get();
        $allBookings = Booking::where('provider_id', $provider->id);
        $stats = [
            'total' => (clone $allBookings)->count(),
            'pending' => (clone $allBookings)->where('status','pending')->count(),
            'completed' => (clone $allBookings)->where('status','completed')->count(),
            'earnings' => (clone $allBookings)->where('status','completed')->sum('total_amount'),
        ];
        $monthExpr = config('database.default') === 'sqlite'
            ? "cast(strftime('%m', created_at) as integer)"
            : 'MONTH(created_at)';
        $earningsMonthly = Booking::where('provider_id', $provider->id)
            ->where('status','completed')
            ->selectRaw("{$monthExpr} as month, SUM(total_amount) as total")
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $notifications = Booking::where('provider_id', $provider->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->with(['customer', 'category'])
            ->latest()
            ->take(5)
            ->get();
        return view('provider.dashboard', compact('user','provider','bookings','stats','earningsMonthly','notifications'));
    }
}
