<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\AuthApi\UseCase\SignupWithPhoneNumberUseCase;
use App\Application\AuthApi\Request\SignupWithPhoneNumberRequest;


class SignupWithPhoneNumberController extends Controller
{
    public function __invoke(SignupWithPhoneNumberRequest $request, SignupWithPhoneNumberUseCase $useCase)
    {
       return $useCase->index($request);
    }
}
