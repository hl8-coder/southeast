<?php

$api->get('news', 'NewsController@index')->name('backstage.news.index');
$api->post('news', 'NewsController@store')->name('backstage.news.store');
$api->get('news/{news}', 'NewsController@show')->name('backstage.news.show');
$api->patch('news/{news}', 'NewsController@update')->name('backstage.news.update');
$api->delete('news/{news}', 'NewsController@destroy')->name('backstage.news.destroy');


