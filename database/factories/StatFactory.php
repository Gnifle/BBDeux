<?php

use Faker\Generator as Faker;
use App\Models\Stat;
use App\Models\Weapon;
use App\Models\CharacterClass;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Stat::class, function (Faker $faker) {
    return [
        'title' => ucfirst($faker->word),
        'value' => $faker->boolean ? $faker->numberBetween(1, 1000) : $faker->randomFloat(2, 1, 1000),
        'unit' => $faker->randomElement(['seconds', 'minutes', 'rounds']),
    ];
});

$factory->state(Stat::class, 'weapon', function () {
    /** @var Weapon $weapon */
    $weapon = factory(Weapon::class)->create();

    return [
        'statable_id' => $weapon->id,
        'statable_type' => Weapon::class,
    ];
});

$factory->state(Stat::class, 'class', function () {
    /** @var CharacterClass $class */
    $class = factory(CharacterClass::class)->create();

    return [
        'statable_id' => $class->id,
        'statable_type' => CharacterClass::class,
    ];
});
