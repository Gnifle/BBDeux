<?php

namespace App\Traits\Tests;

use Carbon\Carbon;

/**
 *
 */
trait ProvidesPeriods
{
    protected static function activePeriodMonth()
    {
        return [
            'from' => Carbon::now()->subMonth()->startOfMonth(),
            'to' => Carbon::now()->addMonth()->endOfMonth(),
        ];
    }
}
