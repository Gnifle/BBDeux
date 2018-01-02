<?php

namespace App\Providers;

use App\Models\CharacterClass;
use App\Models\Weapon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'class' => CharacterClass::class,
            'weapon' => Weapon::class,
        ]);
    }
}
