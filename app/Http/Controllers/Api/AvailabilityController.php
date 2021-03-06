<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Services\PeriodService;
use App\Transformers\AvailabilityTransformer;
use Illuminate\Http\Request;
use Validator;
use Response;

class AvailabilityController extends Controller
{
    /** @var PeriodService */
    protected $period_service;

    /**
     * @param PeriodService $period_service
     */
    public function __construct(PeriodService $period_service)
    {
        $this->period_service = $period_service;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Availability::$validation);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        $availability = Availability::create($request->all());
        
        return fractal($availability, new AvailabilityTransformer)->respond(201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        return fractal(Availability::findOrFail($id), new AvailabilityTransformer)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Availability $availability
     *
     * @return Response
     */
    public function update(Request $request, Availability $availability)
    {
        $validator = Validator::make($request->all(), Availability::$validation);

        if ($validator->fails()) {
            throw new \InvalidArgumentException('Invalid arguments supplied for Availability');
        }

        /** @var Availability $availability */
        $availability->update($request->all());

        return fractal($availability->fresh(), new AvailabilityTransformer)->respond(204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        Availability::findOrFail($id)->delete();

        return response(null, 204);
    }
}
