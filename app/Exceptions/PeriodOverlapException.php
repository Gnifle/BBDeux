<?php

namespace App\Exceptions;

use App\Contracts\Periodable;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class PeriodOverlapException extends PeriodException
{
    public function __construct(
        Periodable $periodable,
        Builder $overlapping_periods = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = $this->composeMessage($periodable, $overlapping_periods);

        parent::__construct($message, $periodable->from, $periodable->to, $code, $previous);
    }

    /**
     * Compose the actual error message before passing it to the parent constructor
     *
     * @param Periodable $periodable
     * @param Builder $overlapping_periods
     *
     * @return string
     */
    protected function composeMessage(Periodable $periodable, Builder $overlapping_periods)
    {
        if ($overlapping_periods->count() === 1) {
            /** @var Periodable $overlapping_period */
            $overlapping_period = $overlapping_periods->first();

            return sprintf(
                'Period from %s to %s overlaps another period, where period from %s to %s',
                $periodable->from->toDateTimeString(),
                $periodable->to === null ? 'indefinite' : $periodable->to->toDateTimeString(),
                $overlapping_period->from,
                $overlapping_period->to
            );
        }

        /** @var Periodable $period */
        $overlapping_periods = $overlapping_periods->get();

        $overlapping_periods->each(function (Periodable $period) use (&$periods_from_to) {
            $periods_from_to .= "\n[{$period->from->toDateTimeString()} to {$period->to->toDateTimeString()}]";
        });

        return sprintf(
            "Period from %s to %s overlaps with multiple other periods:%s",
            $periodable->from->toDateTimeString(),
            $periodable->to === null ? 'indefinite' : $periodable->to->toDateTimeString(),
            $periods_from_to
        );
    }
}
