<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Columns:
 * @property int $id
 * @property string $name
 * @property string $gender
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * Relationships:
 * @property-read \App\Models\CharacterClass $class
 *
 * Scopes:
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereTitle($value)
 * @method static Builder|self whereGender($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @method static Builder|self male($value)
 * @method static Builder|self female($value)
 * @method static Builder|self genderUnknown($value)
 */
class Character extends Model
{
    const GENDER_MALE = 'Male';
    const GENDER_FEMALE = 'Female';
    const GENDER_UNKNOWN = 'Unknown';

    public function class()
    {
        return $this->hasOne(CharacterClass::class);
    }

    public function scopeMale(Builder $query)
    {
        return $query->where('gender', self::GENDER_MALE);
    }

    public function scopeFemale(Builder $query)
    {
        return $query->where('gender', self::GENDER_FEMALE);
    }

    public function scopeGenderUnknown(Builder $query)
    {
        return $query->where('gender', self::GENDER_UNKNOWN);
    }
}
