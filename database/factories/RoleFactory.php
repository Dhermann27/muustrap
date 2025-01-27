<?php

use Faker\Generator as Faker;

$factory->define(\App\Role::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'display_name' => $faker->title,
        'description' => $faker->sentence
    ];
});
