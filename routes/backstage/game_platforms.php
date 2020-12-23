<?php

$api->get('game_platforms', 'GamePlatformsController@index')->name('backstage.game_platforms.index'); 
$api->get('game_platforms/{code}/balance', 'GamePlatformsController@balance')->name('backstage.game_platforms.balance');
$api->patch('game_platforms/{game_platform}', 'GamePlatformsController@update')->name('backstage.game_platforms.update');
$api->post('game_platforms/transfer', 'GamePlatformsController@transfer')->name('backstage.game_platforms.transfer'); 


