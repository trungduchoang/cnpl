<?php

namespace App\Http\Controllers\Fido;

use App\Http\Controllers\Controller;
use App\Application\Fido\Request\FidoSignInRequest;
use App\Application\Fido\UseCase\FidoSignInUseCase;

class FidoSignInController extends Controller
{
    public function __invoke(FidoSignInRequest $request, FidoSignInUseCase $usecase)
    {
        $usecase->index($request);
        return view('fido/fido_signin');
    }
}