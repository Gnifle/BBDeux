<?php

namespace App\Traits;

use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Dynamic Properties:
 * @property-read Availability $current_availability
 *
 * Relationships:
 * @property-read Collection|Availability[] $availability
 *
 * Scopes:
 * @method Builder|static available(Carbon $when = null) Return only available items
 */
trait HasAvailabilityPeriods
{
    /** @var bool */
    protected $is_available;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function availability()
    {
        return $this->morphMany(Availability::class, 'availability');
    }

    /**
     * Return only currently available items.
     *
     * First we check for open-ended ($this->to === null) active periods. We assume there will never be more than one
     * active period at most, and thus return the first result if any is found.
     * If no match is found, we check for fixed active periods, also assuming there will be one result at most.
     *
     * @param Builder $query
     * @param Carbon|null $when Optional. Defaults to Carbon::now()
     *
     * @return Builder|static
     */
    public function scopeAvailable(Builder $query, Carbon $when = null)
    {
        $when = $when ?: Carbon::now();

//        dd(
//            $query->join('availabilities', 'availabilities.availability_id', '=', "{$this->getTable()}.{$this->getKeyName()}")
//                ->where(function (Builder $query) use ($when) {
//                    return $query->whereNotNull('to')
//                        ->where('from', '<=', $when);
//                        ->where('to', '>=', $when);
//                })
//                ->get()
//        );

        return $query->has('availability')
            ->join('availabilities', 'availabilities.availability_id', '=', "{$this->getTable()}.{$this->getKeyName()}")
            ->where(function (Builder $query) use ($when) {
                return $query->whereNull('to')
                    ->where('from', '<=', Carbon::now());
            })->orWhere(function (Builder $query) use ($when) {
                return $query->whereNotNull('to')
                    ->where('from', '<=', $when)
                    ->where('to', '>=', $when);
            });
    }

    /**
     * Determines if the item is available for purchase.
     * If $this->to is NULL, the item is available if $this->from is larger than NOW()
     * If $this->to is NOT NULL, NOW() must be between $this->from and $this->to
     *
     * @return bool Whether the item is available for purchase
     */
    public function getIsAvailableAttribute() : bool
    {
        return $this->current_availability !== null;
    }

    /**
     * Returns the currently active availability period, if any.
     *
     * First we check for open-ended ($this->to === null) active periods. We assume there will never be more than one
     * active period at most, and thus return the first result if any is found.
     * If no match is found, we check for fixed active periods, also assuming there will be one result at most.
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function getCurrentAvailabilityAttribute()
    {
        $now = Carbon::now();

        return $this->availability()->whereNull('to')
            ->where('from', '<=', $now)
            ->firstOr(function () use ($now) {
                return $this->availability()->whereNotNull('to')
                    ->where('from', '<=', $now)
                    ->where('to', '>=', $now)
                    ->first();
            });
    }
}
