<?php

$api->get('banks', 'BanksController@index')->name('backstage.banks.index'); 
$api->post('banks', 'BanksController@store')->name('backstage.banks.store'); 
$api->patch('banks/{bank}', 'BanksController@update')->name('backstage.banks.update'); 


