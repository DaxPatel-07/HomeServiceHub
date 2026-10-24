<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class SearchController extends Controller {
    public function index(Request $request) {
        $categories = ServiceCategory::where('is_active', true)->get();
        $query = Provider::with(['user','category','badges'])
            ->where('verification_status','verified')
            ->where('is_active', true);

        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->city) {
            $query->where(function($q) use ($request) {
                $q->where('service_area', 'like', '%'.$request->city.'%')
                  ->orWhereHas('user', fn($sub) => $sub->where('city', $request->city));
            });
        }
        if ($request->min_rating) {
            $query->where('average_rating', '>=', $request->min_rating);
        }
        if ($request->boolean('neighborhood_trusted')) {
            $query->neighborhoodTrusted();
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('user', fn($sub) => $sub->where('name','like','%'.$request->search.'%'))
                  ->orWhere('business_name','like','%'.$request->search.'%')
                  ->orWhereHas('category', fn($sub) => $sub->where('name','like','%'.$request->search.'%'));
            });
        }
        $providers = $query->orderByDesc('skill_score')->paginate(9);
        return view('customer.search', compact('providers','categories'));
    }

    public function providerProfile(Request $request, Provider $provider) {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Please log in to view full provider details.'
                ], 401);
            }
            return redirect()->guest(route('login'))->with('info', 'Please log in to view full provider details.');
        }

        $canPreview = auth()->user()->isAdminOrMaster() || auth()->id() === $provider->user_id;
        if (($provider->verification_status !== 'verified' || !$provider->is_active) && !$canPreview) {
            abort(404, 'Provider profile not found or pending verification.');
        }

        $provider->load(['user','category','badges','reviews.customer']);
        $recentReviews = $provider->reviews()->where('is_visible',true)->with('customer')->latest()->get();
        $relatedProviders = Provider::with(['user','category','badges'])
            ->where('category_id', $provider->category_id)
            ->where('id','!=', $provider->id)
            ->where('verification_status','verified')
            ->take(3)->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'provider' => [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'bio' => $provider->bio,
                    'category' => $provider->category->name ?? null,
                    'experience_years' => $provider->experience_years,
                    'service_area' => $provider->service_area,
                    'hourly_rate' => $provider->hourly_rate,
                    'average_rating' => $provider->average_rating,
                    'total_jobs' => $provider->total_jobs,
                    'completed_jobs' => $provider->completed_jobs,
                    'is_available' => (bool)$provider->is_available,
                    'is_neighborhood_trusted' => $provider->isNeighborhoodTrusted(),
                    'badges' => $provider->badges->pluck('name'),
                    'reviews' => $recentReviews->map(fn($r) => [
                        'rating' => $r->rating,
                        'comment' => $r->comment,
                        'customer_name' => $r->customer->name ?? 'Customer',
                        'created_at' => $r->created_at->diffForHumans(),
                    ]),
                ]
            ]);
        }

        return view('customer.provider-profile', compact('provider','recentReviews','relatedProviders'));
    }

    public function apiProviderDetails(Request $request, Provider $provider) {
        return $this->providerProfile($request, $provider);
    }
}
