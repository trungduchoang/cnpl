<?php

namespace App\Providers;

use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class CognitoAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(CognitoClient::class, function (Application $app) {

            $config = [
                'region' => config('services.ses.region'),
                'version' => config('services.cognito.version'),
            ];

            return new CognitoClient(
                new CognitoIdentityProviderClient($config),
                config('services.cognito.app_client_id'),
                config('services.cognito.user_pool_id')
            );
        });
    }
}