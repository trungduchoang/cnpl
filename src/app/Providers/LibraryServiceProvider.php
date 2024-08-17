<?php

namespace App\Providers;

use App\Libraries\CryptoQueryUtil;
use Illuminate\Support\ServiceProvider;

class LibraryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Libraries\CryptoQueryUtilInterface::class,
            function () {
                return new CryptoQueryUtil(
                    hex2bin(config('config.crypto.iv')),
                    config('config.crypto.key'),
                    config('config.crypto.method')
                );
            }
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
