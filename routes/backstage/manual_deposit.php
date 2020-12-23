<?php

$api->post('manual/deposits', 'ManualDepositController@store')->name('backstage.manual.deposits.store'); 
$api->get('payment_platform/index', 'ManualDepositController@paymentPlatform')->name('backstage.payment_platforms.payment_platforms'); 
$api->get('payment_platform/menu', 'ManualDepositController@menu')->name('backstage.payment_platform.menu'); 
$api->post('check/username', 'ManualDepositController@checkUsername')->name('backstage.check.username'); 
$api->get('get/user/bank/{user}', 'ManualDepositController@getUserBank')->name('backstage.get.userBank'); 


