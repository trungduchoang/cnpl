<?php
namespace App\Repositories\Temp;


interface TempRepositoryInterface 
{
    public function createTemp(string $uid, string $loginData): void;
    public function getTemp(string $uid): mixed;
    public function deleteTemp(string $uid): void;
}