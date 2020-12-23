<?php


namespace Tests\Feature\flow;


use App\Models\Deposit;
use Tests\TestCase;
use Faker\Factory as Faker;

class user_deposit extends TestCase
{
    public function testDeposit()
    {
        $faker = Faker::create();
        for ($i = 1; $i < 51; $i++) {
            $date = '2019-10-'.mt_rand(1, 18). ' ' . $faker->time;
            # 建立充值记录
            $randAmount = rand(10, 10000);
            factory(Deposit::class)
                ->create([
                    "user_id"             => 1,
                    "currency"            => "USD",
                    "language"            => "en-US",
                    "amount"              => $randAmount,
                    "receive_amount"      => $randAmount,
                    "arrival_amount"      => $randAmount,
                    "payment_platform_id" => rand(1, 8),
                    "status"              => rand(1, 4),
                    "created_at"          => $date,
                ]);
        }
    }
}