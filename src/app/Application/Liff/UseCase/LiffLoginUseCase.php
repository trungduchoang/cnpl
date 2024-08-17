<?php

namespace App\Application\Liff\UseCase;

use App\Application\Liff\Request\LiffLoginRequest;
use App\Services\CookieHandleService;

class LiffLoginUseCase
{
    protected $cookieHandleService; 

    public function __construct(CookieHandleService $cookieHandleService)
    {
        $this->cookieHandleService = $cookieHandleService;
    }

    public function index($request)
    {
        try {
            $cookie = $this->getCookie($request);
            $this->cookieHandleService->setCookie($cookie);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }


    private function getCookie($request)
    {
        $cookie = $this->cookieHandleService->getCookie($request);
        if ($cookie) {
            return $cookie;
        }
        $cookie = $this->cookieHandleService->generateCookie();
        return $cookie;
    }
}