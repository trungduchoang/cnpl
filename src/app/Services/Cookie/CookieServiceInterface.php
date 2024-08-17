<?php
namespace App\Services\Cookie;

interface CookieServiceInterface
{
    public function generateCookie(): string;
    public function setCookie(string $cookie): void;
}