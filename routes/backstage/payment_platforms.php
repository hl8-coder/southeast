<?php

$api->get('payment_platforms', 'PaymentPlatformsController@index')->name('backstage.payment_platforms.index');
$api->post('payment_platforms', 'PaymentPlatformsController@store')->name('backstage.payment_platforms.store');
$api->get('payment_platforms/{payment_platform}', 'PaymentPlatformsController@show')->name('backstage.payment_platforms.show');
$api->patch('payment_platforms/{payment_platform}', 'PaymentPlatformsController@update')->name('backstage.payment_platforms.update');
$api->get('payment_platform/search', 'PaymentPlatformsController@searchInReadTime')->name('backstage.payment_platform.search');


