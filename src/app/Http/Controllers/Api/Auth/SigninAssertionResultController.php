<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Application\AuthApi\Request\SigninAssertionResultRequest;
use App\Application\AuthApi\UseCase\SigninAssertionResultUseCase;
use Illuminate\Http\Request;

class SigninAssertionResultController extends Controller
{
    public function index(SigninAssertionResultRequest $request, SigninAssertionResultUseCase $usecase):object
    {
        return response()->json($usecase->index($request), 200);
    }
}
