<?php

namespace App\Http\Controllers\Liff;

use App\Http\Controllers\Controller;
use App\Application\Liff\Request\LiffLoginRequest;
use App\Application\Liff\UseCase\LiffLoginUseCase;

class LiffLoginController extends Controller
{
    public function __invoke(LiffLoginRequest $request, LiffLoginUseCase $usecase)
    {
        $usecase->index($request);
        return view('liff/liff_login');
    }
}