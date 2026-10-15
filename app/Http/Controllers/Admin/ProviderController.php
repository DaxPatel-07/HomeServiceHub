<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Provider;

class ProviderController extends Controller {
    public function index() {
        $providers = Provider::with(['user','category'])->latest()->paginate(15);
        return view('admin.providers', compact('providers'));
    }
    public function verify(Provider $provider) {
        $provider->update([
            'verification_status' => 'verified',
            'is_active' => true,
        ]);
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
