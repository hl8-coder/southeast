<?php

$api->post('actions/menu_route', 'ActionsController@storeAction')->name('backstage.action.menu_route'); 
$api->get('actions', 'ActionsController@index')->name('backstage.actions.index'); 
$api->post('actions', 'ActionsController@store')->name('backstage.actions.store'); 
$api->get('actions/{action}', 'ActionsController@show')->name('backstage.actions.show'); 
$api->put('actions/{action}', 'ActionsController@update')->name('backstage.actions.update'); 
$api->patch('actions/{action}', 'ActionsController@update')->name('backstage.actions.update'); 
$api->delete('actions/{action}', 'ActionsController@destroy')->name('backstage.actions.destroy'); 
$api->get('{admin_role}/action/list', 'ActionsController@actionList')->name('backstage.action.list'); 


