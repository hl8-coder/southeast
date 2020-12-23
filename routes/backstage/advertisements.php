<?php

$api->get('advertisements', 'AdvertisementsController@index')->name('backstage.advertisements.index');
$api->post('advertisements', 'AdvertisementsController@store')->name('backstage.advertisements.store');
# 这两个是由原来 resource 形式生成而来，为了安全起见，都保留
$api->put('advertisements/{advertisement}', 'AdvertisementsController@update')->name('backstage.advertisements.update');
$api->patch('advertisements/{advertisement}', 'AdvertisementsController@update')->name('backstage.advertisements.update');

$api->delete('advertisements/{advertisement}', 'AdvertisementsController@destroy')->name('backstage.advertisements.destroy');


