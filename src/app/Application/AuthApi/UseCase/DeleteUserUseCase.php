<?php

namespace App\Application\AuthApi\UseCase;

use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Services\CookieHandleService;


class DeleteUserUseCase
{
    
    protected $cognitoClient;
    protected $cookieHandleService;

    public function __construct()
    {
        $this->cognitoClient = app()->make(CognitoClient::class);
        $this->cookieHandleService = app()->make(CookieHandleService::class);
    }

    public function index($request)
    {
        try {
            $userName = $request->getParam($request);
            $this->deleteUser($userName);
        } catch (CognitoIdentityProviderException $e) {
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->deleteUserApiResponse($userName);

    }

    public function deleteUser($userName)
    {
        $this->cognitoClient->deleteUser($userName);
    }

    private function deleteUserApiResponse($userName)
    {
        $statusCode = 200;
        return response()->json([
            'statusCode' => $statusCode,
            'userName' => $userName,
        ], $statusCode);
    }
}