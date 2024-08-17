<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\AuthApi\UseCase\SigninWithPhoneNumberUseCase;
use App\Application\AuthApi\Request\SigninWithPhoneNumberRequest;

class SigninWithPhoneNumberController extends Controller
{
    public function __invoke(SigninWithPhoneNumberRequest $request, SigninWithPhoneNumberUseCase $useCase)
    {
        return $useCase->index($request);
    }
}
