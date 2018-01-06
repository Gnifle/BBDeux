<?php

namespace App\Models;

use App\Traits\HasAvailabilityPeriods;
use App\Traits\HasPrices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Columns:
 * @property int $id
 * @property int $class_id
 * @property string $title
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * Dynamic Properties:
 * @property-read Price $price
 *
 * Relationships:
 * @property-read CharacterClass $class
 * @property-read \Illuminate\Database\Eloquent\Collection|Stat[] $stats
 * @property-read \Illuminate\Database\Eloquent\Collection|Price[] $prices
 *
 * Scopes:
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereTitle($value)
 * @method static Builder|self whereClassId($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self primary($value)
 * @method static Builder|self secondary($value)
 * @method static Builder|self melee($value)
 * @method static Builder|static available(Carbon $when = null) Return only available weapons
 *
 * @mixin \Eloquent
 */
class Weapon extends Model
{
    use HasAvailabilityPeriods,
        HasPrices;

    const PRIMARY = 'primary';
    const SECONDARY = 'secondary';
    const MELEE = 'melee';

    protected $guarded = [];

    public function class()
    {
        return $this->belongsTo(CharacterClass::class, 'class_id');
    }

    public function stats()
    {
        return $this->morphMany(Stat::class, 'statable');
    }

    public function scopePrimary(Builder $query)
    {
        return $query->where('type', self::PRIMARY);
    }

    public function scopeSecondary(Builder $query)
    {
        return $query->where('type', self::SECONDARY);
    }

    public function scopeMelee(Builder $query)
    {
        return $query->where('type', self::MELEE);
    }
}
