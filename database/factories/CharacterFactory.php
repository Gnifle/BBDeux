<?php

use Faker\Generator as Faker;
use App\Models\Character;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Character::class, function (Faker $faker) {
    $gender = $faker->randomElement([Character::GENDER_MALE, Character::GENDER_FEMALE, Character::GENDER_UNKNOWN]);

    return [
        'name' => $gender === Character::GENDER_UNKNOWN ?
            ucfirst($faker->unique()->userName)
            : $gender === Character::GENDER_MALE ? $faker->unique()->firstNameMale : $faker->unique()->firstNameFemale,
        'gender' => $gender,
    ];
});

$factory->state(Character::class, 'male', function () {
    return [
        'gender' => Character::GENDER_MALE,
    ];
});

$factory->state(Character::class, 'female', function () {
    return [
        'gender' => Character::GENDER_FEMALE,
    ];
});

$factory->state(Character::class, 'gender_unknown', function () {
    return [
        'gender' => Character::GENDER_UNKNOWN,
    ];
});
