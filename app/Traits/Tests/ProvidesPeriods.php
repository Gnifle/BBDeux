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

    protected static function activeIndefinitePeriodMonth()
    {
        return [
            'from' => Carbon::now()->subMonth()->startOfMonth(),
            'to' => null,
        ];
    }

    protected static function inactiveFuturePeriodMonth()
    {
        return [
            'from' => Carbon::now()->addMonth()->startOfMonth(),
            'to' => Carbon::now()->addMonths(2)->endOfMonth(),
        ];
    }

    protected static function inactiveFutureIndefinitePeriodMonth()
    {
        return [
            'from' => Carbon::now()->addMonth()->startOfMonth(),
            'to' => null,
        ];
    }
}
