<?php
namespace App\Services\Jwt;

interface JwtVerifierServiceInterface
{
    public function decode(string $jwt);
    public function verify(array $data);
}