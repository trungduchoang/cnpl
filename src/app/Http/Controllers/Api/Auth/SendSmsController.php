<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Application\AuthApi\Request\SendSmsRequest;
use App\Application\AuthApi\UseCase\SendSmsUseCase;

class SendSmsController extends Controller
{

    protected $sendSmsUseCase;

    public function __construct()
    {
        $this->sendSmsUseCase = app()->make(SendSmsUseCase::class);
    }

    public function index(SendSmsRequest $request)
    {
        $response = $this->sendSmsUseCase->index($request);
        return $response;
    }
}
