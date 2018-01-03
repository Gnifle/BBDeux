<?php

use Faker\Generator as Faker;
use App\Models\Character;
use App\Models\CharacterClass;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(CharacterClass::class, function (Faker $faker) {
    return [
        'character_id' => factory(Character::class)->create()->id,
        'title' => $faker->firstName,
    ];
});
