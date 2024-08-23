<?php

namespace App\Application\AuthApi\UseCase;

use Illuminate\Support\Facades\DB;
// use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Services\CookieHandleService;
use App\Services\Base64UrlService;
use App\Models\AttestationOpstions;
use App\Models\Credentials;
use App\Models\Login;
use App\Models\LoginLog;
use Illuminate\Support\Arr;

class SigninAssertionResultUseCase
{
    protected $cookieHandleService;
    protected $base64UrlService;
    protected $cognitoClient;


    public function __construct()
    {
        $this->cookieHandleService = app()->make(CookieHandleService::class);
        $this->base64UrlService = app()->make(Base64UrlService::class);
        // $this->cognitoClient = app()->make(CognitoClient::class);
    }



    public function index($request)
    {
        try {
            DB::beginTransaction();
            list($credentialId, $clientDataJson, $authenticatorData, $signature, $projectId, $cognito) = $request->getParam($request);
            $cookie = $this->getCookie($request);
            $clientDataHash = $this->clientDataJsonToHash($clientDataJson);
            // $clientDataJson:
            // array:5 [
            //     "type" => "webauthn.get"
            //     "challenge" => "uwKobZx1iF2rdb18QCjN57e-lfk"
            //     "origin" => "https://localhost"
            //     "crossOrigin" => false
            //     "other_keys_can_be_added_here" => "do not compare clientDataJSON against a template. See https://goo.gl/yabPex"
            //   ]
            $clientDataJson = $this->decodeClientDataJson($clientDataJson);
            $base64Challenge = $this->decodeHexChallenge($clientDataJson['challenge']);
            $options = $this->getAttestationOptions($base64Challenge);
            $this->validateClientDataJson($clientDataJson, $options, $base64Challenge);
            $confirmSig = $this->getConfirmSig($authenticatorData, $clientDataHash);
            $signature = $this->byteArrayToString($signature);
            $pem = $this->getPem($cookie, $projectId);
            $verify = openssl_verify($confirmSig, $signature, $pem, 'sha256');
            $userName = '';
            if (!$verify) {
                throw new \Exception('signature is not valid', 400);
            }
            if ($cognito) {
                list($userName, $code, $session) = $this->signinWithWebAuthnApiRequest('webauthn_' . $cookie);
                $this->secretLoginCodeApiRequest($userName, $code, $session);
            }
            $this->upsertLogin($cookie, $userName, $projectId);
            $this->createLoginLog($cookie, $userName);
            $this->deleteAttestationOptions($clientDataJson['challenge']);
            DB::commit();
        } catch (CognitoIdentityProviderException $e) {
            DB::rollback();
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return ['id' => $credentialId];
    }


    /**
     * get cookie function
     *
     * @param object $request
     * @return string
     */
    private function getCookie(object $request): string
    {
        $cookie = $this->cookieHandleService->getCookie($request);
        if ($cookie) {
            return $cookie;
        }
        throw new \Exception('cookie not found', 400);
    }

    /**
     * make clientData hash function
     *
     * @param array $clientDataJson
     * @return string
     */
    private function clientDataJsonToHash(array $clientDataJson): string
    {
        $clientDataString = $this->byteArrayToString($clientDataJson);
        return hash('sha256', $clientDataString);
    }


    /**
     * get confirm sig function
     *
     * @param array $authenticatorData
     * @param string $clientDataHash
     * @return string
     */
    private function getConfirmSig(array $authenticatorData, string $clientDataHash): string
    {
        $confirmSig = array_merge($authenticatorData, $this->hexToByteArray($clientDataHash));
        return $this->byteArrayToString($confirmSig);
    }

    /**
     * bytearray to string function
     *
     * @param array $byteArray
     * @return string
     */
    function byteArrayToString(array $byteArray): string
    {
        $chars = array_map('chr', $byteArray);
        return join($chars);
    }


    /**
     * hex to string function
     *
     * @param string $hexString
     * @return array
     */
    function hexToByteArray(string $hexString): array
    {
        $string = hex2bin($hexString);
        return unpack('C*', $string);
    }


    /**
     * get attestation options function
     *
     * @param string $challenge
     * @return array
     */
    private function getAttestationOptions(string $challenge): array
    {
        try {
            $data = AttestationOpstions::getAttestationOpsions($challenge);
            return ['challenge' => $data->challenge, 'id' => $data->id, 'origin' => $data->origin];
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    /**
     * convert challenge to base64
     *
     * @param string $challenge
     * @return string
     */
    private function decodeHexChallenge(string $challenge): string
    {
        try {
            $binaryString = base64_decode(strtr($challenge, '-_', '+/'));
            $base64Challenge = '';
            for ($i = 0; $i < strlen($binaryString); $i++) {
                $hexByte = dechex(ord($binaryString[$i]));
                if (strlen($hexByte) === 1) {
                    $hexByte = '0' . $hexByte;
                }
                $base64Challenge .= $hexByte;
            }
            return $base64Challenge;
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('Convert challenge to hex error', 500);
        }
    }

    /**
     * validate client dataJson function
     *
     * @param array $clientDataJson
     * @param array $options
     * @return boolean
     */
    private function validateClientDataJson(array $clientDataJson, array $options, string $base64Challenge): bool
    {
        if ($clientDataJson['type'] !== 'webauthn.get') {
            throw new \Exception('credential type is not valid', 400);
        }
        if ($base64Challenge !== $options['challenge']) {
            throw new \Exception('challenge is not valid', 400);
        }
        if ($clientDataJson['origin'] !== $options['origin']) {
            throw new \Exception('origin is not valid', 400);
        }
        return true;
    }



    /**
     * decode clientDataJson function
     *
     * @param array $clientDataJson
     * @return array
     */
    private function decodeClientDataJson(array $clientDataJson): array
    {
        $clientDataJson = implode(array_map('chr', $clientDataJson));
        return json_decode($clientDataJson, true);
    }

    /**
     * get pem from credentials function
     *
     * @param string $cookie
     * @return string
     */
    private function getPem(string $cookie, string $projectId): string
    {
        $creredentials = Credentials::getCredentials($cookie, $projectId);
        return $creredentials->pem;
    }


    /**
     * signin with webauthn api request function
     *
     * @param string $userName
     * @return array
     */
    private function signinWithWebAuthnApiRequest(string $userName): array
    {
        $response = $this->cognitoClient->signinWithWebAuthn($userName);
        return [$response['ChallengeParameters']['USERNAME'], $response['ChallengeParameters']['code'], $response['Session']];
    }


    /**
     * secret login code api request function
     *
     * @param string $userName
     * @param string $code
     * @param string $session
     * @return void
     */
    private function secretLoginCodeApiRequest(string $userName, string $code, string $session): void
    {
        $response = $this->cognitoClient->secretLoginCode($userName, $code, $session);
        if ($response['AuthenticationResult']['AccessToken']) {
            return;
        }
        throw new \Exception('authentfication is faild', 400);
    }


    /**
     * create login function
     *
     * @param string $cookie
     * @param string $userName
     * @param integer $projectId
     * @return void
     */
    private function upsertLogin(string $cookie, string $userName, int $projectId): void
    {
        try {
            Login::login($cookie, $userName, $projectId);
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }

    /**
     * create login log function
     *
     * @param string $cookie
     * @param string $userName
     * @return void
     */
    private function createLoginLog(string $cookie, string $userName): void
    {
        try {
            LoginLog::login($cookie, $userName);
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }

    private function deleteAttestationOptions(string $challenge): void
    {
        try {
            AttestationOpstions::deleteAttestationOpsions($challenge);
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }

}