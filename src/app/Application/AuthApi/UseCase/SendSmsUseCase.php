<?php

namespace App\Application\AuthApi\UseCase;


use App\Sms\SendSmsClient;
use Aws\Sns\Exception\SnsException;
use App\Cognito\CognitoClient;


class SendSmsUseCase
{
    protected $sendSmsClient;
    protected $cognitoClient;

    public function __construct()
    {
        $this->sendSmsClient = app()->make(SendSmsClient::class);
        $this->cognitoClient = app()->make(CognitoClient::class);
    }

    public function index($request)
    {
        try {
            list($userName, $phoneNumber, $message, $subject) = $request->getParam($request);
            if (!$phoneNumber) {
                $phoneNumber = $this->getPhoneNumberByUserName($userName);
            }
            $response = $this->sendSmsApiRequest($phoneNumber, $message, $subject);
        } catch (SnsException $e) {
            throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->sendSmsApiResponse($phoneNumber, $message, $subject);
    }

    private function sendSmsApiRequest($phoneNumber, $message, $subject)
    {
        return $this->sendSmsClient->sendSmsMessage($phoneNumber, $message, $subject);
    }

    private function getPhoneNumberByUserName($userName)
    {
        $userInfo = $this->cognitoClient->getUserInfoByUserName($userName);
        $phoneNumber = '';
        foreach ($userInfo['Users'][0]['Attributes'] as $value) {
            if ($value['Name'] == 'phone_number') {
                $phoneNumber = $value['Value'];
            }
        }
        return $phoneNumber;
    }

    private function sendSmsApiResponse($phoneNumber, $message, $subject)
    {
        $statusCode = 200;
        return response()->json([
            'statusCode'  => $statusCode,
            'phoneNumber' => $phoneNumber,
            'message'     => $message,
            'subject'     => $subject
        ], $statusCode);
    }
}
