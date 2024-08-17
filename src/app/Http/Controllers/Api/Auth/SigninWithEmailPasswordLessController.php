<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Signin\SigninWithEmailPasswordLessRequest;
use App\UseCases\Signin\SigninWithEmailPassWordLessUseCase;

class SigninWithEmailPasswordLessController extends Controller
{
    public function __invoke(SigninWithEmailPasswordLessRequest $request, SigninWithEmailPassWordLessUseCase $usecase)
    {
        return response()->json($usecase->index($request->getParam()));
    }
}
