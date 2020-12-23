<?php

$api->get('verified_prize_black_users', 'VerifiedPrizeBlackUsersController@index')->name('backstage.verified_prize_black_users.index');
$api->post('verified_prize_black_users', 'VerifiedPrizeBlackUsersController@store')->name('backstage.verified_prize_black_users.store');
$api->delete('verified_prize_black_users/{verified_prize_black_user}', 'VerifiedPrizeBlackUsersController@destroy')->name('backstage.verified_prize_black_users.destroy');
$api->get('verified_prize_black_users/excel_template', 'VerifiedPrizeBlackUsersController@excelTemplate')->name('backstage.verified_prize_black_users.excel_template');
$api->post('verified_prize_black_users/excel', 'VerifiedPrizeBlackUsersController@importByExcel')->name('backstage.verified_prize_black_users.import_by_excel');
