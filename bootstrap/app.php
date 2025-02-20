<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Define your scheduled tasks here
        $schedule->command('app:clean-qr-cache')->daily();

        $schedule->call(function () {
            // Clean cache for expired QR codes
            $keys = Cache::get('qr_limit_*');
            foreach ($keys as $key) {
                $data = Cache::get($key);
                if ($data['expires_at'] && Carbon::parse($data['expires_at'])->isPast()) {
                    Cache::forget($key);
                }
            }
        })->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
