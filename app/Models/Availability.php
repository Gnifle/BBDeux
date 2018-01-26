<?php

namespace App\Models;

use App\Abstracts\BBDeuxModel;
use App\Contracts\Periodable;
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
class Availability extends BBDeuxModel implements Periodable
{
    public $timestamps = false;

    public static $validation = [
        'availability_id' => 'integer|required',
        'availability_type' => 'interface:Available|required',
        'from' => 'date_format:"Y-m-d"|required',
        'to' => 'date_format:"Y-m-d"|required',
    ];

    protected $dates = [
        'from',
        'to'
    ];

    protected $fillable = [
        'availability_id',
        'availability_type',
        'from',
        'to',
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(PeriodObserver::class);
    }

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

    /**
     * @return string
     */
    public function getPeriodableRelationName()
    {
        return 'availability';
    }
}
