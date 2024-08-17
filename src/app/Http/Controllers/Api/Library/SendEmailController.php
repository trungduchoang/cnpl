<?php

namespace App\Http\Controllers\Api\Library;

use App\Http\Controllers\Controller;
use App\Http\Requests\Library\SendEmailRequest;
use App\UseCases\Library\SendEmailUseCase;

class SendEmailController extends Controller
{
    public function __invoke(SendEmailRequest $request, SendEmailUseCase $usecase)
    {
        return response()->json($usecase->index($request->getParam()), 200);
    }
}
