<?php

$api->get('bonuses', 'BonusesController@index')->name('backstage.bonuses.index'); 
$api->post('bonuses', 'BonusesController@store')->name('backstage.bonuses.store'); 
$api->get('bonuses/{bonus}', 'BonusesController@show')->name('backstage.bonuses.show'); 
$api->post('bonuses/{bonus}', 'BonusesController@update')->name('backstage.bonuses.update'); 
$api->get('bonuses/{bonus}/users', 'BonusesController@getUsers')->name('backstage.bonuses.users'); 
$api->get('bonuses/excel/download', 'BonusesController@downloadBonusExcelTemplate')->name('backstage.bonuses.downloadExcelTemplate'); 


