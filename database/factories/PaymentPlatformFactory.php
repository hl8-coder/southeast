<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\PaymentPlatform::class, function (Faker $faker) {
    return [
        "name" => Str::random(10),
        "display_name" => Str::random(10),
        "code" => Str::random(10),
        "currencies" => "VND",
    ];
});

$factory->afterCreating(App\Models\PaymentPlatform::class, function ($item, $faker) {
	switch ($item->payment_type) {
		case App\Models\PaymentPlatform::PAYMENT_TYPE_BANKCARD:
			$item->company_bank_account = factory(App\Models\CompanyBankAccount::class)->create(["platform_id" => $item->id]);
			break;
		
		default:
			# code...
			break;
	}
});