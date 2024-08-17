<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\AuthApi\Request\SigninWithEmailRequest;
use App\Application\AuthApi\UseCase\SigninWithEmailUseCase;

class SigninWithEmailController extends Controller
{
    /**
     * signin controller function
     *
     * @param [type] $request
     * @return json
     */
    public function __invoke(SigninWithEmailRequest $request, SigninWithEmailUseCase $useCase)
    {
        return $useCase->index($request);
    }
}
