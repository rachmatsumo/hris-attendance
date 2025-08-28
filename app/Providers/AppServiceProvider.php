<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Laravel\Pail\PailServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $timezone = setting('app_timezone') ?? 'Asia/Jakarta';
 
        Config::set('app.timezone', $timezone);
        date_default_timezone_set($timezone);

        if ($this->app->runningInConsole()) {
            return;
        }

        $expiredAt = \Carbon\Carbon::parse(
            base64_decode(config('license.expired_at'))
        );

        if (now()->greaterThan($expiredAt)) {
            $response = response()->view('errors.license-expired', [], 403);
            $response->send();
            exit;
        }
    }
}
