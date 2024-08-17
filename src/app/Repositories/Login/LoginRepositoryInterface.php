<?php
namespace App\Repositories\Login;

use App\Entities\Login\LoginEntity;

interface LoginRepositoryInterface
{
    public function isLogin(array $data): bool;
    public function login(array $data);
    public function getUserData(array $data);
    public function createOrUpdate(array $data): LoginEntity;
    public function getUsers(array $cookies, int $projctId): array;
}