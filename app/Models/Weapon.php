<?php

namespace App\Models;

use App\Abstracts\BBDeuxModel;
use App\Traits\HasAvailabilityPeriods;
use App\Traits\HasPrices;
use App\Traits\HasStats;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * Columns:
 * @property int $id
 * @property int $class_id
 * @property string $title
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Dynamic Properties:
 * @property-read Price $price @see App\Traits\HasPrices
 * @property-read bool $is_free @see App\Traits\HasPrices
 *
 * Relationships:
 * @property-read CharacterClass $class
 * @property-read \Illuminate\Database\Eloquent\Collection|Stat[] $stats
 * @property-read \Illuminate\Database\Eloquent\Collection|Stat[] $availabilities
 * @property-read \Illuminate\Database\Eloquent\Collection|Price[] $prices
 *
 * Scopes:
 * @method static Builder|static whereId($value)
 * @method static Builder|static whereTitle($value)
 * @method static Builder|static whereClassId($value)
 * @method static Builder|static whereUpdatedAt($value)
 * @method static Builder|static whereCreatedAt($value)
 * @method static Builder|static primary($value)
 * @method static Builder|static secondary($value)
 * @method static Builder|static melee($value)
 * @method static Builder|static available(Carbon $when = null) Return only available weapons
 * @method static Builder|static unavailable(Carbon $when = null) Return only available weapons
 *
 * @mixin \Eloquent
 */
class Weapon extends BBDeuxModel implements Available
{
    use HasAvailabilityPeriods,
        HasPrices,
        HasStats;

    const PRIMARY = 'primary';
    const SECONDARY = 'secondary';
    const MELEE = 'melee';

    protected $guarded = [];

    public function class()
    {
        return $this->belongsTo(CharacterClass::class, 'class_id');
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
