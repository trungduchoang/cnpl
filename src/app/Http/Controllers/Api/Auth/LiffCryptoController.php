<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\AuthApi\UseCase\LiffCryptoUseCase;
use App\Http\Controllers\Controller;
use App\Application\AuthApi\Request\LiffCryptoRequest;
use Illuminate\Http\Request;

class LiffCryptoController extends Controller
{
    public function __invoke(LiffCryptoRequest $request, LiffCryptoUseCase $usecase)
    {
        try {
            return response()->json($usecase->index($request), 200);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
