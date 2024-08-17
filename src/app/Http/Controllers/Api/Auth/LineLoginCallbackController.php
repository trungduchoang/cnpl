<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\AuthApi\Request\LineLoginCallbackRequest;
use App\Application\AuthApi\UseCase\LineLoginCallbackUseCase;
use App\Http\Controllers\Controller;

class LineLoginCallbackController extends Controller
{
    /**
     * line login controller function
     *
     * @param LineLoginCallbackRequest $request
     * @param LineLoginCallbackUseCase $usecase
     * @return void
     */
    public function index(LineLoginCallbackRequest $request, LineLoginCallbackUseCase $usecase)
    {
        return redirect($usecase->index($request));
    }
}
