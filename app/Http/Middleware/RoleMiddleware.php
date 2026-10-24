<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, ...$roles): mixed {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Unauthenticated', 'message' => 'Please log in to proceed.'], 401);
            }
            return redirect()->route('login');
        }
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Deactivated', 'message' => 'Your account has been deactivated. Please contact support.'], 403);
            }
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);
        }
        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Forbidden', 'message' => 'Unauthorized access.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }
        return $next($request);
    }
}
