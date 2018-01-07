<?php

use Faker\Generator as Faker;
use App\Models\Availability;
use App\Models\Weapon;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Availability::class, function (Faker $faker) {
    $date = Carbon::createFromTimestamp($faker->dateTimeThisYear->getTimestamp());

    return [
        'from' => $date->startOfMonth(),
        'to' => $date->endOfMonth(),
    ];
});

$factory->state(Availability::class, 'indefinite', function () {
    return [
        'to' => null,
    ];
});

$factory->state(Availability::class, 'weapon', function () {
    /** @var Weapon $weapon */
    $weapon = factory(Weapon::class)->create();

    return [
        'availability_id' => $weapon->id,
        'availability_type' => 'weapon',
    ];
});
