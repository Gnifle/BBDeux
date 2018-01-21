<?php

namespace App\Traits;

use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Dynamic Properties:
 * @property-read bool $is_free
 * @property-read Price $current_price
 *
 * Relationships:
 * @property-read Collection|Price[] $prices
 *
 * Scopes:
 * @method Builder|static withPrices() Join the `prices` table on the query
 * @method Builder|static withCurrencies() Join the `currencies` table through the `prices` table on the query
 * @method Builder|static free() Return only currently free items
 * @method Builder|static nonFree() Return only currently non-free items
 * @method Builder|static gas() Return only items with a Gas price
 * @method Builder|static joules() Return only items with a Joule price
 */
trait HasPrices
{
    /** @var bool */
    protected $is_free;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function prices()
    {
        return $this->morphMany(Price::class, 'priceable');
    }

    /**
     * Join the `prices` table
     *
     * @param Builder|static $query
     * @return Builder|static
     */
    public function scopeWithPrices(Builder $query)
    {
        return $query->join('prices', 'prices.priceable_id', '=', "{$this->getTable()}.{$this->getKeyName()}");
    }

    /**
     * Join the `currencies` table through the `prices` table
     *
     * @param Builder|static $query
     * @return Builder|static
     */
    public function scopeWithCurrencies(Builder $query)
    {
        return $query->join('prices', 'prices.priceable_id', '=', "{$this->getTable()}.{$this->getKeyName()}")
            ->join('currencies', 'prices.currency_id', '=', 'currencies.id');
    }

    /**
     * Retrieve only free items
     *
     * @param Builder|static $query
     * @return Builder|static
     */
    public function scopeFree(Builder $query)
    {
        return $query->withPrices()
            ->where('amount', 0)
            ->orWhereNull('amount');
    }

    /**
     * Retrieve only non-free items
     *
     * @param Builder|static $query
     * @return Builder|static
     */
    public function scopeNonFree(Builder $query)
    {
        return $query->withPrices()
            ->whereNotNull('amount')
            ->where('amount', '>', 0);
    }

    /**
     * Retrieve only items with a Joule price
     *
     * @param Builder|static $query
     * @return Builder|static
     */
    public function scopeJoules(Builder $query)
    {
        return $query->withCurrencies()
            ->where('currencies.name', 'Joules');
    }

    /**
     * Retrieve only items with a Gas price
     *
     * @param Builder|static $query
     * @return Builder|static
     */
    public function scopeGas(Builder $query)
    {
        return $query->withCurrencies()
            ->where('currencies.name', 'Gas');
    }

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
        return $this->priceAt(Carbon::now());
    }

    /**
     * @return bool Whether the item is currently free or not
     */
    public function getIsFreeAttribute()
    {
        return $this->freeAt(Carbon::now());
    }

    /**
     * Retrieve item price at a given time.
     *
     * @param Carbon|null $when Defaults to Carbon::now()
     *
     * @return Price
     */
    public function priceAt(Carbon $when = null)
    {
        $when = $when ?: Carbon::now();

        return $this->prices()->whereNull('to')
            ->where('from', '<=', $when)
            ->whereNull('to')
            ->firstOr(function () use ($when) {
                return $this->prices()->whereNotNull('to')
                    ->where('from', '<=', $when)
                    ->where('to', '>=', $when)
                    ->first();
            });
    }

    /**
     * Determines whether the item was free at a given time
     *
     * @param Carbon|null $when
     * @return bool
     */
    public function freeAt(Carbon $when = null)
    {
        $when = $when ?: Carbon::now();
        $price = $this->priceAt($when);

        return $price ? $price->is_free : false;
    }
}
