<?php

namespace App\Traits;

use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Dynamic Properties:
 * @property-read Price $current_price
 *
 * Relationships:
 * @property-read Collection|Price[] $prices
 *
 * Scopes:
 * @method Builder|static free() Return only currently items
 */
trait HasPrices
{
    /** @var bool */
    protected $is_free;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function prices()
    {
        return $this->morphToMany(Price::class, 'priceable');
    }

//    public function scopeFree(Builder $query)
//    {
//        return $query->has('prices')
//            ->join('priceable', 'priceable.priceable_id', '=', "{$this->getTable()}.id")
//            ->join('prices', 'priceable.price_id', '=', 'prices.id')
//            ->where('amount', 0)
//            ->orWhereNull('amount');
//    }
//
//    public function scopeNonFree(Builder $query)
//    {
//        return $query->has('prices')
//            ->join('priceable', 'priceable.priceable_id', '=', "{$this->getTable()}.id")
//            ->join('prices', 'priceable.price_id', '=', 'prices.id')
//            ->whereNotNull('amount')
//            ->where('amount', '>', 0);
//    }
//
//    public function scopeJoules(Builder $query)
//    {
//        return $query->has('prices')
//            ->join('priceable', 'priceable.priceable_id', '=', "{$this->getTable()}.id")
//            ->join('prices', 'priceable.price_id', '=', 'prices.id')
//            ->join('currencies', 'prices.currency_id', '=', 'currencies.id')
//            ->where('name', 'Joules');
//    }
//
//    public function scopeGas(Builder $query)
//    {
//        return $query->has('prices')
//            ->join('priceable', 'priceable.priceable_id', '=', "{$this->getTable()}.id")
//            ->join('prices', 'priceable.price_id', '=', 'prices.id')
//            ->join('currencies', 'prices.currency_id', '=', 'currencies.id')
//            ->where('name', 'Gas');
//    }

    /**
     * Returns the currently active price, if any.
     *
     * First we check for open-ended ($this->to === null) price periods. We assume there will never be more than one
     * active price at most, and thus return the first result if any is found.
     * If no match is found, we check for fixed active periods, also assuming there will be one result at most.
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function getCurrentPriceAttribute()
    {
        $now = Carbon::now();

        return $this->prices()->whereNull('to')
            ->where('from', '<=', $now)
            ->firstOr(function () use ($now) {
                return $this->prices()->whereNotNull('to')
                    ->where('from', '<=', $now)
                    ->where('to', '>=', $now)
                    ->first();
            });
    }

    /**
     * @return bool Whether the item is currently free or not
     */
    public function getIsFreeAttribute()
    {
        return $this->current_price ? $this->current_price->is_free : false;
    }
}
