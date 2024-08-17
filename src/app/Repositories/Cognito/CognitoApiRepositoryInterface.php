<?php
namespace App\Repositories\Cognito;

use App\Entities\Cognito\CognitoUserEntity;


interface CognitoApiRepositoryInterface
{
    public function signup(array $data);
    public function signin(array $data);
    public function confirmSignin(array $data);
    public function getUserInfo(string $email);
    public function getUserInfoByAccessToken(string $accessToken): CognitoUserEntity;
    public function getAccessToken(string $code, string $codeVerifier = null): CognitoUserEntity;
    public function getProviders(): array;
    public function getJwk(): string;
}