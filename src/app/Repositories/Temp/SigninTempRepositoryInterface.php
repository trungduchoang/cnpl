<?php
namespace App\Repositories\Temp;


interface SigninTempRepositoryInterface
{
    public function createSession(array $data);
    public function getSession(string $uid);
    public function deleteSession(string $uid);
}