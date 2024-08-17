<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Application\AuthApi\Request\SignupAttestationOptionsRequest;
use App\Application\AuthApi\UseCase\SignupAttestationOptionsUseCase;

class SignupAttestationOptionsController extends Controller
{

    /**
     * generate signup attestation options api function
     *
     * @param SignupAttestationOptionsRequest $request
     * @param SignupAttestationOptionsUseCase $usecase
     * @return object
     */
    public function index(SignupAttestationOptionsRequest $request, SignupAttestationOptionsUseCase $usecase): object
    {
        return response()->json($usecase->index($request), 200);
    }
}
