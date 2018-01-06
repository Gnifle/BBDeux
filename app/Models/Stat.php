<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Columns:
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
    public function statable()
    {
        return $this->morphTo();
    }
}
