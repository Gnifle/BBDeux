<?php

namespace App\Models;

use App\Traits\HasPrices;
use App\Traits\HasStats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Columns:
 * @property int $id
 * @property string $title
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * Relationships:
 * @property-read \App\Models\Character $character
 * @property-read \Illuminate\Database\Eloquent\Collection|Weapon[] $weapons
 * @property-read \Illuminate\Database\Eloquent\Collection|Stat[] $stats
 *
 * Scopes:
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereTitle($value)
 * @method static Builder|self whereUpdatedAt($value)
 */
class CharacterClass extends Model
{
    use HasPrices,
        HasStats;

    protected $guarded = [];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function weapons()
    {
        return $this->hasMany(Weapon::class, 'class_id');
    }
}
