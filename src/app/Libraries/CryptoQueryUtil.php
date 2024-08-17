<?php
namespace App\Libraries;

class CryptoQueryUtil implements CryptoQueryUtilInterface
{
    protected $iv;
    protected $key;
    protected $method;


    public function __construct($iv, $key, $method)
    {
        $this->iv = $iv;
        $this->key = $key;
        $this->method = $method;
    }


    public function encryptQuery($query): string
    {
        $encrypted = openssl_encrypt($query, $this->method, $this->key, 0, $this->iv);
        $encrypted = strtr($encrypted, '+/', '-_');
        return $encrypted;
    }

    public function decryptQuery(string $query): string
    {
        $query = strtr($query, '-_', '+/');
        $decrypted = openssl_decrypt($query, $this->method, $this->key, 0, $this->iv);
        return $decrypted;
    }
}