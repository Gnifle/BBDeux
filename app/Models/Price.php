<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Columns:
 * @property int $id
 * @property int $amount
 * @property int $currency_id
 *
 * Dynamin Properties:
 * @property-read bool $is_free
 *
 * Relationships:
 * @property-read Currency $currency
 *
 * @mixin \Eloquent
 */
class Price extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function weapons()
    {
        return $this->morphedByMany(Weapon::class, 'priceable');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getIsFreeAttribute()
    {
        return $this->amount === 0 || $this->amount === null;
    }
}
