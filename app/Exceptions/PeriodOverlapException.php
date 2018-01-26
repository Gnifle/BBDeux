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
                'Period from %s to %s overlaps another period for %s with ID %d, where period from %s to %s',
                $periodable->from->toDateTimeString(),
                $periodable->to === null ? 'indefinite' : $periodable->to->toDateTimeString(),
                class_basename($overlapping_period),
                $overlapping_period->id,
                $overlapping_period->from,
                $overlapping_period->to
            );
        }

        /** @var Periodable $period */
        $overlapping_periods = $overlapping_periods->get();

        return sprintf(
            'Period from %s to %s overlaps with multiple other periods for %s with ID %d. Overlap ID\'s: [%s]',
            $periodable->from->toDateTimeString(),
            $periodable->to === null ? 'indefinite' : $periodable->to->toDateTimeString(),
            class_basename($overlapping_periods->first()),
            $overlapping_periods->first()->id,
            $overlapping_periods->implode('id', ', ')
        );
    }
}
