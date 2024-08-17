<?php

namespace App\Http\Controllers\Api\Signin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Oidc\OidcRequest;
use App\UseCases\Oidc\OidcUseCase;
use Illuminate\Http\Request;

class SigninWithOidcController extends Controller
{
    public function __invoke(OidcRequest $request, OidcUseCase $usecase)
    {
        return redirect($usecase->index($request->getParam()), 302);
    }
}
