<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Response;
use Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
//
//    /**
//     * @param Request $request
//     * @param Exception $exception
//     *
//     * @return Response
//     */
//    protected function handleException(Request $request, Exception $exception)
//    {
//        switch (true) {
//            case $exception instanceof \InvalidArgumentException:
//                return response()->json(['errors' => [$exception->getMessage()]]);
//        }
//    }
}
