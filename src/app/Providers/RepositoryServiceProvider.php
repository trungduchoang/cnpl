<?php

namespace App\Providers;

use App\Repositories\S3\S3ApiRepositoryInterface;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\S3\S3Client;
use Aws\Ses\SesClient;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Repositories\Login\LoginRepositoryInterface::class,
            \App\Repositories\Login\LoginRepository::class
        );
        $this->app->bind(
            \App\Repositories\Temp\SignupTempRepositoryInterface::class,
            \App\Repositories\Temp\SignupTempRepository::class
        );
        $this->app->bind(
            \App\Repositories\LoginLog\LoginLogRepositoryInterface::class,
            \App\Repositories\LoginLog\LoginLogRepository::class
        );
        $this->app->bind(
            \App\Repositories\Temp\SigninTempRepositoryInterface::class,
            \App\Repositories\Temp\SigninTempRepository::class
        );
        $this->app->bind(
            \App\Repositories\Xid\XidTempRepositoryInterface::class,
            \App\Repositories\Xid\XidTempRepository::class
        );
        $this->app->bind(
            \App\Repositories\Xid\XidTokenRepositoryInterface::class,
            \App\Repositories\Xid\XidTokenRepository::class
        );
        $this->app->bind(
            \App\Repositories\Line\LineApiRepositoryInterface::class,
            \App\Repositories\Line\LineApiRepository::class
        );
        $this->app->bind(
            \App\Repositories\Temp\TempRepositoryInterface::class,
            \App\Repositories\Temp\TempRepository::class
        );
        $this->app->bind(
            \App\Repositories\Cognito\CognitoApiRepositoryInterface::class,
            function () {
                $config = [
                    'region' => config('services.ses.region'),
                    'version' => config('services.cognito.version'),
                ];
                return new \App\Repositories\Cognito\CognitoApiRepository(
                    new CognitoIdentityProviderClient($config),
                    config('services.cognito.app_client_id'),
                    config('services.cognito.user_pool_id'),
                    config('services.cognito.cognito_domain')
                );
            }
        );
        $this->app->bind(
            \App\Repositories\Ses\SesApiRepositoryInterface::class,
            function () {
                $config = [
                    'region' => 'us-west-2',
                    'version' => config('services.ses.version')
                ];
                return new \App\Repositories\Ses\SesApiRepository(
                    new SesClient($config)
                );
            }
        );
        $this->app->bind(
            \App\Repositories\S3\S3ApiRepositoryInterface::class,
            function () {
                $config = [
                    'region' => config('services.ses.region'),
                    'version' => '2006-03-01',
                ];
                return new \App\Repositories\S3\S3ApiRepository(
                    new S3Client($config)
                );
            }
        );
        $this->app->bind(
            \App\Repositories\Xid\XidApiRepositoryInterface::class,
            \App\Repositories\Xid\XidApiRepository::class
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
