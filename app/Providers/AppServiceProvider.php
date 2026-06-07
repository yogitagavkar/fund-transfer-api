<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for(
            'transfer-api',
            function (Request $request) {

                return Limit::perMinute(20)
                    ->by(
                        $request->user()?->id
                        ?: $request->ip()
                    );
            }
        );

        RateLimiter::for(
            'balance-api',
            function (Request $request) {

                return Limit::perMinute(100)
                    ->by(
                        $request->user()?->id
                        ?: $request->ip()
                    );
            }
        );
    }
}