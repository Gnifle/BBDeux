<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Abstracts\BBDeuxModel;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Carbon\Carbon;

/**
 * Columns:
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $gender
 * @property Carbon|null $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships:
 * @property-read \App\Models\CharacterClass $class
 *
 * Scopes:
 * @method static Builder|self whereId(int $value)
 * @method static Builder|self whereTitle(string $value)
 * @method static Builder|self whereSlug(string $value)
 * @method static Builder|self whereGender(string $value)
 * @method static Builder|self whereDeletedAt(Carbon $value)
 * @method static Builder|self whereCreatedAt(Carbon $value)
 * @method static Builder|self whereUpdatedAt(Carbon $value)
 * @method static Builder|self male()
 * @method static Builder|self female()
 * @method static Builder|self genderUnknown()
 */
class Character extends BBDeuxModel
{
    use HasSlug,
        SoftDeletes;

    const GENDER_MALE = 'Male';
    const GENDER_FEMALE = 'Female';
    const GENDER_UNKNOWN = 'Unknown';

    public static $validation = [
        'name' => 'string',
        'gender' => 'string|gender',
    ];

    public static $validation_required = [
        'name' => 'string|required',
        'gender' => 'string|gender|required',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'gender',
    ];

    public function class() : HasOne
    {
        return $this->hasOne(CharacterClass::class);
    }

    public function scopeMale(Builder $query) : Builder
    {
        return $query->where('gender', self::GENDER_MALE);
    }

    public function scopeFemale(Builder $query) : Builder
    {
        return $query->where('gender', self::GENDER_FEMALE);
    }

    public function scopeGenderUnknown(Builder $query) : Builder
    {
        return $query->where('gender', self::GENDER_UNKNOWN);
    }

    public function getRouteKeyName() : string
    {
        return 'slug';
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public static function genders() : array
    {
        return [
            static::GENDER_MALE,
            static::GENDER_FEMALE,
            static::GENDER_UNKNOWN,
        ];
    }
}
