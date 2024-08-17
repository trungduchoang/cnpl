<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\AuthApi\Request\DeleteUserRequest;
use App\Application\AuthApi\UseCase\DeleteUserUseCase;


class DeleteUserController extends Controller
{


    public function index(DeleteUserRequest $request)
    {
        $response = app()->make(DeleteUserUseCase::class)->index($request);
        return $response;
    }
}
