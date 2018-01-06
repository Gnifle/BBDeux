<?php

namespace App\Providers;

use App\Models\Availability;
use App\Models\Character;
use App\Models\CharacterClass;
use App\Models\Currency;
use App\Models\Price;
use App\Models\Stat;
use App\Models\User;
use App\Models\Weapon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'availability' => Availability::class,
            'character' => Character::class,
            'class' => CharacterClass::class,
            'currency' => Currency::class,
            'price' => Price::class,
            'stat' => Stat::class,
            'user', User::class,
            'weapon' => Weapon::class,
        ]);
    }
}
