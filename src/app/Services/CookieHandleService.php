<?php

namespace App\Services;



class CookieHandleService
{
    public function generateCookie()
    {
        $cookie = sha1("L'Alpe D'Huez".time().mt_rand().$_SERVER['REMOTE_ADDR'].$_SERVER['REMOTE_PORT'].$_SERVER['REQUEST_URI']);
        return $cookie;
    }

    public function setCookie($cookie)
    {
        setcookie('TAPCM', $cookie, 0x7f000000, '/');
        setcookie('PLATEID_TAPCM', $cookie, 0x7f000000, '/', 'plate.id');
    }

    public function getCookie($request)
    {
        $tapcm = '';
        if ($request->cookie('TAPCM')) {
            $tapcm = $request->cookie('TAPCM');
            return $tapcm;
        };
        return;
    }
}