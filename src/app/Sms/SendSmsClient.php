<?php

namespace App\Sms;

use Aws\Sns\SnsClient;
use Aws\Sns\Exception\SnsException;


class SendSmsClient
{
    protected $snsClient;

    public function __construct()
    {
        $this->snsClient = new SnsClient([
            'version' => '2010-03-31',
            'region' => 'ap-northeast-1',
        ]);
    }


    public function sendSmsMessage($phoneNumber, $message, $subject)
    {
        try {
            $response = $this->snsClient->publish([
                'PhoneNumber'       => $phoneNumber, 
                'Message'           => $message,
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType'    => 'String',
                        'StringValue' => 'Transactional'
                    ],
                    'AWS.SNS.SMS.SenderID' => [
                        'DataType'    => 'String',
                        'StringValue' => $subject
                    ]
                ]
            ]);
        } catch (SnsException $e) {
            throw $e;
        }
        return $response;
    }
}