<?php

$api->get('vips', 'VipsController@index')->name('backstage.vips.index'); 
$api->post('vips', 'VipsController@store')->name('backstage.vips.store'); 
$api->put('vips/{vip}', 'VipsController@update')->name('backstage.vips.update'); 
$api->patch('vips/{vip}', 'VipsController@update')->name('backstage.vips.update'); 


