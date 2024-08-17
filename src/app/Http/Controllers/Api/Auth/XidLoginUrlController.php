<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Signin\XidLoginUrlRequest;
use App\UseCases\Signin\XidLoginUrlUseCase;
use Illuminate\Http\Request;


class XidLoginUrlController extends Controller
{
    public function __invoke(XidLoginUrlRequest $request, XidLoginUrlUseCase $usecase)
    {
        return response()->json($usecase->index($request->getParam()), 200);
    }
}
