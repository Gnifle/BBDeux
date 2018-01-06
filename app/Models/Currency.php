<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Columns:
 * @property int $id
 * @property string $name
 *
 * @mixin \Eloquent
 */
class Currency extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function price()
    {
        return $this->hasMany(Price::class);
    }
}
