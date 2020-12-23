<?php

$api->get('affiliate/creative_resources', 'CreativeResourcesController@index')->name('backstage.affiliate.creative_resources.index'); 
$api->post('affiliate/creative_resources', 'CreativeResourcesController@store')->name('backstage.affiliate.creative_resources.store'); 
$api->patch('affiliate/creative_resources/{resource}', 'CreativeResourcesController@update')->name('backstage.affiliate.creative_resources.update'); 
$api->delete('affiliate/creative_resources/{resource}', 'CreativeResourcesController@destroy')->name('backstage.affiliate.creative_resources.destroy'); 


