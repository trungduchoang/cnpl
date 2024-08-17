<?php

namespace App\Cognito;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Log;


class CognitoClient
{
    protected $client;
    protected $clientId;
    protected $poolId;

    public function __construct(CognitoIdentityProviderClient $client, $clientId, $poolId)
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->poolId = $poolId;
    }

    public function signupWithEmail($userName, $email, $password, $redirectUrl, $callbackUrl)
    {
        $attributes['email'] = $email;

        try {
            $response = $this->client->signUp([
                'ClientId'       => $this->clientId,
                'Username'       => $userName,
                'Password'       => $password,
                'UserAttributes' => $this->formatAttributes($attributes),
                'ClientMetadata' => [
                    'callbackUrl' => $callbackUrl,
                    'redirectUrl' => $redirectUrl
                ]
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }

        return $response;
    }

    public function signupWithPhoneNumber($userName, $email, $phoneNumber, $password, $url, $message)
    {
        $attributes['phone_number'] = $phoneNumber;
        $attributes['email'] = $email;
        try {
            $response = $this->client->signUp([
                'ClientId'       => $this->clientId,
                'Password'       => $password,
                'UserAttributes' => $this->formatAttributes($attributes),
                'Username'       => $userName,
                'ClientMetadata' => [
                    'callbackUrl' => $url,
                    'message' => $message
                ]
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }

        return $response;
    }


    public function signupWithWebauthn($userName, $email, $password)
    {
        
        $attributes['email'] = $email;

        try {
            $response = $this->client->signUp([
                'ClientId'       => $this->clientId,
                'Password'       => $password,
                'UserAttributes' => $this->formatAttributes($attributes),
                'Username'       => $userName,
                'ClientMetadata' => [
                    'authType' => 'webauthn',
                ]
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }

        return $response;
    }

    public function signinWithEmail($userName, $password)
    {
        try {
            $response = $this->client->adminInitiateAuth([
                'AuthFlow'       => 'ADMIN_USER_PASSWORD_AUTH',
                'AuthParameters' => [
                    'USERNAME' => $userName,
                    'PASSWORD' => $password,
                ],
                'ClientId'       => $this->clientId,
                'UserPoolId'     => $this->poolId
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }

        return $response;
    }


    public function signinWithPhoneNumber($phoneNumber)
    {
        $userInfo = $this->getUserInfoByPhoneNumber($phoneNumber);
        if (!$userInfo['Users']) {
            throw new \Exception('user not found', 400);
        }
        $email = $userInfo['Users'][0]['Username'];
        try {
            $response = $this->client->adminInitiateAuth([
                'AuthFlow'       => 'CUSTOM_AUTH',
                'AuthParameters' => [
                    'USERNAME'      => $email,
                    'PHONENUMBER'   => $phoneNumber,
                ],
                'ClientId'       => $this->clientId,
                'UserPoolId'     => $this->poolId,
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }

        return $response;
    }


    public function signinWithWebAuthn($userName)
    {
        $userInfo = $this->getUserInfoByUserName($userName);
        if (!$userInfo['Users']) {
            throw new \Exception('user not found', 400);
        }
        try {
            $response = $this->client->adminInitiateAuth([
                'AuthFlow'       => 'CUSTOM_AUTH',
                'AuthParameters' => [
                    'USERNAME'      => $userName,
                ],
                'ClientId'       => $this->clientId,
                'UserPoolId'     => $this->poolId,
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }
        return $response;
    }

    public function confirm($userName, $confirmationCode)
    {
        try {
            $response = $this->client->ConfirmSignUp([
                'ClientId' => $this->clientId,
                'ConfirmationCode' => $confirmationCode,
                'Username' => $userName,
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }

        return $response;
    }


    public function generateAuthUrl($identityProvider, $projectId, $redirectUrl)
    {
        $query = [
            'response_type'     => 'code',
            'client_id'         => config('services.cognito.app_client_id'),
            'redirect_uri'      => config('app.url') .'/api/auth/callback',
            'identity_provider' => $identityProvider,
            'state'             => json_encode(array(
                'project_id'   => $projectId,
                'redirect_uri' => $redirectUrl,
            )),
        ];
        $queryString = http_build_query($query, '', '&');
        $baseUrl  = config('services.cognito.cognito_domain');
        $authPath = '/oauth2/authorize';
        $authUrl  = $baseUrl . $authPath . '?' . $queryString;
        return $authUrl;
    }

    public function getUserInfoByUserName($userName)
    {
        try {
            $response = $this->client->listUsers([
                'UserPoolId' => $this->poolId,
                'Filter'     => "username = \"" .$userName . "\"",
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }
        return $response;
    }

    public function getUserInfoByEmail($email)
    {
        try {
            $response = $this->client->listUsers([
                'UserPoolId' => $this->poolId,
                'Filter'     => "email = \"" .$email . "\"",
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }
        return $response;
    }

    public function getUserInfoByPhoneNumber($phoneNumber)
    {
        try {
            $response = $this->client->listUsers([
                'UserPoolId' => $this->poolId,
                'Filter'     => "phone_number = \"" .$phoneNumber . "\""
            ]);

        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }
        return $response;
    }

    public function getUserInfoByAccessToken($accessToken)
    {
        $baseUrl = config('services.cognito.cognito_domain');
        $endpoint = '/oauth2/userInfo';
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('GET', $baseUrl . $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/x-www-form-urlencoded'
                ]
            ])->wait();
        } catch (\Exception $e) {
            throw $e;
        }
        $response = json_decode((string) $response->getBody());
        return $response;
    }

    public function getAccessToken($code){
        $baseUrl = config('services.cognito.cognito_domain');
        $endpoint = '/oauth2/token';
        $postData = [
            'grant_type'   => 'authorization_code',
            'redirect_uri' => config('app.url') .'/api/auth/callback',
            'client_id'    => $this->clientId,
            'code'         => $code,
        ];
        
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('POST' , $baseUrl . $endpoint, [
                'headers'     => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'form_params' => $postData
            ])->wait();
        } catch (\Exception $e) {
            logger($e);
            throw $e;
        }

        $accessToken = json_decode((string) $response->getBody())->access_token;
        return $accessToken;

    }

    public function secretLoginCode($email, $secretLoginCode, $session)
    {
        try {
            $response = $this->client->AdminRespondToAuthChallenge([
                'ChallengeName'      => 'CUSTOM_CHALLENGE',
                'ChallengeResponses' => [
                    'USERNAME' => $email,
                    'ANSWER'   => $secretLoginCode
                ],
                'ClientId'           => $this->clientId,
                'UserPoolId'         => $this->poolId,
                'Session'            => $session,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }

        return $response;
    }

    public function deleteUser($userName)
    {
        try {
            $response = $this->client->adminDeleteUser([
                'UserPoolId' => $this->poolId,
                'Username'   => $userName
            ]);
        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw $e;
        }
        return $response;
    }

    protected function formatAttributes(array $attributes)
    {
        $userAttributes = [];
        foreach ($attributes as $key => $value) {
            $userAttributes[] = [
                'Name'  => $key,
                'Value' => $value,
            ];
        }
        return $userAttributes;
    }
}