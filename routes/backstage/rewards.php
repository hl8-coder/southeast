<?php

$api->get('rewards', 'RewardsController@index')->name('backstage.rewards.index');
$api->post('rewards', 'RewardsController@store')->name('backstage.rewards.store');
$api->patch('rewards/{reward}', 'RewardsController@update')->name('backstage.rewards.update');
$api->delete('rewards/{reward}', 'RewardsController@destroy')->name('backstage.rewards.destroy');


