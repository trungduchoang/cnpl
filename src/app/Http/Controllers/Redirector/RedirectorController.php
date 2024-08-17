<?php

namespace App\Http\Controllers\Redirector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\Redirector\Request\RedirectorRequest;
use App\Application\Redirector\UseCase\RedirectorUseCase;


class RedirectorController extends Controller
{
    protected $RedirectorUseCase;

    public function __construct()
    {
        $this->redirectorUseCase = app()->make(RedirectorUseCase::class);
    }
    public function redirector(RedirectorRequest $request)
    {
        try {
            $redirectUrl =  $this->redirectorUseCase->redirector($request);
            return redirect($redirectUrl);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
