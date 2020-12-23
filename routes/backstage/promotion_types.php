<?php

$api->get('promotion_types', 'PromotionTypesController@index')->name('backstage.promotion_types.index'); 
$api->post('promotion_types', 'PromotionTypesController@store')->name('backstage.promotion_types.store'); 
$api->patch('promotion_types/{promotion_type}', 'PromotionTypesController@update')->name('backstage.promotion_types.update'); 


