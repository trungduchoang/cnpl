<?php

namespace App\Application\AuthApi\UseCase;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Services\CookieHandleService;
use App\Services\Base64UrlService;
use App\CBOR\CBOREncoder;
use App\Models\AttestationOpstions;
use App\Models\Credentials;
use App\Models\Login;
use App\Models\LoginLog;
use Base64Url\Base64Url;
use CBOR\CBORObject;
use Jose\Component\Core\JWK;
use Jose\Component\Core\Util\ECKey;
use Jose\Component\Core\Util\RSAKey;


class SignupAssertionResultUseCase
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

    /**
     * integrate assertion result function
     *
     * @param object $request
     * @return array
     */
    public function index(object $request): array
    {
        try {
            DB::beginTransaction();
            list($id, $clientDataJson, $attestationObject, $projectId, $cognito) = $request->getParam($request);
            
            $clientDataJson = $this->decodeClientDataJson($clientDataJson);

           // $attestationObject = $this->decodeAttestationObjectDecode($attestationObject);

            $cookie = "";
           // $cookie = 'hodongnhut';

            $userName = $cognito ? Str::random(10) : '';
            // $pem = $this->getPem($attestationObject['authData']);
            $pem = (string) Str::uuid();
            $options = $this->getAttestationOptions($clientDataJson['challenge']);
            $this->validateClientDataJson($clientDataJson, $options);
            
            $this->createCredentials([
                'cookie'         => $cookie,
                'oauth_username' => $userName,
                'credential_id'  => $id,
                'pem'            => $pem,
                'project_id'     => $projectId    
            ]);
            // if ($cognito) {
            //     $email = $this->generateEmail();
            //     $password = $this->generatePassword();
            //    // $this->signupWithWebauthnApiRequest($userName, $email, $password);
            // }
            $this->upsertLogin($cookie, $userName, $projectId);
           // $this->createLoginLog($cookie, $userName);
            DB::commit();
        } catch (CognitoIdentityProviderException $e) {
            DB::rollback();
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return ['id' => $id];
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
     * decode clientDataJson function
     *
     * @param string $clientDataJson
     * @return object
     */
    private function decodeClientDataJson(string $clientDataJson): array
    {
        $clientDataJson = $this->base64UrlDecode($clientDataJson);
        return json_decode($clientDataJson, true);
    }

    /**
     * decode attestationObject function
     *
     * @param string $attestationObject
     * @return array
     */
    private function decodeAttestationObjectDecode(string $attestationObject): mixed
    {
        $attestationObject = $this->base64UrlDecode($attestationObject);
        $attestationObject = json_decode($attestationObject, true);
        // $attestationObject = $this->cborDecode($attestationObject);
        // $attestationObject['attStmt']['sig'] = $this->cborDecode($attestationObject['attStmt']['sig']->get_byte_string());
        // $attestationObject['authData'] = array_values(unpack('C*', $attestationObject['authData']->get_byte_string()));
        $attestationObject['attStmt']['sig'] = $attestationObject['attStmt']['sig'];
        $attestationObject['authData'] = $attestationObject['authData'];
        return $attestationObject;
    }


    /**
     * get pem function
     *
     * @param array $authData
     * @return string
     */
    private function getPem(array $authData): string
    {
        $credentialIdLength = array_slice($authData, 53, 2);
        $credentialIdLength = ($authData[53] << 8) + $authData[54];
        $credentialPublicKey = array_slice($authData, 55 + $credentialIdLength);
        $jwkArray = $this->getJwkArray($credentialPublicKey);
        $jwk = new JWK($jwkArray);
        if ($jwk->get('kty') === 'EC') {
            $ECkey = new ECKey();
            return $ECkey->convertPublicKeyToPEM($jwk);
        } else {
            $RSAKey = new RSAKey($jwk);
            return $RSAKey->toPEM();
        }
    }

    /**
     * get jwk array function
     *
     * @param array $credentialPublicKey
     * @return array
     */
    private function getJwkArray(array $credentialPublicKey): array
    {
        $publicKeyJwk = [];
        $credentialPublicKeyBin = $this->byteArrayToString($credentialPublicKey);
        $publicKeyCbor = CBOREncoder::decode($credentialPublicKeyBin);
        $publicKeyCbor[-2] = $publicKeyCbor[-2]->get_byte_string();
        $publicKeyCbor[-3] = $publicKeyCbor[-3]->get_byte_string();
        if ($publicKeyCbor[3] === -7) {
            $publicKeyJwk = [
                'kty' => 'EC',
                'crv' => 'P-256',
                'x'   => Base64Url::encode($publicKeyCbor[-2]),
                'y'   => Base64Url::encode($publicKeyCbor[-3])
            ];
        } elseif ($publicKeyCbor[3] === -257) {
            $publicKeyJwk = [
                'kty' => 'RSA',
                'n'   => Base64Url::encode($publicKeyCbor[-1]),
                'e'   => Base64Url::encode($publicKeyCbor[-2])
            ];
        }
        return $publicKeyJwk;
    }

    /**
     * bytearray to hex function
     *
     * @param array $byteArray
     * @return void
     */
    function byteArrayToHex(array $byteArray): string
    {
        $chars = array_map('chr', $byteArray);
        $bin = join($chars);
        return bin2hex($bin);
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
     * CBOR deode function
     *
     * @param string $data
     * @return string
     */
    private function cborDecode(string $data): mixed
    {
        return CBOREncoder::decode($data);
    }

    /**
     * base64 Url Decode function
     *
     * @param string $data
     * @return string
     */
    private function base64UrlDecode(string $data): string
    {
        return Base64UrlService::decode($data);
    }


    /**
     * generate user name function
     *
     * @param string $prefix
     * @param string $cookie
     * @return string
     */
    private function generateUserName(string $prefix, string $cookie): string
    {
        return $prefix . '_' . $cookie;
    }


    /**
     * generate email function
     *
     * @return string
     */
    private function generateEmail(): string
    {
        return 'dummy@smartplate.pro';
    }


    /**
     * generate password function
     *
     * @return string
     */
    private function generatePassword(): string
    {
        return sha1(uniqid(mt_rand(), true));
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
     * validate client dataJson function
     *
     * @param array $clientDataJson
     * @param array $options
     * @return boolean
     */
    private function validateClientDataJson(array $clientDataJson, array $options): bool
    {
        if ($clientDataJson['type'] !== 'webauthn.create') {
            throw new \Exception('credential type is not valid', 400);
        }
        if ($clientDataJson['challenge'] !== $options['challenge']) {
            throw new \Exception('challenge is not valid', 400);
        }
        if ($clientDataJson['origin'] !== $options['origin']) {
            throw new \Exception('origin is not valid', 400);
        }
        return true;
    }

    /**
     * create credential data function
     *
     * @param array $data
     * @return void
     */
    private function createCredentials(array $data)
    {
        try {
            $credentials = Credentials::getCredentials($data['cookie'], $data['project_id']);
            if (empty($credentials)) {
                Credentials::createCredentials($data);
            }
            return $credentials;
           
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    /**
     * signup with webauthn api request function
     *
     * @param string $userName
     * @param string $email
     * @param string $password
     * @return void
     */
    private function signupWithWebauthnApiRequest(string $userName, string $email, string $password): void
    {
        $this->cognitoClient->signupWithWebauthn($userName, $email, $password);
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
        Login::login($cookie, $userName, $projectId);
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
        LoginLog::login($cookie, $userName);
    }

}