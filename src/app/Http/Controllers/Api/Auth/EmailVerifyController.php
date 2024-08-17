<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Application\AuthApi\Request\EmailVerifyRequest;
use App\Application\AuthApi\UseCase\EmailVerifyUseCase;

class EmailVerifyController extends Controller
{
    public function index(EmailVerifyRequest $request)
    {
        $redirectUrl = app()->make(EmailVerifyUseCase::class)->index($request);
        return redirect($redirectUrl, 301);
    }
}
