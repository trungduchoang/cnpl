<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\AuthApi\Request\LineLoginUrlRequest;
use App\Application\AuthApi\UseCase\LineLoginUrlUseCase;

class LineLoginUrlController extends Controller
{
    /**
     * Undocumented function
     *
     * @param LineLoginUrlRequest $request
     * @param LineLoginUrlUseCase $usecase
     * @return object
     */
    public function index(LineLoginUrlRequest $request, LineLoginUrlUseCase $usecase): object
    {
        return response()->json($usecase->index($request), 200);
    }
}
