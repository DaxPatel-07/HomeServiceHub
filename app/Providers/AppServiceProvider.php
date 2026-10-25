<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        RateLimiter::for('login', function (Request $request) {
            $key = (string) $request->input('email') . '|' . $request->ip();
            return Limit::perMinute(10)->by($key);
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('ai-chat', function (Request $request) {
            $key = $request->user()?->id ? (string) $request->user()->id : $request->ip();
            return Limit::perMinute(60)->by($key);
        });
    }
}
