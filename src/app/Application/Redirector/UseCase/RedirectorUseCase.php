<?php
namespace App\Application\Redirector\UseCase;

use App\Services\CookieHandleService;


class RedirectorUseCase
{
    protected $cookieHandleService;

    public function __construct()
    {
        $this->cookieHandleService = app()->make(CookieHandleService::class);
    }


    public function redirector($request)
    {
        $cookie = $this->getCookie($request);
        $data = $request->getParam($request);
        $this->setCookie($cookie);
        return $data['redirectUrl'];
    }


    private function getCookie($request)
    {
        $cookie = $this->cookieHandleService->getCookie($request);
        if ($cookie) {
            return $cookie;
        } else {
            $cookie = $this->cookieHandleService->generateCookie();
        }
        return $cookie;
    }


    private function setCookie($cookie) {
        $this->cookieHandleService->setCookie($cookie);
    }
}