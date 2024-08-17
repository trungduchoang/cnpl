<?php
namespace App\Services\Cognito;

interface CheckUserExistenceServiceInterface
{
    public function checkUserExistence(string $type, string $attribute): array;
}