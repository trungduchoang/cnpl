<?php
namespace App\Repositories\Xid;


interface XidApiRepositoryInterface
{
    public function getToken(bool $env ,string $code, string $clientId, string $secret, string $redirectUrl): object;
    public function getUserInfo(bool $env, string $accessToken): object;
}