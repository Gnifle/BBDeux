<?php

namespace App\Libraries\ApiExceptionHandler;

use Carbon\Carbon;
use Illuminate\Http\Request;
use \Exception;

trait HandlerTrait
{
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

        $this->response = $this->prepareResponse($request, $exception);

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

    protected function prepareResponse(Request $request, Exception $exception)
    {
        return (object) [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'trace' => $exception->getTrace(),
            'request' => [
                'data' => $request->all(),
                'user' => $request->user(),
            ],
        ];
    }

    protected function respond(int $status = 500)
    {
        $this->preprocessResponse($status);

        return response()->json((array) $this->response, $status);
    }

    protected function preprocessResponse(int $status)
    {
        $this->response->code = $status;

        if (! (env('APP_DEBUG') && env('APP_DEBUG_API_TRACE'))) {
            $this->response->trace = null;
        }
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
        $this->response->message = "An unhandled error occurred with message: {$exception->getMessage()}";

        return $this->respond(500);
    }
}
