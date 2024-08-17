<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Application\AuthApi\Request\SignupAssertionResultRequest;
use App\Application\AuthApi\UseCase\SignupAssertionResultUseCase;

class SignupAssertionResultController extends Controller
{

    /**
     * assertion result controller function
     * 
     * @param SignupAssertionResultRequest $request
     * @param SignupAssertionResultUseCase $usecase
     * @return object
     */
    public function index(SignupAssertionResultRequest $request, SignupAssertionResultUseCase $usecase): object
    {
        return response()->json($usecase->index($request), 200);
    }
}
