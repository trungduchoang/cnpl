<?php
namespace App\Application\AuthApi\UseCase;

use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Models\SignupTemp;
use App\Services\Base64UrlService;
use App\Services\CryptoQueryService;
use App\Services\CookieHandleService;
use Ramsey\Uuid\Guid\Guid;
use Symfony\Component\Mime\Encoder\Base64Encoder;

Class SignupWithEmailUseCase
{
    protected $cognitoClient;
    protected $cryptoQueryService;
    protected $cookieHandleService;

    public function __construct()
    {
        $this->cognitoClient = app()->make(CognitoClient::class);
        $this->cryptoQueryService = app()->make(CryptoQueryService::class);
        $this->cookieHandleService = app()->make(CookieHandleService::class);
    }

    public function index(array $data)
    {
        try {
            list ($email, $password, $redirectUrl, $projectId, $cookie) = $data;
            $confirmed = $this->checkUserExistsByEmail($email);
            if ($confirmed) {
                return $this->usernameExistsExceptionResponse($email, null, 'User already exists', $confirmed);
            }
            if (!$cookie) throw new \Exception('cookie not found', 400);
            $userName = $this->generateUserName('email_', $cookie);
            $query = $this->encrypt(json_encode(['userName' => $userName, 'redirectUrl' => $redirectUrl, 'projectId' => $projectId]));
            $callbackUrl = $this->generateUrl($query, 'email');
            $this->signupWithEmailApiRequest($userName, $email, $password, $redirectUrl, $callbackUrl);
            $this->setCookie($cookie);
        } catch (CognitoIdentityProviderException $e) {
                if ($e->getAwsErrorCode() === 'UsernameExistsException') {
                    $confirmed = $this->getUserStatus($userName);
                    return $this->usernameExistsExceptionResponse($email, null, $e->getAwsErrorMessage(), $confirmed);
                }
                throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
                throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->signupWithEmailApiResponse($email);
    }

    private function signupWithEmailApiRequest($userName, $email, $password, $redirectUrl, $callbackUrl)
    {
        $this->cognitoClient->signupWithEmail($userName, $email, $password, $redirectUrl, $callbackUrl);
    }


    private function setCookie($cookie) 
    {
        $this->cookieHandleService->setCookie($cookie);
    }

    private function getUserStatus($userName)
    {
        $userInfo = $this->cognitoClient->getUserInfoByUserName($userName);
        $status = $userInfo['Users'][0]['UserStatus'];
        return $status;
    }

    private function checkUserExistsByEmail($email)
    {
        $userInfo = $this->cognitoClient->getUserInfoByEmail($email);
        if (count($userInfo['Users']) >= 0) {
            foreach ($userInfo['Users'] as $user) {
                $userName = $user['Username'];
                if (explode('_', $userName)[0] == 'email') {
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

    private function encrypt($query)
    {
        $encrypted = $this->cryptoQueryService->encryptQuery($query);
        $encrypted = strtr($encrypted, '+/', '-_');
        return $encrypted;
    }

    private function generateUrl($query, $type)
    {
        if ($type == 'phone') {
            $url = request()->getSchemeAndHttpHost() . '/api/auth/signup/confirm/?query=' . $query;
        } else {
            $url = request()->getSchemeAndHttpHost() . '/api/auth/verify-email/?query=' . $query;
        }
        return $url;
    }

    private function signupWithEmailApiResponse($email)
    {
        $statusCode = 200;
        return [
            'statusCode' => $statusCode,
            'email' => $email,
            'confirmed' => false
        ];
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
        return $responseData;
    }
}
