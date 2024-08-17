<?php
namespace App\Repositories\Line;

use Illuminate\Support\Facades\Http;

class LineApiRepository implements LineApiRepositoryInterface
{
    const BASE_URL_V2 = 'https://api.line.me/v2';


    /**
     * verify channel access token function
     *
     * @param string $channelAccessToken
     * @return boolean
     */
    public function verifyChannelAccessToken(string $channelAccessToken): bool
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post(self::BASE_URL_V2 . '/oauth/verify', [
            'access_token' => $channelAccessToken
        ]);
        if ($response->failed()) {
            logger($response->json()['error_description']);
            return false;
        }
        return true;
    }

    /**
     * get channel access token function
     *
     * @param string $channelId
     * @param string $channelAccessToken
     * @return string
     */
    public function getChannelAccessToken(string $channelId, string $channelSecret): string
    {
        $response = Http::asForm()
        ->post(self::BASE_URL_V2 . '/oauth/accessToken', [
            'grant_type'    => 'client_credentials',
            'client_id'     => $channelId,
            'client_secret' => $channelSecret
        ]);
        if ($response->failed()) {
            logger($response->json()['error_description']);
            throw new \Exception($response->json()['error'], 400);
        }
        if ($response->ok()) $accessToken = $response->json()['access_token'];
        $accessToken = json_decode($response)->access_token;
        return $accessToken;
    }


    /**
     * send line message multi cast function
     *
     * @param array $userNames
     * @param array $message
     * @param string $channelAccessToken
     * @return void
     */
    public function sendMessageMulticast(array $userNames, array $messages, string $channelAccessToken): void
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $channelAccessToken
        ])->post(self::BASE_URL_V2 . '/bot/message/multicast', [
            'to'       => $userNames,
            'messages' => $messages
        ]);
        if ($response->failed()) {
            logger($response->json()['error_description']);
            throw new \Exception($response->json()['error'], 400);
        }
    }
}