<?php
namespace App\Repositories\LoginLog;

interface LoginLogRepositoryInterface
{
    public function create(array $data): void;
}