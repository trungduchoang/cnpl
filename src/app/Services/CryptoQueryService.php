<?php

namespace App\Services;

class CryptoQueryService
{

    protected $iv;
    protected $key;
    protected $method;

    public function __construct($iv, $key, $method)
    {
        $this->iv = hex2bin($iv);
        $this->key = $key;
        $this->method = $method;
    }

    public function encryptQuery($query)
    {
        $encrypted = openssl_encrypt($query, $this->method, $this->key, 0, $this->iv);
        return $encrypted;
    }

    public function decryptQuery($encrypted)
    {
        $decrypted = openssl_decrypt($encrypted, $this->method, $this->key, 0, $this->iv);
        return $decrypted;
    }

}
