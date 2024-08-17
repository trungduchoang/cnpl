<?php

namespace App\Http\Controllers\Api\Signup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\AuthApi\UseCase\SignupWithEmailUseCase;
use App\Application\AuthApi\Request\SignupWithEmailRequest;

class SignupWithEmailController extends Controller
{
    /**
     * signup controller function
     *
     * @param [type] $request
     * @return json
     */
    public function __invoke(SignupWithEmailRequest $request, SignupWithEmailUseCase $usecase)
    {
        return response()->json($usecase->index($request->getParam()));
    }
    
}
