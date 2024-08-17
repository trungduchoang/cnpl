<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Callback\XidCallbackRequest;
use App\UseCases\Callback\XidCallbackUseCase;
use Illuminate\Http\Request;

class XidCallbackController extends Controller
{
    public function __invoke(XidCallbackRequest $request, XidCallbackUseCase $usecase)
    {
        $url = $usecase->index($request->getParam());
        return redirect($url);
    }
}
