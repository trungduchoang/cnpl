<?php

namespace Tests\Unit\UseCases\Url;

use App\UseCases\Url\AuthUrlUseCase;
use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;

class AuthUrlUseCaseTest extends TestCase
{
    
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
    
        $this->createApplication();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSuccess()
    {
        $redirectUrl = 'https://google.com';
        $projectId = '99999';

        $identityProviders = ['Google', 'SignInWithApple'];

        $authUrl = config('services.cognito.cognito_domain') . '/oauth2/authorize?';
        $expectedResponse = [
            'google' => [
                'scheme' => parse_url($authUrl)['scheme'],
                'host'   => parse_url($authUrl)['host'],
                'path'   => parse_url($authUrl)['path'],
                'query'  => [
                    'response_type'     => 'code',
                    'client_id'         => config('services.cognito.app_client_id'),
                    'redirect_uri'      => config('app.url') .'/api/auth/callback',
                    'identity_provider' => 'Google',
                    'scope'             => 'openid email',
                    'state'             => json_encode(['project_id'   => $projectId, 'redirect_uri' => $redirectUrl])
                ]
            ],
            'apple' => [
                'scheme' => parse_url($authUrl)['scheme'],
                'host'   => parse_url($authUrl)['host'],
                'path'   => parse_url($authUrl)['path'],
                'query'  => [
                    'response_type'     => 'code',
                    'client_id'         => config('services.cognito.app_client_id'),
                    'redirect_uri'      => config('app.url') .'/api/auth/callback',
                    'identity_provider' => 'SignInWithApple',
                    'scope'             => 'openid email',
                    'state'             => json_encode(['project_id'   => $projectId, 'redirect_uri' => $redirectUrl])
                ]
            ]
        ];

        
        $authUrlUseCase = new AuthUrlUseCase();
        
        $response = $authUrlUseCase->index([$redirectUrl, $projectId]);
        $fixedResponse = [];
        foreach ($response as $key => $url) {
            $fixedResponse[$key] = parse_url($response[$key]);
            parse_str($fixedResponse[$key]['query'], $fixedResponse[$key]['query']);
        }
        
        $this->assertSame($expectedResponse, $fixedResponse);
    }
}
