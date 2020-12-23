<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\UserBankAccount::class, function (Faker $faker) {
    return [
        "bank_id" =>  \App\Models\Bank::inRandomOrder()->first()->id,
        "branch" => "Hanoi" . Str::random(3),
        "account_name" => "Dang Kieu Anh " . Str::random(3),
        "account_no" => "443767" . Str::random(3),
    ];
});
