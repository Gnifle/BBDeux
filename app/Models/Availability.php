<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Columns:
 * @property int $id
 * @property int $availability_id
 * @property string $availability_type
 * @property Carbon $from
 * @property Carbon $to
 *
 * Relationships:
 * @property-read Model $availability
 *
 * Scopes:
 * @method Builder|static whereId(int $id)
 * @method Builder|static whereAvailabilityId(int $availability_id)
 * @method Builder|static whereAvailabilityType(string $availability_type)
 * @method Builder|static whereFrom(Carbon $from)
 * @method Builder|static whereTo(Carbon $to)
 * @method Builder|static indefinite()
 * @method Builder|static definite()
 *
 * @mixin \Eloquent
 */
class Availability extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function availability()
    {
        return $this->morphTo();
    }

    /**
     * Get only indefinite (no end-date) availability periods
     *
     * @param Builder $query
     * @return Builder|static
     */
    public function scopeIndefinite(Builder $query)
    {
        return $query->whereNull('to');
    }

    /**
     * Get only definite (with end-date) availability periods
     *
     * @param Builder $query
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function scopeDefinite(Builder $query)
    {
        return $query->whereNotNull('to');
    }
}
