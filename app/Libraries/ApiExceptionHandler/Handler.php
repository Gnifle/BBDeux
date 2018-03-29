<?php

namespace App\Libraries\ApiExceptionHandler;

use App\Exceptions\PeriodException;
use App\Exceptions\PeriodOverlapException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use \InvalidArgumentException;

class Handler
{
    use HandlerTrait;

    /** @var stdClass */
    protected $response;

    /**
     * Map of fully qualified Exception class names and their respective handler
     * @var array
     */
    protected $exceptionHandlerMap = [
        InvalidArgumentException::class => 'invalidArgumentException',
        ModelNotFoundException::class => 'modelNotFoundException',
        PeriodException::class => 'periodException',
        PeriodOverlapException::class => 'periodException',
    ];

    /**
     * Handle an InvalidArgumentException
     *
     * @param Request $request
     * @param InvalidArgumentException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidArgumentException(Request $request, InvalidArgumentException $exception)
    {
        return $this->respond(400);
    }

    /**
     * Handles a ModelNotFoundException
     *
     * @param Request $request
     * @param ModelNotFoundException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFoundException(Request $request, ModelNotFoundException $exception)
    {
        return $this->respond(404);
    }

    /**
     * Handle a PeriodException
     *
     * @param Request $request
     * @param PeriodException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function periodException(Request $request, PeriodException $exception)
    {
        $this->response->request['data']['from'] = $exception->getFrom();
        $this->response->request['data']['to'] = $exception->getTo();

        return $this->respond(400);
    }
}
