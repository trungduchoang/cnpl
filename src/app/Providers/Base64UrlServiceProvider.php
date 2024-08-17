<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Base64UrlService;

class Base64UrlServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Base64UrlService::class, function () {
            return new Base64UrlService();
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
