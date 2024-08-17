<?php
namespace App\UseCases\Callback;

use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Repositories\LoginLog\LoginLogRepositoryInterface;
use App\Services\Cookie\CookieService;
use App\Services\Jwt\JwtVerifierServiceInterface;
use DateTime;
use Illuminate\Support\Facades\DB;


class CallbackUseCase
{

    private $cookieService;
    private $cognitoApiRepository;
    private $loginRepository;
    private $loginLogRepository;
    private $jwtVerifier;

    public function __construct(
        CookieService $cookieService,
        CognitoApiRepositoryInterface $cognitoApiRepository,
        LoginRepositoryInterface $loginRepository,
        LoginLogRepositoryInterface $loginLogRepository,
        JwtVerifierServiceInterface $jwtVerifier
    ) {
        $this->cookieService = $cookieService;
        $this->cognitoApiRepository = $cognitoApiRepository;
        $this->loginRepository = $loginRepository;
        $this->loginLogRepository = $loginLogRepository;
        $this->jwtVerifier = $jwtVerifier;
    }

    public function index(array $data): string
    {
        DB::beginTransaction();
        try {
            list($redirectUrl, $projectId, $code, $error, $cookie, $ip, $userAgent) = $data;

            if (!$code && $error) throw new \Exception($error, 400);

            if (!$cookie) $cookie = $this->cookieService->generateCookie();

            
            $session = json_decode(session()->pull($cookie));
            if (!$session) new \Exception('temp is not found', 400);
            if (!property_exists($session, 'redirectUrl') || !property_exists($session, 'codeVerifier') || !property_exists($session, 'signup')) new \Exception('temp is not found');

            $cognitoUserEntity = $this->cognitoApiRepository->getAccessToken($code, $session->codeVerifier);
            $jwt = $this->jwtVerifier->decode($cognitoUserEntity->getIdToken());
            $this->jwtVerifier->verify([
                'jwt'   => $jwt,
                'iss'   => 'https://cognito-idp.'. config('services.ses.region') . '.amazonaws.com/' . config('services.cognito.user_pool_id'),
                'aud'   => config('services.cognito.app_client_id'),
                'nonce' => $session->nonce
            ]);

            $cognitoUserEntity = $this->cognitoApiRepository->getUserInfoByAccessToken($cognitoUserEntity->getAccessToken());

            $userExists = $this->loginRepository->getUserData([
                'userName'  => $cognitoUserEntity->getUserName(),
                'projectId' => $projectId
            ]);
            if ($session->signup) {
                if ($userExists) throw new \Exception('User already exists', 400);
            } else {
                if (!$userExists) throw new \Exception('User is not found', 400);
            }

            $expireDate = new DateTime();
            $expireDate->modify('+36 hours');
            $loginEntity = $this->loginRepository->createOrUpdate([
                'cookie'       => $cookie,
                'projectId'    => $projectId,
                'userName'     => $cognitoUserEntity->getUserName(),
                'expireDate'   => $expireDate->format('Y-m-d H:i:s'),
                'ip'           => $ip,
                'userAgent'    => $userAgent
            ]);


            
            $cookie = $cookie === $loginEntity->getCookie() ? $cookie : $loginEntity->getCookie();
            $this->cookieService->setCookie($cookie);
            setcookie('login_time_' . $projectId, strtotime('now'), 0x7f000000, '/');

            $this->loginLogRepository->create([
                'cookie' => $cookie,
                'userName' => $cognitoUserEntity->getUserName()
            ]);

            $redirectUrlParsed = parse_url($redirectUrl);

            DB::commit();
        } catch (\Exception $e) {
            if (!$redirectUrl) $redirectUrl = $session->redirectUrl;
            $redirectUrlParsed = parse_url($redirectUrl);
            if (array_key_exists('query', $redirectUrlParsed)) {
                $redirectUrl .= '&success=false&errorMessage=' . $e->getMessage();
            } else {
                $redirectUrl .= '?success=false&errorMessage=' . $e->getMessage();
            }
            DB::rollBack();
            return $redirectUrl;
        }
        if (array_key_exists('query', $redirectUrlParsed)) {
            $redirectUrl .= '&success=true';
        } else {
            $redirectUrl .= '?success=true';
        }
        return $redirectUrl;
    }
}