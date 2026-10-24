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
    private function isSafeRedirect(?string $url): bool {
        if (empty($url)) {
            return false;
        }
        if (str_contains($url, "\r") || str_contains($url, "\n")) {
            return false;
        }
        // Local relative paths starting with / but not protocol-relative // or /\
        if (str_starts_with($url, '/') && !str_starts_with($url, '//') && !str_starts_with($url, '/\\')) {
            return true;
        }
        $parsed = parse_url($url);
        if ($parsed && isset($parsed['host']) && $parsed['host'] === request()->getHost()) {
            return true;
        }
        return false;
    }

    public function showLogin(Request $request) {
        if ($request->filled('redirect')) {
            $redirect = $request->query('redirect');
            if ($this->isSafeRedirect($redirect)) {
                session()->put('url.intended', $redirect);
            }
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your account has been deactivated. Please contact support.'])->withInput();
            }

            $request->session()->regenerate();

            if ($request->filled('redirect')) {
                $redirect = $request->input('redirect');
                if ($this->isSafeRedirect($redirect) && !str_contains($redirect, 'login')) {
                    return redirect()->to($redirect);
                }
                session()->forget('url.intended');
            }

            $intended = session()->get('url.intended');
            if ($intended && (!$this->isSafeRedirect($intended) || $intended === url('/') || $intended === route('home'))) {
                session()->forget('url.intended');
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

    public function showRegister(Request $request) {
        $categories = ServiceCategory::where('is_active', true)->get();
        $initialRole = $request->query('role', 'customer');
        if (!in_array($initialRole, ['customer', 'provider'])) {
            $initialRole = 'customer';
        }
        return view('auth.register', compact('categories', 'initialRole'));
    }

    public function showBecomeProvider(Request $request) {
        $categories = ServiceCategory::where('is_active', true)->get();
        $initialRole = 'provider';
        return view('auth.register', compact('categories', 'initialRole'));
    }

    public function register(Request $request) {
        $rules = [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:customer,provider',
            'phone'    => 'required|string|max:20',
            'city'     => 'required|in:Ahmedabad,Gandhinagar',
            'address'  => 'nullable|string|max:255',
        ];
        if ($request->role === 'provider') {
            $rules['category_id']      = 'required|exists:service_categories,id';
            $rules['hourly_rate']      = 'required|numeric|min:0|max:100000';
            $rules['experience_years'] = 'nullable|integer|min:0|max:80';
            $rules['service_area']     = 'nullable|string|max:255';
            $rules['business_name']    = 'nullable|string|max:150';
            $rules['bio']              = 'nullable|string|max:2000';
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
