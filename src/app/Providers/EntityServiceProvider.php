<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EntityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Entities\Cognito\CognitoUserEntityInterface::class,
            \App\Entities\Cognito\CognitoUserEntity::class
        );
        $this->app->bind(
            \App\Entities\Login\LoginEntityInterface::class,
            \App\Entities\Login\LoginEntity::class
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
