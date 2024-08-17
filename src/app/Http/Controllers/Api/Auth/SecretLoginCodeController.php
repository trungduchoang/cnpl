<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\AuthApi\UseCase\SecretLoginCodeUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\AuthApi\Request\SecretLoginCodeRequest;

class SecretLoginCodeController extends Controller
{
    public function index(SecretLoginCodeRequest $request)
    {
        $redirectUrl = app()->make(SecretLoginCodeUseCase::class)->index($request);
        return redirect($redirectUrl);
    }
}
