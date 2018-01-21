<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Periodable;

class PeriodService
{
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
     * @throws \InvalidArgumentException
     */
    public function ensureValidDates(Periodable $periodable)
    {
        if ($periodable->from === null) {
            throw new \InvalidArgumentException('A period cannot have a from-value of null');
        }

        if ($periodable->to !== null && $periodable->from->gte($periodable->to)) {
            throw new \InvalidArgumentException('A period\'s to-date cannot be before or equal to from-date');
        }
    }

    /**
     * Checks for overlapping periods and handles any overlaps found, if any
     *
     * @param Periodable $periodable
     */
    public function checkForOverlappingPeriods(Periodable $periodable)
    {
        $overlapping_periods = $this->overlappingPeriods($periodable);

        if ($overlapping_periods->exists()) {
            $this->handlePeriodOverlap($overlapping_periods, $periodable);
        }
    }

    /**
     * Build a query to determine if overlaps exist
     *
     * @param Periodable $periodable
     *
     * @return Builder
     */
    public function overlappingPeriods(Periodable $periodable) : Builder
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

    /**
     * Throws an error with a relevant message depending on the number of overlapping periods found
     *
     * @param Builder $periods
     * @param Periodable $periodable
     *
     * @throws \InvalidArgumentException
     */
    protected function handlePeriodOverlap(Builder $periods, Periodable $periodable) : void
    {
        if ($periods->count() === 1) {
            /** @var Periodable $overlapping_period */
            $overlapping_period = $periods->first();
            throw new \InvalidArgumentException(sprintf(
                'Period from %s to %s overlaps another period for %s with ID %d, where period from %s to %s',
                $periodable->from->toDateTimeString(),
                $periodable->to === null ? 'indefinite' : $periodable->to->toDateTimeString(),
                class_basename($overlapping_period),
                $overlapping_period->id,
                $overlapping_period->from,
                $overlapping_period->to
            ));
        }

        /** @var Periodable $period */
        $periods = $periods->get();

        throw new \InvalidArgumentException(sprintf(
            'Period from %s to %s overlaps with multiple other periods for %s with ID %d. Overlap ID\'s: [%s]',
            $periodable->from->toDateTimeString(),
            $periodable->to === null ? 'indefinite' : $periodable->to->toDateTimeString(),
            class_basename($periods->first()),
            $periods->first()->id,
            $periods->implode('id', ', ')
        ));
    }
}