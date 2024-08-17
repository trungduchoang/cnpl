<?php
namespace App\Entities\Cognito;

interface CognitoUserEntityInterface
{
    public function getUserName(): string;
    public function getSub(): string;
    public function getEmail(): string;
    public function getPhoneNumber(): string;
    public function getUserStaus(): bool;
    public function getAccessToken(): string;
    public function getIdToken(): string;
}