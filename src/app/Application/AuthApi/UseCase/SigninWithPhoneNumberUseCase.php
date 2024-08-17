<?php
namespace App\Application\AuthApi\UseCase;

use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Sms\SendSmsClient;
use App\Services\CryptoQueryService;
use App\Services\CookieHandleService;
use App\Models\SigninTemp;


class SigninWithPhoneNumberUseCase
{
        
    protected $cognitoClient;
    protected $sendSmsClinent;
    protected $cryptoQueryService;
    protected $cookieHandleService;


    public function __construct()
    {
        $this->cognitoClient = app()->make(CognitoClient::class);
        $this->sendSmsClinent = app()->make(SendSmsClient::class);
        $this->cryptoQueryService = app()->make(CryptoQueryService::class);
        $this->cookieHandleService = app()->make(CookieHandleService::class);
    }

    public function index($request)
    {
        try {
            list($phoneNumber, $projectId, $redirectUrl, $message) = $request->getParam($request);
            list($userName, $secretLoginCode, $session) = $this->signinWithPhoneNumberApiRequest($phoneNumber);
            $uid = uniqid();
            $query = $this->encrypt($uid);  
            $url = $this->generateUrl($query, $projectId);
            $this->createSession($uid, $userName, $projectId, $redirectUrl, $session, $secretLoginCode);
            $smsMessage = $this->generateSmsMessage($url, $message);
            $this->sendSmsMessage($phoneNumber, $smsMessage);
        } catch (CognitoIdentityProviderException $e) {
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->signinWithPhoneNumberApiResponse($userName);
    }

    public function signinWithPhoneNumberApiRequest($phoneNumber)
    {
        $response = $this->cognitoClient->signinWithPhoneNumber($phoneNumber);
        return [$response['ChallengeParameters']['USERNAME'], $response['ChallengeParameters']['code'], $response['Session']];
    }

    public function encrypt($query)
    {
        $encrypted = $this->cryptoQueryService->encryptQuery($query);
        $encrypted = strtr($encrypted, '+/', '-_');
        return $encrypted;
    }

    public function generateUrl($uid)
    {
        $url = request()->getSchemeAndHttpHost() . '/api/auth/signin/confirm/?uid=' . $uid;
        return $url;
    }

    public function createSession($uid, $userName, $projectId, $redirectUrl, $session, $secretLoginCode)
    {
        SigninTemp::createSession($uid, $userName, $projectId, $redirectUrl, $session, $secretLoginCode);
    }

    public function generateSmsMessage($url, $message)
    {
        if (strlen($message.'   '.$url) > 1530) {
            throw new \Exception('put message and redirecturl params in total 1530 byte or less', 400);
        }
        return $message.'   '.$url;;
    }

    public function sendSmsMessage($phoneNumber, $message)
    {
        $response = $this->sendSmsClinent->sendSmsMessage($phoneNumber, $message, 'SmartPlate');
        return $response;
    }

    private function signinWithPhoneNumberApiResponse($userName)
    {
        $statusCode = 200;
        return response()->json([
            'statusCode' => $statusCode,
            'userName' => $userName,
        ], $statusCode);
    }

}