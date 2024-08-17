<?php
namespace App\UseCases\Url;

use App\Repositories\Cognito\CognitoApiRepositoryInterface;

class AuthUrlUseCase
{


    private $baseUrl;
    private $clientId;
    private $poolId;
    private $cognitoApiRepository;

    public function __construct(CognitoApiRepositoryInterface $cognitoApiRepository)
    {
        $this->cognitoApiRepository = $cognitoApiRepository;
        $this->baseUrl = config('services.cognito.cognito_domain');
        $this->clientId = config('services.cognito.app_client_id');
        $this->poolId = config('services.cognito.user_pool_id');
    }


    /**
     * auth url usecase function
     *
     * @param array $data
     * @return array
     */
    public function index(array $data): array
    {
        try {
            list($type, $redirectUrl, $projectId) = $data;
            $identityProviders = $this->cognitoApiRepository->getProviders();
            $urls = [];
            foreach ($identityProviders as $key => $provider) {
                $query = [
                    'response_type'     => 'code',
                    'client_id'         => $this->clientId,
                    'redirect_uri'      => config('app.url') .'/api/auth/callback',
                    'identity_provider' => $provider,
                    'scope'             => 'openid email',
                    'nonce'             => substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8),
                    'state'             => json_encode([
                        'project_id'   => $projectId,
                        'redirect_uri' => $redirectUrl,
                    ]),
                ];
                $queryString = http_build_query($query, '', '&');
                $authUrl  = $this->baseUrl . '/oauth2/authorize?' . $queryString;
                if (in_array($provider, $type)) $urls[$provider] = $authUrl;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $urls;
    }
}