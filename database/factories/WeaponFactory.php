<?php

use Faker\Generator as Faker;
use App\Models\Weapon;
use App\Models\Availability;
use App\Models\CharacterClass;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Weapon::class, function (Faker $faker) {
    return [
        'class_id' => factory(CharacterClass::class)->create()->id,
        'title' => ucwords("{$faker->safeColorName} {$faker->word}"),
    ];
});
