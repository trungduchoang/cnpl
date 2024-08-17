<?php
namespace App\Application\AuthApi\UseCase;

use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Application\AuthApi\Request\SigninWithEmailRequest;
use App\Application\AuthApi\Request\SigninWithPhoneNumberRequest;
use App\Sms\SendSmsClient;
use App\Services\CryptoQueryService;
use App\Services\CookieHandleService;
use App\Models\Login;
use App\Models\LoginLog;
use App\Models\SigninTemp;


class SigninUseCase
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

    public function signinWithEmail($request)
    {
        try {
            list($email, $password, $projectId) = app()->make(SigninWithEmailRequest::class)->getParam($request);
            $cookie = $this->getCookie($request);
            $userName = $this->getUserNameByEmail($email);
            $response = $this->signinWithEmailApiRequest($userName, $password);
            $oldCookie = $this->upsertLogin($cookie, $userName, $projectId);
            $cookie = $oldCookie ? $oldCookie : $cookie;
            $this->upsertLoginLog($cookie, $userName);
        } catch (CognitoIdentityProviderException $e) {
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->signinWithEmailApiResponse($email, $oldCookie);
    }

    public function signinWithPhoneNumber($request)
    {
        try {
            list($phoneNumber, $projectId, $redirectUrl) = $request->getParam($request);
            list($userName, $secretLoginCode, $session) = $this->signinWithPhoneNumberApiRequest($phoneNumber);
            $query = $this->encrypt(['userName' => $userName, 'secretLoginCode' => $secretLoginCode]);  
            $url = $this->generateUrl($query, $projectId);
            $this->createSession($userName,  $redirectUrl, $session);
            $smsMessage = $this->generateSmsMessage($url);
            $this->sendSmsMessage($phoneNumber, $smsMessage);
        } catch (CognitoIdentityProviderException $e) {
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->signinWithPhoneNumberApiResponse($userName);
    }

    private function signinWithEmailApiRequest($email, $password)
    {
        return $this->cognitoClient->signinWithEmail($email, $password);
    }

    private function signinWithPhoneNumberApiRequest($phoneNumber)
    {
        $response = $this->cognitoClient->signinWithPhoneNumber($phoneNumber);
        return [$response['ChallengeParameters']['USERNAME'], $response['ChallengeParameters']['code'], $response['Session']];
    }

    private function getCookie($request)
    {
        $cookie = $this->cookieHandleService->getCookie($request);
        if ($cookie) {
            return $cookie;
        }
        throw new \Exception('cookie not found', 400);
    }

    private function getUserNameByEmail($email)
    {
        $userInfo = $this->cognitoClient->getUserInfoByEmail($email);
        foreach ($userInfo['Users'] as $value) {
            list($userName, $provider) = [$value['Username'] ,explode('_', $value['Username'])[0]];
            if ($value['UserStatus'] === 'CONFIRMED' && $provider === 'email') {
                return $userName;
            } else if ($value['UserStatus'] === 'UNCONFIRMED' && $provider === 'email') {
                throw new \Exception('user is not confirmed', 400);
            }
        }
        throw new \Exception('user not found', 400);
    }

    private function generateUrl($query, $projectId)
    {
        $url = request()->getSchemeAndHttpHost() . '/api/auth/signin/confirm/?query=' . $query . '&projectId=' . $projectId;
        return $url;
    }

    private function encrypt($query)
    {
        $query = json_encode($query);
        $encrypted = $this->cryptoQueryService->encryptQuery($query);
        $encrypted = urlencode($encrypted);
        return $encrypted;
    }

    private function generateSmsMessage($url)
    {
        return 'Tap this URL:' . $url;
    }

    private function sendSmsMessage($phoneNumber, $message)
    {
        $response = $this->sendSmsClinent->sendSmsMessage($phoneNumber, $message, 'SmartPlate');
        return $response;
    }

    private function upsertLogin($cookie, $userName, $projectId)
    {
        try {
            $cookie = Login::login($cookie, $userName, $projectId);
            return $cookie;
        } catch (\Exception $e) {
            throw new \Exception('internal server error', 500);
        }
    }

    private function upsertLoginLog($cookie, $userName)
    {
        try {
            LoginLog::login($cookie, $userName);
        } catch (\Exception $e) {
            throw new \Exception('internal server error', 500);
        }
    }

    public function createSession($userName,  $redirectUrl, $session)
    {
        SigninTemp::createSession($userName,  $redirectUrl, $session);
    }


    private function signinWithEmailApiResponse($email, $oldCookie = null)
    {
        $statusCode = 200;
        return response()->json([
            'statusCode' => $statusCode,
            'email' => $email,
            'cookie' => $oldCookie
        ], $statusCode);
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