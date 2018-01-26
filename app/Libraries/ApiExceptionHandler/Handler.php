<?php

namespace App\Libraries\ApiExceptionHandler;

use Illuminate\Http\Request;
use \Exception;
use \InvalidArgumentException;

class Handler
{
    /**
     * Map of fully qualified Exception class names and their respective handler
     * @var array
     */
    protected $exceptionHandlerMap = [
        InvalidArgumentException::class => 'invalidArgumentException',
    ];

    /**
     * Handle an API Exception
     *
     * @param Request $request
     * @param Exception $exception
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Exception $exception)
    {
        $method = $this->determineHandler($exception);

        return $this->{$method}($request, $exception);
    }

    /**
     * Retrieve the method name of the exception handler for a given Exception
     *
     * @param Exception $exception
     *
     * @return string
     */
    protected function determineHandler(Exception $exception)
    {
        return array_get($this->exceptionHandlerMap, get_class($exception), 'default');
    }

    /**
     * Handle an InvalidArgumentException
     *
     * @param Request $request
     * @param Exception $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidArgumentException(Request $request, Exception $exception)
    {
        return response()->json([], 400);
    }

    /**
     * Fallback Exception handler, if no other handle mathod is defined for the given Exception
     *
     * @param Request $request
     * @param Exception $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function default(Request $request, Exception $exception)
    {
        return response()->json([
            'message' => "And unhandled error occurred with message: {$exception->getMessage()}",
            'code' => $exception->getCode(),
            'trace' => $exception->getTrace(),
            'at' => "In {$exception->getFile()} on line {$exception->getLine()}",
            'request' => [
                'data' => $request->all(),
                'user' => $request->user(),
            ],
        ], 500);
    }
}
