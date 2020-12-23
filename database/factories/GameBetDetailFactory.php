<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;

$factory->define(App\Models\GameBetDetail::class, function (Faker $faker) {
    return [
        "product_code" => 'test',
        "platform_currency" => 'VND',
        "order_id" => Str::random(20),
        "game_type" => random_int(1,4),
        "game_code" => 'test',
        "game_name" => 'test',
        "issue" => Str::random(10),
        "bet" => random_int(200000,400000),
        "prize" => random_int(200,50000),
        "profit" => 0,
        "bet_at" => Carbon::now(),
        "payout_at" => Carbon::now(),
        "user_currency" => 'VND',
        "user_bet" => 0,
        "user_prize" => 0,
        "user_profit" => 0,
        "platform_profit" => 0,
        "multiple" => 0,
        "money_unit" => "",
        "available_bet" => 0,
        "available_profit" => 0,
        "is_close" => 1,
        "platform_status" => 1,
        "status" => 3,
        "finished_at" => Carbon::now(),
    ];
});

$factory->afterCreating(App\Models\GameBetDetail::class, function ($bet, $faker) {
    $bet->profit = $bet->prize - $bet->bet;
    $bet->user_bet = $bet->bet;
    $bet->user_prize = $bet->prize;
    $bet->user_profit = $bet->profit;
    $bet->platform_profit = $bet->profit * -1;
    $bet->available_profit = $bet->profit;
    $bet->save();
});