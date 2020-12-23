<?php

$api->get('games', 'GamesController@index')->name('backstage.games.index');
$api->get('games/audit/{game}', 'GamesController@audit')->name('backstage.games.audit');
$api->post('games', 'GamesController@store')->name('backstage.games.store'); 
$api->patch('games/{game}', 'GamesController@update')->name('backstage.games.update'); 
$api->delete('games/{game}', 'GamesController@destroy')->name('backstage.games.destroy'); 


