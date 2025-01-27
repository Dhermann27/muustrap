<?php

use Faker\Generator as Faker;

$factory->define(App\Room::class, function (Faker $faker) {
    return [
        'buildingid' => function () {
            return factory(\App\Building::class)->create()->id;
        },
        'room_number' => $faker->randomNumber(5),
        'capacity' => $faker->randomNumber(1),
        'is_workshop' => $faker->boolean,
        'is_handicap' => $faker->boolean,
        'xcoord' => $faker->randomNumber(3),
        'ycoord' => $faker->randomNumber(3),
        'pixelsize' => $faker->randomNumber(2)
    ];
});
