<?php
namespace App\Application\AuthApi\UseCase;


use App\Models\Login;
use App\Models\LoginLog;
use App\Services\CookieHandleService;
use Illuminate\Support\Facades\DB;
use App\Application\AuthApi\Request\LineLoginCallbackRequest;
use App\Models\LineClient;

class LineLoginCallbackUseCase
{

    protected $cookieHandleService;


    public function __construct(CookieHandleService $cookieHandleService)
    {
        $this->cookieHandleService = $cookieHandleService;
    }

    public function index(LineLoginCallbackRequest $request)
    {
        
        try {
            DB::beginTransaction();
            list($error, $state, $code) = $request->getParam();
            if ($error) {
                return $state['redirectUrlErr'] . '?' . $this->getRedirectQuery($state);
            }
            $cookie = $this->getCookie($request);
            $lineClientData = LineClient::getLineClientData($state['projectId'], [
                'channel_id_login',
                'channel_secret_login',
            ]);
            $tokenResponse = $this->getLineLoginToken($code, $lineClientData->channel_id_login, $lineClientData->channel_secret_login);
            $velifyResponse = $this->verifyLineToken($tokenResponse['id_token'], $lineClientData->channel_id_login);
            if (!$this->checkFriendFlag($tokenResponse['access_token'])) {
                return $state['notFriendUrl'] . '?' . $this->getRedirectQuery($state);
            }
            $userName = $velifyResponse['sub'];
            $oldCookie = $this->upsertLogin($cookie, $userName, $state['projectId']);
            $cookie = $oldCookie ? $oldCookie : $cookie;
            $this->upsertLoginLog($cookie, $userName);
            $this->cookieHandleService->setCookie($cookie);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $state['redirectUrl'] . '?' . $this->getRedirectQuery($state);
    }

    

    /**
     * get cookie function
     *
     * @param LineLoginCallbackRequest $request
     * @return string
     */
    private function getCookie(LineLoginCallbackRequest $request): string
    {
        $cookie = $this->cookieHandleService->getCookie($request);
        if ($cookie) {
            return $cookie;
        }
        $cookie = $this->cookieHandleService->generateCookie();
        return $cookie;
    }

    /**
     * get line user token function
     *
     * @param string $code
     * @return array
     */
    private function getLineLoginToken(string $code, string $clientId, string $clientSecret): array
    {
        try {
            $baseUrl = 'https://api.line.me/oauth2/v2.1/token';
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('POST', $baseUrl, [
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'code'          => $code,
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => config('app.url') .'/api/auth/callback/line'
                ]
            ])->wait();
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('cannot get line access token', 403);
        }
    }


    /**
     * check beeing friend function
     *
     * @param string $token
     * @return boolean
     */
    private function checkFriendFlag(string $token): bool
    {
        try {
            $baseUrl = 'https://api.line.me/friendship/v1/status';
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('GET', $baseUrl, [
                'headers'     => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ])->wait();
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true)['friendFlag'];
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('cannot get line access token', 403);
        }
    }


    /**
     * get redirect query function
     *
     * @param array $state
     * @return string
     */
    private function getRedirectQuery(array $state): string
    {
        $query = [];
        foreach ($state as $key => $value) {
            if (
                $key !== 'redirectUrl' &&
                $key !== 'projectId' &&
                $key !== 'redirectUrlErr' &&
                $key !== 'notFriendUrl'
            ) {
                $query[$key] = $value;
            }
        }
        return http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }


    /**
     * verify line token function
     *
     * @param string $token
     * @return void
     */
    private function verifyLineToken(string $token, string $clientId)
    {
        try {
            $baseUrl = 'https://api.line.me/oauth2/v2.1/verify';
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('POST', $baseUrl, [
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'id_token' => $token,
                    'client_id' => $clientId
                ]
            ])->wait();
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('cannot verify line access token', 403);
        }
    }


    private function upsertLogin($cookie, $userName, $projectId)
    {
        try {
            return Login::login($cookie, $userName, $projectId);
        } catch (\Exception $e) {
            throw new \Exception('internal server error', 500);
        }
    }

    private function upsertLoginLog($cookie, $userName)
    {
        try {
            LoginLog::login($cookie, $userName);
        } catch (\Exception $e) {
            throw new \Exception('internal server error', 500);
        }
    }
}