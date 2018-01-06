<?php

use Faker\Generator as Faker;
use App\Models\Price;
use App\Models\Currency;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Price::class, function (Faker $faker) {
    /** @var Currency $currency */
    $currency = factory(Currency::class)->create();

    return [
        'currency_id' => $currency->id,
        'amount' => $currency->name === 'Gas' ?
            round($faker->numberBetween(0, 300), -1)
            : round($faker->numberBetween(1000, 1000000), -3),
    ];
});
