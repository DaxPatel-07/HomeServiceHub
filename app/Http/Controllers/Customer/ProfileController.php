<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller {
    public function index() {
        $user = Auth::user();
        $allBookings = Booking::where('customer_id', $user->id);
        $stats = [
            'total'     => (clone $allBookings)->count(),
            'completed' => (clone $allBookings)->where('status','completed')->count(),
            'spent'     => (clone $allBookings)->where('status','completed')->sum('total_amount'),
        ];
        return view('customer.profile', compact('user','stats'));
    }

    public function update(Request $request) {
        $user = Auth::user();
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|unique:users,email,'.$user->id,
            'phone'   => 'nullable|string|max:20',
            'city'    => 'nullable|in:Ahmedabad,Gandhinagar',
            'address' => 'nullable|string|max:255',
        ]);
        $data = $request->only('name','phone','city','address');
        if ($request->filled('email')) {
            $data['email'] = $request->email;
        }
        $user->update($data);
        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required','confirmed', Password::min(8)],
        ]);
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password updated successfully!');
    }
}
