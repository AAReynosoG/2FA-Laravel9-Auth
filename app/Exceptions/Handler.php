<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\LostConnectionException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * A catalog of error types and their corresponding views, status codes, and messages.
     *
     * @var array<string, array<string, mixed>>
     */
    protected $errorCatalog = [
        '405_METHOD_NOT_ALLOWED' => [
            'view' => 'errors.general_errors',
            'identifier' => 'HTTPERR405',
            'status' => 405,
            'user_message' => 'The method is not allowed for the requested URL.',
        ],
        '404_NOT_FOUND' => [
            'view' => 'errors.general_errors',
            'identifier' => 'HTTPERR404',
            'status' => 404,
            'user_message' => 'The page you are looking for could not be found.',
        ],
        '419_TOKEN_MISMATCH' => [
            'view' => 'errors.general_errors',
            'identifier' => 'HTTPERR419',
            'status' => 419,
            'user_message' => 'Your session has expired. Please refresh and try again.',
        ],
        '500_INTERNAL_SERVER_ERROR' => [
            'view' => 'errors.server_errors',
            'identifier' => 'HTTPERR500',
            'status' => 500,
            'user_message' => 'An unexpected error occurred. Please try again later.',
        ],
    ];


    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * This method handles the rendering of exceptions into HTTP responses. It checks
     * for specific exception types and renders the corresponding error view. If the
     * exception is not recognized, it falls back to the parent class's render method.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @param \Throwable $e The exception to render.
     * @return \Illuminate\Http\Response The HTTP response corresponding to the exception.
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->renderError('405_METHOD_NOT_ALLOWED');
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->renderError('404_NOT_FOUND');
        }

        if ($e instanceof AuthenticationException) {
            return parent::render($request, $e);
        }

        if ($e instanceof ValidationException) {
            return parent::render($request, $e);
        }

        if ($e instanceof TokenMismatchException) {
            return $this->renderError('419_TOKEN_MISMATCH');
        }

        if ($e instanceof LostConnectionException) {
            return $this->renderError('500_INTERNAL_SERVER_ERROR');
        }

        if ($e instanceof \Exception) {
            return $this->renderError('500_INTERNAL_SERVER_ERROR');
        }



        // Let the parent class handle any other exceptions
        return parent::render($request, $e);
    }

    /**
     * Render the error view for the given error code.
     *
     * This method returns the error view for the given error code, along with the
     * corresponding HTTP status code and user message.
     *
     * @param string $errorCode The error code to render.
     * @return \Illuminate\Http\Response The HTTP response corresponding to the error code.
     */
    protected function renderError($errorCode)
    {
        $error = $this->errorCatalog[$errorCode];
        return response()->view($error['view'], [
            'user_message' => $error['user_message'],
            'error_identifier' => $error['identifier']
        ], $error['status']);
    }

}
