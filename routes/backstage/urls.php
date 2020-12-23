<?php

$api->get('domain_management', 'UrlsController@index')->name('backstage.domain_management.index'); 
$api->post('domain_management', 'UrlsController@store')->name('backstage.domain_management.store'); 
$api->post('domain_management/{url}', 'UrlsController@update')->name('backstage.domain_management.update'); 
$api->delete('domain_management/{url}', 'UrlsController@destroy')->name('backstage.domain_management.destroy'); 


