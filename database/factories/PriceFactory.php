<?php

use Faker\Generator as Faker;
use App\Models\Price;
use App\Models\Currency;
use App\Models\Weapon;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Price::class, function (Faker $faker) {
    /** @var Currency $currency */
    $currency = factory(Currency::class)->create();
    $date = Carbon::createFromTimestamp($faker->dateTimeThisYear->getTimestamp());

    return [
        'currency_id' => $currency->id,
        'amount' => $currency->name === 'Gas' ?
            round($faker->numberBetween(0, 300), -1)
            : round($faker->numberBetween(1000, 1000000), -3),
        'from' => $date->copy()->startOfMonth(),
        'to' => $date->copy()->endOfMonth(),
    ];
});

$factory->state(Price::class, 'free', function () {
    return [
        'currency_id' => null,
        'amount' => null,
    ];
});

$factory->state(Price::class, 'indefinite', function () {
    return [
        'to' => null,
    ];
});

$factory->state(Price::class, 'active_month', function () {
    return [
        'from' => Carbon::now()->subMonth()->startOfMonth(),
        'to' => Carbon::now()->addMonth()->endOfMonth(),
    ];
});

$factory->state(Price::class, 'weapon', function () {
    /** @var Weapon $weapon */
    $weapon = factory(Weapon::class)->create();

    return [
        'priceable_id' => $weapon->id,
        'priceable_type' => Weapon::class,
    ];
});
