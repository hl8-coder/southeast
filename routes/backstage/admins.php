<?php

$api->get('admins/menu', 'AdminsController@menu')->name('backstage.admins.menu');
$api->patch('admins/change/password', 'AdminsController@changePassword')->name('backstage.admins.change_pwd');
$api->patch('admins/password/{admin}', 'AdminsController@updatePassword')->name('backstage.admins.password.update');
$api->get('admins', 'AdminsController@index')->name('backstage.admins.index');
$api->post('admins', 'AdminsController@store')->name('backstage.admins.store');
$api->get('admins/{admin}', 'AdminsController@show')->name('backstage.admins.show');
$api->put('admins/{admin}', 'AdminsController@update')->name('backstage.admins.update');
$api->patch('admins/{admin}', 'AdminsController@update')->name('backstage.admins.update');
$api->delete('admins/{admin}', 'AdminsController@destroy')->name('backstage.admins.destroy');
$api->patch('admins/{admin}/admin_roles', 'AdminsController@addAdminRoles')->name('backstage.admins.admin_roles.add');


