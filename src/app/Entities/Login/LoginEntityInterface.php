<?php
namespace App\Entities\Login;

interface LoginEntityInterface
{
    public function getCookie(): string;
    public function getUserName(): string;
    public function getLastLogin(): string;
    public function getProjectId(): string;
    public function getCreatedAt(): string;
    public function getIp(): string;
    public function getUserAgent(): string;
}