<?php

use Faker\Generator as Faker;
use App\Models\Availability;
use App\Models\Weapon;
use App\Models\Skin;
use App\Models\Stat;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Availability::class, function (Faker $faker) {
    return [
        'from' => Carbon::createFromTimestamp($faker->dateTimeBetween('-6 months', 'now')->getTimestamp()),
        'to' => Carbon::createFromTimestamp($faker->dateTimeBetween('now', '+6 months')->getTimestamp()),
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
        'availability_type' => Weapon::class,
    ];
});

$factory->state(Availability::class, 'skin', function () {
    /** @var Skin $skin */
    $skin = factory(Skin::class)->create();

    return [
        'availability_id' => $skin->id,
        'availability_type' => Skin::class,
    ];
});

$factory->state(Availability::class, 'stat', function () {
    /** @var Stat $stat */
    $stat = factory(Stat::class)->create();

    return [
        'availability_id' => $stat->id,
        'availability_type' => Stat::class,
    ];
});
