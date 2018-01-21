<?php

namespace App\Models;

use App\Services\PeriodService;

class PeriodObserver
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
     * Hooks in on the Periodable models saving event, and determines whether the model can be
     * saved judging by the period
     *
     * @param Periodable $periodable
     */
    public function saving(Periodable $periodable) : void
    {
        $this->period_service->validatePeriod($periodable);
    }
}
