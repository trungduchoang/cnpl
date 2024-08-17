<?php


namespace App\Services;

class Base64UrlService
{
    public static function encode($data, $usePadding = false)
    {
        $encoded = strtr(base64_encode($data), '+/', '-_');

        return true === $usePadding ? $encoded : rtrim($encoded, '=');
    }

    public static function decode($data)
    {
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        return $decoded;
    }
}