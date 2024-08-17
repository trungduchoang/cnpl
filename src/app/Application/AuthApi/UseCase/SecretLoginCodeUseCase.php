<?php

namespace App\Application\AuthApi\UseCase;

use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Support\Facades\DB;
use App\Models\Login;
use App\Models\LoginLog;
use App\Models\SigninTemp;
use App\Services\CookieHandleService;


class SecretLoginCodeUseCase
{

    protected $cookieHandleService;

    public function __construct()
    {
        $this->cookieHandleService = app()->make(CookieHandleService::class);
    }

    public function index($request)
    {
        DB::beginTransaction();
        try {
            $uid = $request->getParam($request);
            list($uid, $userName, $projectId, $session, $redirectUrl, $secretLoginCode) = $this->getTemp($uid);
            $cookie = $this->getCookie($userName);
            $newSession = $this->secretLoginCodeApiRequest($userName, $secretLoginCode, $session);
            if ($newSession) return $redirectUrl;
            $oldCookie = $this->upsertLogin($cookie, $userName, $projectId);
            $cookie = $oldCookie ? $oldCookie : $cookie;
            $this->setCookie($cookie);
            $this->upsertLoginLog($cookie, $userName);
            $this->deleteSession($userName);
            DB::commit();
        } catch (CognitoIdentityProviderException $e) {
            DB::rollback();
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $redirectUrl;
        return $this->apiResponse($userName, $oldCookie);
    }

    private function getCookie($userName)
    {
        return explode('_', $userName)[1];
    }

    private function setCookie($cookie) {
        $this->cookieHandleService->setCookie($cookie);
    }

    private function getTemp($uid)
    {
        $data = SigninTemp::getSession($uid);
        return [$data->uid, $data->oauth_name, $data->project_id, $data->session, $data->redirect_url, $data->secret_code];
    }

    private function deleteSession($userName)
    {
        SigninTemp::deleteSession($userName);
    }

    private function secretLoginCodeApiRequest($email, $secretLoginCode, $session)
    {
        $response = app()->make(CognitoClient::class)->secretLoginCode($email, $secretLoginCode, $session);
        if (!isset($response['AuthenticationResult']['AccessToken'])) {
            return $response['Session'];
        }
    }

    private function upsertLogin($cookie, $email, $projectId)
    {
        try {
            $cookie = Login::login($cookie, $email, $projectId);
            return $cookie;
        } catch (\Exception $e) {
            throw new \Exception('internal server error', 500);
        }
    }

    private function upsertLoginLog($cookie, $email)
    {
        try {
            LoginLog::login($cookie, $email);
        } catch (\Exception $e) {
            throw new \Exception('internal server error', 500);
        }
    }

    private function secretLoginCodeNotMatchResponse($newSession)
    {
        return response()->json([
            'error' => [
                'statusCode' => 400,
                'message' => 'secret login code is not correct',
                'session' => $newSession
            ]
        ], 400);
    }

    private function apiResponse($email, $oldCookie)
    {
        $statusCode = 200;
        return response()->json([
            'statusCode' => $statusCode,
            'email' => $email,
            'cookie' => $oldCookie
        ], $statusCode);
    }
}