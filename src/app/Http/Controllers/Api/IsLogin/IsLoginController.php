<?php

namespace App\Http\Controllers\Api\IsLogin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IsLogin\IsLoginRequest;
use App\UseCases\IsLogin\IsLoginUseCase;
use Illuminate\Http\Request;

class IsLoginController extends Controller
{
    public function __invoke(IsLoginRequest $request, IsLoginUseCase $usecase)
    {
        return response()->json($usecase->index($request->getParam()));
    }
}
