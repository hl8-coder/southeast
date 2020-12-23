<?php

$api->get('user_messages', 'UserMessagesController@index')->name('backstage.users.user_messages.index'); 
$api->post('user_messages', 'UserMessagesController@store')->name('backstage.users.user_messages.store'); 
$api->get('user_messages/{userMessage}', 'UserMessagesController@show')->name('backstage.users.user_messages.show'); 
$api->get('user_messages/excel/download', 'UserMessagesController@downloadUserMessageExcelTemplate')->name('backstage.users.user_messages.downloadExcelTemplate'); 


