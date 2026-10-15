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
            'name'=>'required',
            'phone'=>'required',
            'city'=>'required|in:Ahmedabad,Gandhinagar',
            'bio'=>'nullable|string',
            'hourly_rate'=>'required|numeric',
            'service_area'=>'required|string'
        ]);
        $user->update(['name'=>$request->name,'phone'=>$request->phone,'city'=>$request->city]);
        $provider->update($request->only('business_name','bio','experience_years','service_area','hourly_rate','category_id'));
        return back()->with('success', 'Profile updated!');
    }
    public function toggleAvailability() {
        $provider = Provider::where('user_id', Auth::id())->firstOrFail();
        $provider->update(['is_available' => !$provider->is_available]);
        return response()->json(['available' => $provider->is_available]);
    }
}
