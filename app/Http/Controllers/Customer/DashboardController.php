<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MaintenanceReminder;
use App\Services\NeighbourhoodProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected NeighbourhoodProviderService $neighbourhoodService;

    public function __construct(NeighbourhoodProviderService $neighbourhoodService)
    {
        $this->neighbourhoodService = $neighbourhoodService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $userCity = in_array($user->city, ['Ahmedabad', 'Gandhinagar']) ? $user->city : 'Ahmedabad';
        $bookings = Booking::where('customer_id', $user->id)->with(['provider.user', 'category'])->latest()->take(5)->get();
        $allBookings = Booking::where('customer_id', $user->id);
        $stats = [
            'total'     => (clone $allBookings)->count(),
            'pending'   => (clone $allBookings)->where('status', 'pending')->count(),
            'completed' => (clone $allBookings)->where('status', 'completed')->count(),
            'cancelled' => (clone $allBookings)->where('status', 'cancelled')->count(),
            'spent'     => (clone $allBookings)->where('status', 'completed')->sum('total_amount'),
        ];
        $reminders = MaintenanceReminder::where('customer_id', $user->id)->where('is_active', true)->with('category')->orderBy('next_due_date')->take(3)->get();

        // Location options from query, session, or customer profile
        $locationOptions = [
            'lat'      => $request->query('lat', session('customer_lat')),
            'lng'      => $request->query('lng', session('customer_lng')),
            'radius'   => $request->query('radius', session('customer_radius', config('neighbourhood.default_radius_km', 5.0))),
            'locality' => $request->query('locality'),
            'building' => $request->query('building'),
            'city'     => $request->query('city'),
        ];

        // Persist browser coordinates or radius in session if provided
        if ($request->filled('lat') && $request->filled('lng')) {
            session(['customer_lat' => (float) $request->lat, 'customer_lng' => (float) $request->lng]);
        }
        if ($request->filled('radius')) {
            session(['customer_radius' => (float) $request->radius]);
        }

        $neighbourhoodResult = $this->neighbourhoodService->getNearbyTrustedProviders($user, $locationOptions);
        $recommendedProviders = $neighbourhoodResult['providers'];
        $customerLocation = $neighbourhoodResult['customer_location'];
        $locationStatus = $neighbourhoodResult['status'];
        $currentRadius = $neighbourhoodResult['radius'];
        $radiusOptions = $neighbourhoodResult['radius_options'];

        // If AJAX request or JSON requested, return json response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'            => $locationStatus,
                'customer_location' => $customerLocation,
                'radius'            => $currentRadius,
                'total_matched'     => $neighbourhoodResult['total_matched'],
                'providers'         => $recommendedProviders->map(fn($p) => [
                    'id'             => $p->id,
                    'name'           => $p->name,
                    'category'       => $p->category->name ?? null,
                    'service_area'   => $p->service_area,
                    'distance_km'    => $p->distance_km,
                    'distance_text'  => $p->distance_text,
                    'match_type'     => $p->match_type,
                    'match_badge'    => $p->match_badge,
                    'average_rating' => $p->average_rating,
                    'completed_jobs' => $p->completed_jobs,
                    'hourly_rate'    => $p->hourly_rate,
                ]),
            ]);
        }

        return view('customer.dashboard', compact(
            'user',
            'bookings',
            'stats',
            'reminders',
            'recommendedProviders',
            'userCity',
            'customerLocation',
            'locationStatus',
            'currentRadius',
            'radiusOptions'
        ));
    }

    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'address'   => 'nullable|string|max:255',
            'locality'  => 'nullable|string|max:100',
            'building'  => 'nullable|string|max:150',
            'city'      => 'nullable|in:Ahmedabad,Gandhinagar',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $data = $request->only('address', 'locality', 'building', 'city');
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $data['latitude'] = (float) $request->latitude;
            $data['longitude'] = (float) $request->longitude;
            session(['customer_lat' => (float) $request->latitude, 'customer_lng' => (float) $request->longitude]);
        }

        $user->update($data);

        return back()->with('success', 'Your neighbourhood location has been updated successfully!');
    }
}
