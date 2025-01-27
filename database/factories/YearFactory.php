<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(\App\Year::class, function (Faker $faker) {
    $thisyear = $faker->year;
    return [
        'year' => $thisyear,
        'start_date' => Carbon::parse('first Sunday of July ' . $thisyear)->toDateString(),
        'start_open' => Carbon::parse('first day of February ' . $thisyear)->toDateString(),
        'is_current' => '1',
        'is_live' => '1',
        'is_crunch' => '0',
        'is_accept_paypal' => '1',
        'is_calendar' => '1',
        'is_room_select' => '1',
        'is_workshop_proposal' => '1',
        'is_artfair' => '1',
        'is_coffeehouse' => '0'
    ];
});
