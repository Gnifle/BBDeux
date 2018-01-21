<?php

namespace App\Models;

use App\Traits\HasAvailabilityPeriods;
use App\Traits\HasPrices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|Stat[] $availabilities
 * @property-read \Illuminate\Database\Eloquent\Collection|Price[] $prices
 *
 * Scopes:
 * @method static Builder|static whereId($value)
 * @method static Builder|static whereTitle($value)
 * @method static Builder|static whereClassId($value)
 * @method static Builder|static whereUpdatedAt($value)
 * @method static Builder|static whereCreatedAt($value)
 * @method static Builder|static available(Carbon $when = null) Return only available weapons
 * @method static Builder|static unavailable(Carbon $when = null) Return only available weapons
 *
 * @mixin \Eloquent
 */
class Skin extends Model
{
    use HasAvailabilityPeriods,
        HasPrices;

    public function class()
    {
        return $this->belongsTo(CharacterClass::class, 'class_id');
    }
}
