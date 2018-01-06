<?php

use Faker\Generator as Faker;
use App\Models\Currency;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Currency::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['Joules', 'Gas']),
    ];
});
