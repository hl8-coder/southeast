<?php

$api->get('notifications', 'NotificationsController@index')->name('backstage.users.notifications.index'); 
$api->post('notifications', 'NotificationsController@store')->name('backstage.users.notifications.store'); 
$api->get('notifications/{notificationMessage}', 'NotificationsController@show')->name('backstage.users.notifications.show'); 
$api->patch('notifications/{notification}/reply', 'NotificationsController@reply')->name('backstage.users.notifications.reply'); 
$api->get('notifications/{notification}/detail', 'NotificationsController@detail')->name('backstage.users.notifications.reply'); 


