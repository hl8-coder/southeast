<?php

$api->get('admin_roles', 'AdminRolesController@index')->name('backstage.admin_roles.index'); 
$api->post('admin_roles', 'AdminRolesController@store')->name('backstage.admin_roles.store'); 
$api->get('admin_roles/{admin_role}', 'AdminRolesController@show')->name('backstage.admin_roles.show'); 
$api->put('admin_roles/{admin_role}', 'AdminRolesController@update')->name('backstage.admin_roles.update'); 
$api->patch('admin_roles/{admin_role}', 'AdminRolesController@update')->name('backstage.admin_roles.update'); 
$api->delete('admin_roles/{admin_role}', 'AdminRolesController@destroy')->name('backstage.admin_roles.destroy'); 
$api->post('admin_roles/{admin_role}/actions', 'AdminRolesController@addActions')->name('backstage.admin_roles.actions.add'); 


