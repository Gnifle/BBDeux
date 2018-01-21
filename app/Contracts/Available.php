<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * @property int $id
 *
 * @mixin \Eloquent
 */
interface Available
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function availability();

    /**
     * Join the `availabilities` table on the query
     *
     * @param Builder|static $query
     *
     * @return Builder|static
     */
    public function scopeWithAvailabilities(Builder $query);

    /**
     * Return only currently available items.
     *
     * First we check for open-ended ($this->to === null) active periods. We assume there will never be more than one
     * active period at most, and thus return the first result if any is found.
     * If no match is found, we check for fixed active periods, also assuming there will be one result at most.
     *
     * @param Builder|static $query
     * @param Carbon|null $when Optional. Defaults to Carbon::now()
     *
     * @return Builder|static
     */
    public function scopeAvailable(Builder $query, Carbon $when = null);

    /**
     * @param Builder|static $query
     * @param Carbon|null $when
     *
     * @return Builder|static
     */
    public function scopeUnavailable(Builder $query, Carbon $when = null);

    /**
     * Determines if the item is available for purchase.
     * If $this->to is NULL, the item is available if $this->from is larger than NOW()
     * If $this->to is NOT NULL, NOW() must be between $this->from and $this->to
     *
     * @return bool Whether the item is available for purchase
     */
    public function getIsAvailableAttribute();

    /**
     * Returns the currently active availability period, if any.
     *
     * First we check for open-ended ($this->to === null) active periods. We assume there will never be more than one
     * active period at most, and thus return the first result if any is found.
     * If no match is found, we check for fixed active periods, also assuming there will be one result at most.
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function getCurrentAvailabilityAttribute();
}
