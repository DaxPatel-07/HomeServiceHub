<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller {
    public function index(Request $request) {
        $query = Provider::with(['user','category']);

        if ($request->filled('status') && in_array($request->status, ['pending', 'verified', 'rejected'])) {
            $query->where('verification_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('service_area', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $providers = $query->latest()->paginate(15)->withQueryString();
        $pendingCount = Provider::where('verification_status', 'pending')->count();
        $verifiedCount = Provider::where('verification_status', 'verified')->count();
        $rejectedCount = Provider::where('verification_status', 'rejected')->count();
        $totalCount = Provider::count();

        return view('admin.providers', compact('providers', 'pendingCount', 'verifiedCount', 'rejectedCount', 'totalCount'));
    }

    public function verify(Provider $provider) {
        $provider->update([
            'verification_status' => 'verified',
            'is_active' => true,
        ]);
        if ($provider->user) {
            $provider->user->update(['is_active' => true]);
        }
        app(\App\Services\SkillScoreService::class)->updateAndAssignBadges($provider);
        return back()->with('success', 'Provider "'.$provider->name.'" approved & activated successfully!');
    }

    public function reject(Provider $provider) {
        $provider->update([
            'verification_status' => 'rejected',
            'is_active' => false,
        ]);
        app(\App\Services\SkillScoreService::class)->updateAndAssignBadges($provider);
        return back()->with('success', 'Provider "'.$provider->name.'" application rejected and marked inactive.');
    }
}
