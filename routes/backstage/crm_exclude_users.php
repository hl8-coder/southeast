<?php

$api->get('crm_exclude_users', 'CrmExcludeUsersController@index')->name('backstage.crm_exclude_users.index'); 
$api->patch('crm_exclude_users/{crmExcludeUser}', 'CrmExcludeUsersController@update')->name('backstage.crm_exclude_users.udpate'); 
$api->post('crm_exclude_users', 'CrmExcludeUsersController@store')->name('backstage.crm_exclude_users.store'); 
$api->delete('crm_exclude_users/{crmExcludeUser}', 'CrmExcludeUsersController@delete')->name('backstage.crm_exclude_users.delete'); 


