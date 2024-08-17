<?php

namespace App\Http\Controllers\Api\Line;


use App\Http\Controllers\Controller;
use App\Http\Requests\Line\LineMessageRequest;
use App\UseCases\Line\LineMessageUseCase;
use Illuminate\Http\JsonResponse;

class LineMessageController extends Controller
{


    /**
     * Undocumented function
     *
     * @param LineMessageRequest $request
     * @param LineMessageUseCase $usecase
     * @return JsonResponse
     */
    public function __invoke(LineMessageRequest $request, LineMessageUseCase $usecase): JsonResponse
    {
        return response()->json($usecase->handle($request->getParam()), 200);
    }
}
