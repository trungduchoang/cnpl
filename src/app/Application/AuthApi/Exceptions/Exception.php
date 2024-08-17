<?php

namespace App\Application\AuthApi\Exceptions;


class Exceptions
{
    public function generateExeptionData($error)
    {
        $massage =  $this->getMessage($error);
        $statusCode = $this->getCode($error);
        $data = [
            'error' => [
                'statusCode' => $statusCode,
                'massage' => $massage
            ]
        ];
        return $data;
    }

    public function generateAwsExceptionData($error)
    {
        $statusCode = $this->awsGetMessage($error);
        $massage = $this->awsGetCode($error);
        
    }

    private function getMessage($error)
    {
        return $error->getMessage();
    }

    private function getCode($error)
    {
        return $error->getCode();
    }

    private function awsGetMessage($error)
    {
        return $error->getStatusCode();
    }

    private function awsGetCode($error)
    {
        return $error->getAwsErrorMessage();
    }

    
}