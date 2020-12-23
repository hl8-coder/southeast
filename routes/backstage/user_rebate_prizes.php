<?php

$api->get('marketing/user_rebate_prizes', 'UserRebatePrizesController@marketingIndex')->name('backstage.marketing.user_rebate_prizes.index'); 
$api->get('payment/user_rebate_prizes', 'UserRebatePrizesController@paymentIndex')->name('backstage.payment.user_rebate_prizes.index');
$api->get('marketing/user_rebate_prizes/download', 'UserRebatePrizesController@downloadMarketingReport')->name('backstage.marketing.user_rebate_prizes.download');
$api->get('payment/user_rebate_prizes/download', 'UserRebatePrizesController@downloadPaymentReport')->name('backstage.payment.user_rebate_prizes.download');
$api->get('member/user_rebate_prizes', 'UserRebatePrizesController@memberIndex')->name('backstage.member.user_rebate_prizes.index');
$api->patch('user_rebate_prizes/marketing_send', 'UserRebatePrizesController@marketingSend')->name('backstage.user_rebate_prizes.marketing_send');
$api->patch('user_rebate_prizes/payment_send', 'UserRebatePrizesController@paymentSend')->name('backstage.user_rebate_prizes.payment_send'); 


