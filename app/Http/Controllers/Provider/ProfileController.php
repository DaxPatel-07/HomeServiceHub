<?php
namespace App\Http\Controllers\Provider;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller {
    public function edit() {
        $provider = Provider::where('user_id', Auth::id())->with('category')->firstOrFail();
        $categories = ServiceCategory::where('is_active', true)->get();
        return view('provider.profile', compact('provider', 'categories'));
    }
    public function update(Request $request) {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->firstOrFail();
        $request->validate([
            'name'             => 'required|string|max:100',
            'phone'            => 'required|string|max:20',
            'city'             => 'required|in:Ahmedabad,Gandhinagar',
            'business_name'    => 'nullable|string|max:150',
            'category_id'      => 'required|exists:service_categories,id',
            'experience_years' => 'nullable|integer|min:0|max:80',
            'hourly_rate'      => 'required|numeric|min:0|max:100000',
            'service_area'     => 'required|string|max:255',
            'bio'              => 'nullable|string|max:2000',
        ]);
        $user->update(['name'=>$request->name,'phone'=>$request->phone,'city'=>$request->city]);
        $provider->update($request->only('business_name','bio','experience_years','service_area','hourly_rate','category_id'));
        return back()->with('success', 'Profile updated!');
    }
    public function toggleAvailability(Request $request) {
        $provider = Provider::where('user_id', Auth::id())->firstOrFail();
        abort_if(!$provider->is_active || $provider->verification_status !== 'verified', 403, 'Your provider account is not active or verified.');
        $provider->update(['is_available' => !$provider->is_available]);
        if ($request->expectsJson() || $request->isJson() || $request->ajax()) {
            return response()->json(['available' => $provider->is_available, 'success' => true]);
        }
        return back()->with('success', 'Availability updated to ' . ($provider->is_available ? 'Available' : 'Unavailable') . '.');
    }
}
