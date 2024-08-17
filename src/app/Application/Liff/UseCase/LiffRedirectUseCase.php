<?php

namespace App\Application\Liff\UseCase;

use App\Application\Liff\Request\LiffRedirectRequest;
use App\Services\CookieHandleService;
use App\Models\Login;
use App\Models\LoginLog;
use App\Services\CryptoQueryService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\DB;

class LiffRedirectUseCase
{
    protected $cookieHandleService;
    protected $cryptoQueryService;

    public function __construct(CookieHandleService $cookieHandleService, CryptoQueryService $cryptoQueryService)
    {
        $this->cookieHandleService = $cookieHandleService;
        $this->cryptoQueryService = $cryptoQueryService;
    }

    public function index($request)
    {
        try {
            $params = $request->getParams();
            $params['accessToken'] = $this->cryptoQueryService->decryptQuery($params['accessToken']);
            $cookie = $this->getCookie($request);
            $this->verifyAccessToken($params['accessToken']);
            DB::beginTransaction();
            $userInfo = $this->getUserInfo($params['accessToken']); 
            if (!$this->checkFriendFlag($params['accessToken'])) {
                return $params['notFriendUrl'] . '?' . $this->getRedirectQuery($params);
            }
            $oldCookie = $this->upsertLogin($cookie, $userInfo['userId'], $params['projectId']);
            $cookie = $oldCookie ? $oldCookie : $cookie;
            $this->upsertLoginLog($cookie, $userInfo['userId'], $params['projectId']);
            $this->cookieHandleService->setCookie($cookie);
            DB::commit();
        } catch (\Exception $e) {
            logger($e);
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $params['redirectUrl'] . '?' . $this->getRedirectQuery($params);
    }


    private function getCookie($request)
    {
        $cookie = $this->cookieHandleService->getCookie($request);
        if ($cookie) {
            return $cookie;
        }
        $cookie = $this->cookieHandleService->generateCookie();
        return $cookie;
    }


    /**
     * verify line access token function
     *
     * @param string $token
     * @return void
     */
    private function verifyAccessToken(string $token)
    {
        try {
            $baseUrl = 'https://api.line.me/oauth2/v2.1/verify';
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('GET', $baseUrl, [
                'query' => [
                    'access_token' => $token,
                ]
            ])->wait();
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
        } catch (ClientException $e) {
            logger($e);
            $response = $e->getResponse();
            $message = json_decode($response->getBody()->getContents(), true)['error_description'];
            throw new \Exception($message, $response->getStatusCode());
        }
    }


    /**
     * get line user info function
     *
     * @param string $token
     * @return void
     */
    private function getUserInfo(string $token)
    {
        try {
            $baseUrl = 'https://api.line.me/v2/profile';
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('GET', $baseUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ]
            ])->wait();
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
        } catch (ClientException $e) {
            logger($e);
            $response = $e->getResponse();
            $message = json_decode($response->getBody()->getContents(), true)['error_description'];
            throw new \Exception($message, $response->getStatusCode());
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
    private function getRedirectQuery(array $params): string
    {
        $query = [];
        foreach ($params as $key => $value) {
            if (
                $key !== 'redirectUrl' &&
                $key !== 'projectId' &&
                $key !== 'redirectUrlErr' &&
                $key !== 'notFriendUrl' &&
                $key !== 'accessToken'
            ) {
                $query[$key] = $value;
            }
        }
        return http_build_query($query, '', '&', PHP_QUERY_RFC3986);
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