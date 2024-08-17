<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Application\AuthApi\Request\SigninAttestationOptionsRequest;
use App\Application\AuthApi\UseCase\SigninAttestationOptionsUseCase;
use App\Application\AuthApi\UseCase;
use Illuminate\Http\Request;

class SigninAttestationOptionsController extends Controller
{

    public function index(SigninAttestationOptionsRequest $request, SigninAttestationOptionsUseCase $usecase)
    {
        return response()->json($usecase->index($request), 200);
    }
}
