<?php
namespace App\Repositories\Xid;


use Illuminate\Support\Facades\Http;
use App\Repositories\Xid\XidApiRepositoryInterface;


class XidApiRepository implements XidApiRepositoryInterface
{
    const BASE_URL_DEV = 'https://oidc-uat.x-id.io';
    const BASE_URL_PROD = 'https://oidc.x-id.io';

    public function getToken(bool $env ,string $code, string $clientId, string $secret, string $redirectUrl): object
    {
        try {
            $baseUrl = $env ? self::BASE_URL_PROD : self::BASE_URL_DEV;
            $response = Http::asForm()->withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ])->post($baseUrl . '/oauth2/token',[
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $secret,
                'redirect_uri'  => $redirectUrl,
                'grant_type'    => 'authorization_code',
            ]);
            return $response;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }


    public function getUserInfo(bool $env, string $accessToken): object
    {
        try {
            $baseUrl = $env ? self::BASE_URL_PROD : self::BASE_URL_DEV;
            $response = Http::asForm()->withHeaders([
                'Accept'         => 'application/json',
                'Authorization'  => 'Bearer ' . $accessToken,
            ])->get($baseUrl . '/userinfo');
            return $response;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}