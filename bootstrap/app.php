<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
      $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
                if (! $request->expectsJson()) {
                    // Ambil sisa waktu blokir dari header (default 60 detik jika gagal terbaca)
                    $seconds = $e->getHeaders()['Retry-After'] ?? 60;
                    
                    return back()->with([
                        'sweet_error' => 'Terlalu banyak percobaan pendaftaran!',
                        'retry_after' => $seconds // Kirim angka detiknya ke session
                    ]);
                }
            });
    })->create();
