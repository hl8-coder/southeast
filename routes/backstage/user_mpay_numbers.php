<?php

$api->get('user_mpay_numbers', 'UserMpayNumbersController@index')->name('backstage.user_mpay_numbers.index');
$api->get('user_mpay_numbers/user_index', 'UserMpayNumbersController@userIndex')->name('backstage.user_mpay_numbers.user_index');
$api->delete('user_mpay_numbers/{user_mpay_number}', 'UserMpayNumbersController@destroy')->name('backstage.user_mpay_numbers.destroy');


