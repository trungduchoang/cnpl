<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Confirm\ConfirmSigninUseCase;
use App\Http\Requests\Confirm\ConfirmSigninRequest;

class ConfirmSigninController extends Controller
{
    public function __invoke(ConfirmSigninRequest $request, ConfirmSigninUseCase $usecase)
    {
        $redirectUrl = $usecase->index($request->getParam());
        logger('log 6 : ' . $redirectUrl);
        return redirect($redirectUrl, 301);
    }
}
