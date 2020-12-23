<?php

$api->get('promotions', 'PromotionsController@index')->name('backstage.promotions.index'); 
$api->post('promotions', 'PromotionsController@store')->name('backstage.promotions.store'); 
$api->post('promotions/{promotion}', 'PromotionsController@copy')->name('backstage.promotions.copy'); 
$api->patch('promotions/{promotion}', 'PromotionsController@update')->name('backstage.promotions.update'); 
$api->delete('promotions/{promotion}', 'PromotionsController@destroy')->name('backstage.promotions.delete'); 


