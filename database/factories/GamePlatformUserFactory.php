<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\GamePlatformUser::class, function (Faker $faker) {
    return [
        "password" => Str::random(10)
    ];
});
