<?php

namespace App\Http\Controllers\Api\Url;

use App\Http\Controllers\Controller;
use App\Http\Requests\Url\AuthUrlRequest;
use App\UseCases\Url\AuthUrlUseCase;
use Illuminate\Http\JsonResponse;

class AuthUrlController extends Controller
{

    /**
     * Undocumented function
     *
     * @param AuthUrlRequest $request
     * @param AuthUrlUseCase $usecase
     * @return JsonResponse
     */
    public function __invoke(AuthUrlRequest $request, AuthUrlUseCase $usecase): JsonResponse
    {
        return response()->json($usecase->index($request->getParam()), 200);
    }
}
