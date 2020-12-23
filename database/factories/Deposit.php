<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Deposit::class, function (Faker $faker) {
    return [
        'payment_type' => '1'
    ];
});
