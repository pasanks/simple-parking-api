<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        //Todo
        // Customize the JSON response format for API exceptions.
        //Properly handle errors other than doing this.
        //I did this only for the dev purposes to maintain a consistent error responses.

        $statusCode = $this->determineStatusCode($exception);

        return response()->json([
            'error' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
            ],
        ], $statusCode);
    }

    protected function determineStatusCode(\Throwable $exception)
    {
        switch (true) {
            case $exception instanceof NotFoundHttpException:
                return Response::HTTP_NOT_FOUND;
            case $exception instanceof MethodNotAllowedHttpException:
                return Response::HTTP_METHOD_NOT_ALLOWED;
            case $exception instanceof UnauthorizedHttpException:
                return Response::HTTP_UNAUTHORIZED;
            case $exception instanceof AccessDeniedHttpException:
                return Response::HTTP_FORBIDDEN;
            case $exception instanceof ModelNotFoundException:
                return Response::HTTP_NOT_FOUND;
            case $exception instanceof TokenMismatchException:
                return Response::HTTP_BAD_REQUEST;
            case $exception instanceof ValidationException:
                return Response::HTTP_UNPROCESSABLE_ENTITY;
            case $exception instanceof HttpException:
                return $exception->getStatusCode();
            default:
                return Response::HTTP_INTERNAL_SERVER_ERROR;
        }
    }
}
