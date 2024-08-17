<?php
namespace App\UseCases\Oidc;

use App\Cognito\CognitoClient;
use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Services\Cookie\CookieServiceInterface;

class OidcUseCase
{


    private $baseUrl;
    private $clientId;
    private $poolId;
    private $cognitoApiRepository;
    private $login;
    private $cookieService;

    public function __construct(CognitoApiRepositoryInterface $cognitoApiRepository, LoginRepositoryInterface $login, CookieServiceInterface $cookieService)
    {
        $this->cognitoApiRepository = $cognitoApiRepository;
        $this->baseUrl = config('services.cognito.cognito_domain');
        $this->clientId = config('services.cognito.app_client_id');
        $this->poolId = config('services.cognito.user_pool_id');
        $this->login = $login;
        $this->cookieService = $cookieService;
    }



    /**
     * Undocumented function
     *
     * @param array $data
     * @return string
     */
    public function index(array $data): string
    {
        try {
            list($type, $redirectUrl, $projectId, $cookie, $path) = $data;

            if (!$cookie) {
                $cookie = $this->cookieService->generateCookie();
                $this->cookieService->setCookie($cookie);
            }

            $identityProviders = $this->cognitoApiRepository->getProviders();
            $codeVerifier = str_replace('=', '', strtr(base64_encode(openssl_random_pseudo_bytes(32)), '+/', '-_'));
            $codeChallenge = hash('sha256', $codeVerifier, true);
            $codeChallenge = str_replace('=', '', strtr(base64_encode($codeChallenge), '+/', '-_'));

            $nonce = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8);

            $session = json_encode([
                'redirectUrl'   => $redirectUrl,
                'codeVerifier'  => $codeVerifier,
                'nonce'         => $nonce,
                'signup'        => $path
            ]);
            session()->put($cookie, $session);

            
            if (!in_array($type, $identityProviders)) throw new \Exception('provider not found', 400);
            $query = [
                'response_type'         => 'code',
                'client_id'             => $this->clientId,
                'redirect_uri'          => config('app.url') .'/api/auth/callback',
                'identity_provider'     => $type,
                'scope'                 => 'openid email',
                'nonce'                 => $nonce,
                'code_challenge'        => $codeChallenge,
                'code_challenge_method' => 'S256',
                'state'             => json_encode([
                    'project_id'   => $projectId,
                    'redirect_uri' => $redirectUrl,
                ]),
            ];
            $queryString = http_build_query($query, '', '&');
            $authUrl  = $this->baseUrl . '/oauth2/authorize?' . $queryString;
            $this->cookieService->setCookie($cookie);
            

        } catch (\Exception $e) {
            return $redirectUrl .= '?success=false&errorMessage=' . $e->getMessage();
        }
        return $authUrl;
    }
}