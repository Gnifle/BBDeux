<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * @property int $id
 * @property Carbon $from
 * @property Carbon $to
 *
 * @mixin \Eloquent
 */
interface Periodable
{
    /**
     * @return string
     */
    public function getPeriodableRelationName();
}
