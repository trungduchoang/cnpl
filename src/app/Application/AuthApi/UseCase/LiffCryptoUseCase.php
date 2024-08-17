<?php

namespace App\Application\AuthApi\UseCase;

use App\Services\CryptoQueryService;

class LiffCryptoUseCase
{
    protected $cryptoQueryService;

    public function __construct(CryptoQueryService $cryptoQueryService)
    {
        $this->cryptoQueryService = $cryptoQueryService;
    }

    public function index($request)
    {
        $accessToken = $request->getParams()['accessToken'];
        return [
            'statusCode'  => 200,
            'accessToken' => $this->cryptoQueryService->encryptQuery($accessToken)
        ];
    }
}