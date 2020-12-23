<?php

$api->get('banners', 'BannersController@index')->name('backstage.banners.index'); 
$api->post('banners', 'BannersController@store')->name('backstage.banners.store'); 
$api->patch('banners/{banner}', 'BannersController@update')->name('backstage.banners.update'); 
$api->delete('banners/{banner}', 'BannersController@destroy')->name('backstage.banners.delete'); 


