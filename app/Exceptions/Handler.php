<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        // Add exception types that should not be reported
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Report exceptions to external services
        });
    }

    public function render($request, Throwable $exception)
    {
        // Handle API exceptions
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        // Handle web exceptions
        return $this->handleWebException($request, $exception);
    }

    protected function handleApiException($request, Throwable $exception)
    {
        $statusCode = 500;
        $message = 'Internal Server Error';

        if ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Unauthenticated';
        } elseif ($exception instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = 'Resource not found';
        } elseif ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'Validation failed';
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $exception->errors(),
            ], $statusCode);
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'Endpoint not found';
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $statusCode = 403;
            $message = 'Access denied';
        } elseif (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => config('app.debug') ? [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ] : null,
        ], $statusCode);
    }

    protected function handleWebException($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->view('errors.404', [], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        if ($exception instanceof AccessDeniedHttpException) {
            return response()->view('errors.403', [], 403);
        }

        // For other exceptions, show a generic error page
        if (!config('app.debug')) {
            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $exception);
    }
}