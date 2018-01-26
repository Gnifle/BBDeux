<?php

namespace App\Traits\Exceptions;

use Illuminate\Http\Request;
use \Exception;
use \InvalidArgumentException;

trait HandlesRestExceptions
{
    /** @var array Map of fully qualified Exception class names and their respective handler */
    protected $restExceptionHandlerMap = [
        InvalidArgumentException::class => 'handleInvalidArgumentException',
    ];

    public function handleRestException(Request $request, Exception $exception)
    {
        $method = $this->determineRestExceptionHandler($request, $exception);

        return $this->{$method}($request, $exception);
    }

    protected function determineRestExceptionHandler(Request $request, Exception $exception)
    {
        return array_get($this->restExceptionHandlerMap, get_class($exception), parent::render($request, $exception));
    }

    protected function handleInvalidArgumentException(Request $request, Exception $exception)
    {
        return response()->json([], 400);
    }

    protected function parseException(Request $request, Exception $exception)
    {
        return [];
    }
}
