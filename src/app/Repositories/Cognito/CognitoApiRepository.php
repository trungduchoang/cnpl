<?php
namespace App\Repositories\Cognito;

use App\Entities\Cognito\CognitoUserEntity;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Nette\Utils\Json;

class CognitoApiRepository implements CognitoApiRepositoryInterface
{

    private $cognitoClient;
    private $clientId;
    private $poolId;
    private $domain;

    public function __construct(CognitoIdentityProviderClient $cognitoClient, $clientId, $poolId, $domain)
    {
        $this->cognitoClient = $cognitoClient;
        $this->clientId = $clientId;
        $this->poolId = $poolId;
        $this->domain = $domain;
    }

    public function signup(array $data)
    {
        try {
            $data['ClientId'] = $this->clientId;
            return $this->cognitoClient->signUp($data);
        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        }
    }

    public function signin(array $data)
    {
        try {
            $data['ClientId'] = $this->clientId;
            $data['UserPoolId'] = $this->poolId;
            return $this->cognitoClient->adminInitiateAuth($data);
        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        }
    }



    public function confirmSignin(array $data)
    {
        try {
            $data['ClientId'] = $this->clientId;
            $data['UserPoolId'] = $this->poolId;
            return $this->cognitoClient->AdminRespondToAuthChallenge($data);
        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        }
    }

    public function getUserInfo(string $filter)
    {
        try {
            return $this->cognitoClient->listUsers([
                'UserPoolId' => $this->poolId,
                'Filter'     => $filter,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        }
    }

    /**
     * get user info by access token function
     *
     * @param string $accessToken
     * @return CognitoUserEntity
     */
    public function getUserInfoByAccessToken(string $accessToken): CognitoUserEntity
    {
        $endpoint = '/oauth2/userInfo';
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('GET', $this->domain . $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/x-www-form-urlencoded'
                ]
            ])->wait()->getBody();
            $response = json_decode((string) $response);
        } catch (\Exception $e) {
            throw $e;
        }
        return new CognitoUserEntity(
            $response->username,
            $response->sub,
            property_exists($response, 'email') ? $response->email : '',
            property_exists($response, 'phone_number') ? $response->phone_number : '',
            true,
            $accessToken
        );
    }

    



    public function getAccessToken(string $code, string $codeVerifier = null): CognitoUserEntity
    {
        try {
            $endpoint = '/oauth2/token';
            $postData = [
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => config('app.url') .'/api/auth/callback',
                'client_id'     => $this->clientId,
                'code'          => $code,
            ];
            if ($codeVerifier) $postData['code_verifier'] = $codeVerifier;
            
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('POST' , $this->domain . $endpoint, [
                'headers'     => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'form_params' => $postData
            ])->wait()->getBody();
            $response = json_decode((string) $response);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return new CognitoUserEntity(
            '',
            '',
            '',
            '',
            false,
            $response->access_token,
            $response->id_token
        );
    }


    /**
     * get providers function
     *
     * @return array
     */
    public function getProviders(): array
    {
        try {
            $response = $this->cognitoClient->listIdentityProviders([
                'UserPoolId' => $this->poolId,
            ]);
            $providers = [];
            foreach ($response['Providers'] as $key => $provider) {
                $providers[$key] = $provider['ProviderName'];
            }
        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        }
        return $providers;
    }


    /**
     * get jwk function
     *
     * @return string
     */
    public function getJwk(): string
    {
        try {
            $endpoint = 'https://cognito-idp.'. config('services.ses.region') . '.amazonaws.com/' . $this->poolId . '/.well-known/jwks.json';
            $client = new \GuzzleHttp\Client();
            $response = $client->requestAsync('GET', $endpoint)->wait()->getBody();
            $response = json_decode((string) $response);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return json_encode($response);
    }
}