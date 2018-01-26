<?php

namespace App\Services;

use App\Exceptions\PeriodException;
use App\Exceptions\PeriodOverlapException;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Periodable;

class PeriodService
{
    /**
     * Validate a given Periodable
     *
     * @param Periodable $periodable
     *
     * @throws PeriodException
     */
    public function validatePeriod(Periodable $periodable)
    {
        $this->ensureValidDates($periodable);

        $this->checkForOverlappingPeriods($periodable);
    }

    /**
     * Ensures the Periodable from-date is not not null, and that to-date is not before from.
     *
     * @param Periodable $periodable
     *
     * @throws PeriodException
     */
    public function ensureValidDates(Periodable $periodable)
    {
        if ($periodable->from === null) {
            throw new PeriodException('A period cannot have a from-value of null', $periodable->from);
        }

        if ($periodable->to !== null && $periodable->from->gte($periodable->to)) {
            throw new PeriodException(
                'A period\'s to-date cannot be before or equal to from-date',
                $periodable->from,
                $periodable->to
            );
        }
    }

    /**
     * Checks for overlapping periods and handles any overlaps found, if any
     *
     * @param Periodable $periodable
     *
     * @throws PeriodOverlapException
     */
    public function checkForOverlappingPeriods(Periodable $periodable)
    {
        $overlapping_periods = $this->findOverlappingPeriods($periodable);

        if ($overlapping_periods->exists()) {
            throw new PeriodOverlapException($periodable, $overlapping_periods);
        }
    }

    /**
     * Build a query to determine if overlaps exist
     *
     * @param Periodable $periodable
     *
     * @return Builder
     */
    public function findOverlappingPeriods(Periodable $periodable) : Builder
    {
        $relation = $periodable->getPeriodableRelationName();

        $this->ensureValidRelation($periodable, $relation);

        $periods = $periodable->on()
            ->where("{$relation}_id", $periodable->{$relation}->id)
            ->where("{$relation}_type", get_class($periodable->{$relation}))
            ->where('to', '>', $periodable->from);

        return $periodable->to === null ? $periods : $periods->where('from', '<', $periodable->to);
    }

    /**
     * Ensures that a Periodable has a valid, existing relation
     *
     * @param Periodable $periodable
     * @param string $relation
     *
     * @throws \InvalidArgumentException
     */
    public function ensureValidRelation(Periodable $periodable, string $relation)
    {
        if ($periodable->{$relation} === null) {
            throw new \InvalidArgumentException("Period does not have a valid, existing relation '{$relation}'");
        }
    }
}
