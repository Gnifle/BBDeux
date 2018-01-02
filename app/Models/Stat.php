<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    public function statable()
    {
        return $this->morphTo();
    }
}
