<?php
namespace App\Application\AuthApi\UseCase;

use App\Cognito\CognitoClient;
use App\Models\Login;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Models\SignupTemp;
use App\Services\CryptoQueryService;
use App\Services\CookieHandleService;
use App\Application\AuthApi\UseCase\SigninWithPhoneNumberUseCase;

class signupWithPhoneNumberUseCase
{
    protected $cognitoClient;
    protected $cryptoQueryService;
    protected $cookieHandleService;
    protected $signinWithPhoneNumberUseCase;

    public function __construct()
    {
        $this->cognitoClient = app()->make(CognitoClient::class);
        $this->cryptoQueryService = app()->make(CryptoQueryService::class);
        $this->cookieHandleService = app()->make(CookieHandleService::class);
        $this->signinWithPhoneNumberUseCase = app()->make(SigninWithPhoneNumberUseCase::class);
    }


    public function index($request)
    {
        try {
            list($phoneNumber, $projectId, $redirectUrl, $message) = $request->getParam($request);
            $cookie = $this->getCookie($request);
            $userName = $this->generateUserName('phone_', $cookie);
            $confirmed = $this->checkUserExistsByPhoneNumber($phoneNumber);
            if ($confirmed) {
                if (Login::isLogin($cookie, $projectId)) {
                    return $this->usernameExistsExceptionResponse(null ,$phoneNumber, 'User already exists', $confirmed);
                }
                return $this->signin($phoneNumber, $projectId, $redirectUrl, $message);
            }
            $password = $this->generatePassword();
            $email = $this->generateEmail();
            $uid = uniqid();
            $this->createTemp($uid, $userName, $redirectUrl, $projectId);
            $url = $this->generateUrl($this->encrypt($uid));
            if (strlen($message . $url) > 1530) {
                throw new \Exception('put message and redirecturl params in total 1530 byte or less', 400);
            }
            $message = $this->generateMessage($message, $url);
            $this->signupWithPhoneNumberApiRequest($userName, $email, $phoneNumber, $password, $url, $message);
        } catch (CognitoIdentityProviderException $e) {
                if ($e->getAwsErrorCode() === 'UsernameExistsException') {
                    $confirmed = $this->getUserStatus($userName);
                    return $this->usernameExistsExceptionResponse(null ,$phoneNumber, 'User already exists', $confirmed);
                }
                throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
                throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->signupWithPhoneNumberApiResponse($userName);
    }
    
    private function signupWithPhoneNumberApiRequest($userName, $email, $phoneNumber, $password, $url, $message)
    {
        $this->cognitoClient->signupWithPhoneNumber($userName, $email, $phoneNumber, $password, $url, $message);
    }

    private function getCookie($request)
    {
        $cookie = $this->cookieHandleService->getCookie($request);
        if ($cookie) {
            return $cookie;
        }
        throw new \Exception('cookie not found', 400);
    }

    private function getUserStatus($userName)
    {
        $userInfo = $this->cognitoClient->getUserInfoByUserName($userName);
        $status = $userInfo['Users'][0]['UserStatus'];
        return $status;
    }

    private function checkUserExistsByPhoneNumber($phoneNumber)
    {
        $userInfo = $this->cognitoClient->getUserInfoByPhoneNumber($phoneNumber);
        if (count($userInfo['Users']) >= 0) {
            foreach ($userInfo['Users'] as $user) {
                $userName = $user['Username'];
                if (explode('_', $userName)[0] == 'phone') {
                    $confirmed = $user['UserStatus'];
                    return $confirmed;
                } else {
                    continue;
                }
            }
        }
        return false;
    }

    private function generateUserName($prefix, $cookie)
    {
        return $prefix . $cookie;
    }

    private function generatePassword()
    {
        return sha1(uniqid(mt_rand(), true));
    }

    private function encrypt($query)
    {
        $encrypted = $this->cryptoQueryService->encryptQuery($query);
        $encrypted = strtr($encrypted, '+/', '-_');
        return $encrypted;
    }


    public function signin($phoneNumber, $projectId, $redirectUrl, $message)
    {
        try {
            list($userName, $secretLoginCode, $session) = $this->signinWithPhoneNumberUseCase->signinWithPhoneNumberApiRequest($phoneNumber);
            $uid = uniqid();
            $encryptedUid = $this->signinWithPhoneNumberUseCase->encrypt($uid);  
            $url = $this->signinWithPhoneNumberUseCase->generateUrl($encryptedUid);
            $this->signinWithPhoneNumberUseCase->createSession($uid, $userName, $projectId, $redirectUrl, $session, $secretLoginCode);
            $smsMessage = $this->signinWithPhoneNumberUseCase->generateSmsMessage($url, $message);
            $this->signinWithPhoneNumberUseCase->sendSmsMessage($phoneNumber, $smsMessage);
        } catch (CognitoIdentityProviderException $e) {
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->signupWithPhoneNumberApiResponse($userName);
    }


    private function generateEmail()
    {
        return 'dummy@smartplate.pro';
    }


    private function generateUrl($uid)
    {
        return request()->getSchemeAndHttpHost() . '/api/auth/signup/confirm/?uid=' . $uid;
    }


    private function generateMessage($message, $url)
    {
        $message = $message.'   '.$url;
        return $message;
    }

    private function createTemp($uid, $userName, $redirectUrl, $projectId)
    {
        SignupTemp::createTemp($uid, $userName, $redirectUrl, $projectId);
    }

    private function usernameExistsExceptionResponse($email, $phoneNumber, $message, $confirmed)
    {
        $statusCode = 403;
        $responseData = [
            'error' => [
                'statusCode' => $statusCode,
                'message' => $message,
                'confirmed' => $confirmed == 'CONFIRMED' ? true: false
            ]
        ];
        if ($email) $responseData['error']['email'] = $email;
        if ($phoneNumber) $responseData['error']['phoneNumber'] = $phoneNumber;
        return response()->json($responseData, $statusCode);
    }

    private function signupWithPhoneNumberApiResponse($userName)
    {
        $statusCode = 200;
        return response()->json([
            'statusCode' => $statusCode,
            'userName' => $userName,
            'confirmed' => false
        ], $statusCode);
    }
}