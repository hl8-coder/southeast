<?php

$api->get('languages', 'LanguagesController@index')->name('backstage.languages.index');
$api->post('languages', 'LanguagesController@store')->name('backstage.languages.store');
$api->patch('languages/{language}', 'LanguagesController@update')->name('backstage.languages.update');
$api->delete('languages/{language}', 'LanguagesController@destroy')->name('backstage.languages.destroy');


