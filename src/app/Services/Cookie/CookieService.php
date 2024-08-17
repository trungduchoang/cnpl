<?php
namespace App\Services\Cookie;

class CookieService implements CookieServiceInterface
{
    /**
     * generate cookie function
     *
     * @return string
     */
    public function generateCookie(): string
    {
        return sha1("L'Alpe D'Huez".time().mt_rand().$_SERVER['REMOTE_ADDR'].$_SERVER['REMOTE_PORT'].$_SERVER['REQUEST_URI']);
    }


    /**
     * set cookie function
     *
     * @param string $cookie
     * @return void
     */
    public function setCookie(string $cookie): void
    {
        setcookie('TAPCM', $cookie, 0x7f000000, '/');
        setcookie('PLATEID_TAPCM', $cookie, 0x7f000000, '/', 'plate.id');
    }
}