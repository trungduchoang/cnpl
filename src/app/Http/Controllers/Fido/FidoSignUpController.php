<?php

namespace App\Http\Controllers\Fido;

use App\Http\Controllers\Controller;
use App\Application\Fido\Request\FidoSignUpRequest;
use App\Application\Fido\UseCase\FidoSignUpUseCase;

class FidoSignUpController extends Controller
{
    public function __invoke(FidoSignUpRequest $request, FidoSignUpUseCase $usecase)
    {
        $usecase->index($request);
        return view('fido/fido_signup');
    }
}