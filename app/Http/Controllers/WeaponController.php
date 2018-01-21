<?php

namespace App\Http\Controllers;

use App\AvailabilityTransformer;
use Illuminate\Http\Request;

class WeaponController extends Controller
{
    /** @var PeriodService */
    protected $period_service;

    public function __construct(PeriodService $period_service)
    {
        $this->period_service = $period_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AvailabilityTransformer  $appModelsWeapon
     * @return \Illuminate\Http\Response
     */
    public function show(AvailabilityTransformer $appModelsWeapon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AvailabilityTransformer  $appModelsWeapon
     * @return \Illuminate\Http\Response
     */
    public function edit(AvailabilityTransformer $appModelsWeapon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AvailabilityTransformer  $appModelsWeapon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AvailabilityTransformer $appModelsWeapon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AvailabilityTransformer  $appModelsWeapon
     * @return \Illuminate\Http\Response
     */
    public function destroy(AvailabilityTransformer $appModelsWeapon)
    {
        //
    }
}
