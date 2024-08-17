<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CookieHandleService;

class CookieHandleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CookieHandleService::class, function () {
            return new CookieHandleService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
