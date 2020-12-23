<?php

$api->get('rebates', 'RebatesController@index')->name('backstage.rebates.index'); 
$api->post('rebates', 'RebatesController@store')->name('backstage.rebates.store'); 
$api->patch('rebates/{rebate}', 'RebatesController@update')->name('backstage.rebates.update'); 


