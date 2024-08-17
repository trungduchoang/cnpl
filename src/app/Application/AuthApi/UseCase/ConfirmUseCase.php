<?php

namespace App\Application\AuthApi\UseCase;


use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Support\Facades\DB;
use App\Models\SignupTemp;
use App\Models\Login;
use App\Models\LoginLog;
use App\Services\CookieHandleService;


class ConfirmUseCase
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
            list($uid, $confirmationCode) = $request->getParam($request);
            $data = $this->getTemp($uid);
            if (!$data) {
                return null;
            }
            $cookie = $this->getCookie($data['oauth_name']);
            $this->confirmApiRequest($data['oauth_name'], $confirmationCode);
            $oldCookie = $this->upsertLogin($cookie, $data['oauth_name'], $data['project_id']);
            $cookie = $oldCookie ? $oldCookie : $cookie;
            $this->setCookie($cookie);
            $this->upsertLoginLog($cookie, $data['oauth_name']);
            $this->deleteTemp($uid);
            DB::commit();
        } catch (CognitoIdentityProviderException $e) {
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
            DB::rollback();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
            DB::rollback();
        }
        return $data['redirect_url'];
    }

    private function getCookie($userName)
    {
        return explode('_', $userName)[1];
    }

    private function setCookie($cookie) {
        $this->cookieHandleService->setCookie($cookie);
    }

    private function confirmApiRequest($userName, $confirmationCode)
    {
        app()->make(CognitoClient::class)->confirm($userName, $confirmationCode);
    }

    private function getTemp($uid)
    {
        $data = SignupTemp::getTemp($uid);
        return $data;
    }

    private function deleteTemp($userName)
    {
        SignupTemp::deleteTemp($userName);
    }

    private function upsertLogin($cookie, $userName, $projectId)
    {
        try {
            Login::login($cookie, $userName, $projectId);
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


    private function apiResponse($userName, $oldCookie)
    {
        $statusCode = 200;
        return response()->json([
            'statusCode' => $statusCode,
            'userName' => $userName,
            'cookie' => $oldCookie
        ], $statusCode);
    }
}