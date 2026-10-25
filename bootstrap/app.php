<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->redirectTo(
            guests: '/login',
            users: function () {
                $user = auth()->user();
                if ($user) {
                    return match($user->role) {
                        'customer' => route('customer.dashboard'),
                        'provider' => route('provider.dashboard'),
                        'admin' => route('admin.dashboard'),
                        'master_admin' => route('master.dashboard'),
                        default => route('home'),
                    };
                }
                return route('home');
            }
        );
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });
    })->create();
