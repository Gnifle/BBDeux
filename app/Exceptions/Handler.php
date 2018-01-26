<?php

namespace App\Exceptions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Libraries\ApiExceptionHandler\Handler as ApiExceptionHandler;
use App\Traits\Exceptions\HandlesRestExceptions;
use Exception;

class Handler extends ExceptionHandler
{
    use HandlesRestExceptions;

    protected $rest_exception_handler;

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
        'password',
        'password_confirmation',
    ];

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->rest_exception_handler = new ApiExceptionHandler();
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            return $this->rest_exception_handler->handle($request, $exception);
        }

        return parent::render($request, $exception);
    }
}
