<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\CompanyBankAccount::class, function (Faker $faker) {
    return [
        "payment_group_id" => 1, //暂定1(目前没相关资料)
        "code" => Str::random(10),
        "bank_id" =>  \App\Models\Bank::inRandomOrder()->first()->id,
        "branch" => "Hanoi" . Str::random(3),
        "account_name" => "Dang Kieu Anh " . Str::random(3),
        "account_no" => "443767" . Str::random(3),
    ];
});
