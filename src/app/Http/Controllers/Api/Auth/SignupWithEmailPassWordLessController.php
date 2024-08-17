<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Signup\SignupWithEmailPassWordLessRequest;
use App\UseCases\Signup\SignupWithEmailPasswordLessUseCase;

class SignupWithEmailPassWordLessController extends Controller
{
    /**
     * integragte passwardless email auth controller function
     *
     * @param SignupWithEmailPassWordLessRequest $request
     * @param SignupWithEmailPasswordLessUseCase $usecase
     * @return Json
     */
    public function __invoke(SignupWithEmailPassWordLessRequest $request, SignupWithEmailPasswordLessUseCase $usecase)
    {
        return response()->json($usecase->index($request->getParam()));
    }
}
