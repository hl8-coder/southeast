<?php

$api->get('users/{user}/profile_remarks', 'ProfileRemarksController@index')->name('backstage.profile_remarks.index'); 
$api->post('users/{user}/profile_remarks', 'ProfileRemarksController@store')->name('backstage.profile_remarks.store'); 


