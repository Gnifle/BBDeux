<?php

namespace App\Abstracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
abstract class BBDeuxModel extends Model
{
    public static $rules = [];
}
