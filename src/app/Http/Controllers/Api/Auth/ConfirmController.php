<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\AuthApi\UseCase\ConfirmUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\AuthApi\Request\ConfirmRequest;

class ConfirmController extends Controller
{
    public function index(ConfirmRequest $request)
    {
        $redirectUrl = app()->make(ConfirmUseCase::class)->index($request);
        return $redirectUrl ? redirect($redirectUrl) : view('errors/confirm-error');
        
        return redirect($redirectUrl);
    }
}
