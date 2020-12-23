<?php

$api->get('announcements', 'AnnouncementsController@index')->name('backstage.announcements.index'); 
$api->post('announcements', 'AnnouncementsController@store')->name('backstage.announcements.store'); 
$api->patch('announcements/{announcement}', 'AnnouncementsController@update')->name('backstage.announcements.update'); 


