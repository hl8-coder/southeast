<?php

$api->get('menus', 'MenusController@index')->name('backstage.menus.index');
$api->post('menus', 'MenusController@store')->name('backstage.menus.store');
$api->get('menus/{menu}', 'MenusController@show')->name('backstage.menus.show');
$api->patch('menus/{menu}', 'MenusController@update')->name('backstage.menus.update');
$api->delete('menus/{menu}', 'MenusController@destroy')->name('backstage.menus.destroy');


