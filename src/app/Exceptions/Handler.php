<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception->getMessage() === 'temp is not found') {
                return response()->view('errors/confirm-error');
            }
            if ($request->is('api/atuth/callback') ||
                $request->is('api/auth/verify-email') ||
                $request->is('api/auth/signup/confirm') ||
                // $request->is('api/auth/callback') ||
                $request->is('api/auth/callback/line') ||
                $request->is('api/auth/signin/confirm')) {
                return parent::render($request, $exception);
            }
            $statusCode = 400;
            $statusCode = $exception->getCode() ? $exception->getCode(): 500;
            $message = $exception->getMessage();
            if ($this->isHttpException($exception)) {
                $statusCode = $exception->getStatusCode();
            }

            return response()->json([
                'error' => [
                    'statusCode' => $statusCode,
                    'message' => $message
                ]
            ], $statusCode);
        }

        return parent::render($request, $exception);
    }

}
