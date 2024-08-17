<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Services\Cognito\CheckUserExistenceServiceInterface::class,
            \App\Services\Cognito\CheckUserExistenceService::class,
        );
        $this->app->bind(
            \App\Services\Jwt\JwtVerifierServiceInterface::class,
            \App\Services\Jwt\JwtVerifierService::class
        );
        $this->app->bind(
            \App\Services\Cookie\CookieServiceInterface::class,
            \App\Services\Cookie\CookieService::class
        );
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
