<?php

namespace App\Models;

use App\Traits\HasMorphMapRelation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Columns:
 * @property int $id
 * @property int $amount
 * @property int $currency_id
 * @property int $priceable_id
 * @property string $priceable_type
 * @property Carbon $from
 * @property Carbon $to
 *
 * Dynamin Properties:
 * @property-read bool $is_free
 *
 * Relationships:
 * @property-read Model $priceable
 * @property-read Currency $currency
 *
 * Scopes:
 * @method Builder|static whereId(int $id)
 * @method Builder|static whereAmount(int $amount)
 * @method Builder|static whereCurrencyId(int $amount)
 * @method Builder|static wherePriceableId(int $availability_id)
 * @method Builder|static wherePriceableType(string $availability_type)
 * @method Builder|static whereFrom(Carbon $from)
 * @method Builder|static whereTo(Carbon $to)
 * @method Builder|static indefinite()
 * @method Builder|static definite()
 *
 * @mixin \Eloquent
 */
class Price extends Model implements Periodable
{
    public $timestamps = false;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::observe(PeriodObserver::class);
    }

    public function priceable()
    {
        return $this->morphTo();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
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
     * Whether the price is free, meaning either 0 or null
     *
     * @return bool
     */
    public function getIsFreeAttribute()
    {
        return $this->amount === null || $this->amount === 0;
    }

    /**
     * @return string
     */
    public function getPeriodableRelationName()
    {
        return 'priceable';
    }
}
