<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CryptoQueryService;
use Illuminate\Console\Application;

class CryptoQueryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CryptoQueryService::class, function () {
            return new CryptoQueryService(
                config('config.crypto.iv'),
                config('config.crypto.key'),
                config('config.crypto.method')
            );
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
