<?php
namespace App\Repositories\Temp;

interface SignupTempRepositoryInterface
{
    public function createTemp(array $data): void;
    public function getTemp(string $uid);
    public function deleteTemp(string $uid): void;
}