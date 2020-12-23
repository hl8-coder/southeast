<?php

$api->get('bonus_groups', 'BonusGroupsController@index')->name('backstage.bonus_groups.index'); 
$api->post('bonus_groups', 'BonusGroupsController@store')->name('backstage.bonus_groups.store'); 


