<?php

namespace App\Http\Controllers\Api\UserInfo;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInfo\UserInfoReqest;
use App\UseCases\UserInfo\UserInfoUseCase;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{
    public function __invoke(UserInfoReqest $request, UserInfoUseCase $usecase)
    {
        return response()->json($usecase->index($request->getParam()));
    }
}
