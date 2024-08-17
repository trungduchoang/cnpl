<?php

namespace App\Http\Controllers\Liff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\Liff\UseCase\LiffRedirectUseCase;
use App\Application\Liff\Request\LiffRedirectRequest;
use Illuminate\Support\Facades\DB;

class LiffRedirectController extends Controller
{
    public function __invoke(LiffRedirectRequest $request, LiffRedirectUseCase $usecase)
    {
        return redirect($usecase->index($request));
    }
}
