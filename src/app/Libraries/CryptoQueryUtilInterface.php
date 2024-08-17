<?php
namespace App\Libraries;

interface CryptoQueryUtilInterface
{
    public function encryptQuery(string $query): string;
    public function decryptQuery(string $query): string;
}