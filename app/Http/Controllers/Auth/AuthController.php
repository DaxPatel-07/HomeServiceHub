<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function showLogin(Request $request) {
        if ($request->filled('redirect')) {
            session()->put('url.intended', $request->query('redirect'));
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($request->filled('redirect') && !str_contains($request->input('redirect'), 'login')) {
                return redirect()->to($request->input('redirect'));
            }

            return match($user->role) {
                'customer' => redirect()->intended(route('customer.dashboard')),
                'provider' => redirect()->route('provider.dashboard'),
                'admin' => redirect()->route('admin.dashboard'),
                'master_admin' => redirect()->route('master.dashboard'),
                default => redirect()->intended(route('home')),
            };
        }
        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    public function showRegister() {
        $categories = ServiceCategory::where('is_active', true)->get();
        return view('auth.register', compact('categories'));
    }

    public function showBecomeProvider() {
        $categories = ServiceCategory::where('is_active', true)->get();
        return view('auth.become-provider', compact('categories'));
    }

    public function register(Request $request) {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:customer,provider',
            'phone'    => 'required|string|max:20',
            'city'     => 'required|in:Ahmedabad,Gandhinagar',
        ];
        if ($request->role === 'provider') {
            $rules['category_id']   = 'required|exists:service_categories,id';
            $rules['hourly_rate']   = 'required|numeric|min:0';
            $rules['experience_years'] = 'nullable|numeric|min:0';
            $rules['service_area']  = 'nullable|string|max:255';
        }
        $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'city' => $request->city,
            'address' => $request->address,
        ]);

        if ($request->role === 'provider') {
            Provider::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id'         => $request->category_id,
                    'business_name'       => $request->business_name ?: $user->name,
                    'bio'                 => $request->bio,
                    'experience_years'    => $request->experience_years ?? 0,
                    'service_area'        => $request->service_area ?: $request->city,
                    'hourly_rate'         => $request->hourly_rate ?? 0,
                    'verification_status' => 'pending',
                    'is_active'           => true,
                ]
            );
        }

        Auth::login($user);
        
        if ($user->role === 'provider') {
            return redirect()->route('provider.dashboard')->with('success', '🎉 Welcome to HomeServiceHub! Your provider account was created successfully and is awaiting admin verification.');
        } elseif ($user->role === 'customer') {
            return redirect()->route('customer.dashboard')->with('success', 'Welcome to HomeServiceHub! Your customer account has been created.');
        }

        return redirect()->route('home');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
