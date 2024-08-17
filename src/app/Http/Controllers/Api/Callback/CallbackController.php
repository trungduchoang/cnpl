<?php

namespace App\Http\Controllers\Api\Callback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Callback\CallbackRequest;
use App\UseCases\Callback\CallbackUseCase;
use Illuminate\Http\RedirectResponse;

class CallbackController extends Controller
{
    /**
     * Undocumented function
     *
     * @param CallbackRequest $request
     * @param CallbackUseCase $usecase
     * @return RedirectResponse
     */
    public function __invoke(CallbackRequest $request, CallbackUseCase $usecase): RedirectResponse
    {
        return redirect($usecase->index($request->getParam()), 302);
    }
}
