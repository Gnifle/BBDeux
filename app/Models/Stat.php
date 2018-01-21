<?php

namespace App\Models;

use App\Traits\HasAvailabilityPeriods;
use Illuminate\Database\Eloquent\Model;

/**
 * Columns:
 * @property int $id
 * @property string $title
 * @property string $value
 * @property string $unit
 *
 * Relationships:
 * @property-read Model $statable
 *
 * @mixin \Eloquent
 */
class Stat extends Model
{
    use HasAvailabilityPeriods;

    public function statable()
    {
        return $this->morphTo();
    }
}
