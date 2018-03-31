<?php

namespace App\Http\Controllers;

use App\Services\PeriodService;
use App\Models\Weapon;

class WeaponController extends Controller
{
    /** @var PeriodService */
    protected $period_service;

    public function __construct(PeriodService $period_service)
    {
        $this->period_service = $period_service;
    }

    public function index()
    {
        return 'List of weapons';
    }

    public function show(Weapon $weapon)
    {
        return $weapon;
    }
}
